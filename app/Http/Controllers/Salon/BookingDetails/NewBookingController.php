<?php

namespace App\Http\Controllers\Salon\BookingDetails;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Booking;

class NewBookingController extends Controller
{
    public function __invoke(Request $request)
    {
       
       
        $booking = \DB::transaction(function () use ($request) {
            $booking = new Booking();
            $booking->booking_source = Booking::BOOKING_SOURCE_WALK_IN;
            $booking->save();
            return $booking;
        });
        return redirect()->route('tab.salon.booking.details', [
            'booking_id' => $booking->booking_id,
            'tab_name' => 'edit',
        ]);
    }
}
