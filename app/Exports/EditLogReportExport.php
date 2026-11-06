<?php

namespace App\Exports;

use App\Service\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EditLogReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected string $startDate = '',
        protected string $endDate = '',
        protected string $sourceType = 'all',
        protected string $editedBy = '',
        protected string $search = ''
    ) {
    }

    public function collection()
    {
        $filters = (object) [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'source_type' => $this->sourceType,
            'edited_by' => $this->editedBy,
            'search' => $this->search,
        ];

        return app(ReportService::class)->editLogRows($filters);
    }

    public function headings(): array
    {
        return [
            'Edited At',
            'Type',
            'Reference',
            'Edit Reason',
            'Edited By',
        ];
    }

    public function map($row): array
    {
        return [
            $row['edited_at'] ?? '',
            $row['source_label'] ?? '',
            $row['reference_no'] !== '' ? $row['reference_no'] : '—',
            $row['edit_reason'] ?? '',
            $row['edited_by_name'] !== '' ? $row['edited_by_name'] : '—',
        ];
    }
}
