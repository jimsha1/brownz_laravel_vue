<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Models\Beautician;
use App\Models\Service;
use App\Models\Booking;
use App\Models\BookingService;
use App\Models\Shift;
use App\Models\ServiceTypeManagement;
use Auth;
use DB;
use Symfony\Component\HttpFoundation\Session\Session;

class BookingController extends Controller
{
  // public function __invoke(Request $request)
  // {
  //     $db_beauticians = Beautician::join('users as bt_user', 'beauticians.beautician_id', '=', 'bt_user.id')
  //         ->join('users as c_user', 'bt_user.created_by', '=', 'c_user.id')
  //         ->join('users as u_user', 'bt_user.updated_by', '=', 'u_user.id')
  //         ->orderBy('beauticians.beautician_id')
  //         ->select(
  //             'beauticians.*',
  //             'bt_user.*',
  //             ...user_relation_columns('c_user', 'u_user')
  //         )
  //         ->limit(6)
  //         ->get();

  //     foreach ($db_beauticians as $row) {
  //         $row->photo_src_url = beautician_photo_url($row->profile_photo_path, 250, 250, 1);
  //     }

  //     $db_services = Service::where('allow_online_booking',1)
  //         ->where('is_active', 1)
  //         ->orderBy('name')
  //         ->get();
  //     // $db_time_slot = create_time_range('7:30', '18:30', '30 mins');

  //     $start= "9:00";
  //     $end = "24:00";
  //     $int = 30;


  //     $int *= 60;
  //     $start = strtotime($start);
  //     $end = strtotime($end);

  //     for($i = $start; $i < $end;){
  //         $res[] = date("H:i", $i) ;
  //         $i += $int;
  //     }

  //     $res = [];
  //     $db_beauticians = [];
  //     $db_time_slot =$res;

  //   return view('website.booking',compact('db_services','db_beauticians','db_time_slot'));
  // }





  public function booking_index(Request $request)
  {

    $db_beauticians = Beautician::join('users as bt_user', 'beauticians.beautician_id', '=', 'bt_user.id')
      ->join('users as c_user', 'bt_user.created_by', '=', 'c_user.id')
      ->join('users as u_user', 'bt_user.updated_by', '=', 'u_user.id')
      ->activeListed()
      ->orderBy('beauticians.beautician_id')
      ->select(
        'beauticians.*',
        'bt_user.*',
        ...user_relation_columns('c_user', 'u_user')
      )
      ->limit(6)
      ->get();

    foreach ($db_beauticians as $row) {
      $row->photo_src_url = beautician_photo_url($row->profile_photo_path, 250, 250, 1);
    }
    if ($request->service_id) {
      $db_services = Service::where('service_id', $request->service_id)->get();
    } else {
      $db_services = Service::where('allow_online_booking', 1)
        ->where('is_active', 1)
        ->orderBy('name')
        ->get();
    }
    if ($request->shift_id) {
      $db_shift = Shift::where('shift_id', $request->shift_id)->get();
    } else {
      $db_shift =  Shift::all();
    }
    if ($request->duration) {
      $duration_minutes = $request->duration;
      $h = floor($duration_minutes / 60) ? floor($duration_minutes / 60) . ' hours' : '';
      $m = $duration_minutes % 60 ? $duration_minutes % 60 . ' minutes' : '';
      $duration =  $h && $m ? $h . ' and ' . $m : $h . $m;
      $new_duration = [[$duration_minutes, $duration]];
    } else {
      $durations_data =  \Facades\App\Models\Service::durationStaticData();

      $durations = array_map(function ($value) {
        $h = floor($value / 60) ? floor($value / 60) . ' hours' : '';
        $m = $value % 60 ? $value % 60 . ' minutes' : '';
        return $h && $m ? $h . ' and ' . $m : $h . $m;
      }, $durations_data);

      $new_duration = array_map(null, $durations_data, $durations);

      $duration_minutes = "45";
      $duration = "10";
    }
    if ($request->price) {
      $price = $request->price;
    } else {
      $price = '';
    }
    if ($request->service_type) {
      $service_type = ServiceTypeManagement::where('service_type_management_id', $request->service_type_id)->get();
    } else {
      $service_type = ServiceTypeManagement::all();
    }

    // $db_time_slot = create_time_range('7:30', '18:30', '30 mins');

    $start = "9:00";
    $end = "24:00";
    $int = 30;


    $int *= 60;
    $start = strtotime($start);
    $end = strtotime($end);

    for ($i = $start; $i < $end;) {
      $res[] = date("H:i", $i);
      $i += $int;
    }

    $res = [];
    $db_beauticians = [];
    $db_time_slot = $res;
    $title  = "booking";
    return view('website.booking', compact(
      'db_services',
      'db_shift',
      'duration_minutes',
      'duration',
      'new_duration',
      'price',
      'service_type',
      'db_beauticians',
      'db_time_slot',
      'title'
    ));
  }

  public function change(Request $request)
  {
    $db_shift = "";
    $session = new Session();
    $beautician_flag = false;
    $db_Date = date("Y-m-d", strtotime($request->date));

    $serached_array = [
      'service_id' => $request->service,
      'shift_id' => $request->shift_id,
      'duration' => $request->duration,
      'service_type_id' => $request->service_type,
    ];
    $session->set('serached_array', $serached_array);
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
      $i = 0;
      $temp_index = "";
      foreach ($db_beauticians as $row) {
        if ($temp_index == "") {
          $temp_index = $i;
        }
        // Check for approved leave dates - beautician will still be shown but time slots will be hidden
        $leavedate = DB::table('leave_managements')->where('beautician_id', '=', $row->beautician_id)
          ->where('leave_date', '=', $db_Date)
          ->where('leave_status', 'approved')
          ->first();
        
        // Always show beautician, even if on leave
        if ($row->beautician_id == $request->beautician) {
          $beautician_flag = true;
          $selected_beutician = $row->beautician_id;
        }
        if ($selected_beutician == NULL && !empty($db_beauticians) && !$leavedate) {
          $selected_beutician = $db_beauticians[$temp_index]['beautician_id'];
        }
        $row->photo_src_url = beautician_photo_url($row->profile_photo_path, 250, 250, 1);
        $i++;
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
      // dd($booked_slots);

      $service = Service::find($request->service);

      $tax_percent =  $service->tax_percent;

      $beaut_leave = \App\Models\LeaveManagement::where('beautician_id', $selected_beutician)
        ->where('leave_date', date('Y-m-d', strtotime(str_replace('-', '/', $db_Date))))
        ->where('leave_status','approved')
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

  public function save(Request $request)
  {

    //==========================checking time slot===============================
    $check_time_slot =  BookingService::join('bookings', 'booking_services.booking_id', '=', 'bookings.booking_id')
      ->where('slot_start', $request->time)
      ->where('bookings.payment_status', 'completed')
      ->where('appointment_date', date('Y-m-d', strtotime($request->date)))
      ->where('service_id', $request->service)
      ->where('beautician_id', $request->beautician)
      ->get()->toArray();

    if (count($check_time_slot) > 0) {
      return 0;
    }
    //============================end checkinmg time slot =========================

    $booking = new Booking();
    // $booking->booking_code = mt_rand(1, 99999) . mt_rand(11111, 99999);
    $booking->client_id = Auth::user()->id;
    $booking->outlet_id = 1;
    $booking->client_name = Auth::user()->name;
    $booking->client_email = Auth::user()->email;
    $booking->client_mobile = Auth::user()->mobile;
    $booking->client_telephone = Auth::user()->telephone;
    $booking->booking_source = Booking::BOOKING_SOURCE_WEBSITE;
    $booking->created_by = Auth::user()->id;
    $booking->updated_by = Auth::user()->id;

    $response = DB::transaction(function () use ($booking, $request) {
      $booking->booking_code = 'draft';
      $booking->booking_barcode = $booking->booking_code;
      $booking->save();
      // $booking->booking_code = 'booking' . str_pad($booking->id, 3, '0', STR_PAD_LEFT);
      // $booking->save();

      $service = Service::with('category')->findOrFail($request->service);

      $booking_service = new BookingService();
      $booking_service->booking_id = $booking->booking_id;
      $booking_service->service_id = $request->service;
      $booking_service->category_name = $service->category->name;
      $booking_service->service_name = $service->name;
      $booking_service->duration_minutes = $request->duration;
      $exclude_amt =  $request->price / (($service->tax_percent / 100) + 1);
      $booking_service->service_booking_price = $exclude_amt;
      $booking_service->tax_percent = $service->tax_percent;

      $service_type = ServiceTypeManagement::where('service_type_management_id', $request->service_type)->first();
      $persentage_of_tax = ($service->tax_percent / 100) * $exclude_amt;
      $booking_service->service_type = $service_type->service_type_name;
      $booking_service->tax_amount =  $persentage_of_tax;
      $booking_service->line_total = $request->price;

      // if (isset($request->is_home_service) && $request->is_home_service == 1){
      //     $booking_service->service_type = BookingService::SERVICE_TYPE_HOME;
      //     $booking_service->tax_amount = percentage_amount($service->service_home_price, $service->tax_percent);
      //     $booking_service->line_total = percentage_increase($service->service_home_price, $service->tax_percent);
      // } else {
      //     $booking_service->service_type = BookingService::SERVICE_TYPE_SALON;
      //     $booking_service->tax_amount = percentage_amount($service->service_base_price, $service->tax_percent);
      //     $booking_service->line_total = percentage_increase($service->service_base_price, $service->tax_percent);
      // }

      $booking_service->latitude = $request->map_lat;
      $booking_service->longitude = $request->map_lon;
      $booking_service->address = $request->map_address;

      // $booking_service->appointment_date = date('Y-m-d', strtotime(str_replace('-', '/', $request->date)));
      $booking_service->appointment_date = date('Y-m-d', strtotime($request->date));
      $booking_service->slot_start = $request->time;
      // $booking_service->slot_end = $request->time;
      $booking_service->beautician_id = $request->beautician;
      $booking_service->save();
      \Log::info('booking service query is working');
      \Log::info($booking_service->booking_service_id);
      $last_id = $booking_service->booking_id;
      $booking->resetBookingAmount();
      $booking->orderPlacedMail();

      return $last_id;
    });



    // dd($request->all());
    // $this->applyValidation($request);


    // $contact = new ContactMessage();
    // $contact->name = $request->name;
    // $contact->email = $request->email;
    // $contact->phone = $request->phone;
    // $contact->subject = $request->subject;
    // $contact->message = $request->message;
    // $contact->is_newsletter = $request->terms?$request->terms:0;

    // $response = \DB::transaction(function () use ($contact) {
    //    return $contact->save();
    // });

    // $request->session()->flash('message', 'Message sent successfully, Thank you for contacting us.');
    // $request->session()->flash('message-type', 'success');

    return response()->json([
      'message' => 'Booked successfully',
      'flag' => 'true',
      'data' => $response
    ]);
  }


  private function applyValidation(Request $request)
  {
    $request->validate([
      'service' => 'required',
      'date' => 'required',
      'beautician' => 'required',
      'time' => 'required',
      'price' => 'required',
    ], [
      'price.required' => 'No data for price,Please select values!',
    ]);
  }

  public function checkTimeSlotIsAvailable(Request $request)
  {

    //==========================checking time slot===============================
    $cart = $request->cart;

    $flag = 1;
    foreach ($cart as $item) {
      $check_time_slot =  BookingService::join('bookings', 'booking_services.booking_id', '=', 'bookings.booking_id')
        ->where('slot_start', $item['service_time'])
        ->where('bookings.payment_status', 'completed')
        ->where('appointment_date', date('Y-m-d', strtotime($item['service_date'])))
        ->where('service_id', $item['service_id'])
        ->where('beautician_id', $item['bt_id'])
        ->get()->toArray();

      if (count($check_time_slot) > 0) {
        $flag = 0;
      }
    }

    return $flag ;
  }


  public function placeOrder(Request $request)
  {

    //==========================checking time slot===============================
    $cart = $request->cart;

    foreach ($cart as $item) {
      $check_time_slot =  BookingService::join('bookings', 'booking_services.booking_id', '=', 'bookings.booking_id')
        ->where('slot_start', $item['service_time'])
        ->where('bookings.payment_status', 'completed')
        ->where('appointment_date', date('Y-m-d', strtotime($item['service_date'])))
        ->where('service_id', $item['service_id'])
        ->where('beautician_id', $item['bt_id'])
        ->get()->toArray();

      if (count($check_time_slot) > 0) {
        return 0;
      }
    }


    $booking = new Booking();
    // $booking->booking_code = mt_rand(1, 99999) . mt_rand(11111, 99999);
    $booking->client_id = Auth::user()->id;
    $booking->outlet_id = 1;
    $booking->client_name = Auth::user()->name;
    $booking->client_email = Auth::user()->email;
    $booking->client_mobile = Auth::user()->mobile;
    $booking->client_telephone = Auth::user()->telephone;
    $booking->booking_source = Booking::BOOKING_SOURCE_WEBSITE;
    $booking->created_by = Auth::user()->id;
    $booking->updated_by = Auth::user()->id;

    $response = DB::transaction(function () use ($booking, $request) {
      $cart = $request->cart;

      $booking->booking_code = 'draft';
      $booking->booking_barcode = $booking->booking_code;
      $booking->save();



      foreach ($cart as $item) {
        $service = Service::with('category')->findOrFail($item['service_id']);


        $booking_service = new BookingService();
        $booking_service->booking_id = $booking->booking_id;
        $booking_service->service_id = $item['service_id'];

        $booking_service->category_name = $service->category->name;
        $booking_service->service_name = $service->name;

        $booking_service->category_name_ar = $service->category->name_arabic;
        $booking_service->service_name_ar = $service->name_arabic;

        $booking_service->duration_minutes = $item['service_mintue']; //; 0; //$request->duration;
        $exclude_amt =  $item['service_price'] / (($service->tax_percent / 100) + 1);
        $booking_service->service_booking_price = $exclude_amt;
        $booking_service->tax_percent = $service->tax_percent;

        // $service_type = ServiceTypeManagement::where('service_type_management_id', $request->service_type)->first();

        $persentage_of_tax = ($service->tax_percent / 100) * $exclude_amt;
        $booking_service->service_type = $request->service_type; //$service_type->service_type_name;
        $booking_service->tax_amount =  $persentage_of_tax;
        $booking_service->line_total = $item['service_price'];

        $booking_service->latitude = $request->latitude;
        $booking_service->longitude = $request->logitude;
        $booking_service->address = $request->address;

        $booking_service->appointment_date = date('Y-m-d', strtotime($item['service_date']));
        $booking_service->slot_start = $item['service_time'];
        $booking_service->beautician_id = $item['bt_id'];
        $booking_service->save();

        \Log::info('booking service query is working');
        \Log::info($booking_service->booking_service_id);
      }

      $last_id = $booking_service->booking_id;
      $booking->resetBookingAmount();
      $booking->orderPlacedMail();

      return $last_id;
    });

    return response()->json([
      'message' => 'Booked successfully',
      'flag' => 'true',
      'data' => $response
    ]);
  }
}
