<?php

namespace App\Http\Controllers\MyAccount\BookingServiceList\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MyAccount\BookingServiceList\AjaxTabs\Traits\FiltersAppointmentDateRange;
use App\Models\Booking;
use App\Repositories\BookingServiceRepository;
use App\Models\BookingService;
use Validator;
use Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;

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

        return view('my-account.booking-service-list.ajax-tabs.tab-upcoming', compact('db_results', 'tab_name'));
    }

    public function startService(Request $request)
    {
        $booking_service_model = BookingService::find($request->booking_service_id);

        if (! $booking_service_model) {
            return response()->json([
                'js_notifications' => ['error' => __('Service not found.')],
            ]);
        }

        if (! $this->canManageBookingService($booking_service_model)) {
            return response()->json([
                'js_notifications' => ['error' => __('Unauthorized.')],
            ], 403);
        }

        if (empty($booking_service_model->beautician_id)) {
            return response()->json([
                'js_notifications' => ['error' => __('Assign a beautician before starting the service.')],
            ]);
        }

        $booking_service_model->service_started_at = new \DateTime();
        $booking_service_model->service_started_by = $this->resolveServicePerformerId($booking_service_model);
        $booking_service_model->status = 'start';
        $booking_service_model->save();

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

    public function stopService(Request $request)
    {
        $booking_service_model = BookingService::find($request->booking_service_id);

        if (! $booking_service_model) {
            return response()->json([
                'js_notifications' => ['error' => __('Service not found.')],
            ]);
        }

        if (! $this->canManageBookingService($booking_service_model)) {
            return response()->json([
                'js_notifications' => ['error' => __('Unauthorized.')],
            ], 403);
        }

        if (empty($booking_service_model->beautician_id)) {
            return response()->json([
                'js_notifications' => ['error' => __('Assign a beautician before finishing the service.')],
            ]);
        }

        $booking_service_model->service_finished_at = new \DateTime();
        $booking_service_model->service_finished_by = $this->resolveServicePerformerId($booking_service_model);
        $booking_service_model->status = 'completed';
        $booking_service_model->service_finished_remarks = $request->booking_remarks;
        $booking_service_model->save();
        $service_name = $booking_service_model->service_name;
        $service_id = $request->booking_service_id;
        $booking_id = $request->booking_id;
        $bookings = Booking::find($booking_id);
        $client_name = $bookings->client_name;
        $cliend_number = $bookings->client_mobile;
        $booking_code  = $bookings->booking_code;

        // $this->send_msg_booking($service_name,$service_id,$booking_code,$client_name, $cliend_number);
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

    private function getUpcomingRecords123(Request $request)
    {
        return BookingService::join('bookings', 'bookings.booking_id', '=', 'booking_services.booking_id')
            ->join('clients', 'clients.client_id', '=', 'bookings.client_id')
            ->join('users', 'users.id', '=', 'clients.client_id')
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($where) use ($request) {
                    $where->where('bookings.booking_code', 'like', '%' . $request->search . '%')
                        ->orWhere('users.name', 'like', '%' . $request->search . '%')
                        ->orWhere('booking_services.service_name', 'like', '%' . $request->search . '%');
                });
            })
            ->where('beautician_id', auth()->id())
            ->where('booking_services.status', 'open')
            ->where('booking_code', '!=', null)
            ->where('booking_code', '!=', 'draft')
            ->whereNotNull('bookings.booking_code')

            // Fetch upcoming bookings only
            ->where(function ($where) {
                $where->whereNull('service_finished_at')
                    ->where('appointment_date', '>=', date('Y-m-d'));
            })
            ->orderBy('booking_services.appointment_date')
            ->orderBy('booking_services.slot_start')
            ->select('booking_services.*', 'bookings.booking_code', 'users.name as client_name')
            ->paginate(config('vocomi.settings.pagination.perpage.admin'))
            ->appends(['pgajax' => 'y']);
    }

    public function send_msg_booking($service_name, $service_id, $booking_code, $client_name, $cliend_number)
    {
        $feedback_url = 'http://baheyabeauty.com/user-rating?service_id=' . urlencode(base64_encode($service_id)) . '&service_name=' . urlencode(base64_encode($service_name)) . '&user_name=' . urlencode(base64_encode($client_name)) . "";
        \Log::info($feedback_url);
        $msg_text = "Thank you for booking with Baheya Beauty. Your booking no is " . $booking_code . "is completed . See you again..Please give me your Feedback :" . $feedback_url . "";
        $url =  "https://mshastra.com/sendurl.aspx?user=20095417&pwd=B12345&senderid=BAHEYASTORE&mobileno=$cliend_number&msgtext=" . urlencode($msg_text) . "&priority=High&CountryCode=ALL";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curl_scraped_page = curl_exec($ch);
        curl_close($ch);
        \Log::info($curl_scraped_page);
        return true;
    }

    private function getUpcomingRecords(Request $request)
    {
        $user = Auth::user();
        $userRoles = $user->getRoleNames();
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
            ->when(! $userRoles->contains('super_admin'), function ($query) {
                $query->where('booking_services.beautician_id', auth()->id());
            })
            ->whereNotNull('bookings.booking_code')
            ->where('bookings.booking_code', '!=', 'draft')
            ->where(function ($where) {
                $where->where('booking_services.status', 'open')
                    ->orWhereNull('booking_services.status');
            })
            ->whereNull('booking_services.service_started_at')
            ->whereNull('booking_services.service_finished_at')
            ->whereDate('booking_services.appointment_date', '>=', date('Y-m-d'));

        $this->applyAppointmentDateRange($query, $startDate, $endDate);

        return $query
            ->orderBy('booking_services.appointment_date')
            ->orderBy('booking_services.slot_start')
            ->orderBy('booking_services.booking_service_id')
            ->select(
                'booking_services.*',
                'bookings.booking_code',
                'bookings.canceled_by',
                'cluser.name as client_name',
                'cluser.mobile as client_mobile',
                'btuser.name as beautician_name'
            )
            ->paginate(config('vocomi.settings.pagination.perpage.admin'))
            ->appends($this->appointmentFilterAppends($request, $startDate, $endDate));
    }

    private function canManageBookingService(BookingService $bookingService): bool
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin') || $user->can('view_appointment_of_others')) {
            return true;
        }

        return $user->can('view_own_appointments')
            && (int) $bookingService->beautician_id === (int) $user->id;
    }

    private function resolveServicePerformerId(BookingService $bookingService): int
    {
        return (int) ($bookingService->beautician_id ?: Auth::id());
    }
}