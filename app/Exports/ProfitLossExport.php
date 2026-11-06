<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ProfitLossExport implements FromArray, WithHeadings, WithEvents
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
            'Debit Particular',
            'Debit Amount',
            'Credit Particular',
            'Credit Amount',
        ];
    }

    public function array(): array
    {
        $rows = [];

        $this->appendSectionRows($rows, 'Trading', $this->report['trading_debit_side'] ?? [], $this->report['trading_credit_side'] ?? []);
        $rows[] = ['Trading Total', '', $this->report['trading_total_debit'] ?? 0, '', $this->report['trading_total_credit'] ?? 0];
        $this->appendSectionRows($rows, 'Profit & Loss', $this->report['pl_debit_side'] ?? [], $this->report['pl_credit_side'] ?? []);
        $rows[] = ['Final Total', '', $this->report['pl_total_debit'] ?? 0, '', $this->report['pl_total_credit'] ?? 0];

        return $rows;
    }

    private function appendSectionRows(array &$rows, string $section, array $debitSide, array $creditSide): void
    {
        $debitRows = $this->expandSide($debitSide);
        $creditRows = $creditSide;
        $max = max(count($debitRows), count($creditRows));

        for ($i = 0; $i < $max; $i++) {
            $debit = $debitRows[$i] ?? null;
            $credit = $creditRows[$i] ?? null;

            $rows[] = [
                $section,
                $debit['account_name'] ?? '',
                isset($debit['amount']) ? (float) $debit['amount'] : '',
                $credit['account_name'] ?? '',
                isset($credit['amount']) ? (float) $credit['amount'] : '',
            ];
        }
    }

    private function expandSide(array $rows): array
    {
        $expanded = [];

        foreach ($rows as $row) {
            $expanded[] = $row;

            if (! empty($row['children']) && is_array($row['children'])) {
                foreach ($row['children'] as $child) {
                    $expanded[] = $child;
                }
            }
        }

        return $expanded;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', 'Period');
                $sheet->setCellValue('B1', trim(($this->startDate ?: '—').' to '.($this->endDate ?: '—')));
                $sheet->setCellValue('A2', 'Gross Profit');
                $sheet->setCellValue('B2', $this->report['gross_profit'] ?? 0);
                $sheet->getStyle('A1:A2')->getFont()->setBold(true);
            },
        ];
    }
}
