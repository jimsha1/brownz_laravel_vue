<?php

namespace App\Http\Controllers\Admin\LeaveManagement;

use App\Http\Controllers\Controller;
use App\Models\Beautician;
use App\Models\Employee;
use App\Models\EmployeeAttendence;
use App\Models\LeaveManagement;
use Illuminate\Http\Request;
use Validator;

class LeaveManagementController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        if (empty($startDate) && empty($endDate)) {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        }

        $status = $request->input('leave_status', 'all');
        $employeeId = $request->input('employee_id');

        $db_leaves = LeaveManagement::query()
            ->join('employees', 'employees.emp_id', '=', 'leave_managements.emp_id')
            ->join('users', 'users.id', '=', 'employees.user_id')
            ->leftJoin('beauticians', 'beauticians.beautician_id', '=', 'users.id')
            ->select(
                'leave_managements.leave_id',
                'leave_managements.emp_id',
                'leave_managements.beautician_id',
                'leave_managements.leave_date',
                'leave_managements.leave_type',
                'leave_managements.leave_mode',
                'leave_managements.leave_status',
                'leave_managements.reason',
                'leave_managements.created_at',
                'employees.emp_code',
                'employees.name as employee_name',
                'users.name',
                'beauticians.beautician_code'
            )
            ->orderBy('leave_managements.leave_date', 'desc')
            ->orderBy('leave_managements.created_at', 'desc')
            ->when($request->search_q, function ($query) use ($request) {
                $term = '%'.$request->search_q.'%';
                $query->where(function ($q) use ($term) {
                    $q->where('users.name', 'like', $term)
                        ->orWhere('employees.name', 'like', $term)
                        ->orWhere('employees.emp_code', 'like', $term)
                        ->orWhere('beauticians.beautician_code', 'like', $term)
                        ->orWhere('leave_managements.reason', 'like', $term);
                });
            })
            ->when(! empty($employeeId), function ($query) use ($employeeId) {
                $query->where('leave_managements.emp_id', $employeeId);
            })
            ->when($status && $status !== 'all', function ($query) use ($status) {
                $query->where('leave_managements.leave_status', $status);
            })
            ->when(! empty($startDate), function ($query) use ($startDate) {
                $query->whereDate('leave_managements.leave_date', '>=', $startDate);
            })
            ->when(! empty($endDate), function ($query) use ($endDate) {
                $query->whereDate('leave_managements.leave_date', '<=', $endDate);
            })
            ->paginate(config('vocomi.settings.pagination.perpage.admin'))
            ->appends(array_filter([
                'search_q' => $request->search_q,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'leave_status' => $status,
                'employee_id' => $employeeId,
            ], function ($value) {
                return $value !== null && $value !== '';
            }));

        $beauticians_dropdown = \Facades\App\Models\Beautician::beauticiansDropdown();
        $employees_dropdown = \Facades\App\Models\Employee::employeesDropdown();

        return view('admin.leave-management.leave-management', compact(
            'db_leaves',
            'beauticians_dropdown',
            'employees_dropdown',
            'startDate',
            'endDate',
            'status',
            'employeeId'
        ));
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee' => 'required',
            'leave_dates' => 'required|string',
            'leave_type' => 'required',
            'leave_mode' => 'required',
            'reason' => 'required|string|max:255',
        ]);

        $user = Employee::where('emp_id', $request->employee)->select('user_id')->first();
        $beautician = $user
            ? Beautician::where('beautician_id', $user->user_id)->first()
            : null;
        $beauticianId = $beautician ? $beautician->beautician_id : null;

        $validator->after(function ($validator) use ($request) {
            $dates = array_filter(array_map('trim', explode(',', $request->leave_dates)));

            foreach ($dates as $date) {
                $conflict = LeaveManagement::where('emp_id', $request->employee)
                    ->whereDate('leave_date', $date)
                    ->whereIn('leave_status', ['approved', 'pending'])
                    ->exists();

                if ($conflict) {
                    $validator->errors()->add('leave_dates', "Conflict on date: $date");
                }
            }
        });

        $validator->validate();

        $dates = array_filter(array_map('trim', explode(',', $request->leave_dates)));

        \DB::transaction(function () use ($dates, $request, $beauticianId) {
            foreach ($dates as $date) {
                $leaveData = [
                    'leave_date' => $date,
                    'leave_mode' => $request->leave_mode,
                    'leave_type' => $request->leave_type,
                    'beautician_id' => $beauticianId,
                    'emp_id' => $request->employee,
                    'reason' => $request->reason,
                ];

                if ($request->leave_mode === 'week_off') {
                    $leaveData['leave_status'] = 'approved';
                }

                LeaveManagement::create($leaveData);
            }
        });

        voco_flash()->success(__('Leave Records saved!'));

        return response()->json([
            'gotourl' => route('admin.leave.management.list'),
        ]);
    }

    public function delete(Request $request, $id)
    {
        $leave_model = LeaveManagement::find($id);
        \DB::transaction(function () use ($leave_model) {
            return $leave_model->delete();
        });

        voco_flash()->danger(__('Leave Record deleted!'));

        return response()->json([
            'gotourl' => route('admin.leave.management.list'),
        ]);
    }

    private function applyValidation(Request $request)
    {
        $request->validate([
            'beautician' => 'required',
            'leave_date' => 'required',
            'leave_type' => 'required',
            'leave_mode' => 'required',
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'leave_status' => 'required|in:approved,rejected,pending',
            'reason' => 'nullable|string|max:255',
            'emp_id' => 'nullable',
        ]);

        $leave = LeaveManagement::findOrFail($id);
        $leave->leave_status = $request->leave_status;
        if ($request->filled('reason')) {
            $leave->reason = $request->reason;
        }
        $leave->save();

        if ($request->leave_status === 'approved') {
            $empId = $request->emp_id ?: $leave->emp_id;
            $employee = Employee::where('emp_id', $empId)->first();
            if ($employee) {
                $exists = EmployeeAttendence::where('employee_id', $empId)
                    ->whereDate('date', $leave->leave_date)
                    ->exists();

                if (! $exists) {
                    EmployeeAttendence::create([
                        'employee_id' => $empId,
                        'employee_code' => $employee->emp_code,
                        'employee_name' => $employee->name,
                        'date' => $leave->leave_date,
                        'attendance_status' => 'absent',
                        'user_id' => $leave->beautician_id,
                    ]);
                }
            }
        }

        voco_flash()->success(__('Leave status updated successfully!'));

        return response()->json([
            'gotourl' => route('admin.leave.management.list'),
        ]);
    }
}
