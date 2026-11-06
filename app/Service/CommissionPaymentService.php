<?php

namespace App\Service;

use App\Models\Commission;
use App\Models\CommissionMaster;
use App\Models\CommissionPayment;
use App\Models\EmployeeAccountLedger;
use App\Models\TransactionGroup;

class CommissionPaymentService
{
    /**
     * Undo commission allocations linked to a payment voucher (used when voucher is edited/reversed).
     */
    public function reverseForVoucher(int $transactionGroupId): void
    {
        $payments = CommissionPayment::where('transaction_group_id', $transactionGroupId)->get();

        foreach ($payments as $payment) {
            $commission = Commission::find($payment->commission_id);
            if ($commission) {
                $commission->paid_amount = max(
                    0,
                    round((float) $commission->paid_amount - (float) $payment->amount, 2)
                );
                $this->refreshCommissionPaymentStatus($commission);
                $commission->save();
            }

            $payment->delete();
        }
    }

    /**
     * Build pre-filled payment voucher data for a commission row.
     *
     * @return array<string, mixed>|null
     */
    public function buildPaymentVoucherPrefill(int $commissionId, AccountingService $accountingService): ?array
    {
        $commission = Commission::find($commissionId);
        if (! $commission) {
            return null;
        }

        $due = round((float) $commission->commission_amount - (float) $commission->paid_amount, 2);
        if ($due <= 0) {
            return null;
        }

        $payableId = $accountingService->ensureEmployeeCommissionLedger($commission->emp_id);
        $bankId = (int) getAccount('Bank');
        if ($bankId <= 0) {
            $bankId = (int) getAccount('Cash');
        }

        $employeeName = $commission->emp_name ?: ('Employee #'.$commission->emp_id);

        return [
            'commission_id' => $commission->commission_id,
            'employee_name' => $employeeName,
            'due_amount' => $due,
            'payable_account_id' => $payableId,
            'stock_voucher_info' => [
                'date' => now()->toDateString(),
                'voucher_type' => 'Payment',
                'description' => "Commission payment for {$employeeName}",
            ],
            'sectionDr' => [[
                'account_id' => (int) $payableId,
                'debit_amount' => number_format($due, 2, '.', ''),
                'remarks' => 'Commission #'.$commission->commission_id,
            ]],
            'sectionCr' => [[
                'account_id' => $bankId > 0 ? (int) $bankId : '',
                'credit_amount' => number_format($due, 2, '.', ''),
                'remarks' => '',
            ]],
        ];
    }

    /**
     * Build pre-filled payment voucher for an entire commission batch (all due rows in Dr).
     *
     * @return array<string, mixed>|null
     */
    public function buildBatchPaymentVoucherPrefill(int $masterId, AccountingService $accountingService): ?array
    {
        $master = CommissionMaster::with('commissions')->find($masterId);
        if (! $master) {
            return null;
        }

        $sectionDr = [];
        $totalDue = 0.0;

        foreach ($master->commissions as $commission) {
            $due = round((float) $commission->commission_amount - (float) $commission->paid_amount, 2);
            if ($due <= 0) {
                continue;
            }

            $payableId = $accountingService->ensureEmployeeCommissionLedger($commission->emp_id);
            $employeeName = $commission->emp_name ?: ('Employee #'.$commission->emp_id);

            $sectionDr[] = [
                'account_id' => (int) $payableId,
                'debit_amount' => number_format($due, 2, '.', ''),
                'remarks' => $employeeName.' · Commission #'.$commission->commission_id,
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

        $period = date('d-m-Y', strtotime($master->start_date))
            .' – '
            .date('d-m-Y', strtotime($master->end_date));

        return [
            'commission_master_id' => (int) $master->commission_master_id,
            'batch_label' => $period,
            'employees_count' => count($sectionDr),
            'due_amount' => $totalDue,
            'stock_voucher_info' => [
                'date' => now()->toDateString(),
                'voucher_type' => 'Payment',
                'description' => "Commission payment batch ({$period})",
            ],
            'sectionDr' => $sectionDr,
            'sectionCr' => [[
                'account_id' => $bankId > 0 ? (int) $bankId : '',
                'credit_amount' => number_format($totalDue, 2, '.', ''),
                'remarks' => 'Commission batch #'.$master->commission_master_id,
            ]],
        ];
    }

    /**
     * Allocate payment voucher debits on commission payable ledgers to open commission rows.
     */
    public function syncFromVoucher(
        TransactionGroup $group,
        array $entries,
        string $voucherType,
        string $paymentDate,
        ?int $commissionId = null,
        ?int $commissionMasterId = null
    ): void {
        if ($voucherType !== 'Payment') {
            return;
        }

        $cashId = (int) getAccount('Cash');
        $bankId = (int) getAccount('Bank');
        $paymentMode = $this->inferPaymentMode($entries, $cashId, $bankId);

        if ($commissionMasterId) {
            $this->syncFromVoucherForBatch($group, $entries, $paymentDate, $paymentMode, $commissionMasterId);

            return;
        }

        if ($commissionId) {
            $this->syncFromVoucherForCommission($group, $entries, $paymentDate, $paymentMode, $commissionId);

            return;
        }

        $commissionLedgerMap = EmployeeAccountLedger::query()
            ->where('ledger_type', 'commission')
            ->pluck('employee_id', 'ledger_id');

        if ($commissionLedgerMap->isEmpty()) {
            return;
        }

        $linked = false;

        foreach ($entries as $entry) {
            $accountId = (int) ($entry['account_id'] ?? 0);
            $debit = round((float) ($entry['debit'] ?? 0), 2);

            if ($debit <= 0 || ! $commissionLedgerMap->has($accountId)) {
                continue;
            }

            $employeeId = (int) $commissionLedgerMap[$accountId];
            $remaining = $debit;

            $commissions = Commission::query()
                ->where('emp_id', $employeeId)
                ->whereIn('payment_status', ['unpaid', 'partial'])
                ->orderBy('end_date')
                ->orderBy('commission_id')
                ->get();

            foreach ($commissions as $commission) {
                if ($remaining <= 0) {
                    break;
                }

                $due = round((float) $commission->commission_amount - (float) $commission->paid_amount, 2);
                if ($due <= 0) {
                    continue;
                }

                $allocation = min($remaining, $due);
                $this->recordPayment($commission, $allocation, $group, $paymentDate, $paymentMode);
                $remaining = round($remaining - $allocation, 2);
                $linked = true;
            }
        }

        if ($linked) {
            $group->reference_type = 'commission_payment';
            $group->save();
        }
    }

    /**
     * @param  array<int, array{account_id:mixed,debit:float,credit:float,remarks:?string}>  $entries
     */
    private function syncFromVoucherForCommission(
        TransactionGroup $group,
        array $entries,
        string $paymentDate,
        ?string $paymentMode,
        int $commissionId
    ): void {
        $commission = Commission::find($commissionId);
        if (! $commission) {
            return;
        }

        $payableId = (int) EmployeeAccountLedger::query()
            ->where('employee_id', $commission->emp_id)
            ->where('ledger_type', 'commission')
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

            $due = round((float) $commission->commission_amount - (float) $commission->paid_amount, 2);
            if ($due <= 0) {
                continue;
            }

            $allocation = min($debit, $due);
            $this->recordPayment($commission, $allocation, $group, $paymentDate, $paymentMode);
            $linked = true;
        }

        if ($linked) {
            $group->reference_type = 'commission_payment';
            $group->reference = (string) $commission->commission_id;
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
        $batchCommissions = Commission::query()
            ->where('commission_master_id', $masterId)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->get();

        if ($batchCommissions->isEmpty()) {
            return;
        }

        $payableToCommission = [];
        foreach ($batchCommissions as $commission) {
            $payableId = (int) EmployeeAccountLedger::query()
                ->where('employee_id', $commission->emp_id)
                ->where('ledger_type', 'commission')
                ->value('ledger_id');

            if ($payableId > 0) {
                $payableToCommission[$payableId] = $commission;
            }
        }

        $linked = false;

        foreach ($entries as $entry) {
            $accountId = (int) ($entry['account_id'] ?? 0);
            $debit = round((float) ($entry['debit'] ?? 0), 2);

            if ($debit <= 0 || ! isset($payableToCommission[$accountId])) {
                continue;
            }

            $commission = $payableToCommission[$accountId];
            $due = round((float) $commission->commission_amount - (float) $commission->paid_amount, 2);
            if ($due <= 0) {
                continue;
            }

            $allocation = min($debit, $due);
            $this->recordPayment($commission, $allocation, $group, $paymentDate, $paymentMode);
            $linked = true;
        }

        if ($linked) {
            $group->reference_type = 'commission_batch_payment';
            $group->reference = (string) $masterId;
            $group->save();
        }
    }

    public function recordPayment(
        Commission $commission,
        float $amount,
        TransactionGroup $group,
        string $paymentDate,
        ?string $paymentMode = null
    ): CommissionPayment {
        $amount = round($amount, 2);

        $payment = CommissionPayment::create([
            'commission_id' => $commission->commission_id,
            'transaction_group_id' => $group->id,
            'amount' => $amount,
            'payment_date' => $paymentDate,
            'payment_mode' => $paymentMode,
        ]);

        $commission->paid_amount = round((float) $commission->paid_amount + $amount, 2);
        $commission->payment_date = $paymentDate;
        if ($paymentMode) {
            $commission->payment_mode = $paymentMode;
        }
        $commission->payment_transaction_group_id = $group->id;
        $this->refreshCommissionPaymentStatus($commission);
        $commission->save();

        $group->reference_type = 'commission_payment';
        $group->reference = (string) $commission->commission_id;
        $group->save();

        return $payment;
    }

    private function refreshCommissionPaymentStatus(Commission $commission): void
    {
        $paid = round((float) $commission->paid_amount, 2);
        $total = round((float) $commission->commission_amount, 2);

        if ($paid + 0.009 >= $total) {
            $commission->payment_status = 'paid';
        } elseif ($paid > 0) {
            $commission->payment_status = 'partial';
        } else {
            $commission->payment_status = 'unpaid';
            $commission->payment_date = null;
            $commission->payment_mode = null;
            $commission->payment_transaction_group_id = null;
        }

        $latestPayment = CommissionPayment::query()
            ->where('commission_id', $commission->commission_id)
            ->orderByDesc('id')
            ->first();

        if ($latestPayment) {
            $commission->payment_transaction_group_id = $latestPayment->transaction_group_id;
            $commission->payment_date = $latestPayment->payment_date;
            if ($latestPayment->payment_mode) {
                $commission->payment_mode = $latestPayment->payment_mode;
            }
        }
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
     * @param  iterable<int, Commission>  $commissions
     * @return array<int, array{id:int,voucher_no:?string,view_url:string}>
     */
    public function voucherLinksForCommissions(iterable $commissions): array
    {
        $vouchers = [];

        foreach ($commissions as $commission) {
            foreach ($commission->payments ?? [] as $payment) {
                $group = $payment->transactionGroup;
                if ($group && $group->id && ! isset($vouchers[$group->id])) {
                    $vouchers[$group->id] = $this->formatVoucherLink($group);
                }
            }

            $latest = $commission->paymentVoucher ?? null;
            if ($latest && $latest->id && ! isset($vouchers[$latest->id])) {
                $vouchers[$latest->id] = $this->formatVoucherLink($latest);
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
