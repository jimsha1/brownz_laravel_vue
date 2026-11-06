<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\EmployeeAttendence;
use App\Service\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CheckEmployeeAttendance extends Command
{

    protected $signature = 'attendance:check';
    protected $description = 'Check if every employee has checked in every 30 seconds';
    public function handle()
    {
        $today = Carbon::today()->toDateString();
        $employees = Employee::with('user.shift')->where('status', 'active')->get();

        foreach ($employees as $employee) {
            Log::info("Processing Employee ID: {$employee->emp_code}");
            $hasCheckedIn = EmployeeAttendence::where('employee_code', $employee->emp_code)
                ->whereDate('check_in', $today)
                ->exists();

            if (!$hasCheckedIn) {
                $this->sendReminder($employee, 'check-in');
            } else {
                $hasCheckedOut = EmployeeAttendence::where('employee_code', $employee->emp_code)
                    ->whereDate('check_out', $today)
                    ->exists();

                if (!$hasCheckedOut) {
                    $this->sendReminder($employee, 'check-out');
                }
            }
        }
    }

    private function sendReminder($employee, $type)
    {
        Log::info("Reminder sent to Employee ID: {$employee->emp_code} for {$type}");

        $currentLocation = $this->getFirebaseData($employee->user_id);

        if (!$currentLocation || !isset($currentLocation['latitude'], $currentLocation['longitude'])) {
            Log::error("No location data found for Employee ID: {$employee->emp_code}");
            return;
        }

        $branchLatitude = $employee->latitude;
        $branchLongitude = $employee->longitude;
        $currentLatitude = $currentLocation['latitude'];
        $currentLongitude = $currentLocation['longitude'];

        $distance = calculateDistance($branchLatitude, $branchLongitude, $currentLatitude, $currentLongitude);

        Log::info("Distance for Employee ID: {$employee->emp_code} is {$distance} meters");

        if ($distance > 25) {
            return;
        }



        // $currentTime = Carbon::now();

        // if ($type === 'check-in') {
        //     $shiftStartTime = Carbon::parse($employee->user->shift->start_time);

        //     if ($currentTime->between($shiftStartTime->subMinutes(2), $shiftStartTime->addMinutes(2))) {

        //         Log::info("Sending check-in notification to Employee ID: {$employee->emp_code}");
        //         $this->sendNotification($employee, 'check-in');
        //     } else {
        //         Log::info("Check-in time missed for Employee ID: {$employee->emp_code}");
        //     }
        // } else if ($type === 'check-out') {
        //     $shiftEndTime = Carbon::parse($employee->user->shift->end_time);

        //     if ($currentTime->between($shiftEndTime->subMinutes(2), $shiftEndTime->addMinutes(2))) {

        //         Log::info("Sending check-out notification to Employee ID: {$employee->emp_code}");
        //         $this->sendNotification($employee, 'check-out');
        //     } else {
        //         Log::info("Check-out time missed for Employee ID: {$employee->emp_code}");
        //     }
        // }

        $currentTime = Carbon::now();
        $cacheKey = "notification_count_{$employee->emp_code}_{$type}"; // Unique key for tracking
        $notificationCount = Cache::get($cacheKey, 0); // Get existing count, default to 0

        if ($notificationCount < 3) { // Check if sent less than 3 times
            if ($type === 'check-in') {
                $shiftStartTime = Carbon::parse($employee->user->shift->start_time);

                $startWindow = $shiftStartTime->copy()->subMinutes(2);
                $endWindow = $shiftStartTime->copy()->addMinutes(2);

                if ($currentTime->between($startWindow, $endWindow)) {
                    Log::info("Sending check-in notification to Employee ID: {$employee->emp_code}");
                    $this->sendNotification($employee, 'check-in');

                    // Increment notification count
                    Cache::put($cacheKey, $notificationCount + 1, now()->addMinutes(5)); // Reset after 5 minutes
                } else {
                    Log::info("Check-in time missed for Employee ID: {$employee->emp_code}");
                }
            } else if ($type === 'check-out') {
                $shiftEndTime = Carbon::parse($employee->user->shift->end_time);

                $startWindow = $shiftEndTime->copy()->subMinutes(2);
                $endWindow = $shiftEndTime->copy()->addMinutes(2);

                if ($currentTime->between($startWindow, $endWindow)) {
                    Log::info("Sending check-out notification to Employee ID: {$employee->emp_code}");
                    $this->sendNotification($employee, 'check-out');

                    // Increment notification count
                    Cache::put($cacheKey, $notificationCount + 1, now()->addMinutes(5)); // Reset after 5 minutes
                } else {
                    Log::info("Check-out time missed for Employee ID: {$employee->emp_code}");
                }
            }
        } else {
            Log::info("Max notification limit reached for Employee ID: {$employee->emp_code}");
        }


    }

    public function getFirebaseData($user_id)
    {
        $firebaseDatabaseURL = 'https://brownz-app-25d53-default-rtdb.firebaseio.com';

        // Correct Firebase URL
        $url = $firebaseDatabaseURL . '/users/' . $user_id . '/location.json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::error('Error fetching data from Firebase: ' . curl_error($ch));
            return null;
        }

        curl_close($ch);

        return json_decode($response, true);
    }
    private function sendNotification($employee, $type)
    {
        $firebaseNotificationService = new FirebaseNotificationService();
        if ($type == 'check-in') {
            $notificationMessage = "You are not checked yet.";
        } else {
            $notificationMessage = "You are not checked out yet.";
        }
        $request = [
            'title' => 'Attendance Reminder',
            'body' => $notificationMessage,
            'user_id' => $employee->user_id,
            'type' => $type,
        ];
        $firebaseNotificationService->send_notification($request);
    }


}
