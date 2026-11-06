<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Beautician;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\ServiceTypeManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class allServiceController extends Controller
{
    public function __invoke(Request $request)
    {
        
        $main_categories = ServiceCategory::with(['webChildren' => function ($query) {
            $query->select('category_id', 'parent_id', 'name', 'name_arabic');
        }])
        ->whereHas('webChildren')
        ->whereNull('parent_id')
        ->where('show_on_website', 1)
        ->select('category_id', 'parent_id', 'name', 'name_arabic')
        ->get();        
        
        return view('website.all_service',['main_categories' => $main_categories]);
    }
    public function getServices(Request $request){
        
       $serviceType =  ServiceTypeManagement::where('service_type_name',$request->type)->first();

       config()->set('database.connections.mysql.strict', false);
       \DB::reconnect();
        $categories = ServiceCategory::where('parent_id',$request->id)
        ->where('service_categories.show_on_website', 1)
        ->where('service_categories.name', 'like', '%'.$request->filter.'%')
        ->join('services','service_categories.category_id','=','services.category_id')
        ->select('services.service_id','services.category_id as category_id', 'parent_id', 'service_categories.name as name', 'service_categories.name_arabic','services.duration_minutes','services.service_base_total')
       ->groupBy('category_id')
       ->get();
       config()->set('database.connections.mysql.strict', true);

       \DB::reconnect();
        foreach($categories as $category){
            $category['services'] =  Service::where('category_id', $category->category_id)
            ->select('*', 'price_services.*','shifts.title as shift_title')
            ->where('show_on_website', 1)
            ->join('price_services','price_services.service_id','services.service_id')
            ->join('shifts','shifts.shift_id','price_services.shift_id')
            ->where('price_services.service_type_id', $serviceType->service_type_management_id)
            ->get();

        }

        return  $categories;
    }
}
