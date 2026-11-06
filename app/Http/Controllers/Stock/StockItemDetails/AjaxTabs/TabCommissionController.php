<?php

namespace App\Http\Controllers\Stock\StockItemDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Repositories\StockItemRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TabCommissionController extends Controller
{
    public function index(Request $request, StockItemRepository $stock_item_repo, $item_id, $tab_name)
    {
        $stock_item_info = StockItem::find($item_id);

        $details_edit_vue = [
            'item_id' => $item_id,
            'tab_name' => $tab_name,
            'stock_item_info' => $stock_item_info,
            'urls' => [
                'button_cancel_url' => route('tab.stock.stock.item.list'),
                'form_save_url' => route('tab.stock.stock.item.save', [
                    'item_id' => $item_id,
                    'tab_name' => $tab_name,
                ]),
            ],
        ];

        return view('stock.stock-item-details.ajax-tabs.tab-commission', compact('details_edit_vue'));
    }

    public function save(Request $request, StockItemRepository $stock_item_repo, $item_id, $tab_name)
    {
        if ($request->item_id != $item_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        $stock_item = StockItem::findOrFail($item_id);

        $stock_item->start_date = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $stock_item->end_date = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
        $stock_item->commission_percentage = $request->commission_percentage;
        $stock_item->commission_status = $request->commission_status ? 1 : 0;
        $stock_item->save();

        return response()->json([
            'gotourl' => route('tab.stock.stock.item.details', [
                'item_id' => $item_id,
                'tab_name' => $tab_name,
            ]),
        ]);
    }
}
