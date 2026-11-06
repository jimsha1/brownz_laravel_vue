<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultation Report</title>
    <style>
        @page {
            size: A4;
            margin: 12mm;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #222;
        }

        h2 {
            text-align: center;
            margin: 0 0 12px;
            font-size: 18px;
        }

        .filters {
            margin-bottom: 12px;
        }

        .filters p {
            margin: 2px 0;
            font-style: italic;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f3f3f3;
        }

        .empty {
            text-align: center;
            padding: 20px;
            color: #777;
        }
    </style>
</head>

<body>
    <h2>Consultation Report</h2>

    <div class="filters">
        @if (!empty($start_date) || !empty($end_date))
            <p>Date: {{ $start_date ?: '—' }} to {{ $end_date ?: '—' }}</p>
        @endif
        @if (!empty($client_name))
            <p>Client: {{ $client_name }}</p>
        @endif
        @if (!empty($mobile))
            <p>Mobile: {{ $mobile }}</p>
        @endif
        @if (!empty($type))
            <p>Type: {{ ucfirst($type) }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Type</th>
                <th>Client</th>
                <th>Mobile</th>
                <th>Beautician</th>
                <th>Approval</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->date ? \Carbon\Carbon::parse($row->date)->format('d-m-Y') : '—' }}</td>
                    <td>{{ $row->type ?: '—' }}</td>
                    <td>{{ $row->customer_name ?: '—' }}</td>
                    <td>{{ $row->customer_mobile ?: '—' }}</td>
                    <td>{{ $row->beautician_name ?: '—' }}</td>
                    <td>{{ $row->approve_status ?: 'pending' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty">No consultations found for the selected filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
