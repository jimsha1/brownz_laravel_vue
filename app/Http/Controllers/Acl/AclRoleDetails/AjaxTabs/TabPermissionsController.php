<?php

namespace App\Http\Controllers\Acl\AclRoleDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\AclPermission;
use App\Repositories\AclRoleRepository;
use Illuminate\Http\Request;

class TabPermissionsController extends Controller
{
    public function index(Request $request, AclRoleRepository $acl_role_repo, $id, $tab_name)
    {
        // Fetch all available permissions as plain arrays for Vue
        $db_all_permissions = AclPermission::orderBy('display_order')
            ->get(['id', 'name', 'description', 'group', 'display_order'])
            ->groupBy('group')
            ->map(function ($items) {
                return $items->values()->toArray();
            })
            ->toArray();

        // All permission names associated to this role
        $assigned_permissions = $acl_role_repo->model()
            ->findOrFail($id)
            ->getPermissionNames()
            ->values()
            ->all();

        $details_permissions_vue = [
            'id' => (int) $id,
            'tab_name' => $tab_name,
            'db_all_permissions' => $db_all_permissions,
            'assigned_permissions' => array_values($assigned_permissions),
            'button_cancel_url' => route('tab.acl.role.details', ['id' => $id, 'tab_name' => 'info']),
            'form_save_url' => route('tab.acl.role.save', ['id' => $id, 'tab_name' => $tab_name]),
        ];

        return view('acl.acl-role-details.ajax-tabs.tab-permissions', compact('details_permissions_vue'));
    }

    public function save(Request $request, AclRoleRepository $acl_role_repo, $id, $tab_name)
    {
        if ($request->id != $id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Fetch role model
        $role = $acl_role_repo->model()->findOrFail($id);

        // Sync permissions for this role
        $role->syncPermissions($request->permissions);

        return response()->json([
            'gotourl' => route('tab.acl.role.details', [
                'id' => $id,
                'tab_name' => $tab_name,
            ])
        ]);
    }
}
