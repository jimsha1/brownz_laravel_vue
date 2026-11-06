<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brownz Attendance Report</title>
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
            border: 1px solid #96D4D4;
        }

        th,
        td {
            text-align: center;
            padding: 8px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2><u>Attendance Report</u></h2>
        <div class="filters">
            <p>Employee: {{ $employee_name ?: 'All' }}</p>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>{{ __('SI.No') }}</th>
                    <th>{{ __('Employee Name') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Check in') }}</th>
                    <th>{{ __('Check out') }}</th>
                    <th>{{ __('Attendance Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $key => $record)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $record->employee_name ?: ($record->employee?->name ?? '—') }}</td>
                        <td>
                            {{ $record->date ? \Carbon\Carbon::parse($record->date)->format('d-m-Y') : '—' }}
                        </td>
                        <td>
                            {{ $record->check_in ? \Carbon\Carbon::parse($record->check_in)->format('h:i A') : '—:—' }}
                        </td>
                        <td>
                            {{ $record->check_out ? \Carbon\Carbon::parse($record->check_out)->format('h:i A') : '—:—' }}
                        </td>
                        <td>{{ $record->attendance_status ? strtoupper($record->attendance_status) : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>

</html>
