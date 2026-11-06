<?php

namespace App\Http\Controllers\Salon\BookingDetails;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\BookingService;
use App\Models\Service;
use App\Repositories\BookingRepository;
use App\Service\AccountingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SavePaymentController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    // public function __invoke(Request $request, BookingRepository $booking_repo)
    // {


    //     $float_amonunt = DB::table('float_amount')
    //         ->where('user_id', '=', auth()->id())
    //         ->where('print_bill', '=', '0')
    //         ->get();
    //     $amonut_exists = 0;

    //     if (count($float_amonunt) > 0) {

    //         $amonut_exists = 1;
    //     }
    //     if ($amonut_exists == 0) {
    //         return response()->json([
    //             'js_notifications' => ['info' => 'cashier not assigned to the till'],
    //         ]);
    //     }
    //     $booking_info = Booking::findOrFail($request->booking_id);
    //     dd($booking_info);
    //     if ($booking_info->booking_code != null && $booking_info->payment_status == 'completed' || $booking_info->booking_total == $booking_info->payment_total) {
    //         return response()->json([
    //             'booking_info' => $booking_info,
    //             'js_notifications' => ['warning' => 'Document has been modified after you have opened it, please refresh to get the latest document.'],
    //         ]);
    //     } else {
    //         //==========================checking time slot===============================
    //         \Log::info('checking time slot');
    //         if ($request->grand_total > 0) {
    //             $servuce_res = BookingService::where('booking_id', $request->booking_id)->get();
    //             if (count($servuce_res) > 0) {
    //                 if ($servuce_res[0]->line_total > 0) {

    //                     $time_slot_flag =  $this->checkTimeSlotIsAvailable($request);
    //                     if ($time_slot_flag == 0) {
    //                         return response()->json([
    //                             'js_notifications' => ['info' => 'time slot already taken'],
    //                         ]);
    //                     }
    //                 }
    //             }
    //         }
    //         //============================end checkinmg time slot =========================
    //         if ($request->grand_total < 0) {
    //             $request->amount_taken = $request->amount_taken * -1;
    //         }


    //         if ($booking_info->booking_type == 'Refund') {
    //             //  && abs($request->amount_taken) > abs($request->grand_total)){
    //             $totalPaid = $request->total_paid;
    //             $grandTotal = $request->grand_total;
    //             $balance = abs($grandTotal) - abs($totalPaid);
    //             if ($balance < abs($request->amount_taken)) {
    //                 return response()->json([
    //                     'js_notifications' => ['warning' => 'Amount must be less than or equal to actual amount.'],
    //                 ]);
    //             }
    //         }

    //         $booking_info->status = 1;

    //         $booking_info->booking_total = $request->grand_total  ?? 0;
    //         $booking_info->update();
    //         $payment = new BookingPayment();
    //         $payment->booking_id = $request->booking_id;
    //         $payment->amount_taken = $request->amount_taken;
    //         if (is_numeric($request->amount_given)) {
    //             $payment->amount_given = $request->amount_given ?? 0;
    //         } else {

    //             $payment->amount_given =  0;
    //         }
    //         // $payment->amount_given = $request->amount_given ?? 0;
    //         $payment->payment_mode = $request->payment_mode ?? 'cash';
    //         // $payment_type = 'cash';
    //         // if ($payment->payment_mode === 'card') {
    //         //     $payment_type = 'card';
    //         //     $payment->card_type = $request->card_type;
    //         // }

    //         if ($payment->payment_mode === 'card') {
    //             $payment->card_type = $request->card_type;
    //         }

    //         $payment->save();

    //         //=============update erp  section =============



    //         // Reset payment total
    //         $booking_info->load('payments');
    //         $booking_info->resetPaymentTotal();

    //         // Load booking services, beauticians and slots
    //         $booking_info->loadEssentialsForVueJs();

    //         return response()->json([
    //             'vue_data' => [
    //                 'vue_details_tab_edit' => [
    //                     'booking_info' => $booking_info,
    //                 ],
    //             ],
    //             'response_values' => [
    //                 '#amount_taken' => '',
    //                 '#amount_given' => '',
    //             ],
    //             'js_notifications' => ['success' => 'Payment updated'],
    //         ]);
    //     }
    // }


    public function __invoke(Request $request, BookingRepository $booking_repo)
    {


        if (!hasOpenFloatAmount()) {
            return response()->json([
                'js_notifications' => ['info' => 'cashier not assigned to the till'],
            ]);
        }
        $booking_info = Booking::findOrFail($request->booking_id);

        if (
            $booking_info->booking_code != null
            && $booking_info->payment_status == 'completed'
            && $booking_info->booking_total == $booking_info->payment_total
        ) {
            return response()->json([
                'booking_info' => $booking_info,
                'js_notifications' => ['warning' => 'Document has been modified after you have opened it, please refresh to get the latest document.'],
            ]);
        } else {
            //==========================checking time slot===============================
            // Refunds reuse original appointment metadata for display only —
            // they must not compete for calendar slots with the sale booking.
            $isRefundBooking = in_array($booking_info->booking_type, ['Refund', 'Refunded'], true)
                || (float) $booking_info->booking_total < 0;

            // An issued invoice is a payment correction, not a new appointment.
            // Slot conflicts must not block replacing a voided payment.
            $alreadyInvoiced = ! empty($booking_info->booking_code);

            \Log::info('checking time slot');
            if (! $isRefundBooking && ! $alreadyInvoiced && $request->grand_total > 0) {
                $servuce_res = BookingService::where('booking_id', $request->booking_id)->get();
                if (count($servuce_res) > 0) {
                    if ($servuce_res[0]->line_total > 0) {

                        $time_slot_flag =  $this->checkTimeSlotIsAvailable($request);
                        if ($time_slot_flag == 0) {
                            return response()->json([
                                'js_notifications' => ['info' => 'time slot already taken'],
                            ]);
                        }
                    }
                }
            }
            //============================end checkinmg time slot =========================
            if ($request->grand_total < 0) {
                $request->amount_taken = $request->amount_taken * -1;
            }


            if ($booking_info->booking_type == 'Refund') {
                //  && abs($request->amount_taken) > abs($request->grand_total)){
                $totalPaid = $request->total_paid;
                $grandTotal = $request->grand_total;
                $balance = abs($grandTotal) - abs($totalPaid);
                if ($balance < abs($request->amount_taken)) {
                    return response()->json([
                        'js_notifications' => ['warning' => 'Amount must be less than or equal to actual amount.'],
                    ]);
                }
            }

            $booking_info->status = 1;

            $booking_info->booking_total = $request->grand_total ?? 0;
            $booking_info->update();

            $applyClientCredit = (int) $request->input('apply_client_credit', 0) === 1;
            if ($applyClientCredit) {
                return $this->applyClientCreditPayment($request, $booking_info);
            }

            $payment = new BookingPayment();
            $payment->booking_id = $request->booking_id;
            $payment->amount_taken = $request->amount_taken;
            if (is_numeric($request->amount_given)) {
                $payment->amount_given = $request->amount_given ?? 0;
            } else {

                $payment->amount_given =  0;
            }

            $changeAmount = round((float) $payment->amount_given, 2);
            $applyChangeToCredit = (int) $request->input('apply_change_to_credit', 0) === 1
                && $changeAmount > 0;

            if ($applyChangeToCredit) {
                if (empty($booking_info->client_id)) {
                    return response()->json([
                        'js_notifications' => ['warning' => 'Select a client before adding given amount to client credit.'],
                    ]);
                }
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('booking_payments', 'change_as_credit')) {
                $payment->change_as_credit = $applyChangeToCredit ? 1 : 0;
            }

            // $payment->amount_given = $request->amount_given ?? 0;
            $payment->payment_mode = $request->payment_mode ?? 0;
            $payment_type = 'cash';
            if ($payment->payment_mode === 'card') {
                $payment_type = 'card';
                $payment->card_type = $request->card_type;
            }

            $payment->save();

            // Keep change in till as client prepaid/credit (reduces AR or goes negative as wallet)
            if ($applyChangeToCredit && $changeAmount > 0) {
                DB::table('clients')
                    ->where('client_id', $booking_info->client_id)
                    ->decrement('balance', $changeAmount);
            }
            //=============update erp  section =============



            // Reset payment total
            $booking_info->load('payments');
            $booking_info->resetPaymentTotal();

            // Finalized booking re-completed after void → re-post accounting
            if (! empty($booking_info->booking_code)
                && $booking_info->payment_status === 'completed'
                && $booking_info->booking_total == $booking_info->payment_total
            ) {
                try {
                    $this->resyncBookingAccounting($booking_info);
                } catch (\Throwable $e) {
                    Log::error('Failed to resync accounting after payment save', [
                        'booking_id' => $booking_info->booking_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Load booking services, beauticians and slots
            $booking_info->loadEssentialsForVueJs();

            $successMsg = $applyChangeToCredit
                ? 'Payment updated. Given amount added to client credit.'
                : 'Payment updated';

            return response()->json([
                'booking_info' => $booking_info,
                'vue_data' => [
                    'vue_details_tab_edit' => [
                        'booking_info' => $booking_info,
                        'amount_taken_flag' => 0,
                    ],
                ],
                'js_notifications' => ['success' => $successMsg],
            ]);
        }
    }

    /**
     * Apply previous client prepaid/credit toward the open booking balance.
     */
    private function applyClientCreditPayment(Request $request, Booking $booking_info)
    {
        if (empty($booking_info->client_id)) {
            return response()->json([
                'js_notifications' => ['warning' => 'Select a client before applying credit.'],
            ]);
        }

        $booking_info->load(['payments', 'realClient']);

        // Credit may only be applied once per booking.
        if ($booking_info->hasClientCreditApplied()) {
            return response()->json([
                'js_notifications' => ['warning' => 'Client credit has already been applied on this booking.'],
            ]);
        }

        $availableCredit = $booking_info->resolveAvailableClientCredit();
        if ($availableCredit < 0.01) {
            return response()->json([
                'js_notifications' => ['warning' => 'No previous credit available for this client.'],
            ]);
        }

        $due = round((float) $booking_info->booking_total - (float) $booking_info->payment_total, 2);
        if ($due < 0.01) {
            return response()->json([
                'js_notifications' => ['info' => 'Booking is already fully paid.'],
            ]);
        }

        $applyAmount = round(min($availableCredit, $due), 2);

        $payment = new BookingPayment();
        $payment->booking_id = $booking_info->booking_id;
        $payment->amount_taken = $applyAmount;
        $payment->amount_given = 0;
        $payment->payment_mode = BookingPayment::PAYMENT_MODE_CREDIT;
        if (\Illuminate\Support\Facades\Schema::hasColumn('booking_payments', 'change_as_credit')) {
            $payment->change_as_credit = 0;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('booking_payments', 'prepaid_applied')) {
            $payment->prepaid_applied = 1;
        }
        $payment->save();

        // Consume wallet prepaid (negative balance → toward zero)
        $clientBalance = (float) (DB::table('clients')->where('client_id', $booking_info->client_id)->value('balance') ?? 0);
        if ($clientBalance < 0) {
            $consume = min($applyAmount, abs($clientBalance));
            if ($consume > 0) {
                DB::table('clients')
                    ->where('client_id', $booking_info->client_id)
                    ->increment('balance', $consume);
            }
        }

        $booking_info->load('payments');
        $booking_info->resetPaymentTotal();

        if (! empty($booking_info->booking_code)
            && $booking_info->payment_status === 'completed'
            && $booking_info->booking_total == $booking_info->payment_total
        ) {
            try {
                $this->resyncBookingAccounting($booking_info);
            } catch (\Throwable $e) {
                Log::error('Failed to resync accounting after credit apply', [
                    'booking_id' => $booking_info->booking_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $booking_info->loadEssentialsForVueJs();

        return response()->json([
            'booking_info' => $booking_info,
            'vue_data' => [
                'vue_details_tab_edit' => [
                    'booking_info' => $booking_info,
                    'amount_taken_flag' => 0,
                ],
            ],
            'js_notifications' => [
                'success' => 'Client credit of '.number_format($applyAmount, 2).' applied.',
            ],
        ]);
    }


    public function delete(Request $request, BookingRepository $booking_repo)
    {
        $booking_info = Booking::findOrFail($request->booking_id);

        if (! empty($booking_info->canceled_by) || $booking_info->booking_type === 'Cancel') {
            return response()->json([
                'js_notifications' => ['warning' => 'Cannot void payment on a canceled booking.'],
            ]);
        }

        $payment = BookingPayment::where('payment_id', $request->payment_id)
            ->where('booking_id', $booking_info->booking_id)
            ->firstOrFail();

        $isFinalized = ! empty($booking_info->booking_code);

        $authError = $this->authorizePaymentCorrection($request, $payment->payment_mode, $isFinalized, 'void');
        if ($authError) {
            return $authError;
        }

        $wasFullyPaid = $booking_info->payment_status === 'completed'
            && abs((float) $booking_info->booking_total - (float) $booking_info->payment_total) < 0.01;

        try {
            DB::transaction(function () use ($booking_info, $payment, $isFinalized, $wasFullyPaid) {
                $this->undoPaymentWalletEffects($booking_info, $payment);

                $payment->delete();

                $booking_info->load('payments');
                $booking_info->resetPaymentTotal();

                if ($isFinalized && $wasFullyPaid) {
                    $this->reverseBookingAccounting($booking_info);
                }
            });
        } catch (\Throwable $e) {
            Log::error('Failed to void payment', [
                'booking_id' => $request->booking_id,
                'payment_id' => $request->payment_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'js_notifications' => ['error' => 'Failed to void payment. Please try again.'],
            ], 500);
        }

        $booking_info = Booking::findOrFail($request->booking_id);
        $booking_info->loadEssentialsForVueJs();

        return response()->json([
            'booking_info' => $booking_info,
            'vue_data' => [
                'vue_details_tab_edit' => [
                    'booking_info' => $booking_info,
                    'amount_taken_flag' => 0,
                ],
            ],
            'js_notifications' => ['info' => 'Payment item removed'],
        ]);
    }

    /**
     * Permission + password gate for voiding / correcting payments.
     * Finalized bookings require void_completed_payment + auth password.
     * Draft voids also require matching void_cash/void_card when available.
     */
    private function authorizePaymentCorrection(Request $request, $paymentMode, bool $isFinalized, string $action)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'js_notifications' => ['error' => 'Unauthorized.'],
            ], 401);
        }

        $mode = strtolower((string) $paymentMode);

        if ($isFinalized) {
            if (! $user->hasRole(\App\Models\AclRole::ROLE_SUPER_ADMIN)
                && ! $user->can('void_completed_payment')
            ) {
                return response()->json([
                    'js_notifications' => ['error' => 'You do not have permission to void/correct completed payments.'],
                ], 403);
            }

            $password = (string) $request->input('auth_password', '');
            if ($password === '' || ! Hash::check($password, $user->password)) {
                return response()->json([
                    'js_notifications' => ['error' => 'Invalid password. Payment correction cancelled.'],
                ], 403);
            }

            return null;
        }

        // Draft payments: no password; mode-specific void permission when configured
        $modePermission = null;
        if ($mode === 'cash') {
            $modePermission = 'void_cash_transaction';
        } elseif ($mode === 'card') {
            $modePermission = 'void_card_transaction';
        }

        if ($modePermission
            && ! $user->hasRole(\App\Models\AclRole::ROLE_SUPER_ADMIN)
            && ! $user->can($modePermission)
            && ! $user->can('void_completed_payment')
        ) {
            return response()->json([
                'js_notifications' => ['error' => 'You do not have permission to void this payment.'],
            ], 403);
        }

        return null;
    }

    /**
     * Reverse wallet effects from change-as-credit / prepaid-applied payments.
     */
    private function undoPaymentWalletEffects(Booking $booking_info, BookingPayment $payment)
    {
        if (empty($booking_info->client_id)) {
            return;
        }

        if (! empty($payment->prepaid_applied)) {
            $amount = round((float) $payment->amount_taken, 2);
            if ($amount > 0) {
                // Restore prepaid (was consumed by incrementing balance)
                DB::table('clients')
                    ->where('client_id', $booking_info->client_id)
                    ->decrement('balance', $amount);
            }
        }

        if (! empty($payment->change_as_credit)) {
            $change = round((float) $payment->amount_given, 2);
            if ($change > 0) {
                // Undo prepaid created from overpayment (was decremented)
                DB::table('clients')
                    ->where('client_id', $booking_info->client_id)
                    ->increment('balance', $change);
            }
        }
    }

    /**
     * Change payment mode on an existing payment (e.g. online → cash after completion).
     * Amounts stay locked; accounting is re-synced when the booking is finalized.
     */
    public function updateMode(Request $request)
    {
        $request->validate([
            'booking_id' => 'required',
            'payment_id' => 'required',
            'payment_mode' => 'required|in:cash,card,online,credit',
            'card_type' => 'nullable|string',
        ]);

        $booking_info = Booking::findOrFail($request->booking_id);

        if (! empty($booking_info->canceled_by) || $booking_info->booking_type === 'Cancel') {
            return response()->json([
                'js_notifications' => ['warning' => 'Cannot change payment mode on a canceled booking.'],
            ]);
        }

        $payment = BookingPayment::where('payment_id', $request->payment_id)
            ->where('booking_id', $booking_info->booking_id)
            ->firstOrFail();

        // Password / completed-void permission only after booking code exists
        $isFinalized = ! empty($booking_info->booking_code);
        $authError = $this->authorizePaymentCorrection($request, $request->payment_mode, $isFinalized, 'mode');
        if ($authError) {
            return $authError;
        }

        $newMode = strtolower($request->payment_mode);
        $oldMode = strtolower((string) $payment->payment_mode);

        if ($newMode === $oldMode
            && ($newMode !== 'card' || (string) $payment->card_type === (string) $request->card_type)
        ) {
            $booking_info->loadEssentialsForVueJs();

            return response()->json([
                'booking_info' => $booking_info,
                'vue_data' => [
                    'vue_details_tab_edit' => [
                        'booking_info' => $booking_info,
                    ],
                ],
                'js_notifications' => ['info' => 'Payment mode unchanged.'],
            ]);
        }

        try {
            DB::transaction(function () use ($request, $booking_info, $payment, $newMode) {
                $payment->payment_mode = $newMode;
                $payment->card_type = ($newMode === 'card') ? ($request->card_type ?: null) : null;
                $payment->save();

                // Finalized bookings already have ledger postings — reverse and re-post with new mode
                if (! empty($booking_info->booking_code)
                    && $booking_info->payment_status === 'completed'
                    && $booking_info->booking_total == $booking_info->payment_total
                ) {
                    $this->resyncBookingAccounting($booking_info);
                }
            });
        } catch (\Throwable $e) {
            Log::error('Failed to update payment mode', [
                'booking_id' => $request->booking_id,
                'payment_id' => $request->payment_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'js_notifications' => ['error' => 'Failed to update payment mode. Please try again.'],
            ], 500);
        }

        $booking_info = Booking::findOrFail($request->booking_id);
        $booking_info->loadEssentialsForVueJs();

        return response()->json([
            'booking_info' => $booking_info,
            'vue_data' => [
                'vue_details_tab_edit' => [
                    'booking_info' => $booking_info,
                ],
            ],
            'js_notifications' => ['success' => 'Payment mode updated successfully.'],
        ]);
    }

    /**
     * Reverse the latest booking posting (no re-post).
     * Used by resyncBookingAccounting before re-posting with updated payment mode.
     */
    private function reverseBookingAccounting(Booking $booking_info)
    {
        $this->accountingService->reverseLatestBookingPosting($booking_info);
    }

    /**
     * Reverse the latest booking posting and re-record accounts so Cash/Bank ledgers match payment mode.
     */
    private function resyncBookingAccounting(Booking $booking_info)
    {
        $this->accountingService->resyncSalonBookingAccounting($booking_info);
    }

    public function checkTimeSlotIsAvailable(Request $request)
    {
        //==========================checking time slot===============================
        $booked_services = BookingService::where('booking_id', $request->booking_id)->get();
        $flag = 1;
        if (count($booked_services) > 0) {
            foreach ($booked_services as $item) {
                if (empty($item->slot_start) || empty($item->appointment_date) || empty($item->beautician_id)) {
                    continue;
                }

                $check_time_slot = BookingService::join('bookings', 'booking_services.booking_id', '=', 'bookings.booking_id')
                    ->where('booking_services.slot_start', $item->slot_start)
                    ->where('bookings.payment_status', 'completed')
                    ->where('booking_services.appointment_date', date('Y-m-d', strtotime($item->appointment_date)))
                    ->where('booking_services.service_id', $item->service_id)
                    ->where('booking_services.line_total', '>=', 0)
                    ->where('booking_services.beautician_id', $item->beautician_id)
                    ->where('booking_services.booking_id', '!=', $request->booking_id)
                    ->whereNotIn('bookings.booking_type', ['Refund', 'Refunded', 'Cancel'])
                    ->where('booking_services.status', '!=', BookingService::STATUS_REFUNDED)
                    ->get()
                    ->toArray();

                if (count($check_time_slot) > 0) {
                    $flag = 0;
                }
            }
        }

        return $flag;
    }

    /**
     * Save multiple payment modes in one request (cash / card / online / credit).
     */
    public function saveSplitPayments(Request $request)
    {
        if (! hasOpenFloatAmount()) {
            return response()->json([
                'js_notifications' => ['info' => 'cashier not assigned to the till'],
            ], 422);
        }

        $booking_info = Booking::findOrFail($request->booking_id);

        if (
            $booking_info->booking_code != null
            && $booking_info->payment_status == 'completed'
            && abs((float) $booking_info->booking_total - (float) $booking_info->payment_total) < 0.01
        ) {
            return response()->json([
                'booking_info' => $booking_info,
                'js_notifications' => ['warning' => 'Document has been modified after you have opened it, please refresh to get the latest document.'],
            ], 422);
        }

        $amounts = [
            BookingPayment::PAYMENT_MODE_CASH => round((float) $request->input('cash', 0), 2),
            BookingPayment::PAYMENT_MODE_CARD => round((float) $request->input('card', 0), 2),
            BookingPayment::PAYMENT_MODE_ONLINE => round((float) $request->input('online', 0), 2),
            BookingPayment::PAYMENT_MODE_CREDIT => round((float) $request->input('credit', 0), 2),
        ];

        $splitTotal = array_sum($amounts);
        if ($splitTotal < 0.01) {
            return response()->json([
                'js_notifications' => ['warning' => 'Enter at least one payment amount.'],
            ], 422);
        }

        if ($amounts[BookingPayment::PAYMENT_MODE_CARD] >= 0.01 && empty($request->card_type)) {
            return response()->json([
                'js_notifications' => ['warning' => 'Select card type for card payment.'],
            ], 422);
        }

        $grandTotal = round((float) ($request->grand_total ?? $booking_info->booking_total), 2);
        $booking_info->status = 1;
        $booking_info->booking_total = $grandTotal;
        $booking_info->save();

        $due = round($grandTotal - (float) $booking_info->payment_total, 2);
        if ($splitTotal - $due > 0.01) {
            return response()->json([
                'js_notifications' => ['warning' => 'Split total exceeds balance due.'],
            ], 422);
        }

        $cashTendered = round((float) $request->input('cash_tendered', 0), 2);
        $cashAmount = $amounts[BookingPayment::PAYMENT_MODE_CASH];
        if ($cashTendered > 0 && $cashTendered < $cashAmount) {
            return response()->json([
                'js_notifications' => ['warning' => 'Cash tendered must be greater than or equal to cash amount.'],
            ], 422);
        }

        DB::transaction(function () use ($booking_info, $amounts, $request, $cashTendered, $cashAmount) {
            foreach ($amounts as $mode => $amount) {
                if ($amount < 0.01) {
                    continue;
                }

                $payment = new BookingPayment();
                $payment->booking_id = $booking_info->booking_id;
                $payment->amount_taken = $amount;
                $payment->amount_given = 0;

                if ($mode === BookingPayment::PAYMENT_MODE_CASH && $cashTendered > $cashAmount) {
                    $payment->amount_given = round($cashTendered - $cashAmount, 2);
                }

                $payment->payment_mode = $mode;
                if ($mode === BookingPayment::PAYMENT_MODE_CARD) {
                    $payment->card_type = $request->card_type;
                }

                $payment->save();
            }
        });

        $booking_info->load('payments');
        $booking_info->resetPaymentTotal();

        if (
            ! empty($booking_info->booking_code)
            && $booking_info->payment_status === 'completed'
            && abs((float) $booking_info->booking_total - (float) $booking_info->payment_total) < 0.01
        ) {
            try {
                $this->resyncBookingAccounting($booking_info);
            } catch (\Throwable $e) {
                Log::error('Failed to resync accounting after split payment save', [
                    'booking_id' => $booking_info->booking_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $booking_info->loadEssentialsForVueJs();

        return response()->json([
            'booking_info' => $booking_info,
            'vue_data' => [
                'vue_details_tab_edit' => [
                    'booking_info' => $booking_info,
                    'amount_taken_flag' => 0,
                ],
            ],
            'js_notifications' => ['success' => __('Split payment saved successfully.')],
        ]);
    }

    /**
     * Replace all payment rows on a finalized booking (same total paid, new mode split).
     */
    public function rebalancePayments(Request $request)
    {
        if (! hasOpenFloatAmount()) {
            return response()->json([
                'js_notifications' => ['info' => 'cashier not assigned to the till'],
            ], 422);
        }

        $booking_info = Booking::findOrFail($request->booking_id);

        if (empty($booking_info->booking_code)) {
            return response()->json([
                'js_notifications' => ['warning' => 'Rebalance is only available after the booking is finalized.'],
            ], 422);
        }

        if (! empty($booking_info->canceled_by) || $booking_info->booking_type === 'Cancel') {
            return response()->json([
                'js_notifications' => ['warning' => 'Cannot rebalance payments on a canceled booking.'],
            ], 422);
        }

        $authError = $this->authorizePaymentCorrection($request, 'cash', true, 'rebalance');
        if ($authError) {
            return $authError;
        }

        $amounts = [
            BookingPayment::PAYMENT_MODE_CASH => round((float) $request->input('cash', 0), 2),
            BookingPayment::PAYMENT_MODE_CARD => round((float) $request->input('card', 0), 2),
            BookingPayment::PAYMENT_MODE_ONLINE => round((float) $request->input('online', 0), 2),
            BookingPayment::PAYMENT_MODE_CREDIT => round((float) $request->input('credit', 0), 2),
        ];

        $splitTotal = array_sum($amounts);
        if ($splitTotal < 0.01) {
            return response()->json([
                'js_notifications' => ['warning' => 'Enter at least one payment amount.'],
            ], 422);
        }

        if ($amounts[BookingPayment::PAYMENT_MODE_CARD] >= 0.01 && empty($request->card_type)) {
            return response()->json([
                'js_notifications' => ['warning' => 'Select card type for card payment.'],
            ], 422);
        }

        $booking_info->load('payments');
        $expectedTotal = round((float) $booking_info->payment_total, 2);

        if ($expectedTotal < 0.01) {
            return response()->json([
                'js_notifications' => ['warning' => 'No payment total to rebalance.'],
            ], 422);
        }

        if (abs($splitTotal - $expectedTotal) > 0.01) {
            return response()->json([
                'js_notifications' => ['warning' => 'New split total must equal the amount already paid ('.number_format($expectedTotal, 2).').'],
            ], 422);
        }

        $cashTendered = round((float) $request->input('cash_tendered', 0), 2);
        $cashAmount = $amounts[BookingPayment::PAYMENT_MODE_CASH];
        if ($cashTendered > 0 && $cashTendered < $cashAmount) {
            return response()->json([
                'js_notifications' => ['warning' => 'Cash tendered must be greater than or equal to cash amount.'],
            ], 422);
        }

        $wasFullyPaid = $booking_info->payment_status === 'completed'
            && abs((float) $booking_info->booking_total - (float) $booking_info->payment_total) < 0.01;

        DB::transaction(function () use ($booking_info, $amounts, $request, $cashTendered, $cashAmount, $wasFullyPaid) {
            if ($wasFullyPaid) {
                $this->reverseBookingAccounting($booking_info);
            }

            foreach ($booking_info->payments as $existingPayment) {
                $this->undoPaymentWalletEffects($booking_info, $existingPayment);
                $existingPayment->delete();
            }

            foreach ($amounts as $mode => $amount) {
                if ($amount < 0.01) {
                    continue;
                }

                $payment = new BookingPayment();
                $payment->booking_id = $booking_info->booking_id;
                $payment->amount_taken = $amount;
                $payment->amount_given = 0;

                if ($mode === BookingPayment::PAYMENT_MODE_CASH && $cashTendered > $cashAmount) {
                    $payment->amount_given = round($cashTendered - $cashAmount, 2);
                }

                $payment->payment_mode = $mode;
                if ($mode === BookingPayment::PAYMENT_MODE_CARD) {
                    $payment->card_type = $request->card_type;
                }

                $payment->save();
            }
        });

        $booking_info->refresh();
        $booking_info->load('payments');
        $booking_info->resetPaymentTotal();

        if ($booking_info->payment_status === 'completed'
            && abs((float) $booking_info->booking_total - (float) $booking_info->payment_total) < 0.01
        ) {
            try {
                $this->resyncBookingAccounting($booking_info);
            } catch (\Throwable $e) {
                Log::error('Failed to resync accounting after payment rebalance', [
                    'booking_id' => $booking_info->booking_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $booking_info->loadEssentialsForVueJs();

        return response()->json([
            'booking_info' => $booking_info,
            'vue_data' => [
                'vue_details_tab_edit' => [
                    'booking_info' => $booking_info,
                    'amount_taken_flag' => 0,
                ],
            ],
            'js_notifications' => ['success' => __('Payments rebalanced successfully.')],
            'rebalanced' => true,
        ]);
    }
}
