<?php

namespace App\Service;

use App\Models\Booking;
use App\Models\BookingService;
use App\Models\SplitDraft;
use App\Models\SplitServiceAmount;
use App\Models\User;
use App\Service\BookingCommissionSyncService;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Cache\Lock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SplitServiceService
{

    // public function splitPrice($request)
    // {
    //     // dd($request);
    //     try {
    //         $beauticians = $request->beauticians ?? [];
    //         $prices = $request->prices ?? [];
    //         $booking_id = $request->booking_id;
    //         $service_name = $request->service_name;
    //         $line_total = $request->line_total;
    //         $service_started_at = $request->service_started_at;
    //         $start_date = $request->start_date;
    //         $end_date = $request->end_date;
    //         $commission_status = $request->commission_status;
    //         $commission_percentage = $request->commission_percentage;


    //         $serviceStartedAt = new DateTime($service_started_at);
    //         $startDate = new DateTime($start_date);
    //         $endDate = new DateTime($end_date);

    //         $isCommissionApplicable = ($serviceStartedAt >= $startDate && $serviceStartedAt <= $endDate);

    //         // Calculate total commission if applicable
    //         $commission_amount = $isCommissionApplicable ? ($line_total * $commission_percentage) / 100 : 0;

    //         foreach ($beauticians as $index => $beautician_id) {
    //             SplitServiceAmount::create([
    //                 'booking_id' => $booking_id,
    //                 'beautician_id' => $beautician_id,
    //                 'service_name' => $service_name,
    //                 'total_amount' => $line_total,
    //                 'commission_percentage' => $isCommissionApplicable ? $commission_percentage : null,
    //                 'commission_amount' => $isCommissionApplicable ? $commission_amount : null,
    //                 'price' => $prices[$index] ?? 0,
    //                 'created_at' => now(),
    //             ]);
    //         }

    //         $booking = BookingService::where('booking_id', $booking_id)->first();

    //         if ($booking) {
    //             $booking->split_amount_status = 'completed';
    //             $booking->save();
    //         } else {
    //             return errorResponse('error', 101, 'Booking not found');
    //         }

    //         return successResponse(null, 'Split Successfully', 0);
    //     } catch (Exception $e) {
    //         return errorResponse('error', 101, $e->getMessage());
    //     }
    // }

    public function splitPrice($request)
    {
        // dd($request->all());
        try {
            $beauticians = $request->beauticians ?? [];
            $prices = $request->prices ?? [];
            $booking_id = $request->booking_id;
            $service_name = $request->service_name;
            $line_total = $request->line_total;
            $service_started_at = $request->service_started_at;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $commission_status = $request->commission_status;
            $commission_percentage = $request->commission_percentage;
            $service_id = $request->service_id;
            $booking_service_id = $request->booking_service_id;

            $serviceStartedAt = new DateTime($service_started_at);
            // Null service commission dates used to become "now", which blocked historical appointments
            $isCommissionApplicable = \App\Support\CommissionCalculator::isApplicable(
                $commission_status,
                $serviceStartedAt,
                $start_date,
                $end_date
            );

            // Delete previous splits for this booking service before recreating
            $splitQuery = SplitServiceAmount::where('booking_id', $booking_id);
            if (!empty($booking_service_id)) {
                $splitQuery->where('booking_service_id', $booking_service_id);
            } else {
                $splitQuery->where('service_id', $service_id);
            }
            $splitQuery->delete();

            $beauticians = array_values(array_filter($beauticians, function ($id) {
                return $id !== null && $id !== '';
            }));
            $lineTotal = (float) $line_total;
            $prices = \App\Support\SplitAmountCalculator::normalizePrices(
                $prices,
                $lineTotal,
                count($beauticians)
            );

            foreach ($beauticians as $index => $beautician_id) {
                $beautician_price = $prices[$index] ?? 0;

                // Commission on each beautician's split share at the full commission rate
                $beautician_commission = \App\Support\CommissionCalculator::amount(
                    (float) $beautician_price,
                    (float) $commission_percentage,
                    $isCommissionApplicable
                );

                $payload = [
                    'booking_id' => $booking_id,
                    'service_id' => $service_id,
                    'beautician_id' => $beautician_id,
                    'service_name' => $service_name,
                    'total_amount' => $lineTotal,
                    'commission_percentage' => $isCommissionApplicable ? (float) $commission_percentage : 0,
                    'commission_amount' => $beautician_commission,
                    'price' => $beautician_price,
                    'created_at' => now(),
                ];
                if (!empty($booking_service_id)) {
                    $payload['booking_service_id'] = $booking_service_id;
                }
                SplitServiceAmount::create($payload);
            }

            $bookingServiceQuery = BookingService::where('booking_id', $booking_id);
            if (!empty($booking_service_id)) {
                $bookingServiceQuery->where('booking_service_id', $booking_service_id);
            } else {
                $bookingServiceQuery->where('service_id', $service_id);
            }
            $updated = $bookingServiceQuery->update(['split_amount_status' => 'completed']);

            if ($updated) {
                $bookingService = ! empty($booking_service_id)
                    ? BookingService::find($booking_service_id)
                    : BookingService::where('booking_id', $booking_id)->where('service_id', $service_id)->first();
                $this->syncBookingServiceFromSplit($bookingService, $beauticians, false);

                return successResponse(null, 'Split Successfully', 0);
            } else {
                return errorResponse('No matching bookings found', 101);
            }
        } catch (Exception $e) {
            Log::error('Split save failed', [
                'booking_id' => $request->booking_id ?? null,
                'booking_service_id' => $request->booking_service_id ?? null,
                'error' => $e->getMessage(),
            ]);

            return errorResponse($e->getMessage() ?: 'Unable to save split.', 101);
        }
    }

    public function splitDraftPrice($request)
    {
        try {

            $beauticians = $request->beauticians ?? [];
            $prices = $request->prices ?? [];
            $booking_id = $request->booking_id;
            $service_name = $request->service_name;
            $line_total = $request->line_total;

            // Always take current timestamp
            $service_started_at = now()->toDateTimeString();

            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $commission_status = $request->commission_status;
            $commission_percentage = $request->commission_percentage;
            $service_id = $request->service_id;
            $booking_service_id = $request->booking_service_id;

            $serviceStartedAt = new DateTime($service_started_at);
            $isCommissionApplicable = \App\Support\CommissionCalculator::isApplicable(
                $commission_status,
                $serviceStartedAt,
                $start_date,
                $end_date
            );

            // Delete old draft records for this booking service line only
            $draftQuery = SplitDraft::where('booking_id', $booking_id);
            if (!empty($booking_service_id)) {
                $draftQuery->where('booking_service_id', $booking_service_id);
            } else {
                $draftQuery->where('service_id', $service_id);
            }
            $draftQuery->delete();

            $beauticians = array_values(array_filter($beauticians, function ($id) {
                return $id !== null && $id !== '';
            }));

            if (empty($beauticians)) {
                return errorResponse('Please select at least one beautician.', 101);
            }

            $bookingService = null;
            if (! empty($booking_service_id)) {
                $bookingService = BookingService::find($booking_service_id);
            } else {
                $bookingService = BookingService::where('booking_id', $booking_id)
                    ->where('service_id', $service_id)
                    ->first();
            }

            if (! $bookingService) {
                return errorResponse('No matching bookings found', 101);
            }

            $lineTotal = (float) $line_total;
            $prices = \App\Support\SplitAmountCalculator::normalizePrices(
                $prices,
                $lineTotal,
                count($beauticians)
            );

            // Single beautician: sync line assignment only — no split draft rows
            if (count($beauticians) === 1) {
                $this->syncBookingServiceFromSplit($bookingService, $beauticians, true);

                $booking = Booking::find($booking_id);
                if ($booking && $booking->booking_code) {
                    app(BookingCommissionSyncService::class)->invalidateServiceCommission($bookingService);
                }

                return successResponse(null, 'Split Successfully', 0);
            }

            foreach ($beauticians as $index => $beautician_id) {
                $beautician_price = $prices[$index] ?? 0;

                // Commission on each beautician's split share at the full commission rate
                $beautician_commission = \App\Support\CommissionCalculator::amount(
                    (float) $beautician_price,
                    (float) $commission_percentage,
                    $isCommissionApplicable
                );

                SplitDraft::create([
                    'booking_id' => $booking_id,
                    'booking_service_id' => $booking_service_id,
                    'service_id' => $service_id,
                    'beautician_id' => $beautician_id,
                    'service_name' => $service_name,
                    'total_amount' => $lineTotal,
                    'commission_percentage' => $isCommissionApplicable ? (float) $commission_percentage : 0,
                    'commission_amount' => $beautician_commission,
                    'price' => $beautician_price,
                    'created_at' => now(),
                ]);
            }

            $this->syncBookingServiceFromSplit($bookingService, $beauticians, true);

            $booking = Booking::find($booking_id);
            if ($booking && $booking->booking_code) {
                app(BookingCommissionSyncService::class)->invalidateServiceCommission($bookingService);
            }

            return successResponse(null, 'Split Successfully', 0);
        } catch (Exception $e) {
            Log::error('Split draft save failed', [
                'booking_id' => $request->booking_id ?? null,
                'booking_service_id' => $request->booking_service_id ?? null,
                'error' => $e->getMessage(),
            ]);

            return errorResponse($e->getMessage() ?: 'Unable to save split.', 101);
        }
    }

    /**
     * Keep booking_services.beautician_id in sync with split selections for line display.
     */
    private function syncBookingServiceFromSplit(?BookingService $bookingService, array $beauticianIds, bool $isDraft): void
    {
        if (! $bookingService || empty($beauticianIds)) {
            return;
        }

        $primaryBeauticianId = (int) $beauticianIds[0];
        $user = User::find($primaryBeauticianId);

        $updates = [
            'beautician_id' => $primaryBeauticianId,
        ];

        if ($user && $user->shift_id) {
            $updates['shift_id'] = $user->shift_id;
        }

        if ($bookingService->status === BookingService::STATUS_COMPLETED) {
            $updates['service_started_by'] = $primaryBeauticianId;
            $updates['service_finished_by'] = $primaryBeauticianId;
        }

        if ($isDraft) {
            $updates['split_draft_status'] = count($beauticianIds) > 1 ? 1 : 0;
        }

        $bookingService->forceFill($updates)->save();
    }
}
