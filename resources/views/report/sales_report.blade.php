@extends('layouts.vcapp')
<style>
    .v--modal-overlay .v--modal-box {
        border-radius: 24px;
        padding: 12px;
    }

    .summary-content {
        display: none;
    }

    .sales-summary-modal .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: none;
    }

    .sales-summary-modal .modal-header {
        border-bottom: none;
        padding: 18px 20px 8px;
        align-items: flex-start;
    }

    .sales-summary-modal .modal-title {
        color: #1D1D1D;
        font-size: 1.35rem;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .sales-summary-modal .summary-date {
        margin: 0;
        color: #6c757d;
        font-size: 13px;
    }

    .sales-summary-modal .modal-body {
        padding: 8px 20px 12px !important;
    }

    .sales-summary-modal .summary-hero {
        background: #1D1D1D;
        color: #fff;
        border-radius: 14px;
        padding: 16px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .sales-summary-modal .summary-hero .label {
        font-size: 13px;
        color: #E6CD7F;
        margin-bottom: 4px;
    }

    .sales-summary-modal .summary-hero .value {
        font-size: 1.4rem;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }

    .sales-summary-modal .summary-rows {
        background: #F7F7F9;
        border-radius: 14px;
        padding: 6px 14px;
    }

    .sales-summary-modal .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #ececef;
        margin: 0;
        font-size: 14px;
    }

    .sales-summary-modal .summary-row:last-child {
        border-bottom: none;
    }

    .sales-summary-modal .summary-row .label {
        color: #4a4a4a;
    }

    .sales-summary-modal .summary-row .value {
        font-weight: 600;
        color: #1D1D1D;
        font-variant-numeric: tabular-nums;
    }

    .sales-summary-modal .summary-empty {
        background: #F7F7F9;
        border-radius: 14px;
        padding: 24px 16px;
        text-align: center;
        color: #6c757d;
    }

    .sales-summary-modal .modal-footer {
        border-top: none;
        padding: 8px 20px 18px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .sales-summary-modal .btn-summary-close,
    .sales-summary-modal .btn-summary-print {
        min-width: 96px;
    }

    #sales-summary-print-root {
        display: none;
    }

    @media print {
        body * {
            visibility: hidden !important;
        }

        #sales-summary-print-root,
        #sales-summary-print-root * {
            visibility: visible !important;
        }

        #sales-summary-print-root {
            display: block !important;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 24px;
            background: #fff;
            color: #1D1D1D;
            font-family: Arial, Helvetica, sans-serif;
        }

        #sales-summary-print-root h1 {
            font-size: 22px;
            margin: 0 0 6px;
        }

        #sales-summary-print-root .date {
            color: #6c757d;
            font-size: 13px;
            margin-bottom: 18px;
        }

        #sales-summary-print-root .hero {
            background: #1D1D1D;
            color: #fff;
            border-radius: 12px;
            padding: 16px 18px;
            margin-bottom: 16px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        #sales-summary-print-root .hero .label {
            color: #E6CD7F;
            font-size: 13px;
            margin-bottom: 4px;
        }

        #sales-summary-print-root .hero .value {
            font-size: 24px;
            font-weight: 700;
        }

        #sales-summary-print-root table {
            width: 100%;
            border-collapse: collapse;
        }

        #sales-summary-print-root td {
            padding: 12px 4px;
            border-bottom: 1px solid #ececef;
            font-size: 14px;
        }
    }

    .sales-report-list .list-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
    }

    .sales-report-list .list-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .sales-report-list .list-meta {
        font-size: 12px;
        color: #6c757d;
    }

    .sales-report-list .toolbar-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .sales-report-list .sales-table th,
    .sales-report-list .sales-table td {
        vertical-align: middle;
        white-space: nowrap;
    }

    .sales-report-list .sales-table .amount-cell {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    .sales-report-list .booking-code-link {
        color: #52A8D9;
        font-weight: 600;
        text-decoration: none;
    }

    .sales-report-list .booking-code-link:hover {
        text-decoration: underline;
    }

    .sales-report-list .client-name {
        font-weight: 500;
        color: #1D1D1D;
        white-space: normal;
        max-width: 180px;
    }

    .sales-report-list .type-badge,
    .sales-report-list .mode-chip {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        margin: 1px 2px;
    }

    .sales-report-list .type-badge.is-sale {
        background: rgba(25, 153, 0, 0.12);
        color: #199900;
    }

    .sales-report-list .type-badge.is-refund {
        background: rgba(208, 52, 44, 0.12);
        color: #D0342C;
    }

    .sales-report-list .mode-chip {
        background: #F0F0F3;
        color: #1D1D1D;
        text-transform: capitalize;
    }

    .sales-report-list .sales-table tfoot th {
        background: #F7F7F9;
        border-top: 1px solid #e5e5e5;
        font-weight: 700;
    }

    .sales-report-list .empty-state,
    .sales-report-list .loading-state {
        text-align: center;
        padding: 40px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }

    .sales-report-list .empty-state i,
    .sales-report-list .loading-state i {
        font-size: 28px;
        margin-bottom: 10px;
        color: #b7b7b7;
    }

    .sales-report-filters .filters-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
    }

    .sales-report-filters .filters-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .sales-report-filters .filters-hint {
        font-size: 12px;
        color: #6c757d;
    }

    .sales-report-filters label {
        font-size: 13px;
        color: #1D1D1D;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .sales-report-filters .search-box-bar-container {
        display: flex;
        align-items: center;
        background-color: #FFFFFF;
        box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;
        border-radius: 26px;
        width: 100%;
        padding-left: 16px;
        min-height: 44px;
    }

    .sales-report-filters .brawnz-search-bar-in {
        background-color: transparent;
        border: none;
        box-shadow: none;
        outline: none;
        padding: 10px 16px 10px 8px;
        font-size: 14px;
        width: 100%;
        height: 44px;
    }

    .sales-report-filters select.brawnz-search-bar-in {
        padding-right: 28px;
        appearance: auto;
    }

    .sales-report-filters .filter-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
        margin-top: 8px;
    }
</style>
@section('content')
    <div id="app">

        <div class="fbox-shadow mt-2 fd-card respo-margin-top sales-report-filters">
            <div class="card-body">
                <div class="filters-header">
                    <div>
                        <h5 class="filters-title">{{ __('Sales Report') }}</h5>
                        <div class="filters-hint">{{ __('Filter by date, client, booking, payment and type') }}</div>
                    </div>
                </div>

                <div class="row align-items-end br-row-gap-2">
                    <div class="form-group col-lg-3 col-md-4 col-sm-6 mb-3">
                        <label for="filter_start_date">{{ __('Start Date') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <input type="date"
                                id="filter_start_date"
                                class="form-control brawnz-search-bar-in"
                                v-model="filter_start_date">
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6 mb-3">
                        <label for="filter_end_date">{{ __('End Date') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <input type="date"
                                id="filter_end_date"
                                class="form-control brawnz-search-bar-in"
                                v-model="filter_end_date">
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6 mb-3">
                        <label for="filter_client_name">{{ __('Client Name') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <p class="mb-0"><i class="fa fa-user" aria-hidden="true"></i></p>
                            <input type="text"
                                id="filter_client_name"
                                class="form-control brawnz-search-bar-in"
                                v-model="filter_client_name"
                                placeholder="{{ __('Enter client name') }}"
                                @keyup.enter="getSalesList(1)">
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6 mb-3">
                        <label for="filter_booking_code">{{ __('Booking Code') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <p class="mb-0"><i class="fa fa-hashtag" aria-hidden="true"></i></p>
                            <input type="text"
                                id="filter_booking_code"
                                class="form-control brawnz-search-bar-in"
                                v-model="filter_booking_code"
                                placeholder="{{ __('Enter booking code') }}"
                                @keyup.enter="getSalesList(1)">
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6 mb-3">
                        <label for="filter_payment_mode">{{ __('Payment Mode') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <select id="filter_payment_mode"
                                v-model="filter_payment_mode"
                                class="form-control brawnz-search-bar-in">
                                <option value="">{{ __('All Modes') }}</option>
                                <option value="cash">{{ __('Cash') }}</option>
                                <option value="card">{{ __('Card') }}</option>
                                <option value="online">{{ __('Online') }}</option>
                                <option value="credit">{{ __('Credit') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6 mb-3">
                        <label for="filter_type">{{ __('Type') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <select id="filter_type"
                                v-model="filter_type"
                                class="form-control brawnz-search-bar-in">
                                <option value="All">{{ __('All') }}</option>
                                <option value="Sale">{{ __('Sales') }}</option>
                                <option value="Refund">{{ __('Refund') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6 mb-3">
                        <label for="filter_line_type">{{ __('Service / Item') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <select id="filter_line_type"
                                v-model="filter_line_type"
                                class="form-control brawnz-search-bar-in">
                                <option value="">{{ __('All') }}</option>
                                <option value="service">{{ __('Service') }}</option>
                                <option value="item">{{ __('Item') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6 mb-3">
                        <label class="d-block">&nbsp;</label>
                        <div class="filter-actions">
                            <button type="button"
                                class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                                v-on:click="resetFilter()">
                                <i class="fa fa-refresh"></i> {{ __('Reset') }}
                            </button>
                            <button type="button"
                                class="btn btn-sm btn-primary btn_dark-x"
                                v-on:click="getSalesList(1)">
                                <i class="fa fa-search"></i> {{ __('Search') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fbox-shadow mt-3 fd-card respo-margin-top sales-report-list">

            <div class="m-3">
                <div class="list-toolbar">
                    <div>
                        <p class="list-title">{{ __('Results') }}</p>
                        <div class="list-meta" v-if="pagination.total">
                            {{ __('Showing') }}
                            @{{ pagination.from || 0 }}–@{{ pagination.to || 0 }}
                            {{ __('of') }}
                            @{{ pagination.total }}
                        </div>
                    </div>
                    <div class="toolbar-actions">
                        <button type="button" class="btn btn-sm brownz-btn-yellow-d" v-on:click="popupModal(0)">
                            <i class="fa fa-pie-chart"></i> {{ __('View Summary') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" v-on:click="exportToPDF()">
                            <i class="fa fa-file-pdf-o"></i> PDF
                        </button>
                        <button type="button" class="btn btn-sm btn-success" v-on:click="exportToExcel()">
                            <i class="fa fa-file-excel-o"></i> Excel
                        </button>
                    </div>
                </div>

                <div v-if="loading" class="loading-state">
                    <div><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>
                    <div>{{ __('Loading sales...') }}</div>
                </div>

                <div v-else-if="!SalesList || !SalesList.length" class="empty-state">
                    <div><i class="fa fa-inbox" aria-hidden="true"></i></div>
                    <div><strong>{{ __('No sales found') }}</strong></div>
                    <div>{{ __('Try adjusting the date range or filters.') }}</div>
                </div>

                <div v-else class="table-responsive">
                    <table class="table brownz-table-list-v1 sales-table mb-0">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 56px;">#</th>
                                <th scope="col">{{ __('Booking Date') }}</th>
                                <th scope="col">{{ __('Booking Code') }}</th>
                                <th scope="col">{{ __('Client Name') }}</th>
                                <th scope="col">{{ __('Payment Mode') }}</th>
                                <th scope="col">{{ __('Type') }}</th>
                                <th scope="col" class="amount-cell">{{ __('Service') }}</th>
                                <th scope="col" class="amount-cell">{{ __('Item') }}</th>
                                <th scope="col" class="amount-cell">{{ __('Net Total') }}</th>
                                <th scope="col" class="amount-cell">{{ __('Tax Amount') }}</th>
                                <th scope="col" class="amount-cell">{{ __('Discount') }}</th>
                                <th scope="col" class="amount-cell">{{ __('Payment Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(data, index) in SalesList" :key="data.booking_id || index">
                                <td>@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</td>
                                <td>
                                    @{{ data.booking_services && data.booking_services.appointment_date
                                        ? dt_format(data.booking_services.appointment_date)
                                        : (data.created_at ? dt_format(data.created_at) : '—') }}
                                </td>
                                <td>
                                    <a class="booking-code-link"
                                        v-bind:href="'/salon/booking/tab/' + data.booking_id + '/edit'"
                                        target="_blank"
                                        rel="noopener">
                                        @{{ data.booking_code || '—' }}
                                    </a>
                                </td>
                                <td>
                                    <div class="client-name">@{{ data.client_name || '—' }}</div>
                                </td>
                                <td>
                                    <template v-if="data.booking_payment && data.booking_payment.length">
                                        <span v-for="(payment, paymentIndex) in data.booking_payment"
                                            :key="paymentIndex"
                                            class="mode-chip">
                                            @{{ payment.payment_mode_locale || payment.payment_mode || '—' }}
                                        </span>
                                    </template>
                                    <span v-else class="text-muted">—</span>
                                </td>
                                <td>
                                    <span class="type-badge"
                                        :class="((data.booking_type || 'Sale').toLowerCase().indexOf('refund') !== -1) ? 'is-refund' : 'is-sale'">
                                        @{{ data.booking_type || 'Sale' }}
                                    </span>
                                </td>
                                <td class="amount-cell">@{{ calculateServiceSalesTotal(data) }}</td>
                                <td class="amount-cell">@{{ calculateItemSalesTotal(data) }}</td>
                                <td class="amount-cell">@{{ calculateNetTotal(data) }}</td>
                                <td class="amount-cell">@{{ calculateBookingTaxTotal(data) }}</td>
                                <td class="amount-cell">@{{ calculateBookingDiscountTotals(data) }}</td>
                                <td class="amount-cell">
                                    <span v-if="hasMatchingPaymentMode(data.booking_payment, filter_payment_mode)">
                                        @{{ calculateFilteredAmount(data.booking_payment, filter_payment_mode, data) }}
                                    </span>
                                    <span v-else>
                                        @{{ formatSignedPaymentTotal(data) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-right">{{ __('Total') }}</th>
                                <th class="amount-cell">@{{ sumOfServiceSales }}</th>
                                <th class="amount-cell">@{{ sumOfItemSales }}</th>
                                <th class="amount-cell">@{{ sumOfNetTotal }}</th>
                                <th class="amount-cell">@{{ sumOfTaxTotal }}</th>
                                <th class="amount-cell">@{{ sumOfDiscountTotal }}</th>
                                <th class="amount-cell">@{{ sumOfTotal }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- pagination --}}
            <nav aria-label="pagination" v-if="!loading && SalesList && SalesList.length">
                <ul class="pagination justify-content-end px-3">
                    <li class="page-item" v-if="pagination.current_page > 1">
                        <a class="page-link" href="#" aria-label="Previous"
                            @click.prevent="changePage(pagination.current_page - 1)">
                            <span aria-hidden="true">«</span>
                        </a>
                    </li>
                    <li class="page-item" v-for="page in pagesNumber" :key="page"
                        v-bind:class="[ page == isActived ? 'active' : '']">
                        <a class="page-link" href="#" @click.prevent="changePage(page)">@{{ page }}</a>
                    </li>
                    <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                        <a class="page-link" href="#" aria-label="Next"
                            @click.prevent="changePage(pagination.current_page + 1)">
                            <span aria-hidden="true">»</span>
                        </a>
                    </li>
                </ul>
            </nav>
            {{-- end --}}

            <!-- Summary Modal -->
            <modal name="summaryModal" width="520" height="auto" :scrollable="true" :adaptive="true"
                class="brownz-custom-popup m-0 sales-summary-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h4 class="modal-title">{{ __('Sales Summary') }}</h4>
                            <p class="summary-date" v-if="summaryData">
                                {{ __('Date Range') }}:
                                @{{ dt_format(summaryData.start_date) }} {{ __('To') }} @{{ dt_format(summaryData.end_date) }}
                            </p>
                        </div>
                        <button type="button" class="bootbox-close-button close"
                            v-on:click="$modal.hide('summaryModal')" data-dismiss="modal" aria-label="Close">
                            ×
                        </button>
                    </div>

                    <div class="modal-body">
                        <template v-if="summaryData">
                            <div class="summary-hero">
                                <div>
                                    <div class="label">
                                        @{{ (filter_type || 'All').toLowerCase() === 'refund'
                                            ? 'Refunds'
                                            : ((filter_type || 'All').toLowerCase() === 'sale' || (filter_type || 'All').toLowerCase() === 'sales'
                                                ? 'Sales'
                                                : 'Net Sales') }}
                                    </div>
                                    <div class="value">₹ @{{ summaryData.total_sales || '0.00' }}</div>
                                </div>
                            </div>

                            <div class="summary-rows">
                                <p class="summary-row">
                                    <span class="label">{{ __('Cash Sales') }}</span>
                                    <span class="value">₹ @{{ summaryData.cash_sales || '0.00' }}</span>
                                </p>
                                <p class="summary-row">
                                    <span class="label">{{ __('Online Sales') }}</span>
                                    <span class="value">₹ @{{ summaryData.online_sales || '0.00' }}</span>
                                </p>
                                <p class="summary-row">
                                    <span class="label">{{ __('Card Sales') }}</span>
                                    <span class="value">₹ @{{ summaryData.card_sales || '0.00' }}</span>
                                </p>
                                <p class="summary-row">
                                    <span class="label">{{ __('Credit Sales') }}</span>
                                    <span class="value">₹ @{{ summaryData.credit_sales || '0.00' }}</span>
                                </p>
                            </div>
                        </template>
                        <div v-else class="summary-empty">
                            {{ __('No summary data available for the selected date range.') }}
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-summary-print"
                            @click="printSummary">
                            <i class="fa fa-print"></i> {{ __('Print') }}
                        </button>
                        <button type="button" class="btn btn-sm brownz-btn-yellow-d btn-summary-close"
                            v-on:click="$modal.hide('summaryModal')">
                            {{ __('Close') }}
                        </button>
                    </div>
                </div>
            </modal>
            <!-- Summary Modal -->

            <div id="sales-summary-print-root" aria-hidden="true"></div>

        </div>



    </div>
    <script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
    <script>
        var vc_moment_date_offset = new Date("2021-10-05T17:28:50.403+03:00").getTime() - Date.now();
        var vc_jscalendar_moment_format = "DD/MM/YYYY";

        function currentMonthRange() {
            var now = new Date();
            var year = now.getFullYear();
            var month = now.getMonth();
            var start = new Date(year, month, 1);
            var end = new Date(year, month + 1, 0);
            function pad(n) { return n < 10 ? '0' + n : String(n); }
            return {
                start: year + '-' + pad(month + 1) + '-' + pad(start.getDate()),
                end: year + '-' + pad(month + 1) + '-' + pad(end.getDate())
            };
        }

        var monthRange = currentMonthRange();
    </script>
    <script>
        Vue.use(window["vue-js-modal"].default);
        var app = new Vue({
            el: '#app',

            data: {

                loading: false,

                base_url: "{{ '/' }}",

                SalesList: [],

                sumOfCredit: '',
                sumOfDebit: '',

                //
                formSubmitted: false,

                search: '',
                page_length: '100',

                pagination: {
                    total: 0,
                    per_page: 2,
                    from: 1,
                    to: 0,
                    current_page: 1
                },
                offset: 4,
                formErrors: {},

                //
                filter_client_name: '',
                filter_end_date: monthRange.end,
                filter_start_date: monthRange.start,
                filter_payment_mode: '',
                filter_booking_code: '',
                filter_type: 'All',
                filter_line_type: '',

                summaryData: '',
            },

            //

            computed: {

                sumOfServiceSales() {
                    return this.SalesList.reduce((total, data) => {
                        return total + parseFloat(this.calculateServiceSalesTotal(data) || 0);
                    }, 0).toFixed(2);
                },

                sumOfItemSales() {
                    return this.SalesList.reduce((total, data) => {
                        return total + parseFloat(this.calculateItemSalesTotal(data) || 0);
                    }, 0).toFixed(2);
                },

                sumOfNetTotal() {
                    return this.SalesList.reduce((total, data) => {
                        return total + parseFloat(this.calculateNetTotal(data) || 0);
                    }, 0).toFixed(2);
                },

                sumOfTaxTotal() {
                    return this.SalesList.reduce((total, data) => {
                        return total + parseFloat(this.calculateBookingTaxTotal(data) || 0);
                    }, 0).toFixed(2);
                },

                sumOfDiscountTotal() {
                    return this.SalesList.reduce((total, data) => {
                        return total + parseFloat(this.calculateBookingDiscountTotals(data) || 0);
                    }, 0).toFixed(2);
                },

                sumOfTotal() {
                    if (this.filter_payment_mode) {
                        // Net of change (amount_taken - amount_given), signed for refunds
                        return this.SalesList.reduce((total, data) => {
                            const payments = Array.isArray(data.booking_payment) ? data.booking_payment : [];
                            const paymentSum = payments
                                .filter(payment => payment.payment_mode === this.filter_payment_mode)
                                .reduce((sum, payment) => {
                                    const net = parseFloat(payment.amount_taken || 0) - parseFloat(payment.amount_given || 0);
                                    return sum + Math.abs(net);
                                }, 0);
                            return total + (paymentSum * this.salesSign(data));
                        }, 0).toFixed(2);
                    } else {
                        // When no filter_payment_mode, sum signed payment_total
                        return this.SalesList.reduce((total, data) => {
                            return total + (Math.abs(parseFloat(data.payment_total || 0)) * this.salesSign(data));
                        }, 0).toFixed(2);
                    }
                },

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

            //

            methods: {

                popupModal: function(data) {
                    if (data === 0) {
                        this.getSummaryData();
                        this.$modal.show('summaryModal');
                    }
                },
                getSummaryData() {
                    if (!this.filter_start_date || !this.filter_end_date) {
                        console.warn("Start date and end date are required.");
                        this.summaryData = null;
                        return;
                    }

                    this.loading = true;
                    const url = "{{ route('sales.summary') }}";
                    axios.post(url, {
                        start_date: this.filter_start_date,
                        end_date: this.filter_end_date,
                        payment_mode: this.filter_payment_mode,
                        type: this.filter_type || 'All',
                        line_type: this.filter_line_type || ''
                    }).then(response => {
                        if (response.data.errorcode === 0) {
                            app.summaryData = response.data.summary;
                        } else {
                            app.summaryData = null;
                        }
                        this.loading = false;
                    }).catch(error => {
                        console.error('Error fetching summary data:', error);
                        app.summaryData = null;
                        this.loading = false;
                    });
                },
                calculateBookingDiscountTotals(bookingData) {
                    let totalDiscount = 0;
                    let totalIndividualDiscount = 0;

                    // Calculate discount from services
                    if (bookingData && bookingData.booking_only_services) {
                        const serviceTotals = bookingData.booking_only_services.reduce(
                            (acc, service) => {
                                const discountAmount = parseFloat(service.discount_amount) || 0;
                                const individualDiscountAmt = parseFloat(service.individual_discount_amt) || 0;

                                acc.totalDiscount += discountAmount;
                                acc.totalIndividualDiscount += individualDiscountAmt;

                                return acc;
                            }, {
                                totalDiscount: 0,
                                totalIndividualDiscount: 0
                            }
                        );
                        totalDiscount += serviceTotals.totalDiscount;
                        totalIndividualDiscount += serviceTotals.totalIndividualDiscount;
                    }

                    // Calculate discount from items
                    if (bookingData && bookingData.booking_items && bookingData.booking_items.length > 0) {
                        const itemTotals = bookingData.booking_items.reduce(
                            (acc, item) => {
                                const discountAmount = parseFloat(item.discount_amount) || 0;
                                return acc + discountAmount;
                            }, 0
                        );
                        totalDiscount += itemTotals;
                    }

                    const appliedDiscountPrice = parseFloat(bookingData?.applied_discount_price) || 0;

                    const combinedDiscount = totalDiscount + totalIndividualDiscount + appliedDiscountPrice;
                    return (Math.abs(combinedDiscount) * this.salesSign(bookingData)).toFixed(2);
                },

                isRefundBooking(bookingData) {
                    const type = String((bookingData && bookingData.booking_type) || '').toLowerCase();
                    return type === 'refund' || type === 'refunded';
                },

                // Refunds reduce net sales except on Refund-only drill-down
                salesSign(bookingData) {
                    if (!this.isRefundBooking(bookingData)) {
                        return 1;
                    }
                    if (String(this.filter_type || '').toLowerCase() === 'refund') {
                        return 1;
                    }
                    return -1;
                },

                formatSignedPaymentTotal(bookingData) {
                    const amount = Math.abs(parseFloat(bookingData?.payment_total) || 0);
                    return (amount * this.salesSign(bookingData)).toFixed(2);
                },

                calculateServiceSalesTotal(bookingData) {
                    if (!bookingData || !bookingData.booking_only_services) {
                        return "0.00";
                    }

                    // Sum up the `line_total` for services
                    const totalServiceSales = bookingData.booking_only_services.reduce((acc, service) => {
                        const lineTotal = parseFloat(service.line_total) || 0;
                        return acc + lineTotal;
                    }, 0);

                    return (Math.abs(totalServiceSales) * this.salesSign(bookingData)).toFixed(2);
                },

                calculateItemSalesTotal(bookingData) {
                    if (!bookingData || !bookingData.booking_items || bookingData.booking_items.length === 0) {
                        return "0.00";
                    }

                    // Sum up the `line_total` for items
                    const totalItemSales = bookingData.booking_items.reduce((acc, item) => {
                        const lineTotal = parseFloat(item.line_total) || 0;
                        return acc + lineTotal;
                    }, 0);

                    return (Math.abs(totalItemSales) * this.salesSign(bookingData)).toFixed(2);
                },

                calculateNetTotal(bookingData) {
                    // Calculate total from both services and items
                    const serviceTotal = parseFloat(this.calculateServiceSalesTotal(bookingData)) || 0;
                    const itemTotal = parseFloat(this.calculateItemSalesTotal(bookingData)) || 0;
                    return (serviceTotal + itemTotal).toFixed(2);
                },


                calculateBookingTaxTotal(bookingData) {
                    let totalTax = 0;

                    // Calculate tax from services
                    if (bookingData && bookingData.booking_only_services) {
                        totalTax += bookingData.booking_only_services.reduce((acc, service) => {
                            const taxAmount = parseFloat(service.tax_amount) || 0;
                            return acc + taxAmount;
                        }, 0);
                    }

                    // Calculate tax from items
                    if (bookingData && bookingData.booking_items && bookingData.booking_items.length > 0) {
                        totalTax += bookingData.booking_items.reduce((acc, item) => {
                            const taxAmount = parseFloat(item.tax_amount) || 0;
                            return acc + taxAmount;
                        }, 0);
                    }

                    return (Math.abs(totalTax) * this.salesSign(bookingData)).toFixed(2);
                },

                // Check if any payment mode matches the filter
                hasMatchingPaymentMode(bookingPayments, filterPaymentMode) {
                    if (!filterPaymentMode || !Array.isArray(bookingPayments)) return false;
                    return bookingPayments.some(payment => payment.payment_mode === filterPaymentMode);
                },

                // Calculate total amount for filtered payment mode (net of change, signed for refunds)
                calculateFilteredAmount(bookingPayments, filterPaymentMode, bookingData) {
                    if (!filterPaymentMode || !Array.isArray(bookingPayments)) return '0.00';
                    const raw = bookingPayments
                        .filter(payment => payment.payment_mode === filterPaymentMode)
                        .reduce((total, payment) => {
                            const net = parseFloat(payment.amount_taken || 0) - parseFloat(payment.amount_given || 0);
                            return total + net;
                        }, 0);
                    return (Math.abs(raw) * this.salesSign(bookingData)).toFixed(2);
                },


                dt_format: function(val) {
                    // Format the date to 'DD-MM-YYYY'
                    return moment(val).format('DD-MM-YYYY');
                },


                validateBeforeSubmit(e) {
                    console.log(this.errors);
                    this.$validator.validateAll();
                    if (!this.errors.any()) {
                        this.submitForm()
                    }
                },
                validatePriceBeforeSubmit(e) {
                    console.log(this.errors);
                    this.$validator.validateAll();
                    if (!this.errors.any()) {
                        this.submitPriceForm()
                    }
                },
                submitForm() {
                    // this.formSubmitted = true
                    this.addEditSubmitForm()
                },
                submitPriceForm() {
                    // this.formSubmitted = true
                    this.addEditSubmitPriceForm()
                },

                changePage: function(page) {
                    this.pagination.current_page = page;
                    app.getSalesList(page);
                },

                getSalesList: function(page = 1) {

                    url = "{{ route('sales.vue-list') }}";
                    this.loading = true;

                    axios.post(url, {
                        "page": page,
                        "page_length": this.page_length,
                        "search": this.search,
                        "client_name": this.filter_client_name,
                        "booking_code": this.filter_booking_code,
                        "start_date": this.filter_start_date,
                        "end_date": this.filter_end_date,
                        "payment_mode": this.filter_payment_mode,
                        "type": this.filter_type || 'All',
                        "line_type": this.filter_line_type || '',

                    }).then(function(response) {
                        if (response.data && response.data.errorcode === 0) {
                            app.SalesList = (response.data.data && response.data.data.data)
                                ? response.data.data.data
                                : [];
                            app.pagination = response.data.pagination || app.pagination;
                        } else {
                            app.SalesList = [];
                            console.error('Sales list error:', response.data && response.data.errormessage);
                        }
                        app.loading = false;
                    }).catch(function(error) {
                        console.error('Sales list request failed:', error);
                        app.SalesList = [];
                        app.loading = false;
                    });

                },

                resetFilter: function() {
                    this.applyDefaultDates();
                    this.filter_client_name = '';
                    this.filter_booking_code = '';
                    this.filter_payment_mode = '';
                    this.filter_type = 'All';
                    this.filter_line_type = '';
                    this.getSalesList(1);
                },

                applyDefaultDates: function() {
                    var range = currentMonthRange();
                    this.filter_start_date = range.start;
                    this.filter_end_date = range.end;
                },

                exportToPDF: function() {

                    var url = "{{ route('sales-report.export-to-pdf') }}?search=" + this.search +
                        "&start_date=" + this.filter_start_date + "&end_date=" + this.filter_end_date +
                        "&client_name=" + this.filter_client_name + "&booking_code=" + this
                        .filter_booking_code + "&payment_mode=" + this.filter_payment_mode +
                        "&type=" + encodeURIComponent(this.filter_type || 'All') +
                        "&line_type=" + encodeURIComponent(this.filter_line_type || '');

                    console.log(url);

                    window.open(url, '_self');
                },

                exportToExcel: function() {
                    var url = "{{ route('sales-report.export-to-excel') }}?start_date=" + this
                        .filter_start_date + "&end_date=" + this.filter_end_date +
                        "&client_name=" + this.filter_client_name + "&booking_code=" + this
                        .filter_booking_code + "&payment_mode=" + this.filter_payment_mode +
                        "&type=" + encodeURIComponent(this.filter_type || 'All') +
                        "&line_type=" + encodeURIComponent(this.filter_line_type || '');

                    console.log(url);

                    window.open(url, '_self');
                },

                printSummary: function() {
                    if (!this.summaryData) {
                        return;
                    }

                    var type = String(this.filter_type || 'All').toLowerCase();
                    var heroLabel = 'Net Sales';
                    if (type === 'refund') {
                        heroLabel = 'Refunds';
                    } else if (type === 'sale' || type === 'sales') {
                        heroLabel = 'Sales';
                    }

                    var startDate = this.dt_format(this.summaryData.start_date);
                    var endDate = this.dt_format(this.summaryData.end_date);
                    var rows = [
                        { label: '{{ __('Cash Sales') }}', value: this.summaryData.cash_sales || '0.00' },
                        { label: '{{ __('Online Sales') }}', value: this.summaryData.online_sales || '0.00' },
                        { label: '{{ __('Card Sales') }}', value: this.summaryData.card_sales || '0.00' },
                        { label: '{{ __('Credit Sales') }}', value: this.summaryData.credit_sales || '0.00' }
                    ];

                    var rowsHtml = rows.map(function (row) {
                        return '<tr><td>' + row.label + '</td><td style="text-align:right;font-weight:600;">₹ ' + row.value + '</td></tr>';
                    }).join('');

                    var printRoot = document.getElementById('sales-summary-print-root');
                    if (!printRoot) {
                        return;
                    }

                    printRoot.innerHTML = ''
                        + '<h1>{{ __('Sales Summary') }}</h1>'
                        + '<div class="date">{{ __('Date Range') }}: ' + startDate + ' {{ __('To') }} ' + endDate + '</div>'
                        + '<div class="hero"><div class="label">' + heroLabel + '</div><div class="value">₹ '
                        + (this.summaryData.total_sales || '0.00') + '</div></div>'
                        + '<table><tbody>' + rowsHtml + '</tbody></table>';

                    window.print();

                    setTimeout(function () {
                        printRoot.innerHTML = '';
                    }, 300);
                }


            },
            mounted() {
                var params = new URLSearchParams(window.location.search);
                var start = params.get('start_date');
                var end = params.get('end_date');
                var iso = /^\d{4}-\d{2}-\d{2}$/;
                if (start && end && iso.test(start) && iso.test(end)) {
                    this.filter_start_date = start;
                    this.filter_end_date = end;
                } else {
                    this.applyDefaultDates();
                }
                this.getSalesList(1);
            }
        });
    </script>
@endsection
{{-- Vue Js Script --}}

<script src="{{ asset('js/vue.min.js') }}"></script>
<script src="{{ asset('assets/vuejs/axios/axios.min.js') }}"></script>
<script src="{{ asset('assets/vuejs/ajax/libs/moment.min.js') }}"></script>

{{-- multiselect --}}
<link href="{{ asset('assets/vuejs/multi_select/vue-multiselect.min.css') }}" rel="stylesheet">
<script src="{{ asset('assets/vuejs/multi_select/vue-multiselect.min.js') }}"></script>

{{-- toast-text-data --}}
<link href="{{ asset('assets/vuejs/toast/vue_toast.css') }}" rel="stylesheet">
<script src="{{ asset('assets/vuejs/toast/vue_toast.js') }}"></script>

<!-- Init the plugin -->
<script>
    Vue.use(VueToast, {
        position: "top-right",
        duration: 5000,
        dismissible: true
    })
</script>

{{-- validation --}}
<script src="{{ asset('assets/vuejs/vee_validate/veeValidate.js') }}"></script>
<script>
    Vue.use(VeeValidate);
</script>

<script>
    Vue.component("vue-multiselect", window.VueMultiselect.default);
</script>


{{-- Infinite scroll --}}
<script src="{{ asset('assets/vuejs/infinite_scroll/vue-infinite-scroll.js') }}"></script>
{{-- modal --}}
<script src="{{ asset('assets/vuejs/dist/vue-js-modal.js?11') }}"></script>
