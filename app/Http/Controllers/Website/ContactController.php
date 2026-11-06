<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function index(){
      $title  = "contact_us";
      return view('website.contact',compact('title'));
    }

    public function save(Request $request){
        

        $this->applyValidation($request);


        $contact = new ContactMessage();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->subject = $request->subject;
        $contact->message = $request->message;
        $contact->is_newsletter = $request->terms?$request->terms:0;

        $response = \DB::transaction(function () use ($contact) {
           return $contact->save();
        });
        
        $request->session()->flash('message', 'Message sent successfully, Thank you for contacting us.');
        $request->session()->flash('message-type', 'success');

        return response()->json([
          'message' => 'Message sent successfully, Thank you for contacting us.',
          'flag' => 'true',
          'data' => $response
        ]); 
    }

    private function applyValidation(Request $request)
    {
      if (app()->getLocale() == 'en') {
        $request->validate([
            'email' => 'required|email',
            'phone' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);
      }else {
        $request->validate([
          'email' => 'required|email',
          'phone' => 'required',
          'subject' => 'required',
          'message' => 'required',
        ],
        [
          'email.required' => 'البريد الالكتروني مطلوب',
          'phone.required' => 'الجوال مطلوب',
          'subject.required' => ' موضوع مطلوب',
          'message.required' => ' رسالة مطلوب',
          
          
      ]);
      }
    }
}
