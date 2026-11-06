<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Beautician;
use Illuminate\Http\Request;

class locationPickerController extends Controller
{
    public function __invoke(Request $request)
    {       
        $type = $request->type;
        return view('website.location_picker', compact('type'));
    }
}
