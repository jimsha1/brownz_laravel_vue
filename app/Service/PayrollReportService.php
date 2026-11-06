<?php

namespace App\Service;

use App\Models\Booking;
use App\Models\Commission;
use App\Models\Employee;
use App\Models\EmployeeAttendence;
use App\Models\Incentive;
use App\Models\Payroll;
use App\Models\PayrollPayments;
use App\Models\Salary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayrollReportService
{
    public function salaryReportList($data = null, $platform = 'web')
    {
        $result = PayrollPayments::with('payroll.employee')
            ->orderByDesc('payment_date')
            ->orderByDesc('payroll_payment_id');

        $this->applyPayrollPaymentReportFilters($result, $data);

        if ($platform == 'web') {
            $result = $result->paginate(config('vocomi.settings.pagination.perpage.admin'));

            $response = [
                'pagination' => generatePaginationData($result),
                'data' => $result,
            ];

            return apiResponse(0, 'success', $response);
        } else {
        }
    }

    public function commissionReportList($data = null, $platform = 'web')
    {
        $commission = Commission::with('employee', 'employee.user')
            ->where('commission_amount', '!=', '0.00')->orderBy('created_at', 'DESC');

        $startDate = $this->normalizeReportDate($data->start_date);
        $endDate = $this->normalizeReportDate($data->end_date);

        $this->applyStringDateRangeFilter($commission, 'start_date', $startDate, $endDate);

        if (! empty($data->emp_id)) {
            $commission->where('emp_id', $data->emp_id);
        } elseif (! empty($data->employee_name)) {
            $commission->where('emp_name', 'LIKE', "%{$data->employee_name}%");
        }

        if ($platform == 'web') {
            $result = $commission->paginate(config('vocomi.settings.pagination.perpage.admin'));

            $response = [
                'pagination' => generatePaginationData($result),
                'data' => $result,
            ];

            return apiResponse(0, 'success', $response);
        } else {
        }
    }

    public function attendanceReportList($data = null, $platform = 'web')
    {
        $attendanceQuery = $this->buildAttendanceReportQuery($data);

        if ($platform == 'web') {
            $result = $attendanceQuery->paginate(config('vocomi.settings.pagination.perpage.admin'));

            $result->getCollection()->transform(function ($row) {
                return $this->formatAttendanceReportRow($row);
            });

            return apiResponse(0, 'success', [
                'pagination' => generatePaginationData($result),
                'data' => $result->items(),
            ]);
        }
    }

    /**
     * Shared attendance-report query for list / PDF / Excel.
     */
    private function buildAttendanceReportQuery($data)
    {
        $query = EmployeeAttendence::query()
            ->with('employee:emp_id,emp_code,name,user_id')
            ->leftJoin('employees', 'employees.emp_id', '=', 'employee_attendences.employee_id')
            ->leftJoin('users', 'users.id', '=', 'employees.user_id')
            ->leftJoin('shifts', 'shifts.shift_id', '=', 'users.shift_id')
            ->select(
                'employee_attendences.*',
                'shifts.start_time as shift_start_time',
                'shifts.end_time as shift_end_time'
            )
            ->orderByDesc('employee_attendences.date')
            ->orderByDesc('employee_attendences.attendence_id');

        $startDate = $this->normalizeReportDate($data->start_date ?? null);
        $endDate = $this->normalizeReportDate($data->end_date ?? null);

        // `date` is a DATE column — compare as dates only
        if ($startDate && $endDate) {
            $query->whereBetween('employee_attendences.date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('employee_attendences.date', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('employee_attendences.date', '<=', $endDate);
        }

        if (! empty($data->emp_id)) {
            $query->where('employee_attendences.employee_id', $data->emp_id);
        } elseif (! empty($data->employee_name)) {
            $name = trim($data->employee_name);
            $query->where(function ($q) use ($name) {
                $q->where('employee_attendences.employee_name', 'LIKE', "%{$name}%")
                    ->orWhere('employees.name', 'LIKE', "%{$name}%");
            });
        }

        if (! empty($data->attendanceType)) {
            switch ($data->attendanceType) {
                case 'leave':
                case 'absent':
                    $query->where('employee_attendences.attendance_status', 'absent');
                    break;
                case 'late':
                    $query->whereNotNull('employee_attendences.check_in')
                        ->whereNotNull('shifts.start_time')
                        ->whereRaw('TIME(employee_attendences.check_in) > TIME(shifts.start_time)');
                    break;
                case 'over_time':
                    $query->whereNotNull('employee_attendences.check_out')
                        ->whereNotNull('shifts.end_time')
                        ->whereRaw('TIME(employee_attendences.check_out) > TIME(shifts.end_time)');
                    break;
                case 'present':
                    $query->where('employee_attendences.attendance_status', 'present');
                    break;
            }
        }

        return $query;
    }

    private function formatAttendanceReportRow($row)
    {
        $checkIn = $row->check_in;
        $checkOut = $row->check_out;
        $shiftStart = $row->shift_start_time ?? null;
        $shiftEnd = $row->shift_end_time ?? null;

        $isLate = false;
        $isOvertime = false;

        if ($checkIn && $shiftStart && $checkIn !== '--:--') {
            $isLate = strtotime(substr((string) $checkIn, 0, 8)) > strtotime(substr((string) $shiftStart, 0, 8));
        }

        if ($checkOut && $shiftEnd && $checkOut !== '--:--') {
            $isOvertime = strtotime(substr((string) $checkOut, 0, 8)) > strtotime(substr((string) $shiftEnd, 0, 8));
        }

        return [
            'attendence_id' => $row->attendence_id,
            'employee_id' => $row->employee_id,
            'employee_code' => $row->employee_code,
            'employee_name' => $row->employee_name ?: ($row->employee->name ?? null),
            'date' => $row->date ? substr((string) $row->date, 0, 10) : null,
            'check_in' => ($checkIn && $checkIn !== '--:--') ? substr((string) $checkIn, 0, 8) : null,
            'check_out' => ($checkOut && $checkOut !== '--:--') ? substr((string) $checkOut, 0, 8) : null,
            'attendance_status' => $row->attendance_status,
            'overtime' => $row->overtime,
            'shift_start_time' => $shiftStart ? substr((string) $shiftStart, 0, 8) : null,
            'shift_end_time' => $shiftEnd ? substr((string) $shiftEnd, 0, 8) : null,
            'is_late' => $isLate,
            'is_overtime' => $isOvertime,
        ];
    }

    public function payrollReportList($data = null, $platform = 'web')
    {
        $payroll = $this->buildPayrollReportQuery($data);

        if ($platform == 'web') {
            $result = $payroll->paginate(config('vocomi.settings.pagination.perpage.admin'));

            $response = [
                'pagination' => generatePaginationData($result),
                'data' => $result,
            ];

            return apiResponse(0, 'success', $response);
        } else {
        }
    }

    public function incentiveReportList($data = null, $platform = 'web')
    {
        $payroll = $this->buildIncentiveReportQuery($data);

        if ($platform == 'web') {
            $result = $payroll->paginate(config('vocomi.settings.pagination.perpage.admin'));

            $response = [
                'pagination' => generatePaginationData($result),
                'data' => $result,
            ];

            return apiResponse(0, 'success', $response);
        } else {
        }
    }

    public function salaryexportToPDF($data = null, $platform = 'web')
    {
        $records = PayrollPayments::with('payroll.employee')
            ->orderByDesc('payment_date')
            ->orderByDesc('payroll_payment_id');

        $this->applyPayrollPaymentReportFilters($records, $data);

        $records = $records->get();

        return [
            'records' => $records,
        ];
    }

    public function commissionexportToPDF($data = null, $platform = 'web')
    {
        $records = Commission::with('employee', 'employee.user')
            ->where('commission_amount', '!=', '0.00');

        $startDate = $this->normalizeReportDate($data->start_date);
        $endDate = $this->normalizeReportDate($data->end_date);

        $this->applyStringDateRangeFilter($records, 'start_date', $startDate, $endDate);

        if (! empty($data->emp_id)) {
            $records->where('emp_id', $data->emp_id);
        } elseif (! empty($data->employee_name)) {
            $records->where('emp_name', 'LIKE', "%{$data->employee_name}%");
        }

        $records = $records->get();

        return [
            'records' => $records,
        ];
    }

    public function attendanceexportToPDF($data = null, $platform = 'web')
    {
        $records = $this->buildAttendanceReportQuery($data)->get();

        return [
            'records' => $records,
        ];
    }

    public function attendanceReportFormattedRows($data = null)
    {
        return $this->buildAttendanceReportQuery($data)->get()->map(function ($row) {
            return $this->formatAttendanceReportRow($row);
        });
    }

    public function payrollexportToPDF($data = null, $platform = 'web')
    {
        $records = $this->buildPayrollReportQuery($data)->get();

        return [
            'records' => $records,
        ];
    }

    public function incentiveexportToPDF($data = null, $platform = 'web')
    {
        $records = $this->buildIncentiveReportQuery($data)->get();

        return [
            'records' => $records,
        ];
    }

    // public function workinHoursReportList($data = null, $platform = 'web')
    // {
    //     // Extract start and end dates from input data
    //     $startDate = $data->start_date ?? null;
    //     $endDate = $data->end_date ?? null;

    //     // Base Query to fetch booking services, beautician, user, and shift details
    //     $result = DB::table('booking_services as bs')
    //         ->join('beauticians as b', 'bs.beautician_id', '=', 'b.beautician_id')
    //         ->join('users as u', 'b.beautician_id', '=', 'u.id')
    //         ->leftJoin('shifts as s', 'bs.shift_id', '=', 's.shift_id')
    //         ->select(
    //             'bs.beautician_id',
    //             'u.name as employee_name',
    //             'bs.appointment_date',
    //             DB::raw("SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(bs.service_finished_at, bs.service_started_at)))) as total_working_time"),
    //             DB::raw("SEC_TO_TIME(SUM(bs.duration_minutes * 60)) as total_scheduled_time"),
    //             DB::raw("s.start_time"),
    //             DB::raw("s.end_time")
    //         )
    //         ->whereNotNull('bs.service_started_at')
    //         ->whereNotNull('bs.service_finished_at');

    //     // Apply date filters
    //     if ($startDate && $endDate) {
    //         $result->whereBetween('bs.appointment_date', [$startDate, $endDate]);
    //     } elseif ($startDate) {
    //         $result->where('bs.appointment_date', '>=', $startDate);
    //     } elseif ($endDate) {
    //         $result->where('bs.appointment_date', '<=', $endDate);
    //     }

    //     // Group by beautician and day, order by date, and paginate
    //     $result = $result
    //         ->groupBy('bs.beautician_id', 'u.name', 'bs.appointment_date', 's.start_time', 's.end_time')
    //         ->orderBy('bs.appointment_date', 'asc')
    //         ->paginate(config('vocomi.settings.pagination.perpage.admin'));

    //     // Map and calculate additional fields
    //     $dataList = $result->map(function ($item) {
    //         // Fetch employee details
    //         $employee = Employee::where('user_id', $item->beautician_id)->first();

    //         // Fetch attendance for this beautician and date, casting times to H:i:s
    //         $attendance = DB::table('employee_attendences')
    //             ->where('employee_id', $employee->emp_id)
    //             ->whereDate('date', $item->appointment_date)
    //             ->select(
    //                 DB::raw('TIME_FORMAT(check_in, "%H:%i:%s") as check_in'),
    //                 DB::raw('TIME_FORMAT(check_out, "%H:%i:%s") as check_out')
    //             )
    //             ->first();

    //         $checkIn = $attendance->check_in ?? null;
    //         $checkOut = $attendance->check_out ?? null;

    //         // Define shift times
    //         $shiftStartTime = $item->start_time ?? null;
    //         $shiftEndTime = $item->end_time ?? null;

    //         // Calculate adjusted shift end due to late check-in
    //         $adjustedShiftEnd = $shiftEndTime;
    //         $checkInDelaySeconds = 0;
    //         if ($shiftStartTime && $shiftEndTime && $checkIn) {
    //             $checkInDelaySeconds = max(0, strtotime($item->appointment_date . ' ' . $checkIn) - strtotime($item->appointment_date . ' ' . $shiftStartTime));
    //             $adjustedShiftEnd = date('H:i:s', strtotime($item->appointment_date . ' ' . $shiftEndTime) + $checkInDelaySeconds);
    //             Log::debug("Beautician {$item->beautician_id}, Date {$item->appointment_date}: Check-in: $checkIn, Shift Start: $shiftStartTime, Shift End: $shiftEndTime, Adjusted Shift End: $adjustedShiftEnd");
    //         } else {
    //             Log::warning("Missing check-in or shift times for beautician {$item->beautician_id} on {$item->appointment_date}");
    //         }

    //         // Calculate check-out delay
    //         $checkOutDelaySeconds = 0;
    //         if ($checkOut && $adjustedShiftEnd) {
    //             $checkOutDelaySeconds = max(0, strtotime($item->appointment_date . ' ' . $checkOut) - strtotime($item->appointment_date . ' ' . $adjustedShiftEnd));
    //         }

    //         // Fetch services attended, casting timestamps to H:i:s
    //         $services = DB::table('booking_services as bs')
    //             ->join('services as srv', 'bs.service_id', '=', 'srv.service_id')
    //             ->select(
    //                 'srv.name as service_name',
    //                 DB::raw('TIME_FORMAT(bs.service_started_at, "%H:%i:%s") as service_started_at'),
    //                 DB::raw('TIME_FORMAT(bs.service_finished_at, "%H:%i:%s") as service_finished_at'),
    //                 DB::raw("TIME_FORMAT(TIMEDIFF(bs.service_finished_at, bs.service_started_at), '%H:%i:%s') as duration"),
    //                 DB::raw("TIME_FORMAT(bs.service_started_at, '%H:%i:%s') as start_time"),
    //                 DB::raw("TIME_FORMAT(bs.service_finished_at, '%H:%i:%s') as end_time")
    //             )
    //             ->where('bs.beautician_id', $item->beautician_id)
    //             ->whereDate('bs.appointment_date', $item->appointment_date)
    //             ->whereNotNull('bs.service_started_at')
    //             ->whereNotNull('bs.service_finished_at')
    //             ->orderBy('bs.service_started_at', 'asc')
    //             ->get();

    //         Log::debug("Services for beautician {$item->beautician_id} on {$item->appointment_date}: " . json_encode($services->toArray()));

    //         // Calculate overtime based on services after adjusted shift end
    //         $overTimeSeconds = 0;
    //         $adjustedShiftEndTime = $adjustedShiftEnd ? strtotime($item->appointment_date . ' ' . $adjustedShiftEnd) : ($shiftEndTime ? strtotime($item->appointment_date . ' ' . $shiftEndTime) : null);

    //         foreach ($services as $service) {
    //             $serviceEnd = strtotime($service->service_finished_at);
    //             $serviceStart = strtotime($service->service_started_at);
    //             if ($adjustedShiftEndTime && $serviceEnd > $adjustedShiftEndTime) {
    //                 // Calculate the portion of the service that is overtime
    //                 $overtimeStart = max($serviceStart, $adjustedShiftEndTime);
    //                 $overTimeSeconds += $serviceEnd - $overtimeStart;
    //             }
    //         }

    //         // Calculate free time: gaps between check-in, services, and check-out
    //         $freeSeconds = 0;
    //         if ($checkIn && $checkOut && $services->count() > 0) {
    //             $checkInTime = strtotime($item->appointment_date . ' ' . $checkIn);
    //             $checkOutTime = strtotime($item->appointment_date . ' ' . $checkOut);
    //             Log::debug("Check-in time: $checkIn ($checkInTime), Check-out time: $checkOut ($checkOutTime)");

    //             // From check-in to first service start
    //             $firstServiceStart = strtotime($services[0]->service_started_at);
    //             $firstGap = $firstServiceStart > $checkInTime ? $firstServiceStart - $checkInTime : 0;
    //             Log::debug("First gap (check-in to first service): $firstGap seconds (First service start: {$services[0]->service_started_at})");
    //             $freeSeconds += $firstGap;

    //             // Between consecutive services
    //             $gapSeconds = 0;
    //             for ($i = 1; $i < $services->count(); $i++) {
    //                 $prevEnd = strtotime($services[$i - 1]->service_finished_at);
    //                 $currStart = strtotime($services[$i]->service_started_at);
    //                 $gap = $currStart > $prevEnd ? $currStart - $prevEnd : 0;
    //                 $gapSeconds += $gap;
    //                 Log::debug("Gap between service {$i} and " . ($i - 1) . ": $gap seconds (Prev end: {$services[$i - 1]->service_finished_at}, Curr start: {$services[$i]->service_started_at})");
    //             }
    //             $freeSeconds += $gapSeconds;

    //             // From last service end to check-out
    //             $lastServiceEnd = strtotime($services->last()->service_finished_at);
    //             $lastGap = $checkOutTime > $lastServiceEnd ? $checkOutTime - $lastServiceEnd : 0;
    //             Log::debug("Last gap (last service to check-out): $lastGap seconds (Last service end: {$services->last()->service_finished_at})");
    //             $freeSeconds += $lastGap;

    //             Log::debug("Total free seconds for beautician {$item->beautician_id} on {$item->appointment_date}: $freeSeconds");
    //         } elseif ($checkIn && $checkOut && $services->isEmpty()) {
    //             // If no services, entire period from check-in to check-out is free
    //             $checkInTime = strtotime($item->appointment_date . ' ' . $checkIn);
    //             $checkOutTime = strtotime($item->appointment_date . ' ' . $checkOut);
    //             $freeSeconds = $checkOutTime - $checkInTime;
    //             Log::debug("No services, free time: $freeSeconds seconds (Check-in: $checkIn, Check-out: $checkOut)");
    //         }
    //         // If no check-in or check-out, free time remains 0

    //         // Format durations, using floor to avoid fractional seconds
    //         $item->total_working_time = gmdate("H:i:s", strtotime($item->total_working_time) - strtotime('00:00:00'));
    //         $item->total_scheduled_time = gmdate("H:i:s", strtotime($item->total_scheduled_time) - strtotime('00:00:00'));
    //         $item->over_time = gmdate("H:i:s", floor($overTimeSeconds));
    //         $item->free_time = gmdate("H:i:s", floor($freeSeconds));
    //         $item->check_in_delay = gmdate("H:i:s", floor($checkInDelaySeconds));
    //         $item->check_out_delay = gmdate("H:i:s", floor($checkOutDelaySeconds));

    //         // Add status to each service
    //         $item->services = $services->map(function ($service) use ($adjustedShiftEnd, $item) {
    //             $serviceStart = strtotime($service->service_started_at);
    //             $serviceEnd = strtotime($service->service_finished_at);
    //             $adjustedShiftEndTime = $adjustedShiftEnd ? strtotime($item->appointment_date . ' ' . $adjustedShiftEnd) : ($item->end_time ? strtotime($item->appointment_date . ' ' . $item->end_time) : null);

    //             // Default = working
    //             $service->status = 'working';

    //             // If service ends after adjusted shift end → overtime
    //             if ($adjustedShiftEndTime && $serviceEnd > $adjustedShiftEndTime) {
    //                 $service->status = 'overtime';
    //             }

    //             Log::debug("Service {$service->service_name}: serviceEnd=$serviceEnd, adjustedShiftEndTime=" . ($adjustedShiftEndTime ?? 'null') . ", Status={$service->status}");

    //             return $service;
    //         });

    //         return $item;
    //     });

    //     // Prepare response with pagination and data
    //     $response = [
    //         'pagination' => generatePaginationData($result),
    //         'data' => $dataList,
    //     ];

    //     return apiResponse(0, "success", $response);
    // }

    // public function workinHoursReportList($data = null, $platform = 'web')
    // {
    //     $startDate = $data->start_date ?? null;
    //     $endDate = $data->end_date ?? null;

    //     $result = DB::table('booking_services as bs')
    //         ->join('beauticians as b', 'bs.beautician_id', '=', 'b.beautician_id')
    //         ->join('users as u', 'b.beautician_id', '=', 'u.id')
    //         ->leftJoin('shifts as s', 'bs.shift_id', '=', 's.shift_id')
    //         ->select(
    //             'bs.beautician_id',
    //             'u.name as employee_name',
    //             'bs.appointment_date',
    //             DB::raw("SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(bs.service_finished_at, bs.service_started_at)))) as total_working_time"),
    //             DB::raw("SEC_TO_TIME(SUM(bs.duration_minutes * 60)) as total_scheduled_time"),
    //             DB::raw("MIN(s.start_time) as start_time"),
    //             DB::raw("MAX(s.end_time) as end_time")
    //         )

    //         ->whereNotNull('bs.service_started_at')
    //         ->whereNotNull('bs.service_finished_at');

    //     if ($startDate && $endDate) {
    //         $result->whereBetween('bs.appointment_date', [$startDate, $endDate]);
    //     } elseif ($startDate) {
    //         $result->where('bs.appointment_date', '>=', $startDate);
    //     } elseif ($endDate) {
    //         $result->where('bs.appointment_date', '<=', $endDate);
    //     }

    //     $result = $result
    //         ->groupBy('bs.beautician_id', 'u.name', 'bs.appointment_date', 's.start_time', 's.end_time')
    //         ->orderBy('bs.appointment_date', 'asc')
    //         ->paginate(config('vocomi.settings.pagination.perpage.admin'));

    //     $dataList = $result->map(function ($item) {

    //         // Employee record
    //         $employee = Employee::where('user_id', $item->beautician_id)->first();

    //         // Attendance
    //         $attendance = DB::table('employee_attendences')
    //             ->where('employee_id', $employee->emp_id)
    //             ->whereDate('date', $item->appointment_date)
    //             ->select(
    //                 DB::raw('TIME_FORMAT(check_in, "%H:%i:%s") as check_in'),
    //                 DB::raw('TIME_FORMAT(check_out, "%H:%i:%s") as check_out')
    //             )
    //             ->first();

    //         $checkIn = $attendance->check_in ?? null;
    //         $checkOut = $attendance->check_out ?? null;

    //         $shiftStartTime = $item->start_time ?? null;
    //         $shiftEndTime = $item->end_time ?? null;

    //         // --- SHIFT ADJUSTMENT ---
    //         $adjustedShiftStart = $shiftStartTime;
    //         $adjustedShiftEnd = $shiftEndTime;
    //         $checkInDelaySeconds = 0;
    //         $earlyCheckInSeconds = 0;

    //         if ($shiftStartTime && $shiftEndTime && $checkIn) {
    //             $shiftStart = strtotime($item->appointment_date . ' ' . $shiftStartTime);
    //             $shiftEnd   = strtotime($item->appointment_date . ' ' . $shiftEndTime);
    //             $checkInTs  = strtotime($item->appointment_date . ' ' . $checkIn);

    //             // Late check-in
    //             if ($checkInTs > $shiftStart) {
    //                 $checkInDelaySeconds = $checkInTs - $shiftStart;
    //                 $adjustedShiftEnd = date('H:i:s', $shiftEnd + $checkInDelaySeconds);
    //                 Log::debug("Late Check-in detected for beautician {$item->beautician_id}: +{$checkInDelaySeconds}s");
    //             }
    //             // Early check-in
    //             elseif ($checkInTs < $shiftStart) {
    //                 $earlyCheckInSeconds = $shiftStart - $checkInTs;
    //                 $adjustedShiftStart = $checkIn;
    //                 $adjustedShiftEnd = date('H:i:s', $shiftEnd - $earlyCheckInSeconds);
    //                 Log::debug("Early Check-in detected for beautician {$item->beautician_id}: -{$earlyCheckInSeconds}s");
    //             }

    //             Log::debug("Adjusted shift for beautician {$item->beautician_id} on {$item->appointment_date}: $adjustedShiftStart → $adjustedShiftEnd");
    //         } else {
    //             Log::warning("Missing check-in or shift times for beautician {$item->beautician_id} on {$item->appointment_date}");
    //         }

    //         $checkOutDelaySeconds = 0;
    //         if ($checkOut && $adjustedShiftEnd) {
    //             $checkOutDelaySeconds = max(0, strtotime($item->appointment_date . ' ' . $checkOut) - strtotime($item->appointment_date . ' ' . $adjustedShiftEnd));
    //         }

    //         $services = DB::table('booking_services as bs')
    //             ->join('services as srv', 'bs.service_id', '=', 'srv.service_id')
    //             ->select(
    //                 'srv.name as service_name',
    //                 DB::raw('TIME_FORMAT(bs.service_started_at, "%H:%i:%s") as service_started_at'),
    //                 DB::raw('TIME_FORMAT(bs.service_finished_at, "%H:%i:%s") as service_finished_at'),
    //                 DB::raw("TIME_FORMAT(TIMEDIFF(bs.service_finished_at, bs.service_started_at), '%H:%i:%s') as duration"),
    //                 DB::raw("TIME_FORMAT(bs.service_started_at, '%H:%i:%s') as start_time"),
    //                 DB::raw("TIME_FORMAT(bs.service_finished_at, '%H:%i:%s') as end_time")
    //             )
    //             ->where('bs.beautician_id', $item->beautician_id)
    //             ->whereDate('bs.appointment_date', $item->appointment_date)
    //             ->whereNotNull('bs.service_started_at')
    //             ->whereNotNull('bs.service_finished_at')
    //             ->orderBy('bs.service_started_at', 'asc')
    //             ->get();

    //         $overTimeSeconds = 0;
    //         $adjustedShiftEndTime = $adjustedShiftEnd ? strtotime($item->appointment_date . ' ' . $adjustedShiftEnd) : null;
    //         $adjustedShiftStartTime = $adjustedShiftStart ? strtotime($item->appointment_date . ' ' . $adjustedShiftStart) : null;

    //         foreach ($services as $service) {
    //             $serviceStart = strtotime($item->appointment_date . ' ' . $service->service_started_at);
    //             $serviceEnd   = strtotime($item->appointment_date . ' ' . $service->service_finished_at);

    //             // Early work (before shift start)
    //             if ($adjustedShiftStartTime && $serviceStart < $adjustedShiftStartTime) {
    //                 $overTimeSeconds += $adjustedShiftStartTime - $serviceStart;
    //             }

    //             // After-shift overtime
    //             if ($adjustedShiftEndTime && $serviceEnd > $adjustedShiftEndTime) {
    //                 $overtimeStart = max($serviceStart, $adjustedShiftEndTime);
    //                 $overTimeSeconds += $serviceEnd - $overtimeStart;
    //             }
    //         }

    //         $freeSeconds = 0;
    //         if ($checkIn && $checkOut && $services->count() > 0) {
    //             $checkInTime = strtotime($item->appointment_date . ' ' . $checkIn);
    //             $checkOutTime = strtotime($item->appointment_date . ' ' . $checkOut);

    //             $firstServiceStart = strtotime($item->appointment_date . ' ' . $services[0]->service_started_at);
    //             $firstGap = $firstServiceStart > $checkInTime ? $firstServiceStart - $checkInTime : 0;
    //             $freeSeconds += $firstGap;

    //             for ($i = 1; $i < $services->count(); $i++) {
    //                 $prevEnd = strtotime($item->appointment_date . ' ' . $services[$i - 1]->service_finished_at);
    //                 $currStart = strtotime($item->appointment_date . ' ' . $services[$i]->service_started_at);
    //                 $gap = $currStart > $prevEnd ? $currStart - $prevEnd : 0;
    //                 $freeSeconds += $gap;
    //             }

    //             $lastServiceEnd = strtotime($item->appointment_date . ' ' . $services->last()->service_finished_at);
    //             $lastGap = $checkOutTime > $lastServiceEnd ? $checkOutTime - $lastServiceEnd : 0;
    //             $freeSeconds += $lastGap;
    //         } elseif ($checkIn && $checkOut && $services->isEmpty()) {
    //             $checkInTime = strtotime($item->appointment_date . ' ' . $checkIn);
    //             $checkOutTime = strtotime($item->appointment_date . ' ' . $checkOut);
    //             $freeSeconds = $checkOutTime - $checkInTime;
    //         }

    //         $item->total_working_time = gmdate("H:i:s", strtotime($item->total_working_time) - strtotime('00:00:00'));
    //         $item->total_scheduled_time = gmdate("H:i:s", strtotime($item->total_scheduled_time) - strtotime('00:00:00'));
    //         $item->early_check_in_time = gmdate("H:i:s", floor($earlyCheckInSeconds));
    //         $item->check_in_delay = gmdate("H:i:s", floor($checkInDelaySeconds));
    //         $item->check_out_delay = gmdate("H:i:s", floor($checkOutDelaySeconds));
    //         $item->over_time = gmdate("H:i:s", floor($overTimeSeconds));
    //         $item->free_time = gmdate("H:i:s", floor($freeSeconds));
    //         $item->adjusted_shift_start = $adjustedShiftStart;
    //         $item->adjusted_shift_end = $adjustedShiftEnd;

    //         // Add service status
    //         $item->services = $services->map(function ($service) use ($adjustedShiftEnd, $item) {
    //             $serviceStart = strtotime($item->appointment_date . ' ' . $service->service_started_at);
    //             $serviceEnd = strtotime($item->appointment_date . ' ' . $service->service_finished_at);
    //             $adjustedShiftEndTime = $adjustedShiftEnd ? strtotime($item->appointment_date . ' ' . $adjustedShiftEnd) : null;

    //             $service->status = 'working';
    //             if ($adjustedShiftEndTime && $serviceEnd > $adjustedShiftEndTime) {
    //                 $service->status = 'overtime';
    //             }

    //             return $service;
    //         });

    //         return $item;
    //     });

    //     $response = [
    //         'pagination' => generatePaginationData($result),
    //         'data' => $dataList,
    //     ];

    //     return apiResponse(0, "success", $response);
    // }

    /**
     * Exclude refund bookings and negative/refund service lines from working-hours calcs.
     */
    private function applyNonRefundServiceFilters($query, string $bookingAlias = 'bk', string $serviceAlias = 'bs')
    {
        return $query
            ->whereNotIn("{$bookingAlias}.booking_type", [Booking::BOOKING_TYPE_REFUND, 'Refunded'])
            ->where(function ($q) use ($serviceAlias) {
                $q->whereNull("{$serviceAlias}.line_total")
                    ->orWhere("{$serviceAlias}.line_total", '>=', 0);
            });
    }

    /**
     * Format seconds as H:i:s without wrapping past 24 hours.
     */
    private function formatDurationSeconds($seconds)
    {
        $seconds = max(0, (int) $seconds);
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    /**
     * Parse "H:i:s" / "H:i:s.u" duration into seconds.
     */
    private function durationToSeconds($duration)
    {
        if ($duration === null || $duration === '' || $duration === '--:--') {
            return 0;
        }

        $parts = explode(':', (string) $duration);
        $hours = (int) ($parts[0] ?? 0);
        $minutes = (int) ($parts[1] ?? 0);
        $seconds = (int) floor((float) ($parts[2] ?? 0));

        return ($hours * 3600) + ($minutes * 60) + $seconds;
    }

    /**
     * Convert a date + time-of-day into a timestamp (timezone-safe for comparisons).
     */
    private function dayTimeTimestamp($date, $time)
    {
        if (empty($date) || empty($time) || $time === '--:--') {
            return null;
        }

        $dateOnly = substr((string) $date, 0, 10);
        $timeOnly = substr((string) $time, 0, 8);

        $timestamp = strtotime($dateOnly.' '.$timeOnly);

        return $timestamp === false ? null : $timestamp;
    }

    /**
     * Fetch non-refund completed services for a beautician/day.
     */
    private function fetchDayServices($beauticianId, $appointmentDate)
    {
        $servicesQuery = DB::table('booking_services as bs')
            ->join('bookings as bk', 'bs.booking_id', '=', 'bk.booking_id')
            ->join('services as srv', 'bs.service_id', '=', 'srv.service_id')
            ->select(
                'srv.name as service_name',
                DB::raw('TIME_FORMAT(bs.service_started_at, "%H:%i:%s") as service_started_at'),
                DB::raw('TIME_FORMAT(bs.service_finished_at, "%H:%i:%s") as service_finished_at'),
                DB::raw("TIME_FORMAT(TIMEDIFF(bs.service_finished_at, bs.service_started_at), '%H:%i:%s') as duration"),
                DB::raw("TIME_FORMAT(bs.service_started_at, '%H:%i:%s') as start_time"),
                DB::raw("TIME_FORMAT(bs.service_finished_at, '%H:%i:%s') as end_time")
            )
            ->where('bs.beautician_id', $beauticianId)
            ->whereDate('bs.appointment_date', $appointmentDate)
            ->whereNotNull('bs.service_started_at')
            ->whereNotNull('bs.service_finished_at')
            ->whereRaw('bs.service_finished_at >= bs.service_started_at')
            ->whereNull('bk.deleted_at');

        $this->applyNonRefundServiceFilters($servicesQuery);

        return $servicesQuery
            ->orderBy('bs.service_started_at', 'asc')
            ->get();
    }

    /**
     * Resolve shift times: prefer booking-service shift, else employee's user shift.
     */
    private function resolveShiftTimes($item)
    {
        $shiftStartTime = $item->start_time ?? null;
        $shiftEndTime = $item->end_time ?? null;

        if ($shiftStartTime && $shiftEndTime) {
            return [$shiftStartTime, $shiftEndTime];
        }

        $userShift = DB::table('users as u')
            ->leftJoin('shifts as s', 'u.shift_id', '=', 's.shift_id')
            ->where('u.id', $item->beautician_id)
            ->select('s.start_time', 's.end_time')
            ->first();

        return [
            $shiftStartTime ?: ($userShift->start_time ?? null),
            $shiftEndTime ?: ($userShift->end_time ?? null),
        ];
    }

    /**
     * Shared day metrics: overtime, free time, early/late check-in.
     * Always compares using appointment_date + time-of-day (never time-only strtotime).
     */
    private function calculateDayWorkingMetrics($item, $services, $checkIn, $checkOut)
    {
        [$shiftStartTime, $shiftEndTime] = $this->resolveShiftTimes($item);
        $appointmentDate = substr((string) $item->appointment_date, 0, 10);

        $adjustedShiftEnd = $shiftEndTime;
        $checkInDelaySeconds = 0;
        $earlyCheckInSeconds = 0;

        if ($shiftStartTime && $shiftEndTime && $checkIn) {
            $shiftStart = $this->dayTimeTimestamp($appointmentDate, $shiftStartTime);
            $shiftEnd = $this->dayTimeTimestamp($appointmentDate, $shiftEndTime);
            $actualCheckIn = $this->dayTimeTimestamp($appointmentDate, $checkIn);

            if ($shiftStart && $shiftEnd && $actualCheckIn) {
                if ($actualCheckIn > $shiftStart) {
                    // Late: extend OT boundary by how late they arrived
                    $checkInDelaySeconds = $actualCheckIn - $shiftStart;
                    $adjustedShiftEnd = date('H:i:s', $shiftEnd + $checkInDelaySeconds);
                    $earlyCheckInSeconds = 0;
                } elseif ($actualCheckIn < $shiftStart) {
                    // Early: keep scheduled shift end for OT (do not shrink window)
                    $earlyCheckInSeconds = $shiftStart - $actualCheckIn;
                    $checkInDelaySeconds = 0;
                }
            }
        }

        $checkOutDelaySeconds = 0;
        if ($checkOut && $adjustedShiftEnd) {
            $checkOutTs = $this->dayTimeTimestamp($appointmentDate, $checkOut);
            $adjustedEndTs = $this->dayTimeTimestamp($appointmentDate, $adjustedShiftEnd);
            if ($checkOutTs && $adjustedEndTs) {
                $checkOutDelaySeconds = max(0, $checkOutTs - $adjustedEndTs);
            }
        }

        $adjustedShiftEndTime = $adjustedShiftEnd
            ? $this->dayTimeTimestamp($appointmentDate, $adjustedShiftEnd)
            : ($shiftEndTime ? $this->dayTimeTimestamp($appointmentDate, $shiftEndTime) : null);

        $overTimeSeconds = 0;
        foreach ($services as $service) {
            $serviceStart = $this->dayTimeTimestamp($appointmentDate, $service->service_started_at);
            $serviceEnd = $this->dayTimeTimestamp($appointmentDate, $service->service_finished_at);

            if (! $serviceStart || ! $serviceEnd || ! $adjustedShiftEndTime) {
                continue;
            }

            if ($serviceEnd > $adjustedShiftEndTime) {
                $overtimeStart = max($serviceStart, $adjustedShiftEndTime);
                $overTimeSeconds += max(0, $serviceEnd - $overtimeStart);
            }
        }

        $freeSeconds = 0;
        if ($checkIn && $checkOut && $services->count() > 0) {
            $checkInTime = $this->dayTimeTimestamp($appointmentDate, $checkIn);
            $checkOutTime = $this->dayTimeTimestamp($appointmentDate, $checkOut);
            $firstServiceStart = $this->dayTimeTimestamp($appointmentDate, $services[0]->service_started_at);

            if ($checkInTime && $firstServiceStart && $firstServiceStart > $checkInTime) {
                $freeSeconds += $firstServiceStart - $checkInTime;
            }

            for ($i = 1; $i < $services->count(); $i++) {
                $prevEnd = $this->dayTimeTimestamp($appointmentDate, $services[$i - 1]->service_finished_at);
                $currStart = $this->dayTimeTimestamp($appointmentDate, $services[$i]->service_started_at);
                if ($prevEnd && $currStart && $currStart > $prevEnd) {
                    $freeSeconds += $currStart - $prevEnd;
                }
            }

            $lastServiceEnd = $this->dayTimeTimestamp($appointmentDate, $services->last()->service_finished_at);
            if ($checkOutTime && $lastServiceEnd && $checkOutTime > $lastServiceEnd) {
                $freeSeconds += $checkOutTime - $lastServiceEnd;
            }
        } elseif ($checkIn && $checkOut && $services->isEmpty()) {
            $checkInTime = $this->dayTimeTimestamp($appointmentDate, $checkIn);
            $checkOutTime = $this->dayTimeTimestamp($appointmentDate, $checkOut);
            if ($checkInTime && $checkOutTime && $checkOutTime > $checkInTime) {
                $freeSeconds = $checkOutTime - $checkInTime;
            }
        } elseif ($services->count() > 1) {
            // No attendance punches: free time = gaps between services only
            for ($i = 1; $i < $services->count(); $i++) {
                $prevEnd = $this->dayTimeTimestamp($appointmentDate, $services[$i - 1]->service_finished_at);
                $currStart = $this->dayTimeTimestamp($appointmentDate, $services[$i]->service_started_at);
                if ($prevEnd && $currStart && $currStart > $prevEnd) {
                    $freeSeconds += $currStart - $prevEnd;
                }
            }
        }

        $taggedServices = $services->map(function ($service) use ($appointmentDate, $adjustedShiftEndTime) {
            $serviceEnd = $this->dayTimeTimestamp($appointmentDate, $service->service_finished_at);
            $service->status = ($adjustedShiftEndTime && $serviceEnd && $serviceEnd > $adjustedShiftEndTime)
                ? 'overtime'
                : 'working';

            return $service;
        });

        return [
            'start_time' => $shiftStartTime,
            'end_time' => $shiftEndTime,
            'over_time_seconds' => $overTimeSeconds,
            'free_time_seconds' => $freeSeconds,
            'check_in_delay_seconds' => $checkInDelaySeconds,
            'check_out_delay_seconds' => $checkOutDelaySeconds,
            'early_check_in_seconds' => $earlyCheckInSeconds,
            'services' => $taggedServices,
        ];
    }

    public function workinHoursReportList($data = null, $platform = 'web')
    {
        $result = $this->buildWorkingHoursDailyBaseQuery($data)
            ->paginate(config('vocomi.settings.pagination.perpage.admin'));

        $dataList = $result->getCollection()->map(function ($item) {
            return $this->enrichWorkingHoursDailyRow($item);
        });

        $result->setCollection($dataList);

        return apiResponse(0, 'success', [
            'pagination' => generatePaginationData($result),
            'data' => $dataList->values(),
        ]);
    }

    public function workingHoursDailyRows($data = null)
    {
        return $this->buildWorkingHoursDailyBaseQuery($data)
            ->get()
            ->map(function ($item) {
                return $this->enrichWorkingHoursDailyRow($item);
            })
            ->values();
    }

    public function workingHoursSummaryReportList($data = null, $platform = 'web')
    {
        $report = $this->buildWorkingHoursSummaryReport($data);

        return apiResponse(0, 'success', $report);
    }

    public function buildWorkingHoursSummaryReport($data = null): array
    {
        $startDate = $this->normalizeReportDate($data->start_date ?? null);
        $endDate = $this->normalizeReportDate($data->end_date ?? null);
        $beauticianId = $data->beautician_id ?? null;

        $result = DB::table('booking_services as bs')
            ->join('bookings as bk', 'bs.booking_id', '=', 'bk.booking_id')
            ->join('beauticians as b', 'bs.beautician_id', '=', 'b.beautician_id')
            ->join('users as u', 'b.beautician_id', '=', 'u.id')
            ->select(
                'bs.beautician_id',
                'u.name as employee_name',
                'bs.appointment_date',
                DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(bs.service_finished_at, bs.service_started_at)))) as total_working_time')
            )
            ->whereNotNull('bs.service_started_at')
            ->whereNotNull('bs.service_finished_at')
            ->whereRaw('bs.service_finished_at >= bs.service_started_at')
            ->whereNull('bk.deleted_at');

        $this->applyNonRefundServiceFilters($result);

        if ($startDate && $endDate) {
            $result->whereBetween('bs.appointment_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $result->where('bs.appointment_date', '>=', $startDate);
        } elseif ($endDate) {
            $result->where('bs.appointment_date', '<=', $endDate);
        }

        if (! empty($beauticianId)) {
            $result->where('bs.beautician_id', $beauticianId);
        }

        $result = $result
            ->groupBy('bs.beautician_id', 'u.name', 'bs.appointment_date')
            ->orderBy('bs.appointment_date', 'asc')
            ->get();

        $dataList = $result->map(function ($item) {
            $employee = Employee::where('user_id', $item->beautician_id)->first();

            $attendance = null;
            if ($employee) {
                $attendance = DB::table('employee_attendences')
                    ->where('employee_id', $employee->emp_id)
                    ->whereDate('date', $item->appointment_date)
                    ->select(
                        DB::raw('TIME_FORMAT(MIN(check_in), "%H:%i:%s") as check_in'),
                        DB::raw('TIME_FORMAT(MAX(check_out), "%H:%i:%s") as check_out')
                    )
                    ->first();
            }

            $checkIn = $attendance->check_in ?? null;
            $checkOut = $attendance->check_out ?? null;
            $services = $this->fetchDayServices($item->beautician_id, $item->appointment_date);
            $metrics = $this->calculateDayWorkingMetrics($item, $services, $checkIn, $checkOut);

            $workingSeconds = $services->sum(function ($service) {
                return $this->durationToSeconds($service->duration ?? null);
            });

            $item->total_working_time = $this->formatDurationSeconds($workingSeconds);
            $item->over_time = $this->formatDurationSeconds($metrics['over_time_seconds']);
            $item->free_time = $this->formatDurationSeconds($metrics['free_time_seconds']);
            $item->total_services = $metrics['services']->count();

            return $item;
        });

        $summary = $dataList->groupBy('beautician_id')->map(function ($group, $beauticianId) {
            $firstItem = $group->first();

            $totalWorkingSeconds = 0;
            $totalFreeSeconds = 0;
            $totalOverSeconds = 0;
            $totalServices = 0;
            $totalDays = $group->count();

            foreach ($group as $item) {
                $totalWorkingSeconds += $this->durationToSeconds($item->total_working_time);
                $totalFreeSeconds += $this->durationToSeconds($item->free_time);
                $totalOverSeconds += $this->durationToSeconds($item->over_time);
                $totalServices += $item->total_services;
            }

            return (object) [
                'beautician_id' => $beauticianId,
                'employee_name' => $firstItem->employee_name,
                'total_days' => $totalDays,
                'total_working_hours' => $this->formatDurationSeconds($totalWorkingSeconds),
                'total_free_time' => $this->formatDurationSeconds($totalFreeSeconds),
                'total_over_time' => $this->formatDurationSeconds($totalOverSeconds),
                'total_services' => $totalServices,
            ];
        })->values();

        return [
            'data' => $summary,
            'totals' => [
                'employees' => $summary->count(),
                'total_working_hours' => $this->formatDurationSeconds($summary->sum(function ($row) {
                    return $this->durationToSeconds($row->total_working_hours);
                })),
                'total_free_time' => $this->formatDurationSeconds($summary->sum(function ($row) {
                    return $this->durationToSeconds($row->total_free_time);
                })),
                'total_over_time' => $this->formatDurationSeconds($summary->sum(function ($row) {
                    return $this->durationToSeconds($row->total_over_time);
                })),
                'total_services' => $summary->sum('total_services'),
            ],
        ];
    }

    private function buildWorkingHoursDailyBaseQuery($data)
    {
        $startDate = $this->normalizeReportDate($data->start_date ?? null);
        $endDate = $this->normalizeReportDate($data->end_date ?? null);
        $beauticianId = $data->beautician_id ?? null;

        $result = DB::table('booking_services as bs')
            ->join('bookings as bk', 'bs.booking_id', '=', 'bk.booking_id')
            ->join('beauticians as b', 'bs.beautician_id', '=', 'b.beautician_id')
            ->join('users as u', 'b.beautician_id', '=', 'u.id')
            ->select(
                'bs.beautician_id',
                'u.name as employee_name',
                'bs.appointment_date',
                DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(bs.service_finished_at, bs.service_started_at)))) as total_working_time'),
                DB::raw('SEC_TO_TIME(SUM(COALESCE(bs.duration_minutes, 0) * 60)) as total_scheduled_time')
            )
            ->whereNotNull('bs.service_started_at')
            ->whereNotNull('bs.service_finished_at')
            ->whereRaw('bs.service_finished_at >= bs.service_started_at')
            ->whereNull('bk.deleted_at');

        $this->applyNonRefundServiceFilters($result);

        if ($startDate && $endDate) {
            $result->whereBetween('bs.appointment_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $result->where('bs.appointment_date', '>=', $startDate);
        } elseif ($endDate) {
            $result->where('bs.appointment_date', '<=', $endDate);
        }

        if (! empty($beauticianId)) {
            $result->where('bs.beautician_id', $beauticianId);
        }

        return $result
            ->groupBy('bs.beautician_id', 'u.name', 'bs.appointment_date')
            ->orderBy('bs.appointment_date', 'asc');
    }

    private function enrichWorkingHoursDailyRow($item)
    {
        $employee = Employee::where('user_id', $item->beautician_id)->first();

        $attendance = null;
        if ($employee) {
            $attendance = DB::table('employee_attendences')
                ->where('employee_id', $employee->emp_id)
                ->whereDate('date', $item->appointment_date)
                ->select(
                    DB::raw('TIME_FORMAT(MIN(check_in), "%H:%i:%s") as check_in'),
                    DB::raw('TIME_FORMAT(MAX(check_out), "%H:%i:%s") as check_out')
                )
                ->first();
        }

        $checkIn = $attendance->check_in ?? null;
        $checkOut = $attendance->check_out ?? null;
        $services = $this->fetchDayServices($item->beautician_id, $item->appointment_date);
        $metrics = $this->calculateDayWorkingMetrics($item, $services, $checkIn, $checkOut);

        $workingSeconds = $services->sum(function ($service) {
            return $this->durationToSeconds($service->duration ?? null);
        });

        $item->start_time = $metrics['start_time'];
        $item->end_time = $metrics['end_time'];
        $item->total_working_time = $this->formatDurationSeconds($workingSeconds);
        $item->total_scheduled_time = $this->formatDurationSeconds($this->durationToSeconds($item->total_scheduled_time));
        $item->over_time = $this->formatDurationSeconds($metrics['over_time_seconds']);
        $item->free_time = $this->formatDurationSeconds($metrics['free_time_seconds']);
        $item->check_in = $checkIn;
        $item->check_out = $checkOut;
        $item->check_in_delay = $this->formatDurationSeconds($metrics['check_in_delay_seconds']);
        $item->check_out_delay = $this->formatDurationSeconds($metrics['check_out_delay_seconds']);
        $item->early_check_in_time = $this->formatDurationSeconds($metrics['early_check_in_seconds']);
        $item->service_count = $metrics['services']->count();
        $item->services = $metrics['services'];

        return $item;
    }

    private function normalizeReportDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return substr((string) $value, 0, 10);
    }

    /**
     * commissions.start_date is stored as a plain Y-m-d string, not datetime.
     * Appending time causes MySQL string comparisons to exclude same-day rows.
     */
    private function applyStringDateRangeFilter($query, string $column, ?string $startDate, ?string $endDate): void
    {
        if ($startDate && $endDate) {
            $query->whereBetween($column, [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where($column, '>=', $startDate);
        } elseif ($endDate) {
            $query->where($column, '<=', $endDate);
        }
    }

    private function applyPayrollPaymentReportFilters($query, $data): void
    {
        $startDate = $this->normalizeReportDate($data->start_date ?? null);
        $endDate = $this->normalizeReportDate($data->end_date ?? null);

        $this->applyStringDateRangeFilter($query, 'payment_date', $startDate, $endDate);

        if (! empty($data->emp_id)) {
            $query->whereHas('payroll', function ($subQuery) use ($data) {
                $subQuery->where('emp_id', $data->emp_id);
            });
        } elseif (! empty($data->employee_name)) {
            $query->whereHas('payroll.employee', function ($subQuery) use ($data) {
                $subQuery->where('name', 'LIKE', "%{$data->employee_name}%");
            });
        }

        if (! empty($data->balance_amount) && (string) $data->balance_amount === '1') {
            $query->where('balance_amount', '>', 0);
        }
    }

    private function buildPayrollReportQuery($data)
    {
        $query = Payroll::with('employee')
            ->orderByDesc('year')
            ->orderByRaw("STR_TO_DATE(CONCAT(payrolls.month, ' 1 ', payrolls.year), '%M %d %Y') DESC")
            ->orderByDesc('payroll_id');

        $this->applyPayrollReportFilters($query, $data);

        return $query;
    }

    private function payrollPeriodExpression(): string
    {
        return "STR_TO_DATE(CONCAT(payrolls.month, ' 1 ', payrolls.year), '%M %d %Y')";
    }

    private function applyPayrollReportFilters($query, $data): void
    {
        $startDate = $this->normalizeReportDate($data->start_date ?? null);
        $endDate = $this->normalizeReportDate($data->end_date ?? null);
        $periodExpr = $this->payrollPeriodExpression();

        if ($startDate) {
            $query->whereRaw("{$periodExpr} >= ?", [substr($startDate, 0, 7).'-01']);
        }

        if ($endDate) {
            $query->whereRaw("{$periodExpr} <= ?", [substr($endDate, 0, 7).'-01']);
        }

        if (! empty($data->emp_id)) {
            $query->where('payrolls.emp_id', $data->emp_id);
        } elseif (! empty($data->employee_name)) {
            $query->whereHas('employee', function ($subQuery) use ($data) {
                $subQuery->where('name', 'LIKE', "%{$data->employee_name}%");
            });
        }

        if (! empty($data->payment_mode)) {
            $query->where('payrolls.payment_mode', 'LIKE', "%{$data->payment_mode}%");
        }
    }

    private function buildIncentiveReportQuery($data)
    {
        $query = Incentive::with('employee')
            ->where('incentive_amount', '!=', '0.00')
            ->orderByDesc('start_date')
            ->orderByDesc('incentive_id');

        $startDate = $this->normalizeReportDate($data->start_date ?? null);
        $endDate = $this->normalizeReportDate($data->end_date ?? null);

        $this->applyStringDateRangeFilter($query, 'start_date', $startDate, $endDate);

        if (! empty($data->emp_id)) {
            $query->where('emp_id', $data->emp_id);
        } elseif (! empty($data->employee_name)) {
            $query->whereHas('employee', function ($subQuery) use ($data) {
                $subQuery->where('name', 'LIKE', "%{$data->employee_name}%");
            });
        }

        return $query;
    }
}
