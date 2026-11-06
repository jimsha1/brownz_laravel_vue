<?php

namespace App\Http\Controllers\Acl\UserList\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Auth;
use App\Models\User;

class TabAllController extends Controller
{
    public function index(Request $request, UserRepository $user_repo, $tab_name)
    {
        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        // Default status filter to active when none provided
        $status = $request->input('status', 'active');
        $statusFilter = ($status === 'all') ? null : $status;

        $db_records = $user_repo->hunter()
            ->setSearchTerm($request->search)
            ->setRoleName($request->role)
            ->setUserStatus($statusFilter)
            ->search();

        if (method_exists($db_records, 'appends')) {
            $db_records->appends(array_filter([
                'pgajax' => 'y',
                'search' => $request->search,
                'role' => $request->role,
                'status' => $status,
            ], function ($value) {
                return $value !== null && $value !== '';
            }));
        }

        // Add additional properties for the records
        foreach ($db_records as $row) {
            $row->edit_url = route('tab.acl.user.details', ['id' => $row->id, 'tab_name' => 'edit']);
            $row->active_action = false;
            $row->inactive_action = false;
            $row->pending_action = false;
            if($row->user_status == 'active'){
                $row->active_action = 'changestatus('.$row->id.',0)';
            }else if($row->user_status == 'inactive'){
                $row->inactive_action = 'changestatus('.$row->id.',1)';
            }else if($row->user_status == 'pending'){
                $row->pending_action = true;
            }
            $row->role_names = $row->roles->pluck('name');
            $row->can_delete = !$row->hasRole(\App\Models\AclRole::ROLE_SUPER_ADMIN);
            $row->delete_url = $row->can_delete ? 'delete_user('.$row->id.')' : null;
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

        return view('acl.user-list.ajax-tabs.tab-all', compact('db_results', 'tab_name'));
    }

    public function delete(Request $request){
        $user_data = User::find($request->id);

        if (!$user_data) {
            voco_flash()->error(__('User not found.'));
            return redirect()->route('tab.acl.user.list');
        }

        if ($user_data->hasRole(\App\Models\AclRole::ROLE_SUPER_ADMIN)) {
            voco_flash()->error(__('Super admin users cannot be deleted.'));
            return redirect()->route('tab.acl.user.list');
        }

        $user_data->delete();
        voco_flash()->success(__('Successfully deleted the user!'));
        return redirect()->route('tab.acl.user.list');
    }

    public function changestatus(Request $request){
        $user_data = User::find($request->id);
        $status = $request->status;
        $new_status = '';
        if($status == 0){
            $new_status = 'inactive';
        }else if($status == 1){
            $new_status = 'active';
        }
        if($new_status){
            $user_data->user_status = $new_status;
            $user_data->save();
        }
        voco_flash()->success(__('Successfully updated the user status!'));
        return redirect()->route('tab.acl.user.list');
    }
}
