<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Stock Item Report</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 16px;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1D1D1D;
        }

        h2 {
            text-align: center;
            margin: 0 0 8px;
            font-size: 18px;
        }

        .filters p,
        .summary p {
            margin: 0;
            font-style: italic;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th,
        td {
            border: 1px solid #96D4D4;
            padding: 4px 5px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #F7F7F9;
        }

        .num {
            text-align: right;
            white-space: nowrap;
        }

        .total td {
            font-weight: bold;
            background: #F7F7F9;
        }
    </style>
</head>

<body>
    <h2>Stock Item Report</h2>
    <div class="filters">
        <p>Search: {{ $filters['search'] }}</p>
        <p>Status: {{ $filters['status'] }}</p>
        <p>Stock level: {{ $filters['stock_level'] }}</p>
        <p>Sales item: {{ $filters['sales'] }}</p>
    </div>
    <div class="summary">
        <p>
            Items: {{ $summary['items'] }}
            · Inventory value: {{ number_format($summary['inventory_value'], 2) }}
            · In stock: {{ $summary['in_stock'] }}
            · Low stock: {{ $summary['low_stock'] }}
            · Out of stock: {{ $summary['out_of_stock'] }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Group</th>
                <th class="num">Current Stock</th>
                <th>Stock Level</th>
                <th class="num">Stock In</th>
                <th class="num">Stock Out</th>
                <th>Last Movement</th>
                <th class="num">Value</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $index => $record)
                @php
                    $parent = $record->category->parent->group_name ?? null;
                    $group = $record->category->group_name ?? null;
                    $groupLabel = $parent && $group ? $parent.' » '.$group : ($group ?: '—');
                    $uom = $record->stockUom->uom_name ?? '';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ $record->item_name }}
                        <br>{{ $record->item_code }}
                    </td>
                    <td>
                        {{ $groupLabel }}
                        @if ($record->brand)
                            <br>{{ $record->brand->brand_name }}
                        @endif
                    </td>
                    <td class="num">{{ number_format((float) $record->current_stock, 2) }} {{ $uom }}</td>
                    <td>
                        {{ $record->stock_level_label }}
                        <br>{{ number_format((float) $record->packs_available, 2) }} packs
                    </td>
                    <td class="num">{{ number_format((float) $record->stock_in_total, 2) }}</td>
                    <td class="num">{{ number_format((float) $record->stock_out_total, 2) }}</td>
                    <td>
                        {{ $record->last_movement_at ?: '—' }}
                        <br>{{ (int) $record->movement_count }} movements
                    </td>
                    <td class="num">{{ number_format((float) $record->inventory_value, 2) }}</td>
                    <td>{{ $record->is_active ? 'Active' : 'Inactive' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">No stock items found for the selected filters.</td>
                </tr>
            @endforelse
            @if ($records->count())
                <tr class="total">
                    <td colspan="5">Total</td>
                    <td class="num">{{ number_format($totals['stock_in'], 2) }}</td>
                    <td class="num">{{ number_format($totals['stock_out'], 2) }}</td>
                    <td></td>
                    <td class="num">{{ number_format($totals['inventory_value'], 2) }}</td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>
</body>

</html>
