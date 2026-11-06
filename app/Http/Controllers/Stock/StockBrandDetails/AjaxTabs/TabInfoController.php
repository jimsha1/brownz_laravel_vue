<?php

namespace App\Http\Controllers\Stock\StockBrandDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\StockBrandRepository;
use Auth;

class TabInfoController extends Controller
{
    public function index(Request $request, StockBrandRepository $stock_brand_repo, $brand_id, $tab_name)
    {
        $stock_brand_info = $stock_brand_repo->model()
                ->with('createdUser')
                ->with('updatedUser')
                ->findOrFail($brand_id);

        $details_info_vue = [
            'brand_id' => $brand_id,
            'tab_name' => $tab_name,
            'stock_brand_info' => $stock_brand_info,
        ];

        return view('stock.stock-brand-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
