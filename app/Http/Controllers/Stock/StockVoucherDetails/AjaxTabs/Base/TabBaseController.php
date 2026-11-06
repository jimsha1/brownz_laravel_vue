<?php

namespace App\Http\Controllers\Stock\StockVoucherDetails\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use App\Models\StockVoucher;
use App\Models\TransactionGroup;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.stock.stock.voucher.details';
    private $module = 'Stock';
    private $namespace = 'StockVoucherDetails';
    private $model = 'StockVoucher';

    public function index($id, $tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $stock_voucher_info = null;
        if ($id > 0) {
            try {
                $stock_voucher_info = TransactionGroup::findOrFail($id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return $this->tabViewNotFound();
            }
        }

        // Add route params
        $this->addRouteParam('id', $id);

        $this->addDependencyInjection(\App\Repositories\TransactionGroupRepository::class, 'transaction_group_repo');

        // Add tabs — booking vouchers are view-only (no edit)
        $isBookingVoucher = $stock_voucher_info && $stock_voucher_info->isBookingVoucher();
        if ($isBookingVoucher) {
            $this->addTabs('info', 'fa fa-eye', true);
        } else {
            $this->addTabs('info', 'fa fa-eye');
            $this->addTabs('edit', 'fa fa-edit', true);
        }

        $selected_tab_name = $this->getSelectedTab($tab_name);

        if (! ($id > 0) && (
            request()->filled('commission_id')
            || request()->filled('commission_master_id')
            || request()->filled('incentive_id')
            || request()->filled('incentive_master_id')
            || request()->filled('payroll_id')
            || request()->filled('payroll_master_id')
            || request()->filled('vendor_id')
        )) {
            $queryParams = [];
            if (request()->filled('commission_master_id')) {
                $queryParams['commission_master_id'] = request('commission_master_id');
            }
            if (request()->filled('commission_id')) {
                $queryParams['commission_id'] = request('commission_id');
            }
            if (request()->filled('incentive_master_id')) {
                $queryParams['incentive_master_id'] = request('incentive_master_id');
            }
            if (request()->filled('incentive_id')) {
                $queryParams['incentive_id'] = request('incentive_id');
            }
            if (request()->filled('payroll_master_id')) {
                $queryParams['payroll_master_id'] = request('payroll_master_id');
            }
            if (request()->filled('payroll_id')) {
                $queryParams['payroll_id'] = request('payroll_id');
            }
            if (request()->filled('vendor_id')) {
                $queryParams['vendor_id'] = request('vendor_id');
            }

            $this->appendTabUrlQuery($queryParams);

            if ($selected_tab_name === 'edit') {
                $tab_html = $this->renderView($this->module, $this->namespace, 'edit', [
                    'id' => $id,
                    'tab_name' => 'edit',
                ]);

                if (is_string($tab_html) && $tab_html !== '') {
                    $this->setTabContent('edit', $tab_html);
                }
            }
        }

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
            'href' => route('tab.stock.stock.voucher.list'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('stock.stock-voucher-details.index', array_merge($tabs_data, [
            'stock_voucher_info' => $stock_voucher_info,
            'page_name' => 'voucher_details',
            'category_name' => 'accounts',
        ]));
    }
}
