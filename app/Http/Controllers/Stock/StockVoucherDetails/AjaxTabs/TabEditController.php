<?php

namespace App\Http\Controllers\Stock\StockVoucherDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionGroup;
use App\Models\StockVoucherEditLog;
use App\Models\Vendor;
use App\Repositories\TransactionGroupRepository;
use App\Service\AccountingService;
use App\Service\CommissionPaymentService;
use App\Service\EmployeeService;
use App\Service\IncentivePaymentService;
use App\Service\PayrollPaymentService;
use App\Service\VendorPaymentService;
use Facades\App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TabEditController extends Controller
{
    protected $accountingService;

    protected $commissionPaymentService;

    protected $incentivePaymentService;

    protected $payrollPaymentService;

    protected $vendorPaymentService;

    public function __construct(
        AccountingService $accountingService,
        CommissionPaymentService $commissionPaymentService,
        IncentivePaymentService $incentivePaymentService,
        PayrollPaymentService $payrollPaymentService,
        VendorPaymentService $vendorPaymentService
    ) {
        $this->accountingService = $accountingService;
        $this->commissionPaymentService = $commissionPaymentService;
        $this->incentivePaymentService = $incentivePaymentService;
        $this->payrollPaymentService = $payrollPaymentService;
        $this->vendorPaymentService = $vendorPaymentService;
    }

    public function index(Request $request, TransactionGroupRepository $transaction_group_repo, $id, $tab_name)
    {
        $stock_voucher_info = $this->findById($id, $transaction_group_repo);

        if ($id > 0 && $stock_voucher_info->isBookingVoucher()) {
            abort(403, __('Booking vouchers cannot be edited.'));
        }

        if (! ($id > 0)) {
            $stock_voucher_info->date = now()->toDateString();
            $stock_voucher_info->voucher_type = 'Journal';
            $stock_voucher_info->description = '';
        }

        $account = Account::orderBy('name')->get(['account_id', 'name', 'code']);

        $voucherTypes = collect(config('vocomi.settings.voucher_types'))
            ->map(function ($text, $typeId) {
                return ['id' => $typeId, 'text' => $text];
            })
            ->values();

        $transactions = Transaction::with('journal')
            ->where('transaction_group_id', $id)
            ->get();

        $sectionDr = $transactions->where('debit', '>', 0)->map(function ($t) {
            return [
                'account_id' => optional($t->journal)->account_id,
                'debit_amount' => (string) $t->debit,
                'remarks' => $t->remarks,
            ];
        })->values();

        $sectionCr = $transactions->where('credit', '>', 0)->map(function ($t) {
            return [
                'account_id' => optional($t->journal)->account_id,
                'credit_amount' => (string) $t->credit,
                'remarks' => $t->remarks,
            ];
        })->values();

        if ($sectionDr->isEmpty()) {
            $sectionDr = collect([[
                'account_id' => '',
                'debit_amount' => '',
                'remarks' => '',
            ]]);
        }

        if ($sectionCr->isEmpty()) {
            $sectionCr = collect([[
                'account_id' => '',
                'credit_amount' => '',
                'remarks' => '',
            ]]);
        }

        $commissionId = null;
        $commissionMasterId = null;
        $commissionPrefill = null;
        $commissionBatchPrefill = null;
        $incentiveId = null;
        $incentiveMasterId = null;
        $incentivePrefill = null;
        $incentiveBatchPrefill = null;
        $payrollId = null;
        $payrollMasterId = null;
        $payrollPrefill = null;
        $payrollBatchPrefill = null;
        $vendorId = null;
        $vendorPrefill = null;

        if ($id > 0 && $stock_voucher_info->reference_type === 'vendor_payment' && $stock_voucher_info->reference) {
            $vendorId = (int) $stock_voucher_info->reference;
            $vendor = Vendor::find($vendorId);
            if ($vendor) {
                $vendorPrefill = [
                    'vendor_id' => $vendor->vendor_id,
                    'vendor_name' => $vendor->name,
                    'due_amount' => $this->vendorPaymentService->getMaxPayable($vendor),
                ];
            }
        }

        if (! ($id > 0) && $request->filled('commission_master_id')) {
            $prefill = $this->commissionPaymentService->buildBatchPaymentVoucherPrefill(
                (int) $request->commission_master_id,
                $this->accountingService
            );

            if ($prefill) {
                $commissionMasterId = $prefill['commission_master_id'];
                $commissionBatchPrefill = [
                    'commission_master_id' => $prefill['commission_master_id'],
                    'batch_label' => $prefill['batch_label'],
                    'employees_count' => $prefill['employees_count'],
                    'due_amount' => $prefill['due_amount'],
                ];
                $stock_voucher_info->date = $prefill['stock_voucher_info']['date'];
                $stock_voucher_info->voucher_type = $prefill['stock_voucher_info']['voucher_type'];
                $stock_voucher_info->description = $prefill['stock_voucher_info']['description'];
                $sectionDr = collect($prefill['sectionDr'])->values();
                $sectionCr = collect($prefill['sectionCr'])->values();
            }
        } elseif (! ($id > 0) && $request->filled('commission_id')) {
            $prefill = $this->commissionPaymentService->buildPaymentVoucherPrefill(
                (int) $request->commission_id,
                $this->accountingService
            );

            if ($prefill) {
                $commissionId = $prefill['commission_id'];
                $commissionPrefill = [
                    'commission_id' => $prefill['commission_id'],
                    'employee_name' => $prefill['employee_name'],
                    'due_amount' => $prefill['due_amount'],
                ];
                $stock_voucher_info->date = $prefill['stock_voucher_info']['date'];
                $stock_voucher_info->voucher_type = $prefill['stock_voucher_info']['voucher_type'];
                $stock_voucher_info->description = $prefill['stock_voucher_info']['description'];
                $sectionDr = collect($prefill['sectionDr'])->values();
                $sectionCr = collect($prefill['sectionCr'])->values();
            }
        } elseif (! ($id > 0) && $request->filled('incentive_master_id')) {
            $prefill = $this->incentivePaymentService->buildBatchPaymentVoucherPrefill(
                (int) $request->incentive_master_id,
                $this->accountingService
            );

            if ($prefill) {
                $incentiveMasterId = $prefill['incentive_master_id'];
                $incentiveBatchPrefill = [
                    'incentive_master_id' => $prefill['incentive_master_id'],
                    'batch_label' => $prefill['batch_label'],
                    'employees_count' => $prefill['employees_count'],
                    'due_amount' => $prefill['due_amount'],
                ];
                $stock_voucher_info->date = $prefill['stock_voucher_info']['date'];
                $stock_voucher_info->voucher_type = $prefill['stock_voucher_info']['voucher_type'];
                $stock_voucher_info->description = $prefill['stock_voucher_info']['description'];
                $sectionDr = collect($prefill['sectionDr'])->values();
                $sectionCr = collect($prefill['sectionCr'])->values();
            }
        } elseif (! ($id > 0) && $request->filled('incentive_id')) {
            $prefill = $this->incentivePaymentService->buildPaymentVoucherPrefill(
                (int) $request->incentive_id,
                $this->accountingService
            );

            if ($prefill) {
                $incentiveId = $prefill['incentive_id'];
                $incentivePrefill = [
                    'incentive_id' => $prefill['incentive_id'],
                    'employee_name' => $prefill['employee_name'],
                    'due_amount' => $prefill['due_amount'],
                ];
                $stock_voucher_info->date = $prefill['stock_voucher_info']['date'];
                $stock_voucher_info->voucher_type = $prefill['stock_voucher_info']['voucher_type'];
                $stock_voucher_info->description = $prefill['stock_voucher_info']['description'];
                $sectionDr = collect($prefill['sectionDr'])->values();
                $sectionCr = collect($prefill['sectionCr'])->values();
            }
        } elseif (! ($id > 0) && $request->filled('payroll_master_id')) {
            $prefill = $this->payrollPaymentService->buildBatchPaymentVoucherPrefill(
                (int) $request->payroll_master_id,
                $this->accountingService
            );

            if ($prefill) {
                $payrollMasterId = $prefill['payroll_master_id'];
                $payrollBatchPrefill = [
                    'payroll_master_id' => $prefill['payroll_master_id'],
                    'batch_label' => $prefill['batch_label'],
                    'employees_count' => $prefill['employees_count'],
                    'due_amount' => $prefill['due_amount'],
                ];
                $stock_voucher_info->date = $prefill['stock_voucher_info']['date'];
                $stock_voucher_info->voucher_type = $prefill['stock_voucher_info']['voucher_type'];
                $stock_voucher_info->description = $prefill['stock_voucher_info']['description'];
                $sectionDr = collect($prefill['sectionDr'])->values();
                $sectionCr = collect($prefill['sectionCr'])->values();
            }
        } elseif (! ($id > 0) && $request->filled('payroll_id')) {
            $prefill = $this->payrollPaymentService->buildPaymentVoucherPrefill(
                (int) $request->payroll_id,
                $this->accountingService
            );

            if ($prefill) {
                $payrollId = $prefill['payroll_id'];
                $payrollPrefill = [
                    'payroll_id' => $prefill['payroll_id'],
                    'employee_name' => $prefill['employee_name'],
                    'due_amount' => $prefill['due_amount'],
                ];
                $stock_voucher_info->date = $prefill['stock_voucher_info']['date'];
                $stock_voucher_info->voucher_type = $prefill['stock_voucher_info']['voucher_type'];
                $stock_voucher_info->description = $prefill['stock_voucher_info']['description'];
                $sectionDr = collect($prefill['sectionDr'])->values();
                $sectionCr = collect($prefill['sectionCr'])->values();
            }
        } elseif (! ($id > 0) && $request->filled('vendor_id')) {
            $requestedVendorId = (int) $request->vendor_id;
            $prefill = $this->vendorPaymentService->buildPaymentVoucherPrefill(
                $requestedVendorId,
                $this->accountingService
            );

            if ($prefill) {
                $vendorId = $prefill['vendor_id'];
                $vendorPrefill = [
                    'vendor_id' => $prefill['vendor_id'],
                    'vendor_name' => $prefill['vendor_name'],
                    'due_amount' => $prefill['due_amount'],
                ];
                $stock_voucher_info->date = $prefill['stock_voucher_info']['date'];
                $stock_voucher_info->voucher_type = $prefill['stock_voucher_info']['voucher_type'];
                $stock_voucher_info->description = $prefill['stock_voucher_info']['description'];
                $sectionDr = collect($prefill['sectionDr'])->values();
                $sectionCr = collect($prefill['sectionCr'])->values();
            } else {
                $vendor = Vendor::find($requestedVendorId);
                if ($vendor) {
                    $vendorId = $requestedVendorId;
                    $vendorPrefill = [
                        'vendor_id' => $requestedVendorId,
                        'vendor_name' => $vendor->name,
                        'due_amount' => 0,
                        'error' => __('This vendor has no outstanding balance to pay.'),
                    ];
                }
            }
        }

        $cashAccountId = (int) getAccount('Cash');
        $bankAccountId = (int) getAccount('Bank');
        $originalCashCredit = 0.0;
        $originalBankCredit = 0.0;

        if ($id > 0) {
            foreach ($sectionCr as $cr) {
                $accountId = (int) ($cr['account_id'] ?? 0);
                $amount = round((float) ($cr['credit_amount'] ?? 0), 2);
                if ($accountId === $cashAccountId) {
                    $originalCashCredit += $amount;
                } elseif ($accountId === $bankAccountId) {
                    $originalBankCredit += $amount;
                }
            }
        }

        $details_edit_vue = [
            'id' => $id,
            'tab_name' => $tab_name,
            'cash_account_id' => $cashAccountId,
            'bank_account_id' => $bankAccountId,
            'cash_balance' => $this->accountingService->getCashOrBankBalance('cash'),
            'bank_balance' => $this->accountingService->getCashOrBankBalance('bank'),
            'original_cash_credit' => $originalCashCredit,
            'original_bank_credit' => $originalBankCredit,
            'commission_id' => $commissionId,
            'commission_master_id' => $commissionMasterId,
            'commission_prefill' => $commissionPrefill,
            'commission_batch_prefill' => $commissionBatchPrefill,
            'incentive_id' => $incentiveId,
            'incentive_master_id' => $incentiveMasterId,
            'incentive_prefill' => $incentivePrefill,
            'incentive_batch_prefill' => $incentiveBatchPrefill,
            'payroll_id' => $payrollId,
            'payroll_master_id' => $payrollMasterId,
            'payroll_prefill' => $payrollPrefill,
            'payroll_batch_prefill' => $payrollBatchPrefill,
            'vendor_id' => $vendorId,
            'vendor_prefill' => $vendorPrefill,
            'stock_voucher_info' => [
                'date' => $stock_voucher_info->date ?? now()->toDateString(),
                'voucher_type' => $stock_voucher_info->voucher_type ?? 'Journal',
                'description' => $stock_voucher_info->description ?? '',
                'voucher_no' => $stock_voucher_info->voucher_no ?? '',
            ],
            'accounts' => $account,
            'dropdowns' => [
                'voucherTypeList' => $voucherTypes,
                'accounts' => Account::accountsDropdownJson(),
            ],
            'urls' => [
                'button_cancel_url' => $vendorId
                    ? route('tab.vendors.vendor.details', ['vendor_id' => $vendorId, 'tab_name' => 'payment'])
                    : route('tab.stock.stock.voucher.list'),
                'form_save_url' => route('tab.stock.stock.voucher.save', ['id' => $id, 'tab_name' => $tab_name]),
                'account_balance_url' => url('/stock/stock-voucher/account-balance'),
            ],
        ];

        return view('stock.stock-voucher-details.ajax-tabs.tab-edit', compact('details_edit_vue', 'account', 'sectionDr', 'sectionCr'));
    }

    public function save(Request $request, TransactionGroupRepository $transaction_group_repo, EmployeeService $employeeService, $id, $tab_name)
    {
        if ((string) $request->id !== (string) $id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        $isCreate = ! ($id > 0);
        if ($isCreate && ! auth()->user()->can('voucher_create')) {
            abort(403, __('You do not have permission to create vouchers.'));
        }
        if (! $isCreate && ! auth()->user()->can('voucher_edit')) {
            abort(403, __('You do not have permission to edit vouchers.'));
        }

        $stock_voucher = $this->findById($id, $transaction_group_repo);

        if ($id > 0 && $stock_voucher->isBookingVoucher()) {
            abort(403, __('Booking vouchers cannot be edited.'));
        }

        $this->applyValidation($request);

        // Editing an existing voucher requires the current user's password and an edit reason
        $editReason = trim((string) $request->input('edit_reason', ''));
        if ($id > 0) {
            $request->validate([
                'auth_password' => 'required|string',
                'edit_reason' => 'required|string|max:1000',
            ], [
                'auth_password.required' => __('Password is required to update this voucher.'),
                'edit_reason.required' => __('Please enter a reason for this edit.'),
                'edit_reason.max' => __('Edit reason cannot exceed 1000 characters.'),
            ]);

            $passwordError = $employeeService->validateCurrentUserPassword($request->auth_password);
            if ($passwordError) {
                return response()->json([
                    'message' => $passwordError,
                    'js_notifications' => ['error' => $passwordError],
                    'errors' => ['auth_password' => [$passwordError]],
                ], 422);
            }
        }

        $sectionDr = json_decode($request->sectionDr, true) ?: [];
        $sectionCr = json_decode($request->sectionCr, true) ?: [];
        $entries = $this->buildValidatedEntries($sectionDr, $sectionCr);

        $group = DB::transaction(function () use ($request, $stock_voucher, $id, $entries, $editReason) {
            $preservedVoucherNo = null;
            $vendorIdForSync = $request->filled('vendor_id')
                ? (int) $request->vendor_id
                : (($id > 0 && $stock_voucher->reference_type === 'vendor_payment' && $stock_voucher->reference)
                    ? (int) $stock_voucher->reference
                    : null);

            if ($id > 0 && $stock_voucher->id) {
                $this->commissionPaymentService->reverseForVoucher((int) $id);
                $this->incentivePaymentService->reverseForVoucher((int) $id);
                $this->payrollPaymentService->reverseForVoucher((int) $id);
                $this->vendorPaymentService->reverseForVoucher((int) $id);

                $originalGroup = TransactionGroup::with('transactions.journal')->findOrFail($id);
                $preservedVoucherNo = $originalGroup->voucher_no;
                $existingTransactions = $originalGroup->transactions;
                $reverseEntries = [];

                foreach ($existingTransactions as $trans) {
                    if (! $trans->journal || ! $trans->journal->account_id) {
                        throw ValidationException::withMessages([
                            'sectionDr' => __('Unable to reverse voucher: a ledger line is missing its account.'),
                        ]);
                    }

                    $reverseEntries[] = [
                        'account_id' => $trans->journal->account_id,
                        'debit' => (float) $trans->credit,
                        'credit' => (float) $trans->debit,
                        'remarks' => 'Reversal: '.($trans->remarks ?? 'Voucher on update'),
                    ];
                }

                if (! empty($reverseEntries)) {
                    $reverseGroup = $this->accountingService->recordMultiEntryTransaction(
                        $reverseEntries,
                        'Reversal of Voucher Transaction #'.$originalGroup->id,
                        $request->date,
                        $originalGroup->voucher_type,
                        0
                    );
                    $reverseGroup->voucher_no = ($preservedVoucherNo ?: $reverseGroup->voucher_no).'-REV';
                    // Original lines are deleted below, so this reversal is an audit trail only.
                    // Leaving it in the books would change Cash and the other ledgers with no offsetting entry.
                    $reverseGroup->is_audit_only = true;
                    $reverseGroup->save();
                }

                $originalGroup->delete();
            }

            $this->accountingService->validateVoucherCashBankCredits($entries);

            if ($vendorIdForSync) {
                $this->vendorPaymentService->validatePaymentEntries(
                    $entries,
                    $vendorIdForSync,
                    $this->accountingService
                );
            }

            $group = $this->accountingService->recordMultiEntryTransaction(
                $entries,
                $request->description,
                $request->date,
                $request->voucher_type,
                1
            );

            if ($preservedVoucherNo) {
                $group->voucher_no = $preservedVoucherNo;
                $group->save();
            }

            $this->commissionPaymentService->syncFromVoucher(
                $group,
                $entries,
                (string) $request->voucher_type,
                (string) $request->date,
                $request->filled('commission_id') ? (int) $request->commission_id : null,
                $request->filled('commission_master_id') ? (int) $request->commission_master_id : null
            );

            $this->incentivePaymentService->syncFromVoucher(
                $group,
                $entries,
                (string) $request->voucher_type,
                (string) $request->date,
                $request->filled('incentive_id') ? (int) $request->incentive_id : null,
                $request->filled('incentive_master_id') ? (int) $request->incentive_master_id : null
            );

            $this->payrollPaymentService->syncFromVoucher(
                $group,
                $entries,
                (string) $request->voucher_type,
                (string) $request->date,
                $request->filled('payroll_id') ? (int) $request->payroll_id : null,
                $request->filled('payroll_master_id') ? (int) $request->payroll_master_id : null
            );

            $this->vendorPaymentService->syncFromVoucher(
                $group,
                $entries,
                (string) $request->voucher_type,
                (string) $request->date,
                $vendorIdForSync
            );

            if ($id > 0 && $editReason !== '') {
                $voucherNo = $group->voucher_no ?: $preservedVoucherNo;
                StockVoucherEditLog::query()
                    ->where(function ($q) use ($id, $voucherNo, $preservedVoucherNo) {
                        $q->where('transaction_group_id', $id);
                        $nos = array_values(array_filter([$voucherNo, $preservedVoucherNo]));
                        if ($nos) {
                            $q->orWhereIn('voucher_no', $nos);
                        }
                    })
                    ->update([
                        'transaction_group_id' => $group->id,
                        'voucher_no' => $voucherNo,
                    ]);

                StockVoucherEditLog::create([
                    'transaction_group_id' => $group->id,
                    'voucher_no' => $voucherNo,
                    'edit_reason' => $editReason,
                    'edited_by' => Auth::id(),
                ]);
            }

            return $group;
        });

        voco_flash()->success(__('Voucher details saved!'));

        $redirectUrl = ($request->filled('commission_id') || $request->filled('commission_master_id'))
            ? route('commission')
            : (($request->filled('incentive_id') || $request->filled('incentive_master_id'))
                ? route('incentive')
                : (($request->filled('payroll_id') || $request->filled('payroll_master_id'))
                    ? route('payroll')
                    : (($request->filled('vendor_id') || ($id > 0 && $stock_voucher->reference_type === 'vendor_payment' && $stock_voucher->reference))
                        ? route('tab.vendors.vendor.details', [
                            'vendor_id' => (int) ($request->vendor_id ?: $stock_voucher->reference),
                            'tab_name' => 'payment',
                        ])
                        : route('tab.stock.stock.voucher.list'))));

        return response()->json([
            'gotourl' => $redirectUrl,
            'voucher_id' => $group->id,
        ]);
    }

    public function checkPassword(Request $request, EmployeeService $employeeService)
    {
        $user = auth()->user();

        if (! $user->can('voucher_edit')) {
            return response()->json([
                'valid' => false,
                'statusText' => 'You do not have permission to edit.',
                'errorcode' => 403,
            ], 403);
        }

        $passwordError = $employeeService->validateCurrentUserPassword($request->password);
        if ($passwordError) {
            return response()->json([
                'valid' => false,
                'statusText' => $passwordError,
                'errorcode' => 401,
            ], 401);
        }

        // Mark unlock for this voucher (still require password again on save)
        if ($request->filled('voucher_id')) {
            session([
                'voucher_edit_unlock_id' => (int) $request->voucher_id,
                'voucher_edit_unlock_at' => now()->timestamp,
            ]);
        }

        return response()->json([
            'valid' => true,
            'statusText' => 'Validation successful.',
            'errorcode' => 0,
        ]);
    }

    public function accountBalance($account_id)
    {
        $account = Account::findOrFail($account_id);

        return response()->json([
            'account_id' => $account->account_id,
            'name' => $account->name,
            'balance' => round((float) $account->balance, 2),
        ]);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function applyValidation(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'voucher_type' => 'required|in:Payment,Receipt,Journal,Contra',
            'description' => 'required|string|max:500',
            'sectionDr' => 'required|string',
            'sectionCr' => 'required|string',
        ]);
    }

    /**
     * @return array<int, array{account_id:mixed,debit:float,credit:float,remarks:?string}>
     */
    private function buildValidatedEntries(array $sectionDr, array $sectionCr): array
    {
        $entries = [];
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        foreach ($sectionDr as $index => $dr) {
            $accountId = $dr['account_id'] ?? null;
            $amount = round((float) ($dr['debit_amount'] ?? 0), 2);

            if (empty($accountId)) {
                throw ValidationException::withMessages([
                    'sectionDr' => __('Debit row #:row requires an account.', ['row' => $index + 1]),
                ]);
            }

            if ($amount <= 0) {
                throw ValidationException::withMessages([
                    'sectionDr' => __('Debit row #:row requires an amount greater than zero.', ['row' => $index + 1]),
                ]);
            }

            $entries[] = [
                'account_id' => $accountId,
                'debit' => $amount,
                'credit' => 0,
                'remarks' => $dr['remarks'] ?? null,
            ];
            $totalDebit += $amount;
        }

        foreach ($sectionCr as $index => $cr) {
            $accountId = $cr['account_id'] ?? null;
            $amount = round((float) ($cr['credit_amount'] ?? 0), 2);

            if (empty($accountId)) {
                throw ValidationException::withMessages([
                    'sectionCr' => __('Credit row #:row requires an account.', ['row' => $index + 1]),
                ]);
            }

            if ($amount <= 0) {
                throw ValidationException::withMessages([
                    'sectionCr' => __('Credit row #:row requires an amount greater than zero.', ['row' => $index + 1]),
                ]);
            }

            $entries[] = [
                'account_id' => $accountId,
                'debit' => 0,
                'credit' => $amount,
                'remarks' => $cr['remarks'] ?? null,
            ];
            $totalCredit += $amount;
        }

        if (empty($entries)) {
            throw ValidationException::withMessages([
                'sectionDr' => __('At least one debit and one credit entry is required.'),
            ]);
        }

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            throw ValidationException::withMessages([
                'sectionDr' => __('Debits (:debit) must equal Credits (:credit).', [
                    'debit' => number_format($totalDebit, 2),
                    'credit' => number_format($totalCredit, 2),
                ]),
            ]);
        }

        return $entries;
    }

    private function findById($id, \App\Repositories\Base\BaseRepository $repo)
    {
        return ($id > 0) ? $repo->model()->findOrFail($id) : $repo->model();
    }
}
