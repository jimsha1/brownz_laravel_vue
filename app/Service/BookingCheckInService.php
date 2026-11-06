<?php

namespace App\Service;

use App\Models\Booking;
use App\Models\BookingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingCheckInService
{
    public function canCheckIn(Booking $booking): bool
    {
        return $booking->booking_code === null
            && (float) $booking->payment_total === 0.0
            && (int) $booking->check_availability !== 1;
    }

    public function canUndoCheckIn(Booking $booking): bool
    {
        return $booking->booking_code === null
            && (float) $booking->payment_total === 0.0
            && (int) $booking->check_availability === 1
            && ! $this->hasStartedService($booking);
    }

    public function hasStartedService(Booking $booking): bool
    {
        return $booking->bookingOnlyServices()
            ->whereNotNull('service_started_at')
            ->exists();
    }

    public function checkIn(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $booking->check_availability = 1;
            $booking->save();

            $this->syncServicesCheckIn($booking, true);
        });
    }

    public function undoCheckIn(Booking $booking): void
    {
        if ($this->hasStartedService($booking)) {
            throw new \RuntimeException('Cannot undo check-in after a service has started.');
        }

        DB::transaction(function () use ($booking) {
            $booking->check_availability = 0;
            $booking->save();

            $this->syncServicesCheckIn($booking, false);
        });
    }

    public function checkInService(BookingService $service): void
    {
        DB::transaction(function () use ($service) {
            $service->client_checkin_at = now();
            $service->client_checkin_by = Auth::id();
            $service->status = BookingService::STATUS_OPEN;
            $service->save();

            if ($service->booking_id) {
                Booking::query()
                    ->where('booking_id', $service->booking_id)
                    ->whereNull('booking_code')
                    ->where('payment_total', 0)
                    ->update(['check_availability' => 1]);
            }
        });
    }

    public function undoCheckInService(BookingService $service): void
    {
        if ($service->service_started_at) {
            throw new \RuntimeException('Cannot undo check-in after service has started.');
        }

        DB::transaction(function () use ($service) {
            $service->client_checkin_at = null;
            $service->client_checkin_by = null;
            $service->service_started_at = null;
            $service->save();

            if (! $service->booking_id) {
                return;
            }

            $booking = Booking::find($service->booking_id);
            if (! $booking || $booking->booking_code !== null || (float) $booking->payment_total !== 0.0) {
                return;
            }

            $stillCheckedIn = BookingService::query()
                ->where('booking_id', $booking->booking_id)
                ->whereNotNull('client_checkin_at')
                ->exists();

            if (! $stillCheckedIn) {
                $booking->check_availability = 0;
                $booking->save();
            }
        });
    }

    private function syncServicesCheckIn(Booking $booking, bool $checkingIn): void
    {
        $services = $booking->bookingOnlyServices()
            ->whereNull('service_started_at')
            ->get();

        foreach ($services as $service) {
            if ($checkingIn) {
                $service->client_checkin_at = now();
                $service->client_checkin_by = Auth::id();
                $service->status = BookingService::STATUS_OPEN;
            } else {
                $service->client_checkin_at = null;
                $service->client_checkin_by = null;
            }

            $service->save();
        }
    }
}
