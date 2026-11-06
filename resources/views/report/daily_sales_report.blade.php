@extends('layouts.vcapp')
<style>
    .daily-sales-report .filters-header,
    .daily-sales-report .list-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .daily-sales-report .filters-title,
    .daily-sales-report .list-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .daily-sales-report .filters-hint,
    .daily-sales-report .list-meta {
        font-size: 12px;
        color: #6c757d;
    }

    .daily-sales-report label {
        font-size: 13px;
        color: #1D1D1D;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .daily-sales-report .search-box-bar-container {
        display: flex;
        align-items: center;
        background-color: #FFFFFF;
        box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;
        border-radius: 26px;
        width: 100%;
        padding-left: 16px;
        min-height: 44px;
    }

    .daily-sales-report .brawnz-search-bar-in {
        background-color: transparent;
        border: none;
        box-shadow: none;
        outline: none;
        padding: 10px 16px 10px 8px;
        font-size: 14px;
        width: 100%;
        height: 44px;
    }

    .daily-sales-report .filter-actions,
    .daily-sales-report .toolbar-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .daily-sales-report .net-hero {
        background: #1D1D1D;
        color: #fff;
        border-radius: 14px;
        padding: 16px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 0 0 14px;
    }

    .daily-sales-report .net-hero .label {
        font-size: 13px;
        color: #E6CD7F;
        margin-bottom: 4px;
    }

    .daily-sales-report .net-hero .value {
        font-size: 1.4rem;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }

    .daily-sales-report .sales-table th,
    .daily-sales-report .sales-table td {
        vertical-align: middle;
        white-space: nowrap;
    }

    .daily-sales-report .sales-table .amount-cell {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    .daily-sales-report .day-link {
        color: #52A8D9;
        font-weight: 600;
        text-decoration: none;
    }

    .daily-sales-report .day-link:hover {
        text-decoration: underline;
    }

    .daily-sales-report .weekday {
        display: block;
        font-size: 11px;
        color: #6c757d;
        font-weight: 500;
    }

    .daily-sales-report .sales-table tbody tr.has-sales {
        background: rgba(230, 205, 127, 0.18);
    }

    .daily-sales-report .sales-table tbody tr.is-today td:first-child {
        box-shadow: inset 3px 0 0 #E6CD7F;
    }

    .daily-sales-report .sales-table tfoot th {
        background: #F7F7F9;
        border-top: 1px solid #e5e5e5;
        font-weight: 700;
    }

    .daily-sales-report .empty-state,
    .daily-sales-report .loading-state {
        text-align: center;
        padding: 40px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }

    .daily-sales-report .refund-note {
        display: block;
        font-size: 11px;
        color: #D0342C;
        font-weight: 600;
    }

    @media print {
        body * {
            visibility: hidden !important;
        }

        #daily-sales-print,
        #daily-sales-print * {
            visibility: visible !important;
        }

        #daily-sales-print {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            background: #fff;
            color: #1D1D1D;
        }

        .daily-sales-report .toolbar-actions,
        .daily-sales-report .day-link {
            text-decoration: none;
            color: #1D1D1D;
        }
    }
</style>
@section('content')
    <div id="daily-sales-app" class="daily-sales-report">
        <div class="fbox-shadow mt-2 fd-card respo-margin-top">
            <div class="card-body">
                <div class="filters-header mb-3">
                    <div>
                        <h5 class="filters-title">{{ __('Daily Sales Report') }}</h5>
                        <div class="filters-hint">{{ __('One row per day. Refunds reduce net sales. Open a date to see that day’s bookings.') }}</div>
                    </div>
                </div>

                <div class="row align-items-end br-row-gap-2">
                    <div class="form-group col-lg-3 col-md-4 col-sm-6 mb-3">
                        <label for="daily_start_date">{{ __('Start Date') }}</label>
                        <div class="search-box-bar-container">
                            <input type="date" id="daily_start_date" class="form-control brawnz-search-bar-in" v-model="filter_start_date">
                        </div>
                    </div>
                    <div class="form-group col-lg-3 col-md-4 col-sm-6 mb-3">
                        <label for="daily_end_date">{{ __('End Date') }}</label>
                        <div class="search-box-bar-container">
                            <input type="date" id="daily_end_date" class="form-control brawnz-search-bar-in" v-model="filter_end_date">
                        </div>
                    </div>
                    <div class="form-group col-lg-6 col-md-4 col-sm-12 mb-3">
                        <label class="d-block">&nbsp;</label>
                        <div class="filter-actions">
                            <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d" @click="setToday()">
                                {{ __('Today') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d" @click="setThisMonth()">
                                {{ __('This Month') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-primary btn_dark-x" @click="loadReport()">
                                <i class="fa fa-search"></i> {{ __('Search') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="text-danger" v-if="errorMessage">@{{ errorMessage }}</div>
            </div>
        </div>

        <div class="fbox-shadow mt-3 fd-card respo-margin-top" id="daily-sales-print">
            <div class="m-3">
                <div class="list-toolbar mb-3">
                    <div>
                        <p class="list-title">{{ __('Daily totals') }}</p>
                        <div class="list-meta" v-if="start_date && end_date">
                            @{{ formatDate(start_date) }} – @{{ formatDate(end_date) }}
                        </div>
                    </div>
                    <div class="toolbar-actions">
                        <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d" @click="printReport()" :disabled="loading || !days.length">
                            <i class="fa fa-print"></i> {{ __('Print') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-success" @click="exportExcel()" :disabled="loading">
                            <i class="fa fa-file-excel-o"></i> {{ __('Excel') }}
                        </button>
                    </div>
                </div>

                <div class="net-hero" v-if="!loading && days.length">
                    <div>
                        <div class="label">{{ __('Net Sales') }}</div>
                        <div class="value">₹ @{{ totals.net }}</div>
                    </div>
                    <div class="text-right">
                        <div class="label">{{ __('Bookings') }}</div>
                        <div class="value">@{{ totals.bookings }}</div>
                    </div>
                </div>

                <div v-if="loading" class="loading-state">
                    <div><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>
                    <div>{{ __('Loading daily sales...') }}</div>
                </div>

                <div v-else-if="!days.length" class="empty-state">
                    <div><strong>{{ __('No days in this range') }}</strong></div>
                </div>

                <div v-else class="table-responsive">
                    <table class="table brownz-table-list-v1 sales-table mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th class="amount-cell">{{ __('Bookings') }}</th>
                                <th class="amount-cell">{{ __('Service') }}</th>
                                <th class="amount-cell">{{ __('Item') }}</th>
                                <th class="amount-cell">{{ __('Tax') }}</th>
                                <th class="amount-cell">{{ __('Discount') }}</th>
                                <th class="amount-cell">{{ __('Cash') }}</th>
                                <th class="amount-cell">{{ __('Card') }}</th>
                                <th class="amount-cell">{{ __('Online') }}</th>
                                <th class="amount-cell">{{ __('Credit') }}</th>
                                <th class="amount-cell">{{ __('Refunds') }}</th>
                                <th class="amount-cell">{{ __('Net Sales') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="day in days" :key="day.date" :class="{ 'has-sales': Number(day.bookings) > 0, 'is-today': day.date === today }">
                                <td>
                                    <a class="day-link" :href="salesReportUrl(day.date)">@{{ day.date_label }}</a>
                                    <span class="weekday">@{{ day.weekday }}</span>
                                </td>
                                <td class="amount-cell">
                                    @{{ day.bookings }}
                                    <span class="refund-note" v-if="Number(day.refund_count) > 0">@{{ day.refund_count }} {{ __('refund') }}</span>
                                </td>
                                <td class="amount-cell">@{{ day.service }}</td>
                                <td class="amount-cell">@{{ day.item }}</td>
                                <td class="amount-cell">@{{ day.tax }}</td>
                                <td class="amount-cell">@{{ day.discount }}</td>
                                <td class="amount-cell">@{{ day.cash }}</td>
                                <td class="amount-cell">@{{ day.card }}</td>
                                <td class="amount-cell">@{{ day.online }}</td>
                                <td class="amount-cell">@{{ day.credit }}</td>
                                <td class="amount-cell">@{{ day.refund }}</td>
                                <td class="amount-cell">@{{ day.net }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>{{ __('Total') }}</th>
                                <th class="amount-cell">@{{ totals.bookings }}</th>
                                <th class="amount-cell">@{{ totals.service }}</th>
                                <th class="amount-cell">@{{ totals.item }}</th>
                                <th class="amount-cell">@{{ totals.tax }}</th>
                                <th class="amount-cell">@{{ totals.discount }}</th>
                                <th class="amount-cell">@{{ totals.cash }}</th>
                                <th class="amount-cell">@{{ totals.card }}</th>
                                <th class="amount-cell">@{{ totals.online }}</th>
                                <th class="amount-cell">@{{ totals.credit }}</th>
                                <th class="amount-cell">@{{ totals.refund }}</th>
                                <th class="amount-cell">@{{ totals.net }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/vuejs/axios/axios.min.js') }}"></script>
    <script>
        function dailyIso(date) {
            var year = date.getFullYear();
            var month = date.getMonth() + 1;
            var day = date.getDate();
            function pad(n) { return n < 10 ? '0' + n : String(n); }
            return year + '-' + pad(month) + '-' + pad(day);
        }

        function dailyMonthStart() {
            var now = new Date();
            return dailyIso(new Date(now.getFullYear(), now.getMonth(), 1));
        }

        new Vue({
            el: '#daily-sales-app',
            data: {
                loading: false,
                errorMessage: '',
                filter_start_date: dailyMonthStart(),
                filter_end_date: dailyIso(new Date()),
                start_date: '',
                end_date: '',
                today: dailyIso(new Date()),
                days: [],
                totals: {
                    bookings: 0,
                    service: '0.00',
                    item: '0.00',
                    tax: '0.00',
                    discount: '0.00',
                    cash: '0.00',
                    card: '0.00',
                    online: '0.00',
                    credit: '0.00',
                    refund: '0.00',
                    net: '0.00'
                }
            },
            mounted: function () {
                this.loadReport();
            },
            methods: {
                formatDate: function (value) {
                    if (!value) {
                        return '';
                    }
                    var parts = String(value).split('-');
                    if (parts.length !== 3) {
                        return value;
                    }
                    return parts[2] + '-' + parts[1] + '-' + parts[0];
                },
                salesReportUrl: function (date) {
                    return "{{ route('sales.report') }}?start_date=" + encodeURIComponent(date) + "&end_date=" + encodeURIComponent(date);
                },
                setToday: function () {
                    var today = dailyIso(new Date());
                    this.filter_start_date = today;
                    this.filter_end_date = today;
                    this.loadReport();
                },
                setThisMonth: function () {
                    this.filter_start_date = dailyMonthStart();
                    this.filter_end_date = dailyIso(new Date());
                    this.loadReport();
                },
                loadReport: function () {
                    var self = this;
                    self.loading = true;
                    self.errorMessage = '';
                    axios.post("{{ route('daily-sales.vue-list') }}", {
                        start_date: self.filter_start_date,
                        end_date: self.filter_end_date
                    }).then(function (response) {
                        var data = response.data || {};
                        if (data.errorcode === 0) {
                            self.days = data.days || [];
                            self.totals = data.totals || self.totals;
                            self.start_date = data.start_date || self.filter_start_date;
                            self.end_date = data.end_date || self.filter_end_date;
                        } else {
                            self.days = [];
                            self.errorMessage = data.errormessage || 'Could not load the daily sales report.';
                        }
                        self.loading = false;
                    }).catch(function (error) {
                        self.days = [];
                        var message = error.response && error.response.data && error.response.data.errormessage;
                        self.errorMessage = message || 'Could not load the daily sales report.';
                        self.loading = false;
                    });
                },
                printReport: function () {
                    window.print();
                },
                exportExcel: function () {
                    var url = "{{ route('daily-sales-report.export-to-excel') }}?start_date="
                        + encodeURIComponent(this.filter_start_date || '')
                        + "&end_date=" + encodeURIComponent(this.filter_end_date || '');
                    window.location.href = url;
                }
            }
        });
    </script>
@endsection
