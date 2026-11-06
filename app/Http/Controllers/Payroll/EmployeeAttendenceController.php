<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Service\EmployeeAttendenceService;
use Exception;
use Illuminate\Http\Request;

class EmployeeAttendenceController extends Controller
{
    private EmployeeAttendenceService $employeeAttendenceService;

    public function __construct(EmployeeAttendenceService $employeeAttendenceService){
        $this->employeeAttendenceService = $employeeAttendenceService;
    }
    public function updateEmployeePunchTimeApi(Request $request){
        try {
         
            return $this->employeeAttendenceService->updateEmployeePunchTime($request);

        }catch(Exception $e){
            return apiResponse(100,$e->getMessage(),null);
        }
    }

    public function attendance()
    {
        return view('payroll.attendance');
    }

    public function attendanceList(Request $request)
    {
        return $this->employeeAttendenceService->attendanceList($request);
    }

    public function attendanceStore(Request $request)
    {

        if (empty($request->attendence_id)) {
            $this->validate($request, [
                // 'role_ids' => 'required',
                // 'mobile' => 'required|unique:users,mobile',
                // 'username' => 'required|unique:users,username',
            ]);
        } else {
            $this->validate($request, [
                // 'role_ids' => 'required',
                // 'mobile' => 'required|unique:users,mobile,' . $request->id . ',id',
            ]);
        }
        return $this->employeeAttendenceService->attendance_store($request);
    }
}
