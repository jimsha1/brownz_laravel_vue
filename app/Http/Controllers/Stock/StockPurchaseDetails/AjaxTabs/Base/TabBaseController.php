<?php

namespace App\Http\Controllers\Stock\StockPurchaseDetails\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use App\Repositories\StockPurchaseRepository;
use Illuminate\Http\Request;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.stock.stock.purchase.details';
    private $module = 'Stock';
    private $namespace = 'StockPurchaseDetails';
    private $model = 'StockPurchase';

    public function index(StockPurchaseRepository $stock_purchase_repo, $purchase_id, $tab_name = null)
    {

        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $purchase_info = null;
        if ($purchase_id > 0) {
            try {
                $purchase_info = $stock_purchase_repo->hunter()->findPurchase($purchase_id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return $this->tabViewNotFound();
            }
        }

        // Add route params
        $this->addRouteParam('purchase_id', $purchase_id);
        $this->addDependencyInjection(\App\Repositories\StockPurchaseRepository::class, 'stock_purchase_repo');

        // Add tabs
        if ($purchase_id > 0) {
            $this->addTabs('info', 'fa fa-eye');
        }

        $hasReturns = false;
        if ($purchase_id > 0) {
            $hasReturns = \App\Models\StockPurchaseItem::where('purchase_id', $purchase_id)
                ->where('refunded_qty', '>', 0)
                ->exists()
                || \App\Models\StockPurchase::where('parent_id', $purchase_id)
                    ->where('purchase_type', 'refund')
                    ->exists();
        }

        // Editing after returns corrupts stock/GL — only allow return / info
        if (! $hasReturns && auth()->user()->can('purchase_edit')) {
            $this->addTabs('edit', 'fa fa-edit', true);
        }

        // Add return tab (not for refund documents themselves)
        if (
            $purchase_id > 0
            && auth()->user()->can('purchase_return')
            && ! ($purchase_info && ($purchase_info->parent_id || $purchase_info->purchase_type === 'refund'))
        ) {
            $this->addTabs('return', 'fa fa-undo', true);
        }

        $selected_tab_name = $this->getSelectedTab($tab_name);

        // Be careful! this will auto generate controller and blade files,
        // based on the above tabs configured (when not in production environment)
        $this->ensureFiles(dirname(__DIR__));

        // Ajax request must be treated separately
        if (\Request::ajax()) {
            $tab_html = $this->renderView($this->module, $this->namespace, $selected_tab_name, [
                'purchase_id' => $purchase_id,
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

        // New booking button
        $this->addTabButton('a', __('New'), 'fa fa-plus', [
            'class' => 'btn_dark-x',
            'href' => route('tab.stock.stock.purchase.details', ['purchase_id' => '0', 'tab_name' => 'edit']),
            'data-toggle' => 'tooltip',
            'title' => 'Create New Purchase',
        ]);

        // Tab back button
        $this->addTabButton('a', __('Back'), 'fa fa-backward', [
            'class' => 'btn_dark-x vctab-btn',
            'href' => route('tab.stock.stock.purchase.list'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('stock.stock-purchase-details.index', array_merge($tabs_data, [
            'purchase_info' => $purchase_info,
            'page_name' => 'purchase_details',
            'category_name' => 'Stock',

        ]));
    }
}
