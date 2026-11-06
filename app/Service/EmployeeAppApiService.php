<?php

namespace App\Service;

use App\Models\Beautician;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\BookingService;
use App\Models\Employee;
use App\Models\EmployeeAttendence;
use App\Models\LeaveManagement;
use App\Models\Service;
use App\Models\SplitServiceAmount;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeAppApiService
{
    public function getBookings($request)
    {
        try {
            $beautician_id = $request['beautician_id'];
            $bookings = Booking::with('bookingServices')
                ->whereHas('bookingServices', function ($query) use ($beautician_id) {
                    $query->where('beautician_id', $beautician_id);
                })
                ->orderByRaw('booking_code IS NOT NULL, booking_code DESC')
                ->where('booking_type', 'Sale')
                ->orderBy('created_at', 'DESC')
                ->get();

            return apiResponse(0, 'Bookings retrieved successfully', $bookings);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }

    public function getBookedServiceItems($request)
    {
        try {
            $beautician_id = $request['beautician_id'];
            $booking_id = $request['booking_id'];
            $booking_service_model = BookingService::with('service.category.parent')
                ->where('booking_id', $booking_id)
                ->where('beautician_id', $beautician_id)
                ->get();

            return apiResponse(0, 'Service items retrieved successfully', $booking_service_model);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }

    public function getBookedServiceForCalendar($request)
    {
        try {
            $user_id = $request['user_id'];
            $booking_service_model = BookingService::with('service')
                ->where('beautician_id', $user_id)
                ->where('status', '!=', 'completed')
                ->get();

            return apiResponse(0, 'Service items retrieved successfully', $booking_service_model);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }

    public function getAllServices()
    {
        try {
            $services = Service::with(['category.parent'])->get();

            return apiResponse(0, 'Services retrieved successfully', $services);
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }

    public function startService($request)
    {
        try {
            $booking_service_id = $request['booking_service_id'];
            $user_id = (int) $request['user_id'];
            $booking_service_model = BookingService::find($booking_service_id);

            if (! $booking_service_model) {
                return errorResponse('Service not found', 404);
            }

            if (! $this->canManageBookingServiceViaApp($booking_service_model, $user_id)) {
                return errorResponse('Unauthorized', 403);
            }

            if (empty($booking_service_model->beautician_id)) {
                return errorResponse('Assign a beautician before starting the service', 422);
            }

            $booking_service_model->service_started_at = new DateTime;
            $booking_service_model->service_started_by = (int) $booking_service_model->beautician_id;
            $booking_service_model->status = 'start';
            $booking_service_model->save();

            return apiResponse(0, 'Service started successfully');
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }

    public function stopService($request)
    {
        try {
            $booking_service_id = $request['booking_service_id'];
            $user_id = (int) $request['user_id'];
            $booking_remarks = $request['remarks'];
            $booking_service_model = BookingService::find($booking_service_id);

            if (! $booking_service_model) {
                return errorResponse('Service not found', 404);
            }

            if (! $this->canManageBookingServiceViaApp($booking_service_model, $user_id)) {
                return errorResponse('Unauthorized', 403);
            }

            if (empty($booking_service_model->beautician_id)) {
                return errorResponse('Assign a beautician before finishing the service', 422);
            }

            $booking_service_model->service_finished_at = new DateTime;
            $booking_service_model->service_finished_by = (int) $booking_service_model->beautician_id;
            $booking_service_model->status = 'completed';
            $booking_service_model->service_finished_remarks = $booking_remarks;
            $booking_service_model->save();

            return apiResponse(0, 'Service finished successfully');
        } catch (Exception $e) {
            return errorResponse(100, $e->getMessage());
        }
    }

    private function canManageBookingServiceViaApp(BookingService $bookingService, int $userId): bool
    {
        return (int) $bookingService->beautician_id === $userId;
    }

    public function addServicesToBooking($request)
    {

        try {

            $booking_id = $request['booking_id'];

            $booking_info = Booking::where('booking_id', $booking_id)
                ->with(['bookingItems', 'bookingServices'])
                ->first();

            if ($booking_info->payment_status == 'completed') {
                return apiResponse(1, 'Cannot add services ');
            }
            // DB::transaction(function () use ($request, $booking_info) {

            return $this->createBookingService11($booking_info, $request['service_id'], $request);
            // });

            // $booking_info->resetBookingAmount();

            // $booking_info->loadEssentialsForVueJs();
            // $res=["bok"=>$booking_info];
            // return apiResponse(0, 'Services added to booking successfully', null);

        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 0);
        }
    }

    private function createBookingService($booking_info, $service_id, $request)
    {
        try {
            $service_info = Service::with('category')->findOrFail($service_id);
            // dd($service_info);
            $global_discount = $this->getGlobalDiscount($booking_info);

            $discount_percent = $global_discount['percent'];
            $discount_amount = $global_discount['amount'];

            $booking_service = new BookingService;

            $booking_service->forceFill([
                'booking_id' => $booking_info->booking_id,
                'service_id' => $service_info->service_id,
                'appointment_date' => Carbon::now(),
                'category_name' => $service_info->category->name,
                'service_name' => $service_info->name,
                'duration_minutes' => $service_info->duration_minutes,
                'status' => 'draft',
                'slot_start' => Carbon::now()->format('H:i:s'),
                'beautician_id' => $request['user_id'],
                'shift_id' => $request['shift_id'],
                'service_booking_price' => $service_info->service_base_price,
                'tax_percent' => $service_info->tax_percent,
                'discount_percent' => $discount_percent,
                'show_discount_persent' => $discount_percent,
                'discount_amount' => $discount_amount,
            ]);
            // dd($booking_service);

            if ($discount_percent > 0) {
                $adjusted_price = $service_info->service_base_price - $discount_amount;
                $booking_service->reCalculateLineTotalExistsDiscount($booking_info, $adjusted_price);
            } else {
                $booking_service->reCalculateLineTotal($booking_info);
            }
            // dd($booking_service);

            $booking_service->save();

            $this->updateBookingPrice($request);

            return apiResponse(0, 'Booking service created successfully', $booking_service);
        } catch (Exception $e) {
            Log::error('Error in createBookingService: '.$e->getMessage());

            return errorResponse($e->getMessage(), 500);
        }
    }

    private function createBookingService11($booking_info, $service_id, $request)
    {
        try {
            // Check if the service is already added to the booking
            $existing_service = BookingService::where('booking_id', $booking_info->booking_id)
                ->where('service_id', $service_id)
                ->first();

            if ($existing_service) {
                return apiResponse(1, 'Service is already added to this booking', null);
            }

            $service_info = Service::with('category')->findOrFail($service_id);

            $service_price = $service_info->service_base_total;

            $global_discount = $this->getGlobalDiscount($booking_info);

            $discount_percent = $global_discount['percent'];
            $discount_amount = $global_discount['amount'];

            $booking_service = new BookingService;

            $booking_service->forceFill([
                'booking_id' => $booking_info->booking_id,
                'service_id' => $service_info->service_id,
                'appointment_date' => Carbon::now(),
                'category_name' => $service_info->category->name,
                'service_name' => $service_info->name,
                'duration_minutes' => $service_info->duration_minutes,
                'status' => 'draft',
                'slot_start' => Carbon::now()->format('H:i:s'),
                'beautician_id' => $request['user_id'],
                'shift_id' => $request['shift_id'],
                'service_booking_price' => $service_info->service_base_price,
                'tax_percent' => $service_info->tax_percent,
                'discount_percent' => $discount_percent,
                'show_discount_persent' => $discount_percent,
                'discount_amount' => $discount_amount,
            ]);

            if ($discount_percent > 0) {
                $adjusted_price = $service_info->service_base_price - $discount_amount;
                $booking_service->reCalculateLineTotalExistsDiscount($booking_info, $adjusted_price);
            } else {
                $booking_service->reCalculateLineTotal($booking_info);
            }

            $booking_service->save();

            // Update booking table
            $booking_info->booking_total += $service_price;
            $booking_info->save();

            $this->updateBookingPrice($request);

            return apiResponse(0, 'Booking service created successfully', null);
        } catch (Exception $e) {
            Log::error('Error in createBookingService: '.$e->getMessage());

            return errorResponse($e->getMessage(), 500);
        }
    }

    public function updateBookingPrice($request)
    {

        try {
            // dd($request['beautician_id']);
            $users = DB::table('users')->where('id', $request['beautician_id'])->first();
            // dd($users->shift_id);
            $price_service = DB::table('price_services')
                ->select('price')
                ->where('service_id', '=', $request['service_id'])
                ->where('shift_id', '=', $users->shift_id)
                ->where('service_type_id', '=', 3)
                ->get()->toArray();

            $discount_item_array = BookingItem::where('booking_id', '=', $request['booking_id'])
                ->where('discount_percent', '>', 0)
                ->get();

            $discount_service_array = BookingService::where('booking_id', '=', $request['booking_id'])
                ->where('discount_percent', '>', 0)
                ->get();

            $price = isset($price_service[0]->price) ? $price_service[0]->price : 0;

            $discount_amt = 0.00;
            $changed_price = 0;
            if (count($discount_item_array) > 0 || count($discount_service_array) > 0) {
                if (count($discount_item_array) > 0) {
                    $discount_percent = $discount_item_array['0']->discount_percent;
                } else {
                    $discount_percent = $discount_service_array['0']->discount_percent;
                }

                $discount_amt = ($price / 100) * $discount_percent;
                $changed_price = $price - $discount_amt;
            }
            $tax = $changed_price * 15 / 100;
            $booking_res = DB::table('booking_services')
                ->where('booking_services.booking_service_id', '=', $request['service_id'])
                ->update([
                    'service_booking_price' => $price,
                    'tax_amount' => $tax,
                    'discount_amount' => $discount_amt,
                ]);

            return ['success' => 'price  changed'];
        } catch (Exception $e) {
            Log::error('Error in updateBookingPrice: '.$e->getMessage());

            return errorResponse($e->getMessage(), 0);
        }
    }

    private function getGlobalDiscount($booking_info)
    {
        $booking_items_discount = BookingItem::where('booking_id', $booking_info->booking_id)
            ->where('discount_percent', '>', 0)
            ->first();

        $booking_services_discount = BookingService::where('booking_id', $booking_info->booking_id)
            ->where('discount_percent', '>', 0)
            ->first();

        $discount_percent = 0;
        $discount_amount = 0;

        if ($booking_services_discount || $booking_items_discount) {
            $discount_percent = $booking_services_discount->show_discount_persent ?? $booking_items_discount->show_discount_persent;
            $service_price = $booking_info->bookingServices->sum('service_booking_price');
            $discount_amount = ($discount_percent / 100) * $service_price;
        }

        return [
            'percent' => $discount_percent,
            'amount' => $discount_amount,
        ];
    }

    public function employeeAppLogin($request)
    {
        try {
            $credentials = $request->only('email', 'password');
            //  dd($credentials);
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('api')->accessToken;
                $user->token = $token;
                $user->token_type = 'Bearer ';

                $employee = Employee::where('user_id', $user->id)->where('status', 'active')->first();
                if ($employee) {
                    $employee->fcm_token = $request->fcm_token;
                    $employee->save();
                } else {
                    return successResponse(null, 'User is Invalid', 10);
                }

                $user->emp_id = (string) $employee->emp_id;

                return successResponse($user, 'Login successful', 0);
            } else {
                return errorResponse('Invalid credentials', 401);
            }
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    public function getEmployeeProfile1($request)
    {
        try {
            $user_id = $request['user_id'];
            $currentTime = now();
            $currentDate = $currentTime->format('Y-m-d');
            $user = User::with('employee.salary')->find($user_id);
            // $split = SplitServiceAmount::where('beautician_id', $user->id)->sum('total_amount');
            $split = SplitServiceAmount::where('beautician_id', $user->id)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_amount');

            $totalService = SplitServiceAmount::where('beautician_id', $user->id)->count();
            $attendance = EmployeeAttendence::where('employee_code', $user->employee->emp_code)
                ->where('date', $currentDate)
                ->first();
            $totalTarget = $user->employee->salary->target_value;
            // $remainingTargetAmount = $totalTarget - $split;
            $remainingTargetAmount = max(0, $totalTarget - $split);
            $user->total_service = $totalService;
            $user->split_service_amount = $split;
            $user->remaining_target_amount = $remainingTargetAmount;
            $user->attendance = $attendance;

            return successResponse($user, 'Employee profile', 0);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    public function getEmployeeProfile($request)
    {
        try {
            $user_id = $request['user_id'];
            $currentDate = now()->format('Y-m-d');

            $user = User::with(['employee.salary'])->find($user_id);

            $splitV2 = SplitServiceAmount::where('beautician_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('price');
            $split = SplitServiceAmount::with('booking.bookingOnlyServices')
                ->where('beautician_id', $user->id)
                ->whereHas('booking.bookingOnlyServices', function ($query) {
                    $query->whereMonth('appointment_date', now()->month)
                        ->whereYear('appointment_date', now()->year);
                })
                ->sum('total_amount');

            $totalServiceV1 = SplitServiceAmount::where('beautician_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count();

            $totalService22 = SplitServiceAmount::with('booking.bookingOnlyServices')
                ->where('beautician_id', $user->id)
                ->whereHas('booking.bookingOnlyServices', function ($query) {
                    $query->whereMonth('appointment_date', now()->month)
                        ->whereYear('appointment_date', now()->year);
                })
                ->count();
            $totalService = BookingService::with('booking.bookingOnlyServices')
                ->where('beautician_id', $user->id)
                ->whereHas('booking.bookingOnlyServices', function ($query) {
                    $query->whereMonth('appointment_date', now()->month)
                        ->whereYear('appointment_date', now()->year);
                })
                ->count();

            $attendance = EmployeeAttendence::where('employee_code', $user->employee->emp_code)
                ->where('date', $currentDate)
                ->first();

            $totalTarget = $user->employee->salary->target_value ?? 0;
            $remainingTargetAmount = max(0, $totalTarget - $split);

            // best perfomer
            $monthStart = Carbon::now()->startOfMonth();
            $monthEnd = Carbon::now();
            // $monthlySalesByBeautician = DB::table('booking_services')
            //     ->join('bookings', 'booking_services.booking_id', '=', 'bookings.booking_id')
            //     ->join('users', 'booking_services.beautician_id', '=', 'users.id')
            //     ->where('bookings.payment_status', 'completed')
            //     ->whereBetween('bookings.created_at', [$monthStart, $monthEnd])
            //     ->where('booking_services.status', 'completed')
            //     ->select(
            //         'users.name as beautician_name',
            //         'users.profile_photo_path',
            //         DB::raw('SUM(booking_services.line_total) as total_sales'),
            //         DB::raw('COUNT(booking_services.booking_service_id) as total_services')
            //     )
            //     ->groupBy('users.name', 'users.profile_photo_path')
            //     // ->orderByDesc('total_sales')
            //     ->get();
            // $maxSales = $monthlySalesByBeautician->max('total_sales');
            // $bestPerformers = $monthlySalesByBeautician->filter(function ($item) use ($maxSales) {
            //     return $item->total_sales == $maxSales;
            // });

            // $bestPerformer = $bestPerformers->first();
            // Filter only required fields

            $monthlySalesByBeautician = SplitServiceAmount::with(['booking', 'beautician'])
                ->whereHas('booking', function ($query) use ($monthStart, $monthEnd) {
                    $query->where('payment_status', 'completed')
                        ->whereBetween('created_at', [$monthStart, $monthEnd]);
                })
                ->select(
                    'beautician_id',
                    DB::raw('SUM(price) as total_sales'),
                    DB::raw('COUNT(*) as total_services')
                )
                ->groupBy('beautician_id')
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'beautician_name' => $item->beautician->name ?? '',
                        'profile_photo_path' => $item->beautician->profile_photo_path ?? '',
                        'total_sales' => number_format((float) $item->total_sales, 2, '.', ''),
                        'total_services' => (int) $item->total_services,
                    ];
                });

            $maxSales = $monthlySalesByBeautician->max('total_sales');

            $bestPerformers = $monthlySalesByBeautician->filter(function ($item) use ($maxSales) {
                return $item->total_sales == $maxSales;
            });

            $bestPerformer = $bestPerformers->first();
            $response = [
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo_path' => $user->profile_photo_path,
                'total_service' => $totalService,
                'split_service_amount' => $split,
                'remaining_target_amount' => $remainingTargetAmount,
                'attendance' => $attendance ? [
                    'check_in' => $attendance->check_in,
                    'check_out' => $attendance->check_out,
                ] : null,
                'employee' => [
                    'emp_code' => $user->employee->emp_code,
                    'punch_status' => $user->employee->punch_status,
                    'latitude' => $user->employee->latitude,
                    'longitude' => $user->employee->longitude,
                    'salary' => [
                        'target_value' => $user->employee->salary->target_value ?? 0,
                    ],
                    'best_performer' => [
                        'data' => $bestPerformer,
                    ],
                ],
            ];

            return successResponse($response, 'Employee profile', 0);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    public function deleteBookingServiceFromBooking($request)
    {
        try {
            $booking_id = $request['booking_id'];
            $booking_info = Booking::where('booking_id', $booking_id)
                ->with(['bookingItems', 'bookingServices'])
                ->first();
            $booking_service = BookingService::where('booking_id', $booking_info->booking_id)
                ->where('booking_service_id', $request['booking_service_id'])
                ->first();

            if (! $booking_service) {
                return apiResponse(1, 'Invalid booking_service_id. Service not found.', null);
            }
            DB::transaction(function () use ($request, $booking_info) {

                BookingService::where('booking_id', $booking_info->booking_id)
                    ->where('booking_service_id', $request['booking_service_id'])
                    ->delete();
                $booking_info->resetBookingAmount();
            });
            $booking_info->loadEssentialsForVueJs();

            return apiResponse(0, 'Service deleted from booking successfully', null);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    public function updateEmployeeProfile($request)
    {
        try {
            DB::beginTransaction();
            $user_id = $request['user_id'];
            $name = $request['name'];
            $email = $request['email'];
            $mobile = $request['mobile'];
            $user = User::find($user_id);
            $user->name = $name;
            $user->email = $email;
            $user->mobile = $mobile;
            $user->save();
            DB::commit();

            return successResponse($user, 'profile updated successfully', 0);
        } catch (Exception $e) {
            DB::rollBack();

            return errorResponse($e->getMessage(), 500);
        }
    }

    public function updateEmployeeProfilePhoto($request, $file)
    {
        try {
            $user_id = $request['user_id'];
            $user = User::find($user_id);

            if (! $user) {
                return errorResponse('User not found', 404);
            }

            if (! $file) {
                return errorResponse('Profile photo is required', 400);
            }

            if (! $file->isValid()) {
                return errorResponse('Invalid file uploaded', 400);
            }

            $existing_photo = $user->profile_photo_path;

            if ($file->isValid()) {
                $ext = $file->extension();
                $filename = 'image_'.preg_replace('/\s+/', '', $user->name).'-'.time().'.'.$ext;
                $path = $file->storeAs('uploads/'.Beautician::BEAUTICIAN_UPLOAD_PATH, $filename, ['disk' => 'public']);
                $user->profile_photo_path = $path;
                $user->save();
                $this->deleteDiskImage($existing_photo);

                return successResponse($user, 'profile photo updated successfully', 0);
            }

            return errorResponse('Invalid file', 400);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    public function deleteDiskImage($filename)
    {
        if (empty($filename)) {
            return;
        }

        $relative = str_contains($filename, '/')
            ? ltrim($filename, '/')
            : Beautician::BEAUTICIAN_UPLOAD_PATH . '/' . $filename;

        $path = storage_path('app/public/' . $relative);

        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function leaveRequest($request)
    {
        try {
            $dates = explode(',', $request['leave_dates']);
            foreach ($dates as $date) {
                $conflict = LeaveManagement::where('beautician_id', $request['user_id'])
                    ->whereDate('leave_date', $date)
                    ->where('leave_status', 'approved')
                    ->exists();

                if ($conflict) {
                    return apiResponse(1, 'Leave request already exists for this date.', null);
                }
            }
            $dates = explode(',', $request['leave_dates']);
            DB::transaction(function () use ($dates, $request) {
                foreach ($dates as $date) {
                    LeaveManagement::create([
                        'leave_date' => $date,
                        'leave_mode' => $request['leave_mode'],
                        'leave_type' => $request['leave_type'],
                        'beautician_id' => $request['user_id'],
                        'emp_id' => $request['emp_id'],
                    ]);
                }
            });

            return apiResponse(0, 'Leave request submitted successfully.', null);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    public function getLeaveRequestsList($request)
    {
        try {
            $leave_requests = LeaveManagement::where('beautician_id', $request['user_id'])
                ->get();

            return apiResponse(0, 'Leave requests fetched successfully', $leave_requests);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    public function deleteLeaveRequest($request)
    {
        try {
            LeaveManagement::where('beautician_id', $request['user_id'])
                ->where('leave_id', $request['leave_id'])
                ->delete();

            return apiResponse(0, 'Leave request deleted successfully.', null);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }
}
