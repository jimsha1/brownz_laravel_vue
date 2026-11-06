<?php

namespace App\Http\Controllers\Salon\BookingDetails;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\BookingService;
use App\Repositories\BookingRepository;
use App\Service\BookingCommissionSyncService;
use Illuminate\Support\Facades\DB;

class SaveDiscountController extends Controller
{
    protected BookingCommissionSyncService $commissionSync;

    public function __construct(BookingCommissionSyncService $commissionSync)
    {
        $this->commissionSync = $commissionSync;
    }

    public function __invoke(Request $request, BookingRepository $booking_repo)
    {
        $booking_info = Booking::findOrFail($request->booking_id);

        $denied = app(\App\Service\BookingEditGuard::class)->authorize($request, $booking_info);
        if ($denied) {
            return $denied;
        }

        // BE CAREFUL!! LOADING THE RELATIONS PRIOR TO MAKING CHANGES TO TABLE DATA
        // Load booking services, beauticians and slots
        $booking_info->loadEssentialsForVueJs();

        if ($request->type == 'global') {
            $message = $this->applyDiscountGlobal($request, $booking_info);
        } elseif ($request->type == 'price') {
            $message = $this->applyDiscountPrice($request, $booking_info);
        } else {
            $message = $this->applyDiscount($request, $booking_info);
        }

        if ($booking_info->booking_code) {
            $this->commissionSync->invalidateAllForBooking($request->booking_id);
            $booking_info->refresh();
            $booking_info->load(['bookingOnlyServices', 'bookingItems']);
            $booking_info->resetBookingAmount();
        }

        $booking_info->loadEssentialsForVueJs();
        return response()->json([
            'vue_data' => [
                'vue_details_tab_edit' => [
                    'booking_info' => $booking_info,
                ],
            ],
            'js_notifications' => $message,
        ]);
    }

    public function applyDiscountPrice(Request $request, Booking $booking_info)
    {
        $discount = isset($request->applied_price_percent) ? (float) $request->applied_price_percent : 0;
        if ($discount < 0) {
            $discount = 0;
        }

        // Amount and percentage modes must not stack
        $booking_info->applied_discount_price = $discount;
        $booking_info->applied_discount_percent = 0;
        $booking_info->save();

        $this->clearLinePercentageDiscounts($request->booking_id);

        if ($discount > 0) {
            return ['success' => 'Discount applied'];
        }

        return ['info' => 'Discount removed'];
    }

    public function applyDiscount(Request $request, Booking $booking_info)
    {
        $discount = max((float) $request->applied_discount_percent, 0);

        $booking_info->applied_discount_percent = $discount;
        $booking_info->save();

        $unit_price = (float) $request->total_price;
        $quantity = max((float) ($request->quantity ?? 1), 1);
        $discount_percent = $discount;

        // Calculate total before discount
        $line_total = $unit_price * $quantity;

        // Calculate discount amount
        $discount_amount = ($discount_percent / 100) * $line_total;

        // Total after discount
        $discounted_total = $line_total - $discount_amount;

        $booking_res = 0;

        if ($request->booking_service_id != 0) {
            // Fetch tax_percent from services table via booking_services
            $booking_service = DB::table('booking_services')
                ->join('services', 'booking_services.service_id', '=', 'services.service_id')
                ->where('booking_services.booking_service_id', '=', $request->booking_service_id)
                ->select('services.tax_percent')
                ->first();

            $tax_percent = $booking_service ? ($booking_service->tax_percent ?? 0) : 0;
            $tax = ($tax_percent / 100) * $discounted_total;
            $final_total = $discounted_total + $tax;

            $booking_res = DB::table('booking_services')
                ->where('booking_services.booking_service_id', '=', $request->booking_service_id)
                ->update([
                    'individual_discount_percent' => $discount,
                    'individual_discount_amt' => $discount_amount,
                    'discount_percent' => 0,
                    'discount_amount' => 0,
                    'tax_amount' => $tax,
                    'line_total' => $final_total,
                ]);
        }

        if ($request->booking_item_id != 0) {
            $booking_item = DB::table('booking_items')
                ->where('booking_items.booking_item_id', '=', $request->booking_item_id)
                ->select('tax_percent', 'selling_price', 'quantity')
                ->first();

            if ($booking_item) {
                $qty = max((float) ($request->quantity ?? $booking_item->quantity ?? 1), 1);
                $price = (float) ($request->total_price ?? $booking_item->selling_price);
                $item_line_total = $price * $qty;
                $item_discount_amount = ($discount_percent / 100) * $item_line_total;
                $item_discounted_total = $item_line_total - $item_discount_amount;
                $tax_percent = $booking_item->tax_percent ?? 0;
                $tax = ($tax_percent / 100) * $item_discounted_total;
                $final_total = $item_discounted_total + $tax;

                $booking_res = DB::table('booking_items')
                    ->where('booking_items.booking_item_id', '=', $request->booking_item_id)
                    ->update([
                        'individual_discount_percent' => $discount,
                        'individual_discount_amt' => $item_discount_amount,
                        'discount_percent' => 0,
                        'discount_amount' => 0,
                        'tax_amount' => $tax,
                        'line_total' => $final_total,
                    ]);
            }
        }

        if ($booking_res) {
            return ['success' => 'Discount applied'];
        }

        return ['info' => 'Discount removed'];
    }

    public function applyDiscountGlobal(Request $request, Booking $booking_info)
    {
        $booking_total = 0;
        $discount = isset($request->applied_discount_percent) ? (float) $request->applied_discount_percent : 0;
        if ($discount < 0) {
            $discount = 0;
        }

        // Percentage and amount modes must not stack
        $booking_info->applied_discount_percent = $discount;
        $booking_info->applied_discount_price = 0;
        $booking_info->save();

        // Apply discount to booking services
        $services = BookingService::where('booking_id', $request->booking_id)->get();

        foreach ($services as $service) {
            $quantity = $service->quantity;
            $total_price = $service->service_booking_price * $quantity;
            $discount_amount = ($discount / 100) * $total_price;
            $total_after_discount = $total_price - $discount_amount;

            $service_tax = DB::table('services')
                ->where('service_id', $service->service_id)
                ->select('tax_percent')
                ->first();

            $tax_percent = $service_tax ? ($service_tax->tax_percent ?? 0) : 0;
            $tax = ($tax_percent / 100) * $total_after_discount;
            $final_total = $total_after_discount + $tax;

            // Only apply global discount if no individual discount set
            if ($service->individual_discount_percent == 0) {
                DB::table('booking_services')
                    ->where('booking_service_id', $service->booking_service_id)
                    ->update([
                        'discount_percent' => $discount,
                        'discount_amount' => $discount_amount,
                        'tax_amount' => $tax,
                        'line_total' => $final_total,
                        'show_discount_persent' => $discount,
                    ]);
            }

            $booking_total += $final_total;
        }

        // Apply discount to booking items (where individual_discount_percent == 0)
        $booking_items = DB::table('booking_items')
            ->where('booking_id', $request->booking_id)
            ->where('individual_discount_percent', '=', 0)
            ->get();

        foreach ($booking_items as $bki) {
            $tax_percentage = $bki->tax_percent ?? 0;
            $line_price = ((float) $bki->selling_price) * ((float) $bki->quantity);
            $discount_amount = ($discount / 100) * $line_price;
            $total_after_discount = $line_price - $discount_amount;
            $tax = ($tax_percentage / 100) * $total_after_discount;
            $final_total = $total_after_discount + $tax;

            DB::table('booking_items')
                ->where('booking_item_id', $bki->booking_item_id)
                ->update([
                    'discount_percent' => $discount,
                    'discount_amount' => $discount_amount,
                    'show_discount_persent' => $discount,
                    'tax_amount' => $tax,
                    'line_total' => $final_total,
                ]);

            $booking_total += $final_total;
        }

        // Keep show_discount_persent in sync for items with individual discount too
        DB::table('booking_items')
            ->where('booking_id', $request->booking_id)
            ->update([
                'show_discount_persent' => $discount,
            ]);

        // Update payment if already completed
        if ($booking_info->payment_status === 'completed') {
            $latest_payment = BookingPayment::where('booking_id', $request->booking_id)
                ->orderBy('payment_id', 'desc')
                ->first();

            if ($latest_payment) {
                $latest_payment->amount_given = $latest_payment->amount_taken - $booking_total;
                $latest_payment->save();
            }
        }

        return ['success' => 'Discount applied'];
    }

    /**
     * Reset non-individual line percentage discounts (used when switching to amount mode).
     */
    protected function clearLinePercentageDiscounts($booking_id)
    {
        $services = BookingService::where('booking_id', $booking_id)->get();

        foreach ($services as $service) {
            if ((float) $service->individual_discount_percent > 0) {
                continue;
            }

            $quantity = $service->quantity;
            $total_price = $service->service_booking_price * $quantity;

            $service_tax = DB::table('services')
                ->where('service_id', $service->service_id)
                ->select('tax_percent')
                ->first();

            $tax_percent = $service_tax ? ($service_tax->tax_percent ?? 0) : 0;
            $tax = ($tax_percent / 100) * $total_price;
            $final_total = $total_price + $tax;

            DB::table('booking_services')
                ->where('booking_service_id', $service->booking_service_id)
                ->update([
                    'discount_percent' => 0,
                    'discount_amount' => 0,
                    'tax_amount' => $tax,
                    'line_total' => $final_total,
                    'show_discount_persent' => 0,
                ]);
        }

        $booking_items = DB::table('booking_items')
            ->where('booking_id', $booking_id)
            ->where('individual_discount_percent', '=', 0)
            ->get();

        foreach ($booking_items as $bki) {
            $tax_percentage = $bki->tax_percent ?? 0;
            $line_price = ((float) $bki->selling_price) * ((float) $bki->quantity);
            $tax = ($tax_percentage / 100) * $line_price;
            $final_total = $line_price + $tax;

            DB::table('booking_items')
                ->where('booking_item_id', $bki->booking_item_id)
                ->update([
                    'discount_percent' => 0,
                    'discount_amount' => 0,
                    'show_discount_persent' => 0,
                    'tax_amount' => $tax,
                    'line_total' => $final_total,
                ]);
        }

        DB::table('booking_items')
            ->where('booking_id', $booking_id)
            ->update([
                'show_discount_persent' => 0,
            ]);
    }

    public function totalDiscountPrice(Request $request, BookingRepository $booking_repo)
    {
        $booking_info = Booking::findOrFail($request->booking_id);

        $message = $this->applyTotalPriceDiscount($request, $booking_info);

        $booking_info->loadEssentialsForVueJs();
        return response()->json([
            'vue_data' => [
                'vue_details_tab_edit' => [
                    'booking_info' => $booking_info,
                ],
            ],
            'js_notifications' => $message,
        ]);
    }

    public function applyTotalPriceDiscount(Request $request, Booking $booking_info)
    {
        $discount = max((float) $request->applied_discount_percent, 0);

        $booking_info->applied_discount_price = $discount;
        $booking_info->applied_discount_percent = 0;
        $booking_info->save();

        $this->clearLinePercentageDiscounts($request->booking_id);

        if ($discount > 0) {
            return ['success' => 'Discount applied'];
        }

        return ['info' => 'Discount removed'];
    }

    public function store_roundoff(Request $request)
    {
        $rountoff = Booking::where('booking_id', $request->booking_id)->first();

        if (!$rountoff) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        $rountoff->applied_roundoff_price = $request->roundoffValue;

        $rountoff->save();

        if ($rountoff->wasChanged('applied_roundoff_price')) {
            return response()->json([
                'success' => 'Roundoff applied successfully',
                'roundoffValue' => $request->roundoffValue,
            ]);
        }

        return response()->json(['info' => 'Roundoff removed or unchanged']);
    }
}
