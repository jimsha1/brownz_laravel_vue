<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Balance Sheet') }}</title>
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

        .columns {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }

        .column:last-child {
            padding-right: 0;
            padding-left: 10px;
        }

        .panel-title {
            font-size: 14px;
            font-weight: 700;
            margin: 0 0 8px;
        }

        .section-label {
            font-size: 12px;
            font-weight: 700;
            margin: 12px 0 6px;
            color: #6c757d;
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

        .amount {
            text-align: right;
            white-space: nowrap;
        }

        .footer-row td {
            font-weight: 700;
            background: #F7F7F9;
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
        <h2><u>{{ __('Balance Sheet') }}</u></h2>

        <div class="meta">
            <p>{{ __('Period') }}: {{ $start_date ? \Carbon\Carbon::parse($start_date)->format('d-m-Y') : '—' }}
                – {{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d-m-Y') : '—' }}</p>
            <p>{{ __('Generated on') }}: {{ now()->format('d-m-Y H:i') }}</p>
        </div>

        <div class="columns">
            <div class="column">
                <div class="panel-title">{{ __('Assets') }}</div>
                <table>
                    <tbody>
                        @forelse ($assets as $item)
                            <tr>
                                <td>{{ $item['name'] ?? '—' }}</td>
                                <td class="amount">{{ number_format((float) ($item['balance'] ?? 0), 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">{{ __('No asset balances') }}</td>
                            </tr>
                        @endforelse
                        <tr class="footer-row">
                            <td>{{ __('Total Assets') }}</td>
                            <td class="amount">{{ number_format($total_assets ?? 0, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="column">
                <div class="panel-title">{{ __('Liabilities & Equity') }}</div>

                <div class="section-label">{{ __('Liabilities') }}</div>
                <table>
                    <tbody>
                        @forelse ($liabilities as $item)
                            <tr>
                                <td>{{ $item['name'] ?? '—' }}</td>
                                <td class="amount">{{ number_format((float) ($item['balance'] ?? 0), 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">{{ __('No liability balances') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="section-label">{{ __('Equity') }}</div>
                <table>
                    <tbody>
                        @forelse ($equity as $item)
                            <tr>
                                <td>{{ $item['name'] ?? '—' }}</td>
                                <td class="amount">{{ number_format((float) ($item['balance'] ?? 0), 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">{{ __('No equity balances') }}</td>
                            </tr>
                        @endforelse
                        <tr class="footer-row">
                            <td>{{ __('Total Liabilities & Equity') }}</td>
                            <td class="amount">{{ number_format($total_liabilities_and_equity ?? 0, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="summary {{ ($balance_check ?? false) ? 'is-balanced' : 'is-unbalanced' }}">
            @if ($balance_check ?? false)
                {{ __('Balance sheet is balanced.') }}
            @else
                {{ __('Balance sheet is not balanced.') }}
                {{ __('Difference') }}: {{ number_format($balance_difference ?? 0, 2) }}
            @endif
        </div>
    </div>
</body>

</html>
