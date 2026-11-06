<?php

namespace App\Http\Controllers\Salon\BookingDetails;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Repositories\BookingRepository;
use Illuminate\Http\Request;

class FilterBookingModalController extends Controller
{
    public function __invoke(Request $request, BookingRepository $booking_repo)
    {
        if (! hasOpenFloatAmount()) {
            return redirect()->route('float_amt', ['id' => $request->booking_id])->with('message', 'cashier not assigned to the till');
        }

        $booking_info = $booking_repo->hunter()->findBooking($request->booking_id);

        if ($booking_info->booking_type == 'Draft') {
            Booking::where('booking_id', $request->booking_id)->update(['booking_type' => 'Refunded']);
        }

        if (! $booking_info->booking_code) {
            \DB::transaction(function () use ($booking_info) {
                $booking_info->booking_code = Booking::assignNextBookingCode($booking_info);
                $booking_info->booking_barcode = $booking_info->booking_code;
                $booking_info->save();
                $booking_info->orderPlacedMail();
            });
        }

        $this->send_msg_booking($booking_info);

        return redirect()->route('tab.salon.booking.details', ['booking_id' => '0', 'tab_name' => 'edit']);
    }

    public function send_msg_booking($booking)
    {
        if (isset($booking->refund_invoice_no)) {
            $msg_text = 'We have processed your return for the Order '.$booking->refund_invoice_no
                .",\nand have refunded (Refund Order {$booking->booking_code}) successfully.Thank you.\n\nSee you next time!";
        } else {
            $msg_text = 'Thank you for booking with Baheya Beauty. Your booking no is '.$booking->booking_code.'. See you soon';
        }

        $number = $booking->client_mobile;
        $url = "https://mshastra.com/sendurl.aspx?user=20095417&pwd=B12345&senderid=BAHEYASTORE&mobileno=$number&msgtext=".urlencode($msg_text).'&priority=High&CountryCode=ALL';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curl_scraped_page = curl_exec($ch);
        curl_close($ch);
        \Log::info($curl_scraped_page);

        return true;
    }
}
