<?php

namespace App\Http\Controllers\Vendors\VendorDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\PurchasePayment;
use App\Models\Vendor;
use App\Repositories\VendorRepository;
use App\Service\AccountingService;
use App\Service\VendorPaymentService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TabPaymentController extends Controller
{
    protected $accountingService;

    protected $vendorPaymentService;

    public function __construct(AccountingService $accountingService, VendorPaymentService $vendorPaymentService)
    {
        $this->accountingService = $accountingService;
        $this->vendorPaymentService = $vendorPaymentService;
    }

    public function index(Request $request, VendorRepository $vendor_repo, $vendor_id, $tab_name)
    {
        $vendor = Vendor::with('purchasePayment')->findOrFail($vendor_id);
        $maxPayable = $this->vendorPaymentService->getMaxPayable($vendor);

        $transactions = PurchasePayment::query()
            ->where('vendor_id', $vendor_id)
            ->whereIn('payment_mode', [
                PurchasePayment::PAYMENT_MODE_CASH,
                PurchasePayment::PAYMENT_MODE_BANK,
                PurchasePayment::PAYMENT_MODE_ONLINE,
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('payment_id')
            ->limit(50)
            ->get()
            ->map(function ($payment) {
                return [
                    'payment_id' => $payment->payment_id,
                    'purchase_id' => $payment->purchase_id,
                    'amount' => round((float) $payment->amount_taken, 2),
                    'payment_mode' => $payment->payment_mode,
                    'created_at' => optional($payment->created_at)->toDateTimeString(),
                    'payment_date' => optional($payment->created_at)->format('Y-m-d'),
                ];
            })
            ->values();

        $details_payment_vue = [
            'vendor_id' => $vendor_id,
            'tab_name' => $tab_name,
            'vendor_info' => $vendor,
            'credit_amount' => $maxPayable,
            'max_payable' => $maxPayable,
            'cash_balance' => $this->accountingService->getCashOrBankBalance('cash'),
            'bank_balance' => $this->accountingService->getCashOrBankBalance('bank'),
            'payment_date' => now()->format('Y-m-d'),
            'transactions' => $transactions,
            'voucher_links' => $this->vendorPaymentService->voucherLinksForVendor((int) $vendor_id),
            'voucher_pay_url' => route('tab.stock.stock.voucher.details', [
                'id' => 0,
                'tab_name' => 'edit',
            ]).'?vendor_id='.$vendor_id,
            'button_cancel_url' => route('tab.vendors.vendor.list'),
            'form_save_url' => route('tab.vendors.vendor.save', ['vendor_id' => $vendor_id, 'tab_name' => $tab_name]),
        ];

        return view('vendors.vendor-details.ajax-tabs.tab-payment', compact('details_payment_vue'));
    }

    public function save(Request $request, VendorRepository $vendor_repo, $vendor_id, $tab_name)
    {
        if ($request->vendor_id != $vendor_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        $response = DB::transaction(function () use ($request, $vendor_id) {
            $vendor = Vendor::findOrFail($vendor_id);
            $this->applyValidation($request, $vendor);

            return $this->saveFromRequest($request, $vendor);
        });

        return response()->json([
            'gotourl' => route('tab.vendors.vendor.details', [
                'vendor_id' => $response->vendor_id,
                'tab_name' => $tab_name,
            ]),
        ]);
    }

    private function applyValidation(Request $request, Vendor $vendor)
    {
        $maxPayable = $this->vendorPaymentService->getMaxPayable($vendor);

        if ($maxPayable < 0.01) {
            throw ValidationException::withMessages([
                'amount' => [__('This vendor has no outstanding credit balance to pay.')],
            ]);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:'.$maxPayable,
            'payment_mode' => 'required|in:cash,bank',
            'payment_date' => 'required|date',
        ], [
            'amount.max' => __('Payment amount cannot exceed the remaining credit amount (:amount).', [
                'amount' => number_format($maxPayable, 2),
            ]),
            'payment_date.required' => __('Please select a payment date.'),
        ]);
    }

    private function saveFromRequest(Request $request, Vendor $vendor)
    {
        $amount = round((float) $request->amount, 2);
        $paymentMode = strtolower((string) $request->payment_mode);
        $paymentDate = Carbon::parse($request->payment_date)->toDateString();

        $this->accountingService->validateAccountBalance($paymentMode, $amount);

        $creditPayments = PurchasePayment::where('vendor_id', $vendor->vendor_id)
            ->where('payment_mode', PurchasePayment::PAYMENT_MODE_CREDIT)
            ->where('amount_taken', '>', 0)
            ->whereNotNull('purchase_id')
            ->orderBy('created_at', 'asc')
            ->orderBy('payment_id', 'asc')
            ->get();

        $remainingAmount = $amount;

        foreach ($creditPayments as $creditPayment) {
            if ($remainingAmount <= 0) {
                break;
            }

            $paymentAmount = round(min($remainingAmount, (float) $creditPayment->amount_taken), 2);
            if ($paymentAmount < 0.01) {
                continue;
            }

            $creditPayment->amount_taken = round((float) $creditPayment->amount_taken - $paymentAmount, 2);
            $creditPayment->save();

            PurchasePayment::create([
                'purchase_id' => $creditPayment->purchase_id,
                'vendor_id' => $vendor->vendor_id,
                'amount_taken' => $paymentAmount,
                'payment_mode' => $paymentMode,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $remainingAmount = round($remainingAmount - $paymentAmount, 2);
        }

        // Fallback for legacy balances not tied to open credit purchase rows
        if ($remainingAmount >= 0.01) {
            PurchasePayment::create([
                'purchase_id' => null,
                'vendor_id' => $vendor->vendor_id,
                'amount_taken' => $remainingAmount,
                'payment_mode' => $paymentMode,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        $description = 'Manual vendor payment';

        if ($paymentMode === 'cash') {
            $this->accountingService->recordCreditCashPayment(
                $amount,
                $description,
                $paymentDate,
                $vendor->vendor_id
            );
        } else {
            $this->accountingService->recordCreditBankPayment(
                $amount,
                $description,
                $paymentDate,
                $vendor->vendor_id
            );
        }

        voco_flash()->success(__('Vendor Payment updated!'));

        return $vendor->fresh();
    }
}
