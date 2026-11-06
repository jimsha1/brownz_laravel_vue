<?php

namespace App\Http\Controllers\Reports;

use App\Exports\BalanceSheetExport;
use App\Exports\DayBookExport;
use App\Exports\ProfitLossAccountTransactionsExport;
use App\Exports\ProfitLossExport;
use App\Exports\TrialBalanceExport;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Journal;
use App\Models\StockItem;
use App\Models\Transaction;
use App\Models\TransactionGroup;
use App\Service\FinancialYearService;
use App\Support\ReversalPairFilter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class FinancialStatementController extends Controller
{
    public function __construct(private FinancialYearService $financialYearService) {}

    private function reportViewData(): array
    {
        $financialYears = $this->financialYearService->listYearsForReports();
        $currentYear = $this->financialYearService->getCurrentFinancialYear();
        $selectedFinancialYearId = $currentYear?->id ?? ($financialYears[0]['id'] ?? null);
        $selectedYear = collect($financialYears)->firstWhere('id', $selectedFinancialYearId);
        $defaultReportDates = $selectedYear
            ? [
                'start_date' => $selectedYear['report_start_date'],
                'end_date' => $selectedYear['report_end_date'],
            ]
            : $this->financialYearService->getDefaultReportDateRange();

        return compact('financialYears', 'selectedFinancialYearId', 'defaultReportDates');
    }

    // Trial Balance
    public function trialBalance(Request $request)
    {
        return view('accounts.financial-statement.trial-balance', $this->reportViewData());
    }
    // public function trialbalance_vue_list(Request $request)
    // {
    //     $startDate = $request->start_date;
    //     $endDate = $request->end_date;
    //     $search = $request->search;
    //     $pageLength = $request->page_length ?? 10;

    //     $query = Account::whereHas('journals.transactions', function ($q) use ($startDate, $endDate) {
    //         if (!empty($startDate) && !empty($endDate)) {
    //             $q->whereBetween('date', [$startDate, $endDate]);
    //         }
    //     })->with(['journals.transactions' => function ($q) use ($startDate, $endDate) {
    //         $q->whereBetween('date', [$startDate, $endDate]);
    //     }]);

    //     if (!empty($search)) {
    //         $query->where('name', 'like', "%{$search}%");
    //     }

    //     $accounts = $query->paginate($pageLength);

    //     $trialBalanceData = [];
    //     $totalDebit = 0;
    //     $totalCredit = 0;

    //     foreach ($accounts as $account) {
    //         $balance = $account->balance;
    //         $debit = 0;
    //         $credit = 0;

    //         // Assign balance to Debit or Credit column based on type and sign
    //         if ($balance >= 0) {
    //             if (in_array($account->type, ['asset', 'expense'])) {
    //                 $debit = $balance;
    //                 $totalDebit += $balance;
    //             } elseif (in_array($account->type, ['liability', 'equity', 'revenue'])) {
    //                 $credit = $balance;
    //                 $totalCredit += $balance;
    //             }
    //         } else {
    //             // Negative balance flips to opposite column
    //             $absBalance = abs($balance);
    //             if (in_array($account->type, ['asset', 'expense'])) {
    //                 $credit = $absBalance;
    //                 $totalCredit += $absBalance;
    //             } elseif (in_array($account->type, ['liability', 'equity', 'revenue'])) {
    //                 $debit = $absBalance;
    //                 $totalDebit += $absBalance;
    //             }
    //         }

    //         $trialBalanceData[] = [
    //             'account_id' => $account->account_id,
    //             'name' => $account->name,
    //             'type' => $account->type,
    //             'debit' => number_format($debit, 2, '.', ','),
    //             'credit' => number_format($credit, 2, '.', ','),
    //         ];
    //     }

    //     // Replace paginated account data with trial balance data
    //     $response = [
    //         'pagination' => [
    //             'total' => $accounts->total(),
    //             'per_page' => $accounts->perPage(),
    //             'current_page' => $accounts->currentPage(),
    //             'last_page' => $accounts->lastPage(),
    //             'from' => $accounts->firstItem(),
    //             'to' => $accounts->lastItem()
    //         ],
    //         'data' => $trialBalanceData,
    //         'total_debit' => number_format($totalDebit, 2, '.', ','),
    //         'total_credit' => number_format($totalCredit, 2, '.', ',')
    //     ];

    //     return response()->json($response);
    // }

    public function trialbalance_vue_list(Request $request)
    {
        $report = $this->buildTrialBalanceReport($request);

        return response()->json([
            'pagination' => $report['pagination'],
            'data' => $report['page_rows'],
            'total_debit' => number_format($report['total_debit'], 2, '.', ','),
            'total_credit' => number_format($report['total_credit'], 2, '.', ','),
            'balance_check' => $report['balance_check'],
            'balance_difference' => number_format($report['balance_difference'], 2, '.', ','),
            'actual_total_debit' => number_format($report['actual_total_debit'], 2, '.', ','),
            'actual_total_credit' => number_format($report['actual_total_credit'], 2, '.', ','),
        ]);
    }

    public function trialBalanceExportToPDF(Request $request)
    {
        $report = $this->buildTrialBalanceReport($request, paginate: false);
        $fileName = 'trial_balance_'.date('Y_m_d_H_i_s').'.pdf';

        $pdf = Pdf::loadView('accounts.financial-statement.trial-balance-pdf-view', [
            'records' => $report['rows'],
            'total_debit' => $report['total_debit'],
            'total_credit' => $report['total_credit'],
            'balance_check' => $report['balance_check'],
            'balance_difference' => $report['balance_difference'],
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'search' => $request->input('search'),
        ]);

        return $pdf->download($fileName);
    }

    public function trialBalanceExportToExcel(Request $request)
    {
        $report = $this->buildTrialBalanceReport($request, paginate: false);
        $fileName = 'trial_balance_'.date('Y_m_d_H_i_s').'.xlsx';

        return Excel::download(new TrialBalanceExport(
            $report['rows'],
            $report['total_debit'],
            $report['total_credit'],
            $request->input('start_date'),
            $request->input('end_date'),
            $request->input('search'),
        ), $fileName);
    }

    public function trialBalancePrint(Request $request)
    {
        $report = $this->buildTrialBalanceReport($request, paginate: false);

        return view('accounts.financial-statement.trial-balance-print', [
            'records' => $report['rows'],
            'total_debit' => $report['total_debit'],
            'total_credit' => $report['total_credit'],
            'balance_check' => $report['balance_check'],
            'balance_difference' => $report['balance_difference'],
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'search' => $request->input('search'),
        ]);
    }

    /**
     * @return array{
     *     rows: array<int, array<string, mixed>>,
     *     page_rows: array<int, array<string, mixed>>,
     *     total_debit: float,
     *     total_credit: float,
     *     balance_check: bool,
     *     balance_difference: float,
     *     actual_total_debit: float,
     *     actual_total_credit: float,
     *     pagination: array<string, int>|null
     * }
     */
    private function buildTrialBalanceReport(Request $request, bool $paginate = true): array
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $search = $request->input('search');
        $pageLength = (int) $request->input('page_length', config('settings.pagination.perpage.admin', 10));
        $sortOrder = $request->input('sort_order', 'asc');
        $currentPage = max(1, (int) $request->input('page', 1));

        $accountsQuery = $this->trialBalanceAccountsQuery($startDate, $endDate, $search)
            ->orderBy('accounts.name', $sortOrder);

        $totalCount = (clone $accountsQuery)->get()->count();

        if ($paginate) {
            $offset = ($currentPage - 1) * $pageLength;
            $accounts = (clone $accountsQuery)
                ->offset($offset)
                ->limit($pageLength)
                ->get();
            $lastPage = max(1, (int) ceil($totalCount / $pageLength));
            $pagination = [
                'total' => $totalCount,
                'per_page' => $pageLength,
                'current_page' => $currentPage,
                'last_page' => $lastPage,
                'from' => $totalCount > 0 ? $offset + 1 : 0,
                'to' => min($offset + $pageLength, $totalCount),
            ];
        } else {
            $accounts = (clone $accountsQuery)->get();
            $pagination = null;
        }

        $allAccounts = (clone $accountsQuery)->get();
        $rows = $this->mapTrialBalanceAccounts($allAccounts);
        $pageRows = $paginate ? $this->mapTrialBalanceAccounts($accounts) : $rows;

        $totalDebit = round(collect($rows)->sum('debit_value'), 2);
        $totalCredit = round(collect($rows)->sum('credit_value'), 2);

        $actualTransactionTotals = Transaction::forReporting()
            ->when(! empty($startDate) && ! empty($endDate), function ($q) use ($startDate, $endDate) {
                return $q->whereBetween('date', [$startDate, $endDate]);
            })
            ->when(empty($startDate) && ! empty($endDate), function ($q) use ($endDate) {
                return $q->where('date', '<=', $endDate);
            })
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();

        return [
            'rows' => $rows,
            'page_rows' => $pageRows,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'balance_check' => abs($totalDebit - $totalCredit) < 0.01,
            'balance_difference' => round(abs($totalDebit - $totalCredit), 2),
            'actual_total_debit' => (float) ($actualTransactionTotals->total_debit ?? 0),
            'actual_total_credit' => (float) ($actualTransactionTotals->total_credit ?? 0),
            'pagination' => $pagination,
        ];
    }

    private function trialBalanceAccountsQuery(?string $startDate, ?string $endDate, ?string $search = null)
    {
        $nonZeroHaving = 'ABS(COALESCE(SUM(transactions.debit), 0) - COALESCE(SUM(transactions.credit), 0)) > 0.009';

        $query = Account::leftJoin('journals', 'accounts.account_id', '=', 'journals.account_id')
            ->leftJoin('transactions', function ($join) use ($startDate, $endDate) {
                $join->on('journals.id', '=', 'transactions.journal_id')
                    ->whereRaw(reportingTransactionSqlConstraint());
                if (! empty($startDate) && ! empty($endDate)) {
                    $join->whereBetween('transactions.date', [$startDate, $endDate]);
                } elseif (! empty($endDate)) {
                    $join->where('transactions.date', '<=', $endDate);
                }
            })
            ->selectRaw('accounts.account_id, accounts.name, accounts.type,
                        COALESCE(SUM(transactions.debit), 0) as total_debit,
                        COALESCE(SUM(transactions.credit), 0) as total_credit')
            ->groupBy('accounts.account_id', 'accounts.name', 'accounts.type')
            ->havingRaw($nonZeroHaving);

        if (! empty($search)) {
            $query->where('accounts.name', 'like', "%{$search}%");
        }

        return $query;
    }

    private function mapTrialBalanceAccounts(Collection $accounts): array
    {
        $rows = [];

        foreach ($accounts as $account) {
            [$debit, $credit] = $this->trialBalanceDebitCredit(
                (float) $account->total_debit,
                (float) $account->total_credit,
                $account->type
            );

            if (abs($debit) < 0.01 && abs($credit) < 0.01) {
                continue;
            }

            $rows[] = [
                'account_id' => $account->account_id,
                'name' => $account->name,
                'type' => $account->type,
                'debit' => number_format($debit, 2, '.', ','),
                'credit' => number_format($credit, 2, '.', ','),
                'debit_value' => $debit,
                'credit_value' => $credit,
            ];
        }

        return $rows;
    }

    /**
     * Map period debit/credit totals to trial-balance display columns.
     *
     * @return array{0: float, 1: float} [debit, credit]
     */
    private function trialBalanceDebitCredit(float $totalDebitAmount, float $totalCreditAmount, ?string $type): array
    {
        $netBalance = round($totalDebitAmount - $totalCreditAmount, 2);
        if (abs($netBalance) < 0.01) {
            return [0.0, 0.0];
        }

        $type = strtolower((string) $type);

        if (in_array($type, ['asset', 'expense'], true)) {
            return $netBalance >= 0 ? [$netBalance, 0.0] : [0.0, abs($netBalance)];
        }

        if (in_array($type, ['liability', 'equity', 'revenue'], true)) {
            return $netBalance <= 0 ? [0.0, abs($netBalance)] : [$netBalance, 0.0];
        }

        // Fallback for unexpected type casing/values
        return $netBalance >= 0 ? [$netBalance, 0.0] : [0.0, abs($netBalance)];
    }

    // Profit And Loss
    public function profitLoss(Request $request)
    {
        return view('accounts.financial-statement.profit-loss', $this->reportViewData());
    }

    // public function profitloss_vue_list(Request $request)
    // {
    //     $startDate = $request->start_date;
    //     $endDate = $request->end_date;
    //     $search = $request->search;

    //     // Fetch revenue and expense accounts
    //     $revenueAccounts = Account::where('type', 'revenue')->pluck('account_id');
    //     $expenseAccounts = Account::where('type', 'expense')->pluck('account_id');

    //     // Get all revenue transactions for the period
    //     $revenues = Transaction::whereIn('journal_id', function ($query) use ($revenueAccounts) {
    //         $query->select('id')->from('journals')->whereIn('account_id', $revenueAccounts);
    //     })
    //         ->whereBetween('date', [$startDate, $endDate])
    //         ->selectRaw('journal_id, SUM(credit) - SUM(debit) as net_amount')
    //         ->groupBy('journal_id')
    //         ->with(['journal.account' => function ($query) {
    //             $query->select('account_id', 'name');
    //         }])
    //         ->get();

    //     // Get all expense transactions for the period
    //     $expenses = Transaction::whereIn('journal_id', function ($query) use ($expenseAccounts) {
    //         $query->select('id')->from('journals')->whereIn('account_id', $expenseAccounts);
    //     })
    //         ->whereBetween('date', [$startDate, $endDate])
    //         ->selectRaw('journal_id, SUM(debit) - SUM(credit) as net_amount')
    //         ->groupBy('journal_id')
    //         ->with(['journal.account' => function ($query) {
    //             $query->select('account_id', 'name');
    //         }])
    //         ->get();
    //     $openingStock = DB::table('stock_movements as sm')
    //         ->join('items as i', 'i.item_id', '=', 'sm.item_id')
    //         ->whereNull('sm.type')
    //         ->where('sm.description', 'like', 'Opening stock%')
    //         ->selectRaw('SUM(sm.stock_in * i.purchase_price) as amount')
    //         ->value('amount') ?? 0;

    //     // Calculate totals
    //     $totalRevenue = $revenues->sum('net_amount');
    //     $totalExpenses = $expenses->sum('net_amount');
    //     $netIncome = $totalRevenue - $totalExpenses;

    //     return response()->json([
    //         'data' => [
    //             'revenues' => $revenues,
    //             'expenses' => $expenses,
    //         ],
    //         'total_debit' => $totalExpenses,
    //         'total_credit' => $totalRevenue,
    //         'net_income' => $netIncome,
    //     ]);
    // }

    // public function profitloss_vue_list(Request $request)
    // {
    //     $startDate = $request->start_date;
    //     $endDate = $request->end_date;

    //     $revenueAccounts = Account::where('type', 'revenue')
    //         ->pluck('account_id');

    //     $expenseAccounts = Account::where('type', 'expense')
    //         ->pluck('account_id');

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Revenue Accounts
    //     |--------------------------------------------------------------------------
    //     */
    //     $revenues = Transaction::whereIn('journal_id', function ($query) use ($revenueAccounts) {
    //         $query->select('id')
    //             ->from('journals')
    //             ->whereIn('account_id', $revenueAccounts);
    //     })
    //         ->whereBetween('date', [$startDate, $endDate])
    //         ->selectRaw('journal_id,SUM(credit)-SUM(debit) as net_amount')
    //         ->groupBy('journal_id')
    //         ->with('journal.account')
    //         ->get();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Expense Accounts
    //     |--------------------------------------------------------------------------
    //     */
    //     $expenses = Transaction::whereIn('journal_id', function ($query) use ($expenseAccounts) {
    //         $query->select('id')
    //             ->from('journals')
    //             ->whereIn('account_id', $expenseAccounts);
    //     })
    //         ->whereBetween('date', [$startDate, $endDate])
    //         ->selectRaw('journal_id,SUM(debit)-SUM(credit) as net_amount')
    //         ->groupBy('journal_id')
    //         ->with('journal.account')
    //         ->get();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Opening Stock
    //     |--------------------------------------------------------------------------
    //     */
    //     $openingStock = DB::table('stock_movements as sm')
    //         ->join('items as i', 'i.item_id', '=', 'sm.item_id')
    //         ->whereNull('sm.type')
    //         ->where('sm.description', 'like', 'Opening stock%')
    //         ->selectRaw('SUM(sm.stock_in * i.purchase_price) as amount')
    //         ->value('amount') ?? 0;

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Debit Side
    //     |--------------------------------------------------------------------------
    //     */
    //     $debitSide = [];

    //     if ($openingStock > 0) {
    //         $debitSide[] = [
    //             'account_name' => 'Opening Stock',
    //             'amount' => round($openingStock, 2),
    //         ];
    //     }

    //     foreach ($expenses as $expense) {
    //         $debitSide[] = [
    //             'account_name' => $expense->journal->account->name,
    //             'amount' => round($expense->net_amount, 2),
    //         ];
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Credit Side
    //     |--------------------------------------------------------------------------
    //     */
    //     $creditSide = [];

    //     foreach ($revenues as $revenue) {
    //         $creditSide[] = [
    //             'account_name' => $revenue->journal->account->name,
    //             'amount' => round($revenue->net_amount, 2),
    //         ];
    //     }

    //     $totalDebit = collect($debitSide)->sum('amount');
    //     $totalCredit = collect($creditSide)->sum('amount');

    //     $netIncome = $totalCredit - $totalDebit;

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Balance Both Sides
    //     |--------------------------------------------------------------------------
    //     */
    //     if ($netIncome > 0) {

    //         $debitSide[] = [
    //             'account_name' => 'To Net Profit',
    //             'amount' => round($netIncome, 2),
    //         ];

    //         $totalDebit += $netIncome;

    //     } elseif ($netIncome < 0) {

    //         $creditSide[] = [
    //             'account_name' => 'By Net Loss',
    //             'amount' => round(abs($netIncome), 2),
    //         ];

    //         $totalCredit += abs($netIncome);
    //     }

    //     return response()->json([
    //         'debit_side' => $debitSide,
    //         'credit_side' => $creditSide,
    //         'total_debit' => number_format($totalDebit, 2, '.', ''),
    //         'total_credit' => number_format($totalCredit, 2, '.', ''),
    //         'net_income' => round($netIncome, 2),
    //     ]);
    // }

    // public function profitloss_vue_list(Request $request)
    // {
    //     $startDate = $request->start_date;
    //     $endDate = $request->end_date;

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Revenue Accounts
    //     |--------------------------------------------------------------------------
    //     */
    //     $revenueAccounts = Account::where('type', 'revenue')
    //         ->pluck('account_id');

    //     $revenues = Transaction::whereIn('journal_id', function ($query) use ($revenueAccounts) {
    //         $query->select('id')
    //             ->from('journals')
    //             ->whereIn('account_id', $revenueAccounts);
    //     })
    //         ->whereBetween('date', [$startDate, $endDate])
    //         ->selectRaw('journal_id, SUM(credit) - SUM(debit) as net_amount')
    //         ->groupBy('journal_id')
    //         ->with('journal.account')
    //         ->get();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Expense Accounts
    //     |--------------------------------------------------------------------------
    //     */
    //     $expenseAccounts = Account::where('type', 'expense')
    //         ->pluck('account_id');

    //     $expenses = Transaction::whereIn('journal_id', function ($query) use ($expenseAccounts) {
    //         $query->select('id')
    //             ->from('journals')
    //             ->whereIn('account_id', $expenseAccounts);
    //     })
    //         ->whereBetween('date', [$startDate, $endDate])
    //         ->selectRaw('journal_id, SUM(debit) - SUM(credit) as net_amount')
    //         ->groupBy('journal_id')
    //         ->with('journal.account')
    //         ->get();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Opening Stock
    //     |--------------------------------------------------------------------------
    //     */
    //     $openingStock = DB::table('stock_movements as sm')
    //         ->join('inventory_items as ii', 'ii.item_id', '=', 'sm.item_id')
    //         ->whereNull('sm.type')
    //         ->where('sm.description', 'like', 'Opening stock%')
    //         ->selectRaw('SUM(sm.stock_in * ii.cost_price) as amount')
    //         ->value('amount') ?? 0;

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Closing Stock
    //     |--------------------------------------------------------------------------
    //     */
    //     $closingStock = DB::table('inventory_items')
    //         ->selectRaw('SUM(quantity * cost_price) as amount')
    //         ->value('amount') ?? 0;

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Debit Side
    //     |--------------------------------------------------------------------------
    //     */
    //     $debitSide = [];

    //     if ($openingStock > 0) {
    //         $debitSide[] = [
    //             'account_name' => 'Opening Stock',
    //             'amount' => round($openingStock, 2),
    //         ];
    //     }

    //     foreach ($expenses as $expense) {

    //         if ($expense->net_amount == 0) {
    //             continue;
    //         }

    //         $debitSide[] = [
    //             'account_name' => $expense->journal->account->name ?? '',
    //             'amount' => round($expense->net_amount, 2),
    //         ];
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Credit Side
    //     |--------------------------------------------------------------------------
    //     */
    //     $creditSide = [];

    //     if ($closingStock > 0) {
    //         $creditSide[] = [
    //             'account_name' => 'Closing Stock',
    //             'amount' => round($closingStock, 2),
    //         ];
    //     }

    //     foreach ($revenues as $revenue) {

    //         if ($revenue->net_amount == 0) {
    //             continue;
    //         }

    //         $creditSide[] = [
    //             'account_name' => $revenue->journal->account->name ?? '',
    //             'amount' => round($revenue->net_amount, 2),
    //         ];
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Totals Before Profit/Loss
    //     |--------------------------------------------------------------------------
    //     */
    //     $totalDebit = collect($debitSide)->sum('amount');
    //     $totalCredit = collect($creditSide)->sum('amount');

    //     $netIncome = $totalCredit - $totalDebit;

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Balance Both Sides
    //     |--------------------------------------------------------------------------
    //     */
    //     if ($netIncome > 0) {

    //         // Profit => Debit Side
    //         $debitSide[] = [
    //             'account_name' => 'To Net Profit',
    //             'amount' => round($netIncome, 2),
    //         ];

    //         $totalDebit += $netIncome;

    //     } elseif ($netIncome < 0) {

    //         // Loss => Credit Side
    //         $creditSide[] = [
    //             'account_name' => 'By Net Loss',
    //             'amount' => round(abs($netIncome), 2),
    //         ];

    //         $totalCredit += abs($netIncome);
    //     }

    //     return response()->json([
    //         'debit_side' => $debitSide,
    //         'credit_side' => $creditSide,
    //         'total_debit' => number_format($totalDebit, 2, '.', ''),
    //         'total_credit' => number_format($totalCredit, 2, '.', ''),
    //         'net_income' => round($netIncome, 2),
    //     ]);
    // }

    // public function profitloss_vue_list(Request $request)
    // {
    //     $startDate = $request->start_date;
    //     $endDate = $request->end_date;

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Revenue Accounts
    //     |--------------------------------------------------------------------------
    //     */
    //     $revenueAccounts = Account::where('type', 'revenue')
    //         ->pluck('account_id');

    //     $revenues = Transaction::whereIn('journal_id', function ($query) use ($revenueAccounts) {
    //         $query->select('id')
    //             ->from('journals')
    //             ->whereIn('account_id', $revenueAccounts);
    //     })
    //         ->whereBetween('date', [$startDate, $endDate])
    //         ->selectRaw('journal_id, SUM(credit) - SUM(debit) as net_amount')
    //         ->groupBy('journal_id')
    //         ->with('journal.account')
    //         ->get();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Expense Accounts
    //     |--------------------------------------------------------------------------
    //     */
    //     $expenseAccounts = Account::where('type', 'expense')
    //         ->pluck('account_id');

    //     $expenses = Transaction::whereIn('journal_id', function ($query) use ($expenseAccounts) {
    //         $query->select('id')
    //             ->from('journals')
    //             ->whereIn('account_id', $expenseAccounts);
    //     })
    //         ->whereBetween('date', [$startDate, $endDate])
    //         ->selectRaw('journal_id, SUM(debit) - SUM(credit) as net_amount')
    //         ->groupBy('journal_id')
    //         ->with('journal.account')
    //         ->get();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Opening Stock
    //     |--------------------------------------------------------------------------
    //     */
    //     $openingStock = DB::table('stock_movements as sm')
    //         ->join('inventory_items as ii', 'ii.item_id', '=', 'sm.item_id')
    //         ->whereNull('sm.type')
    //         ->where('sm.description', 'like', 'Opening stock%')
    //         ->selectRaw('SUM(sm.stock_in * ii.cost_price) as amount')
    //         ->value('amount') ?? 0;

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Closing Stock
    //     |--------------------------------------------------------------------------
    //     */
    //     // $closingStock = DB::table('inventory_items')
    //     //     ->selectRaw('SUM(quantity * cost_price) as amount')
    //     //     ->value('amount') ?? 0;
    //     $closingStock = DB::table('stock_items')
    //         ->selectRaw('SUM(current_stock * cost_price) as amount')
    //         ->value('amount') ?? 0;

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Classify Revenue Accounts
    //     |--------------------------------------------------------------------------
    //     */
    //     $salesRevenue = 0;
    //     $serviceRevenue = 0;
    //     $salesReturn = 0;
    //     $discountReceived = 0;
    //     $indirectIncome = 0;

    //     foreach ($revenues as $revenue) {

    //         $accountName = strtolower($revenue->journal->account->name ?? '');
    //         $amount = (float) $revenue->net_amount;

    //         if (str_contains($accountName, 'sales')) {

    //             $salesRevenue += $amount;

    //         } elseif (str_contains($accountName, 'service')) {

    //             $serviceRevenue += $amount;

    //         } elseif (str_contains($accountName, 'return')) {

    //             $salesReturn += $amount;

    //         } elseif (str_contains($accountName, 'discount')) {

    //             $discountReceived += $amount;

    //         } else {

    //             $indirectIncome += $amount;
    //         }
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Classify Expense Accounts
    //     |--------------------------------------------------------------------------
    //     */
    //     $purchaseAccount = 0;
    //     $directExpenses = 0;
    //     $indirectExpenses = 0;

    //     foreach ($expenses as $expense) {

    //         $accountName = strtolower($expense->journal->account->name ?? '');
    //         $amount = (float) $expense->net_amount;

    //         if (str_contains($accountName, 'purchase')) {

    //             $purchaseAccount += $amount;

    //         } elseif (
    //             str_contains($accountName, 'carriage') ||
    //             str_contains($accountName, 'freight') ||
    //             str_contains($accountName, 'loading') ||
    //             str_contains($accountName, 'direct')
    //         ) {

    //             $directExpenses += $amount;

    //         } else {

    //             $indirectExpenses += $amount;
    //         }
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Trading Account
    //     |--------------------------------------------------------------------------
    //     */
    //     $tradingDebit =
    //         $openingStock +
    //         $purchaseAccount +
    //         $directExpenses;

    //     $tradingCredit =
    //         $salesRevenue +
    //         $serviceRevenue +
    //         $salesReturn +
    //         $discountReceived +
    //         $closingStock;

    //     $grossProfit = $tradingCredit - $tradingDebit;

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Profit & Loss Account
    //     |--------------------------------------------------------------------------
    //     */
    //     $plCredit =
    //         ($grossProfit > 0 ? $grossProfit : 0) +
    //         $indirectIncome;

    //     $plDebit = $indirectExpenses;

    //     $netProfit = $plCredit - $plDebit;

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Trading Account Rows
    //     |--------------------------------------------------------------------------
    //     */
    //     $tradingDebitSide = [
    //         [
    //             'account_name' => 'Opening Stock',
    //             'amount' => round($openingStock, 2),
    //         ],
    //         [
    //             'account_name' => 'Purchase Account',
    //             'amount' => round($purchaseAccount, 2),
    //         ],
    //         [
    //             'account_name' => 'Direct Expenses',
    //             'amount' => round($directExpenses, 2),
    //         ],
    //         [
    //             'account_name' => 'Gross Profit c/o',
    //             'amount' => round(max($grossProfit, 0), 2),
    //         ],
    //     ];

    //     $tradingCreditSide = [
    //         [
    //             'account_name' => 'Sales Revenue',
    //             'amount' => round($salesRevenue, 2),
    //         ],
    //         [
    //             'account_name' => 'Service Revenue',
    //             'amount' => round($serviceRevenue, 2),
    //         ],
    //         [
    //             'account_name' => 'Sales Return',
    //             'amount' => round($salesReturn, 2),
    //         ],
    //         [
    //             'account_name' => 'Discount Received',
    //             'amount' => round($discountReceived, 2),
    //         ],
    //         [
    //             'account_name' => 'Closing Stock',
    //             'amount' => round($closingStock, 2),
    //         ],
    //     ];

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Profit & Loss Rows
    //     |--------------------------------------------------------------------------
    //     */
    //     $plDebitSide = [
    //         [
    //             'account_name' => 'Indirect Expenses',
    //             'amount' => round($indirectExpenses, 2),
    //         ],
    //     ];

    //     if ($netProfit < 0) {
    //         $plDebitSide[] = [
    //             'account_name' => 'Net Loss',
    //             'amount' => round(abs($netProfit), 2),
    //         ];
    //     }

    //     $plCreditSide = [
    //         [
    //             'account_name' => 'Gross Profit b/f',
    //             'amount' => round(max($grossProfit, 0), 2),
    //         ],
    //         [
    //             'account_name' => 'Indirect Income',
    //             'amount' => round($indirectIncome, 2),
    //         ],
    //     ];

    //     if ($netProfit > 0) {
    //         $plCreditSide[] = [
    //             'account_name' => 'Net Profit',
    //             'amount' => round($netProfit, 2),
    //         ];
    //     }

    //     return response()->json([
    //         'trading_debit_side' => $tradingDebitSide,
    //         'trading_credit_side' => $tradingCreditSide,

    //         'pl_debit_side' => $plDebitSide,
    //         'pl_credit_side' => $plCreditSide,

    //         'gross_profit' => round($grossProfit, 2),
    //         'net_profit' => round($netProfit, 2),

    //         'opening_stock' => round($openingStock, 2),
    //         'closing_stock' => round($closingStock, 2),
    //     ]);
    // }
    public function profitloss_vue_list(Request $request)
    {
        return response()->json($this->buildProfitLossReport($request));
    }

    public function profitLossExportToPDF(Request $request)
    {
        $report = $this->buildProfitLossReport($request);
        $fileName = 'profit_loss_'.date('Y_m_d_H_i_s').'.pdf';

        $pdf = Pdf::loadView('accounts.financial-statement.profit-loss-pdf-view', array_merge($report, [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]));

        return $pdf->download($fileName);
    }

    public function profitLossExportToExcel(Request $request)
    {
        $report = $this->buildProfitLossReport($request);
        $fileName = 'profit_loss_'.date('Y_m_d_H_i_s').'.xlsx';

        return Excel::download(new ProfitLossExport(
            $report,
            $request->input('start_date'),
            $request->input('end_date'),
        ), $fileName);
    }

    public function profitLossPrint(Request $request)
    {
        $report = $this->buildProfitLossReport($request);

        return view('accounts.financial-statement.profit-loss-print', array_merge($report, [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]));
    }

    private function buildProfitLossReport(Request $request): array
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $row = function (string $name, float $amount, array $extra = []) {
            return array_merge([
                'account_name' => $name,
                'amount' => round($amount, 2),
                'include_in_total' => true,
                'is_italic' => false,
                'is_indent' => false,
                'is_deduction' => false,
            ], $extra);
        };

        $openingStock = $this->resolveProfitLossOpeningStock($startDate, $endDate);

        /*
        |--------------------------------------------------------------------------
        | Closing Stock (inventory at period end; uses year-end close when available)
        |--------------------------------------------------------------------------
        */
        $closingStock = $this->financialYearService->resolveClosingStockForProfitLoss($endDate);

        /*
        |--------------------------------------------------------------------------
        | Purchases / Purchase Returns (gross debit & credit on Purchase Account)
        |--------------------------------------------------------------------------
        */
        $purchaseAccountId = getAccount('Purchase Account');
        $grossPurchases = 0.0;
        $purchaseReturns = 0.0;

        if ($purchaseAccountId) {
            $purchaseTotals = Transaction::forProfitLoss()
                ->whereHas('journal', function ($q) use ($purchaseAccountId) {
                    $q->where('account_id', $purchaseAccountId);
                })
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
                ->first();

            $grossPurchases = (float) ($purchaseTotals->total_debit ?? 0);
            $purchaseReturns = (float) ($purchaseTotals->total_credit ?? 0);
        }

        // Legacy: stock purchases previously posted to Inventory (asset)
        $legacyInventoryId = getAccount('Inventory');
        if ($legacyInventoryId) {
            $legacyTotals = Transaction::forProfitLoss()
                ->whereHas('journal', function ($q) use ($legacyInventoryId) {
                    $q->where('account_id', $legacyInventoryId);
                })
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
                ->first();

            $legacyDebit = (float) ($legacyTotals->total_debit ?? 0);
            $legacyCredit = (float) ($legacyTotals->total_credit ?? 0);
            if (abs($legacyDebit) > 0.01 || abs($legacyCredit) > 0.01) {
                $grossPurchases += $legacyDebit;
                $purchaseReturns += $legacyCredit;
            }
        }

        $netPurchases = $grossPurchases - $purchaseReturns;

        /*
        |--------------------------------------------------------------------------
        | Sales Revenue + Service Revenue (Trading credit)
        |--------------------------------------------------------------------------
        */
        $salesRevenueId = getAccount('Sales Revenue')
            ?: Account::where('name', 'Sales Revenue')->value('account_id');
        $serviceRevenueId = getAccount('Service Revenue')
            ?: Account::where('name', 'Service Revenue')->value('account_id');

        $salesAccountIds = collect([$salesRevenueId, $serviceRevenueId])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $salesRevenue = 0.0;
        $serviceRevenue = 0.0;
        $salesReturns = 0.0;
        $grossSales = 0.0;

        if ($salesRevenueId) {
            $salesRow = Transaction::forProfitLoss()
                ->whereHas('journal', function ($q) use ($salesRevenueId) {
                    $q->where('account_id', $salesRevenueId);
                })
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('COALESCE(SUM(credit), 0) as total_credit, COALESCE(SUM(debit), 0) as total_debit')
                ->first();

            $salesCredits = (float) ($salesRow->total_credit ?? 0);
            $salesDebits = (float) ($salesRow->total_debit ?? 0);
            $salesRevenue = $salesCredits - $salesDebits;
            $salesReturns += $salesDebits;
            $grossSales += $salesCredits;
        }

        if ($serviceRevenueId) {
            $serviceRow = Transaction::forProfitLoss()
                ->whereHas('journal', function ($q) use ($serviceRevenueId) {
                    $q->where('account_id', $serviceRevenueId);
                })
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('COALESCE(SUM(credit), 0) as total_credit, COALESCE(SUM(debit), 0) as total_debit')
                ->first();

            $serviceCredits = (float) ($serviceRow->total_credit ?? 0);
            $serviceDebits = (float) ($serviceRow->total_debit ?? 0);
            $serviceRevenue = $serviceCredits - $serviceDebits;
            $salesReturns += $serviceDebits;
            $grossSales += $serviceCredits;
        }

        $netSales = $salesRevenue + $serviceRevenue;

        /*
        |--------------------------------------------------------------------------
        | Direct / Indirect Expenses & Indirect Incomes
        |--------------------------------------------------------------------------
        */
        $expenseAccountIds = Account::where('type', 'expense')->pluck('account_id');

        $expenseRows = collect();
        if ($expenseAccountIds->isNotEmpty()) {
            $expenseRows = Transaction::forProfitLoss()
                ->whereIn('journal_id', function ($query) use ($expenseAccountIds) {
                    $query->select('id')
                        ->from('journals')
                        ->whereIn('account_id', $expenseAccountIds);
                })
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('journal_id, SUM(debit) - SUM(credit) as net_amount')
                ->groupBy('journal_id')
                ->with('journal.account')
                ->get();
        }

        $directExpenses = 0.0;
        $indirectExpenses = 0.0;
        $directExpenseDetails = [];
        $indirectExpenseDetails = [];
        $discountReceivedId = getAccount('Discount Received');
        $discountFromExpense = 0.0;

        foreach ($expenseRows as $expense) {
            $account = $expense->journal->account ?? null;
            $accountName = strtolower($account->name ?? '');
            $accountId = (int) ($account->account_id ?? 0);
            $amount = (float) $expense->net_amount;

            if (
                $accountId === (int) $purchaseAccountId
                || str_contains($accountName, 'purchase')
                || str_contains($accountName, 'cost of goods sold')
            ) {
                continue;
            }

            // Move Discount Received credits into Indirect Incomes (if posted under expenses)
            if ($discountReceivedId && $accountId === (int) $discountReceivedId) {
                $discountFromExpense = max(0, -$amount);
                continue;
            }

            if (
                str_contains($accountName, 'carriage')
                || str_contains($accountName, 'freight')
                || str_contains($accountName, 'loading')
                || str_contains($accountName, 'direct')
                || strcasecmp((string) ($account->category ?? ''), 'Direct Expense') === 0
            ) {
                $directExpenses += $amount;

                if (! isset($directExpenseDetails[$accountId])) {
                    $directExpenseDetails[$accountId] = [
                        'account_name' => $account->name ?? ('Account #'.$accountId),
                        'amount' => 0.0,
                    ];
                }
                $directExpenseDetails[$accountId]['amount'] += $amount;
            } else {
                $indirectExpenses += $amount;

                if (! isset($indirectExpenseDetails[$accountId])) {
                    $indirectExpenseDetails[$accountId] = [
                        'account_name' => $account->name ?? ('Account #'.$accountId),
                        'amount' => 0.0,
                    ];
                }
                $indirectExpenseDetails[$accountId]['amount'] += $amount;
            }
        }

        $otherRevenueAccountIds = Account::where('type', 'revenue')
            ->whereNotIn('account_id', $salesAccountIds)
            ->pluck('account_id');

        $indirectIncome = 0.0;
        if ($otherRevenueAccountIds->isNotEmpty()) {
            $indirectIncome = (float) Transaction::forProfitLoss()
                ->whereHas('journal', function ($q) use ($otherRevenueAccountIds) {
                    $q->whereIn('account_id', $otherRevenueAccountIds);
                })
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('COALESCE(SUM(credit - debit), 0) as amount')
                ->value('amount');
        }

        if ($discountFromExpense > 0.01) {
            $indirectIncome += $discountFromExpense;
        }

        /*
        |--------------------------------------------------------------------------
        | Trading Account
        | Gross Profit = (Net Sales + Closing Stock)
        |              - (Opening Stock + Net Purchases + Direct Expenses)
        |--------------------------------------------------------------------------
        */
        $grossProfit = ($netSales + $closingStock) - ($openingStock + $netPurchases + $directExpenses);

        $directExpenseChildren = collect($directExpenseDetails)
            ->filter(fn ($item) => abs((float) $item['amount']) > 0.01)
            ->sortByDesc(fn ($item) => (float) $item['amount'])
            ->map(function ($item) use ($row) {
                return $row($item['account_name'], (float) $item['amount'], [
                    'is_indent' => true,
                    'is_child' => true,
                    'include_in_total' => false,
                    'drilldown_type' => 'account',
                    'drilldown_name' => $item['account_name'],
                ]);
            })
            ->values()
            ->toArray();

        // Trading credit: both revenue ledgers shown separately, then Closing Stock
        $tradingDebitSide = [
            $row('Opening Stock', $openingStock),
            $row('Purchase Accounts', $netPurchases),
            $row('Direct Expenses', $directExpenses, [
                'is_expandable' => count($directExpenseChildren) > 0,
                'children' => $directExpenseChildren,
                'drilldown_type' => 'category',
                'drilldown_name' => 'Direct Expense',
            ]),
        ];

        $tradingCreditSide = [
            $row('Sales Revenue', $salesRevenue),
            $row('Service Revenue', $serviceRevenue),
            $row('Closing Stock', $closingStock),
        ];

        $salesBreakdown = [
            'sales_revenue' => round($salesRevenue, 2),
            'service_revenue' => round($serviceRevenue, 2),
            'sales_accounts_total' => round($netSales, 2),
            'sales_revenue_account_id' => $salesRevenueId ? (int) $salesRevenueId : null,
            'service_revenue_account_id' => $serviceRevenueId ? (int) $serviceRevenueId : null,
        ];

        if ($grossProfit >= 0) {
            $tradingDebitSide[] = $row('Gross Profit c/o', $grossProfit, ['is_italic' => true]);
        } else {
            $tradingCreditSide[] = $row('Gross Loss c/o', abs($grossProfit), ['is_italic' => true]);
        }

        // Trading totals ignore expanded child detail rows
        $tradingTotalDebit = round(collect($tradingDebitSide)
            ->filter(fn ($item) => ($item['include_in_total'] ?? true) !== false)
            ->sum('amount'), 2);
        $tradingTotalCredit = round(collect($tradingCreditSide)
            ->filter(fn ($item) => ($item['include_in_total'] ?? true) !== false)
            ->sum('amount'), 2);

        /*
        |--------------------------------------------------------------------------
        | Profit & Loss Account
        | Net Profit = Gross Profit + Indirect Incomes - Indirect Expenses
        | (shown on debit side as a signed balancing figure, matching screenshot)
        |--------------------------------------------------------------------------
        */
        $grossProfitBF = max($grossProfit, 0);
        $grossLossBF = max(-$grossProfit, 0);
        $netResult = ($grossProfitBF - $grossLossBF) + $indirectIncome - $indirectExpenses;

        $indirectExpenseChildren = collect($indirectExpenseDetails)
            ->filter(fn ($item) => abs((float) $item['amount']) > 0.01)
            ->sortByDesc(fn ($item) => (float) $item['amount'])
            ->map(function ($item) use ($row) {
                return $row($item['account_name'], (float) $item['amount'], [
                    'is_indent' => true,
                    'is_child' => true,
                    'include_in_total' => false,
                    'drilldown_type' => 'account',
                    'drilldown_name' => $item['account_name'],
                ]);
            })
            ->values()
            ->toArray();

        $plDebitSide = [
            $row('Indirect Expenses', $indirectExpenses, [
                'is_expandable' => count($indirectExpenseChildren) > 0,
                'children' => $indirectExpenseChildren,
                'drilldown_type' => 'category',
                'drilldown_name' => 'Indirect Expense',
            ]),
        ];

        if ($grossLossBF > 0.01) {
            $plDebitSide[] = $row('Gross Loss b/f', $grossLossBF, ['is_italic' => true]);
        }

        // Always show Net Profit on debit (negative = net loss), as in the shared screenshot
        $plDebitSide[] = $row('Net Profit', $netResult, ['is_italic' => true]);

        $plCreditSide = [
            $row('Gross Profit b/f', $grossProfitBF, ['is_italic' => true]),
            $row('Indirect Incomes', $indirectIncome),
        ];

        // Final totals both equal Gross Profit b/f + Indirect Incomes (screenshot balancing)
        $plTotalCredit = round($grossProfitBF + $indirectIncome, 2);
        $plTotalDebit = $plTotalCredit;
        $plTotal = $plTotalCredit;

        $netProfit = $netResult >= 0 ? $netResult : 0.0;
        $netLoss = $netResult < 0 ? abs($netResult) : 0.0;

        return [
            'trading_debit_side' => $tradingDebitSide,
            'trading_credit_side' => $tradingCreditSide,

            'pl_debit_side' => $plDebitSide,
            'pl_credit_side' => $plCreditSide,

            'trading_total_debit' => $tradingTotalDebit,
            'trading_total_credit' => $tradingTotalCredit,

            'gross_profit' => round($grossProfit, 2),

            'net_profit' => round($netProfit, 2),
            'net_loss' => round($netLoss, 2),
            'net_result' => round($netResult, 2),

            'pl_total' => round($plTotal, 2),
            'pl_total_debit' => $plTotalDebit,
            'pl_total_credit' => $plTotalCredit,

            'opening_stock' => round($openingStock, 2),
            'closing_stock' => round($closingStock, 2),
            'gross_purchases' => round($grossPurchases, 2),
            'purchase_returns' => round($purchaseReturns, 2),
            'net_purchases' => round($netPurchases, 2),
            'gross_sales' => round($grossSales, 2),
            'sales_returns' => round($salesReturns, 2),
            'net_sales' => round($netSales, 2),
            'sales_revenue' => round($salesRevenue, 2),
            'service_revenue' => round($serviceRevenue, 2),
            'sales_breakdown' => $salesBreakdown,
            'direct_expenses' => round($directExpenses, 2),
            'direct_expense_details' => $directExpenseChildren,
            'indirect_expenses' => round($indirectExpenses, 2),
            'indirect_expense_details' => $indirectExpenseChildren,
            'indirect_incomes' => round($indirectIncome, 2),
        ];
    }

    private function expandProfitLossSide(array $rows): array
    {
        $expanded = [];

        foreach ($rows as $row) {
            $expanded[] = $row;

            if (! empty($row['children']) && is_array($row['children'])) {
                foreach ($row['children'] as $child) {
                    $expanded[] = $child;
                }
            }
        }

        return $expanded;
    }

    // Balance Sheet
    public function balanceSheet1(Request $request)
    {
        return view('accounts.financial-statement.balance-sheet');
    }

    public function balanceSheet(Request $request)
    {
        return view('accounts.financial-statement.balance-sheet', $this->reportViewData());
    }

    // public function balanceSheet_vue_list(Request $request)
    // {
    //     $endDate = $request->input('end_date', now()->toDateString());
    //     $startDate = $request->input('start_date', null); // Optional: for retained earnings calculation

    //     // Calculate account balances as of the end date from transactions
    //     // Use a more efficient query by joining transactions directly

    //     $inventoryAccountId = getAccount('Inventory');

    //     $assets = Account::where('type', 'asset')
    //         ->leftJoin('journals', 'accounts.account_id', '=', 'journals.account_id')
    //         ->leftJoin('transactions', function ($join) use ($endDate) {
    //             $join->on('journals.id', '=', 'transactions.journal_id')
    //                 ->whereRaw(reportingTransactionSqlConstraint())
    //                 ->where('transactions.date', '<=', $endDate);
    //         })
    //         ->selectRaw('
    //     accounts.account_id,
    //     accounts.name,
    //     accounts.balance as account_balance,
    //     COALESCE(SUM(transactions.debit - transactions.credit),0) as transaction_balance
    //  ')
    //         ->groupBy(
    //             'accounts.account_id',
    //             'accounts.name',
    //             'accounts.balance'
    //         )
    //         ->get()
    //         ->map(function ($account) use ($inventoryAccountId) {

    //             $balance = ($account->account_id == $inventoryAccountId)
    //                 ? (float) $account->account_balance
    //                 : (float) $account->transaction_balance;

    //             return [
    //                 'account_id' => $account->account_id,
    //                 'name' => $account->name,
    //                 'balance' => $balance,
    //             ];
    //         })
    //         ->filter(function ($account) {
    //             return abs($account['balance']) > 0.01;
    //         })
    //         ->values();

    //     $liabilities = Account::where('type', 'liability')
    //         ->leftJoin('journals', 'accounts.account_id', '=', 'journals.account_id')
    //         ->leftJoin('transactions', function ($join) use ($endDate) {
    //             $join->on('journals.id', '=', 'transactions.journal_id')
    //                 ->whereRaw(reportingTransactionSqlConstraint())
    //                 ->where('transactions.date', '<=', $endDate);
    //         })
    //         ->selectRaw('accounts.account_id, accounts.name,
    //                     COALESCE(SUM(transactions.credit - transactions.debit), 0) as balance')
    //         ->groupBy('accounts.account_id', 'accounts.name')
    //         ->havingRaw('ABS(COALESCE(SUM(transactions.credit - transactions.debit), 0)) > 0.01')
    //         ->get()
    //         ->map(function ($account) {
    //             return [
    //                 'account_id' => $account->account_id,
    //                 'name' => $account->name,
    //                 'balance' => (float) $account->balance,
    //             ];
    //         })
    //         ->values();

    //     $equity = Account::where('type', 'equity')
    //         ->leftJoin('journals', 'accounts.account_id', '=', 'journals.account_id')
    //         ->leftJoin('transactions', function ($join) use ($endDate) {
    //             $join->on('journals.id', '=', 'transactions.journal_id')
    //                 ->whereRaw(reportingTransactionSqlConstraint())
    //                 ->where('transactions.date', '<=', $endDate);
    //         })
    //         ->selectRaw('accounts.account_id, accounts.name,
    //                     COALESCE(SUM(transactions.credit - transactions.debit), 0) as balance')
    //         ->groupBy('accounts.account_id', 'accounts.name')
    //         ->havingRaw('ABS(COALESCE(SUM(transactions.credit - transactions.debit), 0)) > 0.01')
    //         ->get()
    //         ->map(function ($account) {
    //             return [
    //                 'account_id' => $account->account_id,
    //                 'name' => $account->name,
    //                 'balance' => (float) $account->balance,
    //             ];
    //         })
    //         ->values();

    //     // Calculate Retained Earnings (Net Income) from P&L
    //     // Retained Earnings = Revenue - Expenses (cumulative up to end date)
    //     $revenueAccounts = Account::where('type', 'revenue')->pluck('account_id');
    //     // $expenseAccounts = Account::where('type', 'expense')->pluck('account_id');

    //     //
    //     $purchaseAccountId = getAccount('Purchase Account'); // or 'Purchase Account' based on your chart of accounts

    //     $expenseAccounts = Account::where('type', 'expense')
    //         ->where('account_id', '!=', $purchaseAccountId)
    //         ->pluck('account_id');
    //     //

    //     // Calculate total revenue up to end date
    //     $totalRevenue = Transaction::forProfitLoss()->whereIn('journal_id', function ($query) use ($revenueAccounts) {
    //         $query->select('id')->from('journals')->whereIn('account_id', $revenueAccounts);
    //     })
    //         ->where('date', '<=', $endDate)
    //         ->selectRaw('SUM(credit) - SUM(debit) as net_amount')
    //         ->value('net_amount') ?? 0;

    //     // Calculate total expenses up to end date
    //     $totalExpenses = Transaction::forProfitLoss()->whereIn('journal_id', function ($query) use ($expenseAccounts) {
    //         $query->select('id')->from('journals')->whereIn('account_id', $expenseAccounts);
    //     })
    //         ->where('date', '<=', $endDate)
    //         ->selectRaw('SUM(debit) - SUM(credit) as net_amount')
    //         ->value('net_amount') ?? 0;

    //     $retainedEarnings = $totalRevenue - $totalExpenses;

    //     // Check if Retained Earnings account exists in equity and update/add it
    //     $retainedEarningsAccount = $equity->firstWhere('name', 'Retained Earnings');
    //     if ($retainedEarningsAccount) {
    //         // Update existing Retained Earnings account balance
    //         $retainedEarningsAccount['balance'] = $retainedEarnings;
    //     } elseif (abs($retainedEarnings) > 0.01) {
    //         // Add retained earnings to equity if non-zero and account doesn't exist
    //         $equity->push([
    //             'account_id' => null,
    //             'name' => 'Retained Earnings',
    //             'balance' => $retainedEarnings,
    //         ]);
    //     }

    //     // Calculate totals
    //     $totalAssets = $assets->sum('balance');
    //     $totalLiabilities = $liabilities->sum('balance');
    //     $totalEquity = $equity->sum('balance');
    //     $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;

    //     return response()->json([
    //         'assets' => $assets,
    //         'liabilities' => $liabilities,
    //         'equity' => $equity,
    //         'total_assets' => round($totalAssets, 2),
    //         'total_liabilities' => round($totalLiabilities, 2),
    //         'total_equity' => round($totalEquity, 2),
    //         'total_liabilities_and_equity' => round($totalLiabilitiesAndEquity, 2),
    //         'retained_earnings' => round($retainedEarnings, 2),
    //         'balance_check' => abs($totalAssets - $totalLiabilitiesAndEquity) < 0.01,
    //         'balance_difference' => round($totalAssets - $totalLiabilitiesAndEquity, 2),
    //     ]);
    // }

    public function balanceSheet_vue_list(Request $request)
    {
        return response()->json($this->buildBalanceSheetReport($request));
    }

    public function balanceSheetExportToPDF(Request $request)
    {
        $report = $this->buildBalanceSheetReport($request);
        $fileName = 'balance_sheet_'.date('Y_m_d_H_i_s').'.pdf';

        $pdf = Pdf::loadView('accounts.financial-statement.balance-sheet-pdf-view', array_merge($report, [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]));

        return $pdf->download($fileName);
    }

    public function balanceSheetExportToExcel(Request $request)
    {
        $report = $this->buildBalanceSheetReport($request);
        $fileName = 'balance_sheet_'.date('Y_m_d_H_i_s').'.xlsx';

        return Excel::download(new BalanceSheetExport(
            $report,
            $request->input('start_date'),
            $request->input('end_date'),
        ), $fileName);
    }

    public function balanceSheetPrint(Request $request)
    {
        $report = $this->buildBalanceSheetReport($request);

        return view('accounts.financial-statement.balance-sheet-print', array_merge($report, [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]));
    }

    private function buildBalanceSheetReport(Request $request): array
    {
        $endDate = $request->input('end_date', now()->toDateString());
        $startDate = $request->input('start_date', null);

        // Calculate account balances as of the end date from transactions
        // Use a more efficient query by joining transactions directly

        //     $inventoryAccountId = getAccount('Inventory');

        //     $assets = Account::where('type', 'asset')
        //         ->leftJoin('journals', 'accounts.account_id', '=', 'journals.account_id')
        //         ->leftJoin('transactions', function ($join) use ($endDate) {
        //             $join->on('journals.id', '=', 'transactions.journal_id')
        //                 ->whereRaw(reportingTransactionSqlConstraint())
        //                 ->where('transactions.date', '<=', $endDate);
        //         })
        //         ->selectRaw('
        //     accounts.account_id,
        //     accounts.name,
        //     accounts.balance as account_balance,
        //     COALESCE(SUM(transactions.debit - transactions.credit),0) as transaction_balance
        //  ')
        //         ->groupBy(
        //             'accounts.account_id',
        //             'accounts.name',
        //             'accounts.balance'
        //         )
        //         ->get()
        //         ->map(function ($account) use ($inventoryAccountId) {

        //             $balance = ($account->account_id == $inventoryAccountId)
        //                 ? (float) $account->account_balance
        //                 : (float) $account->transaction_balance;

        //             return [
        //                 'account_id' => $account->account_id,
        //                 'name' => $account->name,
        //                 'balance' => $balance,
        //             ];
        //         })
        //         ->filter(function ($account) {
        //             return abs($account['balance']) > 0.01;
        //         })
        //         ->values();

        $assets = Account::where('type', 'asset')
            // Periodic inventory: stock asset is Stock in Hand (from stock_items), not GL Inventory / OSA
            ->whereNotIn('name', ['Opening Stock Adjustment', 'Inventory'])
            ->leftJoin('journals', 'accounts.account_id', '=', 'journals.account_id')
            ->leftJoin('transactions', function ($join) use ($endDate) {
                $join->on('journals.id', '=', 'transactions.journal_id')
                    ->whereRaw(reportingTransactionSqlConstraint())
                    ->where('transactions.date', '<=', $endDate);
            })
            ->selectRaw('
        accounts.account_id,
        accounts.name,
        COALESCE(SUM(transactions.debit - transactions.credit),0) as transaction_balance
    ')
            ->groupBy('accounts.account_id', 'accounts.name')
            ->get()
            ->map(function ($account) {
                return [
                    'account_id' => $account->account_id,
                    'name' => $account->name,
                    'balance' => (float) $account->transaction_balance,
                ];
            })
            ->filter(function ($account) {
                return abs($account['balance']) > 0.01;
            })
            ->values();

        $stockInHand = $this->financialYearService->resolveClosingStockForProfitLoss($endDate);

        $assets->push([
            'account_id' => null,
            'name' => 'Stock in Hand',
            'balance' => (float) $stockInHand,
        ]);

        $liabilities = Account::where('type', 'liability')
            ->leftJoin('journals', 'accounts.account_id', '=', 'journals.account_id')
            ->leftJoin('transactions', function ($join) use ($endDate) {
                $join->on('journals.id', '=', 'transactions.journal_id')
                    ->whereRaw(reportingTransactionSqlConstraint())
                    ->where('transactions.date', '<=', $endDate);
            })
            ->selectRaw('accounts.account_id, accounts.name,
                        COALESCE(SUM(transactions.credit - transactions.debit), 0) as balance')
            ->groupBy('accounts.account_id', 'accounts.name')
            ->havingRaw('ABS(COALESCE(SUM(transactions.credit - transactions.debit), 0)) > 0.01')
            ->get()
            ->map(function ($account) {
                return [
                    'account_id' => $account->account_id,
                    'name' => $account->name,
                    'balance' => (float) $account->balance,
                ];
            })
            ->values();

        $equity = Account::where('type', 'equity')
            ->leftJoin('journals', 'accounts.account_id', '=', 'journals.account_id')
            ->leftJoin('transactions', function ($join) use ($endDate) {
                $join->on('journals.id', '=', 'transactions.journal_id')
                    ->whereRaw(reportingTransactionSqlConstraint())
                    ->where('transactions.date', '<=', $endDate);
            })
            ->selectRaw('accounts.account_id, accounts.name,
                        COALESCE(SUM(transactions.credit - transactions.debit), 0) as balance')
            ->groupBy('accounts.account_id', 'accounts.name')
            ->havingRaw('ABS(COALESCE(SUM(transactions.credit - transactions.debit), 0)) > 0.01')
            ->get()
            ->map(function ($account) {
                return [
                    'account_id' => $account->account_id,
                    'name' => $account->name,
                    'balance' => (float) $account->balance,
                ];
            })
            ->values();

        // Retained Earnings = ledger balance + current open-year P&L not yet closed.
        // Closed years already transfer profit via the year-end close voucher.
        $ledgerRetainedEarnings = (float) (optional($equity->firstWhere('name', 'Retained Earnings'))['balance'] ?? 0);
        $financialYearForEndDate = $this->financialYearService->findForDate($endDate);

        if ($financialYearForEndDate && ! $financialYearForEndDate->isClosed()) {
            $retainedEarnings = round(
                $ledgerRetainedEarnings + $this->calculatePeriodNetProfit(
                    $financialYearForEndDate->start_date->toDateString(),
                    $endDate
                ),
                2
            );
        } else {
            $retainedEarnings = round($ledgerRetainedEarnings, 2);
        }

        $equity = $equity->map(function ($account) use ($retainedEarnings) {
            if ($account['name'] === 'Retained Earnings') {
                $account['balance'] = $retainedEarnings;
            }

            return $account;
        });

        if (! $equity->contains(fn ($account) => $account['name'] === 'Retained Earnings') && abs($retainedEarnings) > 0.01) {
            $equity->push([
                'account_id' => null,
                'name' => 'Retained Earnings',
                'balance' => $retainedEarnings,
            ]);
        }
        // Calculate totals
        $totalAssets = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity = $equity->sum('balance');
        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'total_assets' => round($totalAssets, 2),
            'total_liabilities' => round($totalLiabilities, 2),
            'total_equity' => round($totalEquity, 2),
            'total_liabilities_and_equity' => round($totalLiabilitiesAndEquity, 2),
            'retained_earnings' => round($retainedEarnings, 2),
            'balance_check' => abs($totalAssets - $totalLiabilitiesAndEquity) < 0.01,
            'balance_difference' => round($totalAssets - $totalLiabilitiesAndEquity, 2),
        ];
    }

    public function dayBook(Request $request)
    {
        $accounts = Account::query()
            ->where('is_active', 1)
            ->orderBy('code')
            ->orderBy('name')
            ->get(['account_id', 'code', 'name', 'type']);

        return view('accounts.financial-statement.day-book', array_merge(
            compact('accounts'),
            $this->reportViewData()
        ));
    }

    public function day_book_vue_list(Request $request)
    {
        $report = $this->buildDayBookReport($request);

        return response()->json([
            'pagination' => $report['pagination'],
            'data' => $report['page_rows'],
            'total_debit' => number_format($report['total_debit'], 2, '.', ','),
            'total_credit' => number_format($report['total_credit'], 2, '.', ','),
        ]);
    }

    public function dayBookExportToPDF(Request $request)
    {
        $report = $this->buildDayBookReport($request, paginate: false);
        $fileName = 'day_book_'.date('Y_m_d_H_i_s').'.pdf';

        $pdf = Pdf::loadView('accounts.financial-statement.day-book-pdf-view', [
            'records' => $report['rows'],
            'total_debit' => $report['total_debit'],
            'total_credit' => $report['total_credit'],
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'search' => $request->input('search'),
            'account_name' => $report['account_name'],
        ]);

        return $pdf->download($fileName);
    }

    public function dayBookExportToExcel(Request $request)
    {
        $report = $this->buildDayBookReport($request, paginate: false);
        $fileName = 'day_book_'.date('Y_m_d_H_i_s').'.xlsx';

        return Excel::download(new DayBookExport(
            $report['rows'],
            $report['total_debit'],
            $report['total_credit'],
            $request->input('start_date'),
            $request->input('end_date'),
            $request->input('search'),
            $report['account_name'],
        ), $fileName);
    }

    public function dayBookPrint(Request $request)
    {
        $report = $this->buildDayBookReport($request, paginate: false);

        return view('accounts.financial-statement.day-book-print', [
            'records' => $report['rows'],
            'total_debit' => $report['total_debit'],
            'total_credit' => $report['total_credit'],
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'search' => $request->input('search'),
            'account_name' => $report['account_name'],
        ]);
    }

    /**
     * @return array{
     *     rows: array<int, array<string, mixed>>,
     *     page_rows: array<int, mixed>,
     *     total_debit: float,
     *     total_credit: float,
     *     account_name: string|null,
     *     pagination: array<string, int>|null
     * }
     */
    private function buildDayBookReport(Request $request, bool $paginate = true): array
    {
        $query = $this->dayBookTransactionsQuery($request);
        $totalDebit = (float) (clone $query)->sum('debit');
        $totalCredit = (float) (clone $query)->sum('credit');
        $pageLength = (int) ($request->page_length ?? config('settings.pagination.perpage.admin', 10));
        $currentPage = max(1, (int) $request->input('page', 1));

        $accountName = null;
        if (! empty($request->chart_of_account_id)) {
            $accountName = Account::query()
                ->where('account_id', $request->chart_of_account_id)
                ->value('name');
        }

        if ($paginate) {
            $result = (clone $query)->paginate($pageLength, ['*'], 'page', $currentPage);
            $pageRows = $result->items();
            $pagination = [
                'total' => $result->total(),
                'per_page' => $result->perPage(),
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
                'from' => $result->firstItem() ?? 0,
                'to' => $result->lastItem() ?? 0,
            ];
        } else {
            $pageRows = [];
            $pagination = null;
        }

        $exportRows = (clone $query)->get()
            ->map(fn ($item) => $this->mapDayBookRow($item))
            ->all();

        return [
            'rows' => $exportRows,
            'page_rows' => $pageRows,
            'total_debit' => round($totalDebit, 2),
            'total_credit' => round($totalCredit, 2),
            'account_name' => $accountName,
            'pagination' => $pagination,
        ];
    }

    private function dayBookTransactionsQuery(Request $request)
    {
        $query = Transaction::forReporting()->with('transactionGroup', 'journal.account')
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        if (! empty($request->search)) {
            $vs = $request->search;
            $query->where(function ($q) use ($vs) {
                $q->whereHas('transactionGroup', function ($groupQuery) use ($vs) {
                    $groupQuery->where('voucher_no', 'LIKE', "%{$vs}%")
                        ->orWhere('description', 'LIKE', "%{$vs}%");
                })->orWhereHas('journal.account', function ($accountQuery) use ($vs) {
                    $accountQuery->where('name', 'LIKE', "%{$vs}%")
                        ->orWhere('code', 'LIKE', "%{$vs}%");
                });
            });
        }

        if (! empty($request->start_date) && ! empty($request->end_date)) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        if (! empty($request->chart_of_account_id)) {
            $query->whereHas('journal', function ($q) use ($request) {
                $q->where('account_id', $request->chart_of_account_id);
            });
        }

        $groupsForFilter = TransactionGroup::query()
            ->whereIn('id', (clone $query)->select('transaction_group_id'))
            ->get(['id', 'description']);

        $excludedGroupIds = ReversalPairFilter::excludedGroupIds($groupsForFilter);
        if ($excludedGroupIds->isNotEmpty()) {
            $query->whereNotIn('transaction_group_id', $excludedGroupIds->all());
        }

        return $query;
    }

    private function mapDayBookRow(Transaction $transaction): array
    {
        return [
            'date' => $transaction->date,
            'voucher_no' => $transaction->transactionGroup->voucher_no ?? '',
            'account_name' => $transaction->journal->account->name ?? '',
            'debit' => (float) $transaction->debit,
            'credit' => (float) $transaction->credit,
            'particular' => $transaction->transactionGroup->description ?? '',
        ];
    }

    public function profitlossAccountTransactions(Request $request)
    {
        return response()->json($this->buildProfitLossAccountTransactions($request));
    }

    public function profitLossAccountTransactionsExportToPDF(Request $request)
    {
        $report = $this->buildProfitLossAccountTransactions($request);
        $title = $request->input('title', $request->input('account_name', 'Account Transactions'));
        $fileName = 'profit_loss_transactions_'.date('Y_m_d_H_i_s').'.pdf';

        $pdf = Pdf::loadView('accounts.financial-statement.profit-loss-account-transactions-pdf-view', array_merge($report, [
            'title' => $title,
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]));

        return $pdf->download($fileName);
    }

    public function profitLossAccountTransactionsExportToExcel(Request $request)
    {
        $report = $this->buildProfitLossAccountTransactions($request);
        $title = $request->input('title', $request->input('account_name', 'Account Transactions'));
        $fileName = 'profit_loss_transactions_'.date('Y_m_d_H_i_s').'.xlsx';

        return Excel::download(new ProfitLossAccountTransactionsExport(
            collect($report['transactions'])->values()->all(),
            $report['total_debit'],
            $report['total_credit'],
            $report['net_amount'],
            $title,
            $request->input('start_date'),
            $request->input('end_date'),
            $report['subtitle'] ?? null,
        ), $fileName);
    }

    public function profitLossAccountTransactionsPrint(Request $request)
    {
        $report = $this->buildProfitLossAccountTransactions($request);
        $title = $request->input('title', $request->input('account_name', 'Account Transactions'));

        return view('accounts.financial-statement.profit-loss-account-transactions-print', array_merge($report, [
            'title' => $title,
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]));
    }

    private function buildProfitLossAccountTransactions(Request $request): array
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $accountName = $request->account_name;

        $query = Transaction::forProfitLoss()
            ->join('journals', 'transactions.journal_id', '=', 'journals.id')
            ->join('accounts', 'journals.account_id', '=', 'accounts.account_id')
            ->join('transaction_groups', 'transactions.transaction_group_id', '=', 'transaction_groups.id')
            ->whereBetween('transactions.date', [$startDate, $endDate]);

        if ($request->type == 'category') {
            $query->where('accounts.category', $accountName);

            // Purchase Account is charted under "Direct Expense" but is shown
            // separately as "Purchase Accounts" on the trading account.
            if (strcasecmp((string) $accountName, 'Direct Expense') === 0) {
                $purchaseAccountId = getAccount('Purchase Account');
                if ($purchaseAccountId) {
                    $query->where('accounts.account_id', '!=', $purchaseAccountId);
                }
            }

            // Salaries & Wages / Commission / Incentive are shown as separate P&L lines.
            if (strcasecmp((string) $accountName, 'Indirect Expense') === 0) {
                $excludeIds = collect([
                    getAccount('Salaries & Wages'),
                    getAccount('Commission Expense'),
                    getAccount('Incentive Expense'),
                ])->filter()->map(fn ($id) => (int) $id)->unique()->values()->all();

                if (! empty($excludeIds)) {
                    $query->whereNotIn('accounts.account_id', $excludeIds);
                }
            }
        } else {
            $query->where('accounts.name', $accountName);
        }

        $transactions = $query->select(
            'transactions.date',
            'transactions.debit',
            'transactions.credit',
            'accounts.name as account_name',
            'transaction_groups.id as transaction_group_id',
            'transaction_groups.description',
            'transaction_groups.voucher_no'
        )
            ->orderBy('transactions.date')
            ->orderBy('transaction_groups.id')
            ->get();

        $excludedGroupIds = ReversalPairFilter::excludedGroupIds(
            $transactions->map(function ($row) {
                return (object) [
                    'id' => (int) $row->transaction_group_id,
                    'description' => $row->description,
                ];
            })
        );

        $transactions = $transactions
            ->reject(function ($row) use ($excludedGroupIds) {
                return $excludedGroupIds->contains((int) $row->transaction_group_id);
            })
            ->map(function ($row) {
                $groupId = (int) $row->transaction_group_id;

                return [
                    'date' => $row->date,
                    'debit' => (float) $row->debit,
                    'credit' => (float) $row->credit,
                    'account_name' => $row->account_name,
                    'description' => $row->description,
                    'voucher_no' => $row->voucher_no,
                    'transaction_group_id' => $groupId,
                    'voucher_url' => $groupId > 0
                        ? route('tab.stock.stock.voucher.details', [
                            'id' => $groupId,
                            'tab_name' => 'info',
                        ])
                        : null,
                ];
            })
            ->values();

        $totalDebit = round($transactions->sum('debit'), 2);
        $totalCredit = round($transactions->sum('credit'), 2);
        $netAmount = round($totalCredit - $totalDebit, 2);

        // Clarify sales/service drill-down: ledger shows full selling price; profit is after COGS
        $accountKey = strtolower((string) $accountName);
        $isSalesOrService = $request->type !== 'category'
            && (
                str_contains($accountKey, 'sales revenue')
                || str_contains($accountKey, 'service revenue')
                || $accountKey === 'service'
            );

        $note = null;
        $subtitle = null;
        if ($isSalesOrService) {
            $subtitle = __('Gross sales — full selling price');
            $note = __('This list shows the complete sales amount credited to the ledger. Gross Profit on the P&L is lower because Cost of Goods Sold (and other trading deductions) are subtracted separately. Tax and discounts are posted to other accounts, not to Sales Revenue.');
        }

        return [
            'transactions' => $transactions,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'net_amount' => $netAmount,
            'subtitle' => $subtitle,
            'note' => $note,
        ];
    }

    /**
     * Opening stock for a P&L period.
     * Includes the original posting and the reversal created when opening stock is edited,
     * so the line shows the corrected value rather than original + replacement.
     */
    private function resolveProfitLossOpeningStock(string $startDate, string $endDate): float
    {
        $inventoryId = getAccount('Opening Stock Adjustment') ?: getAccount('Inventory');
        $openingStock = 0.0;

        if ($inventoryId) {
            $resolvedOpeningStock = $this->financialYearService->resolveOpeningStockForProfitLoss($startDate);

            if ($resolvedOpeningStock !== null) {
                $openingStockPrior = $resolvedOpeningStock;
            } else {
                $openingStockPrior = (float) Transaction::forReporting()->whereHas('journal', function ($q) use ($inventoryId) {
                    $q->where('account_id', $inventoryId);
                })
                    ->whereDate('date', '<', $startDate)
                    ->selectRaw('COALESCE(SUM(debit - credit), 0) as amount')
                    ->value('amount');
            }

            $openingStockInPeriod = (float) Transaction::forProfitLoss()
                ->whereHas('journal', function ($q) use ($inventoryId) {
                    $q->where('account_id', $inventoryId);
                })
                ->whereHas('transactionGroup', function ($q) {
                    $q->where('description', 'like', 'Opening stock%')
                        ->orWhere('description', 'like', 'Reversal for Opening Stock%');
                })
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('COALESCE(SUM(debit - credit), 0) as amount')
                ->value('amount');

            $openingStock = $openingStockPrior + $openingStockInPeriod;
        }

        if (abs($openingStock) < 0.01) {
            $openingStock = (float) (DB::table('stock_movements as sm')
                ->join('stock_items as si', 'si.item_id', '=', 'sm.item_id')
                ->whereNull('sm.type')
                ->where('sm.description', 'like', 'Opening stock%')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereDate('sm.created_at', '<', $startDate)
                        ->orWhereBetween(DB::raw('DATE(sm.created_at)'), [$startDate, $endDate]);
                })
                ->selectRaw('SUM('.StockItem::inventoryValueSql('sm.stock_in', 'si.cost_price', 'si.pack_size').') as amount')
                ->value('amount') ?? 0);
        }

        return round((float) $openingStock, 2);
    }

    private function calculatePeriodNetProfit(string $startDate, string $endDate): float
    {
        return $this->calculateNetProfitForRange($startDate, $endDate);
    }

    private function calculateLifetimeNetProfit(string $endDate): float
    {
        return $this->calculateNetProfitForRange(null, $endDate);
    }

    private function calculateNetProfitForRange(?string $startDate, string $endDate): float
    {
        $revenueAccounts = Account::where('type', 'revenue')->pluck('account_id');
        $purchaseAccountId = getAccount('Purchase Account');
        $expenseAccounts = Account::where('type', 'expense')
            ->when($purchaseAccountId, fn ($query) => $query->where('account_id', '!=', $purchaseAccountId))
            ->pluck('account_id');

        $revenueQuery = Transaction::forProfitLoss()
            ->whereIn('journal_id', function ($query) use ($revenueAccounts) {
                $query->select('id')->from('journals')->whereIn('account_id', $revenueAccounts);
            });

        $expenseQuery = Transaction::forProfitLoss()
            ->whereIn('journal_id', function ($query) use ($expenseAccounts) {
                $query->select('id')->from('journals')->whereIn('account_id', $expenseAccounts);
            });

        if ($startDate) {
            $revenueQuery->whereBetween('date', [$startDate, $endDate]);
            $expenseQuery->whereBetween('date', [$startDate, $endDate]);
        } else {
            $revenueQuery->where('date', '<=', $endDate);
            $expenseQuery->where('date', '<=', $endDate);
        }

        $totalRevenue = (float) ($revenueQuery
            ->selectRaw('COALESCE(SUM(credit) - SUM(debit), 0) as net_amount')
            ->value('net_amount') ?? 0);

        $totalExpenses = (float) ($expenseQuery
            ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as net_amount')
            ->value('net_amount') ?? 0);

        $openingStockAdjId = getAccount('Opening Stock Adjustment');
        $openingStock = 0.0;

        if ($openingStockAdjId) {
            if ($startDate) {
                $openingStock = $this->resolveProfitLossOpeningStock($startDate, $endDate);
            } else {
                $openingStock = (float) Transaction::forProfitLoss()
                    ->whereHas('journal', function ($query) use ($openingStockAdjId) {
                        $query->where('account_id', $openingStockAdjId);
                    })
                    ->where('date', '<=', $endDate)
                    ->selectRaw('COALESCE(SUM(debit - credit), 0) as amount')
                    ->value('amount');
            }
        }

        $purchases = 0.0;
        if ($purchaseAccountId) {
            $purchaseQuery = Transaction::forProfitLoss()
                ->whereHas('journal', function ($query) use ($purchaseAccountId) {
                    $query->where('account_id', $purchaseAccountId);
                });

            if ($startDate) {
                $purchaseQuery->whereBetween('date', [$startDate, $endDate]);
            } else {
                $purchaseQuery->where('date', '<=', $endDate);
            }

            $purchases = (float) $purchaseQuery
                ->selectRaw('COALESCE(SUM(debit - credit), 0) as amount')
                ->value('amount');
        }

        $legacyInventoryId = getAccount('Inventory');
        if ($legacyInventoryId) {
            $legacyQuery = Transaction::forProfitLoss()
                ->whereHas('journal', function ($query) use ($legacyInventoryId) {
                    $query->where('account_id', $legacyInventoryId);
                });

            if ($startDate) {
                $legacyQuery->whereBetween('date', [$startDate, $endDate]);
            } else {
                $legacyQuery->where('date', '<=', $endDate);
            }

            $purchases += (float) $legacyQuery
                ->selectRaw('COALESCE(SUM(debit - credit), 0) as amount')
                ->value('amount');
        }

        $closingStock = $this->financialYearService->resolveClosingStockForProfitLoss($endDate);

        $cogs = $openingStock + $purchases - $closingStock;

        return round($totalRevenue - $cogs - $totalExpenses, 2);
    }
}
