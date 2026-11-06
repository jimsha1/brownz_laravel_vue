<?php

namespace App\Service;

use App\Models\Account;
use App\Models\FinancialYear;
use App\Models\FinancialYearOpeningBalance;
use App\Models\StockItem;
use App\Models\TransactionGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinancialYearService
{
    public function __construct(private AccountingService $accountingService) {}

    public function buildLabel(Carbon $startDate, Carbon $endDate): string
    {
        return $startDate->year.'-'.$endDate->year;
    }

    public function getFinancialYearStartForDate(string $date): Carbon
    {
        $carbon = Carbon::parse($date);
        $year = $carbon->year;

        if ($carbon->month < 4) {
            $year--;
        }

        return Carbon::create($year, 4, 1)->startOfDay();
    }

    public function getFinancialYearEndForStart(Carbon $startDate): Carbon
    {
        return $startDate->copy()->addYear()->subDay()->endOfDay();
    }

    public function findForDate(string $date): ?FinancialYear
    {
        return FinancialYear::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();
    }

    public function getCurrentFinancialYear(): ?FinancialYear
    {
        return FinancialYear::where('is_current', true)->first()
            ?? FinancialYear::where('status', FinancialYear::STATUS_OPEN)
                ->orderByDesc('start_date')
                ->first();
    }

    /**
     * Default start/end dates for financial statement reports.
     *
     * @return array{start_date: string, end_date: string}
     */
    public function getDefaultReportDateRange(): array
    {
        $year = $this->getCurrentFinancialYear();

        if ($year) {
            return $this->getReportDateRangeForYear($year);
        }

        $startDate = $this->getFinancialYearStartForDate(now()->toDateString());

        return [
            'start_date' => $startDate->toDateString(),
            'end_date' => now()->toDateString(),
        ];
    }

    /**
     * @return array{start_date: string, end_date: string}
     */
    public function getReportDateRangeForYear(FinancialYear $year): array
    {
        $startDate = $year->start_date->format('Y-m-d');
        $yearEnd = $year->end_date->format('Y-m-d');

        if ($year->isClosed()) {
            return [
                'start_date' => $startDate,
                'end_date' => $yearEnd,
            ];
        }

        $today = now()->toDateString();

        return [
            'start_date' => $startDate,
            'end_date' => $today <= $yearEnd ? $today : $yearEnd,
        ];
    }

    public function listYearsForReports(): array
    {
        return FinancialYear::orderByDesc('start_date')
            ->get()
            ->map(function (FinancialYear $year) {
                $range = $this->getReportDateRangeForYear($year);

                return array_merge($this->formatYear($year), [
                    'report_start_date' => $range['start_date'],
                    'report_end_date' => $range['end_date'],
                ]);
            })
            ->values()
            ->all();
    }

    public function assertTransactionDateAllowed(string $date): void
    {
        $financialYear = $this->findForDate($date);

        if (! $financialYear) {
            throw new \Exception(
                "No financial year exists for date {$date}. Initialize or open a financial year before posting."
            );
        }

        if ($financialYear->isClosed()) {
            throw new \Exception(
                "Cannot post transactions to closed financial year {$financialYear->label} ({$financialYear->start_date->format('d M Y')} - {$financialYear->end_date->format('d M Y')})."
            );
        }
    }

    public function initializeCurrentFinancialYear(): FinancialYear
    {
        $existing = $this->getCurrentFinancialYear();
        if ($existing) {
            return $existing;
        }

        $startDate = $this->getFinancialYearStartForDate(now()->toDateString());
        $endDate = $this->getFinancialYearEndForStart($startDate);

        if (FinancialYear::where('start_date', $startDate->toDateString())->exists()) {
            return FinancialYear::where('start_date', $startDate->toDateString())->firstOrFail();
        }

        return FinancialYear::create([
            'label' => $this->buildLabel($startDate, $endDate),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'status' => FinancialYear::STATUS_OPEN,
            'is_current' => true,
            'opened_at' => now(),
            'opened_by' => Auth::id(),
        ]);
    }

    public function previewClose(FinancialYear $year): array
    {
        $this->assertYearCanBeClosed($year);
        $this->accountingService->syncAccountBalancesFromJournals();
        $closePlan = $this->buildYearClosePlan($year);

        return [
            'financial_year' => $this->formatYear($year),
            'revenue_accounts' => $closePlan['revenue_accounts'],
            'expense_accounts' => $closePlan['expense_accounts'],
            'stock_adjustments' => $closePlan['stock_adjustments'],
            'total_revenue_transfer' => $closePlan['total_revenue'],
            'total_expense_transfer' => $closePlan['total_expense'],
            'opening_stock' => $closePlan['opening_stock'],
            'closing_stock' => $closePlan['closing_stock'],
            'cogs' => $closePlan['cogs'],
            'net_profit_loss' => $closePlan['net_profit_loss'],
            'retained_earnings_effect' => $closePlan['net_profit_loss'] > 0
                ? ['debit' => 0, 'credit' => $closePlan['net_profit_loss']]
                : ($closePlan['net_profit_loss'] < 0
                    ? ['debit' => abs($closePlan['net_profit_loss']), 'credit' => 0]
                    : ['debit' => 0, 'credit' => 0]),
        ];
    }

    public function closeFinancialYear(FinancialYear $year): TransactionGroup
    {
        $this->assertYearCanBeClosed($year);
        // Ensure ledger balances match journals before closing P&L accounts.
        $this->accountingService->syncAccountBalancesFromJournals();
        $closePlan = $this->buildYearClosePlan($year);

        return DB::transaction(function () use ($year, $closePlan) {
            $group = null;

            if (! empty($closePlan['entries'])) {
                $group = $this->accountingService->recordMultiEntryTransaction(
                    $closePlan['entries'],
                    "Financial Year Close - {$year->label}",
                    $year->end_date->toDateString(),
                    'Journal',
                    0,
                    $year->id,
                    true
                );
            }

            $year->update([
                'status' => FinancialYear::STATUS_CLOSED,
                'net_profit_loss' => $closePlan['net_profit_loss'],
                'closing_transaction_group_id' => $group?->id,
                'closed_at' => now(),
                'closed_by' => Auth::id(),
                'is_current' => false,
            ]);

            return $group ?? new TransactionGroup([
                'description' => "Financial Year Close - {$year->label}",
                'date' => $year->end_date->toDateString(),
            ]);
        });
    }

    public function openNextFinancialYear(?FinancialYear $afterYear = null): FinancialYear
    {
        $afterYear = $afterYear
            ?? FinancialYear::where('status', FinancialYear::STATUS_CLOSED)->orderByDesc('end_date')->first();

        if ($afterYear && ! $afterYear->isClosed()) {
            throw new \Exception('The selected financial year must be closed before opening the next year.');
        }

        if (FinancialYear::where('status', FinancialYear::STATUS_OPEN)->exists()) {
            throw new \Exception('An open financial year already exists. Close it before opening a new one.');
        }

        if ($afterYear) {
            $startDate = $afterYear->end_date->copy()->addDay();
        } else {
            $startDate = $this->getFinancialYearStartForDate(now()->toDateString());
        }

        $endDate = $this->getFinancialYearEndForStart($startDate);

        if (FinancialYear::where('start_date', $startDate->toDateString())->exists()) {
            throw new \Exception('Financial year already exists for the selected period.');
        }

        // Repair account.balance drift (e.g. AccountSeeder overwrite) before carrying forward.
        $this->accountingService->syncAccountBalancesFromJournals();
        $openingPreview = $this->buildOpeningBalancePayload();

        if (round($openingPreview['total_debit'], 2) !== round($openingPreview['total_credit'], 2)) {
            throw new \Exception(
                'Opening balance sheet is unbalanced (Dr '.$openingPreview['total_debit'].' / Cr '.$openingPreview['total_credit'].'). Resolve account balances before opening the next year.'
            );
        }

        return DB::transaction(function () use ($startDate, $endDate, $openingPreview) {
            FinancialYear::where('is_current', true)->update(['is_current' => false]);

            $year = FinancialYear::create([
                'label' => $this->buildLabel($startDate, $endDate),
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'status' => FinancialYear::STATUS_OPEN,
                'is_current' => true,
                'opened_at' => now(),
                'opened_by' => Auth::id(),
            ]);

            $openingGroup = $this->recordOpeningBalanceAudit($year, $openingPreview);

            if ($openingGroup) {
                $year->update(['opening_transaction_group_id' => $openingGroup->id]);
            }

            return $year->fresh(['openingTransactionGroup', 'openingBalances.account']);
        });
    }

    public function previewOpeningBalance(?FinancialYear $afterYear = null): array
    {
        $afterYear = $afterYear
            ?? FinancialYear::where('status', FinancialYear::STATUS_CLOSED)->orderByDesc('end_date')->first();

        if ($afterYear && ! $afterYear->isClosed()) {
            throw new \Exception('The selected financial year must be closed before opening the next year.');
        }

        if ($afterYear) {
            $startDate = $afterYear->end_date->copy()->addDay();
        } else {
            $startDate = $this->getFinancialYearStartForDate(now()->toDateString());
        }

        $endDate = $this->getFinancialYearEndForStart($startDate);
        $this->accountingService->syncAccountBalancesFromJournals();
        $payload = $this->buildOpeningBalancePayload();

        return [
            'next_label' => $this->buildLabel($startDate, $endDate),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'accounts' => $payload['accounts'],
            'total_debit' => $payload['total_debit'],
            'total_credit' => $payload['total_credit'],
            'stock_in_hand_note' => $payload['stock_in_hand_note'],
            'books_balanced' => abs($payload['total_debit'] - $payload['total_credit']) < 0.01,
        ];
    }

    public function getOpeningBalanceDetails(FinancialYear $year): array
    {
        $year->load(['openingBalances.account', 'openingTransactionGroup']);

        return [
            'financial_year' => $this->formatYear($year),
            'accounts' => $year->openingBalances->map(function ($row) {
                return [
                    'account_id' => $row->account_id,
                    'name' => $row->account->name ?? '',
                    'debit' => number_format((float) $row->debit, 2, '.', ''),
                    'credit' => number_format((float) $row->credit, 2, '.', ''),
                ];
            })->values()->all(),
            'total_debit' => number_format((float) $year->openingBalances->sum('debit'), 2, '.', ''),
            'total_credit' => number_format((float) $year->openingBalances->sum('credit'), 2, '.', ''),
        ];
    }

    public function getYearDetails(FinancialYear $year): array
    {
        $year->load([
            'closedByUser:id,name',
            'openedByUser:id,name',
            'closingTransactionGroup.transactions.journal.account',
            'openingTransactionGroup',
            'openingBalances.account',
        ]);

        $closingLines = [];
        $closingTotalDebit = 0;
        $closingTotalCredit = 0;

        if ($year->closingTransactionGroup) {
            foreach ($year->closingTransactionGroup->transactions as $transaction) {
                $debit = round((float) $transaction->debit, 2);
                $credit = round((float) $transaction->credit, 2);
                $closingTotalDebit += $debit;
                $closingTotalCredit += $credit;

                $closingLines[] = [
                    'account_id' => $transaction->journal?->account_id,
                    'account_name' => $transaction->journal?->account?->name ?? 'Unknown account',
                    'debit' => number_format($debit, 2, '.', ''),
                    'credit' => number_format($credit, 2, '.', ''),
                    'remarks' => $transaction->remarks,
                    'date' => $transaction->date,
                ];
            }
        }

        $openingAccounts = $year->openingBalances->map(function ($row) {
            return [
                'account_id' => $row->account_id,
                'name' => $row->account->name ?? '',
                'debit' => number_format((float) $row->debit, 2, '.', ''),
                'credit' => number_format((float) $row->credit, 2, '.', ''),
            ];
        })->values()->all();

        $netProfitLoss = $year->net_profit_loss !== null ? (float) $year->net_profit_loss : null;

        return [
            'financial_year' => $this->formatYear($year),
            'net_profit_loss' => $netProfitLoss,
            'retained_earnings_effect' => $netProfitLoss === null || abs($netProfitLoss) < 0.01
                ? ['debit' => 0, 'credit' => 0]
                : ($netProfitLoss > 0
                    ? ['debit' => 0, 'credit' => round($netProfitLoss, 2)]
                    : ['debit' => round(abs($netProfitLoss), 2), 'credit' => 0]),
            'closing_voucher' => $year->closingTransactionGroup ? [
                'voucher_no' => $year->closingTransactionGroup->voucher_no,
                'date' => $year->closingTransactionGroup->date,
                'description' => $year->closingTransactionGroup->description,
                'lines' => $closingLines,
                'total_debit' => number_format($closingTotalDebit, 2, '.', ''),
                'total_credit' => number_format($closingTotalCredit, 2, '.', ''),
            ] : null,
            'opening_balance' => [
                'voucher_no' => $year->openingTransactionGroup?->voucher_no,
                'accounts' => $openingAccounts,
                'total_debit' => number_format((float) $year->openingBalances->sum('debit'), 2, '.', ''),
                'total_credit' => number_format((float) $year->openingBalances->sum('credit'), 2, '.', ''),
            ],
        ];
    }

    public function listYears(): array
    {
        return FinancialYear::with([
            'closedByUser:id,name',
            'openedByUser:id,name',
            'closingTransactionGroup:id,voucher_no',
            'openingTransactionGroup:id,voucher_no',
        ])
            ->orderByDesc('start_date')
            ->get()
            ->map(fn (FinancialYear $year) => $this->formatYear($year))
            ->values()
            ->all();
    }

    public function resolveClosingStockForProfitLoss(string $endDate): float
    {
        $year = FinancialYear::where('status', FinancialYear::STATUS_CLOSED)
            ->whereDate('end_date', $endDate)
            ->with('closingTransactionGroup.transactions.journal')
            ->first();

        if ($year?->closingTransactionGroup) {
            $openingStockAdjId = getAccount('Opening Stock Adjustment');
            if ($openingStockAdjId) {
                $closingStock = $year->closingTransactionGroup->transactions
                    ->filter(function ($transaction) use ($openingStockAdjId) {
                        return (int) ($transaction->journal?->account_id) === (int) $openingStockAdjId
                            && (float) $transaction->debit > 0.009
                            && str_contains((string) ($transaction->remarks ?? ''), 'Year-end closing stock');
                    })
                    ->sum('debit');

                if ($closingStock >= 0.01) {
                    return round((float) $closingStock, 2);
                }
            }
        }

        return round((float) DB::table('stock_items')
            ->selectRaw('COALESCE(SUM('.StockItem::inventoryValueSql().'), 0) as amount')
            ->value('amount'), 2);
    }

    public function resolveOpeningStockForProfitLoss(string $startDate): ?float
    {
        $start = Carbon::parse($startDate);
        $currentYear = FinancialYear::whereDate('start_date', $startDate)->first();

        if ($currentYear) {
            $previousYear = FinancialYear::where('status', FinancialYear::STATUS_CLOSED)
                ->whereDate('end_date', $start->copy()->subDay()->toDateString())
                ->first();

            if ($previousYear) {
                return $this->resolveClosingStockForProfitLoss($previousYear->end_date->toDateString());
            }

            $openingStockAdjId = getAccount('Opening Stock Adjustment');
            if ($openingStockAdjId) {
                $openingRow = $currentYear->openingBalances()
                    ->where('account_id', $openingStockAdjId)
                    ->first();

                if ($openingRow && (float) $openingRow->debit >= 0.01) {
                    return round((float) $openingRow->debit, 2);
                }
            }
        }

        return null;
    }

    private function assertYearCanBeClosed(FinancialYear $year): void
    {
        if ($year->isClosed()) {
            throw new \Exception("Financial year {$year->label} is already closed.");
        }

        $newerOpenYear = FinancialYear::where('start_date', '>', $year->start_date)
            ->where('status', FinancialYear::STATUS_OPEN)
            ->exists();

        if ($newerOpenYear) {
            throw new \Exception('Close newer financial years before closing this year.');
        }
    }

    /**
     * Build year-end close entries:
     * - zero revenue into P&L Closing
     * - zero expenses (including Purchase Account) into P&L Closing
     * - roll Opening Stock Adjustment into P&L Closing
     * - book Closing Stock onto Opening Stock Adjustment
     * - transfer net P&L Closing to Retained Earnings
     */
    private function buildYearClosePlan(FinancialYear $year): array
    {
        $purchaseAccountId = getAccount('Purchase Account');
        $openingStockAdjId = getAccount('Opening Stock Adjustment');
        $plClosingId = getAccount('Profit & Loss Closing account');
        $retainedEarningsId = getAccount('Retained Earnings');

        if (! $plClosingId || ! $retainedEarningsId) {
            throw new \Exception('Required system accounts (Profit & Loss Closing / Retained Earnings) are missing.');
        }

        $entries = [];
        $revenueAccounts = [];
        $expenseAccounts = [];
        $stockAdjustments = [];
        $totalRevenue = 0;
        $totalExpense = 0;
        $plDebit = 0;
        $plCredit = 0;

        foreach (Account::whereRaw('LOWER(type) = ?', ['revenue'])->orderBy('name')->get() as $account) {
            $balance = round((float) $account->balance, 2);
            if (abs($balance) < 0.01) {
                continue;
            }

            // Credit-nature revenue (balance < 0 under debit-credit convention) is closed by debit.
            $closeDebit = $balance < 0 ? abs($balance) : 0;
            $closeCredit = $balance > 0 ? $balance : 0;
            $amount = abs($balance);
            $totalRevenue += $amount;

            if ($closeDebit > 0) {
                $entries[] = [
                    'account_id' => $account->account_id,
                    'debit' => $closeDebit,
                    'credit' => 0,
                    'remarks' => 'Year-end P&L transfer',
                ];
                $entries[] = [
                    'account_id' => $plClosingId,
                    'debit' => 0,
                    'credit' => $closeDebit,
                    'remarks' => 'Year-end revenue transfer',
                ];
                $plCredit += $closeDebit;
            }

            if ($closeCredit > 0) {
                $entries[] = [
                    'account_id' => $account->account_id,
                    'debit' => 0,
                    'credit' => $closeCredit,
                    'remarks' => 'Year-end P&L transfer',
                ];
                $entries[] = [
                    'account_id' => $plClosingId,
                    'debit' => $closeCredit,
                    'credit' => 0,
                    'remarks' => 'Year-end revenue transfer',
                ];
                $plDebit += $closeCredit;
            }

            $revenueAccounts[] = [
                'account_id' => $account->account_id,
                'name' => $account->name,
                'balance' => $balance,
                'close_debit' => round($closeDebit, 2),
                'close_credit' => round($closeCredit, 2),
            ];
        }

        foreach (
            Account::whereRaw('LOWER(type) = ?', ['expense'])
                ->orderBy('name')
                ->get() as $account
        ) {
            $balance = round((float) $account->balance, 2);
            if (abs($balance) < 0.01) {
                continue;
            }

            // Debit-nature expense (balance > 0) is closed by credit.
            $closeCredit = $balance > 0 ? $balance : 0;
            $closeDebit = $balance < 0 ? abs($balance) : 0;
            $amount = abs($balance);
            $totalExpense += $amount;

            if ($closeCredit > 0) {
                $entries[] = [
                    'account_id' => $account->account_id,
                    'debit' => 0,
                    'credit' => $closeCredit,
                    'remarks' => 'Year-end P&L transfer',
                ];
                $entries[] = [
                    'account_id' => $plClosingId,
                    'debit' => $closeCredit,
                    'credit' => 0,
                    'remarks' => 'Year-end expense transfer',
                ];
                $plDebit += $closeCredit;
            }

            if ($closeDebit > 0) {
                $entries[] = [
                    'account_id' => $account->account_id,
                    'debit' => $closeDebit,
                    'credit' => 0,
                    'remarks' => 'Year-end P&L transfer',
                ];
                $entries[] = [
                    'account_id' => $plClosingId,
                    'debit' => 0,
                    'credit' => $closeDebit,
                    'remarks' => 'Year-end expense transfer',
                ];
                $plCredit += $closeDebit;
            }

            $expenseAccounts[] = [
                'account_id' => $account->account_id,
                'name' => $account->name,
                'balance' => $balance,
                'close_debit' => round($closeDebit, 2),
                'close_credit' => round($closeCredit, 2),
                'is_purchase' => (int) $account->account_id === (int) $purchaseAccountId,
            ];
        }

        $openingStock = 0;
        if ($openingStockAdjId) {
            $osaAccount = Account::find($openingStockAdjId);
            $osaBalance = round((float) ($osaAccount?->balance ?? 0), 2);
            $openingStock = $osaBalance > 0 ? $osaBalance : 0;

            if ($openingStock >= 0.01) {
                $entries[] = [
                    'account_id' => $openingStockAdjId,
                    'debit' => 0,
                    'credit' => $openingStock,
                    'remarks' => 'Year-end opening stock transfer',
                ];
                $entries[] = [
                    'account_id' => $plClosingId,
                    'debit' => $openingStock,
                    'credit' => 0,
                    'remarks' => 'Year-end opening stock transfer',
                ];
                $plDebit += $openingStock;

                $stockAdjustments[] = [
                    'name' => 'Opening Stock',
                    'debit' => round($openingStock, 2),
                    'credit' => 0,
                ];
            }
        }

        $closingStock = round((float) DB::table('stock_items')
            ->selectRaw('COALESCE(SUM('.StockItem::inventoryValueSql().'), 0) as amount')
            ->value('amount'), 2);

        if ($openingStockAdjId && $closingStock >= 0.01) {
            $entries[] = [
                'account_id' => $openingStockAdjId,
                'debit' => $closingStock,
                'credit' => 0,
                'remarks' => 'Year-end closing stock',
            ];
            $entries[] = [
                'account_id' => $plClosingId,
                'debit' => 0,
                'credit' => $closingStock,
                'remarks' => 'Year-end closing stock',
            ];
            $plCredit += $closingStock;

            $stockAdjustments[] = [
                'name' => 'Closing Stock',
                'debit' => 0,
                'credit' => round($closingStock, 2),
            ];
        }

        $purchaseBalance = 0;
        if ($purchaseAccountId) {
            $purchaseAccount = collect($expenseAccounts)->firstWhere('account_id', $purchaseAccountId);
            $purchaseBalance = $purchaseAccount ? abs((float) $purchaseAccount['balance']) : 0;
        }

        $cogs = round($openingStock + $purchaseBalance - $closingStock, 2);
        $netProfit = round($plCredit - $plDebit, 2);

        if (abs($netProfit) >= 0.01) {
            if ($netProfit > 0) {
                $entries[] = [
                    'account_id' => $plClosingId,
                    'debit' => $netProfit,
                    'credit' => 0,
                    'remarks' => 'Year-end profit transfer to Retained Earnings',
                ];
                $entries[] = [
                    'account_id' => $retainedEarningsId,
                    'debit' => 0,
                    'credit' => $netProfit,
                    'remarks' => 'Year-end profit transfer',
                ];
            } else {
                $loss = abs($netProfit);
                $entries[] = [
                    'account_id' => $retainedEarningsId,
                    'debit' => $loss,
                    'credit' => 0,
                    'remarks' => 'Year-end loss transfer',
                ];
                $entries[] = [
                    'account_id' => $plClosingId,
                    'debit' => 0,
                    'credit' => $loss,
                    'remarks' => 'Year-end loss transfer from Retained Earnings',
                ];
            }
        }

        return [
            'entries' => $entries,
            'revenue_accounts' => $revenueAccounts,
            'expense_accounts' => $expenseAccounts,
            'stock_adjustments' => $stockAdjustments,
            'total_revenue' => round($totalRevenue, 2),
            'total_expense' => round($totalExpense, 2),
            'opening_stock' => round($openingStock, 2),
            'closing_stock' => round($closingStock, 2),
            'cogs' => $cogs,
            'net_profit_loss' => $netProfit,
        ];
    }

    private function buildOpeningBalancePayload(): array
    {
        $accounts = [];
        $totalDebit = 0;
        $totalCredit = 0;
        $balanceSheetTypes = ['asset', 'liability', 'equity'];

        $profitAndLossClosingId = getAccount('Profit & Loss Closing account');

        foreach (Account::orderBy('name')->get() as $account) {
            // P&L Closing should be zero after a proper year close; skip residual noise.
            if ($profitAndLossClosingId && (int) $account->account_id === (int) $profitAndLossClosingId) {
                continue;
            }

            $type = strtolower((string) $account->type);
            if (! in_array($type, $balanceSheetTypes, true)) {
                continue;
            }

            $balance = round((float) $account->balance, 2);
            if (abs($balance) < 0.01) {
                continue;
            }

            $debit = 0;
            $credit = 0;

            // balance = sum(debit - credit): assets positive = debit; liabilities/equity credit-nature = negative.
            if ($type === 'asset') {
                if ($balance > 0) {
                    $debit = $balance;
                } else {
                    $credit = abs($balance);
                }
            } else {
                if ($balance < 0) {
                    $credit = abs($balance);
                } else {
                    $debit = $balance;
                }
            }

            $totalDebit += $debit;
            $totalCredit += $credit;

            $accounts[] = [
                'account_id' => $account->account_id,
                'name' => $account->name,
                'type' => $type,
                'balance' => $balance,
                'debit' => round($debit, 2),
                'credit' => round($credit, 2),
            ];
        }

        $stockInHand = round((float) DB::table('stock_items')
            ->selectRaw('COALESCE(SUM('.StockItem::inventoryValueSql().'), 0) as amount')
            ->value('amount'), 2);

        $openingStockAdjId = getAccount('Opening Stock Adjustment');
        $osaBalance = 0;
        if ($openingStockAdjId) {
            $osaBalance = round((float) (Account::find($openingStockAdjId)?->balance ?? 0), 2);
        }

        $stockInHandNote = null;
        if ($stockInHand >= 0.01) {
            $stockInHandNote = [
                'amount' => $stockInHand,
                'opening_stock_adjustment_balance' => $osaBalance,
                'message' => abs($stockInHand - $osaBalance) >= 0.01
                    ? 'Stock in Hand from inventory differs from Opening Stock Adjustment ledger. Inventory is shown for reference only and is not posted to the audit voucher.'
                    : 'Stock in Hand matches Opening Stock Adjustment and is already included in the ledger opening balances.',
            ];
        }

        return [
            'accounts' => $accounts,
            'total_debit' => round($totalDebit, 2),
            'total_credit' => round($totalCredit, 2),
            'stock_in_hand_note' => $stockInHandNote,
        ];
    }

    private function recordOpeningBalanceAudit(FinancialYear $year, array $payload): ?TransactionGroup
    {
        if (empty($payload['accounts'])) {
            return null;
        }

        $entries = [];

        foreach ($payload['accounts'] as $account) {
            if (empty($account['account_id'])) {
                continue;
            }

            $entries[] = [
                'account_id' => $account['account_id'],
                'debit' => $account['debit'],
                'credit' => $account['credit'],
                'remarks' => 'Opening balance brought forward',
            ];
        }

        if (empty($entries)) {
            return null;
        }

        $group = $this->accountingService->recordAuditVoucher(
            $entries,
            "Opening Balance Audit - {$year->label}",
            $year->start_date->toDateString(),
            'Journal',
            $year->id,
            'financial_year_opening',
            (string) $year->id
        );

        foreach ($payload['accounts'] as $account) {
            if (empty($account['account_id'])) {
                continue;
            }

            FinancialYearOpeningBalance::create([
                'financial_year_id' => $year->id,
                'account_id' => $account['account_id'],
                'debit' => $account['debit'],
                'credit' => $account['credit'],
            ]);
        }

        return $group;
    }

    /**
     * Letter used in purchase reference numbers for a financial year (A, B, C, ...).
     * The first configured year is A; each subsequent year increments the letter.
     */
    public function getRefLetter(FinancialYear $year): string
    {
        $index = FinancialYear::where('start_date', '<=', $year->start_date->toDateString())
            ->count() - 1;

        return $this->indexToRefLetter(max(0, $index));
    }

    /**
     * Build a document ref prefix for a financial year, e.g. PRA, JVA, PVB.
     */
    public function getRefPrefix(FinancialYear $year, string $basePrefix): string
    {
        return $basePrefix.$this->getRefLetter($year);
    }

    /**
     * Build the purchase ref prefix for a financial year, e.g. PRA, PRB.
     */
    public function getPurchaseRefPrefix(FinancialYear $year, string $basePrefix = 'PR'): string
    {
        return $this->getRefPrefix($year, $basePrefix);
    }

    public function indexToRefLetter(int $index): string
    {
        $letter = '';
        $index++;

        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)).$letter;
            $index = intdiv($index, 26);
        }

        return $letter;
    }

    private function formatYear(FinancialYear $year): array
    {
        return [
            'id' => $year->id,
            'label' => $year->label,
            'start_date' => $year->start_date->format('Y-m-d'),
            'end_date' => $year->end_date->format('Y-m-d'),
            'status' => $year->status,
            'is_current' => $year->is_current,
            'net_profit_loss' => $year->net_profit_loss !== null ? number_format((float) $year->net_profit_loss, 2, '.', '') : null,
            'closing_voucher_no' => $year->closingTransactionGroup?->voucher_no,
            'opening_voucher_no' => $year->openingTransactionGroup?->voucher_no,
            'closed_at' => $year->closed_at?->format('Y-m-d H:i:s'),
            'closed_by' => $year->closedByUser?->name,
            'opened_at' => $year->opened_at?->format('Y-m-d H:i:s'),
            'opened_by' => $year->openedByUser?->name,
        ];
    }
}
