@extends('layouts.vcapp')

@section('content')
    <div id="app">
        <div class="" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="voco-ajax-modal-label">{{ __('Search Product Delivery') }}</h5>

                </div>
                <div class="modal-body">
                    <div>
                        {{-- <form method="get" action="{{ route('salon.booking.product.delivery') }}">
                            @csrf --}}
                        <div class="row">

                            <div class="form-group col-md-4">
                                <label for="code">{{ __('Code') }}</label>
                                <input type="text" name="code" placeholder="{{ __('Code') }}" id="code"
                                    v-model="code" class="form-control" value="{{ request()->get('code') }}">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="status">{{ __('Status') }}</label>

                                <select name="status" v-model="status" class="form-control"
                                    aria-label="Default select example">

                                    <option value="pending">{{ __('Pending') }}</option>
                                    <option value="delivered">{{ __('Delivered') }}</option>
                                    <option value="all">{{ __('All') }} </option>

                                    {{-- <option value="pending"
                                            {{ request()->get('status') == 'pending' ? 'selected' : '' }}>
                                            {{ __('Pending') }}</option>
                                        <option value="All" {{ request()->get('status') == 'All' ? 'selected' : '' }}>
                                            {{ __('All') }} </option>
                                        <option value="delivered"
                                            {{ request()->get('status') == 'delivered' ? 'selected' : '' }}>
                                            {{ __('Delivered') }}</option> --}}

                                </select>
                            </div>
                            <!--</div> -->

                            <div class="row" style="margin-left: 5px;">
                                {{-- <div style="margin:5px;" class="vc-form-buttons text-left">
                    <a href="{{ route('salon.booking.product.delivery') }}"><button  class="btn btn-primary"><i class="fa fa-close"></i>{{ __('Reset') }}
                    </button></a>
                </div> --}}
                                <div style="margin:5px;" class="vc-form-buttons text-left">
                                    <button style="margin-top: 20px;" type="submit" class="btn btn-secondary"
                                        v-on:click="resetFilter()"><i class="fa fa-undo"></i>{{ __('Reset') }}
                                    </button>

                                    <button style="margin-top: 20px;" type="submit" class="btn btn-primary"
                                        v-on:click="getProductDeliveryList()"><i class="fa fa-check"></i>{{ __('Search') }}
                                    </button>

                                </div>
                            </div>
                            {{-- </form> --}}
                        </div>
                    </div>
                </div>
            </div>

            <div id="sync_out" style="margin-top:20px " class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="voco-ajax-modal-label">{{ __('Product Delivery List') }}</h5>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('SL#') }}</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Client') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Date') }}</th>
                            <!-- <th>{{ __('Payment') }}</th> -->
                            <th>{{ __('Source') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="hidden-print no-sort">{{ __('Manage') }}</th>
                            <th class="hidden-print no-sort">{{ __('Print') }}</th>

                        </tr>
                    </thead>
                    <tbody>
                        <template v-if="!productDeliveryList || !productDeliveryList.length">
                            <tr>
                                <td colspan="10">No data found</td>
                            </tr>
                        </template>
                        <template v-else v-for="(data,index) in productDeliveryList">

                            <tr>
                                <td>@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</td>
                                <td>@{{ data.booking_code }}</td>
                                <td>@{{ data.client_name }}</td>
                                <td>@{{ data.booking_total }}</td>
                                <td>@{{ dt_format(data.created_at) }}</td>
                                <td>@{{ (data.booking_source) }}</td>
                                <td>@{{ (data.booking_type) }}</td>
                                <td>@{{ ucwords(data.status) }}</td>

                                <template v-if="data.status">
                                    <td>
                                        <a :href=`{{ route('salon.booking.product.delivery.details', ['booking_id' => '']) }}${data.booking_id}`>
                                            <button class="btn btn-success">{{ __('View') }}</button>
                                        </a>
                                    </td>
                                    <td> <a v-on:click="openPrintWindow(data.booking_id)"><button
                                            class="btn btn-danger">{{ __('Print') }}</button></a>
                                    </td>
                                </template>
                                <template v-else>
                                    <td></td>
                                    <td></td>
                                </template>
                            </tr>

                        </template>

                        {{-- @if (!is_null($bookings) && count($bookings) > 0)
                        @foreach ($bookings as $val => $bk)
                            <tr>
                                <td>{{ ($bookings->currentpage() - 1) * $bookings->perpage() + $loop->index + 1 }}</td>
                                <td>{{ $bk->booking_code }}</td>
                                <td>{{ $bk->client_name }}</td>
                                <td>{{ $bk->booking_total }}</td>
                                <td>{{ $bk->created_at }}</td>
                                <td>{{ __($bk->booking_source) }}</td>
                                <td>{{ __($bk->booking_type) }}</td>
                                <td>{{ ucwords($bk->status) }}</td>



                                @if ($bk->status)
                                    <td> <a
                                            href="{{ route('salon.booking.product.delivery.details', ['booking_id' => $bk->booking_id]) }}"><button
                                                class="btn btn-success">{{ __('View') }}</button></a>
                                    </td>
                                    <td> <a
                                            onclick="openWindow('{{ url('/') . '/salon/booking-details/invoice/' . $bk->booking_id }}')"><button
                                                class="btn btn-danger">{{ __('Print') }}</button></a>
                                    </td>
                                @else
                                    <td></td>
                                    <td></td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                    @endif --}}

                    </tbody>
                </table>

                {{-- pagination --}}
                <div class="pagination">
                    <nav aria-label="pagination">
                        <ul class="pagination justify-content-end">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#" aria-label="Previous"
                                    @click.prevent="changePage(pagination.current_page - 1)">
                                    <span aria-hidden="true">«</span>
                                </a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber"
                                v-bind:class="[ page == isActived ? 'active' : '']">
                                <a class="page-link" href="#"
                                    @click.prevent="changePage(page)">@{{ page }}</a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#" aria-label="Next"
                                    @click.prevent="changePage(pagination.current_page + 1)">
                                    <span aria-hidden="true">»</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
                {{-- end --}}

                {{-- @if (!is_null($bookings) && count($bookings) > 0)
                <div class="pagination">
                    {{ $bookings->links() }}
                </div>
            @endif --}}

            </div>
        </div>

        @push('scripts')
            <script>
                var app = null;
                jQuery(function($) {
                    app = new Vue({
                        el: "#app",
                        data: {
                            productDeliveryList: [],
                            code: '',
                            status: 'pending',

                            search: '',
                            page_length: '100',

                            pagination: {
                                total: 0,
                                per_page: 2,
                                from: 1,
                                to: 0,
                                current_page: 1
                            },
                            offset: 6,

                        },

                        computed: {
                            isActived: function() {
                                return app.pagination.current_page;
                            },
                            pagesNumber: function() {
                                if (!this.pagination.to) {
                                    return [];
                                }
                                var from = this.pagination.current_page - this.offset;
                                if (from < 1) {
                                    from = 1;
                                }
                                var to = from + (this.offset * 2);
                                if (to >= this.pagination.last_page) {
                                    to = this.pagination.last_page;
                                }
                                var pagesArray = [];
                                while (from <= to) {
                                    pagesArray.push(from);
                                    from++;
                                }
                                return pagesArray;
                            }
                        },

                        methods: {

                            handleChangePage(e) {
                                console.log(this.page_length)
                                app.getProductDeliveryList();
                            },

                            changePage: function(page) {
                                this.pagination.current_page = page;
                                app.getProductDeliveryList(page);
                            },

                            getProductDeliveryList: function(page = 1) {
                                this.loading = true;

                                $.ajax({
                                    method: "POST",
                                    url: "{{ route('salon.booking.product.delivery') }}",
                                    data: {
                                        "_token": "{{ csrf_token() }}",
                                        "page": page,
                                        "code": this.code,
                                        "status": this.status
                                    },
                                }).done(function(data) {
                                    // console.log(data.data.data);

                                    app.productDeliveryList = data.data.data;
                                    app.pagination = data.pagination;
                                });

                            },

                            openPrintWindow: function(data){
                                url = '{{ url('/') }}' + '/salon/booking-details/invoice/' + data;
                                invoice_window = window.open(url, "",
                                    "toolbar=no,status=no,menubar=no,location=center,scrollbars=no,resizable=no,height=500,width=657"
                                );
                            },

                            resetFilter: function() {
                                app.code = '';
                                app.status = 'pending';
                                app.getProductDeliveryList();
                            },

                            // date time format
                            dt_format: function(val) {
                                var localTime = moment(val).toDate();
                                localTime = moment(localTime).format('DD-MM-YYYY HH:mm:ss A');
                                return localTime;
                                // console.log(localTime);
                            },

                            // date format
                            d_format: function(val) {
                                var localTime = moment(val).toDate();
                                localTime = moment(localTime).format('DD-MM-YYYY');
                                return localTime;
                                // console.log(localTime);
                            },

                            // time format
                            t_format: function(val) {
                                return moment(val, 'hh:mm').format('hh:mm A');
                                // console.log(moment(val,'hh:mm').format('hh:mm A'));
                            },

                            // Capitalise Word
                            ucwords: function(value) {
                                return (value + '').replace(/^(.)|\s+(.)/g, function($1) {
                                    return $1.toUpperCase();
                                });
                            },
                        },

                        mounted() {
                            this.getProductDeliveryList();
                        },
                    });
                });
            </script>

            <script>
                function openWindow(params) {
                    invoice_window = window.open(params, "",
                        "toolbar=no,status=no,menubar=no,location=center,scrollbars=no,resizable=no,height=500,width=657"
                    );
                }
            </script>
        @endpush
    @endsection
