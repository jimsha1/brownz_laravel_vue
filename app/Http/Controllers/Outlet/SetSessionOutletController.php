<?php

namespace App\Http\Controllers\Outlet;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SetSessionOutletController extends Controller
{
    public function __invoke(Request $request)
    {
        // Save requested `outlet_id` in session
        $status = $request->user()->setSessionOutlet($request->outlet_id);

        if($status) {
            voco_flash()->success(__('Default outlet has changed.'));
        } else {
            voco_flash()->error('Could not change outlet');
        }

        return back();
    }
}
