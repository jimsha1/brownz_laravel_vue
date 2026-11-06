<?php

namespace App\Http\Controllers\Master\ShiftList\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ShiftRepository;
use Auth;

class TabAllController extends Controller
{
    public function index(Request $request, ShiftRepository $shift_repo, $tab_name)
    {
        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        $status = $request->status ?: 'all';

        $db_records = $shift_repo->hunter()
            ->setSearchTerm($request->search)
            ->setStatus($status === 'all' ? null : $status)
            ->search();

        // Add additional properties for the records
        foreach ($db_records as $row) {
            $row->edit_url = route('tab.master.shift.details', ['shift_id' => $row->shift_id, 'tab_name' => 'edit']);
            $row->view_url = route('tab.master.shift.details', ['shift_id' => $row->shift_id, 'tab_name' => 'info']);
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

        return view('master.shift-list.ajax-tabs.tab-all', compact('db_results', 'tab_name'));
    }
}
