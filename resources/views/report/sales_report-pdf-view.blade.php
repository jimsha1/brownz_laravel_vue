<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brownz Sales Report</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

    <style>
        /* A4 Page Size */
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            font-size: 9px;
        }

        .container {
            padding: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .filters {
            font-size: 8px;
        }

        .filters p {
            margin: 2px 0;
            font-style: italic;
            text-align: left;
        }

        .table-responsive {
            margin: 0 auto;
            width: 100%;
            overflow: visible;
        }

        table {
            margin: 0 auto;
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 8px;
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
            padding: 4px 2px;
            word-wrap: break-word;
        }

        .col0 {
            width: 4%;
        }

        .col-date {
            width: 9%;
        }

        .col-code {
            width: 9%;
        }

        .col-name {
            width: 12%;
        }

        .col-amount {
            width: 9.4%;
        }

        tfoot td {
            font-weight: bold;
        }

    </style>
</head>

<body>
    <div class="container">
        <h2><u>Sales Report</u></h2>
        <div class="filters">
            <p>Start Date: {{ $start_date ?? 'N/A' }}</p>
            <p>End Date: {{ $end_date ?? 'N/A' }}</p>
            <p>Client Name: {{ $client_name ?? 'All' }}</p>
            <p>Booking Code: {{ $booking_code ?? 'All' }}</p>
            <p>Payment Mode: {{ $payment_mode ?? 'All' }}</p>
            <p>Type: {{ ($type ?? 'All') === 'Refund' ? 'Refund' : (($type ?? 'All') === 'Sale' || ($type ?? 'All') === 'Sales' ? 'Sales' : 'All') }}</p>
            <p>Service / Item: {{
                strtolower((string) ($line_type ?? '')) === 'service' ? 'Service'
                    : (in_array(strtolower((string) ($line_type ?? '')), ['item', 'product'], true) ? 'Item' : 'All')
            }}</p>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" class="col0">#</th>
                                <th scope="col" class="col-date">Booking Date</th>
                                <th scope="col" class="col-code">Booking Code</th>
                                <th scope="col" class="col-name">Client Name</th>
                                <th scope="col" class="col-amount">Type</th>
                                <th scope="col" class="col-amount">Service</th>
                                <th scope="col" class="col-amount">Item</th>
                                <th scope="col" class="col-amount">Net Total</th>
                                <th scope="col" class="col-amount">Tax Amount</th>
                                <th scope="col" class="col-amount">Discount</th>
                                <th scope="col" class="col-amount">Payment Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records as $key => $record)

                                <tr>
                                    <td class="col0">{{ $key + 1 }}</td>
                                    <td class="col-date">{{ \Carbon\Carbon::parse(optional($record->bookingServices)->appointment_date ?? $record->created_at)->format('Y-m-d') }}</td>
                                    <td class="col-code">{{ $record->booking_code }}</td>
                                    <td class="col-name">{{ $record->client_name}}</td>
                                    <td class="col-amount">{{ $record->booking_type ?? 'Sale' }}</td>
                                    <td class="col-amount">{{ number_format($record->service_sales_total ?? 0, 2) }}</td>
                                    <td class="col-amount">{{ number_format($record->item_sales_total ?? 0, 2) }}</td>
                                    <td class="col-amount">{{ number_format(($record->service_sales_total ?? 0) + ($record->item_sales_total ?? 0), 2) }}</td>
                                    <td class="col-amount">{{ number_format($record->tax_amount_total ?? 0, 2)}}</td>
                                    <td class="col-amount">{{ number_format(($record->discount_amount ?? 0) + ($record->individual_discount_amt ?? 0) + ($record->applied_discount_price ?? 0), 2)}}</td>
                                    <td class="col-amount">
                                        @php
                                            $sign = in_array(strtolower((string) ($record->booking_type ?? '')), ['refund', 'refunded'], true)
                                                && strcasecmp((string) ($type ?? 'All'), 'Refund') !== 0
                                                ? -1 : 1;
                                            if ($payment_mode && $record->bookingPayment->where('payment_mode', $payment_mode)->count()) {
                                                $payAmt = abs((float) $record->bookingPayment->where('payment_mode', $payment_mode)->sum(function ($payment) {
                                                    return (float) $payment->amount_taken - (float) ($payment->amount_given ?? 0);
                                                })) * $sign;
                                            } else {
                                                $payAmt = $record->signed_payment_total ?? (abs((float) $record->payment_total) * $sign);
                                            }
                                        @endphp
                                        {{ number_format($payAmt, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" style="text-align: right;"><strong>Total:</strong></td>
                                <td class="col-amount"><strong>{{ number_format($records->sum('service_sales_total'), 2) }}</strong></td>
                                <td class="col-amount"><strong>{{ number_format($records->sum('item_sales_total'), 2) }}</strong></td>
                                <td class="col-amount"><strong>{{ number_format($records->sum('service_sales_total') + $records->sum('item_sales_total'), 2) }}</strong></td>
                                <td class="col-amount"><strong>{{ number_format($records->sum('tax_amount_total'), 2) }}</strong></td>
                                <td class="col-amount"><strong>{{ number_format($records->sum('discount_amount') + $records->sum('individual_discount_amt') + $records->sum('applied_discount_price'), 2) }}</strong></td>
                                <td class="col-amount"><strong>{{ number_format($sum_of_amount ?? 0, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KinkN" crossorigin="anonymous">
    </script>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
</body>

</html>
