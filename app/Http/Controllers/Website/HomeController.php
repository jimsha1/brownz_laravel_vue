<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $sub_categories = ServiceCategory::whereHas('parent', function ($query) {
                $query->where('show_on_website', 1)->select('category_id');
            })
            ->where('show_on_website', 1)
            ->whereNotNull('square_image')
            ->orderBy('display_order')
            ->orderBy('category_id', 'desc')
            ->select('category_id', 'name', 'name_arabic', 'square_image')
            ->get();

        foreach ($sub_categories as $row) {
            $row->square_image_url = category_square_image_url($row->square_image, 440, 320, 1);
        }
        $slider = DB::table('home_page')->get();
        $service_home = "";
        $service_salon = "";
        foreach($slider as $service){
            if ($service->type == 'service_type_home'){
                $service_home = $service;
            }
            if ($service->type == 'service_type_salon'){
                $service_salon = $service;
            }
        }

        return view('website.home', compact('sub_categories','slider','service_home','service_salon'));
    }
}
