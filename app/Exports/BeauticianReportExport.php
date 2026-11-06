<?php

namespace App\Exports;

use App\Service\ReportService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class BeauticianReportExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected float $filteredTotal = 0;
    protected float $filteredCommissionTotal = 0;
    protected $rows;

    public function __construct(
        public $start_date,
        public $end_date,
        public $booking_code,
        public $beautician_id,
        public $line_type = ''
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
            'booking_code' => $this->booking_code,
            'beautician_id' => $this->beautician_id,
            'line_type' => $this->line_type,
        ];

        $this->rows = app(ReportService::class)->beauticianexportToPDF($filters);
        $this->filteredTotal = (float) $this->rows->sum(function ($row) {
            return (float) ($row->price ?? 0);
        });
        $this->filteredCommissionTotal = (float) $this->rows->sum(function ($row) {
            return (float) ($row->commission_amount ?? 0);
        });

        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Booking Code',
            'Beautician Name',
            'Type',
            'Service / Product',
            'Service Amount',
            'Split Amount',
            'Commission Amount',
        ];
    }

    public function map($booking): array
    {
        $date = $booking->report_date ?? $booking->created_at;

        return [
            $date ? Carbon::parse($date)->format('d-m-Y') : '',
            optional($booking->booking)->booking_code,
            optional($booking->beautician)->name,
            $booking->line_type_label ?? ((! empty($booking->booking_item_id) || ! empty($booking->item_id)) ? 'Product' : 'Service'),
            $booking->service_name,
            number_format((float) ($booking->total_amount ?? 0), 2, '.', ''),
            number_format((float) ($booking->price ?? 0), 2, '.', ''),
            number_format((float) ($booking->commission_amount ?? 0), 2, '.', ''),
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
                $sheet->setCellValue('G'.$totalRow, number_format($this->filteredTotal, 2, '.', ''));
                $sheet->setCellValue('H'.$totalRow, number_format($this->filteredCommissionTotal, 2, '.', ''));
                $sheet->getStyle('A'.$totalRow.':H'.$totalRow)->getFont()->setBold(true);
            },
        ];
    }
}
