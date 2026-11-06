<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping
{
    public $start_date;
    public $end_date;
    public $client_name;
    public $booking_code;
    public $payment_mode;
    public $type;
    public $line_type;

    public function __construct($start_date, $end_date, $client_name, $booking_code, $payment_mode, $type = null, $line_type = null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->client_name = $client_name;
        $this->booking_code = $booking_code;
        $this->payment_mode = $payment_mode;
        $this->type = $type;
        $this->line_type = $line_type;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Booking::whereNotNull('booking_code')
            ->with('bookingServices.user', 'bookingServices.service', 'bookingPayment', 'bookingOnlyServices', 'bookingItems')
            ->where('payment_status', 'completed')
            ->orderBy('created_at', 'DESC');

        $startDate = $this->start_date ?? null;
        $endDate = $this->end_date ?? null;

        if ($startDate || $endDate) {
            $startBound = $startDate ? $startDate.' 00:00:00' : null;
            $endBound = $endDate ? $endDate.' 23:59:59' : null;

            $query->where(function ($outer) use ($startDate, $endDate, $startBound, $endBound) {
                $outer->whereHas('bookingOnlyServices', function ($q) use ($startDate, $endDate, $startBound, $endBound) {
                    if ($startDate && $endDate) {
                        $q->whereBetween('appointment_date', [$startBound, $endBound]);
                    } elseif ($startDate) {
                        $q->where('appointment_date', '>=', $startBound);
                    } else {
                        $q->where('appointment_date', '<=', $endBound);
                    }
                })->orWhere(function ($q) use ($startBound, $endBound) {
                    $q->whereDoesntHave('bookingOnlyServices')
                        ->whereHas('bookingItems');

                    if ($startBound && $endBound) {
                        $q->whereBetween('bookings.created_at', [$startBound, $endBound]);
                    } elseif ($startBound) {
                        $q->where('bookings.created_at', '>=', $startBound);
                    } elseif ($endBound) {
                        $q->where('bookings.created_at', '<=', $endBound);
                    }
                });
            });
        }

        // Apply client name filter
        if ($this->client_name) {
            $query->where('client_name', 'like', '%' . $this->client_name . '%');
        }

        // Apply bookingcode filter
        if ($this->booking_code) {
            $query->where('booking_code', $this->booking_code);
        }

        // Apply payment mode filter
        if ($this->payment_mode) {
            $query->whereHas('bookingPayment', function ($subQuery) {
                $subQuery->where('payment_mode', $this->payment_mode);
            });
        }

        // Type filter: All (default) / Sale / Refund
        $type = is_string($this->type) ? trim($this->type) : $this->type;
        if (!empty($type) && strcasecmp((string) $type, 'Sale') === 0) {
            $query->where('booking_type', Booking::BOOKING_TYPE_SALE);
        } elseif (!empty($type) && strcasecmp((string) $type, 'Sales') === 0) {
            $query->where('booking_type', Booking::BOOKING_TYPE_SALE);
        } elseif (!empty($type) && strcasecmp((string) $type, 'Refund') === 0) {
            $query->whereIn('booking_type', [Booking::BOOKING_TYPE_REFUND, 'Refunded']);
        } else {
            $query->whereIn('booking_type', [
                Booking::BOOKING_TYPE_SALE,
                Booking::BOOKING_TYPE_REFUND,
                'Refunded',
            ]);
        }

        $lineType = strtolower(trim((string) ($this->line_type ?? '')));
        if ($lineType === 'service') {
            $query->whereHas('bookingOnlyServices');
        } elseif ($lineType === 'item' || $lineType === 'product') {
            $query->whereHas('bookingItems');
        }

        // Get the filtered records
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Booking Date',
            'Booking Code',
            'Client Name',
            'Type',
            'Service',
            'Item',
            'Net Total',
            'Tax Amount',
            'Discount',
            'Payment Total',
        ];
    }

    public function map($booking): array
    {
        $sign = $this->amountSign($booking);

        // Service totals
        $serviceSalesTotal = 0;
        $serviceDiscount = 0;
        $serviceTaxAmount = 0;

        if ($booking->bookingOnlyServices && $booking->bookingOnlyServices->count() > 0) {
            foreach ($booking->bookingOnlyServices as $service) {
                $serviceSalesTotal += $service->line_total ?? 0;
                $serviceDiscount += ($service->discount_amount ?? 0) + ($service->individual_discount_amt ?? 0);
                $serviceTaxAmount += $service->tax_amount ?? 0;
            }
        }

        // Item totals
        $itemSalesTotal = 0;
        $itemDiscount = 0;
        $itemTaxAmount = 0;

        if ($booking->bookingItems && $booking->bookingItems->count() > 0) {
            foreach ($booking->bookingItems as $item) {
                $itemSalesTotal += $item->line_total ?? 0;
                $itemDiscount += $item->discount_amount ?? 0;
                $itemTaxAmount += $item->tax_amount ?? 0;
            }
        }

        // Combined totals (refunds signed for net sales)
        $serviceSalesTotal = abs((float) $serviceSalesTotal) * $sign;
        $itemSalesTotal = abs((float) $itemSalesTotal) * $sign;
        $netTotal = $serviceSalesTotal + $itemSalesTotal;
        $totalTaxAmount = (abs((float) $serviceTaxAmount) + abs((float) $itemTaxAmount)) * $sign;
        $totalDiscount = (abs((float) $serviceDiscount) + abs((float) $itemDiscount) + abs((float) ($booking->applied_discount_price ?? 0))) * $sign;
        $paymentTotal = abs((float) ($booking->payment_total ?? 0)) * $sign;

        return [
            optional($booking->bookingServices)->appointment_date
                ?? optional($booking->created_at)->format('Y-m-d H:i:s')
                ?? 'N/A',
            $booking->booking_code,
            $booking->client_name,
            $booking->booking_type ?? 'Sale',
            number_format($serviceSalesTotal, 2),
            number_format($itemSalesTotal, 2),
            number_format($netTotal, 2),
            number_format($totalTaxAmount, 2),
            number_format($totalDiscount, 2),
            number_format($paymentTotal, 2),
        ];
    }

    private function amountSign($booking): float
    {
        $type = strtolower(trim((string) ($booking->booking_type ?? '')));
        $isRefund = in_array($type, ['refund', 'refunded'], true);
        if (! $isRefund) {
            return 1.0;
        }

        $filter = is_string($this->type) ? trim($this->type) : $this->type;
        if (is_string($filter) && strcasecmp($filter, 'Refund') === 0) {
            return 1.0;
        }

        return -1.0;
    }
}
