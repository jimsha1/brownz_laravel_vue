<!DOCTYPE html>
<html>

<head>
    <title>Beautician Summary PDF</title>
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
        <h1>Beautician Summary Report</h1>
    </center>

    <p><strong>Start Date:</strong> {{ $start_date }}</p>
    <p><strong>End Date:</strong> {{ $end_date }}</p>
    <p><strong>Beautician Name:</strong> {{ $beautician_id }}</p>

    <table>
        <thead>
            <tr>
                <th>Beautician Name</th>
                <th>NO.Of Service</th>
                <th>Amount</th>
                <th>Commission Amount</th>
                <th>Target</th>
                <th>Balance Amount</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($data as $item)
                <tr>
                    <td>{{ $item['beautician_name'] }}</td>
                    <td>{{ $item['number_of_services'] }}</td>
                    <td>{{ $item['sum_total_amount'] }}</td>
                    <td>{{ number_format((float) ($item['sum_commission_amount'] ?? 0), 2, '.', '') }}</td>
                    <td>{{ $item['target_value'] }}</td>
                    <td>{{ $item['balance_amount'] }}</td>
                </tr>

            @endforeach

        </tbody>
    </table>
</body>

</html>
