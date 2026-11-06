@extends('layouts.vcapp')
@section('content')
<style>
  body {
    font-family: arial;
    background: #f0f3f5
}

.logo {
    border: 1px solid #f6f6f6
}

.logo img {
    width: 60px;
    height: 60px
}

.card {
    display: block;
    padding: 3vh 2vh 7vh 5vh;
    border: none;
    border-radius: 15px;
    margin-top: 5%;
    margin-bottom: 5%;
    max-width: 500px
}

.header {
    margin-bottom: 5vh;
    margin-right: 2vh;
    float: right;
    margin-left: auto
}

.far {
    color: rgba(15, 198, 239, 0.97) !important;
    font-size: 16px !important
}

p.heading {
    font-weight: bold;
    font-size: 25px
}

p.text-muted {
    font-size: 17px;
    font-weight: bold;
    color: #a1a7ae !important
}

.btn-sm {
    border-radius: 8px
}

.fas.fa-users {
    color: rgba(15, 198, 239, 0.97) !important
}

.mutual span {
    font-size: 14px;
    color: #adb5bd;
    font-weight: bold
}

.btn-primary.btn-lg {
    border-radius: 30px;
    width: 90%;
    border: none;
    background: #8c02e3
}

.btn-dark.btn-lg {
    border-radius: 30px;
    width: 90%;
    border: none;
    background: #dee2e6
}

.btn-dark span {
    font-size: 14px;
    text-align: center;
    color: #0000008c;
    font-weight: bold
}

.btn-primary span {
    font-size: 14px;
    text-align: center;
    color: #fff;
    font-weight: bold
}
</style>

<div  class="container">
    @foreach ($booking_record as $booking)
  <div class="card mx-auto" style="padding-right: 5vh">
      <div class="row">
          {{-- <div class="logo ml-3 mb-3"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcTxDRpxI5gXgaVmnO-VgcVUNOkca91jIpS75Flbzkz5W_5g5_V5&usqp=CAU"></div> --}}
          {{-- <div class="header right"><i class="fas fa-ellipsis-h"></i></div> --}}
      </div>
      <div class="card-title text-center">
          <p class="heading">Booking Details&nbsp;</p>
      </div>
      
      <div class="table-responsive info-sec-tab-cont">
      <table class="table mb-0">
        <tr>
          <td >Name</td>
          <td><div class="col-md-6"><button type="label" class="btn btn-outline-primary btn-sm " style="background: rgba(82, 168, 217, 0.2) !important; border: none; ">{{$booking->client_name}}</button></div></td>
        </tr>
        <tr>
          <td >Booking Code</td>
          <td><div class="col-md-6"><button type="label" class="btn btn-outline-primary btn-sm " style="background: rgba(82, 168, 217, 0.2) !important; border: none; ">{{$booking->booking_code}}</button></div></td>
        </tr>
        <tr>
          <td >Date</td>
          <td><div class="col-md-6"><button type="label" class="btn btn-outline-primary btn-sm " style="background: rgba(82, 168, 217, 0.2) !important; border: none; ">{{$booking->appointment_date}}</button></div></td>
        </tr>
        <tr>
          <td>Service</td>
          <td><div class="col-md-6"><button type="label" class="btn btn-outline-primary btn-sm " style="background: rgba(82, 168, 217, 0.2) !important; border: none; ">{{$booking->service_name}}</button></div></td>
        </tr>
        <tr>
          <td>Time</td>
          <td><div class="col-md-6"><button type="label" class="btn btn-outline-primary btn-sm " style="background: rgba(82, 168, 217, 0.2) !important; border: none; ">{{$booking->slot_start}}</button></div></td>
        </tr>
      </table>
  </div>
  </div>
  @endforeach
</div>
@endsection
