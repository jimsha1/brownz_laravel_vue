<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Day Book') }}</title>
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
    </style>
</head>

<body>
    <div class="container">
        <h2><u>{{ __('Day Book') }}</u></h2>

        <div class="meta">
            <p>{{ __('Period') }}: {{ $start_date ? \Carbon\Carbon::parse($start_date)->format('d-m-Y') : '—' }}
                – {{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d-m-Y') : '—' }}</p>
            <p>{{ __('Search') }}: {{ !empty($search) ? $search : __('All') }}</p>
            <p>{{ __('Account') }}: {{ !empty($account_name) ? $account_name : __('All') }}</p>
            <p>{{ __('Generated on') }}: {{ now()->format('d-m-Y H:i') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 56px;">{{ __('SL#') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Voucher#') }}</th>
                    <th>{{ __('Account') }}</th>
                    <th class="amount">{{ __('Debit') }}</th>
                    <th class="amount">{{ __('Credit') }}</th>
                    <th>{{ __('Particular') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ !empty($record['date']) ? \Carbon\Carbon::parse($record['date'])->format('d-m-Y') : '—' }}</td>
                        <td>{{ $record['voucher_no'] ?: '—' }}</td>
                        <td>{{ $record['account_name'] ?: '—' }}</td>
                        <td class="amount">{{ number_format((float) ($record['debit'] ?? 0), 2) }}</td>
                        <td class="amount">{{ number_format((float) ($record['credit'] ?? 0), 2) }}</td>
                        <td>{{ $record['particular'] ?: '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">{{ __('No transactions found for the selected filters.') }}</td>
                    </tr>
                @endforelse
                @if (count($records))
                    <tr style="background: #F7F7F9; font-weight: bold;">
                        <td colspan="4" class="amount">{{ __('Total') }}</td>
                        <td class="amount">{{ number_format($total_debit, 2) }}</td>
                        <td class="amount">{{ number_format($total_credit, 2) }}</td>
                        <td></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>

</html>
