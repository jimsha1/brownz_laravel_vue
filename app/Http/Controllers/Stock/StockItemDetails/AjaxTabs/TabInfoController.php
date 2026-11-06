<?php

namespace App\Http\Controllers\Stock\StockItemDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\StockItemRepository;
use Auth;

class TabInfoController extends Controller
{
    public function index(Request $request, StockItemRepository $stock_item_repo, $item_id, $tab_name)
    {
        $stock_item_info = $stock_item_repo->model()
                ->with('createdUser')
                ->with('updatedUser')
                ->with('category')
                ->with('brand')
                ->with('stockUom')
                ->findOrFail($item_id);

        $details_info_vue = [
            'item_id' => $item_id,
            'tab_name' => $tab_name,
            'stock_item_info' => $stock_item_info,
            'urls' => [
                'stock_adjustment_url' => route('tab.stock.stock.item.details', [
                    'item_id' => $item_id,
                    'tab_name' => 'stock-adjustment',
                ]),
                'stock_movements_url' => route('tab.stock.stock.item.details', [
                    'item_id' => $item_id,
                    'tab_name' => 'stock-movements',
                ]),
            ],
        ];
// dd($details_info_vue);
        return view('stock.stock-item-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
