<!DOCTYPE html>
<html>

<head>
    <title>Service Summary PDF</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .summary-table {
            margin-top: 40px;
        }
    </style>
</head>

<body>
    <center>
        <h1>Service Summary Report</h1>
    </center>

    <p><strong>Start Date:</strong> {{ $start_date }}</p>
    <p><strong>End Date:</strong> {{ $end_date }}</p>
    <p><strong>Beautician Name:</strong> {{ $beautician_name ?? 'N/A' }}</p>
    <p><strong>Service Name:</strong> {{ $service_name ?? 'N/A' }}</p>

    <table>
        <thead>
            <tr>
                <th>Service Name</th>
                <th>Count</th>
                <th>Service Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
      
            @foreach ($data['summary'] as $item)
                <tr>
                    <td>{{ $item['service_name'] ?? 'NA' }}</td>
                    <td>{{ $item['count'] ?? 'N/A' }}</td>
                    <td>{{ number_format($item['total_service_price'], 2) }}</td>
                </tr>
                @php $total += $item['total_service_price']; @endphp
            @endforeach
            <tr>
                <th colspan="2">Total Service Amount</th>
                <th>{{ number_format($total, 2) }}</th>
            </tr>
        </tbody>
    </table>
</body>

</html>
