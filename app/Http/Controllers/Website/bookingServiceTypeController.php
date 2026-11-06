<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Beautician;
use Illuminate\Http\Request;

class bookingServiceTypeController extends Controller
{
    public function __invoke(Request $request)
    {
       
        return view('website.service_type_list');
    }
}
