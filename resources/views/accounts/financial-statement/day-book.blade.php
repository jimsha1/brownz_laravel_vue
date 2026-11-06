@extends('layouts.vcapp')

<style>
    .day-book-filters .filters-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
    }

    .day-book-filters .filters-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .day-book-filters .filters-hint {
        font-size: 12px;
        color: #6c757d;
    }

    .day-book-filters label {
        font-size: 13px;
        color: #1D1D1D;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .day-book-filters .search-box-bar-container {
        display: flex;
        align-items: center;
        background-color: #FFFFFF;
        box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;
        border-radius: 26px;
        width: 100%;
        padding-left: 16px;
        min-height: 44px;
    }

    .day-book-filters .brawnz-search-bar-in {
        background-color: transparent;
        border: none;
        box-shadow: none;
        outline: none;
        padding: 10px 16px 10px 8px;
        font-size: 14px;
        width: 100%;
        height: 44px;
    }

    .day-book-filters .filter-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
        margin-top: 8px;
    }

    .day-book-filters .active-filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        padding: 6px 12px;
        border-radius: 999px;
        background: #F0F0F3;
        color: #1D1D1D;
        font-size: 12px;
        font-weight: 600;
    }

    .day-book-filters .select2-container {
        width: 100% !important;
    }

    .day-book-filters .select2-container .select2-selection--single {
        height: 44px;
        border: none;
        background: transparent;
        display: flex;
        align-items: center;
    }

    .day-book-filters .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 44px;
        padding-left: 8px;
        color: #1D1D1D;
    }

    .day-book-filters .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px;
        right: 10px;
    }

    .day-book-filters .select2-container--default .select2-selection--single .select2-selection__clear {
        margin-right: 22px;
    }

    .day-book-list .list-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
    }

    .day-book-list .list-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .day-book-list .list-meta {
        font-size: 12px;
        color: #6c757d;
    }

    .day-book-list .summary-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .day-book-list .export-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .day-book-list .toolbar-right {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        justify-content: flex-end;
    }

    .day-book-list .summary-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 8px 14px;
        background: #F0F0F3;
        font-size: 12px;
        font-weight: 600;
        color: #1D1D1D;
    }

    .day-book-list .summary-pill .value {
        font-variant-numeric: tabular-nums;
    }

    .day-book-list .summary-pill.is-debit .value {
        color: #199900;
    }

    .day-book-list .summary-pill.is-credit .value {
        color: #D0342C;
    }

    .day-book-list .day-book-table th,
    .day-book-list .day-book-table td {
        vertical-align: middle;
    }

    .day-book-list .day-book-table .amount-cell {
        text-align: right;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .day-book-list .voucher-link {
        color: #52A8D9;
        font-weight: 600;
        text-decoration: none;
    }

    .day-book-list .account-name {
        display: block;
        font-weight: 600;
        color: #1D1D1D;
    }

    .day-book-list .particular-text {
        color: #4a4a4a;
        max-width: 320px;
        white-space: normal;
    }

    .day-book-list .amount-pill {
        display: inline-flex;
        min-width: 88px;
        justify-content: flex-end;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
    }

    .day-book-list .amount-pill.is-debit {
        color: #199900;
        background: rgba(25, 153, 0, 0.12);
    }

    .day-book-list .amount-pill.is-credit {
        color: #D0342C;
        background: rgba(208, 52, 44, 0.12);
    }

    .day-book-list .amount-pill.is-zero {
        color: #6c757d;
        background: #F0F0F3;
    }

    .day-book-list .day-book-table tfoot th {
        background: #F7F7F9;
        border-top: 1px solid #e5e5e5;
        font-weight: 700;
    }

    .day-book-list .empty-state,
    .day-book-list .loading-state {
        text-align: center;
        padding: 40px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }

    .day-book-list .empty-state i,
    .day-book-list .loading-state i {
        font-size: 28px;
        margin-bottom: 10px;
        color: #b7b7b7;
    }

    .day-book-list .tab-pagination {
        margin-top: 16px;
    }
</style>

@section('content')
    <div id="app">
        <div class="fbox-shadow mt-2 fd-card respo-margin-top day-book-filters">
            <div class="card-body">
                <div class="filters-header">
                    <div>
                        <h5 class="filters-title">{{ __('Day Book') }}</h5>
                        <div class="filters-hint">{{ __('Filter daily ledger entries by date, account, voucher or particular') }}</div>
                    </div>
                </div>

                <div class="row align-items-end">
                    @include('accounts.financial-statement.partials.financial-year-filter', ['columnClass' => 'col-lg-2 col-md-4 col-sm-6 mb-3'])

                    <div class="form-group col-lg-2 col-md-4 col-sm-6 mb-3">
                        <label for="filter_start_date">{{ __('Start Date') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <input type="date"
                                id="filter_start_date"
                                class="form-control brawnz-search-bar-in"
                                v-model="filter_start_date"
                                @input="handleChangeStartDate">
                        </div>
                    </div>

                    <div class="form-group col-lg-2 col-md-4 col-sm-6 mb-3">
                        <label for="filter_end_date">{{ __('End Date') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <input type="date"
                                id="filter_end_date"
                                class="form-control brawnz-search-bar-in"
                                v-model="filter_end_date">
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6 mb-3">
                        <label for="chart_of_account_id">{{ __('Account') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b pr-2">
                            <select class="form-control"
                                id="chart_of_account_id"
                                name="chart_of_account_id"
                                data-placeholder="{{ __('All Accounts') }}">
                                <option value=""></option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->account_id }}">
                                        {{ $account->code }} — {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6 mb-3">
                        <label for="day_book_search">{{ __('Search') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <p class="mb-0"><i class="fa fa-search" aria-hidden="true"></i></p>
                            <input type="text"
                                id="day_book_search"
                                class="form-control brawnz-search-bar-in"
                                v-model="search"
                                placeholder="{{ __('Voucher #, particular, account...') }}"
                                @keyup.enter="getDayBook(1)">
                        </div>
                    </div>

                    <div class="form-group col-12 mb-0">
                        <div class="filter-actions">
                            <button type="button"
                                class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                                @click="resetFilter()"
                                :disabled="loading">
                                <i class="fa fa-refresh"></i> {{ __('Reset') }}
                            </button>
                            <button type="button"
                                class="btn btn-sm btn-primary btn_dark-x"
                                @click="getDayBook(1)"
                                :disabled="loading">
                                <i class="fa fa-search"></i> {{ __('Search') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="selectedAccountLabel" class="active-filter-chip">
                    <i class="fa fa-filter" aria-hidden="true"></i>
                    <span>{{ __('Account') }}: @{{ selectedAccountLabel }}</span>
                </div>
            </div>
        </div>

        <div class="fbox-shadow mt-3 fd-card respo-margin-top day-book-list">
            <div class="m-3">
                <div class="list-toolbar">
                    <div>
                        <p class="list-title">{{ __('Results') }}</p>
                        <div class="list-meta" v-if="!loading && pagination.total">
                            {{ __('Showing') }}
                            @{{ pagination.from || 0 }}–@{{ pagination.to || 0 }}
                            {{ __('of') }}
                            @{{ pagination.total }}
                        </div>
                        <div class="list-meta" v-else-if="!loading">
                            {{ __('No matching entries for the selected filters') }}
                        </div>
                    </div>
                    <div class="toolbar-right">
                        <div class="export-actions" v-if="!loading && dayBookList.length">
                            <button type="button"
                                class="btn btn-sm brownz-btn-yellow-d"
                                @click="printReport()"
                                :disabled="loading">
                                <i class="fa fa-print"></i> {{ __('Print') }}
                            </button>
                            <button type="button"
                                class="btn btn-sm btn-outline-secondary"
                                @click="exportToPDF()"
                                :disabled="loading">
                                <i class="fa fa-file-pdf-o"></i> {{ __('PDF') }}
                            </button>
                            <button type="button"
                                class="btn btn-sm btn_dark-x"
                                @click="exportToExcel()"
                                :disabled="loading">
                                <i class="fa fa-file-excel-o"></i> {{ __('Excel') }}
                            </button>
                        </div>
                        <div class="summary-pills" v-if="!loading && dayBookList.length">
                        <span class="summary-pill is-debit">
                            <span>{{ __('Total Debit') }}</span>
                            <span class="value">@{{ formatAmount(totalDebit) }}</span>
                        </span>
                        <span class="summary-pill is-credit">
                            <span>{{ __('Total Credit') }}</span>
                            <span class="value">@{{ formatAmount(totalCredit) }}</span>
                        </span>
                        </div>
                    </div>
                </div>

                <div v-if="loading" class="loading-state">
                    <div><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>
                    <div>{{ __('Loading day book...') }}</div>
                </div>

                <div v-else-if="!dayBookList.length" class="empty-state">
                    <div><i class="fa fa-book" aria-hidden="true"></i></div>
                    <div><strong>{{ __('No transactions found') }}</strong></div>
                    <div>{{ __('Try adjusting the date range, account, or search.') }}</div>
                </div>

                <template v-else>
                    <div class="table-responsive">
                        <table class="table brownz-table-list-v1 day-book-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 56px;">#</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Voucher#') }}</th>
                                    <th>{{ __('Account') }}</th>
                                    <th class="amount-cell">{{ __('Debit') }}</th>
                                    <th class="amount-cell">{{ __('Credit') }}</th>
                                    <th>{{ __('Particular') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in dayBookList" :key="item.id || index">
                                    <td>@{{ rowNumber(index) }}</td>
                                    <td>@{{ dt_format(item.date) }}</td>
                                    <td>
                                        <span class="voucher-link">
                                            @{{ voucherNo(item) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="account-name">
                                            @{{ accountName(item) }}
                                        </span>
                                    </td>
                                    <td class="amount-cell">
                                        <span class="amount-pill"
                                            :class="Number(item.debit || 0) > 0 ? 'is-debit' : 'is-zero'">
                                            @{{ formatAmount(item.debit) }}
                                        </span>
                                    </td>
                                    <td class="amount-cell">
                                        <span class="amount-pill"
                                            :class="Number(item.credit || 0) > 0 ? 'is-credit' : 'is-zero'">
                                            @{{ formatAmount(item.credit) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="particular-text">
                                            @{{ particularText(item) }}
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">{{ __('Total') }}</th>
                                    <th class="amount-cell">@{{ formatAmount(totalDebit) }}</th>
                                    <th class="amount-cell">@{{ formatAmount(totalCredit) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <nav class="tab-pagination" aria-label="pagination" v-if="pagination.last_page > 1">
                        <ul class="pagination justify-content-end mb-0">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#" aria-label="Previous"
                                    @click.prevent="changePage(pagination.current_page - 1)">
                                    <span aria-hidden="true">«</span>
                                </a>
                            </li>
                            <li class="page-item"
                                v-for="page in pagesNumber"
                                :key="'page-' + page"
                                :class="{ active: page === isActived }">
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
                </template>
            </div>
        </div>
    </div>

    <script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
    <script>
        var vc_moment_date_offset = new Date("2021-10-05T17:28:50.403+03:00").getTime() - Date.now();
        var vc_jscalendar_moment_format = "DD/MM/YYYY";
    </script>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vuejs/axios/axios.min.js') }}"></script>
    <script src="{{ asset('assets/vuejs/dist/vue-js-modal.js?11') }}"></script>
@endpush

@push('inline-scripts')
    <script>
        if (window['vue-js-modal'] && window['vue-js-modal'].default) {
            Vue.use(window['vue-js-modal'].default);
        }

        var app = new Vue({
            el: '#app',
            data: {
                loading: false,
                base_url: "{{ '/' }}",
                dayBookList: [],
                search: '',
                page_length: '500',
                pagination: {
                    total: 0,
                    per_page: 2,
                    from: 1,
                    to: 0,
                    current_page: 1,
                    last_page: 1
                },
                offset: 4,
                formErrors: {},
                @include('accounts.financial-statement.partials.financial-year-picker-data')
                filter_end_date: '',
                filter_start_date: '',
                chart_of_account_id: '',
                selectedAccountLabel: '',
                totalDebit: 0,
                totalCredit: 0,
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
                },
            },
            methods: {
                handleChangeStartDate() {
                    if (!this.filter_start_date) {
                        return;
                    }
                    var end = new Date(this.filter_start_date);
                    var month = end.getMonth() + 1;
                    const months = [1, 3, 5, 7, 8, 10, 12];

                    if (months.includes(month)) {
                        end.setDate(end.getDate() + 30);
                    } else if (month == 2) {
                        if (end.getFullYear() % 4 == 0) {
                            end.setDate(end.getDate() + 28);
                        } else {
                            end.setDate(end.getDate() + 27);
                        }
                    } else {
                        end.setDate(end.getDate() + 29);
                    }

                    this.filter_end_date = end.toISOString().substr(0, 10);
                },
                dt_format: function(val) {
                    if (!val) return '—';
                    return moment(val).format('DD-MM-YYYY');
                },
                formatAmount(amount) {
                    return parseFloat(amount || 0).toFixed(2);
                },
                voucherNo(item) {
                    if (!item || !item.transaction_group || !item.transaction_group.voucher_no) {
                        return '—';
                    }
                    return item.transaction_group.voucher_no;
                },
                accountName(item) {
                    if (!item || !item.journal || !item.journal.account || !item.journal.account.name) {
                        return '—';
                    }
                    return item.journal.account.name;
                },
                particularText(item) {
                    if (!item || !item.transaction_group || !item.transaction_group.description) {
                        return '—';
                    }
                    return item.transaction_group.description;
                },
                rowNumber(index) {
                    var perPage = Number(this.pagination.per_page || this.page_length || 0);
                    var current = Number(this.pagination.current_page || 1);
                    return ((current - 1) * perPage) + index + 1;
                },
                changePage: function(page) {
                    this.pagination.current_page = page;
                    this.getDayBook(page);
                },
                syncAccountFilter: function() {
                    var $select = $('#chart_of_account_id');
                    this.chart_of_account_id = $select.val() || '';
                    var selectedText = $select.find('option:selected').text().trim();
                    this.selectedAccountLabel = this.chart_of_account_id ? selectedText : '';
                },
                getDayBook: function(page = 1) {
                    const url = "{{ route('day-book-vue-list') }}";
                    this.syncAccountFilter();
                    this.loading = true;
                    axios.post(url, {
                            page: page,
                            page_length: this.page_length,
                            search: this.search,
                            start_date: this.filter_start_date,
                            end_date: this.filter_end_date,
                            chart_of_account_id: this.chart_of_account_id,
                        })
                        .then(response => {
                            this.dayBookList = response.data.data || [];
                            this.pagination = response.data.pagination || this.pagination;
                            this.totalDebit = parseFloat(String(response.data.total_debit || '0').replace(/,/g, ''));
                            this.totalCredit = parseFloat(String(response.data.total_credit || '0').replace(/,/g, ''));
                        })
                        .catch(error => {
                            console.error("Error fetching day book data:", error);
                            this.dayBookList = [];
                            this.totalDebit = 0;
                            this.totalCredit = 0;
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },
                applyDefaultDates: function() {
                    this.applyFinancialYearDates();
                },
                onFinancialYearChange: function() {
                    this.applyFinancialYearDates();
                    this.getDayBook(1);
                },
                @include('accounts.financial-statement.partials.financial-year-picker-methods')
                resetFilter: function() {
                    this.resetFinancialYearSelection();
                    this.search = '';
                    this.chart_of_account_id = '';
                    this.selectedAccountLabel = '';
                    var $select = $('#chart_of_account_id');
                    if ($select.data('select2')) {
                        $select.val(null).trigger('change');
                    } else {
                        $select.val('');
                    }
                    this.getDayBook(1);
                },
                exportQuery: function() {
                    this.syncAccountFilter();
                    return '?start_date=' + encodeURIComponent(this.filter_start_date || '')
                        + '&end_date=' + encodeURIComponent(this.filter_end_date || '')
                        + '&search=' + encodeURIComponent(this.search || '')
                        + '&chart_of_account_id=' + encodeURIComponent(this.chart_of_account_id || '');
                },
                printReport: function() {
                    var printWindow = window.open(
                        "{{ route('day-book.print') }}" + this.exportQuery(),
                        '_blank',
                        'noopener,noreferrer'
                    );

                    if (!printWindow) {
                        alert('{{ __('Please allow popups to print the day book.') }}');
                    }
                },
                exportToPDF: function() {
                    window.open("{{ route('day-book.export-to-pdf') }}" + this.exportQuery(), '_self');
                },
                exportToExcel: function() {
                    window.open("{{ route('day-book.export-to-excel') }}" + this.exportQuery(), '_self');
                },
                formatDateLocal(date) {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, "0");
                    const day = String(date.getDate()).padStart(2, "0");
                    return `${year}-${month}-${day}`;
                }
            },
            mounted() {
                this.applyFinancialYearDates();

                var self = this;
                jQuery(function($) {
                    var $select = $('#chart_of_account_id');
                    window.vocoInitSelect2($select, {
                        placeholder: '{{ __('All Accounts') }}',
                        allowClear: true,
                        width: '100%'
                    }).on('change', function() {
                        self.syncAccountFilter();
                    });

                    self.getDayBook();
                });
            }
        });
    </script>
@endpush
