<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brownz Client Report</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

    <style>
        /* A4 Page Size */
        @page {
            size: A4;
            /* margin: 20mm; */
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

        caption {
            caption-side: top;
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2><u>Client Report</u></h2>
        <div class="filters">
            <p>Client Name: {{ !empty($client_name) ? $client_name : 'All' }}</p>
            <p>Client Mobile: {{ !empty($mobile) ? $mobile : 'All' }}</p>
            <p>Status: {{ !empty($status) && strtolower($status) !== 'all' ? ucfirst($status) : 'All' }}</p>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" class="col0">#</th>
                                <th scope="col" class="col1">Client Name</th>
                                <th scope="col" class="col1">Client Username</th>
                                <th scope="col" class="col1">Client Mobile</th>
                                <th scope="col" class="col1">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $key => $record)
                                <tr>
                                    <td class="col0">{{ $key + 1 }}</td>
                                    <td class="col1">{{ optional($record->user)->name }}</td>
                                    <td class="col1">{{ optional($record->user)->username }}</td>
                                    <td class="col1">{{ optional($record->user)->mobile }}</td>
                                    <td class="col1">{{ ucfirst((string) optional($record->user)->user_status) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">No clients found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
