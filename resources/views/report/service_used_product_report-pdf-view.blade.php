<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Brownz Service Used Products</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 16px;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        h2 {
            text-align: center;
            margin: 0 0 12px;
        }

        .filters p {
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
            padding: 5px 6px;
            vertical-align: top;
        }

        th {
            text-align: center;
        }

        td.num {
            text-align: right;
            white-space: nowrap;
        }

        tfoot td {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h2><u>Service Used Products</u></h2>
    <div class="filters">
        <p>From: {{ !empty($start_date) ? $start_date : 'All' }}</p>
        <p>To: {{ !empty($end_date) ? $end_date : 'All' }}</p>
        <p>Status: {{ $status ?? 'Used' }}</p>
        <p>Search: {{ !empty($search) ? $search : 'All' }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Booking</th>
                <th>Client</th>
                <th>Service</th>
                <th>Beautician</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Unit</th>
                <th>Est. cost</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $key => $record)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $record['date'] ?: '—' }}</td>
                    <td>{{ $record['booking_code'] !== '' ? $record['booking_code'] : '—' }}</td>
                    <td>{{ $record['client_name'] !== '' ? $record['client_name'] : '—' }}</td>
                    <td>{{ $record['service_name'] ?: '—' }}</td>
                    <td>{{ $record['beautician_name'] !== '' ? $record['beautician_name'] : '—' }}</td>
                    <td>{{ $record['item_name'] ?: '—' }}</td>
                    <td class="num">{{ $record['quantity'] }}</td>
                    <td>{{ $record['uom_name'] ?: '—' }}</td>
                    <td class="num">{{ $record['estimated_cost'] }}</td>
                    <td>{{ $record['status'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11">No products used in this period.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($records->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="9">Estimated cost</td>
                    <td class="num">{{ $total_cost }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>

</html>
