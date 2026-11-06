<?php

namespace App\Http\Controllers\Acl\UserDetails\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use App\Models\User;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.acl.user.details';
    private $module = 'Acl';
    private $namespace = 'UserDetails';
    private $model = 'User';

    public function index($id, $tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        // Direct user creation is disabled — create via Payroll → Employee
        if ((int) $id <= 0) {
            if (\Request::ajax() || request('return_html') === 'y') {
                abort(403, __('Users can only be created from Payroll → Employee Management.'));
            }

            voco_flash()->error(__('Users can only be created from Payroll → Employee Management.'));

            return redirect()->route('tab.acl.user.list');
        }

        $user_info = null;
        try {
            $user_info = User::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->tabViewNotFound();
        }

        // Add route params
        $this->addRouteParam('id', $id);

        $this->addDependencyInjection(\App\Repositories\UserRepository::class, 'user_repo');

        // Add tabs
        $this->addTabs('info', 'fa fa-eye');
        $this->addTabs('edit', 'fa fa-edit', true);
        $this->addTabs('roles', 'fa fa-graduation-cap', true);
        $this->addTabs('permissions', 'fa fa-gavel');
        $this->addTabs('password', 'fa fa-user-secret', true);

        if (config('vocomi.settings.outlet.enabled') === true) {
            $this->addTabs('outlets', 'fa fa-university', true);
        }

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
            'href' => route('tab.acl.user.list'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('acl.user-details.index', array_merge($tabs_data, [
            'user_info' => $user_info,
            'page_name' => 'user_details',
            'category_name' => 'acl',
        ]));
    }
}
