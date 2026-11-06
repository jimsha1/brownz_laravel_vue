<?php

namespace App\Service;

use App\Models\Employee;
use App\Models\EmployeeAttendence;
use App\Models\EmployeePunch;
use App\Models\Shift;
use App\Models\User;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeAttendenceService
{

    public function updateEmployeePunchTime($request)
    {
        try {
            $employee_code = $request->employee_code;
            $punchType = $request->punch_type;
            $currentTime = now();
            $currentDate = $currentTime->format('Y-m-d');

            if (isset($request->image) && $request->hasFile('image')) {
                $imagePath = $request->file('image')->store('uploads/attendence', 'public');
                $request->image = $imagePath;
            }

            $employee = Employee::where('emp_code', $employee_code)->first();
            if (!$employee) {
                return apiResponse(102, "Employee not found.", null);
            }

            $lastPunch = EmployeePunch::where('employee_code', $employee_code)
                ->whereDate('punch_date_time', $currentTime)
                ->orderBy('id', 'desc')
                ->first();

            // Prevent duplicate punches
            if ($lastPunch && $lastPunch->punch_type === $punchType) {
                return apiResponse(101, "Employee already punched $punchType.", null);
            }

            EmployeePunch::create([
                'employee_id' => $employee->emp_id,
                'employee_code' => $employee_code,
                'punch_type' => $punchType,
                'punch_date_time' => $currentTime
            ]);

            $employee->update(['punch_status' => $punchType]);

            // Fetch existing attendance entry for the day
            $attendance = EmployeeAttendence::where('employee_code', $employee_code)
                ->where('date', $currentDate)
                ->first();

            if (!$attendance) {
                // If the attendance entry does not exist, create one (ONLY for check-in)
                if ($punchType === 'IN') {
                    $attendance = new EmployeeAttendence([
                        'employee_id' => $employee->emp_id,
                        'employee_name' => $employee->name,
                        'employee_code' => $employee->emp_code,
                        'date' => $currentDate,
                        'attendance_status' => 'present',
                        'check_in' => $currentTime->format('H:i'),
                        'check_out' => null,
                        'check_in_image' => $request->image ?? null,
                        'check_out_image' => null,
                        'punch_status' => 'checked_in'
                    ]);
                } else {
                    return apiResponse(103, "No check-in record found for check-out.", null);
                }
            } else {
                // If attendance already exists, update check-in or check-out
                if ($punchType === 'IN') {
                    $attendance->check_in = $currentTime->format('H:i');
                    $attendance->punch_status = 'checked_in';
                    $attendance->check_out = null; // Reset check-out on new check-in
                    $attendance->check_in_image = $request->image ?? null;
                    $attendance->check_out_image = null;
                    $attendance->overtime = null; // Reset overtime on new check-in
                } elseif ($punchType === 'OUT') {
                    if (!$attendance->check_in) {
                        return apiResponse(104, "Cannot check out without checking in first.", null);
                    }
                    $attendance->check_out = $currentTime->format('H:i');
                    $attendance->punch_status = 'checked_out';
                    $attendance->check_out_image = $request->image ?? null;

                    // Calculate overtime when check-out is recorded
                    $user_id = $employee->user_id;
                    $shift_id = User::where('id', $user_id)->value('shift_id');
                    $shift = Shift::where('shift_id', $shift_id)->first();

                    if ($shift && $attendance->check_in && $attendance->check_out) {
                        $overtime = $this->calculateOvertime(
                            $attendance->check_in,
                            $attendance->check_out,
                            $shift->start_time,
                            $shift->end_time,
                            $employee->emp_id,
                            $currentDate
                        );
                        $attendance->overtime = $overtime;
                    }
                }
            }

            $attendance->save();

            return apiResponse(0, "Employee punch $punchType recorded successfully.", ['data' => $attendance]);
        } catch (Exception $e) {
            return apiResponse(100, $e->getMessage(), null);
        }
    }



    public function attendanceList($data = null, $platform = 'web')
    {

        $employeesList = Employee::with('leave', 'user.shift')->where('status', 'active')->get();
      
        $result = EmployeeAttendence::where('date', $data->date)
            ->orderBy($data->order_by ?? 'employee_id', $data->sort_order ?? 'DESC')
            ->paginate(config('vocomi.settings.pagination.perpage.admin'));

        $attendanceCollection = $result->getCollection()->keyBy('employee_id');

        $employees = [];

        foreach ($employeesList as $employee) {
            $attendanceEntry = $attendanceCollection->get($employee->emp_id);

            // $weekOffLeave = $employee->leave->firstWhere('leave_mode', 'week_off');
            $leaveEntries = $employee->leave->where('leave_status', 'approved');

            $leaveData = [];
            foreach ($leaveEntries as $leave) {

                if ($leave->leave_date == $data->date) {
                    $leaveData[] = [
                        'leave_mode' => $leave->leave_mode,
                        'leave_type' => $leave->leave_type,
                    ];
                }
            }

            $employees[] = [
                'employee_id' => $employee->emp_id,
                'attendence_id' => $attendanceEntry->attendence_id ?? null,
                'employee_name' => $employee->name,
                'employee_code' => $employee->emp_code,
                'check_in' => $attendanceEntry->check_in ?? null,
                'check_out' => $attendanceEntry->check_out ?? null,
                'attendance_date' => $attendanceEntry->date ?? $data->date,
                'attendance_status' => $attendanceEntry->attendance_status ?? null,
                'attendance_updated' => $attendanceEntry->updated_at ?? null,
                'check_in_image' => $attendanceEntry->check_in_image ?? null,
                'check_out_image' => $attendanceEntry->check_out_image ?? null,
                'overtime' => $attendanceEntry->overtime ?? null,
                'leave_data' => $leaveData,

                'shift_start_time' => $employee->user->shift->start_time ?? null,
                'shift_end_time' => $employee->user->shift->end_time ?? null,

            ];
        }

        $response['pagination'] = generatePaginationData($result);
        $response['data'] = $employees;

        return apiResponse(0, 'success', $response);
    }

    public function attendance_store($data = null, $platform = 'web')
    {

        try {
            DB::beginTransaction();

            $checkIn = $this->normalizeAttendanceTime($data->check_in ?? null);
            $checkOut = $this->normalizeAttendanceTime($data->check_out ?? null);
            $attendanceStatus = $data->attendance_status ?? 'present';
            $overtime = null;
            $attendanceDate = $data->date ?? now()->format('Y-m-d');

            if ($attendanceStatus === 'absent') {
                $checkIn = null;
                $checkOut = null;
            }

            if ($checkOut && !$checkIn) {
                DB::rollBack();
                return apiResponse(16, 'Please enter Check In before Check Out.', null);
            }

            if ($checkIn && $checkOut) {
                $checkInTime = Carbon::parse($checkIn);
                $checkOutTime = Carbon::parse($checkOut);

                if ($checkOutTime->lte($checkInTime)) {
                    DB::rollBack();
                    return apiResponse(17, 'Check Out must be after Check In.', null);
                }

                $user_id = Employee::where('emp_id', $data->employee_id)->value('user_id');
                $shift_id = User::where('id', $user_id)->value('shift_id');
                $shift = Shift::where('shift_id', $shift_id)->first();

                $overtime = $this->calculateOvertime(
                    $checkIn,
                    $checkOut,
                    $shift ? $shift->start_time : null,
                    $shift ? $shift->end_time : null,
                    $data->employee_id,
                    $attendanceDate
                );
            }

            $attendancePayload = [
                'employee_id' => $data->employee_id,
                'employee_name' => $data->employee_name,
                'employee_code' => $data->employee_code,
                'date' => $attendanceDate,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'attendance_status' => $attendanceStatus,
                'overtime' => $overtime,
                'punch_status' => $checkOut ? 'checked_out' : ($checkIn ? 'checked_in' : null),
            ];

            if (!empty($data->attendence_id)) {
                $attendanceStore = EmployeeAttendence::updateOrCreate(
                    ['attendence_id' => $data->attendence_id],
                    $attendancePayload
                );
            } else {
                $attendanceStore = EmployeeAttendence::updateOrCreate(
                    [
                        'employee_id' => $data->employee_id,
                        'date' => $attendanceDate,
                    ],
                    $attendancePayload
                );
            }

            if ($attendanceStore) {
                if ($attendanceStatus === 'absent') {
                    $errormessage = 'Marked as Absent successfully.';
                } elseif ($checkIn && !$checkOut) {
                    $errormessage = 'Check In saved successfully.';
                } elseif ($checkIn && $checkOut) {
                    $errormessage = 'Check In & Check Out saved successfully.';
                } else {
                    $errormessage = $data->attendence_id > 0
                        ? 'Attendance Updated Successfully.'
                        : 'Attendance Created Successfully.';
                }
                $errorcode = 0;
            } else {
                $errormessage = 'Database error...';
                $errorcode = 15;
            }

            DB::commit();

            if ($platform == 'web') {
                return apiResponse($errorcode, $errormessage, $attendanceStore);
            }
        } catch (\Exception $e) {
            Log::debug($e);
            DB::rollBack();
            return apiResponse(25, $e->getMessage());
        }
    }

    private function normalizeAttendanceTime($time)
    {
        if ($time === null || $time === '' || $time === '--:--') {
            return null;
        }

        return $time;
    }

    private function calculateOvertime($checkIn, $checkOut, $shiftStart, $shiftEnd, $employeeId = null, $date = null)
    {
        if (
            !$checkIn || !$checkOut || !$shiftStart || !$shiftEnd ||
            $checkIn === '--:--' || $checkOut === '--:--'
        ) {
            return '00:00:00';
        }

        try {
            // If employee_id and date are provided, use the same logic as daily working hour report
            if ($employeeId && $date) {
                // Get user_id (beautician_id) from employee_id
                $user_id = Employee::where('emp_id', $employeeId)->value('user_id');

                if (!$user_id) {
                    Log::warning("Employee not found for employee_id: {$employeeId}");
                    return '00:00:00';
                }

                $beauticianId = $user_id; // beautician_id is the same as user_id

                // Format check-in and check-out times to H:i:s format
                $checkInFormatted = strlen($checkIn) == 5 ? $checkIn . ':00' : $checkIn;
                $checkOutFormatted = strlen($checkOut) == 5 ? $checkOut . ':00' : $checkOut;

                // Handle early and late check-in adjustments (same as working hours report)
                $adjustedShiftEnd = $shiftEnd;
                $checkInDelaySeconds = 0;
                $earlyCheckInSeconds = 0;

                if ($shiftStart && $shiftEnd && $checkInFormatted) {
                    $shiftStartTime = strtotime($date . ' ' . $shiftStart);
                    $shiftEndTime = strtotime($date . ' ' . $shiftEnd);
                    $actualCheckIn = strtotime($date . ' ' . $checkInFormatted);

                    if ($actualCheckIn > $shiftStartTime) {
                        // Late check-in → extend shift end
                        $checkInDelaySeconds = $actualCheckIn - $shiftStartTime;
                        $adjustedShiftEnd = date('H:i:s', $shiftEndTime + $checkInDelaySeconds);
                        $earlyCheckInSeconds = 0;
                    } elseif ($actualCheckIn < $shiftStartTime) {
                        // Early check-in → reduce shift end
                        $earlyCheckInSeconds = $shiftStartTime - $actualCheckIn;
                        $adjustedShiftEnd = date('H:i:s', $shiftEndTime - $earlyCheckInSeconds);
                        $checkInDelaySeconds = 0;
                    }

                    Log::debug("Employee {$employeeId} on {$date}: Check-in={$checkInFormatted}, ShiftStart={$shiftStart}, ShiftEnd={$shiftEnd}, AdjustedShiftEnd={$adjustedShiftEnd}, EarlyCheckIn=" . gmdate('H:i:s', $earlyCheckInSeconds) . ", LateCheckIn=" . gmdate('H:i:s', $checkInDelaySeconds));
                }

                // Fetch services attended for this beautician on this date
                $services = DB::table('booking_services as bs')
                    ->join('services as srv', 'bs.service_id', '=', 'srv.service_id')
                    ->select(
                        'srv.name as service_name',
                        DB::raw('TIME_FORMAT(bs.service_started_at, "%H:%i:%s") as service_started_at'),
                        DB::raw('TIME_FORMAT(bs.service_finished_at, "%H:%i:%s") as service_finished_at')
                    )
                    ->where('bs.beautician_id', $beauticianId)
                    ->whereDate('bs.appointment_date', $date)
                    ->whereNotNull('bs.service_started_at')
                    ->whereNotNull('bs.service_finished_at')
                    ->orderBy('bs.service_started_at', 'asc')
                    ->get();

                Log::debug("Services for employee {$employeeId} (beautician {$beauticianId}) on {$date}: " . json_encode($services->toArray()));

                // Calculate overtime based on services after adjusted shift end
                $overTimeSeconds = 0;
                $adjustedShiftEndTime = $adjustedShiftEnd ? strtotime($date . ' ' . $adjustedShiftEnd) : ($shiftEnd ? strtotime($date . ' ' . $shiftEnd) : null);

                foreach ($services as $service) {
                    $serviceEnd = strtotime($service->service_finished_at);
                    $serviceStart = strtotime($service->service_started_at);
                    if ($adjustedShiftEndTime && $serviceEnd > $adjustedShiftEndTime) {
                        // Calculate the portion of the service that is overtime
                        $overtimeStart = max($serviceStart, $adjustedShiftEndTime);
                        $overTimeSeconds += $serviceEnd - $overtimeStart;
                    }
                }

                // Format overtime
                $hours = floor($overTimeSeconds / 3600);
                $minutes = floor(($overTimeSeconds % 3600) / 60);
                $seconds = $overTimeSeconds % 60;

                return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }

            // Fallback to old calculation if employee_id or date not provided
            $checkInTime = Carbon::parse($checkIn);
            $checkOutTime = Carbon::parse($checkOut);
            $shiftStartTime = Carbon::parse($shiftStart);
            $shiftEndTime = Carbon::parse($shiftEnd);

            $workedSeconds = $checkInTime->diffInSeconds($checkOutTime);
            $shiftSeconds = $shiftStartTime->diffInSeconds($shiftEndTime);

            if ($workedSeconds > $shiftSeconds) {
                $overtimeSeconds = $workedSeconds - $shiftSeconds;

                $hours = floor($overtimeSeconds / 3600);
                $minutes = floor(($overtimeSeconds % 3600) / 60);
                $seconds = $overtimeSeconds % 60;

                return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }
            return '00:00:00';
        } catch (\Exception $e) {
            Log::error('Overtime Calculation Error: ' . $e->getMessage());
            return '00:00:00';
        }
    }
}
