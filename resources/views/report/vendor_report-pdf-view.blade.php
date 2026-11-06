<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brownz Vendor Report</title>
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

        .table-responsive {
            margin: 0 auto;
            width: 100%;
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
        <h2><u>Vendor Report</u></h2>
        <div class="filters">
            <p>Vendor Name: {{ !empty($vendor_name) ? $vendor_name : 'All' }}</p>
            <p>Vendor Email: {{ !empty($vendor_email) ? $vendor_email : 'All' }}</p>
            <p>Vendor Phone: {{ !empty($vendor_phone) ? $vendor_phone : 'All' }}</p>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" class="col0">#</th>
                                <th scope="col" class="col1">Vendor Name</th>
                                <th scope="col" class="col1">Email</th>
                                <th scope="col" class="col1">Phone</th>
                                <th scope="col" class="col1">Purchase Amount</th>
                                <th scope="col" class="col1">Balance (Credit Amount)</th>
                                <th scope="col" class="col1">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $key => $record)
                                <tr>
                                    <td class="col0">{{ $key + 1 }}</td>
                                    <td class="col1">{{ $record->name }}</td>
                                    <td class="col1">{{ $record->email ?? '-' }}</td>
                                    <td class="col1">{{ $record->phone ?? '-' }}</td>
                                    <td class="col1">{{ number_format($record->total_purchase_amount ?? 0, 2) }}</td>
                                    <td class="col1">{{ number_format($record->credit_amount ?? 0, 2) }}</td>
                                    <td class="col1">{{ $record->is_active ? 'Active' : 'Inactive' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">No vendors found for the selected filters.</td>
                                </tr>
                            @endforelse
                            @if ($records->count())
                                <tr style="background-color: #f5f5f5; font-weight: bold;">
                                    <td colspan="4" class="text-right">Total</td>
                                    <td>{{ number_format($totals['total_purchase_amount'] ?? 0, 2) }}</td>
                                    <td>{{ number_format($totals['total_credit_amount'] ?? 0, 2) }}</td>
                                    <td></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
