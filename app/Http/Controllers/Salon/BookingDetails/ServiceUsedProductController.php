<?php

namespace App\Http\Controllers\Salon\BookingDetails;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingService;
use App\Models\BookingServiceProduct;
use App\Repositories\BookingRepository;
use App\Service\BookingEditGuard;
use App\Service\ServiceUsedProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceUsedProductController extends Controller
{
    public function __construct(private ServiceUsedProductService $usedProducts)
    {
    }

    public function search(Request $request)
    {
        return response()->json([
            'items' => $this->usedProducts->searchItems((string) $request->input('q', '')),
        ]);
    }

    public function updateQuantity(Request $request, BookingRepository $booking_repo)
    {
        $line = BookingServiceProduct::findOrFail($request->booking_service_product_id);
        $booking = $booking_repo->hunter()->findBooking($line->booking_id);
        $this->guard($request, $booking);

        $quantity = round((float) $request->input('quantity', 0), 3);
        if ($quantity < 0) {
            return $this->error(__('Enter a quantity of zero or more.'));
        }

        try {
            DB::transaction(function () use ($line, $quantity) {
                $this->usedProducts->updateLineQuantity($line, $quantity);
            });
        } catch (\RuntimeException $e) {
            return $this->stockError($e);
        }

        return $this->payload($booking);
    }

    public function add(Request $request, BookingRepository $booking_repo)
    {
        $service = BookingService::findOrFail($request->booking_service_id);
        $booking = $booking_repo->hunter()->findBooking($service->booking_id);
        $this->guard($request, $booking);

        if ($service->status === BookingService::STATUS_REFUNDED) {
            return $this->error(__('Refunded services cannot use more products.'));
        }

        $quantity = round((float) $request->input('quantity', 0), 3);
        if ($quantity <= 0) {
            return $this->error(__('Enter a quantity greater than zero.'));
        }

        try {
            DB::transaction(function () use ($service, $request, $quantity) {
                $this->usedProducts->addLine($service, (int) $request->item_id, $quantity);
            });
        } catch (\RuntimeException $e) {
            return $this->stockError($e);
        }

        return $this->payload($booking);
    }

    public function remove(Request $request, BookingRepository $booking_repo)
    {
        $line = BookingServiceProduct::findOrFail($request->booking_service_product_id);
        $booking = $booking_repo->hunter()->findBooking($line->booking_id);
        $this->guard($request, $booking);

        try {
            DB::transaction(function () use ($line) {
                $this->usedProducts->removeLine($line);
            });
        } catch (\RuntimeException $e) {
            return $this->stockError($e);
        }

        return $this->payload($booking);
    }

    private function guard(Request $request, ?Booking $booking): void
    {
        $denied = app(BookingEditGuard::class)->authorize($request, $booking);
        if ($denied) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException($denied);
        }
    }

    private function payload(Booking $booking)
    {
        $booking->refresh();
        $booking->loadEssentialsForVueJs();

        return response()->json([
            'booking_info' => $booking,
        ]);
    }

    private function error(string $message)
    {
        return response()->json([
            'js_notifications' => ['error' => $message],
        ], 422);
    }

    private function stockError(\RuntimeException $e)
    {
        $message = $e->getMessage();
        $known = str_starts_with($message, 'Not enough stock')
            || $message === 'This product is already on the service.'
            || $message === 'Select a valid product.'
            || $message === 'Stock item not found.';

        if (! $known) {
            throw $e;
        }

        return $this->error($message);
    }
}
