<?php

namespace App\Http\Controllers\Salon\BookingList\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingItem;
use GuzzleHttp\Psr7\Request;
use Illuminate\Http\Request as postRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.salon.booking.list';
    private $module       = 'Salon';
    private $namespace    = 'BookingList';
    private $model        = 'Booking';

    public function index($tab_name = null)
    {

        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $this->addDependencyInjection(\App\Repositories\BookingRepository::class, 'booking_repo');

        // Add tabs
        $this->addTabs('all', 'fa fa-folder-open-o');

        $selected_tab_name = $this->getSelectedTab($tab_name);

        // Be careful! this will auto generate controller and blade files,
        // based on the above tabs configured (when not in production environment)
        $this->ensureFiles(dirname(__DIR__));
        // Ajax request must be treated separately
        if (\Request::ajax()) {
            $tab_html = $this->renderView($this->module, $this->namespace, $selected_tab_name, [
                'tab_name' => $selected_tab_name,
            ]);

            if ($tab_html instanceof \Illuminate\Http\JsonResponse) {
                return $tab_html;
            }

            return response()->json([
                'tab_name'      => $selected_tab_name,
                'ajax_tab_html' => $tab_html,
            ]);
        }

        // // Buttons for navigation
        // $this->addTabButton('button', __('New'), 'fa fa-plus', [
        //     'type' => 'button',
        //     'class' => 'btn btn-sm btn-primary vctab-btn',
        //     'data-modal-wide' => 'true',
        //     'data-url' => route('tab.salon.booking.details', ['booking_id' => '0', 'tab_name' => 'edit', 'return_html' => 'y']),
        //     'data-modal-title' => 'Create New Booking',
        //     'data-voco-toggle' => 'modal',
        //     'data-target' => '#voco-ajax-modal',
        //     'data-toggle' => 'tooltip',
        //     'title' => 'Create New Booking',
        // ]);

        // Buttons for navigation
        // $this->addTabButton('a', __('New'), 'fa fa-plus', [
        //     'class' => 'btn btn-sm btn-primary',
        //     'href' => route('salon.booking.new'),
        //     'data-toggle' => 'tooltip',
        //     'title' => 'Create New Booking',
        // ]);

        if (Auth::user()->hasPermissionTo('create_booking')) {
            $this->addTabButton('a', __('New'), 'fa fa-plus', [
                'class'       => 'btn btn-sm btn-primary btn_dark-x',
                'href'        => route('tab.salon.booking.details', ['booking_id' => '0', 'tab_name' => 'edit']),
                'data-toggle' => 'tooltip',
                'title'       => 'Create New Booking',
            ]);
        }

        // Buttons for navigation
        // $this->addTabButton('a', __('New'), 'fa fa-plus', [
        //     'class' => 'btn btn-sm btn-primary',
        //     'href' => route('tab.salon.booking.details', ['booking_id' => '0', 'tab_name' => 'edit']),
        //     'data-toggle' => 'tooltip',
        //     'title' => 'Create New Booking',
        // ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('salon.booking-list.index', $tabs_data);
    }

    public function productDelivery(postRequest $request)
    {

        // if (isset($request->code)) {

        //     // $bookings = Booking::where('booking_code', 'LIKE', '%' . $request->code . '%')->get()->toQuery()->paginate(10);
        //     $bookings = DB::table('bookings')->where('bookings.booking_code', 'LIKE', '%' . $request->code . '%')
        //         ->join('booking_items', 'booking_items.booking_id', '=', 'bookings.booking_id')
        //         ->select('bookings.booking_id', 'bookings.booking_code', 'bookings.client_name', 'bookings.booking_total', 'bookings.created_at', 'bookings.booking_source', 'bookings.booking_type', 'booking_items.status')
        //         ->orderBy('bookings.booking_id', 'DESC')->groupBy('bookings.booking_id')->paginate(100);

        //     if ($bookings->isEmpty()) {
        //         $bookings = null; // set $bookings to null to indicate no data found
        //     }
        // } else {
        //     $bookings = null; // set $bookings to null to indicate no data found
        //     // $bookings = Booking::where('booking_code')->get()->toQuery()->paginate(10);

        //     $bookings = DB::table('bookings')
        //         ->join('booking_items', 'booking_items.booking_id', '=', 'bookings.booking_id')->where('booking_items.status', '=', 'pending')->whereNotNull('bookings.booking_code')
        //         ->select('bookings.booking_id', 'bookings.booking_code', 'bookings.client_name', 'bookings.booking_total', 'bookings.created_at', 'bookings.booking_source', 'bookings.booking_type', 'booking_items.status')
        //         ->orderBy('bookings.booking_id', 'DESC')->groupBy('bookings.booking_id')->paginate(100);

        // }

        if ($request->isMethod('post')) {

            if(isset($request->code)){
                $bookings = DB::table('bookings')
                    ->where('bookings.booking_code', 'LIKE', '%' . $request->code . '%')
                    ->join('booking_items', 'booking_items.booking_id', '=', 'bookings.booking_id')
                    ->select('bookings.booking_id', 'bookings.booking_code', 'bookings.client_name', 'bookings.booking_total', 'bookings.created_at', 'bookings.booking_source', 'bookings.booking_type', 'booking_items.status')
                    ->orderBy('bookings.booking_id', 'DESC')
                    ->groupBy('bookings.booking_id')
                    ->paginate(config('vocomi.settings.pagination.perpage.admin'));
            } else if($request->status == 'pending'){
                $bookings = DB::table('bookings')
                ->join('booking_items', 'booking_items.booking_id', '=', 'bookings.booking_id')
                ->where('booking_items.status', '=', 'pending')
                ->whereNotNull('bookings.booking_code')
                ->select('bookings.booking_id', 'bookings.booking_code', 'bookings.client_name', 'bookings.booking_total', 'bookings.created_at', 'bookings.booking_source', 'bookings.booking_type', 'booking_items.status')
                ->orderBy('bookings.booking_id', 'DESC')
                ->groupBy('bookings.booking_id')
                ->paginate(config('vocomi.settings.pagination.perpage.admin'));
            } else if($request->status == 'delivered') {

                $bookings = DB::table('bookings')
                    ->join('booking_items', 'booking_items.booking_id', '=', 'bookings.booking_id')
                    ->where('booking_items.status', '=', 'deliverd')
                    ->whereNotNull('bookings.booking_code')
                    ->select('bookings.booking_id', 'bookings.booking_code', 'bookings.client_name', 'bookings.booking_total', 'bookings.created_at', 'bookings.booking_source', 'bookings.booking_type', 'booking_items.status')
                    ->orderBy('bookings.booking_id', 'DESC')
                    ->groupBy('bookings.booking_id')
                    ->paginate(config('vocomi.settings.pagination.perpage.admin'));
            } else if($request->status == 'all') {

                $bookings = DB::table('bookings')
                    ->join('booking_items', 'booking_items.booking_id', '=', 'bookings.booking_id')
                    ->where(function($query) {
                        $query->where('booking_items.status', '=', 'pending')
                            ->orWhere('booking_items.status', '=', 'deliverd');
                    })
                    ->whereNotNull('bookings.booking_code')
                    ->select('bookings.booking_id', 'bookings.booking_code', 'bookings.client_name', 'bookings.booking_total', 'bookings.created_at', 'bookings.booking_source', 'bookings.booking_type', 'booking_items.status')
                    ->orderBy('bookings.booking_id', 'DESC')
                    ->groupBy('bookings.booking_id')
                    ->paginate(config('vocomi.settings.pagination.perpage.admin'));
            }

            $response = [
                'errorcode' => 0,
                'errormessage' => 'Success',
                'pagination' => [
                    'total' => $bookings->total(),
                    'per_page' => $bookings->perPage(),
                    'current_page' => $bookings->currentPage(),
                    'last_page' => $bookings->lastPage(),
                    'from' => $bookings->firstItem(),
                    'to' => $bookings->lastItem()
                ],
                'data' => $bookings
            ];

            return response()->json($response);
        }

        $bookings = null;
        return view('salon.booking-list.delivery_product_list', compact('bookings'));
    }

    public function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page  = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function productDeliveryDetails(postRequest $request)
    {

        $products = BookingItem::where('booking_id', $request->booking_id)->get();

        $bookingDetails = Booking::where('booking_id', $request->booking_id)->first();

        return view('salon.booking-list.delivery_product_details', [
            'products'       => $products,
            'bookingDetails' => $bookingDetails,
        ]);
    }
}
