<?php

namespace App\Http\Controllers\Stock\StockBrandDetails\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use App\Models\StockBrand;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.stock.stock.brand.details';
    private $module = 'Stock';
    private $namespace = 'StockBrandDetails';
    private $model = 'StockBrand';

    public function index($brand_id, $tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $stock_brand_info = null;
        if ($brand_id > 0) {
            try {
                $stock_brand_info = StockBrand::findOrFail($brand_id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return $this->tabViewNotFound();
            }
        }

        // Add route params
        $this->addRouteParam('brand_id', $brand_id);

        $this->addDependencyInjection(\App\Repositories\StockBrandRepository::class, 'stock_brand_repo');

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
                'brand_id' => $brand_id,
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
            'href' => route('tab.stock.stock.brand.list'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('stock.stock-brand-details.index', array_merge($tabs_data, [
            'stock_brand_info' => $stock_brand_info,
            'page_name' =>'brand_details',
            'category_name' =>'stock',
        ]));
    }
}
