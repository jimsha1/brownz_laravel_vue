<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Beautician;
use Illuminate\Http\Request;

class YourHomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $title  = "your-home";
        return view('website.your-home', compact('title'));
    }
}
