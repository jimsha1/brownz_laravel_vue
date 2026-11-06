<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brownz Incentive Report</title>
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
        <h2><u>Incentive Report</u></h2>
        <div class="filters">
            <p>Incentive Name: {{ $name ?? 'All' }}</p>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        {{-- <caption class="text-center">Client Report</caption> --}}
                        <thead>
                            <tr>
                                <th scope="col">{{ __('SI.No') }} </th>
                                <th scope="col">{{ __('Employee Name') }} </th>
                                <th scope="col">{{ __('Date') }}</th>
                                <th scope="col">{{ __('Target Amount') }}</th>
                                <th scope="col">{{ __('Total Amount') }}</th>
                                <th scope="col">{{ __('Incentive(%)') }}</th>
                                <th scope="col">{{ __('Incentive Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records as $key => $record)
                                <tr>
                                    <td class="col0">{{ $key + 1 }}</td>
                                    <td class="col1">{{ $record->employee->name }}</td>
                                    <td class="col1">{{ \Carbon\Carbon::parse($record->start_date)->format('M Y') }}</td>
                                    <td class="col1">{{ $record->target_amount  }}</td>
                                    <td class="col1">{{ $record->total_amount  }}</td>
                                    <td class="col1">{{ $record->incentive_percentage  }}</td>
                                    <td class="col1">{{ $record->incentive_amount  }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
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
