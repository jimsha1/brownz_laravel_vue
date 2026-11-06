<?php

namespace App\Exports;

use App\Models\Vendor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;

class VendorReportExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $totals = ['total_purchase_amount' => 0, 'total_credit_amount' => 0];

    public function __construct(
        protected string $vendorName = '',
        protected string $vendorEmail = '',
        protected string $vendorPhone = ''
    ) {
    }

    public function collection()
    {
        $reportService = app(\App\Service\ReportService::class);

        return Vendor::query()
            ->withSum('stockPurchases', 'total_purchase_amount')
            ->when($this->vendorName, fn ($q) => $q->where('name', 'LIKE', '%' . $this->vendorName . '%'))
            ->when($this->vendorEmail, fn ($q) => $q->where('email', 'LIKE', '%' . $this->vendorEmail . '%'))
            ->when($this->vendorPhone, fn ($q) => $q->where('phone', 'LIKE', '%' . $this->vendorPhone . '%'))
            ->orderBy('name', 'desc')
            ->get()
            ->map(function ($vendor) use ($reportService) {
                $vendor->total_purchase_amount = (float) ($vendor->stock_purchases_sum_total_purchase_amount ?? 0);
                $vendor->credit_amount = $reportService->getVendorCreditAmount($vendor->vendor_id);
                $this->totals['total_purchase_amount'] += $vendor->total_purchase_amount;
                $this->totals['total_credit_amount'] += $vendor->credit_amount;
                return $vendor;
            });
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function ($event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $totalRow = $lastRow + 1;
                $sheet->setCellValue('A' . $totalRow, 'Total');
                $sheet->setCellValue('D' . $totalRow, number_format($this->totals['total_purchase_amount'], 2));
                $sheet->setCellValue('E' . $totalRow, number_format($this->totals['total_credit_amount'], 2));
                $sheet->getStyle('A' . $totalRow . ':E' . $totalRow)->getFont()->setBold(true);
            },
        ];
    }

    public function headings(): array
    {
        return [
            'Vendor Name',
            'Email',
            'Phone',
            'Purchase Amount',
            'Balance (Credit Amount)',
            'Status',
        ];
    }

    public function map($vendor): array
    {
        return [
            $vendor->name,
            $vendor->email ?? '',
            $vendor->phone ?? '',
            number_format($vendor->total_purchase_amount ?? 0, 2),
            number_format($vendor->credit_amount ?? 0, 2),
            $vendor->is_active ? 'Active' : 'Inactive',
        ];
    }
}
