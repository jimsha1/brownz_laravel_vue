<?php

namespace App\Http\Controllers\Master\ServiceDetails;

use App\Http\Controllers\Controller;
use App\Models\SplitDraft;
use App\Models\SplitServiceAmount;
use App\Service\BookingEditGuard;
use App\Service\SplitServiceService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SplitServiceController extends Controller
{
    private SplitServiceService $salesReport;

    public function __construct(SplitServiceService $salesReport)
    {
        $this->salesReport = $salesReport;
    }
    public function storeSplitPrice(Request $request)
    {
        try {
            return $this->salesReport->splitPrice($request);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 100, null);
        }
    }

    public function getSplitDetails(Request $request)
    {
        $query = SplitServiceAmount::query()
            ->with(['user:id,name', 'beautician']);

        if (!empty($request->booking_service_id)) {
            $query->where('booking_service_id', $request->booking_service_id);
        } else {
            $query->where('booking_id', $request->booking_id);

            if (!empty($request->service_id)) {
                $query->where('service_id', $request->service_id);
            }
        }

        return response()->json(
            $query->orderBy('split_service_amount_id')->get()
        );
    }
    public function storeSplitDraft(Request $request)
    {
        try {
            $booking = \App\Models\Booking::find($request->booking_id);
            $denied = app(BookingEditGuard::class)->authorize($request, $booking);
            if ($denied) {
                $payload = $denied->getData(true);
                $message = $payload['js_notifications']['error'] ?? __('Password required to edit this booking.');

                return errorResponse($message, 101);
            }

            return $this->salesReport->splitDraftPrice($request);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 100, null);
        }
    }
    public function getSplitAmount(Request $request)
    {
        $query = SplitDraft::where('booking_id', $request->booking_id);

        if (!empty($request->booking_service_id)) {
            $query->where('booking_service_id', $request->booking_service_id);
        } elseif (!empty($request->service_id)) {
            $query->where('service_id', $request->service_id);
        }

        $drafts = $query->get();
        if ($drafts->isNotEmpty()) {
            return response()->json($drafts);
        }

        // Fall back to finalized commission rows when drafts were already posted
        $amountQuery = SplitServiceAmount::query()
            ->where('booking_id', $request->booking_id);

        if (!empty($request->booking_service_id)) {
            $amountQuery->where('booking_service_id', $request->booking_service_id);
        } elseif (!empty($request->service_id)) {
            $amountQuery->where('service_id', $request->service_id);
        }

        return response()->json($amountQuery->orderBy('split_service_amount_id')->get());
    }
    public function deleteSplitAmount(Request $request)
    {
        $bookingId = $request->booking_id;
        $serviceId = $request->service_id;
        $bookingServiceId = $request->booking_service_id;

        try {
            $query = SplitDraft::where('booking_id', $bookingId);
            if (!empty($bookingServiceId)) {
                $query->where('booking_service_id', $bookingServiceId);
            } elseif (!empty($serviceId)) {
                $query->where('service_id', $serviceId);
            }

            $deleted = $query->delete();

            if ($deleted) {
                return response()->json(['errorcode' => 0, 'message' => 'Deleted successfully']);
            } else {
                return response()->json(['errorcode' => 1, 'message' => 'No record found or already deleted']);
            }
        } catch (\Exception $e) {
            return response()->json(['errorcode' => 1, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }
}
