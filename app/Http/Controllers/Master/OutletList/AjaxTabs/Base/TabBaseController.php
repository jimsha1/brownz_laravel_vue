<?php

namespace App\Http\Controllers\Master\OutletList\AjaxTabs\Base;

use App\Http\Controllers\Controller;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.master.outlet.list';
    private $module = 'Master';
    private $namespace = 'OutletList';
    private $model = 'Outlet';

    public function index($tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $this->addDependencyInjection(\App\Repositories\OutletRepository::class, 'outlet_repo');

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
            'data-url' => route('tab.master.outlet.details', ['outlet_id' => '0', 'tab_name' => 'edit', 'return_html' => 'y']),
            'data-modal-title' => 'Create New Outlet',
            'data-voco-toggle' => 'modal',
            'data-target' => '#voco-ajax-modal',
            'data-toggle' => 'tooltip',
            'title' => 'Create New Outlet',
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('master.outlet-list.index', $tabs_data);
    }
}
