<?php

namespace App\Http\Controllers\Acl\AclRoleDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Repositories\AclRoleRepository;
use Illuminate\Http\Request;

class TabInfoController extends Controller
{
    public function index(Request $request, AclRoleRepository $acl_role_repo, $id, $tab_name)
    {
        $acl_role_info = $acl_role_repo->model()
                ->withCount(['permissions', 'users'])
                ->findOrFail($id);

        $details_info_vue = [
            'id' => $id,
            'tab_name' => $tab_name,
            'acl_role_info' => $acl_role_info,
        ];

        return view('acl.acl-role-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
