<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Invoice</title>
<style>
@media print { @page { size: auto; } }
.col, .col-sm-6 {
width: 50%;
float:left;
}
.text-right{
text-align: right
}

</style>
</head>
<body>

<div class="container-fluid">

  <div class="">
    <div class="col">
      <img src="{{ asset('web/images/logo.png') }}" style="max-height:30px">
    </div>

    <div class="col text-right">
      <p class="" style="margin:0">Invoice #BS095</p>
      <p class="">26 Nov, 2020</p>
      <!-- <p class="text-muted">Due to: 4 Dec, 2019</p> -->
    </div>

    <hr class="">
  </div>


  <div class="">
    <div class="col">
      <!-- <p class="font-weight-bold mb-4">Customer</p> -->
      <p class="" style="font-size: 14px;">
         Demo Client <br>
        +916239231970 <br>
        Kota, Puducherry  <br>
        213582  <br>
      </p>
    </div>

    <div class="col text-right">
      <img src="data:image/png;base64, {{ \DNS1D::getBarcodePNG($booking_info->booking_barcode, 'C128') }}" alt="barcode">
      <!-- <p class="font-weight-bold mb-4">Payment Details</p>
      <p class="mb-1"><span class="text-muted">VAT: </span> 1425782</p>
      <p class="mb-1"><span class="text-muted">VAT ID: </span> 10253642</p>
      <p class="mb-1"><span class="text-muted">Payment Type: </span> Root</p>
      <p class="mb-1"><span class="text-muted">Name: </span> John Doe</p> -->
    </div>
  </div>


  <div class="">
    <div class="">
        <table class="table" style="width:100%">
            <thead>
                <tr>
                    <!-- <th class="border-0 text-uppercase small font-weight-bold">ID</th> -->
                    <th class="border-0 text-uppercase small font-weight-bold" style="{{$gift_flag?'width:70%':'width:30%'}}">Item</th>
                    <th class="border-0 text-uppercase small font-weight-bold">Quantity</th>
                    <th class="border-0 text-uppercase small font-weight-bold text-right">Price</th>
                    <th class="border-0 text-uppercase small font-weight-bold text-right">Tax</th>
                    <th class="border-0 text-uppercase small font-weight-bold text-right">Total</th>

                  
                </tr>
            </thead>
            <tbody>
              
                <tr>
                    <!-- <td>1</td> -->
                    <td>Hair</td>
                    <td>1</td>

                   

                    <td class="text-right">860.00</td>


                    <td class="text-right">129.00</td>
                    <td class="text-right">989.00</td>

                    
                </tr>
                <tr class="">
                  <td colspan="5" style="color:#777;padding-bottom:10px">
                    Date: 26 Nov, 2020 Time: 09:00 Beautician: Urvashi Prabhakar 
                  </td>
                </tr>
                
                
            </tbody>
        </table>
    </div>
</div>



<div class="">
    <!-- <div class="py-3 px-5 text-right">
        <div class="mb-2">Grand Total</div>
        <div class="h2 font-weight-light">{{$booking_info->payment_total}}</div>
    </div> -->

    <div class="text-right">
        <!-- <div class="mb-2"></div> -->
        <div class="">
            Sub Total: 860.00
        </div>
    </div>

    <div class="text-right">
        <!-- <div class="mb-2"></div> -->
        <div class="">
            Discount: -0.00 
        </div>
    </div>

    <div class="text-right">
        <!-- <div class="mb-2"></div> -->
        <div class="h5 font-weight-light">
            Net Total: 860.00 
        </div>
    </div>

    <div class="text-right">
        <!-- <div class="mb-2"></div> -->
        <div class="">
            VAT 15%: 129.00 
        </div>
    </div>

    <div class=" text-right">
        <!-- <div class="mb-2"></div> -->
        <div class="">
            Receipt Total: 989.00 
        </div>
    </div>

    
    <div class="text-right">
        Cash Tendered: 989.00 
    </div>
    

 
</div>



</div>

</body>
</html>

