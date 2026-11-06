<!DOCTYPE html>
<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

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
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td colspan="2" style="border-bottom: 22px solid #d8d8d8;padding-bottom: 20px;text-align: center;"><img
                    src="{{ asset('web/images/logo.png') }}" width="150px"><br>

                <p style="margin-top:10px ">Bill Close Report</p>
                <p>

                </p>
            </td>
        </tr>
        
        <tr style="text-align: center">
            <td style="border-bottom: 5px solid #d8d8d8;padding-bottom: 20px;text-align: center;" colspan="2">Close Cash
                Report</td>
        </tr>

    </table>
        <table style="border-bottom: 5px solid #d8d8d8;padding-bottom: 20px;width:100%">
            <tr>
                <td style="width: 50%;text-align: left;">User</td>
                <td style="width: 50%;text-align: right;">{{ $foatAmtDetails->name }}</td>
            </tr>

            <tr>
                <td style="width: 50%;text-align: left;">Report Date</td>
                <td style="width: 50%;text-align: right;">
                    {{ date('m/d/y g:i A', strtotime($foatAmtDetails->added_date)) }}</td>
            </tr>
            <tr>
                <td style="width: 50%;text-align: left;">Opening Balance</td>
                <td style="width: 50%;text-align: right;">{{ $foatAmtDetails->amonut }}</td>
            </tr>
            <tr>
                <td style="width: 50%;text-align: left;">Open At</td>
                <td style="width: 50%;text-align: right;">
                    {{ date('m/d/y g:i A', strtotime($foatAmtDetails->added_date)) }}</td>
            </tr>

            <tr>
                <td style="width: 50%;text-align: left;">Open By</td>
                <td style="width: 50%;text-align: right;">{{ $foatAmtDetails->name }}</td>
            </tr>
            <tr>
                <td style="width: 50%;text-align: left;">Closed At</td>
                <td style="width: 50%;text-align: right;">
                    {{ date('m/d/y g:i A', strtotime($foatAmtDetails->updated_date)) }}</td>
            </tr>
            <tr>
                <td style="width: 50%;text-align: left;">Closed By</td>
                <td style="width: 50%;text-align: right;">{{ $foatAmtDetails->name }}</td>
            </tr>
        </table>

        <table style="border-bottom: 5px solid #d8d8d8; width:100%">
            <tr style="text-align: center">
                <td style="text-align: center;" colspan="2">Tenderd Payment</td>
            </tr>
        </table>
        <table style="border-bottom: 5px solid #d8d8d8; width:100%">
            <tr>
                <td style="width: 50%;text-align: left;">Cash</td>
                <td style="width: 50%;text-align: right;">{{ $denomination_records[0]->total }}</td>
            </tr>
            @php
                $mada = 0;
                $credit = 0;
                $cash = 0;
                $total_db_cash = 0;
                $total_invoice = count($booking_records);
            @endphp

            @php
                foreach ($booking_records as $booking) {
                    if ($booking->card_type == 'credit') {
                        $credit = $credit + $booking->amount_taken;
                    }
                    if ($booking->card_type == 'mada') {
                        $mada = $mada + $booking->amount_taken;
                    }
                    if ($booking->card_type == 'mada') {
                        $mada = $mada + $booking->amount_taken;
                    }
                    if($booking->payment_mode == 'cash'){
                        $total_db_cash = $cash + $booking->amount_taken;
                    }
                }
                $grand_total = $mada + $credit + $denomination_records[0]->total;
                $card_total = $mada + $credit;
            @endphp
            @if ($mada != 0)
                <tr>
                    <td style="width: 50%;text-align: left;">Mada</td>
                    <td style="width: 50%;text-align: right;">{{ $mada }}</td>
                </tr>
            @endif
            @if ($credit != 0)
                <tr>
                    <td style="width: 50%;text-align: left;">credit</td>
                    <td style="width: 50%;text-align: right;">{{ $credit }}</td>
                </tr>
            @endif
            <tr>
                <td style="width: 50%;text-align: left;">  Total</td>
                <td style="width: 50%;text-align: right;">{{ $grand_total }}</td>
            </tr>


        </table>
        @php
            $refund_amount = 0;
            foreach ($refund_records as $refund) {
                $refund_amount = $refund_amount + $refund->amount_given;
            }
        @endphp
        @if ($refund_amount != 0)
            <table style="border-bottom: 5px solid #d8d8d8; width:100%">
                <tr style="text-align: center">
                    <td style="text-align: center;" colspan="2">Refund Payment</td>
                </tr>
            </table>
            <table style="border-bottom: 5px solid #d8d8d8; width:100%">
                <tr>
                    <td style="width: 50%;text-align: left;">Total</td>
                    <td style="width: 50%;text-align: right;">{{ $refund_amount }}}</td>
                </tr>

            </table>
        @endif

        <table style="border-bottom: 5px solid #d8d8d8; width:100%">
            <tr style="text-align: center">
                <td style="text-align: center;" colspan="2">Net Payment</td>
            </tr>
        </table>
        <table style="border-bottom: 5px solid #d8d8d8; width:100%">
            
            
            <tr>
                <td style="width: 50%;text-align: left;">Total</td>
                <td style="width: 50%;text-align: right;">{{ $grand_total - $refund_amount }}</td>
            </tr>

        </table>
        <table style="border-bottom: 5px solid #d8d8d8; width:100%">
            <tr style="text-align: center">
                <td style="text-align: center;" colspan="2"> Taxes Report</td>
            </tr>
        </table>
        @php
        $tax_amount = 0;
           foreach ($taxamount_records as $taxamount) {
            $tax_amount =  $tax_amount +  $taxamount->tax_amount;
           } 
        @endphp
        <table style="border-bottom: 5px solid #d8d8d8; width:100%">
            <tr>
                <td style="width: 50%;text-align: left;">Tax</td>
                <td style="width: 50%;text-align: right;">{{$tax_amount}}</td>
            </tr>
        </table>

        @if ($foatAmtDetails->is_denomination_table == 0)
        <table style="border-bottom: 5px solid #d8d8d8; width:100%">
            <tr style="text-align: center">
                <td style="text-align: center;" colspan="2"> Closing Count</td>
            </tr>
        </table>
            <table style="border-bottom: 5px solid #d8d8d8;width: 100%" class="table">
                <thead>
                    <tr>
                        <th style="text-align: left" scope="col">#</th>
                        <th style="text-align: left" scope="col">Title</th>
                        <th style="text-align: left" scope="col">Denomination</th>
                        <th style="text-align: left" scop="col">Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($denomination_records as $money)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $money->title }}</td>
                            <td>{{ $money->amount }}</td>
                            <td>{{ $money->qty }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <table style="border-bottom: 5px solid #d8d8d8; width:100%">
            <tr style="text-align: center">
                <td style="text-align: center;" colspan="2"> Variance</td>
            </tr>
        </table>
        <table style="border-bottom: 5px solid #d8d8d8; width:100%">
            <tr>
                <td style="width: 50%;text-align: left;">Cash</td>
                <td style="width: 50%;text-align: right;">{{ $denomination_records[0]->total - $total_db_cash }}</td>
            </tr>
            <tr>
                <td style="width: 50%;text-align: left;">Total</td>
                <td style="width: 50%;text-align: right;">{{$total_invoice}}</td>
            </tr>
        </table>
        <table style="border-bottom: 5px solid #d8d8d8; width:100%">
            <tr style="text-align: center">
                <td style="text-align: center;" colspan="2"> Stats</td>
            </tr>
        </table>
        <table style="border-bottom: 5px solid #d8d8d8; width:100%">
            <tr>
                <td style="width: 50%;text-align: left;">Total</td>
                <td style="width: 50%;text-align: right;"></td>
            </tr>
            <tr>
                <td style="width: 50%;text-align: left;">Transactions</td>
                <td style="width: 50%;text-align: right;"></td>
            </tr>
            <tr>
                <td style="width: 50%;text-align: left;">Paid Invoice</td>
                <td style="width: 50%;text-align: right;"></td>
            </tr>
            <tr>
                <td style="width: 50%;text-align: left;">partial Invoice</td>
                <td style="width: 50%;text-align: right;"></td>
            </tr>
            <tr>
                <td style="width: 50%;text-align: left;">Retuerns</td>
                <td style="width: 50%;text-align: right;"></td>
            </tr>
        </table>
        {{-- -------------------------------------------------------- end part ----------------------------------------- --}}
        <table>
            <td style="width: 50%;text-align: left;">Signed ________________</td>
        </table>
</body>
<script>
     $(document).ready(function () { 
  window.print(); 
});
</script>

</html>
