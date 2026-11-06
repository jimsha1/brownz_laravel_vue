<?php

namespace App\Http\Controllers\Stock\StockItemList\AjaxTabs;

use App\Exports\StockItemReportExport;
use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Models\StockMovement;
use App\Repositories\StockItemRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TabAllController extends Controller
{
    public function index(Request $request, StockItemRepository $stock_item_repo, $tab_name)
    {
        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        $db_records = $stock_item_repo->hunter()
            ->setSearchTerm($request->search)
            ->query(function ($query) use ($request) {
                $this->applyListFilters($query, $request, true);
            })
            ->search();

        if (method_exists($db_records, 'appends')) {
            $db_records->appends(array_filter([
                'pgajax' => 'y',
                'search' => $request->search,
                'status' => $request->status,
                'is_sales_item' => $request->is_sales_item,
                'stock_level' => $request->stock_level,
            ], function ($value) {
                return $value !== null && $value !== '';
            }));
        }

        $this->attachInventoryReport($db_records);

        $db_results = [
            'vue_data' => [
                'vue_list_tab_all' => [
                    'paginator' => paginator_vue_data($db_records),
                    'summary' => $this->reportSummary($request),
                ],
            ],
            'response_values' => [
                '#tab_search' => '0',
            ],
        ];

        if ($request->tab_search || $request->pgajax === 'y') {
            return response()->json($db_results);
        }

        return view('stock.stock-item-list.ajax-tabs.tab-all', compact('db_results', 'tab_name'));
    }

    public function exportPdf(Request $request)
    {
        $records = $this->reportRecords($request);
        $fileName = 'stock_item_report_'.date('Y_m_d_H_i_s').'.pdf';

        $pdf = Pdf::loadView('stock.stock-item-list.stock-item-report-pdf', [
            'records' => $records,
            'summary' => $this->reportSummary($request),
            'filters' => $this->exportFilterLabels($request),
            'totals' => [
                'stock_in' => round((float) $records->sum('stock_in_total'), 2),
                'stock_out' => round((float) $records->sum('stock_out_total'), 2),
                'inventory_value' => round((float) $records->sum('inventory_value'), 2),
            ],
        ])->setPaper('a4', 'landscape');

        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        $fileName = 'stock_item_report_'.date('Y_m_d_H_i_s').'.xlsx';

        return Excel::download(new StockItemReportExport($this->reportRecords($request)), $fileName);
    }

    private function reportRecords(Request $request)
    {
        $query = StockItem::query()
            ->with(['category.parent', 'brand', 'stockUom'])
            ->orderByDesc('item_id');

        $this->applyListFilters($query, $request, true);
        $records = $query->get();
        $this->attachInventoryReport($records, false);

        return $records;
    }

    private function exportFilterLabels(Request $request): array
    {
        $status = match ((string) $request->input('status', '')) {
            '1' => __('Active'),
            '0' => __('Inactive'),
            default => __('All'),
        };
        $sales = match ((string) $request->input('is_sales_item', '')) {
            '1' => __('Sales Items'),
            '0' => __('Non Sales Items'),
            default => __('All'),
        };
        $level = $request->filled('stock_level')
            ? $this->stockLevelLabel((string) $request->stock_level)
            : __('All');

        return [
            'search' => $request->filled('search') ? $request->search : __('All'),
            'status' => $status,
            'sales' => $sales,
            'stock_level' => $level,
        ];
    }

    private function applyListFilters($query, Request $request, bool $includeStockLevel = false): void
    {
        if ($request->filled('status')) {
            $query->where('is_active', (int) $request->status);
        }
        if ($request->filled('is_sales_item')) {
            $query->where('is_sales_item', (int) $request->is_sales_item);
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($where) use ($term) {
                $where->where('item_code', 'like', '%'.$term.'%')
                    ->orWhere('item_name', 'like', '%'.$term.'%')
                    ->orWhere('description', 'like', '%'.$term.'%');
            });
        }

        if (! $includeStockLevel || ! $request->filled('stock_level')) {
            return;
        }

        $pack = StockItem::packSizeSql();
        $low = (int) StockItem::LOW_STOCK_PACKS;

        if ($request->stock_level === 'out') {
            $query->whereRaw("current_stock < {$pack}");
        } elseif ($request->stock_level === 'low') {
            $query->whereRaw("current_stock >= {$pack}")
                ->whereRaw("current_stock < {$pack} * {$low}");
        } elseif ($request->stock_level === 'ok') {
            $query->whereRaw("current_stock >= {$pack} * {$low}");
        }
    }

    private function reportSummary(Request $request): array
    {
        $pack = StockItem::packSizeSql();
        $low = (int) StockItem::LOW_STOCK_PACKS;
        $valueSql = StockItem::inventoryValueSql();

        $query = StockItem::query();
        $this->applyListFilters($query, $request, true);

        $row = $query->selectRaw("
            COUNT(*) as items,
            COALESCE(SUM({$valueSql}), 0) as inventory_value,
            SUM(CASE WHEN current_stock < {$pack} THEN 1 ELSE 0 END) as out_of_stock,
            SUM(CASE WHEN current_stock >= {$pack} AND current_stock < {$pack} * {$low} THEN 1 ELSE 0 END) as low_stock,
            SUM(CASE WHEN current_stock >= {$pack} * {$low} THEN 1 ELSE 0 END) as in_stock
        ")->first();

        return [
            'items' => (int) ($row->items ?? 0),
            'inventory_value' => round((float) ($row->inventory_value ?? 0), 2),
            'out_of_stock' => (int) ($row->out_of_stock ?? 0),
            'low_stock' => (int) ($row->low_stock ?? 0),
            'in_stock' => (int) ($row->in_stock ?? 0),
            'low_pack_limit' => $low,
        ];
    }

    private function attachInventoryReport($db_records, bool $withHistory = true): void
    {
        $itemIds = $db_records->pluck('item_id')->filter()->values();

        $totals = collect();
        $recent = collect();

        if ($itemIds->isNotEmpty()) {
            $totals = StockMovement::query()
                ->select('item_id')
                ->selectRaw('COALESCE(SUM(stock_in), 0) as stock_in_total')
                ->selectRaw('COALESCE(SUM(stock_out), 0) as stock_out_total')
                ->selectRaw('COUNT(*) as movement_count')
                ->selectRaw('MAX(created_at) as last_movement_at')
                ->whereIn('item_id', $itemIds)
                ->groupBy('item_id')
                ->get()
                ->keyBy('item_id');

            if ($withHistory) {
                $recent = StockMovement::query()
                    ->whereIn('item_id', $itemIds)
                    ->orderByDesc('stock_movement_id')
                    ->get()
                    ->groupBy('item_id')
                    ->map(function ($rows) {
                        return $rows->take(8)->values();
                    });
            }
        }

        foreach ($db_records as $row) {
            $level = $row->stockLevelKey();
            $history = $totals->get($row->item_id);
            $lastAt = optional($history)->last_movement_at;

            $row->view_url = route('tab.stock.stock.item.details', [
                'item_id' => $row->item_id,
                'tab_name' => 'info',
            ]);
            $row->edit_url = route('tab.stock.stock.item.details', [
                'item_id' => $row->item_id,
                'tab_name' => 'edit',
            ]);
            $row->movements_url = route('tab.stock.stock.item.details', [
                'item_id' => $row->item_id,
                'tab_name' => 'stock-movements',
            ]);
            $row->packs_available = $row->packsAvailable();
            $row->stock_level = $level;
            $row->stock_level_label = $this->stockLevelLabel($level);
            $row->inventory_value = $row->inventoryValue();
            $row->stock_in_total = round((float) (optional($history)->stock_in_total ?? 0), 3);
            $row->stock_out_total = round((float) (optional($history)->stock_out_total ?? 0), 3);
            $row->movement_count = (int) (optional($history)->movement_count ?? 0);
            $row->last_movement_at = $lastAt ? date('d M Y H:i', strtotime($lastAt)) : null;
            $row->inventory_history = ($recent->get($row->item_id) ?? collect())
                ->map(function ($movement) {
                    return [
                        'stock_movement_id' => $movement->stock_movement_id,
                        'kind' => $this->movementKind($movement),
                        'stock_in' => round((float) ($movement->stock_in ?? 0), 3),
                        'stock_out' => round((float) ($movement->stock_out ?? 0), 3),
                        'description' => $movement->description,
                        'happened_at' => optional($movement->created_at)->format('d M Y H:i'),
                    ];
                })
                ->values()
                ->all();
        }
    }

    private function stockLevelLabel(string $level): string
    {
        if ($level === 'out') {
            return __('Out of stock');
        }
        if ($level === 'low') {
            return __('Low stock');
        }

        return __('In stock');
    }

    private function movementKind(StockMovement $movement): string
    {
        $type = (string) ($movement->type ?? '');
        $description = strtolower((string) ($movement->description ?? ''));

        if (str_contains($description, 'opening stock for item:')) {
            return __('Opening');
        }
        if (str_contains($description, 'stock adjustment for item:')) {
            return __('Adjustment');
        }
        if ($type === 'purchase_return') {
            return __('Return');
        }
        if ($type === 'purchase' || str_starts_with($description, 'purchase id:')) {
            return __('Purchase');
        }
        if (str_contains($description, 'item sold:')) {
            return __('Sale');
        }
        if (str_contains($description, 'service used:')) {
            return __('Service use');
        }
        if ((float) ($movement->stock_in ?? 0) > 0) {
            return __('Stock in');
        }
        if ((float) ($movement->stock_out ?? 0) > 0) {
            return __('Stock out');
        }

        return __('Movement');
    }
}
