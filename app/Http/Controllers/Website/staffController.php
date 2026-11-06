<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Beautician;
use Illuminate\Http\Request;

class staffController extends Controller
{
    public function __invoke(Request $request)
    {
       
        $beauticians = Beautician::join('users as bt_user', 'beauticians.beautician_id', '=', 'bt_user.id')
        ->join('beautician_services', 'beauticians.beautician_id', '=', 'beautician_services.beautician_id')
        ->where('beautician_services.service_id',$request->id)
        //->where('bt_user.shift_id',$request->shift_id)
        ->join('shifts', 'bt_user.shift_id', '=', 'shifts.shift_id')
        ->join('services', 'beautician_services.service_id', '=', 'services.service_id')
        ->where('beautician_services.status', 1) //1 = Active
        ->activeListed()
        ->select(
            'beauticians.beautician_id',
            'bt_user.profile_photo_path',
            'bt_user.name',
            'bt_user.name_arabic',
            'shifts.title',
            'shifts.start_time',
            'shifts.end_time',
            'services.name as ser_name',
            'services.name_arabic as ser_name_arabic'
        )
        ->get();

        return view('website.staff',['beauticians' => $beauticians]);
    }
}
