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
    public $payment_mode;

    public function __construct($start_date, $end_date, $client_name, $payment_mode)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->client_name = $client_name;
        $this->payment_mode = $payment_mode;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Booking::whereNotNull('booking_code')
            ->with('bookingServices.user', 'bookingServices.service', 'bookingPayment', 'bookingOnlyServices')
            ->where('payment_status', 'completed')
            ->orderBy('created_at', 'DESC');

        $startDate = $this->start_date ?? null;
        $endDate = $this->end_date ?? null;

        if ($startDate || $endDate) {
            $query->whereHas('bookingServices', function ($query) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $query->whereBetween('appointment_date', ["$startDate 00:00:00", "$endDate 23:59:59"]);
                } elseif ($startDate) {
                    $query->where('appointment_date', '>=', "$startDate 00:00:00");
                } elseif ($endDate) {
                    $query->where('appointment_date', '<=', "$endDate 23:59:59");
                }
            });
        }

        // Apply client name filter
        if ($this->client_name) {
            $query->where('client_name', 'like', '%' . $this->client_name . '%');
        }

        // Apply payment mode filter
        if ($this->payment_mode) {
            $query->whereHas('bookingPayment', function ($subQuery) {
                $subQuery->where('payment_mode', $this->payment_mode);
            });
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
            'Net Total',
            'Tax Amount',
            'Discount',
            'Payment Total',
        ];
    }

    public function map($booking): array
    {

        $totalDiscount = 0;
        $tax_amount = 0;
        $service_booking_price = 0;

        if ($booking->bookingOnlyServices && $booking->bookingOnlyServices->count() > 0) {

            foreach ($booking->bookingOnlyServices as $service) {
                $totalDiscount += $service->discount_amount + $service->individual_discount_amt;
                $tax_amount += $service->tax_amount;
                $service_booking_price += $service->service_booking_price;
            }
        }

        return [
            $booking->bookingServices->appointment_date,
            $booking->booking_code,
            $booking->client_name,
            $service_booking_price,
            $tax_amount,
            $booking->applied_discount_price + $totalDiscount,
            $booking->payment_total,
        ];
    }
}
