<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockItemReportExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
    protected array $totals = [
        'stock_in' => 0,
        'stock_out' => 0,
        'inventory_value' => 0,
    ];

    public function __construct(protected Collection $records)
    {
        $this->totals['stock_in'] = round((float) $records->sum('stock_in_total'), 2);
        $this->totals['stock_out'] = round((float) $records->sum('stock_out_total'), 2);
        $this->totals['inventory_value'] = round((float) $records->sum('inventory_value'), 2);
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'Item Code',
            'Item Name',
            'Group',
            'Brand',
            'Current Stock',
            'UOM',
            'Packs',
            'Stock Level',
            'Stock In',
            'Stock Out',
            'Movements',
            'Last Movement',
            'Inventory Value',
            'Cost',
            'Status',
        ];
    }

    public function map($item): array
    {
        return [
            $item->item_code,
            $item->item_name,
            $this->groupLabel($item),
            optional($item->brand)->brand_name ?? '',
            number_format((float) $item->current_stock, 2, '.', ''),
            optional($item->stockUom)->uom_name ?? '',
            number_format((float) $item->packs_available, 2, '.', ''),
            $item->stock_level_label,
            number_format((float) $item->stock_in_total, 2, '.', ''),
            number_format((float) $item->stock_out_total, 2, '.', ''),
            (int) $item->movement_count,
            $item->last_movement_at ?? '',
            number_format((float) $item->inventory_value, 2, '.', ''),
            number_format((float) ($item->cost_price ?? 0), 2, '.', ''),
            $item->is_active ? 'Active' : 'Inactive',
        ];
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function ($event) {
                $sheet = $event->sheet->getDelegate();
                $totalRow = $sheet->getHighestRow() + 1;
                $sheet->setCellValue('A'.$totalRow, 'Total');
                $sheet->setCellValue('I'.$totalRow, number_format($this->totals['stock_in'], 2, '.', ''));
                $sheet->setCellValue('J'.$totalRow, number_format($this->totals['stock_out'], 2, '.', ''));
                $sheet->setCellValue('M'.$totalRow, number_format($this->totals['inventory_value'], 2, '.', ''));
                $sheet->getStyle('A'.$totalRow.':O'.$totalRow)->getFont()->setBold(true);
            },
        ];
    }

    private function groupLabel($item): string
    {
        $parent = $item->category->parent->group_name ?? null;
        $group = $item->category->group_name ?? null;

        if ($parent && $group) {
            return $parent.' » '.$group;
        }

        return $group ?: '';
    }
}
