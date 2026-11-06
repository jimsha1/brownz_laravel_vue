<?php

namespace App\Http\Controllers\Accounts\AccountList\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AccountRepository;

class TabAllController extends Controller
{
    public function index(Request $request, AccountRepository $account_repo, $tab_name)
    {
        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        $db_records = $account_repo->hunter()
            ->query(function ($query) use ($request) {
                if ($request->filled('type')) {
                    $query->where('type', $request->type);
                }

                if ($request->filled('account_id')) {
                    $query->where('account_id', $request->account_id);
                }

                if ($request->filled('code')) {
                    $query->where('code', 'like', '%' . $request->code . '%');
                }

                if ($request->filled('category')) {
                    $query->where('category', $request->category);
                }

                if ($request->filled('status')) {
                    $query->where('is_active', (int) $request->status);
                }

                if ($request->filled('is_system_defined')) {
                    $query->where('is_system_defined', (int) $request->is_system_defined);
                }
            })
            ->search();

        if (method_exists($db_records, 'appends')) {
            $db_records->appends(array_filter([
                'pgajax' => 'y',
                'type' => $request->type,
                'account_id' => $request->account_id,
                'code' => $request->code,
                'category' => $request->category,
                'status' => $request->status,
                'is_system_defined' => $request->is_system_defined,
            ], function ($value) {
                return $value !== null && $value !== '';
            }));
        }

        // Add additional properties for the records
        foreach ($db_records as $row) {
            $row->is_system_defined = (int) $row->is_system_defined;
            $row->is_active = (int) $row->is_active;
            $row->view_url = route('tab.accounts.account.details', [
                'account_id' => $row->account_id,
                'tab_name' => 'info',
            ]);
            $row->edit_url = route('tab.accounts.account.details', [
                'account_id' => $row->account_id,
                'tab_name' => 'edit',
            ]);
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

        return view('accounts.account-list.ajax-tabs.tab-all', compact('db_results', 'tab_name'));
    }
}
