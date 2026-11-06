<?php

namespace App\Http\Controllers\EmployeeApp;

use App\Http\Controllers\Controller;
use App\Service\EmployeeAppApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Mail\SendOTPEmail;
use App\Models\PasswordChange;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Info(
 *     title="API Documentation",
 *     version="1.0.0"
 * )
 * @OA\PathItem(path="/api")
 */

class EmployeeAppApiController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/app-login",
     *     summary="App Login",
     *     description="Logs in a user and returns authentication data.",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     )
     * )
     */
    private EmployeeAppApiService $employeeAppApiService;

    public function __construct(EmployeeAppApiService $employeeAppApiService)
    {
        $this->employeeAppApiService = $employeeAppApiService;
    }
    public function getBookingsApi(Request $request)
    {

        try {
            $validatedData = $this->validate($request, [
                'beautician_id' => 'required',
            ]);
            return $this->employeeAppApiService->getBookings($validatedData);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }
    public function getBookedServiceItemsApi(Request $request)
    {
        try {
            $validatedData = $this->validate($request, [
                'beautician_id' => 'required',
                'booking_id' => 'required',
            ]);
            return $this->employeeAppApiService->getBookedServiceItems($validatedData);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }
    public function getAllServicesApi(Request $request)
    {
        try {
            return $this->employeeAppApiService->getAllServices($request);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }
    public function startServiceApi(Request $request)
    {
        try {
            $validatedData = $this->validate($request, [
                'booking_service_id' => 'required',
                'user_id' => 'required',
            ]);
            return $this->employeeAppApiService->startService($validatedData);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }
    public function stopServiceApi(Request $request)
    {
        try {
            $validatedData = $this->validate($request, [
                'booking_service_id' => 'required',
                'user_id' => 'required',
                'remarks' => 'required',
            ]);
            return $this->employeeAppApiService->stopService($validatedData);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }
    public function addServicesToBookingApi(Request $request)
    {
        try {
            $validatedData = $this->validate($request, [
                'booking_id' => 'required',
                'service_id' => 'required',
                'shift_id' => 'required',
                'user_id' => 'required',
            ]);
            return $this->employeeAppApiService->addServicesToBooking($validatedData);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }

    }
    public function appLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);
            return $this->employeeAppApiService->employeeAppLogin($request);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }
    public function getProfile(Request $request)
    {
        try {
            $validatedData = $this->validate($request, [
                'user_id' => 'required',
            ]);
            return $this->employeeAppApiService->getEmployeeProfile($validatedData);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }
    public function getBookedServiceForCalendarApi(Request $request)
    {
        try {
            $validatedData = $this->validate($request, [
                'user_id' => 'required',
            ]);
            return $this->employeeAppApiService->getBookedServiceForCalendar($validatedData);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }
    public function deleteBookingServiceFromBookingApi(Request $request)
    {
        try {
            $validatedData = $this->validate($request, [
                'booking_service_id' => 'required',
                'booking_id' => 'required',
            ]);
            return $this->employeeAppApiService->deleteBookingServiceFromBooking($validatedData);

        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }
    public function updateEmployeeProfileApi(Request $request)
    {
        try {
            $validatedData = $this->validate($request, [
                'user_id' => 'required',
                'name' => 'required',
                'email' => 'required',
                'mobile' => 'required'
            ]);
            return $this->employeeAppApiService->updateEmployeeProfile($validatedData);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }
    public function updateEmployeeProfilePhotoApi(Request $request)
    {
        try {
            $validatedData = $this->validate($request, [
                'user_id' => 'required',
                'profile_photo_path' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
            ]);
            return $this->employeeAppApiService->updateEmployeeProfilePhoto($validatedData, $request->file('profile_photo_path'));
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }
    public function employeeLeaveRequestApi(Request $request)
    {
        try {
            $validatedData = $this->validate($request, [
                'user_id' => 'required',
                'leave_dates' => 'required|string',
                'leave_type' => 'required',
                'leave_mode' => 'required',
                'emp_id' => 'required',
            ]);
            return $this->employeeAppApiService->leaveRequest($validatedData);

        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }
    public function getEmployeeLeaveRequestsApi(Request $request)
    {
        try {
            $validatedData = $this->validate($request, [
                'user_id' => 'required',
            ]);
            return $this->employeeAppApiService->getLeaveRequestsList($validatedData);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }
    public function deleteLeaveRequestApi(Request $request)
    {
        try {
            $validatedData = $this->validate($request, [
                'user_id' => 'required',
                'leave_id' => 'required',
            ]);
            return $this->employeeAppApiService->deleteLeaveRequest($validatedData);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }

    /**
     * Forgot Password - Send OTP
     */
    public function sendOTP(Request $request)
    {
        try {

            $validateDate = $request->validate([
                'email' => 'required|email',
            ]);

            $user = User::where('email', $validateDate['email'])->first();
            if (!$user) {
                return errorResponse('User not found.', 401);
            }

            $otp = rand(100000, 999999);
            $email = $validateDate['email'];

            PasswordChange::updateOrInsert(
                ['email' => $email],
                ['token' => $otp, 'created_at' => Carbon::now()]
            );

            $mailer = Mail::to($email)->send(new SendOTPEmail($otp));
            Log::info("-----------------FROM SEND OTP SUCCESS ----------------");

            return successResponse($otp, 'OTP sent successfully', 0);

        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
            Log::info("-----------------FROM SEND OTP ERROR ----------------");
            Log::error($e->getMessage());
        }
    }

    /*
     * Verify OTP and Reset Password
     */


    public function verifyOTP(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required|digits:6',
            ]);

            $record = PasswordChange::where('email', $request->email)
                ->where('token', $request->otp)
                ->first();

            if (!$record) {
                return errorResponse('Invalid OTP or Email.', 401);
            }

            return successResponse(null, 'OTP verified successfully', 0);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $validateData = $request->validate([
                'email' => 'required',
                'password' => 'required',
            ]);
            $user = User::where('email', $validateData['email'])->first();
            if (!$user) {
                return errorResponse('User not found.', 401);
            }
            $user->password = Hash::make($validateData['password']);
            $user->save();
            $passwordChange = PasswordChange::where('email', $validateData['email'])->first();
            if ($passwordChange){
                $passwordChange->delete();
            }
            return successResponse(null, 'Password Change  sucessfully ', 0);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }

}
