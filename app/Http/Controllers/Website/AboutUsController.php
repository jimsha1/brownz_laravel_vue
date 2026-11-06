<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    public function __invoke(Request $request)
    {
        $title  = "about_Us";
        return view('website.about_us',compact('title'));
    }
}
