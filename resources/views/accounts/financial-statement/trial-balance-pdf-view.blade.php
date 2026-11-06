<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Trial Balance') }}</title>
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
            padding: 20px;
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

        .summary.is-balanced {
            color: #199900;
        }

        .summary.is-unbalanced {
            color: #D0342C;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2><u>{{ __('Trial Balance') }}</u></h2>

        <div class="meta">
            <p>{{ __('Period') }}: {{ $start_date ? \Carbon\Carbon::parse($start_date)->format('d-m-Y') : '—' }}
                – {{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d-m-Y') : '—' }}</p>
            <p>{{ __('Search') }}: {{ !empty($search) ? $search : __('All') }}</p>
            <p>{{ __('Generated on') }}: {{ now()->format('d-m-Y H:i') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 56px;">{{ __('SL#') }}</th>
                    <th>{{ __('Account Name') }}</th>
                    <th class="amount">{{ __('Debit') }}</th>
                    <th class="amount">{{ __('Credit') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $record['name'] ?? '—' }}</td>
                        <td class="amount">{{ number_format((float) ($record['debit_value'] ?? 0), 2) }}</td>
                        <td class="amount">{{ number_format((float) ($record['credit_value'] ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">{{ __('No balances found for the selected filters.') }}</td>
                    </tr>
                @endforelse
                @if (count($records))
                    <tr style="background: #F7F7F9; font-weight: bold;">
                        <td></td>
                        <td class="amount">{{ __('Total') }}</td>
                        <td class="amount">{{ number_format($total_debit, 2) }}</td>
                        <td class="amount">{{ number_format($total_credit, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        @if (count($records))
            <div class="summary {{ $balance_check ? 'is-balanced' : 'is-unbalanced' }}">
                @if ($balance_check)
                    {{ __('Trial Balance is balanced.') }}
                @else
                    {{ __('Trial Balance is not balanced.') }}
                    {{ __('Difference') }}: {{ number_format($balance_difference, 2) }}
                @endif
            </div>
        @endif
    </div>
</body>

</html>
