<?php

namespace App\Http\Controllers\Expenses\ExpenseDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Repositories\ExpenseRepository;
use Illuminate\Http\Request;

class TabInfoController extends Controller
{
      public function index(Request $request, ExpenseRepository $expense_repo, $expense_id, $tab_name)
    {
        $expense_info = $expense_repo->model()
                ->with('createdUser')
                ->with('account')
                ->findOrFail($expense_id);

        $details_info_vue = [
            'expense_id' => $expense_id,
            'tab_name' => $tab_name,
            'expense_info' => $expense_info,
        ];

        return view('expenses.expense-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
