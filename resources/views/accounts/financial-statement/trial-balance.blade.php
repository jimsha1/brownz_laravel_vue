@extends('layouts.vcapp')

<style>
    .trial-balance-filters .filters-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
    }

    .trial-balance-filters .filters-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .trial-balance-filters .filters-hint {
        font-size: 12px;
        color: #6c757d;
    }

    .trial-balance-filters label {
        font-size: 13px;
        color: #1D1D1D;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .trial-balance-filters .search-box-bar-container {
        display: flex;
        align-items: center;
        background-color: #FFFFFF;
        box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;
        border-radius: 26px;
        width: 100%;
        padding-left: 16px;
        min-height: 44px;
    }

    .trial-balance-filters .brawnz-search-bar-in {
        background-color: transparent;
        border: none;
        box-shadow: none;
        outline: none;
        padding: 10px 16px 10px 8px;
        font-size: 14px;
        width: 100%;
        height: 44px;
    }

    .trial-balance-filters .filter-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
        margin-top: 8px;
    }

    .trial-balance-list .list-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 14px;
    }

    .trial-balance-list .list-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .trial-balance-list .list-meta {
        font-size: 12px;
        color: #6c757d;
    }

    .trial-balance-list .toolbar-right {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        justify-content: flex-end;
    }

    .trial-balance-list .export-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .trial-balance-list .status-pill,
    .trial-balance-list .summary-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.2;
    }

    .trial-balance-list .status-pill.is-balanced {
        color: #199900;
        background: rgba(25, 153, 0, 0.15);
    }

    .trial-balance-list .status-pill.is-unbalanced {
        color: #D0342C;
        background: rgba(208, 52, 44, 0.12);
    }

    .trial-balance-list .summary-pill {
        background: #F0F0F3;
        color: #1D1D1D;
    }

    .trial-balance-list .summary-pill .value {
        font-variant-numeric: tabular-nums;
    }

    .trial-balance-list .summary-pill.is-debit .value {
        color: #199900;
    }

    .trial-balance-list .summary-pill.is-credit .value {
        color: #D0342C;
    }

    .trial-balance-list .tb-table th,
    .trial-balance-list .tb-table td {
        vertical-align: middle;
    }

    .trial-balance-list .tb-table .amount-cell {
        text-align: right;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .trial-balance-list .account-name {
        font-weight: 600;
        color: #1D1D1D;
    }

    .trial-balance-list .amount-pill {
        display: inline-flex;
        min-width: 96px;
        justify-content: flex-end;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
    }

    .trial-balance-list .amount-pill.is-debit {
        color: #199900;
        background: rgba(25, 153, 0, 0.12);
    }

    .trial-balance-list .amount-pill.is-credit {
        color: #D0342C;
        background: rgba(208, 52, 44, 0.12);
    }

    .trial-balance-list .amount-pill.is-zero {
        color: #6c757d;
        background: #F0F0F3;
    }

    .trial-balance-list .tb-table tfoot th {
        background: #F7F7F9;
        border-top: 1px solid #e5e5e5;
        font-weight: 700;
    }

    .trial-balance-list .empty-state,
    .trial-balance-list .loading-state {
        text-align: center;
        padding: 40px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }

    .trial-balance-list .empty-state i,
    .trial-balance-list .loading-state i {
        font-size: 28px;
        margin-bottom: 10px;
        color: #b7b7b7;
    }

    .trial-balance-list .balance-banner {
        margin-top: 14px;
        border-radius: 14px;
        padding: 12px 16px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .trial-balance-list .balance-banner.is-balanced {
        background: rgba(25, 153, 0, 0.12);
        color: #199900;
    }

    .trial-balance-list .balance-banner.is-unbalanced {
        background: rgba(208, 52, 44, 0.12);
        color: #D0342C;
    }
</style>

@section('content')
    <div id="app">
        <div class="fbox-shadow mt-2 fd-card respo-margin-top trial-balance-filters">
            <div class="card-body">
                <div class="filters-header">
                    <div>
                        <h5 class="filters-title">{{ __('Trial Balance') }}</h5>
                        <div class="filters-hint">{{ __('Review account debit and credit balances for the selected period') }}</div>
                    </div>
                </div>

                <div class="row align-items-end">
                    @include('accounts.financial-statement.partials.financial-year-filter')

                    <div class="form-group col-lg-2 col-md-4 col-sm-6 mb-3">
                        <label for="filter_start_date">{{ __('Start Date') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <input type="date"
                                id="filter_start_date"
                                class="form-control brawnz-search-bar-in"
                                v-model="filter_start_date">
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
                        <label for="trial_balance_search">{{ __('Search') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <p class="mb-0"><i class="fa fa-search" aria-hidden="true"></i></p>
                            <input type="text"
                                id="trial_balance_search"
                                class="form-control brawnz-search-bar-in"
                                v-model="search"
                                placeholder="{{ __('Account name...') }}"
                                @keyup.enter="getTrialBalance(1)">
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-md-12 mb-3">
                        <label class="d-block">&nbsp;</label>
                        <div class="filter-actions">
                            <button type="button"
                                class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                                @click="resetFilter()"
                                :disabled="loading">
                                <i class="fa fa-refresh"></i> {{ __('Reset') }}
                            </button>
                            <button type="button"
                                class="btn btn-sm btn-primary btn_dark-x"
                                @click="getTrialBalance(1)"
                                :disabled="loading">
                                <i class="fa fa-search"></i> {{ __('Search') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fbox-shadow mt-3 fd-card respo-margin-top trial-balance-list">
            <div class="m-3">
                <div class="list-toolbar">
                    <div>
                        <p class="list-title">{{ __('Results') }}</p>
                        <div class="list-meta" v-if="filter_start_date && filter_end_date">
                            {{ __('Period') }}:
                            @{{ formatDisplayDate(filter_start_date) }}
                            –
                            @{{ formatDisplayDate(filter_end_date) }}
                            <span v-if="!loading && pagination.total">
                                · {{ __('Showing') }}
                                @{{ pagination.from || 0 }}–@{{ pagination.to || 0 }}
                                {{ __('of') }}
                                @{{ pagination.total }}
                            </span>
                        </div>
                    </div>
                    <div class="toolbar-right">
                        <div class="export-actions" v-if="!loading && trialBalanceList.length">
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
                        <span class="status-pill" v-if="!loading && trialBalanceList.length" :class="isBalanced ? 'is-balanced' : 'is-unbalanced'">
                            <i class="fa" :class="isBalanced ? 'fa-check-circle' : 'fa-exclamation-circle'" aria-hidden="true"></i>
                            <span v-if="isBalanced">{{ __('Balanced') }}</span>
                            <span v-else>{{ __('Difference') }} · @{{ formatAmount(balanceDifference) }}</span>
                        </span>
                        <span class="summary-pill is-debit" v-if="!loading && trialBalanceList.length">
                            <span>{{ __('Debit') }}</span>
                            <span class="value">@{{ formatAmount(totalDebit) }}</span>
                        </span>
                        <span class="summary-pill is-credit" v-if="!loading && trialBalanceList.length">
                            <span>{{ __('Credit') }}</span>
                            <span class="value">@{{ formatAmount(totalCredit) }}</span>
                        </span>
                    </div>
                </div>

                <div v-if="loading" class="loading-state">
                    <div><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>
                    <div>{{ __('Loading trial balance...') }}</div>
                </div>

                <div v-else-if="!trialBalanceList.length" class="empty-state">
                    <div><i class="fa fa-balance-scale" aria-hidden="true"></i></div>
                    <div><strong>{{ __('No balances found') }}</strong></div>
                    <div>{{ __('Try adjusting the date range or account search.') }}</div>
                </div>

                <template v-else>
                    <div class="table-responsive">
                        <table class="table brownz-table-list-v1 tb-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 56px;">{{ __('SL#') }}</th>
                                    <th>{{ __('Account Name') }}</th>
                                    <th class="amount-cell">{{ __('Debit') }}</th>
                                    <th class="amount-cell">{{ __('Credit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, index) in trialBalanceList" :key="data.account_id || index">
                                    <td>@{{ rowNumber(index) }}</td>
                                    <td>
                                        <span class="account-name">@{{ data.name || '—' }}</span>
                                    </td>
                                    <td class="amount-cell">
                                        <span class="amount-pill"
                                            :class="parseAmount(data.debit) > 0 ? 'is-debit' : 'is-zero'">
                                            @{{ formatAmount(data.debit) }}
                                        </span>
                                    </td>
                                    <td class="amount-cell">
                                        <span class="amount-pill"
                                            :class="parseAmount(data.credit) > 0 ? 'is-credit' : 'is-zero'">
                                            @{{ formatAmount(data.credit) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th class="text-right">{{ __('Total') }}</th>
                                    <th class="amount-cell">@{{ formatAmount(totalDebit) }}</th>
                                    <th class="amount-cell">@{{ formatAmount(totalCredit) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="balance-banner" :class="isBalanced ? 'is-balanced' : 'is-unbalanced'">
                        <span v-if="isBalanced">
                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                            {{ __('Trial Balance is balanced.') }}
                        </span>
                        <span v-else>
                            <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                            {{ __('Trial Balance is not balanced.') }}
                        </span>
                        <span v-if="!isBalanced">
                            {{ __('Difference') }}: @{{ formatAmount(balanceDifference) }}
                        </span>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
    <script>
        var vc_moment_date_offset = new Date("2021-10-05T17:28:50.403+03:00").getTime() - Date.now();
        var vc_jscalendar_moment_format = "DD/MM/YYYY";
    </script>
    <script>
        Vue.use(window["vue-js-modal"].default);
        var app = new Vue({
            el: '#app',
            data: {
                loading: false,
                base_url: "{{ '/' }}",
                trialBalanceList: [],
                formSubmitted: false,
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
                totalDebit: 0,
                totalCredit: 0,
            },
            computed: {
                debitValue() {
                    return this.parseAmount(this.totalDebit);
                },
                creditValue() {
                    return this.parseAmount(this.totalCredit);
                },
                isBalanced() {
                    return Math.abs(this.debitValue - this.creditValue) < 0.01;
                },
                balanceDifference() {
                    return Math.abs(this.debitValue - this.creditValue);
                },
            },
            methods: {
                parseAmount(value) {
                    if (typeof value === 'number') {
                        return value;
                    }
                    return parseFloat(String(value || '0').replace(/,/g, '')) || 0;
                },
                formatAmount(value) {
                    return this.parseAmount(value).toFixed(2);
                },
                formatDisplayDate(val) {
                    if (!val) {
                        return '—';
                    }
                    return moment(val).format('DD-MM-YYYY');
                },
                rowNumber(index) {
                    var perPage = Number(this.pagination.per_page || this.page_length || 0);
                    var current = Number(this.pagination.current_page || 1);
                    return ((current - 1) * perPage) + index + 1;
                },
                changePage: function(page) {
                    this.pagination.current_page = page;
                    this.getTrialBalance(page);
                },
                getTrialBalance: function(page = 1) {
                    const url = "{{ route('trialbalance-vue-list') }}";
                    this.loading = true;

                    axios.post(url, {
                            page: page,
                            page_length: this.page_length,
                            search: this.search,
                            start_date: this.filter_start_date,
                            end_date: this.filter_end_date,
                        })
                        .then(response => {
                            this.trialBalanceList = response.data.data || [];
                            this.pagination = response.data.pagination || this.pagination;
                            this.totalDebit = response.data.total_debit || 0;
                            this.totalCredit = response.data.total_credit || 0;
                        })
                        .catch(error => {
                            console.error('Error fetching trial balance:', error);
                            this.trialBalanceList = [];
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
                    this.getTrialBalance(1);
                },
                @include('accounts.financial-statement.partials.financial-year-picker-methods')
                resetFilter: function() {
                    this.search = '';
                    this.resetFinancialYearSelection();
                    this.getTrialBalance(1);
                },
                exportQuery: function() {
                    return '?start_date=' + encodeURIComponent(this.filter_start_date || '')
                        + '&end_date=' + encodeURIComponent(this.filter_end_date || '')
                        + '&search=' + encodeURIComponent(this.search || '');
                },
                printReport: function() {
                    var printWindow = window.open(
                        "{{ route('trial-balance.print') }}" + this.exportQuery(),
                        '_blank',
                        'noopener,noreferrer'
                    );

                    if (!printWindow) {
                        alert('{{ __('Please allow popups to print the trial balance.') }}');
                    }
                },
                exportToPDF: function() {
                    window.open("{{ route('trial-balance.export-to-pdf') }}" + this.exportQuery(), '_self');
                },
                exportToExcel: function() {
                    window.open("{{ route('trial-balance.export-to-excel') }}" + this.exportQuery(), '_self');
                },
                formatDateLocal: function(date) {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, "0");
                    const day = String(date.getDate()).padStart(2, "0");
                    return `${year}-${month}-${day}`;
                },
            },
            mounted() {
                this.applyFinancialYearDates();
                this.getTrialBalance();
            }
        });
    </script>
@endsection

@include('layouts.vue_script')
