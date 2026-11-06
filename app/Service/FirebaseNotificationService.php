<?php

namespace App\Service;

use App\Models\Employee;
use Exception;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;

class FirebaseNotificationService
{
    public function send_notification($request)
    {

        try {
            $employee = Employee::where('user_id', $request['user_id'])->first();
            if (!$employee) {
                return response()->json(['error' => 'Employee not found.'], 404);
            }
            $fcm_token = $employee->fcm_token;

            if (!$fcm_token) {
                return response()->json(['error' => 'FCM token not found for user_id: ' . $request['user_id']], 400);
            }
            
                

            $notification = [
                'title' => $request['title'],
                'body' => $request['body'],
            ];

            $data = [
                'user_id' => (string) $request['user_id'],
                'token' => $fcm_token,
                'type' => $request['type'],
            ];
            $firebaseCredentials = [
                "type" => env('FIREBASE_TYPE'),
                "project_id" => env('FIREBASE_PROJECT_ID'),
                "private_key_id" => env('FIREBASE_PRIVATE_KEY_ID'),
                "private_key" => str_replace("\\n", "\n", env('FIREBASE_PRIVATE_KEY')),
                "client_email" => env('FIREBASE_CLIENT_EMAIL'),
                "client_id" => env('FIREBASE_CLIENT_ID'),
                "auth_uri" => env('FIREBASE_AUTH_URI'),
                "token_uri" => env('FIREBASE_TOKEN_URI'),
                "auth_provider_x509_cert_url" => env('FIREBASE_AUTH_PROVIDER_X509_CERT_URL'),
                "client_x509_cert_url" => env('FIREBASE_CLIENT_X509_CERT_URL'),
                "universe_domain" => env('FIREBASE_UNIVERSE_DOMAIN')
            ];
            Log::info('Firebase Credentials:', $firebaseCredentials);


            $firebase = (new Factory)
                ->withServiceAccount($firebaseCredentials);

            $messaging = $firebase->createMessaging();
            
            // dd($employee->fcm_token);

            $message = [
                //fhsmYdCwQdSN1oomYClBwX:APA91bGMxazIxC2bsx6YUPl6Y-UnreVtxIXjTQa4H9IS-2P52GK_4W1s3uFF_gIyB3i7jAs82s6i18R8n-OzUzQW93kFkLb5Z2xIGSFvJBTvQBDs9TAGpdU
                //fhsmYdCwQdSN1oomYClBwX:APA91bE880r1awa9gCxFL4KuX8oyPV91JGn7DMxRrJnoFFvxWUtqaIjyJgUqDjX7TLErPTrSYSqUHjapzmBrRwa-5f47z4UsHfPMk3CWM9jYfkGEWCrSZfE
                //fhsmYdCwQdSN1oomYClBwX:APA91bFIhHaqxpw0tl8Qm3yReEFl15pjWmQPjeOh-1wlkxDzTMxsLt3w7XzIr02fQy033pvSla96LIJGMQWFlB-Ek7uH6ALTc-ECoGjnmseWQqf7A6lEnJc
                // 'token' => 'fhsmYdCwQdSN1oomYClBwX:APA91bFIhHaqxpw0tl8Qm3yReEFl15pjWmQPjeOh-1wlkxDzTMxsLt3w7XzIr02fQy033pvSla96LIJGMQWFlB-Ek7uH6ALTc-ECoGjnmseWQqf7A6lEnJc',
                'token' => $employee->fcm_token,
                'notification' => $notification,
                'data' => $data,
                'android' => [
                    'priority' => 'high'
                ]
            ];


            $messaging->send($message);

            return response()->json([
                'message' => 'Notification sent successfully',
                'status' => 'success',
                'notification' => $notification,
                'data' => $data,
            ]);

        } catch (MessagingException $e) {
            Log::error('Firebase Messaging Exception: ' . $e->getMessage());
            return errorResponse($e->getMessage(), 100);
        } catch (FirebaseException $e) {
            Log::error('Firebase Exception: ' . $e->getMessage());
            return errorResponse($e->getMessage(), 100);
        }
    }
}