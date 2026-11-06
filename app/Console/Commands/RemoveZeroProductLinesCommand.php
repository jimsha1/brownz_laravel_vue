<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\SplitServiceAmount;
use App\Models\StockItem;
use App\Models\StockMovement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveZeroProductLinesCommand extends Command
{
    protected $signature = 'booking:remove-zero-product-lines
                            {--booking= : Limit to one booking_id}
                            {--execute : Apply the removal. Without this flag the command only prints what it would do}';

    protected $description = 'Remove zero-value product lines from bookings and return stock that was taken. Does not create a refund.';

    public function handle(): int
    {
        $execute = (bool) $this->option('execute');
        $bookingId = $this->option('booking');

        $bookingQuery = Booking::query()
            ->whereNotIn('booking_type', [Booking::BOOKING_TYPE_REFUND, 'Refunded']);

        if ($bookingId !== null && $bookingId !== '') {
            $bookingQuery->where('booking_id', (int) $bookingId);
        }

        $query = BookingItem::query()
            ->where('selling_price', 0)
            ->where('line_total', 0)
            ->where(function ($q) {
                $q->whereNull('refunded_qty')->orWhere('refunded_qty', 0);
            })
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'refunded');
            })
            ->whereIn('booking_id', $bookingQuery->select('booking_id'))
            ->orderBy('booking_id')
            ->orderBy('booking_item_id');

        $items = $query->get();

        if ($items->isEmpty()) {
            $this->info('No zero-value product lines found.');

            return self::SUCCESS;
        }

        $rows = [];
        foreach ($items as $item) {
            $booking = Booking::find($item->booking_id);
            $netOut = $this->netStockOut($item->booking_item_id);
            $rows[] = [
                $item->booking_id,
                $booking->booking_code ?: '-',
                $booking->payment_status,
                $item->booking_item_id,
                $item->item_name,
                $item->quantity,
                $netOut,
                SplitServiceAmount::where('booking_item_id', $item->booking_item_id)->count(),
            ];
        }

        $this->table(
            ['Booking', 'Code', 'Payment', 'Line', 'Product', 'Qty', 'Stock to return', 'Commission rows'],
            $rows
        );

        $this->line('Priced products are left alone. Completed invoice totals are not recalculated.');

        if (! $execute) {
            $this->warn('Dry run only. Re-run with --execute to remove these lines.');

            return self::SUCCESS;
        }

        if (! $this->confirm('Remove these '.count($rows).' product line(s) and return stock?', false)) {
            $this->info('Cancelled.');

            return self::SUCCESS;
        }

        $removed = 0;
        DB::transaction(function () use ($items, &$removed) {
            foreach ($items as $item) {
                $booking = Booking::find($item->booking_id);
                if (! $booking) {
                    continue;
                }

                $this->returnStock($item);
                SplitServiceAmount::where('booking_item_id', $item->booking_item_id)->delete();
                StockMovement::where('booking_item_id', $item->booking_item_id)->delete();
                $item->delete();
                $removed++;

                if (empty($booking->booking_code) && $booking->payment_status !== Booking::PAYMENT_STATUS_COMPLETED) {
                    $booking->unsetRelation('bookingItems');
                    $booking->unsetRelation('bookingOnlyServices');
                    $booking->load(['bookingItems', 'bookingOnlyServices']);
                    $booking->resetBookingAmount();
                }
            }
        });

        $this->info("Removed {$removed} product line(s).");

        return self::SUCCESS;
    }

    private function netStockOut(int $bookingItemId): float
    {
        $stockOut = (float) StockMovement::where('booking_item_id', $bookingItemId)->sum('stock_out');
        $stockIn = (float) StockMovement::where('booking_item_id', $bookingItemId)->sum('stock_in');

        return max(0, round($stockOut - $stockIn, 3));
    }

    /**
     * Put back only stock this line actually took. A line with no stock movement is deleted without changing inventory.
     */
    private function returnStock(BookingItem $item): void
    {
        $quantityToReturn = $this->netStockOut($item->booking_item_id);
        if ($quantityToReturn <= 0) {
            return;
        }

        StockItem::where('item_id', $item->item_id)->update([
            'current_stock' => StockItem::sqlAdd('current_stock', $quantityToReturn),
        ]);

        if (! empty($item->price_id)) {
            DB::table('stock_item_prices')
                ->where('price_id', $item->price_id)
                ->update([
                    'quantity' => StockItem::sqlAdd('quantity', $quantityToReturn),
                ]);

            return;
        }

        $priceRows = DB::table('stock_item_prices')->where('item_id', $item->item_id)->count();
        if ($priceRows === 1) {
            DB::table('stock_item_prices')
                ->where('item_id', $item->item_id)
                ->update([
                    'quantity' => StockItem::sqlAdd('quantity', $quantityToReturn),
                ]);
        }
    }
}
