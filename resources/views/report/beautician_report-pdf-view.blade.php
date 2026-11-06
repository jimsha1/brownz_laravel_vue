<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brownz Beautician Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

    <style>
        @page {
            size: A4 landscape;
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
        <h2><u>Beautician Report</u></h2>
        <div class="filters">
            <p>Start Date: {{ !empty($start_date) ? $start_date : 'All' }}</p>
            <p>End Date: {{ !empty($end_date) ? $end_date : 'All' }}</p>
            <p>Booking Code: {{ !empty($booking_code) ? $booking_code : 'All' }}</p>
            <p>Beautician Name: {{ !empty($beautician_id) ? $beautician_id : 'All' }}</p>
            <p>Type: {{ !empty($line_type) ? ucfirst($line_type) : 'All' }}</p>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Booking Code</th>
                                <th>Beautician Name</th>
                                <th>Type</th>
                                <th>Service / Product</th>
                                <th>Service Amount</th>
                                <th>Split Amount</th>
                                <th>Commission Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $key => $record)
                                @php
                                    $date = $record->report_date ?? $record->created_at;
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $date ? \Carbon\Carbon::parse($date)->format('j F, Y') : '-' }}</td>
                                    <td>{{ optional($record->booking)->booking_code ?: '-' }}</td>
                                    <td>{{ optional($record->beautician)->name ?: '-' }}</td>
                                    <td>{{ $record->line_type_label ?? ((!empty($record->booking_item_id) || !empty($record->item_id)) ? 'Product' : 'Service') }}</td>
                                    <td>{{ $record->service_name ?: '-' }}</td>
                                    <td>{{ number_format((float) ($record->total_amount ?? 0), 2) }}</td>
                                    <td>{{ number_format((float) ($record->price ?? 0), 2) }}</td>
                                    <td>{{ number_format((float) ($record->commission_amount ?? 0), 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">No beautician records found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if (collect($records)->count())
                            <tfoot>
                                <tr>
                                    <td colspan="7" class="text-right"><strong>Total:</strong></td>
                                    <td><strong>{{ $sum_of_amount ?? '0.00' }}</strong></td>
                                    <td><strong>{{ $sum_of_commission ?? '0.00' }}</strong></td>
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
