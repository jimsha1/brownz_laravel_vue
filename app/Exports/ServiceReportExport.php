<?php

namespace App\Exports;

use App\Service\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ServiceReportExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected float $filteredTotal = 0;
    protected $rows;

    public function __construct(
        public $start_date,
        public $end_date,
        public $beautician_id,
        public $service_name,
        public $booking_code
    ) {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->rows !== null) {
            return $this->rows;
        }

        $filters = (object) [
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'beautician_id' => $this->beautician_id,
            'service_name' => $this->service_name,
            'booking_code' => $this->booking_code,
        ];

        $this->rows = app(ReportService::class)->serviceexportToPDF($filters);
        $this->filteredTotal = (float) $this->rows->sum(function ($record) {
            return (float) ($record->line_total ?? 0);
        });

        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Booking Date',
            'Booking Code',
            'Beautician Name',
            'Service Name',
            'Service Amount',
        ];
    }

    public function map($booking): array
    {
        return [
            $booking->appointment_date,
            optional($booking->booking)->booking_code,
            optional($booking->beautician)->name,
            $booking->service_name,
            number_format((float) ($booking->line_total ?? 0), 2, '.', ''),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->collection();
                $sheet = $event->sheet->getDelegate();
                $totalRow = $sheet->getHighestRow() + 1;
                $sheet->setCellValue('A'.$totalRow, 'Total');
                $sheet->setCellValue('E'.$totalRow, number_format($this->filteredTotal, 2, '.', ''));
                $sheet->getStyle('A'.$totalRow.':E'.$totalRow)->getFont()->setBold(true);
            },
        ];
    }
}
