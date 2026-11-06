<?php

namespace App\Http\Controllers;

use App\Models\MakeupBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'title' => 'required|string'
        ]);

        $booking = MakeupBooking::create([
            'makeup_client_name' => $request->makeup_client_name,
            'makeup_date_booked' => $request->makeup_date_booked,
            'makeup_date_booked' => $request->makeup_date_booked,
        ]);


        $color = '#924ACE';

        return response()->json([
            'id' => $booking->id,
            'start' => $booking->makeup_date_booked,
            'end' => $booking->makeup_date_booked,
            'title' => $booking->makeup_client_name,
            'color' => $color ? $color: '',

        ]);
    }

    public function update(Request $request ,$id)
    {
        $booking = MakeupBooking::find($id);
        if(! $booking) {
            return response()->json([
                'error' => 'Unable to locate the event'
            ], 404);
        }
        $booking->update([
            'makeup_date_booked' => $request->makeup_date_booked,
            'makeup_date_booked' => $request->makeup_date_booked,
        ]);
        return response()->json('Event updated');
    }
    public function destroy($id)
    {
        $booking = MakeupBooking::find($id);
        if(! $booking) {
            return response()->json([
                'error' => 'Unable to locate the event'
            ], 404);
        }
        $booking->delete();
        return $id;
    }
    public function beautician_calender_index(Request $request)
    {
        $user_id = $request->user_id;

        if (empty($user_id)) {
            return redirect()->route('all.beatician.calender')
                ->with('error', 'Beautician is required to view calendar.');
        }

        $user = DB::table('users')
            ->select('id', 'name', 'week_off')
            ->where('id', $user_id)
            ->first();

        if (!$user) {
            return redirect()->route('all.beatician.calender')
                ->with('error', 'Beautician not found.');
        }

        $calender = [];
        $week_off = $user->week_off ?? null;
        $user_name = $user->name ?? 'Beautician';

        $style = '';
        $weekOffStyles = [
            'Sunday' => '.fc-sun {background-color: #D3D3D3;}',
            'Monday' => '.fc-mon {background-color: #D3D3D3;}',
            'Tuesday' => '.fc-tue {background-color: #D3D3D3;}',
            'Wednesday' => '.fc-wed {background-color: #D3D3D3;}',
            'Thursday' => '.fc-thu {background-color: #D3D3D3;}',
            'Friday' => '.fc-fri {background-color: #D3D3D3;}',
            'Saturday' => '.fc-sat {background-color: #D3D3D3;}',
        ];
        if (!empty($week_off) && isset($weekOffStyles[$week_off])) {
            $style = $weekOffStyles[$week_off];
        }

        $leave_days = DB::table('leave_managements')
            ->select('leave_date as date', 'reason', 'leave_type', 'leave_mode')
            ->where('beautician_id', $user_id)
            ->get()
            ->toArray();

        $booking_service = DB::table('booking_services')
            ->join('bookings', 'booking_services.booking_id', 'bookings.booking_id')
            ->select(
                'appointment_date',
                'beautician_id',
                'slot_start',
                'booking_services.status'
            )
            ->where('beautician_id', $user_id)
            ->whereNotNull('bookings.booking_code')
            ->where('bookings.booking_code', '!=', 'draft')
            ->where('line_total', '>=', 0)
            ->get()
            ->toArray();

        $now = date('Y-m-d H:i:s');
        foreach ($booking_service as $booking) {
            $slotStart = $booking->slot_start ?: '00:00:00';
            $appointmentDate = trim($booking->appointment_date . ' ' . $slotStart);

            // Skip future refunded services
            if ($booking->status == 'refunded' && $appointmentDate > $now) {
                continue;
            }

            if ($booking->status == 'start') {
                $color = Config::get('constants.service_status_color.on_going');
            } elseif ($booking->status == 'completed') {
                $color = Config::get('constants.service_status_color.completed');
            } elseif ($appointmentDate > $now) {
                $color = Config::get('constants.service_status_color.pending');
            } else {
                $color = Config::get('constants.service_status_color.expired');
            }

            $calender[] = [
                'title' => 'Booking - ' . $slotStart,
                'date' => $booking->appointment_date,
                'color' => $color ?: '#0070c2',
                'url' => route('booking.details', [
                    'id' => $booking->beautician_id,
                    'date' => $booking->appointment_date,
                ]),
                'reason' => "<span style='color:#ffb800;font:bold 9pt arial'>Booking</span>",
            ];
        }

        foreach ($leave_days as $leave) {
            $str = '';
            $color = '';

            if ($leave->leave_mode == 'week_off') {
                $str = 'Week Off';
                $color = 'rgba(25, 153, 0, 0.2)';
            } elseif ($leave->leave_type == 'full') {
                $str = 'Full Leave';
                $color = 'rgba(208, 52, 44, 0.2)';
            } elseif ($leave->leave_type == 'first_half') {
                $str = 'First Half Leave';
                $color = 'rgba(208, 52, 44, 0.2)';
            } elseif ($leave->leave_type == 'second_half') {
                $str = 'Second Half Leave';
                $color = 'rgba(208, 52, 44, 0.2)';
            } else {
                $str = 'Leave';
                $color = 'rgba(208, 52, 44, 0.2)';
            }

            $reasonText = e($leave->reason ?? $str);

            $calender[] = [
                'title' => $str,
                'date' => $leave->date,
                'color' => $color,
                'reason' => "<span style='color:#FFFFFF;font:bold 9pt arial'>{$reasonText}</span>",
            ];
        }

        $user_calender = json_encode($calender);

        return view('appointment.booking-service-list.calender', compact('user_calender', 'user_name', 'style'));
    }
    public function allBeaticianCalender(Request $request)
    {
        $searchName = trim((string) $request->input('name', ''));
        $roleName = trim((string) $request->input('role', ''));
        $userModelType = \App\Models\User::class;

        $roles = \App\Models\AclRole::query()
            ->orderBy('name')
            ->pluck('name', 'name');

        $user_records = DB::table('beauticians')
            ->join('users', 'beauticians.beautician_id', '=', 'users.id')
            ->join('employees', 'employees.user_id', '=', 'beauticians.beautician_id')
            ->where('beauticians.status', 'A')
            ->where('employees.status', 'active')
            ->where('users.user_status', 'active')
            ->select(
                'beauticians.beautician_id as user_id',
                'beauticians.beautician_code',
                'users.name as user_name',
                'users.mobile as user_mobile'
            )
            ->when($searchName !== '', function ($query) use ($searchName) {
                $query->where(function ($q) use ($searchName) {
                    $q->where('users.name', 'like', '%' . $searchName . '%')
                        ->orWhere('users.username', 'like', '%' . $searchName . '%')
                        ->orWhere('beauticians.beautician_code', 'like', '%' . $searchName . '%');
                });
            })
            ->when($roleName !== '', function ($query) use ($roleName, $userModelType) {
                $query->whereExists(function ($q) use ($roleName, $userModelType) {
                    $q->select(DB::raw(1))
                        ->from('model_has_roles')
                        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                        ->whereColumn('model_has_roles.model_id', 'users.id')
                        ->where('model_has_roles.model_type', $userModelType)
                        ->where('roles.name', $roleName);
                });
            })
            ->orderBy('users.name')
            ->paginate(10)
            ->appends(array_filter([
                'name' => $searchName,
                'role' => $roleName,
            ], function ($value) {
                return $value !== null && $value !== '';
            }));

        $userIds = $user_records->pluck('user_id')->filter()->unique()->values();
        $rolesByUser = collect();
        if ($userIds->isNotEmpty()) {
            $rolesByUser = DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->whereIn('model_has_roles.model_id', $userIds)
                ->where('model_has_roles.model_type', $userModelType)
                ->orderBy('roles.name')
                ->get(['model_has_roles.model_id', 'roles.name'])
                ->groupBy('model_id')
                ->map(function ($rows) {
                    return $rows->pluck('name')->unique()->values();
                });
        }

        foreach ($user_records as $user) {
            $user->role_names = $rolesByUser->get($user->user_id, collect());
        }

        return view('appointment.booking-service-list.listAllBeaticiancalender', compact(
            'user_records',
            'searchName',
            'roleName',
            'roles'
        ));
    }
  public function bookingCard(Request $request)
  {
    $booking_record = array();
    $service_id = $request->id;
    $booking_record = DB::table('booking_services')
      ->join('bookings', 'booking_services.booking_id', '=', 'bookings.booking_id')
      ->select(
        'bookings.client_name',
        'bookings.booking_code',
        'booking_services.service_name',
        'booking_services.duration_minutes',
        'booking_services.appointment_date',
        'booking_services.slot_start'
      )
      ->where('booking_services.booking_service_id', $service_id)
      ->get();
    if (count($booking_record) > 0) {
      $booking_record = $booking_record;
    }
    return view('appointment.booking-service-list.bookingCard', compact('booking_record'));
  }

    public function allBookingDetails(Request $request)
  {
    $booking_record = [];
    $user_id = $request->id;
    $date = $request->date;

    if (empty($user_id) || empty($date)) {
      return redirect()->route('all.beatician.calender')
        ->with('error', 'Beautician and date are required.');
    }

    $user = DB::table('users')
      ->select('name')
      ->where('id', $user_id)
      ->first();

    if (!$user) {
      return redirect()->route('all.beatician.calender')
        ->with('error', 'Beautician not found.');
    }

    $booking_record = DB::table('booking_services')
      ->join('bookings', 'booking_services.booking_id', '=', 'bookings.booking_id')
      ->select(
        'bookings.client_name',
        'bookings.booking_code',
        'booking_services.service_name',
        'booking_services.duration_minutes',
        'booking_services.appointment_date',
        'booking_services.booking_service_id',
        'booking_services.status',
        'booking_services.slot_start'
      )
      ->where('beautician_id', $user_id)
      ->whereDate('booking_services.appointment_date', $date)
      ->whereNotNull('bookings.booking_code')
      ->where('bookings.booking_code', '!=', 'draft')
      ->where('line_total', '>=', 0)
      ->get();

    $user_name = $user->name ?? 'Beautician';

    return view('appointment.booking-service-list.bookingList', compact('booking_record', 'user_name'));
  }
}
