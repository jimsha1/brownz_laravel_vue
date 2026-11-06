<?php

namespace App\Http\Controllers\Salon\BookingDetails;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Service\BookingCommissionSyncService;
use App\Service\BookingEditGuard;
use App\Models\Beautician;
use App\Models\Booking;
use App\Models\BookingService;
use App\Models\BookingItem;
use App\Models\StockItem;
use App\Models\Service;
use App\Models\SplitServiceAmount;
use App\Models\StockItemPrice;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\LeaveManagement;
use App\Repositories\BookingRepository;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Null_;

use function Laravel\Prompts\alert;

class SaveBookingServiceController extends Controller
{
    protected BookingCommissionSyncService $commissionSync;

    public function __construct(BookingCommissionSyncService $commissionSync)
    {
        $this->commissionSync = $commissionSync;
    }

    private function guardFinalizedEdit(Request $request, ?Booking $booking)
    {
        $denied = app(BookingEditGuard::class)->authorize($request, $booking);
        if ($denied) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException($denied);
        }
    }

    public function quickServiceItem(Request $request, BookingRepository $booking_repo)
    {
        // dd($request->all());
        $booking_info = $booking_repo->hunter()->findBooking($request->booking_id);

        try {
            DB::transaction(function () use ($request, $booking_info) {

                if ($request->is_update == 1 && $request->line_item_type === 'service') {
                    $this->update($request);
                    return response()->json([
                        'js_notifications' => ['success' => 'succ'],
                    ]);
                }
                if ($request->line_item_type === 'service' && $request->is_update != 1) {
                    $this->createQuickBookingService($booking_info, $request->service_ids, $request->all());
                }
            });

            // Refresh booking to get latest data after transaction
            $booking_info->refresh();
            
            // Load relationships needed for resetBookingAmount
            $booking_info->load(['bookingOnlyServices', 'bookingItems']);
            
            // Update booking_total in bookings table
            $booking_info->resetBookingAmount();

            $booking_info->loadEssentialsForVueJs();

            return response()->json([
                'booking_info' => $booking_info,
                'js_notifications' => ['success' => 'Booking item saved'],
            ]);
        } catch (\Exception $e) {
            Log::error('Quick Service Booking Error: ' . $e->getMessage());
            return response()->json([
                'js_notifications' => ['error' => $e->getMessage()],
            ], 400);
        }
    }

    public function quickProductItem(Request $request, BookingRepository $booking_repo)
    {
        $booking_info = $booking_repo->hunter()->findBooking($request->booking_id);

        // Beautician is optional for product sales
        $beauticianId = (!empty($request->beautician_id) && (int) $request->beautician_id > 0)
            ? $request->beautician_id
            : null;

        $priceIds = array_values(array_filter((array) $request->price_ids));
        if (empty($priceIds)) {
            return response()->json([
                'js_notifications' => ['error' => 'Please select at least one product.'],
            ], 400);
        }

        try {
            DB::transaction(function () use ($booking_info, $priceIds, $beauticianId) {
                $this->createQuickBookingItems($booking_info, $priceIds, $beauticianId);
            });

            $booking_info->refresh();
            $booking_info->load(['bookingOnlyServices', 'bookingItems']);
            $booking_info->resetBookingAmount();
            $booking_info->loadEssentialsForVueJs();

            return response()->json([
                'booking_info' => $booking_info,
                'js_notifications' => ['success' => 'Booking products saved'],
            ]);
        } catch (\Exception $e) {
            Log::error('Quick Product Booking Error: ' . $e->getMessage());
            return response()->json([
                'js_notifications' => ['error' => $e->getMessage()],
            ], 400);
        }
    }
    public function __invoke(Request $request, BookingRepository $booking_repo)
    {

        // dd($request->all());

        $booking_info = $booking_repo->hunter()->findBooking($request->booking_id);

        $this->guardFinalizedEdit($request, $booking_info);

        $BookingItem = BookingItem::where('booking_id', $booking_info->booking_id)
            ->where('item_id', $request->item_id)
            ->get();


        if (count($BookingItem) > 0) {
            return response()->json([
                'js_notifications' => ['warning' => 'item already exists'],
            ]);
        }

        try {
            DB::transaction(function () use ($request, $booking_info) {

                if ($request->is_update == 1 && $request->line_item_type === 'service') {
                    $this->update($request);
                    return response()->json([
                        'js_notifications' => ['success' => 'succ'],
                    ]);
                }
                if ($request->line_item_type === 'service' && $request->is_update != 1) {

                    return $this->createBookingService($booking_info, $request->service_id, $request->all());
                } elseif ($request->line_item_type === 'item') {

                    return $this->createBookingItem($booking_info, $request->price_id, $request->quantity, $request->beautician_id);
                }
            });
        } catch (\Exception $e) {
            return response()->json([
                'js_notifications' => ['error' => $e->getMessage()],
            ], 400);
        }

        // Load booking services, beauticians and slots
        $booking_info->loadEssentialsForVueJs();

        $booking_info->resetBookingAmount();

        return response()->json([
            'booking_info' => $booking_info,
            'needs_update' => (bool) $booking_info->booking_code,
            'js_notifications' => ['success' => 'Booking item saved'],
        ]);
    }



    public function update(Request $request)
    {
        Log::channel('reschedule_log')->info("==============START==============");

        Log::channel('reschedule_log')->info("Inside Update");
        $booking_info = Booking::findOrFail($request->booking_id);
        $this->guardFinalizedEdit($request, $booking_info);
        // dd($booking_info);

        Log::channel('reschedule_log')->info("Booking Info");
        Log::channel('reschedule_log')->info($booking_info);

        $beatician = User::where('id', $request->beautician_id)->firstOrFail();

        // $booking_info = Booking::findOrFail($request->booking_id);
        $booking_item = BookingService::where('service_id', $request->service_id)
            ->where('booking_service_id', $request->booking_service_id)
            ->firstOrFail();
        $booking_item->beautician_id = $request->beautician_id;
        $booking_item->slot_start = $request->slot_start;
        $booking_item->shift_id = $beatician->shift_id;


        // $request->appointment_date = str_replace('/', '-', $request->appointment_date);
        // $request->appointment_date = date("Y-m-d", strtotime($request->appointment_date));
        // $booking_item->appointment_date = $request->appointment_date;

        // Format the appointment date
        $request->appointment_date = str_replace('/', '-', $request->appointment_date);
        $appointmentDate = date("Y-m-d", strtotime($request->appointment_date));
        $booking_item->appointment_date = $appointmentDate;

        // dd($request->appointment_date);

        // Compare with current date and set is_booking
        $currentDate = date("Y-m-d");
        $booking_info->is_booking = ($appointmentDate > $currentDate) ? 1 : 0;

        if ($booking_item->status == BookingService::STATUS_REJECTED) {
            $booking_item->status = BookingService::STATUS_REOPENED;
            $booking_item->rejected_by = NULL;
            $booking_item->rejected_at = NULL;
            $booking_item->service_finished_at = NULL;
            $booking_item->service_finished_by = NULL;
            $booking_item->service_started_at = NULL;
            $booking_item->service_started_by = NULL;
        } elseif ($booking_item->status === BookingService::STATUS_COMPLETED
            && (int) $booking_item->beautician_id !== (int) $request->beautician_id) {
            $booking_item->service_started_by = $request->beautician_id;
            $booking_item->service_finished_by = $request->beautician_id;
        }

        $booking_item->save();
        $booking_info->save();

        if ($booking_info->booking_code) {
            $this->commissionSync->invalidateServiceCommission($booking_item);
        }

        // Load booking services, beauticians and slots
        $booking_info->loadEssentialsForVueJs();

        Log::channel('reschedule_log')->info("Success");

        return response()->json([
            'status' => 'success',
            'booking_info' => $booking_info,
            'needs_update' => (bool) $booking_info->booking_code,
            'js_notifications' => ['success' => __('Beautician and time slot updated. Click Update to apply changes.')],
        ]);
        // } else {
        //     Log::channel('reschedule_log')->info("Failed");

        //     return response()->json([
        //         'status' => 'failed',
        //         'booking_info' => $booking_info,
        //         'js_notifications' => ['warning' => 'Failed to update ERP. Please try again later.'],
        //     ]);
        // }

    }



    public function updateDate(Request $request, BookingRepository $booking_repo)
    {

        // dd($request->all());
        $booking_info = Booking::findOrFail($request->booking_id);

        $booking_item = BookingService::where('booking_id', $request->booking_id)
            ->where('service_id', $request->service_id)
            ->firstOrFail();

        $booking_item->beautician_id = NULL;
        $booking_item->slot_start = NULL;
        $booking_item->appointment_date = voco_calendar_to_mysql_date($request->booking_date, null);

        if ($booking_item->status == BookingService::STATUS_REJECTED) {
            $booking_item->status = BookingService::STATUS_REOPENED;
            $booking_item->rejected_by = NULL;
            $booking_item->rejected_at = NULL;
            $booking_item->service_finished_at = NULL;
            $booking_item->service_finished_by = NULL;
            $booking_item->service_started_at = NULL;
            $booking_item->service_started_by = NULL;
        }
        $booking_item->save();

        // Load booking services, beauticians and slots
        $booking_info->loadEssentialsForVueJs(voco_calendar_to_mysql_date($request->booking_date, null));

        return response()->json([
            'booking_info' => $booking_info,
            'js_notifications' => ['success' => 'Slot time updated'],
        ]);
    }


    private function createQuickBookingItems($booking_info, $price_ids, $beautician_id)
    {
        $added = 0;

        foreach ($price_ids as $price_id) {
            $item_info = StockItem::join('stock_item_prices', 'stock_items.item_id', '=', 'stock_item_prices.item_id')
                ->where('stock_item_prices.price_id', $price_id)
                ->first();

            if (!$item_info) {
                continue;
            }

            $requiredPack = \App\Models\StockItem::normalizePackSize($item_info->pack_size ?? 1);
            if ((float) $item_info->current_stock + 0.0001 < $requiredPack) {
                throw new \Exception('Insufficient stock for item: ' . $item_info->item_name);
            }

            $exists = BookingItem::where('booking_id', $booking_info->booking_id)
                ->where('item_id', $item_info->item_id)
                ->exists();

            if ($exists) {
                continue;
            }

            $this->createBookingItem($booking_info, $price_id, 1, $beautician_id);
            $added++;
        }

        if ($added === 0) {
            throw new \Exception('No new products were added. Selected products may already exist on this booking.');
        }
    }

    private function createQuickBookingService($booking_info, $service_ids, $request)
    {
        $flag_global_discount = "";
        $global_discount_persentage = 0;
        $percentToGet = 0;
        $percent = 0;

        $currentDate = Carbon::now()->format('Y-m-d');
        $appointmentDate = Carbon::parse($request->appointment_date ?? $request['appointment_date'])->format('Y-m-d');

        // Check if beautician is on approved leave for the selected date
        // Handle both beautician_id formats (from frontend: selectedBeautician or beautician_id)
        $beauticianId = $request->beautician_id ?? $request['beautician_id'] ?? null;
        
        if (!empty($beauticianId)) {
            $beaut_leave = LeaveManagement::where('beautician_id', $beauticianId)
                ->where('leave_date', $appointmentDate)
                ->where('leave_status', 'approved')
                ->first();
            
            if ($beaut_leave) {
                // Beautician is on leave - prevent booking
                throw new \Exception('Cannot book service: Beautician is on leave for the selected date.');
            }
        }

        // Update booking status if appointment date is in the future
        $booking = Booking::where('booking_id', $booking_info->booking_id)->first();
        if ($appointmentDate > $currentDate) {
            $booking->is_booking = 1;
            $booking->save();
        }

        // Global discount calculation (outside the loop since it applies across booking)
        $BookingItem_discount = BookingItem::where('booking_id', $booking_info->booking_id)
            ->where('discount_percent', '>', 0)
            ->get()->toArray();

        $Booking_service_discount = BookingService::where('booking_id', $booking_info->booking_id)
            ->where('discount_percent', '>', 0)
            ->get()->toArray();

        if (count($Booking_service_discount) > 0 || count($BookingItem_discount) > 0) {
            if (count($Booking_service_discount) > 0) {
                $global_discount_persentage = $Booking_service_discount[0]['show_discount_persent'];
            } else {
                $global_discount_persentage = $BookingItem_discount[0]['show_discount_persent'];
            }
        }


        // dd($request);
        // Loop through each service ID
        foreach ($request['service_ids'] as $service_id) {
            $percentToGet = 0;
            $percent = 0;

            $service_info = Service::with('category')->findOrFail($service_id);

            $beautician = DB::table('users')->where('id', $request['beautician_id'])->first();
            // dd($service_info->service_base_price);
            if ($global_discount_persentage > 0) {
                $flag_global_discount = $global_discount_persentage;
                $total_price = $service_info->service_base_price;
                $percentToGet = $global_discount_persentage;
                $percentInDecimal = $percentToGet / 100;
                $percent = $percentInDecimal * $total_price;
            }

            $booking_service = new BookingService();
            $booking_service->discount_percent = $percentToGet;
            $booking_service->show_discount_persent = $percentToGet;
            $booking_service->discount_amount = $percent;

            $booking_service->booking_id = $booking_info->booking_id;
            $booking_service->service_id = $service_info->service_id;
            $booking_service->appointment_date = $request['appointment_date'];

            // Master record info
            $booking_service->category_name = $service_info->category->name;
            $booking_service->service_name = $service_info->name;
            $booking_service->duration_minutes = $service_info->duration_minutes;
            $booking_service->status = 'draft';
            $booking_service->slot_start = $request['slot_start'];
            $booking_service->beautician_id = $request['beautician_id'];
            $booking_service->shift_id = $beautician->shift_id;
            $booking_service->quantity = $request['quantity'];

            $booking_service->service_booking_price = $service_info->service_base_price;
            $booking_service->tax_percent = $service_info->tax_percent;

            if ($flag_global_discount > 0) {
                $total_price = $service_info->service_base_price - $percent;
                $booking_service->reCalculateLineTotalExistsDiscount($booking_info, $total_price);
            } else {
                $booking_service->reCalculateLineTotal($booking_info);
            }

            $booking_service->start_date = $service_info->start_date;
            $booking_service->end_date = $service_info->end_date;
            $booking_service->commission_percentage = $service_info->commission_percentage;
            $booking_service->commission_status = $service_info->commission_status;

            $booking_service->save();
            app(\App\Service\ServiceUsedProductService::class)->ensureSnapshot($booking_service);
        }

        // $this->updateBookingPrice1($request);
    }

    public function updateBookingPrice1($request)
    {

        $beautician = DB::table('users')->where('id', $request['beautician_id'])->first();

        if (!$beautician || !$beautician->shift_id) {
            return ['error' => 'Beautician or shift ID not found.'];
        }

        $discount_item_array = BookingItem::where('booking_id', $request['booking_id'])
            ->where('discount_percent', '>', 0)
            ->get();

        $discount_service_array = BookingService::where('booking_id', $request['booking_id'])
            ->where('discount_percent', '>', 0)
            ->get();

        foreach ($request['service_ids'] as $service_id) {
            \Log::info('Looping service_id: ' . $service_id);
            // dd($beautician);
            $price_service = DB::table('price_services')
                ->select('price')
                ->where('service_id', $service_id)
                ->where('shift_id', $beautician->shift_id)
                ->where('service_type_id', 3)
                ->first();

            \Log::info('Price service result:', (array) $price_service);

            // dd($price_service); // should now work


            $price = $price_service ? $price_service->price : 0;

            $discount_amt = 0.00;
            $changed_price = $price;

            if (count($discount_item_array) > 0 || count($discount_service_array) > 0) {
                $discount_percent = count($discount_item_array) > 0
                    ? $discount_item_array[0]->discount_percent
                    : $discount_service_array[0]->discount_percent;

                $discount_amt = ($price / 100) * $discount_percent;
                $changed_price = $price - $discount_amt;
            }

            $tax = $changed_price * 15 / 100;

            DB::table('booking_services')
                ->where('booking_id', $request['booking_id'])
                ->where('service_id', $service_id)
                ->update([
                    'service_booking_price' => $price,
                    'tax_amount' => $tax,
                    'discount_amount' => $discount_amt,
                ]);
        }

        return ['success' => 'Price updated successfully.'];
    }

    private function createBookingService($booking_info, $service_id, $request)
    {
        // dd($service_id);
        $Booking_service = array();
        $flag_global_discount = "";
        $global_discount_persentage = 0;
        $percentToGet = 0;
        $percent = 0;

        $currentDate = Carbon::now()->format('Y-m-d');
        $appointmentDate = Carbon::parse($request->appointment_date ?? $request['appointment_date'])->format('Y-m-d');

        // Update booking status if appointment date is in the future
        $booking = Booking::where('booking_id', $booking_info->booking_id)->first();
   
        if ($appointmentDate > $currentDate) {
            $booking->is_booking = 1;
            $booking->save();
        }


        $service_info = Service::with('category')->findOrFail($service_id);

        $Booking_service = BookingService::where('booking_id', $booking_info->booking_id)
            ->get()->toarray();
        //=================================== check global discount section ============================================
        $BookingItem_discount = BookingItem::where('booking_id', $booking_info->booking_id)
            ->where('discount_percent', '>', 0)
            ->get()->toarray();

        $Booking_service_discount = BookingService::where('booking_id', $booking_info->booking_id)
            ->where('discount_percent', '>', 0)
            ->get()->toarray();

        if (count($Booking_service_discount) > 0 || count($BookingItem_discount) > 0) {
            if (count($Booking_service_discount) > 0) {
                $global_discount_persentage = $Booking_service_discount[0]['show_discount_persent'];
            } else {
                $global_discount_persentage = $BookingItem_discount[0]['show_discount_persent'];
            }
        }
        if ($global_discount_persentage > 0) {
            $flag_global_discount = $global_discount_persentage;
            $total_price = $service_info->service_base_price;
            $percentToGet = $global_discount_persentage;
            $percentInDecimal = $percentToGet / 100;
            $percent = $percentInDecimal * $total_price;
        }
        //========================================end checking ================================================
        $booking_service = new BookingService();
        $booking_service->discount_percent = $percentToGet;
        $booking_service->show_discount_persent = $percentToGet;
        $booking_service->discount_amount = $percent;


        $booking_service->booking_id = $booking_info->booking_id;
        $booking_service->service_id = $service_info->service_id;

        // @TODO - get date from REQUEST
        $booking_service->appointment_date = $request['date'];

        /* Master record information */
        $booking_service->category_name = $service_info->category->name;
        // $booking_service->category_name_ar = $service_info->category->name_arabic;
        $booking_service->service_name = $service_info->name;
        // $booking_service->service_name_ar = $service_info->name_arabic;
        $booking_service->duration_minutes = $service_info->duration_minutes;
        $booking_service->status = 'draft';
        $booking_service->slot_start = $request['slot_start'];
        $booking_service->beautician_id = $request['beautician_id'];
        $booking_service->shift_id = $request['shift_id'];
        $booking_service->quantity = $request['quantity'];

        // Default price and tax percent
        $booking_service->service_booking_price = $service_info->service_base_price;
        $booking_service->tax_percent = $service_info->tax_percent;

        // Re-calculate discount, tax and line total
        if ($flag_global_discount > 0) {
            $total_price = $service_info->service_base_price - $percent;
            $booking_service->reCalculateLineTotalExistsDiscount($booking_info, $total_price);
        } else {
            $booking_service->reCalculateLineTotal($booking_info);
        }


        $booking_service->discount_percent = $percentToGet;
        $booking_service->show_discount_persent = $percentToGet;
        $booking_service->discount_amount = $percent;
        $booking_service->start_date = $service_info->start_date;
        $booking_service->end_date = $service_info->end_date;
        $booking_service->commission_percentage = $service_info->commission_percentage;
        $booking_service->commission_status = $service_info->commission_status;

        $booking_service->save();
        app(\App\Service\ServiceUsedProductService::class)->ensureSnapshot($booking_service);

        $this->updateBookingPrice($request);
    }

    private function createBookingItem($booking_info, $price_id, $quantity, $beautician_id)
    {

        $BookingItem = array();
        $flag_global_discount = "";
        $percentToGet = 0;
        $percent = 0;
        $global_discount_persentage = 0;
        //line_item_type
        // Alias price-table columns. Both tables have selling_price/cost_price, and a
        // plain join keeps stock_items.* so a 0 master price hides the real sale price.
        $item_info = StockItem::query()
            ->join('stock_item_prices', 'stock_items.item_id', '=', 'stock_item_prices.item_id')
            ->where('stock_item_prices.price_id', $price_id)
            ->select(
                'stock_items.*',
                'stock_item_prices.price_id',
                'stock_item_prices.selling_price as price_selling_price',
                'stock_item_prices.cost_price as price_cost_price'
            )
            ->first();

        $BookingItem = BookingItem::where('booking_id', $booking_info->booking_id)
            ->get()->toarray();

        //=================================== check global discount section ============================================
        $BookingItem_discount = BookingItem::where('booking_id', $booking_info->booking_id)
            ->where('discount_percent', '>', 0)
            ->get()->toarray();

        $Booking_service_discount = BookingService::where('booking_id', $booking_info->booking_id)
            ->where('discount_percent', '>', 0)
            ->get()->toarray();
        if (count($Booking_service_discount) > 0 || count($BookingItem_discount) > 0) {
            if (count($Booking_service_discount) > 0) {
                $global_discount_persentage = $Booking_service_discount[0]['show_discount_persent'];
            } else {
                $global_discount_persentage = $BookingItem_discount[0]['show_discount_persent'];
            }
        }
        $sellingPrice = (float) ($item_info->price_selling_price ?? 0);
        if ($sellingPrice <= 0) {
            $sellingPrice = (float) ($item_info->selling_price ?? 0);
        }
        $costPrice = (float) ($item_info->price_cost_price ?? 0);
        if ($costPrice <= 0) {
            $costPrice = (float) ($item_info->cost_price ?? 0);
        }

        if ($global_discount_persentage > 0) {
            $flag_global_discount = $global_discount_persentage;
            $total_price = $sellingPrice;
            $percentToGet = $global_discount_persentage;
            $percentInDecimal = $percentToGet / 100;
            $percent = $percentInDecimal * $total_price;
        }
        //========================================end checking ================================================



        $booking_item = new BookingItem();
        $booking_item->booking_id = $booking_info->booking_id;
        $booking_item->item_id = $item_info->item_id;
        $booking_item->price_id = $item_info->price_id;
        $booking_item->discount_amount = $percent;
        $booking_item->discount_percent = $flag_global_discount;
        /* Master record information */
        $booking_item->item_code = $item_info->item_code;
        $booking_item->item_name = $item_info->item_name;
        // $booking_item->quantity = $item_info->quantity;
        $booking_item->quantity = 1;
        $booking_item->status = 'deliverd';

        // Default price and tax percent
        $booking_item->cost_price = $costPrice;
        $booking_item->selling_price = $sellingPrice;
        $booking_item->tax_percent = $item_info->tax_percent;


        if ($flag_global_discount > 0) {
            $total_price = $booking_item->selling_price - $percent;
            $booking_item->reCalculateLineTotalExistsDiscount($booking_info, $total_price);
            $booking_item->discount_amount = $percent;
            $booking_item->discount_percent = $flag_global_discount;
        } else {
            $booking_item->reCalculateLineTotal($booking_info);
            // $BookingItem->discount_percent = 0;
        }

        // Beautician is optional for product sales
        $booking_item->beautician_id = (!empty($beautician_id) && (int) $beautician_id > 0)
            ? $beautician_id
            : null;

        // Stamp product commission settings from master (same pattern as services)
        $booking_item->start_date = $item_info->start_date;
        $booking_item->end_date = $item_info->end_date;
        $booking_item->commission_percentage = $item_info->commission_percentage;
        $booking_item->commission_status = $item_info->commission_status ?? 0;

        $stockItem = StockItem::findOrFail($item_info->item_id);
        $baseQty = $stockItem->baseQuantity(1);
        if ((float) $stockItem->current_stock + 0.0001 < $baseQty) {
            throw new \Exception('Out of Stock! Cannot add item: '.$item_info->item_name);
        }

        $booking_item->save();

        // Add stock movement in the item's stock unit (one sold pack)
        StockMovement::create([
            'item_id'    => $item_info->item_id,
            'booking_item_id'  => $booking_item->booking_item_id,
            'stock_out'  => $baseQty,
            'description'  => 'Item sold: ' . $item_info->item_name,
        ]);

        DB::table('stock_items')
            ->where('item_id', $item_info->item_id)
            ->update([
                'current_stock' => StockItem::sqlAdd('current_stock', -$baseQty),
            ]);

        DB::table('stock_item_prices')
            ->where('price_id', $item_info->price_id)
            ->update([
                'quantity' => StockItem::sqlAdd('quantity', -$baseQty),
            ]);
    }


    public function updateBookingPrice($request)
    {

        // dd($request);
        $users = DB::table('users')->where('id', $request['beautician_id'])->first();
        // dd($users->shift_id);
        $price_service = DB::table('price_services')
            ->select('price')
            ->where('service_id', '=', $request['service_id'])
            ->where('shift_id', '=', $users->shift_id)
            ->where('service_type_id', '=', 3)
            ->get()->toArray();

        $discount_item_array = BookingItem::where('booking_id', '=', $request['booking_id'])
            ->where('discount_percent', '>', 0)
            ->get();

        $discount_service_array = BookingService::where('booking_id', '=', $request['booking_id'])
            ->where('discount_percent', '>', 0)
            ->get();


        $price = isset($price_service[0]->price) ? $price_service[0]->price : 0;

        $discount_amt = 0.00;
        $changed_price = 0;
        if (count($discount_item_array) > 0 || count($discount_service_array) > 0) {
            if (count($discount_item_array) > 0) {
                $discount_percent = $discount_item_array['0']->discount_percent;
            } else {
                $discount_percent = $discount_service_array['0']->discount_percent;
            }

            $discount_amt = ($price / 100) * $discount_percent;
            $changed_price = $price - $discount_amt;
        }
        $tax = $changed_price * 18 / 100;
        $booking_res = DB::table('booking_services')
            ->where('booking_services.booking_service_id', '=', $request['service_id'])
            ->update([
                'service_booking_price' => $price,
                'tax_amount' => $tax,
                'discount_amount' => $discount_amt,
            ]);

        return ['success' => 'price  changed'];
    }


    public function filterProdcts(Request $request)
    {

        $stock_item = StockItem::join('stock_item_prices', 'stock_items.item_id', '=', 'stock_item_prices.item_id')
            ->where('item_name', 'LIKE', '%' . $request->search . '%')
            ->where('item_group_id', $request->cat_id)
            ->get();
        return $stock_item;
    }


    public function addQty(Request $request)
    {
        // Prepare booking item update data
        $data = [
            'quantity' => $request->qty,
            'tax_amount' => $request->tax_amount,
            'line_total' => $request->line_total,
        ];

        if (isset($request->discount_type) && $request->discount_type == 'individual') {
            $data['individual_discount_amt'] = $request->discount_amt;
        } else {
            $data['discount_amount'] = $request->discount_amt;
        }

        // Fetch the booking item
        $bookingItem = DB::table('booking_items')->where('booking_item_id', $request->item_id)->first();

        if (!$bookingItem) {
            return response()->json(['error' => 'Booking item not found'], 404);
        }

        $booking_info = Booking::find($bookingItem->booking_id);
        $this->guardFinalizedEdit($request, $booking_info);

        $oldQty = (float) $bookingItem->quantity;
        $newQty = (float) $request->qty;
        $qtyDiff = $newQty - $oldQty;

        // Booking quantity is in packs. Stock is stored in the stock unit.
        $stockItemModel = StockItem::find($bookingItem->item_id);
        $packSize = $stockItemModel ? $stockItemModel->packSize() : 1.0;
        $baseDiff = round($qtyDiff * $packSize, 3);

        $stockItem = DB::table('stock_items')->where('item_id', $bookingItem->item_id)->first();

        if ($baseDiff > 0 && $stockItem && ((float) $stockItem->current_stock - $baseDiff) < -0.0001) {
            $booking_info = Booking::findOrFail($request->booking_id);
            $booking_info->loadEssentialsForVueJs();

            return response()->json([
                'vue_data' => [
                    'vue_details_tab_edit' => [
                        'booking_info' => $booking_info,
                    ],
                ],
                'js_notifications' => ['error' => 'Out of Stock! Cannot update quantity.'],
            ]);
        }

        if ($baseDiff != 0) {
            DB::table('stock_items')
                ->where('item_id', $bookingItem->item_id)
                ->update([
                    'current_stock' => StockItem::sqlAdd('current_stock', -$baseDiff),
                ]);

            if (! empty($bookingItem->price_id)) {
                DB::table('stock_item_prices')
                    ->where('price_id', $bookingItem->price_id)
                    ->update([
                        'quantity' => StockItem::sqlAdd('quantity', -$baseDiff),
                    ]);
            }

            $stockMovement = StockMovement::where('booking_item_id', $bookingItem->booking_item_id)->first();
            if ($stockMovement) {
                $stockMovement->stock_out = max(0, (float) $stockMovement->stock_out + $baseDiff);
                $stockMovement->save();
            }
        }

        // Update booking_items
        DB::table('booking_items')
            ->where('booking_item_id', $request->item_id)
            ->update($data);

        // Reload booking info for VueJs and recalculate booking total
        $booking_info = Booking::findOrFail($request->booking_id);
        if ($booking_info->booking_code && ! empty($bookingItem->beautician_id)) {
            $itemModel = BookingItem::find($request->item_id);
            if ($itemModel) {
                $this->commissionSync->invalidateItemCommission($itemModel);
            }
        }
        $booking_info->load(['bookingOnlyServices', 'bookingItems']);
        $booking_info->resetBookingAmount();
        $booking_info->loadEssentialsForVueJs();

        return response()->json([
            'vue_data' => [
                'vue_details_tab_edit' => [
                    'booking_info' => $booking_info,
                ],
            ],
            'js_notifications' => ['info' => 'Quantity Updated Successfully.'],
        ]);
    }



    public function updateRefundQty(Request $request)
    {
        $refundedBookingId = $request->refunded_booking_id;
        $itemId = $request->item_id;
        $requestQty = abs((float) $request->qty);

        $refundedFrom = DB::table('booking_items')
            ->select('booking_item_id', 'quantity', 'refunded_qty')
            ->where('booking_id', '=', $refundedBookingId)
            ->where('item_id', '=', $itemId)
            ->first();

        if (! $refundedFrom) {
            return response()->json([
                'js_notifications' => ['warning' => 'Original sale item not found.'],
            ], 422);
        }

        $totalRefundedQty = (float) DB::table('booking_items')
            ->leftJoin('bookings', 'booking_items.booking_id', '=', 'bookings.booking_id')
            ->where('booking_items.refunded_booking_id', '=', $refundedBookingId)
            ->where('booking_items.item_id', '=', $itemId)
            ->where('booking_items.booking_item_id', '!=', $request->booking_item_id)
            ->where('bookings.booking_type', '!=', 'Cancel')
            ->sum('booking_items.quantity');

        $newRefundedQty = $totalRefundedQty + $requestQty;
        $isSuccess = false;

        if ((float) $refundedFrom->quantity >= $newRefundedQty && $requestQty > 0) {
            // Keep money columns positive (UNSIGNED)
            $lineTotal = abs((float) $request->line_total);
            $taxAmount = abs((float) $request->tax_amount);
            $discountAmt = abs((float) $request->discount_amt);

            if (isset($request->discount_type) && $request->discount_type == 'individual') {
                $data = [
                    'quantity' => $requestQty,
                    'tax_amount' => $taxAmount,
                    'line_total' => $lineTotal,
                    'individual_discount_amt' => $discountAmt,
                ];
            } else {
                $data = [
                    'quantity' => $requestQty,
                    'tax_amount' => $taxAmount,
                    'line_total' => $lineTotal,
                    'discount_amount' => $discountAmt,
                ];
            }

            DB::table('booking_items')
                ->where('booking_item_id', '=', $request->booking_item_id)
                ->update($data);

            DB::table('booking_items')
                ->where('booking_id', '=', $refundedBookingId)
                ->where('item_id', '=', $itemId)
                ->update([
                    'refunded_qty' => $newRefundedQty,
                    'status' => ((float) $refundedFrom->quantity <= $newRefundedQty)
                        ? BookingService::STATUS_REFUNDED
                        : 'deliverd',
                ]);

            $isSuccess = true;
        }

        $booking_info = Booking::findOrFail($request->booking_id);

        if ($isSuccess) {
            // Recalc refund booking_total from item/service lines (negative for refund docs)
            $itemSum = (float) BookingItem::where('booking_id', $booking_info->booking_id)->sum('line_total');
            $serviceSum = (float) BookingService::where('booking_id', $booking_info->booking_id)->sum('line_total');
            $booking_info->booking_total = round(($itemSum + $serviceSum) * -1, 2);
            $booking_info->save();
        }

        $booking_info->loadEssentialsForVueJs();

        return response()->json([
            'vue_data' => [
                'vue_details_tab_edit' => [
                    'booking_info' => $booking_info,
                ],
            ],
            'js_notifications' => $isSuccess
                ? ['info' => 'Quantity Updated..']
                : ['warning' => 'Quantity is greater than allowed.'],
        ]);
    }

    public function updateRefundServiceQty(Request $request)
    {
        $refundedBookingId = (int) $request->refunded_booking_id;
        $serviceId = (int) $request->service_id;
        $requestQty = abs((float) $request->qty);
        $bookingServiceId = (int) $request->booking_service_id;

        $refundedFrom = DB::table('booking_services')
            ->select('booking_service_id', 'quantity', 'refunded_qty', 'status')
            ->where('booking_id', '=', $refundedBookingId)
            ->where('service_id', '=', $serviceId)
            ->first();

        if (! $refundedFrom) {
            return response()->json([
                'js_notifications' => ['warning' => 'Original sale service not found.'],
            ], 422);
        }

        $totalRefundedQty = (float) DB::table('booking_services')
            ->leftJoin('bookings', 'booking_services.booking_id', '=', 'bookings.booking_id')
            ->where('booking_services.refunded_booking_id', '=', $refundedBookingId)
            ->where('booking_services.service_id', '=', $serviceId)
            ->where('booking_services.booking_service_id', '!=', $bookingServiceId)
            ->where('bookings.booking_type', '!=', 'Cancel')
            ->sum('booking_services.quantity');

        $newRefundedQty = $totalRefundedQty + $requestQty;
        $isSuccess = false;

        if ((float) $refundedFrom->quantity >= $newRefundedQty && $requestQty > 0) {
            $lineTotal = abs((float) $request->line_total);
            $taxAmount = abs((float) $request->tax_amount);
            $discountAmt = abs((float) $request->discount_amt);

            if (isset($request->discount_type) && $request->discount_type == 'individual') {
                $data = [
                    'quantity' => $requestQty,
                    'tax_amount' => $taxAmount,
                    'line_total' => $lineTotal,
                    'individual_discount_amt' => $discountAmt,
                ];
            } else {
                $data = [
                    'quantity' => $requestQty,
                    'tax_amount' => $taxAmount,
                    'line_total' => $lineTotal,
                    'discount_amount' => $discountAmt,
                ];
            }

            DB::table('booking_services')
                ->where('booking_service_id', '=', $bookingServiceId)
                ->update($data);

            $parentUpdate = ['refunded_qty' => $newRefundedQty];
            if ((float) $refundedFrom->quantity <= $newRefundedQty) {
                $parentUpdate['status'] = BookingService::STATUS_REFUNDED;
            } elseif ($refundedFrom->status === BookingService::STATUS_REFUNDED) {
                $parentUpdate['status'] = BookingService::STATUS_COMPLETED;
            }

            DB::table('booking_services')
                ->where('booking_id', '=', $refundedBookingId)
                ->where('service_id', '=', $serviceId)
                ->update($parentUpdate);

            $isSuccess = true;
        }

        $booking_info = Booking::findOrFail($request->booking_id);

        if ($isSuccess) {
            $itemSum = (float) BookingItem::where('booking_id', $booking_info->booking_id)->sum('line_total');
            $serviceSum = (float) BookingService::where('booking_id', $booking_info->booking_id)->sum('line_total');
            $booking_info->booking_total = round(($itemSum + $serviceSum) * -1, 2);
            $booking_info->save();
        }

        $booking_info->loadEssentialsForVueJs();

        return response()->json([
            'vue_data' => [
                'vue_details_tab_edit' => [
                    'booking_info' => $booking_info,
                ],
            ],
            'js_notifications' => $isSuccess
                ? ['info' => 'Quantity Updated..']
                : ['warning' => 'Quantity is greater than allowed.'],
        ]);
    }

    public function updateServicePrice(Request $request)
    {
        $service = BookingService::findOrFail($request->booking_service_id);
        $booking_info = Booking::find($service->booking_id);
        $this->guardFinalizedEdit($request, $booking_info);

        // Update price and tax percent
        $service->service_booking_price = $request->service_booking_price;
        $taxPercent = $request->tax_percent ?? 0.00;

        // Update tax amount
        $service->tax_amount = ($service->service_booking_price * $taxPercent) / 100;

        // Fetch discount amounts
        $discountAmount = $service->discount_amount ?? 0;
        $individualDiscountAmount = $service->individual_discount_amt ?? 0;

        // Calculate line total
        $service->line_total = ($service->service_booking_price - $discountAmount - $individualDiscountAmount) + $service->tax_amount;

        // Save the changes
        $service->save();

        if ($booking_info && $booking_info->booking_code) {
            $this->commissionSync->invalidateServiceCommission($service);
            $booking_info->load(['bookingOnlyServices', 'bookingItems']);
            $booking_info->resetBookingAmount();
            $booking_info->loadEssentialsForVueJs();
        }

        // Return updated data
        return response()->json([
            'booking_service_id' => $service->booking_service_id,
            'service_booking_price' => $service->service_booking_price,
            'tax_amount' => $service->tax_amount,
            'line_total' => $service->line_total,
            'booking_info' => $booking_info,
            'needs_update' => $booking_info && $booking_info->booking_code ? true : false,
            'message' => __('Price and Tax updated successfully.'),
        ]);
    }


    // public function updateServiceQuantity(Request $request)
    // {
    //     // dd($request->all());
    //     $service = BookingService::findOrFail($request->booking_service_id);

    //     // Update quantity
    //     $service->quantity = $request->quantity;

    //     // Calculate subtotal (before tax and discount)
    //     $subtotal = $request->service_booking_price * $request->quantity;

    //     // $service->service_booking_price = $subtotal;

    //     $taxPercent = $request->tax_percent ?? 0.00;

    //     // Calculate tax amount
    //     $service->tax_amount = ($subtotal * $taxPercent) / 100;

    //     $discountAmount = $service->discount_amount ?? 0;
    //     $individualDiscountAmount = $service->individual_discount_amt ?? 0;

    //     $totalDiscount = ($discountAmount + $individualDiscountAmount) * $request->quantity;
    //     $service->line_total = ($subtotal - $totalDiscount) + $service->tax_amount;

    //     $service->save();

    //     return response()->json([
    //         'booking_service_id' => $service->id,
    //         'service_booking_price' => $service->service_booking_price,
    //         'tax_amount' => $service->tax_amount,
    //         'line_total' => $service->line_total,
    //         'message' => __('Quantity updated successfully.'),
    //     ]);
    // }

    public function updateServiceQuantity(Request $request)
    {
        $service = BookingService::findOrFail($request->booking_service_id);
        $booking_info = Booking::find($service->booking_id);
        $this->guardFinalizedEdit($request, $booking_info);

        // Set values from request
        $service->quantity = $request->quantity;
        $service->service_booking_price = $request->service_booking_price;
        $service->tax_percent = $request->tax_percent ?? 0;

        // Calculate subtotal
        $subtotal = $service->service_booking_price * $service->quantity;

        // Apply discounts (calculated per unit if percentage is given)
        $discountPercent = $request->discount_percent ?? 0.00;
        $individualDiscountPercent = $request->individual_discount_percent ?? 0.00;

        $discountAmount = ($service->service_booking_price * $discountPercent / 100) * $service->quantity;
        $individualDiscountAmount = ($service->service_booking_price * $individualDiscountPercent / 100) * $service->quantity;

        $totalDiscount = $discountAmount + $individualDiscountAmount;

        $service->discount_amount = $discountAmount;
        $service->individual_discount_amt = $individualDiscountAmount;

        // Calculate tax on amount after discount
        $amountAfterDiscount = $subtotal - $totalDiscount;
        $service->tax_amount = ($amountAfterDiscount * $service->tax_percent) / 100;

        // Final total
        $service->line_total = $amountAfterDiscount + $service->tax_amount;

        try {
            DB::transaction(function () use ($service, $booking_info) {
                $service->save();
                app(\App\Service\ServiceUsedProductService::class)->rescaleForServiceQuantity($service);

                if ($booking_info && $booking_info->booking_code) {
                    $this->commissionSync->invalidateServiceCommission($service);
                }
            });
        } catch (\RuntimeException $e) {
            if (str_starts_with($e->getMessage(), 'Not enough stock')) {
                return response()->json([
                    'js_notifications' => ['error' => $e->getMessage()],
                ], 422);
            }

            throw $e;
        }

        if ($booking_info && $booking_info->booking_code) {
            $booking_info->load(['bookingOnlyServices', 'bookingItems']);
            $booking_info->resetBookingAmount();
            $booking_info->loadEssentialsForVueJs();
        }

        return response()->json([
            'booking_service_id' => $service->booking_service_id,
            'service_booking_price' => $service->service_booking_price,
            'quantity' => $service->quantity,
            'discount_amount' => $service->discount_amount,
            'individual_discount_amt' => $service->individual_discount_amt,
            'tax_amount' => $service->tax_amount,
            'line_total' => $service->line_total,
            'booking_info' => $booking_info,
            'needs_update' => $booking_info && $booking_info->booking_code ? true : false,
            'message' => __('Quantity updated successfully.'),
        ]);
    }



    // public function approveItemStore(Request $request)
    // {
    //     try {

    //         $selectedBookingsList = $request->all();
    //         foreach ($selectedBookingsList['selectedBookings'] as $booking) {

    //             $booking_service_model = BookingService::find($booking['booking_service_id']);
    //             $booking_service_model->approved_at = new \DateTime();
    //             $booking_service_model->approved_by = Auth::user()->id;
    //             $booking_service_model->status = BookingService::STATUS_COMPLETED;
    //             $booking_service_model->save();

    //             if ($booking_service_model->save()) {
    //                 $booking_id = $booking['booking_id'] ?? null;
    //                 $beautician_id = $booking['beautician_id'] ?? null;
    //                 $service_name = $booking['service_name'] ?? null;
    //                 $line_total = $booking['line_total'] ?? 0;
    //                 $service_started_at = $booking['service_started_at'] ?? null;
    //                 $start_date = $booking['start_date'] ?? null;
    //                 $end_date = $booking['end_date'] ?? null;
    //                 $commission_status = $booking['commission_status'] ?? null;
    //                 $commission_percentage = $booking['commission_percentage'] ?? 0;
    //                 $service_id = $booking['service_id'] ?? null;

    //                 $serviceStartedAt = new DateTime($service_started_at);
    //                 $startDate = new DateTime($start_date);
    //                 $endDate = new DateTime($end_date);

    //                 $isCommissionApplicable = ($serviceStartedAt >= $startDate && $serviceStartedAt <= $endDate);


    //                 // Calculate commission individually for each beautician using the split percentage
    //                 $beautician_commission = $isCommissionApplicable ? ($line_total * $commission_percentage) / 100 : 0;

    //                 if ($beautician_id) {
    //                     SplitServiceAmount::create([
    //                         'booking_id' => $booking_id,
    //                         'beautician_id' => $beautician_id,
    //                         'service_name' => $service_name,
    //                         'total_amount' => $line_total,
    //                         'commission_percentage' => $isCommissionApplicable ? $commission_percentage : 0, // Split percentage
    //                         'commission_amount' => $beautician_commission,
    //                         'price' => $line_total,
    //                         'created_at' => now(),
    //                     ]);
    //                 }
    //                 $updated = BookingService::where('booking_id', $booking_id)
    //                     ->where('service_id', $service_id)
    //                     ->update(['split_amount_status' => 'completed']);
    //                 if ($updated) {
    //                     return successResponse(null, 'Approved Successfully', 0);
    //                 }
    //             } else {
    //                 return errorResponse('error', 101, ' Not approved');
    //             }
    //         }
    //     } catch (Exception $e) {
    //         return errorResponse($e->getMessage(), 100);
    //     }
    // }

    public function approveItemStore(Request $request)
    {
        try {
            $selectedBookingsList = $request->all();
            $allApproved = true;

            foreach ($selectedBookingsList['selectedBookings'] as $booking) {
                $booking_service_model = BookingService::find($booking['booking_service_id']);

                if (!$booking_service_model) {
                    continue; // Skip if booking service is not found
                }

                $wasCompleted = $booking_service_model->status === BookingService::STATUS_COMPLETED;
                $booking_service_model->approved_at = now();
                $booking_service_model->approved_by = Auth::id();
                $booking_service_model->status = BookingService::STATUS_COMPLETED;

                $saved = DB::transaction(function () use ($booking_service_model, $wasCompleted) {
                    if (! $booking_service_model->save()) {
                        return false;
                    }

                    if (! $wasCompleted) {
                        app(\App\Service\ServiceUsedProductService::class)->postConsumption($booking_service_model);
                    }

                    return true;
                });

                if (! $saved) {
                    $allApproved = false;
                    continue;
                }

                $booking_id = $booking['booking_id'] ?? null;
                $beautician_id = $booking['beautician_id'] ?? null;
                $service_name = $booking['service_name'] ?? null;
                $line_total = $booking['line_total'] ?? 0;
                $service_started_at = $booking['service_started_at'] ?? null;
                $start_date = $booking['start_date'] ?? null;
                $end_date = $booking['end_date'] ?? null;
                $commission_status = $booking['commission_status'] ?? null;
                $commission_percentage = $booking['commission_percentage'] ?? 0;
                $service_id = $booking['service_id'] ?? null;

                $serviceStartedAt = new DateTime($service_started_at);
                $isCommissionApplicable = \App\Support\CommissionCalculator::isApplicable(
                    $commission_status,
                    $serviceStartedAt,
                    $start_date,
                    $end_date
                );

                // Calculate commission individually for each beautician using the split percentage
                $beautician_commission = \App\Support\CommissionCalculator::amount(
                    (float) $line_total,
                    (float) $commission_percentage,
                    $isCommissionApplicable
                );

                if ($beautician_id) {
                    SplitServiceAmount::create([
                        'booking_id' => $booking_id,
                        'booking_service_id' => $booking['booking_service_id'] ?? null,
                        'service_id' => $service_id,
                        'beautician_id' => $beautician_id,
                        'service_name' => $service_name,
                        'total_amount' => $line_total,
                        'commission_percentage' => $isCommissionApplicable ? (float) $commission_percentage : 0,
                        'commission_amount' => $beautician_commission,
                        'price' => $line_total,
                        'created_at' => now(),
                    ]);
                }

                BookingService::where('booking_id', $booking_id)
                    ->where('service_id', $service_id)
                    ->update(['split_amount_status' => 'completed']);
            }

            return $allApproved
                ? successResponse(null, 'All items approved successfully', 0)
                : errorResponse('Some items were not approved', 101);
        } catch (\Exception $e) {
            return errorResponse($e->getMessage(), 100);
        }
    }
}
