<?php

use App\Http\Controllers\AdminAppAndroid\AdminAppAndroidController;
use App\Http\Controllers\EmployeeApp\EmployeeAppApiController;
use App\Http\Controllers\Firebase\FirebaseNotificationController;
use App\Http\Controllers\Payroll\EmployeeAttendenceController;
use App\Http\Controllers\Payroll\EmployeeController;
use App\Http\Controllers\Payroll\PayrollReportController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(ReportController::class)->group(function () {

    Route::post('report/sales-vue-list-admin-app', 'sales_vue_list_api_admin_app');
    Route::post('report/sales-vue-list', 'sales_vue_list_api')->name('sales.vue-list');
    Route::post('report/daily-sales-vue-list', 'daily_sales_vue_list_api')->name('daily-sales.vue-list');
    Route::post('report/client-vue-list', 'client_vue_list_api')->name('client.vue-list');
    Route::post('report/vendor-vue-list', 'vendor_vue_list_api')->name('vendor.vue-list');
    Route::post('report/edit-log-vue-list', 'edit_log_vue_list_api')->name('edit-log.vue-list');
    Route::post('report/tax-vue-list', 'tax_vue_list_api')->name('tax.vue-list');
    Route::post('report/service-vue-list', 'service_vue_list_api')->name('service.vue-list');
    Route::post('report/service-used-product-vue-list', 'used_product_vue_list_api')->name('service-used-product.vue-list');
    Route::post('report/beautician-vue-list', 'beautician_vue_list_api')->name('beautician.vue-list');
    Route::post('report/client_vue_list_api_for_wiring', 'client_vue_list_api_for_wiring');
    Route::post('report/get_bargraph_data_for_wiring', 'getBarGraphDataApi');
    Route::post('report/whatsapp-invoice-vue-list', 'whatsapp_invoice_vue_list_api')->name('whatsapp-invoice.vue-list');


    Route::post('sales/summary', 'getSaleSummary')->name('sales.summary');
    Route::post('service/summary', 'getServiceSummary')->name('service.summary');
    Route::post('beautician/summary', 'getBeauticianSummary')->name('beautician.summary');
});
Route::controller(EmployeeAppApiController::class)->group(function () {
    Route::post('app-login', 'appLogin');
    Route::post('forgot-password', 'sendOTP');
    Route::post('verify-otp', 'verifyOTP');
    Route::post('change-password', 'changePassword');
});
Route::middleware('employee_app_auth')->group(function () {
    Route::controller(EmployeeAppApiController::class)->group(function () {
        Route::post('get-bookings', 'getBookingsApi');
        Route::post('get-booked-services', 'getBookedServiceItemsApi');
        Route::post('get-services', 'getAllServicesApi');
        Route::post('start-service', 'startServiceApi');
        Route::post('stop-service', 'stopServiceApi');
        Route::post('add-service-to-booking', 'addServicesToBookingApi');
        Route::post('get-profile', 'getProfile');
        Route::post('update-profile', 'updateEmployeeProfileApi');
        Route::post('upload-profile-photo', 'updateEmployeeProfilePhotoApi');
        Route::post('get-booked-services-for-calender', 'getBookedServiceForCalendarApi');
        Route::post('delete-service-from-booking', 'deleteBookingServiceFromBookingApi');
        Route::post('request-leave', 'employeeLeaveRequestApi');
        Route::post('get-request-leaves', 'getEmployeeLeaveRequestsApi');
        Route::post('delete-leave-request', 'deleteLeaveRequestApi');
    });
    Route::controller(EmployeeAttendenceController::class)->group(function () {
        Route::post('update-employee-punch-time', 'updateEmployeePunchTimeApi');
    });
});


Route::controller(EmployeeController::class)->group(function () {
    Route::post('role/list', 'roleList')->name('role.list');
    Route::post('shift/list', 'shiftList')->name('shift.list');
    Route::post('employee/store', 'employeeStore')->name('employee.store');
    Route::post('employee/list', 'employeeList')->name('employee.list');
    Route::post('employee-list-for-app', 'employeeListAndroid')->name('employee.list.app');
    Route::post('salary/store', 'salaryStore')->name('salary.store');
    Route::post('salary/list', 'salaryList')->name('salary.list');
    Route::post('get-incentive-value', 'incentive_value')->name('get-incentive-value');
    Route::post('incentive-terms/store', 'incentiveTermStore')->name('incentive-terms.store');
    Route::post('incentive-terms/list', 'incentiveTermList')->name('incentive-terms.list');
    Route::post('incentive/store', 'incentiveStore')->name('incentive.store');
    Route::post('incentive/list', 'incentiveList')->name('incentive.list');
    Route::post('new-incentive/list', 'newIncentiveList')->name('new-incentive.list');


    Route::get('/incentives/{id}', 'getIncentiveByMasterId');

    Route::post('/save-incentive-data', 'saveIncentiveData')->name('saveIncentiveData');

    // Route::post('payroll/store', 'payrollStore')->name('payroll.store');
    Route::post('payroll/list', 'payrollList')->name('payroll.list');
    Route::post('payroll-masters/list', 'payrollMastersList')->name('payroll-masters.list');
    Route::get('/payrolls/{id}', 'getPayrollByMasterId');
    Route::post('get-commission-value', 'commission_value')->name('get-commission-value');
    Route::post('commission/store', 'commissionStore')->name('commission.store');
    // Route::post('commission/list', 'commissionList')->name('commission.list');







    Route::post('generate-commission', 'generateCommission')->name('generate-commission');
    Route::post('/save-commission-data', 'saveCommissionData')->name('save-commission-data');
    Route::post('commission/pay', 'payCommission')->name('commission.pay');
    Route::post('incentive/pay', 'payIncentive')->name('incentive.pay');
    Route::post('commission/list', 'commissionsList')->name('commission.list');
    Route::get('/commissions/{id}', 'commissionByMasterId');
    Route::post('/view-commission-data', 'viewCommission')->name('view-commission-data');

    Route::post('salary-advance/store', 'advanceSalaryStore')->name('salary-advance.store');
    Route::post('salary-advance/list', 'advanceSalaryList')->name('salary-advance.list');
    Route::post('salary/get-by-employee', 'getSalaryByEmployeeId')->name('salary.get-by-employee');

    Route::post('payroll/store', 'salaryPayrollStore')->name('payroll.store');
    Route::post('payroll/update', 'salaryPayrollUpdate')->name('payroll.update');
    Route::post('payroll/list', 'salaryPayrollList')->name('payroll.list');
    Route::post('payroll-salary-schedule/update', 'salaryPayrollScheduleUpdate')->name('payroll-salary-schedule.update');
    Route::post('salary-scheduled/update', 'salarySchedule')->name('salary-scheduled.update');
    Route::post('payroll-payment/list', 'payrollPaymentList')->name('payroll-payment.list');
    Route::post('/view-payroll-data', 'viewPayroll')->name('view-payroll-data');
});

Route::controller(EmployeeAttendenceController::class)->group(function () {
    // Route::post('update-employee-punch-time','updateEmployeePunchTimeApi');
    Route::post('attendance/list', 'attendanceList')->name('attendance.list');
    Route::post('attendance/store', 'attendanceStore')->name('attendance.store');
});
Route::controller(PayrollReportController::class)->group(function () {
    Route::post('report/salary-vue-list', 'salary_vue_list')->name('salary.vue-list');
});
Route::controller(FirebaseNotificationController::class)->group(function () {
    Route::post('send-notification', 'send_notification_api');
});
Route::controller(PayrollReportController::class)->group(function () {
    Route::post('report/salary-vue-list', 'salary_vue_list')->name('salary.vue-list');
    Route::post('report/commission-vue-list', 'commission_vue_list')->name('commission.vue-list');
    Route::post('report/attendance_vue_list', 'attendance_vue_list')->name('attendance_vue_list');
    Route::post('report/payroll_vue_list', 'payroll_vue_list')->name('payroll_vue_list');
    Route::post('report/incentive_vue_list', 'incentive_vue_list')->name('incentive_vue_list');
});
Route::controller(AdminAppAndroidController::class)->group(function () {
    Route::post('get-sales-analytics', 'getSalesAnalyticsApi');
    Route::post('get-employe-attendance', 'getEmployeeAttendanceTimeApi');
    Route::post('get-employees', 'employeeListAndroidApi');
});
Route::controller(PayrollReportController::class)->group(function () {
    Route::post('report/working-hours-vue-list', 'working_hours_vue_list')->name('workin_hours.vue-list');
    Route::post('report/working-hours-summary-vue-list', 'working_hours_summary_vue_list')->name('workin_hours_summary.vue-list');
});
