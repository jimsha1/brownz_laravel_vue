<?php

namespace App\Http\Controllers\Admin\ClientList\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ClientRepository;

class TabAllController extends Controller
{
    public function index(Request $request, ClientRepository $client_repo, $tab_name)
    {
        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        $status = $request->status ?: 'active';
        $creditBalance = $request->credit_balance ?: '';

        $db_records = $client_repo->hunter()
            ->setSearchTerm($request->search)
            ->setStatus($status === 'all' ? null : $status)
            ->setCreditBalance($creditBalance)
            ->search();

        foreach ($db_records as $row) {
            $row->edit_url = route('tab.admin.client.details', [
                'client_id' => $row->client_id,
                'tab_name' => 'edit',
            ]);
            $row->view_url = route('tab.admin.client.details', [
                'client_id' => $row->client_id,
                'tab_name' => 'info',
            ]);
            $row->payment_url = route('tab.admin.client.details', [
                'client_id' => $row->client_id,
                'tab_name' => 'payment',
            ]);
            $row->status_label = $row->user_status ?: '—';
            $row->has_credit = (float) ($row->balance ?? 0) > 0;
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

        return view('admin.client-list.ajax-tabs.tab-all', compact('db_results', 'tab_name'));
    }
}
