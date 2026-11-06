<?php

namespace App\Http\Controllers\Stock\StockBrandList\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Repositories\StockBrandRepository;
use Illuminate\Http\Request;

class TabAllController extends Controller
{
    public function index(Request $request, StockBrandRepository $stock_brand_repo, $tab_name)
    {
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        $db_records = $stock_brand_repo->hunter()
            ->setSearchTerm($request->search)
            ->query(function ($query) use ($request) {
                if ($request->filled('status')) {
                    $query->where('is_active', (int) $request->status);
                }
            })
            ->search();

        if (method_exists($db_records, 'appends')) {
            $db_records->appends(array_filter([
                'pgajax' => 'y',
                'search' => $request->search,
                'status' => $request->status,
            ], function ($value) {
                return $value !== null && $value !== '';
            }));
        }

        foreach ($db_records as $row) {
            $row->view_url = route('tab.stock.stock.brand.details', [
                'brand_id' => $row->brand_id,
                'tab_name' => 'info',
            ]);
            $row->edit_url = route('tab.stock.stock.brand.details', [
                'brand_id' => $row->brand_id,
                'tab_name' => 'edit',
            ]);
        }

        $db_results = [
            'vue_data' => [
                'vue_list_tab_all' => [
                    'paginator' => paginator_vue_data($db_records),
                ],
            ],
            'response_values' => [
                '#tab_search' => '0',
            ],
        ];

        if ($request->tab_search || $request->pgajax === 'y') {
            return response()->json($db_results);
        }

        return view('stock.stock-brand-list.ajax-tabs.tab-all', compact('db_results', 'tab_name'));
    }
}
