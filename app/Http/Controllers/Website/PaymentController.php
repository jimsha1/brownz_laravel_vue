<?php

namespace App\Http\Controllers\Website;

use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\BookingService;
use App\Models\Service;
use Illuminate\Support\Facades\DB as FacadesDB;

class PaymentController extends Controller
{

  public function checkout_cart(Request $request)
  {


    //  $seconddigit = '.00';
    //  $price =  $request->price.$seconddigit;

    $price =  number_format((float)$request->price, 2, '.', '');
    // dd( $price = number_format((float)$price, 2, '.', ''));
    $endity_id = $request->endity_id;
    $testmode = "";
    if ($request->card == 'creditcard') {
      $testmode = "&testMode=EXTERNAL";
    }
    $responseData = [];
    $url = "https://test.oppwa.com/v1/checkouts";
    $data = "entityId=" . $endity_id . "" .
      $testmode .
      "&customer.email=sakeer@printechs.com" .
      "&billing.street1=Malaz Street" .
      "&billing.city=riyadh" .
      "&billing.state=riyadh" .
      "&billing.country=SA" .
      "&amount=" . $price .
      "&currency=SAR" .
      "&paymentType=DB" .
      "&billing.postcode=11312" .
      "&customer.givenName=" . Auth::user()->name . "" .
      "&customer.surname=" . Auth::user()->name . "" .
      "&merchantTransactionId=" . $request->booking_id . "";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Authorization:Bearer OGFjN2E0Y2E3NjA4NGJlMTAxNzYxNGJmN2RlZTE3MTF8MkZQZ1FwYkdxTQ=='
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $responseData = curl_exec($ch);
    if (curl_errno($ch)) {
      return curl_error($ch);
    }
    curl_close($ch);
    // $responseData   = json_decode($responseData);
    // $responseData   = json_encode($responseData); 
    \Log::info('reached checkout section');
    \Log::info($responseData);
    //$payamentId = $responseData->id;
    return $responseData;
    // $responseData = request();

  }
  public function payment(Request $request)
  {

    $payemntid = $request->input('id');
    $endityid = $request->input('endity_id');
    $this->endityid = $endityid;
    return view('website.payment-gateway', compact('payemntid', 'endityid'));
  }
  function payment_request($id, $endityid)
  {

    $url = "https://test.oppwa.com/v1/checkouts/$id/payment";
    $url .= "?entityId=" . $endityid . "";
    //dd($url);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Authorization:Bearer OGFjN2E0Y2E3NjA4NGJlMTAxNzYxNGJmN2RlZTE3MTF8MkZQZ1FwYkdxTQ=='
    ));
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $responseData = curl_exec($ch);
    if (curl_errno($ch)) {
      return curl_error($ch);
    }
    curl_close($ch);
    \Log::info('reached payment section');
    \Log::info($responseData);
    return $responseData;
  }
  public function payment_procces(Request $request)
  {

    $responseData = $this->payment_request($request->id, '8ac7a4ca76084be1017614c05f361715');
    $res = json_decode($responseData);

    if ($res->result->code == '000.100.110') {

      // DB::table('bookings')
      //   ->where('client_id', Auth::user()->id)  // find your user by their email
      //   ->update(array('payment_status' => Booking::PAYMENT_STATUS_COMPLETED));  // update the record in the DB.

      // $bookings = DB::table('bookings')
      //   ->where('client_id', Auth::user()->id)  // find your user by their email
      //   ->orderBy('booking_id', 'DESC')->first();
      //   $booking_code = $bookings->booking_code;
      // $booking_data = Booking::find($bookings->booking_id);
      // $booking_data->payment_status = 'completed';
      // $booking_data->status = 1;
      // $booking_data->payment_total = $bookings->booking_total;
      // $booking_data->save();
      // $booking_payment = new BookingPayment();
      // $booking_payment->booking_id = $bookings->booking_id;
      // $booking_payment->amount_taken = $bookings->booking_total;
      // $booking_payment->amount_given = 0;
      // $booking_payment->payment_mode = "card";
      // $booking_payment->card_type = $res->paymentBrand;
      // $booking_payment->created_by  = Auth::user()->id;
      // $booking_payment->updated_by  = Auth::user()->id;
      // $booking_payment->updated_at  = date("H:i:s");
      // $booking_payment->created_at  = date("H:i:s");
      // $booking_payment->save();
      // $res_msg = $this->send_msg_booking($booking_code);

      $response = DB::transaction(function () use ($res, $request) {
        // DB::table('bookings')
        //   ->where('client_id', Auth::user()->id)  // find your user by their email
        //   ->update(array('payment_status' => Booking::PAYMENT_STATUS_COMPLETED));  // update the record in the DB.

        $bookings = DB::table('bookings')
          ->where('client_id', Auth::user()->id)  // find your user by their email
          ->orderBy('booking_id', 'DESC')->first();
        $booking_code = next_reference_code(\App\Models\AutoIncrementMaster::AI_WEBSITE_BOOKING_CODE);
        $booking_data = Booking::find($bookings->booking_id);
        $booking_data->payment_status = 'completed';
        $booking_data->booking_code =  $booking_code;
        $booking_data->booking_barcode = $booking_code;
        $booking_data->status = 1;
        $booking_data->payment_total = $bookings->booking_total;
        $booking_data->save();
        $booking_payment = new BookingPayment();
        $booking_payment->booking_id = $bookings->booking_id;
        $booking_payment->amount_taken = $bookings->booking_total;
        $booking_payment->amount_given = 0;
        $booking_payment->payment_mode = "card";
        $booking_payment->card_type = $res->paymentBrand;
        $booking_payment->created_by  = Auth::user()->id;
        $booking_payment->updated_by  = Auth::user()->id;
        $booking_payment->save();
        $res_msg = $this->send_msg_booking($booking_code);
        \Log::info('send msg');
        \Log::info($res_msg);
      });
      return redirect('/payment-succes');
    } else {
      return redirect('/payment?id=0');
      //return view('website.payment-failed' );
    }
  }

  public function mada_payment_procces(Request $request)
  {

    $card_type = "";

    $responseData = $this->payment_request($request->id, '8ac7a4ca76084be1017614c1352a1719');
    $res = json_decode($responseData);
    if ($res->result->code == '000.100.110') {

      $response = DB::transaction(function () use ($res, $request) {
        // DB::table('bookings')
        //   ->where('client_id', Auth::user()->id)  // find your user by their email
        //   ->update(array('payment_status' => Booking::PAYMENT_STATUS_COMPLETED));  // update the record in the DB.

        $bookings = DB::table('bookings')
          ->where('client_id', Auth::user()->id)  // find your user by their email
          ->orderBy('booking_id', 'DESC')->first();
        $booking_code = next_reference_code(\App\Models\AutoIncrementMaster::AI_WEBSITE_BOOKING_CODE);
        $booking_data = Booking::find($bookings->booking_id);
        $booking_data->payment_status = 'completed';
        $booking_data->booking_code =  $booking_code;
        $booking_data->booking_barcode = $booking_code;
        $booking_data->status = 1;
        $booking_data->payment_total = $bookings->booking_total;
        $booking_data->save();
        $booking_payment = new BookingPayment();
        $booking_payment->booking_id = $bookings->booking_id;
        $booking_payment->amount_taken = $bookings->booking_total;
        $booking_payment->amount_given = 0;
        $booking_payment->payment_mode = "card";
        $booking_payment->card_type = $res->paymentBrand;
        $booking_payment->created_by  = Auth::user()->id;
        $booking_payment->updated_by  = Auth::user()->id;
        $booking_payment->save();
        $res_msg = $this->send_msg_booking($booking_code);
      });
      return redirect('/payment-succes');
    } else {
      $bookings = DB::table('bookings')
        ->where('client_id', Auth::user()->id)  // find your user by their email
        ->orderBy('booking_id', 'DESC')->first();
      $booking_data = Booking::find($bookings->booking_id);
      $booking_data->is_failed_web_payment = 1;
      $booking_data->save();
      // return redirect('/booking?payment=0');
      return redirect('/payment?id=0');
      //return view('website.payment-failed' );
    }
  }

  public function payment_succes(Request $request)
  {

    return view('website.payment-succes');
  }
  public function send_msg_booking($booking_code)
  {

    $number = Auth::user()->mobile;
    $msg_text = "Thank you for booking with Baheya Beauty. Your booking no is " . $booking_code . ". See you soon";
    $url =
      "https://mshastra.com/sendurl.aspx?user=20095417&pwd=B12345&senderid=BAHEYASTORE&mobileno=$number&msgtext=" . urlencode($msg_text) . "&priority=High&CountryCode=ALL";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $curl_scraped_page = curl_exec($ch);
    curl_close($ch);
    \Log::info($curl_scraped_page);
    return true;
  }

}
