<?php

namespace App\Http\Controllers\Stock\StockPurchaseDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\StockPurchaseRepository;
use Validator;
use Auth;

class TabInfoController extends Controller
{
    public function index(Request $request, StockPurchaseRepository $stock_purchase_repo, $purchase_id, $tab_name)
    {
        $stock_purchase_info = $stock_purchase_repo->model()
                ->with('createdUser')
                ->with('updatedUser')
                ->with(['product_rows.stockItem'])
                ->with('vendor')
                ->with(['editLogs.editedUser'])
                ->findOrFail($purchase_id);

        $details_info_vue = [
            'purchase_id' => $purchase_id,
            'tab_name' => $tab_name,
            'stock_purchase_info' => $stock_purchase_info,
        ];
        // dd($details_info_vue);
        return view('stock.stock-purchase-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
