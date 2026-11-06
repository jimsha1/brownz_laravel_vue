<?php

namespace App\Http\Controllers;

use App\Models\SocialMediaContacts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SocialContactUsController extends Controller
{
    public function index(){
        return view('social-media-contact.contact');
    }
    public function store(Request $request){
        try{
            $request->validate([
                'full_name' => ['required', 'string'],
                'phone_number' => ['required', 'string'],
                'address' => ['required', 'string'],
            ]);
            DB::beginTransaction();
            $contact = new SocialMediaContacts();
            $contact->full_name = $request->full_name;
            $contact->phone_number = $request->phone_number;
            $contact->address = $request->address;
            $contact->save();
            DB::commit(); 
            return redirect()->route('contact.index')->with('success', 'Contact saved successfully!');

            
        }
        catch(\Exception $e){
            DB::rollBack(); 
            return redirect()->route('contact.index')->with('error', 'An error occurred: '.$e->getMessage());  
        }
    }
}
