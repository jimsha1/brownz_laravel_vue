<?php

namespace App\Http\Controllers\Acl\UserDetails\AjaxTabs;

use App\Models\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;

class TabOutletsController extends Controller
{
    public function index(Request $request, UserRepository $user_repo, $id, $tab_name)
    {
        // Fetch all outlets
        $db_all_outlets = Outlet::all(['outlet_id', 'name'])->values()->toArray();

        // All outlets associated to this user.
        $assigned_outlets = $user_repo->getOutlets($id)
            ->pluck('outlet_id')
            ->values()
            ->all();

        $details_outlets_vue = [
            'id' => (int) $id,
            'tab_name' => $tab_name,
            'db_all_outlets' => $db_all_outlets,
            'assigned_outlets' => array_values($assigned_outlets),
            'button_cancel_url' => route('tab.acl.user.details', ['id' => $id, 'tab_name' => 'info']),
            'form_save_url' => route('tab.acl.user.save', ['id' => $id, 'tab_name' => $tab_name]),
        ];

        return view('acl.user-details.ajax-tabs.tab-outlets', compact('details_outlets_vue'));
    }

    public function save(Request $request, UserRepository $user_repo, $id, $tab_name)
    {
        if ($request->id != $id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Should use "\App\User" (NOT \App\Models\User)
        $user = \App\Models\User::findOrFail($id);

        // Sync outlets for this user
        $user_repo->syncOutlets($user, $request->outlets);

        return response()->json([
            'gotourl' => route('tab.acl.user.details', [
                'id' => $id,
                'tab_name' => $tab_name,
            ])
        ]);
    }
}
