<?php

namespace App\Http\Controllers\Acl\UserList\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TabBaseController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:create_user|edit_user|delete_user', ['only' => ['index','create','store']]);
         $this->middleware('permission:edit_user', ['only' => ['edit','update']]);
         $this->middleware('permission:delete_user', ['only' => ['destroy']]);
    }

    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.acl.user.list';
    private $module = 'Acl';
    private $namespace = 'UserList';
    private $model = 'User';

    public function index($tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $this->addDependencyInjection(\App\Repositories\UserRepository::class, 'user_repo');

        // Add tabs
        $this->addTabs('all', 'fa fa-folder-open-o');

        $selected_tab_name = $this->getSelectedTab($tab_name);

        // Be careful! this will auto generate controller and blade files,
        // based on the above tabs configured (when not in production environment)
        $this->ensureFiles(dirname(__DIR__));

        // Ajax request must be treated separately
        if (\Request::ajax()) {
            $tab_html = $this->renderView($this->module, $this->namespace, $selected_tab_name, [
                'tab_name' => $selected_tab_name,
            ]);

            if ($tab_html instanceof \Illuminate\Http\JsonResponse) {
                return $tab_html;
            }

            return response()->json([
                'tab_name' => $selected_tab_name,
                'ajax_tab_html' => $tab_html
            ]);
        }

        // Users must be created from Payroll → Employee Management (not here).
        // $this->addTabButton('button', __('New'), 'fa fa-plus', [
        //     'type' => 'button',
        //     'class' => 'btn btn-sm btn-primary vctab-btn btn_dark-x',
        //     'data-modal-wide' => 'true',
        //     'data-url' => route('tab.acl.user.details', ['id' => '0', 'tab_name' => 'edit', 'return_html' => 'y']),
        //     'data-modal-title' => 'Create New User',
        //     'data-voco-toggle' => 'modal',
        //     'data-target' => '#voco-ajax-modal',
        //     'data-toggle' => 'tooltip',
        //     'title' => 'Create New User',
        // ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);
        $roles = \App\Models\AclRole::query()
            ->orderBy('name')
            ->pluck('name', 'name');

        return view('acl.user-list.index', array_merge($tabs_data, [
            'roles' => $roles,
        ]));
    }
}
