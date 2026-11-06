<?php

namespace App\Http\Controllers\Appointment\BookingServiceList\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MyAccount\BookingServiceList\AjaxTabs\Traits\FiltersAppointmentDateRange;
use App\Repositories\BookingServiceRepository;
use App\Models\BookingService;
use Validator;
use Auth;

class TabOnGoingController extends Controller
{
    use FiltersAppointmentDateRange;

    public function index(Request $request, BookingServiceRepository $booking_service_repo, $tab_name)
    {

        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        [$startDate, $endDate] = $this->resolveAppointmentDateRange($request);

        $query = BookingService::join('bookings', 'bookings.booking_id', '=', 'booking_services.booking_id')
            ->join('clients', 'clients.client_id', '=', 'bookings.client_id')
            ->join('users as cluser', 'cluser.id', '=', 'clients.client_id')
            ->join('users as btuser', 'btuser.id', '=', 'booking_services.beautician_id')
            ->when($request->search, function ($query) use ($request) {
                $query->where(function($where) use ($request) {
                    $where->where('bookings.booking_code', 'like', '%' . $request->search . '%')
                        ->orWhere('cluser.name', 'like', '%' . $request->search . '%')
                        ->orWhere('btuser.name', 'like', '%' . $request->search . '%')
                        ->orWhere('booking_services.service_name', 'like', '%' . $request->search . '%');
                });
            })
            // Fetch on going bookings only
            ->where(function($where){
                $where->whereNotNull('service_started_at')
                    ->whereNull('service_finished_at');
            });

        $this->applyAppointmentDateRange($query, $startDate, $endDate);

        $db_records = $query
            ->orderBy('booking_services.service_finished_at', 'desc')
            ->orderBy('booking_services.booking_service_id', 'desc')
            ->select(
                'booking_services.*',
                'bookings.booking_code',
                'cluser.name as client_name',
                'cluser.mobile as client_mobile',
                'btuser.name as beautician_name'
            )
            ->paginate(config('vocomi.settings.pagination.perpage.admin'))
            ->appends($this->appointmentFilterAppends($request, $startDate, $endDate));

        $db_results = [
            // Send some data for VueJs
            'vue_data' => [
                'vue_list_tab_on_going' => [
                    'paginator' => paginator_vue_data($db_records),
                ],
            ],
            'response_values' => [
                '#tab_search' => '0',
            ]
        ];

        if ($request->tab_search || $request->pgajax === 'y') {
            return response()->json($db_results);
        }

        return view('appointment.booking-service-list.ajax-tabs.tab-on-going', compact('db_results', 'tab_name'));
    }
}
