<?php

namespace App\Http\Controllers\Expenses\ExpenseList\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Repositories\ExpenseRepository;
use Illuminate\Http\Request;

class TabAllController extends Controller
{
    public function index(Request $request, ExpenseRepository $expense_repo, $tab_name)
    {
        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        // Default to current month when dates are not provided (keeps list aligned with filter defaults)
        if (! $request->filled('start_date') && ! $request->filled('end_date')) {
            $request->merge([
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->toDateString(),
            ]);
        }

        $hunter = $expense_repo->hunter()
            ->setSearchTerm($request->search)
            ->setStartDate($request->start_date)
            ->setEndDate($request->end_date)
            ->query(function ($query) use ($request) {
                // Account Name filter (dropdown: filter by account_id)
                if ($request->filled('account_id')) {
                    $query->where('account_id', $request->account_id);
                }
                // Account Type filter (from expenses table column)
                if ($request->filled('account_type')) {
                    $query->where('account_type', $request->account_type);
                }
            });

        $db_records = $hunter->search();
        // Preserve filter params in pagination links
        $db_records->appends($request->only([
            'search',
            'start_date',
            'end_date',
            'account_id',
            'account_type',
            'tab_search',
        ]));

        // Add additional properties for the records
        foreach ($db_records as $row) {
            $expenseDate = $row->date ?: optional($row->created_at)->toDateString();

            $row->view_url = route('tab.expenses.expense.details', [
                'expense_id' => $row->expense_id,
                'tab_name' => 'info',
            ]);
            $row->edit_url = route('tab.expenses.expense.details', [
                'expense_id' => $row->expense_id,
                'tab_name' => 'edit',
            ]);
            $row->account_name = $row->account->name ?? '—';
            $row->account_type_label = $row->account_type
                ? ucfirst(strtolower((string) $row->account_type))
                : '—';
            $row->amount_display = number_format((float) ($row->amount ?? 0), 2, '.', ',');
            $row->expense_date_display = $expenseDate
                ? \Carbon\Carbon::parse($expenseDate)->format('d M Y')
                : '—';
        }

        $db_results = [
            // Send some data for VueJs
            'vue_data' => [
                'vue_list_tab_all' => [
                    'paginator' => paginator_vue_data($db_records),
                ],
            ],
            'response_values' => [
                '#tab_search' => '0',
            ]
        ];

        if ($request->tab_search || $request->pgajax === 'y') {
            return response()->json($db_results);
        }

        return view('expenses.expense-list.ajax-tabs.tab-all', compact('db_results', 'tab_name'));
    }
}
