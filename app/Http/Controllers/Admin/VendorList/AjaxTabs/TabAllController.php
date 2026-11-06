<?php

namespace App\Http\Controllers\Admin\VendorList\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\VendorRepository;
use Auth;

class TabAllController extends Controller
{
    public function index(Request $request, VendorRepository $vendor_repo, $tab_name)
    {
        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        $db_records = $vendor_repo->hunter()
            ->setSearchTerm($request->search)
            ->search();

        // Add additional properties for the records
        foreach ($db_records as $row) {
            $row->edit_url = route('tab.admin.vendor.details', ['vendor_id' => $row->vendor_id, 'tab_name' => 'edit']);
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

        return view('admin.vendor-list.ajax-tabs.tab-all', compact('db_results', 'tab_name'));
    }
}
