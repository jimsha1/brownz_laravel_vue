<?php

namespace App\Exports;

use App\Models\EmployeeAttendence;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private $employee_name = '',
        private $start_date = '',
        private $end_date = '',
        private $attendanceType = '',
        private $emp_id = null,
    ) {
    }

    public function collection()
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

        $startDate = $this->normalizeReportDate($this->start_date);
        $endDate = $this->normalizeReportDate($this->end_date);

        if ($startDate && $endDate) {
            $query->whereBetween('employee_attendences.date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('employee_attendences.date', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('employee_attendences.date', '<=', $endDate);
        }

        if (! empty($this->emp_id)) {
            $query->where('employee_attendences.employee_id', $this->emp_id);
        } elseif (! empty($this->employee_name)) {
            $name = trim($this->employee_name);
            $query->where(function ($q) use ($name) {
                $q->where('employee_attendences.employee_name', 'LIKE', "%{$name}%")
                    ->orWhere('employees.name', 'LIKE', "%{$name}%");
            });
        }

        if (! empty($this->attendanceType)) {
            switch ($this->attendanceType) {
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

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Date',
            'Check-in Time',
            'Check-out Time',
            'Shift Start',
            'Shift End',
            'Attendance Status',
            'Late',
            'Over Time',
        ];
    }

    public function map($attendance): array
    {
        $checkIn = $attendance->check_in;
        $checkOut = $attendance->check_out;
        $shiftStart = $attendance->shift_start_time ?? null;
        $shiftEnd = $attendance->shift_end_time ?? null;

        $isLate = $checkIn && $shiftStart
            ? strtotime(substr((string) $checkIn, 0, 8)) > strtotime(substr((string) $shiftStart, 0, 8))
            : false;
        $isOvertime = $checkOut && $shiftEnd
            ? strtotime(substr((string) $checkOut, 0, 8)) > strtotime(substr((string) $shiftEnd, 0, 8))
            : false;

        return [
            $attendance->employee_name ?: ($attendance->employee?->name ?? '—'),
            $attendance->date ? Carbon::parse($attendance->date)->format('d-m-Y') : '—',
            $checkIn ? Carbon::parse($checkIn)->format('h:i A') : '—:—',
            $checkOut ? Carbon::parse($checkOut)->format('h:i A') : '—:—',
            $shiftStart ? Carbon::parse($shiftStart)->format('h:i A') : '—',
            $shiftEnd ? Carbon::parse($shiftEnd)->format('h:i A') : '—',
            $attendance->attendance_status ? strtoupper($attendance->attendance_status) : '—',
            $isLate ? 'Yes' : 'No',
            $isOvertime ? 'Yes' : 'No',
        ];
    }

    private function normalizeReportDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return substr((string) $value, 0, 10);
    }
}
