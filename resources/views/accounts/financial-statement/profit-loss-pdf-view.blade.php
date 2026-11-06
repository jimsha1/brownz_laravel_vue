<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Profit & Loss') }}</title>
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

        .italic {
            font-style: italic;
        }

        .indent {
            padding-left: 24px;
        }

        .mid-total td {
            font-weight: 700;
            background: #F7F7F9;
        }

        .final-total td {
            font-weight: 700;
            background: #ececef;
        }
    </style>
</head>

<body>
    @php
        $expandSide = function (array $rows): array {
            $expanded = [];
            foreach ($rows as $row) {
                $expanded[] = $row;
                if (!empty($row['children']) && is_array($row['children'])) {
                    foreach ($row['children'] as $child) {
                        $expanded[] = $child;
                    }
                }
            }
            return $expanded;
        };

        $formatAmount = function (?array $row): string {
            if (!$row || !isset($row['amount'])) {
                return '';
            }
            $amount = (float) $row['amount'];
            if (abs($amount) < 0.01) {
                return '';
            }
            return number_format($amount, 2);
        };

        $rowClass = function (?array $row): string {
            if (!$row) {
                return '';
            }
            $classes = [];
            if (!empty($row['is_italic'])) {
                $classes[] = 'italic';
            }
            if (!empty($row['is_indent']) || !empty($row['is_child'])) {
                $classes[] = 'indent';
            }
            return implode(' ', $classes);
        };

        $tradingDebit = $expandSide($trading_debit_side ?? []);
        $plDebit = $expandSide($pl_debit_side ?? []);
        $tradingCredit = $trading_credit_side ?? [];
        $plCredit = $pl_credit_side ?? [];
    @endphp

    <div class="container">
        <h2><u>{{ __('Trading and Profit & Loss Statement') }}</u></h2>

        <div class="meta">
            <p>{{ __('Period') }}: {{ $start_date ? \Carbon\Carbon::parse($start_date)->format('d-m-Y') : '—' }}
                – {{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d-m-Y') : '—' }}</p>
            <p>{{ __('Generated on') }}: {{ now()->format('d-m-Y H:i') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>{{ __('Particulars') }}</th>
                    <th class="amount">{{ __('Amount') }}</th>
                    <th>{{ __('Particulars') }}</th>
                    <th class="amount">{{ __('Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < max(count($tradingDebit), count($tradingCredit)); $i++)
                    @php
                        $debit = $tradingDebit[$i] ?? null;
                        $credit = $tradingCredit[$i] ?? null;
                    @endphp
                    <tr>
                        <td class="{{ $rowClass($debit) }}">{{ $debit['account_name'] ?? '' }}</td>
                        <td class="amount {{ $rowClass($debit) }}">{{ $formatAmount($debit) }}</td>
                        <td class="{{ $rowClass($credit) }}">{{ $credit['account_name'] ?? '' }}</td>
                        <td class="amount {{ $rowClass($credit) }}">{{ $formatAmount($credit) }}</td>
                    </tr>
                @endfor

                <tr class="mid-total">
                    <td></td>
                    <td class="amount">{{ number_format($trading_total_debit ?? 0, 2) }}</td>
                    <td></td>
                    <td class="amount">{{ number_format($trading_total_credit ?? 0, 2) }}</td>
                </tr>

                @for ($i = 0; $i < max(count($plDebit), count($plCredit)); $i++)
                    @php
                        $debit = $plDebit[$i] ?? null;
                        $credit = $plCredit[$i] ?? null;
                    @endphp
                    <tr>
                        <td class="{{ $rowClass($debit) }}">{{ $debit['account_name'] ?? '' }}</td>
                        <td class="amount {{ $rowClass($debit) }}">{{ $formatAmount($debit) }}</td>
                        <td class="{{ $rowClass($credit) }}">{{ $credit['account_name'] ?? '' }}</td>
                        <td class="amount {{ $rowClass($credit) }}">{{ $formatAmount($credit) }}</td>
                    </tr>
                @endfor

                <tr class="final-total">
                    <td>{{ __('Total') }}</td>
                    <td class="amount">{{ number_format($pl_total_debit ?? 0, 2) }}</td>
                    <td></td>
                    <td class="amount">{{ number_format($pl_total_credit ?? 0, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
