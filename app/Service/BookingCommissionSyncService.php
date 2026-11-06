<?php

namespace App\Service;

use App\Models\BookingItem;
use App\Models\BookingService;
use App\Models\SplitServiceAmount;

class BookingCommissionSyncService
{
    /**
     * Clear finalized commission rows so the next Update rebuilds from current line data.
     */
    public function invalidateServiceCommission(BookingService $service): void
    {
        SplitServiceAmount::where('booking_service_id', $service->booking_service_id)->delete();

        $service->split_amount_status = 'incompleted';
        $service->approved_at = null;
        $service->approved_by = null;
        $service->save();
    }

    public function invalidateItemCommission(BookingItem $item): void
    {
        SplitServiceAmount::where('booking_item_id', $item->booking_item_id)->delete();

        $item->commission_split_status = 'incompleted';
        $item->save();
    }

    public function invalidateAllForBooking(int $bookingId): void
    {
        foreach (BookingService::where('booking_id', $bookingId)->get() as $service) {
            $this->invalidateServiceCommission($service);
        }

        foreach (BookingItem::where('booking_id', $bookingId)->get() as $item) {
            if (! empty($item->beautician_id)) {
                $this->invalidateItemCommission($item);
            }
        }
    }

    public function invalidateServiceCommissionIfFinalized(BookingService $service): void
    {
        $booking = $service->booking;
        if ($booking && $booking->booking_code) {
            $this->invalidateServiceCommission($service);
        }
    }

    public function invalidateItemCommissionIfFinalized(BookingItem $item): void
    {
        if (\App\Models\Booking::where('booking_id', $item->booking_id)->whereNotNull('booking_code')->exists()) {
            $this->invalidateItemCommission($item);
        }
    }

    public function invalidateAllForBookingIfFinalized(int $bookingId): void
    {
        $booking = \App\Models\Booking::find($bookingId);
        if ($booking && $booking->booking_code) {
            $this->invalidateAllForBooking($bookingId);
        }
    }
}
