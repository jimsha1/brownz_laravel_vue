<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class FinancialYearOpeningBalanceExport implements FromArray, WithEvents
{
    public function __construct(protected array $report)
    {
    }

    public function array(): array
    {
        $rows = [];
        $year = $this->report['financial_year'] ?? [];

        $rows[] = ['Opening Balance Audit', $year['label'] ?? ''];
        $rows[] = ['Voucher', $year['opening_voucher_no'] ?? '—'];
        $rows[] = ['Period', ($year['start_date'] ?? '').' to '.($year['end_date'] ?? '')];
        $rows[] = ['Status', ($year['status'] ?? '') === 'open' ? 'Open' : 'Closed'];
        $rows[] = [];

        $rows[] = ['Account', 'Debit', 'Credit'];
        foreach ($this->report['accounts'] ?? [] as $account) {
            $rows[] = [
                $account['name'] ?? '',
                $account['debit'] ?? 0,
                $account['credit'] ?? 0,
            ];
        }

        $rows[] = ['Total', $this->report['total_debit'] ?? 0, $this->report['total_credit'] ?? 0];

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:A4')->getFont()->setBold(true);
                $headerRow = 6;
                $sheet->getStyle('A'.$headerRow.':C'.$headerRow)->getFont()->setBold(true);
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A'.$lastRow.':C'.$lastRow)->getFont()->setBold(true);
            },
        ];
    }
}
