<?php

namespace App\Http\Controllers\Acl\AclRoleDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\AclRole;
use App\Repositories\AclRoleRepository;
use Illuminate\Http\Request;

class TabEditController extends Controller
{
    public function index(Request $request, AclRoleRepository $acl_role_repo, $id, $tab_name)
    {
        $acl_role_info = $this->findById($id, $acl_role_repo);

        $cancelUrl = ((int) $id > 0)
            ? route('tab.acl.role.details', ['id' => $id, 'tab_name' => 'info'])
            : route('tab.acl.role.list');

        $details_edit_vue = [
            'id' => $id,
            'tab_name' => $tab_name,
            'acl_role_info' => $acl_role_info,
            'urls' => [
                'button_cancel_url' => $cancelUrl,
                'form_save_url' => route('tab.acl.role.save', ['id' => $id, 'tab_name' => $tab_name]),
            ],
        ];

        return view('acl.acl-role-details.ajax-tabs.tab-edit', compact('details_edit_vue'));
    }

    public function save(Request $request, AclRoleRepository $acl_role_repo, $id, $tab_name)
    {
        if ($request->id != $id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Fetch db record for update, or create a new model for insert
        $acl_role = $this->findById($id, $acl_role_repo);

        // Apply validation
        $this->applyValidation($request);

        // Start database transaction
        $response = \DB::transaction(function () use ($request, $acl_role) {
            return $this->saveFromRequest($request, $acl_role);
        });

        return response()->json([
            'gotourl' => route('tab.acl.role.details', [
                'id' => $response->id,
                'tab_name' => $tab_name,
            ])
        ]);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function applyValidation(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);
    }

    private function saveFromRequest(Request $request, AclRole $acl_role)
    {
        $acl_role->name = $request->name;
        $acl_role->description = $request->description;

        $acl_role->save();

        voco_flash()->success(__('Acl role details updated!'));

        return $acl_role;
    }

    private function findById($id, \App\Repositories\Base\BaseRepository $repo)
    {
        return ($id > 0) ? $repo->model()->findOrFail($id) : $repo->model();
    }
}
