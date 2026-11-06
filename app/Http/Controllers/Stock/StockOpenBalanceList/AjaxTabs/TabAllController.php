<?php

namespace App\Http\Controllers\Stock\StockOpenBalanceList\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Repositories\StockOpenBalanceRepository;
use Illuminate\Http\Request;

class TabAllController extends Controller
{
    public function index(Request $request, StockOpenBalanceRepository $stock_open_balance_repo, $tab_name)
    {
        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        // Default to current month when dates are not provided
        if (! $request->filled('start_date') && ! $request->filled('end_date')) {
            $request->merge([
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->toDateString(),
            ]);
        }

        $db_records = $stock_open_balance_repo->hunter()
            ->setSearchTerm($request->search)
            ->setStartDate($request->start_date)
            ->setEndDate($request->end_date)
            ->search();

        $db_records->appends($request->only([
            'search',
            'start_date',
            'end_date',
            'tab_search',
        ]));

        $canEdit = auth()->user()->can('opening_balance_edit');

        foreach ($db_records as $row) {
            $row->can_edit = $canEdit;
            $row->view_url = route('tab.stock.open.balance.details', [
                'open_balance_id' => $row->open_balance_id,
                'tab_name' => 'info',
            ]);
            $row->edit_url = route('tab.stock.open.balance.details', [
                'open_balance_id' => $row->open_balance_id,
                'tab_name' => 'edit',
            ]);
            $row->account_name = $row->account->name ?? '—';
            $row->amount_display = number_format((float) ($row->amount ?? 0), 2, '.', ',');
            $row->opening_date_display = $row->opening_date
                ? \Carbon\Carbon::parse($row->opening_date)->format('d M Y')
                : '—';
        }

        $db_results = [
            'vue_data' => [
                'vue_list_tab_all' => [
                    'paginator' => paginator_vue_data($db_records),
                ],
            ],
            'response_values' => [
                '#tab_search' => '0',
            ],
        ];

        if ($request->tab_search || $request->pgajax === 'y') {
            return response()->json($db_results);
        }

        return view('stock.stock-open-balance-list.ajax-tabs.tab-all', compact('db_results', 'tab_name'));
    }
}
