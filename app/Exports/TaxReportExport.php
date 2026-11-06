<?php

namespace App\Exports;

use App\Service\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TaxReportExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected array $totals = [];
    protected $rows;

    public function __construct(
        protected ?string $startDate = null,
        protected ?string $endDate = null,
        protected ?string $bookingCode = null,
        protected string $taxType = 'output',
        protected ?string $purchaseRef = null,
        protected ?string $vendorName = null,
        protected ?string $type = null
    ) {
        $this->taxType = in_array(strtolower($taxType), ['output', 'input'], true)
            ? strtolower($taxType)
            : 'output';
    }

    public function collection()
    {
        if ($this->rows !== null) {
            return $this->rows;
        }

        $payload = (object) [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'booking_code' => $this->bookingCode,
            'purchase_ref' => $this->purchaseRef,
            'vendor_name' => $this->vendorName,
            'tax_type' => $this->taxType,
            'type' => $this->type,
        ];

        $export = app(ReportService::class)->taxexportToPDF($payload);
        $this->rows = collect($export['records'] ?? []);
        $this->totals = $export['totals'] ?? [];

        return $this->rows;
    }

    public function headings(): array
    {
        if ($this->taxType === 'input') {
            return [
                'Purchase Ref',
                'Invoice No',
                'Purchase Date',
                'Vendor',
                'GST(%)',
                'Taxable Base',
                'Input Tax',
            ];
        }

        return [
            'Booking No',
            'Booking Date',
            'Client',
            'Type',
            'GST(%)',
            'Service Tax',
            'Item Tax',
            'Output Tax',
        ];
    }

    public function map($row): array
    {
        if ($this->taxType === 'input') {
            return [
                $row['ref_no'] ?? '',
                $row['invoice_no'] ?? '',
                $row['date'] ?? '',
                $row['party'] ?? '',
                $row['tax_percent'] ?? '',
                number_format((float) ($row['taxable_base'] ?? 0), 2, '.', ''),
                number_format((float) ($row['tax_amount'] ?? 0), 2, '.', ''),
            ];
        }

        return [
            $row['ref_no'] ?? '',
            $row['date'] ?? '',
            $row['party'] ?? '',
            $row['booking_type'] ?? '',
            $row['tax_percent'] ?? '',
            number_format((float) ($row['service_tax'] ?? 0), 2, '.', ''),
            number_format((float) ($row['item_tax'] ?? 0), 2, '.', ''),
            number_format((float) ($row['tax_amount'] ?? 0), 2, '.', ''),
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

                if ($this->taxType === 'input') {
                    $sheet->setCellValue('F'.$totalRow, number_format((float) ($this->totals['taxable_base'] ?? 0), 2, '.', ''));
                    $sheet->setCellValue('G'.$totalRow, number_format((float) ($this->totals['tax_amount'] ?? 0), 2, '.', ''));
                    $sheet->getStyle('A'.$totalRow.':G'.$totalRow)->getFont()->setBold(true);
                } else {
                    $sheet->setCellValue('F'.$totalRow, number_format((float) ($this->totals['service_tax'] ?? 0), 2, '.', ''));
                    $sheet->setCellValue('G'.$totalRow, number_format((float) ($this->totals['item_tax'] ?? 0), 2, '.', ''));
                    $sheet->setCellValue('H'.$totalRow, number_format((float) ($this->totals['tax_amount'] ?? 0), 2, '.', ''));
                    $sheet->getStyle('A'.$totalRow.':H'.$totalRow)->getFont()->setBold(true);
                }
            },
        ];
    }
}
