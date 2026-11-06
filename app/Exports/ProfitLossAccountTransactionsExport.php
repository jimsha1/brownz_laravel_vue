<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ProfitLossAccountTransactionsExport implements FromArray, WithHeadings, WithEvents
{
    public function __construct(
        protected array $transactions,
        protected float $totalDebit,
        protected float $totalCredit,
        protected float $netAmount,
        protected string $title,
        protected ?string $startDate = null,
        protected ?string $endDate = null,
        protected ?string $subtitle = null,
    ) {
    }

    public function headings(): array
    {
        return [
            'SL#',
            'Voucher No.',
            'Date',
            'Description',
            'Debit',
            'Credit',
        ];
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->transactions as $index => $transaction) {
            $rows[] = [
                $index + 1,
                $transaction['voucher_no'] ?? '',
                $transaction['date'] ?? '',
                $transaction['description'] ?? '',
                $transaction['debit'] ?? 0,
                $transaction['credit'] ?? 0,
            ];
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $metaRows = 3;
                $sheet->insertNewRowBefore(1, $metaRows);
                $sheet->setCellValue('A1', 'Account / Category');
                $sheet->setCellValue('B1', $this->title);
                $sheet->setCellValue('A2', 'Period');
                $sheet->setCellValue('B2', trim(($this->startDate ?: '—').' to '.($this->endDate ?: '—')));
                $sheet->setCellValue('A3', 'Subtitle');
                $sheet->setCellValue('B3', $this->subtitle ?: '—');
                $sheet->getStyle('A1:A3')->getFont()->setBold(true);

                $lastRow = $sheet->getHighestRow();
                $totalRow = $lastRow + 1;
                $sheet->setCellValue('D'.$totalRow, 'Total');
                $sheet->setCellValue('E'.$totalRow, $this->totalDebit);
                $sheet->setCellValue('F'.$totalRow, $this->totalCredit);
                $sheet->setCellValue('A'.($totalRow + 1), 'Net (Cr - Dr)');
                $sheet->setCellValue('F'.($totalRow + 1), $this->netAmount);
                $sheet->getStyle('D'.$totalRow.':F'.($totalRow + 1))->getFont()->setBold(true);
            },
        ];
    }
}
