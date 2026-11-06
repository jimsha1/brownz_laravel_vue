<?php

namespace App\Http\Controllers\Stock\StockItemDetails\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use App\Models\StockItem;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.stock.stock.item.details';
    private $module = 'Stock';
    private $namespace = 'StockItemDetails';
    private $model = 'StockItem';

    public function index($item_id, $tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $stock_item_info = null;
        if ($item_id > 0) {
            try {
                $stock_item_info = StockItem::with('stockUom')->findOrFail($item_id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return $this->tabViewNotFound();
            }
        }

        // Add route params
        $this->addRouteParam('item_id', $item_id);

        $this->addDependencyInjection(\App\Repositories\StockItemRepository::class, 'stock_item_repo');

        // Add tabs
        $this->addTabs('info', 'fa fa-eye');
        $this->addTabs('edit', 'fa fa-edit', true);
        // $this->addTabs('units', 'fa fa-balance-scale', true);
        // $this->addTabs('pricing', 'fa fa-usd', true);
        $this->addTabs('Opening Stock', 'fa fa-usd', true);
        $this->addTabs('Stock Adjustment', 'fa fa-adjust', true);
        $this->addTabs('Stock Movements', 'fa fa-exchange');
        $this->addTabs('commission', 'fa fa-money');

        $selected_tab_name = $this->getSelectedTab($tab_name);

        // Be careful! this will auto generate controller and blade files,
        // based on the above tabs configured (when not in production environment)
        $this->ensureFiles(dirname(__DIR__));

        // Ajax request must be treated separately
        if (\Request::ajax()) {
            $tab_html = $this->renderView($this->module, $this->namespace, $selected_tab_name, [
                'item_id' => $item_id,
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
            'href' => route('tab.stock.stock.item.list'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('stock.stock-item-details.index', array_merge($tabs_data, [
            'stock_item_info' => $stock_item_info,
            'page_name' =>'item_details',
            'category_name' =>'stock',
        ]));
    }
}
