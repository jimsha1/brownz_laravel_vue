<?php

namespace App\Http\Controllers\AdminAppAndroid;

use App\Http\Controllers\Controller;
use App\Service\AdminAppAndroidService;
use Exception;
use Illuminate\Http\Request;

class AdminAppAndroidController extends Controller
{
    private AdminAppAndroidService $adminAppAndroidService;

    public function __construct(AdminAppAndroidService $adminAppAndroidService){
        $this->adminAppAndroidService = $adminAppAndroidService;
    }

    public function getSalesAnalyticsApi(Request $request){
        return $this->adminAppAndroidService->getSalesAnalytics('mobile',$request->year);
    }
    public function getEmployeeAttendanceTimeApi(Request $request){
        try {
            $validatedData = $this->validate($request, [
                'emp_id' => 'required',
            ]);
            return $this->adminAppAndroidService->getEmployeeAttendanceTime($validatedData);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(),100);
        }
    }
    public function employeeListAndroidApi(Request $request){
        return $this->adminAppAndroidService->employeeListAndroid($request);
    }
}
