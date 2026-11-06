<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class TrialBalanceExport implements FromArray, WithHeadings, WithEvents
{
    public function __construct(
        protected array $records,
        protected float $totalDebit,
        protected float $totalCredit,
        protected ?string $startDate = null,
        protected ?string $endDate = null,
        protected ?string $search = null,
    ) {
    }

    public function headings(): array
    {
        return [
            'SL#',
            'Account Name',
            'Debit',
            'Credit',
        ];
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->records as $index => $record) {
            $rows[] = [
                $index + 1,
                $record['name'] ?? '',
                $record['debit_value'] ?? 0,
                $record['credit_value'] ?? 0,
            ];
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $metaRow = 1;

                if ($this->startDate || $this->endDate) {
                    $sheet->insertNewRowBefore(1, 2);
                    $sheet->setCellValue('A1', 'Period');
                    $sheet->setCellValue('B1', trim(($this->startDate ?: '—').' to '.($this->endDate ?: '—')));
                    $sheet->setCellValue('A2', 'Search');
                    $sheet->setCellValue('B2', $this->search ?: 'All');
                    $metaRow = 3;
                }

                $lastRow = $sheet->getHighestRow();
                $totalRow = $lastRow + 1;
                $sheet->setCellValue('B'.$totalRow, 'Total');
                $sheet->setCellValue('C'.$totalRow, $this->totalDebit);
                $sheet->setCellValue('D'.$totalRow, $this->totalCredit);
                $sheet->getStyle('B'.$totalRow.':D'.$totalRow)->getFont()->setBold(true);

                if ($metaRow > 1) {
                    $sheet->getStyle('A1:A2')->getFont()->setBold(true);
                }
            },
        ];
    }
}
