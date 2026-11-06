<?php

namespace App\Exports;

use App\Service\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ServiceUsedProductReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected string $startDate = '',
        protected string $endDate = '',
        protected string $status = 'used',
        protected string $beauticianId = '',
        protected string $search = ''
    ) {
    }

    public function collection()
    {
        return app(ReportService::class)->usedProductReportRows((object) [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'status' => $this->status,
            'beautician_id' => $this->beauticianId,
            'search' => $this->search,
        ]);
    }

    public function headings(): array
    {
        return [
            'Date',
            'Booking',
            'Client',
            'Service',
            'Beautician',
            'Product',
            'Quantity',
            'Unit',
            'Est. cost',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row['date'] ?? '',
            $row['booking_code'] !== '' ? $row['booking_code'] : '—',
            $row['client_name'] !== '' ? $row['client_name'] : '—',
            $row['service_name'] ?? '',
            $row['beautician_name'] !== '' ? $row['beautician_name'] : '—',
            $row['item_name'] ?? '',
            $row['quantity'] ?? '',
            $row['uom_name'] ?? '',
            $row['estimated_cost'] ?? '0.00',
            $row['status'] ?? '',
        ];
    }
}
