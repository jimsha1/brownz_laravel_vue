<?php

namespace App\Service;

use App\Models\Account;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Employee;
use App\Models\EmployeeAccountLedger;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Journal;
use App\Models\PurchasePayment;
use App\Models\StockItem;
use App\Models\StockOpenBalance;
use App\Models\StockPurchase;
use App\Models\StockPurchaseItem;
use App\Models\Transaction;
use App\Models\TransactionGroup;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AccountingService
{
    public function recordMultiEntryTransaction(
        $entries,
        $description,
        $date,
        $voucher_type = null,
        $is_create_voucher = null,
        $financial_year_id = null,
        $skipFinancialYearCheck = false
    ) {
        if (! $skipFinancialYearCheck) {
            app(FinancialYearService::class)->assertTransactionDateAllowed($date);
        }

        return DB::transaction(function () use ($entries, $description, $date, $voucher_type, $is_create_voucher, $financial_year_id) {
            $totalDebit = round(array_sum(array_column($entries, 'debit')), 2);
            $totalCredit = round(array_sum(array_column($entries, 'credit')), 2);
            if ($totalDebit !== $totalCredit) {
                throw new \Exception("Debits ($totalDebit) must equal Credits ($totalCredit)");
            }

            $resolvedVoucherType = $voucher_type ?? 'Journal';

            $group = TransactionGroup::create([
                'description' => $description,
                'date' => $date,
                'voucher_type' => $resolvedVoucherType,
                'is_create_voucher' => $is_create_voucher ?? 0,
                'financial_year_id' => $financial_year_id ?? app(FinancialYearService::class)->findForDate($date)?->id,
            ]);

            $group->voucher_no = TransactionGroup::getNextVoucherNo(
                $resolvedVoucherType,
                $date,
                $group->financial_year_id
            );
            $group->save();
            foreach ($entries as $entry) {
                $accountId = $entry['account_id'];
                $debit = $entry['debit'] ?? 0;
                $credit = $entry['credit'] ?? 0;

                $journal = Journal::where('account_id', $accountId)->first();
                if (! $journal) {
                    $journal = Journal::create(['account_id' => $accountId]);
                }

                Transaction::create([
                    'transaction_group_id' => $group->id,
                    'journal_id' => $journal->id,
                    'debit' => $debit,
                    'credit' => $credit,
                    'date' => $date,
                    'remarks' => $entry['remarks'] ?? null,
                ]);

                $journal->balance += $debit - $credit;
                $account = Account::find($accountId);
                if (! $account) {
                    throw new \Exception("Account not found for id: {$accountId}");
                }
                $account->balance += $debit - $credit;
                $journal->save();
                $account->save();

            }

            return $group; // Return the group from the closure
        });
    }

    /**
     * Rebuild accounts.balance from journals.balance (posting source of truth).
     * Fixes drift caused by seeders/manual ledger edits overwriting account.balance.
     *
     * @return int Number of accounts corrected
     */
    public function syncAccountBalancesFromJournals(): int
    {
        $corrected = 0;

        DB::transaction(function () use (&$corrected) {
            $rows = Journal::query()
                ->selectRaw('account_id, COALESCE(SUM(balance), 0) as journal_balance')
                ->groupBy('account_id')
                ->get();

            $touchedAccountIds = [];

            foreach ($rows as $row) {
                $touchedAccountIds[] = $row->account_id;
                $account = Account::find($row->account_id);
                if (! $account) {
                    continue;
                }

                $journalBalance = round((float) $row->journal_balance, 2);
                $accountBalance = round((float) $account->balance, 2);

                if (abs($accountBalance - $journalBalance) >= 0.01) {
                    $account->balance = $journalBalance;
                    $account->save();
                    $corrected++;
                }
            }

            $zeroQuery = Account::query()->where(function ($q) {
                $q->where('balance', '!=', 0)->orWhereNull('balance');
            });

            if (! empty($touchedAccountIds)) {
                $zeroQuery->whereNotIn('account_id', $touchedAccountIds);
            }

            $corrected += $zeroQuery->update(['balance' => 0]);
        });

        return $corrected;
    }

    /**
     * Ensure chart balances match journals; throw if books are still unbalanced after sync.
     */
    public function assertBooksBalanced(string $context = 'Accounting'): void
    {
        $this->syncAccountBalancesFromJournals();

        $totals = $this->trialBalanceTotalsFromAccountBalances();
        if (abs($totals['difference']) >= 0.01) {
            throw new \Exception(
                "{$context}: books are unbalanced after sync (Dr {$totals['total_debit']} / Cr {$totals['total_credit']}, diff {$totals['difference']})."
            );
        }
    }

    /**
     * @return array{total_debit: float, total_credit: float, difference: float}
     */
    public function trialBalanceTotalsFromAccountBalances(): array
    {
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        foreach (Account::all(['account_id', 'type', 'balance']) as $account) {
            $balance = round((float) $account->balance, 2);
            if (abs($balance) < 0.01) {
                continue;
            }

            $type = strtolower((string) $account->type);
            if (in_array($type, ['asset', 'expense'], true)) {
                if ($balance >= 0) {
                    $totalDebit += $balance;
                } else {
                    $totalCredit += abs($balance);
                }
            } elseif (in_array($type, ['liability', 'equity', 'revenue'], true)) {
                if ($balance <= 0) {
                    $totalCredit += abs($balance);
                } else {
                    $totalDebit += $balance;
                }
            } else {
                if ($balance >= 0) {
                    $totalDebit += $balance;
                } else {
                    $totalCredit += abs($balance);
                }
            }
        }

        return [
            'total_debit' => round($totalDebit, 2),
            'total_credit' => round($totalCredit, 2),
            'difference' => round($totalDebit - $totalCredit, 2),
        ];
    }

    public function recordAuditVoucher(
        $entries,
        $description,
        $date,
        $voucher_type = null,
        $financial_year_id = null,
        $reference_type = null,
        $reference = null
    ) {
        return DB::transaction(function () use ($entries, $description, $date, $voucher_type, $financial_year_id, $reference_type, $reference) {
            $totalDebit = round(array_sum(array_column($entries, 'debit')), 2);
            $totalCredit = round(array_sum(array_column($entries, 'credit')), 2);
            if ($totalDebit !== $totalCredit) {
                throw new \Exception("Debits ($totalDebit) must equal Credits ($totalCredit)");
            }

            $group = TransactionGroup::create([
                'description' => $description,
                'date' => $date,
                'voucher_type' => $voucher_type ?? 'Journal',
                'is_create_voucher' => 0,
                'is_audit_only' => true,
                'financial_year_id' => $financial_year_id ?? app(FinancialYearService::class)->findForDate($date)?->id,
                'reference_type' => $reference_type,
                'reference' => $reference,
            ]);

            $resolvedVoucherType = $voucher_type ?? 'Journal';

            $group->voucher_no = TransactionGroup::getNextVoucherNo(
                $resolvedVoucherType,
                $date,
                $group->financial_year_id
            );
            $group->save();

            foreach ($entries as $entry) {
                $accountId = $entry['account_id'];
                $debit = $entry['debit'] ?? 0;
                $credit = $entry['credit'] ?? 0;

                $journal = Journal::where('account_id', $accountId)->first();
                if (! $journal) {
                    $journal = Journal::create(['account_id' => $accountId]);
                }

                Transaction::create([
                    'transaction_group_id' => $group->id,
                    'journal_id' => $journal->id,
                    'debit' => $debit,
                    'credit' => $credit,
                    'date' => $date,
                    'remarks' => $entry['remarks'] ?? null,
                ]);
            }

            return $group;
        });
    }
    // =============================================================================== //

    // Opening stock
    // public function recordOpeningStock(

    //     $items,
    //     $description,
    //     $date,
    //     $equityAccountId = null, // Default to Owner’s Capital
    //     $priceId = null
    // ) {
    //     // Validate items array structure

    //     $entries = [];
    //     $totalOpeningValue = 0;

    //     foreach ($items as $item) {
    //         $openingValue = round($item['quantity'] * $item['cost_price'], 2);
    //         $totalOpeningValue += $openingValue;

    //         $entries[] = ['account_id' => getAccount('Inventory'), 'debit' => $openingValue, 'credit' => 0]; // Inventory (ID 4)
    //     }
    //     $entries[] = ['account_id' => getAccount('Owner’s Capital'), 'debit' => 0, 'credit' => $totalOpeningValue]; // Equity (e.g., Owner’s Capital)

    //     return DB::transaction(function () use ($entries, $description, $date, $items, $priceId) {
    //         $group = null;

    //         if (empty($priceId)) {
    //             $group = $this->recordMultiEntryTransaction($entries, $description, $date);
    //         }

    //         foreach ($items as $item) {

    //             $inventoryItem = InventoryItem::firstOrNew(['item_id' => $item['item_id']]);
    //             // Set opening stock (assuming no prior quantity)
    //             $inventoryItem->quantity = $item['quantity'];
    //             $inventoryItem->cost_price = $item['cost_price'];
    //             $inventoryItem->selling_price = $item['selling_price'];
    //             $inventoryItem->save();

    //             if (empty($priceId)) {
    //                 InventoryTransaction::create([
    //                     'inventory_item_id' => $item['item_id'],
    //                     'transaction_group_id' => $group->id,
    //                     'type' => 'opening_stock',
    //                     'quantity' => $item['quantity'],
    //                     'cost_price' => $item['cost_price'],
    //                     'selling_price' => $item['selling_price'],
    //                     'date' => $date,
    //                 ]);
    //             }
    //         }

    //         return $group;
    //     });
    // }

    // public function recordOpeningStock(
    //     $items,
    //     $description,
    //     $date,
    //     $equityAccountId = null,
    //     $priceId = null
    // ) {
    //     $entries = [];
    //     $totalOpeningValue = 0;

    //     foreach ($items as $item) {
    //         $openingValue = round($item['quantity'] * $item['cost_price'], 2);
    //         $totalOpeningValue += $openingValue;

    //         $entries[] = [
    //             'account_id' => getAccount('Inventory'),
    //             'debit'      => $openingValue,
    //             'credit'     => 0
    //         ];
    //     }

    //     $entries[] = [
    //         'account_id' => getAccount('Owner’s Capital'),
    //         'debit'      => 0,
    //         'credit'     => $totalOpeningValue
    //     ];

    //     return DB::transaction(function () use ($entries, $description, $date, $items, $priceId) {
    //         $group = null;

    //         if (empty($priceId)) {
    //             // Fresh entry
    //             $group = $this->recordMultiEntryTransaction($entries, $description, $date);
    //         } else {
    //             // Reverse old accounting entry
    //             $reverseEntries = [];
    //             foreach ($entries as $entry) {
    //                 $reverseEntries[] = [
    //                     'account_id' => $entry['account_id'],
    //                     'debit'      => $entry['credit'], // swap
    //                     'credit'     => $entry['debit'],
    //                 ];
    //             }

    //             $reverseDescription = "Reversal for Opening Stock update (ID: {$priceId})";
    //             $this->recordMultiEntryTransaction($reverseEntries, $reverseDescription, $date);

    //             // Record new entry
    //             $group = $this->recordMultiEntryTransaction($entries, $description, $date);
    //         }

    //         foreach ($items as $item) {
    //             $inventoryItem = InventoryItem::where('item_id', $item['item_id'])->first();
    //             $inventoryItem->quantity      = $item['quantity'];
    //             $inventoryItem->cost_price    = $item['cost_price'];
    //             $inventoryItem->selling_price = $item['selling_price'];
    //             $inventoryItem->save();

    //             InventoryTransaction::create([
    //                 'inventory_item_id'    => $inventoryItem->id,
    //                 'transaction_group_id' => $group->id,
    //                 'type'                 => 'opening_stock',
    //                 'quantity'             => $item['quantity'],
    //                 'cost_price'           => $item['cost_price'],
    //                 'selling_price'        => $item['selling_price'],
    //                 'date'                 => $date,
    //             ]);
    //         }

    //         return $group;
    //     });
    // }

    public function recordOpeningStock(
        $items,
        $description,
        $date,
        $equityAccountId = null,
        $openStockId = null
    ) {
        // Periodic inventory: opening stock rolls through Opening Stock Adjustment (not Inventory asset)
        $openingStockAdjId = getAccount('Opening Stock Adjustment') ?: getAccount('Inventory');
        $capitalAccountId = $equityAccountId ?: getAccount('Owner’s Capital');

        $entries = [];
        $totalOpeningValue = 0;

        foreach ($items as $item) {
            $openingValue = $this->stockMoneyValue(
                (int) $item['item_id'],
                (float) $item['quantity'],
                (float) $item['cost_price']
            );
            $totalOpeningValue += $openingValue;

            $entries[] = [
                'account_id' => $openingStockAdjId,
                'debit' => $openingValue,
                'credit' => 0,
            ];
        }

        $entries[] = [
            'account_id' => $capitalAccountId,
            'debit' => 0,
            'credit' => $totalOpeningValue,
        ];

        return DB::transaction(function () use ($entries, $description, $date, $items, $openStockId, $openingStockAdjId, $capitalAccountId) {
            $oldMovement = null;
            if ($openStockId) {
                $oldMovement = DB::table('stock_movements')->where('stock_movement_id', $openStockId)->first();
                if ($oldMovement) {
                    $oldInventory = InventoryItem::where('item_id', $oldMovement->item_id)->first();
                    $oldQty = (float) ($oldMovement->stock_in ?? 0);
                    $oldCost = (float) ($oldInventory->cost_price ?? 0);
                    $oldValue = $this->stockMoneyValue((int) $oldMovement->item_id, $oldQty, $oldCost);

                    if ($oldValue >= 0.01) {
                        $this->recordMultiEntryTransaction([
                            [
                                'account_id' => $openingStockAdjId,
                                'debit' => 0,
                                'credit' => $oldValue,
                            ],
                            [
                                'account_id' => $capitalAccountId,
                                'debit' => $oldValue,
                                'credit' => 0,
                            ],
                        ], "Reversal for Opening Stock update (ID: {$openStockId})", $date);
                    }
                }
            }

            $group = $this->recordMultiEntryTransaction($entries, $description, $date);

            foreach ($items as $item) {
                $inventoryItem = InventoryItem::firstOrCreate(
                    ['item_id' => $item['item_id']],
                    ['quantity' => 0, 'cost_price' => 0, 'selling_price' => 0]
                );

                // Adjust on-hand inventory by opening qty change (never overwrite absolute qty).
                $newOpeningQty = (float) $item['quantity'];
                $oldOpeningQty = 0.0;
                if ($openStockId && isset($oldMovement) && (int) $oldMovement->item_id === (int) $item['item_id']) {
                    $oldOpeningQty = (float) ($oldMovement->stock_in ?? 0);
                }
                $inventoryItem->quantity = (float) $inventoryItem->quantity + ($newOpeningQty - $oldOpeningQty);
                $inventoryItem->cost_price = $item['cost_price'];
                $inventoryItem->selling_price = $item['selling_price'];
                $inventoryItem->save();

                InventoryTransaction::create([
                    'inventory_item_id' => $inventoryItem->id,
                    'transaction_group_id' => $group->id,
                    'type' => 'opening_stock',
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'],
                    'selling_price' => $item['selling_price'],
                    'date' => $date,
                ]);

                if ($openStockId) {
                    DB::table('stock_movements')
                        ->where('stock_movement_id', $openStockId)
                        ->update([
                            'stock_in' => $item['quantity'],
                            'stock_out' => 0,
                            'description' => $description,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('stock_movements')->insert([
                        'item_id' => $item['item_id'],
                        'stock_in' => $item['quantity'],
                        'stock_out' => 0,
                        'description' => $description,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            return $group;
        });
    }

    /**
     * Quantity-only stock adjustment. Periodic inventory already values stock through
     * Stock in Hand / closing stock, so posting Inventory Adjustment Gain/Loss here
     * would double-count the same amount in Retained Earnings and unbalance the BS.
     */
    public function recordStockAdjustment(
        int $itemId,
        float $quantity,
        string $direction,
        string $description,
        $date,
        float $costPrice = 0,
        float $sellingPrice = 0
    ) {
        $qty = abs($quantity);
        if ($qty <= 0) {
            throw new \Exception('Adjustment quantity must be greater than zero.');
        }

        $isIncrease = strtolower($direction) === 'increase';

        return DB::transaction(function () use (
            $itemId,
            $qty,
            $isIncrease,
            $description,
            $date,
            $costPrice,
            $sellingPrice
        ) {
            $inventoryItem = InventoryItem::firstOrCreate(
                ['item_id' => $itemId],
                ['quantity' => 0, 'cost_price' => $costPrice, 'selling_price' => $sellingPrice]
            );

            if ($isIncrease) {
                $inventoryItem->quantity = (float) $inventoryItem->quantity + $qty;
            } else {
                $newInvQty = (float) $inventoryItem->quantity - $qty;
                if ($newInvQty < -0.0001) {
                    throw new \Exception('Adjustment would make inventory quantity negative.');
                }
                $inventoryItem->quantity = max(0, $newInvQty);
            }
            $inventoryItem->save();

            InventoryTransaction::create([
                'inventory_item_id' => $inventoryItem->id,
                'transaction_group_id' => null,
                'type' => 'adjustment',
                'quantity' => $isIncrease ? $qty : -$qty,
                'cost_price' => $costPrice,
                'selling_price' => $sellingPrice,
                'date' => $date,
            ]);

            DB::table('stock_movements')->insert([
                'item_id' => $itemId,
                'stock_in' => $isIncrease ? $qty : 0,
                'stock_out' => $isIncrease ? 0 : $qty,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return null;
        });
    }

    /**
     * Post a purchase once against the vendor ledger, then settle cash/bank/online portions.
     * Credit portions remain payable on the vendor ledger.
     */
    /**
     * Line amounts for a purchase or purchase return.
     *
     * When discount_percent is present, the discount is a percent of the taxable
     * rate and GST is calculated on the discounted amount. Rows without a percent
     * keep the earlier rule: tax on the gross amount, then a rupee discount.
     */
    public function purchaseLineFigures(array $item): array
    {
        $qty = (float) ($item['quantity'] ?? 0);
        $rate = (float) ($item['cost_price'] ?? 0);
        $taxPercent = (float) ($item['tax'] ?? $item['tax_percent'] ?? 0);
        $gross = round($qty * $rate, 2);

        $percent = $item['discount_percent'] ?? null;
        $usesPercent = array_key_exists('discount_percent', $item)
            && $percent !== null
            && $percent !== '';

        if ($usesPercent) {
            $discountPercent = round((float) $percent, 4);
            $taxable = round($qty * $rate * (1 - ($discountPercent / 100)), 2);
            $discount = round($gross - $taxable, 2);
            $tax = round($taxable * $taxPercent / 100, 2);
        } else {
            $discountPercent = null;
            $discount = round((float) ($item['discount'] ?? 0), 2);
            $taxable = round($gross - $discount, 2);
            $tax = round($gross * $taxPercent / 100, 2);
        }

        return [
            'gross' => $gross,
            'discount_percent' => $discountPercent,
            'discount' => $discount,
            'taxable' => $taxable,
            'tax' => $tax,
            'payable' => round($taxable + $tax, 2),
        ];
    }

    /**
     * @param  bool  $roundToRupee  Nearest-rupee round off used on GST bills.
     */
    public function purchaseDocumentTotals(array $items, bool $roundToRupee = true, float $freightAmount = 0.0, float $freightTaxPercent = 0.0): array
    {
        $gross = 0.0;
        $discount = 0.0;
        $taxable = 0.0;
        $tax = 0.0;

        foreach ($items as $item) {
            $line = $this->purchaseLineFigures($item);
            $gross += $line['gross'];
            $discount += $line['discount'];
            $taxable += $line['taxable'];
            $tax += $line['tax'];
        }

        $gross = round($gross, 2);
        $discount = round($discount, 2);
        $taxable = round($taxable, 2);
        $tax = round($tax, 2);
        $freightAmount = round(max(0, $freightAmount), 2);
        $freightTaxPercent = round(min(100, max(0, $freightTaxPercent)), 2);
        $freightTax = round($freightAmount * $freightTaxPercent / 100, 2);
        $taxWithFreight = round($tax + $freightTax, 2);
        $exact = round($taxable + $tax + $freightAmount + $freightTax, 2);
        $grand = $roundToRupee ? (float) round($exact, 0) : $exact;
        $roundOff = round($grand - $exact, 2);
        $cgst = round($taxWithFreight / 2, 2);
        $sgst = round($taxWithFreight - $cgst, 2);

        return [
            'gross' => $gross,
            'discount' => $discount,
            'taxable' => $taxable,
            'tax' => $tax,
            'freight' => $freightAmount,
            'freight_tax_percent' => $freightTaxPercent,
            'freight_tax' => $freightTax,
            'cgst' => $cgst,
            'sgst' => $sgst,
            'exact' => $exact,
            'round_off' => $roundOff,
            'grand' => $grand,
        ];
    }

    public function recordPurchaseWithPayments($vendorId, $items, array $payments, $description, $date, float $roundOff = 0.0, float $freightAmount = 0.0, float $freightTax = 0.0)
    {
        // Periodic inventory: purchases are Direct Expense (Purchase Account), not Inventory asset
        $purchaseAccountId = getAccount('Purchase Account') ?: getAccount('Inventory');
        $gstAccountId = getAccount('Input Tax (GST/VAT Credit)');
        $discountAccountId = getAccount('Discount Received');
        $vendorLedgerId = $this->getOrCreateVendorLedger($vendorId);
        $cashAccountId = getAccount('Cash');
        $bankAccountId = getAccount('Bank');

        $entries = [];
        $totalPurchaseAmount = 0;
        $totalTaxAmount = 0;
        $totalDiscountAmount = 0;

        foreach ($items as $item) {
            $line = $this->purchaseLineFigures($item);

            $totalPurchaseAmount += $line['taxable'];
            $totalTaxAmount += $line['tax'];
            $totalDiscountAmount += $line['discount'];

            $entries[] = ['account_id' => $purchaseAccountId, 'debit' => $line['gross'], 'credit' => 0];
        }

        $roundOff = round($roundOff, 2);
        $freightAmount = round(max(0, $freightAmount), 2);
        $freightTax = round(max(0, $freightTax), 2);
        $totalTaxAmount = round($totalTaxAmount + $freightTax, 2);
        $payableAmount = round($totalPurchaseAmount + $totalTaxAmount + $freightAmount + $roundOff, 2);

        if ($freightAmount > 0) {
            $freightAccountId = (int) getAccount('Freight Inwards');
            if (! $freightAccountId) {
                throw new \Exception(__('Freight Inwards account is missing from the chart of accounts.'));
            }
            $entries[] = ['account_id' => $freightAccountId, 'debit' => $freightAmount, 'credit' => 0];
        }

        if ($totalTaxAmount > 0) {
            $entries[] = ['account_id' => $gstAccountId, 'debit' => $totalTaxAmount, 'credit' => 0];
        }

        $entries[] = ['account_id' => $vendorLedgerId, 'debit' => 0, 'credit' => $payableAmount];

        if ($totalDiscountAmount > 0) {
            $entries[] = ['account_id' => $discountAccountId, 'debit' => 0, 'credit' => $totalDiscountAmount];
        }

        $roundOffEntry = $this->roundOffEntry($roundOff, false);
        if ($roundOffEntry) {
            $entries[] = $roundOffEntry;
        }

        $this->validateOutgoingPaymentRows($payments);

        foreach ($payments as $payment) {
            $mode = strtolower((string) ($payment['payment_mode'] ?? ''));
            $amount = round((float) ($payment['amount_taken'] ?? 0), 2);
            if ($amount < 0.01 || $mode === 'credit') {
                continue;
            }

            $paymentAccountId = match ($mode) {
                'cash' => $cashAccountId,
                'bank', 'online' => $bankAccountId,
                default => null,
            };

            if (! $paymentAccountId) {
                continue;
            }

            $entries[] = ['account_id' => $vendorLedgerId, 'debit' => $amount, 'credit' => 0];
            $entries[] = ['account_id' => $paymentAccountId, 'debit' => 0, 'credit' => $amount];
        }

        return DB::transaction(function () use ($entries, $description, $date, $vendorId, $items) {
            $group = $this->recordMultiEntryTransaction($entries, $description, $date);

            Transaction::where('transaction_group_id', $group->id)->update(['vendor_id' => $vendorId]);

            foreach ($items as $item) {
                $inventoryItem = InventoryItem::where('item_id', $item['item_id'])->first();
                if (! $inventoryItem) {
                    continue;
                }

                $oldTotalCost = $inventoryItem->cost_price * $inventoryItem->quantity;
                $newTotalCost = $oldTotalCost + ($item['quantity'] * $item['cost_price']);
                $inventoryItem->quantity += $item['quantity'];

                if ($inventoryItem->costing_method === 'weighted_average' && $inventoryItem->quantity > 0) {
                    $inventoryItem->cost_price = $newTotalCost / $inventoryItem->quantity;
                }

                $inventoryItem->save();

                InventoryTransaction::create([
                    'inventory_item_id' => $inventoryItem->id,
                    'transaction_group_id' => $group->id,
                    'type' => 'purchase',
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'],
                    'date' => $date,
                ]);
            }

            return $group;
        });
    }

    public function recordCreditPurchaseWithInventory($vendorId, $items, $description, $date)
    {
        return $this->recordPurchaseWithPayments($vendorId, $items, [
            ['payment_mode' => 'credit', 'amount_taken' => 0],
        ], $description, $date);
    }

    public function recordCashPurchaseWithInventory($vendorId, $items, $description, $date, $vendorLedgerId = null)
    {
        $payable = $this->calculatePurchasePayable($items);

        return $this->recordPurchaseWithPayments($vendorId, $items, [
            ['payment_mode' => 'cash', 'amount_taken' => $payable],
        ], $description, $date);
    }

    public function recordBankPurchaseWithInventory($vendorId, $items, $description, $date)
    {
        $payable = $this->calculatePurchasePayable($items);

        return $this->recordPurchaseWithPayments($vendorId, $items, [
            ['payment_mode' => 'bank', 'amount_taken' => $payable],
        ], $description, $date);
    }

    public function recordOnlinePurchaseWithInventory($vendorId, $items, $description, $date)
    {
        $payable = $this->calculatePurchasePayable($items);

        return $this->recordPurchaseWithPayments($vendorId, $items, [
            ['payment_mode' => 'online', 'amount_taken' => $payable],
        ], $description, $date);
    }

    /**
     * Opening stock is entered in the stock unit. Item cost is the price of one pack.
     */
    private function stockMoneyValue(int $itemId, float $baseQuantity, float $packCost): float
    {
        $pack = 1.0;
        $item = StockItem::find($itemId);
        if ($item) {
            $pack = $item->packSize();
        }

        return round(($baseQuantity / $pack) * $packCost, 2);
    }

    private function calculatePurchasePayable(array $items): float
    {
        $totalPurchaseAmount = 0;
        $totalTaxAmount = 0;

        foreach ($items as $item) {
            $baseAmount = round($item['quantity'] * $item['cost_price'], 2);
            $taxRate = ((float) ($item['tax'] ?? 0)) / 100;
            $discount = round((float) ($item['discount'] ?? 0), 2);
            $totalPurchaseAmount += round($baseAmount - $discount, 2);
            $totalTaxAmount += round($baseAmount * $taxRate, 2);
        }

        return round($totalPurchaseAmount + $totalTaxAmount, 2);
    }

    public function reverseTransactionGroup($transactionGroupId, $date = null)
    {
        $originalGroup = TransactionGroup::with('transactions.journal')->findOrFail($transactionGroupId);
        $originalTransactions = $originalGroup->transactions;
        $date = $date ?? now()->toDateString();
        $description = 'Reversal of: '.$originalGroup->description;

        $reversalEntries = $originalTransactions->map(function ($transaction) use ($originalGroup, $date) {
            return [
                'account_id' => $transaction->journal->account_id,
                'debit' => $transaction->credit,
                'credit' => $transaction->debit,
                'remarks' => 'Reversal of: '.$originalGroup->description,
                'date' => $date,
            ];
        })->toArray();

        return $this->recordMultiEntryTransaction($reversalEntries, $description, $date);
    }

    public function reverseInventoryTransaction(StockPurchase $stock_purchase)
    {
        $reversalDate = now();

        // Undo the quantities that were posted, including a second line of the same
        // product. The saved purchase row can already have been overwritten by then.
        $posted = collect();
        if ($stock_purchase->transaction_group_id) {
            $posted = InventoryTransaction::where('transaction_group_id', $stock_purchase->transaction_group_id)
                ->where('type', 'purchase')
                ->where('quantity', '>', 0)
                ->get();
        }

        if ($posted->isNotEmpty()) {
            foreach ($posted as $txn) {
                $inventoryItem = InventoryItem::find($txn->inventory_item_id);
                if (! $inventoryItem) {
                    continue;
                }
                $this->reverseInventoryQuantity(
                    $inventoryItem,
                    (float) $txn->quantity,
                    (float) $txn->cost_price,
                    $stock_purchase->transaction_group_id,
                    $reversalDate
                );
            }

            return;
        }

        $items = StockPurchaseItem::where('purchase_id', $stock_purchase->purchase_id)->get();

        foreach ($items as $item) {
            $inventoryItem = InventoryItem::where('item_id', $item->item_id)->first();
            if (! $inventoryItem) {
                continue;
            }
            $this->reverseInventoryQuantity(
                $inventoryItem,
                (float) $item->quantity,
                (float) $item->cost_price,
                $stock_purchase->transaction_group_id,
                $reversalDate
            );
        }
    }

    private function reverseInventoryQuantity(InventoryItem $inventoryItem, float $quantity, float $costPrice, $transactionGroupId, $reversalDate): void
    {
        InventoryTransaction::create([
            'inventory_item_id' => $inventoryItem->id,
            'transaction_group_id' => $transactionGroupId,
            'type' => 'adjustment',
            'quantity' => $quantity,
            'cost_price' => $costPrice,
            'date' => $reversalDate,
        ]);

        $inventoryItem->quantity -= $quantity;

        if ($inventoryItem->quantity > 0 && $inventoryItem->costing_method === 'weighted_average') {
            $oldTotalCost = ($inventoryItem->cost_price * ($inventoryItem->quantity + $quantity));
            $reversedCost = $quantity * $costPrice;
            $newTotalCost = $oldTotalCost - $reversedCost;
            $inventoryItem->cost_price = $newTotalCost / $inventoryItem->quantity;
        } elseif ($inventoryItem->quantity <= 0) {
            $inventoryItem->cost_price = 0;
        }

        $inventoryItem->save();
    }

    /**
     * Reverse vendor balance for a purchase (on edit). Subtracts only the credit amount
     * that was stored for this purchase (sum of credit payment rows).
     */
    public function reverseVendorBalance(StockPurchase $stock_purchase)
    {
        $creditAmount = PurchasePayment::where('purchase_id', $stock_purchase->purchase_id)
            ->where('payment_mode', PurchasePayment::PAYMENT_MODE_CREDIT)
            ->sum('amount_taken');

        if ((float) $creditAmount <= 0) {
            return;
        }

        $vendor = Vendor::findOrFail($stock_purchase->vendor_id);
        $vendor->balance -= (float) $creditAmount;
        $vendor->save();
    }

    public function recordAccounts(
        $booking_info,
        $gross_service_amount,
        $gross_item_amount,
        $taxAmount,
        $discountAmount,
        $roundOffAmount,
        $cogsAmount,
        $description,
        $date
    ) {
        $salesAccountId = getAccount('Sales Revenue');
        $serviceRevenueAccountId = getAccount('Service Revenue');
        $taxAccountId = getAccount('Output Tax Payable (GST/VAT)');
        $discountAccountId = getAccount('Sales Discounts');
        $roundOffLossAccountId = getAccount('Round Off Loss');
        $roundOffGainAccountId = getAccount('Round Off Gain');
        // Periodic inventory: COGS is derived at reporting (Opening + Purchases − Closing), not posted at sale
        $accountsReceivableId = getAccount('Accounts Receivable');
        // Client Personal Ledger
        $clientLedgerId = $this->ensureClientLedger($booking_info->client_id, $booking_info->client_name);

        $cashAccountId = getAccount('Cash');
        $bankAccountId = getAccount('Bank');

        $entries = [];
        // dd($booking_info, $gross_service_amount, $gross_item_amount, $taxAmount, $discountAmount, $roundOffAmount, $cogsAmount);

        /*

        |--------------------------------------------------------------------------
        | Calculate Invoice Amount
        |--------------------------------------------------------------------------
        */
        $invoiceAmount =
            $gross_item_amount +
            $gross_service_amount +
            $taxAmount -
            $discountAmount -
            $roundOffAmount;

        /*
        |--------------------------------------------------------------------------
        | Debit Client Ledger (Invoice Raised)
        |--------------------------------------------------------------------------
        */
        $entries[] = [
            'account_id' => $clientLedgerId,
            'debit' => $invoiceAmount,
            'credit' => 0,
            'remarks' => 'Booking Invoice',
        ];
        // Credit side
        if ($gross_item_amount > 0) {
            $entries[] = ['account_id' => $salesAccountId, 'debit' => 0, 'credit' => $gross_item_amount];
        }

        if ($gross_service_amount > 0) {
            $entries[] = ['account_id' => $serviceRevenueAccountId, 'debit' => 0, 'credit' => $gross_service_amount];
        }

        if ($taxAmount > 0) {
            $entries[] = ['account_id' => $taxAccountId, 'debit' => 0, 'credit' => $taxAmount];
        }

        if ($discountAmount > 0) {
            $entries[] = ['account_id' => $discountAccountId, 'debit' => $discountAmount, 'credit' => 0];
        }
        if ($roundOffAmount != 0) {
            if ($roundOffAmount > 0) {
                // ROUND OFF LOSS (Debit)
                $entries[] = [
                    'account_id' => $roundOffLossAccountId,
                    'debit' => $roundOffAmount,
                    'credit' => 0,
                ];
            } else {
                // ROUND OFF GAIN (Credit)
                $entries[] = [
                    'account_id' => $roundOffGainAccountId,
                    'debit' => 0,
                    'credit' => abs($roundOffAmount),
                ];
            }
        }
        // Periodic inventory: do not post Dr COGS / Cr Inventory at sale.
        // Stock quantity still updates separately; P&L uses Opening + Purchases − Stock in Hand.

        // Debit side – Payments
        // amount_taken = cash/bank received; amount_given = change.
        // Returned change → post NET (taken - given).
        // change_as_credit → post FULL taken (change kept; client ledger holds prepaid).
        foreach ($booking_info->payments as $payment) {
            $amount = $this->resolvePaymentAccountingAmount($payment);
            if ($amount == 0.0) {
                continue;
            }
            switch (strtolower($payment->payment_mode)) {
                case 'cash':
                    $entries[] = ['account_id' => $cashAccountId, 'debit' => $amount, 'credit' => 0];
                    $entries[] = [
                        'account_id' => $clientLedgerId,
                        'debit' => 0,
                        'credit' => $amount,
                    ];
                    break;
                case 'online':
                case 'card':
                    $entries[] = ['account_id' => $bankAccountId, 'debit' => $amount, 'credit' => 0];
                    $entries[] = [
                        'account_id' => $clientLedgerId,
                        'debit' => 0,
                        'credit' => $amount,
                    ];
                    break;
                case 'credit':
                    $entries[] = [
                        'account_id' => $clientLedgerId,
                        'debit' => 0,
                        'credit' => $amount,
                        'remarks' => ! empty($payment->prepaid_applied)
                            ? 'Client prepaid payment'
                            : 'Client credit payment',
                    ];
                    $entries[] = [
                        'account_id' => getAccount('Accounts Receivable'),
                        'debit' => $amount,
                        'credit' => 0,
                        'remarks' => ! empty($payment->prepaid_applied)
                            ? 'Client prepaid settlement'
                            : 'Client credit settlement',
                    ];
                    break;
                default:
                    Log::warning("Unknown payment mode: {$payment->payment_mode}");
            }
        }

        // Verify balance
        $totalDebits = array_sum(array_column($entries, 'debit'));
        $totalCredits = array_sum(array_column($entries, 'credit'));

        if (abs($totalDebits - $totalCredits) > 0.01) {
            \Log::error("Unbalanced entries for booking ID {$booking_info->booking_id}: Debits = $totalDebits, Credits = $totalCredits");
            throw new \Exception("Accounting entries are unbalanced. Debits ($totalDebits) do not equal Credits ($totalCredits).");
        }

        // Ensure balanced double-entry
        $this->recordMultiEntryTransaction($entries, $description, $date);
    }

    /**
     * Resolve cash/bank amount to post for a booking payment.
     * Returned change → net (taken - given). Kept as client credit → full taken.
     * Prepaid applied from previous credit → 0 (no cash movement).
     */
    private function resolvePaymentAccountingAmount($payment): float
    {
        $taken = (float) ($payment->amount_taken ?? 0);
        $given = (float) ($payment->amount_given ?? 0);
        $changeAsCredit = ! empty($payment->change_as_credit);

        if ($given > 0 && ! $changeAsCredit) {
            return round($taken - $given, 2);
        }

        return round($taken, 2);
    }

    // Expense Section
    public function recordCashExpense($expenseAmount, $description, $date, $expenseAccountId)
    {
        $this->validateAccountBalance('cash', $expenseAmount);
        $cashAccountId = getAccount('Cash');

        $entries = [
            ['account_id' => $expenseAccountId, 'debit' => $expenseAmount, 'credit' => 0],
            ['account_id' => $cashAccountId, 'debit' => 0, 'credit' => $expenseAmount],
        ];

        $this->recordMultiEntryTransaction($entries, $description, $date);
    }

    public function recordBankExpense($expenseAmount, $description, $date, $expenseAccountId)
    {
        $this->validateAccountBalance('bank', $expenseAmount);
        $bankAccountId = getAccount('Bank');

        $entries = [
            ['account_id' => $expenseAccountId, 'debit' => $expenseAmount, 'credit' => 0],
            ['account_id' => $bankAccountId, 'debit' => 0, 'credit' => $expenseAmount],
        ];

        $this->recordMultiEntryTransaction($entries, $description, $date);
    }

    public function reverseCashExpense($expenseAmount, $description, $date, $expenseAccountId)
    {
        $cashAccountId = getAccount('Cash');

        $entries = [
            ['account_id' => $expenseAccountId, 'debit' => 0, 'credit' => $expenseAmount],
            ['account_id' => $cashAccountId, 'debit' => $expenseAmount, 'credit' => 0],
        ];

        $this->recordMultiEntryTransaction($entries, $description, $date);
    }

    public function reverseBankExpense($expenseAmount, $description, $date, $expenseAccountId)
    {
        $bankAccountId = getAccount('Bank');

        $entries = [
            ['account_id' => $expenseAccountId, 'debit' => 0, 'credit' => $expenseAmount],
            ['account_id' => $bankAccountId, 'debit' => $expenseAmount, 'credit' => 0],
        ];

        $this->recordMultiEntryTransaction($entries, $description, $date);
    }

    // Vendor Credit Payment Section
    public function recordCreditCashPayment($amount, $description, $date, $vendorId)
    {
        $this->validateAccountBalance('cash', $amount);
        $cashAccountId = getAccount('Cash');
        $vendorLedgerId = $this->getOrCreateVendorLedger($vendorId);

        $entries = [
            ['account_id' => $vendorLedgerId, 'debit' => $amount, 'credit' => 0], // Reduce vendor payable
            ['account_id' => $cashAccountId, 'debit' => 0, 'credit' => $amount], // Cash Account
        ];

        return DB::transaction(function () use ($entries, $description, $date, $vendorId, $amount) {
            $group = $this->recordMultiEntryTransaction($entries, $description, $date);

            $vendor = Vendor::findOrFail($vendorId);
            $vendor->balance = max(0, round((float) $vendor->balance - $amount, 2));
            $vendor->save();

            Transaction::where('transaction_group_id', $group->id)->update(['vendor_id' => $vendorId]);

            return $group;
        });
    }

    public function recordCreditBankPayment($amount, $description, $date, $vendorId)
    {
        $this->validateAccountBalance('bank', $amount);
        $bankAccountId = getAccount('Bank');
        $vendorLedgerId = $this->getOrCreateVendorLedger($vendorId);

        $entries = [
            ['account_id' => $vendorLedgerId, 'debit' => $amount, 'credit' => 0], // Reduce vendor payable
            ['account_id' => $bankAccountId, 'debit' => 0, 'credit' => $amount],
        ];

        return DB::transaction(function () use ($entries, $description, $date, $vendorId, $amount) {
            $group = $this->recordMultiEntryTransaction($entries, $description, $date);

            $vendor = Vendor::findOrFail($vendorId);
            $vendor->balance = max(0, round((float) $vendor->balance - $amount, 2));
            $vendor->save();

            Transaction::where('transaction_group_id', $group->id)->update(['vendor_id' => $vendorId]);

            return $group;
        });
    }

    /**
     * Record vendor payment as credit (add to amount owed - deferred payment).
     */
    public function recordVendorCreditPayment($amount, $description, $date, $vendorId)
    {
        $vendorLedgerId = $this->getOrCreateVendorLedger($vendorId);
        $expenseAccountId = getAccount('Purchase Account') ?: getAccount('Cost of Goods Sold');

        if (! $expenseAccountId || ! $vendorLedgerId) {
            throw new \Exception(__('Required accounting accounts are not configured. Please ensure Purchase Account and vendor ledger exist.'));
        }

        $entries = [
            ['account_id' => $expenseAccountId, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $vendorLedgerId, 'debit' => 0, 'credit' => $amount],
        ];

        return DB::transaction(function () use ($entries, $description, $date, $vendorId, $amount) {
            $group = $this->recordMultiEntryTransaction($entries, $description, $date);

            $vendor = Vendor::findOrFail($vendorId);
            $vendor->balance += $amount;
            $vendor->save();

            Transaction::where('transaction_group_id', $group->id)->update(['vendor_id' => $vendorId]);

            return $group;
        });
    }

    // Client Credit Payment Section
    public function recordClientCreditCashPayment($amount, $description, $date, $clientId)
    {
        $cashAccountId = getAccount('Cash');
        $client = Client::findOrFail($clientId);
        $clientName = User::find($clientId)?->name ?? ('Client '.$clientId);
        $clientLedgerId = $this->ensureClientLedger($clientId, $clientName);

        $entries = [
            ['account_id' => $cashAccountId, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $clientLedgerId, 'debit' => 0, 'credit' => $amount],
        ];

        return DB::transaction(function () use ($entries, $description, $date, $client, $amount) {
            $group = $this->recordMultiEntryTransaction($entries, $description, $date);

            $client->balance = max(0, $client->balance - $amount);
            $client->save();

            Transaction::where('transaction_group_id', $group->id)->update(['client_id' => $client->client_id]);

            return $group;
        });
    }

    public function recordClientCreditBankPayment($amount, $description, $date, $clientId)
    {
        $bankAccountId = getAccount('Bank');
        $client = Client::findOrFail($clientId);
        $clientName = User::find($clientId)?->name ?? ('Client '.$clientId);
        $clientLedgerId = $this->ensureClientLedger($clientId, $clientName);

        $entries = [
            ['account_id' => $bankAccountId, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $clientLedgerId, 'debit' => 0, 'credit' => $amount],
        ];

        return DB::transaction(function () use ($entries, $description, $date, $client, $amount) {
            $group = $this->recordMultiEntryTransaction($entries, $description, $date);

            $client->balance = max(0, $client->balance - $amount);
            $client->save();

            Transaction::where('transaction_group_id', $group->id)->update(['client_id' => $client->client_id]);

            return $group;
        });
    }

    // Employee advance salary.
    // Dr the employee's existing "NAME SALARY ADVANCE" ledger, Cr cash or bank.
    public function recordAdvanceSalary($amount, $description, $date, $employeeId, $paymentMode)
    {
        $this->validateAccountBalance($paymentMode, $amount);
        $advanceLedgerId = $this->ensureEmployeeAdvanceLedger($employeeId);
        $paymentAccountId = $this->resolveCashOrBankAccountId($paymentMode);

        $entries = [
            ['account_id' => $advanceLedgerId, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $paymentAccountId, 'debit' => 0, 'credit' => $amount],
        ];

        $this->recordMultiEntryTransaction($entries, $description, $date, 'Payment', 1);
    }

    public function reverseAdvanceSalary($amount, $description, $date, $employeeId, $paymentMode)
    {
        $advanceLedgerId = $this->ensureEmployeeAdvanceLedger($employeeId);
        $paymentAccountId = $this->resolveCashOrBankAccountId($paymentMode);

        $entries = [
            ['account_id' => $advanceLedgerId, 'debit' => 0, 'credit' => $amount],
            ['account_id' => $paymentAccountId, 'debit' => $amount, 'credit' => 0],
        ];

        $this->recordMultiEntryTransaction($entries, $description, $date, 'Payment', 1);
    }

    /**
     * Accrue payroll for an employee.
     *
     * Dr Salaries & Wages (gross + OT - leave)
     *   Cr TDS Payable
     *   Cr Professional Tax Payable
     *   Cr Employee Salary Payable (net + OT - leave)
     * Then clear advance once (if any):
     * Dr Employee Salary Payable / Cr Employee Advance Ledger
     */
    public function recordPayrollGeneration(
        $employeeId,
        $grossSalary,
        $overtimeAmount,
        $leaveDeduction,
        $tdsAmount,
        $professionalTaxAmount,
        $advanceAmount,
        $description,
        $date
    ) {
        $salaryLedgerId = $this->ensureEmployeeSalaryLedger($employeeId);
        $salariesWagesId = $this->requireAccountId('Salaries & Wages');
        $tdsPayableId = $this->requireAccountId('TDS Payable');
        $professionalTaxId = $this->requireAccountId('Professional Tax Payable');

        $grossSalary = round((float) $grossSalary, 2);
        $overtimeAmount = round((float) $overtimeAmount, 2);
        $leaveDeduction = round((float) $leaveDeduction, 2);
        $tdsAmount = round((float) $tdsAmount, 2);
        $professionalTaxAmount = round((float) $professionalTaxAmount, 2);
        $advanceAmount = round((float) $advanceAmount, 2);

        $expenseAmount = round($grossSalary + $overtimeAmount - $leaveDeduction, 2);
        if ($expenseAmount < 0) {
            $expenseAmount = 0;
        }

        $salaryPayable = round($expenseAmount - $tdsAmount - $professionalTaxAmount, 2);
        if ($salaryPayable < 0) {
            throw new \Exception('Payroll tax deductions exceed salary expense for employee #'.$employeeId);
        }

        $entries = [
            [
                'account_id' => $salariesWagesId,
                'debit' => $expenseAmount,
                'credit' => 0,
            ],
        ];

        if ($tdsAmount > 0) {
            $entries[] = [
                'account_id' => $tdsPayableId,
                'debit' => 0,
                'credit' => $tdsAmount,
            ];
        }

        if ($professionalTaxAmount > 0) {
            $entries[] = [
                'account_id' => $professionalTaxId,
                'debit' => 0,
                'credit' => $professionalTaxAmount,
            ];
        }

        if ($salaryPayable > 0) {
            $entries[] = [
                'account_id' => $salaryLedgerId,
                'debit' => 0,
                'credit' => $salaryPayable,
            ];
        }

        $this->recordMultiEntryTransaction($entries, $description, $date);

        $advanceToClear = min($advanceAmount, $salaryPayable);
        if ($advanceToClear > 0) {
            $advanceLedgerId = $this->ensureEmployeeAdvanceLedger($employeeId);
            $this->recordMultiEntryTransaction([
                [
                    'account_id' => $salaryLedgerId,
                    'debit' => $advanceToClear,
                    'credit' => 0,
                ],
                [
                    'account_id' => $advanceLedgerId,
                    'debit' => 0,
                    'credit' => $advanceToClear,
                ],
            ], 'Advance adjustment on payroll for employee #'.$employeeId, $date);
        }
    }

    /**
     * Reverse a previously posted payroll generation (and its advance clear, if any).
     * Uses "Reversal of: {description}" so ledger/P&L filters can hide the pair.
     */
    public function reversePayrollGeneration(
        $employeeId,
        $grossSalary,
        $overtimeAmount,
        $leaveDeduction,
        $tdsAmount,
        $professionalTaxAmount,
        $advanceAmount,
        $description,
        $date
    ) {
        $salaryLedgerId = $this->ensureEmployeeSalaryLedger($employeeId);
        $salariesWagesId = $this->requireAccountId('Salaries & Wages');
        $tdsPayableId = $this->requireAccountId('TDS Payable');
        $professionalTaxId = $this->requireAccountId('Professional Tax Payable');

        $grossSalary = round((float) $grossSalary, 2);
        $overtimeAmount = round((float) $overtimeAmount, 2);
        $leaveDeduction = round((float) $leaveDeduction, 2);
        $tdsAmount = round((float) $tdsAmount, 2);
        $professionalTaxAmount = round((float) $professionalTaxAmount, 2);
        $advanceAmount = round((float) $advanceAmount, 2);

        $expenseAmount = round($grossSalary + $overtimeAmount - $leaveDeduction, 2);
        if ($expenseAmount < 0) {
            $expenseAmount = 0;
        }

        $salaryPayable = round($expenseAmount - $tdsAmount - $professionalTaxAmount, 2);
        if ($salaryPayable < 0) {
            $salaryPayable = 0;
        }

        $advanceToClear = min($advanceAmount, $salaryPayable);
        if ($advanceToClear > 0) {
            $advanceLedgerId = $this->ensureEmployeeAdvanceLedger($employeeId);
            $this->recordMultiEntryTransaction([
                [
                    'account_id' => $salaryLedgerId,
                    'debit' => 0,
                    'credit' => $advanceToClear,
                ],
                [
                    'account_id' => $advanceLedgerId,
                    'debit' => $advanceToClear,
                    'credit' => 0,
                ],
            ], 'Reversal of: Advance adjustment on payroll for employee #'.$employeeId, $date);
        }

        $entries = [
            [
                'account_id' => $salariesWagesId,
                'debit' => 0,
                'credit' => $expenseAmount,
            ],
        ];

        if ($tdsAmount > 0) {
            $entries[] = [
                'account_id' => $tdsPayableId,
                'debit' => $tdsAmount,
                'credit' => 0,
            ];
        }

        if ($professionalTaxAmount > 0) {
            $entries[] = [
                'account_id' => $professionalTaxId,
                'debit' => $professionalTaxAmount,
                'credit' => 0,
            ];
        }

        if ($salaryPayable > 0) {
            $entries[] = [
                'account_id' => $salaryLedgerId,
                'debit' => $salaryPayable,
                'credit' => 0,
            ];
        }

        if ($expenseAmount > 0 || $tdsAmount > 0 || $professionalTaxAmount > 0 || $salaryPayable > 0) {
            $this->recordMultiEntryTransaction(
                $entries,
                'Reversal of: '.$description,
                $date
            );
        }
    }

    /**
     * Settle salary payable against Cash/Bank. Taxes and advances are handled at generation.
     */
    public function recordEmployeeSalary(
        $employeeId,
        $paymentAmount,
        $description,
        $date,
        $paymentMode = 'bank'
    ) {
        $paymentAmount = round((float) $paymentAmount, 2);
        if ($paymentAmount <= 0) {
            throw new \Exception('Salary payment amount must be greater than zero.');
        }

        $this->validateAccountBalance($paymentMode, $paymentAmount);
        $salaryLedgerId = $this->ensureEmployeeSalaryLedger($employeeId);
        $paymentAccountId = $this->resolveCashOrBankAccountId($paymentMode);

        return $this->recordMultiEntryTransaction([
            [
                'account_id' => $salaryLedgerId,
                'debit' => $paymentAmount,
                'credit' => 0,
            ],
            [
                'account_id' => $paymentAccountId,
                'debit' => 0,
                'credit' => $paymentAmount,
            ],
        ], $description, $date, 'Payment');
    }

    public function reverseEmployeeSalary(
        $employeeId,
        $paymentAmount,
        $description,
        $date,
        $paymentMode = 'bank'
    ) {
        $paymentAmount = round((float) $paymentAmount, 2);
        if ($paymentAmount <= 0) {
            return;
        }

        $salaryLedgerId = $this->ensureEmployeeSalaryLedger($employeeId);
        $paymentAccountId = $this->resolveCashOrBankAccountId($paymentMode);

        $this->recordMultiEntryTransaction([
            [
                'account_id' => $salaryLedgerId,
                'debit' => 0,
                'credit' => $paymentAmount,
            ],
            [
                'account_id' => $paymentAccountId,
                'debit' => $paymentAmount,
                'credit' => 0,
            ],
        ], $description, $date);
    }

    /**
     * Accrue beautician commission for a period (separate from salary payroll).
     * Dr Commission Expense / Cr employee Commission Payable ledger
     */
    public function recordCommissionAccrual($employeeId, $amount, $description, $date)
    {
        $amount = round((float) $amount, 2);
        if ($amount <= 0) {
            return null;
        }

        $expenseId = $this->requireAccountId('Commission Expense');
        $payableId = $this->ensureEmployeeCommissionLedger($employeeId);

        return $this->recordMultiEntryTransaction([
            ['account_id' => $expenseId, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $payableId, 'debit' => 0, 'credit' => $amount],
        ], $description, $date, 'Journal');
    }

    /**
     * Accrue employee incentive for a period (separate from salary payroll).
     * Dr Incentive Expense / Cr employee Incentive Payable ledger
     */
    public function recordIncentiveAccrual($employeeId, $amount, $description, $date)
    {
        $amount = round((float) $amount, 2);
        if ($amount <= 0) {
            return null;
        }

        $expenseId = $this->requireAccountId('Incentive Expense');
        $payableId = $this->ensureEmployeeIncentiveLedger($employeeId);

        return $this->recordMultiEntryTransaction([
            ['account_id' => $expenseId, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $payableId, 'debit' => 0, 'credit' => $amount],
        ], $description, $date, 'Journal');
    }

    /**
     * Pay commission separately from salary.
     * Dr employee Commission Payable / Cr Cash or Bank
     */
    public function recordCommissionPayment($employeeId, $amount, $description, $date, $paymentMode = 'bank')
    {
        $amount = round((float) $amount, 2);
        if ($amount <= 0) {
            throw new \Exception('Commission payment amount must be greater than zero.');
        }

        $this->validateAccountBalance($paymentMode, $amount);
        $payableId = $this->ensureEmployeeCommissionLedger($employeeId);
        $paymentAccountId = $this->resolveCashOrBankAccountId($paymentMode);

        return $this->recordMultiEntryTransaction([
            ['account_id' => $payableId, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $paymentAccountId, 'debit' => 0, 'credit' => $amount],
        ], $description, $date, 'Payment');
    }

    /**
     * Pay incentive separately from salary.
     * Dr employee Incentive Payable / Cr Cash or Bank
     */
    public function recordIncentivePayment($employeeId, $amount, $description, $date, $paymentMode = 'bank')
    {
        $amount = round((float) $amount, 2);
        if ($amount <= 0) {
            throw new \Exception('Incentive payment amount must be greater than zero.');
        }

        $this->validateAccountBalance($paymentMode, $amount);
        $payableId = $this->ensureEmployeeIncentiveLedger($employeeId);
        $paymentAccountId = $this->resolveCashOrBankAccountId($paymentMode);

        return $this->recordMultiEntryTransaction([
            ['account_id' => $payableId, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $paymentAccountId, 'debit' => 0, 'credit' => $amount],
        ], $description, $date, 'Payment');
    }

    public function reverseCommissionPayment($employeeId, $amount, $description, $date, $paymentMode = 'bank')
    {
        $amount = round((float) $amount, 2);
        if ($amount <= 0) {
            return null;
        }

        $payableId = $this->ensureEmployeeCommissionLedger($employeeId);
        $paymentAccountId = $this->resolveCashOrBankAccountId($paymentMode);

        return $this->recordMultiEntryTransaction([
            ['account_id' => $payableId, 'debit' => 0, 'credit' => $amount],
            ['account_id' => $paymentAccountId, 'debit' => $amount, 'credit' => 0],
        ], $description, $date, 'Payment');
    }

    public function reverseIncentivePayment($employeeId, $amount, $description, $date, $paymentMode = 'bank')
    {
        $amount = round((float) $amount, 2);
        if ($amount <= 0) {
            return null;
        }

        $payableId = $this->ensureEmployeeIncentiveLedger($employeeId);
        $paymentAccountId = $this->resolveCashOrBankAccountId($paymentMode);

        return $this->recordMultiEntryTransaction([
            ['account_id' => $payableId, 'debit' => 0, 'credit' => $amount],
            ['account_id' => $paymentAccountId, 'debit' => $amount, 'credit' => 0],
        ], $description, $date, 'Payment');
    }

    private function resolveCashOrBankAccountId($paymentMode): int
    {
        $mode = strtolower((string) $paymentMode);
        $accountName = $mode === 'cash' ? 'Cash' : 'Bank';

        return $this->requireAccountId($accountName);
    }

    private function requireAccountId(string $name): int
    {
        $accountId = (int) getAccount($name);
        if ($accountId <= 0) {
            throw new \Exception("Required ledger account \"{$name}\" was not found. Please seed chart of accounts.");
        }

        return $accountId;
    }

    public function recordSaleReturn(
        $booking_info,
        $gross_service_amount,
        $gross_item_amount,
        $taxAmount,
        $discountAmount,
        $roundOffAmount,
        $cogsAmount,
        $description,
        $date
    ) {
        $salesAccountId = getAccount('Sales Revenue');
        $serviceRevenueAccountId = getAccount('Service Revenue');
        $taxAccountId = getAccount('Output Tax Payable (GST/VAT)');
        $discountAccountId = getAccount('Sales Discounts');
        $roundOffLossAccountId = getAccount('Round Off Loss');
        $roundOffGainAccountId = getAccount('Round Off Gain');
        // $cogsAccountId = getAccount('Cost of Goods Sold');
        // $inventoryAccountId = getAccount('Inventory');
        // $accountsReceivableId = getAccount('Accounts Receivable');

        $clientLedgerId = $this->ensureClientLedger(
            $booking_info->client_id,
            $booking_info->client_name
        );
        $cashAccountId = getAccount('Cash');
        $bankAccountId = getAccount('Bank');

        $entries = [];

        $invoiceAmount = abs(
            $gross_item_amount +
            $gross_service_amount +
            $taxAmount -
            $discountAmount -
            $roundOffAmount
        );

        // Mirror recordAccounts(): reverse the booking invoice on the client sub-ledger
        if ($invoiceAmount > 0) {
            $entries[] = [
                'account_id' => $clientLedgerId,
                'debit' => 0,
                'credit' => $invoiceAmount,
                'remarks' => 'Sale Return Invoice',
            ];
        }

        if ($gross_item_amount > 0) {
            $entries[] = ['account_id' => $salesAccountId, 'debit' => abs($gross_item_amount), 'credit' => 0];
        }

        if ($gross_service_amount > 0) {
            $entries[] = ['account_id' => $serviceRevenueAccountId, 'debit' => abs($gross_service_amount), 'credit' => 0];
        }

        if ($taxAmount > 0) {
            $entries[] = ['account_id' => $taxAccountId, 'debit' => abs($taxAmount), 'credit' => 0];
        }

        if ($discountAmount > 0) {
            $entries[] = ['account_id' => $discountAccountId, 'debit' => 0, 'credit' => abs($discountAmount)];
        }

        // Reverse the original sale round-off effect
        if ($roundOffAmount != 0) {
            if ($roundOffAmount > 0) {
                $entries[] = [
                    'account_id' => $roundOffLossAccountId,
                    'debit' => 0,
                    'credit' => abs($roundOffAmount),
                ];
            } else {
                $entries[] = [
                    'account_id' => $roundOffGainAccountId,
                    'debit' => abs($roundOffAmount),
                    'credit' => 0,
                ];
            }
        }

        // Periodic inventory: no COGS / Inventory reversal on refund (mirrors sale posting above)

        $refundCash = 0.0;
        $refundBank = 0.0;
        foreach ($booking_info->payments as $payment) {
            $amount = abs($this->resolvePaymentAccountingAmount($payment));
            if ($amount == 0.0) {
                continue;
            }
            switch (strtolower($payment->payment_mode)) {
                case 'cash':
                    $refundCash += $amount;
                    $entries[] = ['account_id' => $clientLedgerId, 'debit' => $amount, 'credit' => 0];
                    $entries[] = ['account_id' => $cashAccountId, 'debit' => 0, 'credit' => $amount];
                    break;
                case 'online':
                case 'bank':
                case 'card':
                    $refundBank += $amount;
                    $entries[] = ['account_id' => $clientLedgerId, 'debit' => $amount, 'credit' => 0];
                    $entries[] = ['account_id' => $bankAccountId, 'debit' => 0, 'credit' => $amount];
                    break;
                case 'credit':
                    $entries[] = [
                        'account_id' => $clientLedgerId,
                        'debit' => $amount,
                        'credit' => 0,
                        'remarks' => ! empty($payment->prepaid_applied)
                            ? 'Refund client prepaid payment'
                            : 'Refund client credit payment',
                    ];
                    $entries[] = [
                        'account_id' => getAccount('Accounts Receivable'),
                        'debit' => 0,
                        'credit' => $amount,
                        'remarks' => ! empty($payment->prepaid_applied)
                            ? 'Refund client prepaid settlement'
                            : 'Refund client credit settlement',
                    ];
                    break;
                default:
                    Log::warning("Unknown refund mode: {$payment->payment_mode}");
            }
        }

        if ($refundCash > 0) {
            $this->validateAccountBalance('cash', $refundCash);
        }
        if ($refundBank > 0) {
            $this->validateAccountBalance('bank', $refundBank);
        }

        $totalDebits = array_sum(array_column($entries, 'debit'));
        $totalCredits = array_sum(array_column($entries, 'credit'));

        if (abs($totalDebits - $totalCredits) > 0.01) {
            Log::error("Unbalanced Sales Return for booking ID {$booking_info->booking_id}: Debits = $totalDebits, Credits = $totalCredits");
            throw new \Exception("Sales Return entries are unbalanced. Debits ($totalDebits) do not equal Credits ($totalCredits).");
        }

        // Save transaction
        $this->recordMultiEntryTransaction($entries, $description, $date);
    }

    public function recordOpeningBalance($account, $amount, $description, $date)
    {
        $ownersCapitalId = getAccount('Owner’s Capital');
        $type = strtolower((string) $account->type);
        $amount = round((float) $amount, 2);

        if (in_array($type, ['asset', 'expense'], true)) {
            $entries = [
                ['account_id' => $account->account_id, 'debit' => $amount, 'credit' => 0],
                ['account_id' => $ownersCapitalId, 'debit' => 0, 'credit' => $amount],
            ];
        } else {
            $entries = [
                ['account_id' => $account->account_id, 'debit' => 0, 'credit' => $amount],
                ['account_id' => $ownersCapitalId, 'debit' => $amount, 'credit' => 0],
            ];
        }

        return DB::transaction(function () use ($entries, $description, $date) {
            return $this->recordMultiEntryTransaction($entries, $description, $date);
        });
    }

    public function reverseOpeningBalance($account, $amount, $description, $date)
    {
        $ownersCapitalId = getAccount('Owner’s Capital');
        $type = strtolower((string) $account->type);
        $amount = round((float) $amount, 2);

        if ($amount < 0.01) {
            return null;
        }

        // Opposite of recordOpeningBalance
        if (in_array($type, ['asset', 'expense'], true)) {
            $entries = [
                ['account_id' => $account->account_id, 'debit' => 0, 'credit' => $amount],
                ['account_id' => $ownersCapitalId, 'debit' => $amount, 'credit' => 0],
            ];
        } else {
            $entries = [
                ['account_id' => $account->account_id, 'debit' => $amount, 'credit' => 0],
                ['account_id' => $ownersCapitalId, 'debit' => 0, 'credit' => $amount],
            ];
        }

        return DB::transaction(function () use ($entries, $description, $date) {
            return $this->recordMultiEntryTransaction($entries, $description, $date);
        });
    }

    /**
     * Rewrite the existing opening-balance voucher. Account balances move by the
     * difference only, so the ledger keeps a single Opening Balance line.
     */
    public function updateOpeningBalance($transactionGroupId, $account, $amount, $description, $date)
    {
        $group = TransactionGroup::findOrFail($transactionGroupId);

        if (trim((string) $group->description) !== 'Opening Balance') {
            throw new \Exception('Only an opening balance voucher can be updated in place.');
        }

        $date = \Carbon\Carbon::parse($date)->toDateString();
        app(FinancialYearService::class)->assertTransactionDateAllowed($date);

        $lines = $this->openingBalanceEntries($account, $amount);

        return DB::transaction(function () use ($group, $lines, $description, $date) {
            $this->reverseStoredBalanceEffect($group);
            Transaction::where('transaction_group_id', $group->id)->delete();
            $this->postOpeningBalanceLines($group, $lines, $date);

            $group->description = $description;
            $group->date = $date;
            $group->financial_year_id = app(FinancialYearService::class)->findForDate($date)?->id;
            $group->save();

            return $group->fresh();
        });
    }

    /**
     * Drop superseded opening-balance vouchers and their reversals when those
     * vouchers net to zero, and keep one voucher per opening-balance record.
     */
    public function consolidateOpeningBalanceLedgers(): void
    {
        DB::transaction(function () {
            $balancesBefore = Account::query()->pluck('balance', 'account_id')
                ->map(fn ($balance) => round((float) $balance, 2));

            $claimedIds = StockOpenBalance::query()
                ->whereNotNull('transaction_group_id')
                ->pluck('transaction_group_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            foreach (StockOpenBalance::query()->orderBy('open_balance_id')->get() as $openBalance) {
                if ($openBalance->transaction_group_id) {
                    continue;
                }

                if (! $openBalance->account_id) {
                    continue;
                }

                $openingGroups = $this->openingBalanceGroupsForAccount((int) $openBalance->account_id, $claimedIds);
                $keeper = $openingGroups->first();
                if (! $keeper) {
                    continue;
                }

                $superseded = TransactionGroup::query()
                    ->with(['transactions.journal'])
                    ->where('description', 'Reversal of Opening Balance #'.$openBalance->open_balance_id)
                    ->when($claimedIds, fn ($query) => $query->whereNotIn('id', $claimedIds))
                    ->get()
                    ->concat($openingGroups->slice(1)->load(['transactions.journal']))
                    ->unique('id')
                    ->values();

                $this->assertGroupsAreBalanceNeutral($superseded, $openBalance->open_balance_id);
                $this->assertKeeperMatchesOpenBalance($keeper, $openBalance);

                foreach ($superseded as $group) {
                    $this->deleteTransactionGroupAndReverseBalances($group);
                }

                DB::table('stock_open_balances')
                    ->where('open_balance_id', $openBalance->open_balance_id)
                    ->update(['transaction_group_id' => $keeper->id]);

                $claimedIds[] = (int) $keeper->id;
            }

            $balancesAfter = Account::query()->pluck('balance', 'account_id');
            foreach ($balancesBefore as $accountId => $before) {
                $after = round((float) ($balancesAfter[$accountId] ?? 0), 2);
                if (abs($before - $after) >= 0.01) {
                    throw new \Exception(
                        "Opening balance consolidation changed account {$accountId} balance from {$before} to {$after}."
                    );
                }
            }
        });
    }

    private function openingBalanceEntries($account, $amount): array
    {
        $ownersCapitalId = getAccount('Owner’s Capital');
        if (! $ownersCapitalId) {
            throw new \Exception('Owner’s Capital account was not found.');
        }

        if ((int) $account->account_id === (int) $ownersCapitalId) {
            throw new \Exception('Opening balance cannot be posted directly to Owner’s Capital.');
        }

        $amount = round((float) $amount, 2);
        $type = strtolower((string) $account->type);

        if (in_array($type, ['asset', 'expense'], true)) {
            return [
                ['account_id' => $account->account_id, 'debit' => $amount, 'credit' => 0],
                ['account_id' => $ownersCapitalId, 'debit' => 0, 'credit' => $amount],
            ];
        }

        return [
            ['account_id' => $account->account_id, 'debit' => 0, 'credit' => $amount],
            ['account_id' => $ownersCapitalId, 'debit' => $amount, 'credit' => 0],
        ];
    }

    private function openingBalanceGroupsForAccount(int $accountId, array $claimedIds)
    {
        return TransactionGroup::query()
            ->with(['transactions.journal'])
            ->where('description', 'Opening Balance')
            ->where(function ($query) {
                $query->where('is_audit_only', false)->orWhereNull('is_audit_only');
            })
            ->when($claimedIds, fn ($query) => $query->whereNotIn('id', $claimedIds))
            ->whereHas('transactions.journal', function ($query) use ($accountId) {
                $query->where('account_id', $accountId);
            })
            ->orderByDesc('id')
            ->get();
    }

    private function assertGroupsAreBalanceNeutral($groups, $openBalanceId): void
    {
        $deltas = [];

        foreach ($groups as $group) {
            foreach ($group->transactions as $transaction) {
                $accountId = $transaction->journal?->account_id;
                if (! $accountId) {
                    continue;
                }

                $deltas[$accountId] = ($deltas[$accountId] ?? 0)
                    + (float) $transaction->debit
                    - (float) $transaction->credit;
            }
        }

        foreach ($deltas as $accountId => $delta) {
            if (abs(round($delta, 2)) >= 0.01) {
                throw new \Exception(
                    "Cannot remove old opening balance vouchers for #{$openBalanceId}: account {$accountId} would change by ".round($delta, 2).'.'
                );
            }
        }
    }

    private function assertKeeperMatchesOpenBalance(TransactionGroup $keeper, StockOpenBalance $openBalance): void
    {
        $account = Account::find($openBalance->account_id);
        if (! $account) {
            throw new \Exception('Opening balance account was not found.');
        }

        $type = strtolower((string) $account->type);
        $expected = in_array($type, ['asset', 'expense'], true)
            ? round((float) $openBalance->amount, 2)
            : -round((float) $openBalance->amount, 2);

        $actual = 0.0;
        foreach ($keeper->transactions as $transaction) {
            if ((int) ($transaction->journal?->account_id) !== (int) $openBalance->account_id) {
                continue;
            }

            $actual += (float) $transaction->debit - (float) $transaction->credit;
        }

        if (abs(round($actual, 2) - $expected) >= 0.01) {
            throw new \Exception(
                "Opening balance #{$openBalance->open_balance_id} amount {$openBalance->amount} does not match voucher {$keeper->voucher_no}."
            );
        }
    }

    private function postOpeningBalanceLines(TransactionGroup $group, array $lines, string $date): void
    {
        foreach ($lines as $entry) {
            $journal = Journal::where('account_id', $entry['account_id'])->first();
            if (! $journal) {
                $journal = Journal::create(['account_id' => $entry['account_id']]);
            }

            Transaction::create([
                'transaction_group_id' => $group->id,
                'journal_id' => $journal->id,
                'debit' => $entry['debit'],
                'credit' => $entry['credit'],
                'date' => $date,
            ]);

            $this->applyAccountBalanceDelta(
                (int) $entry['account_id'],
                round((float) $entry['debit'] - (float) $entry['credit'], 2)
            );
        }
    }

    private function reverseStoredBalanceEffect(TransactionGroup $group): void
    {
        $transactions = Transaction::with('journal')
            ->where('transaction_group_id', $group->id)
            ->get();

        foreach ($transactions as $transaction) {
            $accountId = $transaction->journal?->account_id;
            if (! $accountId) {
                continue;
            }

            $this->applyAccountBalanceDelta(
                (int) $accountId,
                -round((float) $transaction->debit - (float) $transaction->credit, 2)
            );
        }
    }

    private function deleteTransactionGroupAndReverseBalances(TransactionGroup $group): void
    {
        if (\App\Models\InventoryTransaction::where('transaction_group_id', $group->id)->exists()) {
            throw new \Exception("Voucher {$group->voucher_no} has stock movements and cannot be removed.");
        }

        $this->reverseStoredBalanceEffect($group);
        $group->delete();
    }

    private function applyAccountBalanceDelta(int $accountId, float $delta): void
    {
        $delta = round($delta, 2);
        if (abs($delta) < 0.005) {
            return;
        }

        $journal = Journal::where('account_id', $accountId)->lockForUpdate()->first();
        if (! $journal) {
            $journal = Journal::create(['account_id' => $accountId]);
        }

        $account = Account::where('account_id', $accountId)->lockForUpdate()->first();
        if (! $account) {
            throw new \Exception("Account not found for id: {$accountId}");
        }

        $journal->balance = round((float) $journal->balance + $delta, 2);
        $account->balance = round((float) $account->balance + $delta, 2);
        $journal->save();
        $account->save();
    }

    /**
     * Reverse a purchase once against the vendor ledger, then settle cash/bank/online refunds.
     * Credit portions remain as a reduction of vendor payable.
     */
    public function recordPurchaseReturnWithPayments($vendorId, $items, array $payments, $description, $date, float $roundOff = 0.0)
    {
        // Periodic inventory: returns credit Purchase Account (expense), not Inventory asset
        $purchaseAccountId = getAccount('Purchase Account') ?: getAccount('Inventory');
        $gstAccountId = getAccount('Input Tax (GST/VAT Credit)');
        $discountAccountId = getAccount('Discount Received');
        $vendorLedgerId = $this->getOrCreateVendorLedger($vendorId);
        $cashAccountId = getAccount('Cash');
        $bankAccountId = getAccount('Bank');

        $entries = [];
        $totalReturnAmount = 0;
        $totalTaxAmount = 0;
        $totalDiscountAmount = 0;

        foreach ($items as $item) {
            $line = $this->purchaseLineFigures($item);

            $totalReturnAmount += $line['taxable'];
            $totalTaxAmount += $line['tax'];
            $totalDiscountAmount += $line['discount'];

            $entries[] = ['account_id' => $purchaseAccountId, 'debit' => 0, 'credit' => $line['gross']];
        }

        $roundOff = round($roundOff, 2);
        $payableReversal = round($totalReturnAmount + $totalTaxAmount + $roundOff, 2);

        if ($totalTaxAmount > 0) {
            $entries[] = ['account_id' => $gstAccountId, 'debit' => 0, 'credit' => $totalTaxAmount];
        }

        // Reverse vendor payable created by the original purchase
        $entries[] = ['account_id' => $vendorLedgerId, 'debit' => $payableReversal, 'credit' => 0];

        if ($totalDiscountAmount > 0) {
            $entries[] = ['account_id' => $discountAccountId, 'debit' => $totalDiscountAmount, 'credit' => 0];
        }

        $roundOffEntry = $this->roundOffEntry($roundOff, true);
        if ($roundOffEntry) {
            $entries[] = $roundOffEntry;
        }

        $creditSettled = 0.0;

        foreach ($payments as $payment) {
            $mode = strtolower((string) ($payment['payment_mode'] ?? ''));
            $amount = round(abs((float) ($payment['amount_taken'] ?? 0)), 2);
            if ($amount < 0.01) {
                continue;
            }

            if ($mode === 'credit') {
                $creditSettled += $amount;
                continue;
            }

            $paymentAccountId = match ($mode) {
                'cash' => $cashAccountId,
                'bank', 'online' => $bankAccountId,
                default => null,
            };

            if (! $paymentAccountId) {
                continue;
            }

            // Vendor refunds cash/bank: clear the temporary vendor debit and receive cash
            $entries[] = ['account_id' => $vendorLedgerId, 'debit' => 0, 'credit' => $amount];
            $entries[] = ['account_id' => $paymentAccountId, 'debit' => $amount, 'credit' => 0];
        }

        return DB::transaction(function () use ($entries, $description, $date, $vendorId, $items, $creditSettled) {
            $group = $this->recordMultiEntryTransaction($entries, $description, $date);

            if ($creditSettled >= 0.01) {
                $vendor = Vendor::findOrFail($vendorId);
                $vendor->balance -= $creditSettled;
                $vendor->save();
            }

            Transaction::where('transaction_group_id', $group->id)->update(['vendor_id' => $vendorId]);

            foreach ($items as $item) {
                $inventoryItem = InventoryItem::where('item_id', $item['item_id'])->first();
                if (! $inventoryItem) {
                    continue;
                }

                $qty = (float) $item['quantity'];
                $inventoryItem->quantity = max(0, (float) $inventoryItem->quantity - $qty);
                $inventoryItem->save();

                InventoryTransaction::create([
                    'inventory_item_id' => $inventoryItem->id,
                    'transaction_group_id' => $group->id,
                    'type' => 'purchase_return',
                    'quantity' => -$qty,
                    'cost_price' => $item['cost_price'],
                    'date' => $date,
                ]);
            }

            return $group;
        });
    }

    public function recordCreditPurchaseReturnWithInventory($vendorId, $items, $description, $date)
    {
        $payable = $this->calculatePurchasePayable($items);

        return $this->recordPurchaseReturnWithPayments($vendorId, $items, [
            ['payment_mode' => 'credit', 'amount_taken' => $payable],
        ], $description, $date);
    }

    public function recordCashPurchaseReturnWithInventory($vendorId, $items, $description, $date)
    {
        $payable = $this->calculatePurchasePayable($items);

        return $this->recordPurchaseReturnWithPayments($vendorId, $items, [
            ['payment_mode' => 'cash', 'amount_taken' => $payable],
        ], $description, $date);
    }

    public function recordBankPurchaseReturnWithInventory($vendorId, $items, $description, $date)
    {
        $payable = $this->calculatePurchasePayable($items);

        return $this->recordPurchaseReturnWithPayments($vendorId, $items, [
            ['payment_mode' => 'bank', 'amount_taken' => $payable],
        ], $description, $date);
    }

    public function recordOnlinePurchaseReturnWithInventory($vendorId, $items, $description, $date)
    {
        $payable = $this->calculatePurchasePayable($items);

        return $this->recordPurchaseReturnWithPayments($vendorId, $items, [
            ['payment_mode' => 'online', 'amount_taken' => $payable],
        ], $description, $date);
    }

    /**
     * Positive round off is a loss (we pay more). Negative round off is a gain.
     */
    private function roundOffEntry(float $roundOff, bool $isReturn): ?array
    {
        $roundOff = round($roundOff, 2);
        if (abs($roundOff) < 0.01) {
            return null;
        }

        $gainId = (int) getAccount('Round Off Gain');
        $lossId = (int) getAccount('Round Off Loss');

        if ($roundOff > 0 && ! $lossId) {
            throw new \Exception(__('Round Off Loss account is missing from the chart of accounts.'));
        }
        if ($roundOff < 0 && ! $gainId) {
            throw new \Exception(__('Round Off Gain account is missing from the chart of accounts.'));
        }

        if (! $isReturn) {
            if ($roundOff > 0) {
                return ['account_id' => $lossId, 'debit' => $roundOff, 'credit' => 0];
            }

            return ['account_id' => $gainId, 'debit' => 0, 'credit' => abs($roundOff)];
        }

        if ($roundOff > 0) {
            return ['account_id' => $lossId, 'debit' => 0, 'credit' => $roundOff];
        }

        return ['account_id' => $gainId, 'debit' => abs($roundOff), 'credit' => 0];
    }

    public function ensureClientLedger($clientId, $clientName)
    {

        $client = Client::findOrFail($clientId);
        // $user = User::findOrFail($clientId);

        if (! empty($client->ledger_id)) {
            return $client->ledger_id;
        }

        $ledger = Account::create([
            'code' => Account::generateNextCode('Asset'),
            'name' => $clientName.' Ledger',
            'type' => 'asset',
            'category' => 'Current Asset',
            // 'group_name' => 'Accounts Receivable',
            'balance' => 0,
        ]);

        $client->ledger_id = $ledger->account_id;
        $client->save();

        return $ledger->account_id;
    }

    /**
     * Outstanding salary advance sitting on the employee's linked asset ledger.
     * This is the balance already posted by payment vouchers and by this screen.
     * Does not create a ledger.
     */
    public function outstandingEmployeeAdvance(int $employeeId): float
    {
        $summary = $this->employeeAdvanceLedgerSummary($employeeId);

        return (float) $summary['outstanding'];
    }

    /**
     * @return array{account_id: ?int, code: ?string, name: ?string, balance: float, outstanding: float}
     */
    public function employeeAdvanceLedgerSummary(int $employeeId): array
    {
        $ledgerId = $this->attachExistingEmployeeAdvanceLedger($employeeId);
        if (! $ledgerId) {
            return [
                'account_id' => null,
                'code' => null,
                'name' => null,
                'balance' => 0.0,
                'outstanding' => 0.0,
            ];
        }

        $account = Account::query()->find($ledgerId);
        $balance = round((float) ($account->balance ?? 0), 2);

        return [
            'account_id' => (int) $ledgerId,
            'code' => $account->code ?? null,
            'name' => $account->name ?? null,
            'balance' => $balance,
            'outstanding' => round(max(0, $balance), 2),
        ];
    }

    /**
     * Link an employee to the chart ledger already used for their salary advance
     * (for example "BASIM SALARY ADVANCE"). Never creates a second ledger.
     */
    public function attachExistingEmployeeAdvanceLedger(int $employeeId): ?int
    {
        $existing = EmployeeAccountLedger::where('employee_id', $employeeId)
            ->where('ledger_type', 'advance_salary')
            ->first();

        if ($existing) {
            return (int) $existing->ledger_id;
        }

        $employeeName = $this->employeeLedgerName($employeeId);
        if ($employeeName === null) {
            return null;
        }

        $takenLedgerIds = EmployeeAccountLedger::where('ledger_type', 'advance_salary')
            ->pluck('ledger_id');

        $match = $this->salaryAdvanceLedgerCandidates()
            ->reject(function ($account) use ($takenLedgerIds) {
                return $takenLedgerIds->contains($account->account_id);
            })
            ->filter(function ($account) use ($employeeName) {
                return $this->employeeNameMatchesAdvanceLedger($employeeName, (string) $account->name);
            })
            ->sort(function ($left, $right) use ($employeeName) {
                $byScore = $this->advanceMatchScore($employeeName, (string) $right->name)
                    <=> $this->advanceMatchScore($employeeName, (string) $left->name);
                if ($byScore !== 0) {
                    return $byScore;
                }

                $byBalance = (float) $right->balance <=> (float) $left->balance;
                if ($byBalance !== 0) {
                    return $byBalance;
                }

                return (int) $right->account_id <=> (int) $left->account_id;
            })
            ->first();

        if (! $match) {
            return null;
        }

        EmployeeAccountLedger::create([
            'employee_id' => $employeeId,
            'ledger_id' => $match->account_id,
            'ledger_type' => 'advance_salary',
        ]);

        return (int) $match->account_id;
    }

    public function ensureEmployeeAdvanceLedger($employeeId)
    {
        $employeeId = (int) $employeeId;
        $linkedLedgerId = $this->attachExistingEmployeeAdvanceLedger($employeeId);
        if ($linkedLedgerId) {
            return $linkedLedgerId;
        }

        $employeeName = $this->employeeLedgerName($employeeId) ?? ('Employee '.$employeeId);

        $ledger = Account::create([
            'name' => $employeeName.' SALARY ADVANCE',
            'code' => Account::generateNextCode('Asset'),
            'type' => 'asset',
            'category' => 'Current Asset',
            'balance' => 0,
            'is_active' => 1,
        ]);

        EmployeeAccountLedger::create([
            'employee_id' => $employeeId,
            'ledger_id' => $ledger->account_id,
            'ledger_type' => 'advance_salary',
        ]);

        $this->salaryAdvanceLedgerCandidates = null;

        return $ledger->account_id;
    }

    private ?\Illuminate\Support\Collection $salaryAdvanceLedgerCandidates = null;

    private function salaryAdvanceLedgerCandidates(): \Illuminate\Support\Collection
    {
        if ($this->salaryAdvanceLedgerCandidates === null) {
            $this->salaryAdvanceLedgerCandidates = Account::query()
                ->where('is_active', 1)
                ->where('type', 'asset')
                ->where('name', 'like', '%salary advance%')
                ->get(['account_id', 'code', 'name', 'balance', 'is_active', 'type']);
        }

        return $this->salaryAdvanceLedgerCandidates;
    }

    private function employeeLedgerName(int $employeeId): ?string
    {
        $employee = Employee::with('user')->where('emp_id', $employeeId)->first();
        if (! $employee) {
            return null;
        }

        $name = trim((string) ($employee->name ?: optional($employee->user)->name));

        return $name !== '' ? $name : null;
    }

    private function employeeNameMatchesAdvanceLedger(string $employeeName, string $accountName): bool
    {
        return $this->advanceMatchScore($employeeName, $accountName) > 0;
    }

    /**
     * Higher scores are closer name matches. Zero means the account is not this employee.
     */
    private function advanceMatchScore(string $employeeName, string $accountName): int
    {
        $person = $this->salaryAdvancePersonName($accountName);
        if ($person === null) {
            return 0;
        }

        $employee = $this->normalizeLedgerLabel($employeeName);
        if ($employee === '') {
            return 0;
        }

        $firstName = strtok($employee, ' ') ?: $employee;
        if ($person === $employee) {
            return 100;
        }

        if ($person === $firstName) {
            return 90;
        }

        if ($this->knownAdvanceNameAlias($person) === $firstName) {
            return 80;
        }

        if (strlen($person) >= 4 && str_starts_with($firstName, $person)) {
            return 70;
        }

        if (strlen($firstName) >= 4 && str_starts_with($person, $firstName)) {
            return 65;
        }

        if ($this->sameNameWithOneVowelDifference($person, $firstName)) {
            return 50;
        }

        return 0;
    }

    /**
     * Ledger cards for the employee list. Reads linked accounts and, when an
     * advance ledger is not linked yet, the existing salary-advance account
     * whose name matches the employee. Does not create accounts.
     *
     * @param  iterable<int, Employee>  $employees
     * @return array<int, array<int, array<string, mixed>>>
     */
    public function ledgersForEmployees(iterable $employees): array
    {
        if (! $employees instanceof \Illuminate\Database\Eloquent\Collection) {
            $items = $employees instanceof \Illuminate\Support\Collection
                ? $employees->all()
                : $employees;
            $employees = new \Illuminate\Database\Eloquent\Collection($items);
        }

        if ($employees->isEmpty()) {
            return [];
        }

        $employees->loadMissing(['accountLedgers.account', 'user']);

        $labels = [
            'advance_salary' => 'Salary Advance',
            'salary' => 'Salary',
            'incentive' => 'Incentive',
            'commission' => 'Commission',
        ];

        $reservedIds = EmployeeAccountLedger::query()
            ->where('ledger_type', 'advance_salary')
            ->pluck('ledger_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $suggestions = $this->suggestAdvanceLedgers($employees, $reservedIds);

        $result = [];
        foreach ($employees as $employee) {
            $cards = [];
            $byType = $employee->accountLedgers->keyBy('ledger_type');

            foreach ($labels as $type => $label) {
                $linkedAccount = optional($byType->get($type))->account;
                $account = $linkedAccount;
                $linked = $linkedAccount !== null;

                if (! $account && $type === 'advance_salary') {
                    $account = $suggestions[(int) $employee->emp_id] ?? null;
                }

                if (! $account) {
                    continue;
                }

                $cards[] = [
                    'type' => $type,
                    'label' => $label,
                    'account_id' => (int) $account->account_id,
                    'code' => $account->code,
                    'name' => $account->name,
                    'balance' => round((float) $account->balance, 2),
                    'linked' => $linked,
                    'view_url' => route('tab.accounts.account.details', [
                        'account_id' => $account->account_id,
                        'tab_name' => 'transactions',
                    ]),
                ];
            }

            $result[(int) $employee->emp_id] = $cards;
        }

        return $result;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Employee>  $employees
     * @param  array<int, int>  $reservedIds
     * @return array<int, Account>
     */
    private function suggestAdvanceLedgers($employees, array $reservedIds): array
    {
        $candidates = $this->salaryAdvanceLedgerCandidates();
        $ranked = [];

        foreach ($employees as $employee) {
            $linkedAdvance = $employee->accountLedgers->firstWhere('ledger_type', 'advance_salary');
            if ($linkedAdvance && $linkedAdvance->account) {
                continue;
            }

            $name = trim((string) ($employee->name ?: optional($employee->user)->name));
            if ($name === '') {
                continue;
            }

            foreach ($candidates as $account) {
                if (in_array((int) $account->account_id, $reservedIds, true)) {
                    continue;
                }

                $score = $this->advanceMatchScore($name, (string) $account->name);
                if ($score === 0) {
                    continue;
                }

                $ranked[] = [
                    'emp_id' => (int) $employee->emp_id,
                    'account' => $account,
                    'score' => $score,
                    'balance' => (float) $account->balance,
                ];
            }
        }

        usort($ranked, function (array $left, array $right) {
            if ($left['score'] !== $right['score']) {
                return $right['score'] <=> $left['score'];
            }

            return $right['balance'] <=> $left['balance'];
        });

        $suggestions = [];
        $usedEmployees = [];
        $usedAccounts = array_fill_keys($reservedIds, true);

        foreach ($ranked as $row) {
            $accountId = (int) $row['account']->account_id;
            if (isset($usedEmployees[$row['emp_id']]) || isset($usedAccounts[$accountId])) {
                continue;
            }

            $suggestions[$row['emp_id']] = $row['account'];
            $usedEmployees[$row['emp_id']] = true;
            $usedAccounts[$accountId] = true;
        }

        return $suggestions;
    }

    /**
     * Production chart names that belong to a current employee under another spelling.
     */
    private function knownAdvanceNameAlias(string $accountPerson): ?string
    {
        $aliases = [
            'jayasree' => 'jayashree',
            'ashiq' => 'ashik',
            'binu' => 'binyamin',
        ];

        return $aliases[$accountPerson] ?? null;
    }

    /**
     * Accept a spelling difference such as swathi / swathy.
     * A consonant change such as basil / basim is a different person.
     */
    private function sameNameWithOneVowelDifference(string $left, string $right): bool
    {
        if ($left === $right || strlen($left) < 4 || strlen($left) !== strlen($right)) {
            return false;
        }

        $differences = 0;
        $length = strlen($left);
        for ($index = 0; $index < $length; $index++) {
            if ($left[$index] === $right[$index]) {
                continue;
            }

            $differences++;
            if ($differences > 1 || ! $this->isVowelLetter($left[$index]) || ! $this->isVowelLetter($right[$index])) {
                return false;
            }
        }

        return $differences === 1;
    }

    private function isVowelLetter(string $letter): bool
    {
        return str_contains('aeiouy', $letter);
    }

    private function salaryAdvancePersonName(string $accountName): ?string
    {
        $normalized = $this->normalizeLedgerLabel($accountName);
        if (! preg_match('/^(.+?)\s+salary\s+advance$/', $normalized, $matches)) {
            return null;
        }

        $person = trim($matches[1]);

        return $person !== '' ? $person : null;
    }

    private function normalizeLedgerLabel(string $value): string
    {
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9\s]/', ' ', $value) ?? '';
        $value = preg_replace('/\s+/', ' ', trim($value)) ?? '';

        return $value;
    }

    /**
     * Make sure an employee has all four sub-ledgers:
     * salary advance, salary payable, incentive payable, and commission payable.
     *
     * @return array{advance_salary: int, salary: int, incentive: int, commission: int}
     */
    public function ensureEmployeeLedgers($employeeId): array
    {
        $employeeId = (int) $employeeId;

        return [
            'advance_salary' => (int) $this->ensureEmployeeAdvanceLedger($employeeId),
            'salary' => (int) $this->ensureEmployeeSalaryLedger($employeeId),
            'incentive' => (int) $this->ensureEmployeeIncentiveLedger($employeeId),
            'commission' => (int) $this->ensureEmployeeCommissionLedger($employeeId),
        ];
    }

    public function ensureEmployeeSalaryLedger($employeeId)
    {
        return $this->ensureEmployeePayableLedger($employeeId, 'salary', 'Salary Payable');
    }

    public function ensureEmployeeCommissionLedger($employeeId)
    {
        return $this->ensureEmployeePayableLedger($employeeId, 'commission', 'Commission Payable');
    }

    public function ensureEmployeeIncentiveLedger($employeeId)
    {
        return $this->ensureEmployeePayableLedger($employeeId, 'incentive', 'Incentive Payable');
    }

    private function ensureEmployeePayableLedger($employeeId, string $ledgerType, string $suffix): int
    {
        $existing = EmployeeAccountLedger::where('employee_id', $employeeId)
            ->where('ledger_type', $ledgerType)
            ->first();

        if ($existing) {
            return (int) $existing->ledger_id;
        }

        $employeeName = $this->employeeLedgerName((int) $employeeId) ?? ('Employee '.$employeeId);

        $ledger = Account::create([
            'name' => $employeeName.' '.$suffix,
            'code' => Account::generateNextCode('Liability'),
            'type' => 'liability',
            'category' => 'Current Liability',
            'balance' => 0,
            'is_active' => 1,
        ]);

        EmployeeAccountLedger::create([
            'employee_id' => $employeeId,
            'ledger_id' => $ledger->account_id,
            'ledger_type' => $ledgerType,
        ]);

        return (int) $ledger->account_id;
    }

    public function getOrCreateVendorLedger($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);

        if (! empty($vendor->ledger_id)) {
            return $vendor->ledger_id;
        }

        $account = Account::create([
            'code' => Account::generateNextCode('Liability'),
            'name' => $vendor->name,
            'type' => 'liability',
            'category' => 'Current Liability',
            'balance' => 0,
        ]);

        $vendor->ledger_id = $account->account_id;
        $vendor->save();

        return $account->account_id;
    }

    public function getCashOrBankBalance($paymentMode): float
    {
        $mode = strtolower((string) $paymentMode);
        if (! in_array($mode, ['cash', 'bank', 'online'], true)) {
            return 0.0;
        }

        $accountName = $mode === 'cash' ? 'Cash' : 'Bank';
        $accountId = getAccount($accountName);
        if (! $accountId) {
            return 0.0;
        }

        $account = Account::find($accountId);

        return $account ? round((float) $account->balance, 2) : 0.0;
    }

    public function validateAccountBalance($paymentMode, $amount)
    {
        $mode = strtolower((string) $paymentMode);
        if (! in_array($mode, ['cash', 'bank', 'online'], true)) {
            return;
        }

        $accountName = $mode === 'cash' ? 'Cash' : 'Bank';
        $accountId = getAccount($accountName);
        if (! $accountId) {
            throw ValidationException::withMessages([
                'amount' => [__(':account account is not configured.', ['account' => $accountName])],
                'payment_mode' => [__(':account account is not configured.', ['account' => $accountName])],
                'payment_amount' => [__(':account account is not configured.', ['account' => $accountName])],
            ]);
        }

        $account = Account::findOrFail($accountId);
        $available = round((float) $account->balance, 2);
        $required = round((float) $amount, 2);

        if ($available < $required) {
            $message = __(':account has insufficient balance. Available: :available, Required: :required', [
                'account' => $accountName,
                'available' => number_format($available, 2),
                'required' => number_format($required, 2),
            ]);

            throw ValidationException::withMessages([
                'amount' => [$message],
                'account_type' => [$message],
                'payment_mode' => [$message],
                'payment_amount' => [$message],
                'advance_amount' => [$message],
            ]);
        }
    }

    /**
     * Ensure credits to Cash/Bank ledger accounts do not exceed available balance.
     *
     * @param  array<int, array{account_id:mixed,debit:float,credit:float,remarks:?string}>  $entries
     */
    public function validateVoucherCashBankCredits(array $entries): void
    {
        $cashAccountId = (int) getAccount('Cash');
        $bankAccountId = (int) getAccount('Bank');

        $cashCredit = 0.0;
        $bankCredit = 0.0;

        foreach ($entries as $entry) {
            $accountId = (int) ($entry['account_id'] ?? 0);
            $credit = round((float) ($entry['credit'] ?? 0), 2);

            if ($credit < 0.01) {
                continue;
            }

            if ($cashAccountId > 0 && $accountId === $cashAccountId) {
                $cashCredit += $credit;
            } elseif ($bankAccountId > 0 && $accountId === $bankAccountId) {
                $bankCredit += $credit;
            }
        }

        if ($cashCredit > 0) {
            $this->validateAccountBalance('cash', $cashCredit);
        }
        if ($bankCredit > 0) {
            $this->validateAccountBalance('bank', $bankCredit);
        }
    }

    /**
     * Sum cash/bank outgoing rows and ensure each account has enough balance.
     */
    public function validateOutgoingPaymentRows(array $payments): void
    {
        $cashAmount = 0.0;
        $bankAmount = 0.0;

        foreach ($payments as $payment) {
            $mode = strtolower((string) ($payment['payment_mode'] ?? ''));
            $amount = round(abs((float) ($payment['amount_taken'] ?? 0)), 2);
            if ($amount < 0.01) {
                continue;
            }

            if ($mode === 'cash') {
                $cashAmount += $amount;
            } elseif (in_array($mode, ['bank', 'online'], true)) {
                $bankAmount += $amount;
            }
        }

        if ($cashAmount > 0) {
            $this->validateAccountBalance('cash', $cashAmount);
        }
        if ($bankAmount > 0) {
            $this->validateAccountBalance('bank', $bankAmount);
        }
    }

    // update inventory balance
    // public function updateInventoryBalance($amount, $type = 'add')
    // {
    //     $inventoryAccount = Account::find(getAccount('Inventory'));

    //     if (! $inventoryAccount) {
    //         throw new \Exception('Inventory account not found.');
    //     }

    //     if ($type == 'add') {
    //         $inventoryAccount->balance += $amount;
    //     } else {
    //         $inventoryAccount->balance -= $amount;
    //     }

    //     $inventoryAccount->save();
    // }

    /**
     * Latest salon booking voucher that is not already reversed (paired out).
     */
    public function findActiveBookingPosting(Booking $booking): ?TransactionGroup
    {
        if (empty($booking->booking_code)) {
            return null;
        }

        $groups = TransactionGroup::where('description', 'like', '%Booking Code: '.$booking->booking_code.'%')
            ->orderByDesc('id')
            ->get();

        if ($groups->isEmpty()) {
            return null;
        }

        $excludedIds = \App\Support\ReversalPairFilter::excludedGroupIds($groups);

        return $groups->first(function ($group) use ($excludedIds) {
            if ($excludedIds->contains($group->id)) {
                return false;
            }

            return ! \App\Support\ReversalPairFilter::isReversalDescription($group->description);
        });
    }

    /**
     * Reverse the latest non-reversal salon booking voucher for this booking code.
     */
    public function reverseLatestBookingPosting(Booking $booking): void
    {
        if (empty($booking->booking_code) || empty($booking->client_id)) {
            return;
        }

        $existingTransaction = $this->findActiveBookingPosting($booking);

        if (! $existingTransaction) {
            return;
        }

        $this->reverseBookingPosting($booking, $existingTransaction);
    }

    /**
     * Reverse one salon booking voucher group.
     */
    public function reverseBookingPosting(Booking $booking, TransactionGroup $existingTransaction): void
    {
        if (empty($booking->client_id)) {
            return;
        }

        $ledgerId = $this->ensureClientLedger($booking->client_id, $booking->client_name);
        $originalTransactions = $existingTransaction->transactions()->with('journal')->get();
        $date = $booking->created_at->format('Y-m-d');
        $reverseDescription = 'Reversal of booking: '.$booking->client_name.' (Booking Code: '.$booking->booking_code.')';

        $reversalEntries = [];
        foreach ($originalTransactions as $transaction) {
            $reversalEntries[] = [
                'account_id' => $transaction->journal->account_id,
                'debit' => $transaction->credit,
                'credit' => $transaction->debit,
                'remarks' => 'Reversal: '.($transaction->remarks ?? ''),
            ];

            if ((int) $transaction->journal->account_id === (int) $ledgerId && $transaction->debit > 0) {
                DB::table('clients')
                    ->where('client_id', $booking->client_id)
                    ->decrement('balance', $transaction->debit);
            } elseif ((int) $transaction->journal->account_id === (int) $ledgerId && $transaction->credit > 0) {
                DB::table('clients')
                    ->where('client_id', $booking->client_id)
                    ->increment('balance', $transaction->credit);
            }
        }

        if (! empty($reversalEntries)) {
            $this->recordMultiEntryTransaction($reversalEntries, $reverseDescription, $date);
        }
    }

    /**
     * Reverse all non-reversal booking vouchers for a code, then post one fresh sale entry.
     */
    public function rebuildSalonBookingAccounting(Booking $booking): void
    {
        $saleGroups = TransactionGroup::where('description', 'like', '%Booking Code: '.$booking->booking_code.'%')
            ->where('description', 'not like', 'Reversal%')
            ->orderByDesc('id')
            ->get();

        foreach ($saleGroups as $group) {
            $this->reverseBookingPosting($booking, $group);
        }

        $booking->refresh();
        $this->postSalonBookingAccounting($booking);
    }

    /**
     * Reverse and re-post salon booking accounting from current cart + payments.
     */
    public function resyncSalonBookingAccounting(Booking $booking): void
    {
        $this->reverseLatestBookingPosting($booking);
        $this->postSalonBookingAccounting($booking);
    }

    private function postSalonBookingAccounting(Booking $booking): void
    {
        $booking->load(['payments', 'bookingOnlyServices', 'bookingItems']);

        $gross_service_amount = 0;
        $gross_item_amount = 0;
        $taxAmount = 0;
        $service_discount_amount = 0;
        $item_discount_amount = 0;
        $cogsAmount = 0;

        foreach ($booking->bookingOnlyServices as $service) {
            $gross_service_amount += $service->service_booking_price * $service->quantity;
            $taxAmount += $service->tax_amount;
            $service_discount_amount += $service->discount_amount;
            $service_discount_amount += $service->individual_discount_amt;
        }
        foreach ($booking->bookingItems as $item) {
            $gross_item_amount += $item->selling_price * $item->quantity;
            $taxAmount += $item->tax_amount;
            $item_discount_amount += $item->discount_amount;
            $item_discount_amount += $item->individual_discount_amt;
            $cost_price = (float) ($item->cost_price ?? 0);
            if ($cost_price <= 0 && ! empty($item->item_id)) {
                $cost_price = (float) (DB::table('stock_items')->where('item_id', $item->item_id)->value('cost_price') ?? 0);
            }
            $cogsAmount += $cost_price * $item->quantity;
        }

        $discountAmount = $booking->applied_discount_price + $item_discount_amount + $service_discount_amount;
        $roundOffAmount = ($booking->applied_roundoff_price ?? 0.0);
        $date = $booking->created_at->format('Y-m-d');

        if ($booking->booking_type == 'Refund') {
            $originalCode = $booking->refund_invoice_no;
            if (empty($originalCode) && ! empty($booking->parent_id)) {
                $originalCode = Booking::where('booking_id', $booking->parent_id)->value('booking_code');
            }
            $originalCode = $originalCode ?: ($booking->booking_code ?: $booking->booking_id);
            $description = 'Salon sale return for client: '.$booking->client_name
                .' (Refund of Booking Code: '.$originalCode.')';

            $this->recordSaleReturn(
                $booking,
                $gross_service_amount,
                $gross_item_amount,
                $taxAmount,
                $discountAmount,
                $roundOffAmount,
                $cogsAmount,
                $description,
                $date
            );

            return;
        }

        $description = 'Salon booking for client: '.$booking->client_name.' (Booking Code: '.$booking->booking_code.')';
        $this->recordAccounts(
            $booking,
            $gross_service_amount,
            $gross_item_amount,
            $taxAmount,
            $discountAmount,
            $roundOffAmount,
            $cogsAmount,
            $description,
            $date
        );
    }
}
