<?php

namespace App\Service;

use App\Models\Employee;
use App\Models\EmployeeAccountLedger;
use App\Models\Payroll;
use App\Models\PayrollMaster;
use App\Models\PayrollPayments;
use App\Models\TransactionGroup;

class PayrollPaymentService
{
    public function payrollDue(Payroll $payroll): float
    {
        $lastPayment = PayrollPayments::query()
            ->where('payroll_id', $payroll->payroll_id)
            ->latest('payroll_payment_id')
            ->first();

        if ($lastPayment) {
            return max(0, round((float) $lastPayment->balance_amount, 2));
        }

        return max(0, round((float) $payroll->payable_amount, 2));
    }

    public function reverseForVoucher(int $transactionGroupId): void
    {
        $payments = PayrollPayments::where('transaction_group_id', $transactionGroupId)->get();

        foreach ($payments as $payment) {
            $payroll = Payroll::find($payment->payroll_id);
            if ($payroll) {
                $paymentAmount = round((float) $payment->payment_amount, 2);
                $currentDue = $this->payrollDue($payroll);
                $restoredDue = round($currentDue + $paymentAmount, 2);

                $payroll->payable_amount = $restoredDue;
                $payroll->is_scheduled = $restoredDue <= 0 ? 1 : 0;
                $payroll->save();
            }

            $payment->delete();
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function buildPaymentVoucherPrefill(int $payrollId, AccountingService $accountingService): ?array
    {
        $payroll = Payroll::with('employee')->find($payrollId);
        if (! $payroll) {
            return null;
        }

        $due = $this->payrollDue($payroll);
        if ($due <= 0) {
            return null;
        }

        $payableId = $accountingService->ensureEmployeeSalaryLedger($payroll->emp_id);
        $bankId = (int) getAccount('Bank');
        if ($bankId <= 0) {
            $bankId = (int) getAccount('Cash');
        }

        $employeeName = optional($payroll->employee)->name ?: ('Employee #'.$payroll->emp_id);

        return [
            'payroll_id' => $payroll->payroll_id,
            'employee_name' => $employeeName,
            'due_amount' => $due,
            'stock_voucher_info' => [
                'date' => now()->toDateString(),
                'voucher_type' => 'Payment',
                'description' => "Salary payment for {$employeeName} ({$payroll->month} {$payroll->year})",
            ],
            'sectionDr' => [[
                'account_id' => (int) $payableId,
                'debit_amount' => number_format($due, 2, '.', ''),
                'remarks' => 'Payroll #'.$payroll->payroll_id,
            ]],
            'sectionCr' => [[
                'account_id' => $bankId > 0 ? (int) $bankId : '',
                'credit_amount' => number_format($due, 2, '.', ''),
                'remarks' => '',
            ]],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function buildBatchPaymentVoucherPrefill(int $masterId, AccountingService $accountingService): ?array
    {
        $master = PayrollMaster::with(['payrolls.employee'])->find($masterId);
        if (! $master) {
            return null;
        }

        $sectionDr = [];
        $totalDue = 0.0;

        foreach ($master->payrolls as $payroll) {
            $due = $this->payrollDue($payroll);
            if ($due <= 0) {
                continue;
            }

            $payableId = $accountingService->ensureEmployeeSalaryLedger($payroll->emp_id);
            $employeeName = optional($payroll->employee)->name ?: ('Employee #'.$payroll->emp_id);

            $sectionDr[] = [
                'account_id' => (int) $payableId,
                'debit_amount' => number_format($due, 2, '.', ''),
                'remarks' => $employeeName.' · Payroll #'.$payroll->payroll_id,
            ];
            $totalDue += $due;
        }

        if (empty($sectionDr)) {
            return null;
        }

        $totalDue = round($totalDue, 2);
        $bankId = (int) getAccount('Bank');
        if ($bankId <= 0) {
            $bankId = (int) getAccount('Cash');
        }

        return [
            'payroll_master_id' => (int) $master->payroll_master_id,
            'batch_label' => $master->month.' '.$master->year,
            'employees_count' => count($sectionDr),
            'due_amount' => $totalDue,
            'stock_voucher_info' => [
                'date' => now()->toDateString(),
                'voucher_type' => 'Payment',
                'description' => "Payroll payment batch ({$master->month} {$master->year})",
            ],
            'sectionDr' => $sectionDr,
            'sectionCr' => [[
                'account_id' => $bankId > 0 ? (int) $bankId : '',
                'credit_amount' => number_format($totalDue, 2, '.', ''),
                'remarks' => 'Payroll batch #'.$master->payroll_master_id,
            ]],
        ];
    }

    public function syncFromVoucher(
        TransactionGroup $group,
        array $entries,
        string $voucherType,
        string $paymentDate,
        ?int $payrollId = null,
        ?int $payrollMasterId = null
    ): void {
        if ($voucherType !== 'Payment') {
            return;
        }

        $cashId = (int) getAccount('Cash');
        $bankId = (int) getAccount('Bank');
        $paymentMode = $this->inferPaymentMode($entries, $cashId, $bankId);

        if ($payrollMasterId) {
            $this->syncFromVoucherForBatch($group, $entries, $paymentDate, $paymentMode, $payrollMasterId);

            return;
        }

        if ($payrollId) {
            $this->syncFromVoucherForPayroll($group, $entries, $paymentDate, $paymentMode, $payrollId);

            return;
        }

        $salaryLedgerMap = EmployeeAccountLedger::query()
            ->where('ledger_type', 'salary')
            ->pluck('employee_id', 'ledger_id');

        if ($salaryLedgerMap->isEmpty()) {
            return;
        }

        $linked = false;

        foreach ($entries as $entry) {
            $accountId = (int) ($entry['account_id'] ?? 0);
            $debit = round((float) ($entry['debit'] ?? 0), 2);

            if ($debit <= 0 || ! $salaryLedgerMap->has($accountId)) {
                continue;
            }

            $employeeId = (int) $salaryLedgerMap[$accountId];
            $remaining = $debit;

            $payrolls = Payroll::query()
                ->where('emp_id', $employeeId)
                ->where('is_scheduled', 0)
                ->orderBy('year')
                ->orderByRaw("FIELD(month,'January','February','March','April','May','June',
                        'July','August','September','October','November','December')")
                ->orderBy('payroll_id')
                ->get();

            foreach ($payrolls as $payroll) {
                if ($remaining <= 0) {
                    break;
                }

                $due = $this->payrollDue($payroll);
                if ($due <= 0) {
                    continue;
                }

                $allocation = min($remaining, $due);
                $this->recordPayment($payroll, $allocation, $group, $paymentDate, $paymentMode);
                $remaining = round($remaining - $allocation, 2);
                $linked = true;
            }
        }

        if ($linked) {
            $group->reference_type = 'payroll_payment';
            $group->save();
        }
    }

    /**
     * @param  array<int, array{account_id:mixed,debit:float,credit:float,remarks:?string}>  $entries
     */
    private function syncFromVoucherForPayroll(
        TransactionGroup $group,
        array $entries,
        string $paymentDate,
        ?string $paymentMode,
        int $payrollId
    ): void {
        $payroll = Payroll::find($payrollId);
        if (! $payroll) {
            return;
        }

        $payableId = (int) EmployeeAccountLedger::query()
            ->where('employee_id', $payroll->emp_id)
            ->where('ledger_type', 'salary')
            ->value('ledger_id');

        if ($payableId <= 0) {
            return;
        }

        $linked = false;

        foreach ($entries as $entry) {
            $accountId = (int) ($entry['account_id'] ?? 0);
            $debit = round((float) ($entry['debit'] ?? 0), 2);

            if ($debit <= 0 || $accountId !== $payableId) {
                continue;
            }

            $due = $this->payrollDue($payroll);
            if ($due <= 0) {
                continue;
            }

            $allocation = min($debit, $due);
            $this->recordPayment($payroll, $allocation, $group, $paymentDate, $paymentMode);
            $linked = true;
        }

        if ($linked) {
            $group->reference_type = 'payroll_payment';
            $group->reference = (string) $payroll->payroll_id;
            $group->save();
        }
    }

    /**
     * @param  array<int, array{account_id:mixed,debit:float,credit:float,remarks:?string}>  $entries
     */
    private function syncFromVoucherForBatch(
        TransactionGroup $group,
        array $entries,
        string $paymentDate,
        ?string $paymentMode,
        int $masterId
    ): void {
        $batchPayrolls = Payroll::query()
            ->where('payroll_master_id', $masterId)
            ->where('is_scheduled', 0)
            ->get();

        if ($batchPayrolls->isEmpty()) {
            return;
        }

        $payableToPayroll = [];
        foreach ($batchPayrolls as $payroll) {
            $payableId = (int) EmployeeAccountLedger::query()
                ->where('employee_id', $payroll->emp_id)
                ->where('ledger_type', 'salary')
                ->value('ledger_id');

            if ($payableId > 0) {
                $payableToPayroll[$payableId] = $payroll;
            }
        }

        $linked = false;

        foreach ($entries as $entry) {
            $accountId = (int) ($entry['account_id'] ?? 0);
            $debit = round((float) ($entry['debit'] ?? 0), 2);

            if ($debit <= 0 || ! isset($payableToPayroll[$accountId])) {
                continue;
            }

            $payroll = $payableToPayroll[$accountId];
            $due = $this->payrollDue($payroll);
            if ($due <= 0) {
                continue;
            }

            $allocation = min($debit, $due);
            $this->recordPayment($payroll, $allocation, $group, $paymentDate, $paymentMode);
            $linked = true;
        }

        if ($linked) {
            $group->reference_type = 'payroll_batch_payment';
            $group->reference = (string) $masterId;
            $group->save();
        }
    }

    public function recordPayment(
        Payroll $payroll,
        float $amount,
        TransactionGroup $group,
        string $paymentDate,
        ?string $paymentMode = null,
        ?string $narrations = null
    ): PayrollPayments {
        $amount = round($amount, 2);
        $dueBefore = $this->payrollDue($payroll);
        $balance = round(max(0, $dueBefore - $amount), 2);

        $payment = PayrollPayments::create([
            'payroll_id' => $payroll->payroll_id,
            'transaction_group_id' => $group->id,
            'payable_amount' => number_format($dueBefore, 2, '.', ''),
            'payment_date' => $paymentDate,
            'payment_amount' => number_format($amount, 2, '.', ''),
            'balance_amount' => number_format($balance, 2, '.', ''),
            'narrations' => $narrations,
            'payment_mode' => $paymentMode,
        ]);

        $payroll->payable_amount = $balance;
        $payroll->is_scheduled = $balance <= 0 ? 1 : 0;
        $payroll->save();

        $group->reference_type = 'payroll_payment';
        $group->reference = (string) $payroll->payroll_id;
        $group->save();

        return $payment;
    }

    /**
     * @param  array<int, array{account_id:mixed,debit:float,credit:float,remarks:?string}>  $entries
     */
    private function inferPaymentMode(array $entries, int $cashId, int $bankId): ?string
    {
        foreach ($entries as $entry) {
            $accountId = (int) ($entry['account_id'] ?? 0);
            $credit = round((float) ($entry['credit'] ?? 0), 2);

            if ($credit <= 0) {
                continue;
            }

            if ($accountId === $cashId) {
                return 'cash';
            }

            if ($accountId === $bankId) {
                return 'bank';
            }
        }

        return null;
    }

    /**
     * @param  iterable<int, Payroll>  $payrolls
     * @return array<int, array{id:int,voucher_no:?string,view_url:string}>
     */
    public function voucherLinksForPayrolls(iterable $payrolls): array
    {
        $vouchers = [];

        foreach ($payrolls as $payroll) {
            foreach ($payroll->payments ?? [] as $payment) {
                $group = $payment->transactionGroup;
                if ($group && $group->id && ! isset($vouchers[$group->id])) {
                    $vouchers[$group->id] = $this->formatVoucherLink($group);
                }
            }
        }

        return array_values($vouchers);
    }

    /**
     * @return array{id:int,voucher_no:?string,view_url:string}
     */
    private function formatVoucherLink(TransactionGroup $group): array
    {
        return [
            'id' => (int) $group->id,
            'voucher_no' => $group->voucher_no,
            'view_url' => route('tab.stock.stock.voucher.details', [
                'id' => $group->id,
                'tab_name' => 'info',
            ]),
        ];
    }
}
