<?php

namespace App\Http\Controllers\Stock\StockPurchaseList\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Models\StockPurchaseItem;
use App\Repositories\StockPurchaseRepository;
use Illuminate\Http\Request;

// class TabAllController extends Controller
// {
//     public function index(Request $request, StockPurchaseRepository $stock_purchase_repo, $tab_name)
//     {
//         // Reset pagination page number, when search form submitted
//         if ($request->tab_search) {
//             $request->merge(['page' => 1]);
//         }

//         $db_records = $stock_purchase_repo->hunter()
//             ->setSearchTerm($request->search)
//             ->search();

//         // Add additional properties for the records
//         foreach ($db_records as $row) {
//             $row->edit_url = route('tab.stock.stock.purchase.details', ['purchase_id' => $row->purchase_id, 'tab_name' => 'edit']);
//         }

//         $db_results = [
//             // Send some data for VueJs
//             'vue_data' => [
//                 'vue_list_tab_all' => [
//                     'paginator' => paginator_vue_data($db_records),
//                 ],
//             ],
//             'response_values' => [
//                 '#tab_search' => '0',
//             ]
//         ];

//         if ($request->tab_search || $request->pgajax === 'y') {
//             return response()->json($db_results);
//         }

//         return view('stock.stock-purchase-list.ajax-tabs.tab-all', compact('db_results', 'tab_name'));
//     }

// }

class TabAllController extends Controller
{
    public function index(Request $request, StockPurchaseRepository $stock_purchase_repo, $tab_name)
    {
        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        if (! $request->filled('start_date') && ! $request->filled('end_date')) {
            $request->merge([
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->toDateString(),
            ]);
        }

        $db_records = $stock_purchase_repo->hunter()
            ->setSearchTerm($request->search)
            ->query(function ($query) use ($request) {
                if ($request->filled('start_date')) {
                    $query->whereDate('purchase_date', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $query->whereDate('purchase_date', '<=', $request->end_date);
                }
            })
            ->search();

        if (method_exists($db_records, 'appends')) {
            $db_records->appends(array_filter([
                'pgajax' => 'y',
                'search' => $request->search,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ], function ($value) {
                return $value !== null && $value !== '';
            }));
        }

        // Add additional properties for the records
        $canEditPermission = auth()->user()->can('purchase_edit');
        $canReturnPermission = auth()->user()->can('purchase_return');

        foreach ($db_records as $row) {
            $isRefund = $row->parent_id || $row->purchase_type === 'refund';
            $hasReturns = ! $isRefund && (
                StockPurchaseItem::where('purchase_id', $row->purchase_id)
                    ->where('refunded_qty', '>', 0)
                    ->exists()
                || \App\Models\StockPurchase::where('parent_id', $row->purchase_id)
                    ->where('purchase_type', 'refund')
                    ->exists()
            );

            $row->is_refund = $isRefund;
            $row->has_returns = $hasReturns;
            $row->can_edit = $canEditPermission && ! $isRefund && ! $hasReturns;
            $row->can_return = $canReturnPermission && ! $isRefund;

            $row->edit_url = route(
                'tab.stock.stock.purchase.details',
                [
                    'purchase_id' => $row->purchase_id,
                    'tab_name' => 'edit',
                ]
            );
            $row->return_url = route(
                'tab.stock.stock.purchase.details',
                [
                    'purchase_id' => $row->purchase_id,
                    'tab_name' => 'return',
                ]
            );
            $row->info_url = route(
                'tab.stock.stock.purchase.details',
                [
                    'purchase_id' => $row->purchase_id,
                    'tab_name' => 'info',
                ]
            );
            $row->vendor_name = optional($row->vendor)->name;

            // Fetch purchase products
            $purchaseProducts = StockPurchaseItem::where('purchase_id', $row->purchase_id)
                ->get();
                
            $itemIds = $purchaseProducts->pluck('item_id');

            $stockItems = StockItem::whereIn('item_id', $itemIds)
                ->get(['item_id', 'item_name', 'hsn_code'])
                ->keyBy('item_id');

            $products = [];

            foreach ($purchaseProducts as $product) {
                $stockItem = $stockItems->get($product->item_id);
                $taxPercent = (float) $product->tax_percent;
                $rateIncl = $product->rate_incl_tax;
                if ($rateIncl === null) {
                    $rateIncl = round((float) $product->cost_price * (1 + ($taxPercent / 100)), 2);
                }

                $products[] = [
                    'product_name' => optional($stockItem)->item_name ?? 'N/A',
                    'hsn_code' => $product->hsn_code ?: (optional($stockItem)->hsn_code ?? ''),
                    'quantity' => $product->quantity,
                    'rate_incl_tax' => round((float) $rateIncl, 2),
                    'cost_price' => round((float) $product->cost_price, 2),
                    'tax_percent' => round($taxPercent, 2),
                    'tax_amount' => round((float) $product->tax_amount, 2),
                    'discount' => round((float) $product->discount, 2),
                    'discount_percent' => $product->discount_percent !== null
                        ? round((float) $product->discount_percent, 2)
                        : null,
                    'purchase_total' => round((float) $product->purchase_total, 2),
                ];
            }

            $row->products = $products;

            $paymentRows = [];
            foreach ($row->payment_rows ?? [] as $payment) {
                $paymentRows[] = [
                    'payment_mode' => $payment->payment_mode,
                    'amount_taken' => round((float) $payment->amount_taken, 2),
                ];
            }
            $row->payment_summary = $paymentRows;
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

        return view(
            'stock.stock-purchase-list.ajax-tabs.tab-all',
            compact('db_results', 'tab_name')
        );
    }
}
