<?php

namespace App\Http\Controllers\Acl\UserDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Models\AclPermission;

class TabPermissionsController extends Controller
{
    public function index(Request $request, UserRepository $user_repo, $id, $tab_name)
    {
        $user_info = $user_repo->model()->findOrFail($id);
        $is_super_admin = $user_info->hasRole(\App\Models\AclRole::ROLE_SUPER_ADMIN);

        if ($is_super_admin) {
            // No need to fetch full permissions.
            // We'll show a message (in blade) saying "Super admin have all permissions"
            $db_all_permissions = [];
            $assigned_permissions = [];
        } else {
            // Fetch all available permissions as plain arrays for Vue
            $db_all_permissions = AclPermission::orderBy('display_order')
                ->get(['id', 'name', 'description', 'group', 'display_order'])
                ->groupBy('group')
                ->map(function ($items) {
                    return $items->values()->toArray();
                })
                ->toArray();

            // Get all permissions for the user (either directly or from roles)
            $assigned_permissions = $user_info->getAllPermissions()
                ->pluck('name')
                ->values()
                ->all();
        }

        $details_permissions_vue = [
            'id' => (int) $id,
            'tab_name' => $tab_name,
            'is_super_admin' => $is_super_admin,
            'db_all_permissions' => $db_all_permissions,
            'assigned_permissions' => array_values($assigned_permissions),
        ];

        return view('acl.user-details.ajax-tabs.tab-permissions', compact('details_permissions_vue'));
    }
}
