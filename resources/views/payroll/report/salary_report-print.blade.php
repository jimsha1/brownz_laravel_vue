<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Salary Report') }}</title>
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

        <h2><u>{{ __('Salary Report') }}</u></h2>

        <div class="meta">
            <p>{{ __('Employee') }}: {{ $employee_label ?? __('All') }}</p>
            <p>{{ __('Salary Filter') }}: {{ $salary_filter_label ?? __('All Employees') }}</p>
            <p>{{ __('Period') }}:
                {{ $start_date ? \Carbon\Carbon::parse($start_date)->format('d-m-Y') : '—' }}
                – {{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d-m-Y') : '—' }}</p>
            <p>{{ __('Generated on') }}: {{ now()->format('d-m-Y H:i') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 56px;">{{ __('SL#') }}</th>
                    <th>{{ __('Voucher Date') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th class="amount">{{ __('Net Salary') }}</th>
                    <th class="amount">{{ __('Advance Salary') }}</th>
                    <th class="amount">{{ __('Paid Amount') }}</th>
                    <th class="amount">{{ __('Pending Salary') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $record->payment_date ? \Carbon\Carbon::parse($record->payment_date)->format('d M Y') : '—' }}</td>
                        <td>{{ $record->payroll->employee->name ?? '—' }}</td>
                        <td class="amount">{{ number_format((float) ($record->payroll->net_salary ?? 0), 2) }}</td>
                        <td class="amount">{{ number_format((float) ($record->payroll->advance ?? 0), 2) }}</td>
                        <td class="amount">{{ number_format((float) ($record->payment_amount ?? 0), 2) }}</td>
                        <td class="amount">{{ number_format((float) ($record->balance_amount ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">{{ __('No salary records found for the selected filters.') }}</td>
                    </tr>
                @endforelse
                @if ($records->count())
                    <tr style="background: #F7F7F9; font-weight: bold;">
                        <td></td>
                        <td colspan="2">{{ __('Total') }}</td>
                        <td class="amount">{{ number_format($total_net_salary, 2) }}</td>
                        <td class="amount">{{ number_format($total_advance, 2) }}</td>
                        <td class="amount">{{ number_format($total_paid, 2) }}</td>
                        <td class="amount">{{ number_format($total_pending, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        @if ($records->count())
            <div class="summary">
                {{ __('Records') }}: {{ $records->count() }}
                · {{ __('Total Paid') }}: {{ number_format($total_paid, 2) }}
                · {{ __('Total Pending') }}: {{ number_format($total_pending, 2) }}
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
