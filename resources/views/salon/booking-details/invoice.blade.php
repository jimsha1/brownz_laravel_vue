<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice - {{ $booking_info->booking_code ?? '' }}</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #fff;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.35;
        }

        .invoice-wrap {
            width: 80mm;
            max-width: 100%;
            margin: 0 auto;
            padding: 8px 10px 16px;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
        }

        td,
        th {
            vertical-align: top;
        }

        p {
            margin: 0;
            font-size: 12px;
            color: #000;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
            margin-bottom: 8px;
        }

        .header img {
            max-width: 140px;
            height: auto;
            max-height: 70px;
            display: block;
            margin: 0 auto 6px;
        }

        .header .gst {
            font-size: 12px;
            margin: 2px 0;
        }

        .header .title {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 4px 0 0;
        }

        .meta-table td {
            border-bottom: 1px solid #000;
            padding: 5px 0;
            font-size: 12px;
        }

        .meta-table td:first-child {
            width: 42%;
            font-weight: 600;
        }

        .meta-table tr:last-child td {
            border-bottom: none;
        }

        .items-table {
            margin-top: 10px;
            border-top: 2px solid #000;
        }

        .items-table th {
            padding: 6px 2px;
            text-align: center;
            border-bottom: 1px solid #000;
            font-size: 12px;
            font-weight: bold;
        }

        .items-table th.col-item {
            text-align: left;
            width: 48%;
        }

        .items-table th.col-qty {
            width: 20%;
        }

        .items-table th.col-price {
            width: 32%;
            text-align: right;
        }

        .items-table td {
            padding: 5px 2px;
            font-size: 12px;
        }

        .items-table td.col-item {
            text-align: left;
            word-break: break-word;
        }

        .items-table td.col-qty {
            text-align: center;
        }

        .items-table td.col-price {
            text-align: right;
            white-space: nowrap;
        }

        .item-meta {
            border-bottom: 1px solid #e0e0e0;
            padding: 2px 2px 6px !important;
            font-size: 10px !important;
            color: #333 !important;
        }

        .totals-table {
            margin-top: 8px;
        }

        .totals-table td {
            padding: 5px 0;
            font-size: 12px;
            border-bottom: 1px solid #000;
        }

        .totals-table td.label {
            text-align: left;
            font-weight: bold;
            width: 55%;
        }

        .totals-table td.value {
            text-align: right;
            font-weight: bold;
            width: 45%;
            white-space: nowrap;
        }

        .totals-table tr.grand td {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            font-size: 13px;
        }

        .brand-line {
            width: 100%;
            margin: 12px 0 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid #000;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
        }

        .policy {
            font-size: 10px;
            line-height: 1.4;
            text-align: left;
        }

        .policy b {
            display: block;
            margin-bottom: 4px;
            font-size: 11px;
        }

        .qr-wrap {
            text-align: center;
            margin-top: 10px;
        }

        .qr-wrap img,
        .qr-wrap svg {
            max-width: 120px;
            height: auto;
        }

        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }

            html,
            body {
                width: 80mm;
                margin: 0;
                padding: 0;
                background: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .invoice-wrap {
                width: 80mm;
                max-width: 80mm;
                margin: 0;
                padding: 4px 6px 10px;
            }

            .header img {
                max-height: 60px;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <?php
    $customer_name = $client_info->name ?? '';
    $item_label = 'Item';
    $quantity_label = 'Qty';
    $price_label = 'Price';
    $date_label = 'Date';
    $mobile_label = 'Mobile';
    $customer_label = 'Customer';
    $beautician_label = 'Beautician';
    $noPrefernceBt = 'Beautician No Preference';

    $amountToPay = $booking_info->payment_total;
    $vatPercentage = 18;
    $vatAmount = $amountToPay - $amountToPay / (1 + $vatPercentage / 100);
    $discountAmount = 0;
    $colCount = $gift_flag ? 2 : 3;
    ?>

    <div class="invoice-wrap">
        <div class="header">
            <img src="{{ asset('web/images/invoice_logo_manjeri.png') }}" alt="Logo">
            @if (!empty($gst_number))
                <p class="gst">GST No : {{ $gst_number }}</p>
            @else
                <p class="gst">GST No :</p>
            @endif
            <p class="title">INVOICE</p>
        </div>

        <table class="meta-table">
            <tr>
                <td>{{ $customer_label }}</td>
                <td>{{ $customer_name }}</td>
            </tr>
            <tr>
                <td>{{ $mobile_label }}</td>
                <td>{{ $client_info->mobile ?? '' }}</td>
            </tr>
            <tr>
                <td>Invoice#</td>
                <td>{{ $booking_info->booking_code }}</td>
            </tr>
            @if (in_array($booking_info->booking_type, ['Refund', 'Refunded'], true) && ! empty($booking_info->refund_invoice_no))
                <tr>
                    <td>Original Invoice#</td>
                    <td>{{ $booking_info->refund_invoice_no }}</td>
                </tr>
            @endif
            <tr>
                <td>{{ $date_label }}</td>
                <td>{{ \Carbon\Carbon::parse($booking_info->created_at)->format('d M, Y') }}</td>
            </tr>
            <tr>
                <td>Cashier</td>
                <td>
                    @if ($booking_info->booking_source == 'Website')
                        OnWeb
                    @else
                        {{ $cashier->username ?? '' }}
                    @endif
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-item">{{ $item_label }}</th>
                    <th class="col-qty">{{ $quantity_label }}</th>
                    @if (!$gift_flag)
                        <th class="col-price">{{ $price_label }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($booking_info->bookingOnlyServices as $service)
                    @php
                        $lineQty =
                            $booking_info->booking_type == 'Refunded' || $booking_info->booking_type == 'Refund'
                                ? -1 * (float) ($service->quantity ?: 1)
                                : (float) ($service->quantity ?: 1);

                        if ($service->individual_discount_amt != 0) {
                            $discountAmount += $service->individual_discount_amt;
                        } else {
                            $discountAmount += $service->discount_amount;
                        }

                        $beautician_name = $service->beautician->name ?? null;
                    @endphp
                    <tr>
                        <td class="col-item">{{ $service->service_name }}</td>
                        <td class="col-qty">{{ $lineQty }}</td>
                        @if (!$gift_flag)
                            <td class="col-price">{{ number_format($service->service_booking_price) }}</td>
                        @endif
                    </tr>
                    <tr>
                        <td colspan="{{ $colCount }}" class="item-meta">
                            {{ $date_label }}:
                            {{ \Carbon\Carbon::parse($service->appointment_date)->format('d M, Y') }}
                            &nbsp;|&nbsp;
                            Time:
                            {{ $service->slot_start ? date('h:i A', strtotime($service->slot_start)) : '--' }}
                            &nbsp;|&nbsp;
                            {{ $beautician_label }}:
                            {{ $beautician_name ?: __($noPrefernceBt) }}
                        </td>
                    </tr>
                @endforeach

                @foreach ($booking_info->bookingItems as $item)
                    @php
                        $lineQty =
                            $booking_info->booking_type == 'Refunded' || $booking_info->booking_type == 'Refund'
                                ? (float) $item->quantity * -1
                                : (float) $item->quantity;

                        if ($item->individual_discount_amt != 0) {
                            $discountAmount += $item->individual_discount_amt;
                        } else {
                            $discountAmount += $item->discount_amount;
                        }
                    @endphp
                    <tr>
                        <td class="col-item">
                            {{ $item->item_name }}
                            @if (!empty($item->item_name_ar))
                                <br><span style="font-size:10px;direction:rtl;display:inline-block;">{{ $item->item_name_ar }}</span>
                            @endif
                        </td>
                        <td class="col-qty">{{ $lineQty }}</td>
                        @if (!$gift_flag)
                            <td class="col-price">{{ number_format((float) ($item->selling_price ?? 0) * (float) ($item->quantity ?: 1)) }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if (!$gift_flag)
            <table class="totals-table">
                <tr>
                    <td class="label">Discount</td>
                    <td class="value">
                        {{ number_format($calculations['discount_total'] ?? ($discountAmount + ($booking_info->applied_discount_price ?? 0))) }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Net</td>
                    <td class="value">{{ number_format($net_amount) }}</td>
                </tr>
                <tr>
                    <td class="label">GST</td>
                    <td class="value">{{ number_format($vat_amt) }}</td>
                </tr>
                <tr class="grand">
                    <td class="label">Total</td>
                    <td class="value">{{ number_format(abs($amountToPay)) }}</td>
                </tr>
            </table>
        @endif

        <p class="brand-line">Brownz Hair And Beauty Salon</p>

        <div class="policy">
            <b>Refund Policy:</b>
            1. We do not offer refund on completed services.<br>
            2. If you are dissatisfied with the services, please let us know within 24 hrs, and we will make reasonable effort to resolve the issue.<br>
            3. Unused product in original condition can be exchanged or returned within 24 hrs.<br>
            4. Advance payments once made are non-refundable and non-adjustable toward future services under any circumstances.
        </div>

        <div class="qr-wrap">
            {!! $qr_code ?? '' !!}
        </div>
    </div>
</body>


<script>
    $(document).ready(function () {

        // Do not auto-print the public WhatsApp invoice page
        @if (!request()->routeIs('salon.invoice'))
            window.print();
        @endif

    });
</script>


</html>
<?php
if (!function_exists('vc_money')) {
    function vc_money($value)
    {
        return number_format((float) $value, 2, '.', '');
    }
}
?>
