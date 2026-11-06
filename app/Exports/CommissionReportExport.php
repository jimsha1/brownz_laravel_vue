<?php

namespace App\Exports;

use App\Models\Commission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CommissionReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private $employee_name = '',
        private $start_date = '',
        private $end_date = '',
        private $emp_id = null,
    ) {
    }

    public function collection()
    {
        $records = Commission::with('employee', 'employee.user')
            ->where('commission_amount', '!=', '0.00');

        $startDate = $this->normalizeReportDate($this->start_date);
        $endDate = $this->normalizeReportDate($this->end_date);

        if ($startDate && $endDate) {
            $records->whereBetween('start_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $records->where('start_date', '>=', $startDate);
        } elseif ($endDate) {
            $records->where('start_date', '<=', $endDate);
        }

        if (! empty($this->emp_id)) {
            $records->where('emp_id', $this->emp_id);
        } elseif (! empty($this->employee_name)) {
            $records->where('emp_name', 'LIKE', "%{$this->employee_name}%");
        }

        return $records->get();
    }

    public function headings(): array
    {
        return [
            'name',
            'Date',
            'Commission Amount',
        ];
    }

    public function map($Commission): array
    {
        return [
            $Commission->employee->name,
            $Commission->start_date ? \Carbon\Carbon::parse($Commission->start_date)->format('d M Y') : '---',
            $Commission->commission_amount,
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
