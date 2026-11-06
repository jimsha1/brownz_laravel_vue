@extends('layouts.vcapp')

<style>
    .fy-details-modal {
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 48px);
        padding: 0 !important;
    }

    .fy-details-modal__header {
        flex: 0 0 auto;
        padding: 1.25rem 1.25rem 0.75rem;
        border-bottom: 1px solid #ececf0;
    }

    .fy-details-modal__title {
        color: #1D1D1D;
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 0.35rem;
    }

    .fy-details-modal__meta {
        color: #6c757d;
        font-size: 12px;
        margin-bottom: 0.25rem;
    }

    .fy-details-modal__toolbar {
        flex: 0 0 auto;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 16px;
        background: #F7F7F9;
        border-bottom: 1px solid #ececf0;
    }

    .fy-details-modal__export-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .fy-details-modal__export-actions .btn {
        white-space: nowrap;
    }

    .fy-details-export-pdf {
        border-color: #D0342C;
        color: #D0342C;
    }

    .fy-details-export-pdf:hover {
        background: #D0342C;
        color: #fff;
    }

    .fy-details-modal__body {
        flex: 1 1 auto;
        min-height: 0;
        overflow: auto;
        padding: 1rem 1.25rem 1.25rem;
    }

    .fy-details-modal__summary {
        margin-bottom: 1rem;
    }

    .fy-details-modal__section-title {
        margin: 1.25rem 0 0.5rem;
        font-size: 1rem;
        font-weight: 600;
        color: #1D1D1D;
    }

    .fy-details-modal__section-note {
        margin-bottom: 0.5rem;
        font-size: 13px;
        color: #6c757d;
    }

    .fy-details-modal__table-wrap {
        overflow-x: auto;
    }

    .fy-details-modal__table-wrap .table {
        table-layout: fixed;
        width: 100%;
        min-width: 720px;
        margin-bottom: 0;
    }

    .fy-details-modal__table-wrap .table th:nth-child(1),
    .fy-details-modal__table-wrap .table td:nth-child(1) {
        width: 22%;
    }

    .fy-details-modal__table-wrap .table th:nth-child(2),
    .fy-details-modal__table-wrap .table td:nth-child(2) {
        width: auto;
        word-break: break-word;
    }

    .fy-details-modal__table-wrap .table th:nth-child(3),
    .fy-details-modal__table-wrap .table td:nth-child(3),
    .fy-details-modal__table-wrap .table th:nth-child(4),
    .fy-details-modal__table-wrap .table td:nth-child(4) {
        width: 14%;
        white-space: nowrap;
    }

    .fy-details-modal__table-wrap.is-three-col .table {
        min-width: 560px;
    }

    .fy-details-modal__table-wrap.is-three-col .table th:nth-child(1),
    .fy-details-modal__table-wrap.is-three-col .table td:nth-child(1) {
        width: auto;
        word-break: break-word;
    }

    .fy-details-modal__table-wrap.is-three-col .table th:nth-child(2),
    .fy-details-modal__table-wrap.is-three-col .table td:nth-child(2),
    .fy-details-modal__table-wrap.is-three-col .table th:nth-child(3),
    .fy-details-modal__table-wrap.is-three-col .table td:nth-child(3) {
        width: 18%;
        white-space: nowrap;
    }

    .fy-details-modal__totals {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .fy-details-modal__total-pill {
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

    .fy-details-modal__total-pill .value {
        font-variant-numeric: tabular-nums;
    }

    .fy-details-modal__total-pill.is-debit .value {
        color: #199900;
    }

    .fy-details-modal__total-pill.is-credit .value {
        color: #D0342C;
    }

    .fy-details-modal__footer {
        flex: 0 0 auto;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        padding: 12px 16px;
        border-top: 1px solid #ececf0;
        background: #fff;
    }

    @media (max-width: 767.98px) {
        .fy-details-modal__toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .fy-details-modal__export-actions {
            width: 100%;
        }

        .fy-details-modal__export-actions .btn {
            flex: 1 1 calc(33.333% - 6px);
        }
    }
</style>

@section('content')
    <script>
        var vc_moment_date_offset = new Date("2021-10-05T17:28:50.403+03:00").getTime() - Date.now();
        var vc_jscalendar_moment_format = "DD/MM/YYYY";
    </script>
    <div id="app">
        <div class="fbox-shadow mt-2 fd-card respo-margin-top">
            <div class="m-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h5 style="color: #1D1D1D; font-size: 1.53125rem;">{{ __('Financial Year') }}</h5>
                    <div class="vc-form-buttons d-flex">
                        <button v-if="permissions.initialize && !years.length" class="btn btn-primary btn-sm mr-2"
                            @click="initializeYear()" :disabled="loading">
                            Initialize Current Year
                        </button>
                        <button v-if="permissions.open && canOpenNextYear" class="btn btn-success btn-sm"
                            @click="previewOpenYear()" :disabled="loading">
                            Open Next Financial Year
                        </button>
                    </div>
                </div>
                <p class="text-muted mt-2 mb-0">
                    Financial year runs from 1 April to 31 March. Closing zeros revenue/expense (including purchases and
                    stock adjustments) into Profit &amp; Loss Closing, then transfers the net result to Retained Earnings
                    and locks the year from new postings. Opening a new year creates an audit-only opening balance voucher.
                </p>
            </div>
        </div>

        <div class="fbox-shadow mt-2 fd-card respo-margin-top">
            <div class="m-3">
                <div class="table-responsive">
                    <table class="table brownz-table-list-v1">
                        <thead>
                            <tr>
                                <th>{{ __('SL#') }}</th>
                                <th>{{ __('Year') }}</th>
                                <th>{{ __('Period') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Net Profit / Loss') }}</th>
                                <th>{{ __('Closing Voucher') }}</th>
                                <th>{{ __('Opening Voucher') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="!years.length">
                                <tr>
                                    <td colspan="8">
                                        <center>No financial year configured.
                                            <span v-if="permissions.initialize">Click "Initialize Current Year" to begin.</span>
                                        </center>
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr v-for="(year, index) in years" :key="year.id">
                                    <td>@{{ index + 1 }}</td>
                                    <td>
                                        @{{ year.label }}
                                        <span v-if="year.is_current" class="badge badge-primary ml-1">Current</span>
                                    </td>
                                    <td>@{{ formatDate(year.start_date) }} - @{{ formatDate(year.end_date) }}</td>
                                    <td>
                                        <span :class="year.status === 'open' ? 'text-success' : 'text-secondary'">
                                            @{{ year.status === 'open' ? 'Open' : 'Closed' }}
                                        </span>
                                    </td>
                                    <td>@{{ year.net_profit_loss ?? '-' }}</td>
                                    <td>
                                        <button v-if="year.closing_voucher_no" class="btn btn-link btn-sm p-0"
                                            @click="viewYearDetails(year)">
                                            @{{ year.closing_voucher_no }}
                                        </button>
                                        <span v-else>-</span>
                                    </td>
                                    <td>
                                        <button v-if="year.opening_voucher_no" class="btn btn-link btn-sm p-0"
                                            @click="viewOpeningBalance(year)">
                                            @{{ year.opening_voucher_no }}
                                        </button>
                                        <span v-else>-</span>
                                    </td>
                                    <td>
                                        <button v-if="year.status === 'closed'"
                                            class="btn btn-info btn-sm mr-1" @click="viewYearDetails(year)"
                                            :disabled="loading">
                                            View Details
                                        </button>
                                        <button v-if="permissions.close && year.status === 'open'"
                                            class="btn btn-warning btn-sm" @click="previewClose(year)" :disabled="loading">
                                            Close Year
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <modal name="close-preview-modal" width="900" height="auto" :scrollable="true" :adaptive="true">
            <div class="p-4" v-if="closePreview">
                <h5>Close Financial Year @{{ closePreview.financial_year.label }}</h5>
                <p class="text-muted">
                    Period: @{{ formatDate(closePreview.financial_year.start_date) }} -
                    @{{ formatDate(closePreview.financial_year.end_date) }}
                </p>

                <div class="row">
                    <div class="col-md-6">
                        <h6>Revenue Accounts</h6>
                        <table class="table table-sm brownz-table-list-v1">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th class="text-right">Close Dr</th>
                                    <th class="text-right">Close Cr</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!closePreview.revenue_accounts.length">
                                    <td colspan="3">No revenue balances</td>
                                </tr>
                                <tr v-for="row in closePreview.revenue_accounts" :key="'rev-' + row.account_id">
                                    <td>@{{ row.name }}</td>
                                    <td class="text-right">@{{ row.close_debit }}</td>
                                    <td class="text-right">@{{ row.close_credit }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Expense Accounts</h6>
                        <table class="table table-sm brownz-table-list-v1">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th class="text-right">Close Dr</th>
                                    <th class="text-right">Close Cr</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!closePreview.expense_accounts.length">
                                    <td colspan="3">No expense balances</td>
                                </tr>
                                <tr v-for="row in closePreview.expense_accounts" :key="'exp-' + row.account_id">
                                    <td>@{{ row.name }}</td>
                                    <td class="text-right">@{{ row.close_debit }}</td>
                                    <td class="text-right">@{{ row.close_credit }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-3" v-if="closePreview.stock_adjustments && closePreview.stock_adjustments.length">
                    <h6>Stock Adjustments</h6>
                    <table class="table table-sm brownz-table-list-v1">
                        <thead>
                            <tr>
                                <th>Particulars</th>
                                <th class="text-right">Dr (to P&amp;L)</th>
                                <th class="text-right">Cr (to P&amp;L)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in closePreview.stock_adjustments" :key="'stock-' + row.name">
                                <td>@{{ row.name }}</td>
                                <td class="text-right">@{{ row.debit }}</td>
                                <td class="text-right">@{{ row.credit }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="text-muted mb-0">
                        COGS: @{{ Number(closePreview.cogs || 0).toFixed(2) }}
                        (Opening @{{ Number(closePreview.opening_stock || 0).toFixed(2) }}
                        + Purchases in expenses
                        - Closing @{{ Number(closePreview.closing_stock || 0).toFixed(2) }})
                    </p>
                </div>

                <div class="alert alert-info mt-3">
                    <strong>Net @{{ closePreview.net_profit_loss >= 0 ? 'Profit' : 'Loss' }}:</strong>
                    @{{ Math.abs(closePreview.net_profit_loss).toFixed(2) }}
                    <span v-if="closePreview.net_profit_loss != 0">
                        (Transferred to Retained Earnings:
                        Dr @{{ closePreview.retained_earnings_effect.debit }}
                        / Cr @{{ closePreview.retained_earnings_effect.credit }})
                    </span>
                </div>

                <div class="mt-3 p-3" style="background:#F7F7F9;border-radius:12px;">
                    <p class="text-muted mb-2" style="font-size:14px;">
                        Enter your password to confirm closing this financial year. This cannot be undone and will lock postings for the period.
                    </p>
                    <div class="form-group mb-1">
                        <label for="close_auth_password">Your Password</label>
                        <input id="close_auth_password"
                            type="password"
                            class="form-control"
                            v-model="closeAuthPassword"
                            autocomplete="current-password"
                            placeholder="Password"
                            @keyup.enter="confirmClose()"
                            :disabled="loading">
                    </div>
                    <div v-if="closePasswordError" class="text-danger small mt-1">@{{ closePasswordError }}</div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-secondary btn-sm mr-2" @click="cancelClosePreview()" :disabled="loading">Cancel</button>
                    <button v-if="permissions.close" class="btn btn-danger btn-sm" @click="confirmClose()"
                        :disabled="loading || !closeAuthPassword">
                        <i class="fa fa-lock"></i> Confirm Year Close
                    </button>
                </div>
            </div>
        </modal>

        <modal name="open-preview-modal" width="900" height="auto" :scrollable="true" :adaptive="true">
            <div class="p-4" v-if="openPreview">
                <h5>Open Financial Year @{{ openPreview.next_label }}</h5>
                <p class="text-muted">
                    Period: @{{ formatDate(openPreview.start_date) }} - @{{ formatDate(openPreview.end_date) }}
                </p>
                <p class="text-muted">
                    An audit-only opening balance voucher will be created. It does not change account balances but records
                    the brought-forward balance sheet position.
                </p>

                <table class="table table-sm brownz-table-list-v1">
                    <thead>
                        <tr>
                            <th>Account</th>
                            <th>Type</th>
                            <th class="text-right">Debit</th>
                            <th class="text-right">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!openPreview.accounts.length">
                            <td colspan="4">No balance sheet balances to carry forward</td>
                        </tr>
                        <tr v-for="row in openPreview.accounts" :key="'open-' + row.account_id">
                            <td>@{{ row.name }}</td>
                            <td>@{{ row.type }}</td>
                            <td class="text-right">@{{ row.debit }}</td>
                            <td class="text-right">@{{ row.credit }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Total</th>
                            <th class="text-right">@{{ openPreview.total_debit }}</th>
                            <th class="text-right">@{{ openPreview.total_credit }}</th>
                        </tr>
                    </tfoot>
                </table>

                <div class="alert alert-secondary"
                    v-if="openPreview.stock_in_hand_note && openPreview.stock_in_hand_note.amount">
                    Inventory Stock in Hand (reference only):
                    @{{ Number(openPreview.stock_in_hand_note.amount).toFixed(2) }}.
                    @{{ openPreview.stock_in_hand_note.message }}
                </div>

                <div class="alert alert-danger"
                    v-if="openPreview && openPreview.books_balanced === false">
                    Opening totals are unbalanced
                    (Dr @{{ openPreview.total_debit }} / Cr @{{ openPreview.total_credit }}).
                    Fix ledger balance drift before opening the next year.
                </div>

                <div class="d-flex justify-content-end">
                    <button class="btn btn-secondary btn-sm mr-2" @click="$modal.hide('open-preview-modal')">Cancel</button>
                    <button v-if="permissions.open" class="btn btn-success btn-sm" @click="confirmOpen()"
                        :disabled="loading || openPreview.books_balanced === false">
                        Confirm Open Year
                    </button>
                </div>
            </div>
        </modal>

        <modal name="opening-balance-modal" width="1000" height="auto" :scrollable="true" :adaptive="true">
            <div class="fy-details-modal" v-if="openingBalanceDetails">
                <div class="fy-details-modal__header">
                    <div class="fy-details-modal__title">
                        {{ __('Opening Balance Audit') }} - @{{ openingBalanceDetails.financial_year.label }}
                    </div>
                    <div class="fy-details-modal__meta">
                        {{ __('Voucher') }}: @{{ openingBalanceDetails.financial_year.opening_voucher_no || '—' }}
                    </div>
                    <div class="fy-details-modal__meta">
                        {{ __('Period') }}: @{{ formatDate(openingBalanceDetails.financial_year.start_date) }} -
                        @{{ formatDate(openingBalanceDetails.financial_year.end_date) }}
                        | {{ __('Status') }}: @{{ openingBalanceDetails.financial_year.status === 'open' ? 'Open' : 'Closed' }}
                    </div>
                </div>

                <div class="fy-details-modal__toolbar">
                    <div class="fy-details-modal__totals">
                        <span class="fy-details-modal__total-pill is-debit">
                            <span>{{ __('Total Debit') }}</span>
                            <span class="value">@{{ openingBalanceDetails.total_debit }}</span>
                        </span>
                        <span class="fy-details-modal__total-pill is-credit">
                            <span>{{ __('Total Credit') }}</span>
                            <span class="value">@{{ openingBalanceDetails.total_credit }}</span>
                        </span>
                    </div>
                    <div class="fy-details-modal__export-actions">
                        <button type="button"
                            class="btn btn-sm brownz-btn-yellow-d"
                            @click="printOpeningBalance()">
                            <i class="fa fa-print"></i> {{ __('Print') }}
                        </button>
                        <button type="button"
                            class="btn btn-sm btn-outline-dark fy-details-export-pdf"
                            @click="exportOpeningBalancePDF()">
                            <i class="fa fa-file-pdf-o"></i> {{ __('PDF') }}
                        </button>
                        <button type="button"
                            class="btn btn-sm btn_dark-x"
                            @click="exportOpeningBalanceExcel()">
                            <i class="fa fa-file-excel-o"></i> {{ __('Excel') }}
                        </button>
                    </div>
                </div>

                <div class="fy-details-modal__body">
                    <div class="fy-details-modal__table-wrap is-three-col">
                        <table class="table table-sm brownz-table-list-v1">
                            <thead>
                                <tr>
                                    <th>{{ __('Account') }}</th>
                                    <th class="text-right">{{ __('Debit') }}</th>
                                    <th class="text-right">{{ __('Credit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!openingBalanceDetails.accounts.length">
                                    <td colspan="3">{{ __('No opening balance accounts recorded.') }}</td>
                                </tr>
                                <tr v-for="row in openingBalanceDetails.accounts" :key="'ob-' + row.account_id">
                                    <td>@{{ row.name }}</td>
                                    <td class="text-right">@{{ row.debit }}</td>
                                    <td class="text-right">@{{ row.credit }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>{{ __('Total') }}</th>
                                    <th class="text-right">@{{ openingBalanceDetails.total_debit }}</th>
                                    <th class="text-right">@{{ openingBalanceDetails.total_credit }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="fy-details-modal__footer">
                    <button type="button" class="btn btn-secondary btn-sm" @click="$modal.hide('opening-balance-modal')">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </modal>

        <modal name="year-details-modal" width="1200" height="auto" :scrollable="true" :adaptive="true">
            <div class="fy-details-modal" v-if="yearDetails">
                <div class="fy-details-modal__header">
                    <div class="fy-details-modal__title">
                        {{ __('Financial Year Details') }} - @{{ yearDetails.financial_year.label }}
                    </div>
                    <div class="fy-details-modal__meta">
                        {{ __('Period') }}: @{{ formatDate(yearDetails.financial_year.start_date) }} -
                        @{{ formatDate(yearDetails.financial_year.end_date) }}
                        | {{ __('Status') }}: @{{ yearDetails.financial_year.status === 'open' ? 'Open' : 'Closed' }}
                    </div>
                    <div class="fy-details-modal__meta" v-if="yearDetails.financial_year.closed_at">
                        {{ __('Closed at') }}: @{{ yearDetails.financial_year.closed_at }}
                        <span v-if="yearDetails.financial_year.closed_by">
                            {{ __('by') }} @{{ yearDetails.financial_year.closed_by }}
                        </span>
                    </div>
                </div>

                <div class="fy-details-modal__toolbar">
                    <div class="alert alert-info mb-0 fy-details-modal__summary">
                        <strong>{{ __('Net') }} @{{ (yearDetails.net_profit_loss || 0) >= 0 ? 'Profit' : 'Loss' }}:</strong>
                        @{{ Math.abs(Number(yearDetails.net_profit_loss || 0)).toFixed(2) }}
                        <span v-if="yearDetails.net_profit_loss != 0 && yearDetails.net_profit_loss != null">
                            ({{ __('Retained Earnings') }}:
                            Dr @{{ yearDetails.retained_earnings_effect.debit }}
                            / Cr @{{ yearDetails.retained_earnings_effect.credit }})
                        </span>
                    </div>
                    <div class="fy-details-modal__export-actions">
                        <button type="button"
                            class="btn btn-sm brownz-btn-yellow-d"
                            @click="printYearDetails()">
                            <i class="fa fa-print"></i> {{ __('Print') }}
                        </button>
                        <button type="button"
                            class="btn btn-sm btn-outline-dark fy-details-export-pdf"
                            @click="exportYearDetailsPDF()">
                            <i class="fa fa-file-pdf-o"></i> {{ __('PDF') }}
                        </button>
                        <button type="button"
                            class="btn btn-sm btn_dark-x"
                            @click="exportYearDetailsExcel()">
                            <i class="fa fa-file-excel-o"></i> {{ __('Excel') }}
                        </button>
                    </div>
                </div>

                <div class="fy-details-modal__body">
                    <div class="fy-details-modal__section-title">
                        {{ __('Closing Voucher') }}
                        <span v-if="yearDetails.closing_voucher">
                            - @{{ yearDetails.closing_voucher.voucher_no }}
                            (@{{ formatDate(yearDetails.closing_voucher.date) }})
                        </span>
                    </div>
                    <p class="fy-details-modal__section-note" v-if="yearDetails.closing_voucher">
                        @{{ yearDetails.closing_voucher.description }}
                    </p>
                    <div class="fy-details-modal__table-wrap" v-if="yearDetails.closing_voucher">
                        <table class="table table-sm brownz-table-list-v1">
                            <thead>
                                <tr>
                                    <th>{{ __('Account') }}</th>
                                    <th>{{ __('Remarks') }}</th>
                                    <th class="text-right">{{ __('Debit') }}</th>
                                    <th class="text-right">{{ __('Credit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!yearDetails.closing_voucher.lines.length">
                                    <td colspan="4">{{ __('No closing voucher lines') }}</td>
                                </tr>
                                <tr v-for="(row, index) in yearDetails.closing_voucher.lines" :key="'close-line-' + index">
                                    <td>@{{ row.account_name }}</td>
                                    <td>@{{ row.remarks || '-' }}</td>
                                    <td class="text-right">@{{ row.debit }}</td>
                                    <td class="text-right">@{{ row.credit }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">{{ __('Total') }}</th>
                                    <th class="text-right">@{{ yearDetails.closing_voucher.total_debit }}</th>
                                    <th class="text-right">@{{ yearDetails.closing_voucher.total_credit }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <p class="fy-details-modal__section-note" v-else>{{ __('No closing voucher recorded for this year.') }}</p>

                    <div class="fy-details-modal__section-title">
                        {{ __('Opening Balance Audit') }}
                        <span v-if="yearDetails.opening_balance.voucher_no">
                            - @{{ yearDetails.opening_balance.voucher_no }}
                        </span>
                    </div>
                    <div class="fy-details-modal__table-wrap"
                        v-if="yearDetails.opening_balance.accounts.length">
                        <table class="table table-sm brownz-table-list-v1">
                            <thead>
                                <tr>
                                    <th>{{ __('Account') }}</th>
                                    <th class="text-right">{{ __('Debit') }}</th>
                                    <th class="text-right">{{ __('Credit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="row in yearDetails.opening_balance.accounts" :key="'ob-detail-' + row.account_id">
                                    <td>@{{ row.name }}</td>
                                    <td class="text-right">@{{ row.debit }}</td>
                                    <td class="text-right">@{{ row.credit }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>{{ __('Total') }}</th>
                                    <th class="text-right">@{{ yearDetails.opening_balance.total_debit }}</th>
                                    <th class="text-right">@{{ yearDetails.opening_balance.total_credit }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <p class="fy-details-modal__section-note" v-else>{{ __('No opening balance audit for this year.') }}</p>
                </div>

                <div class="fy-details-modal__footer">
                    <button type="button" class="btn btn-secondary btn-sm" @click="$modal.hide('year-details-modal')">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </modal>
    </div>
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
                years: [],
                closePreview: null,
                closeAuthPassword: '',
                closePasswordError: '',
                openPreview: null,
                openingBalanceDetails: null,
                yearDetails: null,
                selectedYear: null,
                selectedYearForDetails: null,
                selectedYearForOpeningBalance: null,
                permissions: @json($permissions),
            },
            computed: {
                canOpenNextYear() {
                    if (!this.years.length) {
                        return false;
                    }
                    const hasOpenYear = this.years.some(year => year.status === 'open');
                    const hasClosedYear = this.years.some(year => year.status === 'closed');
                    return hasClosedYear && !hasOpenYear;
                }
            },
            mounted() {
                this.fetchYears();
            },
            methods: {
                notify(msg, type = 'success') {
                    if (typeof js_notify === 'function') {
                        js_notify(msg, type);
                    } else if (typeof toastr !== 'undefined' && typeof toastr[type] === 'function') {
                        toastr[type](msg);
                    } else {
                        alert(msg);
                    }
                },
                formatDate(date) {
                    if (!date) return '';
                    return moment(date).format('DD/MM/YYYY');
                },
                fetchYears() {
                    this.loading = true;
                    axios.get("{{ route('financial-year.list') }}")
                        .then(response => {
                            this.years = response.data.data || [];
                            if (response.data.permissions) {
                                this.permissions = response.data.permissions;
                            }
                        })
                        .catch(() => {
                            this.notify('Unable to load financial years.', 'error');
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },
                initializeYear() {
                    if (!confirm('Initialize the current financial year (1 Apr - 31 Mar)?')) {
                        return;
                    }
                    this.loading = true;
                    axios.post("{{ route('financial-year.initialize') }}")
                        .then(response => {
                            this.notify(response.data.message, 'success');
                            this.years = response.data.data || [];
                        })
                        .catch(error => {
                            this.notify(error.response?.data?.message || 'Unable to initialize financial year.', 'error');
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },
                previewClose(year) {
                    this.selectedYear = year;
                    this.closeAuthPassword = '';
                    this.closePasswordError = '';
                    this.loading = true;
                    axios.post("{{ route('financial-year.preview-close') }}", {
                            financial_year_id: year.id
                        })
                        .then(response => {
                            this.closePreview = response.data.data;
                            this.$modal.show('close-preview-modal');
                            this.$nextTick(() => {
                                const input = document.getElementById('close_auth_password');
                                if (input) {
                                    input.focus();
                                }
                            });
                        })
                        .catch(error => {
                            this.notify(error.response?.data?.message || 'Unable to preview year close.', 'error');
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },
                cancelClosePreview() {
                    this.$modal.hide('close-preview-modal');
                    this.closePreview = null;
                    this.closeAuthPassword = '';
                    this.closePasswordError = '';
                },
                confirmClose() {
                    if (!this.selectedYear) {
                        return;
                    }
                    this.closePasswordError = '';
                    if (!this.closeAuthPassword || !String(this.closeAuthPassword).trim()) {
                        this.closePasswordError = 'Password is required.';
                        return;
                    }
                    this.loading = true;
                    axios.post("{{ route('financial-year.close') }}", {
                            financial_year_id: this.selectedYear.id,
                            auth_password: this.closeAuthPassword
                        })
                        .then(response => {
                            this.notify(response.data.message, 'success');
                            this.$modal.hide('close-preview-modal');
                            this.closePreview = null;
                            this.selectedYear = null;
                            this.closeAuthPassword = '';
                            this.closePasswordError = '';
                            this.years = response.data.data || [];
                        })
                        .catch(error => {
                            const message = error.response?.data?.message
                                || error.response?.data?.errors?.auth_password?.[0]
                                || 'Unable to close financial year.';
                            if (error.response?.data?.errors?.auth_password || /password/i.test(message)) {
                                this.closePasswordError = message;
                                this.closeAuthPassword = '';
                                this.$nextTick(() => {
                                    const input = document.getElementById('close_auth_password');
                                    if (input) {
                                        input.focus();
                                    }
                                });
                            } else {
                                this.notify(message, 'error');
                            }
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },
                previewOpenYear() {
                    const lastClosed = this.years.find(year => year.status === 'closed');
                    this.selectedYear = lastClosed || null;
                    this.loading = true;
                    axios.post("{{ route('financial-year.preview-open') }}", {
                            financial_year_id: lastClosed ? lastClosed.id : null
                        })
                        .then(response => {
                            this.openPreview = response.data.data;
                            this.$modal.show('open-preview-modal');
                        })
                        .catch(error => {
                            this.notify(error.response?.data?.message || 'Unable to preview year opening.', 'error');
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },
                confirmOpen() {
                    if (!confirm('Open the next financial year and create the opening balance audit voucher?')) {
                        return;
                    }
                    this.loading = true;
                    axios.post("{{ route('financial-year.open') }}", {
                            financial_year_id: this.selectedYear ? this.selectedYear.id : null
                        })
                        .then(response => {
                            this.notify(response.data.message, 'success');
                            this.$modal.hide('open-preview-modal');
                            this.openPreview = null;
                            this.selectedYear = null;
                            this.years = response.data.data || [];
                        })
                        .catch(error => {
                            this.notify(error.response?.data?.message || 'Unable to open financial year.', 'error');
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },
                viewOpeningBalance(year) {
                    this.selectedYearForOpeningBalance = year;
                    this.loading = true;
                    axios.get("{{ route('financial-year.opening-balance') }}", {
                            params: {
                                financial_year_id: year.id
                            }
                        })
                        .then(response => {
                            this.openingBalanceDetails = response.data.data;
                            this.$modal.show('opening-balance-modal');
                        })
                        .catch(error => {
                            this.notify(error.response?.data?.message || 'Unable to load opening balance voucher.', 'error');
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },
                openingBalanceExportQuery() {
                    if (!this.selectedYearForOpeningBalance) {
                        return '';
                    }

                    return '?financial_year_id=' + encodeURIComponent(this.selectedYearForOpeningBalance.id);
                },
                printOpeningBalance() {
                    if (!this.selectedYearForOpeningBalance) {
                        return;
                    }

                    window.open(
                        "{{ route('financial-year.opening-balance.print') }}" + this.openingBalanceExportQuery(),
                        '_blank',
                        'noopener,noreferrer'
                    );
                },
                exportOpeningBalancePDF() {
                    if (!this.selectedYearForOpeningBalance) {
                        return;
                    }

                    window.location.href = "{{ route('financial-year.opening-balance.export-to-pdf') }}" + this.openingBalanceExportQuery();
                },
                exportOpeningBalanceExcel() {
                    if (!this.selectedYearForOpeningBalance) {
                        return;
                    }

                    window.location.href = "{{ route('financial-year.opening-balance.export-to-excel') }}" + this.openingBalanceExportQuery();
                },
                viewYearDetails(year) {
                    this.selectedYearForDetails = year;
                    this.loading = true;
                    axios.get("{{ route('financial-year.details') }}", {
                            params: {
                                financial_year_id: year.id
                            }
                        })
                        .then(response => {
                            this.yearDetails = response.data.data;
                            this.$modal.show('year-details-modal');
                        })
                        .catch(error => {
                            this.notify(error.response?.data?.message || 'Unable to load financial year details.', 'error');
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },
                detailsExportQuery() {
                    if (!this.selectedYearForDetails) {
                        return '';
                    }

                    return '?financial_year_id=' + encodeURIComponent(this.selectedYearForDetails.id);
                },
                printYearDetails() {
                    if (!this.selectedYearForDetails) {
                        return;
                    }

                    window.open(
                        "{{ route('financial-year.details.print') }}" + this.detailsExportQuery(),
                        '_blank',
                        'noopener,noreferrer'
                    );
                },
                exportYearDetailsPDF() {
                    if (!this.selectedYearForDetails) {
                        return;
                    }

                    window.location.href = "{{ route('financial-year.details.export-to-pdf') }}" + this.detailsExportQuery();
                },
                exportYearDetailsExcel() {
                    if (!this.selectedYearForDetails) {
                        return;
                    }

                    window.location.href = "{{ route('financial-year.details.export-to-excel') }}" + this.detailsExportQuery();
                }
            }
        });
    </script>
@endpush
