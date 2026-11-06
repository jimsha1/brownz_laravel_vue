<?php

namespace App\Http\Controllers\Master\ServiceList\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ServiceRepository;
use Auth;

class TabAllController extends Controller
{
    public function index(Request $request, ServiceRepository $service_repo, $tab_name)
    {
        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        $status = $request->status ?: 'all';
        $showOnWebsite = $request->show_on_website ?: 'all';
        $categoryId = $request->category_id ?: 'all';

        $db_records = $service_repo->hunter()
            ->setSearchTerm($request->search)
            ->setStatus($status === 'all' ? null : $status)
            ->setShowOnWebsite($showOnWebsite === 'all' ? null : $showOnWebsite)
            ->setCategoryId($categoryId === 'all' ? null : $categoryId)
            ->search();

        // Add additional properties for the records
        foreach ($db_records as $row) {
            $row->edit_url = route('tab.master.service.details', ['service_id' => $row->service_id, 'tab_name' => 'edit']);
            $row->view_url = route('tab.master.service.details', ['service_id' => $row->service_id, 'tab_name' => 'info']);
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

        return view('master.service-list.ajax-tabs.tab-all', compact('db_results', 'tab_name'));
    }
}
