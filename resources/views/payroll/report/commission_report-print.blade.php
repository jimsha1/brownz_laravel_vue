<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Commission Report') }}</title>
    <style>
        @page {
            size: A4;
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
            font-size: 12px;
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

        <h2><u>{{ __('Commission Report') }}</u></h2>

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
                    <th style="width: 56px;">{{ __('SL#') }}</th>
                    <th>{{ __('Employee Name') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th class="amount">{{ __('Commission Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $record->employee->name ?? '—' }}</td>
                        <td>{{ $record->start_date ? \Carbon\Carbon::parse($record->start_date)->format('d M Y') : '—' }}</td>
                        <td class="amount">{{ number_format((float) $record->commission_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">{{ __('No commission records found for the selected filters.') }}</td>
                    </tr>
                @endforelse
                @if ($records->count())
                    <tr style="background: #F7F7F9; font-weight: bold;">
                        <td></td>
                        <td colspan="2">{{ __('Total') }}</td>
                        <td class="amount">{{ number_format($total_commission, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        @if ($records->count())
            <div class="summary">
                {{ __('Records') }}: {{ $records->count() }}
                · {{ __('Total Commission') }}: {{ number_format($total_commission, 2) }}
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
