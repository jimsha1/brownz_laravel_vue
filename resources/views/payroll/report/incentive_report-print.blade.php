<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Incentive Report') }}</title>
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

        .amount {
            text-align: right;
            white-space: nowrap;
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

        <h2><u>{{ __('Incentive Report') }}</u></h2>

        <div class="meta">
            <p>{{ __('Employee') }}: {{ $employee_label ?? __('All') }}</p>
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
                    <th class="amount">{{ __('Target Amount') }}</th>
                    <th class="amount">{{ __('Total Amount') }}</th>
                    <th class="amount">{{ __('Incentive(%)') }}</th>
                    <th class="amount">{{ __('Incentive Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $record->employee->name ?? '—' }}</td>
                        <td>{{ $record->start_date ? \Carbon\Carbon::parse($record->start_date)->format('M Y') : '—' }}</td>
                        <td class="amount">{{ number_format((float) ($record->target_amount ?? 0), 2) }}</td>
                        <td class="amount">{{ number_format((float) ($record->total_amount ?? 0), 2) }}</td>
                        <td class="amount">{{ number_format((float) ($record->incentive_percentage ?? 0), 2) }}</td>
                        <td class="amount">{{ number_format((float) ($record->incentive_amount ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">{{ __('No incentive records found for the selected filters.') }}</td>
                    </tr>
                @endforelse
                @if ($records->count())
                    <tr style="background: #F7F7F9; font-weight: bold;">
                        <td></td>
                        <td colspan="2">{{ __('Total') }}</td>
                        <td class="amount">{{ number_format($total_target, 2) }}</td>
                        <td class="amount">{{ number_format($total_amount, 2) }}</td>
                        <td></td>
                        <td class="amount">{{ number_format($total_incentive, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        @if ($records->count())
            <div class="summary">
                {{ __('Records') }}: {{ $records->count() }}
                · {{ __('Total Incentive') }}: {{ number_format($total_incentive, 2) }}
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
