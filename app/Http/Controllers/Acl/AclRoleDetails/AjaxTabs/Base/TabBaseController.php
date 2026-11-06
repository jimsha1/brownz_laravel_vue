<?php

namespace App\Http\Controllers\Acl\AclRoleDetails\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use App\Models\AclRole;
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
    protected $route_name = 'tab.acl.role.details';
    private $module = 'Acl';
    private $namespace = 'AclRoleDetails';
    private $model = 'AclRole';

    public function index($id, $tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $acl_role_info = null;
        if ($id > 0) {
            try {
                $acl_role_info = AclRole::findOrFail($id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return $this->tabViewNotFound();
            }
        }

        // Add route params
        $this->addRouteParam('id', $id);

        $this->addDependencyInjection(\App\Repositories\AclRoleRepository::class, 'acl_role_repo');

        // Add tabs
        $this->addTabs('info', 'fa fa-eye');
        $this->addTabs('edit', 'fa fa-edit', true);
        $this->addTabs('permissions', 'fa fa-user-secret', true);

        $selected_tab_name = $this->getSelectedTab($tab_name);

        // Be careful! this will auto generate controller and blade files,
        // based on the above tabs configured (when not in production environment)
        $this->ensureFiles(dirname(__DIR__));

        // Ajax request must be treated separately
        if (\Request::ajax()) {
            $tab_html = $this->renderView($this->module, $this->namespace, $selected_tab_name, [
                'id' => $id,
                'tab_name' => $selected_tab_name,
            ]);

            if ($tab_html instanceof \Illuminate\Http\JsonResponse) {
                return $tab_html;
            }

            if ('y' === request('return_html')) {
                return $tab_html;
            }

            return response()->json([
                'tab_name' => $selected_tab_name,
                'ajax_tab_html' => $tab_html
            ]);
        }

        // Tab back button
        $this->addTabButton('a', __('Back'), 'fa fa-backward', [
            'class' => 'btn btn-sm btn-primary vctab-btn btn_dark-x',
            'href' => route('tab.acl.role.list'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('acl.acl-role-details.index', array_merge($tabs_data, [
            'acl_role_info' => $acl_role_info,
            'page_name' => 'role_details',
            'category_name' => 'acl',
        ]));
    }
}
