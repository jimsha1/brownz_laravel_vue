<?php

namespace App\Http\Controllers\Expenses\ExpenseList\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.expenses.expense.list';
    private $module = 'Expenses';
    private $namespace = 'ExpenseList';
    private $model = 'Expense';

    public function index($tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $this->addDependencyInjection(\App\Repositories\ExpenseRepository::class, 'expense_repo');

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
                'tab_name' => $selected_tab_name,
                'ajax_tab_html' => $tab_html
            ]);
        }

        // Buttons for navigation
        $this->addTabButton('button', __('New'), 'fa fa-plus', [
            'type' => 'button',
            'class' => 'btn btn-sm btn-primary vctab-btn btn_dark-x',
            'data-modal-wide' => 'true',
            'data-url' => route('tab.expenses.expense.details', ['expense_id' => '0', 'tab_name' => 'edit', 'return_html' => 'y']),
            'data-modal-title' => 'Create Expenses',
            'data-voco-toggle' => 'modal',
            'data-target' => '#voco-ajax-modal',
            'data-toggle' => 'tooltip',
            'title' => 'Create Expenses',
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);
        $tabs_data['accounts'] = Account::where('is_active', 1)
            ->where('type', 'expense')
            ->orderBy('name')
            ->get(['account_id', 'name']);

        return view('expenses.expense-list.index', $tabs_data);
    }
}
