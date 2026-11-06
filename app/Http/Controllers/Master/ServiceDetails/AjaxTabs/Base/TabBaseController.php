<?php

namespace App\Http\Controllers\Master\ServiceDetails\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use App\Models\Service;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.master.service.details';
    private $module = 'Master';
    private $namespace = 'ServiceDetails';
    private $model = 'Service';

    public function index($service_id, $tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $service_info = null;
        if ($service_id > 0) {
            try {
                $service_info = Service::findOrFail($service_id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return $this->tabViewNotFound();
            }
        }

        // Add route params
        $this->addRouteParam('service_id', $service_id);

        $this->addDependencyInjection(\App\Repositories\ServiceRepository::class, 'service_repo');

        // Add tabs
        $this->addTabs('info', 'fa fa-eye');
        $this->addTabs('edit', 'fa fa-edit', true);
        $this->addTabs('commission', 'fa fa-money');
        if ($service_id > 0) {
            $this->addTabs('used-products', 'fa fa-cubes');
        }

        $selected_tab_name = $this->getSelectedTab($tab_name);

        // Be careful! this will auto generate controller and blade files,
        // based on the above tabs configured (when not in production environment)
        $this->ensureFiles(dirname(__DIR__));

        // Ajax request must be treated separately
        if (\Request::ajax()) {
            $tab_html = $this->renderView($this->module, $this->namespace, $selected_tab_name, [
                'service_id' => $service_id,
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
            'href' => route('tab.master.service.list'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);
        // return $service_info;
        return view('master.service-details.index', array_merge($tabs_data, [
            'service_info' => $service_info,
            'page_name' => 'service_details',
            'category_name' => 'master',
        ]));
    }
}
