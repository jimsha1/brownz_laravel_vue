<?php

namespace App\Http\Controllers\Acl\AclRoleList\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TabBaseController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:create_role|delete_role', ['only' => ['index','create','store','edit','update']]);
         $this->middleware('permission:delete_role', ['only' => ['destroy']]);
    }

    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.acl.role.list';
    private $module = 'Acl';
    private $namespace = 'AclRoleList';
    private $model = 'AclRole';

    public function index($tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $this->addDependencyInjection(\App\Repositories\AclRoleRepository::class, 'acl_role_repo');

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

        // Buttons for navigation
        $this->addTabButton('button', __('New'), 'fa fa-plus', [
            'type' => 'button',
            'class' => 'btn btn-sm btn-primary vctab-btn btn_dark-x',
            'data-modal-wide' => 'true',
            'data-url' => route('tab.acl.role.details', ['id' => '0', 'tab_name' => 'edit', 'return_html' => 'y']),
            'data-modal-title' => 'Create New Acl Role',
            'data-voco-toggle' => 'modal',
            'data-target' => '#voco-ajax-modal',
            'data-toggle' => 'tooltip',
            'title' => 'Create New Acl Role',
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('acl.acl-role-list.index', $tabs_data);
    }
}
