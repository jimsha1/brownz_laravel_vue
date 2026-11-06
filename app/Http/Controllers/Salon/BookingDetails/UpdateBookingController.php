<?php

namespace App\Http\Controllers\Salon\BookingDetails;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingEditLog;
use App\Models\BookingItem;
use App\Models\BookingPayment;
use App\Models\BookingService;
use App\Models\Service;
use App\Models\SplitDraft;
use App\Models\SplitServiceAmount;
use App\Models\StockItem;
use App\Models\StockMovement;
use App\Models\TransactionGroup;
use App\Repositories\BookingRepository;
use App\Service\AccountingService;
use App\Service\BookingEditGuard;
use App\User;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateBookingController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function __invoke(Request $request, BookingRepository $booking_repo)
    {

        try {
            // dd($request);

            if (!hasOpenFloatAmount()) {
                return redirect()->route('float_amt', ['id' => $request->booking_id])->with('message', 'cashier not assigned to the till');
            }
            $booking_info = $booking_repo->hunter()->findBooking($request->booking_id);
            $isFirstFinalize = empty($booking_info->booking_code);

            // Print Update on an already-finalized booking: open invoice only (no re-update / accounting reverse).
            if ((int) $request->is_update === 1 && $this->isBookingAlreadyFinalized($booking_info, $request->booking_id)) {
                return $request->print_url;
            }

            $editReason = trim((string) $request->input('edit_reason', ''));
            if ($booking_info->booking_code) {
                $denied = app(BookingEditGuard::class)->authorize($request, $booking_info);
                if ($denied) {
                    return $denied;
                }

                if ($editReason === '') {
                    return response()->json([
                        'js_notifications' => ['error' => __('Please enter a reason for this edit.')],
                    ], 422);
                }

                if (mb_strlen($editReason) > 1000) {
                    return response()->json([
                        'js_notifications' => ['error' => __('Edit reason cannot exceed 1000 characters.')],
                    ], 422);
                }
            }

            $is_pending = 1;
            $shouldNotify = false;

            // Keep commission + accounting atomic so a mid-finalize failure
            // cannot leave services "completed" without account entries.
            DB::transaction(function () use ($request, $booking_info, $isFirstFinalize, $editReason, &$is_pending, &$shouldNotify) {
            //   dd($booking_info);
            if ($booking_info->booking_type == 'Draft') {

                Booking::where('booking_id', $request->booking_id)->update(['booking_type' => 'Refunded']);

            }

            if ($booking_info->booking_type == 'Refund') {
                $items = BookingItem::where('booking_id', $request->booking_id)->get();
                foreach ($items as $item) {
                    // Idempotent: skip if refund stock-in already recorded for this line
                    $alreadyRestored = StockMovement::where('booking_item_id', $item->booking_item_id)
                        ->where('stock_in', '>', 0)
                        ->exists();
                    if ($alreadyRestored) {
                        continue;
                    }

                    $qty = abs((float) $item->quantity);
                    if ($qty <= 0) {
                        continue;
                    }

                    $stockItemModel = StockItem::find($item->item_id);
                    $baseQty = $stockItemModel ? $stockItemModel->baseQuantity($qty) : $qty;

                    StockItem::where('item_id', $item->item_id)->update([
                        'current_stock' => StockItem::sqlAdd('current_stock', $baseQty),
                    ]);

                    if (! empty($item->price_id)) {
                        DB::table('stock_item_prices')
                            ->where('price_id', $item->price_id)
                            ->update([
                                'quantity' => StockItem::sqlAdd('quantity', $baseQty),
                            ]);
                    } else {
                        $priceRows = DB::table('stock_item_prices')
                            ->where('item_id', $item->item_id)
                            ->count();
                        if ($priceRows === 1) {
                            DB::table('stock_item_prices')
                                ->where('item_id', $item->item_id)
                                ->update([
                                    'quantity' => StockItem::sqlAdd('quantity', $baseQty),
                                ]);
                        }
                    }

                    StockMovement::create([
                        'item_id' => $item->item_id,
                        'booking_item_id' => $item->booking_item_id,
                        'stock_out' => 0,
                        'stock_in' => $baseQty,
                        'description' => 'Item refunded: '.$item->item_name,
                    ]);
                }
            }

            if (! $booking_info->booking_code) {
                $booking_info->booking_code = Booking::assignNextBookingCode($booking_info);
                $booking_info->booking_barcode = $booking_info->booking_code;
                $booking_info->created_at = date('Y-m-d H:i:s');
                $booking_info->save();
                $shouldNotify = true;
                Log::info('Booking Number - '.$booking_info->booking_code);
            }

            app(\App\Service\ServiceUsedProductService::class)->refreshNotesForBooking($booking_info);

            // start — beautician commission / split_service_amounts
            $isRefundDoc = in_array($booking_info->booking_type, ['Refund', 'Refunded'], true)
                || (float) $booking_info->booking_total < 0;

            if ($isRefundDoc) {
                // Refund finalize: reverse parent sale commissions; never credit beauticians again.
                $this->handleRefundBeauticianSplits($booking_info);
            } else {
            $booking_services = BookingService::where('booking_id', $request->booking_id)->get();
            // dd($booking_services);
            $defaultArray = [];
            $total_line_total = 0;

            foreach ($booking_services as $service) {
                $total_line_total += $service->line_total;
            }

            foreach ($booking_services as $service) {
                $defaultArray[] = [
                    'booking_service_id' => $service->booking_service_id,
                    'beautician_id' => $service->beautician_id,
                    'booking_id' => $service->booking_id,
                    'service_id' => $service->service_id,
                    'price' => $service->line_total,
                    'total_amount' => $service->line_total,
                    'start_date' => $service->start_date,
                    'end_date' => $service->end_date,
                    'service_name' => $service->service_name,
                    'commission_percentage' => $service->commission_percentage,
                ];
            }
            // dd($defaultArray);
            foreach ($booking_services as $service) {

                if ($service->status != 'completed') {

                    $service->closing_remarks = 'processed by system automatically';
                    $service->status = 'completed';

                    if (empty($service->service_started_at)) {
                        $serviceStartedAt = Carbon::parse($service->appointment_date.' '.$service->slot_start);
                        $service->service_started_at = $serviceStartedAt;
                    } else {
                        $serviceStartedAt = Carbon::parse($service->service_started_at);
                    }

                    if (empty($service->service_started_by)) {
                        $service->service_started_by = $service->beautician_id;
                    }

                    if (empty($service->service_finished_at)) {
                        $duration = (int) $service->duration_minutes;
                        $service->service_finished_at = $serviceStartedAt->copy()->addMinutes($duration);
                    }

                    if (empty($service->service_finished_by)) {
                        $service->service_finished_by = $service->beautician_id;
                    }
                    $service->save();
                    app(\App\Service\ServiceUsedProductService::class)->postConsumption($service);
                }

                if ($service->split_amount_status != 'completed') {

                    SplitServiceAmount::where('booking_service_id', $service->booking_service_id)->delete();

                    $split_draft = SplitDraft::where('booking_id', $request->booking_id)
                        ->where('booking_service_id', $service->booking_service_id)
                        ->get();

                    if (! $split_draft->isEmpty()) {
                        // Remove the entry from $defaultArray that matches this booking_service_id
                        $defaultArray = array_filter($defaultArray, function ($item) use ($service) {
                            return $item['booking_service_id'] != $service->booking_service_id;
                        });

                        $draftPrices = \App\Support\SplitAmountCalculator::normalizePrices(
                            $split_draft->pluck('price')->all(),
                            (float) ($split_draft->first()->total_amount ?: $service->line_total),
                            $split_draft->count()
                        );

                        // Add all split_draft entries to the defaultArray with corrected prices
                        foreach ($split_draft->values() as $index => $split) {
                            $sharePrice = $draftPrices[$index] ?? 0;
                            $commissionPct = (float) ($split->commission_percentage ?? $service->commission_percentage ?? 0);
                            $defaultArray[] = [
                                'beautician_id' => $split->beautician_id,
                                'booking_id' => $split->booking_id,
                                'booking_service_id' => $split->booking_service_id,
                                'service_id' => $split->service_id,
                                'service_name' => $split->service_name,
                                'commission_percentage' => $commissionPct,
                                'commission_amount' => null, // recalculate from corrected share
                                'total_amount' => $split->total_amount ?: $service->line_total,
                                'price' => $sharePrice,
                            ];
                        }
                        // dd($defaultArray);
                    }

                    // Get the service start time, fallback to appointment date and slot_start if not available
                    $serviceStartedAt = $service->service_started_at
                        ? new DateTime($service->service_started_at)
                        : new DateTime($service->appointment_date.' '.$service->slot_start);

                    $beauticians = is_array($service->beautician_id) ? $service->beautician_id : [$service->beautician_id];
                    $prices = is_array($service->line_total) ? $service->line_total : [$service->line_total];

                    $commission_status = $service->commission_status ?? null;
                    $serviceCommissionPercentage = (float) ($service->commission_percentage ?? 0);

                    // Check if commission is applicable: must be active (date range optional)
                    $isCommissionApplicable = \App\Support\CommissionCalculator::isApplicable(
                        $commission_status,
                        $serviceStartedAt,
                        $service->start_date,
                        $service->end_date
                    );

                    // Filter defaultArray to only process entries for the current booking_service_id
                    $currentServiceEntries = array_filter($defaultArray, function ($item) use ($service) {
                        return $item['booking_service_id'] == $service->booking_service_id;
                    });

                    foreach ($currentServiceEntries as $array) {
                        $bookingId = $array['booking_id'];
                        $serviceId = $array['service_id'];
                        $serviceName = $array['service_name'] ?? '';
                        $totalAmount = $array['total_amount'] ?? 0;

                        // Get beauticians for this service
                        $beauticianIds = is_array($array['beautician_id']) ? $array['beautician_id'] : explode(',', (string) $array['beautician_id']);
                        $beauticianIds = array_values(array_filter($beauticianIds, function ($id) {
                            return $id !== null && $id !== '';
                        }));
                        $beauticianCount = count($beauticianIds);

                        // Handle divide-by-zero
                        if ($beauticianCount === 0) {
                            continue;
                        }

                        // Prefer draft commission %, otherwise service commission %
                        $commissionPercentage = $isCommissionApplicable
                            ? (float) ($array['commission_percentage'] ?? $serviceCommissionPercentage)
                            : 0;

                        if (isset($array['price']) && $array['price'] > 0) {
                            $splitPrice = $array['price'];
                        } else {
                            $splitPrice = $totalAmount / $beauticianCount;
                        }

                        // Prefer explicit draft commission amount when present; otherwise % of this split share
                        if ($isCommissionApplicable && array_key_exists('commission_amount', $array) && $array['commission_amount'] !== null) {
                            $splitCommissionAmount = (float) $array['commission_amount'];
                        } elseif ($isCommissionApplicable) {
                            $splitCommissionAmount = \App\Support\CommissionCalculator::amount(
                                (float) $splitPrice,
                                (float) $commissionPercentage,
                                true
                            );
                        } else {
                            $splitCommissionAmount = 0;
                        }

                        foreach ($beauticianIds as $beauticianId) {
                            // Guard: one row with multiple beauticians + full-line price → equal split
                            $priceForBeautician = ($beauticianCount > 1 && abs((float) $splitPrice - (float) $totalAmount) < 0.009)
                                ? round(((float) $totalAmount) / $beauticianCount, 2)
                                : $splitPrice;

                            $commissionForBeautician = ($beauticianCount > 1 && abs((float) $splitPrice - (float) $totalAmount) < 0.009)
                                ? \App\Support\CommissionCalculator::amount(
                                    (float) $priceForBeautician,
                                    (float) $commissionPercentage,
                                    $isCommissionApplicable
                                )
                                : $splitCommissionAmount;

                            SplitServiceAmount::updateOrCreate(
                                [
                                    'booking_id' => $bookingId,
                                    'service_id' => $serviceId,
                                    'beautician_id' => $beauticianId,
                                    'booking_service_id' => $array['booking_service_id'],
                                ],
                                [
                                    'service_name' => $serviceName,
                                    'total_amount' => $totalAmount,
                                    'commission_percentage' => $commissionPercentage,
                                    'commission_amount' => $commissionForBeautician,
                                    'price' => $priceForBeautician,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]
                            );
                        }

                    }

                    BookingService::where('booking_id', $request->booking_id)
                        ->where('booking_service_id', $service->booking_service_id)
                        ->update([
                            'split_amount_status' => 'completed',
                            'approved_at' => Carbon::now(),
                            'approved_by' => Auth::user()->id,
                        ]);
                }
            }
            // end

            // Product commissions: credit assigned beautician when payment/close runs
            $paymentDate = Carbon::now();
            $bookingItemsForCommission = BookingItem::where('booking_id', $request->booking_id)->get();

            foreach ($bookingItemsForCommission as $bookingItem) {
                if (empty($bookingItem->beautician_id)) {
                    continue;
                }

                if ($bookingItem->commission_split_status === 'completed') {
                    continue;
                }

                SplitServiceAmount::where('booking_item_id', $bookingItem->booking_item_id)->delete();

                $isCommissionApplicable = \App\Support\CommissionCalculator::isApplicable(
                    $bookingItem->commission_status,
                    $paymentDate,
                    $bookingItem->start_date,
                    $bookingItem->end_date
                );

                $baseAmount = (float) ($bookingItem->line_total ?? 0);
                $commissionPercentage = $isCommissionApplicable
                    ? (float) ($bookingItem->commission_percentage ?? 0)
                    : 0;
                $commissionAmount = \App\Support\CommissionCalculator::amount(
                    $baseAmount,
                    $commissionPercentage,
                    $isCommissionApplicable
                );

                SplitServiceAmount::updateOrCreate(
                    [
                        'booking_id' => $bookingItem->booking_id,
                        'booking_item_id' => $bookingItem->booking_item_id,
                        'beautician_id' => $bookingItem->beautician_id,
                        'item_id' => $bookingItem->item_id,
                    ],
                    [
                        'service_id' => null,
                        'booking_service_id' => null,
                        'service_name' => $bookingItem->item_name,
                        'total_amount' => $baseAmount,
                        'price' => $baseAmount,
                        'commission_percentage' => $commissionPercentage,
                        'commission_amount' => $commissionAmount,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $bookingItem->commission_split_status = 'completed';
                $bookingItem->save();
            }
            } // end non-refund sale commission path

            $booking_item = BookingItem::where('booking_id', $request->booking_id)->get();

            // // dd($booking_item);

            $is_pending = 1;

            foreach ($booking_item as $item) {
                if ($item->status == 'pending') {
                    $is_pending = 0;
                    break;
                }

            }

            \Log::info('is_pending status: '.$is_pending);

            // Refresh totals — qty changes can leave stale in-memory booking_total/payment_total
            $booking_info->refresh();
            $booking_info->load(['bookingOnlyServices', 'bookingItems', 'payments']);

            // Only create account entries once per booking when payment is completed
            if (abs((float) $booking_info->booking_total - (float) $booking_info->payment_total) < 0.01 && $booking_info->booking_code) {

                $this->accountingService->reverseLatestBookingPosting($booking_info);

                // Create new account entries (whether reversing or creating for first time)
                $gross_service_amount = 0;
                $gross_item_amount = 0;
                $taxAmount = 0;
                $service_discount_amount = 0;
                $item_discount_amount = 0;
                $cogsAmount = 0;

                foreach ($booking_info->bookingOnlyServices as $service) {
                    $gross_service_amount += $service->service_booking_price * $service->quantity;
                    $taxAmount += $service->tax_amount;
                    $service_discount_amount += $service->discount_amount;
                    $service_discount_amount += $service->individual_discount_amt;
                }
                foreach ($booking_info->bookingItems as $item) {
                    $gross_item_amount += $item->selling_price * $item->quantity;
                    $taxAmount += $item->tax_amount;
                    $item_discount_amount += $item->discount_amount;
                    $item_discount_amount += $item->individual_discount_amt;
                    $cost_price = (float) ($item->cost_price ?? 0);
                    if ($cost_price <= 0 && ! empty($item->item_id)) {
                        $cost_price = (float) (DB::table('stock_items')->where('item_id', $item->item_id)->value('cost_price') ?? 0);
                    }
                    $cogsAmount += $cost_price * $item->quantity;
                }

                $discountAmount = $booking_info->applied_discount_price + $item_discount_amount + $service_discount_amount;
                $roundOffAmount = ($booking_info->applied_roundoff_price ?? 0.0);
                $date = $booking_info->created_at->format('Y-m-d');

                // Call main accounting record function

                if ($booking_info->booking_type == 'Refund') {
                    $originalCode = $booking_info->refund_invoice_no;
                    if (empty($originalCode) && ! empty($booking_info->parent_id)) {
                        $originalCode = Booking::where('booking_id', $booking_info->parent_id)->value('booking_code');
                    }
                    $originalCode = $originalCode ?: ($booking_info->booking_code ?: $booking_info->booking_id);
                    $description = 'Salon sale return for client: '.$booking_info->client_name
                        .' (Refund of Booking Code: '.$originalCode.')';

                    $this->accountingService->recordSaleReturn(
                        $booking_info,
                        $gross_service_amount,
                        $gross_item_amount,
                        $taxAmount,
                        $discountAmount,
                        $roundOffAmount,
                        $cogsAmount,
                        $description,
                        $date
                    );
                } else {
                    $description = 'Salon booking for client: '.$booking_info->client_name.' (Booking Code: '.$booking_info->booking_code.')';
                    $this->accountingService->recordAccounts(
                        $booking_info,
                        $gross_service_amount,
                        $gross_item_amount,
                        $taxAmount,
                        $discountAmount,
                        $roundOffAmount,
                        $cogsAmount,
                        $description,
                        $date
                    );
                }
            }

            if (! $isFirstFinalize && $editReason !== '') {
                BookingEditLog::create([
                    'booking_id' => $booking_info->booking_id,
                    'edit_reason' => $editReason,
                    'edited_by' => Auth::id(),
                ]);
            }
            }); // end DB::transaction

            $booking_info->refresh();
            $booking_info->load('payments');
            $booking_info->resetPaymentTotal();

            if ($shouldNotify) {
                try {
                    $booking_info->orderPlacedMail();
                } catch (Exception $mailEx) {
                    Log::warning('Booking mail failed after finalize: '.$mailEx->getMessage());
                }
            }

            if ($is_pending == 1) {
                $this->send_msg_booking($booking_info);
            }

            $successMessage = $isFirstFinalize
                ? __('Booking completed successfully.')
                : __('Booking updated successfully.');

            $newBookingUrl = route('tab.salon.booking.details', ['booking_id' => '0', 'tab_name' => 'edit']);

            if ($request->is_update == 1) {
                return response()->json([
                    'js_notifications' => ['success' => $successMessage],
                    'print_url' => $request->print_url,
                    'redirect' => $newBookingUrl,
                ]);
            }

            return response()->json([
                'js_notifications' => ['success' => $successMessage],
                'redirect' => $newBookingUrl,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update booking #'.($request->booking_id ?? 'n/a').': '.$e->getMessage());
            Log::error($e);

            $message = str_starts_with($e->getMessage(), 'Not enough stock')
                ? $e->getMessage()
                : 'Could not update booking';

            return response()->json([
                'js_notifications' => ['error' => $message],
            ], str_starts_with($e->getMessage(), 'Not enough stock') ? 422 : 500);
        }
    }

    /**
     * Booking already has a code, is fully paid, services/splits are completed,
     * and accounting has been posted. Used so Print Update can open the invoice
     * without re-running finalization — but never skip when accounting is missing.
     */
    private function isBookingAlreadyFinalized($booking_info, $booking_id): bool
    {
        if (! $booking_info->booking_code) {
            return false;
        }

        if (abs((float) $booking_info->booking_total - (float) $booking_info->payment_total) >= 0.01) {
            return false;
        }

        $incompleteServices = BookingService::where('booking_id', $booking_id)
            ->where(function ($q) {
                $q->where('status', '!=', 'completed')
                    ->orWhereNull('split_amount_status')
                    ->orWhere('split_amount_status', '!=', 'completed');
            })
            ->exists();

        if ($incompleteServices) {
            return false;
        }

        // Accounting must exist — otherwise a prior partial failure left the booking
        // looking complete while journal entries were never created.
        $hasAccounting = TransactionGroup::where('description', 'like', '%Booking Code: '.$booking_info->booking_code.'%')
            ->exists();

        return $hasAccounting;
    }


    public function updatetimePrice(Request $request)
    {
        $price = DB::table('price_services')
            ->select('price_services')
            ->join('shifts', 'shifts.shift_id', '=', 'price_services.shift_id')
            ->where('price_services.service_id', $request->service_id)
            ->where('shifts.start_time', '>', $request->slot_start)
            ->get();
    }

    public function send_msg_booking($booking)
    {

        $msg_text = '';
        if (isset($booking->refund_invoice_no)) {
            $msg_text = 'We have processed your return for the Order '.$booking->refund_invoice_no
                .",\nand have refunded (Refund Order {$booking->booking_code}) successfully.Thank you.\n\nSee you next time!";
        } else {
            $msg_text = 'Thank you for booking with Baheya Beauty. Your booking no is '.$booking->booking_code.'. See you soon';
        }
        $number = $booking->client_mobile;
        $url =
            "https://mshastra.com/sendurl.aspx?user=20095417&pwd=B12345&senderid=BAHEYASTORE&mobileno=$number&msgtext=".urlencode($msg_text).'&priority=High&CountryCode=ALL';
        $ch = curl_init($url);
        \Log::info('==================refund message url==============');
        \Log::info($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curl_scraped_page = curl_exec($ch);
        curl_close($ch);
        \Log::info($curl_scraped_page);

        return true;
    }

    public function checkItemExistInBatchNo(Request $request)
    {
        $do = DB::table('batch_no')->where('item_code', '=', $request->item_code)->get();

        return count($do) > 0;
    }

    public function validateBatchNo(Request $request)
    {

        $batch = DB::table('batch_no')
            ->where('item_code', '=', $request->item_code)
            ->where('batch_no', '=', $request->batch_no)
            ->first();

        if ($batch != null) {
            if ($batch->expiry_date >= date('Y-m-d')) {

                $do = DB::table('booking_items')->where('booking_item_id', '=', $request->item_id)
                    ->update([
                        'batch_no' => $request->batch_no,
                        'status' => 'deliverd',
                    ]);

                if ($do) {
                    return ['status' => 0, 'message' => 'Success'];
                } else {
                    return ['status' => 3, 'message' => 'Error occurred while updating document.'];
                }
            } else {
                return ['status' => 2, 'message' => 'Batch no expired'];
            }
        } else {
            return ['status' => 1, 'message' => 'No matching record found. Please try again.'];
        }
    }

    public function productDeliveryStatus(Request $request)
    {

        DB::table('booking_items')->where('booking_item_id', '=', $request->item_id)
            ->update([
                'status' => 'deliverd',
            ]);
        return true;
    }

    /**
     * On refund finalize: remove/rebuild parent sale commissions for refunded lines,
     * and never create split_service_amounts against the refund booking itself.
     */
    private function handleRefundBeauticianSplits(Booking $refundBooking): void
    {
        foreach (BookingService::where('booking_id', $refundBooking->booking_id)->get() as $refundLine) {
            if (($refundLine->split_amount_status ?? '') === 'completed') {
                // Still mark line lifecycle complete if needed
                if ($refundLine->status !== 'completed' && $refundLine->status !== BookingService::STATUS_REFUNDED) {
                    $refundLine->status = 'completed';
                    $refundLine->closing_remarks = $refundLine->closing_remarks ?: 'refund processed';
                    $refundLine->save();
                }
                continue;
            }

            $parent = null;
            if (! empty($refundLine->refunded_from_booking_service_id)) {
                $parent = BookingService::find($refundLine->refunded_from_booking_service_id);
            }
            if (! $parent && ! empty($refundLine->refunded_booking_id)) {
                $parent = BookingService::where('booking_id', $refundLine->refunded_booking_id)
                    ->where('service_id', $refundLine->service_id)
                    ->orderByDesc('booking_service_id')
                    ->first();
            }

            if ($parent) {
                $this->resyncParentServiceCommissionSplits($parent);
            }

            // Never keep/create commission on the refund document
            SplitServiceAmount::where('booking_id', $refundBooking->booking_id)
                ->where('booking_service_id', $refundLine->booking_service_id)
                ->delete();

            $refundLine->status = 'completed';
            $refundLine->split_amount_status = 'completed';
            $refundLine->closing_remarks = $refundLine->closing_remarks ?: 'refund processed';
            $refundLine->approved_at = Carbon::now();
            $refundLine->approved_by = Auth::id();
            $refundLine->save();
        }

        foreach (BookingItem::where('booking_id', $refundBooking->booking_id)->get() as $refundItem) {
            if (($refundItem->commission_split_status ?? '') === 'completed') {
                continue;
            }

            $parent = null;
            if (! empty($refundItem->refunded_from_booking_item_id)) {
                $parent = BookingItem::find($refundItem->refunded_from_booking_item_id);
            }
            if (! $parent && ! empty($refundItem->refunded_booking_id)) {
                $parent = BookingItem::where('booking_id', $refundItem->refunded_booking_id)
                    ->where('item_id', $refundItem->item_id)
                    ->orderByDesc('booking_item_id')
                    ->first();
            }

            if ($parent) {
                $this->resyncParentItemCommissionSplits($parent);
            }

            SplitServiceAmount::where('booking_id', $refundBooking->booking_id)
                ->where('booking_item_id', $refundItem->booking_item_id)
                ->delete();

            $refundItem->commission_split_status = 'completed';
            $refundItem->save();
        }
    }

    /**
     * Rebuild parent service split_service_amounts for remaining (non-refunded) qty.
     * Full refund removes all related beautician commission rows.
     */
    private function resyncParentServiceCommissionSplits(BookingService $parent): void
    {
        $parentQty = max(1.0, (float) ($parent->quantity ?: 1));
        $remainingQty = max(0.0, $parentQty - (float) ($parent->refunded_qty ?? 0));

        SplitServiceAmount::where('booking_service_id', $parent->booking_service_id)->delete();

        if ($remainingQty < 0.0001) {
            return;
        }

        $portion = $remainingQty / $parentQty;
        $remainingLineTotal = round(abs((float) $parent->line_total) * $portion, 2);
        if ($remainingLineTotal < 0.01) {
            return;
        }

        $serviceStartedAt = $parent->service_started_at
            ? new DateTime($parent->service_started_at)
            : new DateTime(($parent->appointment_date ?: date('Y-m-d')).' '.($parent->slot_start ?: '00:00:00'));

        $isCommissionApplicable = \App\Support\CommissionCalculator::isApplicable(
            $parent->commission_status,
            $serviceStartedAt,
            $parent->start_date,
            $parent->end_date
        );
        $commissionPercentage = $isCommissionApplicable ? (float) ($parent->commission_percentage ?? 0) : 0;

        $drafts = SplitDraft::where('booking_service_id', $parent->booking_service_id)->get();
        if ($drafts->isNotEmpty()) {
            $draftPrices = \App\Support\SplitAmountCalculator::normalizePrices(
                $drafts->pluck('price')->all(),
                (float) ($drafts->first()->total_amount ?: $parent->line_total),
                $drafts->count()
            );

            foreach ($drafts->values() as $index => $draft) {
                $sharePrice = round(((float) ($draftPrices[$index] ?? 0)) * $portion, 2);
                if ($sharePrice < 0.01 || empty($draft->beautician_id)) {
                    continue;
                }
                $commissionPct = $isCommissionApplicable
                    ? (float) ($draft->commission_percentage ?? $commissionPercentage)
                    : 0;
                $commissionAmt = \App\Support\CommissionCalculator::amount(
                    $sharePrice,
                    $commissionPct,
                    $isCommissionApplicable
                );

                SplitServiceAmount::create([
                    'booking_id' => $parent->booking_id,
                    'service_id' => $parent->service_id,
                    'booking_service_id' => $parent->booking_service_id,
                    'beautician_id' => $draft->beautician_id,
                    'service_name' => $parent->service_name,
                    'total_amount' => $remainingLineTotal,
                    'price' => $sharePrice,
                    'commission_percentage' => $commissionPct,
                    'commission_amount' => $commissionAmt,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return;
        }

        if (empty($parent->beautician_id)) {
            return;
        }

        $commissionAmt = \App\Support\CommissionCalculator::amount(
            $remainingLineTotal,
            $commissionPercentage,
            $isCommissionApplicable
        );

        SplitServiceAmount::create([
            'booking_id' => $parent->booking_id,
            'service_id' => $parent->service_id,
            'booking_service_id' => $parent->booking_service_id,
            'beautician_id' => $parent->beautician_id,
            'service_name' => $parent->service_name,
            'total_amount' => $remainingLineTotal,
            'price' => $remainingLineTotal,
            'commission_percentage' => $commissionPercentage,
            'commission_amount' => $commissionAmt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Rebuild parent product commission for remaining (non-refunded) qty.
     */
    private function resyncParentItemCommissionSplits(BookingItem $parent): void
    {
        $parentQty = max(1.0, (float) ($parent->quantity ?: 1));
        $remainingQty = max(0.0, $parentQty - (float) ($parent->refunded_qty ?? 0));

        SplitServiceAmount::where('booking_item_id', $parent->booking_item_id)->delete();

        if ($remainingQty < 0.0001 || empty($parent->beautician_id)) {
            if ($remainingQty < 0.0001) {
                $parent->commission_split_status = 'completed';
                $parent->save();
            }
            return;
        }

        $portion = $remainingQty / $parentQty;
        $remainingLineTotal = round(abs((float) $parent->line_total) * $portion, 2);
        if ($remainingLineTotal < 0.01) {
            return;
        }

        $isCommissionApplicable = \App\Support\CommissionCalculator::isApplicable(
            $parent->commission_status,
            Carbon::now(),
            $parent->start_date,
            $parent->end_date
        );
        $commissionPercentage = $isCommissionApplicable ? (float) ($parent->commission_percentage ?? 0) : 0;
        $commissionAmt = \App\Support\CommissionCalculator::amount(
            $remainingLineTotal,
            $commissionPercentage,
            $isCommissionApplicable
        );

        SplitServiceAmount::create([
            'booking_id' => $parent->booking_id,
            'booking_item_id' => $parent->booking_item_id,
            'item_id' => $parent->item_id,
            'beautician_id' => $parent->beautician_id,
            'service_id' => null,
            'booking_service_id' => null,
            'service_name' => $parent->item_name,
            'total_amount' => $remainingLineTotal,
            'price' => $remainingLineTotal,
            'commission_percentage' => $commissionPercentage,
            'commission_amount' => $commissionAmt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $parent->commission_split_status = 'completed';
        $parent->save();
    }

}
