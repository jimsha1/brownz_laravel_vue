<?php

namespace App\Http\Controllers\Admin\ClientDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\BookingPayment;
use App\Models\Client;
use App\Repositories\ClientRepository;
use App\Service\AccountingService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TabPaymentController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index(Request $request, ClientRepository $client_repo, $client_id, $tab_name)
    {
        $client = Client::join('users', 'clients.client_id', '=', 'users.id')
            ->where('clients.client_id', $client_id)
            ->select('clients.*', 'users.name', 'users.mobile', 'users.email')
            ->firstOrFail();

        $creditOutstanding = $this->getCreditOutstanding($client_id);
        $balance = max(0, round((float) $client->balance, 2));
        $maxPayable = round(min($balance, $creditOutstanding > 0 ? $creditOutstanding : $balance), 2);

        $details_payment_vue = [
            'client_id' => $client_id,
            'tab_name' => $tab_name,
            'client_info' => $client,
            'balance_amount' => $balance,
            'max_payable' => $maxPayable,
            'payment_date' => now()->format('Y-m-d'),
            'button_cancel_url' => route('tab.admin.client.list'),
            'form_save_url' => route('tab.admin.client.save', ['client_id' => $client_id, 'tab_name' => $tab_name]),
        ];

        return view('admin.client-details.ajax-tabs.tab-payment', compact('details_payment_vue'));
    }

    public function save(Request $request, ClientRepository $client_repo, $client_id, $tab_name)
    {
        if ($request->client_id != $client_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        $response = DB::transaction(function () use ($request, $client_id) {
            $client = Client::findOrFail($client_id);
            $this->applyValidation($request, $client);

            return $this->saveFromRequest($request, $client);
        });

        return response()->json([
            'gotourl' => route('tab.admin.client.details', [
                'client_id' => $response->client_id,
                'tab_name' => $tab_name,
            ]),
        ]);
    }

    private function applyValidation(Request $request, Client $client)
    {
        $balance = max(0, round((float) $client->balance, 2));
        $creditOutstanding = $this->getCreditOutstanding($client->client_id);
        $maxPayable = round(min($balance, $creditOutstanding > 0 ? $creditOutstanding : $balance), 2);

        if ($maxPayable < 0.01) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'amount' => [__('This client has no outstanding credit balance to collect.')],
            ]);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:'.$maxPayable,
            'payment_mode' => 'required|in:cash,bank',
            'payment_date' => 'required|date',
        ], [
            'amount.max' => __('Payment amount cannot exceed the outstanding credit (:amount).', [
                'amount' => number_format($maxPayable, 2),
            ]),
            'payment_date.required' => __('Please select a payment date.'),
        ]);
    }

    private function saveFromRequest(Request $request, Client $client)
    {
        $amount = round((float) $request->amount, 2);
        $paymentDate = Carbon::parse($request->payment_date)->toDateString();

        $creditPayments = BookingPayment::join('bookings', 'booking_payments.booking_id', '=', 'bookings.booking_id')
            ->where('booking_payments.payment_mode', BookingPayment::PAYMENT_MODE_CREDIT)
            ->where('bookings.client_id', $client->client_id)
            ->where('booking_payments.amount_taken', '>', 0)
            ->select('booking_payments.*')
            ->orderBy('booking_payments.created_at', 'asc')
            ->get();

        $bookingPaymentMode = $request->payment_mode === 'bank' ? 'online' : $request->payment_mode;
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

            BookingPayment::create([
                'booking_id' => $creditPayment->booking_id,
                'amount_taken' => $paymentAmount,
                'amount_given' => 0,
                'payment_mode' => $bookingPaymentMode,
                'card_type' => null,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $remainingAmount = round($remainingAmount - $paymentAmount, 2);
        }

        $description = 'Manual client credit payment';

        if ($request->payment_mode === 'cash') {
            $this->accountingService->recordClientCreditCashPayment(
                $amount,
                $description,
                $paymentDate,
                $client->client_id
            );
        } else {
            $this->accountingService->recordClientCreditBankPayment(
                $amount,
                $description,
                $paymentDate,
                $client->client_id
            );
        }

        voco_flash()->success(__('Client Payment updated!'));

        return $client->fresh();
    }

    private function getCreditOutstanding($clientId): float
    {
        return round((float) BookingPayment::join('bookings', 'booking_payments.booking_id', '=', 'bookings.booking_id')
            ->where('booking_payments.payment_mode', BookingPayment::PAYMENT_MODE_CREDIT)
            ->where('bookings.client_id', $clientId)
            ->where('booking_payments.amount_taken', '>', 0)
            ->sum('booking_payments.amount_taken'), 2);
    }
}
