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


class BookingIndexController extends Controller
{
    public function index(Request $request)
    {
     
     \Log::info($request->service_id);
     \Log::info($request->shift_id);
     \Log::info($request->duration);
     \Log::info($request->price);
     \Log::info($request->service_type_id);
     
      $payment_failed = false;
      if (isset($request->payment) && $request->payment == 0) {
        $payment_failed = true;
      }
       
      $session = new Session();
      $check_session = 0;
      if (Auth::user()){
        $check_session = Auth::user()->id;
      }
      
      $details_location = [
        'address' => $request->search_place,
        'city' =>  $request->locality,
        'complaint_info' =>$request->is_hotel,
    ];
   
      if($request->service_id !=''){
          $request->session()->forget('serached_array');
        $serached_array = [
          'service_id' => $request->service_id, 
          'shift_id' => $request->shift_id,
          'duration' => $request->duration,
          'service_type_id' => $request->service_type_id,
        ] ;
        $session->set('serached_array', $serached_array);

      }else{
        $serached_array =  $session->get('serached_array');
        if(empty($serached_array)){
           $serached_array = [
        'service_id' => 0, 
          'shift_id' => 0,
          'duration' => 0,
          'service_type_id' => 0
        ];
        }
       
      }

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
        if($serached_array['service_id'] > 0){

            $db_service_value = Service::where('service_id',$serached_array['service_id'])->first();
            $db_service_id = $serached_array['service_id'];
            $service_prices = DB::table('price_services')->pluck('price_services.service_id');
            $db_services = Service::whereIn('service_id',$service_prices)->where('allow_online_booking',1)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
            $tax_percent =  $db_service_value->tax_percent;
        }else{
            $db_service_value = Service::where('allow_online_booking',1)->where('is_active', 1)->orderBy('name')->first();
            $db_service_id = $db_service_value->service_id;
            $service_prices = DB::table('price_services')->pluck('price_services.service_id');
            $db_services = Service::whereIn('service_id',$service_prices)->where('allow_online_booking',1)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
            $tax_percent =  $db_service_value->tax_percent;
        }
        if($serached_array['shift_id'] > 0){

          $db_shift_value = Shift::where('shift_id',$serached_array['shift_id'])->first();
          $db_shift_id = $serached_array['shift_id'];
          $shift_prices = DB::table('price_services')->pluck('price_services.shift_id');
          $db_shift =  Shift::whereIn('shift_id',$shift_prices)->get();
          // $db_shift =  Shift::all();
        }else{
          $shift_prices = DB::table('price_services')->pluck('price_services.shift_id');
          $db_shift =  Shift::whereIn('shift_id',$shift_prices)->get();
          // $db_shift =  Shift::all();
          $db_shift_value = Shift::orderBy('shift_id')->first();
          $db_shift_id = $db_shift_value->shift_id;
        }
   
        if($serached_array['duration'] > 0 ){
          $duration_minutes = $serached_array['duration'];
          
          $durations_data = DB::table('price_services')->where([['price_services.service_id','=',$serached_array['service_id']],['price_services.service_type_id','=',$serached_array['service_type_id'] ],['price_services.shift_id','=',$serached_array['shift_id']]])->orderBy('price_services.duration')->pluck('price_services.duration')->toArray();


          $durations = array_map(function($value) {
            $h = floor($value/60) ? floor($value/60) .' hours' : '';
            $m = $value%60 ? $value%60 .' minutes' : '';
             return $h && $m ? $h.' and '.$m : $h.$m;
          }, $durations_data);

          $new_duration = array_map(null, $durations_data, $durations);
        
        }else{
         
          $db_service_value = Service::where('allow_online_booking',1)->where('is_active', 1)->orderBy('name')->first();
          $db_service_id = $db_service_value->service_id;
          $service_type_value = ServiceTypeManagement::orderBy('service_type_management_id')->first();
          $service_type_id = $service_type_value->service_type_management_id;
          $db_shift_value = Shift::orderBy('shift_id')->first();
          $db_shift_id = $db_shift_value->shift_id;

          $durations_data = DB::table('price_services')->where([['price_services.service_id','=',$db_service_id],['price_services.service_type_id','=',$service_type_id],['price_services.shift_id','=',$db_shift_id]])->orderBy('price_services.duration')->pluck('price_services.duration')->toArray();

          $durations = array_map(function($value) {
            $h = floor($value/60) ? floor($value/60) .' hours' : '';
            $m = $value%60 ? $value%60 .' minutes' : '';
             return $h && $m ? $h.' and '.$m : $h.$m;
          }, $durations_data);

          $new_duration = array_map(null, $durations_data, $durations);
          if(empty($new_duration)){
            $duration_minutes = '';
          }else{
            $duration_minutes = min($durations_data);
          }
         
        }
        if($request->price){
          $amount = $request->price;

          $price = percentage_increase($amount, $tax_percent);
          
        }else{
          $price ='';
        }
        if($serached_array['service_type_id']){

          $service_type_prices = DB::table('price_services')->pluck('price_services.service_type_id');
          $service_type = ServiceTypeManagement::whereIn('service_type_management_id',$service_type_prices)->get();
          // $service_type = ServiceTypeManagement::all();
          $service_type_value = ServiceTypeManagement::where('service_type_management_id',$serached_array['service_type_id'])->first();
          $service_type_id = $serached_array['service_type_id'];
        }else{
          $service_type_prices = DB::table('price_services')->pluck('price_services.service_type_id');
          $service_type = ServiceTypeManagement::whereIn('service_type_management_id',$service_type_prices)->get();
          // $service_type = ServiceTypeManagement::all();
          $service_type_value = ServiceTypeManagement::orderBy('service_type_management_id')->first();
          $service_type_id = $service_type_value->service_type_management_id;
        }

        // $db_time_slot = create_time_range('7:30', '18:30', '30 mins');

        $start= "9:00";
        $end = "24:00";
        $int = 30;


        $int *= 60;
        $start = strtotime($start);
        $end = strtotime($end);

        for($i = $start; $i < $end;){
            $res[] = date("H:i", $i) ;
            $i += $int;
        }

        $res = [];
        $db_beauticians = [];
        $db_time_slot =$res;
        // $new_duration = [];
        // $duration_minutes = '';
        $title  = "booking";
      return view('website.booking',compact('db_services','db_service_id',
      'db_shift','db_shift_id','duration_minutes','new_duration',
      'price', 'service_type',
      'service_type_id', 'db_beauticians','db_time_slot',
      'details_location','check_session','tax_percent','payment_failed','title'));
    }
    public function subscription_succes(Request $request){
      return view('website.subscribe-succes');
    }
    
}