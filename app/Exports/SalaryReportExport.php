<?php

namespace App\Exports;

use App\Models\PayrollPayments;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalaryReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private $employee_name = '',
        private $start_date = '',
        private $end_date = '',
        private $emp_id = null,
        private $balance_amount = null,
    ) {
    }

    public function collection()
    {
        $result = PayrollPayments::with('payroll.employee')
            ->orderByDesc('payment_date')
            ->orderByDesc('payroll_payment_id');

        $startDate = $this->normalizeReportDate($this->start_date);
        $endDate = $this->normalizeReportDate($this->end_date);

        if ($startDate && $endDate) {
            $result->whereBetween('payment_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $result->where('payment_date', '>=', $startDate);
        } elseif ($endDate) {
            $result->where('payment_date', '<=', $endDate);
        }

        if (! empty($this->emp_id)) {
            $result->whereHas('payroll', function ($subQuery) {
                $subQuery->where('emp_id', $this->emp_id);
            });
        } elseif (! empty($this->employee_name)) {
            $result->whereHas('payroll.employee', function ($subQuery) {
                $subQuery->where('name', 'LIKE', "%{$this->employee_name}%");
            });
        }

        if (! empty($this->balance_amount) && (string) $this->balance_amount === '1') {
            $result->where('balance_amount', '>', 0);
        }

        return $result->get();
    }

    public function headings(): array
    {
        return [
            'Voucher Date',
            'Name',
            'Net Salary',
            'Advance Salary',
            'Paid Amount',
            'Pending Salary',
            'Narrations',
        ];
    }

    public function map($salary): array
    {
        return [
            $salary->payment_date ? \Carbon\Carbon::parse($salary->payment_date)->format('d M Y') : '---',
            $salary->payroll->employee->name ?? '---',
            $salary->payroll->net_salary ?? '0.00',
            $salary->payroll->advance ?? '0.00',
            $salary->payment_amount ?? '0.00',
            $salary->balance_amount ?? '0.00',
            $salary->narrations ?? '',
        ];
    }

    private function normalizeReportDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return substr((string) $value, 0, 10);
    }
}
