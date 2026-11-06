<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Attendance Report') }}</title>
    <style>
        @page {
            size: A4 landscape;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #1D1D1D;
        }

        .container {
            padding: 24px;
        }

        h2 {
            text-align: center;
            margin: 0 0 8px;
        }

        .meta {
            margin-bottom: 16px;
            font-size: 12px;
        }

        .meta p {
            margin: 0 0 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #96D4D4;
            padding: 8px;
            font-size: 11px;
        }

        th {
            background: #F7F7F9;
        }

        .summary {
            margin-top: 12px;
            font-size: 12px;
            font-weight: 700;
        }

        .print-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-bottom: 16px;
        }

        @media print {
            .print-actions {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="print-actions">
            <button type="button" onclick="window.print()">{{ __('Print') }}</button>
        </div>

        <h2><u>{{ __('Attendance Report') }}</u></h2>

        <div class="meta">
            <p>{{ __('Employee') }}: {{ $employee_label ?? __('All') }}</p>
            <p>{{ __('Attendance Type') }}: {{ $attendance_type_label ?? __('All') }}</p>
            <p>{{ __('Period') }}:
                {{ $start_date ? \Carbon\Carbon::parse($start_date)->format('d-m-Y') : '—' }}
                – {{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d-m-Y') : '—' }}</p>
            <p>{{ __('Generated on') }}: {{ now()->format('d-m-Y H:i') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 42px;">{{ __('SL#') }}</th>
                    <th>{{ __('Employee Name') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Shift') }}</th>
                    <th>{{ __('Check in') }}</th>
                    <th>{{ __('Check out') }}</th>
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{ $record['employee_name'] ?? '—' }}
                            @if (! empty($record['employee_code']))
                                <br><small>{{ $record['employee_code'] }}</small>
                            @endif
                        </td>
                        <td>
                            {{ ! empty($record['date']) ? \Carbon\Carbon::parse($record['date'])->format('d-m-Y') : '—' }}
                        </td>
                        <td>
                            @if (! empty($record['shift_start_time']) || ! empty($record['shift_end_time']))
                                {{ ! empty($record['shift_start_time']) ? \Carbon\Carbon::parse($record['shift_start_time'])->format('h:i A') : '—' }}
                                –
                                {{ ! empty($record['shift_end_time']) ? \Carbon\Carbon::parse($record['shift_end_time'])->format('h:i A') : '—' }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            {{ ! empty($record['check_in']) ? \Carbon\Carbon::parse($record['check_in'])->format('h:i A') : '—:—' }}
                            @if (! empty($record['is_late']))
                                ({{ __('Late') }})
                            @endif
                        </td>
                        <td>
                            {{ ! empty($record['check_out']) ? \Carbon\Carbon::parse($record['check_out'])->format('h:i A') : '—:—' }}
                            @if (! empty($record['is_overtime']))
                                ({{ __('OT') }})
                            @endif
                        </td>
                        <td>{{ ! empty($record['attendance_status']) ? strtoupper($record['attendance_status']) : __('Not marked') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">{{ __('No attendance records found for the selected filters.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if (count($records))
            <div class="summary">
                {{ __('Records') }}: {{ count($records) }}
            </div>
        @endif
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 350);
        });
    </script>
</body>

</html>
