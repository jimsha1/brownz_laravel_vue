<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Beautician;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class bookingCalenderController extends Controller
{
    public function __invoke(Request $request)
    {


        $user_res = DB::table('users')->where('id', '=', $request->id)
            ->select('week_off')
            ->first();
        $weekoff =  isset($user_res->week_off) ? $user_res->week_off : null;
        // dd($weekoff);
        $datetime = new DateTime();
        
        $list = array();

        if ($weekoff != $datetime->format('l')) {
            array_push($list, [
                'char_date' => $datetime->format('M d'),
                'h_day' => $datetime->format('d'),
                'h_day_text' => $datetime->format('D'),
                'h_day_full_text' => $datetime->format('l'),
                'num_date' => $datetime->format('Y-m-d'),
            ]);
        }


        $daysCount = 16;
        for ($i = 0; $i <= $daysCount; $i++) {
            $date = $datetime->modify('+1 day');

            if ($weekoff != $datetime->format('l')) {
                array_push($list, [
                    'char_date' => $date->format('M d'),
                    'h_day' => $date->format('d'),
                    'h_day_text' => $date->format('D'),
                    'h_day_full_text' => $datetime->format('l'),
                    'num_date' => $date->format('Y-m-d'),
                ]);
            }
        }

       



        //


        // $list = array();
        $month = date('m');

        $year = date('Y');
        $date = date('d');
        // $count_days = $date + 15;

        // $i = 0;
        // for ($date; $date <= $count_days; $date++) {
        //     $time = mktime(12, 0, 0, $month, $date, $year);
        //     if (date('m', $time) == $month)
        //         $full_date = date('j M', $time);
        //     $tomorrow_timestamp = strtotime("+ " . $i . " day");
        //     $upcoming_date = date("Y-m-d", $tomorrow_timestamp);
        //     array_push($list, [
        //         'char_date' => $full_date,
        //         'num_date' => $upcoming_date,

        //     ]);
        //     $i++;
        // }


        $monthNum  = $month;
        $dateObj   = DateTime::createFromFormat('!m', $monthNum);
        $monthName = $dateObj->format('F');

        //=================================time slot section ================================================
        $booked_slots = \App\Models\BookingService::where('appointment_date', Carbon::today())
            ->where('beautician_id', $request->id)
            ->select('slot_start')
            ->get();
        $leavedate = DB::table('leave_managements')->where('beautician_id', '=', $request->id)
            ->where('leave_date', '=', Carbon::today())
            ->where('leave_status', 'approved')
            ->first();

        $time_slot = array();
        $arr = $this->hoursRange();
        // Only show time slots if beautician is not on approved leave
        if (empty($leavedate)) {
            $i++;

            $alreadyBooked = array();
            foreach ($booked_slots as $bk_slot) {
                $convert_bk_time = date('H:i', strtotime($bk_slot->slot_start));
                array_push($alreadyBooked, $convert_bk_time);
            }

            // dd($alreadyBooked);

            foreach ($arr as $value) {
                $i++;

                if (date('H:i') <= $value && $request->start_time <= $value && $request->end_time >= $value) {
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
        $bt_id = $request->id;
        $bt_start_time =  $request->start_time;
        $bt_end_time = $request->end_time;

        return view('website.booking_calender', [
            'month' => $monthName,
            'year' => $year,
            'date_list' => $list,
            'time_slot' => $time_slot,
            'bt_id' => $bt_id,
            'bt_start_time' => $bt_start_time,
            'bt_end_time' => $bt_end_time
        ]);
    }

    function getTimeSlot(Request $request)
    {

        if ($request->end_time == "00:00:00") {
            $request->merge([
                'end_time' =>  "23:59:00",
            ]);
        }
        // if($request->start_time == "00:00:00"){
        //     $request->merge([
        //         'start_time' =>  "00:00:00",
        //     ]);

        // }

        $is_current_date = false;
        if (Carbon::parse($request->date) == Carbon::today()) {
            $is_current_date = true;
            // date('H:i') < $value
        }
        //=================================time slot section ================================================
        $booked_slots = \App\Models\BookingService::where('appointment_date', Carbon::parse($request->date))
            ->where('beautician_id', $request->id)
            ->select('slot_start')
            ->get();
        $leavedate = DB::table('leave_managements')->where('beautician_id', '=', $request->id)
            ->where('leave_date', '=', Carbon::parse($request->date))
            ->where('leave_status', 'approved')
            ->first();

        $time_slot = array();
        $arr = $this->hoursRange();
        $alreadyBooked = array();
        foreach ($booked_slots as $bk_slot) {
            $convert_bk_time = date('H:i', strtotime($bk_slot->slot_start));
            array_push($alreadyBooked, $convert_bk_time);
        }

        // Only show time slots if beautician is not on approved leave
        if (empty($leavedate)) {
            foreach ($arr as $value) {

                if (substr($request->start_time, 0, -3) <= $value && substr($request->end_time, 0, -3) >= $value) {


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

        return $time_slot;
    }



    function hoursRange($lower = 0, $upper = 86400, $step = 900, $format = '')
    {
        $times = array();

        if (empty($format)) {
            $format = 'g:i a';
        }

        foreach (range($lower, $upper, $step) as $increment) {
            $increment = gmdate('H:i', $increment);

            list($hour, $minutes) = explode(':', $increment);

            $date = new DateTime($hour . ':' . $minutes);

            $times[(string) $increment] = (string) $increment;
            // $date->format($format)
        }

        return $times;
    }
}
