<?php

namespace App\Http\Controllers\Salon\BookingDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Beautician;
use App\Repositories\BookingRepository;
use App\Repositories\ClientRepository;
use App\Models\Booking;
use App\Models\Client;
use App\Models\User;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceType;
use App\Models\ServiceTypeManagement;
use App\Models\StockItem;
use App\Models\StockItemGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TabEditController extends Controller
{
    public function index(Request $request, BookingRepository $booking_repo, $booking_id, $tab_name)
    {
        // Existing bookings can be opened for view/edit; payment/update still enforce open float server-side.
        // New bookings (booking_id = 0) require an open register before the POS screen loads.
        $check_float_amount = ($booking_id > 0 || hasOpenFloatAmount()) ? 1 : 0;

        $booking_info = $this->findById($booking_id, $booking_repo);

        if ($booking_id > 0) {
            $booking_info->loadEssentialsForVueJs();
        }

        // Catalog tabs only — product/service lists load via AJAX when their modals open.
        $items_groupe = $this->getItemGroupes();
        $service_category = $this->getAllServiceCategory();

        $details_edit_vue = [
            'booking_id' => $booking_id,
            'tab_name' => $tab_name,
            'booking_info' => $booking_info,
            'focused_active_service' => null,
            'focused_active_booking_date' => null,
            'service_quick_search_results' => [
                'paginator' => [],
            ],
            'urls' => $this->getUrlsForVue($booking_info->booking_id, $tab_name),
            'all_db_clients' => [],
            'all_db_services' => [],
            'all_db_items' => [],
            'all_item_groupes' => $items_groupe,
            'all_items_list' => [],
            'all_service_category' => $service_category,
            'all_services' => [],
            'services_for_select2' => [],
            'can_void_completed_payment' => auth()->user()
                && (auth()->user()->hasRole(\App\Models\AclRole::ROLE_SUPER_ADMIN)
                    || auth()->user()->can('void_completed_payment')),
            'can_void_cash_transaction' => auth()->user()
                && (auth()->user()->hasRole(\App\Models\AclRole::ROLE_SUPER_ADMIN)
                    || auth()->user()->can('void_cash_transaction')
                    || auth()->user()->can('void_completed_payment')),
            'can_void_card_transaction' => auth()->user()
                && (auth()->user()->hasRole(\App\Models\AclRole::ROLE_SUPER_ADMIN)
                    || auth()->user()->can('void_card_transaction')
                    || auth()->user()->can('void_completed_payment')),
        ];

        $beauticians_dropdown = \Facades\App\Models\Beautician::beauticiansDropdown();
        $split_beauticians = \Facades\App\Models\Beautician::beauticiansListForSplit();

        return view('salon.booking-details.ajax-tabs.tab-edit', compact(
            'details_edit_vue',
            'beauticians_dropdown',
            'split_beauticians',
            'check_float_amount'
        ));
    }

    public function save(Request $request, BookingRepository $booking_repo, $tab_name)
    {

        $booking_id = $request->booking_id;

        // Fetch db record for update, or create a new model for insert
        $booking = $this->findById($booking_id, $booking_repo);

        // Apply validation
        $request->validate([
            'client_id' => 'required',
        ]);

        // Start database transaction
        DB::transaction(function () use ($request, $booking) {
            return $this->createOrUpdateBooking($booking, $request->client_id);
        });

        return $this->sendBookingResponseJson($booking, $tab_name);
    }

    public function saveBookingClient(Request $request, BookingRepository $booking_repo, ClientRepository $client_repo)
    {
        // Fetch db record for update, or create a new model for insert
        $booking_info = $this->findById($request->booking_id, $booking_repo);

        // Fetch db record for update, or create a new model for insert
        if ($request->client_id > 0) {
            $client = $client_repo->model()->findOrFail($request->client_id);
            $user = User::find($client->client_id);
        } else {
            $client = $client_repo->model();
            $user = new User();
        }

        $request->validate([
            'name' => 'required',
            'mobile' => ['required', 'digits:10'],
            // 'gender' => 'required',
            'email' => 'nullable|email|unique:users,email,' . $request->client_id,
        ], [
            'mobile.digits' => 'The mobile number must be exactly 10 digits.',
        ]);

        $request->merge([
            'mobile' => '+91'.$request->mobile,
        ]);

        if ($request->client_id > 0) {
            if ($user->mobile != $request->mobile) {
                if (User::where('mobile', $request->mobile)->exists()) {
                    $data = [
                        "message" => "The given data was invalid.",
                        "errors" => [
                            "mobile" => [
                                "Mobile number already exist."
                            ]
                        ]
                    ];

                    return response()->json($data, 422);
                }
            }
        } else {
            if (User::where('mobile', $request->mobile)->exists()) {
                $data = [
                    "message" => "The given data was invalid.",
                    "errors" => [
                        "mobile" => [
                            "Mobile number already exist."
                        ]
                    ]
                ];

                return response()->json($data, 422);
            }
        }



        // Start database transaction
        DB::transaction(function () use ($request, $booking_info, $client_repo, $client, $user) {
            $client_info = $this->saveClientFromRequest($request, $client_repo, $client, $user);

            // Create new booking or update existing booking with client meta data
            $this->createOrUpdateBooking($booking_info, $client_info->client_id);
        });

        return $this->sendBookingResponseJson($booking_info, 'edit', [
            'all_db_clients' => $this->getClientsForSelect2(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function sendBookingResponseJson($booking, $tab_name, $data = [])
    {
        // Load booking services, beauticians and slots
        $booking->loadEssentialsForVueJs();

        $vue_data = [
            'vue_details_tab_edit' => array_merge($data, [
                'booking_id' => $booking->booking_id,
                'booking_info' => $booking,
                'urls' => $this->getUrlsForVue($booking->booking_id, $tab_name),
            ]),
        ];

        return response()->json([
            'vue_data' => $vue_data,
            'js_notifications' => ['success' => 'Booking details updated'],
            'url_history_push' => url_history(route('tab.salon.booking.details', [
                'booking_id' => $booking->booking_id,
                'tab_name' => $tab_name,
            ])),
        ]);
    }

    private function saveClientFromRequest(Request $request, ClientRepository $client_repo, Client $client, User $user)
    {
        $user->name = $request->name;
        $user->email = (string) $request->email;
        $user->date_of_birth = $request->date_of_birth;
        $user->mobile = $request->mobile;
        $user->telephone = (string) $request->telephone;
        $user->address = $request->address;
        $user->city = (string) $request->city;
        $user->state = (string) $request->state;
        $user->zip = (string) $request->zip;
        $user->gender = $request->gender;

        return $client_repo->saveClientAndUser($client, $user);
    }

    private function createOrUpdateBooking(Booking $booking, $client_id)
    {
        $client_info = Client::join('users', 'clients.client_id', '=', 'users.id')
            ->select(
                'clients.client_id',
                'users.name',
                'users.mobile',
                'users.telephone',
                'users.email'
            )
            ->where('clients.client_id', $client_id)
            ->firstOrFail();

        if (empty($booking->booking_id)) {
            $booking->booking_source = Booking::BOOKING_SOURCE_WALK_IN;
        }

        $booking->client_id = $client_id;
        $booking->client_name = $client_info->name;
        $booking->client_mobile = $client_info->mobile;
        $booking->client_telephone = $client_info->telephone;
        $booking->client_email = $client_info->email;

        $booking->save();

        return $booking;
    }

    private function findById($id, \App\Repositories\Base\BaseRepository $repo)
    {
        return ($id > 0) ? $repo->model()->findOrFail($id) : $repo->model();
    }

    private function getClientsForSelect2()
    {
        return Client::join('users', 'clients.client_id', '=', 'users.id')
            ->select('clients.client_id', 'users.name', 'mobile')
            ->get()
            ->toArray();
    }

    private function getItemsForSelect2()
    {
        $db_items = StockItem::where('is_active', 1)
            ->where('is_sales_item', 1)
            ->leftJoin('stock_item_prices', 'stock_items.item_id', '=', 'stock_item_prices.item_id')
            ->select('stock_items.*', 'stock_item_prices.price_id', 'stock_item_prices.cost_price', 'stock_item_prices.quantity')
            ->orderBy('stock_items.item_id', 'desc')
            ->get();

        $batchesByCode = DB::table('batch_no')
            ->whereIn('item_code', $db_items->pluck('item_code')->filter()->unique()->values())
            ->get()
            ->groupBy('item_code');

        $select2_data = [];
        foreach ($db_items as $row) {
            $batches = $batchesByCode->get($row->item_code, collect());
            if ($batches->isNotEmpty()) {
                foreach ($batches as $no) {
                    $select2_data[] = [
                        'item_id' => $row->item_id,
                        'item_name' => $row->item_name,
                        'price_id' => $row->price_id,
                        'price' => $row->price,
                        'barcode' => $row->barcode,
                        'quantity' => $row->quantity,
                        'batch_no' => $no->batch_no,
                        'expiry_date' => $no->expiry_date,
                    ];
                }
            } else {
                $select2_data[] = [
                    'item_id' => $row->item_id,
                    'item_name' => $row->item_name,
                    'price_id' => $row->price_id,
                    'price' => $row->price,
                    'quantity' => $row->quantity,
                    'barcode' => $row->barcode,
                    'batch_no' => '',
                    'expiry_date' => '',
                ];
            }
        }

        return $select2_data;
    }

    public function getAllitems(Request $request)
    {
        $parentId = (int) $request->input('id', 0);
        $name = trim((string) $request->input('name', ''));

        $groupsQuery = StockItemGroup::query()->where('is_active', 1);

        if ($parentId > 0) {
            // Browse: child groups under selected top-level group
            $groupsQuery->where('parent_id', $parentId);
        } elseif ($name !== '') {
            // Search: groups that contain matching in-stock sales items
            $groupIds = StockItem::query()
                ->where('is_active', 1)
                ->where('is_sales_item', 1)
                ->availableForSale()
                ->where(function ($query) use ($name) {
                    $query->where('item_name', 'like', '%'.$name.'%')
                        ->orWhere('item_code', 'like', '%'.$name.'%');
                })
                ->distinct()
                ->pluck('item_group_id');

            if ($groupIds->isEmpty()) {
                return [];
            }

            $groupsQuery->whereIn('item_group_id', $groupIds);
        } else {
            return [];
        }

        $itemscat = $groupsQuery->orderBy('group_name')->get();
        if ($itemscat->isEmpty()) {
            return [];
        }

        $itemsQuery = StockItem::query()
            ->with(['itemPrice', 'stockUom'])
            ->whereIn('item_group_id', $itemscat->pluck('item_group_id'))
            ->where('is_active', 1)
            ->where('is_sales_item', 1)
            ->availableForSale()
            ->select(
                'item_id',
                'item_group_id',
                'item_name',
                'item_name_ar',
                'item_code',
                'current_stock',
                'selling_price',
                'pack_size',
                'stock_uom_id'
            )
            ->orderBy('item_name');

        if ($name !== '') {
            $itemsQuery->where(function ($query) use ($name) {
                $query->where('item_name', 'like', '%'.$name.'%')
                    ->orWhere('item_code', 'like', '%'.$name.'%');
            });
        }

        $itemsByGroup = $itemsQuery->get()->groupBy('item_group_id');

        foreach ($itemscat as $group) {
            $group->items = $itemsByGroup->get($group->item_group_id, collect())->map(function ($item) {
                $price = $item->itemPrice;
                $itemPricePayload = [
                    'price_id' => $price->price_id ?? null,
                    'item_id' => $item->item_id,
                    // Always expose warehouse stock — stock_item_prices.quantity is often stale/0
                    'quantity' => round((float) $item->current_stock, 3),
                    'selling_price' => $price->selling_price ?? $item->selling_price,
                    'cost_price' => $price->cost_price ?? null,
                ];

                // Keep both relation JSON key and explicit attribute for the Vue modal
                $item->setAttribute('item_price', $itemPricePayload);
                $item->setAttribute('uom_name', $item->stockUom->uom_name ?? '');
                if ($item->selling_price === null && $itemPricePayload['selling_price'] !== null) {
                    $item->selling_price = $itemPricePayload['selling_price'];
                }

                return $item;
            })->values();
        }

        // Hide empty groups after stock/search filters
        return $itemscat->filter(function ($group) {
            return collect($group->items)->isNotEmpty();
        })->values();
    }

    public function getAllServices(Request $request)
    {
        $serviceType = ServiceTypeManagement::where('service_type_name', 'salon')->first();
        if (! $serviceType) {
            return [];
        }

        $parentId = (int) $request->input('id', 0);
        $name = trim((string) $request->input('name', ''));
        $serviceTypeId = $serviceType->service_type_management_id;

        // Categories that have active salon-priced services (optionally matching search)
        $categoryIdsQuery = Service::query()
            ->join('price_services', 'price_services.service_id', '=', 'services.service_id')
            ->where('services.is_active', '1')
            ->where('price_services.service_type_id', $serviceTypeId)
            ->when($name !== '', function ($query) use ($name) {
                $query->where('services.name', 'like', '%'.$name.'%');
            })
            ->distinct()
            ->pluck('services.category_id');

        if ($categoryIdsQuery->isEmpty()) {
            return [];
        }

        $categoriesQuery = ServiceCategory::query()
            ->select('category_id', 'parent_id', 'name')
            ->whereIn('category_id', $categoryIdsQuery)
            ->where('is_active', 1);

        if ($parentId > 0) {
            $categoriesQuery->where('parent_id', $parentId);
        }

        $categories = $categoriesQuery->orderBy('name')->get();
        if ($categories->isEmpty()) {
            return [];
        }

        $servicesQuery = Service::query()
            ->with(['category.parent'])
            ->join('price_services', 'price_services.service_id', '=', 'services.service_id')
            ->whereIn('services.category_id', $categories->pluck('category_id'))
            ->where('services.is_active', '1')
            ->where('price_services.service_type_id', $serviceTypeId)
            ->when($name !== '', function ($query) use ($name) {
                $query->where('services.name', 'like', '%'.$name.'%');
            })
            ->select(
                'services.service_id',
                'services.category_id',
                'services.name',
                'services.duration_minutes',
                'services.service_base_price',
                'services.service_base_total',
                'price_services.price',
                'price_services.duration',
                'price_services.service_type_id'
            )
            ->orderBy('services.name');

        $servicesByCategory = $servicesQuery->get()->groupBy('category_id');

        foreach ($categories as $category) {
            $category->setAttribute('services', $servicesByCategory->get($category->category_id, collect())->values());
        }

        return $categories->filter(function ($category) {
            return collect($category->services)->isNotEmpty();
        })->values();
    }

    public function getAllQuickServices(Request $request)
    {
        $serviceType = ServiceTypeManagement::where('service_type_name', 'salon')->first();
        if (! $serviceType) {
            return [];
        }

        $beauticianId = (int) $request->input('beautician_id', 0);
        $parentId = (int) $request->input('catid', 0);
        $name = trim((string) $request->input('name', ''));
        $serviceTypeId = $serviceType->service_type_management_id;

        $servicesQuery = Service::query()
            ->with(['category.parent'])
            ->join('price_services', 'price_services.service_id', '=', 'services.service_id')
            ->where('services.is_active', '1')
            ->where('price_services.service_type_id', $serviceTypeId)
            ->when($beauticianId > 0, function ($query) use ($beauticianId) {
                $query->whereExists(function ($exists) use ($beauticianId) {
                    $exists->select(DB::raw(1))
                        ->from('beautician_services')
                        ->whereColumn('beautician_services.service_id', 'services.service_id')
                        ->where('beautician_services.beautician_id', $beauticianId);
                });
            })
            ->when($name !== '', function ($query) use ($name) {
                $query->where('services.name', 'like', '%'.$name.'%');
            })
            ->select(
                'services.service_id',
                'services.category_id',
                'services.name',
                'services.duration_minutes',
                'services.service_base_price',
                'services.service_base_total',
                'price_services.price',
                'price_services.duration',
                'price_services.service_type_id'
            )
            ->orderBy('services.name');

        $services = $servicesQuery->get();
        if ($services->isEmpty()) {
            return [];
        }

        $categoriesQuery = ServiceCategory::query()
            ->select('category_id', 'parent_id', 'name')
            ->whereIn('category_id', $services->pluck('category_id')->unique())
            ->where('is_active', 1)
            ->when($parentId > 0, function ($query) use ($parentId) {
                $query->where('parent_id', $parentId);
            })
            ->orderBy('name');

        $categories = $categoriesQuery->get();
        if ($categories->isEmpty()) {
            return [];
        }

        $allowedCategoryIds = $categories->pluck('category_id');
        $servicesByCategory = $services
            ->filter(function ($service) use ($allowedCategoryIds) {
                return $allowedCategoryIds->contains($service->category_id);
            })
            ->groupBy('category_id');

        foreach ($categories as $category) {
            $category->setAttribute('services', $servicesByCategory->get($category->category_id, collect())->values());
        }

        return $categories->filter(function ($category) {
            return collect($category->services)->isNotEmpty();
        })->values();
    }

    public function getBeauticiansForQuickService(Request $request)
    {
        $date = $request->input('date');

        $beauticians = Beautician::activeListedQuery()
            ->select('beauticians.beautician_id', 'beauticians.beautician_code', 'users.name', 'users.username', 'users.mobile')
            ->orderBy('users.name')
            ->get();

        if ($date) {
            $formattedDate = date('Y-m-d', strtotime($date));
            // Only exclude full-day leave (half-day staff remain selectable)
            $beauticiansOnFullLeave = DB::table('leave_managements')
                ->where('leave_date', $formattedDate)
                ->where('leave_status', 'approved')
                ->where('leave_type', \App\Models\LeaveManagement::LEAVE_TYPE_FULL)
                ->pluck('beautician_id')
                ->all();

            if (! empty($beauticiansOnFullLeave)) {
                $beauticians = $beauticians->reject(function ($beautician) use ($beauticiansOnFullLeave) {
                    return in_array($beautician->beautician_id, $beauticiansOnFullLeave, true);
                })->values();
            }
        }

        return response()->json($beauticians->map(function ($beautician) {
            return [
                'id' => $beautician->beautician_id,
                'text' => $beautician->name.' ('.$beautician->beautician_code.')',
                'beautician_id' => $beautician->beautician_id,
                'beautician_code' => $beautician->beautician_code,
                'name' => $beautician->name,
            ];
        })->values());
    }


    private function getServicesForSelect2()
    {
        $db_services = Service::where('is_active', 1)
            ->orderBy('allow_online_booking', 'desc')
            ->orderBy('display_order')
            ->orderBy('name')
            ->orderBy('service_id')
            ->get(['service_id', 'name', 'service_base_price', 'service_base_total', 'duration_minutes']);

        return $db_services->map(function ($row) {
            return [
                'service_id' => $row->service_id,
                'name' => $row->name,
                'service_base_price' => $row->service_base_price,
                'service_base_total' => $row->service_base_total,
                'duration_minutes' => $row->duration_minutes,
            ];
        })->all();
    }

    private function getAllServiceCategory()
    {
        return Cache::remember('salon.booking.service_categories', 300, function () {
            return ServiceCategory::with(['webChildren' => function ($query) {
                $query->select('category_id', 'parent_id', 'name');
            }])
                ->whereHas('webChildren')
                ->where('is_active', 1)
                ->whereNull('parent_id')
                ->select('category_id', 'parent_id', 'name')
                ->get();
        });
    }

    private function getItemGroupes()
    {
        return Cache::remember('salon.booking.item_groups', 300, function () {
            return StockItemGroup::whereNull('parent_id')
                ->where('is_active', 1)
                ->where('is_show_website', 1)
                ->orderBy('item_group_id')
                ->get();
        });
    }

    private function getUrlsForVue($booking_id, $tab_name)
    {
        return [
            'button_cancel_url' => route('tab.salon.booking.list'),
            'form_save_url' => route('tab.salon.booking.save', ['tab_name' => $tab_name]),
            'item_form_save_url' => route('salon.booking.item.save'),
            'item_form_update_url' => route('salon.booking.item.update'),
            'item_form_delete_url' => route('salon.booking.item.delete'),
            'form_save_payment_url' => route('salon.booking.payment.save'),
            'form_save_split_payment_url' => route('salon.booking.payment.split.save'),
            'form_rebalance_payment_url' => route('salon.booking.payment.rebalance'),
            'form_delete_payment_url' => route('salon.booking.payment.delete'),
            'form_update_payment_mode_url' => route('salon.booking.payment.mode.update'),
            'discount_save_url' => route('salon.booking.discount.save'),
            'form_final_update_url' => route('salon.booking.final.update'),
            'form_cancel_booking_url' => route('salon.booking.cancel.order'),
            'booking_invoice_url' => $booking_id ? route('salon.booking.invoice', ['id' => $booking_id]) : null,
            'booking_invoice_gift_url' => $booking_id ? route('salon.booking.invoice', ['id' => $booking_id, 'gift' => 'gift']) : null,
            'date_form_update_url' => route('salon.booking.date.update'),
            'booking_client_update_url' => route('salon.booking.client.save'),
            'booking_notify_whatsapp_url' => route('salon.booking.notify.whatsapp'),
        ];
    }
}
