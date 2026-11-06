<?php

namespace App\Http\Controllers\Stock\StockOpenBalanceDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\StockOpenBalanceRepository;
use Validator;
use Auth;

class TabInfoController extends Controller
{
    public function index(Request $request, StockOpenBalanceRepository $stock_open_balance_repo, $open_balance_id, $tab_name)
    {
        $stock_open_balance_info = $stock_open_balance_repo->model()
                ->with(['createdUser', 'updatedUser', 'account'])
                ->findOrFail($open_balance_id);

        $details_info_vue = [
            'open_balance_id' => $open_balance_id,
            'tab_name' => $tab_name,
            'stock_open_balance_info' => $stock_open_balance_info,
        ];
        // dd($details_info_vue);
        return view('stock.stock-open-balance-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
