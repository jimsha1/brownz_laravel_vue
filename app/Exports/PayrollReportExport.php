<?php

namespace App\Exports;

use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PayrollReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private $employee_name = '',
        private $start_date = '',
        private $end_date = '',
        private $payment_mode = '',
        private $emp_id = null,
    ) {
    }

    public function collection()
    {
        $payroll = Payroll::query()->with('employee')
            ->orderByDesc('year')
            ->orderByRaw("STR_TO_DATE(CONCAT(payrolls.month, ' 1 ', payrolls.year), '%M %d %Y') DESC")
            ->orderByDesc('payroll_id');

        $startDate = $this->normalizeReportDate($this->start_date);
        $endDate = $this->normalizeReportDate($this->end_date);
        $periodExpr = "STR_TO_DATE(CONCAT(payrolls.month, ' 1 ', payrolls.year), '%M %d %Y')";

        if ($startDate) {
            $payroll->whereRaw("{$periodExpr} >= ?", [substr($startDate, 0, 7).'-01']);
        }

        if ($endDate) {
            $payroll->whereRaw("{$periodExpr} <= ?", [substr($endDate, 0, 7).'-01']);
        }

        if (! empty($this->emp_id)) {
            $payroll->where('payrolls.emp_id', $this->emp_id);
        } elseif (! empty($this->employee_name)) {
            $payroll->whereHas('employee', function ($query) {
                $query->where('name', 'LIKE', "%{$this->employee_name}%");
            });
        }

        if (! empty($this->payment_mode)) {
            $payroll->where('payrolls.payment_mode', 'LIKE', "%{$this->payment_mode}%");
        }

        return $payroll->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Date',
            'Salary',
            'Advance',
            'Leave',
            'Deduction',
            'Payable Amount',
        ];
    }

    public function map($Payroll): array
    {
        $period = '—';
        if ($Payroll->month && $Payroll->year) {
            try {
                $period = \Carbon\Carbon::createFromFormat('F Y', $Payroll->month.' '.$Payroll->year)->format('M Y');
            } catch (\Throwable $e) {
                $period = $Payroll->month.' '.$Payroll->year;
            }
        }

        return [
            $Payroll->employee->name ?? '—',
            $period,
            $Payroll->basic_salary ?? '0.00',
            $Payroll->advance ?? '0.00',
            $Payroll->approve_leave ?? '0.00',
            $Payroll->deduction ?? '0.00',
            $Payroll->payable_amount ?? '0.00',
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
