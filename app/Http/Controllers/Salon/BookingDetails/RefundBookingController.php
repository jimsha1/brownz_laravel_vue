<?php

namespace App\Http\Controllers\Salon\BookingDetails;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RefundBookingController extends Controller
{
    public function __invoke(Request $request)
    {
        if (empty($request->itemRefundIds) && empty($request->serveRefundIds)) {
            return response()->json([
                'js_notifications' => ['error' => 'Please select at least one item or service to refund.'],
            ], 400);
        }

        $itemIds = array_values(array_filter((array) ($request->itemRefundIds ?? [])));
        $serviceIds = array_values(array_filter((array) ($request->serveRefundIds ?? [])));

        try {
            $newBookingId = DB::transaction(function () use ($itemIds, $serviceIds) {
                $parentBookingId = null;
                $refundTotal = 0.0;
                $servicesToCopy = [];
                $itemsToCopy = [];

                // --- Services ---
                foreach ($serviceIds as $serviceId) {
                    $service = BookingService::lockForUpdate()->find($serviceId);
                    if (! $service) {
                        continue;
                    }

                    if ($service->status === BookingService::STATUS_REFUNDED) {
                        continue;
                    }

                    $parentBookingId = $parentBookingId ?: $service->booking_id;

                    $totalQty = max(1.0, (float) ($service->quantity ?: 1));
                    $alreadyRefunded = (float) ($service->refunded_qty ?? 0);
                    $remainingQty = $totalQty - $alreadyRefunded;

                    if ($remainingQty <= 0) {
                        continue;
                    }

                    // Keep parent money fields positive (columns are UNSIGNED).
                    $absLineTotal = abs((float) $service->line_total);
                    if ($absLineTotal < 0.01) {
                        $absLineTotal = $this->reconstructServiceLineTotal($service);
                    }

                    $portion = $totalQty > 0 ? ($remainingQty / $totalQty) : 1;
                    $refundLineTotal = round($absLineTotal * $portion, 2);
                    $refundTax = round(abs((float) $service->tax_amount) * $portion, 2);
                    $refundDisc = round(abs((float) $service->discount_amount) * $portion, 2);
                    $refundIndivDisc = round(abs((float) ($service->individual_discount_amt ?? 0)) * $portion, 2);

                    $refundTotal += $refundLineTotal;
                    $servicesToCopy[] = [
                        'model' => $service,
                        'qty' => $remainingQty,
                        'line_total' => $refundLineTotal,
                        'tax_amount' => $refundTax,
                        'discount_amount' => $refundDisc,
                        'individual_discount_amt' => $refundIndivDisc,
                    ];

                    $service->refunded_qty = $alreadyRefunded + $remainingQty;
                    if ($service->refunded_qty >= $totalQty) {
                        $service->status = BookingService::STATUS_REFUNDED;
                    }
                    $service->save();
                    app(\App\Service\ServiceUsedProductService::class)->syncPostedStock($service);
                }

                // --- Items ---
                foreach ($itemIds as $itemId) {
                    $item = BookingItem::lockForUpdate()->find($itemId);
                    if (! $item) {
                        continue;
                    }

                    $parentBookingId = $parentBookingId ?: $item->booking_id;

                    $totalQty = (float) $item->quantity;
                    $alreadyRefunded = (float) ($item->refunded_qty ?? 0);
                    $remainingQty = $totalQty - $alreadyRefunded;

                    if ($remainingQty <= 0 || $item->status === BookingService::STATUS_REFUNDED) {
                        continue;
                    }

                    $absLineTotal = abs((float) $item->line_total);
                    if ($absLineTotal < 0.01) {
                        $absLineTotal = $this->reconstructItemLineTotal($item);
                    }

                    $portion = $totalQty > 0 ? ($remainingQty / $totalQty) : 1;
                    $refundLineTotal = round($absLineTotal * $portion, 2);
                    $refundTax = round(abs((float) $item->tax_amount) * $portion, 2);
                    $refundDisc = round(abs((float) $item->discount_amount) * $portion, 2);
                    $refundIndivDisc = round(abs((float) ($item->individual_discount_amt ?? 0)) * $portion, 2);

                    $refundTotal += $refundLineTotal;

                    $itemsToCopy[] = [
                        'model' => $item,
                        'qty' => $remainingQty,
                        'line_total' => $refundLineTotal,
                        'tax_amount' => $refundTax,
                        'discount_amount' => $refundDisc,
                        'individual_discount_amt' => $refundIndivDisc,
                    ];

                    $item->refunded_qty = $alreadyRefunded + $remainingQty;
                    if ($item->refunded_qty >= $totalQty) {
                        $item->status = BookingService::STATUS_REFUNDED;
                    }
                    $item->save();
                }

                if (! $parentBookingId || ($refundTotal < 0.01 && count($servicesToCopy) === 0 && count($itemsToCopy) === 0)) {
                    throw new \RuntimeException('No refundable items/services found, or they were already refunded.');
                }

                $parent = Booking::findOrFail($parentBookingId);

                $booking = new Booking();
                $booking->outlet_id = $parent->outlet_id;
                $booking->client_id = $parent->client_id;
                $booking->client_name = $parent->client_name;
                $booking->client_mobile = $parent->client_mobile;
                $booking->client_telephone = $parent->client_telephone;
                $booking->client_email = $parent->client_email;
                $booking->booking_source = $parent->booking_source;
                $booking->booking_type = 'Refund';
                // Refund totals are stored negative for payment UX; magnitude from selected lines.
                $booking->booking_total = round($refundTotal * -1, 2);
                $booking->parent_id = $parentBookingId;
                $booking->refund_invoice_no = $parent->booking_code;
                $booking->created_by = Auth::id();
                $booking->updated_by = Auth::id();
                $booking->save();

                foreach ($servicesToCopy as $row) {
                    $src = $row['model'];
                    $copy = new BookingService();
                    $copy->booking_id = $booking->booking_id;
                    $copy->service_id = $src->service_id;
                    $copy->category_name = $src->category_name;
                    $copy->service_name = $src->service_name;
                    $copy->duration_minutes = $src->duration_minutes;
                    $copy->service_booking_price = $src->service_booking_price;
                    $copy->tax_percent = $src->tax_percent;
                    $copy->tax_amount = $row['tax_amount'];
                    $copy->line_total = $row['line_total'];
                    $copy->discount_percent = abs((float) $src->discount_percent);
                    $copy->discount_amount = $row['discount_amount'];
                    $copy->show_discount_persent = abs((float) $src->show_discount_persent);
                    $copy->individual_discount_amt = $row['individual_discount_amt'];
                    $copy->individual_discount_percent = abs((float) $src->individual_discount_percent);
                    $copy->quantity = $row['qty'];
                    $copy->appointment_date = $src->appointment_date;
                    $copy->slot_start = $src->slot_start;
                    $copy->status = 'draft';
                    $copy->beautician_id = $src->beautician_id;
                    $copy->refunded_booking_id = $src->booking_id;
                    $copy->refunded_from_booking_service_id = $src->booking_service_id;
                    $copy->commission_percentage = $src->commission_percentage;
                    $copy->commission_status = $src->commission_status;
                    $copy->start_date = $src->start_date;
                    $copy->end_date = $src->end_date;
                    $copy->save();
                }

                foreach ($itemsToCopy as $row) {
                    $src = $row['model'];
                    $copy = new BookingItem();
                    $copy->booking_id = $booking->booking_id;
                    $copy->item_id = $src->item_id;
                    $copy->price_id = $src->price_id;
                    $copy->item_code = $src->item_code;
                    $copy->item_name = $src->item_name;
                    $copy->batch_no = $src->batch_no;
                    $copy->item_name_ar = $src->item_name_ar;
                    $copy->cost_price = $src->cost_price;
                    $copy->selling_price = $src->selling_price;
                    $copy->discount_percent = $src->discount_percent;
                    $copy->discount_amount = $row['discount_amount'];
                    $copy->individual_discount_amt = $row['individual_discount_amt'];
                    $copy->individual_discount_percent = $src->individual_discount_percent;
                    $copy->show_discount_persent = $src->show_discount_persent;
                    $copy->tax_percent = $src->tax_percent;
                    $copy->tax_amount = $row['tax_amount'];
                    $copy->line_total = $row['line_total'];
                    $copy->quantity = $row['qty'];
                    $copy->beautician_id = $src->beautician_id;
                    $copy->commission_percentage = $src->commission_percentage;
                    $copy->commission_status = $src->commission_status;
                    $copy->start_date = $src->start_date;
                    $copy->end_date = $src->end_date;
                    $copy->refunded_booking_id = $src->booking_id;
                    $copy->refunded_from_booking_item_id = $src->booking_item_id;
                    $copy->save();
                }

                return $booking->booking_id;
            });
        } catch (\Throwable $e) {
            return response()->json([
                'js_notifications' => ['error' => $e->getMessage() ?: 'Could not create refund booking.'],
            ], 422);
        }

        return json_encode($newBookingId);
    }

    private function reconstructItemLineTotal(BookingItem $item): float
    {
        $qty = max(1, (float) $item->quantity);
        $gross = ((float) $item->selling_price) * $qty;
        $discount = abs((float) ($item->discount_amount ?? 0)) + abs((float) ($item->individual_discount_amt ?? 0));
        $tax = abs((float) ($item->tax_amount ?? 0));

        return round(max(0, $gross - $discount + $tax), 2);
    }

    private function reconstructServiceLineTotal(BookingService $service): float
    {
        $qty = max(1, (float) ($service->quantity ?: 1));
        $gross = ((float) $service->service_booking_price) * $qty;
        $discount = abs((float) ($service->discount_amount ?? 0)) + abs((float) ($service->individual_discount_amt ?? 0));
        $tax = abs((float) ($service->tax_amount ?? 0));

        return round(max(0, $gross - $discount + $tax), 2);
    }
}
