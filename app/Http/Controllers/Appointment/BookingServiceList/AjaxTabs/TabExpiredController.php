<?php

namespace App\Http\Controllers\Appointment\BookingServiceList\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MyAccount\BookingServiceList\AjaxTabs\Traits\FiltersAppointmentDateRange;
use App\Models\Booking;
use App\Models\BookingService;
use App\Repositories\BookingServiceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabExpiredController extends Controller
{
    use FiltersAppointmentDateRange;
    public function index(Request $request, BookingServiceRepository $booking_service_repo, $tab_name)
    {

        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        // $db_records = BookingService::join('bookings', 'bookings.booking_id', '=', 'booking_services.booking_id')
        //     ->join('clients', 'clients.client_id', '=', 'bookings.client_id')
        //     ->join('users as cluser', 'cluser.id', '=', 'clients.client_id')
        //     ->join('users as btuser', 'btuser.id', '=', 'booking_services.beautician_id')
        //     ->when($request->search, function ($query) use ($request) {
        //         $query->where(function($where) use ($request) {
        //             $where->where('bookings.booking_code', 'like', '%' . $request->search . '%')
        //                 ->orWhere('cluser.name', 'like', '%' . $request->search . '%')
        //                 ->orWhere('btuser.name', 'like', '%' . $request->search . '%')
        //                 ->orWhere('booking_services.service_name', 'like', '%' . $request->search . '%');
        //         });
        //     })
        //     ->where('bookings.outlet_id', session_outlet_id())

        //     // Fetch upcoming bookings only
        //     ->where(function($where){
        //         $where->whereNull('service_started_at')
        //             ->where('appointment_date', '<', date('Y-m-d'));
        //     })
        //     ->orderBy('booking_services.appointment_date', 'desc')
        //     ->orderBy('booking_services.slot_start', 'desc')
        //     ->select(
        //         'booking_services.*',
        //         'bookings.booking_code',
        //         'cluser.name as client_name',
        //         'btuser.name as beautician_name'
        //     )
        //     ->paginate(config('vocomi.settings.pagination.perpage.admin'))
        //     ->appends(['pgajax' => 'y']);

// dd($db_records);
        $db_results = [
            // Send some data for VueJs
            'vue_data'        => [
                'vue_list_tab_expired' => [
                    'paginator'                   => paginator_vue_data($this->getExpiredRecords($request)),
                    'booking_id'                  => null,
                    'booking_info'                => [],
                    'focused_active_service'      => null,
                    'focused_active_booking_date' => null,
                    'beauticians'                 => [],
                    'urls'                        => [
                        'item_form_update_url' => route('salon.booking.item.update'),
                        'date_form_update_url' => route('salon.booking.date.update'),
                    ],
                ],
            ],
            'response_values' => [
                '#tab_search' => '0',
            ],
        ];

        if ($request->tab_search || $request->pgajax === 'y') {

            return response()->json($db_results);

        }
//dd($db_results);
        return view('appointment.booking-service-list.ajax-tabs.tab-expired', compact('db_results', 'tab_name'));

        /*  if ($request->has('tab_search') || $request->pgajax === 'y') {
    $db_results = [
    'vue_data' => [
    'vue_list_tab_expired' => [
    'paginator' => paginator_vue_data($this->getExpiredRecords($request)),
    'booking_id' => null,
    'booking_info' => [],
    'focused_active_service' => null,
    'focused_active_booking_date' =>null,
    'beauticians' => [],
    'urls' => [
    'item_form_update_url' => route('salon.booking.item.update'),
    'date_form_update_url' => route('salon.booking.date.update'),
    ]
    ],
    ],
    'response_values' => [
    '#tab_search' => '0',
    ]
    ];

    return response()->json($db_results);
    } else {
    $db_results = []; // initialize as empty array

    }

    return view('appointment.booking-service-list.ajax-tabs.tab-expired', compact('db_results', 'tab_name'));

     */

    }

    public function getBookingDetails(Request $request)
    {

        $booking_service_info = BookingService::find($request->booking_service_id);
        // $beatician =  Beautician::join('users','beauticians.beautician_id','users.id')
        // ->join('beautician_services','beauticians.beautician_id','beautician_services.beautician_id')
        // ->where('beautician_services.service_id', $booking_service_info->service_id)
        // ->select('users.*')
        // ->get();
        $booking_info = Booking::find($booking_service_info->booking_id);

        $booking_info->loadEssentialsForVueJs();

        $result = [
            'booking_info' => $booking_info,
            'service_id'   => $booking_service_info->service_id,
            'booking_date' => $booking_service_info->appointment_date,
            // 'beauticians' =>   $beatician
        ];

        return response()->json($result);

        // dd($request->booking_service_id);
    }

    private function getExpiredRecords(Request $request)
    {
        [$startDate, $endDate] = $this->resolveAppointmentDateRange($request);

        $query = BookingService::join('bookings', 'bookings.booking_id', '=', 'booking_services.booking_id')
            ->when($request->search, function ($query) use ($request) {
                if ($request->search != null) {
                    $query->where(function ($where) use ($request) {
                        $where->where('bookings.booking_code', 'like', '%' . $request->search . '%')
                            ->orWhere('booking_services.service_name', 'like', '%' . $request->search . '%');
                    });
                }
            })
            ->distinct()
            ->where('line_total', '>=', 0)
            // Fetch expired bookings only
            ->where(function ($where) {
                $where->whereNull('service_started_at')
                    ->where('appointment_date', '<', date('Y-m-d'))
                    ->where('booking_code', '!=', null)
                    ->where('booking_code', '!=', 'draft');
            });

        $this->applyAppointmentDateRange($query, $startDate, $endDate);

        return $query
            ->orderBy('booking_services.appointment_date', 'desc')
            ->orderBy('booking_services.slot_start', 'desc')
            ->select(
                'booking_services.*',
                'bookings.booking_code',
                 DB::raw("(SELECT GetClientName(bookings.client_id)) as client_name "),
                 DB::raw("(SELECT GetClientName(booking_services.beautician_id)) as beautician_name "),
            )
           ->paginate(config('vocomi.settings.pagination.perpage.admin'))
            ->appends($this->appointmentFilterAppends($request, $startDate, $endDate));
    }

}
