@extends('layouts.vcapp')

<style>
    .balance-sheet-filters .filters-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
    }

    .balance-sheet-filters .filters-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .balance-sheet-filters .filters-hint {
        font-size: 12px;
        color: #6c757d;
    }

    .balance-sheet-filters label {
        font-size: 13px;
        color: #1D1D1D;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .balance-sheet-filters .search-box-bar-container {
        display: flex;
        align-items: center;
        background-color: #FFFFFF;
        box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;
        border-radius: 26px;
        width: 100%;
        padding-left: 16px;
        min-height: 44px;
    }

    .balance-sheet-filters .brawnz-search-bar-in {
        background-color: transparent;
        border: none;
        box-shadow: none;
        outline: none;
        padding: 10px 16px 10px 8px;
        font-size: 14px;
        width: 100%;
        height: 44px;
    }

    .balance-sheet-filters .filter-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
        margin-top: 8px;
    }

    .balance-sheet-list .list-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 14px;
    }

    .balance-sheet-list .list-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .balance-sheet-list .list-meta {
        font-size: 12px;
        color: #6c757d;
    }

    .balance-sheet-list .toolbar-right {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        justify-content: flex-end;
    }

    .balance-sheet-list .export-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .balance-sheet-list .status-pill,
    .balance-sheet-list .summary-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.2;
    }

    .balance-sheet-list .status-pill.is-balanced {
        color: #199900;
        background: rgba(25, 153, 0, 0.15);
    }

    .balance-sheet-list .status-pill.is-unbalanced {
        color: #D0342C;
        background: rgba(208, 52, 44, 0.12);
    }

    .balance-sheet-list .summary-pill {
        background: #F0F0F3;
        color: #1D1D1D;
    }

    .balance-sheet-list .summary-pill .value {
        font-variant-numeric: tabular-nums;
    }

    .balance-sheet-list .statement-row {
        display: flex;
        flex-wrap: wrap;
    }

    .balance-sheet-list .statement-col {
        display: flex;
    }

    .balance-sheet-list .statement-panel {
        background: #F7F7F9;
        border-radius: 16px;
        padding: 14px;
        width: 100%;
        display: flex;
        flex-direction: column;
        min-height: 100%;
    }

    .balance-sheet-list .panel-body {
        flex: 1 1 auto;
    }

    .balance-sheet-list .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        padding: 0 4px;
    }

    .balance-sheet-list .panel-title {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #1D1D1D;
    }

    .balance-sheet-list .panel-total {
        font-size: 13px;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        color: #1D1D1D;
    }

    .balance-sheet-list .section-label {
        margin: 10px 0 6px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #FFFFFF;
        color: #6c757d;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        display: inline-flex;
    }

    .balance-sheet-list .stmt-table {
        width: 100%;
        margin-bottom: 0;
        background: #FFFFFF;
        border-radius: 12px;
        overflow: hidden;
    }

    .balance-sheet-list .stmt-table th,
    .balance-sheet-list .stmt-table td {
        vertical-align: middle;
        border-top: none;
        border-bottom: 1px solid #ececef;
        padding: 10px 12px;
    }

    .balance-sheet-list .stmt-table tr:last-child td {
        border-bottom: none;
    }

    .balance-sheet-list .account-name {
        color: #1D1D1D;
        font-weight: 500;
    }

    .balance-sheet-list .amount-cell {
        text-align: right;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
        font-weight: 600;
        color: #1D1D1D;
    }

    .balance-sheet-list .panel-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
        padding: 12px 14px;
        min-height: 48px;
        border-radius: 12px;
        background: #1D1D1D;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        flex: 0 0 auto;
    }

    .balance-sheet-list .panel-footer .value {
        color: #E6CD7F;
        font-variant-numeric: tabular-nums;
        font-size: 14px;
        min-width: 96px;
        text-align: right;
    }

    .balance-sheet-list .empty-state,
    .balance-sheet-list .loading-state {
        text-align: center;
        padding: 40px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }

    .balance-sheet-list .empty-state i,
    .balance-sheet-list .loading-state i {
        font-size: 28px;
        margin-bottom: 10px;
        color: #b7b7b7;
    }

    .balance-sheet-list .balance-banner {
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

    .balance-sheet-list .balance-banner.is-balanced {
        background: rgba(25, 153, 0, 0.12);
        color: #199900;
    }

    .balance-sheet-list .balance-banner.is-unbalanced {
        background: rgba(208, 52, 44, 0.12);
        color: #D0342C;
    }

    @media (max-width: 767.98px) {
        .balance-sheet-list .statement-col {
            display: block;
        }

        .balance-sheet-list .statement-col + .statement-col {
            margin-top: 12px;
        }
    }
</style>

@section('content')
    <div id="app">
        <div class="fbox-shadow mt-2 fd-card respo-margin-top balance-sheet-filters">
            <div class="card-body">
                <div class="filters-header">
                    <div>
                        <h5 class="filters-title">{{ __('Balance Sheet') }}</h5>
                        <div class="filters-hint">{{ __('View assets, liabilities and equity as of the selected period') }}</div>
                    </div>
                </div>

                <div class="row align-items-end">
                    @include('accounts.financial-statement.partials.financial-year-filter')

                    <div class="form-group col-lg-3 col-md-6 mb-3">
                        <label for="filter_start_date">{{ __('Start Date') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <input type="date"
                                id="filter_start_date"
                                class="form-control brawnz-search-bar-in"
                                v-model="filter_start_date"
                                @input="handleChangeStartDate">
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-md-6 mb-3">
                        <label for="filter_end_date">{{ __('End Date') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <input type="date"
                                id="filter_end_date"
                                class="form-control brawnz-search-bar-in"
                                v-model="filter_end_date">
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
                                @click="getBalanceSheet()"
                                :disabled="loading">
                                <i class="fa fa-search"></i> {{ __('Search') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fbox-shadow mt-3 fd-card respo-margin-top balance-sheet-list">
            <div class="m-3">
                <div class="list-toolbar">
                    <div>
                        <p class="list-title">{{ __('Statement') }}</p>
                        <div class="list-meta" v-if="filter_start_date && filter_end_date">
                            {{ __('Period') }}:
                            @{{ formatDisplayDate(filter_start_date) }}
                            –
                            @{{ formatDisplayDate(filter_end_date) }}
                        </div>
                    </div>
                    <div class="toolbar-right">
                        <div class="export-actions" v-if="!loading && hasData">
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
                        <span class="status-pill" v-if="!loading && hasData" :class="balanceCheck ? 'is-balanced' : 'is-unbalanced'">
                            <i class="fa" :class="balanceCheck ? 'fa-check-circle' : 'fa-exclamation-circle'" aria-hidden="true"></i>
                            <span v-if="balanceCheck">{{ __('Balanced') }}</span>
                            <span v-else>{{ __('Not balanced') }} · @{{ formatAmount(balanceDifference) }}</span>
                        </span>
                        <span class="summary-pill" v-if="!loading && hasData">
                            <span>{{ __('Assets') }}</span>
                            <span class="value">@{{ formatAmount(totalAssets) }}</span>
                        </span>
                        <span class="summary-pill" v-if="!loading && hasData">
                            <span>{{ __('Liab. + Equity') }}</span>
                            <span class="value">@{{ formatAmount(totalLiabilitiesAndEquity) }}</span>
                        </span>
                    </div>
                </div>

                <div v-if="loading" class="loading-state">
                    <div><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>
                    <div>{{ __('Loading balance sheet...') }}</div>
                </div>

                <div v-else-if="!hasData" class="empty-state">
                    <div><i class="fa fa-balance-scale" aria-hidden="true"></i></div>
                    <div><strong>{{ __('No balances found') }}</strong></div>
                    <div>{{ __('Try adjusting the date range and search again.') }}</div>
                </div>

                <div v-else>
                    <div class="row statement-row">
                        <div class="col-md-6 statement-col">
                            <div class="statement-panel">
                                <div class="panel-header">
                                    <h6 class="panel-title">{{ __('Assets') }}</h6>
                                    <div class="panel-total">@{{ assets.length }} {{ __('accounts') }}</div>
                                </div>

                                <div class="panel-body">
                                    <table class="table stmt-table mb-0">
                                        <tbody>
                                            <tr v-for="(item, index) in assets" :key="'asset-' + (item.account_id || index)">
                                                <td>
                                                    <span class="account-name">@{{ item.name }}</span>
                                                </td>
                                                <td class="amount-cell">@{{ formatAmount(item.balance) }}</td>
                                            </tr>
                                            <tr v-if="!assets.length">
                                                <td colspan="2" class="text-muted text-center">{{ __('No asset balances') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="panel-footer">
                                    <span>{{ __('Total Assets') }}</span>
                                    <span class="value">@{{ formatAmount(totalAssets) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 statement-col">
                            <div class="statement-panel">
                                <div class="panel-header">
                                    <h6 class="panel-title">{{ __('Liabilities & Equity') }}</h6>
                                    <div class="panel-total">
                                        @{{ (liabilities.length + equity.length) }} {{ __('accounts') }}
                                    </div>
                                </div>

                                <div class="panel-body">
                                    <div class="section-label">{{ __('Liabilities') }}</div>
                                    <table class="table stmt-table mb-0">
                                        <tbody>
                                            <tr v-for="(item, index) in liabilities" :key="'liab-' + (item.account_id || index)">
                                                <td>
                                                    <span class="account-name">@{{ item.name }}</span>
                                                </td>
                                                <td class="amount-cell">@{{ formatAmount(item.balance) }}</td>
                                            </tr>
                                            <tr v-if="!liabilities.length">
                                                <td colspan="2" class="text-muted text-center">{{ __('No liability balances') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="section-label">{{ __('Equity') }}</div>
                                    <table class="table stmt-table mb-0">
                                        <tbody>
                                            <tr v-for="(item, index) in equity" :key="'equity-' + (item.account_id || index)">
                                                <td>
                                                    <span class="account-name">@{{ item.name }}</span>
                                                </td>
                                                <td class="amount-cell">@{{ formatAmount(item.balance) }}</td>
                                            </tr>
                                            <tr v-if="!equity.length">
                                                <td colspan="2" class="text-muted text-center">{{ __('No equity balances') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="panel-footer">
                                    <span>{{ __('Total Liabilities & Equity') }}</span>
                                    <span class="value">@{{ formatAmount(totalLiabilitiesAndEquity) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="balance-banner" :class="balanceCheck ? 'is-balanced' : 'is-unbalanced'">
                        <span v-if="balanceCheck">
                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                            {{ __('Balance Sheet is balanced.') }}
                        </span>
                        <span v-else>
                            <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                            {{ __('Balance Sheet is not balanced.') }}
                        </span>
                        <span v-if="!balanceCheck">
                            {{ __('Difference') }}: @{{ formatAmount(balanceDifference) }}
                        </span>
                    </div>
                </div>
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
                assets: [],
                liabilities: [],
                equity: [],
                totalAssets: 0,
                totalLiabilities: 0,
                totalEquity: 0,
                totalLiabilitiesAndEquity: 0,
                retainedEarnings: 0,
                balanceCheck: false,
                balanceDifference: 0,
                formSubmitted: false,
                search: '',
                page_length: '500',
                pagination: {
                    total: 0,
                    per_page: 2,
                    from: 1,
                    to: 0,
                    current_page: 1
                },
                offset: 4,
                formErrors: {},
                @include('accounts.financial-statement.partials.financial-year-picker-data')
                filter_end_date: '',
                filter_start_date: '',
            },
            computed: {
                hasData() {
                    return (this.assets && this.assets.length)
                        || (this.liabilities && this.liabilities.length)
                        || (this.equity && this.equity.length);
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
                formatDisplayDate(val) {
                    if (!val) return '—';
                    return moment(val).format('DD-MM-YYYY');
                },
                formatAmount(amount) {
                    return parseFloat(amount || 0).toFixed(2);
                },
                getBalanceSheet() {
                    const url = "{{ route('balance-sheet-vue-list') }}";
                    this.loading = true;

                    axios.post(url, {
                            page_length: this.page_length,
                            search: this.search,
                            start_date: this.filter_start_date,
                            end_date: this.filter_end_date,
                        })
                        .then(response => {
                            this.assets = response.data.assets || [];
                            this.liabilities = response.data.liabilities || [];
                            this.equity = response.data.equity || [];
                            this.totalAssets = response.data.total_assets || 0;
                            this.totalLiabilities = response.data.total_liabilities || 0;
                            this.totalEquity = response.data.total_equity || 0;
                            this.totalLiabilitiesAndEquity = response.data.total_liabilities_and_equity || 0;
                            this.retainedEarnings = response.data.retained_earnings || 0;
                            this.balanceCheck = response.data.balance_check || false;
                            this.balanceDifference = response.data.balance_difference || 0;
                        })
                        .catch(error => {
                            console.error("Error fetching balance sheet data:", error);
                            this.assets = [];
                            this.liabilities = [];
                            this.equity = [];
                            this.totalAssets = 0;
                            this.totalLiabilities = 0;
                            this.totalEquity = 0;
                            this.totalLiabilitiesAndEquity = 0;
                            this.balanceCheck = false;
                            this.balanceDifference = 0;
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },
                applyDefaultDates() {
                    this.applyFinancialYearDates();
                },
                onFinancialYearChange() {
                    this.applyFinancialYearDates();
                    this.getBalanceSheet();
                },
                @include('accounts.financial-statement.partials.financial-year-picker-methods')
                resetFilter() {
                    this.resetFinancialYearSelection();
                    this.getBalanceSheet();
                },
                exportQuery() {
                    return '?start_date=' + encodeURIComponent(this.filter_start_date || '')
                        + '&end_date=' + encodeURIComponent(this.filter_end_date || '');
                },
                printReport() {
                    var printWindow = window.open(
                        "{{ route('balance-sheet.print') }}" + this.exportQuery(),
                        '_blank',
                        'noopener,noreferrer'
                    );

                    if (!printWindow) {
                        alert('{{ __('Please allow popups to print the balance sheet.') }}');
                    }
                },
                exportToPDF() {
                    window.open("{{ route('balance-sheet.export-to-pdf') }}" + this.exportQuery(), '_self');
                },
                exportToExcel() {
                    window.open("{{ route('balance-sheet.export-to-excel') }}" + this.exportQuery(), '_self');
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
                this.getBalanceSheet();
            }
        });
    </script>
@endsection
@include('layouts.vue_script')
