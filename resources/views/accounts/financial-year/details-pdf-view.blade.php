<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Financial Year Details') }} - {{ $financial_year['label'] ?? '' }}</title>
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

        .summary {
            margin-bottom: 16px;
            padding: 10px 12px;
            background: #e8f4fd;
            border: 1px solid #b8daf0;
            font-size: 12px;
        }

        h3 {
            margin: 18px 0 8px;
            font-size: 14px;
        }

        .section-note {
            margin: 0 0 8px;
            font-size: 12px;
            color: #6c757d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
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
        <h2><u>{{ __('Financial Year Details') }} - {{ $financial_year['label'] ?? '' }}</u></h2>

        <div class="meta">
            <p>
                {{ __('Period') }}:
                {{ ! empty($financial_year['start_date']) ? \Carbon\Carbon::parse($financial_year['start_date'])->format('d/m/Y') : '—' }}
                –
                {{ ! empty($financial_year['end_date']) ? \Carbon\Carbon::parse($financial_year['end_date'])->format('d/m/Y') : '—' }}
                | {{ __('Status') }}: {{ ($financial_year['status'] ?? '') === 'open' ? __('Open') : __('Closed') }}
            </p>
            @if (! empty($financial_year['closed_at']))
                <p>
                    {{ __('Closed at') }}: {{ $financial_year['closed_at'] }}
                    @if (! empty($financial_year['closed_by']))
                        {{ __('by') }} {{ $financial_year['closed_by'] }}
                    @endif
                </p>
            @endif
            <p>{{ __('Generated on') }}: {{ now()->format('d-m-Y H:i') }}</p>
        </div>

        @if ($net_profit_loss !== null)
            <div class="summary">
                <strong>{{ __('Net') }} {{ ($net_profit_loss ?? 0) >= 0 ? __('Profit') : __('Loss') }}:</strong>
                {{ number_format(abs((float) $net_profit_loss), 2) }}
                @if ($net_profit_loss != 0)
                    ({{ __('Retained Earnings') }}:
                    Dr {{ $retained_earnings_effect['debit'] ?? 0 }}
                    / Cr {{ $retained_earnings_effect['credit'] ?? 0 }})
                @endif
            </div>
        @endif

        <h3>
            {{ __('Closing Voucher') }}
            @if (! empty($closing_voucher['voucher_no']))
                - {{ $closing_voucher['voucher_no'] }}
                ({{ ! empty($closing_voucher['date']) ? \Carbon\Carbon::parse($closing_voucher['date'])->format('d/m/Y') : '' }})
            @endif
        </h3>
        @if (! empty($closing_voucher['description']))
            <p class="section-note">{{ $closing_voucher['description'] }}</p>
        @endif

        @if (! empty($closing_voucher))
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Account') }}</th>
                        <th>{{ __('Remarks') }}</th>
                        <th class="amount">{{ __('Debit') }}</th>
                        <th class="amount">{{ __('Credit') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($closing_voucher['lines'] ?? [] as $row)
                        <tr>
                            <td>{{ $row['account_name'] ?? '—' }}</td>
                            <td>{{ $row['remarks'] ?? '—' }}</td>
                            <td class="amount">{{ number_format((float) ($row['debit'] ?? 0), 2) }}</td>
                            <td class="amount">{{ number_format((float) ($row['credit'] ?? 0), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">{{ __('No closing voucher lines') }}</td>
                        </tr>
                    @endforelse
                    @if (! empty($closing_voucher['lines']))
                        <tr style="background: #F7F7F9; font-weight: bold;">
                            <td colspan="2">{{ __('Total') }}</td>
                            <td class="amount">{{ number_format((float) ($closing_voucher['total_debit'] ?? 0), 2) }}</td>
                            <td class="amount">{{ number_format((float) ($closing_voucher['total_credit'] ?? 0), 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @else
            <p class="section-note">{{ __('No closing voucher recorded for this year.') }}</p>
        @endif

        <h3>
            {{ __('Opening Balance Audit') }}
            @if (! empty($opening_balance['voucher_no']))
                - {{ $opening_balance['voucher_no'] }}
            @endif
        </h3>

        @if (! empty($opening_balance['accounts']))
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Account') }}</th>
                        <th class="amount">{{ __('Debit') }}</th>
                        <th class="amount">{{ __('Credit') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($opening_balance['accounts'] as $row)
                        <tr>
                            <td>{{ $row['name'] ?? '—' }}</td>
                            <td class="amount">{{ number_format((float) ($row['debit'] ?? 0), 2) }}</td>
                            <td class="amount">{{ number_format((float) ($row['credit'] ?? 0), 2) }}</td>
                        </tr>
                    @endforeach
                    <tr style="background: #F7F7F9; font-weight: bold;">
                        <td>{{ __('Total') }}</td>
                        <td class="amount">{{ number_format((float) ($opening_balance['total_debit'] ?? 0), 2) }}</td>
                        <td class="amount">{{ number_format((float) ($opening_balance['total_credit'] ?? 0), 2) }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <p class="section-note">{{ __('No opening balance audit for this year.') }}</p>
        @endif
    </div>
</body>

</html>
