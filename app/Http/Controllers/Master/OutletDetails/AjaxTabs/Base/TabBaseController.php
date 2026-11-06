<?php

namespace App\Http\Controllers\Master\OutletDetails\AjaxTabs\Base;

use App\Http\Controllers\Controller;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.master.outlet.details';
    private $module = 'Master';
    private $namespace = 'OutletDetails';
    private $model = 'Outlet';

    public function index($outlet_id, $tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $outlet_info = null;
        if ($outlet_id > 0) {
            $model_class = '\App\Models\\' . $this->model;
            $outlet_info = $model_class::find($outlet_id);

            // Check whether db record exists
            if (empty($outlet_info['outlet_id'])) {
                return $this->tabViewNotFound();
            }
        }

        // Add route params
        $this->addRouteParam('outlet_id', $outlet_id);

        $this->addDependencyInjection(\App\Repositories\OutletRepository::class, 'outlet_repo');

        // Add tabs
        $this->addTabs('info', 'fa fa-eye');
        $this->addTabs('edit', 'fa fa-edit', true);

        $selected_tab_name = $this->getSelectedTab($tab_name);

        // Be careful! this will auto generate controller and blade files,
        // based on the above tabs configured (when not in production environment)
        $this->ensureFiles(dirname(__DIR__));

        // Ajax request must be treated separately
        if (\Request::ajax()) {
            $tab_html = $this->renderView($this->module, $this->namespace, $selected_tab_name, [
                'outlet_id' => $outlet_id,
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
            'href' => route('tab.master.outlet.list'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('master.outlet-details.index', array_merge($tabs_data, [
            'outlet_info' => $outlet_info,
            'category_name' => 'master',
            'page_name' => 'outlet_edit',

        ]));
    }
}
