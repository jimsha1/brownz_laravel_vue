<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;
use Auth;
use App\User;

class ProfileController extends Controller
{
    public function index(){
        $user = User::find(Auth::user()->id);
        $user->photo_src_url = beautician_photo_url($user->profile_photo_path, 250, 250, 1);
      return view('website.profile',compact('user'));
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
