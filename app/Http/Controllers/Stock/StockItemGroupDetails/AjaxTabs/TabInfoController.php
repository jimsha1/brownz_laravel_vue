<?php

namespace App\Http\Controllers\Stock\StockItemGroupDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Repositories\StockItemGroupRepository;
use Illuminate\Http\Request;

class TabInfoController extends Controller
{
    public function index(Request $request, StockItemGroupRepository $stock_item_group_repo, $item_group_id, $tab_name)
    {
        $stock_item_group_info = $stock_item_group_repo->model()
            ->with('createdUser')
            ->with('updatedUser')
            ->with('parent')
            ->findOrFail($item_group_id);

        $details_info_vue = [
            'item_group_id' => $item_group_id,
            'tab_name' => $tab_name,
            'stock_item_group_info' => $stock_item_group_info,
        ];

        return view('stock.stock-item-group-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
