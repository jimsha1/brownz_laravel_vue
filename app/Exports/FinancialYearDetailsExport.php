<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class FinancialYearDetailsExport implements FromArray, WithEvents
{
    public function __construct(protected array $report)
    {
    }

    public function array(): array
    {
        $rows = [];
        $year = $this->report['financial_year'] ?? [];
        $closing = $this->report['closing_voucher'] ?? null;
        $opening = $this->report['opening_balance'] ?? [];

        $rows[] = ['Financial Year', $year['label'] ?? ''];
        $rows[] = ['Period', ($year['start_date'] ?? '').' to '.($year['end_date'] ?? '')];
        $rows[] = ['Status', ($year['status'] ?? '') === 'open' ? 'Open' : 'Closed'];
        if (! empty($year['closed_at'])) {
            $rows[] = ['Closed at', $year['closed_at'].(! empty($year['closed_by']) ? ' by '.$year['closed_by'] : '')];
        }
        $rows[] = [];

        $net = $this->report['net_profit_loss'] ?? null;
        if ($net !== null) {
            $rows[] = ['Net '.($net >= 0 ? 'Profit' : 'Loss'), number_format(abs((float) $net), 2, '.', '')];
            $effect = $this->report['retained_earnings_effect'] ?? ['debit' => 0, 'credit' => 0];
            $rows[] = ['Retained Earnings Dr', $effect['debit'] ?? 0];
            $rows[] = ['Retained Earnings Cr', $effect['credit'] ?? 0];
            $rows[] = [];
        }

        $rows[] = ['Closing Voucher', $closing['voucher_no'] ?? '—'];
        if ($closing) {
            $rows[] = ['Voucher Date', $closing['date'] ?? ''];
            $rows[] = ['Description', $closing['description'] ?? ''];
            $rows[] = [];
            $rows[] = ['Account', 'Remarks', 'Debit', 'Credit'];
            foreach ($closing['lines'] ?? [] as $line) {
                $rows[] = [
                    $line['account_name'] ?? '',
                    $line['remarks'] ?? '',
                    $line['debit'] ?? 0,
                    $line['credit'] ?? 0,
                ];
            }
            $rows[] = ['Total', '', $closing['total_debit'] ?? 0, $closing['total_credit'] ?? 0];
            $rows[] = [];
        }

        $rows[] = ['Opening Balance Audit', $opening['voucher_no'] ?? '—'];
        if (! empty($opening['accounts'])) {
            $rows[] = [];
            $rows[] = ['Account', 'Debit', 'Credit'];
            foreach ($opening['accounts'] as $account) {
                $rows[] = [
                    $account['name'] ?? '',
                    $account['debit'] ?? 0,
                    $account['credit'] ?? 0,
                ];
            }
            $rows[] = ['Total', $opening['total_debit'] ?? 0, $opening['total_credit'] ?? 0];
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:A6')->getFont()->setBold(true);
            },
        ];
    }
}
