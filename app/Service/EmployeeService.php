<?php

namespace App\Service;

use App\Models\AclRole;
use App\Models\AutoIncrementMaster;
use App\Models\Beautician;
use App\Models\BusinessConfig;
use App\Models\Commission;
use App\Models\CommissionMaster;
use App\Models\Employee;
use App\Models\EmployeeAttendence;
use App\Models\EmployeeSalaryAdvance;
use App\Models\Incentive;
use App\Models\IncentiveMaster;
use App\Models\IncentiveTerms;
use App\Models\LeaveManagement;
use App\Models\Payroll;
use App\Models\PayrollMaster;
use App\Models\PayrollPayments;
use App\Models\Salary;
use App\Models\Shift;
use App\Models\SplitServiceAmount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EmployeeService
{
    protected $accountingService;

    protected $commissionPaymentService;

    protected $incentivePaymentService;

    protected $payrollPaymentService;

    public function __construct(
        AccountingService $accountingService,
        CommissionPaymentService $commissionPaymentService,
        IncentivePaymentService $incentivePaymentService,
        PayrollPaymentService $payrollPaymentService
    ) {
        $this->accountingService = $accountingService;
        $this->commissionPaymentService = $commissionPaymentService;
        $this->incentivePaymentService = $incentivePaymentService;
        $this->payrollPaymentService = $payrollPaymentService;
    }

    /**
     * FormData/JS often sends empty coords as "", "null", or null.
     * Persist real SQL NULL when blank; otherwise a numeric float.
     */
    private function nullableCoordinate($value): ?float
    {
        if ($value === null || $value === '' || $value === 'null' || $value === 'undefined') {
            return null;
        }

        if (! is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    private function paymentFailureResponse(\Throwable $e, string $context = 'Payment')
    {
        DB::rollBack();

        if ($e instanceof ValidationException) {
            $message = collect($e->errors())->flatten()->first() ?: $e->getMessage();

            return apiResponse(25, $message);
        }

        Log::error($context.': '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

        return apiResponse(25, $e->getMessage());
    }

    public function getroleList($data = null, $platform = 'web')
    {
        $result = AclRole::query();

        if ($platform == 'web') {
            $result = $result->orderBy('created_at', 'DESC')->get();

            // $response['pagination'] = generatePaginationData($result);
            // $response['data'] = $result;
            return apiResponse(0, 'success', $result);
        } else {
        }
    }

    public function getshiftList($data = null, $platform = 'web')
    {
        $result = Shift::where('is_active', 1);

        if ($platform == 'web') {
            $result = $result->orderBy('created_at', 'DESC')->get();

            // $response['pagination'] = generatePaginationData($result);
            // $response['data'] = $result;
            return apiResponse(0, 'success', $result);
        } else {
        }
    }

    public function generateEmployeeCode()
    {
        $auto_emp_code = AutoIncrementMaster::where('auto_code', 'AI_EMPLOYEE_CODE')->first();
        $emp_code = $auto_emp_code->auto_increment_general_value;
        $auto_emp_code->auto_increment_general_value = $emp_code + 1;
        $auto_emp_code->save();

        return $auto_emp_code->auto_prefix.$emp_code;
    }

    public function employee_store($data = null, $platform = 'web')
    {
        try {
            DB::beginTransaction();

            $isNew = empty($data->emp_id);
            $userId = ! empty($data->id) ? (int) $data->id : null;
            $existingUser = $userId ? User::find($userId) : null;

            $userData = [
                'name' => $data->name,
                'email' => $data->email ?? '',
                'date_of_birth' => ! empty($data->dob) ? $data->dob : null,
                'mobile' => $data->mobile,
                'address' => $data->address ?? '',
                'gender' => $data->gender,
                'shift_id' => ! empty($data->shift) ? $data->shift : null,
                'user_status' => $data->status,
            ];

            if (! empty($data->username)) {
                $userData['username'] = $data->username;
            }

            // Plain password — User model 'hashed' cast handles hashing
            if (! empty($data->password)) {
                $userData['password'] = $data->password;
            }

            if ($isNew) {
                if (empty($userData['username']) || empty($userData['password'])) {
                    throw ValidationException::withMessages([
                        'password' => ['Username and password are required for new employees.'],
                    ]);
                }

                $userCreate = User::create($userData);
            } else {
                if (! $existingUser) {
                    throw new Exception('User not found for this employee.');
                }

                $existingUser->fill($userData);
                $existingUser->save();
                $userCreate = $existingUser;
            }

            // Handle roles (if any)
            $roleIds = array_map('intval', $data->role_ids ?? []);
            $roleNames = AclRole::whereIn('id', $roleIds)->pluck('name')->toArray();

            if (! empty($roleNames)) {
                $user = User::findOrFail($userCreate->id);
                $user->syncRoles($roleNames);
            }

            if (! empty(array_intersect(['Stylist', 'Hairstylist Unisex', 'Hairstylist', 'beautition'], $roleNames))) {
                Beautician::updateOrCreate(
                    ['beautician_id' => $userCreate->id],
                    [
                        'created_outlet_id' => 1,
                        'join_date' => $data->joing_date,
                        'status' => ($data->status === 'active') ? Beautician::STATUS_ACTIVE : Beautician::STATUS_INACTIVE,
                    ]
                );
            } else {
                // Keep row but hide from listings when not a stylist role
                Beautician::where('beautician_id', $userCreate->id)
                    ->update(['status' => Beautician::STATUS_INACTIVE]);
            }

            // Handle file upload for profile photo
            if ($data->hasFile('image')) {
                $file = $data->file('image');
                $this->uploadProfilePhoto($userCreate->id, $file);
            }

            $employeePayload = [
                'user_id' => $userCreate->id,
                'name' => $data->name,
                'designation' => $data->designation,
                'join_date' => $data->joing_date,
                'status' => $data->status,
                'acc_no' => $data->acc_no ?? '',
                'ifsc_code' => $data->ifsc_code ?? '',
                'branch_name' => $data->branch ?? '',
                'payment_mode' => $data->payment_mode,
                'latitude' => $this->nullableCoordinate($data->branch_latitude ?? null),
                'longitude' => $this->nullableCoordinate($data->branch_longitude ?? null),
                'hourly_basic_pay' => $data->hourly_basic_pay,
            ];

            if ($isNew) {
                $employeePayload['emp_code'] = ! empty($data->emp_code)
                    ? $data->emp_code
                    : $this->generateEmployeeCode();

                $employeeCreate = Employee::create($employeePayload);
            } else {
                $employeeCreate = Employee::where('emp_id', $data->emp_id)->firstOrFail();

                if (! empty($data->emp_code)) {
                    $employeePayload['emp_code'] = $data->emp_code;
                }

                $employeeCreate->fill($employeePayload);
                $employeeCreate->save();
            }

            // If still a stylist role, keep beautician status aligned with employee status
            if (! empty(array_intersect(['Stylist', 'Hairstylist Unisex', 'Hairstylist'], $roleNames))) {
                Beautician::syncStatusFromEmployee((int) $userCreate->id, (string) $data->status);
            }

            $this->accountingService->ensureEmployeeLedgers($employeeCreate->emp_id);

            if ($employeeCreate) {
                $errormessage = $isNew ? 'Employee Created Successfully.' : 'Employee Updated Successfully.';
                $errorcode = 0;
            } else {
                $errormessage = 'Database error...';
                $errorcode = 15;
            }

            DB::commit();

            if ($platform == 'web') {
                return apiResponse($errorcode, $errormessage, $data);
            }

            return apiResponse($errorcode, $errormessage);
        } catch (\Exception $e) {
            Log::debug($e);
            DB::rollBack();

            if ($e instanceof ValidationException) {
                throw $e;
            }

            return apiResponse(25, $e->getMessage());
        }
    }

    public function uploadProfilePhoto($user_id, $file)
    {
        $user = User::findOrFail($user_id);

        // Save existing profile photo
        $existing_photo = $user->profile_photo_path;

        if ($file->isValid()) {

            $ext = $file->extension();

            $filename = 'image_'.preg_replace('/\s+/', '', $user->name).'-'.time().'.'.$ext;

            $path = $file->storeAs('uploads/'.Beautician::BEAUTICIAN_UPLOAD_PATH, $filename, ['disk' => 'public']);

            // Save the filename to the user's profile photo path
            $user->profile_photo_path = $path;
            $user->save();

            // Delete the existing profile photo from the disk
            $this->deleteDiskImage($existing_photo);
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

    public function employeeList($data = null, $platform = 'web')
    {
        $result = Employee::with(['user', 'user.beautician', 'user.roles', 'currentAttendence']);

        // Status filter: active | inactive | all (default active for management UI)
        $statusFilter = $data->status ?? $data->filter_status ?? 'active';
        if ($statusFilter === 'active' || $statusFilter === 'inactive') {
            $result->where('status', $statusFilter);
        }

        if ($platform == 'web') {

            if (! empty($data->name)) {
                $name = trim($data->name);
                $result->where(function ($q) use ($name) {
                    $q->where('employees.name', 'LIKE', "%{$name}%")
                        ->orWhere('employees.emp_code', 'LIKE', "%{$name}%")
                        ->orWhereHas('user', function ($uq) use ($name) {
                            $uq->where('name', 'LIKE', "%{$name}%")
                                ->orWhere('username', 'LIKE', "%{$name}%")
                                ->orWhere('mobile', 'LIKE', "%{$name}%");
                        });
                });
            }

            $roleName = trim((string) ($data->role ?? $data->filter_role ?? ''));
            if ($roleName !== '') {
                $result->whereHas('user.roles', function ($q) use ($roleName) {
                    $q->where('name', $roleName);
                });
            }

            $result->with(['accountLedgers.account']);

            $result = $result->orderByRaw("CASE WHEN employees.status = 'active' THEN 0 ELSE 1 END")
                ->orderBy('created_at', 'DESC')
                ->paginate(config('vocomi.settings.pagination.perpage.admin'));

            $ledgerMap = $this->accountingService->ledgersForEmployees($result->getCollection());

            $result->each(function ($employee) use ($ledgerMap) {
                $employee->assigned_roles = optional(optional($employee->user)->roles)->pluck('name') ?? collect();
                $employee->setAttribute('ledgers', $ledgerMap[(int) $employee->emp_id] ?? []);
                $employee->unsetRelation('accountLedgers');

                if ($employee->user) {
                    $employee->user->photo_src_url = beautician_photo_url(
                        $employee->user->profile_photo_path ?? null,
                        44,
                        44,
                        1
                    );
                }
            });

            $response['pagination'] = generatePaginationData($result);
            $response['data'] = $result;

            return apiResponse(0, 'success', $response);
        } else {
            // App: keep only active employees
            $result = $result->where('status', 'active')->get();

            return apiResponse(0, 'success', $result);
        }
    }

    public function salary_store($data = null, $platform = 'web')
    {
        // return $data;
        try {
            DB::beginTransaction();

            $isNew = empty($data->salary_id);
            $incomingStatus = $data->salary_status ?: 'active';

            // Only one active salary per employee: when adding a new Active record,
            // mark any previous Active rows for that employee as Inactive.
            if ($isNew && $incomingStatus === 'active' && ! empty($data->emp_id)) {
                Salary::where('emp_id', $data->emp_id)
                    ->where('status', 'active')
                    ->update(['status' => 'inactive']);
            }

            $salaryCreate = Salary::updateOrCreate(
                ['salary_id' => $data->salary_id],
                [
                    'emp_id' => $data->emp_id,
                    'basic_salary' => $data->basic_salary ?? 0,
                    'TA' => $data->TA ?? 0,
                    'DA' => $data->DA ?? 0,
                    'HRA' => $data->HRA ?? 0,
                    'special_allowance' => $data->special_allowance ?? 0,
                    'TDS' => $data->TDS ?? 0,
                    'TDS_amount' => (($data->basic_salary ?? 0) * ($data->TDS ?? 0)) / 100,
                    'professional_tax' => $data->professional_tax ?? 0,
                    'professional_tax_amount' => (($data->basic_salary ?? 0) * ($data->professional_tax ?? 0)) / 100,
                    'target_value' => $data->target_value ?? 0,
                    // 'incentive_percentage' => $data->incentive_percentage ?? 0,
                    'status' => $incomingStatus,
                    'net_salary' => ($data->basic_salary ?? 0) +
                        ($data->TA ?? 0) +
                        ($data->DA ?? 0) +
                        ($data->HRA ?? 0) +
                        ($data->special_allowance ?? 0) -
                        ((($data->basic_salary ?? 0) * ($data->TDS ?? 0)) / 100) -
                        ((($data->basic_salary ?? 0) * ($data->professional_tax ?? 0)) / 100),
                ]
            );

            if ($salaryCreate) {
                $errormessage = $isNew ? 'Salary Created Successfully.' : 'Salary Updated Successfully.';
                $errorcode = 0;
            } else {
                $errormessage = 'Database error...';
                $errorcode = 15;
            }

            DB::commit();

            if ($platform == 'web') {
                return apiResponse($errorcode, $errormessage, $data);
            } else {
                // return apiResponse($errorcode, $errormessage);
            }
        } catch (\Exception $e) {
            Log::debug($e);
            DB::rollBack();

            return apiResponse(25, $e);
        }
    }

    public function salaryList($data = null, $platform = 'web')
    {
        $result = Salary::with('employee');

        if ($platform == 'web') {

            if (! empty($data->emp_id)) {
                $result->where('emp_id', $data->emp_id);
            }

            if (! empty($data->salary_id)) {
                $result->where('salary_id', $data->salary_id);
            }

            $result = $result->orderBy('created_at', 'DESC')->paginate(config('vocomi.settings.pagination.perpage.admin'));
            $response['pagination'] = generatePaginationData($result);
            $response['data'] = $result;

            return apiResponse(0, 'success', $response);
            // return response()->json($response);
        } else {
        }
    }

    public function incentiveValue($data = null, $platform = 'web')
    {
        $salaryInfo = Salary::where('emp_id', $data->emp_id)->where('status', 'active')->orderBy('created_at', 'desc')->first();

        if (! $salaryInfo) {
            return apiResponse(0, 'No active salary record found for the employee.', null);
        }

        $user_id = Employee::where('emp_id', $data->emp_id)->pluck('user_id')->first();

        $totalSplitAmount = $this->sumBeauticianSplitPrice($user_id, $data->start_date, $data->end_date);
        $calculated = $this->calculateIncentiveForSales($salaryInfo, $totalSplitAmount);

        if ($calculated['incentive_amount'] <= 0) {
            return apiResponse(10, ' No Incentive Amount.', null);
        }

        $response = [
            'data' => [
                'incentiveAmount' => $calculated['incentive_amount'],
                'incentivePercentage' => $calculated['incentive_percentage'],
                'salaryInfo' => $salaryInfo,
                'totalSplitAmount' => $totalSplitAmount,
            ],
        ];

        if ($platform == 'web') {
            return apiResponse(0, 'Success', $response);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Success',
            'data' => $response,
        ]);
    }

    public function incentive_term_store($data = null, $platform = 'web')
    {
        try {
            DB::beginTransaction();

            $status = in_array(($data->status ?? 'active'), ['active', 'inactive'], true)
                ? $data->status
                : 'active';

            $incentiveCreate = IncentiveTerms::updateOrCreate([
                'incentive_terms_id' => $data->incentive_terms_id,
            ], [
                'incentive_title' => $data->incentive_title,
                'incentive_range_from' => 0,
                'incentive_range_to' => 0,
                'incentive_percentage' => $data->incentive_percentage,
                'status' => $status,
            ]);

            if ($incentiveCreate) {
                $errormessage = $data->incentive_terms_id > 0 ? 'Incentive Terms Updated Successfully.' : 'Incentive Terms Created Successfully.';
                $errorcode = 0;
            } else {
                $errormessage = 'Database error...';
                $errorcode = 15;
            }

            DB::commit();

            if ($platform == 'web') {
                return apiResponse($errorcode, $errormessage, $data);
            } else {
                // return apiResponse($errorcode, $errormessage);
            }
        } catch (\Exception $e) {
            Log::debug($e);
            DB::rollBack();

            return apiResponse(25, $e);
        }
    }

    public function incentiveTermList($data = null, $platform = 'web')
    {

        $result = IncentiveTerms::query();

        if ($platform == 'web') {
            $search = trim((string) ($data->search ?? $data->name ?? ''));
            if ($search !== '') {
                $result->where(function ($q) use ($search) {
                    $q->where('incentive_title', 'LIKE', "%{$search}%")
                        ->orWhere('incentive_percentage', 'LIKE', "%{$search}%");
                });
            }

            $statusFilter = $data->status ?? $data->filter_status ?? 'all';
            if ($statusFilter === 'active' || $statusFilter === 'inactive') {
                $result->where('status', $statusFilter);
            }

            $result = $result->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
                ->orderBy('created_at', 'DESC')
                ->paginate(config('vocomi.settings.pagination.perpage.admin'));

            $response['pagination'] = generatePaginationData($result);
            $response['data'] = $result;

            return apiResponse(0, 'success', $response);
            // return response()->json($response);
        } else {
        }
    }

    // public function incentive_store($data = null, $platform = 'web')
    // {
    //     try {
    //         DB::beginTransaction();

    //         $incentiveCreate = Incentive::updateOrCreate([
    //             'incentive_id' => $data->incentive_id
    //         ], [
    //             'emp_id' => $data->emp_id,
    //             'start_date' => $data->start_date,
    //             'end_date' => $data->end_date,
    //             'incentive_status' => $data->incentive_status,
    //             'incentive_amount' => $data->incentive,
    //             'target_amount' => $data->target_amount,
    //             'incentive_percentage' => $data->incentive_percentage,
    //             'total_amount' => $data->total_amount,
    //         ]);

    //         if ($incentiveCreate) {
    //             $errormessage = $data->incentive_id > 0 ? 'Incentive Updated Successfully.' : 'Incentive Created Successfully.';
    //             $errorcode = 0;
    //         } else {
    //             $errormessage = 'Database error...';
    //             $errorcode = 15;
    //         }

    //         DB::commit();

    //         if ($platform == 'web') {
    //             return apiResponse($errorcode, $errormessage, $data);
    //         } else {
    //             // return apiResponse($errorcode, $errormessage);
    //         }
    //     } catch (\Exception $e) {
    //         Log::debug($e);
    //         DB::rollBack();
    //         return apiResponse(25,  $e);
    //     }
    // }

    public function incentive_store($data = null, $platform = 'web')
    {
        $startDate = $data->start_date ?? null;
        $endDate = $data->end_date ?? null;

        if (empty($startDate) || empty($endDate)) {
            return apiResponse(1, 'Start date and end date are required.', []);
        }

        if (strtotime($startDate) > strtotime($endDate)) {
            return apiResponse(1, 'Start date cannot be after end date.', []);
        }

        $excludeMasterId = ! empty($data->incentive_master_id) ? (int) $data->incentive_master_id : null;
        $overlap = $this->findOverlappingIncentiveMaster($startDate, $endDate, $excludeMasterId);
        if ($overlap) {
            return apiResponse(
                1,
                sprintf(
                    'Incentive already exists for overlapping dates (%s to %s). Choose a different date range.',
                    date('d-m-Y', strtotime($overlap->start_date)),
                    date('d-m-Y', strtotime($overlap->end_date))
                ),
                [
                    'overlap' => [
                        'incentive_master_id' => $overlap->incentive_master_id,
                        'start_date' => $overlap->start_date,
                        'end_date' => $overlap->end_date,
                    ],
                ]
            );
        }

        $employees = Employee::with('user')
            ->where('status', 'active')
            ->whereHas('user', function ($q) {
                $q->where('user_status', 'active')
                    ->whereHas('beautician', function ($bq) {
                        $bq->where('status', Beautician::STATUS_ACTIVE);
                    });
            })
            ->get();
        $responseData = [];

        foreach ($employees as $employee) {
            $emp_id = $employee->emp_id;
            $user_id = $employee->user_id;

            $salaryInfo = Salary::where('emp_id', $emp_id)
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->first();

            $totalSplitAmount = 0;
            $incentiveAmount = 0;
            $incentivePercentage = 0;

            if ($salaryInfo) {
                $totalSplitAmount = $this->sumBeauticianSplitPrice($user_id, $startDate, $endDate);
                $calculated = $this->calculateIncentiveForSales($salaryInfo, $totalSplitAmount);
                $incentiveAmount = $calculated['incentive_amount'];
                $incentivePercentage = $calculated['incentive_percentage'];
            }

            $responseData[] = [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'emp_id' => $emp_id,
                'user_id' => $user_id,
                'name' => optional($employee->user)->name ?? 'N/A',
                'incentivePercentage' => $incentivePercentage,
                'incentiveAmount' => $incentiveAmount,
                'totalSplitAmount' => $totalSplitAmount,
                'salary' => $salaryInfo ? (float) ($salaryInfo->basic_salary ?? 0) : 0,
                'target_amount' => $salaryInfo ? (float) ($salaryInfo->target_value ?? 0) : 0,
                'salaryInfo' => $salaryInfo ? [
                    'basic_salary' => (float) ($salaryInfo->basic_salary ?? 0),
                    'target_value' => (float) ($salaryInfo->target_value ?? 0),
                    'incentive_percentage' => (float) ($salaryInfo->incentive_percentage ?? 0),
                ] : [
                    'basic_salary' => 0,
                    'target_value' => 0,
                    'incentive_percentage' => 0,
                ],
            ];
        }

        $response = ['data' => $responseData];

        if ($platform == 'web') {
            return apiResponse(0, 'Success', $response);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Success',
            'data' => $response,
        ]);
    }

    public function saveIncentiveData($data = null, $platform = 'web')
    {
        $incentives = $data['incentives'] ?? [];
        if (! is_array($incentives)) {
            $incentives = [];
        }

        // Keep only employees with an actual incentive amount
        $incentives = array_values(array_filter($incentives, function ($incentive) {
            $amount = round((float) ($incentive['incentiveAmount'] ?? 0), 2);
            $empId = $incentive['emp_id'] ?? null;

            return $empId && $amount > 0;
        }));

        if (empty($incentives)) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => 'No employees with incentive amount to save. Generate incentives only for employees who met target with invoiced sales.',
            ]);
        }

        $startDates = array_column($incentives, 'start_date');
        $endDates = array_column($incentives, 'end_date');

        $firstStartDate = $startDates[0] ?? null;
        $firstEndDate = $endDates[0] ?? null;

        if (empty($firstStartDate) || empty($firstEndDate)) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => 'Start date and end date are required.',
            ]);
        }

        if (strtotime($firstStartDate) > strtotime($firstEndDate)) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => 'Start date cannot be after end date.',
            ]);
        }

        $excludeMasterId = ! empty($data['incentive_master_id']) ? (int) $data['incentive_master_id'] : null;
        $overlap = $this->findOverlappingIncentiveMaster($firstStartDate, $firstEndDate, $excludeMasterId);
        if ($overlap) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => sprintf(
                    'Incentive already exists for overlapping dates (%s to %s). Choose a different date range.',
                    date('d-m-Y', strtotime($overlap->start_date)),
                    date('d-m-Y', strtotime($overlap->end_date))
                ),
                'data' => [
                    'overlap' => [
                        'incentive_master_id' => $overlap->incentive_master_id,
                        'start_date' => $overlap->start_date,
                        'end_date' => $overlap->end_date,
                    ],
                ],
            ]);
        }

        try {
            DB::beginTransaction();

            $incentiveMasterCreate = IncentiveMaster::updateOrCreate(
                ['incentive_master_id' => $data['incentive_master_id'] ?? null],
                [
                    'start_date' => $firstStartDate,
                    'end_date' => $firstEndDate,
                ]
            );

            $masterId = $incentiveMasterCreate->incentive_master_id;

            // Reverse previous ledger vouchers before regenerating rows
            $existingIncentives = Incentive::where('incentive_master_id', $masterId)->get();
            foreach ($existingIncentives as $existing) {
                if (! empty($existing->payment_transaction_group_id)) {
                    $this->incentivePaymentService->reverseForVoucher((int) $existing->payment_transaction_group_id);
                    $this->accountingService->reverseTransactionGroup(
                        $existing->payment_transaction_group_id,
                        $firstEndDate ?? now()->toDateString()
                    );
                }
                if (! empty($existing->transaction_group_id)) {
                    $this->accountingService->reverseTransactionGroup(
                        $existing->transaction_group_id,
                        $firstEndDate ?? now()->toDateString()
                    );
                }
            }
            Incentive::where('incentive_master_id', $masterId)->delete();

            $incentiveCreated = false;
            $voucherDate = $firstEndDate ?: now()->toDateString();

            foreach ($incentives as $incentive) {
                $salaryInfo = $incentive['salaryInfo'] ?? [];
                if (is_object($salaryInfo)) {
                    $salaryInfo = (array) $salaryInfo;
                }
                if (! is_array($salaryInfo)) {
                    $salaryInfo = [];
                }

                $incentiveAmount = round((float) ($incentive['incentiveAmount'] ?? 0), 2);
                $empId = $incentive['emp_id'] ?? null;
                $empName = $incentive['name'] ?? null;

                $basicSalary = (float) data_get($salaryInfo, 'basic_salary', $incentive['salary'] ?? 0);
                $targetAmount = (float) data_get($salaryInfo, 'target_value', $incentive['target_amount'] ?? 0);

                // Fallback to the employee's current active salary when payload is incomplete
                if ($empId && ($basicSalary <= 0 || $targetAmount <= 0)) {
                    $activeSalary = Salary::where('emp_id', $empId)
                        ->where('status', 'active')
                        ->orderBy('created_at', 'desc')
                        ->first();
                    if ($activeSalary) {
                        if ($basicSalary <= 0) {
                            $basicSalary = (float) ($activeSalary->basic_salary ?? 0);
                        }
                        if ($targetAmount <= 0) {
                            $targetAmount = (float) ($activeSalary->target_value ?? 0);
                        }
                    }
                }

                $group = $this->accountingService->recordIncentiveAccrual(
                    $empId,
                    $incentiveAmount,
                    "Incentive accrued for {$empName} ({$incentive['start_date']} to {$incentive['end_date']})",
                    $voucherDate
                );

                $incentiveRecord = Incentive::create([
                    'incentive_master_id' => $masterId,
                    'emp_id' => $empId,
                    'emp_name' => $empName,
                    'salary' => $basicSalary,
                    'start_date' => $incentive['start_date'] ?? null,
                    'end_date' => $incentive['end_date'] ?? null,
                    'total_amount' => $incentive['totalSplitAmount'] ?? 0.00,
                    'target_amount' => $targetAmount,
                    'incentive_percentage' => $incentive['incentivePercentage'] ?? 0.00,
                    'incentive_amount' => $incentiveAmount,
                    'incentive_status' => 'active',
                    'transaction_group_id' => $group?->id,
                    'paid_amount' => 0,
                    'payment_status' => 'unpaid',
                ]);

                if ($incentiveRecord) {
                    $incentiveCreated = true;
                }
            }

            if (! $incentiveCreated) {
                DB::rollBack();

                return response()->json([
                    'errorcode' => 1,
                    'errormessage' => 'No employees with incentive amount to save.',
                ]);
            }

            $errormessage = isset($data['incentive_master_id']) && $data['incentive_master_id'] > 0
                ? 'Incentive Updated Successfully.'
                : 'Incentive Created Successfully.';

            DB::commit();

            if ($platform === 'web') {
                return response()->json([
                    'errorcode' => 0,
                    'errormessage' => $errormessage,
                    'data' => Incentive::where('incentive_master_id', $masterId)->get(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in saveIncentiveData: '.$e->getMessage());
            DB::rollBack();

            return response()->json([
                'errorcode' => 25,
                'errormessage' => 'Something went wrong',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Find an existing incentive batch whose date range overlaps the given window.
     */
    protected function findOverlappingIncentiveMaster(string $startDate, string $endDate, ?int $excludeMasterId = null): ?IncentiveMaster
    {
        return IncentiveMaster::query()
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->when($excludeMasterId, function ($query) use ($excludeMasterId) {
                $query->where('incentive_master_id', '!=', $excludeMasterId);
            })
            ->orderByDesc('incentive_master_id')
            ->first();
    }

    public function newIncentiveList($data = null, $platform = 'web')
    {
        $result = IncentiveMaster::with([
            'incentives.paymentVoucher',
            'incentives.payments.transactionGroup',
        ]);

        if ($platform == 'web') {
            $month = $data->month ?? null;
            $year = $data->year ?? null;

            if (! empty($month) && ! empty($year)) {
                $monthNumber = date('m', strtotime("1 $month $year"));
                $startDate = date("$year-$monthNumber-01");
                $endDate = date('Y-m-t', strtotime($startDate));
                $result->where(function ($q) use ($startDate, $endDate) {
                    $q->whereDate('start_date', '<=', $endDate)
                        ->whereDate('end_date', '>=', $startDate);
                });
            }

            $result = $result->orderBy('created_at', 'DESC')->paginate(config('vocomi.settings.pagination.perpage.admin'));

            $result->getCollection()->transform(function ($master) {
                $rows = $master->incentives ?? collect();
                $master->employees_count = $rows->count();
                $master->total_incentive_amount = round((float) $rows->sum('incentive_amount'), 2);
                $master->total_paid_amount = round((float) $rows->sum('paid_amount'), 2);
                $master->total_due_amount = round($master->total_incentive_amount - $master->total_paid_amount, 2);
                $unpaid = $rows->where('payment_status', '!=', 'paid')->count();
                $master->payment_summary = $rows->isEmpty()
                    ? '—'
                    : ($unpaid === 0 ? 'paid' : ($unpaid === $rows->count() ? 'unpaid' : 'partial'));
                $master->payment_vouchers = $this->incentivePaymentService->voucherLinksForIncentives($rows);

                return $master;
            });

            $response['pagination'] = generatePaginationData($result);
            $response['data'] = $result;

            return apiResponse(0, 'success', $response);
        } else {
        }
    }

    public function incentiveList($data = null, $platform = 'web')
    {

        $result = Incentive::with('employee')->where('incentive_status', 'active');

        if ($platform == 'web') {

            if (! empty($data->emp_id)) {

                $result->where('emp_id', $data->emp_id);
            }
            $startDate = $data->start_date;
            $endDate = $data->end_date;
            if ($endDate != null && $startDate != null) {
                $result->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            } elseif ($endDate == null && $startDate != null) {
                $result->where('created_at', '>=', $startDate.' 00:00:00');
            } elseif ($endDate != null && $startDate == null) {
                $result->where('created_at', '<=', $endDate.' 23:59:59');
            } else {
            }

            $result = $result->orderBy('created_at', 'DESC')->paginate(config('vocomi.settings.pagination.perpage.admin'));

            $response['pagination'] = generatePaginationData($result);
            $response['data'] = $result;

            return apiResponse(0, 'success', $response);
            // return response()->json($response);
        } else {
        }
    }

    public function payroll_store($data = null, $platform = 'web')
    {
        // dd($data);
        $salaryInfo = Salary::with('employee')->where('emp_id', $data->emp_id)->where('status', 'active')->orderBy('created_at', 'desc')->first();

        try {
            DB::beginTransaction();

            $grossSalary = $salaryInfo->basic_salary
                + ($salaryInfo->basic_salary * $salaryInfo->HRA) / 100
                + ($salaryInfo->basic_salary * $salaryInfo->TA) / 100
                + ($salaryInfo->basic_salary * $salaryInfo->DA) / 100
                + $salaryInfo->special_allowance;

            $deductions = ($salaryInfo->basic_salary * $salaryInfo->professional_tax) / 100
                + ($salaryInfo->basic_salary * $salaryInfo->TDS) / 100;

            $netSalary = $grossSalary - $deductions;

            $payrollCreate = Payroll::updateOrCreate([
                'payroll_id' => $data->payroll_id,
            ], [
                'emp_id' => $data->emp_id,
                'date' => $data->date,
                'month' => $data->month,
                'year' => $data->year,
                'emp_name' => $salaryInfo->employee->name,
                'emp_account_no' => $salaryInfo->employee->acc_no ?? 0,
                // 'payment_mode' => $salaryInfo->employee,
                'basic_salary' => $salaryInfo->basic_salary,
                'HRA_percentage' => $salaryInfo->HRA,
                'HRA_amount' => ($salaryInfo->basic_salary * $salaryInfo->HRA) / 100,
                'TA_percentage' => $salaryInfo->TA,
                'TA_amount' => ($salaryInfo->basic_salary * $salaryInfo->TA) / 100,
                'DA_percentage' => $salaryInfo->DA,
                'DA_amount' => ($salaryInfo->basic_salary * $salaryInfo->DA) / 100,
                'special_allowance' => $salaryInfo->special_allowance,
                'professional_tax_percentage' => $salaryInfo->professional_tax,
                'professional_tax_amount' => ($salaryInfo->basic_salary * $salaryInfo->professional_tax) / 100,
                'TDS_percentage' => $salaryInfo->TDS,
                'deduction' => 0,
                'TDS_amount' => ($salaryInfo->basic_salary * $salaryInfo->TDS) / 100,
                'net_salary' => $netSalary,

            ]);

            if ($payrollCreate) {
                $errormessage = $data->payroll_id > 0 ? 'Payroll Updated Successfully.' : 'Payroll Created Successfully.';
                $errorcode = 0;
            } else {
                $errormessage = 'Database error...';
                $errorcode = 15;
            }

            DB::commit();

            if ($platform == 'web') {
                return apiResponse($errorcode, $errormessage, $data);
            } else {
                // return apiResponse($errorcode, $errormessage);
            }
        } catch (\Exception $e) {
            Log::debug($e);
            DB::rollBack();

            return apiResponse(25, $e);
        }
    }

    public function payrollList($data = null, $platform = 'web')
    {

        $result = Payroll::with('payments', 'employee');

        if ($platform == 'web') {

            if (! empty($data->emp_id)) {

                $result->where('emp_id', $data->emp_id);
            }
            if (! empty($data->month)) {

                $result->where('month', $data->month);
            }
            if (! empty($data->year)) {

                $result->where('year', $data->year);
            }

            $result = $result->orderBy('created_at', 'DESC')->paginate(config('vocomi.settings.pagination.perpage.admin'));

            $response['pagination'] = generatePaginationData($result);
            $response['data'] = $result;

            return apiResponse(0, 'success', $response);
            // return response()->json($response);
        } else {
        }
    }

    public function payrollPaymentList($data = null, $platform = 'web')
    {
        $result = PayrollPayments::with(['payroll.employee']);

        if ($platform == 'web') {

            if (! empty($data->payroll_id)) {
                $result->where('payroll_id', $data->payroll_id);
            }

            if (! empty($data->payroll_payment_id)) {
                $result->where('payroll_payment_id', $data->payroll_payment_id);
            }

            $result = $result->orderBy('created_at', 'DESC')->paginate(config('vocomi.settings.pagination.perpage.admin'));
            $response['pagination'] = generatePaginationData($result);
            $response['data'] = $result;

            return apiResponse(0, 'success', $response);
        }
    }

    public function commissionValue($data = null, $platform = 'web')
    {
        $employee = Employee::where('emp_id', $data->emp_id)->first();

        if (! $employee) {
            return apiResponse(10, 'Employee not found.', null);
        }

        $commissionInfo = round($this->sumBeauticianCommissionAmount(
            $employee->user_id,
            $data->start_date,
            $data->end_date
        ), 2);

        if ($commissionInfo <= 0) {
            return apiResponse(10, 'No Commission Amount.', null);
        }

        if ($platform == 'web') {
            return apiResponse(0, 'Success', $commissionInfo);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Success',
            'data' => $commissionInfo,
        ]);
    }

    public function commission_store($data = null, $platform = 'web')
    {
        try {
            DB::beginTransaction();

            $existing = null;
            if (! empty($data->commission_id)) {
                $existing = Commission::find($data->commission_id);
                if ($existing && ! empty($existing->transaction_group_id)) {
                    $this->accountingService->reverseTransactionGroup(
                        $existing->transaction_group_id,
                        $data->end_date ?? now()->toDateString()
                    );
                }
            }

            $commissionAmount = round((float) ($data->commission ?? 0), 2);
            $transactionGroupId = null;

            if ($commissionAmount > 0 && ! empty($data->emp_id)) {
                $employee = Employee::with('user')->find($data->emp_id);
                $empName = optional(optional($employee)->user)->name ?? ('Employee #'.$data->emp_id);
                $group = $this->accountingService->recordCommissionAccrual(
                    $data->emp_id,
                    $commissionAmount,
                    "Commission accrued for {$empName} ({$data->start_date} to {$data->end_date})",
                    $data->end_date ?? now()->toDateString()
                );
                $transactionGroupId = $group?->id;
            }

            $commissionCreate = Commission::updateOrCreate([
                'commission_id' => $data->commission_id,
            ], [
                'emp_id' => $data->emp_id,
                'start_date' => $data->start_date,
                'end_date' => $data->end_date,
                'commission_amount' => $commissionAmount,
                'commission_status' => $data->commission_status,
                'transaction_group_id' => $transactionGroupId,
            ]);

            if ($commissionCreate) {
                $errormessage = $data->commission_id > 0 ? 'Commission Updated Successfully.' : 'Commission Created Successfully.';
                $errorcode = 0;
            } else {
                $errormessage = 'Database error...';
                $errorcode = 15;
            }

            DB::commit();

            if ($platform == 'web') {
                return apiResponse($errorcode, $errormessage, $data);
            }
        } catch (\Exception $e) {
            Log::debug($e);
            DB::rollBack();

            return apiResponse(25, $e);
        }
    }

    public function commissionList($data = null, $platform = 'web')
    {

        $result = Commission::with('employee')->where('commission_status', 'active');

        if ($platform == 'web') {

            if (! empty($data->emp_id)) {

                $result->where('emp_id', $data->emp_id);
            }
            $startDate = $data->start_date;
            $endDate = $data->end_date;
            if ($endDate != null && $startDate != null) {
                $result->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            } elseif ($endDate == null && $startDate != null) {
                $result->where('created_at', '>=', $startDate.' 00:00:00');
            } elseif ($endDate != null && $startDate == null) {
                $result->where('created_at', '<=', $endDate.' 23:59:59');
            } else {
            }

            $result = $result->orderBy('created_at', 'DESC')->paginate(config('vocomi.settings.pagination.perpage.admin'));

            $response['pagination'] = generatePaginationData($result);
            $response['data'] = $result;

            return apiResponse(0, 'success', $response);
            // return response()->json($response);
        } else {
        }
    }

    public function generate_commission($data = null, $platform = 'web')
    {
        $startDate = $data->start_date ?? null;
        $endDate = $data->end_date ?? null;

        if (empty($startDate) || empty($endDate)) {
            return apiResponse(1, 'Start date and end date are required.', []);
        }

        if (strtotime($startDate) > strtotime($endDate)) {
            return apiResponse(1, 'Start date cannot be after end date.', []);
        }

        $excludeMasterId = ! empty($data->commission_master_id) ? (int) $data->commission_master_id : null;
        $overlap = $this->findOverlappingCommissionMaster($startDate, $endDate, $excludeMasterId);
        if ($overlap) {
            return apiResponse(
                1,
                sprintf(
                    'Commission already exists for overlapping dates (%s to %s). Choose a different date range.',
                    date('d-m-Y', strtotime($overlap->start_date)),
                    date('d-m-Y', strtotime($overlap->end_date))
                ),
                [
                    'overlap' => [
                        'commission_master_id' => $overlap->commission_master_id,
                        'start_date' => $overlap->start_date,
                        'end_date' => $overlap->end_date,
                    ],
                ]
            );
        }

        $employees = Employee::with('user')
            ->where('status', 'active')
            ->whereHas('user', function ($q) {
                $q->where('user_status', 'active')
                    ->whereHas('beautician', function ($bq) {
                        $bq->where('status', Beautician::STATUS_ACTIVE);
                    });
            })
            ->get();
        $responseData = [];

        foreach ($employees as $employee) {
            $totals = $this->sumBeauticianCommissionTotals(
                $employee->user_id,
                $startDate,
                $endDate
            );

            $responseData[] = [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'emp_id' => $employee->emp_id,
                'user_id' => $employee->user_id,
                'name' => optional($employee->user)->name ?? '',
                'total_amount' => round((float) $totals['total_amount'], 2),
                'commission_percentage' => round((float) $totals['avg_commission_percentage'], 2),
                'commissionInfo' => round((float) $totals['commission_amount'], 2),
            ];
        }

        $response = ['data' => $responseData];

        if ($platform == 'web') {
            return apiResponse(0, 'Success', $response);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Success',
            'data' => $response,
        ]);
    }

    public function saveCommissionData($data = null, $platform = 'web')
    {
        $commissions = $data['commissions'] ?? [];
        if (! is_array($commissions)) {
            $commissions = [];
        }

        // Keep only employees with an actual commission amount
        $commissions = array_values(array_filter($commissions, function ($commission) {
            $amount = round((float) ($commission['commissionInfo'] ?? 0), 2);
            $empId = $commission['emp_id'] ?? null;

            return $empId && $amount > 0;
        }));

        if (empty($commissions)) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => 'No employees with commission amount to save. Only employees with commission on invoiced sales can be saved.',
            ]);
        }

        $startDates = array_column($commissions, 'start_date');
        $endDates = array_column($commissions, 'end_date');

        $firstStartDate = $startDates[0] ?? null;
        $firstEndDate = $endDates[0] ?? null;

        if (empty($firstStartDate) || empty($firstEndDate)) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => 'Start date and end date are required.',
            ]);
        }

        if (strtotime($firstStartDate) > strtotime($firstEndDate)) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => 'Start date cannot be after end date.',
            ]);
        }

        $excludeMasterId = ! empty($data['commission_master_id']) ? (int) $data['commission_master_id'] : null;
        $overlap = $this->findOverlappingCommissionMaster($firstStartDate, $firstEndDate, $excludeMasterId);
        if ($overlap) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => sprintf(
                    'Commission already exists for overlapping dates (%s to %s). Choose a different date range.',
                    date('d-m-Y', strtotime($overlap->start_date)),
                    date('d-m-Y', strtotime($overlap->end_date))
                ),
                'data' => [
                    'overlap' => [
                        'commission_master_id' => $overlap->commission_master_id,
                        'start_date' => $overlap->start_date,
                        'end_date' => $overlap->end_date,
                    ],
                ],
            ]);
        }

        try {
            DB::beginTransaction();

            $commissionMasterCreate = CommissionMaster::updateOrCreate(
                ['commission_master_id' => $data['commission_master_id'] ?? null],
                [
                    'start_date' => $firstStartDate,
                    'end_date' => $firstEndDate,
                ]
            );

            $masterId = $commissionMasterCreate->commission_master_id;

            // Reverse previous ledger vouchers before regenerating rows
            $existingCommissions = Commission::where('commission_master_id', $masterId)->get();
            foreach ($existingCommissions as $existing) {
                if (! empty($existing->payment_transaction_group_id)) {
                    $this->commissionPaymentService->reverseForVoucher((int) $existing->payment_transaction_group_id);
                    $this->accountingService->reverseTransactionGroup(
                        $existing->payment_transaction_group_id,
                        $firstEndDate ?? now()->toDateString()
                    );
                }
                if (! empty($existing->transaction_group_id)) {
                    $this->accountingService->reverseTransactionGroup(
                        $existing->transaction_group_id,
                        $firstEndDate ?? now()->toDateString()
                    );
                }
            }
            Commission::where('commission_master_id', $masterId)->delete();

            $commissionCreated = false;
            $voucherDate = $firstEndDate ?: now()->toDateString();

            foreach ($commissions as $commission) {
                $commissionAmount = round((float) ($commission['commissionInfo'] ?? 0), 2);
                $empId = $commission['emp_id'] ?? null;
                $empName = $commission['name'] ?? null;

                $group = $this->accountingService->recordCommissionAccrual(
                    $empId,
                    $commissionAmount,
                    "Commission accrued for {$empName} ({$commission['start_date']} to {$commission['end_date']})",
                    $voucherDate
                );

                $commissionRecord = Commission::create([
                    'commission_master_id' => $masterId,
                    'emp_id' => $empId,
                    'emp_name' => $empName,
                    'start_date' => $commission['start_date'] ?? null,
                    'end_date' => $commission['end_date'] ?? null,
                    'commission_amount' => $commissionAmount,
                    'commission_status' => 'active',
                    'transaction_group_id' => $group?->id,
                    'paid_amount' => 0,
                    'payment_status' => 'unpaid',
                ]);

                if ($commissionRecord) {
                    $commissionCreated = true;
                }
            }

            if (! $commissionCreated) {
                DB::rollBack();

                return response()->json([
                    'errorcode' => 1,
                    'errormessage' => 'No employees with commission amount to save.',
                ]);
            }

            $errormessage = isset($data['commission_master_id']) && $data['commission_master_id'] > 0
                ? 'Commission Updated Successfully.'
                : 'Commission Created Successfully.';

            DB::commit();

            if ($platform === 'web') {
                return response()->json([
                    'errorcode' => 0,
                    'errormessage' => $errormessage,
                    'data' => Commission::where('commission_master_id', $masterId)->get(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in saveCommissionData: '.$e->getMessage());
            DB::rollBack();

            return response()->json([
                'errorcode' => 25,
                'errormessage' => 'Something went wrong',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate the current logged-in user's password.
     * Returns an error message string on failure, or null on success.
     */
    public function validateCurrentUserPassword($password): ?string
    {
        $user = auth('web')->user() ?: auth()->user();
        if (! $user) {
            return 'Unauthorized. Please refresh and log in again.';
        }

        if ($password === null || trim((string) $password) === '') {
            return 'Password is required.';
        }

        if (! \Illuminate\Support\Facades\Hash::check((string) $password, $user->password)) {
            return 'Incorrect password.';
        }

        return null;
    }

    /**
     * Find an existing commission batch whose date range overlaps the given window.
     */
    protected function findOverlappingCommissionMaster(string $startDate, string $endDate, ?int $excludeMasterId = null): ?CommissionMaster
    {
        return CommissionMaster::query()
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->when($excludeMasterId, function ($query) use ($excludeMasterId) {
                $query->where('commission_master_id', '!=', $excludeMasterId);
            })
            ->orderByDesc('commission_master_id')
            ->first();
    }

    public function commissionsList($data = null, $platform = 'web')
    {

        $result = CommissionMaster::with([
            'commissions.paymentVoucher',
            'commissions.payments.transactionGroup',
        ]);

        if ($platform == 'web') {

            $month = $data->month ?? null;
            $year = $data->year ?? null;

            if (! empty($month) && ! empty($year)) {
                // Convert month name to number (if needed)
                $monthNumber = date('m', strtotime("1 $month $year"));

                // Get first and last date of the selected month
                $startDate = date("$year-$monthNumber-01");
                $endDate = date('Y-m-t', strtotime($startDate)); // Get last date of month
                $result->where(function ($q) use ($startDate, $endDate) {
                    $q->whereDate('start_date', '<=', $endDate)
                        ->whereDate('end_date', '>=', $startDate);
                });
            }

            $result = $result->orderBy('created_at', 'DESC')->paginate(config('vocomi.settings.pagination.perpage.admin'));

            $result->getCollection()->transform(function ($master) {
                $rows = $master->commissions ?? collect();
                $master->employees_count = $rows->count();
                $master->total_commission_amount = round((float) $rows->sum('commission_amount'), 2);
                $master->total_paid_amount = round((float) $rows->sum('paid_amount'), 2);
                $master->total_due_amount = round($master->total_commission_amount - $master->total_paid_amount, 2);
                $unpaid = $rows->where('payment_status', '!=', 'paid')->count();
                $master->payment_summary = $rows->isEmpty()
                    ? '—'
                    : ($unpaid === 0 ? 'paid' : ($unpaid === $rows->count() ? 'unpaid' : 'partial'));
                $master->payment_vouchers = $this->commissionPaymentService->voucherLinksForCommissions($rows);

                return $master;
            });

            $response['pagination'] = generatePaginationData($result);
            $response['data'] = $result;

            return apiResponse(0, 'success', $response);
            // return response()->json($response);
        } else {
        }
    }

    public function viewCommission($data = null, $platform = 'web')
    {

        $result = Commission::with('employee.user.split')->where('commission_id', $data['commission_id'])->first();
        if ($platform == 'web') {
            return apiResponse(0, 'success', $result);
            // return response()->json($response);
        } else {
        }
    }

    public function viewPayroll($data = null, $platform = 'web')
    {

        $result = Payroll::where('payroll_id', $data['payroll_id'])->first();
        if ($platform == 'web') {
            return successResponse($result, 'success', 0);
            // return response()->json($response);
        } else {
        }
    }

    public function advance_salary_store($data = null, $platform = 'web')
    {
        try {
            $empId = (int) ($data->emp_id ?? 0);
            $advanceAmount = round((float) ($data->advance_amount ?? 0), 2);
            $paymentMode = strtolower((string) ($data->payment_mode ?? ''));
            $voucherDate = $data->voucher_date ?? null;

            if ($empId <= 0) {
                return apiResponse(1, 'Employee is required.', []);
            }
            if ($advanceAmount <= 0) {
                return apiResponse(1, 'Advance amount must be greater than zero.', []);
            }
            if (! in_array($paymentMode, ['cash', 'bank'], true)) {
                return apiResponse(1, 'Payment mode must be cash or bank.', []);
            }
            if (empty($voucherDate)) {
                return apiResponse(1, 'Voucher date is required.', []);
            }

            // Check if employee has salary defined
            $salary = Salary::where('emp_id', $empId)
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->first();

            if (! $salary) {
                return apiResponse(1, 'No active salary found for this employee. Please define salary first before giving advance salary.', []);
            }

            $oldAdvance = null;
            if (! empty($data->emp_salary_advance_id)) {
                $oldAdvance = EmployeeSalaryAdvance::find($data->emp_salary_advance_id);
            }

            $availableBalance = (float) $this->accountingService->getCashOrBankBalance($paymentMode);
            if ($oldAdvance && strtolower((string) $oldAdvance->payment_mode) === $paymentMode) {
                $availableBalance += (float) $oldAdvance->advance_amount;
            }
            if ($advanceAmount > $availableBalance + 0.0001) {
                return apiResponse(
                    1,
                    sprintf(
                        '%s has insufficient balance. Available: %.2f, Required: %.2f',
                        $paymentMode === 'cash' ? 'Cash' : 'Bank',
                        $availableBalance,
                        $advanceAmount
                    ),
                    []
                );
            }

            DB::beginTransaction();
            // Ensure advance salary ledger exists
            $this->accountingService->ensureEmployeeAdvanceLedger($empId);

            $employee = Employee::with('user')->find($empId);
            $empName = optional(optional($employee)->user)->name
                ?? optional($employee)->name
                ?? ('Employee #'.$empId);

            $advanceSalary = EmployeeSalaryAdvance::updateOrCreate([
                'emp_salary_advance_id' => $data->emp_salary_advance_id,
            ], [
                'year' => $data->year,
                'month' => $data->month,
                'voucher_date' => $voucherDate,
                'emp_id' => $empId,
                'advance_amount' => $advanceAmount,
                'payment_mode' => $paymentMode,
                'narrations' => $data->narrations,
                'status' => 'active',
            ]);

            if ($oldAdvance) {
                // Reverse old entry on original voucher date
                $this->accountingService->reverseAdvanceSalary(
                    $oldAdvance->advance_amount,
                    'Reversal of: Salary advance for '.$empName,
                    $oldAdvance->voucher_date ?: now()->toDateString(),
                    $oldAdvance->emp_id,
                    $oldAdvance->payment_mode
                );
            }

            // Record accounting entry
            $this->accountingService->recordAdvanceSalary(
                $advanceAmount,
                "Salary advance for {$empName}",
                $voucherDate,
                $empId,
                $paymentMode
            );

            if ($advanceSalary) {
                $errormessage = ! empty($data->emp_salary_advance_id) && $data->emp_salary_advance_id > 0
                    ? 'Salary Advance Updated Successfully.'
                    : 'Salary Advance Created Successfully.';
                $errorcode = 0;
            } else {
                $errormessage = 'Database error...';
                $errorcode = 15;
            }

            DB::commit();

            if ($platform == 'web') {
                return apiResponse($errorcode, $errormessage, $advanceSalary);
            } else {
                // return apiResponse($errorcode, $errormessage);
            }
        } catch (\Exception $e) {
            return $this->paymentFailureResponse($e, 'Salary advance');
        }
    }

    public function advanceSalaryList($data = null, $platform = 'web')
    {
        $result = EmployeeSalaryAdvance::with('employee.user')->where('status', 'active');

        if ($platform == 'web') {

            if (! empty($data->emp_id)) {
                $result->where('emp_id', $data->emp_id);
            }
            if (! empty($data->filter_start_date)) {
                $result->whereDate('voucher_date', '>=', $data->filter_start_date);
            }
            if (! empty($data->filter_end_date)) {
                $result->whereDate('voucher_date', '<=', $data->filter_end_date);
            }
            if (! empty($data->filter_payment_mode)) {
                $result->where('payment_mode', $data->filter_payment_mode);
            }

            $totalAmount = (float) (clone $result)->sum('advance_amount');

            $result = $result->orderByDesc('voucher_date')
                ->orderByDesc('emp_salary_advance_id')
                ->paginate(config('vocomi.settings.pagination.perpage.admin'));

            $response['pagination'] = generatePaginationData($result);
            $response['data'] = $result;
            $response['summary'] = [
                'total_amount' => round($totalAmount, 2),
            ];

            return apiResponse(0, 'success', $response);
        } else {
        }
    }

    public function getSalaryByEmployeeId($data = null, $platform = 'web')
    {
        try {
            if (empty($data->emp_id)) {
                return apiResponse(1, 'Employee ID is required', []);
            }

            $salary = Salary::where('emp_id', $data->emp_id)
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($salary) {
                $advanceLedger = $this->accountingService->employeeAdvanceLedgerSummary((int) $data->emp_id);
                $salary->setAttribute('advance_ledger', $advanceLedger);
                $response['data'] = $salary;

                return apiResponse(0, 'Salary found', $response);
            } else {
                return apiResponse(1, 'No active salary found for this employee', []);
            }
        } catch (\Exception $e) {
            return apiResponse(1, 'Error', $e->getMessage());
        }
    }

    /**
     * Amount of the employee's salary-advance ledger that this payroll can recover.
     * The ledger balance already includes advances posted from payment vouchers.
     */
    protected function recoverableAdvanceForPayroll(int $empId, float $payableBeforeAdvance): float
    {
        $outstanding = round(max(0, $this->accountingService->outstandingEmployeeAdvance($empId)), 2);
        $payableBeforeAdvance = round(max(0, $payableBeforeAdvance), 2);

        return round(min($outstanding, $payableBeforeAdvance), 2);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function calculateEmployeePayrollPreview(
        Employee $employee,
        string $month,
        int $year,
        string $voucherDate,
        string $scheduleDate,
        float $paidLeaveLimit,
        float $salaryDaysInMonth
    ): ?array {
        $monthNumber = (int) date('m', strtotime("1 {$month} {$year}"));
        $startDate = Carbon::create($year, $monthNumber, 1)->startOfDay();
        $endDate = (clone $startDate)->endOfMonth()->endOfDay();
        $payrollMonthEnd = $endDate->toDateString();

        if ($employee->join_date && $employee->join_date > $payrollMonthEnd) {
            return null;
        }

        $salaryInfo = Salary::where('emp_id', $employee->emp_id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $salaryInfo) {
            return null;
        }

        $basicSalary = (float) ($salaryInfo->basic_salary ?? 0);
        $HRA = (float) ($salaryInfo->HRA ?? 0);
        $TA = (float) ($salaryInfo->TA ?? 0);
        $DA = (float) ($salaryInfo->DA ?? 0);
        $specialAllowance = (float) ($salaryInfo->special_allowance ?? 0);
        $professionalTax = (float) ($salaryInfo->professional_tax ?? 0);
        $TDS = (float) ($salaryInfo->TDS ?? 0);

        $totalLeaves = EmployeeAttendence::where('employee_id', $employee->emp_id)
            ->where('attendance_status', 'absent')
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->count();

        $approveLeaves = LeaveManagement::where('emp_id', $employee->emp_id)
            ->where('leave_status', 'approved')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('leave_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orWhereBetween('leave_date_end', [$startDate->toDateString(), $endDate->toDateString()]);
            })
            ->count();

        $overtimes = EmployeeAttendence::where('employee_id', $employee->emp_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('overtime')
            ->pluck('overtime');

        $totalSeconds = 0;
        foreach ($overtimes as $time) {
            if (! is_string($time) || substr_count($time, ':') < 2) {
                continue;
            }
            [$hours, $minutes, $seconds] = array_pad(explode(':', $time), 3, 0);
            $totalSeconds += ((int) $hours * 3600) + ((int) $minutes * 60) + (int) $seconds;
        }

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;
        $totalOvertime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        $overtimeHoursDecimal = $totalSeconds / 3600;
        $overtimeRatePerHour = is_numeric($employee->hourly_basic_pay) ? (float) $employee->hourly_basic_pay : 0;
        $overtimeAmount = round($overtimeHoursDecimal * $overtimeRatePerHour, 2);

        $professionalTaxAmount = round(($basicSalary * $professionalTax) / 100, 2);
        $TDSAmount = round(($basicSalary * $TDS) / 100, 2);

        $grossSalary = round($basicSalary + $HRA + $TA + $DA + $specialAllowance, 2);
        $netSalary = round($grossSalary - $professionalTaxAmount - $TDSAmount, 2);

        $leaveDeduction = 0;
        if ($totalLeaves > $paidLeaveLimit && $salaryDaysInMonth > 0) {
            $salaryPerDay = $netSalary / $salaryDaysInMonth;
            $leaveDeduction = round($salaryPerDay * ($totalLeaves - $paidLeaveLimit), 2);
        }

        $payableBeforeAdvance = round($netSalary + $overtimeAmount - $leaveDeduction, 2);
        if ($payableBeforeAdvance < 0) {
            $payableBeforeAdvance = 0;
        }
        $advanceSalary = $this->recoverableAdvanceForPayroll((int) $employee->emp_id, $payableBeforeAdvance);
        $payableAmount = round(max(0, $payableBeforeAdvance - $advanceSalary), 2);

        return [
            'emp_id' => $employee->emp_id,
            'name' => $employee->name,
            'month' => $month,
            'year' => $year,
            'voucher_date' => $voucherDate,
            'schedule_date' => $scheduleDate,
            'basic_salary' => $basicSalary,
            'HRA' => $HRA,
            'TA' => $TA,
            'DA' => $DA,
            'special_allowance' => $specialAllowance,
            'professional_tax_percentage' => $professionalTax,
            'professional_tax_amount' => $professionalTaxAmount,
            'TDS_percentage' => $TDS,
            'TDS_amount' => $TDSAmount,
            'advance' => $advanceSalary,
            'total_leave' => $totalLeaves,
            'approve_leave' => $approveLeaves,
            'deduction' => $leaveDeduction,
            'net_salary' => $netSalary,
            'payable_amount' => $payableAmount,
            'overtime' => $totalOvertime,
            'overtime_amount' => $overtimeAmount,
            'gross_salary' => $grossSalary,
        ];
    }

    protected function findOverlappingPayrollMaster(string $month, int $year, ?int $excludeMasterId = null): ?PayrollMaster
    {
        return PayrollMaster::query()
            ->where('month', $month)
            ->where('year', $year)
            ->when($excludeMasterId, function ($query) use ($excludeMasterId) {
                $query->where('payroll_master_id', '!=', $excludeMasterId);
            })
            ->first();
    }

    public function generatePayrollPreview($data = null, $platform = 'web')
    {
        $month = $data->month ?? null;
        $year = (int) ($data->year ?? 0);
        $voucherDate = $data->voucher_date ?? null;
        $scheduleDate = $data->schedule_date ?? null;

        if (empty($month) || $year <= 0 || empty($voucherDate) || empty($scheduleDate)) {
            return apiResponse(1, 'Month, year, voucher date and schedule date are required.', []);
        }

        $excludeMasterId = ! empty($data->payroll_master_id) ? (int) $data->payroll_master_id : null;
        $overlap = $this->findOverlappingPayrollMaster($month, $year, $excludeMasterId);
        if ($overlap) {
            return apiResponse(
                1,
                sprintf(
                    'Payroll already exists for %s %s. Edit the existing batch or choose a different period.',
                    $month,
                    $year
                ),
                [
                    'overlap' => [
                        'payroll_master_id' => $overlap->payroll_master_id,
                        'month' => $overlap->month,
                        'year' => $overlap->year,
                    ],
                ]
            );
        }

        $businessConfigs = BusinessConfig::pluck('config_value', 'config_key');
        $paidLeaveLimit = (float) ($businessConfigs['paid_leave_limit'] ?? 0);
        $salaryDaysInMonth = (float) ($businessConfigs['salary_days_in_a_month'] ?? 30);

        $employees = Employee::where('status', 'active')->get();
        $responseData = [];

        foreach ($employees as $employee) {
            $row = $this->calculateEmployeePayrollPreview(
                $employee,
                $month,
                $year,
                $voucherDate,
                $scheduleDate,
                $paidLeaveLimit,
                $salaryDaysInMonth
            );

            if ($row) {
                $responseData[] = $row;
            }
        }

        if (empty($responseData)) {
            return apiResponse(1, 'No eligible employees with active salary master found.', []);
        }

        return apiResponse(0, 'Success', ['data' => $responseData]);
    }

    public function savePayrollData($data = null, $platform = 'web')
    {
        $payrolls = $data['payrolls'] ?? [];
        if (! is_array($payrolls)) {
            $payrolls = [];
        }

        $payrolls = array_values(array_filter($payrolls, function ($row) {
            return ! empty($row['emp_id']);
        }));

        if (empty($payrolls)) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => 'No employee payroll rows to save.',
            ]);
        }

        $month = $data['month'] ?? ($payrolls[0]['month'] ?? null);
        $year = (int) ($data['year'] ?? ($payrolls[0]['year'] ?? 0));
        $voucherDate = $data['voucher_date'] ?? ($payrolls[0]['voucher_date'] ?? null);
        $scheduleDate = $data['schedule_date'] ?? ($payrolls[0]['schedule_date'] ?? null);

        if (empty($month) || $year <= 0 || empty($voucherDate) || empty($scheduleDate)) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => 'Month, year, voucher date and schedule date are required.',
            ]);
        }

        $excludeMasterId = ! empty($data['payroll_master_id']) ? (int) $data['payroll_master_id'] : null;
        $overlap = $this->findOverlappingPayrollMaster($month, $year, $excludeMasterId);
        if ($overlap) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => sprintf(
                    'Payroll already exists for %s %s. Choose a different period.',
                    $month,
                    $year
                ),
            ]);
        }

        try {
            DB::beginTransaction();

            $payrollMaster = PayrollMaster::updateOrCreate(
                ['payroll_master_id' => $data['payroll_master_id'] ?? null],
                [
                    'month' => $month,
                    'year' => $year,
                    'voucher_date' => $voucherDate,
                    'schedule_date' => $scheduleDate,
                ]
            );

            $masterId = $payrollMaster->payroll_master_id;

            $existingPayrolls = Payroll::where('payroll_master_id', $masterId)->get();
            foreach ($existingPayrolls as $existing) {
                if (PayrollPayments::where('payroll_id', $existing->payroll_id)->exists()) {
                    DB::rollBack();

                    return response()->json([
                        'errorcode' => 1,
                        'errormessage' => 'Cannot regenerate payroll after salary payments have been recorded.',
                    ]);
                }

                $employee = Employee::where('emp_id', $existing->emp_id)->first();
                $employeeName = $employee?->name ?? ('Employee #'.$existing->emp_id);
                $oldGross = round(
                    (float) $existing->basic_salary
                    + (float) $existing->HRA
                    + (float) $existing->TA
                    + (float) $existing->DA
                    + (float) $existing->special_allowance,
                    2
                );

                $this->accountingService->reversePayrollGeneration(
                    $existing->emp_id,
                    $oldGross,
                    (float) $existing->overtime_amount,
                    (float) $existing->deduction,
                    (float) $existing->TDS_amount,
                    (float) $existing->professional_tax_amount,
                    (float) $existing->advance,
                    "Payroll generated for {$employeeName} ({$existing->month} {$existing->year})",
                    $existing->voucher_date
                );
            }

            Payroll::where('payroll_master_id', $masterId)->delete();

            $payrollCreated = false;

            foreach ($payrolls as $row) {
                $empId = (int) ($row['emp_id'] ?? 0);
                if ($empId <= 0) {
                    continue;
                }

                $employee = Employee::where('emp_id', $empId)->first();
                if (! $employee) {
                    continue;
                }

                $basicSalary = round((float) ($row['basic_salary'] ?? 0), 2);
                $HRA = round((float) ($row['HRA'] ?? 0), 2);
                $TA = round((float) ($row['TA'] ?? 0), 2);
                $DA = round((float) ($row['DA'] ?? 0), 2);
                $specialAllowance = round((float) ($row['special_allowance'] ?? 0), 2);
                $TDSAmount = round((float) ($row['TDS_amount'] ?? 0), 2);
                $professionalTaxAmount = round((float) ($row['professional_tax_amount'] ?? 0), 2);
                $overtimeAmount = round((float) ($row['overtime_amount'] ?? 0), 2);
                $leaveDeduction = round((float) ($row['deduction'] ?? 0), 2);
                $advanceSalary = round((float) ($row['advance'] ?? 0), 2);
                $netSalary = round((float) ($row['net_salary'] ?? 0), 2);
                $payableAmount = round((float) ($row['payable_amount'] ?? 0), 2);
                $grossSalary = round((float) ($row['gross_salary'] ?? ($basicSalary + $HRA + $TA + $DA + $specialAllowance)), 2);

                $payroll = Payroll::create([
                    'payroll_master_id' => $masterId,
                    'year' => $year,
                    'month' => $month,
                    'voucher_date' => $voucherDate,
                    'schedule_date' => $scheduleDate,
                    'emp_id' => $empId,
                    'basic_salary' => $basicSalary,
                    'HRA' => $HRA,
                    'TA' => $TA,
                    'DA' => $DA,
                    'special_allowance' => $specialAllowance,
                    'professional_tax_percentage' => $row['professional_tax_percentage'] ?? 0,
                    'professional_tax_amount' => $professionalTaxAmount,
                    'TDS_percentage' => $row['TDS_percentage'] ?? 0,
                    'TDS_amount' => $TDSAmount,
                    'advance' => $advanceSalary,
                    'total_leave' => (int) ($row['total_leave'] ?? 0),
                    'approve_leave' => (int) ($row['approve_leave'] ?? 0),
                    'deduction' => $leaveDeduction,
                    'net_salary' => $netSalary,
                    'payable_amount' => $payableAmount,
                    'overtime' => $row['overtime'] ?? '00:00:00',
                    'overtime_amount' => $overtimeAmount,
                    'is_scheduled' => $payableAmount <= 0 ? 1 : 0,
                ]);

                $this->accountingService->recordPayrollGeneration(
                    $empId,
                    $grossSalary,
                    $overtimeAmount,
                    $leaveDeduction,
                    $TDSAmount,
                    $professionalTaxAmount,
                    $advanceSalary,
                    "Payroll generated for {$employee->name} ({$month} {$year})",
                    $voucherDate
                );

                if ($payroll) {
                    $payrollCreated = true;
                }
            }

            if (! $payrollCreated) {
                DB::rollBack();

                return response()->json([
                    'errorcode' => 1,
                    'errormessage' => 'No payroll rows were saved.',
                ]);
            }

            $errormessage = isset($data['payroll_master_id']) && $data['payroll_master_id'] > 0
                ? 'Payroll Updated Successfully.'
                : 'Payroll Created Successfully.';

            DB::commit();

            return response()->json([
                'errorcode' => 0,
                'errormessage' => $errormessage,
                'data' => Payroll::where('payroll_master_id', $masterId)->with('employee')->get(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in savePayrollData: '.$e->getMessage());
            DB::rollBack();

            return response()->json([
                'errorcode' => 25,
                'errormessage' => 'Something went wrong',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    protected function enrichPayrollRow(Payroll $payroll): Payroll
    {
        $payroll->has_payments = PayrollPayments::where('payroll_id', $payroll->payroll_id)->exists();

        $totalPayable = round((float) $payroll->payable_amount, 2);
        $payroll->total_payable_amount = $totalPayable;

        $lastPayment = PayrollPayments::where('payroll_id', $payroll->payroll_id)
            ->latest('payroll_payment_id')
            ->first();

        $payroll->paid_amount = round(
            max(0, (float) PayrollPayments::where('payroll_id', $payroll->payroll_id)->sum('payment_amount')),
            2
        );

        if ($lastPayment) {
            $due = round(max(0, (float) $lastPayment->balance_amount), 2);
            $payroll->due_amount = $due;
            $payroll->payable_amount = $due;

            if ($due <= 0) {
                $payroll->is_scheduled = 1;
            }
        } else {
            $payroll->due_amount = $totalPayable;
        }

        if ($payroll->paid_amount > 0 && $payroll->total_payable_amount <= 0) {
            $payroll->total_payable_amount = round($payroll->paid_amount + ($payroll->due_amount ?? 0), 2);
        }

        return $payroll;
    }

    public function payrollMastersList($data = null, $platform = 'web')
    {
        $result = PayrollMaster::with(['payrolls.employee', 'payrolls.payments.transactionGroup']);

        if (! empty($data->month)) {
            $result->where('month', $data->month);
        }

        if (! empty($data->year)) {
            $result->where('year', (int) $data->year);
        }

        $result = $result->orderBy('year', 'DESC')
            ->orderByRaw("FIELD(month,'January','February','March','April','May','June',
                        'July','August','September','October','November','December')")
            ->paginate(config('vocomi.settings.pagination.perpage.admin'));

        $result->getCollection()->transform(function ($master) {
            $rows = ($master->payrolls ?? collect())->map(function ($payroll) {
                return $this->enrichPayrollRow($payroll);
            });

            $master->setRelation('payrolls', $rows);
            $master->employees_count = $rows->count();
            $master->total_payable_amount = round((float) $rows->sum(function ($row) {
                return (float) $row->payable_amount + (float) ($row->paid_amount ?? 0);
            }), 2);
            $master->total_paid_amount = round((float) $rows->sum('paid_amount'), 2);
            $master->total_due_amount = round((float) $rows->sum('payable_amount'), 2);

            $unpaid = $rows->filter(function ($row) {
                return (int) $row->is_scheduled !== 1 && (float) $row->payable_amount > 0;
            })->count();

            $master->payment_summary = $rows->isEmpty()
                ? '—'
                : ($unpaid === 0 ? 'paid' : ($unpaid === $rows->count() ? 'unpaid' : 'partial'));
            $master->payment_vouchers = $this->payrollPaymentService->voucherLinksForPayrolls($rows);

            return $master;
        });

        $response['pagination'] = generatePaginationData($result);
        $response['data'] = $result;

        return apiResponse(0, 'success', $response);
    }

    public function getPayrollByMasterId($id)
    {
        $master = PayrollMaster::with(['payrolls.employee', 'payrolls.payments.transactionGroup'])
            ->where('payroll_master_id', $id)
            ->first();

        if (! $master) {
            return apiResponse(1, 'Payroll batch not found.', []);
        }

        $master->setRelation('payrolls', $master->payrolls->map(function ($payroll) {
            $payroll = $this->enrichPayrollRow($payroll);
            $payroll->name = optional($payroll->employee)->name;
            $payroll->payment_vouchers = $this->payrollPaymentService->voucherLinksForPayrolls([$payroll]);

            return $payroll;
        }));

        $master->payment_vouchers = $this->payrollPaymentService->voucherLinksForPayrolls($master->payrolls);

        return apiResponse(0, 'success', ['data' => [$master]]);
    }

    public function salaryPayrollStore($data = null, $platform = 'web')
    {
        try {
            DB::beginTransaction();

            $monthNumber = (int) date('m', strtotime($data->month));
            $startDate = Carbon::create((int) $data->year, $monthNumber, 1)->startOfDay();
            $endDate = (clone $startDate)->endOfMonth()->endOfDay();
            $payrollMonthEnd = $endDate->toDateString();

            $employees = Employee::where('status', 'active')
                ->whereDate('join_date', '<=', $payrollMonthEnd)
                ->get();

            $payrollCreated = false;
            $skippedExisting = 0;
            $skippedNoSalary = 0;

            $payrollMaster = PayrollMaster::updateOrCreate(
                ['month' => $data->month, 'year' => (int) $data->year],
                [
                    'voucher_date' => $data->voucher_date,
                    'schedule_date' => $data->schedule_date,
                ]
            );

            $business_configs = BusinessConfig::pluck('config_value', 'config_key');
            $paid_leave_limit = (float) ($business_configs['paid_leave_limit'] ?? 0);
            $salary_days_in_a_month = (float) ($business_configs['salary_days_in_a_month'] ?? 30);

            foreach ($employees as $employee) {
                $exists = Payroll::where('emp_id', $employee->emp_id)
                    ->where('month', $data->month)
                    ->where('year', $data->year)
                    ->exists();

                if ($exists) {
                    $skippedExisting++;
                    continue;
                }

                $salaryInfo = Salary::where('emp_id', $employee->emp_id)
                    ->where('status', 'active')
                    ->orderBy('created_at', 'desc')
                    ->first();

                if (! $salaryInfo) {
                    $skippedNoSalary++;
                    continue;
                }

                $basicSalary = (float) ($salaryInfo->basic_salary ?? 0);
                $HRA = (float) ($salaryInfo->HRA ?? 0);
                $TA = (float) ($salaryInfo->TA ?? 0);
                $DA = (float) ($salaryInfo->DA ?? 0);
                $specialAllowance = (float) ($salaryInfo->special_allowance ?? 0);
                $professionalTax = (float) ($salaryInfo->professional_tax ?? 0);
                $TDS = (float) ($salaryInfo->TDS ?? 0);

                $totalLeaves = EmployeeAttendence::where('employee_id', $employee->emp_id)
                    ->where('attendance_status', 'absent')
                    ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->count();

                $approveLeaves = LeaveManagement::where('emp_id', $employee->emp_id)
                    ->where('leave_status', 'approved')
                    ->where(function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('leave_date', [$startDate->toDateString(), $endDate->toDateString()])
                            ->orWhereBetween('leave_date_end', [$startDate->toDateString(), $endDate->toDateString()]);
                    })
                    ->count();

                $overtimes = EmployeeAttendence::where('employee_id', $employee->emp_id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->whereNotNull('overtime')
                    ->pluck('overtime');

                $totalSeconds = 0;
                foreach ($overtimes as $time) {
                    if (! is_string($time) || substr_count($time, ':') < 2) {
                        continue;
                    }
                    [$hours, $minutes, $seconds] = array_pad(explode(':', $time), 3, 0);
                    $totalSeconds += ((int) $hours * 3600) + ((int) $minutes * 60) + (int) $seconds;
                }

                $hours = floor($totalSeconds / 3600);
                $minutes = floor(($totalSeconds % 3600) / 60);
                $seconds = $totalSeconds % 60;
                $totalOvertime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

                $overtimeHoursDecimal = $totalSeconds / 3600;
                $overtimeRatePerHour = is_numeric($employee->hourly_basic_pay) ? (float) $employee->hourly_basic_pay : 0;
                $overtimeAmount = round($overtimeHoursDecimal * $overtimeRatePerHour, 2);

                $professionalTaxAmount = round(($basicSalary * $professionalTax) / 100, 2);
                $TDSAmount = round(($basicSalary * $TDS) / 100, 2);

                $grossSalary = round($basicSalary + $HRA + $TA + $DA + $specialAllowance, 2);
                $deductions = $professionalTaxAmount + $TDSAmount;
                $netSalary = round($grossSalary - $deductions, 2);

                $leaveDeduction = 0;
                if ($totalLeaves > $paid_leave_limit && $salary_days_in_a_month > 0) {
                    $salary_per_day = $netSalary / $salary_days_in_a_month;
                    $leaveDeduction = round($salary_per_day * ($totalLeaves - $paid_leave_limit), 2);
                }

                $payableBeforeAdvance = round($netSalary + $overtimeAmount - $leaveDeduction, 2);
                if ($payableBeforeAdvance < 0) {
                    $payableBeforeAdvance = 0;
                }
                $advanceSalary = $this->recoverableAdvanceForPayroll((int) $employee->emp_id, $payableBeforeAdvance);
                $payableAmount = round(max(0, $payableBeforeAdvance - $advanceSalary), 2);

                $payroll = Payroll::create([
                    'payroll_master_id' => $payrollMaster->payroll_master_id,
                    'year' => $data->year,
                    'month' => $data->month,
                    'voucher_date' => $data->voucher_date,
                    'schedule_date' => $data->schedule_date,
                    'emp_id' => $employee->emp_id,
                    'basic_salary' => $basicSalary,
                    'HRA' => $HRA,
                    'TA' => $TA,
                    'DA' => $DA,
                    'special_allowance' => $specialAllowance,
                    'professional_tax_percentage' => $professionalTax,
                    'professional_tax_amount' => $professionalTaxAmount,
                    'TDS_percentage' => $TDS,
                    'TDS_amount' => $TDSAmount,
                    'advance' => $advanceSalary,
                    'total_leave' => $totalLeaves,
                    'approve_leave' => $approveLeaves,
                    'deduction' => $leaveDeduction,
                    'net_salary' => $netSalary,
                    'payable_amount' => $payableAmount,
                    'overtime' => $totalOvertime,
                    'overtime_amount' => $overtimeAmount,
                    'is_scheduled' => $payableAmount <= 0 ? 1 : 0,
                ]);

                if ($payroll) {
                    $this->accountingService->recordPayrollGeneration(
                        $employee->emp_id,
                        $grossSalary,
                        $overtimeAmount,
                        $leaveDeduction,
                        $TDSAmount,
                        $professionalTaxAmount,
                        $advanceSalary,
                        "Payroll generated for {$employee->name} ({$data->month} {$data->year})",
                        $data->voucher_date
                    );

                    $payrollCreated = true;
                }
            }

            if ($payrollCreated) {
                $errormessage = 'Payroll Created Successfully.';
                if ($skippedExisting > 0 || $skippedNoSalary > 0) {
                    $errormessage .= " Skipped existing: {$skippedExisting}, no salary master: {$skippedNoSalary}.";
                }
                $errorcode = 0;
            } elseif ($skippedExisting > 0 && $skippedNoSalary === 0) {
                $errormessage = 'Payroll already exists for this month/year for all eligible employees.';
                $errorcode = 1;
            } elseif ($skippedNoSalary > 0) {
                $errormessage = 'No payroll created. Employees are missing an active salary master.';
                $errorcode = 1;
            } else {
                $errormessage = 'No eligible employees found for payroll generation.';
                $errorcode = 15;
            }

            DB::commit();

            return apiResponse($errorcode, $errormessage, $payrollCreated);
        } catch (\Exception $e) {
            Log::error('Payroll Error: '.$e->getMessage());
            DB::rollBack();

            return apiResponse(25, $e->getMessage());
        }
    }

    /**
     * Update an unpaid generated payroll (no payments yet): reverse old GL, save amounts, repost.
     */
    public function salaryPayrollUpdate($data = null, $platform = 'web')
    {
        try {
            DB::beginTransaction();

            $payroll = Payroll::where('payroll_id', $data->payroll_id)->first();
            if (! $payroll) {
                throw new \Exception('Payroll record not found.');
            }

            if (PayrollPayments::where('payroll_id', $payroll->payroll_id)->exists()) {
                throw new \Exception('Cannot edit payroll after a salary payment has been recorded.');
            }

            if ((int) $payroll->is_scheduled === 1) {
                throw new \Exception('Cannot edit a fully settled payroll.');
            }

            $employee = Employee::where('emp_id', $payroll->emp_id)->firstOrFail();
            $originalDescription = "Payroll generated for {$employee->name} ({$payroll->month} {$payroll->year})";
            $oldGross = round(
                (float) $payroll->basic_salary
                + (float) $payroll->HRA
                + (float) $payroll->TA
                + (float) $payroll->DA
                + (float) $payroll->special_allowance,
                2
            );

            $this->accountingService->reversePayrollGeneration(
                $payroll->emp_id,
                $oldGross,
                (float) $payroll->overtime_amount,
                (float) $payroll->deduction,
                (float) $payroll->TDS_amount,
                (float) $payroll->professional_tax_amount,
                (float) $payroll->advance,
                $originalDescription,
                $payroll->voucher_date
            );

            $basicSalary = round((float) ($data->basic_salary ?? $payroll->basic_salary), 2);
            $HRA = round((float) ($data->HRA ?? $payroll->HRA), 2);
            $TA = round((float) ($data->TA ?? $payroll->TA), 2);
            $DA = round((float) ($data->DA ?? $payroll->DA), 2);
            $specialAllowance = round((float) ($data->special_allowance ?? $payroll->special_allowance), 2);
            $TDSAmount = round((float) ($data->TDS_amount ?? $payroll->TDS_amount), 2);
            $professionalTaxAmount = round((float) ($data->professional_tax_amount ?? $payroll->professional_tax_amount), 2);
            $overtimeAmount = round((float) ($data->overtime_amount ?? $payroll->overtime_amount), 2);
            $leaveDeduction = round((float) ($data->deduction ?? $payroll->deduction), 2);
            $advanceSalary = round((float) ($data->advance ?? $payroll->advance), 2);
            $voucherDate = $data->voucher_date ?: $payroll->voucher_date;
            $scheduleDate = $data->schedule_date ?: $payroll->schedule_date;

            if ($basicSalary < 0 || $HRA < 0 || $TA < 0 || $DA < 0 || $specialAllowance < 0
                || $TDSAmount < 0 || $professionalTaxAmount < 0 || $overtimeAmount < 0
                || $leaveDeduction < 0 || $advanceSalary < 0) {
                throw new \Exception('Payroll amounts cannot be negative.');
            }

            $grossSalary = round($basicSalary + $HRA + $TA + $DA + $specialAllowance, 2);
            $netSalary = round($grossSalary - $TDSAmount - $professionalTaxAmount, 2);
            if ($netSalary < 0) {
                throw new \Exception('Tax deductions cannot exceed gross salary.');
            }

            $payableBeforeAdvance = round($netSalary + $overtimeAmount - $leaveDeduction, 2);
            if ($payableBeforeAdvance < 0) {
                $payableBeforeAdvance = 0;
            }
            $payableAmount = round(max(0, $payableBeforeAdvance - $advanceSalary), 2);

            $payroll->voucher_date = $voucherDate;
            $payroll->schedule_date = $scheduleDate;
            $payroll->basic_salary = $basicSalary;
            $payroll->HRA = $HRA;
            $payroll->TA = $TA;
            $payroll->DA = $DA;
            $payroll->special_allowance = $specialAllowance;
            $payroll->TDS_amount = $TDSAmount;
            $payroll->professional_tax_amount = $professionalTaxAmount;
            $payroll->advance = $advanceSalary;
            $payroll->deduction = $leaveDeduction;
            $payroll->net_salary = $netSalary;
            $payroll->payable_amount = $payableAmount;
            $payroll->overtime_amount = $overtimeAmount;
            $payroll->is_scheduled = $payableAmount <= 0 ? 1 : 0;
            $payroll->save();

            $this->accountingService->recordPayrollGeneration(
                $payroll->emp_id,
                $grossSalary,
                $overtimeAmount,
                $leaveDeduction,
                $TDSAmount,
                $professionalTaxAmount,
                $advanceSalary,
                "Payroll generated for {$employee->name} ({$payroll->month} {$payroll->year})",
                $voucherDate
            );

            DB::commit();

            return apiResponse(0, 'Payroll Updated Successfully.', $payroll->fresh('employee'));
        } catch (\Exception $e) {
            Log::error('Payroll Update Error: '.$e->getMessage());
            DB::rollBack();

            return apiResponse(25, $e->getMessage());
        }
    }

    // public function salaryPayrollList($data = null, $platform = 'web')
    // {
    //     $result = Payroll::query()->with('employee');

    //     if ($platform == 'web') {

    //         if (! empty($data->emp_id)) {
    //             $result->where('emp_id', $data->emp_id);
    //         }

    //         $result = $result->orderBy('created_at', 'DESC')->paginate(config('vocomi.settings.pagination.perpage.admin'));
    //         $response['pagination'] = generatePaginationData($result);
    //         $response['data'] = $result;

    //         return apiResponse(0, 'success', $response);
    //         // return response()->json($response);
    //     } else {
    //     }
    // }

    public function salaryPayrollList($data = null, $platform = 'web')
    {
        // $result = Payroll::query()->with('employee');

        // if ($platform == 'web') {

        //     if (!empty($data->emp_id)) {
        //         $result->where('emp_id', $data->emp_id);
        //     }
        $result = Payroll::query()->with('employee');

        if (! empty($data->emp_id)) {
            $result->where('emp_id', $data->emp_id);
        }

        if (! empty($data->month)) {
            $result->where('month', $data->month);
        }

        if (! empty($data->year)) {
            $result->where('year', $data->year);
        }

        $result = $result->orderBy('year', 'DESC')
            ->orderByRaw("FIELD(month,'January','February','March','April','May','June',
                        'July','August','September','October','November','December')")
            ->orderBy('created_at', 'DESC')
            ->paginate(config('vocomi.settings.pagination.perpage.admin'));

        // Calculate remaining payable amount
        $result->getCollection()->transform(function ($payroll) {

            $payroll->has_payments = PayrollPayments::where('payroll_id', $payroll->payroll_id)->exists();

            $lastPayment = PayrollPayments::where('payroll_id', $payroll->payroll_id)
                ->latest('payroll_payment_id')
                ->first();

            if ($lastPayment) {

                $payroll->payable_amount = $lastPayment->balance_amount;

                if ($lastPayment->balance_amount <= 0) {
                    $payroll->is_scheduled = 1;
                }

            }

            return $payroll;
        });

        $response['pagination'] = generatePaginationData($result);
        $response['data'] = $result;

        return apiResponse(0, 'success', $response);
    }

    // public function salaryPayrollScheduleUpdate($data = null, $platform = 'web')
    // {
    //     //  dd($data);
    //     try {
    //         DB::beginTransaction();

    //         $payrollPaymentCreate = PayrollPayments::updateOrCreate([
    //             'payroll_payment_id' => $data->payroll_payment_id,
    //         ], [
    //             'payroll_id' => $data->payroll_id,
    //             'payable_amount' => $data->payable_amount,
    //             'payment_date' => $data->payment_date,
    //             'payment_amount' => $data->payment_amount,
    //             'balance_amount' => $data->balance_amount,
    //             'narrations' => $data->narrations,
    //         ]);

    //         $payroll = Payroll::where('payroll_id', $payrollPaymentCreate->payroll_id)->first();
    //   // dd($payroll);
    //         if ($payroll) {
    //             // If balance_amount is zero, update is_scheduled to zero
    //             if ($data->balance_amount == 0) {
    //                 $payroll->is_scheduled = 1;
    //                 $payroll->save();
    //             }
    //         }

    //         // Create accounting entry
    //         if ($payrollPaymentCreate) {
    //             $employee = Employee::findOrFail($data->emp_id);

    //             // $salaryLedgerId = $this->accountingService
    //             //     ->ensureEmployeeSalaryLedger($data->emp_id);

    //             // $this->accountingService->recordEmployeeSalary(
    //             //     $data->payment_amount ?? 0,
    //             //     "Salary for employee #{$employee->emp_id}",
    //             //     $data->payment_date,
    //             //     $data->balance_amount ?? 0,
    //             //     $data->TDS_amount ?? 0,
    //             //     $data->professional_tax_amount ?? 0
    //             // );
    //             $this->accountingService->recordEmployeeSalary(
    //                 $data->emp_id,
    //                 $data->payment_amount ?? 0,
    //                 "Salary payment for {$employee->name}",
    //                 $data->payment_date,
    //                 $payroll->advance ?? 0,
    //                 $data->TDS_amount ?? 0,
    //                 $data->professional_tax_amount ?? 0
    //             );
    //         }

    //         if ($payrollPaymentCreate) {
    //             $errormessage = $data->payroll_payment_id > 0 ? 'Payment Updated Successfully.' : 'Payment Advance Created Successfully.';
    //             $errorcode = 0;
    //         } else {
    //             $errormessage = 'Database error...';
    //             $errorcode = 15;
    //         }

    //         DB::commit();

    //         if ($platform == 'web') {
    //             return apiResponse($errorcode, $errormessage, $payrollPaymentCreate);
    //         } else {
    //             // return apiResponse($errorcode, $errormessage);
    //         }
    //     } catch (\Exception $e) {
    //         Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
    //         DB::rollBack();

    //         return apiResponse(25, $e->getMessage());
    //     }
    // }

    public function salaryPayrollScheduleUpdate($data = null, $platform = 'web')
    {
        try {
            DB::beginTransaction();

            $oldPayment = null;
            if (! empty($data->payroll_payment_id)) {
                $oldPayment = PayrollPayments::find($data->payroll_payment_id);
            }

            $payroll = Payroll::where('payroll_id', $data->payroll_id)->first();
            if (! $payroll) {
                throw new \Exception('Payroll record not found.');
            }

            $empId = $data->emp_id ?: $payroll->emp_id;
            $paymentMode = $data->payment_mode
                ?: optional(Employee::find($empId))->payment_mode
                ?: 'bank';

            $paymentAmount = round((float) ($data->payment_amount ?? 0), 2);
            if ($paymentAmount <= 0) {
                throw new \Exception('Payment amount must be greater than zero.');
            }

            $employee = Employee::with('user')->where('emp_id', $empId)->firstOrFail();
            $salaryPaymentDescription = "Salary payment for {$employee->name}";

            if ($oldPayment) {
                if (! empty($oldPayment->transaction_group_id)) {
                    $this->payrollPaymentService->reverseForVoucher((int) $oldPayment->transaction_group_id);
                    $this->accountingService->reverseTransactionGroup(
                        (int) $oldPayment->transaction_group_id,
                        $data->payment_date ?: now()->toDateString()
                    );
                } else {
                    $this->accountingService->reverseEmployeeSalary(
                        $empId,
                        (float) $oldPayment->payment_amount,
                        'Reversal of: '.$salaryPaymentDescription,
                        $oldPayment->payment_date ?: $data->payment_date,
                        $oldPayment->payment_mode ?: $paymentMode
                    );
                    $oldPayment->delete();
                }
                $payroll->refresh();
            }

            $group = $this->accountingService->recordEmployeeSalary(
                $empId,
                $paymentAmount,
                $salaryPaymentDescription,
                $data->payment_date,
                $paymentMode
            );

            $payrollPaymentCreate = $this->payrollPaymentService->recordPayment(
                $payroll,
                $paymentAmount,
                $group,
                $data->payment_date,
                $paymentMode,
                $data->narrations ?? null
            );

            $errormessage = ! empty($data->payroll_payment_id)
                ? 'Payment Updated Successfully.'
                : 'Payment Created Successfully.';

            DB::commit();

            return apiResponse(0, $errormessage, $payrollPaymentCreate);
        } catch (\Exception $e) {
            return $this->paymentFailureResponse($e, 'Salary payment');
        }
    }

    public function salarySchedule($data = null, $platform = 'web')
    {
        try {
            DB::beginTransaction();

            $payroll = Payroll::where('payroll_id', $data->payroll_id)->first();
            if (! $payroll) {
                DB::rollBack();

                return apiResponse(15, 'Payroll not found.');
            }

            $payroll->is_scheduled = $data->is_scheduled ? 1 : 0;
            $payrollSave = $payroll->save();

            if ($payrollSave) {
                $errormessage = 'Salary Scheduled.';
                $errorcode = 0;
            } else {
                DB::rollBack();

                return apiResponse(15, 'Database error while updating payroll.');
            }

            DB::commit();

            return apiResponse($errorcode, $errormessage, $payroll);
        } catch (\Exception $e) {
            DB::rollBack();

            return apiResponse(25, 'An error occurred while updating payroll.', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Split sales for a beautician in a date range (appointment date preferred).
     */
    private function sumBeauticianSplitPrice($beauticianUserId, $startDate, $endDate): float
    {
        if (empty($beauticianUserId) || empty($startDate) || empty($endDate)) {
            return 0.0;
        }

        return (float) $this->beauticianSplitBaseQuery($beauticianUserId, $startDate, $endDate)
            ->sum('split_service_amounts.price');
    }

    private function sumBeauticianCommissionAmount($beauticianUserId, $startDate, $endDate): float
    {
        if (empty($beauticianUserId) || empty($startDate) || empty($endDate)) {
            return 0.0;
        }

        return (float) ($this->sumBeauticianCommissionTotals($beauticianUserId, $startDate, $endDate)['commission_amount'] ?? 0);
    }

    private function sumBeauticianCommissionTotals($beauticianUserId, $startDate, $endDate): array
    {
        // Prefer stored commission_amount; otherwise recalculate from price * effective %
        // (covers historical splits that were zeroed due to null commission date ranges)
        $row = $this->beauticianSplitBaseQuery($beauticianUserId, $startDate, $endDate)
            ->leftJoin('services', 'split_service_amounts.service_id', '=', 'services.service_id')
            ->leftJoin('stock_items', 'split_service_amounts.item_id', '=', 'stock_items.item_id')
            ->selectRaw("
                COALESCE(SUM(
                    CASE
                        WHEN split_service_amounts.commission_amount > 0 THEN split_service_amounts.commission_amount
                        ELSE split_service_amounts.price * COALESCE(
                            NULLIF(split_service_amounts.commission_percentage, 0),
                            NULLIF(booking_services.commission_percentage, 0),
                            NULLIF(booking_items.commission_percentage, 0),
                            NULLIF(services.commission_percentage, 0),
                            NULLIF(stock_items.commission_percentage, 0),
                            0
                        ) / 100
                    END
                ), 0) as commission_amount
            ")
            ->selectRaw('COALESCE(SUM(split_service_amounts.price), 0) as total_amount')
            ->selectRaw("
                COALESCE(AVG(
                    COALESCE(
                        NULLIF(split_service_amounts.commission_percentage, 0),
                        NULLIF(booking_services.commission_percentage, 0),
                        NULLIF(booking_items.commission_percentage, 0),
                        NULLIF(services.commission_percentage, 0),
                        NULLIF(stock_items.commission_percentage, 0),
                        0
                    )
                ), 0) as avg_commission_percentage
            ")
            ->first();

        return [
            'commission_amount' => round((float) ($row->commission_amount ?? 0), 2),
            'total_amount' => round((float) ($row->total_amount ?? 0), 2),
            'avg_commission_percentage' => round((float) ($row->avg_commission_percentage ?? 0), 2),
        ];
    }

    private function beauticianSplitBaseQuery($beauticianUserId, $startDate, $endDate)
    {
        $from = Carbon::parse($startDate)->toDateString();
        $to = Carbon::parse($endDate)->toDateString();

        return SplitServiceAmount::query()
            ->join('bookings', 'split_service_amounts.booking_id', '=', 'bookings.booking_id')
            ->leftJoin(
                'booking_services',
                'split_service_amounts.booking_service_id',
                '=',
                'booking_services.booking_service_id'
            )
            ->leftJoin(
                'booking_items',
                'split_service_amounts.booking_item_id',
                '=',
                'booking_items.booking_item_id'
            )
            ->where('split_service_amounts.beautician_id', $beauticianUserId)
            // Only count invoiced bookings (have booking/invoice code)
            ->whereNotNull('bookings.booking_code')
            ->where('bookings.booking_code', '!=', '')
            ->where('bookings.payment_status', 'completed')
            ->whereRaw(
                'COALESCE(
                    booking_services.appointment_date,
                    DATE(split_service_amounts.created_at),
                    DATE(bookings.created_at)
                ) BETWEEN ? AND ?',
                [$from, $to]
            );
    }

    /**
     * Stage-based incentive on basic salary once target is met.
     */
    private function calculateIncentiveForSales(Salary $salaryInfo, float $totalSplitAmount): array
    {
        $targetValue = (float) ($salaryInfo->target_value ?? 0);
        $basicSalary = (float) ($salaryInfo->basic_salary ?? 0);

        if ($targetValue <= 0 || $totalSplitAmount < $targetValue || $basicSalary <= 0) {
            return [
                'incentive_percentage' => 0,
                'incentive_amount' => 0,
            ];
        }

        if ($totalSplitAmount < ($targetValue + ($targetValue / 2))) {
            $stage = 'First Stage';
        } elseif ($totalSplitAmount < ($targetValue * 2)) {
            $stage = 'Second Stage';
        } else {
            $stage = 'Third Stage';
        }

        $incentiveTerm = IncentiveTerms::where('incentive_title', $stage)
            ->where('status', 'active')
            ->first();

        if (! $incentiveTerm) {
            return [
                'incentive_percentage' => 0,
                'incentive_amount' => 0,
            ];
        }

        $percentage = (float) $incentiveTerm->incentive_percentage;

        return [
            'incentive_percentage' => $percentage,
            'incentive_amount' => round(($basicSalary * $percentage) / 100, 2),
        ];
    }

    /**
     * Pay commission separately from salary payroll.
     */
    public function payCommission($data = null, $platform = 'web')
    {
        try {
            DB::beginTransaction();

            $commission = Commission::findOrFail($data->commission_id);
            $due = round((float) $commission->commission_amount - (float) $commission->paid_amount, 2);
            $payAmount = round((float) ($data->payment_amount ?? $due), 2);
            $paymentMode = $data->payment_mode ?: 'bank';
            $paymentDate = $data->payment_date ?: now()->toDateString();

            if ($payAmount <= 0) {
                throw new \Exception('Payment amount must be greater than zero.');
            }
            if ($payAmount > $due + 0.009) {
                throw new \Exception('Payment amount cannot exceed unpaid balance ('.$due.').');
            }

            $employee = Employee::with('user')->findOrFail($commission->emp_id);
            $empName = optional($employee->user)->name ?? $commission->emp_name ?? ('Employee #'.$commission->emp_id);

            $group = $this->accountingService->recordCommissionPayment(
                $commission->emp_id,
                $payAmount,
                "Commission payment for {$empName}",
                $paymentDate,
                $paymentMode
            );

            $this->commissionPaymentService->recordPayment(
                $commission,
                $payAmount,
                $group,
                $paymentDate,
                $paymentMode
            );

            DB::commit();

            return apiResponse(0, 'Commission payment recorded successfully.', $commission->fresh());
        } catch (\Exception $e) {
            return $this->paymentFailureResponse($e, 'Commission payment');
        }
    }

    /**
     * Pay incentive separately from salary payroll.
     */
    public function payIncentive($data = null, $platform = 'web')
    {
        try {
            DB::beginTransaction();

            $incentive = Incentive::findOrFail($data->incentive_id);
            $due = round((float) $incentive->incentive_amount - (float) $incentive->paid_amount, 2);
            $payAmount = round((float) ($data->payment_amount ?? $due), 2);
            $paymentMode = $data->payment_mode ?: 'bank';
            $paymentDate = $data->payment_date ?: now()->toDateString();

            if ($payAmount <= 0) {
                throw new \Exception('Payment amount must be greater than zero.');
            }
            if ($payAmount > $due + 0.009) {
                throw new \Exception('Payment amount cannot exceed unpaid balance ('.$due.').');
            }

            $employee = Employee::with('user')->findOrFail($incentive->emp_id);
            $empName = optional($employee->user)->name ?? $incentive->emp_name ?? ('Employee #'.$incentive->emp_id);

            $group = $this->accountingService->recordIncentivePayment(
                $incentive->emp_id,
                $payAmount,
                "Incentive payment for {$empName}",
                $paymentDate,
                $paymentMode
            );

            $this->incentivePaymentService->recordPayment(
                $incentive,
                $payAmount,
                $group,
                $paymentDate,
                $paymentMode
            );

            DB::commit();

            return apiResponse(0, 'Incentive payment recorded successfully.', $incentive->fresh());
        } catch (\Exception $e) {
            return $this->paymentFailureResponse($e, 'Incentive payment');
        }
    }
}
