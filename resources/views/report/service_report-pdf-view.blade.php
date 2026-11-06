<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brownz Service Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

    <style>
        @page {
            size: A4;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .filters p {
            margin: 0;
            font-style: italic;
            text-align: left;
        }

        table {
            margin: 0 auto;
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            border-color: #96D4D4;
            text-align: center;
            padding: 8px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2><u>Service Report</u></h2>
        <div class="filters">
            <p>Start Date: {{ !empty($start_date) ? $start_date : 'All' }}</p>
            <p>End Date: {{ !empty($end_date) ? $end_date : 'All' }}</p>
            <p>Beautician Name: {{ !empty($beautician_id) ? $beautician_id : 'All' }}</p>
            <p>Service Name: {{ !empty($service_name) ? $service_name : 'All' }}</p>
            <p>Booking Code: {{ !empty($booking_code) ? $booking_code : 'All' }}</p>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Booking Date</th>
                                <th scope="col">Booking Code</th>
                                <th scope="col">Beautician Name</th>
                                <th scope="col">Service Name</th>
                                <th scope="col">Service Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $key => $record)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        {{ $record->appointment_date ? \Carbon\Carbon::parse($record->appointment_date)->format('j F, Y') : '-' }}
                                    </td>
                                    <td>{{ optional($record->booking)->booking_code ?: '-' }}</td>
                                    <td>{{ optional($record->beautician)->name ?: '-' }}</td>
                                    <td>{{ $record->service_name ?: '-' }}</td>
                                    <td>{{ number_format((float) ($record->line_total ?? 0), 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">No services found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if (collect($records)->count())
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-right"><strong>Total Service Amount:</strong></td>
                                    <td><strong>{{ $sum_of_amount ?? '0.00' }}</strong></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
