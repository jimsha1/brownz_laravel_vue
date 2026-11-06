<?php

namespace App\Http\Controllers\Accounts\AccountDetails\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use App\Models\Account;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.accounts.account.details';
    private $module = 'Accounts';
    private $namespace = 'AccountDetails';
    private $model = 'Account';

    public function index($account_id, $tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $account_info = null;
        if ($account_id > 0) {
            try {
                $account_info = Account::findOrFail($account_id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return $this->tabViewNotFound();
            }
        }

        // Add route params
        $this->addRouteParam('account_id', $account_id);

        $this->addDependencyInjection(\App\Repositories\AccountRepository::class, 'account_repo');

        // Add tabs — system defined ledgers have no edit tab
        $is_system_defined = $account_info && (int) $account_info->is_system_defined === 1;

        $this->addTabs('info', 'fa fa-eye');
        if (! $is_system_defined) {
            $this->addTabs('edit', 'fa fa-edit', true);
        }
        $this->addTabs('transactions', 'fa fa-exchange-alt');

        $selected_tab_name = $this->getSelectedTab($tab_name);

        // Be careful! this will auto generate controller and blade files,
        // based on the above tabs configured (when not in production environment)
        $this->ensureFiles(dirname(__DIR__));

        // Ajax request must be treated separately
        if (\Request::ajax()) {
            $tab_html = $this->renderView($this->module, $this->namespace, $selected_tab_name, [
                'account_id' => $account_id,
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
            'href' => route('tab.accounts.account.list'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('accounts.account-details.index', array_merge($tabs_data, [
            'account_info' => $account_info,
            'page_name' =>'account_details',
            'category_name' =>'accounts',
        ]));
    }
}
