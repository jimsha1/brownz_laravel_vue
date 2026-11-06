<?php

namespace App\Http\Controllers;

use App\Models\Beautician;
use App\Models\GlobalTax;
use App\Models\MakeupBooking;
use App\Models\MakeupService;
use App\Models\Service;
use App\Service\MakeupBookingService;
use Illuminate\Http\Request;

class MakeupController extends Controller
{
    private MakeupBookingService $makeupBooking;

    public function __construct(MakeupBookingService $makeupBooking)
    {
        $this->makeupBooking = $makeupBooking;
    }

    public function index_bridal(Request $request)
    {
        $beauticians = Beautician::activeListedQuery()
            ->select('beauticians.beautician_id', 'beauticians.beautician_code', 'users.name', 'users.username', 'users.mobile')
            ->orderBy('users.name')
            ->get();
        $services = MakeupService::orderBy('service_name')->get();
        $serviceMap = MakeupService::pluck('service_name', 'id');

        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', 'all');
        $date = trim((string) $request->input('date', ''));
        $beautician = trim((string) $request->input('beautician', ''));

        // Include legacy "all" rows previously saved for bridal bookings
        $bookings = MakeupBooking::query()
            ->whereIn('booking_type', ['bridal', 'all'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('makeup_client_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('makeup_contact_number', 'LIKE', '%' . $search . '%')
                        ->orWhere('makeup_client_email', 'LIKE', '%' . $search . '%')
                        ->orWhere('makeup_place', 'LIKE', '%' . $search . '%')
                        ->orWhere('makeup_clent_address', 'LIKE', '%' . $search . '%')
                        ->orWhere('beautician_name', 'LIKE', '%' . $search . '%');
                });
            })
            ->when(in_array($status, ['active', 'inactive'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($date !== '', function ($query) use ($date) {
                $query->whereDate('makeup_date_booked', $date);
            })
            ->when($beautician !== '', function ($query) use ($beautician) {
                $query->where('beautician_name', 'LIKE', '%' . $beautician . '%');
            })
            ->orderBy('makeup_date_booked', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('makeupBooking.makeup_bridal_booking', [
            'beauticians' => $beauticians,
            'services' => $services,
            'serviceMap' => $serviceMap,
            'makeupBookings' => $bookings,
            'filter_search' => $search,
            'filter_status' => $status,
            'filter_date' => $date,
            'filter_beautician' => $beautician,
            'page_name' => 'makeup_booking',
            'category_name' => 'makeup',
        ]);
    }

    public function index_groom()
    {
        $beauticians = Beautician::activeListedQuery()
            ->select('beauticians.beautician_id', 'beauticians.beautician_code', 'users.name', 'users.username', 'users.mobile')
            ->orderBy('users.name')
            ->get();
        $services = Service::all();
        $bookings = MakeupBooking::with('service')
            ->where('booking_type', 'groom')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('makeupBooking.makeup_groom_booking', [
            'beauticians' => $beauticians,
            'services' => $services,
            'makeupBookings' => $bookings,
        ]);
    }

    public function index_party()
    {
        $beauticians = Beautician::activeListedQuery()
            ->select('beauticians.beautician_id', 'beauticians.beautician_code', 'users.name', 'users.username', 'users.mobile')
            ->orderBy('users.name')
            ->get();
        $services = Service::all();
        $bookings = MakeupBooking::with('service')
            ->where('booking_type', 'party')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('makeupBooking.makeup_party_booking', [
            'beauticians' => $beauticians,
            'services' => $services,
            'makeupBookings' => $bookings,
        ]);
    }

    private function validateBridalRequest(Request $request): void
    {
        $request->validate([
            'date' => ['required', 'date'],
            'client_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:50'],
            'place' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'time_slot' => ['required'],
            'address' => ['required', 'string'],
            'services' => ['required', 'array', 'min:1'],
            'beauticians' => ['required', 'array', 'min:1'],
            'beauticians.*' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);
    }

    private function validateGroomPartyRequest(Request $request): void
    {
        $request->validate([
            'date' => ['required', 'date'],
            'client_name' => ['required'],
            'contact_number' => ['required'],
            'time_slot' => ['required'],
            'address' => ['required'],
            'services' => ['required', 'array', 'min:1'],
            'beautician' => ['required'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);
    }

    public function store_bridal(Request $request)
    {
        $this->validateBridalRequest($request);

        return $this->makeupBooking->storeBooking($request, 'bridal');
    }

    public function store_groom(Request $request)
    {
        $this->validateGroomPartyRequest($request);

        return $this->makeupBooking->storeBooking($request, 'groom');
    }

    public function store_party(Request $request)
    {
        $this->validateGroomPartyRequest($request);

        return $this->makeupBooking->storeBooking($request, 'party');
    }

    public function update_bridal(Request $request)
    {
        $this->validateBridalRequest($request);
        $request->validate(['id' => ['required']]);

        return $this->makeupBooking->updateBooking($request, 'bridal');
    }

    public function update_party(Request $request)
    {
        $this->validateGroomPartyRequest($request);
        $request->validate(['id' => ['required']]);

        return $this->makeupBooking->updateBooking($request, 'party');
    }

    public function update_groom(Request $request)
    {
        $this->validateGroomPartyRequest($request);
        $request->validate(['id' => ['required']]);

        return $this->makeupBooking->updateBooking($request, 'groom');
    }

    public function makeup_booking_service(Request $request)
    {
        $taxs = GlobalTax::where('is_active', 1)->orderBy('name')->get();
        $duration_data = \Facades\App\Models\Service::durationStaticData();

        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', 'all');

        $makeupServices = MakeupService::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('service_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('service_base_price', 'LIKE', '%' . $search . '%');
                });
            })
            ->when(in_array($status, ['active', 'inactive'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('makeupBooking.makeup_booking_services', [
            'taxs' => $taxs,
            'duration_data' => $duration_data,
            'makeupServices' => $makeupServices,
            'filter_search' => $search,
            'filter_status' => $status,
            'page_name' => 'makeup_services',
            'category_name' => 'makeup',
        ]);
    }

    public function store_service(Request $request)
    {
        $request->validate([
            'service_name' => ['required', 'string', 'max:255'],
            'service_base_price' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        return $this->makeupBooking->storeService($request);
    }

    public function update_service(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer'],
            'service_name' => ['required', 'string', 'max:255'],
            'service_base_price' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        return $this->makeupBooking->updateService($request);
    }

    public function search(Request $request)
    {
        // Keep legacy search URL working by reusing the main list filters
        return $this->makeup_booking_service($request);
    }
}
