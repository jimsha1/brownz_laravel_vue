<!DOCTYPE html>
<html>
<body>
    <h1>Sales Report</h1>
    <table>
        <thead>
            <tr>
                <th>Booking Date</th>
                <th>Booking Code</th>
                <th>Client Name</th>
                <th>Booking Total</th>
                <th>Payment Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $booking)
            <tr>
                <td>{{ \Carbon\Carbon::parse($booking->created_at)->format('d-m-Y') }}</td>
                <td>{{ $booking->booking_code }}</td>
                <td>{{ $booking->client_name }}</td>
                <td>{{ $booking->booking_total }}</td>
                <td>{{ $booking->payment_total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>