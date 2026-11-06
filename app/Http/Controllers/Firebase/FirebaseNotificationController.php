<?php

namespace App\Http\Controllers\Firebase;

use App\Http\Controllers\Controller;
use App\Service\FirebaseNotificationService;
use Exception;
use Illuminate\Http\Request;

class FirebaseNotificationController extends Controller
{
    private FirebaseNotificationService $firebaseNotificationService;

    public function __construct(FirebaseNotificationService $firebaseNotificationService){
        $this->firebaseNotificationService = $firebaseNotificationService;
    }
    
    public function send_notification_api(Request $request){
        try {
            $validatedData = $this->validate($request, [
                'user_id' => 'required',
                'title' => 'required',
                'body' => 'required',
                'type' => 'required'
            ]);
            return $this->firebaseNotificationService->send_notification($validatedData);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(),100);
        }
    }
}
