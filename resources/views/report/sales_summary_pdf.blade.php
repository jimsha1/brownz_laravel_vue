<!DOCTYPE html>
<html>
<head>
    <title>Sales Summary PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <center><h1>Sales Summary Report</h1></center>
    <p><strong>Start Date:</strong> {{ $start_date }}</p>
    <p><strong>End Date:</strong> {{ $end_date }}</p>

    <table>
        <tr>
            <th>Total Sales</th>
            <td>{{ $total_sales }}</td>
        </tr>
        <tr>
            <th>Cash Sales</th>
            <td>{{ $cash_sales }}</td>
        </tr>
        <tr>
            <th>Online Sales</th>
            <td>{{ $online_sales }}</td>
        </tr>
        <tr>
            <th>Card Sales</th>
            <td>{{ $card_sales }}</td>
        </tr>
        <tr>
            <th>Credit Sales</th>
            <td>{{ $credit_sales }}</td>
        </tr>
        {{-- <tr>
            <th>Total Tax Amount</th>
            <td>{{ $total_tax_amount }}</td>
        </tr> --}}
    </table>
</body>
</html>
