<?php

namespace App\Service;

use App\Models\EmployeeAccountLedger;
use App\Models\Incentive;
use App\Models\IncentiveMaster;
use App\Models\IncentivePayment;
use App\Models\TransactionGroup;

class IncentivePaymentService
{
    public function reverseForVoucher(int $transactionGroupId): void
    {
        $payments = IncentivePayment::where('transaction_group_id', $transactionGroupId)->get();

        foreach ($payments as $payment) {
            $incentive = Incentive::find($payment->incentive_id);
            if ($incentive) {
                $incentive->paid_amount = max(
                    0,
                    round((float) $incentive->paid_amount - (float) $payment->amount, 2)
                );
                $this->refreshIncentivePaymentStatus($incentive);
                $incentive->save();
            }

            $payment->delete();
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function buildPaymentVoucherPrefill(int $incentiveId, AccountingService $accountingService): ?array
    {
        $incentive = Incentive::find($incentiveId);
        if (! $incentive) {
            return null;
        }

        $due = round((float) $incentive->incentive_amount - (float) $incentive->paid_amount, 2);
        if ($due <= 0) {
            return null;
        }

        $payableId = $accountingService->ensureEmployeeIncentiveLedger($incentive->emp_id);
        $bankId = (int) getAccount('Bank');
        if ($bankId <= 0) {
            $bankId = (int) getAccount('Cash');
        }

        $employeeName = $incentive->emp_name ?: ('Employee #'.$incentive->emp_id);

        return [
            'incentive_id' => $incentive->incentive_id,
            'employee_name' => $employeeName,
            'due_amount' => $due,
            'stock_voucher_info' => [
                'date' => now()->toDateString(),
                'voucher_type' => 'Payment',
                'description' => "Incentive payment for {$employeeName}",
            ],
            'sectionDr' => [[
                'account_id' => (int) $payableId,
                'debit_amount' => number_format($due, 2, '.', ''),
                'remarks' => 'Incentive #'.$incentive->incentive_id,
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
        $master = IncentiveMaster::with('incentives')->find($masterId);
        if (! $master) {
            return null;
        }

        $sectionDr = [];
        $totalDue = 0.0;

        foreach ($master->incentives as $incentive) {
            $due = round((float) $incentive->incentive_amount - (float) $incentive->paid_amount, 2);
            if ($due <= 0) {
                continue;
            }

            $payableId = $accountingService->ensureEmployeeIncentiveLedger($incentive->emp_id);
            $employeeName = $incentive->emp_name ?: ('Employee #'.$incentive->emp_id);

            $sectionDr[] = [
                'account_id' => (int) $payableId,
                'debit_amount' => number_format($due, 2, '.', ''),
                'remarks' => $employeeName.' · Incentive #'.$incentive->incentive_id,
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
            'incentive_master_id' => (int) $master->incentive_master_id,
            'batch_label' => $period,
            'employees_count' => count($sectionDr),
            'due_amount' => $totalDue,
            'stock_voucher_info' => [
                'date' => now()->toDateString(),
                'voucher_type' => 'Payment',
                'description' => "Incentive payment batch ({$period})",
            ],
            'sectionDr' => $sectionDr,
            'sectionCr' => [[
                'account_id' => $bankId > 0 ? (int) $bankId : '',
                'credit_amount' => number_format($totalDue, 2, '.', ''),
                'remarks' => 'Incentive batch #'.$master->incentive_master_id,
            ]],
        ];
    }

    public function syncFromVoucher(
        TransactionGroup $group,
        array $entries,
        string $voucherType,
        string $paymentDate,
        ?int $incentiveId = null,
        ?int $incentiveMasterId = null
    ): void {
        if ($voucherType !== 'Payment') {
            return;
        }

        $cashId = (int) getAccount('Cash');
        $bankId = (int) getAccount('Bank');
        $paymentMode = $this->inferPaymentMode($entries, $cashId, $bankId);

        if ($incentiveMasterId) {
            $this->syncFromVoucherForBatch($group, $entries, $paymentDate, $paymentMode, $incentiveMasterId);

            return;
        }

        if ($incentiveId) {
            $this->syncFromVoucherForIncentive($group, $entries, $paymentDate, $paymentMode, $incentiveId);

            return;
        }

        $incentiveLedgerMap = EmployeeAccountLedger::query()
            ->where('ledger_type', 'incentive')
            ->pluck('employee_id', 'ledger_id');

        if ($incentiveLedgerMap->isEmpty()) {
            return;
        }

        $linked = false;

        foreach ($entries as $entry) {
            $accountId = (int) ($entry['account_id'] ?? 0);
            $debit = round((float) ($entry['debit'] ?? 0), 2);

            if ($debit <= 0 || ! $incentiveLedgerMap->has($accountId)) {
                continue;
            }

            $employeeId = (int) $incentiveLedgerMap[$accountId];
            $remaining = $debit;

            $incentives = Incentive::query()
                ->where('emp_id', $employeeId)
                ->whereIn('payment_status', ['unpaid', 'partial'])
                ->orderBy('end_date')
                ->orderBy('incentive_id')
                ->get();

            foreach ($incentives as $incentive) {
                if ($remaining <= 0) {
                    break;
                }

                $due = round((float) $incentive->incentive_amount - (float) $incentive->paid_amount, 2);
                if ($due <= 0) {
                    continue;
                }

                $allocation = min($remaining, $due);
                $this->recordPayment($incentive, $allocation, $group, $paymentDate, $paymentMode);
                $remaining = round($remaining - $allocation, 2);
                $linked = true;
            }
        }

        if ($linked) {
            $group->reference_type = 'incentive_payment';
            $group->save();
        }
    }

    /**
     * @param  array<int, array{account_id:mixed,debit:float,credit:float,remarks:?string}>  $entries
     */
    private function syncFromVoucherForIncentive(
        TransactionGroup $group,
        array $entries,
        string $paymentDate,
        ?string $paymentMode,
        int $incentiveId
    ): void {
        $incentive = Incentive::find($incentiveId);
        if (! $incentive) {
            return;
        }

        $payableId = (int) EmployeeAccountLedger::query()
            ->where('employee_id', $incentive->emp_id)
            ->where('ledger_type', 'incentive')
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

            $due = round((float) $incentive->incentive_amount - (float) $incentive->paid_amount, 2);
            if ($due <= 0) {
                continue;
            }

            $allocation = min($debit, $due);
            $this->recordPayment($incentive, $allocation, $group, $paymentDate, $paymentMode);
            $linked = true;
        }

        if ($linked) {
            $group->reference_type = 'incentive_payment';
            $group->reference = (string) $incentive->incentive_id;
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
        $batchIncentives = Incentive::query()
            ->where('incentive_master_id', $masterId)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->get();

        if ($batchIncentives->isEmpty()) {
            return;
        }

        $payableToIncentive = [];
        foreach ($batchIncentives as $incentive) {
            $payableId = (int) EmployeeAccountLedger::query()
                ->where('employee_id', $incentive->emp_id)
                ->where('ledger_type', 'incentive')
                ->value('ledger_id');

            if ($payableId > 0) {
                $payableToIncentive[$payableId] = $incentive;
            }
        }

        $linked = false;

        foreach ($entries as $entry) {
            $accountId = (int) ($entry['account_id'] ?? 0);
            $debit = round((float) ($entry['debit'] ?? 0), 2);

            if ($debit <= 0 || ! isset($payableToIncentive[$accountId])) {
                continue;
            }

            $incentive = $payableToIncentive[$accountId];
            $due = round((float) $incentive->incentive_amount - (float) $incentive->paid_amount, 2);
            if ($due <= 0) {
                continue;
            }

            $allocation = min($debit, $due);
            $this->recordPayment($incentive, $allocation, $group, $paymentDate, $paymentMode);
            $linked = true;
        }

        if ($linked) {
            $group->reference_type = 'incentive_batch_payment';
            $group->reference = (string) $masterId;
            $group->save();
        }
    }

    public function recordPayment(
        Incentive $incentive,
        float $amount,
        TransactionGroup $group,
        string $paymentDate,
        ?string $paymentMode = null
    ): IncentivePayment {
        $amount = round($amount, 2);

        $payment = IncentivePayment::create([
            'incentive_id' => $incentive->incentive_id,
            'transaction_group_id' => $group->id,
            'amount' => $amount,
            'payment_date' => $paymentDate,
            'payment_mode' => $paymentMode,
        ]);

        $incentive->paid_amount = round((float) $incentive->paid_amount + $amount, 2);
        $incentive->payment_date = $paymentDate;
        if ($paymentMode) {
            $incentive->payment_mode = $paymentMode;
        }
        $incentive->payment_transaction_group_id = $group->id;
        $this->refreshIncentivePaymentStatus($incentive);
        $incentive->save();

        $group->reference_type = 'incentive_payment';
        $group->reference = (string) $incentive->incentive_id;
        $group->save();

        return $payment;
    }

    private function refreshIncentivePaymentStatus(Incentive $incentive): void
    {
        $paid = round((float) $incentive->paid_amount, 2);
        $total = round((float) $incentive->incentive_amount, 2);

        if ($paid + 0.009 >= $total) {
            $incentive->payment_status = 'paid';
        } elseif ($paid > 0) {
            $incentive->payment_status = 'partial';
        } else {
            $incentive->payment_status = 'unpaid';
            $incentive->payment_date = null;
            $incentive->payment_mode = null;
            $incentive->payment_transaction_group_id = null;
        }

        $latestPayment = IncentivePayment::query()
            ->where('incentive_id', $incentive->incentive_id)
            ->orderByDesc('id')
            ->first();

        if ($latestPayment) {
            $incentive->payment_transaction_group_id = $latestPayment->transaction_group_id;
            $incentive->payment_date = $latestPayment->payment_date;
            if ($latestPayment->payment_mode) {
                $incentive->payment_mode = $latestPayment->payment_mode;
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
     * @param  iterable<int, Incentive>  $incentives
     * @return array<int, array{id:int,voucher_no:?string,view_url:string}>
     */
    public function voucherLinksForIncentives(iterable $incentives): array
    {
        $vouchers = [];

        foreach ($incentives as $incentive) {
            foreach ($incentive->payments ?? [] as $payment) {
                $group = $payment->transactionGroup;
                if ($group && $group->id && ! isset($vouchers[$group->id])) {
                    $vouchers[$group->id] = $this->formatVoucherLink($group);
                }
            }

            $latest = $incentive->paymentVoucher ?? null;
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
