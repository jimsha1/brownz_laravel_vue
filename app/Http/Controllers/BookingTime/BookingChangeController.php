<?php

namespace App\Http\Controllers\BookingTime;

use App\Http\Controllers\Controller;
use App\Models\Beautician;
use App\Models\BeauticianService;
use App\Models\BookingService;
use App\Models\Client;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceTypeManagement;
use App\Models\Shift;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BookingChangeController extends Controller
{

    // function hoursRange($lower = 0, $upper = 86400, $step = 60, $format = '')
    // {

    //     $times = array();

    //     if (empty($format)) {
    //         $format = 'g:i a';
    //     }

    //     foreach (range($lower, $upper, $step) as $increment) {
    //         $increment = gmdate('H:i', $increment);

    //         list($hour, $minutes) = explode(':', $increment);

    //         $date = new DateTime($hour . ':' . $minutes);

    //         $times[(string) $increment] = (string) $increment;
    //         //   $date->format($format)
    //     }

    //     return $times;
    // }

    // public function __invoke(Request $request)
    // {


    //     $day_text = "";
    //     $BookedSlots = array();
    //     $is_reshedule = 1;
    //     if ($request->is_reshedule == 1) {
    //         $is_reshedule = 0;
    //     }

    //     $shift = array();
    //     $db_shift = "";

    //     // $session = new Session();
    //     $beautician_flag = false;
    //     if ($request->date == 'date') {
    //         $request->date = date("Y-m-d");
    //     }
    //     // $db_Date =  date("Y-m-d");
    //     if ($request->date != "") {
    //         $db_Date = date("Y-m-d", strtotime($request->date));
    //         $day_text = date("l", strtotime($request->date));
    //     }
    //     //================================manage week off==============================================================
    //     $weekoff = "";
    //     if ($request->beautician) {
    //         $user_res = DB::table('users')->where('id', '=', $request->beautician)
    //             ->select('week_off')
    //             ->first();
    //         $weekoff = $user_res->week_off;
    //     }

    //     //====================================================================================================
    //     // dd(date)
    //     $serached_array = [
    //         'service_id' => $request->service,
    //         'shift_id' => $request->shift_id,
    //         'duration' => $request->duration,
    //         'service_type_id' => $request->service_type,
    //     ];

    //     //   $session->set('serached_array', $serached_array);
    //     $selected_beutician = isset($request->beautician) ? $request->beautician : NULL;
    //     if ($request->service) {


    //         // $db_beauticians = Beautician::join('users', 'beauticians.beautician_id', '=', 'users.id')
    //         //     ->join('users as c_user', 'bt_user.created_by', '=', 'c_user.id')
    //         //     ->join('users as u_user', 'bt_user.updated_by', '=', 'u_user.id')
    //         //     ->join('beautician_services', 'beautician_services.beautician_id', 'beauticians.beautician_id')
    //         //     ->where('beautician_services.service_id', $request->service)
    //         //     ->where('beautician_services.status', 1) //1 = Active
    //         //     ->where('beauticians.status', 'A') //A = Active
    //         //     // ->where('bt_user.shift_id', $shift_id)
    //         //     ->orderBy('beauticians.beautician_id')
    //         //     ->select(
    //         //         'beauticians.*',
    //         //         'bt_user.*',
    //         //         ...user_relation_columns('c_user', 'u_user')
    //         //     )

    //         //     ->get();


    //         $db_beauticians = Beautician::join('users', 'beauticians.beautician_id', '=', 'users.id')
    //             ->join('beautician_services', 'beautician_services.beautician_id', '=', 'beauticians.beautician_id')
    //             ->where('beautician_services.service_id', $request->service)
    //             ->where('beauticians.status', 'A')
    //             ->where('beautician_services.status', 1)
    //             ->orderBy('beauticians.beautician_id')
    //             ->select('beauticians.*', 'users.*', 'beautician_services.*')
    //             ->get();


    //         //   dd($db_beauticians);
    //         foreach ($db_beauticians as $row) {
    //             if ($row->beautician_id == $request->beautician) {
    //                 $beautician_flag = true;
    //             }
    //             if ($selected_beutician == NULL && !empty($db_beauticians)) {
    //                 $selected_beutician = $db_beauticians[0]['beautician_id'];
    //             }
    //             $row->photo_src_url = beautician_photo_url($row->profile_photo_path, 250, 250, 1);
    //         }

    //         $booked_slots = [];
    //         //   dd($request->all());
    //         if (!empty($request->date) && !empty($request->service)) {
    //             $booked_slots = \App\Models\BookingService::join('bookings', 'booking_services.booking_id', '=', 'bookings.booking_id')
    //                 ->where('appointment_date', $db_Date)
    //                 ->where('bookings.canceled_at', null)
    //                 ->where('beautician_id', $request->beautician)
    //                 // ->pluck('slot_start')->toArray();
    //                 ->select('booking_service_id', 'appointment_date', 'service_id', 'beautician_id', 'slot_start', 'slot_end')
    //                 ->get();
    //         }
    //         //   dd($booked_slots);

    //         $service = Service::find($request->service);

    //         $tax_percent = (isset($service->tax_percent) ? $service->tax_percent : 15);

    //         $beaut_leave = \App\Models\LeaveManagement::where('beautician_id', $request->beautician)
    //             ->where('leave_status', 'approved')
    //             ->where('leave_date', date('Y-m-d', strtotime(str_replace('-', '/', $db_Date))))
    //             ->first();

    //         // dd($beaut_leave);
    //         if (empty($beaut_leave) || $beaut_leave->leave_type != \App\Models\LeaveManagement::LEAVE_TYPE_FULL) {

    //             if (empty($beaut_leave)) {
    //                 $start = \App\Models\Beautician::DEFAULT_SHIFT_START_TIME;
    //                 $end = \App\Models\Beautician::DEFAULT_SHIFT_END_TIME;
    //             } else {
    //                 // dd($beaut_leave);
    //                 $start = $beaut_leave->leave_type == \App\Models\LeaveManagement::LEAVE_TYPE_FIRST_HALF ? \App\Models\Beautician::DEFAULT_SHIFT_HALF_TIME : \App\Models\Beautician::DEFAULT_SHIFT_START_TIME;
    //                 $end = $beaut_leave->leave_type == \App\Models\LeaveManagement::LEAVE_TYPE_SECOND_HALF ? \App\Models\Beautician::DEFAULT_SHIFT_HALF_TIME : \App\Models\Beautician::DEFAULT_SHIFT_END_TIME;
    //             }

    //             // $start= "9:00";
    //             // $end = "24:00";
    //             // $int = $service->duration_minutes;

    //             $shift_prices = DB::table('price_services')->where([['price_services.service_id', '=', $request->service], ['price_services.service_type_id', '=', $request->service_type]])->pluck('price_services.shift_id');

    //             $db_shift = Shift::whereIn('shift_id', $shift_prices)->get();

    //             if ($request->duration) {
    //                 $int = $request->duration;
    //             } else {
    //                 $int = "10";
    //             }

    //             if ($request->shift_id && $request->shift_id < 0) {

    //                 $shift = Shift::where('shift_id', $request->shift_id)->first();
    //             } else {
    //                 $shift = Shift::orderBy('shift_id')->first();
    //             }

    //             if ($request->duration) {
    //                 $int = $request->duration;
    //             } else {
    //                 $int = "10";
    //             }

    //             if ($request->shift_id) {

    //                 $shift = Shift::where('shift_id', $request->shift_id)->first();
    //             } else {
    //                 $shift = Shift::orderBy('shift_id')->first();
    //             }

    //             $int *= 60;
    //             $start = strtotime($shift->start_time);
    //             if (strtotime($shift->end_time) == strtotime('00:00:00')) {
    //                 $end = strtotime('24:00:00');
    //             } else {
    //                 $end = strtotime($shift->end_time);
    //             }
    //             for ($i = $start; $i < $end;) {
    //                 // $res[] = date("H:i", $i) ;
    //                 $temp_slot = date('H:i:s', $i);
    //                 $available = false;


    //                 foreach ($booked_slots as $bk_row) {
    //                     if ($bk_row->slot_start == $temp_slot) {
    //                         $temp_next_slot = date('H:i:s', $i + $int);
    //                         if (!$bk_row->slot_end || ($bk_row->slot_end >= $temp_next_slot)) {
    //                             $available = true;
    //                         }

    //                         break;
    //                     }
    //                 }

    //                 $res[] = [
    //                     'date' => date("H:i", $i),
    //                     'is_booked' => $available,
    //                     'is_active' => false
    //                 ];

    //                 $i += $int;
    //             }
    //         } else {
    //             $res = [];
    //         }
    //     } else {
    //         $res = [];
    //         // $db_beauticians = [];
    //         $db_beauticians = Beautician::join('users as bt_user', 'beauticians.beautician_id', '=', 'bt_user.id')
    //             ->join('users as c_user', 'bt_user.created_by', '=', 'c_user.id')
    //             ->join('users as u_user', 'bt_user.updated_by', '=', 'u_user.id')
    //             // ->join('beautician_services', 'beautician_services.beautician_id', 'beauticians.beautician_id')
    //             // ->where('beautician_services.service_id', $request->service)
    //             // ->where('beautician_services.status', 1) //1 = Active
    //             ->where('beauticians.status', 'A') //A = Active
    //             //->where('bt_user.shift_id', $shift_id)
    //             ->orderBy('beauticians.beautician_id')
    //             ->select(
    //                 'beauticians.*',
    //                 'bt_user.*',
    //                 ...user_relation_columns('c_user', 'u_user')
    //             )

    //             ->get();

    //         foreach ($db_beauticians as $row) {
    //             if ($row->beautician_id == $request->beautician) {
    //                 $beautician_flag = true;
    //             }
    //             if ($selected_beutician == NULL && !empty($db_beauticians)) {
    //                 $selected_beutician = $db_beauticians[0]['beautician_id'];
    //             }
    //             $row->photo_src_url = beautician_photo_url($row->profile_photo_path, 250, 250, 1);
    //         }
    //         //
    //         $tax_percent = '';
    //     }




    //     $durations_data = DB::table('price_services')->where([['price_services.service_id', '=', $request->service], ['price_services.service_type_id', '=', $request->service_type], ['price_services.shift_id', '=', $request->shift_id]])->orderBy('price_services.duration')->pluck('price_services.duration')->toArray();

    //     $durations = array_map(function ($value) {
    //         $h = floor($value / 60) ? floor($value / 60) . ' hours' : '';
    //         $m = $value % 60 ? $value % 60 . ' minutes' : '';
    //         return $h && $m ? $h . ' and ' . $m : $h . $m;
    //     }, $durations_data);

    //     $new_duration = array_map(null, $durations_data, $durations);

    //     if (empty($new_duration)) {
    //         $new_res = '';
    //     } else {
    //         if ($db_Date == date("Y-m-d")) {
    //             $new_res = array_filter($res, function ($var) {
    //                 return (strtotime($var['date']) >= strtotime(date('H:i')));
    //             });
    //         } else {
    //             $new_res = $res;
    //         }
    //     }

    //     if (empty($request->duration)) {
    //         $new_res = '';
    //     }

    //     $service_price = DB::table('price_services')->where([['price_services.service_id', '=', $request->service], ['price_services.service_type_id', '=', $request->service_type], ['price_services.shift_id', '=', $request->shift_id], ['price_services.duration', '=', $request->duration]])->select('price_services.*')->first();

    //     if (!empty($service_price)) {
    //         $amount = $service_price->price;
    //         $price = $service_price->price;
    //     } else {
    //         $price = '';
    //         $new_res = '';
    //     }

    //     //=================================time slot section ================================================
    //     $is_current_date = false;
    //     if ($request->date == "") {
    //         $request->date = date('y-m-d');
    //     }
    //     if (Carbon::parse($request->date) == Carbon::today()) {
    //         $is_current_date = true;
    //         $current_time = date('H:i');
    //         // date('H:i') < $value
    //     } else {
    //         $current_time = '00:00';
    //     }

    //     $booked_slots = \App\Models\BookingService::where('appointment_date', Carbon::parse($request->date))
    //         ->where('line_total', '>=', 0)
    //         ->where('beautician_id', $request->beautician)
    //         ->select('slot_start')
    //         ->get();

    //     // $leavedate = DB::table('leave_managements')->where('beautician_id', '=', $request->beautician)
    //     //     ->where('leave_date', '=', Carbon::parse($request->date))
    //     //     ->get();
    //     $leaveDetails = DB::table('leave_managements')
    //         ->where('beautician_id', $request->beautician)
    //         ->where('leave_date', Carbon::parse($request->date))
    //         ->where('leave_status', 'approved')
    //         ->first();

    //     $time_slot = [];
    //     $arr = $this->hoursRange(); // Generate slots e.g., ['09:00', '09:30', ...]
    //     $alreadyBooked = [];
    //     $current_time = Carbon::now()->format('H:i');
    //     $selected_date = Carbon::parse($request->date)->format('Y-m-d');
    //     $today_date = Carbon::now()->format('Y-m-d');

    //     // Retrieve booked slots
    //     foreach ($booked_slots as $bk_slot) {
    //         $convert_bk_time = date('H:i', strtotime($bk_slot->slot_start));
    //         array_push($alreadyBooked, $convert_bk_time);
    //     }

    //     $BookedSlots = $alreadyBooked;

    //     // Retrieve the appropriate shift
    //     $shift = $request->shift_id
    //         ? Shift::where('shift_id', $request->shift_id)->first()
    //         : Shift::orderBy('shift_id')->first();

    //     if ($shift->end_time == "00:00:00") {
    //         $shift->end_time = "21:30:00";
    //     }

    //     // Define leave timings
    //     $leaveStart = null;
    //     $leaveEnd = null;

    //     if ($leaveDetails) {
    //         if ($leaveDetails->leave_type == 'full') {
    //             $leaveStart = '00:00';
    //             $leaveEnd = '23:59';
    //         } elseif ($leaveDetails->leave_type == 'first_half') {
    //             $leaveStart = substr($shift->start_time, 0, -3); // Start of shift
    //             $leaveEnd = '19:00';
    //         } elseif ($leaveDetails->leave_type == 'second_half') {
    //             $leaveStart = '11:30';
    //             $leaveEnd = substr($shift->end_time, 0, -3); // End of shift
    //         }
    //     }

    //     // Generate time slots
    //     foreach ($arr as $value) {
    //         $isLeaveSlot = $leaveStart && $leaveEnd && $value >= $leaveStart && $value < $leaveEnd;



    //         if (
    //             // For today's date, filter based on current time
    //             (($selected_date === $today_date && $current_time <= $value) || $selected_date !== $today_date) &&
    //             substr($shift->start_time, 0, -3) <= $value &&
    //             $shift->end_time >= $value &&
    //             !$isLeaveSlot // Exclude leave slots
    //         ) {
    //             if (count($booked_slots) > 0) {
    //                 if (!in_array($value, $alreadyBooked)) {
    //                     array_push($time_slot, $value);
    //                 }
    //             } else {
    //                 array_push($time_slot, $value);
    //             }
    //         }
    //     }


    //     if ($day_text == $weekoff) {
    //         $time_slot = [];
    //     }

    //     return response()->json([
    //         'beauticians' => $db_beauticians,
    //         'beautician_id' => $request->beautician,
    //         'beautician_flag' => $beautician_flag,
    //         'time_slot' => $time_slot,
    //         'db_shift' => $db_shift,
    //         'new_duration' => $new_duration,
    //         'alreadyBooked' => $BookedSlots,
    //         // 'all_db_shifts' => $all_db_shifts,
    //         // 'duration_minutes' => $duration_minutes,
    //         'price' => $price,

    //     ]);
    // }

    protected function hoursRange($start = '00:00', $end = '23:59', $interval = 1)
    {
        $times = [];
        $startTime = Carbon::parse($start);
        $endTime = Carbon::parse($end);
        while ($startTime <= $endTime) {
            $times[] = $startTime->format('H:i');
            $startTime->addMinutes($interval);
        }
        return $times;
    }

    public function __invoke(Request $request)
    {
        $day_text = "";
        $BookedSlots = array();
        $is_reshedule = 1;
        if ($request->is_reshedule == 1) {
            $is_reshedule = 0;
        }

        $beautician_flag = false;
        if ($request->date == 'date') {
            $request->date = date("Y-m-d");
        }

        if ($request->date != "") {
            $db_Date = date("Y-m-d", strtotime($request->date));
            $day_text = date("l", strtotime($request->date));
        }

        //================================manage week off==============================================================
        $weekoff = "";
        if ($request->beautician) {
            $user_res = DB::table('users')->where('id', '=', $request->beautician)
                ->select('week_off')
                ->first();
            $weekoff = $user_res->week_off ?? '';
        }

        //====================================================================================================

        $serached_array = [
            'service_id' => $request->service,
            'duration' => $request->duration,
            'service_type_id' => $request->service_type,
        ];

        $selected_beutician = isset($request->beautician) ? $request->beautician : NULL;
        $db_beauticians = [];
        if ($request->service) {
            $db_beauticians = Beautician::join('users', 'beauticians.beautician_id', '=', 'users.id')
                ->join('beautician_services', 'beautician_services.beautician_id', '=', 'beauticians.beautician_id')
                ->where('beautician_services.service_id', $request->service)
                ->where('beautician_services.status', 1)
                ->activeListed()
                ->orderBy('beauticians.beautician_id')
                ->select('beauticians.*', 'users.*', 'beautician_services.*')
                ->get();

            foreach ($db_beauticians as $row) {
                if ($row->beautician_id == $request->beautician) {
                    $beautician_flag = true;
                }
                if ($selected_beutician == NULL && !empty($db_beauticians)) {
                    $selected_beutician = $db_beauticians[0]['beautician_id'];
                }
                $row->photo_src_url = beautician_photo_url($row->profile_photo_path, 250, 250, 1);
            }
        } else {
            $db_beauticians = Beautician::join('users as bt_user', 'beauticians.beautician_id', '=', 'bt_user.id')
                // ->join('users as c_user', 'bt_user.created_by', '=', 'c_user.id')
                // ->join('users as u_user', 'bt_user.updated_by', '=', 'u_user.id')
                ->activeListed()
                ->orderBy('beauticians.beautician_id')
                ->select(
                    'beauticians.*',
                    'bt_user.*'
                    // ,
                    // ...user_relation_columns('c_user', 'u_user')
                )
                ->get();

            foreach ($db_beauticians as $row) {
                if ($row->beautician_id == $request->beautician) {
                    $beautician_flag = true;
                }
                if ($selected_beutician == NULL && !empty($db_beauticians)) {
                    $selected_beutician = $db_beauticians[0]['beautician_id'];
                }
                $row->photo_src_url = beautician_photo_url($row->profile_photo_path, 250, 250, 1);
            }
        }

        $service = $request->service ? Service::find($request->service) : null;
        $tax_percent = $service ? (isset($service->tax_percent) ? $service->tax_percent : 15) : '';

        //=================================time slot section for selected beautician================================
        $is_current_date = false;
        if ($request->date == "") {
            $request->date = date('Y-m-d');
        }
        if (Carbon::parse($request->date)->isToday()) {
            $is_current_date = true;
            $current_time = date('H:i');
        } else {
            $current_time = '00:00';
        }

        $time_slot = [];
        if ($selected_beutician) {
            // Retrieve booked slots for the selected beautician
            $booked_slots = BookingService::where('appointment_date', Carbon::parse($request->date))
                ->where('line_total', '>=', 0)
                ->where('beautician_id', $selected_beutician)
                ->where('bookings.canceled_at', null)
                ->join('bookings', 'booking_services.booking_id', '=', 'bookings.booking_id')
                ->select('slot_start', 'slot_end')
                ->get();

            // Retrieve leave details for the selected beautician
            $leaveDetails = DB::table('leave_managements')
                ->where('beautician_id', $selected_beutician)
                ->where('leave_date', Carbon::parse($request->date))
                ->where('leave_status', 'approved')
                ->first();

            $arr = $this->hoursRange(); 
            $alreadyBooked = [];
            $selected_date = Carbon::parse($request->date)->format('Y-m-d');
            $today_date = Carbon::now()->format('Y-m-d');

            // Retrieve booked slots
            foreach ($booked_slots as $bk_slot) {
                $start = Carbon::parse($bk_slot->slot_start);
                $end = Carbon::parse($bk_slot->slot_end);
                while ($start <= $end) {
                    $alreadyBooked[] = $start->format('H:i');
                    $start->addMinute();
                }
            }

            $BookedSlots = $alreadyBooked;

            // Define leave timings
            $leaveStart = null;
            $leaveEnd = null;
            if ($leaveDetails) {
                if ($leaveDetails->leave_type == 'full') {
                    $leaveStart = '00:00';
                    $leaveEnd = '23:59';
                } elseif ($leaveDetails->leave_type == 'first_half') {
                    $leaveStart = '00:00';
                    $leaveEnd = '12:00';
                } elseif ($leaveDetails->leave_type == 'second_half') {
                    $leaveStart = '12:00';
                    $leaveEnd = '23:59';
                }
            }

            // Generate time slots for 24-hour period
            foreach ($arr as $value) {
                $isLeaveSlot = $leaveStart && $leaveEnd && $value >= $leaveStart && $value <= $leaveEnd;

                if (
                    // For today's date, filter based on current time
                    (($selected_date === $today_date && $current_time <= $value) || $selected_date !== $today_date) &&
                    !$isLeaveSlot // Exclude leave slots
                ) {
                    if (count($booked_slots) > 0) {
                        if (!in_array($value, $alreadyBooked)) {
                            array_push($time_slot, $value);
                        }
                    } else {
                        array_push($time_slot, $value);
                    }
                }
            }

            // Clear time slots if the selected date is the beautician's week-off
            if ($day_text == $weekoff) {
                $time_slot = [];
            }
        }

        // Handle duration and price without shift_id
        $durations_data = DB::table('price_services')
            ->where([
                ['price_services.service_id', '=', $request->service],
                ['price_services.service_type_id', '=', $request->service_type]
            ])
            ->orderBy('price_services.duration')
            ->pluck('price_services.duration')
            ->toArray();

        $durations = array_map(function ($value) {
            $h = floor($value / 60) ? floor($value / 60) . ' hours' : '';
            $m = $value % 60 ? $value % 60 . ' minutes' : '';
            return $h && $m ? $h . ' and ' . $m : $h . $m;
        }, $durations_data);

        $new_duration = array_map(null, $durations_data, $durations);

        $service_price = DB::table('price_services')
            ->where([
                ['price_services.service_id', '=', $request->service],
                ['price_services.service_type_id', '=', $request->service_type],
                ['price_services.duration', '=', $request->duration]
            ])
            ->select('price_services.*')
            ->first();

        $price = !empty($service_price) ? $service_price->price : '';

        $new_res = '';
        if (!empty($new_duration)) {
            if ($db_Date == date("Y-m-d")) {
                $new_res = array_filter($time_slot, function ($var) use ($current_time) {
                    return (strtotime($var) >= strtotime($current_time));
                });
            } else {
                $new_res = $time_slot;
            }
        }

        if (empty($request->duration)) {
            $new_res = '';
        }

        return response()->json([
            'beauticians' => $db_beauticians,
            'beautician_id' => $request->beautician,
            'beautician_flag' => $beautician_flag,
            'time_slot' => $time_slot, // Single time_slot array for the selected beautician
            'new_duration' => $new_duration,
            'alreadyBooked' => $BookedSlots,
            'price' => $price,
        ]);
    }

    public function allBooking(Request $request)
    {

        $all_db_shifts = Shift::all();
        $request->shift_id = $all_db_shifts[0]->shift_id;
        $beautician_flag = false;
        $db_Date = date("Y-m-d");
        $durations_data = DB::table('price_services')->where([['price_services.service_id', '=', $request->service], ['price_services.service_type_id', '=', $request->service_type], ['price_services.shift_id', '=', $request->shift_id]])->orderBy('price_services.duration')->pluck('price_services.duration')->toArray();
        if (count($durations_data) > 0) {
            $request->duration = $durations_data[0];
        }
        $serached_array = [
            'service_id' => $request->service,
            'shift_id' => $request->shift_id,
            'duration' => $request->duration,
            'service_type_id' => $request->service_type,
        ];

        //   $session->set('serached_array', $serached_array);
        $selected_beutician = isset($request->beautician) ? $request->beautician : NULL;
        if ($request->service) {


            $db_beauticians = Beautician::join('users as bt_user', 'beauticians.beautician_id', '=', 'bt_user.id')
                ->join('users as c_user', 'bt_user.created_by', '=', 'c_user.id')
                ->join('users as u_user', 'bt_user.updated_by', '=', 'u_user.id')
                ->join('beautician_services', 'beautician_services.beautician_id', 'beauticians.beautician_id')
                ->where('beautician_services.service_id', $request->service)
                ->where('bt_user.shift_id', $request->shift_id)
                ->activeListed()
                ->orderBy('beauticians.beautician_id')
                ->select(
                    'beauticians.*',
                    'bt_user.*',
                    ...user_relation_columns('c_user', 'u_user')
                )

                ->get();
            foreach ($db_beauticians as $row) {
                if ($row->beautician_id == $request->beautician) {
                    $beautician_flag = true;
                }
                if ($selected_beutician == NULL && !empty($db_beauticians)) {
                    $selected_beutician = $db_beauticians[0]['beautician_id'];
                }
                $row->photo_src_url = beautician_photo_url($row->profile_photo_path, 250, 250, 1);
            }
            if (count($db_beauticians) > 0) {
                $selected_beutician = $db_beauticians[0]['beautician_id'];
            }
            $booked_slots = [];
            // dd($request->all());
            if (!empty($request->date) && !empty($request->service)) {
                $booked_slots = \App\Models\BookingService::where('appointment_date', $db_Date)
                    ->where('beautician_id', $selected_beutician)
                    // ->pluck('slot_start')->toArray();
                    ->select('booking_service_id', 'appointment_date', 'service_id', 'beautician_id', 'slot_start', 'slot_end')
                    ->get();
            }


            $service = Service::find($request->service);

            $tax_percent = $service->tax_percent;

            $beaut_leave = \App\Models\LeaveManagement::where('beautician_id', $selected_beutician)
                ->where('leave_date', date('Y-m-d', strtotime(str_replace('-', '/', $db_Date))))
                ->where('leave_status', 'approved')
                ->first();

            //   $beaut_leave);
            if (empty($beaut_leave) || $beaut_leave->leave_type != \App\Models\LeaveManagement::LEAVE_TYPE_FULL) {


                if (empty($beaut_leave)) {
                    $start = \App\Models\Beautician::DEFAULT_SHIFT_START_TIME;
                    $end = \App\Models\Beautician::DEFAULT_SHIFT_END_TIME;
                } else {
                    $start = $beaut_leave->leave_type == \App\Models\LeaveManagement::LEAVE_TYPE_FIRST_HALF ? \App\Models\Beautician::DEFAULT_SHIFT_HALF_TIME : \App\Models\Beautician::DEFAULT_SHIFT_START_TIME;
                    //   return $start;
                    $end = $beaut_leave->leave_type == \App\Models\LeaveManagement::LEAVE_TYPE_SECOND_HALF ? \App\Models\Beautician::DEFAULT_SHIFT_HALF_TIME : \App\Models\Beautician::DEFAULT_SHIFT_END_TIME;
                }

                // $start= "9:00";
                // $end = "24:00";
                // $int = $service->duration_minutes;

                $shift_prices = DB::table('price_services')->where([['price_services.service_id', '=', $request->service], ['price_services.service_type_id', '=', $request->service_type]])->pluck('price_services.shift_id');

                $db_shift = Shift::whereIn('shift_id', $shift_prices)->get();

                if ($request->duration) {
                    $int = $request->duration;
                } else {
                    $int = "10";
                }

                if ($request->shift_id) {

                    $shift = Shift::where('shift_id', $request->shift_id)->first();
                } else {
                    $shift = Shift::orderBy('shift_id')->first();
                }

                $int *= 60;
                $start = strtotime($shift->start_time);
                if (strtotime($shift->end_time) == strtotime('00:00:00')) {
                    $end = strtotime('24:00:00');
                } else {
                    $end = strtotime($shift->end_time);
                }
                for ($i = $start; $i < $end;) {
                    // $res[] = date("H:i", $i) ;
                    $temp_slot = date('H:i:s', $i);
                    $available = false;


                    foreach ($booked_slots as $bk_row) {
                        if ($bk_row->slot_start == $temp_slot) {
                            $temp_next_slot = date('H:i:s', $i + $int);
                            if (!$bk_row->slot_end || ($bk_row->slot_end >= $temp_next_slot)) {
                                $available = true;
                            }

                            break;
                        }
                    }

                    $res[] = [
                        'date' => date("H:i", $i),
                        'is_booked' => $available,
                        'is_active' => false
                    ];

                    $i += $int;
                }
            } else {
                $res = [];
            }
        } else {
            $res = [];
            $db_beauticians = [];
            $tax_percent = '';
        }


        $durations_data = DB::table('price_services')->where([['price_services.service_id', '=', $request->service], ['price_services.service_type_id', '=', $request->service_type], ['price_services.shift_id', '=', $request->shift_id]])->orderBy('price_services.duration')->pluck('price_services.duration')->toArray();

        $durations = array_map(function ($value) {
            $h = floor($value / 60) ? floor($value / 60) . ' hours' : '';
            $m = $value % 60 ? $value % 60 . ' minutes' : '';
            return $h && $m ? $h . ' and ' . $m : $h . $m;
        }, $durations_data);

        $new_duration = array_map(null, $durations_data, $durations);
        // return  $new_duration;
        if (empty($new_duration)) {
            $new_res = '';
        } else {
            if ($db_Date == date("Y-m-d")) {
                $new_res = array_filter($res, function ($var) {
                    return (strtotime($var['date']) >= strtotime(date('H:i')));
                });
            } else {
                $new_res = $res;
            }
        }

        if (empty($request->duration)) {
            $new_res = '';
        }

        $service_price = DB::table('price_services')->where([['price_services.service_id', '=', $request->service], ['price_services.service_type_id', '=', $request->service_type], ['price_services.shift_id', '=', $request->shift_id], ['price_services.duration', '=', $request->duration]])->select('price_services.*')->first();

        if (!empty($service_price)) {
            $amount = $service_price->price;
            $price = percentage_increase($amount, $tax_percent);
        } else {
            $price = '';
            $new_res = '';
        }
        $all_db_shifts = Shift::all();
        return response()->json([
            'beauticians' => $db_beauticians,
            'time_slot' => $new_res,
            'beautician_id' => $request->beautician,
            'beautician_flag' => $beautician_flag,
            'db_shift' => $db_shift,
            'new_duration' => $new_duration,
            'all_db_shifts' => $all_db_shifts,
            // 'duration_minutes' => $duration_minutes,
            'price' => $price,

        ]);
    }

    public function allBookingExisting(Request $request)
    {
        // dd($request);
        $beautician_flag = false;
        if ($request->date != null) {
            $db_Date = $request->date;
        } else {
            $db_Date = date("Y-m-d");
        }

        $durations_data = DB::table('price_services')->where([['price_services.service_id', '=', $request->service], ['price_services.service_type_id', '=', $request->service_type], ['price_services.shift_id', '=', $request->shift_id]])->orderBy('price_services.duration')->pluck('price_services.duration')->toArray();
        if (count($durations_data) > 0) {
            $request->duration = $durations_data[0];
        }
        $serached_array = [
            'service_id' => $request->service,
            'shift_id' => $request->shift_id,
            'duration' => $request->duration,
            'service_type_id' => $request->service_type,
        ];

        //   $session->set('serached_array', $serached_array);
        $selected_beutician = isset($request->beautician) ? $request->beautician : NULL;
        $db_beauticians = [];
        $db_shift = collect();
        if ($request->service) {


            $db_beauticians = Beautician::join('users', 'beauticians.beautician_id', '=', 'users.id')
                ->join('beautician_services', 'beautician_services.beautician_id', '=', 'beauticians.beautician_id')
                ->where('beautician_services.service_id', $request->service)
                ->where('beautician_services.status', 1)
                ->activeListed()
                ->orderBy('beauticians.beautician_id')
                ->select('beauticians.*', 'users.*', 'beautician_services.*')
                ->get();


            foreach ($db_beauticians as $row) {
                if ($row->beautician_id == $request->beautician) {
                    $beautician_flag = true;
                }
                if ($selected_beutician == NULL && !empty($db_beauticians)) {
                    $selected_beutician = $db_beauticians[0]['beautician_id'];
                }
                $row->photo_src_url = beautician_photo_url($row->profile_photo_path, 250, 250, 1);
            }
            if (count($db_beauticians) > 0) {
                $selected_beutician = $db_beauticians[0]['beautician_id'];
            }
            $booked_slots = [];
            // dd($request->all());
            if (!empty($request->date) && !empty($request->service)) {
                $booked_slots = \App\Models\BookingService::where('appointment_date', $db_Date)
                    ->where('beautician_id', $selected_beutician)
                    // ->pluck('slot_start')->toArray();
                    ->select('booking_service_id', 'appointment_date', 'service_id', 'beautician_id', 'slot_start', 'slot_end')
                    ->get();
            }
            $service = Service::find($request->service);
            $tax_percent = $service->tax_percent;
            $beaut_leave = \App\Models\LeaveManagement::where('beautician_id', $selected_beutician)
                ->where('leave_date', date('Y-m-d', strtotime(str_replace('-', '/', $db_Date))))
                ->where('leave_status', 'approved')
                ->first();

            //   $beaut_leave);
            if (empty($beaut_leave) || $beaut_leave->leave_type != \App\Models\LeaveManagement::LEAVE_TYPE_FULL) {


                if (empty($beaut_leave)) {
                    $start = \App\Models\Beautician::DEFAULT_SHIFT_START_TIME;
                    $end = \App\Models\Beautician::DEFAULT_SHIFT_END_TIME;
                } else {
                    $start = $beaut_leave->leave_type == \App\Models\LeaveManagement::LEAVE_TYPE_FIRST_HALF ? \App\Models\Beautician::DEFAULT_SHIFT_HALF_TIME : \App\Models\Beautician::DEFAULT_SHIFT_START_TIME;
                    $end = $beaut_leave->leave_type == \App\Models\LeaveManagement::LEAVE_TYPE_SECOND_HALF ? \App\Models\Beautician::DEFAULT_SHIFT_HALF_TIME : \App\Models\Beautician::DEFAULT_SHIFT_END_TIME;
                }

                // $start= "9:00";
                // $end = "24:00";
                // $int = $service->duration_minutes;

                $shift_prices = DB::table('price_services')->where([['price_services.service_id', '=', $request->service], ['price_services.service_type_id', '=', $request->service_type]])->pluck('price_services.shift_id');

                $db_shift = Shift::whereIn('shift_id', $shift_prices)->get();
                // DD($db_shift);
                if ($request->duration) {
                    $int = $request->duration;
                } else {
                    $int = "10";
                }

                if ($request->shift_id) {

                    $shift = Shift::where('shift_id', $request->shift_id)->first();
                } else {
                    $shift = Shift::orderBy('shift_id')->first();
                }

                $int *= 60;
                $start = strtotime($shift->start_time);
                if (strtotime($shift->end_time) == strtotime('00:00:00')) {
                    $end = strtotime('24:00:00');
                } else {
                    $end = strtotime($shift->end_time);
                }
                for ($i = $start; $i < $end;) {
                    // $res[] = date("H:i", $i) ;
                    $temp_slot = date('H:i:s', $i);
                    $available = false;


                    foreach ($booked_slots as $bk_row) {
                        if ($bk_row->slot_start == $temp_slot) {
                            $temp_next_slot = date('H:i:s', $i + $int);
                            if (!$bk_row->slot_end || ($bk_row->slot_end >= $temp_next_slot)) {
                                $available = true;
                            }

                            break;
                        }
                    }

                    $res[] = [
                        'date' => date("H:i", $i),
                        'is_booked' => $available,
                        'is_active' => false
                    ];

                    $i += $int;
                }
            } else {
                $res = [];
            }
        } else {
            $res = [];
            $db_beauticians = [];
            $tax_percent = '';
        }


        $durations_data = DB::table('price_services')->where([['price_services.service_id', '=', $request->service], ['price_services.service_type_id', '=', $request->service_type], ['price_services.shift_id', '=', $request->shift_id]])->orderBy('price_services.duration')->pluck('price_services.duration')->toArray();

        $durations = array_map(function ($value) {
            $h = floor($value / 60) ? floor($value / 60) . ' hours' : '';
            $m = $value % 60 ? $value % 60 . ' minutes' : '';
            return $h && $m ? $h . ' and ' . $m : $h . $m;
        }, $durations_data);

        $new_duration = array_map(null, $durations_data, $durations);

        if (empty($new_duration)) {
            $new_res = '';
        } else {
            if ($db_Date == date("Y-m-d")) {
                $new_res = array_filter($res, function ($var) {
                    return (strtotime($var['date']) >= strtotime(date('H:i')));
                });
            } else {
                $new_res = $res;
            }
        }

        if (empty($request->duration)) {
            $new_res = '';
        }

        $service_price = DB::table('price_services')->where([['price_services.service_id', '=', $request->service], ['price_services.service_type_id', '=', $request->service_type], ['price_services.shift_id', '=', $request->shift_id], ['price_services.duration', '=', $request->duration]])->select('price_services.*')->first();

        if (!empty($service_price)) {
            $amount = $service_price->price;
            $price = percentage_increase($amount, $tax_percent);
        } else {
            $price = '';
            $new_res = '';
        }
        $all_db_shifts = Shift::all();

        $all_services = Service::where('category_id', $request->category_id)
            ->get();

        $booked_slots = \App\Models\BookingService::where('appointment_date', Carbon::parse($request->date))
            ->where('beautician_id', $request->beautician)
            ->select('slot_start')
            ->get();
        $leavedate = DB::table('leave_managements')->where('beautician_id', '=', $request->beautician)
            ->where('leave_date', '=', Carbon::parse($request->date))
            ->get();

        $time_slot = array();
        $arr = $this->hoursRange();
        if (Carbon::parse($request->date) == Carbon::today()) {
            $is_current_date = true;
            $current_time = date('H:i');
            // date('H:i') < $value
        } else {
            $current_time = '00:00';
        }
        // $time_slot = array();
        // $arr = $this->hoursRange();
        // if (count($leavedate)  == 0) {
        //   $i++;
        //   $alreadyBooked = array();
        //   foreach ($booked_slots as $bk_slot) {
        //     $convert_bk_time = date('H:i', strtotime($bk_slot->slot_start));
        //     array_push($alreadyBooked, $convert_bk_time);
        //   }

        //   // dd($alreadyBooked);

        //   foreach ($arr as $value) {
        //     $i++;

        //     if ($current_time < $value && $shift->start_time <= $value && $shift->end_time >= $value) {
        //       if (count($booked_slots) > 0) {
        //         if (!in_array($value, $alreadyBooked)) {
        //           array_push($time_slot, $value);
        //         }
        //       } else {

        //         array_push($time_slot, $value);
        //       }
        //     }
        //   }
        // }

        $time_slot = array();
        $arr = $this->hoursRange();
        if (count($leavedate) == 0) {
            $i++;
            $alreadyBooked = array();
            foreach ($booked_slots as $bk_slot) {
                $convert_bk_time = date('H:i', strtotime($bk_slot->slot_start));

                array_push($alreadyBooked, $convert_bk_time);
            }

            // dd($alreadyBooked);
            $BookedSlots = $alreadyBooked;
            if ($request->shift_id) {

                $shift = Shift::where('shift_id', $request->shift_id)->first();
            } else {
                $shift = Shift::orderBy('shift_id')->first();
            }
            if ($shift->end_time == "00:00:00") {

                $shift->end_time = "23:59:00";
            }


            foreach ($arr as $value) {
                $i++;

                if ($current_time <= $value && substr($shift->start_time, 0, -3) <= $value && $shift->end_time >= $value) {
                    if (count($booked_slots) > 0) {
                        if (!in_array($value, $alreadyBooked)) {
                            array_push($time_slot, $value);
                        }
                    } else {

                        array_push($time_slot, $value);
                    }
                }
            }
        }

        return response()->json([
            'beauticians' => $db_beauticians,
            'time_slot' => $time_slot,
            'beautician_id' => $request->beautician,
            'beautician_flag' => $beautician_flag,
            'db_shift' => $db_shift,
            'new_duration' => $new_duration,
            'all_db_shifts' => $all_db_shifts,
            'services' => $all_db_shifts,
            // 'duration_minutes' => $duration_minutes,
            'price' => $price,

        ]);
    }

    public function selectSearch(Request $request)
    {
        $clients = [];
        if ($request->has('q')) {
            $search = $request->q;
            // $movies =Movie::select("id", "name")
            // 		->where('name', 'LIKE', "%$search%")
            // 		->get();
            $clients = Client::join('users', 'clients.client_id', '=', 'users.id')
                ->select('clients.client_id', 'users.name', 'mobile')
                ->where('users.user_status', 'active')
                ->where('users.name', 'LIKE', "%$search%")
                ->orWhere('users.mobile', 'LIKE', "%$search%")
                ->get();
        }
        // dd($clients);
        return response()->json($clients);
    }

    public function changeServiceByBookingold(Request $request)
    {
        $service_type = ServiceTypeManagement::where('service_type_name', 'salon')->first();

        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();

        // Step 1: Get beautician's service IDs
        $beauticianServices = BeauticianService::where('beautician_id', $request->beautician_id)->get();
        $serviceIds = $beauticianServices->pluck('service_id')->toArray();

        // Step 2: Get filtered services
        $services = Service::with('category.parent')
            ->join('price_services', 'price_services.service_id', '=', 'services.service_id')
            ->whereIn('services.service_id', $serviceIds)
            ->where('services.is_active', 1)
            ->where('price_services.service_type_id', $service_type->service_type_management_id)
            ->when($request->name, function ($q) use ($request) {
                $q->where('services.name', 'like', '%' . $request->name . '%');
            })
            ->select(
                'services.service_id',
                'services.name as service_name',
                'services.category_id',
                'services.duration_minutes',
                'services.service_base_total'
            )
            ->get();

        // Step 3: Group services by category
        $grouped = $services->groupBy('category_id');
        $categories = [];

        foreach ($grouped as $categoryId => $serviceGroup) {
            $category = ServiceCategory::with('parent')
                ->where('category_id', $categoryId)
                ->first();

            if ($category) {
                $categories[] = [
                    'category_id' => $categoryId,
                    'category_name' => $category->name,
                    'parent_id' => $category->parent_id,
                    'services' => $serviceGroup->values(),
                ];
            }
        }

        config()->set('database.connections.mysql.strict', true);
        DB::reconnect();

        return $categories;
    }


    public function changeServiceByBooking(Request $request)
    {
        $service_type = ServiceTypeManagement::where('service_type_name', 'salon')->first();

        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();

        // Step 1: Get beautician's service IDs
        $beauticianServices = BeauticianService::where('beautician_id', $request->beautician_id)->get();
        $serviceIds = $beauticianServices->pluck('service_id')->toArray();

        // Step 2: Get filtered services
        $services = Service::with('category.parent')
            ->join('price_services', 'price_services.service_id', '=', 'services.service_id')
            ->whereIn('services.service_id', $serviceIds)
            ->where('services.is_active', 1)
            ->where('services.parent_category_id', 1)
            ->where('price_services.service_type_id', $service_type->service_type_management_id)
            ->when($request->name, function ($q) use ($request) {
                $q->where('services.name', 'like', '%' . $request->name . '%');
            })
            ->select(
                'services.service_id',
                'services.name as service_name',
                'services.category_id',
                'services.duration_minutes',
                'services.service_base_total'
            )
            ->get();

        // Step 3: Group services by category
        $grouped = $services->groupBy('category_id');
        $categories = [];

        foreach ($grouped as $categoryId => $serviceGroup) {
            $category = ServiceCategory::with('parent')
                ->where('category_id', $categoryId)
                ->first();

            if ($category) {
                $categories[] = [
                    'category_id' => $categoryId,
                    'category_name' => $category->name,
                    'parent_id' => $category->parent_id,
                    'services' => $serviceGroup->values(),
                ];
            }
        }

        config()->set('database.connections.mysql.strict', true);
        DB::reconnect();

        return $categories;
    }
}
