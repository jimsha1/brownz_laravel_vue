<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Working Hours Report') }}</title>
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
            font-size: 10px;
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

        <h2><u>{{ __('Working Hours Report') }}</u></h2>

        <div class="meta">
            <p>{{ __('Report Type') }}:
                @if (($tab ?? 'daily') === 'summary')
                    {{ __('Summary') }}
                @else
                    {{ __('Daily') }}
                @endif
            </p>
            <p>{{ __('Employee') }}: {{ $employee_label ?? __('All') }}</p>
            @if (($tab ?? 'daily') === 'summary')
                <p>{{ __('Period') }}:
                    {{ $start_date ? \Carbon\Carbon::parse($start_date)->format('d-m-Y') : '—' }}
                    – {{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d-m-Y') : '—' }}</p>
            @else
                <p>{{ __('Date') }}:
                    {{ ($filter_date ?? $start_date) ? \Carbon\Carbon::parse($filter_date ?? $start_date)->format('d-m-Y') : '—' }}</p>
            @endif
            <p>{{ __('Generated on') }}: {{ now()->format('d-m-Y H:i') }}</p>
        </div>

        @if (($tab ?? 'daily') === 'summary')
            <table>
                <thead>
                    <tr>
                        <th style="width: 42px;">{{ __('SL#') }}</th>
                        <th>{{ __('Employee') }}</th>
                        <th>{{ __('Days') }}</th>
                        <th>{{ __('Working Hours') }}</th>
                        <th>{{ __('Free Time') }}</th>
                        <th>{{ __('Over Time') }}</th>
                        <th>{{ __('Services') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $index => $record)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $record->employee_name ?? '—' }}</td>
                            <td>{{ $record->total_days ?? '—' }}</td>
                            <td>{{ $record->total_working_hours ?? '00:00:00' }}</td>
                            <td>{{ $record->total_free_time ?? '00:00:00' }}</td>
                            <td>{{ $record->total_over_time ?? '00:00:00' }}</td>
                            <td>{{ $record->total_services ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">{{ __('No summary records found for the selected filters.') }}</td>
                        </tr>
                    @endforelse
                    @if (count($records))
                        <tr style="background: #F7F7F9; font-weight: bold;">
                            <td></td>
                            <td>{{ __('Total') }}</td>
                            <td></td>
                            <td>{{ $totals['total_working_hours'] ?? '00:00:00' }}</td>
                            <td>{{ $totals['total_free_time'] ?? '00:00:00' }}</td>
                            <td>{{ $totals['total_over_time'] ?? '00:00:00' }}</td>
                            <td>{{ $totals['total_services'] ?? 0 }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 42px;">{{ __('SL#') }}</th>
                        <th>{{ __('Employee') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Shift') }}</th>
                        <th>{{ __('Check In') }}</th>
                        <th>{{ __('Check Out') }}</th>
                        <th>{{ __('Working Hours') }}</th>
                        <th>{{ __('Free Time') }}</th>
                        <th>{{ __('Over Time') }}</th>
                        <th>{{ __('Services') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $index => $record)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $record->employee_name ?? '—' }}</td>
                            <td>{{ ! empty($record->appointment_date) ? \Carbon\Carbon::parse($record->appointment_date)->format('d-m-Y') : '—' }}</td>
                            <td>
                                {{ ! empty($record->start_time) ? \Carbon\Carbon::parse($record->start_time)->format('h:i A') : '—' }}
                                –
                                {{ ! empty($record->end_time) ? \Carbon\Carbon::parse($record->end_time)->format('h:i A') : '—' }}
                            </td>
                            <td>{{ ! empty($record->check_in) ? \Carbon\Carbon::parse($record->check_in)->format('h:i A') : '—:—' }}</td>
                            <td>{{ ! empty($record->check_out) ? \Carbon\Carbon::parse($record->check_out)->format('h:i A') : '—:—' }}</td>
                            <td>{{ $record->total_working_time ?? '00:00:00' }}</td>
                            <td>{{ $record->free_time ?? '00:00:00' }}</td>
                            <td>{{ $record->over_time ?? '00:00:00' }}</td>
                            <td>{{ $record->service_count ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">{{ __('No working hours records found for the selected filters.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif

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
