<?php

namespace App\Http\Controllers\Stock\StockOpenBalanceDetails\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use App\Repositories\StockOpenBalanceRepository;
use Illuminate\Http\Request;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.stock.open.balance.details';
    private $module = 'Stock';
    private $namespace = 'StockOpenBalanceDetails';
    private $model = 'StockOpenBalance';

    public function index(StockOpenBalanceRepository $stock_open_balance_repo, $open_balance_id, $tab_name = null)
    {

        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $open_balance_info = null;
        if ($open_balance_id > 0) {
            try {
                $open_balance_info = $stock_open_balance_repo->hunter()->findPurchase($open_balance_id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return $this->tabViewNotFound();
            }
        }

        // Add route params
        $this->addRouteParam('open_balance_id', $open_balance_id);
        $this->addDependencyInjection(\App\Repositories\StockOpenBalanceRepository::class, 'stock_open_balance_repo');

        // Add tabs
        if ($open_balance_id > 0) {
            $this->addTabs('info', 'fa fa-eye');
            if (auth()->user()->can('opening_balance_edit')) {
                $this->addTabs('edit', 'fa fa-edit', true);
            }
        } elseif (auth()->user()->can('opening_balance_create')) {
            $this->addTabs('edit', 'fa fa-edit', true);
        } else {
            abort(403, __('You do not have permission to create an opening balance.'));
        }

        $selected_tab_name = $this->getSelectedTab($tab_name);

        // Be careful! this will auto generate controller and blade files,
        // based on the above tabs configured (when not in production environment)
        $this->ensureFiles(dirname(__DIR__));

        // Ajax request must be treated separately
        if (\Request::ajax()) {
            $tab_html = $this->renderView($this->module, $this->namespace, $selected_tab_name, [
                'open_balance_id' => $open_balance_id,
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

        if (auth()->user()->can('opening_balance_create')) {
            $this->addTabButton('a', __('New'), 'fa fa-plus', [
                'class' => 'btn_dark-x',
                'href' => route('tab.stock.open.balance.details', ['open_balance_id' => '0', 'tab_name' => 'edit']),
                'data-toggle' => 'tooltip',
                'title' => 'Create New',
            ]);
        }

        // Tab back button
        $this->addTabButton('a', __('Back'), 'fa fa-backward', [
            'class' => 'btn_dark-x vctab-btn',
            'href' => route('tab.stock.open.balance.list'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('stock.stock-open-balance-details.index', array_merge($tabs_data, [
            'open_balance_info' => $open_balance_info,
            'page_name' => 'open_balance_details',
            'category_name' => 'Stock',

        ]));
    }
}
