<?php

namespace App\Http\Controllers\Master\GlobalTaxDetails\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use App\Models\GlobalTax;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.master.global.tax.details';
    private $module = 'Master';
    private $namespace = 'GlobalTaxDetails';
    private $model = 'GlobalTax';

    public function index($tax_id, $tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $global_tax_info = null;
        if ($tax_id > 0) {
            try {
                $global_tax_info = GlobalTax::findOrFail($tax_id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return $this->tabViewNotFound();
            }
        }

        // Add route params
        $this->addRouteParam('tax_id', $tax_id);

        $this->addDependencyInjection(\App\Repositories\GlobalTaxRepository::class, 'global_tax_repo');

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
                'tax_id' => $tax_id,
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
            'href' => route('tab.master.global.tax.list'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('master.global-tax-details.index', array_merge($tabs_data, [
            'global_tax_info' => $global_tax_info,
            'category_name' =>'master',
            'page_name' =>'global_tax_edit',
        ]));
    }
}
