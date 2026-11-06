<?php

namespace App\Http\Controllers\Stock\StockItemDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Models\StockItemPrice;
use App\Models\StockMovement;
use App\Repositories\StockItemRepository;
use App\Service\AccountingService;
use App\Service\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabOpeningStockController extends Controller
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
                'vue_list_tab_opening_stock' => $this->prepareVueData($request, $item_id, $tab_name),
            ],
        ];

        return view('stock.stock-item-details.ajax-tabs.tab-opening-stock', compact('db_results', 'tab_name'));
    }

    public function checkPassword(Request $request, EmployeeService $employeeService)
    {
        $user = auth()->user();

        if (! $user->can('items')) {
            return response()->json([
                'valid' => false,
                'statusText' => __('You do not have permission to edit.'),
                'errorcode' => 403,
            ], 403);
        }

        $passwordError = $employeeService->validateCurrentUserPassword($request->password);
        if ($passwordError) {
            return response()->json([
                'valid' => false,
                'statusText' => $passwordError,
                'errorcode' => 401,
            ], 401);
        }

        if ($request->filled('item_id')) {
            session([
                'opening_stock_edit_unlock_id' => (int) $request->item_id,
                'opening_stock_edit_unlock_at' => now()->timestamp,
            ]);
        }

        return response()->json([
            'valid' => true,
            'statusText' => __('Validation successful.'),
            'errorcode' => 0,
        ]);
    }

    public function save(Request $request, StockItemRepository $stock_item_repo, EmployeeService $employeeService, $item_id, $tab_name)
    {
        if ($request->item_id != $item_id) {
            throw new \Exception('You are redirected from an invalid link');
        }

        $request->validate([
            'quantity' => 'required|numeric|min:0',
            'auth_password' => 'required|string',
        ], [
            'auth_password.required' => __('Password is required to save opening stock.'),
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
            $newQty = (float) $request->quantity;
            $oldQty = 0.0;
            $openStockId = $request->filled('open_stock_id') ? (int) $request->open_stock_id : null;

            if ($openStockId) {
                $oldMovement = StockMovement::where('stock_movement_id', $openStockId)
                    ->where('item_id', $item_id)
                    ->lockForUpdate()
                    ->first();

                if (! $oldMovement) {
                    throw new \Exception(__('Opening stock record not found.'));
                }

                $oldQty = (float) ($oldMovement->stock_in ?? 0);
            }

            // Adjust on-hand stock by the change in opening quantity (do not overwrite).
            $delta = $newQty - $oldQty;
            $item->current_stock = (float) $item->current_stock + $delta;
            $item->save();

            StockItemPrice::updateOrCreate(
                ['item_id' => $item->item_id],
                ['quantity' => $newQty]
            );

            if ($newQty != 0 || $openStockId) {
                $items = [[
                    'item_id' => $item_id,
                    'quantity' => $newQty,
                    'cost_price' => $item->cost_price ?? 0,
                    'selling_price' => $item->selling_price ?? 0,
                ]];

                $item_name = $item->item_name ?? 'Unknown Item';
                $description = "Opening stock for item: {$item_name}";
                $date = now()->toDateString();
                $equityAccountId = getAccount('Owner’s Capital');

                $this->accountingService->recordOpeningStock(
                    $items,
                    $description,
                    $date,
                    $equityAccountId,
                    $openStockId
                );
            }

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
                'error' => 'Failed to record opening stock: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function prepareVueData(Request $request, $item_id, $tab_name)
    {
        $openingStock = StockMovement::where('item_id', $item_id)
            ->where('description', 'like', 'Opening stock for item:%')
            ->orderBy('stock_movement_id')
            ->first();

        $item = StockItem::with('stockUom')->find($item_id);

        return [
            'item_id' => $item_id,
            'tab_name' => $tab_name,
            'opening_stock' => $openingStock,
            'uom_name' => optional($item?->stockUom)->uom_name ?? '',
            'pack_size' => $item ? $item->packSize() : 1,
            'urls' => [
                'form_save_url' => route('tab.stock.stock.item.save', ['item_id' => $item_id, 'tab_name' => $tab_name]),
                'auth_check_url' => route('opening.stock.auth-check'),
                'stock_movements_url' => route('tab.stock.stock.item.details', [
                    'item_id' => $item_id,
                    'tab_name' => 'stock-movements',
                ]),
            ],
        ];
    }
}
