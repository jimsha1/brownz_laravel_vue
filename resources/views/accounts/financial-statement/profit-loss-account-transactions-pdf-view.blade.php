<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? __('Account Transactions') }}</title>
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
            gap: 12px;
            margin-bottom: 14px;
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

        .note {
            margin-bottom: 12px;
            padding: 10px 12px;
            background: #fff8e8;
            border: 1px solid #f0e2c0;
            font-size: 12px;
            color: #5c4a1f;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2><u>{{ $title ?? __('Account Transactions') }}</u></h2>

        <div class="meta">
            @if (!empty($subtitle))
                <p>{{ $subtitle }}</p>
            @endif
            <p>{{ __('Period') }}: {{ $start_date ? \Carbon\Carbon::parse($start_date)->format('d-m-Y') : '—' }}
                – {{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d-m-Y') : '—' }}</p>
            <p>{{ __('Generated on') }}: {{ now()->format('d-m-Y H:i') }}</p>
        </div>

        @if (!empty($note))
            <div class="note">{{ $note }}</div>
        @endif

        <div class="summary">
            <span>{{ __('Total Debit') }}: {{ number_format($total_debit ?? 0, 2) }}</span>
            <span>{{ __('Total Credit') }}: {{ number_format($total_credit ?? 0, 2) }}</span>
            <span>{{ __('Net (Cr − Dr)') }}: {{ number_format($net_amount ?? 0, 2) }}</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 48px;">#</th>
                    <th>{{ __('Voucher No.') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th class="amount">{{ __('Debit') }}</th>
                    <th class="amount">{{ __('Credit') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['voucher_no'] ?? '—' }}</td>
                        <td>{{ !empty($item['date']) ? \Carbon\Carbon::parse($item['date'])->format('d-m-Y') : '—' }}</td>
                        <td>{{ $item['description'] ?? '—' }}</td>
                        <td class="amount">{{ number_format((float) ($item['debit'] ?? 0), 2) }}</td>
                        <td class="amount">{{ number_format((float) ($item['credit'] ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">{{ __('No transactions found for the selected period.') }}</td>
                    </tr>
                @endforelse
                @if (count($transactions))
                    <tr style="background: #F7F7F9; font-weight: bold;">
                        <td colspan="4" class="amount">{{ __('Total') }}</td>
                        <td class="amount">{{ number_format($total_debit ?? 0, 2) }}</td>
                        <td class="amount">{{ number_format($total_credit ?? 0, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>

</html>
