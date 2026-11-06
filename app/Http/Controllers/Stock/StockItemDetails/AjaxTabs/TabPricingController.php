<?php

namespace App\Http\Controllers\Stock\StockItemDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockItem;
use App\Repositories\StockItemRepository;
use App\Models\StockItemPrice;
use App\Service\AccountingService;
use Validator;
use Auth;
use Illuminate\Support\Facades\DB;

class TabPricingController extends Controller
{
    private AccountingService $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }
    public function index(Request $request, StockItemRepository $stock_item_repo, $item_id, $tab_name)
    {

        $db_results = [
            // Send some data for VueJs
            'vue_data' => [
                'vue_list_tab_pricing' => $this->prepareVueData($request, $item_id, $tab_name),
            ],
        ];

        return view('stock.stock-item-details.ajax-tabs.tab-pricing', compact('db_results', 'tab_name'));
    }

    public function save(Request $request, StockItemRepository $stock_item_repo, $item_id, $tab_name)
    {

        $priceId = $request->price_id;

        if ($request->item_id != $item_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        try {
            DB::beginTransaction();

            // Save stock item price
            $item_price = StockItemPrice::updateOrCreate(
                ['price_id' => $request->price_id],
                [
                    'item_id' => $request->item_id,
                    'quantity' => $request->quantity,
                    'cost_price' => $request->cost_price,
                    'selling_price' => $request->selling_price
                ]
            );

            // Save stock item
            $stock_item = StockItem::updateOrCreate(
                ['item_id' => $request->item_id],
                [
                    'cost_price' => $request->cost_price,
                    'selling_price' => $request->selling_price,
                    'current_stock' => $request->quantity,
                ]
            );
            // Prepare items for opening stock
            $items = [
                [
                    'item_id' => $item_id,
                    'quantity' => $request->quantity,
                    'cost_price' => $request->cost_price,
                    'selling_price' => $request->selling_price,
                ]
            ];

            if ((float) $request->quantity != 0) {
                $item = StockItem::findOrFail($item_id);

                $item_name = $item->item_name ?? 'Unknown Item';
                $description = "Opening stock for item: {$item_name}";
                $date = now()->toDateString();
                $equityAccountId = getAccount('Owner’s Capital');

                // Record opening stock
                $this->accountingService->recordOpeningStock($items, $description, $date, $equityAccountId, $priceId);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'gotourl' => route('tab.stock.stock.item.details', [
                    'item_id' => $item_id,
                    'tab_name' => $tab_name,
                ])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Failed to record opening stock: ' . $e->getMessage()
            ], 500);
        }
    }

    private function prepareVueData(Request $request, $item_id, $tab_name)
    {
        $db_records = StockItemPrice::where('item_id', $item_id)
            ->orderBy('selling_price')
            ->get();

        // Add additional properties for the records
        foreach ($db_records as $row) {
            // $row->edit_url = route('tab.stock.stock.item.price.details', ['price_id' => $row->price_id, 'tab_name' => 'edit']);
        }

        return [
            'item_id' => $item_id,
            'tab_name' => $tab_name,
            'price_list' => $db_records,
            'urls' => [
                'form_save_url' => route('tab.stock.stock.item.save', ['item_id' => $item_id, 'tab_name' => $tab_name]),
            ]
        ];
    }
}
