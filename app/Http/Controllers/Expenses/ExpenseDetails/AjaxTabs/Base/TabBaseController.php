<?php

namespace App\Http\Controllers\Expenses\ExpenseDetails\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class TabBaseController extends Controller
{
     use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.expenses.expense.details';
    private $module = 'Expenses';
    private $namespace = 'ExpenseDetails';
    private $model = 'Expense';

    public function index($expense_id, $tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $expense_info = null;
        if ($expense_id > 0) {
            try {
                $expense_info = Expense::findOrFail($expense_id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return $this->tabViewNotFound();
            }
        }

        // Add route params
        $this->addRouteParam('expense_id', $expense_id);

        $this->addDependencyInjection(\App\Repositories\ExpenseRepository::class, 'expense_repo');

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
                'expense_id' => $expense_id,
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
            'href' => route('tab.expenses.expense.list'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('expenses.expense-details.index', array_merge($tabs_data, [
            'expense_info' => $expense_info,
            'page_name' =>'expense_details',
            'category_name' =>'expenses',
        ]));
    }

}
