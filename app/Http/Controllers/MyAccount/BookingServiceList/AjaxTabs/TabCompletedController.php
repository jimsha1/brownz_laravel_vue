<?php

namespace App\Http\Controllers\MyAccount\BookingServiceList\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MyAccount\BookingServiceList\AjaxTabs\Traits\FiltersAppointmentDateRange;
use App\Repositories\BookingServiceRepository;
use App\Models\BookingService;
use Validator;
use Auth;

class TabCompletedController extends Controller
{
    use FiltersAppointmentDateRange;

    public function index(Request $request, BookingServiceRepository $booking_service_repo, $tab_name)
    {
        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        $user = Auth::user();
        $userRoles = $user->getRoleNames();
        [$startDate, $endDate] = $this->resolveAppointmentDateRange($request);

        $query = BookingService::join('bookings', 'bookings.booking_id', '=', 'booking_services.booking_id')
            ->join('clients', 'clients.client_id', '=', 'bookings.client_id')
            ->join('users', 'users.id', '=', 'clients.client_id')
            ->when($request->search, function ($query) use ($request) {
                $query->where(function($where) use ($request) {
                    $where->where('bookings.booking_code', 'like', '%' . $request->search . '%')
                        ->orWhere('users.name', 'like', '%' . $request->search . '%')
                        ->orWhere('booking_services.service_name', 'like', '%' . $request->search . '%');
                });
            })
            ->when(! $userRoles->contains('super_admin'), function ($query) {
                $query->where('booking_services.beautician_id', auth()->id());
            })
            ->whereNotNull('booking_services.service_finished_at');

        $this->applyAppointmentDateRange($query, $startDate, $endDate);

        $db_records = $query
            ->orderBy('booking_services.service_finished_at', 'desc')
            ->orderBy('booking_services.booking_service_id', 'desc')
            ->select('booking_services.*', 'bookings.booking_code', 'users.name as client_name', 'users.mobile as client_mobile')
            ->paginate(config('vocomi.settings.pagination.perpage.admin'))
            ->appends($this->appointmentFilterAppends($request, $startDate, $endDate));

        $db_results = [
            // Send some data for VueJs
            'vue_data' => [
                'vue_list_tab_completed' => [
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

        return view('my-account.booking-service-list.ajax-tabs.tab-completed', compact('db_results', 'tab_name'));
    }

}
