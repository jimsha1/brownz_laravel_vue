<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Beautician;
use App\Models\Booking;
use App\Models\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class thanksController extends Controller
{
    public function __invoke(Request $request)
    {
        $bookings_data = Booking::where('client_id',Auth::user()->id)->orderBy('updated_at','desc')->first();
        $booking_services = BookingService::where('booking_id', $bookings_data->booking_id)
        ->leftjoin('users','users.id','booking_services.beautician_id')
        ->select('users.name', 'users.name_arabic','booking_services.*')
        ->orderBy('updated_at','desc')
        ->get();
          
      // dd($booking_services);

        return view('website.thanks',['bookings_data'=>$booking_services,'booking_id'=> $bookings_data->booking_id]);
    }
}
