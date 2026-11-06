<?php

namespace App\Http\Controllers\Salon\BookingDetails;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Repositories\BookingRepository;
use App\Models\BookingService;
use App\Models\BookingItem;
use Illuminate\Support\Facades\DB;

class DeleteBookingServiceController extends Controller
{
    public function __invoke(Request $request, BookingRepository $booking_repo)
    {
        $booking_info = $booking_repo->hunter()->findBooking($request->booking_id);

        if ($booking_info->booking_code != null
            || $booking_info->payment_total > 0
            || $booking_info->payment_status === 'completed'
        ) {
            return response()->json([
                'booking_info' => $booking_info,
                'js_notifications' => ['warning' => 'Completed bookings cannot remove lines. Use Refund for returns.'],
            ]);
        }

        DB::transaction(function () use ($request, $booking_info) {
            if ($request->service_id) {
                $bookingService = BookingService::where('booking_id', $booking_info->booking_id)
                    ->where('booking_service_id', $request->booking_service_id)
                    ->first();

                if ($bookingService) {
                    app(\App\Service\ServiceUsedProductService::class)->removeForBookingService($bookingService);
                    $bookingService->delete();
                }
            } elseif ($request->booking_item_id || $request->item_id) {
                $this->deleteBookingItem($request, $booking_info);
            }
        });

        $booking_info->loadEssentialsForVueJs();
        $booking_info->resetBookingAmount();

        return response()->json([
            'booking_info' => $booking_info,
            'js_notifications' => ['info' => 'Booking item removed'],
        ]);
    }

    private function deleteBookingItem(Request $request, $booking_info)
    {
        $query = BookingItem::where('booking_id', $booking_info->booking_id);

        if ($request->filled('booking_item_id')) {
            $query->where('booking_item_id', $request->booking_item_id);
        } else {
            $query->where('item_id', $request->item_id)
                ->where('price_id', $request->price_id);
        }

        $booking_item = $query->firstOrFail();

        // Same stock restore rules as cancel/delete booking
        Booking::restoreStockForBookingItem($booking_item, $booking_info);

        $booking_item->delete();
    }
}
