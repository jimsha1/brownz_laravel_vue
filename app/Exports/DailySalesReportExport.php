<?php

namespace App\Exports;

use App\Service\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DailySalesReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected ?string $startDate = null,
        protected ?string $endDate = null
    ) {
    }

    public function collection()
    {
        $report = app(ReportService::class)->dailySalesRows((object) [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);

        $rows = collect($report['days']);
        $totals = $report['totals'];
        $totals['date_label'] = 'Total';
        $totals['weekday'] = '';
        $rows->push($totals);

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Weekday',
            'Bookings',
            'Service',
            'Item',
            'Tax',
            'Discount',
            'Cash',
            'Card',
            'Online',
            'Credit',
            'Refunds',
            'Net Sales',
        ];
    }

    public function map($row): array
    {
        return [
            $row['date_label'] ?? '',
            $row['weekday'] ?? '',
            $row['bookings'] ?? 0,
            $row['service'] ?? '0.00',
            $row['item'] ?? '0.00',
            $row['tax'] ?? '0.00',
            $row['discount'] ?? '0.00',
            $row['cash'] ?? '0.00',
            $row['card'] ?? '0.00',
            $row['online'] ?? '0.00',
            $row['credit'] ?? '0.00',
            $row['refund'] ?? '0.00',
            $row['net'] ?? '0.00',
        ];
    }
}
