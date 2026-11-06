<?php

namespace App\Http\Controllers\Acl\UserDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Models\AclRole;
use Auth;

class TabRolesController extends Controller
{
    public function index(Request $request, UserRepository $user_repo, $id, $tab_name)
    {
        // Fetch all available roles
        $db_all_roles = AclRole::orderBy('system_defined', 'desc')
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'system_defined'])
            ->values()
            ->toArray();

        // All role names associated to this user.
        $assigned_roles = \App\Models\User::findOrFail($id)
            ->getRoleNames()
            ->values()
            ->all();

        $details_roles_vue = [
            'id' => (int) $id,
            'tab_name' => $tab_name,
            'db_all_roles' => $db_all_roles,
            'assigned_roles' => array_values($assigned_roles),
            'button_cancel_url' => route('tab.acl.user.details', ['id' => $id, 'tab_name' => 'info']),
            'form_save_url' => route('tab.acl.user.save', ['id' => $id, 'tab_name' => $tab_name]),
        ];

        return view('acl.user-details.ajax-tabs.tab-roles', compact('details_roles_vue'));
    }

    public function save(Request $request, UserRepository $user_repo, $id, $tab_name)
    {

        if ($request->id != $id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Should use "\App\User" (NOT \App\Models\User)
        $user = \App\Models\User::findOrFail($id);
        // return $request;
        // Sync roles for this user
        $user->syncRoles($request->roles);

        return response()->json([
            'gotourl' => route('tab.acl.user.details', [
                'id' => $id,
                'tab_name' => $tab_name,
            ])
        ]);
    }
}
