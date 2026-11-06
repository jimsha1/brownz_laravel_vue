<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brownz Tax Report</title>
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
            margin-bottom: 12px;
        }

        .filters p,
        .summary p {
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
    @php
        $taxType = $tax_type ?? 'output';
        $isInput = $taxType === 'input';
        $records = collect($records ?? []);
        $totals = $totals ?? [];
        $summary = $summary ?? ['output_tax' => 0, 'input_tax' => 0, 'net_tax' => 0];
    @endphp

    <div class="container">
        <h2><u>{{ $isInput ? 'Input Tax Report' : 'Output Tax Report' }}</u></h2>

        <div class="filters">
            <p>Start Date: {{ !empty($start_date) ? $start_date : 'All' }}</p>
            <p>End Date: {{ !empty($end_date) ? $end_date : 'All' }}</p>
            @if ($isInput)
                <p>Purchase Ref / Invoice: {{ !empty($purchase_ref) ? $purchase_ref : 'All' }}</p>
                <p>Vendor: {{ !empty($vendor_name) ? $vendor_name : 'All' }}</p>
                <p>Type: {{ !empty($type) && strtolower($type) !== 'all' ? $type : 'All' }}</p>
            @else
                <p>Booking Code: {{ !empty($booking_code) ? $booking_code : 'All' }}</p>
                <p>Type: {{ !empty($type) && strtolower($type) !== 'all' ? $type : 'All' }}</p>
            @endif
        </div>

        <br>
        <div class="summary">
            <p>Period Output Tax: {{ number_format((float) ($summary['output_tax'] ?? 0), 2) }}</p>
            <p>Period Input Tax: {{ number_format((float) ($summary['input_tax'] ?? 0), 2) }}</p>
            <p>Net GST Payable: {{ number_format((float) ($summary['net_tax'] ?? 0), 2) }}</p>
        </div>
        <br>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            @if ($isInput)
                                <tr>
                                    <th>#</th>
                                    <th>Purchase Ref</th>
                                    <th>Invoice No</th>
                                    <th>Date</th>
                                    <th>Vendor</th>
                                    <th>GST(%)</th>
                                    <th>Taxable Base</th>
                                    <th>Input Tax</th>
                                </tr>
                            @else
                                <tr>
                                    <th>#</th>
                                    <th>Booking No</th>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Type</th>
                                    <th>GST(%)</th>
                                    <th>Service Tax</th>
                                    <th>Item Tax</th>
                                    <th>Output Tax</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @forelse ($records as $key => $record)
                                @php $row = (array) $record; @endphp
                                @if ($isInput)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $row['ref_no'] ?? '' }}</td>
                                        <td>{{ $row['invoice_no'] ?? '-' }}</td>
                                        <td>{{ $row['date'] ?? '-' }}</td>
                                        <td>{{ $row['party'] ?? '-' }}</td>
                                        <td>{{ $row['tax_percent'] ?? '-' }}</td>
                                        <td>{{ number_format((float) ($row['taxable_base'] ?? 0), 2) }}</td>
                                        <td>{{ number_format((float) ($row['tax_amount'] ?? 0), 2) }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $row['ref_no'] ?? '' }}</td>
                                        <td>{{ $row['date'] ?? '-' }}</td>
                                        <td>{{ $row['party'] ?? '-' }}</td>
                                        <td>{{ $row['booking_type'] ?? '-' }}</td>
                                        <td>{{ $row['tax_percent'] ?? '-' }}</td>
                                        <td>{{ number_format((float) ($row['service_tax'] ?? 0), 2) }}</td>
                                        <td>{{ number_format((float) ($row['item_tax'] ?? 0), 2) }}</td>
                                        <td>{{ number_format((float) ($row['tax_amount'] ?? 0), 2) }}</td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="{{ $isInput ? 8 : 9 }}">No tax records found for the selected filters.</td>
                                </tr>
                            @endforelse

                            @if ($records->count())
                                @if ($isInput)
                                    <tr style="background-color:#f5f5f5;font-weight:bold;">
                                        <td colspan="6" class="text-right">Total</td>
                                        <td>{{ number_format((float) ($totals['taxable_base'] ?? 0), 2) }}</td>
                                        <td>{{ number_format((float) ($totals['tax_amount'] ?? 0), 2) }}</td>
                                    </tr>
                                @else
                                    <tr style="background-color:#f5f5f5;font-weight:bold;">
                                        <td colspan="6" class="text-right">Total</td>
                                        <td>{{ number_format((float) ($totals['service_tax'] ?? 0), 2) }}</td>
                                        <td>{{ number_format((float) ($totals['item_tax'] ?? 0), 2) }}</td>
                                        <td>{{ number_format((float) ($totals['tax_amount'] ?? 0), 2) }}</td>
                                    </tr>
                                @endif
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
