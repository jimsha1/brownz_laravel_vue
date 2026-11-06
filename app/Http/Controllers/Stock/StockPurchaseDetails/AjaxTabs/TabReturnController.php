<?php

namespace App\Http\Controllers\Stock\StockPurchaseDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\PurchasePayment;
use App\Models\StockItem;
use App\Models\StockItemPrice;
use App\Models\StockMovement;
use App\Models\StockPurchase;
use App\Models\StockPurchaseItem;
use App\Repositories\StockPurchaseRepository;
use App\Service\AccountingService;
use App\Service\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TabReturnController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index(Request $request, StockPurchaseRepository $stock_purchase_repo, $purchase_id, $tab_name)
    {
        if (! auth()->user()->can('purchase_return')) {
            abort(403, __('You do not have permission to process purchase returns.'));
        }

        $stock_purchase_info = $stock_purchase_repo->model()
            ->with('createdUser')
            ->with('updatedUser')
            ->with('product_rows')
            ->with('payment_rows')
            ->with('vendor')
            ->findOrFail($purchase_id);

        if ($stock_purchase_info->parent_id || $stock_purchase_info->purchase_type === 'refund') {
            voco_flash()->error(__('Cannot create a return for a return entry.'));
            if (\Request::ajax()) {
                return response()->json([
                    'gotourl' => route('tab.stock.stock.purchase.list'),
                ]);
            }

            return redirect()->route('tab.stock.stock.purchase.list');
        }

        $purchaseItems = [];
        $purchasedItemIds = [];
        $returnRows = [];

        foreach ($stock_purchase_info->product_rows as $item) {
            $availableQty = max(0, (float) $item->quantity - (float) ($item->refunded_qty ?? 0));
            if ($availableQty < 0.01) {
                continue;
            }

            $purchasedItemIds[] = $item->item_id;

            $purchaseItems[$item->item_id] = [
                'item_id' => $item->item_id,
                'quantity' => (float) $item->quantity,
                'available_quantity' => $availableQty,
                'cost_price' => (float) $item->cost_price,
                'rate_incl_tax' => $item->rate_incl_tax !== null
                    ? (float) $item->rate_incl_tax
                    : round((float) $item->cost_price * (1 + ((float) $item->tax_percent / 100)), 2),
                'tax_percent' => (float) $item->tax_percent,
                'discount' => (float) $item->discount,
                'discount_percent' => $item->discount_percent !== null ? (float) $item->discount_percent : null,
                'hsn_code' => $item->hsn_code,
            ];

            $returnRows[] = [
                'purchase_item_id' => $item->purchase_item_id,
                'item_id' => $item->item_id,
                'hsn_code' => $item->hsn_code,
                'quantity' => 0,
                'purchase_quantity' => $availableQty,
                'original_quantity' => (float) $item->quantity,
                'original_discount' => (float) $item->discount,
                'cost_price' => (float) $item->cost_price,
                'rate_incl_tax' => $item->rate_incl_tax !== null
                    ? (float) $item->rate_incl_tax
                    : round((float) $item->cost_price * (1 + ((float) $item->tax_percent / 100)), 2),
                'tax' => (float) $item->tax_percent,
                'discount' => 0,
                'discount_percent' => $item->discount_percent !== null ? (float) $item->discount_percent : null,
                'pricing_mode' => $item->discount_percent === null ? 'legacy' : 'gst',
                'total' => 0,
            ];
        }

        if (empty($returnRows)) {
            voco_flash()->error(__('No remaining quantity available to return for this purchase.'));
            if (\Request::ajax()) {
                return response()->json([
                    'gotourl' => route('tab.stock.stock.purchase.list'),
                ]);
            }

            return redirect()->route('tab.stock.stock.purchase.list');
        }

        $purchasedItemsDropdown = StockItem::query()
            ->leftJoin('unit_of_measurements', 'unit_of_measurements.uom_id', '=', 'stock_items.stock_uom_id')
            ->whereIn('stock_items.item_id', $purchasedItemIds)
            ->where('stock_items.is_active', 1)
            ->select(
                'stock_items.item_name as text',
                'stock_items.item_id as id',
                'stock_items.cost_price as cost_price',
                'stock_items.tax_percent as tax',
                'stock_items.hsn_code as hsn_code',
                'unit_of_measurements.uom_name as uom_name'
            )
            ->get();

        // Prefill settlement rows from original purchase payments (amounts filled by UI from return total)
        $originalPayments = [];
        $paymentRows = [];
        $originalPaymentTotal = 0.0;

        if ($stock_purchase_info->payment_rows && $stock_purchase_info->payment_rows->count() > 0) {
            foreach ($stock_purchase_info->payment_rows as $row) {
                $amount = abs((float) $row->amount_taken);
                if ($amount < 0.01) {
                    continue;
                }
                $originalPaymentTotal += $amount;
                $originalPayments[] = [
                    'payment_mode' => $row->payment_mode,
                    'amount' => $amount,
                ];
            }
        }

        if ($originalPaymentTotal > 0) {
            foreach ($originalPayments as $payment) {
                $paymentRows[] = [
                    'payment_id' => null,
                    'amount_taken' => 0,
                    'payment_mode' => $payment['payment_mode'],
                    'share' => round($payment['amount'] / $originalPaymentTotal, 6),
                    'disabled' => false,
                ];
            }
        }

        if (empty($paymentRows)) {
            $paymentRows = [[
                'payment_id' => null,
                'amount_taken' => 0,
                'payment_mode' => $stock_purchase_info->payment_method ?: 'credit',
                'share' => 1,
                'disabled' => false,
            ]];
        }

        $originalCredit = collect($originalPayments)
            ->filter(fn ($p) => strtolower((string) $p['payment_mode']) === 'credit')
            ->sum('amount');

        $alreadyReturnedCredit = abs((float) PurchasePayment::query()
            ->whereIn('purchase_id', function ($q) use ($purchase_id) {
                $q->select('purchase_id')
                    ->from('stock_purchases')
                    ->where('parent_id', $purchase_id)
                    ->where('purchase_type', 'refund');
            })
            ->where('payment_mode', PurchasePayment::PAYMENT_MODE_CREDIT)
            ->sum('amount_taken'));

        $details_return_vue = [
            'purchase_id' => $purchase_id,
            'tab_name' => $tab_name,
            'stock_purchase_info' => $stock_purchase_info,
            'purchase_items' => $purchaseItems,
            'return_date' => now()->format('d/m/Y'),
            'max_credit_settlement' => max(0, round($originalCredit - $alreadyReturnedCredit, 2)),
            'urls' => [
                'button_cancel_url' => route('tab.stock.stock.purchase.list'),
                'form_save_url' => route('tab.stock.stock.purchase.save', ['purchase_id' => $purchase_id, 'tab_name' => $tab_name]),
            ],
            'dropdowns' => [
                'vendors' => \Facades\App\Models\Vendor::vendorsDropdownJson(),
                'stock_items' => $purchasedItemsDropdown,
            ],
            'return_rows' => $returnRows,
            'payment_rows' => $paymentRows,
        ];

        return view('stock.stock-purchase-details.ajax-tabs.tab-return', compact('details_return_vue'));
    }

    public function save(Request $request, StockPurchaseRepository $stock_purchase_repo, EmployeeService $employeeService, $purchase_id, $tab_name)
    {
        if (! auth()->user()->can('purchase_return')) {
            abort(403, __('You do not have permission to process purchase returns.'));
        }

        if ($request->purchase_id != $purchase_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        $request->validate([
            'auth_password' => 'required|string',
        ], [
            'auth_password.required' => __('Password is required to save this return.'),
        ]);

        $passwordError = $employeeService->validateCurrentUserPassword($request->auth_password);
        if ($passwordError) {
            return response()->json([
                'message' => $passwordError,
                'js_notifications' => ['error' => $passwordError],
                'errors' => ['auth_password' => [$passwordError]],
            ], 422);
        }

        $stock_purchase = $stock_purchase_repo->model()->findOrFail($purchase_id);

        try {
            \DB::transaction(function () use ($request, $stock_purchase) {
                return $this->saveReturnRows($request, $stock_purchase);
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'gotourl' => route('tab.stock.stock.purchase.list'),
        ]);
    }

    private function saveReturnRows(Request $request, StockPurchase $purchase)
    {
        // Re-load and lock parent purchase so concurrent returns serialize on DB too
        $purchase = StockPurchase::where('purchase_id', $purchase->purchase_id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($purchase->parent_id || $purchase->purchase_type === 'refund') {
            throw new \Exception(__('Cannot create a return for a return entry.'));
        }

        $rows = json_decode($request->return_rows, true) ?? [];
        if (empty($rows)) {
            throw new \Exception(__('No return items.'));
        }

        // Aggregate by purchase line so a paid row and a complimentary row of the
        // same product are returned separately. Rows without a line id stay grouped
        // by product, which is the single-line purchase case.
        $aggregated = [];
        foreach ($rows as $row) {
            if (empty($row['item_id'])) {
                continue;
            }
            $qty = (float) ($row['quantity'] ?? 0);
            if ($qty <= 0) {
                continue;
            }
            $lineId = (int) ($row['purchase_item_id'] ?? 0);
            $key = $lineId > 0 ? 'line:'.$lineId : 'item:'.(int) $row['item_id'];
            if (! isset($aggregated[$key])) {
                $aggregated[$key] = 0.0;
            }
            $aggregated[$key] += $qty;
        }

        if (empty($aggregated)) {
            throw new \Exception(__('No valid return items.'));
        }

        $items = [];
        $totalAmount = 0;

        foreach ($aggregated as $key => $returnQty) {
            $lineId = str_starts_with((string) $key, 'line:') ? (int) substr((string) $key, 5) : 0;
            $itemId = str_starts_with((string) $key, 'item:') ? (int) substr((string) $key, 5) : 0;
            $originalQuery = StockPurchaseItem::where('purchase_id', $purchase->purchase_id);
            if ($lineId > 0) {
                $originalQuery->where('purchase_item_id', $lineId);
            } else {
                $originalQuery->where('item_id', $itemId);
            }
            $originalItem = $originalQuery->lockForUpdate()->first();

            if (! $originalItem) {
                throw new \Exception(__('Return item does not belong to this purchase.'));
            }

            $itemId = (int) $originalItem->item_id;
            $available = (float) $originalItem->quantity - (float) ($originalItem->refunded_qty ?? 0);
            if ($returnQty > $available + 0.0001) {
                throw new \Exception(__('Return quantity exceeds available quantity.'));
            }

            $stockItem = StockItem::where('item_id', $itemId)->lockForUpdate()->first();
            $baseReturn = $stockItem ? $stockItem->baseQuantity($returnQty) : (float) $returnQty;
            if ($stockItem && (float) $stockItem->current_stock + 0.0001 < $baseReturn) {
                throw new \Exception(__('Insufficient stock on hand to return this quantity.'));
            }

            $originalQty = max((float) $originalItem->quantity, 0.0001);
            $cost = (float) $originalItem->cost_price;
            $taxP = (float) $originalItem->tax_percent;
            $accountItem = [
                'item_id' => $itemId,
                'quantity' => $returnQty,
                'cost_price' => $cost,
                'tax' => $taxP,
            ];
            if ($originalItem->discount_percent !== null) {
                $accountItem['discount_percent'] = (float) $originalItem->discount_percent;
            } else {
                $accountItem['discount'] = round(((float) $originalItem->discount) * ($returnQty / $originalQty), 2);
            }

            $line = $this->accountingService->purchaseLineFigures($accountItem);

            $items[] = [
                'originalItem' => $originalItem,
                'returnQty' => $returnQty,
                'account_item' => $accountItem,
                'line' => $line,
                'stockItem' => $stockItem,
                'base_return' => $baseReturn,
            ];
        }

        $usesGstPricing = collect($items)->contains(function ($item) {
            return array_key_exists('discount_percent', $item['account_item']);
        });
        $totals = $this->accountingService->purchaseDocumentTotals(
            array_map(fn ($item) => $item['account_item'], $items),
            $usesGstPricing
        );
        $totalAmount = (float) $totals['grand'];

        // Guard against accidental duplicate submit of the exact same return within a short window
        $recentDuplicate = StockPurchase::query()
            ->where('parent_id', $purchase->purchase_id)
            ->where('purchase_type', 'refund')
            ->where('total_purchase_amount', -abs($totalAmount))
            ->where('created_at', '>=', now()->subSeconds(15))
            ->exists();

        if ($recentDuplicate) {
            throw new \Exception(__('This purchase return was already saved. Please refresh and check the list.'));
        }

        $paymentRows = json_decode($request->payment_rows, true) ?? [];
        $paymentRows = array_values(array_filter($paymentRows, function ($payment) {
            return ! empty($payment['payment_mode']) && abs((float) ($payment['amount_taken'] ?? 0)) > 0.009;
        }));

        if (empty($paymentRows)) {
            throw new \Exception(__('Please enter return settlement payment rows that equal the return total.'));
        }

        $paymentTotal = round(array_reduce($paymentRows, function ($sum, $row) {
            return $sum + abs((float) ($row['amount_taken'] ?? 0));
        }, 0.0), 2);

        if (abs($paymentTotal - $totalAmount) > 0.01) {
            throw new \Exception(__('Return payment total must equal return grand total.'));
        }

        $creditSettlement = round(array_reduce($paymentRows, function ($sum, $row) {
            return $sum + (strtolower((string) ($row['payment_mode'] ?? '')) === 'credit'
                ? abs((float) ($row['amount_taken'] ?? 0))
                : 0);
        }, 0.0), 2);

        $originalCredit = (float) PurchasePayment::where('purchase_id', $purchase->purchase_id)
            ->where('payment_mode', PurchasePayment::PAYMENT_MODE_CREDIT)
            ->sum('amount_taken');

        $alreadyReturnedCredit = abs((float) PurchasePayment::query()
            ->whereIn('purchase_id', function ($q) use ($purchase) {
                $q->select('purchase_id')
                    ->from('stock_purchases')
                    ->where('parent_id', $purchase->purchase_id)
                    ->where('purchase_type', 'refund');
            })
            ->where('payment_mode', PurchasePayment::PAYMENT_MODE_CREDIT)
            ->sum('amount_taken'));

        $maxCredit = max(0, round($originalCredit - $alreadyReturnedCredit, 2));
        if ($creditSettlement > $maxCredit + 0.01) {
            throw new \Exception(__('Credit settlement cannot exceed remaining unpaid credit on this purchase (:max).', [
                'max' => number_format($maxCredit, 2),
            ]));
        }

        $returnDate = now()->toDateString();
        if ($request->filled('return_date')) {
            try {
                $returnDate = Carbon::createFromFormat('d/m/Y', $request->return_date)->toDateString();
            } catch (\Exception $e) {
                try {
                    $returnDate = Carbon::parse($request->return_date)->toDateString();
                } catch (\Exception $e2) {
                    $returnDate = now()->toDateString();
                }
            }
        }

        $primaryMode = $paymentRows[0]['payment_mode'] ?? 'credit';

        $refund = StockPurchase::create([
            'vendor_id' => $purchase->vendor_id,
            'parent_id' => $purchase->purchase_id,
            'purchase_type' => 'refund',
            'purchase_date' => $returnDate,
            'purchase_invoice_no' => 'RET-'.$purchase->purchase_invoice_no.'-'.now()->format('Hisu'),
            'refund_invoice_no' => $purchase->purchase_invoice_no,
            'purchase_description' => 'Purchase Return - '.$purchase->purchase_invoice_no,
            'payment_method' => $primaryMode,
            'total_purchase_amount' => -abs($totalAmount),
            'round_off' => $totals['round_off'],
        ]);

        foreach ($items as $item) {
            $itemId = $item['originalItem']->item_id;
            $returnQty = $item['returnQty'];

            StockPurchaseItem::create([
                'purchase_id' => $refund->purchase_id,
                'refunded_purchase_id' => $purchase->purchase_id,
                'item_id' => $itemId,
                'hsn_code' => $item['originalItem']->hsn_code,
                'quantity' => $returnQty,
                'cost_price' => $item['account_item']['cost_price'],
                'rate_incl_tax' => $item['originalItem']->rate_incl_tax,
                'tax_percent' => $item['account_item']['tax'],
                'tax_amount' => $item['line']['tax'],
                'discount' => $item['line']['discount'],
                'discount_percent' => $item['line']['discount_percent'],
                'purchase_total' => $item['line']['payable'],
                'status' => 'refunded',
            ]);

            $item['originalItem']->increment('refunded_qty', $returnQty);
            $item['originalItem']->refresh();

            if ((float) $item['originalItem']->refunded_qty >= (float) $item['originalItem']->quantity) {
                $item['originalItem']->update(['status' => 'refunded']);
            }

            $stockItem = $item['stockItem'] ?: StockItem::where('item_id', $itemId)->first();
            $baseReturn = (float) ($item['base_return'] ?? $returnQty);
            if ($stockItem) {
                DB::table('stock_items')->where('item_id', $itemId)->update([
                    'current_stock' => StockItem::sqlAdd('current_stock', -$baseReturn),
                ]);

                StockMovement::create([
                    'item_id' => $itemId,
                    'stock_in' => 0,
                    'stock_out' => $baseReturn,
                    'description' => 'Purchase Return - Purchase ID: '.$purchase->purchase_id.', Refund ID: '.$refund->purchase_id,
                    'vendor_id' => $purchase->vendor_id,
                    'type' => 'purchase_return',
                ]);
            }

            $stockItemPrice = StockItemPrice::where('item_id', $itemId)->first();
            if ($stockItemPrice) {
                DB::table('stock_item_prices')->where('item_id', $itemId)->update([
                    'quantity' => StockItem::sqlAdd('quantity', -$baseReturn),
                ]);
            }
        }

        $returnItemsForAccounting = array_map(function ($item) {
            return $item['account_item'];
        }, $items);

        foreach ($paymentRows as $payment) {
            PurchasePayment::create([
                'purchase_id' => $refund->purchase_id,
                'vendor_id' => $purchase->vendor_id,
                'amount_taken' => -abs((float) $payment['amount_taken']),
                'payment_mode' => $payment['payment_mode'] ?? 'credit',
            ]);
        }

        $group = $this->accountingService->recordPurchaseReturnWithPayments(
            $purchase->vendor_id,
            $returnItemsForAccounting,
            $paymentRows,
            'Purchase Return - '.$purchase->purchase_invoice_no,
            $returnDate,
            (float) $totals['round_off']
        );

        if ($group) {
            $refund->transaction_group_id = $group->id;
            $refund->save();
        }

        voco_flash()->success(__('Purchase return processed.'));

        return $purchase;
    }
}
