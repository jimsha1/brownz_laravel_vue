<?php

use App\Http\Controllers\Payroll\EmployeeAttendenceController;
use App\Http\Controllers\Payroll\EmployeeController;
use App\Http\Controllers\Payroll\PayrollReportController;
use App\Models\EmployeeAttendence;

Route::get('/incentive', [EmployeeController::class, 'incentive_index'])->name('incentive');
Route::get('/incentive-terms', [EmployeeController::class, 'incentiveTerms'])->name('incentive-terms');
Route::get('/commission', [EmployeeController::class, 'commission_index'])->name('commission');
Route::get('/payroll', [EmployeeController::class, 'payroll_index'])->name('payroll');
Route::get('/employee', [EmployeeController::class, 'employee'])->name('employee');
Route::get('download/{id}', [EmployeeController::class, 'payrollDownload']);
Route::get('/attendance', [EmployeeAttendenceController::class, 'attendance'])->name('attendance');
Route::get('/advance-salary', [EmployeeController::class, 'advance_salary'])->name('advance-salary');

Route::post('/payroll/auth-check', [EmployeeController::class, 'payrollAuthCheck'])
    ->name('payroll.auth-check');
Route::post('/payroll/generate', [EmployeeController::class, 'generatePayroll'])
    ->name('payroll.generate');
Route::post('/payroll/save', [EmployeeController::class, 'savePayrollData'])
    ->name('payroll.save');
Route::post('/payroll/update', [EmployeeController::class, 'updatePayroll'])
    ->name('payroll.update');
Route::post('/payroll/payment', [EmployeeController::class, 'payPayroll'])
    ->name('payroll.payment');

Route::post('/commission/auth-check', [EmployeeController::class, 'commissionAuthCheck'])
    ->name('commission.auth-check');
Route::post('/commission/generate', [EmployeeController::class, 'generateCommission'])
    ->name('commission.generate');
Route::post('/commission/save', [EmployeeController::class, 'saveCommissionData'])
    ->name('commission.save');
Route::post('/commission/pay', [EmployeeController::class, 'payCommission'])
    ->name('commission.pay.web');

Route::post('/incentive/auth-check', [EmployeeController::class, 'incentiveAuthCheck'])
    ->name('incentive.auth-check');
Route::post('/incentive/generate', [EmployeeController::class, 'incentiveStore'])
    ->name('incentive.generate');
Route::post('/incentive/save', [EmployeeController::class, 'saveIncentiveData'])
    ->name('incentive.save');
Route::post('/incentive/pay', [EmployeeController::class, 'payIncentive'])
    ->name('incentive.pay.web');


// Route::get('/salary-report', [EmployeeController::class, 'incentive_index'])->name('incentive');



Route::controller(PayrollReportController::class)->group(function () {
    Route::get('report/salary', 'salary_index')->name('salary.report');
});
