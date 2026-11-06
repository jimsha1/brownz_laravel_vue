<?php

namespace App\Http\Controllers\Appointment\BookingServiceList\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MyAccount\BookingServiceList\AjaxTabs\Traits\FiltersAppointmentDateRange;
use App\Repositories\BookingServiceRepository;
use App\Models\BookingService;
use Validator;
use App\Models\Booking;
use App\Models\SplitServiceAmount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class TabCompletedController extends Controller
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
                'vue_list_tab_completed' => [
                    'paginator' => paginator_vue_data($this->getCompletedRecords($request)),
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
            ],
        ];
        $beauticians_dropdown = array();
        $beauticians_dropdown = \Facades\App\Models\Beautician::beauticiansDropdown();

        if ($request->tab_search || $request->pgajax === 'y') {
            return response()->json($db_results);
        }

        return view('appointment.booking-service-list.ajax-tabs.tab-completed', compact('db_results', 'tab_name', 'beauticians_dropdown'));
    }

    public function splitServiceAmount(Request $request)
    {
        for ($i = 0; $i < count($request->beautician); $i++) {
            $split_amount = new SplitServiceAmount();
            $split_amount->booking_id = $request->booking_id;
            $split_amount->service_id = $request->service_id;
            $split_amount->booking_service_id = $request->booking_service_id;
            $split_amount->beautician_id = $request->beautician[$i];
            $split_amount->service_name = $request->service_name;
            $split_amount->total_amount = $request->line_total;
            $split_amount->price = $request->price[$i] ?? 0;
            $split_amount->split_amount = $request->price[$i] ?? 0;
            $split_amount->save();
        }
    }

    public function refundService(Request $request)
    {
        $booking_code = "";
        $booking_service_model = BookingService::find($request->booking_service_id);

        $booking_service_model->status = BookingService::STATUS_REFUNDED;
        // $booking_service_model->discount_amount = $booking_service_model->discount_amount * -1;
        // $booking_service_model->individual_discount_amt = $booking_service_model->individual_discount_amt * -1;
        // $booking_service_model->tax_amount = $booking_service_model->tax_amount * -1;
        $booking_service_model->line_total = $booking_service_model->line_total * -1;


        // $booking_service_model->save();

        $booking_model = Booking::find($booking_service_model->booking_id);
        $booking_code = $booking_model->booking_code;

        $booking = new Booking();
        // $booking->booking_code = mt_rand(1, 99999) . mt_rand(11111, 99999);
        $booking->client_id = Auth::user()->id;
        $booking->outlet_id = $booking_model->outlet_id;
        $booking->client_id = $booking_model->client_id;

        $booking->client_name = $booking_model->client_name;
        $booking->client_mobile = $booking_model->client_mobile;
        $booking->client_telephone = $booking_model->client_telephone;
        $booking->client_email = $booking_model->client_email;

        $booking->booking_source = $booking_model->booking_source;
        $booking->booking_type = 'Refund';
        $booking->parent_id = $booking_service_model->booking_id;
        $booking->refund_invoice_no = $booking_code;
        $booking->created_by = Auth::user()->id;
        $booking->updated_by = Auth::user()->id;

        $response = \DB::transaction(function () use ($booking, $request, $booking_service_model, $booking_code) {

            $booking_service_model->save();

            $booking->booking_code = Booking::assignNextBookingCode($booking);
            $booking->booking_barcode = $booking->booking_code;
            $booking->save();

            $booking_service = new BookingService();
            $booking_service->booking_id = $booking->booking_id;
            $booking_service->service_id = $booking_service_model->service_id;
            $booking_service->category_name = $booking_service_model->category_name;
            $booking_service->service_name = $booking_service_model->service_name;
            $booking_service->duration_minutes = $booking_service_model->duration_minutes;

            $booking_service->service_booking_price = $booking_service_model->service_booking_price;
            $booking_service->tax_percent = $booking_service_model->tax_percent;
            $booking_service->tax_amount = $booking_service_model->tax_amount;
            $booking_service->line_total = $booking_service_model->line_total;

            $booking_service->appointment_date = $booking_service_model->appointment_date;
            $booking_service->slot_start = $booking_service_model->slot_start;
            $booking_service->status = "draft";


            // $booking_service->slot_end = $request->time;
            $booking_service->beautician_id = $booking_service_model->beautician_id;
            $booking_service->save();
            $booking->resetBookingAmount();
        });



        $db_results = [
            // Send some data for VueJs
            'vue_data' => [
                'vue_list_tab_completed' => [
                    'paginator' => paginator_vue_data($this->getCompletedRecords($request)),
                    'todays_date' => date('Y-m-d'),
                ],
            ],
            'response_values' => [
                '#tab_search' => '0',
            ]
        ];

        return response()->json($db_results);
    }

    public function approveService(Request $request)
    {

        $booking_service_model = BookingService::find($request->booking_service_id);
        $booking_service_model->approved_at = new \DateTime();
        $booking_service_model->approved_by = Auth::user()->id;
        $booking_service_model->approval_remarks = $request->booking_remarks;
        $booking_service_model->status = BookingService::STATUS_COMPLETED;
        $booking_service_model->save();


        $service_name = $booking_service_model->service_name;
        $service_id = $request->booking_service_id;
        $booking_id = $request->booking_id;
        $bookings = Booking::find($booking_id);
        $client_name = $bookings->client_name;
        $cliend_number = $bookings->client_mobile;
        $booking_code  = $bookings->booking_code;
        $this->send_msg_booking($service_name, $service_id, $booking_code, $client_name, $cliend_number, $request->booking_service_id);
        $db_results = [
            // Send some data for VueJs
            'vue_data' => [
                'vue_list_tab_completed' => [
                    'paginator' => paginator_vue_data($this->getCompletedRecords($request)),
                    'todays_date' => date('Y-m-d'),
                ],
            ],
            'response_values' => [
                '#tab_search' => '0',
            ]
        ];
        // return response()->json($db_results);
        // return redirect()->back();
        return redirect()->route('report.appointment.list', ['tab_name' => 'completed']);

    }

    public function rejectService(Request $request)
    {
        $booking_service_model = BookingService::find($request->booking_service_id);
        $booking_service_model->rejected_at = new \DateTime();
        $booking_service_model->rejected_by = Auth::user()->id;
        $booking_service_model->rejection_remarks = $request->booking_remarks;
        $booking_service_model->status = BookingService::STATUS_REJECTED;
        $booking_service_model->save();

        $db_results = [
            // Send some data for VueJs
            'vue_data' => [
                'vue_list_tab_completed' => [
                    'paginator' => paginator_vue_data($this->getCompletedRecords($request)),
                    'todays_date' => date('Y-m-d'),
                ],
            ],
            'response_values' => [
                '#tab_search' => '0',
            ]
        ];

        // return response()->json($db_results);
        // return redirect()->back();
        return redirect()->route('report.appointment.list', ['tab_name' => 'completed']);
    }

    private function getCompletedRecords(Request $request)
    {
        [$startDate, $endDate] = $this->resolveAppointmentDateRange($request);

        $query = BookingService::join('bookings', 'bookings.booking_id', '=', 'booking_services.booking_id')
            ->join('clients', 'clients.client_id', '=', 'bookings.client_id')
            ->join('users as cluser', 'cluser.id', '=', 'clients.client_id')
            ->join('users as btuser', 'btuser.id', '=', 'booking_services.beautician_id')
            ->when($request->search, function ($query) use ($request) {
                if ($request->search != null) {
                    $query->where(function ($where) use ($request) {
                        $where->where('bookings.booking_code', 'like', '%' . $request->search . '%')
                            ->orWhere('cluser.name', 'like', '%' . $request->search . '%')
                            ->orWhere('btuser.name', 'like', '%' . $request->search . '%')
                            ->orWhere('booking_services.service_name', 'like', '%' . $request->search . '%');
                    });
                }
            })
            ->where('line_total', '>=', 0)
            // Fetch completed bookings only
            ->where(function ($where) {
                $where->whereNotNull('service_finished_at');
            });

        $this->applyAppointmentDateRange($query, $startDate, $endDate);

        return $query
            ->with(['splitAmounts.user:id,name'])
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
    }
    public function send_msg_booking($service_name, $service_id, $booking_code, $client_name, $cliend_number, $booking_service_id)
    {
        $feedback_url = config('vocomi.domain.website') . '/user-rating?service_id=' . urlencode(base64_encode($service_id)) . '&service_name=' . urlencode(base64_encode($service_name)) . '&user_name=' . urlencode(base64_encode($client_name)) . '&booking_service_id=' . urlencode(base64_encode($booking_service_id)) . "";
        \Log::info($feedback_url);
        $msg_text = "Dear Customer,\nThank you for choosing BaheyaBeauty!\nYour service " . $booking_code . " has been completed.\nWe Love to hear your feedback with following link to improve our services!\n" . $feedback_url . "\nSee you next time!";
        $url =  "https://mshastra.com/sendurl.aspx?user=20095417&pwd=B12345&senderid=BAHEYASTORE&mobileno=$cliend_number&msgtext=" . urlencode($msg_text) . "&priority=High&CountryCode=ALL";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curl_scraped_page = curl_exec($ch);
        curl_close($ch);
        \Log::info($curl_scraped_page);
        return true;
    }
}
