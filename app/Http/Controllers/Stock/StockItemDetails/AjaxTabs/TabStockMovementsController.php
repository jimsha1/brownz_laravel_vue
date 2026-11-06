<?php

namespace App\Http\Controllers\Stock\StockItemDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Repositories\StockItemRepository;
use Illuminate\Http\Request;

class TabStockMovementsController extends Controller
{
    public function index(Request $request, StockItemRepository $stock_item_repo, $item_id, $tab_name)
    {
        $movements = StockMovement::where('item_id', $item_id)
            ->orderBy('stock_movement_id')
            ->get();

        $details_movements_vue = [
            'item_id' => $item_id,
            'tab_name' => $tab_name,
            'movements' => $movements,
            'urls' => [
                'opening_stock_url' => route('tab.stock.stock.item.details', [
                    'item_id' => $item_id,
                    'tab_name' => 'opening-stock',
                ]),
                'stock_adjustment_url' => route('tab.stock.stock.item.details', [
                    'item_id' => $item_id,
                    'tab_name' => 'stock-adjustment',
                ]),
            ],
        ];

        return view(
            'stock.stock-item-details.ajax-tabs.tab-stock-movements',
            compact('details_movements_vue')
        );
    }
}
