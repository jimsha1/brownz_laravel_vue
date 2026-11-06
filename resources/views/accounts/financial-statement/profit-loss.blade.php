@extends('layouts.vcapp')

<style>
    .profit-loss-filters .filters-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
    }

    .profit-loss-filters .filters-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .profit-loss-filters .filters-hint {
        font-size: 12px;
        color: #6c757d;
    }

    .profit-loss-filters label {
        font-size: 13px;
        color: #1D1D1D;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .profit-loss-filters .search-box-bar-container {
        display: flex;
        align-items: center;
        background-color: #FFFFFF;
        box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;
        border-radius: 26px;
        width: 100%;
        padding-left: 16px;
        min-height: 44px;
    }

    .profit-loss-filters .brawnz-search-bar-in {
        background-color: transparent;
        border: none;
        box-shadow: none;
        outline: none;
        padding: 10px 16px 10px 8px;
        font-size: 14px;
        width: 100%;
        height: 44px;
    }

    .profit-loss-filters .filter-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
        margin-top: 8px;
    }

    .profit-loss-list .list-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 14px;
    }

    .profit-loss-list .list-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .profit-loss-list .list-meta {
        font-size: 12px;
        color: #6c757d;
    }

    .profit-loss-list .toolbar-right {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        justify-content: flex-end;
    }

    .profit-loss-list .export-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .profit-loss-list .summary-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 8px 14px;
        background: #F0F0F3;
        color: #1D1D1D;
        font-size: 12px;
        font-weight: 600;
    }

    .profit-loss-list .summary-pill .value {
        font-variant-numeric: tabular-nums;
    }

    .profit-loss-list .summary-pill.is-profit .value {
        color: #199900;
    }

    .profit-loss-list .summary-pill.is-loss .value {
        color: #D0342C;
    }

    .profit-loss-list .empty-state,
    .profit-loss-list .loading-state {
        text-align: center;
        padding: 40px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }

    .profit-loss-list .empty-state i,
    .profit-loss-list .loading-state i {
        font-size: 28px;
        margin-bottom: 10px;
        color: #b7b7b7;
    }

    .profit-loss-list .statement-wrap {
        border-radius: 16px;
        background: #fff;
        padding: 8px 4px 4px;
    }

    #transactionModal.modal .modal-dialog.pl-txn-modal-dialog {
        max-width: min(1720px, calc(100vw - 32px)) !important;
        width: min(1720px, calc(100vw - 32px)) !important;
        margin: 0.75rem auto !important;
    }

    #transactionModal .pl-txn-modal {
        max-height: calc(100vh - 1.5rem);
        display: flex;
        flex-direction: column;
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.2);
    }

    #transactionModal .pl-txn-modal__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        flex: 0 0 auto;
        background: #fff;
        border-bottom: 1px solid #ececf0;
        padding: 1rem 1.25rem;
    }

    #transactionModal .pl-txn-modal__toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        flex: 0 0 auto;
        padding: 10px 16px;
        background: #F7F7F9;
        border-bottom: 1px solid #ececf0;
    }

    #transactionModal .pl-txn-modal__body {
        flex: 1 1 auto;
        min-height: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    #transactionModal .pl-txn-table-wrap {
        flex: 1 1 auto;
        overflow: auto;
        max-height: none;
    }

    #transactionModal .pl-txn-table {
        table-layout: fixed;
        width: 100%;
    }

    #transactionModal .pl-txn-modal__footer {
        flex: 0 0 auto;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        padding: 12px 16px;
        background: #fff;
        border-top: 1px solid #ececf0;
    }

    #transactionModal .pl-txn-modal__title-wrap {
        flex: 1 1 auto;
        min-width: 0;
        padding-right: 8px;
    }

    #transactionModal .pl-txn-modal .modal-title {
        color: #1D1D1D;
        font-size: 1.3rem;
        font-weight: 600;
    }

    #transactionModal .pl-txn-modal__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 6px;
        color: #6c757d;
        font-size: 12px;
    }

    #transactionModal .pl-txn-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #F0F0F3;
        border-radius: 999px;
        padding: 5px 10px;
        font-weight: 600;
        color: #1D1D1D;
    }

    #transactionModal .pl-txn-close {
        flex: 0 0 auto;
        width: 34px;
        height: 34px;
        margin-left: auto;
        border: none;
        border-radius: 50%;
        background: #F0F0F3;
        color: #1D1D1D;
        font-size: 22px;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        opacity: 1;
    }

    #transactionModal .pl-txn-close:hover {
        background: #1D1D1D;
        color: #E6CD7F;
    }

    #transactionModal .pl-txn-export-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    #transactionModal .pl-txn-export-actions .btn {
        white-space: nowrap;
    }

    #transactionModal .pl-txn-export-pdf {
        border-color: #D0342C;
        color: #D0342C;
    }

    #transactionModal .pl-txn-export-pdf:hover {
        background: #D0342C;
        color: #fff;
    }

    #transactionModal .pl-txn-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    #transactionModal .pl-txn-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 8px 12px;
        background: #F0F0F3;
        color: #1D1D1D;
        font-size: 12px;
        font-weight: 600;
    }

    #transactionModal .pl-txn-pill .value {
        font-variant-numeric: tabular-nums;
    }

    #transactionModal .pl-txn-pill.is-debit .value,
    #transactionModal .pl-txn-pill.is-profit .value {
        color: #199900;
    }

    #transactionModal .pl-txn-pill.is-credit .value,
    #transactionModal .pl-txn-pill.is-loss .value {
        color: #D0342C;
    }

    #transactionModal .pl-txn-note {
        display: flex;
        gap: 0.6rem;
        align-items: flex-start;
        flex: 0 0 auto;
        background: #fff8e8;
        border-bottom: 1px solid #f0e2c0;
        color: #5c4a1f;
        font-size: 0.82rem;
        line-height: 1.45;
        padding: 0.75rem 1.25rem;
    }

    #transactionModal .pl-txn-note .fa {
        margin-top: 0.15rem;
        color: #c3922e;
    }

    #transactionModal .pl-txn-empty {
        text-align: center;
        padding: 40px 16px;
        color: #6c757d;
        background: #F7F7F9;
        margin: 12px 16px 16px;
        border-radius: 16px;
    }

    #transactionModal .pl-txn-empty i {
        font-size: 28px;
        margin-bottom: 10px;
        color: #b7b7b7;
    }

    #transactionModal .pl-txn-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #1d1d1d;
        color: #fff;
        font-weight: 500;
        font-size: 0.85rem;
        border: none;
        padding: 0.75rem 1rem;
        white-space: nowrap;
    }

    #transactionModal .pl-txn-table tbody td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
        border-color: #eee;
        font-size: 0.9rem;
    }

    #transactionModal .pl-txn-table tbody tr:hover {
        background: #f7f9fb;
    }

    #transactionModal .pl-txn-table .amount-pill {
        display: inline-flex;
        min-width: 104px;
        justify-content: flex-end;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
    }

    #transactionModal .pl-txn-table .amount-pill.is-debit {
        color: #199900;
        background: rgba(25, 153, 0, 0.12);
    }

    #transactionModal .pl-txn-table .amount-pill.is-credit {
        color: #D0342C;
        background: rgba(208, 52, 44, 0.12);
    }

    #transactionModal .pl-txn-table .amount-pill.is-zero {
        color: #6c757d;
        background: #F0F0F3;
    }

    #transactionModal .pl-voucher-link {
        color: #52A8D9;
        font-weight: 600;
        text-decoration: none;
    }

    #transactionModal .pl-voucher-link:hover {
        color: #0f3d52;
        text-decoration: underline;
    }

    #transactionModal .pl-voucher-link .fa {
        font-size: 0.7rem;
        margin-left: 4px;
        opacity: 0.75;
    }

    #transactionModal .pl-txn-desc {
        display: block;
        white-space: normal;
        word-break: break-word;
    }

    @media (max-width: 991.98px) {
        #transactionModal.modal .modal-dialog.pl-txn-modal-dialog {
            max-width: calc(100vw - 16px) !important;
            width: calc(100vw - 16px) !important;
            margin: 0.5rem auto !important;
        }

        #transactionModal .pl-txn-modal__toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        #transactionModal .pl-txn-export-actions {
            width: 100%;
        }

        #transactionModal .pl-txn-export-actions .btn {
            flex: 1 1 calc(33.333% - 6px);
        }
    }
</style>

@section('content')
    <div id="app">
        <div class="fbox-shadow mt-2 fd-card respo-margin-top profit-loss-filters">
            <div class="card-body">
                <div class="filters-header">
                    <div>
                        <h5 class="filters-title">{{ __('Profit & Loss Report') }}</h5>
                        <div class="filters-hint">{{ __('Trading account and profit & loss for the selected period') }}</div>
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
                                @click="getProfitAndLossStatements()"
                                :disabled="loading">
                                <i class="fa fa-search"></i> {{ __('Search') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fbox-shadow mt-3 fd-card respo-margin-top profit-loss-list">
            <div class="m-3">
                <div class="list-toolbar">
                    <div>
                        <p class="list-title">{{ __('Trading and Profit&Loss Statements') }}</p>
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
                        <span class="summary-pill" v-if="!loading && hasData" :class="Number(gross_profit) >= 0 ? 'is-profit' : 'is-loss'">
                            <span>{{ __('Gross') }}</span>
                            <span class="value">@{{ formatMoneyPlain(gross_profit) }}</span>
                        </span>
                        <span class="summary-pill" v-if="!loading && hasData" :class="Number(net_profit) >= 0 ? 'is-profit' : 'is-loss'">
                            <span>{{ __('Net') }}</span>
                            <span class="value">@{{ formatMoneyPlain(net_profit) }}</span>
                        </span>
                    </div>
                </div>

                <div v-if="loading" class="loading-state">
                    <div><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>
                    <div>{{ __('Loading profit & loss...') }}</div>
                </div>

                <div v-else-if="!hasData" class="empty-state">
                    <div><i class="fa fa-line-chart" aria-hidden="true"></i></div>
                    <div><strong>{{ __('No statement data found') }}</strong></div>
                    <div>{{ __('Try adjusting the date range and search again.') }}</div>
                </div>

                <div v-else class="statement-wrap">
                <div class="table-responsive">
                    <table class="table tpl-statement mb-0">
                        <thead>
                            <tr>
                                <th>Particulars</th>
                                <th class="text-end">Amount</th>
                                <th>Particulars</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>

                        <tbody>
                            <!-- Trading lines -->
                            <tr v-for="i in Math.max(visibleTradingDebitSide.length, trading_credit_side.length)"
                                :key="'trade-' + i">
                                <td :class="lineClass(visibleTradingDebitSide[i - 1])">
                                    <span>@{{ visibleTradingDebitSide[i - 1]?.account_name || '' }}</span>
                                    <button
                                        v-if="visibleTradingDebitSide[i - 1]?.is_expandable"
                                        type="button"
                                        class="btn btn-sm btn-primary ml-2 tpl-expand-btn"
                                        :title="isExpanded(visibleTradingDebitSide[i - 1]) ? 'Collapse' : 'Expand'"
                                        @click="toggleExpand(visibleTradingDebitSide[i - 1])">
                                        @{{ isExpanded(visibleTradingDebitSide[i - 1]) ? '−' : '+' }}
                                    </button>
                                    <button
                                        v-else-if="drilldownKey(visibleTradingDebitSide[i - 1])"
                                        type="button"
                                        class="btn btn-sm btn-primary ml-2 tpl-expand-btn"
                                        @click="openRowDrilldown(visibleTradingDebitSide[i - 1])">+</button>
                                </td>
                                <td class="text-end" :class="lineClass(visibleTradingDebitSide[i - 1])">
                                    @{{ formatAmount(visibleTradingDebitSide[i - 1]) }}
                                </td>
                                <td :class="lineClass(trading_credit_side[i - 1])">
                                    <span>@{{ trading_credit_side[i - 1]?.account_name || '' }}</span>
                                    <button
                                        v-if="drilldownKey(trading_credit_side[i - 1])"
                                        type="button"
                                        class="btn btn-sm btn-primary ml-2 tpl-expand-btn"
                                        @click="openRowDrilldown(trading_credit_side[i - 1])">+</button>
                                </td>
                                <td class="text-end" :class="lineClass(trading_credit_side[i - 1])">
                                    @{{ formatAmount(trading_credit_side[i - 1]) }}
                                </td>
                            </tr>

                            <!-- Trading subtotal (screenshot mid-line totals) -->
                            <tr class="tpl-mid-total">
                                <td></td>
                                <td class="text-end">@{{ formatMoneyPlain(trading_total_debit) }}</td>
                                <td></td>
                                <td class="text-end">@{{ formatMoneyPlain(trading_total_credit) }}</td>
                            </tr>

                            <!-- P&L lines continue in same table -->
                            <tr v-for="i in Math.max(visiblePlDebitSide.length, pl_credit_side.length)"
                                :key="'pl-' + i">
                                <td :class="lineClass(visiblePlDebitSide[i - 1])">
                                    <span>@{{ visiblePlDebitSide[i - 1]?.account_name || '' }}</span>
                                    <button
                                        v-if="visiblePlDebitSide[i - 1]?.is_expandable"
                                        type="button"
                                        class="btn btn-sm btn-primary ml-2 tpl-expand-btn"
                                        :title="isExpanded(visiblePlDebitSide[i - 1]) ? 'Collapse' : 'Expand'"
                                        @click="toggleExpand(visiblePlDebitSide[i - 1])">
                                        @{{ isExpanded(visiblePlDebitSide[i - 1]) ? '−' : '+' }}
                                    </button>
                                    <button
                                        v-else-if="drilldownKey(visiblePlDebitSide[i - 1])"
                                        type="button"
                                        class="btn btn-sm btn-primary ml-2 tpl-expand-btn"
                                        @click="openRowDrilldown(visiblePlDebitSide[i - 1])">+</button>
                                </td>
                                <td class="text-end" :class="lineClass(visiblePlDebitSide[i - 1])">
                                    @{{ formatAmount(visiblePlDebitSide[i - 1]) }}
                                </td>
                                <td :class="lineClass(pl_credit_side[i - 1])">
                                    <span>@{{ pl_credit_side[i - 1]?.account_name || '' }}</span>
                                </td>
                                <td class="text-end" :class="lineClass(pl_credit_side[i - 1])">
                                    @{{ formatAmount(pl_credit_side[i - 1]) }}
                                </td>
                            </tr>

                            <!-- Final Total -->
                            <tr class="tpl-final-total">
                                <td><strong>Total</strong></td>
                                <td class="text-end"><strong>@{{ formatMoneyPlain(pl_total_debit) }}</strong></td>
                                <td></td>
                                <td class="text-end"><strong>@{{ formatMoneyPlain(pl_total_credit) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>

                    <style>
                        .tpl-statement {
                            width: 100%;
                            border-collapse: collapse;
                            background: #fff;
                            border-top: 3px double #1d1d1d;
                            border-bottom: 3px double #1d1d1d;
                        }

                        .tpl-statement th,
                        .tpl-statement td {
                            border: none;
                            border-bottom: 1px solid #ddd;
                            vertical-align: middle;
                            padding: 0.5rem 0.75rem;
                            font-size: 0.95rem;
                            color: #1d1d1d;
                        }

                        .tpl-statement thead th {
                            background: #fff;
                            font-weight: 700;
                            border-bottom: 2px solid #1d1d1d;
                        }

                        .tpl-statement th:nth-child(2),
                        .tpl-statement td:nth-child(2) {
                            border-right: 1px solid #1d1d1d;
                            width: 18%;
                        }

                        .tpl-statement th:nth-child(1),
                        .tpl-statement td:nth-child(1),
                        .tpl-statement th:nth-child(3),
                        .tpl-statement td:nth-child(3) {
                            width: 32%;
                        }

                        .tpl-statement th:nth-child(4),
                        .tpl-statement td:nth-child(4) {
                            width: 18%;
                        }

                        .tpl-statement .text-end {
                            font-variant-numeric: tabular-nums;
                            white-space: nowrap;
                        }

                        .tpl-mid-total td {
                            border-top: 1px solid #1d1d1d;
                            border-bottom: 1px solid #1d1d1d;
                            font-weight: 600;
                            padding-top: 0.45rem;
                            padding-bottom: 0.45rem;
                        }

                        .tpl-final-total td {
                            border-top: 1px solid #1d1d1d;
                            border-bottom: 2px solid #1d1d1d;
                            background: #F7F7F9;
                        }

                        .tpl-italic {
                            font-style: italic;
                        }

                        .tpl-indent {
                            padding-left: 2rem !important;
                        }

                        .tpl-expand-btn {
                            min-width: 28px;
                            width: 28px;
                            height: 28px;
                            padding: 0;
                            border-radius: 50%;
                            font-weight: 700;
                            line-height: 1;
                            background: #1D1D1D !important;
                            border-color: #1D1D1D !important;
                            color: #E6CD7F !important;
                        }
                    </style>

            </div>
        </div>

        <div class="modal fade" id="transactionModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog pl-txn-modal-dialog" role="document">
                <div class="modal-content pl-txn-modal">
                    <div class="modal-header pl-txn-modal__header">
                        <div class="pl-txn-modal__title-wrap">
                            <h5 class="modal-title mb-1">@{{ modalTitle }}</h5>
                            <div class="pl-txn-modal__meta" v-if="modalSubtitle">
                                @{{ modalSubtitle }}
                            </div>
                            <div class="pl-txn-modal__meta" v-if="filter_start_date && filter_end_date">
                                <span class="pl-txn-chip">
                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                    @{{ formatDisplayDate(filter_start_date) }} → @{{ formatDisplayDate(filter_end_date) }}
                                </span>
                                <span class="pl-txn-chip">
                                    <i class="fa fa-list" aria-hidden="true"></i>
                                    @{{ transactionDetails.length }} entr@{{ transactionDetails.length === 1 ? 'y' : 'ies' }}
                                </span>
                            </div>
                        </div>
                        <button type="button" class="pl-txn-close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="pl-txn-modal__toolbar" v-if="!modalLoading && transactionDetails.length">
                        <div class="pl-txn-summary">
                            <span class="pl-txn-pill is-debit">
                                <span>{{ __('Debit') }}</span>
                                <span class="value">@{{ formatMoney(transactionTotalDebit) }}</span>
                            </span>
                            <span class="pl-txn-pill is-credit">
                                <span>{{ __('Credit') }}</span>
                                <span class="value">@{{ formatMoney(transactionTotalCredit) }}</span>
                            </span>
                            <span class="pl-txn-pill" :class="Number(transactionNetAmount) >= 0 ? 'is-profit' : 'is-loss'">
                                <span>{{ __('Net') }}</span>
                                <span class="value">@{{ formatMoney(transactionNetAmount) }}</span>
                            </span>
                        </div>
                        <div class="pl-txn-export-actions">
                            <button type="button"
                                class="btn btn-sm brownz-btn-yellow-d"
                                @click="printDrilldown()"
                                :disabled="modalLoading">
                                <i class="fa fa-print"></i> {{ __('Print') }}
                            </button>
                            <button type="button"
                                class="btn btn-sm btn-outline-dark pl-txn-export-pdf"
                                @click="exportDrilldownPDF()"
                                :disabled="modalLoading">
                                <i class="fa fa-file-pdf-o"></i> {{ __('PDF') }}
                            </button>
                            <button type="button"
                                class="btn btn-sm btn_dark-x"
                                @click="exportDrilldownExcel()"
                                :disabled="modalLoading">
                                <i class="fa fa-file-excel-o"></i> {{ __('Excel') }}
                            </button>
                        </div>
                    </div>

                    <div class="modal-body pl-txn-modal__body p-0">
                        <div v-if="modalNote" class="pl-txn-note">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                            <span>@{{ modalNote }}</span>
                        </div>

                        <div v-if="modalLoading" class="pl-txn-empty">
                            <div><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>
                            <div>{{ __('Loading transactions...') }}</div>
                        </div>

                        <div v-else-if="!transactionDetails.length" class="pl-txn-empty">
                            <div><i class="fa fa-inbox" aria-hidden="true"></i></div>
                            <div><strong>{{ __('No transactions found') }}</strong></div>
                            <div>{{ __('Nothing posted for this account in the selected period.') }}</div>
                        </div>

                        <div v-else class="pl-txn-table-wrap">
                            <table class="table pl-txn-table mb-0">
                                <colgroup>
                                    <col style="width: 4%;">
                                    <col style="width: 10%;">
                                    <col style="width: 11%;">
                                    <col style="width: auto;">
                                    <col style="width: 12%;">
                                    <col style="width: 12%;">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Voucher No.') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th class="text-right">{{ __('Debit') }}</th>
                                        <th class="text-right">{{ __('Credit') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in transactionDetails"
                                        :key="'txn-' + index + '-' + item.voucher_no">
                                        <td>@{{ index + 1 }}</td>
                                        <td>
                                            <a v-if="item.voucher_url"
                                                :href="item.voucher_url"
                                                class="pl-voucher-link"
                                                target="_blank"
                                                rel="noopener"
                                                :title="'Open voucher ' + item.voucher_no">
                                                @{{ item.voucher_no }}
                                                <i class="fa fa-external-link" aria-hidden="true"></i>
                                            </a>
                                            <span v-else>@{{ item.voucher_no || '—' }}</span>
                                        </td>
                                        <td class="text-nowrap">@{{ formatTxnDate(item.date) }}</td>
                                        <td>
                                            <span class="pl-txn-desc" :title="item.description">
                                                @{{ item.description || '—' }}
                                            </span>
                                        </td>
                                        <td class="text-right text-nowrap">
                                            <span class="amount-pill"
                                                :class="Number(item.debit || 0) > 0 ? 'is-debit' : 'is-zero'">
                                                @{{ formatMoney(item.debit) }}
                                            </span>
                                        </td>
                                        <td class="text-right text-nowrap">
                                            <span class="amount-pill"
                                                :class="Number(item.credit || 0) > 0 ? 'is-credit' : 'is-zero'">
                                                @{{ formatMoney(item.credit) }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer pl-txn-modal__footer">
                        <button type="button" class="btn btn-sm btn_dark-x" data-dismiss="modal">
                            {{ __('Close') }}
                        </button>
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

                trading_debit_side: [],
                trading_credit_side: [],

                pl_debit_side: [],
                pl_credit_side: [],

                trading_total_debit: 0,
                trading_total_credit: 0,

                pl_total_debit: 0,
                pl_total_credit: 0,

                gross_profit: 0,
                net_profit: 0,

                @include('accounts.financial-statement.partials.financial-year-picker-data')
                filter_start_date: '',
                filter_end_date: '',

                indirectExpensesExpanded: false,
                expandedRows: {},

                transactionDetails: [],
                modalTitle: '',
                modalSubtitle: '',
                modalNote: '',
                modalDrilldownAccountName: '',
                modalDrilldownType: '',
                modalLoading: false,
                transactionTotalDebit: 0,
                transactionTotalCredit: 0,
                transactionNetAmount: 0,
            },

            computed: {
                visibleTradingDebitSide() {
                    return this.expandRows(this.trading_debit_side);
                },

                visiblePlDebitSide() {
                    return this.expandRows(this.pl_debit_side);
                },

                hasData() {
                    return (this.trading_debit_side && this.trading_debit_side.length)
                        || (this.trading_credit_side && this.trading_credit_side.length)
                        || (this.pl_debit_side && this.pl_debit_side.length)
                        || (this.pl_credit_side && this.pl_credit_side.length);
                },
            },

            methods: {

                expandRows(source) {
                    var rows = [];
                    var list = source || [];

                    for (var i = 0; i < list.length; i++) {
                        var row = list[i];
                        rows.push(row);

                        if (
                            row
                            && row.is_expandable
                            && this.isExpanded(row)
                            && Array.isArray(row.children)
                            && row.children.length
                        ) {
                            for (var c = 0; c < row.children.length; c++) {
                                rows.push(row.children[c]);
                            }
                        }
                    }

                    return rows;
                },

                isExpanded(row) {
                    if (!row || !row.account_name) {
                        return false;
                    }
                    return !!this.expandedRows[row.account_name];
                },

                toggleExpand(row) {
                    if (!row || !row.account_name) {
                        return;
                    }
                    var key = row.account_name;
                    this.$set(this.expandedRows, key, !this.expandedRows[key]);
                },

                handleChangeStartDate() {
                    if (!this.filter_start_date) {
                        return;
                    }

                    let end = new Date(this.filter_start_date);
                    let month = end.getMonth() + 1;
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
                    if (!val) {
                        return '—';
                    }
                    if (window.moment) {
                        return moment(val).format('DD-MM-YYYY');
                    }
                    return val;
                },

                getProfitAndLossStatements() {
                    this.loading = true;

                    axios.post("{{ route('profit-and-loss-vue-list') }}", {
                            start_date: this.filter_start_date,
                            end_date: this.filter_end_date
                        })
                        .then(response => {

                            this.trading_debit_side = response.data.trading_debit_side || [];
                            this.trading_credit_side = response.data.trading_credit_side || [];

                            this.pl_debit_side = response.data.pl_debit_side || [];
                            this.pl_credit_side = response.data.pl_credit_side || [];

                            this.trading_total_debit = response.data.trading_total_debit || 0;
                            this.trading_total_credit = response.data.trading_total_credit || 0;

                            this.pl_total_debit = response.data.pl_total_debit || response.data.pl_total || 0;
                            this.pl_total_credit = response.data.pl_total_credit || response.data.pl_total || 0;

                            this.gross_profit = response.data.gross_profit || 0;
                            this.net_profit = response.data.net_profit || 0;
                            this.expandedRows = {};
                            this.indirectExpensesExpanded = false;
                        })
                        .catch(error => {
                            console.error(error);
                            this.trading_debit_side = [];
                            this.trading_credit_side = [];
                            this.pl_debit_side = [];
                            this.pl_credit_side = [];
                            this.gross_profit = 0;
                            this.net_profit = 0;
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },

                toggleIndirectExpenses() {
                    this.toggleExpand({ account_name: 'Indirect Expenses' });
                },

                lineClass(row) {
                    if (!row || !row.account_name) {
                        return '';
                    }
                    return [
                        row.is_italic ? 'tpl-italic' : '',
                        row.is_indent || row.is_child ? 'tpl-indent' : '',
                        row.is_deduction ? 'tpl-deduction' : '',
                    ].filter(Boolean).join(' ');
                },

                formatMoneyPlain(value) {
                    var amount = parseFloat(value);
                    if (isNaN(amount)) {
                        amount = 0;
                    }
                    return amount.toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },

                formatAmount(row) {
                    if (!row || row.account_name === undefined || row.account_name === null || row.account_name === '') {
                        return '';
                    }
                    var amount = parseFloat(row.amount);
                    if (isNaN(amount)) {
                        amount = 0;
                    }
                    // Keep signed values (e.g. Net Profit negative) like the screenshot
                    var formatted = amount.toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    if (row.is_deduction && amount >= 0) {
                        return '(' + this.formatMoneyPlain(amount) + ')';
                    }
                    return formatted;
                },

                drilldownKey(row) {
                    if (!row || !row.account_name) {
                        return null;
                    }

                    if (row.drilldown_name && row.drilldown_type) {
                        return {
                            name: row.drilldown_name,
                            type: row.drilldown_type,
                        };
                    }

                    var map = {
                        'Purchase Accounts': { name: 'Purchase Account', type: 'account' },
                        'Direct Expenses': { name: 'Direct Expense', type: 'category' },
                        'Sales Revenue': { name: 'Sales Revenue', type: 'account' },
                        'Service Revenue': { name: 'Service Revenue', type: 'account' },
                        'Sales Accounts': { name: 'Sales Revenue', type: 'account' },
                        'Indirect Expenses': { name: 'Indirect Expense', type: 'category' },
                    };
                    return map[row.account_name] || null;
                },

                openRowDrilldown(row) {
                    var target = this.drilldownKey(row);
                    if (!target) {
                        return;
                    }
                    this.showTransactions(target.name, target.type, row.account_name);
                },

                showTransactions(accountName, type, modalTitle) {

                    this.modalTitle = modalTitle || accountName;
                    this.modalDrilldownAccountName = accountName;
                    this.modalDrilldownType = type || 'account';
                    this.modalSubtitle = '';
                    this.modalNote = '';
                    this.transactionDetails = [];
                    this.transactionTotalDebit = 0;
                    this.transactionTotalCredit = 0;
                    this.transactionNetAmount = 0;
                    this.modalLoading = true;
                    $('#transactionModal').modal('show');

                    axios.post("{{ route('profit-and-loss-account-transactions') }}", {
                            account_name: accountName,
                            type: type,
                            start_date: this.filter_start_date,
                            end_date: this.filter_end_date
                        })
                        .then(response => {
                            this.transactionDetails = response.data.transactions || [];
                            this.transactionTotalDebit = response.data.total_debit
                                ?? this.sumTxnField('debit');
                            this.transactionTotalCredit = response.data.total_credit
                                ?? this.sumTxnField('credit');
                            this.transactionNetAmount = response.data.net_amount
                                ?? (this.transactionTotalCredit - this.transactionTotalDebit);
                            this.modalSubtitle = response.data.subtitle || '';
                            this.modalNote = response.data.note || '';
                        })
                        .catch(error => {
                            console.error(error);
                            this.transactionDetails = [];
                        })
                        .finally(() => {
                            this.modalLoading = false;
                        });
                },

                sumTxnField(field) {
                    return (this.transactionDetails || []).reduce(function(sum, row) {
                        return sum + (parseFloat(row[field]) || 0);
                    }, 0);
                },

                formatMoney(value) {
                    var amount = parseFloat(value);
                    if (isNaN(amount)) {
                        amount = 0;
                    }
                    return '\u20B9 ' + amount.toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },

                formatTxnDate(date) {
                    if (!date) {
                        return '—';
                    }
                    if (window.moment) {
                        return moment(date).format('DD MMM YYYY');
                    }
                    return date;
                },

                exportQuery() {
                    return '?start_date=' + encodeURIComponent(this.filter_start_date || '')
                        + '&end_date=' + encodeURIComponent(this.filter_end_date || '');
                },
                printReport() {
                    var printWindow = window.open(
                        "{{ route('profit-loss.print') }}" + this.exportQuery(),
                        '_blank',
                        'noopener,noreferrer'
                    );

                    if (!printWindow) {
                        alert('{{ __('Please allow popups to print the profit & loss statement.') }}');
                    }
                },
                exportToPDF() {
                    window.open("{{ route('profit-loss.export-to-pdf') }}" + this.exportQuery(), '_self');
                },
                exportToExcel() {
                    window.open("{{ route('profit-loss.export-to-excel') }}" + this.exportQuery(), '_self');
                },
                drilldownExportQuery() {
                    return '?start_date=' + encodeURIComponent(this.filter_start_date || '')
                        + '&end_date=' + encodeURIComponent(this.filter_end_date || '')
                        + '&account_name=' + encodeURIComponent(this.modalDrilldownAccountName || '')
                        + '&type=' + encodeURIComponent(this.modalDrilldownType || 'account')
                        + '&title=' + encodeURIComponent(this.modalTitle || this.modalDrilldownAccountName || '');
                },
                printDrilldown() {
                    if (!this.modalDrilldownAccountName) {
                        return;
                    }

                    var printWindow = window.open(
                        "{{ route('profit-loss-account-transactions.print') }}" + this.drilldownExportQuery(),
                        '_blank',
                        'noopener,noreferrer'
                    );

                    if (!printWindow) {
                        alert('{{ __('Please allow popups to print these transactions.') }}');
                    }
                },
                exportDrilldownPDF() {
                    if (!this.modalDrilldownAccountName) {
                        return;
                    }

                    window.open(
                        "{{ route('profit-loss-account-transactions.export-to-pdf') }}" + this.drilldownExportQuery(),
                        '_self'
                    );
                },
                exportDrilldownExcel() {
                    if (!this.modalDrilldownAccountName) {
                        return;
                    }

                    window.open(
                        "{{ route('profit-loss-account-transactions.export-to-excel') }}" + this.drilldownExportQuery(),
                        '_self'
                    );
                },
                resetFilter() {
                    this.resetFinancialYearSelection();
                    this.getProfitAndLossStatements();
                },

                applyDefaultDates() {
                    this.applyFinancialYearDates();
                },

                onFinancialYearChange() {
                    this.applyFinancialYearDates();
                    this.getProfitAndLossStatements();
                },

                @include('accounts.financial-statement.partials.financial-year-picker-methods')

                formatDateLocal(date) {

                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');

                    return `${year}-${month}-${day}`;
                }
            },

            mounted() {
                this.applyFinancialYearDates();
                this.getProfitAndLossStatements();
            }




        });
    </script>
@endsection
@include('layouts.vue_script')
