<?php

namespace App\Service;

use App\Models\PurchasePayment;
use App\Models\Transaction;
use App\Models\TransactionGroup;
use App\Models\Vendor;
use App\Models\VendorPayment;
use Auth;
use Illuminate\Validation\ValidationException;

class VendorPaymentService
{
    public function getMaxPayable(Vendor $vendor): float
    {
        $balance = max(0, round((float) $vendor->balance, 2));

        $totalCredit = (float) PurchasePayment::where('vendor_id', $vendor->vendor_id)
            ->where('payment_mode', PurchasePayment::PAYMENT_MODE_CREDIT)
            ->sum('amount_taken');

        $manualPaid = (float) PurchasePayment::where('vendor_id', $vendor->vendor_id)
            ->whereNull('purchase_id')
            ->whereIn('payment_mode', [
                PurchasePayment::PAYMENT_MODE_CASH,
                PurchasePayment::PAYMENT_MODE_BANK,
                PurchasePayment::PAYMENT_MODE_ONLINE,
            ])
            ->sum('amount_taken');

        $fromPayments = max(0, round($totalCredit - $manualPaid, 2));

        if ($fromPayments < 0.01 && $balance < 0.01) {
            return 0.0;
        }

        if ($fromPayments < 0.01) {
            return $balance;
        }

        if ($balance < 0.01) {
            return $fromPayments;
        }

        return round(min($balance, $fromPayments), 2);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function buildPaymentVoucherPrefill(int $vendorId, AccountingService $accountingService): ?array
    {
        $vendor = Vendor::find($vendorId);
        if (! $vendor) {
            return null;
        }

        $due = $this->getMaxPayable($vendor);
        if ($due <= 0) {
            return null;
        }

        $payableId = $accountingService->getOrCreateVendorLedger($vendorId);
        $bankId = (int) getAccount('Bank');
        if ($bankId <= 0) {
            $bankId = (int) getAccount('Cash');
        }

        return [
            'vendor_id' => (int) $vendor->vendor_id,
            'vendor_name' => $vendor->name,
            'due_amount' => $due,
            'stock_voucher_info' => [
                'date' => now()->toDateString(),
                'voucher_type' => 'Payment',
                'description' => "Vendor payment for {$vendor->name}",
            ],
            'sectionDr' => [[
                'account_id' => (int) $payableId,
                'debit_amount' => number_format($due, 2, '.', ''),
                'remarks' => $vendor->name,
            ]],
            'sectionCr' => [[
                'account_id' => $bankId > 0 ? (int) $bankId : '',
                'credit_amount' => number_format($due, 2, '.', ''),
                'remarks' => '',
            ]],
        ];
    }

    /**
     * @param  array<int, array{account_id:mixed,debit:float,credit:float,remarks:?string}>  $entries
     */
    public function validatePaymentEntries(array $entries, int $vendorId, AccountingService $accountingService): void
    {
        if ($vendorId <= 0) {
            return;
        }

        $vendor = Vendor::findOrFail($vendorId);
        $ledgerId = (int) $accountingService->getOrCreateVendorLedger($vendorId);
        $amount = $this->sumVendorLedgerDebit($entries, $ledgerId);

        if ($amount < 0.01) {
            return;
        }

        $maxPayable = $this->getMaxPayable($vendor);
        if ($amount > $maxPayable + 0.0001) {
            throw ValidationException::withMessages([
                'sectionDr' => [__('Payment amount cannot exceed the remaining credit amount (:amount).', [
                    'amount' => number_format($maxPayable, 2),
                ])],
            ]);
        }
    }

    public function reverseForVoucher(int $transactionGroupId): void
    {
        $payments = VendorPayment::where('transaction_group_id', $transactionGroupId)->get();
        if ($payments->isEmpty()) {
            return;
        }

        $vendorId = (int) $payments->first()->vendor_id;
        $totalRestored = 0.0;

        foreach ($payments as $payment) {
            if ($payment->credit_payment_id) {
                $credit = PurchasePayment::find($payment->credit_payment_id);
                if ($credit) {
                    $credit->amount_taken = round((float) $credit->amount_taken + (float) $payment->amount, 2);
                    $credit->save();
                }
            }

            if ($payment->settlement_payment_id) {
                PurchasePayment::where('payment_id', $payment->settlement_payment_id)->delete();
            }

            $totalRestored += (float) $payment->amount;
            $payment->delete();
        }

        if ($vendorId > 0 && $totalRestored > 0) {
            $vendor = Vendor::find($vendorId);
            if ($vendor) {
                $vendor->balance = round((float) $vendor->balance + $totalRestored, 2);
                $vendor->save();
            }
        }
    }

    /**
     * @param  array<int, array{account_id:mixed,debit:float,credit:float,remarks:?string}>  $entries
     */
    public function syncFromVoucher(
        TransactionGroup $group,
        array $entries,
        string $voucherType,
        string $paymentDate,
        ?int $vendorId = null
    ): void {
        if ($voucherType !== 'Payment' || ! $vendorId) {
            return;
        }

        $vendor = Vendor::find($vendorId);
        if (! $vendor) {
            return;
        }

        $accountingService = app(AccountingService::class);
        $ledgerId = (int) $accountingService->getOrCreateVendorLedger($vendorId);
        $amount = $this->sumVendorLedgerDebit($entries, $ledgerId);

        if ($amount < 0.01) {
            return;
        }

        $cashId = (int) getAccount('Cash');
        $bankId = (int) getAccount('Bank');
        $paymentMode = $this->inferPaymentMode($entries, $cashId, $bankId) ?? 'bank';

        $this->allocatePurchasePayments($vendor, $amount, $paymentMode, $paymentDate, $group);

        $vendor->balance = max(0, round((float) $vendor->balance - $amount, 2));
        $vendor->save();

        Transaction::where('transaction_group_id', $group->id)->update(['vendor_id' => $vendorId]);

        $group->reference_type = 'vendor_payment';
        $group->reference = (string) $vendorId;
        $group->save();
    }

    /**
     * @return array<int, array{id:int,voucher_no:?string,view_url:string}>
     */
    public function voucherLinksForVendor(int $vendorId): array
    {
        $payments = VendorPayment::with('transactionGroup')
            ->where('vendor_id', $vendorId)
            ->orderByDesc('id')
            ->get();

        $vouchers = [];

        foreach ($payments as $payment) {
            $group = $payment->transactionGroup;
            if ($group && $group->id && ! isset($vouchers[$group->id])) {
                $vouchers[$group->id] = [
                    'id' => (int) $group->id,
                    'voucher_no' => $group->voucher_no,
                    'view_url' => route('tab.stock.stock.voucher.details', [
                        'id' => $group->id,
                        'tab_name' => 'info',
                    ]),
                ];
            }
        }

        return array_values($vouchers);
    }

    private function allocatePurchasePayments(
        Vendor $vendor,
        float $amount,
        string $paymentMode,
        string $paymentDate,
        TransactionGroup $group
    ): void {
        $creditPayments = PurchasePayment::where('vendor_id', $vendor->vendor_id)
            ->where('payment_mode', PurchasePayment::PAYMENT_MODE_CREDIT)
            ->where('amount_taken', '>', 0)
            ->whereNotNull('purchase_id')
            ->orderBy('created_at', 'asc')
            ->orderBy('payment_id', 'asc')
            ->get();

        $remainingAmount = $amount;

        foreach ($creditPayments as $creditPayment) {
            if ($remainingAmount <= 0) {
                break;
            }

            $paymentAmount = round(min($remainingAmount, (float) $creditPayment->amount_taken), 2);
            if ($paymentAmount < 0.01) {
                continue;
            }

            $creditPayment->amount_taken = round((float) $creditPayment->amount_taken - $paymentAmount, 2);
            $creditPayment->save();

            $settlement = PurchasePayment::create([
                'purchase_id' => $creditPayment->purchase_id,
                'vendor_id' => $vendor->vendor_id,
                'amount_taken' => $paymentAmount,
                'payment_mode' => $paymentMode,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            VendorPayment::create([
                'vendor_id' => $vendor->vendor_id,
                'transaction_group_id' => $group->id,
                'amount' => $paymentAmount,
                'payment_date' => $paymentDate,
                'payment_mode' => $paymentMode,
                'purchase_id' => $creditPayment->purchase_id,
                'credit_payment_id' => $creditPayment->payment_id,
                'settlement_payment_id' => $settlement->payment_id,
            ]);

            $remainingAmount = round($remainingAmount - $paymentAmount, 2);
        }

        if ($remainingAmount >= 0.01) {
            $settlement = PurchasePayment::create([
                'purchase_id' => null,
                'vendor_id' => $vendor->vendor_id,
                'amount_taken' => $remainingAmount,
                'payment_mode' => $paymentMode,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            VendorPayment::create([
                'vendor_id' => $vendor->vendor_id,
                'transaction_group_id' => $group->id,
                'amount' => $remainingAmount,
                'payment_date' => $paymentDate,
                'payment_mode' => $paymentMode,
                'purchase_id' => null,
                'credit_payment_id' => null,
                'settlement_payment_id' => $settlement->payment_id,
            ]);
        }
    }

    /**
     * @param  array<int, array{account_id:mixed,debit:float,credit:float,remarks:?string}>  $entries
     */
    private function sumVendorLedgerDebit(array $entries, int $ledgerId): float
    {
        $amount = 0.0;

        foreach ($entries as $entry) {
            if ((int) ($entry['account_id'] ?? 0) === $ledgerId) {
                $amount += round((float) ($entry['debit'] ?? 0), 2);
            }
        }

        return round($amount, 2);
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
}
