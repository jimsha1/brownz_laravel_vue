@extends('layouts.vcapp')

@section('content')
<style>
    .vendor-report-filters .filter-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 6px;
    }

    .vendor-report-filters .filter-control {
        height: 38px;
        border: none;
        outline: none;
        border-radius: 999px;
        background: #F0F0F3;
        padding: 8px 14px;
        font-size: 14px;
        color: #1D1D1D;
        width: 100%;
    }

    .vendor-report-filters .filter-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        min-height: 38px;
    }

    @media (min-width: 992px) {
        .vendor-report-filters .filter-actions {
            justify-content: flex-end;
        }
    }

    .vendor-report-list .list-toolbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .vendor-report-list .list-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        font-weight: 500;
        margin: 0;
    }

    .vendor-report-list .list-meta {
        display: block;
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    .vendor-report-list .vendor-name {
        display: block;
        font-weight: 600;
        color: #1D1D1D;
    }

    .vendor-report-list .vendor-meta {
        display: block;
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    .vendor-report-list .status-pill {
        display: inline-flex;
        align-items: center;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        padding: 6px 12px;
        line-height: 1.2;
    }

    .vendor-report-list .status-pill.is-active {
        color: #199900;
        background: rgba(25, 153, 0, 0.15);
    }

    .vendor-report-list .status-pill.is-inactive {
        color: #D0342C;
        background: rgba(208, 52, 44, 0.12);
    }

    .vendor-report-list .export-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .vendor-report-list .amount-cell {
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .vendor-report-list .totals-row td {
        background: #F7F7F9;
        font-weight: 600;
    }

    .vendor-report-list .empty-state,
    .vendor-report-list .loading-state {
        text-align: center;
        padding: 36px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }


    .vendor-report-filters .filter-actions {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        flex-wrap: wrap;
        gap: 10px;
        min-height: 38px;
    }

    .vendor-report-filters .filter-actions .btn {
        min-height: 38px;
        white-space: nowrap;
    }

    .vendor-report-filters .search-box-bar-container {
        width: 100%;
    }

    @media (max-width: 767.98px) {
        .vendor-report-filters .filter-actions {
            margin-top: 12px;
            justify-content: flex-start;
        }
    }

    .failed-status {
        display: inline-block;
        padding: 6px 12px;
        background-color: #f8d7da;
        color: #842029;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        text-transform: lowercase;
    }

    .resend-reason-input {
        height: 70px !important;
        min-height: 70px !important;
        resize: vertical;
    }
</style>

<div id="app">
    <div class="fbox-shadow mt-2 fd-card respo-margin-top vendor-report-filters">
        <div class="card-body">
            <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
                {{ __('Whatsapp Invoice Report') }}
            </h5>


           
<div class="row align-items-end br-row-gap-2">
    <div class="col-lg-4 col-md-6">
        <label class="filter-label">Booking Code</label>
        <input
            type="text"
            class="form-control filter-control"
            v-model="filter_code"
            @keyup.enter="getInvoiceList(1)"
            placeholder="Booking code">
    </div>

    <div class="col-lg-4 col-md-6">
        <label class="filter-label">Client Name</label>
        <input
            type="text"
            class="form-control filter-control"
            v-model="filter_client_name"
            @keyup.enter="getInvoiceList(1)"
            placeholder="Client name">
    </div>

    <div class="col-lg-2 col-md-6">
        <label class="filter-label">Status</label>
        <select class="form-control filter-control"
                v-model="filter_status">
            <option value="">All Status</option>
            <option value="sent">Sent</option>
            <option value="failed">Failed</option>
        </select>
    </div>

    <div class="col-lg-2 col-md-6">
        <div class="filter-actions">
            <button type="button"
                    class="btn btn-sm btn-primary btn_dark-x"
                    @click="getInvoiceList(1)">
                <i class="fa fa-search"></i> Search
            </button>

            <button type="button"
                    class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                    @click="resetFilter()">
                <i class="fa fa-refresh"></i> Reset
            </button>
        </div>
    </div>
</div>


        </div>
    </div>

    <div class="fbox-shadow mt-2 fd-card vendor-report-list">
        <div class="card-body">
            <div class="list-toolbar">
                <div>
                    <h5 class="list-title">{{ __('Whatsapp Invoice List') }}</h5>
                    <span class="list-meta" v-if="!loading && pagination.total">
                        {{ __('Showing') }} @{{ pagination.from || 0 }}–@{{ pagination.to || 0 }}
                        {{ __('of') }} @{{ pagination.total }}
                    </span>
                </div>

            </div>

            <div v-if="loading" class="loading-state">
                <div><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>
                <div>{{ __('Loading invoice...') }}</div>
            </div>

            <template v-else-if="InvoiceList.length">
                <div class="table-responsive">
                    <table class="table brownz-table-list-v1 m-0">
                        <thead>
                            <tr>
                                <th style="width: 56px;">{{ __('SL#') }}</th>
                                <th>{{ __('Booking Code') }}</th>
                                <th>{{ __('Client Name') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('url') }}</th>
                                <th>{{ __('Resend Reason') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(data, index) in InvoiceList" :key="data.id || index">
                                <td>
                                    @{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}
                                </td>

                                <td>
                                    <span class="vendor-name">
                                        @{{ data.booking_code || '—' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="vendor-meta">
                                        @{{ data.booking.client_name || '—' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="vendor-meta">
                                        @{{ formatDate(data.created_at) }}
                                    </span>
                                </td>

                                <td>
                                    <a v-if="data.url"
                                        :href="data.url"
                                        target="_blank"
                                        rel="noopener noreferrer">
                                        View Invoice
                                    </a>
                                    <span v-else>—</span>
                                </td>

                                <td>
                                    <span class="vendor-meta">
                                        @{{ data.reason || '—' }}
                                    </span>
                                </td>

                                <td>
                                    <span
                                        v-if="data.status === 'sent'"
                                        class="status-pill is-active">
                                        @{{ data.status }}
                                    </span>

                                    <template v-else-if="data.status === 'failed'">
                                        <div class="d-flex align-items-center" style="gap: 10px;">
                                            <span class="failed-status">
                                                Failed
                                            </span>

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-primary"
                                                :disabled="resendingId === data.id"
                                                @click="resendInvoice(data)">
                                                <i class="fa fa-refresh"
                                                    :class="{ 'fa-spin': resendingId === data.id }"></i>
                                                @{{ resendingId === data.id ? 'Resending...' : 'Resend' }}
                                            </button>
                                        </div>
                                    </template>

                                    <span v-else>
                                        @{{ data.status || '—' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>

                <nav aria-label="pagination" class="mt-3" v-if="pagination.last_page > 1">
                    <ul class="pagination justify-content-end mb-0">
                        <li class="page-item" v-if="pagination.current_page > 1">
                            <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">«</a>
                        </li>
                        <li class="page-item" v-for="page in pagesNumber" :key="page"
                            :class="{ active: page == isActived }">
                            <a class="page-link" href="#" @click.prevent="changePage(page)">@{{ page }}</a>
                        </li>
                        <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                            <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">»</a>
                        </li>
                    </ul>
                </nav>
            </template>

            <div v-else class="empty-state">
                <div><i class="fa fa-truck" aria-hidden="true"></i></div>
                <div><strong>{{ __('No invoice found') }}</strong></div>
                <div>{{ __('Try adjusting code, id filters.') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vuejs/axios/axios.min.js') }}"></script>
<link href="{{ asset('assets/vuejs/toast/vue_toast.css') }}" rel="stylesheet">
<script src="{{ asset('assets/vuejs/toast/vue_toast.js') }}"></script>
@endpush

@push('inline-scripts')
<script>
    if (typeof VueToast !== 'undefined') {
        Vue.use(VueToast, {
            position: "top-right",
            duration: 5000,
            dismissible: true
        });
    }

    var app = new Vue({
        el: '#app',
        data: {
            loading: false,
            InvoiceList: [],
            page_length: '100',
            pagination: {
                total: 0,
                per_page: 100,
                from: 1,
                to: 0,
                current_page: 1,
                last_page: 1
            },
            offset: 4,
            filter_code: '',
            filter_client_name: '',
            filter_status: '',
            resendingId: '',

        },
        computed: {
            isActived: function() {
                return this.pagination.current_page;
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

            formatDate: function(date) {
                if (!date) return '—';

                var parsedDate = new Date(date);

                if (isNaN(parsedDate.getTime())) return '—';

                return parsedDate.toLocaleString('en-IN', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true,
                    timeZone: 'Asia/Kolkata'
                });
            },

            changePage: function(page) {
                this.getInvoiceList(page);
            },

            getInvoiceList: function(page) {
                var self = this;
                page = page || 1;
                self.loading = true;
                self.pagination.current_page = page;

                axios.post("{{ route('whatsapp-invoice.vue-list') }}", {
                    page: page,
                    page_length: self.page_length,
                     filter_code: self.filter_code,
                     filter_client_name: self.filter_client_name,
                     filter_status: self.filter_status
                }).then(function(response) {
                    if (response.data && Number(response.data.errorcode) === 0) {
                        self.InvoiceList = response.data.data.data || [];
                        self.pagination = response.data.pagination || self.pagination;

                    } else {
                        self.InvoiceList = [];
                        if (Vue.$toast) {
                            Vue.$toast.error((response.data && response.data.errormessage) || '{{ __('Failed to load invoice.') }}');
                        }
                    }
                }).catch(function() {
                    self.InvoiceList = [];
                    if (Vue.$toast) {
                        Vue.$toast.error('{{ __('Failed to load invoice.') }}');
                    }
                }).finally(function() {
                    self.loading = false;
                });
            },

            resendInvoice: function(data) {
                let self = this;

                Swal.fire({
                    title: 'Resend Invoice',
                    text: 'Please enter the reason for resending this invoice.',
                    input: 'textarea',
                    inputPlaceholder: 'Enter reason here...',
                    inputAttributes: {
                        rows: 2,
                        maxlength: 1000,
                        style: 'height: 70px; min-height: 70px;'
                    },
                    width: '450px',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa fa-refresh"></i> Yes, Resend',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    showLoaderOnConfirm: true,

                    inputValidator: function(value) {
                        if (!value || !value.trim()) {
                            return 'Please enter a reason for resending the invoice.';
                        }
                    },

                    preConfirm: function(reason) {
                        return axios.post(
                            `/whatsapp-invoice/${data.id}/resend`, {
                                reason: reason.trim()
                            }
                        ).then(function(response) {
                            if (!response.data.success) {
                                Swal.showValidationMessage(
                                    response.data.message || 'Failed to resend invoice.'
                                );
                                return false;
                            }

                            return response.data;
                        }).catch(function(error) {
                            Swal.showValidationMessage(
                                error.response?.data?.message ||
                                'Unable to resend WhatsApp invoice.'
                            );
                            return false;
                        });
                    },

                    allowOutsideClick: function() {
                        return !Swal.isLoading();
                    }
                }).then(function(result) {
                    if (result.isConfirmed && result.value) {
                        self.getInvoiceList(self.pagination.current_page);

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: result.value.message ||
                                'WhatsApp invoice resent successfully.',
                            confirmButtonColor: '#198754'
                        });
                    }
                });
            },
            resetFilter: function() {
                  this.filter_code = '';
    this.filter_client_name = '';
    this.filter_status = '';
                this.getInvoiceList(1);
            },

        },
        mounted: function() {
            this.getInvoiceList(1);
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush