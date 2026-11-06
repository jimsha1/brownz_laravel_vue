<?php

namespace App\Http\Controllers\BookingTime;

use App\Http\Controllers\Controller;
use App\Models\Beautician;
use App\Models\Service;
use App\Models\Shift;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BookingChangeController1111 extends Controller
{

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

  public function __invoke(Request $request)
  {

    $db_shift = "";
    // $session = new Session();
    $beautician_flag = false;
    $db_Date =  date("Y-m-d");
   if ($request->date !=""){
    $db_Date = date("Y-m-d", strtotime($request->date));
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
        //->where('bt_user.shift_id',$request->shift_id)
        ->orderBy('beauticians.beautician_id')
        ->select(
          'beauticians.*',
          'bt_user.*',
          ...user_relation_columns('c_user', 'u_user')
        )

        ->get();

        //dd($db_beauticians);
      foreach ($db_beauticians as $row) {
        if ($row->beautician_id == $request->beautician) {
          $beautician_flag = true;
        }
        if ($selected_beutician == NULL && !empty($db_beauticians)) {
          $selected_beutician = $db_beauticians[0]['beautician_id'];
        }
        $row->photo_src_url = beautician_photo_url($row->profile_photo_path, 250, 250, 1);
      }
     
      $booked_slots = [];
      // dd($request->all());
      if (!empty($request->date) && !empty($request->service)) {
        $booked_slots = \App\Models\BookingService::join('bookings', 'booking_services.booking_id', '=', 'bookings.booking_id')
        ->where('appointment_date', $db_Date)
        ->where('bookings.canceled_at', null)
        ->where('beautician_id',$request->beautician)
          // ->pluck('slot_start')->toArray();
          ->select('booking_service_id', 'appointment_date', 'service_id', 'beautician_id', 'slot_start', 'slot_end')
          ->get();
      }
      // dd($booked_slots);

      $service = Service::find($request->service);

      $tax_percent =  $service->tax_percent;

      $beaut_leave = \App\Models\LeaveManagement::where('beautician_id', $request->beautician)
        ->where('leave_date', date('Y-m-d', strtotime(str_replace('-', '/', $db_Date))))
        ->first();

      //   $beaut_leave);
      if (empty($beaut_leave) || $beaut_leave->leave_type != \App\Models\LeaveManagement::LEAVE_TYPE_FULL) {


        if (empty($beaut_leave)) {
          $start =  \App\Models\Beautician::DEFAULT_SHIFT_START_TIME;
          $end = \App\Models\Beautician::DEFAULT_SHIFT_END_TIME;
        } else {
          $start =  $beaut_leave->leave_type == \App\Models\LeaveManagement::LEAVE_TYPE_FIRST_HALF ? \App\Models\Beautician::DEFAULT_SHIFT_HALF_TIME : \App\Models\Beautician::DEFAULT_SHIFT_START_TIME;
          $end = $beaut_leave->leave_type == \App\Models\LeaveManagement::LEAVE_TYPE_SECOND_HALF ? \App\Models\Beautician::DEFAULT_SHIFT_HALF_TIME : \App\Models\Beautician::DEFAULT_SHIFT_END_TIME;
        }

        // $start= "9:00";
        // $end = "24:00";
        // $int = $service->duration_minutes;

        $shift_prices = DB::table('price_services')->where([['price_services.service_id', '=', $request->service], ['price_services.service_type_id', '=', $request->service_type]])->pluck('price_services.shift_id');
        
        $db_shift =  Shift::whereIn('shift_id', $shift_prices)->get();

        if ($request->duration) {
          $int = $request->duration;
        } else {
          $int = "10";
        }

        if ($request->shift_id && $request->shift_id < 0 ) {
          
          $shift = Shift::where('shift_id', $request->shift_id)->first();
        } else {
          $shift = Shift::orderBy('shift_id')->first();
        }

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
      $price = $service_price->price;
    } else {
      $price = '';
      $new_res = '';
    }
    
    //=================================time slot section ================================================
    $is_current_date = false;
      if ($request->date  == ""){
        $request->date = date('y-m-d');
      }
    if(Carbon::parse($request->date) == Carbon::today()){
        $is_current_date = true;
       $current_time =  date('H:i');
        // date('H:i') < $value
    }else{
      $current_time = '00:00';
    }
   
    $booked_slots = \App\Models\BookingService::where('appointment_date',Carbon::parse($request->date))
    ->where('beautician_id', $request->beautician)
    ->select('slot_start')
    ->get();
$leavedate = DB::table('leave_managements')->where('beautician_id', '=', $request->beautician)
    ->where('leave_date', '=', Carbon::parse($request->date))
    ->get();

$time_slot = array();
$arr = $this->hoursRange();

if (count($leavedate)  == 0) {
    foreach ($arr as $value) {
        if ( $current_time < $value && $shift->start_time <= $value && $shift->end_time >= $value) {
            if (count($booked_slots) > 0) {
                foreach ($booked_slots as $bk_slot) {
                    $convert_bk_time = date('H:i', strtotime($bk_slot->slot_start));
                    if ($convert_bk_time != $value) {
                        array_push($time_slot, $value);
                    }
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
      // 'all_db_shifts' => $all_db_shifts,
      // 'duration_minutes' => $duration_minutes,
      'price' => $price,

    ]);
  }





  public function  allBooking(Request $request)
  {
   
    $all_db_shifts  =  Shift::all();
    $request->shift_id = $all_db_shifts[0]->shift_id;
    $beautician_flag = false;
    $db_Date = date("Y-m-d");
    $durations_data = DB::table('price_services')->where([['price_services.service_id', '=', $request->service], ['price_services.service_type_id', '=', $request->service_type], ['price_services.shift_id', '=', $request->shift_id]])->orderBy('price_services.duration')->pluck('price_services.duration')->toArray();
    if (count( $durations_data) > 0){
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
        ->where('bt_user.shift_id',$request->shift_id)
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
      if (count( $db_beauticians) > 0){
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

      $tax_percent =  $service->tax_percent;

      $beaut_leave = \App\Models\LeaveManagement::where('beautician_id', $selected_beutician)
        ->where('leave_date', date('Y-m-d', strtotime(str_replace('-', '/', $db_Date))))
        ->first();

      //   $beaut_leave);
      if (empty($beaut_leave) || $beaut_leave->leave_type != \App\Models\LeaveManagement::LEAVE_TYPE_FULL) {


        if (empty($beaut_leave)) {
          $start =  \App\Models\Beautician::DEFAULT_SHIFT_START_TIME;
          $end = \App\Models\Beautician::DEFAULT_SHIFT_END_TIME;
        } else {
          $start =  $beaut_leave->leave_type == \App\Models\LeaveManagement::LEAVE_TYPE_FIRST_HALF ? \App\Models\Beautician::DEFAULT_SHIFT_HALF_TIME : \App\Models\Beautician::DEFAULT_SHIFT_START_TIME;
          $end = $beaut_leave->leave_type == \App\Models\LeaveManagement::LEAVE_TYPE_SECOND_HALF ? \App\Models\Beautician::DEFAULT_SHIFT_HALF_TIME : \App\Models\Beautician::DEFAULT_SHIFT_END_TIME;
        }

        // $start= "9:00";
        // $end = "24:00";
        // $int = $service->duration_minutes;

        $shift_prices = DB::table('price_services')->where([['price_services.service_id', '=', $request->service], ['price_services.service_type_id', '=', $request->service_type]])->pluck('price_services.shift_id');

        $db_shift =  Shift::whereIn('shift_id', $shift_prices)->get();

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
    $all_db_shifts  =  Shift::all();
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



  public function  allBookingExisting(Request $request)
  { 
    
    $beautician_flag = false;
    $db_Date = date("Y-m-d");
    $durations_data = DB::table('price_services')->where([['price_services.service_id', '=', $request->service], ['price_services.service_type_id', '=', $request->service_type], ['price_services.shift_id', '=', $request->shift_id]])->orderBy('price_services.duration')->pluck('price_services.duration')->toArray();
    if (count( $durations_data) > 0){
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
       // ->where('bt_user.shift_id',$request->shift_id)
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
      if (count( $db_beauticians) > 0){
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
      $tax_percent =  $service->tax_percent;
      $beaut_leave = \App\Models\LeaveManagement::where('beautician_id', $selected_beutician)
        ->where('leave_date', date('Y-m-d', strtotime(str_replace('-', '/', $db_Date))))
        ->first();

      //   $beaut_leave);
      if (empty($beaut_leave) || $beaut_leave->leave_type != \App\Models\LeaveManagement::LEAVE_TYPE_FULL) {


        if (empty($beaut_leave)) {
          $start =  \App\Models\Beautician::DEFAULT_SHIFT_START_TIME;
          $end = \App\Models\Beautician::DEFAULT_SHIFT_END_TIME;
        } else {
          $start =  $beaut_leave->leave_type == \App\Models\LeaveManagement::LEAVE_TYPE_FIRST_HALF ? \App\Models\Beautician::DEFAULT_SHIFT_HALF_TIME : \App\Models\Beautician::DEFAULT_SHIFT_START_TIME;
          $end = $beaut_leave->leave_type == \App\Models\LeaveManagement::LEAVE_TYPE_SECOND_HALF ? \App\Models\Beautician::DEFAULT_SHIFT_HALF_TIME : \App\Models\Beautician::DEFAULT_SHIFT_END_TIME;
        }

        // $start= "9:00";
        // $end = "24:00";
        // $int = $service->duration_minutes;

        $shift_prices = DB::table('price_services')->where([['price_services.service_id', '=', $request->service], ['price_services.service_type_id', '=', $request->service_type]])->pluck('price_services.shift_id');
        
        $db_shift =  Shift::whereIn('shift_id', $shift_prices)->get();
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
    $all_db_shifts  =  Shift::all();

    $all_services  =  Service::where('category_id',$request->category_id)
    ->get();

    $booked_slots = \App\Models\BookingService::where('appointment_date',Carbon::parse($request->date))
    ->where('beautician_id', $request->beautician)
    ->select('slot_start')
    ->get();
$leavedate = DB::table('leave_managements')->where('beautician_id', '=', $request->beautician)
    ->where('leave_date', '=', Carbon::parse($request->date))
    ->get();

$time_slot = array();
$arr = $this->hoursRange();
if(Carbon::parse($request->date) == Carbon::today()){
  $is_current_date = true;
 $current_time =  date('H:i');
  // date('H:i') < $value
}else{
$current_time = '00:00';
}
if (count($leavedate)  == 0) {

    foreach ($arr as $value) {
  
        if ( $current_time < $value && $shift->start_time <= $value && $shift->end_time >= $value) {
            if (count($booked_slots) > 0) {
                foreach ($booked_slots as $bk_slot) {
                    $convert_bk_time = date('H:i', strtotime($bk_slot->slot_start));
                    if ($convert_bk_time != $value) {
                        array_push($time_slot, $value);
                    }
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

}
