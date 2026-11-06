<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Beautician;
use Illuminate\Http\Request;

class bookingPaymentController extends Controller
{
    public function __invoke(Request $request)
    {
        $booking_id = "";
        $checkout_id = "";
        $endity_id = "";
        $id = "-1";

        if($request->id !=""){
            $id =  $request->id;
        }

        if ($request->bookingid != ""){
            $booking_id = $request->bookingid;
        } 

        if ($request->checkout_id != ""){
            $checkout_id = $request->checkout_id;
        } 

        if ($request->endity_id != ""){
            $endity_id = $request->endity_id;
        } 

        return view('website.bookingPayment',['booking_id'=>$booking_id,'checkout_id'=>$checkout_id,'endity_id'=>$endity_id,'id'=>$id]);
    }
}
