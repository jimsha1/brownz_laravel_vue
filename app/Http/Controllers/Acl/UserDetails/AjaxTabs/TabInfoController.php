<?php

namespace App\Http\Controllers\Acl\UserDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Auth;

class TabInfoController extends Controller
{
    public function index(Request $request, UserRepository $user_repo, $id, $tab_name)
    {
        $user_info = $user_repo->model()
                ->with('createdUser')
                ->with('updatedUser')
                ->with('roles')
                ->findOrFail($id);

        $details_info_vue = [
            'id' => (int) $id,
            'tab_name' => $tab_name,
            'user_info' => $user_info,
            'role_names' => $user_info->getRoleNames()->values()->all(),
        ];

        return view('acl.user-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
