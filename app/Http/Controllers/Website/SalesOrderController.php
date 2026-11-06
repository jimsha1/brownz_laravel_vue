<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;
use Auth;
use App\User;
use App\Models\Booking;
use App\Models\BookingService;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    public function index(){
        $user = User::find(Auth::user()->id);
        $user->photo_src_url = beautician_photo_url($user->profile_photo_path, 250, 250, 1);

        $bookings = Booking::where('client_id',Auth::user()->id)
        ->where('is_failed_web_payment',0)
        ->where('payment_status','completed')
        ->orWhere('payment_status','completed')
        ->with('bookingItems','bookingOnlyServices')->whereNull('parent_id')->orderBy('updated_at','desc')->paginate(5);
      return view('website.sales-order',compact('user','bookings'));
    }

    public function cancelOrder($booking_id,Request $request){
      
        $booking_info = Booking::find($booking_id);
        $result = $booking_info->cancelBooking('web');
       $update_res = DB::table('bookings')
            ->where('booking_id',$booking_id)
            ->update(['cancel_review' => $request->cancel_review]);
        // return redirect()->route('website.orders');
        return response()->json([
          'go_to_url' => route('website.orders'),
        ]); 
    }


    public function sendEmailVerificationNotification()
    {
        $user = User::find(Auth::user()->id);
        $user->sendEmailVerificationNotification();
        return response()->json([
          'message' => 'Message sent successfully.',
          'flag' => 'true',
        ]); 
    }

}
