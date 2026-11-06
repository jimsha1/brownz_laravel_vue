<?php

namespace App\Http\Controllers\Accounts\AccountDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\FinancialYear;
use App\Models\Transaction;
use App\Support\ReversalPairFilter;
use Illuminate\Http\Request;

class TabTransactionsController extends Controller
{
    public function index(Request $request, $account_id)
    {
        $account = Account::findOrFail($account_id);

        $financialYears = FinancialYear::orderByDesc('start_date')->get();

        $selectedFinancialYear = FinancialYear::where('is_current', 1)->value('id');

        // Load initial data
        $response = $this->getTransactions($account, $selectedFinancialYear);

        $details_info_vue = [
            'account' => $account,
            'transactions' => $response['transactions'],
            'running_balance' => $response['running_balance'],
            'financialYears' => $financialYears,
            'selectedFinancialYear' => $selectedFinancialYear,
        ];

        return view(
            'accounts.account-details.ajax-tabs.tab-transactions',
            compact('details_info_vue')
        );
    }

    public function filter(Request $request, $account_id)
    {
        $account = Account::findOrFail($account_id);

        $response = $this->getTransactions(
            $account,
            $request->financial_year_id
        );

        return response()->json($response);
    }

    private function getTransactions($account, $financialYearId)
    {
        $financialYear = FinancialYear::findOrFail($financialYearId);

        $query = Transaction::forReporting()->with([
            'transactionGroup',
            'journal.account',
        ])
            ->whereHas('journal', function ($q) use ($account) {
                $q->where('account_id', $account->account_id);
            })
            ->whereBetween('date', [
                $financialYear->start_date,
                $financialYear->end_date,
            ]);

        $runningBalance = 0;

        // Opening balance is the starting line of the ledger, even when its
        // voucher date is later than other postings in the year.
        $transactions = $query
            ->orderByRaw(
                "CASE WHEN EXISTS (
                    SELECT 1 FROM transaction_groups
                    WHERE transaction_groups.id = transactions.transaction_group_id
                    AND transaction_groups.description = 'Opening Balance'
                ) THEN 0 ELSE 1 END"
            )
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $excludedGroupIds = ReversalPairFilter::excludedGroupIds(
            $transactions->pluck('transactionGroup')->filter()
        );

        $transactions = $transactions
            ->reject(function ($transaction) use ($excludedGroupIds) {
                return $excludedGroupIds->contains($transaction->transaction_group_id);
            })
            ->values();

        foreach ($transactions as $transaction) {

            if (in_array(strtolower($account->type), ['asset', 'expense'])) {

                $runningBalance +=
                    $transaction->debit -
                    $transaction->credit;

            } else {

                $runningBalance +=
                    $transaction->credit -
                    $transaction->debit;
            }

            $transaction->running_balance = $runningBalance;
        }

        // Balances are walked oldest-first (opening balance, then date, then id).
        // The ledger list itself is newest first.
        $transactions = $transactions->reverse()->values();

        return [
            'transactions' => $transactions,
            'running_balance' => $runningBalance,
        ];
    }
}
//     public function index(Request $request, $account_id)
//     {
//         $account = Account::findOrFail($account_id);

//         $query = Transaction::with([
//             'transactionGroup',
//             'journal.account',
//         ])
//             ->whereHas('journal', function ($q) use ($account_id) {
//                 $q->where('account_id', $account_id);
//             });

//         $runningBalance = 0;

//         /*
//         |--------------------------------------------------------------------------
//         | Financial Year Filter
//         |--------------------------------------------------------------------------
//         */

//         if ($request->filled('financial_year_id')) {

//             $financialYear = FinancialYear::findOrFail($request->financial_year_id);

//             // Transactions only within selected financial year
//             $query->whereBetween('date', [
//                 $financialYear->start_date,
//                 $financialYear->end_date,
//             ]);

//             // Opening balance before the financial year
//             if (in_array(strtolower($account->type), ['asset', 'expense'])) {

//                 $runningBalance = Transaction::whereHas('journal', function ($q) use ($account_id) {
//                     $q->where('account_id', $account_id);
//                 })
//                     ->where('date', '<', $financialYear->start_date)
//                     ->selectRaw('COALESCE(SUM(debit - credit),0) as balance')
//                     ->value('balance');

//             } else {

//                 $runningBalance = Transaction::whereHas('journal', function ($q) use ($account_id) {
//                     $q->where('account_id', $account_id);
//                 })
//                     ->where('date', '<', $financialYear->start_date)
//                     ->selectRaw('COALESCE(SUM(credit - debit),0) as balance')
//                     ->value('balance');
//             }
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Transactions
//         |--------------------------------------------------------------------------
//         */

//         $transactions = $query
//             ->orderBy('date')
//             ->orderBy('id')
//             ->get();

//         /*
//         |--------------------------------------------------------------------------
//         | Running Balance
//         |--------------------------------------------------------------------------
//         */

//         $type = strtolower($account->type);

//         foreach ($transactions as $transaction) {

//             if (in_array($type, ['asset', 'expense'])) {

//                 $runningBalance += ($transaction->debit - $transaction->credit);

//             } else {

//                 $runningBalance += ($transaction->credit - $transaction->debit);
//             }

//             $transaction->running_balance = $runningBalance;
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Financial Years
//         |--------------------------------------------------------------------------
//         */

//         $financialYears = FinancialYear::orderByDesc('start_date')->get();

//         $selectedFinancialYear = $request->financial_year_id
//             ?? FinancialYear::where('is_current', 1)->value('id');

//         /*
//         |--------------------------------------------------------------------------
//         | Vue Data
//         |--------------------------------------------------------------------------
//         */
//         if ($request->ajax()) {

//             return response()->json([
//                 'transactions' => $transactions,
//                 'running_balance' => $runningBalance,
//             ]);

//         }

//         $details_info_vue = [
//             'account' => $account,
//             'transactions' => $transactions,
//             'running_balance' => $runningBalance,
//             'financialYears' => $financialYears,
//             'selectedFinancialYear' => $selectedFinancialYear,
//         ];

//         return view(
//             'accounts.account-details.ajax-tabs.tab-transactions',
//             compact('details_info_vue')
//         );
//     }
// }

// namespace App\Http\Controllers\Accounts\AccountDetails\AjaxTabs;

// use App\Http\Controllers\Controller;
// use App\Models\Account;
// use App\Models\FinancialYear;
// use App\Models\Transaction;
// use Illuminate\Http\Request;

// class TabTransactionsController extends Controller
// {
// public function index($account_id)
// {
//     $account = Account::findOrFail($account_id);

//     $transactions = Transaction::with([
//         'transactionGroup',
//         'journal.account',
//     ])
//         ->whereHas('journal', function ($q) use ($account_id) {
//             $q->where('account_id', $account_id);
//         })
//         ->orderBy('date')
//         ->get();

//     $runningBalance = 0;

//     foreach ($transactions as $transaction) {

//         $runningBalance += $transaction->debit;
//         $runningBalance -= $transaction->credit;

//         $transaction->running_balance = $runningBalance;
//     }

//     $details_info_vue = [
//         'account' => $account,
//         'transactions' => $transactions,
//         'running_balance' => $runningBalance,
//     ];

//     return view(
//         'accounts.account-details.ajax-tabs.tab-transactions',
//         compact('details_info_vue')
//     );
// }

//     public function index(Request $request, $account_id)
//     {
//         $account = Account::findOrFail($account_id);

//         $query = Transaction::with([
//             'transactionGroup',
//             'journal.account',
//         ])
//             ->whereHas('journal', function ($q) use ($account_id) {
//                 $q->where('account_id', $account_id);
//             });

//         // Financial Year Filter
//         if ($request->filled('financial_year_id')) {

//             $financialYear = FinancialYear::findOrFail($request->financial_year_id);

//             $query->whereBetween('date', [
//                 $financialYear->start_date,
//                 $financialYear->end_date,
//             ]);
//         }

//         $transactions = $query->orderBy('date')->get();

//         $runningBalance = 0;

//         // foreach ($transactions as $transaction) {

//         //     $runningBalance += $transaction->debit;
//         //     $runningBalance -= $transaction->credit;

//         //     $transaction->running_balance = $runningBalance;
//         // }

//         $runningBalance = Transaction::whereHas('journal', function ($q) use ($account_id) {
//     $q->where('account_id', $account_id);
// })
// ->where('date', '<', $financialYear->start_date)
// ->sum(DB::raw('debit - credit'));

//         foreach ($transactions as $transaction) {

//             $runningBalance += $transaction->debit;
//             $runningBalance -= $transaction->credit;

//             $transaction->running_balance = $runningBalance;
//         }

//         $financialYears = FinancialYear::orderByDesc('start_date')->get();

//         $selectedFinancialYear = $request->financial_year_id
//             ?? FinancialYear::where('is_current', 1)->value('id');

//         $details_info_vue = [
//             'account' => $account,
//             'transactions' => $transactions,
//             'running_balance' => $runningBalance,
//             'financialYears' => $financialYears,
//             'selectedFinancialYear' => $selectedFinancialYear,
//         ];

//         return view(
//             'accounts.account-details.ajax-tabs.tab-transactions',
//             compact('details_info_vue')
//         );
//     }
// }
