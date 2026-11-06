<!DOCTYPE html>
<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    {{-- <title>Baheya</title> --}}
    <style>
        html {
            margin: 15px 15px 15px 15px
        }

        table {
            border-collapse: separate;
            text-indent: initial;
            border-spacing: 0 7px;
        }

        table tr {
            vertical-align: top !important;
        }

        td {
            vertical-align: top !important;
        }

        p {
            margin: 0;
            font-size: 11px;
            font-family: dejavu sans, Arial, Helvetica, sans-serif;
            color: #000000;
            /* direction: rtl; */
        }

        .border-table {
            border-top: 2px solid #e8e8e8;
            border-spacing: 0;
        }

        .border-table th {
            padding: 7.5px 0;
            text-align: center;
            border-bottom: 2px solid #e8e8e8;
        }

        .border-table td {}

        .center {
            text-align: center;
        }

        .pt10 {
            padding-top: 10px;
            font-size: 10px;
            color: #000000;
            font-family: Arial, Helvetica, sans-serif;
        }

        .pb10 {
            padding-bottom: 10px;
            font-size: 10px;
            color: #000000;
            font-family: dejavu sans, Arial, Helvetica, sans-serif;
        }

        .bb {
            border-bottom: 1px solid #e8e8e8
        }

        .pb11 {
            font-size: 11px;
            color: #000000;
            font-family: dejavu sans, Arial, Helvetica, sans-serif;
        }

    </style>
</head>

<body>
    <?php
    
    $heading_ar = 'شركة عالم بهية المحدودة';
    preg_match_all('/./us', $heading_ar, $ar);
    $heading_ar = join('', array_reverse($ar[0]));
    
    $customer_name_ar = $client_info->name;
    preg_match_all('/./us', $customer_name_ar, $ar);
    $customer_name_ar = join('', array_reverse($ar[0]));
    
    $item_ar = 'بند';
    preg_match_all('/./us', $item_ar, $ar);
    $item = join('', array_reverse($ar[0]));
    
    $quantity_ar = 'كمية';
    preg_match_all('/./us', $quantity_ar, $ar);
    $quantity_ar = join('', array_reverse($ar[0]));
    
    $price_ar = 'السعر';
    preg_match_all('/./us', $price_ar, $ar);
    $price_ar = join('', array_reverse($ar[0]));
    
    $tax_ar = 'ضريبة';
    preg_match_all('/./us', $tax_ar, $ar);
    $tax_ar = join('', array_reverse($ar[0]));
    
    $total_ar = 'مجموع';
    preg_match_all('/./us', $total_ar, $ar);
    $total_ar = join('', array_reverse($ar[0]));
    
    $date_ar = 'تاريخ';
    preg_match_all('/./us', $date_ar, $ar);
    $date_ar = join('', array_reverse($ar[0]));
    
    $mobile_ar = 'التليفون المحمول';
    preg_match_all('/./us', $mobile_ar, $ar);
    $mobile_ar = join('', array_reverse($ar[0]));
    
    $customer_ar = 'زبون';
    preg_match_all('/./us', $customer_ar, $ar);
    $customer_ar = join('', array_reverse($ar[0]));
    
    $sub_total_ar = 'المجموع الفرعي';
    preg_match_all('/./us', $sub_total_ar, $ar);
    $sub_total_ar = join('', array_reverse($ar[0]));
    
    $discount_ar = 'خصم';
    preg_match_all('/./us', $discount_ar, $ar);
    $discount_ar = join('', array_reverse($ar[0]));
    
    $net_total_ar = 'صافي الإجمالي';
    preg_match_all('/./us', $net_total_ar, $ar);
    $net_total_ar = join('', array_reverse($ar[0]));
    
    $receipt_total_ar = 'إجمالي الإيصالات';
    preg_match_all('/./us', $receipt_total_ar, $ar);
    $receipt_total_ar = join('', array_reverse($ar[0]));
    
    $cash_tendered_ar = 'النقد مناقصة';
    preg_match_all('/./us', $cash_tendered_ar, $ar);
    $cash_tendered_ar = join('', array_reverse($ar[0]));
    
    $beautician_ar = 'اخصائية';
    preg_match_all('/./us', $beautician_ar, $ar);
    $beautician_ar = join('', array_reverse($ar[0]));
    
    $customer_name = $client_info->name;
    $item = 'Item';
    $quantity = 'Quantity';
    $price = 'Price';
    $tax = 'Tax';
    $total = 'Total';
    $date = 'Date';
    $mobile = 'Mobile';
    $customer = 'Customer';
    $sub_total = 'Sub Total';
    $discount = 'Discount';
    $net_total = 'Net Total';
    $receipt_total = 'Receipt Total';
    $vat_amount = 'VAT Total';
    $amount_with_vat = 'Amount with VAT';
    $cash_tendered = 'Cash Tendered';
    $beautician = 'Beautician';
    
    // $totalAmount = $booking_info->payment_total;
    // $vatAmount = $booking_info->payment_total;
    // $amountToPay = $booking_info->payment_total;
    
    $amountToPay = $booking_info->payment_total;
    
    $vatPercentage = 15;
    $vatAmount = $amountToPay - ($amountToPay / (1 + ($vatPercentage / 100)));
    $totalAmount = $amountToPay - $vatAmount;
    
    ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td colspan="2" style="border-bottom: 2px solid #d8d8d8;padding-bottom: 20px; text-align: center;"><img
                    src="http://baheyaweb.cloudbery.com/web/images/logo.png" width="150px"><br>
                <p dir="rtl" lang="ar" class="mt-2"> شركة عالم بهية المحدودة
                </p>
                <p>Baheya world Ltd. Co.
                <p>
                <p>VAT # 310571900500003</p>
            </td>
        </tr>
        <tr>
            <td width="60%">
                {{-- <p>{{$customer_ar}} : {{$customer_name_ar}}</p> --}}
                <p>{{ $customer }} : {{ $customer_name }}</p>
                {{-- <p>{{$mobile_ar}} : {{ $client_info->mobile }}</p> --}}
                <p>{{ $mobile }} : {{ $client_info->mobile }}</p>
                {{-- <p>{{ $client_info->city }}, {{ $client_info->state }}</p> --}}
                {{-- <p>{{ $client_info->zip }}</p> --}}
            </td>


            <td width="50%" style="text-align: right;">
                <p>{{ __('Invoice') }}#{{ $booking_info->booking_code }}</p>
                <p>{{ $date }}: {{ \Carbon\Carbon::parse($booking_info->updated_at)->format('d M, Y') }}</p>
                <p>Cashier: {{ $cashier->name }}</p>
                @if (isset($serviceType))
                <p>Service at {{ $serviceType }}</p>
                @endif
            </td>
        </tr>
    </table>
    <br>
    <br>
    <?php
    $num_columns = 7;
    
    ?>
    <table class="border-table" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <th style="{{ $gift_flag ? 'width:70%' : 'width:30%' }}" class="pb11">{{ $item }}</th>
            <th class="pb11">{{ $quantity }}</th>
            @if (!$gift_flag)
                <th class="pb11">{{ $price }}</th>


                <th class="pb11">{{ __('Discount') }}</th>
                <th class="pb11">{{ __('Net') }}</th>


                <th class="pb11">{{ $tax }}</th>
                <th class="pb11">{{ $total }}</th>

            @endif
        </tr>

        @foreach ($booking_info->bookingOnlyServices as $service)
            <tr>

                <!-- <td>1</td> -->
                <td></td>
                @if ($booking_info->booking_type == 'Refunded')
                    <td class="center pt10">-1</td>
                @else
                    <td class="center pt10"> 1</td>
                @endif
                @if (!$gift_flag)
                    @if ($booking_info->booking_type == 'Refunded')
                        <td class="center pt10">{{ $service->service_booking_price }}</td>
                    @else
                        <td class="center pt10">{{ $service->service_booking_price }}</td>
                    @endif


                    @if ($service->individual_discount_amt > 0)
                        <td class="center pt10">{{ $service->individual_discount_amt }}</td>
                        @if ($booking_info->booking_type == 'Refunded')
                            <td class="center pt10">
                                {{ vc_money($service->service_booking_price - $service->individual_discount_amt) * -1 }}
                            </td>
                        @else
                            <td class="center pt10">
                                {{ vc_money($service->service_booking_price - $service->individual_discount_amt) }}
                            </td>
                        @endif
                    @else
                        <td class="center pt10">{{ $service->discount_amount }}</td>
                        @if ($booking_info->booking_type == 'Refunded')
                            <td class="center pt10">
                                {{ vc_money($service->service_booking_price - $service->discount_amount) * -1 }}</td>
                        @else
                            <td class="center pt10">
                                {{ vc_money($service->service_booking_price - $service->discount_amount) }}</td>
                        @endif

                    @endif


                    @if ($booking_info->booking_type == 'Refunded')
                        <td class="center pt10">{{ $service->tax_amount * -1 }}</td>
                    @else
                        <td class="center pt10">{{ $service->tax_amount }}</td>
                    @endif
                    @php
                        if ($service->discount_amount > 0) {
                            $net_total = vc_money($service->service_booking_price - $service->discount_amount);
                        } else {
                            $net_total = vc_money($service->service_booking_price - $service->individual_discount_amt);
                        }
                        
                    @endphp
                    @if ($booking_info->booking_type == 'Refunded')
                        <td class="center pt10">{{ abs($net_total + $service->tax_amount) * -1 }}</td>
                    @else
                        <td class="center pt10">{{ $net_total + $service->tax_amount }}</td>
                    @endif
                @endif
            </tr>
            <?php
            
            $service_name_ar = $service->service_name_locale;
            preg_match_all('/./us', $service_name_ar, $ar);
            $service_name_ar = join('', array_reverse($ar[0]));
            
            if ($service->beautician) {
                $beautician_name_ar = $service->beautician->name;
                preg_match_all('/./us', $beautician_name_ar, $ar);
                $beautician_name_ar = join('', array_reverse($ar[0]));
            }
            
            if ($service->beautician) {
                $beautician_name = $service->beautician->name;
            }
            $service_name = $service->service_name_locale;
            
            ?>
            <tr>
                <td colspan="{{ $num_columns }}"
                    style="font-size: 10px; color: #000000; font-family: dejavu sans, Arial, Helvetica, sans-serif;">
                    <p style="text-align: end;direction: rtl;"> {{ $service->service_name_ar }} </p>
                </td>
            </tr>
            <tr>
                <td colspan="{{ $num_columns }}"
                    style="font-size: 10px; color: #000000; font-family: dejavu sans, Arial, Helvetica, sans-serif;">
                    {{ $service->service_name }}<br></td>
            </tr>
            <tr>
                <td colspan="{{ $num_columns }}" style="padding-top:5px; " class="bb pb10">
                    {{ $date }}: {{ \Carbon\Carbon::parse($service->appointment_date)->format('d M, Y') }}
                    {{ 'Time' }}:
                    {{ $service->slot_start ? date('h:i A', strtotime($service->slot_start)) : '--' }}

                    {{ $beautician }}: {{ $service->beautician ? $beautician_name : '--' }}

                </td>
            </tr>
        @endforeach
        @foreach ($booking_info->bookingItems as $item)
            <tr>
                <td></td>

                <td class="center pt10">{{ $item->quantity }}</td>

                {{-- @if (!$gift_flag) --}}

                <td class="center pt10">{{ $item->unit_price }}</td>
               
                @if ($item->individual_discount_percent > 0)
              
                    <td class="center pt10">{{ $item->individual_discount_percent }}</td>
                    <td class="center pt10">{{ vc_money($item->unit_price - $item->individual_discount_amt) }}
                    </td>
                @else

                    <td class="center pt10">{{ $item->discount_percent }}</td>
                    <td class="center pt10">{{ vc_money(($item->unit_price *  $item->quantity) - $item->discount_amount) }}</td>
                @endif

                <td class="center pt10">{{ $item->tax_amount }}</td>

                <td class="center pt10">{{ $item->line_total }}</td>

                {{-- @endif --}}
            </tr>
            <tr>
                <td colspan="{{ $num_columns }}"
                    style="font-size: 10px; color: #000000; font-family: dejavu sans, Arial, Helvetica, sans-serif;">
                    <p style="text-align: end;direction: rtl;"> {{ $item->item_name_ar }} </p>
                </td>
            </tr>
            <tr>
                <td colspan="{{ $num_columns }}"
                    style="font-size: 10px; color: #000000; font-family: Arial, Helvetica, sans-serif;">
                    {{ $item->item_name }}</td>
            </tr>

        @endforeach

    </table>

    @if (!$gift_flag)


        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            {{-- <tr><td style="text-align: right;" class="pb11">{{$sub_total}}: {{ vc_money($calculations['plain_net_total']) }}</td></tr> --}}

            @if (count($booking_info->bookingOnlyServices) > 0)

                {{-- <tr><td style="text-align: right;" class="pb11">{{$discount}}: {{  $booking_info->bookingOnlyServices[0]->show_discount_persent }} %</td></tr> --}}
            @endif
           
            {{-- <tr><td style="text-align: right;" class="pb11">{{$discount}}: {{  $booking_info->bookingItems[0]->show_discount_persent }} %</td></tr>
       @endif 
        <tr><td style="text-align: right;" class="pb11"> {{$net_total}}: {{ vc_money($calculations['plain_net_total'] - $calculations['discount_total']) }}</td></tr>

        <tr><td style="text-align: right;" class="bb pb10 pb11">VAT 15%: {{ vc_money($calculations['tax_total']) }}</td></tr> --}}

            {{-- Reciept Total --}}
            @if ($booking_info->booking_type == 'Refunded')
                <tr>
                    <td class="pb11" style="text-align: right;"><b>{{ $receipt_total }}:
                            {{ vc_money($totalAmount) }}</b></td>
                </tr>

            @else
                <tr>
                    <td class="pb11" style="text-align: right;"><b>{{ $receipt_total }}:
                            {{ vc_money(abs($totalAmount)) }}</b></td>
                </tr>
            @endif

            {{-- VAT Total --}}
            @if ($booking_info->booking_type == 'Refunded')
                <tr>
                    <td class="pb11" style="text-align: right;"><b>{{ $vat_amount }}:
                            {{ vc_money($vatAmount) }}</b></td>
                           
                </tr>

            @else
                <tr>
                    <td class="pb11" style="text-align: right;"><b>{{ $vat_amount }}:
                            {{ vc_money(abs($vatAmount)) }}</b></td>
                </tr>
            @endif


            {{-- Amount with VAT --}}
            @if ($booking_info->booking_type == 'Refunded')
                <tr>
                    <td class="bb pb10 pb11" style="text-align: right;"><b>{{ $amount_with_vat }}:
                            {{ vc_money($amountToPay) }}</b></td>
                </tr>

            @else
                <tr>
                    <td class="bb pb10 pb11" style="text-align: right;"><b>{{ $amount_with_vat }}:
                            {{ vc_money(abs($amountToPay)) }}</b></td>
                </tr>
            @endif



            @if (isset($payments_display['cash']))
                @if ($booking_info->booking_type == 'Refunded')
                    <tr>
                        <td style="text-align: right;" class="pb11">{{ _('Cash Refund Tenderd') }}:
                            {{ vc_money($payments_display['cash']) }}</td>
                    </tr>
                @else
                    <tr>
                        <td style="text-align: right;" class="pb11">{{ $cash_tendered }}:
                            {{ vc_money($payments_display['cash']) }}</td>
                    </tr>
                @endif

            @endif

            @if (isset($payments_display['card']))
                @foreach ($payments_display['card'] as $card_payment)
                    @if ($booking_info->booking_type == 'Refunded')
                        <tr>
                            <td style="text-align: right;" class="pb11">
                                {{ ucwords($card_payment['card_type']) }} {{ __('Card Refund Tendered') }}:
                                {{ vc_money($card_payment['amount']) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td style="text-align: right;" class="pb11">
                                {{ ucwords($card_payment['card_type']) }} {{ __('Card Tendered') }}:
                                {{ vc_money($card_payment['amount']) }}</td>
                        </tr>

                    @endif

                @endforeach
            @endif

        </table>
    @endif
    <br>
    <p style="width: 100%;border-top: 1px solid #e8e8e8;text-align: center;padding: 4px 0;">
        <img src="data:image/png;base64, {{ \DNS1D::getBarcodePNG($booking_info->booking_barcode, 'C128') }}"
            width="150px" alt="barcode">
    </p>
    <p style="text-align: center;font-size: 11px;
    text-align: center;
    margin-bottom: 8px;">www.baheyabeauty.com</p>
    <p style="font-size: 7px;font-size: 11px;
    text-align: center;
    margin-bottom: 8px;">مواد التجميل، العطور، الإكسسوارات والملابس الداخلية لا تشملها سياسة الاستبدال والاسترجاع
        حفاظاً على الصحة العامة للجميع.</p>
    <p style="font-size: 7px;text-align: center;font-size: 11px;">Cosmetics, perfumes, accessories and underwear are not
        covered by the exchange and return policy, in order to preserve the general health of all</p>
       
        @php
      
        $date = \Carbon\Carbon::parse($booking_info->updated_at)->format('d M, Y');
            $data = "Seller Name: $seller_name Seller VAT RN:$seller_vat Date: $date Invoice No: $booking_info->booking_code Total VAT: $vatAmount Total With VAT: $amountToPay ";
        @endphp
        <div style="text-align: center;">
        <img style="
        width: 161px;
    " src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl={{$data}}&choe=UTF-8" title="Link to Google.com" />
        </div>
</body>
<script>
    $(document).ready(function() {
        window.print();
    });
</script>

</html>
<?php

function vc_money($value)
{
    return number_format((float) $value, 2, '.', '');
}
?>
