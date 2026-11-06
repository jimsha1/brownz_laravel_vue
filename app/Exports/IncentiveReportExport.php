<?php

namespace App\Exports;

use App\Models\Incentive;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IncentiveReportExport implements FromCollection, WithHeadings, WithMapping
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
        $records = Incentive::with('employee')
            ->where('incentive_amount', '!=', '0.00')
            ->orderByDesc('start_date')
            ->orderByDesc('incentive_id');

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
            $records->whereHas('employee', function ($query) {
                $query->where('name', 'LIKE', "%{$this->employee_name}%");
            });
        }

        return $records->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Date',
            'Target Amount',
            'Total Amount',
            'Incentive(%)',
            'Incentive Amount',
        ];
    }

    public function map($Incentive): array
    {
        return [
            $Incentive->employee->name ?? '—',
            $Incentive->start_date ? \Carbon\Carbon::parse($Incentive->start_date)->format('M Y') : '—',
            $Incentive->target_amount ?? '0.00',
            $Incentive->total_amount ?? '0.00',
            $Incentive->incentive_percentage ?? '0.00',
            $Incentive->incentive_amount ?? '0.00',
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
