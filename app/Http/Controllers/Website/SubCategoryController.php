<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\Shift;
use App\Models\ServiceTypeManagement;
use App\Models\PriceService;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\URL;
use SebastianBergmann\Timer\Duration;

class SubCategoryController extends Controller
{
    public function __invoke(Request $request, $category_id,$cat_name)
    {
      
         // @TODO - validate parameter
        $category_info = ServiceCategory::where('category_id', $category_id)
            // Check for sub category
            ->whereNotNull('parent_id')
            ->where('show_on_website', 1)
            ->firstOrFail();
        $service_prices = DB::table('price_services')->pluck('price_services.service_id');
        // Services belongs to requested category
        $services = Service::whereIn('service_id',$service_prices)->where('category_id', $category_id)
            ->where('is_active', 1)
            ->where('show_on_website', 1)
            ->orderBy('display_order')
            ->orderBy('service_id', 'desc')
            ->with('servicePrices.serviceType')
            ->get();

            foreach ($services as $key => $service) {
                $arr = [];
               foreach ($service->servicePrices as $key => $value) {
                    $arr[] = $value->serviceType;
               }
               $service['serviceType'] = array_unique($arr);
            }

            $price_servicess = DB::table('price_services')->get()->groupBy('service_id');
            // var_dump($price_servicess); exit;
        $durations_data =  \Facades\App\Models\Service::durationStaticData();

        $durations = array_map(function($value) {
            $h = floor($value/60) ? floor($value/60) .' hours' : '';
            $m = $value%60 ? $value%60 .' minutes' : '';
             return $h && $m ? $h.' and '.$m : $h.$m;
        }, $durations_data);

        $new_duration = array_map(null, $durations_data, $durations);

        $shift_prices = DB::table('price_services')->pluck('price_services.shift_id');
        $shifts =  Shift::whereIn('shift_id',$shift_prices)->get();

        $service_type_prices = DB::table('price_services')->pluck('price_services.service_type_id');
        $service_types = ServiceTypeManagement::whereIn('service_type_management_id',$service_type_prices)->get();
        // $service_types = ServiceTypeManagement::all();
        
        $price_services = DB::table('price_services')->join('service_type_managements','price_services.service_type_id','=','service_type_managements.service_type_management_id')->join('shifts','price_services.shift_id','=','shifts.shift_id')->select('service_type_managements.service_type_name as service_type','shifts.title as shift','price_services.*')->get();
        $service_shifts = [];
        $service_price = [];
        $service_duration = [];
        $text_duration = [];
        $duration = [];
        foreach ($price_services as $key => $value) {
            $service_shifts[$value->service_id][$value->service_type_id][$value->shift_id] = $value->shift;
            $service_price = $value->price;
            
        }

      // $price=[88][2][]
        // -------------GET DURATION----------------------
       
       
        foreach ($price_services as $key => $value) {
            $durations_data = DB::
        table('price_services')->where([
                ['price_services.service_id', '=', $value->service_id],
                ['price_services.service_type_id', '=', $value->service_type_id], 
                ['price_services.shift_id', '=', $value->shift_id]
            ])->orderBy('price_services.duration')->select('price_services.duration as duration','price_services.price as price')->get()->toArray();
            // foreach ($durations_data as $dur_text){
            //     $text_duration[] = $this->timeChange($dur_text->duration); 
               
            // }
            // dd($text_duration);
            $service_duration[$value->service_id][$value->service_type_id][$value->shift_id] =  $this->timeChange($durations_data);
            //dd($service_duration);
            
            // $service_duration[$value->service_id][$value->service_type_id][$value->shift_id]['price'] =  $durations_data->price;
            
        }
            // dd($service_duration);
            $title  = $cat_name;
            $category_info->banner_image =  URL::to('/').'/'.$category_info->banner_image;
            $category_info->square_image =   URL::to('/').'/'.$category_info->square_image;
        //    dd($category_info->banner_image);
        return view('website.sub-category', compact('category_info', 'services', 'new_duration', 'shifts', 'service_types', 'price_servicess','service_shifts','service_duration','title'));
    }

    public function timeChange($data){
        return array_map(function($value) {
            $h = floor($value->duration/60) ? floor($value->duration/60) .' hours' : '';
            $m = $value->duration%60 ? $value->duration%60 .' minutes' : '';
             return ['duration' => [$value->duration,$h && $m ? $h.' and '.$m : $h.$m],'price' => $value->price];
        }, $data);
    }

    public function getServicePrice(Request $request)
    {   
        
        $service_id = $request->service_id;
        $service_type_id = $request->service_type_id;
        $shift_id = $request->shift_id;
        $duration =  $request->duration;

        $service_price = DB::table('price_services')->where([['price_services.service_id', '=', $service_id], ['price_services.service_type_id', '=', $service_type_id], ['price_services.shift_id', '=', $shift_id], ['price_services.duration', '=', $duration]])->select('price_services.*')->get();

        // $service_price = PriceService::where('service_id',$service_id)
        //     ->where('service_type_id',$service_type_id)
        //     ->where('shift_id',$shift_id)
        //     ->where('duration',$duration)->get();
        return $service_price;
    }


    public function getServicePriceFiltering($request)
    {   
        $price_services = array();
        $service_id = $request['service_id'];
        $service_type_id =  $request['service_type_id'];
        $shift_id = $request['shift_id'];
        $duration =  $request['durations'];
        $result=[];
        foreach ( $duration as $dur){ 
            $service_price = DB::table('price_services')
            ->where([
                ['price_services.service_id', '=', $service_id], 
                ['price_services.service_type_id', '=', $service_type_id], 
                ['price_services.shift_id', '=', $shift_id],
                ['price_services.duration', '=',  $dur[0]]
                ])
            ->select('price_services.*')
            ->get()->first();

            $result[]=[
                'duration' => [$dur[0],$dur[1]],
                'price'=>$service_price->price
            ];
           
            // array_push( $price_services,
            // $price_services['price'] = $service_price[0]->price ,
            // $price_services['duration'] = $dur[0],
            // $price_services['duration_text'] = $dur[1] );
            }
        //$service_price = DB::table('price_services')->where([['price_services.service_id', '=', $service_id], ['price_services.service_type_id', '=', $service_type_id], ['price_services.shift_id', '=', $shift_id], ['price_services.duration', '=', $duration]])->select('price_services.*')->get();

        // $service_price = PriceService::where('service_id',$service_id)
        //     ->where('service_type_id',$service_type_id)
        //     ->where('shift_id',$shift_id)
        //     ->where('duration',$duration)->get();
        return $result;
    }


    public function getServiceDurationOnload(Request $request)
    {
        $service_id = $request->service_id;
        $service_type_id = $request->service_type_id;
        $shift_id = $request->shift_id;
        
       // $duration =  $request->duration;
        $durations_data = DB::table('price_services')->where([['price_services.service_id', '=', $service_id], ['price_services.service_type_id', '=', $service_type_id], ['price_services.shift_id', '=', $shift_id]])->orderBy('price_services.duration')->pluck('price_services.duration')->toArray();

        $durations = array_map(function($value) {
            $h = floor($value/60) ? floor($value/60) .' hours' : '';
            $m = $value%60 ? $value%60 .' minutes' : '';
             return $h && $m ? $h.' and '.$m : $h.$m;
        }, $durations_data);

        $new_duration = array_map(null, $durations_data, $durations);
        $service_price = [];
        $service_price['service_id'] = $service_id;
        $service_price['service_type_id'] = $service_type_id;
        $service_price['shift_id'] = $shift_id;
        $service_price['durations'] = $new_duration;
        $price_duration = $this->getServicePriceFiltering($service_price);
        return  $price_duration;
    }
    public function getServiceDuration(Request $request)
    {
        $service_id = $request->service_id;
        $service_type_id = $request->service_type_id;
        $shift_id = $request->shift_id;

        $durations_data = DB::table('price_services')->where([['price_services.service_id', '=', $service_id], ['price_services.service_type_id', '=', $service_type_id], ['price_services.shift_id', '=', $shift_id]])->orderBy('price_services.duration')->pluck('price_services.duration')->toArray();

        $durations = array_map(function($value) {
            $h = floor($value/60) ? floor($value/60) .' hours' : '';
            $m = $value%60 ? $value%60 .' minutes' : '';
             return $h && $m ? $h.' and '.$m : $h.$m;
        }, $durations_data);

        $new_duration = array_map(null, $durations_data, $durations);
        return $new_duration;
    }
}
