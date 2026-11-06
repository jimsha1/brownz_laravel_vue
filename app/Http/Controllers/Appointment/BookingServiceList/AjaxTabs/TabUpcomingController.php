<?php

namespace App\Http\Controllers\Appointment\BookingServiceList\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MyAccount\BookingServiceList\AjaxTabs\Traits\FiltersAppointmentDateRange;
use App\Repositories\BookingServiceRepository;
use App\Models\BookingService;
use App\Service\BookingCheckInService;
use Validator;
use Auth;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Null_;

class TabUpcomingController extends Controller
{
    use FiltersAppointmentDateRange;
    public function index(Request $request, BookingServiceRepository $booking_service_repo, $tab_name)
    {

        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        $db_results = [
            // Send some data for VueJs
            'vue_data' => [
                'vue_list_tab_upcoming' => [
                    'paginator' => paginator_vue_data($this->getUpcomingRecords($request)),
                    'todays_date' => date('Y-m-d'),
                    'booking_id' => null,
                    'booking_info' => [],
                    'focused_active_service' => null,
                    'focused_active_booking_date' => null,
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
        // dd($db_results);

        if ($request->tab_search || $request->pgajax === 'y') {
            return response()->json($db_results);
        }

        return view('appointment.booking-service-list.ajax-tabs.tab-upcoming', compact('db_results', 'tab_name'));
    }



    public function undoClientCheckIn(Request $request, BookingCheckInService $checkInService)
    {
        $booking_service_model = BookingService::findOrFail($request->booking_service_id);

        try {
            $checkInService->undoCheckInService($booking_service_model);
        } catch (\RuntimeException $exception) {
            return response()->json([
                'js_notifications' => ['warning' => $exception->getMessage()],
            ]);
        }

        $db_results = [
            // Send some data for VueJs
            'vue_data' => [
                'vue_list_tab_upcoming' => [
                    'paginator' => paginator_vue_data($this->getUpcomingRecords($request)),
                    'todays_date' => date('Y-m-d'),
                ],
            ],
            'response_values' => [
                '#tab_search' => '0',
            ]
        ];

        return response()->json($db_results);
    }
    public function clientCheckIn(Request $request, BookingCheckInService $checkInService)
    {
        $booking_service_model = BookingService::findOrFail($request->booking_service_id);

        if ($request->type == 'check_in') {
            $checkInService->checkInService($booking_service_model);
        } else {
            try {
                $checkInService->undoCheckInService($booking_service_model);
            } catch (\RuntimeException $exception) {
                return response()->json([
                    'js_notifications' => ['warning' => $exception->getMessage()],
                ]);
            }
        }

        $db_results = [
            // Send some data for VueJs
            'vue_data' => [
                'vue_list_tab_upcoming' => [
                    'paginator' => paginator_vue_data($this->getUpcomingRecords($request)),
                    'todays_date' => date('Y-m-d'),
                ],
            ],
            'response_values' => [
                '#tab_search' => '0',
            ]
        ];

        return response()->json($db_results);
    }

    // @TODO - consider pagination current page
    private function getUpcomingRecords(Request $request)
    {
        [$startDate, $endDate] = $this->resolveAppointmentDateRange($request);

        $query = BookingService::join('bookings', 'bookings.booking_id', '=', 'booking_services.booking_id')
            ->join('clients', 'clients.client_id', '=', 'bookings.client_id')
            ->join('users as cluser', 'cluser.id', '=', 'clients.client_id')
            ->leftJoin('users as btuser', 'btuser.id', '=', 'booking_services.beautician_id')
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($where) use ($request) {
                    $where->where('bookings.booking_code', 'like', '%' . $request->search . '%')
                        ->orWhere('cluser.name', 'like', '%' . $request->search . '%')
                        ->orWhere('btuser.name', 'like', '%' . $request->search . '%')
                        ->orWhere('booking_services.service_name', 'like', '%' . $request->search . '%');
                });
            })
            ->where('line_total', '>=', 0)
            // Fetch upcoming bookings only
            ->where(function ($where) {
                $where->whereNull('service_started_at')
                    ->where('appointment_date', '>=', date('Y-m-d'));
            });

        $this->applyAppointmentDateRange($query, $startDate, $endDate);

        return $query
            ->orderBy('booking_services.booking_service_id', 'desc')
            ->distinct()
            ->select(
                'booking_services.*',
                'bookings.booking_code',
                'bookings.canceled_by',
                'cluser.name as client_name',
                'cluser.mobile as client_mobile',
                'btuser.name as beautician_name',
                'btuser.shift_id'
            )
            ->paginate(config('vocomi.settings.pagination.perpage.admin'))
            ->appends($this->appointmentFilterAppends($request, $startDate, $endDate));
    }
}
