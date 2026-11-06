<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\Service;
use Illuminate\Http\Request;

class MainCategoryController extends Controller
{
    public function __invoke(Request $request, $category_id)
    {
        
        // @TODO - validate parameter
        $main_category = ServiceCategory::where('category_id', $category_id)
            // Check for main category
            ->whereNull('parent_id')
            ->where('show_on_website', 1)
            ->firstOrFail();
           
        return view('website.main-category', compact('main_category'));
    }
}
