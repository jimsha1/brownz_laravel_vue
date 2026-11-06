<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class DayBookExport implements FromArray, WithHeadings, WithEvents
{
    public function __construct(
        protected array $records,
        protected float $totalDebit,
        protected float $totalCredit,
        protected ?string $startDate = null,
        protected ?string $endDate = null,
        protected ?string $search = null,
        protected ?string $accountName = null,
    ) {
    }

    public function headings(): array
    {
        return [
            'SL#',
            'Date',
            'Voucher#',
            'Account',
            'Debit',
            'Credit',
            'Particular',
        ];
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->records as $index => $record) {
            $rows[] = [
                $index + 1,
                $record['date'] ?? '',
                $record['voucher_no'] ?? '',
                $record['account_name'] ?? '',
                $record['debit'] ?? 0,
                $record['credit'] ?? 0,
                $record['particular'] ?? '',
            ];
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->insertNewRowBefore(1, 3);
                $sheet->setCellValue('A1', 'Period');
                $sheet->setCellValue('B1', trim(($this->startDate ?: '—').' to '.($this->endDate ?: '—')));
                $sheet->setCellValue('A2', 'Search');
                $sheet->setCellValue('B2', $this->search ?: 'All');
                $sheet->setCellValue('A3', 'Account');
                $sheet->setCellValue('B3', $this->accountName ?: 'All');
                $sheet->getStyle('A1:A3')->getFont()->setBold(true);

                $lastRow = $sheet->getHighestRow();
                $totalRow = $lastRow + 1;
                $sheet->setCellValue('D'.$totalRow, 'Total');
                $sheet->setCellValue('E'.$totalRow, $this->totalDebit);
                $sheet->setCellValue('F'.$totalRow, $this->totalCredit);
                $sheet->getStyle('D'.$totalRow.':F'.$totalRow)->getFont()->setBold(true);
            },
        ];
    }
}
