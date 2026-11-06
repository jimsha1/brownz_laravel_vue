<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\CommissionMaster;
use App\Models\Incentive;
use App\Models\IncentiveMaster;
use App\Models\Payroll;
use App\Models\PayrollPayments;
use App\Models\BusinessConfig;
use App\Service\CommissionPaymentService;
use App\Service\EmployeeService;
use App\Service\IncentivePaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    private EmployeeService $employeeService;

    public function __construct(EmployeeService $EmployeeService)
    {
        $this->employeeService = $EmployeeService;
    }
    public function employee()
    {
        return view('payroll.employee');
    }

    public function incentiveTerms()
    {
        return view('payroll.incentive_terms');
    }
    public function incentive_index()
    {
        return view('payroll.incentive');
    }

    public function commission_index()
    {
        return view('payroll.commission');
    }
    public function payroll_index()
    {
        return view('payroll.payroll');
    }
    public function roleList(Request $request)
    {
        return $this->employeeService->getroleList($request);
    }
    public function shiftList(Request $request)
    {
        return $this->employeeService->getshiftList($request);
    }
    public function employeeStore(Request $request)
    {
        // return $request;

        // Empty lat/long must not arrive as the string "null" from FormData
        foreach (['branch_latitude', 'branch_longitude'] as $coord) {
            $value = $request->input($coord);
            if ($value === null || $value === '' || $value === 'null' || $value === 'undefined') {
                $request->merge([$coord => null]);
            }
        }

        if (! empty($request->mobile)) {
            $digits = preg_replace('/\D+/', '', (string) $request->mobile);
            if (strlen($digits) > 10 && str_starts_with($digits, '91')) {
                $digits = substr($digits, -10);
            }
            $request->merge(['mobile' => substr($digits, 0, 10)]);
        }

        if (empty($request->emp_id)) {
            $this->validate($request, [
                'role_ids' => 'required',
                'name' => 'required',
                'mobile' => 'required|digits:10|unique:users,mobile',
                'username' => 'required|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'branch_latitude' => 'nullable|numeric',
                'branch_longitude' => 'nullable|numeric',
            ], [
                'mobile.digits' => 'The mobile number must be exactly 10 digits.',
            ]);
        } else {
            $this->validate($request, [
                'role_ids' => 'required',
                'name' => 'required',
                'mobile' => 'required|digits:10|unique:users,mobile,' . $request->id . ',id',
                'email' => 'required|email|unique:users,email,' . $request->id . ',id',
                'branch_latitude' => 'nullable|numeric',
                'branch_longitude' => 'nullable|numeric',
            ], [
                'mobile.digits' => 'The mobile number must be exactly 10 digits.',
            ]);
        }

        return $this->employeeService->employee_store($request);
    }

    public function employeeList(Request $request)
    {
        return $this->employeeService->employeeList($request);
    }
    public function employeeListAndroid(Request $request)
    {
        return $this->employeeService->employeeList($request, 'app');
    }

    public function salaryStore(Request $request)
    {

        if (empty($request->salary_id)) {
            $this->validate($request, [
                'basic_salary' => 'integer|required',

            ]);
        } else {
            $this->validate($request, [
                'basic_salary' => 'integer|required',
            ]);
        }
        return $this->employeeService->salary_store($request);
    }
    public function salaryList(Request $request)
    {
        return $this->employeeService->salaryList($request);
    }

    public function incentive_value(Request $request)
    {
        return $this->employeeService->incentiveValue($request);
    }

    public function incentiveStore(Request $request)
    {
        $this->validate($request, [
            'start_date' => 'required',
            'end_date' => 'required',
            'auth_password' => 'required|string',
        ]);

        $passwordError = $this->employeeService->validateCurrentUserPassword($request->auth_password);
        if ($passwordError) {
            return apiResponse(1, $passwordError, []);
        }

        return $this->employeeService->incentive_store($request);
    }

    public function incentiveTermStore(Request $request)
    {
        // dd($request);
        if (empty($request->incentive_terms_id)) {
            $this->validate($request, [
                'incentive_title' => 'required',
                // 'incentive_range_from' => 'required',
                // 'incentive_range_to' => 'required',
                'incentive_percentage' => 'required'
            ]);
        } else {
            $this->validate($request, [
                'incentive_title' => 'required',
                // 'incentive_range_from' => 'required',
                // 'incentive_range_to' => 'required',
                'incentive_percentage' => 'required'

            ]);
        }
        return $this->employeeService->incentive_term_store($request);
    }

    public function incentiveTermList(Request $request)
    {
        return $this->employeeService->incentiveTermList($request);
    }
    public function saveIncentiveData(Request $request)
    {
        $this->validate($request, [
            'auth_password' => 'required|string',
        ]);

        $passwordError = $this->employeeService->validateCurrentUserPassword($request->auth_password);
        if ($passwordError) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $passwordError,
            ]);
        }

        return $this->employeeService->saveIncentiveData($request);
    }

    public function incentiveAuthCheck(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|string',
        ]);

        $user = auth('web')->user() ?: auth()->user();
        if (! $user) {
            return response()->json([
                'valid' => false,
                'statusText' => 'Unauthorized. Please refresh and log in again.',
                'errorcode' => 401,
            ], 401);
        }

        if (! $user->can('incentive_edit') && ! $user->can('incentive_create')) {
            return response()->json([
                'valid' => false,
                'statusText' => 'You do not have permission to manage incentive.',
                'errorcode' => 403,
            ], 403);
        }

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'valid' => false,
                'statusText' => 'Incorrect password.',
                'errorcode' => 401,
            ], 401);
        }

        return response()->json([
            'valid' => true,
            'statusText' => 'Validation successful.',
            'errorcode' => 0,
        ]);
    }

    public function incentiveList(Request $request)
    {
        return $this->employeeService->incentiveList($request);
    }

    public function newIncentiveList(Request $request)
    {
        return $this->employeeService->newIncentiveList($request);
    }
    // public function payrollStore(Request $request)
    // {
    //     if (empty($request->payroll_id)) {
    //         $this->validate($request, [
    //             'emp_id' => 'integer|required',
    //             'month' => 'required',
    //             'year' => 'required'
    //         ]);
    //     } else {
    //         $this->validate($request, [
    //             'emp_id' => 'integer|required',
    //             'month' => 'required',
    //             'year' => 'required'
    //         ]);
    //     }
    //     return $this->employeeService->payroll_store($request);
    // }

    public function payrollList(Request $request)
    {
        return $this->employeeService->payrollList($request);
    }

    public function payrollDownload($payroll_id)
    {
        $salaryInfo = Payroll::with('employee')->where('payroll_id', $payroll_id)->firstOrFail();

        $totalAddition = (float) $salaryInfo->basic_salary
            + (float) $salaryInfo->TA
            + (float) $salaryInfo->DA
            + (float) $salaryInfo->HRA
            + (float) $salaryInfo->special_allowance
            + (float) ($salaryInfo->overtime_amount ?? 0);

        $totalDeduction = (float) $salaryInfo->TDS_amount
            + (float) $salaryInfo->professional_tax_amount
            + (float) ($salaryInfo->deduction ?? 0)
            + (float) ($salaryInfo->advance ?? 0);

        $netSalary = round(
            (float) $salaryInfo->net_salary
            + (float) ($salaryInfo->overtime_amount ?? 0)
            - (float) ($salaryInfo->deduction ?? 0)
            - (float) ($salaryInfo->advance ?? 0),
            2
        );

        if ($netSalary <= 0) {
            $paidTotal = (float) PayrollPayments::where('payroll_id', $payroll_id)->sum('payment_amount');
            $netSalary = $paidTotal > 0 ? round($paidTotal, 2) : round((float) $salaryInfo->payable_amount, 2);
        }

        $businessConfigs = BusinessConfig::whereIn('config_key', [
            'business_title_english',
            'phone',
            'email',
            'address_english',
            'city_english',
        ])->pluck('config_value', 'config_key');

        $dataToPage = [
            'salaryInfo' => $salaryInfo,
            'totalAddition' => $totalAddition,
            'totalDeduction' => $totalDeduction,
            'netSalary' => $netSalary,
            'company' => [
                'name' => trim((string) ($businessConfigs['business_title_english'] ?? '')),
                'phone' => trim((string) ($businessConfigs['phone'] ?? '')),
                'email' => trim((string) ($businessConfigs['email'] ?? '')),
                'address' => trim((string) ($businessConfigs['address_english'] ?? '')),
                'city' => trim((string) ($businessConfigs['city_english'] ?? '')),
            ],
        ];

        return view('payroll.download', compact('dataToPage'));
    }

    public function payrollPaymentList(Request $request)
    {
        return $this->employeeService->payrollPaymentList($request);
    }
    public function commission_value(Request $request)
    {
        return $this->employeeService->commissionValue($request);
    }

    public function commissionStore(Request $request)
    {
        if (empty($request->commission_id)) {
            $this->validate($request, [
                'emp_id' => 'integer|required',
                'start_date' => 'required',
                'end_date' => 'required',
                'commission_status' => 'required|in:active,inactive'
            ]);
        } else {
            $this->validate($request, [
                'emp_id' => 'integer|required',
                'start_date' => 'required',
                'end_date' => 'required',
                'commission_status' => 'required|in:active,inactive'
            ]);
        }
        return $this->employeeService->commission_store($request);
    }

    public function commissionList(Request $request)
    {
        return $this->employeeService->commissionList($request);
    }

    public function getIncentiveByMasterId($id, IncentivePaymentService $incentivePaymentService)
    {
        $incentives = IncentiveMaster::with([
            'incentives.paymentVoucher',
            'incentives.payments.transactionGroup',
        ])->where('incentive_master_id', $id)->get();

        $incentives->transform(function ($master) use ($incentivePaymentService) {
            $rows = $master->incentives ?? collect();
            $master->payment_vouchers = $incentivePaymentService->voucherLinksForIncentives($rows);
            $master->setRelation('incentives', $rows->map(function ($row) use ($incentivePaymentService) {
                $needsSalary = (float) ($row->salary ?? 0) <= 0;
                $needsTarget = (float) ($row->target_amount ?? 0) <= 0;

                if (($needsSalary || $needsTarget) && ! empty($row->emp_id)) {
                    $activeSalary = \App\Models\Salary::where('emp_id', $row->emp_id)
                        ->where('status', 'active')
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($activeSalary) {
                        if ($needsSalary) {
                            $row->salary = (float) ($activeSalary->basic_salary ?? 0);
                        }
                        if ($needsTarget) {
                            $row->target_amount = (float) ($activeSalary->target_value ?? 0);
                        }
                    }
                }

                $row->salaryInfo = [
                    'basic_salary' => (float) ($row->salary ?? 0),
                    'target_value' => (float) ($row->target_amount ?? 0),
                    'incentive_percentage' => (float) ($row->incentive_percentage ?? 0),
                ];
                $row->payment_vouchers = $incentivePaymentService->voucherLinksForIncentives([$row]);

                return $row;
            }));

            return $master;
        });

        $response['data'] = $incentives;

        return apiResponse(0, 'success', $response);
    }









    public function generateCommission(Request $request)
    {
        // dd($request);
        if (empty($request->commission_master_id)) {
            $this->validate($request, [
                'start_date' => 'required',
                'end_date' => 'required',
                'auth_password' => 'required|string',
            ]);
        } else {
            $this->validate($request, [
                'start_date' => 'required',
                'end_date' => 'required',
                'auth_password' => 'required|string',
            ]);
        }

        $passwordError = $this->employeeService->validateCurrentUserPassword($request->auth_password);
        if ($passwordError) {
            return apiResponse(1, $passwordError, []);
        }

        return $this->employeeService->generate_commission($request);
    }

    public function saveCommissionData(Request $request)
    {
        $this->validate($request, [
            'auth_password' => 'required|string',
        ]);

        $passwordError = $this->employeeService->validateCurrentUserPassword($request->auth_password);
        if ($passwordError) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $passwordError,
            ]);
        }

        return $this->employeeService->saveCommissionData($request);
    }

    public function commissionAuthCheck(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|string',
        ]);

        $user = auth('web')->user() ?: auth()->user();
        if (! $user) {
            return response()->json([
                'valid' => false,
                'statusText' => 'Unauthorized. Please refresh and log in again.',
                'errorcode' => 401,
            ], 401);
        }

        if (! $user->can('commission_edit') && ! $user->can('commission_create')) {
            return response()->json([
                'valid' => false,
                'statusText' => 'You do not have permission to manage commission.',
                'errorcode' => 403,
            ], 403);
        }

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'valid' => false,
                'statusText' => 'Incorrect password.',
                'errorcode' => 401,
            ], 401);
        }

        return response()->json([
            'valid' => true,
            'statusText' => 'Validation successful.',
            'errorcode' => 0,
        ]);
    }

    public function payCommission(Request $request)
    {
        $this->validate($request, [
            'commission_id' => 'required',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|in:cash,bank',
            'auth_password' => 'required|string',
        ]);

        $passwordError = $this->employeeService->validateCurrentUserPassword($request->auth_password);
        if ($passwordError) {
            return apiResponse(1, $passwordError, []);
        }

        return $this->employeeService->payCommission($request);
    }

    public function payIncentive(Request $request)
    {
        $this->validate($request, [
            'incentive_id' => 'required',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|in:cash,bank',
            'auth_password' => 'required|string',
        ]);

        $passwordError = $this->employeeService->validateCurrentUserPassword($request->auth_password);
        if ($passwordError) {
            return apiResponse(1, $passwordError, []);
        }

        return $this->employeeService->payIncentive($request);
    }

    public function commissionsList(Request $request)
    {
        return $this->employeeService->commissionsList($request);
    }

    public function commissionByMasterId($id, CommissionPaymentService $commissionPaymentService)
    {
        $commissions = CommissionMaster::with([
            'commissions.paymentVoucher',
            'commissions.payments.transactionGroup',
        ])->where('commission_master_id', $id)->get();

        $commissions->transform(function ($master) use ($commissionPaymentService) {
            $rows = $master->commissions ?? collect();
            $master->payment_vouchers = $commissionPaymentService->voucherLinksForCommissions($rows);
            $master->setRelation('commissions', $rows->map(function ($commission) use ($commissionPaymentService) {
                $commission->payment_vouchers = $commissionPaymentService->voucherLinksForCommissions([$commission]);

                return $commission;
            }));

            return $master;
        });

        $response['data'] = $commissions;

        return apiResponse(0, 'success', $response);
    }

    public function viewCommission(Request $request)
    {
        try {
            $validateData = $this->validate($request, [
                'commission_id' => 'required',
            ]);
            return $this->employeeService->viewCommission($validateData);
        } catch (Exception $e) {
            return apiResponse(1, 'Error', $e->getMessage());
        }
    }

    public function viewPayroll(Request $request)
    {
        try {
            $validateData = $this->validate($request, [
                'payroll_id' => 'required',
            ]);
            return $this->employeeService->viewPayroll($validateData);
        } catch (Exception $e) {
            return apiResponse(1, 'Error', $e->getMessage());
        }
    }
    public function advance_salary()
    {
        return view('payroll.advance_salary');
    }
    public function advanceSalaryStore(Request $request)
    {
        $this->validate($request, [
            'emp_id' => 'required|integer',
            'month' => 'required',
            'year' => 'required',
            'voucher_date' => 'required|date',
            'advance_amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|in:cash,bank',
        ]);

        return $this->employeeService->advance_salary_store($request);
    }

    public function advanceSalaryList(Request $request)
    {
        return $this->employeeService->advanceSalaryList($request);
    }

    public function getSalaryByEmployeeId(Request $request)
    {
        try {
            $this->validate($request, [
                'emp_id' => 'required',
            ]);
            return $this->employeeService->getSalaryByEmployeeId($request);
        } catch (Exception $e) {
            return apiResponse(1, 'Error', $e->getMessage());
        }
    }

    public function salaryPayrollStore(Request $request)
    {

        if (empty($request->payroll_id)) {
            $this->validate($request, [
                'voucher_date' => 'required',
                'schedule_date' => 'required',
                'month' => 'required',
                'year' => 'required'
            ]);
        } else {
            $this->validate($request, [
                'voucher_date' => 'required',
                'schedule_date' => 'required',
                'month' => 'required',
                'year' => 'required'
            ]);
        }
        return $this->employeeService->salaryPayrollStore($request);
    }

    public function generatePayroll(Request $request)
    {
        $this->validate($request, [
            'voucher_date' => 'required',
            'schedule_date' => 'required',
            'month' => 'required',
            'year' => 'required',
            'auth_password' => 'required|string',
        ]);

        $passwordError = $this->employeeService->validateCurrentUserPassword($request->auth_password);
        if ($passwordError) {
            return apiResponse(1, $passwordError, []);
        }

        return $this->employeeService->generatePayrollPreview($request);
    }

    public function savePayrollData(Request $request)
    {
        $this->validate($request, [
            'month' => 'required',
            'year' => 'required',
            'voucher_date' => 'required|date',
            'schedule_date' => 'required|date',
            'payrolls' => 'required|array|min:1',
            'auth_password' => 'required|string',
        ]);

        $passwordError = $this->employeeService->validateCurrentUserPassword($request->auth_password);
        if ($passwordError) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $passwordError,
            ], 422);
        }

        return $this->employeeService->savePayrollData($request->all());
    }

    public function getPayrollByMasterId($id)
    {
        return $this->employeeService->getPayrollByMasterId($id);
    }

    public function payrollMastersList(Request $request)
    {
        return $this->employeeService->payrollMastersList($request);
    }

    public function updatePayroll(Request $request)
    {
        $this->validate($request, [
            'payroll_id' => 'required|integer',
            'voucher_date' => 'required|date',
            'schedule_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'HRA' => 'nullable|numeric|min:0',
            'TA' => 'nullable|numeric|min:0',
            'DA' => 'nullable|numeric|min:0',
            'special_allowance' => 'nullable|numeric|min:0',
            'TDS_amount' => 'nullable|numeric|min:0',
            'professional_tax_amount' => 'nullable|numeric|min:0',
            'overtime_amount' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'advance' => 'nullable|numeric|min:0',
            'auth_password' => 'required|string',
        ]);

        $passwordError = $this->employeeService->validateCurrentUserPassword($request->auth_password);
        if ($passwordError) {
            return apiResponse(1, $passwordError, []);
        }

        return $this->employeeService->salaryPayrollUpdate($request);
    }

    public function payPayroll(Request $request)
    {
        $this->validate($request, [
            'payroll_id' => 'required',
            'emp_id' => 'required',
            'payable_amount' => 'required',
            'auth_password' => 'required|string',
        ]);

        $passwordError = $this->employeeService->validateCurrentUserPassword($request->auth_password);
        if ($passwordError) {
            return apiResponse(1, $passwordError, []);
        }

        return $this->employeeService->salaryPayrollScheduleUpdate($request);
    }

    public function payrollAuthCheck(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|string',
        ]);

        $user = auth('web')->user() ?: auth()->user();
        if (! $user) {
            return response()->json([
                'valid' => false,
                'statusText' => 'Unauthorized. Please refresh and log in again.',
                'errorcode' => 401,
            ], 401);
        }

        if (
            ! $user->can('payroll_create')
            && ! $user->can('payment_create')
            && ! $user->can('payment_edit')
        ) {
            return response()->json([
                'valid' => false,
                'statusText' => 'You do not have permission to manage payroll.',
                'errorcode' => 403,
            ], 403);
        }

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'valid' => false,
                'statusText' => 'Incorrect password.',
                'errorcode' => 401,
            ], 401);
        }

        return response()->json([
            'valid' => true,
            'statusText' => 'Validation successful.',
            'errorcode' => 0,
        ]);
    }

    public function salaryPayrollUpdate(Request $request)
    {
        $this->validate($request, [
            'payroll_id' => 'required|integer',
            'voucher_date' => 'required|date',
            'schedule_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'HRA' => 'nullable|numeric|min:0',
            'TA' => 'nullable|numeric|min:0',
            'DA' => 'nullable|numeric|min:0',
            'special_allowance' => 'nullable|numeric|min:0',
            'TDS_amount' => 'nullable|numeric|min:0',
            'professional_tax_amount' => 'nullable|numeric|min:0',
            'overtime_amount' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'advance' => 'nullable|numeric|min:0',
        ]);

        return $this->employeeService->salaryPayrollUpdate($request);
    }

    public function salaryPayrollList(Request $request)
    {
        return $this->employeeService->salaryPayrollList($request);
    }

    public function salaryPayrollScheduleUpdate(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'payroll_id' => 'required',
            'emp_id' => 'required',
            'payable_amount' => 'required',
        ]);

        return $this->employeeService->salaryPayrollScheduleUpdate($request);
    }

    public function salarySchedule(Request $request)
    {
        return $this->employeeService->salarySchedule($request);
    }
}
