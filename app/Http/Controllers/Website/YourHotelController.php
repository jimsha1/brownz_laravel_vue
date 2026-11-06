<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Beautician;
use Illuminate\Http\Request;

class YourHotelController extends Controller
{
    public function __invoke(Request $request)
    {
        $title  = "your-hotel";
        return view('website.your-hotel', compact('title'));
    }
}
