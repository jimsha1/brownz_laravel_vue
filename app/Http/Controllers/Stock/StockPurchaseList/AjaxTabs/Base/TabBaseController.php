<?php

namespace App\Http\Controllers\Stock\StockPurchaseList\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use GuzzleHttp\Psr7\Request;
use Illuminate\Http\Request as postRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.stock.stock.purchase.list';
    private $module       = 'Stock';
    private $namespace    = 'StockPurchaseList';
    private $model        = 'StockPurchase';

    public function index($tab_name = null)
    {

        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $this->addDependencyInjection(\App\Repositories\StockPurchaseRepository ::class, 'stock_purchase_repo');

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
                'tab_name'      => $selected_tab_name,
                'ajax_tab_html' => $tab_html,
            ]);
        }

            $this->addTabButton('a', __('New'), 'fa fa-plus', [
                'class'       => 'btn btn-sm btn-primary btn_dark-x',
                'href'        => route('tab.stock.stock.purchase.details', ['purchase_id' => '0', 'tab_name' => 'edit']),
                'data-toggle' => 'tooltip',
                'title'       => 'Create New Purchase',
            ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('stock.stock-purchase-list.index', $tabs_data);
    }
    public function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page  = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

}
