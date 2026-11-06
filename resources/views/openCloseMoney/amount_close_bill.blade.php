<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ __('Close Cash Report') }} #{{ $foatAmtDetails->float_amt_id }}</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            background: #ececef;
            color: #111;
            font-family: "Courier New", Courier, monospace;
        }
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            padding: 8px 12px;
            background: #1D1D1D;
        }
        .toolbar button {
            border: 0;
            border-radius: 999px;
            padding: 7px 12px;
            cursor: pointer;
            font-weight: 600;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }
        .toolbar .btn-print { background: #E6CD7F; color: #1D1D1D; }
        .toolbar .btn-close { background: #F0F0F3; color: #1D1D1D; }

        .sheet {
            width: 320px;
            max-width: 100%;
            margin: 12px auto 20px;
            background: #fff;
            padding: 14px 12px 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,.1);
        }
        .logo {
            text-align: center;
            padding-bottom: 8px;
            border-bottom: 1px dashed #999;
            margin-bottom: 8px;
        }
        .logo img { max-width: 110px; height: auto; }
        .logo h1 {
            margin: 6px 0 0;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0;
        }
        .logo p {
            margin: 2px 0 0;
            font-size: 11px;
        }

        .section {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px dashed #999;
        }
        .section-title {
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            margin: 0 0 4px;
            text-transform: uppercase;
        }

        table.meta,
        table.lines {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            line-height: 1.35;
        }
        table.meta td {
            padding: 1px 0;
            vertical-align: top;
        }
        table.meta td.label { text-align: left; }
        table.meta td.value { text-align: right; white-space: nowrap; }
        table.lines th,
        table.lines td {
            padding: 2px 0;
            border: 0;
        }
        table.lines th {
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #ccc;
        }
        table.lines .num { text-align: right; white-space: nowrap; }
        .strong { font-weight: 700; }
        .muted { color: #555; }
        .variance-pos { color: #199900; font-weight: 700; }
        .variance-neg { color: #D0342C; font-weight: 700; }
        .sign-row {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px dashed #999;
            font-size: 11px;
        }

        @media print {
            @page { margin: 6mm; size: auto; }
            body { background: #fff; }
            .toolbar { display: none !important; }
            .sheet {
                width: 100%;
                max-width: 80mm;
                margin: 0;
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>

<body>
@php
    $card = 0;
    $credit = 0;
    $cash = 0;
    $online = 0;
    $other = 0;
    $count_refund_records = count($refund_records);
    $regular_sale = max(0, (int) $invoice_count);

    foreach ($booking_records as $booking) {
        $mode = strtolower(trim((string) ($booking->payment_mode ?? '')));
        $amountTaken = (float) ($booking->amount_taken ?? 0);
        $amountGiven = (float) ($booking->amount_given ?? 0);

        if ($mode === 'cash') {
            $cash += ($amountTaken - $amountGiven);
        } elseif ($mode === 'card') {
            $card += $amountTaken;
        } elseif ($mode === 'online') {
            $online += $amountTaken;
        } elseif ($mode === 'credit') {
            $credit += $amountTaken;
        } else {
            $other += $amountTaken;
        }
    }
    $grand_total = $card + $credit + $online + $cash + $other;

    $refund_amount = 0;
    foreach ($refund_records as $refund) {
        $refund_amount += (float) ($refund->amount_given ?? 0);
    }

    $close_count_total = 0;
    $hasQtyLines = false;
    $denomLines = [];
    foreach ($denomination_records as $money) {
        $qty = $money->qty;
        $amount = (float) ($money->amount ?? 0);
        if ($qty !== null && $qty !== '' && (float) $qty > 0) {
            $hasQtyLines = true;
            $lineTotal = (float) $qty * $amount;
            $close_count_total += $lineTotal;
            $denomLines[] = [
                'title' => $money->title,
                'amount' => $amount,
                'qty' => $qty,
                'line_total' => $lineTotal,
            ];
        }
    }
    if (!$hasQtyLines && $denomination_records->count() > 0) {
        $close_count_total = (float) ($denomination_records->first()->total ?? 0);
    }

    $expected_cash = $cash + (float) $foatAmtDetails->amonut;
    $cash_variance = $close_count_total - $expected_cash;
    $tax_amount = 0;
    foreach ($taxamount_records as $taxamount) {
        $tax_amount += (float) ($taxamount['tax_amount'] ?? 0);
    }

    $logoPath = public_path('web/images/invoice_logo.png');
    $logoUrl = file_exists($logoPath)
        ? asset('web/images/invoice_logo.png')
        : asset('web/images/invoice_logo_manjeri.png');

    $fmt = function ($n) {
        return number_format((float) $n, 2, '.', '');
    };
@endphp

    <div class="toolbar no-print">
        <button type="button" class="btn-print" onclick="window.print()">{{ __('Print') }}</button>
        <button type="button" class="btn-close" onclick="window.close()">{{ __('Close') }}</button>
    </div>

    <div class="sheet">
        <div class="logo">
            <img src="{{ $logoUrl }}" alt="Logo">
            <h1>{{ __('CLOSE CASH REPORT') }}</h1>
            <p>{{ __('Float') }} #{{ $foatAmtDetails->float_amt_id }}</p>
        </div>

        <table class="meta">
            <tr>
                <td class="label">{{ __('Cashier') }}</td>
                <td class="value">{{ $foatAmtDetails->name }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('Open Amt') }}</td>
                <td class="value">{{ $fmt($foatAmtDetails->amonut) }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('Opened') }}</td>
                <td class="value">{{ date('d/m/y H:i', strtotime($foatAmtDetails->added_date)) }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('Closed') }}</td>
                <td class="value">{{ date('d/m/y H:i', strtotime($foatAmtDetails->updated_date)) }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('Closed By') }}</td>
                <td class="value">{{ $foatAmtDetails->closed_by_name ?: $foatAmtDetails->name }}</td>
            </tr>
            @if (!empty($foatAmtDetails->edited_by_name))
            <tr>
                <td class="label">{{ __('Count Edited') }}</td>
                <td class="value">{{ $foatAmtDetails->edited_by_name }}{{ !empty($foatAmtDetails->edited_at) ? ' ' . date('d/m/y H:i', strtotime($foatAmtDetails->edited_at)) : '' }}</td>
            </tr>
            @endif
            @if (!empty($foatAmtDetails->edit_reason))
            <tr>
                <td class="label">{{ __('Edit Reason') }}</td>
                <td class="value" style="white-space: normal;">{{ $foatAmtDetails->edit_reason }}</td>
            </tr>
            @endif
        </table>

        <div class="section">
            <div class="section-title">{{ __('Tendered') }}</div>
            <table class="meta">
                <tr>
                    <td class="label">{{ __('Cash') }}</td>
                    <td class="value">{{ $fmt($cash) }}</td>
                </tr>
                @if ($card != 0)
                <tr>
                    <td class="label">{{ __('Card') }}</td>
                    <td class="value">{{ $fmt($card) }}</td>
                </tr>
                @endif
                @if ($online != 0)
                <tr>
                    <td class="label">{{ __('Online') }}</td>
                    <td class="value">{{ $fmt($online) }}</td>
                </tr>
                @endif
                @if ($credit != 0)
                <tr>
                    <td class="label">{{ __('Credit') }}</td>
                    <td class="value">{{ $fmt($credit) }}</td>
                </tr>
                @endif
                @if ($other != 0)
                <tr>
                    <td class="label">{{ __('Other') }}</td>
                    <td class="value">{{ $fmt($other) }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label strong">{{ __('Total') }}</td>
                    <td class="value strong">{{ $fmt($grand_total) }}</td>
                </tr>
                @if ($refund_amount != 0)
                <tr>
                    <td class="label">{{ __('Refunds') }}</td>
                    <td class="value">{{ $fmt($refund_amount) }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label strong">{{ __('Net') }}</td>
                    <td class="value strong">{{ $fmt($grand_total - $refund_amount) }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('Tax') }}</td>
                    <td class="value">{{ $fmt($tax_amount) }}</td>
                </tr>
            </table>
        </div>

        @if ($denomination_records->count() > 0)
            <div class="section">
                <div class="section-title">{{ __('Closing Count') }}</div>
                @if (count($denomLines) > 0)
                    <table class="lines">
                        <thead>
                            <tr>
                                <th>{{ __('Item') }}</th>
                                <th class="num">{{ __('Qty') }}</th>
                                <th class="num">{{ __('Amt') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($denomLines as $money)
                                <tr>
                                    <td>{{ $money['title'] }}</td>
                                    <td class="num">{{ $money['qty'] }}x{{ $fmt($money['amount']) }}</td>
                                    <td class="num">{{ $fmt($money['line_total']) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="strong" colspan="2">{{ __('Count Total') }}</td>
                                <td class="num strong">{{ $fmt($close_count_total) }}</td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <table class="meta">
                        <tr>
                            <td class="label strong">{{ __('Count Total') }}</td>
                            <td class="value strong">{{ $fmt($close_count_total) }}</td>
                        </tr>
                    </table>
                @endif
            </div>
        @endif

        <div class="section">
            <div class="section-title">{{ __('Variance') }}</div>
            <table class="meta">
                <tr>
                    <td class="label">{{ __('Expected') }}</td>
                    <td class="value">{{ $fmt($expected_cash) }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('Counted') }}</td>
                    <td class="value">{{ $fmt($close_count_total) }}</td>
                </tr>
                <tr>
                    <td class="label strong">{{ __('Variance') }}</td>
                    <td class="value {{ $cash_variance < 0 ? 'variance-neg' : ($cash_variance > 0 ? 'variance-pos' : 'strong') }}">
                        {{ $fmt($cash_variance) }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">{{ __('Counts') }}</div>
            <table class="meta">
                <tr>
                    <td class="label">{{ __('Sales') }}</td>
                    <td class="value">{{ $regular_sale }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('Returns') }}</td>
                    <td class="value">{{ $count_refund_records }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('Bookings') }}</td>
                    <td class="value">{{ $status_total_count ?? 0 }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('Txns') }}</td>
                    <td class="value">{{ $transaction_count ?? 0 }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('Paid') }}</td>
                    <td class="value">{{ $invoice_count ?? 0 }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('Partial') }}</td>
                    <td class="value">{{ $partial_invoice_count ?? 0 }}</td>
                </tr>
            </table>
        </div>

        <div class="sign-row">{{ __('Signed') }} ______________</div>
    </div>

<script>
    jQuery(function () {
        setTimeout(function () {
            window.print();
        }, 250);
    });
</script>
</body>
</html>
