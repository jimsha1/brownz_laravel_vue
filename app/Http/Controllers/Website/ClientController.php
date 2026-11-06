<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use App\Models\Otp;
use App\Models\User;
use Auth;
use DateTime;
use phpDocumentor\Reflection\Types\This;

class ClientController extends Controller
{
  
  public function save(Request $request)
  {
   
    $ph_no =  '+966'.$request->mobile;
    $request->request->add(['mobile' => $ph_no]);
    //$request->mobile = $ph_no;
    $this->applyValidation($request);

   //$ph_no =  '+966'.$request->mobile;
    $user = new User();

    $user->name = $request->name . $request->l_name;
    $user->email = $request->email;
    // $user->date_of_birth = voco_calendar_to_mysql_date($request->date_of_birth, null);
    $user->mobile = $ph_no;
    // $user->telephone = (string) $request->telephone;
    $user->address = $request->address;
    // $user->city = (string) $request->city;
    // $user->state = (string) $request->state;
    $user->zip = (string) $request->zip;
    // $user->gender = $request->gender;

    $user->password = Hash::make($request->password);
    $user->username = mt_rand(1, 99999) . mt_rand(11111, 99999);


    

    // $contact->is_newsletter = $request->terms?$request->terms:0;

    $response = \DB::transaction(function () use ($user) {
      $user->save();
      $user->username = 'user' . str_pad($user->id, 3, '0', STR_PAD_LEFT);
      $user->save();

      $client = new Client();
      $client->client_id = $user->id;
    
      $client->save();

      Auth::login($user);
      return $user;
    });

    $request->session()->flash('message', 'Registration successful');
    $request->session()->flash('message-type', 'success');

    return response()->json([
      'message' => 'Registration successful',
      'flag' => 'true',
      'data' => $response
    ]);
  }
  private function applyLoginValidation(Request $request)
  {
    $request->validate([
      'username' => 'required',
      'password' => 'required',
    ]);
  }

  private function applyValidation(Request $request)
  {
    
    if (app()->getLocale() == 'en') {
      $request->validate([
        'mobile' => 'required|unique:users,mobile',
        'name' => 'required',

      ]);
    } else {
      $request->validate(
        [
          'mobile' => 'required|unique:users,mobile',
          'name' => 'required',

        ],
        [
          'email.required' => 'البريد الالكتروني مطلوب',
          'mobile.required' => 'الجوال مطلوب',
          'name.required' => 'مطلوب اسم',
          'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',

        ]
      );
    }
  }
  //============================ otp verifictaion section login and signup =========================================
  public function mobileNoOtp(Request $request)
  {
    $mob_no = "+966".$request->mobile;
    \Log::info($mob_no);
    $data = array();
    $ph_no_exists = false;
    $otp_action = false;
    $user = User::where('mobile', '=', $mob_no)->get();
    \Log::info('count user');
    \Log::info(count($user));
    if (count($user) > 0) {
      $ph_no_exists = true;
    }
    if ($ph_no_exists == false) {
      $data  = array('otp_action' => $otp_action,
      'ph_no_exists' => $ph_no_exists);
      return json_encode($data);
    }
    $numbers = preg_replace('/\s+/', '', $request->mobile);
    $number = "+966".$numbers;
    $digits = 4;
    $otp =  rand(pow(10, $digits - 1), pow(10, $digits) - 1);
    $date = new DateTime();
    $db_date = $date->modify("+10 minutes");
    $otp_data = new Otp();
    $otp_data->otp_no = $otp;
    $otp_data->expiry_date = $db_date;
    $otp_data->mob_no = $number;
    $otp_data->save();
    // =====================meassage to  customer section================
    $msg_text = "Welcome to Baheya Beauty, verification code ".$otp.", enjoy";
    $url =
      "https://mshastra.com/sendurl.aspx?user=20095417&pwd=B12345&senderid=BAHEYASTORE&mobileno=$number&msgtext=".urlencode($msg_text)."&priority=High&CountryCode=ALL";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $curl_scraped_page = curl_exec($ch);
    curl_close($ch);
    \Log::info($curl_scraped_page);
    //=======================end msg section ============================
    $otp_action = true;
    $data  = array('otp_action' => $otp_action,
     'ph_no_exists' => $ph_no_exists);
    return json_encode($data);
  }

  public function registerMobNoOtp(Request $request)
  {
    $mob_no = "+966".$request->mobile;
    \Log::info($mob_no);
    $data = array();
    $otp_action = false;
    $user = User::where('mobile', '=', $mob_no)->get();
    \Log::info('count user');
    \Log::info(count($user));
    $numbers = preg_replace('/\s+/', '', $request->mobile);
    $number = "+966".$numbers;
    $digits = 4;
    $otp =  rand(pow(10, $digits - 1), pow(10, $digits) - 1);
    $date = new DateTime();
    $db_date = $date->modify("+10 minutes");
    $otp_data = new Otp();
    $otp_data->otp_no = $otp;
    $otp_data->expiry_date = $db_date;
    $otp_data->mob_no = $number;
    $otp_data->save();
    // =====================meassage to  customer section================
    $msg_text = "Welcome to Baheya Beauty, verification code ".$otp.", enjoy";
    $url =
      "https://mshastra.com/sendurl.aspx?user=20095417&pwd=B12345&senderid=BAHEYASTORE&mobileno=$number&msgtext=".urlencode($msg_text)."&priority=High&CountryCode=ALL";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $curl_scraped_page = curl_exec($ch);
    curl_close($ch);
    \Log::info($curl_scraped_page);
    //=======================end msg section ============================
    $otp_action = true;
    $data  = array('otp_action' => $otp_action);
    return json_encode($data);
  }
//============================ end otp verifictaion section login and signup =========================================
  public function verifyOtp(request $request)
  {
   $otpNo = $request->otp_1.$request->otp_2.$request->otp_3.$request->otp_4;
   \Log::info($otpNo);
   $mob_no = "+966".$request->mob_no;
   \Log::info($mob_no);
    $date = date('Y-m-d H:i:s');
    $otp_verification = Otp::where('otp_no', '=', $otpNo)
      ->where('mob_no', '=', $mob_no)
      ->where('expiry_date', '>=', $date)
      ->get();
      \Log::info(count($otp_verification));
    if (count($otp_verification) > 0) {
      return true;
    } else {
      return false;
    }
  }
  public function loginMobileNoOtp(Request $request)
  {
    $otp = "";
    $user = User::where('mobile', '=', $request->mobile)->get();
    if (count($user) > 0) {
      $digits = 4;
      $otp =  rand(pow(10, $digits - 1), pow(10, $digits) - 1);
      $date = new DateTime();
      $db_date = $date->modify("+10 minutes");
      $otp_data = new Otp();
      $otp_data->otp_no = $otp;
      $otp_data->expiry_date = $db_date;
      $otp_data->mob_no = $request->mobile;
      $otp_data->save();
    } else {
      return "could not find phone number...!!";
    }

    // =====================meassage to  customer section================
    $url =
      "https://mshastra.com/sendurl.aspx?user=20095417&pwd=B12345&senderid=BAHEYASTORE&mobileno=$request->mobile&msgtext=otp:$otp&priority=High&CountryCode=ALL";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $curl_scraped_page = curl_exec($ch);
    curl_close($ch);
    //=======================end msg section ============================
    return true;
  }
  public function verifyLoggedOtp(request $request)
  {
    $mob_no = "+966".$request->mob_no;
    $response = $this->login($mob_no);
    return $response;
   
  }
  public function login($mob_no)
  {
    $user_check  = User::where('mobile', $mob_no)
      ->join('clients', 'users.id', 'clients.client_id')
      ->first();

    if ($user_check) {
      Auth::login($user_check);
      return true;
    } else {
      return false;
    }
  }

  
}
