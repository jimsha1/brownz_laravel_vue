<?php

namespace App\Http\Controllers\Acl\UserDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TabPasswordController extends Controller
{
    public function index(Request $request, UserRepository $user_repo, $id, $tab_name)
    {
        $user_info = $this->findById($id, $user_repo);

        $details_edit_vue = [
            'id' => (int) $id,
            'tab_name' => $tab_name,
            'user_info' => $user_info,
            'urls' => [
                'button_cancel_url' => route('tab.acl.user.details', ['id' => $id, 'tab_name' => 'info']),
                'form_save_url' => route('tab.acl.user.save', ['id' => $id, 'tab_name' => $tab_name]),
            ],
        ];
        return view('acl.user-details.ajax-tabs.tab-password', compact('details_edit_vue'));
    }

    public function save(Request $request, UserRepository $user_repo, $id, $tab_name)
    {
        if ($request->id != $id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        $user = $this->findById($id, $user_repo);
         // Apply validation
        $this->applyValidation($request);

        $response = DB::transaction(function () use ($request, $user) {
            return $this->saveFromRequest($request, $user);
        });

        return response()->json([
            'gotourl' => route('tab.acl.user.details', [
                'id' => $response->id,
                'tab_name' => $tab_name,
            ])
        ]);

        // save form data here
    }

     private function applyValidation(Request $request)
    {
        $request->validate([
            'password' => 'min:8|required_with:confirm-password|same:confirm-password',
            'confirm-password' => 'min:8|required',
        ]);
    }

     private function findById($id, \App\Repositories\Base\BaseRepository $repo)
    {
        return ($id > 0) ? $repo->model()->findOrFail($id) : $repo->model();
    }

    private function saveFromRequest(Request $request, User $user)
    {

        $user->password = Hash::make($request->password);

        $user->save();

        voco_flash()->success(__('User password updated!'));

        return $user;
    }

}
