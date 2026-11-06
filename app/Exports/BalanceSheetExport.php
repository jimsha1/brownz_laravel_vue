<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class BalanceSheetExport implements FromArray, WithHeadings, WithEvents
{
    public function __construct(
        protected array $report,
        protected ?string $startDate = null,
        protected ?string $endDate = null,
    ) {
    }

    public function headings(): array
    {
        return [
            'Section',
            'Account Name',
            'Balance',
        ];
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->report['assets'] ?? [] as $item) {
            $rows[] = ['Assets', $item['name'] ?? '', (float) ($item['balance'] ?? 0)];
        }

        $rows[] = ['Assets Total', 'Total Assets', (float) ($this->report['total_assets'] ?? 0)];

        foreach ($this->report['liabilities'] ?? [] as $item) {
            $rows[] = ['Liabilities', $item['name'] ?? '', (float) ($item['balance'] ?? 0)];
        }

        foreach ($this->report['equity'] ?? [] as $item) {
            $rows[] = ['Equity', $item['name'] ?? '', (float) ($item['balance'] ?? 0)];
        }

        $rows[] = ['Liabilities & Equity Total', 'Total Liabilities & Equity', (float) ($this->report['total_liabilities_and_equity'] ?? 0)];

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', 'Period');
                $sheet->setCellValue('B1', trim(($this->startDate ?: '—').' to '.($this->endDate ?: '—')));
                $sheet->setCellValue('A2', 'As of End Date');
                $sheet->setCellValue('B2', $this->endDate ?: '—');
                $sheet->getStyle('A1:A2')->getFont()->setBold(true);
            },
        ];
    }
}
