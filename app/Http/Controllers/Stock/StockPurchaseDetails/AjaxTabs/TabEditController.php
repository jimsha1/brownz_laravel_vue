<?php

namespace App\Http\Controllers\Stock\StockPurchaseDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockItem;
use App\Models\StockItemPrice;
use App\Models\StockMovement;
use App\Models\StockPurchase;
use App\Models\StockPurchaseEditLog;
use App\Models\StockPurchaseItem;
use App\Models\StockPurchaseItemTrack;
use App\Models\StockPurchaseTrack;
use App\Models\Transaction;
use App\Models\Vendor;
use App\Repositories\Base\BaseRepository;
use App\Repositories\StockPurchaseRepository;
use App\Service\AccountingService;
use App\Service\EmployeeService;
use Facades\App\Models\PurchasePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TabEditController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index(Request $request, StockPurchaseRepository $stock_purchase_repo, $purchase_id, $tab_name)
    {
        $stock_purchase_info = $this->findById($purchase_id, $stock_purchase_repo);

        // For new purchase, preview the next auto-generated Purchase Ref.No without reserving it yet
        if ($purchase_id == 0) {
            $previewDate = $request->input('purchase_date')
                ? Carbon::createFromFormat('d/m/Y', $request->input('purchase_date'))->toDateString()
                : now()->toDateString();
            $stock_purchase_info->purchase_ref_no = StockPurchase::getNextPurchaseRefNo(false, $previewDate);
        }

        // Existing purchases require purchase_edit permission
        if ($purchase_id > 0 && ! auth()->user()->can('purchase_edit')) {
            abort(403, __('You do not have permission to edit purchases.'));
        }

        // Prevent editing if this purchase is a return entry (has parent_id or purchase_type = 'refund')
        if ($stock_purchase_info->parent_id || $stock_purchase_info->purchase_type === 'refund') {
            voco_flash()->error(__('Return entries cannot be edited.'));
            if (\Request::ajax()) {
                return response()->json([
                    'gotourl' => route('tab.stock.stock.purchase.list'),
                ]);
            }

            return redirect()->route('tab.stock.stock.purchase.list');
        }

        // Prevent editing parent purchases that already have returns (stock/GL would corrupt)
        if ($purchase_id > 0 && $this->purchaseHasReturns($purchase_id, $stock_purchase_info)) {
            voco_flash()->error(__('This purchase has returns and cannot be edited. Process further changes via Purchase Return.'));
            if (\Request::ajax()) {
                return response()->json([
                    'gotourl' => route('tab.stock.stock.purchase.list'),
                ]);
            }

            return redirect()->route('tab.stock.stock.purchase.list');
        }

        // Prepare the product_rows data for Vue
        $productRows = $stock_purchase_info->product_rows->map(function ($row) {
            $isLegacy = $row->discount_percent === null;
            $qty = (float) $row->quantity;
            $rate = (float) $row->cost_price;
            $tax = (float) $row->tax_percent;
            $gross = $qty * $rate;
            $discountPercent = $isLegacy
                ? ($gross > 0 ? round(((float) $row->discount / $gross) * 100, 2) : 0)
                : (float) $row->discount_percent;
            $rateIncl = $row->rate_incl_tax;
            if ($rateIncl === null) {
                $rateIncl = round($rate * (1 + ($tax / 100)), 2);
            }

            return [
                'purchase_item_id' => $row->purchase_item_id,
                'item_id' => $row->item_id,
                'hsn_code' => $row->hsn_code ?: optional($row->stockItem)->hsn_code,
                'quantity' => $row->quantity,
                'rate_incl_tax' => $rateIncl,
                'cost_price' => $row->cost_price,
                'tax' => $row->tax_percent,
                'discount_percent' => $discountPercent,
                'discount' => $row->discount,
                'pricing_mode' => $isLegacy ? 'legacy' : 'gst',
                'total' => $row->purchase_total,
            ];
        })->toArray();

        $stock_purchase_info->freight_amount = (float) ($stock_purchase_info->freight_amount ?? 0);
        $stock_purchase_info->freight_tax_percent = (float) ($stock_purchase_info->freight_tax_percent ?? 0);

        $paymentRows = $stock_purchase_info->payment_rows->map(function ($row) {
            return [
                'payment_id' => $row->payment_id,
                'amount_taken' => $row->amount_taken,
                'payment_mode' => $row->payment_mode,
            ];
        })->toArray();

        $details_edit_vue = [
            'stock_purchase_data' => [
                'vendor_id' => $stock_purchase_info->vendor_id,
                'purchase_date' => $stock_purchase_info->purchase_date,
                'purchase_invoice_no' => $stock_purchase_info->purchase_invoice_no,
                'purchase_description' => $stock_purchase_info->purchase_description,
                'payment_method' => $stock_purchase_info->payment_method,
            ],
            'purchase_id' => $purchase_id,
            'tab_name' => $tab_name,
            'stock_purchase_info' => $stock_purchase_info,
            'urls' => [
                'button_cancel_url' => route('tab.stock.stock.purchase.list'),
                'form_save_url' => route('tab.stock.stock.purchase.save', ['purchase_id' => $purchase_id, 'tab_name' => $tab_name]),

            ],
            'dropdowns' => [
                'vendors' => \Facades\App\Models\Vendor::vendorsDropdownJson(),
                'stock_items' => \Facades\App\Models\StockItem::stockItemDropdownJson(),
            ],
            'product_rows' => $productRows,
            'return_rows' => [], // Initialize return_rows as empty array
            'payment_rows' => $paymentRows,
        ];

        return view('stock.stock-purchase-details.ajax-tabs.tab-edit', compact('details_edit_vue'));
    }

    public function save(Request $request, StockPurchaseRepository $stock_purchase_repo, EmployeeService $employeeService, $purchase_id, $tab_name)
    {

        if ($request->purchase_id != $purchase_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        if ($purchase_id > 0 && ! auth()->user()->can('purchase_edit')) {
            abort(403, __('You do not have permission to edit purchases.'));
        }

        // Fetch db record for update, or create a new model for insert
        $stock_purchase = $this->findById($purchase_id, $stock_purchase_repo);

        // Prevent editing if this purchase is a return entry (has parent_id or purchase_type = 'refund')
        if ($stock_purchase->parent_id || $stock_purchase->purchase_type === 'refund') {
            throw new \Exception(__('Return entries cannot be edited.'));
        }

        if ($purchase_id > 0 && $this->purchaseHasReturns($purchase_id, $stock_purchase)) {
            throw new \Exception(__('This purchase has returns and cannot be edited.'));
        }

        // Apply validation
        $this->applyValidation($request);

        // Editing an existing purchase requires the current user's password and an edit reason
        $editReason = trim((string) $request->input('edit_reason', ''));
        if ($purchase_id > 0) {
            $request->validate([
                'auth_password' => 'required|string',
                'edit_reason' => 'required|string|max:1000',
            ], [
                'auth_password.required' => __('Password is required to update this purchase.'),
                'edit_reason.required' => __('Please enter a reason for this edit.'),
                'edit_reason.max' => __('Edit reason cannot exceed 1000 characters.'),
            ]);

            $passwordError = $employeeService->validateCurrentUserPassword($request->auth_password);
            if ($passwordError) {
                return response()->json([
                    'message' => $passwordError,
                    'js_notifications' => ['error' => $passwordError],
                    'errors' => ['auth_password' => [$passwordError]],
                ], 422);
            }
        }

        try {

            $response = \DB::transaction(function () use ($request, $stock_purchase, $purchase_id, $editReason) {
                $saved = $this->saveFromRequest($request, $stock_purchase);

                if ($purchase_id > 0 && $editReason !== '') {
                    StockPurchaseEditLog::create([
                        'purchase_id' => $saved->purchase_id,
                        'edit_reason' => $editReason,
                        'edited_by' => Auth::id(),
                    ]);
                }

                return $saved;
            });

            return response()->json([
                'gotourl' => route('tab.stock.stock.purchase.list'),
            ]);

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

    public function checkPassword(Request $request)
    {
        $user = auth()->user();
        $purpose = strtolower((string) $request->input('purpose', 'edit'));
        $permission = $purpose === 'return' ? 'purchase_return' : 'purchase_edit';

        if (! $user->can($permission)) {
            return response()->json([
                'valid' => false,
                'statusText' => $purpose === 'return'
                    ? __('You do not have permission to return.')
                    : __('You do not have permission to edit.'),
                'errorcode' => 403,
            ], 403);
        }

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'valid' => false,
                'statusText' => __('Incorrect password.'),
                'errorcode' => 401,
            ], 401);
        }

        return response()->json([
            'valid' => true,
            'statusText' => __('Validation successful.'),
            'errorcode' => 0,
        ]);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function applyValidation(Request $request)
    {
        // For return tab, skip some validations
        if ($request->tab_name === 'return') {
            $request->validate([
                'purchase_id' => 'required',
            ]);
        } else {
            $request->validate([
                'vendor_id' => 'required',
                'purchase_date' => 'required',
                'purchase_description' => 'required',
                // 'purchase_invoice_no' => [
                //     'required',
                //     'regex:/^[A-Za-z0-9]+$/',
                //     Rule::unique('stock_purchases', 'purchase_invoice_no')->ignore($request->purchase_id, 'purchase_id')
                // ],

                'purchase_invoice_no' => [
                    'required',
                    'regex:/^[A-Za-z0-9\s\-\/]+$/',
                    Rule::unique('stock_purchases', 'purchase_invoice_no')
                        ->ignore($request->purchase_id, 'purchase_id'),
                ],
                'freight_amount' => 'nullable|numeric|min:0',
                'freight_tax_percent' => 'nullable|numeric|min:0|max:100',
            ], [
                'purchase_invoice_no.regex' => 'The invoice number may only contain letters, numbers, spaces, hyphens (-), and forward slashes (/).',

            ]);
        }
    }

    private function saveFromRequest(Request $request, StockPurchase $stock_purchase)
    {

        $paymentRows = json_decode($request->payment_rows, true);

        // dd($request->all());
        $oldPaymentMethod = $stock_purchase->payment_method;

        // Update stock_purchase
        $stock_purchase->vendor_id = $request->vendor_id;
        $stock_purchase->purchase_date = Carbon::createFromFormat('d/m/Y', $request->purchase_date);
        $stock_purchase->purchase_invoice_no = $request->purchase_invoice_no;
        $stock_purchase->purchase_description = $request->purchase_description;
        $stock_purchase->payment_method = $request->payment_method ?? null;
        if (! isset($stock_purchase->total_purchase_amount)) {
            $stock_purchase->total_purchase_amount = 0;
        }
        $stock_purchase->save();

        // Reverse the **old** accounting entries first so edit can reuse restored cash/bank
        if ($stock_purchase->transaction_group_id) {

            // Store old data in track tables before update
            $this->storeTrackData($stock_purchase);

            $this->accountingService->reverseTransactionGroup($stock_purchase->transaction_group_id);

            $this->accountingService->reverseInventoryTransaction($stock_purchase);

            $oldPayments = PurchasePayment::where('purchase_id', $stock_purchase->purchase_id)->get();
            if ($oldPayments->where('payment_mode', 'credit')->sum('amount_taken') > 0) {
                $this->accountingService->reverseVendorBalance($stock_purchase);
            }
        }

        if (is_array($paymentRows)) {
            $this->accountingService->validateOutgoingPaymentRows($paymentRows);
        }

        // Decode product_rows
        $productRows = json_decode($request->product_rows, true);

        $accountItems = [];

        if (is_array($productRows)) {

            $itemIds = collect($productRows)->pluck('item_id')->filter();
            $currentCosts = StockItem::whereIn('item_id', $itemIds)->pluck('cost_price', 'item_id');
            $existingItems = StockPurchaseItem::where('purchase_id', $stock_purchase->purchase_id)
                ->get()
                ->keyBy(function ($item) {
                    return (int) $item->purchase_item_id;
                });

            // Take every saved line back out once. A later row of the same product must not
            // reverse the line just written above it.
            foreach ($existingItems as $oldItem) {
                $this->adjustItemStock(
                    (int) $oldItem->item_id,
                    (float) $oldItem->quantity,
                    false,
                    'Reversal for Purchase Edit ID: '.$stock_purchase->purchase_id,
                    $stock_purchase->vendor_id
                );
            }

            $keptIds = [];
            $usedLineIds = [];
            $costAdjusted = [];

            foreach ($productRows as $row) {
                if (empty($row['item_id'])) {
                    continue;
                }

                $itemId = (int) $row['item_id'];
                $stockItemForQty = $this->adjustItemStock(
                    $itemId,
                    (float) $row['quantity'],
                    true,
                    'Purchase ID: '.$stock_purchase->purchase_id,
                    $request->vendor_id
                );

                $discountPercent = round((float) ($row['discount_percent'] ?? 0), 4);
                $isComplimentary = ($row['pricing_mode'] ?? 'gst') !== 'legacy' && $discountPercent >= 100;
                $newCost = (float) $row['cost_price'];
                $existingCost = (float) ($currentCosts[$itemId] ?? 0);

                if (! $isComplimentary && empty($costAdjusted[$itemId]) && $newCost > $existingCost) {
                    $averageCost = round(($existingCost + $newCost) / 2, 2);
                    $stockItem = StockItem::where('item_id', $itemId)->first();
                    if ($stockItem) {
                        $stockItem->prev_cost_price = $existingCost;
                        $stockItem->cost_price = $averageCost;
                        $stockItem->save();
                    }
                    $stockItemPrice = StockItemPrice::where('item_id', $itemId)->first();
                    if ($stockItemPrice) {
                        $stockItemPrice->prev_cost_price = $existingCost;
                        $stockItemPrice->cost_price = $averageCost;
                        $stockItemPrice->save();
                    }
                    $inventoryItem = InventoryItem::where('item_id', $itemId)->first();
                    if ($inventoryItem) {
                        $inventoryItem->prev_cost_price = $existingCost;
                        $inventoryItem->cost_price = $averageCost;
                        $inventoryItem->save();
                    }
                    $currentCosts[$itemId] = $averageCost;
                    $costAdjusted[$itemId] = true;
                }

                $accountItem = $this->accountItemFromRow($row);
                $line = $this->accountingService->purchaseLineFigures($accountItem);
                $accountItems[] = $accountItem;

                $hsnCode = $this->normalizeHsn($row['hsn_code'] ?? null);
                if ($hsnCode && $stockItemForQty && trim((string) $stockItemForQty->hsn_code) === '') {
                    $stockItemForQty->hsn_code = $hsnCode;
                    $stockItemForQty->save();
                }

                $rateIncl = $row['rate_incl_tax'] ?? null;
                if ($rateIncl === null || $rateIncl === '') {
                    $taxPercent = (float) ($row['tax'] ?? 0);
                    $rateIncl = round(((float) $row['cost_price']) * (1 + ($taxPercent / 100)), 2);
                }

                $attributes = [
                    'item_id' => $itemId,
                    'hsn_code' => $hsnCode,
                    'quantity' => $row['quantity'],
                    'cost_price' => $row['cost_price'],
                    'rate_incl_tax' => $rateIncl,
                    'tax_percent' => $row['tax'],
                    'tax_amount' => $line['tax'],
                    'discount' => $line['discount'],
                    'discount_percent' => $line['discount_percent'],
                    'purchase_total' => $line['payable'],
                ];

                $purchaseItemId = (int) ($row['purchase_item_id'] ?? 0);
                $existingLine = ($purchaseItemId > 0 && empty($usedLineIds[$purchaseItemId]))
                    ? $existingItems->get($purchaseItemId)
                    : null;

                if ($existingLine) {
                    $existingLine->update($attributes);
                    $keptIds[] = $existingLine->purchase_item_id;
                    $usedLineIds[$purchaseItemId] = true;
                } else {
                    $created = StockPurchaseItem::create(array_merge($attributes, [
                        'purchase_id' => $stock_purchase->purchase_id,
                    ]));
                    $keptIds[] = $created->purchase_item_id;
                }
            }

            $deleteQuery = StockPurchaseItem::where('purchase_id', $stock_purchase->purchase_id);
            if (count($keptIds) > 0) {
                $deleteQuery->whereNotIn('purchase_item_id', $keptIds);
            }
            $deleteQuery->delete();
        }
        $usesGstPricing = collect($accountItems)->contains(function ($item) {
            return array_key_exists('discount_percent', $item);
        });
        $freight = $this->freightFromRequest($request);
        $totals = $this->accountingService->purchaseDocumentTotals(
            $accountItems,
            $usesGstPricing,
            $freight['amount'],
            $freight['tax_percent']
        );
        $stock_purchase->freight_amount = $freight['amount'];
        $stock_purchase->freight_tax_percent = $freight['tax_percent'];
        $stock_purchase->total_purchase_amount = $totals['grand'];
        $stock_purchase->round_off = $totals['round_off'];
        $stock_purchase->save();

        // Handle payment_rows
        // Record accounting transaction once for the full purchase + all payment modes
        $group = null;
        if (is_array($paymentRows)) {
            $paymentTotal = round(array_reduce($paymentRows, function ($sum, $row) {
                return $sum + (float) ($row['amount_taken'] ?? 0);
            }, 0.0), 2);

            if (abs($paymentTotal - (float) $totals['grand']) > 0.05) {
                throw new \Exception(__('Paid amount must equal grand total.'));
            }

            PurchasePayment::where('purchase_id', $stock_purchase->purchase_id)->delete();

            foreach ($paymentRows as $payment) {
                PurchasePayment::create([
                    'purchase_id' => $stock_purchase->purchase_id,
                    'vendor_id' => $request->vendor_id,
                    'amount_taken' => $payment['amount_taken'],
                    'payment_mode' => $payment['payment_mode'],
                ]);
            }

            $group = $this->accountingService->recordPurchaseWithPayments(
                $request->vendor_id,
                $accountItems,
                $paymentRows,
                $request->purchase_description,
                $stock_purchase->purchase_date,
                (float) $totals['round_off'],
                (float) $totals['freight'],
                (float) $totals['freight_tax']
            );

            if ($group) {
                $stock_purchase->transaction_group_id = $group->id;
                $stock_purchase->save();
            }

            // Store only the credit amount in vendor balance (sum of credit payment rows)
            $creditAmount = array_reduce($paymentRows, function ($sum, $row) {
                $isCredit = isset($row['payment_mode'], $row['amount_taken']) && $row['payment_mode'] === 'credit';

                return $sum + ($isCredit ? (float) $row['amount_taken'] : 0);
            }, 0.0);
            if ($creditAmount > 0) {
                $vendor = Vendor::findOrFail($request->vendor_id);
                $vendor->balance += $creditAmount;
                $vendor->save();
            }
        }

        voco_flash()->success(__('Stock Purchase details created!'));

        return $stock_purchase;
    }

    private function storeTrackData(StockPurchase $stock_purchase)
    {
        // Save stock purchase track as a new record
        $purchaseTrack = StockPurchaseTrack::create([
            'purchase_id' => $stock_purchase->purchase_id,
            'vendor_id' => $stock_purchase->vendor_id,
            'purchase_date' => $stock_purchase->purchase_date,
            'purchase_invoice_no' => $stock_purchase->purchase_invoice_no,
            'purchase_description' => $stock_purchase->purchase_description,
            'payment_method' => $stock_purchase->payment_method,
            'freight_amount' => $stock_purchase->freight_amount,
            'freight_tax_percent' => $stock_purchase->freight_tax_percent,
        ]);

        // Save all stock purchase item tracks as new records
        $items = StockPurchaseItem::where('purchase_id', $stock_purchase->purchase_id)->get();

        foreach ($items as $item) {
            StockPurchaseItemTrack::create([
                'purchase_track_id' => $purchaseTrack->purchase_track_id,
                'purchase_id' => $item->purchase_id,
                'item_id' => $item->item_id,
                'hsn_code' => $item->hsn_code,
                'quantity' => $item->quantity,
                'cost_price' => $item->cost_price,
                'rate_incl_tax' => $item->rate_incl_tax,
                'tax_percent' => $item->tax_percent,
                'tax_amount' => $item->tax_amount,
                'discount' => $item->discount,
                'discount_percent' => $item->discount_percent,
                'purchase_total' => $item->purchase_total,
            ]);
        }
    }

    private function adjustItemStock(int $itemId, float $quantity, bool $incoming, string $description, $vendorId): ?StockItem
    {
        $stockItem = StockItem::where('item_id', $itemId)->first();
        $baseQty = $stockItem ? $stockItem->baseQuantity($quantity) : $quantity;
        $signedQty = $incoming ? $baseQty : -$baseQty;

        DB::table('stock_items')->where('item_id', $itemId)->update([
            'current_stock' => StockItem::sqlAdd('current_stock', $signedQty),
        ]);

        StockMovement::create([
            'item_id' => $itemId,
            'stock_in' => $incoming ? $baseQty : 0,
            'stock_out' => $incoming ? 0 : $baseQty,
            'description' => $description,
            'vendor_id' => $vendorId,
            'type' => $incoming ? 'purchase' : 'purchase_return',
        ]);

        if (StockItemPrice::where('item_id', $itemId)->exists()) {
            DB::table('stock_item_prices')->where('item_id', $itemId)->update([
                'quantity' => StockItem::sqlAdd('quantity', $signedQty),
            ]);
        }

        return $stockItem;
    }

    private function freightFromRequest(Request $request): array
    {
        $amount = round((float) $request->input('freight_amount', 0), 2);
        $percent = round((float) $request->input('freight_tax_percent', 0), 2);

        if ($amount < 0) {
            throw new \Exception(__('Freight charges cannot be negative.'));
        }
        if ($percent < 0 || $percent > 100) {
            throw new \Exception(__('Freight tax percent must be between 0 and 100.'));
        }

        return [
            'amount' => $amount,
            'tax_percent' => $percent,
        ];
    }

    private function accountItemFromRow(array $row): array
    {
        $item = [
            'item_id' => $row['item_id'],
            'quantity' => $row['quantity'] ?? 0,
            'cost_price' => $row['cost_price'] ?? 0,
            'tax' => $row['tax'] ?? 0,
        ];

        if (($row['pricing_mode'] ?? 'gst') === 'legacy') {
            $item['discount'] = $row['discount'] ?? 0;

            return $item;
        }

        $percent = round((float) ($row['discount_percent'] ?? 0), 4);
        if ($percent < 0 || $percent > 100) {
            throw new \Exception(__('Discount percent must be between 0 and 100.'));
        }

        $item['discount_percent'] = $percent;

        return $item;
    }

    private function normalizeHsn($hsn): ?string
    {
        $hsn = strtoupper(trim((string) $hsn));
        if ($hsn === '') {
            return null;
        }

        if (! preg_match('/^[A-Z0-9]{1,20}$/', $hsn)) {
            throw new \Exception(__('HSN/SAC may only contain letters and numbers, up to 20 characters.'));
        }

        return $hsn;
    }

    private function purchaseHasReturns($purchaseId, $stockPurchase = null): bool
    {
        if ($stockPurchase && $stockPurchase->relationLoaded('product_rows')) {
            $hasRefundedQty = $stockPurchase->product_rows->contains(function ($row) {
                return (float) ($row->refunded_qty ?? 0) > 0;
            });
            if ($hasRefundedQty) {
                return true;
            }
        } elseif (StockPurchaseItem::where('purchase_id', $purchaseId)->where('refunded_qty', '>', 0)->exists()) {
            return true;
        }

        return StockPurchase::where('parent_id', $purchaseId)
            ->where('purchase_type', 'refund')
            ->exists();
    }

    private function findById($id, BaseRepository $repo)
    {
        return ($id > 0) ? $repo->model()->with(['product_rows.stockItem', 'payment_rows'])->findOrFail($id) : $repo->model();
    }
}
