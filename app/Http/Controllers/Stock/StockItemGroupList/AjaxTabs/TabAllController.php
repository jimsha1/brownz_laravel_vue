<?php

namespace App\Http\Controllers\Stock\StockItemGroupList\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Repositories\StockItemGroupRepository;
use Illuminate\Http\Request;

class TabAllController extends Controller
{
    public function index(Request $request, StockItemGroupRepository $stock_item_group_repo, $tab_name)
    {
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        $db_records = $stock_item_group_repo->hunter()
            ->setSearchTerm($request->search)
            ->query(function ($query) use ($request) {
                $query->with('parent');

                if ($request->filled('status')) {
                    $query->where('is_active', (int) $request->status);
                }

                if ($request->filled('is_show_website')) {
                    $query->where('is_show_website', (int) $request->is_show_website);
                }
            })
            ->search();

        if (method_exists($db_records, 'appends')) {
            $db_records->appends(array_filter([
                'pgajax' => 'y',
                'search' => $request->search,
                'status' => $request->status,
                'is_show_website' => $request->is_show_website,
            ], function ($value) {
                return $value !== null && $value !== '';
            }));
        }

        foreach ($db_records as $row) {
            $row->view_url = route('tab.stock.stock.item.group.details', [
                'item_group_id' => $row->item_group_id,
                'tab_name' => 'info',
            ]);
            $row->edit_url = route('tab.stock.stock.item.group.details', [
                'item_group_id' => $row->item_group_id,
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

        return view('stock.stock-item-group-list.ajax-tabs.tab-all', compact('db_results', 'tab_name'));
    }
}
