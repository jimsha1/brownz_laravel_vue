<?php

namespace App\Http\Controllers\Acl\AclRoleList\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Repositories\AclRoleRepository;
use Illuminate\Http\Request;

class TabAllController extends Controller
{
    public function index(Request $request, AclRoleRepository $acl_role_repo, $tab_name)
    {
        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        $db_records = $acl_role_repo->hunter()
            ->setSearchTerm($request->search)
            ->query(function ($query) {
                $query->withCount(['permissions', 'users']);
            })
            ->search();

        // Add additional properties for the records
        foreach ($db_records as $row) {
            $row->edit_url = route('tab.acl.role.details', ['id' => $row->id, 'tab_name' => 'edit']);
            $row->view_url = route('tab.acl.role.details', ['id' => $row->id, 'tab_name' => 'info']);
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

        return view('acl.acl-role-list.ajax-tabs.tab-all', compact('db_results', 'tab_name'));
    }
}
