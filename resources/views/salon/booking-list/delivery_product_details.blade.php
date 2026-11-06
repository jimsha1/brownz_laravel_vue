@extends('layouts.vcapp')

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="voco-ajax-modal-label">{{ __('Product Delivery Customer Details') }}</h5>
                    </div>
                    <div class="modal-body">

                        @if (isset($bookingDetails->client_name))
                            <div class="row">
                                <div class="col-md-2">
                                   {{ __('Name') }}
                                </div>

                                <div class="col-md-10">
                                    {{ $bookingDetails->client_name }}
                                </div>
                            </div>
                        @endif


                        @if (isset($bookingDetails->client_mobile))
                            <div class="row">
                                <div class="col-md-2">
                                    {{ __('Mobile') }}
                                </div>

                                <div class="col-md-10">
                                    {{ $bookingDetails->client_mobile }}
                                </div>
                            </div>
                        @endif

                        @if (isset($bookingDetails->client_email))
                            <div class="row">
                                <div class="col-md-2">
                                   {{ __('Email') }}
                                </div>

                                <div class="col-md-10">
                                    {{ $bookingDetails->client_email }}
                                </div>
                            </div>
                        @endif





                    </div>
                </div>
            </div>



        </div>
    </div>

    <div class="col-md-12" style="margin-top: 20px">
        <div class="table-responsive tableFixHead">
            <table class="table table-borderless">
                <thead class="thead-light">
                    <tr>
                        <!---->
                        <th>{{ __('Item') }}</th>
                        <th>{{ __('Item Code') }}</th>
                        <th>{{ __('Quantity') }}</th>
                        <th>{{ __('Net') }}</th>
                        <th>{{ __('Tax') }}</th>
                        <th>{{ __('Total') }}</th>
                        <th class="text-center">{{ __('Change Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $item)
                        <tr>
                            <!---->
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->item_code }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->unit_price }}</td>
                            <td>{{ $item->tax_amount }}</td>
                            <td>{{ $item->line_total }}</td>
                            @if ($item->status == 'pending')
                                <td style="
                                                                    text-align: center;
                                                                " class="center" id="total_item_7"> <a
                                        onclick="checkItemExistInBatchNo('{{ $item->booking_item_id }}', '{{ $item->item_code }}',{{ $bookingDetails->booking_id}})"
                                        data-toggle="tooltip" title="{{ __('change status') }}" data-original-title="{{ __('Re Shedule') }}"
                                        class="btn btn-sm btn-danger" style="color: white; text-transform: capitalize;">{{__($item->status)}}</a></td>
                            @else
                                <td style="
                                                                    text-align: center;
                                                                " class="center" id="total_item_7"><label
                                        style="color: green; text-transform: capitalize;">{{__($item->status)}}</label>
                                </td>
                            @endif
                        </tr>
                    @endforeach

                    <!---->
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="popup" tabindex="-1" role="dialog" aria-labelledby="voco-ajax-modal-label"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="voco-ajax-modal-label"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <input type="hidden" name="item_id" id="item_id" value="">
                        <input type="hidden" name="item_code" id="item_code" value="">
                        <input type="hidden" name="booking_id" id="booking_id" value="{{ $bookingDetails->booking_id }}">
                        <div class="form-group col-md-12">
                            <label for="batch_no">{{ __('Batch No') }}</label>
                            <input type="text" name="batch_no" placeholder="{{ __('Enter batch no') }}" id="batch_no"
                                class="form-control" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="btn-submit">{{ __('Submit') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <script>
        function changeProductStatus(item_id,booking_id) {
            if (confirm("{{ __('Are you sure you want to change status?')}}")) {
                $.ajax({
                    method: "POST",
                    url: "{{ route('salon.booking.product.delivery.status') }}",
                    data: {
                        item_id: item_id,
                        booking_id:booking_id,

                    },
                }).done(function(data) {
                    location.reload();
                });
            }
        }

        function checkItemExistInBatchNo(item_id, item_code,booking_id) {

            $.ajax({
                method: "POST",
                url: "{{ route('salon.booking.product.delivery.check') }}",
                data: {
                    item_code: item_code,
                    booking_id: booking_id,
                },
            }).done(function(data) {
                console.log("The value of data is: ",data);

                if(data == 1) {
                    console.log("working is 1");
                } else {
                    console.log("working is 0 ");
                }

                if (data == 1) {
                    $(".modal-body #item_code").val(item_code);
                    $(".modal-body #item_id").val(item_id);
                    $('#popup').modal('show')
                } else {
                    changeProductStatus(item_id,booking_id);
                }
            });

        }

        document.getElementById("btn-submit").addEventListener("click", function() {
            var batchNo = $(".modal-body #batch_no").val();
            var itemCode = $(".modal-body #item_code").val();
            var itemId = $(".modal-body #item_id").val();
            var booking_id = $(".modal-body #booking_id").val();

            if (batchNo == null || batchNo.length <= 0) {
                alert("{{ __('Please enter batch no.')}}")
            } else if (itemCode == null || itemCode.length <= 0) {
                alert("{{ __('Item code is missing...') }}")
            } else {
                $.ajax({
                    method: "POST",
                    url: "{{ route('salon.booking.product.delivery.validate') }}",
                    data: {
                        item_id: itemId,
                        item_code: itemCode,
                        booking_id : booking_id,
                        batch_no: batchNo,
                    },
                }).done(function(data) {
                    if(data['status'] == 0){
                        location.reload();
                    } else {
                        alert(data['message']);
                    }
                });
            }
        });
    </script>

@endsection
