<?php

namespace App\Service;

use App\Models\Booking;
use App\Models\BookingService;
use App\Models\BookingServiceProduct;
use App\Models\ServiceUsedProduct;
use App\Models\StockItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class ServiceUsedProductService
{
    public function ensureSnapshotsForBooking(Booking $booking): void
    {
        if ($this->isStockDocument($booking)) {
            return;
        }

        $services = BookingService::where('booking_id', $booking->booking_id)
            ->whereNull('refunded_from_booking_service_id')
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereNotIn('status', [
                        BookingService::STATUS_COMPLETED,
                        BookingService::STATUS_REFUNDED,
                    ]);
            })
            ->get();

        foreach ($services as $service) {
            $this->ensureSnapshot($service);
        }
    }

    public function ensureSnapshot(BookingService $service): void
    {
        if ((int) ($service->used_products_snapshotted ?? 0) === 1) {
            return;
        }

        if (! $service->booking_service_id || ! $service->service_id) {
            return;
        }

        if (! empty($service->refunded_from_booking_service_id)) {
            return;
        }

        $booking = $service->relationLoaded('booking')
            ? $service->booking
            : Booking::find($service->booking_id);

        if ($booking && $this->isStockDocument($booking)) {
            return;
        }

        $recipe = ServiceUsedProduct::with('item.stockUom')
            ->where('service_id', $service->service_id)
            ->get();

        if ($recipe->isEmpty()) {
            return;
        }

        $serviceQty = $this->serviceQuantity($service);

        foreach ($recipe as $row) {
            if (! $row->item) {
                continue;
            }

            $item = $row->item;
            BookingServiceProduct::firstOrCreate(
                [
                    'booking_service_id' => $service->booking_service_id,
                    'item_id' => $row->item_id,
                ],
                [
                    'booking_id' => $service->booking_id,
                    'service_id' => $service->service_id,
                    'item_name' => $item->item_name,
                    'uom_name' => optional($item->stockUom)->uom_name,
                    'recipe_quantity' => round((float) $row->quantity, 3),
                    'quantity' => round((float) $row->quantity * $serviceQty, 3),
                    'cost_price' => round((float) ($item->cost_price ?? 0), 3),
                    'pack_size' => $item->packSize(),
                    'quantity_overridden' => false,
                ]
            );
        }

        BookingService::where('booking_service_id', $service->booking_service_id)
            ->update(['used_products_snapshotted' => 1]);
        $service->used_products_snapshotted = 1;
    }

    public function postConsumption(BookingService $service): void
    {
        $booking = Booking::find($service->booking_id);
        if ($booking && $this->isStockDocument($booking)) {
            return;
        }

        if (! empty($service->refunded_from_booking_service_id)) {
            return;
        }

        $this->ensureSnapshot($service);

        $lines = BookingServiceProduct::where('booking_service_id', $service->booking_service_id)->get();
        foreach ($lines as $line) {
            $this->applyMovement($service, $line, $this->targetQuantity($service, $line));
        }
    }

    public function rescaleForServiceQuantity(BookingService $service): void
    {
        if (! in_array($service->status, [BookingService::STATUS_COMPLETED, BookingService::STATUS_REFUNDED], true)) {
            $this->ensureSnapshot($service);
        }

        $serviceQty = $this->serviceQuantity($service);
        $lines = BookingServiceProduct::where('booking_service_id', $service->booking_service_id)->get();

        foreach ($lines as $line) {
            if ($line->quantity_overridden) {
                continue;
            }

            $line->quantity = round((float) $line->recipe_quantity * $serviceQty, 3);
            $line->save();
        }

        if (in_array($service->status, [BookingService::STATUS_COMPLETED, BookingService::STATUS_REFUNDED], true)) {
            $this->syncPostedStock($service);
        }
    }

    public function syncPostedStock(BookingService $service): void
    {
        $lines = BookingServiceProduct::where('booking_service_id', $service->booking_service_id)->get();

        foreach ($lines as $line) {
            $hasMovement = StockMovement::where('booking_service_product_id', $line->booking_service_product_id)->exists();
            if (! $hasMovement && empty($line->posted_at)) {
                continue;
            }

            $this->applyMovement($service, $line, $this->targetQuantity($service, $line));
        }
    }

    public function updateLineQuantity(BookingServiceProduct $line, float $quantity): void
    {
        $line->quantity = round(max(0, $quantity), 3);
        $line->quantity_overridden = true;
        $line->save();

        $service = BookingService::findOrFail($line->booking_service_id);
        if (in_array($service->status, [BookingService::STATUS_COMPLETED, BookingService::STATUS_REFUNDED], true)) {
            $this->applyMovement($service, $line, $this->targetQuantity($service, $line));
        }
    }

    public function addLine(BookingService $service, int $itemId, float $quantity): BookingServiceProduct
    {
        $existing = BookingServiceProduct::where('booking_service_id', $service->booking_service_id)
            ->where('item_id', $itemId)
            ->first();

        if ($existing) {
            throw new \RuntimeException('This product is already on the service.');
        }

        $item = StockItem::with('stockUom')
            ->where('is_active', 1)
            ->where('is_sales_item', 0)
            ->find($itemId);
        if (! $item) {
            throw new \RuntimeException('Select a valid product.');
        }

        $serviceQty = $this->serviceQuantity($service);
        $quantity = round(max(0, $quantity), 3);

        $line = BookingServiceProduct::create([
            'booking_service_id' => $service->booking_service_id,
            'booking_id' => $service->booking_id,
            'service_id' => $service->service_id,
            'item_id' => $item->item_id,
            'item_name' => $item->item_name,
            'uom_name' => optional($item->stockUom)->uom_name,
            'recipe_quantity' => $serviceQty > 0 ? round($quantity / $serviceQty, 3) : $quantity,
            'quantity' => $quantity,
            'cost_price' => round((float) ($item->cost_price ?? 0), 3),
            'pack_size' => $item->packSize(),
            'quantity_overridden' => true,
        ]);

        if ($service->status === BookingService::STATUS_COMPLETED) {
            $this->applyMovement($service, $line, $this->targetQuantity($service, $line));
        }

        return $line;
    }

    public function removeLine(BookingServiceProduct $line): void
    {
        $service = BookingService::find($line->booking_service_id);
        $line->quantity = 0;
        $line->save();

        if ($service && ($line->posted_at || StockMovement::where('booking_service_product_id', $line->booking_service_product_id)->exists())) {
            $this->applyMovement($service, $line, 0);
        }

        $line->delete();
    }

    public function removeForBookingService(BookingService $service): void
    {
        $lines = BookingServiceProduct::where('booking_service_id', $service->booking_service_id)->get();
        foreach ($lines as $line) {
            $this->removeLine($line);
        }
    }

    public function restoreForBooking(Booking $booking): void
    {
        $services = BookingService::where('booking_id', $booking->booking_id)->get();
        foreach ($services as $service) {
            $this->removeForBookingService($service);
        }
    }

    public function searchItems(string $term)
    {
        $term = trim($term);
        if (mb_strlen($term) < 1) {
            return collect();
        }

        return StockItem::query()
            ->leftJoin('unit_of_measurements', 'unit_of_measurements.uom_id', '=', 'stock_items.stock_uom_id')
            ->where('stock_items.is_active', 1)
            ->where('stock_items.is_sales_item', 0)
            ->where(function ($query) use ($term) {
                $query->where('stock_items.item_name', 'like', '%'.$term.'%')
                    ->orWhere('stock_items.item_code', 'like', '%'.$term.'%');
            })
            ->orderBy('stock_items.item_name')
            ->limit(20)
            ->get([
                'stock_items.item_id',
                'stock_items.item_name',
                'stock_items.item_code',
                'stock_items.current_stock',
                'unit_of_measurements.uom_name',
            ]);
    }

    public function refreshNotesForBooking(Booking $booking): void
    {
        $lines = BookingServiceProduct::where('booking_id', $booking->booking_id)->get();
        if ($lines->isEmpty()) {
            return;
        }

        $services = BookingService::whereIn('booking_service_id', $lines->pluck('booking_service_id'))
            ->get()
            ->keyBy('booking_service_id');

        foreach ($lines as $line) {
            $service = $services->get($line->booking_service_id);
            $movement = StockMovement::where('booking_service_product_id', $line->booking_service_product_id)->first();
            if (! $service || ! $movement) {
                continue;
            }

            $service->setRelation('booking', $booking);
            $movement->description = $this->movementDescription($service, $line);
            $movement->save();
        }
    }

    private function movementDescription(BookingService $service, BookingServiceProduct $line): string
    {
        $booking = $service->relationLoaded('booking') && $service->booking
            ? $service->booking
            : Booking::find($service->booking_id);

        $note = 'Service used: '.$service->service_name.' / '.$line->item_name;
        $details = [];

        $code = trim((string) ($booking?->booking_code ?? ''));
        if ($code !== '' && strcasecmp($code, 'draft') !== 0) {
            $details[] = 'Booking '.$code;
        }

        $client = trim((string) ($booking?->client_name ?? ''));
        if ($client !== '') {
            $details[] = $client;
        }

        if ($details) {
            $note .= '. '.implode(', ', $details);
        }

        return mb_substr($note, 0, 255);
    }

    private function applyMovement(BookingService $service, BookingServiceProduct $line, float $target): void
    {
        $target = round(max(0, $target), 3);
        $movement = StockMovement::where('booking_service_product_id', $line->booking_service_product_id)->first();
        $current = $movement ? round((float) $movement->stock_out, 3) : 0.0;
        $delta = round($target - $current, 3);

        if (abs($delta) >= 0.0005) {
            $this->adjustItemStock((int) $line->item_id, $delta);
        }

        if ($target < 0.0005) {
            if ($movement) {
                $movement->delete();
            }
            if ($line->posted_at) {
                $line->posted_at = null;
                $line->save();
            }

            return;
        }

        $description = $this->movementDescription($service, $line);

        if ($movement) {
            $movement->stock_out = $target;
            $movement->description = $description;
            $movement->save();
        } else {
            StockMovement::create([
                'item_id' => $line->item_id,
                'booking_service_id' => $service->booking_service_id,
                'booking_service_product_id' => $line->booking_service_product_id,
                'stock_in' => 0,
                'stock_out' => $target,
                'description' => $description,
            ]);
        }

        if (! $line->posted_at) {
            $line->posted_at = now();
            $line->save();
        }
    }

    private function adjustItemStock(int $itemId, float $deltaOut): void
    {
        $deltaOut = round($deltaOut, 3);
        if (abs($deltaOut) < 0.0005) {
            return;
        }

        $item = StockItem::where('item_id', $itemId)->lockForUpdate()->first();
        if (! $item) {
            throw new \RuntimeException('Stock item not found.');
        }

        if ($deltaOut > 0 && ((float) $item->current_stock + 0.0001) < $deltaOut) {
            throw new \RuntimeException(
                'Not enough stock for '.$item->item_name
                .'. Need '.number_format($deltaOut, 3, '.', '')
                .', available '.number_format((float) $item->current_stock, 3, '.', '').'.'
            );
        }

        $item->current_stock = round((float) $item->current_stock - $deltaOut, 3);
        $item->save();

        $priceId = DB::table('stock_item_prices')
            ->where('item_id', $itemId)
            ->orderByDesc('quantity')
            ->value('price_id');

        if ($priceId) {
            DB::table('stock_item_prices')
                ->where('price_id', $priceId)
                ->update([
                    'quantity' => StockItem::sqlAdd('quantity', -$deltaOut),
                ]);
        }
    }

    private function targetQuantity(BookingService $service, BookingServiceProduct $line): float
    {
        if ($service->status === BookingService::STATUS_REFUNDED) {
            return 0.0;
        }

        $serviceQty = (float) ($service->quantity ?: 0);
        if ($serviceQty <= 0) {
            return 0.0;
        }

        $refunded = min($serviceQty, max(0, (float) ($service->refunded_qty ?? 0)));
        $ratio = ($serviceQty - $refunded) / $serviceQty;

        return round(((float) $line->quantity) * $ratio, 3);
    }

    private function serviceQuantity(BookingService $service): float
    {
        $qty = (float) ($service->quantity ?: 1);

        return $qty > 0 ? $qty : 1.0;
    }

    private function isStockDocument(Booking $booking): bool
    {
        return in_array($booking->booking_type, [
            Booking::BOOKING_TYPE_REFUND,
            'Refunded',
            Booking::BOOKING_TYPE_CANCEL,
        ], true);
    }
}
