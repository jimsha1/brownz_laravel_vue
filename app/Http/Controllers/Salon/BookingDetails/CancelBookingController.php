<?php

namespace App\Http\Controllers\Salon\BookingDetails;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Repositories\BookingRepository;
use App\Service\BookingCheckInService;
use Illuminate\Support\Facades\DB;

class CancelBookingController extends Controller
{
    public function __invoke(Request $request, BookingRepository $booking_repo)
    {
        $booking_info = $booking_repo->hunter()->findBooking($request->booking_id);

        if ($booking_info->canceled_at || $booking_info->booking_type === Booking::BOOKING_TYPE_CANCEL) {
            return response()->json([
                'booking_info' => $booking_info,
                'js_notifications' => ['warning' => 'Booking is already canceled.'],
            ]);
        }

        if ($booking_info->payment_status === 'completed' || ! empty($booking_info->booking_code)) {
            return response()->json([
                'booking_info' => $booking_info,
                'js_notifications' => ['warning' => 'Completed bookings cannot be cancelled. Use Refund for returns.'],
            ]);
        }

        if ($booking_info->payment_total > 0) {
            return response()->json([
                'booking_info' => $booking_info,
                'js_notifications' => ['warning' => 'Void all payments before cancelling this order.'],
            ]);
        }

        // List trash icon: cancel + soft-delete (same stock restore as cancel)
        $shouldSoftDelete = $request->boolean('soft_delete')
            || $request->boolean('delete')
            || empty($booking_info->booking_code);

        DB::transaction(function () use ($booking_info, $shouldSoftDelete) {
            // Return product stock that was deducted when items were added
            Booking::restoreStockForBooking($booking_info);

            $booking_info->canceled_at = new \DateTime();
            $booking_info->canceled_by = auth()->id();
            $booking_info->booking_type = Booking::BOOKING_TYPE_CANCEL;

            if ($booking_info->parent_id != null && $booking_info->parent_id > 0) {
                $refundedItems = DB::table('booking_items')
                    ->where('booking_id', $booking_info->booking_id)
                    ->get();

                foreach ($refundedItems as $item) {
                    $qty = (float) $item->quantity;
                    if ($qty <= 0) {
                        continue;
                    }

                    DB::table('booking_items')
                        ->where('booking_id', $booking_info->parent_id)
                        ->where('item_id', $item->item_id)
                        ->update([
                            'refunded_qty' => DB::raw('GREATEST(0, refunded_qty - '.((int) $qty).')'),
                        ]);
                }
            }

            $booking_info->save();

            // Soft-delete so trash/delete removes the draft from active lists.
            // Stock already restored above; deleting hook skips restore when canceled_at is set.
            if ($shouldSoftDelete) {
                $booking_info->delete();
            }
        });

        if ($shouldSoftDelete) {
            return response()->json([
                'deleted' => true,
                'js_notifications' => ['info' => 'Booking deleted'],
                'redirect_url' => route('tab.salon.booking.list'),
            ]);
        }

        // Reload after cancel (non-delete)
        $booking_info = $booking_repo->hunter()->findBooking($request->booking_id);
        $booking_info->loadEssentialsForVueJs();

        return response()->json([
            'vue_data' => [
                'vue_details_tab_edit' => [
                    'booking_info' => $booking_info,
                ],
            ],
            'js_notifications' => ['info' => 'Booking canceled'],
        ]);
    }

    public function check_availabilty(Request $request, BookingRepository $booking_repo, BookingCheckInService $checkInService)
    {
        $booking_info = $booking_repo->hunter()->findBooking($request->booking_id);

        if (! $checkInService->canCheckIn($booking_info)) {
            $message = (int) $booking_info->check_availability === 1
                ? 'Customer is already checked in.'
                : 'Check-in is only available for draft bookings without payment.';

            return response()->json([
                'booking_info' => $booking_info,
                'js_notifications' => ['warning' => $message],
            ]);
        }

        $checkInService->checkIn($booking_info);
        $booking_info->loadEssentialsForVueJs();

        return response()->json([
            'vue_data' => [
                'vue_details_tab_edit' => [
                    'booking_info' => $booking_info,
                ],
            ],
            'js_notifications' => ['info' => 'Customer checked in successfully.'],
        ]);
    }

    public function undoCheckIn(Request $request, BookingRepository $booking_repo, BookingCheckInService $checkInService)
    {
        $booking_info = $booking_repo->hunter()->findBooking($request->booking_id);

        if (! $checkInService->canUndoCheckIn($booking_info)) {
            $message = $checkInService->hasStartedService($booking_info)
                ? 'Cannot undo check-in after a service has started.'
                : 'Check-in can only be undone for checked-in draft bookings without payment.';

            return response()->json([
                'booking_info' => $booking_info,
                'js_notifications' => ['warning' => $message],
            ]);
        }

        $checkInService->undoCheckIn($booking_info);
        $booking_info->loadEssentialsForVueJs();

        return response()->json([
            'vue_data' => [
                'vue_details_tab_edit' => [
                    'booking_info' => $booking_info,
                ],
            ],
            'js_notifications' => ['info' => 'Check-in undone successfully.'],
        ]);
    }
}
