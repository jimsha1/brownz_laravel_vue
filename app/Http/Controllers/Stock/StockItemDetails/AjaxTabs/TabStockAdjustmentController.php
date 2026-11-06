<?php

namespace App\Http\Controllers\Stock\StockItemDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Models\StockMovement;
use App\Repositories\StockItemRepository;
use App\Service\AccountingService;
use App\Service\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabStockAdjustmentController extends Controller
{
    private AccountingService $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index(Request $request, StockItemRepository $stock_item_repo, $item_id, $tab_name)
    {
        $db_results = [
            'vue_data' => [
                'vue_list_tab_stock_adjustment' => $this->prepareVueData($item_id, $tab_name),
            ],
        ];

        return view('stock.stock-item-details.ajax-tabs.tab-stock-adjustment', compact('db_results', 'tab_name'));
    }

    public function save(Request $request, StockItemRepository $stock_item_repo, EmployeeService $employeeService, $item_id, $tab_name)
    {
        if ($request->item_id != $item_id) {
            throw new \Exception('You are redirected from an invalid link');
        }

        $request->validate([
            'direction' => 'required|in:increase,decrease',
            'quantity' => 'required|numeric|gt:0',
            'reason' => 'required|string|max:255',
            'auth_password' => 'required|string',
        ], [
            'direction.required' => __('Choose increase or decrease.'),
            'quantity.gt' => __('Adjustment quantity must be greater than zero.'),
            'reason.required' => __('A reason is required for stock adjustment.'),
            'auth_password.required' => __('Password is required to save stock adjustment.'),
        ]);

        $passwordError = $employeeService->validateCurrentUserPassword($request->auth_password);
        if ($passwordError) {
            return response()->json([
                'success' => false,
                'message' => $passwordError,
                'js_notifications' => ['error' => $passwordError],
                'errors' => ['auth_password' => [$passwordError]],
            ], 422);
        }

        try {
            DB::beginTransaction();

            $item = StockItem::where('item_id', $item_id)->lockForUpdate()->findOrFail($item_id);
            $qty = (float) $request->quantity;
            $isIncrease = $request->direction === 'increase';
            $current = (float) ($item->current_stock ?? 0);

            if (! $isIncrease && ($current + 0.0001) < $qty) {
                throw new \Exception(__('Cannot decrease more than the current stock (:stock).', [
                    'stock' => $current,
                ]));
            }

            $item->current_stock = $isIncrease ? ($current + $qty) : ($current - $qty);
            $item->save();

            $itemName = $item->item_name ?? 'Unknown Item';
            $reason = trim((string) $request->reason);
            $directionLabel = $isIncrease ? 'increase' : 'decrease';
            $description = "Stock adjustment for item: {$itemName} ({$directionLabel}): {$reason}";

            $this->accountingService->recordStockAdjustment(
                (int) $item_id,
                $qty,
                $request->direction,
                $description,
                now()->toDateString(),
                (float) ($item->cost_price ?? 0),
                (float) ($item->selling_price ?? 0)
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'gotourl' => route('tab.stock.stock.item.details', [
                    'item_id' => $item_id,
                    'tab_name' => $tab_name,
                ]),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'js_notifications' => ['error' => $e->getMessage()],
                'error' => 'Failed to record stock adjustment: '.$e->getMessage(),
            ], 422);
        }
    }

    private function prepareVueData($item_id, $tab_name)
    {
        $item = StockItem::with('stockUom')->findOrFail($item_id);

        $adjustments = StockMovement::where('item_id', $item_id)
            ->where('description', 'like', 'Stock adjustment for item:%')
            ->orderByDesc('stock_movement_id')
            ->limit(25)
            ->get();

        return [
            'item_id' => $item_id,
            'tab_name' => $tab_name,
            'current_stock' => (float) ($item->current_stock ?? 0),
            'item_name' => $item->item_name,
            'uom_name' => optional($item->stockUom)->uom_name ?? '',
            'pack_size' => $item->packSize(),
            'adjustments' => $adjustments,
            'urls' => [
                'form_save_url' => route('tab.stock.stock.item.save', [
                    'item_id' => $item_id,
                    'tab_name' => $tab_name,
                ]),
                'stock_movements_url' => route('tab.stock.stock.item.details', [
                    'item_id' => $item_id,
                    'tab_name' => 'stock-movements',
                ]),
            ],
        ];
    }
}
