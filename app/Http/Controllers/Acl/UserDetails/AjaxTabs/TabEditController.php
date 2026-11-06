<?php

namespace App\Http\Controllers\Acl\UserDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TabEditController extends Controller
{
    public function index(Request $request, UserRepository $user_repo, $id, $tab_name)
    {
        $user_info = $user_repo->model()->findOrFail($id);

        $details_edit_vue = [
            'id' => (int) $id,
            'tab_name' => $tab_name,
            'user_info' => $user_info,
            'urls' => [
                'button_cancel_url' => route('tab.acl.user.details', ['id' => $id, 'tab_name' => 'info']),
                'form_save_url' => route('tab.acl.user.save', ['id' => $id, 'tab_name' => $tab_name]),
            ],
        ];

        return view('acl.user-details.ajax-tabs.tab-edit', compact('details_edit_vue'));
    }

    public function save(Request $request, UserRepository $user_repo, $id, $tab_name)
    {
        if ($request->id != $id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Direct user creation is disabled — create via Payroll → Employee
        if ((int) $id <= 0) {
            throw new \Exception(__('Users can only be created from Payroll → Employee Management.'));
        }

        $user = $user_repo->model()->findOrFail($id);

        // Apply validation
        $this->applyValidation($request, $user);

        // Start database transaction
        $response = DB::transaction(function () use ($request, $user) {
            return $this->saveFromRequest($request, $user);
        });

        return response()->json([
            'gotourl' => route('tab.acl.user.details', [
                'id' => $response->id,
                'tab_name' => $tab_name,
            ])
        ]);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function applyValidation(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);
    }

    private function saveFromRequest(Request $request, User $user)
    {
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->gender = $request->gender;
        $user->save();

        voco_flash()->success(__('User details updated!'));

        return $user;
    }
}
