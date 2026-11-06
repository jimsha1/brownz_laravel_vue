<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Opening Balance Audit') }} - {{ $financial_year['label'] ?? '' }}</title>
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
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 16px;
            font-size: 12px;
            font-weight: 700;
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
        <h2><u>{{ __('Opening Balance Audit') }} - {{ $financial_year['label'] ?? '' }}</u></h2>

        <div class="meta">
            <p>{{ __('Voucher') }}: {{ $financial_year['opening_voucher_no'] ?? '—' }}</p>
            <p>
                {{ __('Period') }}:
                {{ ! empty($financial_year['start_date']) ? \Carbon\Carbon::parse($financial_year['start_date'])->format('d/m/Y') : '—' }}
                –
                {{ ! empty($financial_year['end_date']) ? \Carbon\Carbon::parse($financial_year['end_date'])->format('d/m/Y') : '—' }}
                | {{ __('Status') }}: {{ ($financial_year['status'] ?? '') === 'open' ? __('Open') : __('Closed') }}
            </p>
            <p>{{ __('Generated on') }}: {{ now()->format('d-m-Y H:i') }}</p>
        </div>

        <div class="summary">
            <span>{{ __('Total Debit') }}: {{ number_format((float) ($total_debit ?? 0), 2) }}</span>
            <span>{{ __('Total Credit') }}: {{ number_format((float) ($total_credit ?? 0), 2) }}</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>{{ __('Account') }}</th>
                    <th class="amount">{{ __('Debit') }}</th>
                    <th class="amount">{{ __('Credit') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($accounts ?? [] as $row)
                    <tr>
                        <td>{{ $row['name'] ?? '—' }}</td>
                        <td class="amount">{{ number_format((float) ($row['debit'] ?? 0), 2) }}</td>
                        <td class="amount">{{ number_format((float) ($row['credit'] ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">{{ __('No opening balance accounts recorded.') }}</td>
                    </tr>
                @endforelse
                @if (! empty($accounts))
                    <tr style="background: #F7F7F9; font-weight: bold;">
                        <td>{{ __('Total') }}</td>
                        <td class="amount">{{ number_format((float) ($total_debit ?? 0), 2) }}</td>
                        <td class="amount">{{ number_format((float) ($total_credit ?? 0), 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>

</html>
