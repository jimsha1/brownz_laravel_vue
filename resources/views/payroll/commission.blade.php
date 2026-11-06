@extends('payroll.layouts.base')

<style>
    .commission-page .list-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
    }

    .commission-page .list-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .commission-page .list-meta {
        font-size: 12px;
        color: #6c757d;
    }

    .commission-page .commission-table th,
    .commission-page .commission-table td {
        vertical-align: middle;
    }

    .commission-page .period-main {
        display: block;
        font-weight: 600;
        color: #1D1D1D;
    }

    .commission-page .period-sub {
        display: block;
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    .commission-page .amount-cell {
        font-variant-numeric: tabular-nums;
        font-weight: 600;
        white-space: nowrap;
    }

    .commission-page .status-pill {
        display: inline-flex;
        align-items: center;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        padding: 6px 12px;
        text-transform: capitalize;
        line-height: 1.2;
    }

    .commission-page .status-pill.is-paid {
        color: #199900;
        background: rgba(25, 153, 0, 0.15);
    }

    .commission-page .status-pill.is-unpaid {
        color: #D0342C;
        background: rgba(208, 52, 44, 0.12);
    }

    .commission-page .status-pill.is-partial {
        color: #8a6d00;
        background: rgba(230, 205, 127, 0.45);
    }

    .commission-page .manage-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
    }

    .commission-page .commission-row-actions {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .commission-page .voucher-link {
        display: inline-flex;
        align-items: center;
        font-size: 12px;
        font-weight: 600;
        color: #1D1D1D;
        background: #F0F0F3;
        border-radius: 999px;
        padding: 4px 10px;
        margin: 2px 4px 2px 0;
        text-decoration: none;
        white-space: nowrap;
    }

    .commission-page .voucher-link:hover {
        color: #52A8D9;
        text-decoration: none;
    }

    .commission-page .voucher-link.is-action {
        background: rgba(82, 168, 217, 0.18);
    }

    .commission-page .batch-voucher-links {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 4px;
    }

    .commission-page .manage-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #F0F0F3;
        color: #1D1D1D;
        text-decoration: none !important;
    }

    .commission-page .manage-btn:hover {
        background: #1D1D1D;
        color: #E6CD7F;
    }

    .commission-page .password-gate {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: 8px;
        min-width: 280px;
    }

    .commission-page .password-gate .form-control {
        flex: 0 0 150px;
        width: 150px;
        max-width: 150px;
        height: 34px;
        padding: 4px 10px;
        font-size: 13px;
    }

    .commission-page .password-gate .btn_dark-x,
    .commission-page .password-gate .brownz-btn-yellow-d {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        min-width: 56px;
        height: 34px;
        padding: 4px 12px;
        font-size: 13px;
        line-height: 1.2;
        cursor: pointer;
    }

    .commission-page .empty-state {
        text-align: center;
        padding: 36px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }

    #commissionModal .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }

    #commissionModal .modal-header {
        border-bottom: none;
        padding: 18px 20px 8px;
        align-items: flex-start;
    }

    #commissionModal .modal-title {
        color: #1D1D1D;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 2px;
    }

    #commissionModal .commission-subtitle {
        margin: 0;
        font-size: 13px;
        color: #6c757d;
    }

    #commissionModal .modal-body {
        padding: 8px 20px 12px;
    }

    #commissionModal .commission-section {
        background: #F7F7F9;
        border-radius: 14px;
        padding: 14px 14px 6px;
        margin-bottom: 12px;
    }

    #commissionModal .commission-section-title {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #6c757d;
        margin: 0 0 10px;
    }

    #commissionModal .form-label {
        font-size: 13px;
        color: #4a4a4a;
        font-weight: 500;
        margin-bottom: 4px;
    }

    #commissionModal .overlap-alert {
        background: rgba(208, 52, 44, 0.08);
        border: 1px solid rgba(208, 52, 44, 0.25);
        color: #D0342C;
        border-radius: 12px;
        padding: 10px 12px;
        font-size: 13px;
        margin-bottom: 12px;
    }

    #commissionModal .modal-commission-table th,
    #commissionModal .modal-commission-table td {
        vertical-align: middle;
    }

    #commissionModal .modal-commission-table .amount-input {
        max-width: 140px;
        background: #fff;
        font-variant-numeric: tabular-nums;
    }

    #commissionModal .is-zero {
        color: #9a9a9a;
    }

    #commissionModal .summary-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        background: #1D1D1D;
        color: #fff;
        border-radius: 12px;
        padding: 12px 16px;
        margin-top: 10px;
    }

    #commissionModal .summary-bar .label {
        color: #E6CD7F;
        font-size: 12px;
        margin-bottom: 2px;
    }

    #commissionModal .summary-bar .value {
        font-size: 1.15rem;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }

    #commissionModal .modal-footer {
        border-top: none;
        padding: 8px 20px 18px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    #commissionModal .modal-empty {
        text-align: center;
        padding: 24px 12px;
        color: #6c757d;
        background: #F7F7F9;
        border-radius: 14px;
    }

    #commissionPayModal .modal-dialog {
        max-width: min(520px, 96vw);
        margin: 0.5rem auto;
        max-height: calc(100vh - 1rem);
    }

    #commissionPayModal.modal {
        overflow-x: hidden;
        overflow-y: auto;
        z-index: 1065;
    }

    #commissionPayModal .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        max-height: calc(100vh - 1rem);
        display: flex;
        flex-direction: column;
    }

    #commissionPayModal .modal-header {
        border-bottom: none;
        padding: 16px 20px 8px;
        align-items: flex-start;
        flex-shrink: 0;
    }

    #commissionPayModal .modal-title {
        color: #1D1D1D;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 2px;
    }

    #commissionPayModal .pay-subtitle {
        margin: 0;
        font-size: 13px;
        color: #6c757d;
    }

    #commissionPayModal .modal-body {
        padding: 8px 20px 12px;
        overflow-y: auto;
        overflow-x: hidden;
        flex: 1 1 auto;
        max-height: calc(100vh - 1rem - 140px);
        -webkit-overflow-scrolling: touch;
    }

    #commissionPayModal .pay-section {
        background: #F7F7F9;
        border-radius: 14px;
        padding: 12px 12px 4px;
        margin-bottom: 4px;
    }

    #commissionPayModal .pay-section-title {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #6c757d;
        margin: 0 0 8px;
    }

    #commissionPayModal .form-group {
        margin-bottom: 10px;
    }

    #commissionPayModal .form-label {
        font-size: 13px;
        color: #4a4a4a;
        font-weight: 500;
        margin-bottom: 4px;
    }

    #commissionPayModal .form-control {
        height: 38px;
        padding: 6px 12px;
        font-size: 13px;
    }

    #commissionPayModal .due-hero {
        background: #1D1D1D;
        color: #fff;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 10px;
    }

    #commissionPayModal .due-hero .label {
        color: #E6CD7F;
        font-size: 12px;
        margin-bottom: 2px;
    }

    #commissionPayModal .due-hero .value {
        font-size: 1.25rem;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }

    #commissionPayModal .modal-footer {
        border-top: 1px solid #f0f0f0;
        padding: 12px 20px 14px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        flex-shrink: 0;
        background: #fff;
        margin: 0;
    }

    @media (max-width: 768px) {
        #commissionPayModal .modal-dialog {
            max-width: 96vw;
            margin: 0.4rem auto;
        }

        #commissionPayModal .modal-body {
            padding: 6px 14px 10px;
            max-height: calc(100vh - 0.8rem - 130px);
        }

        #commissionPayModal .modal-footer {
            padding: 10px 14px 12px;
        }
    }
</style>

@section('content')
    <div id="app" class="commission-page">

        <div class="fbox-shadow mt-2 fd-card respo-margin-top">
            <div class="card-body">
                <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
                    {{ __('Commission') }}
                </h5>

                <div class="row align-items-center br-row-gap-2">
                    <div class="col-lg-3 col-md-6">
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <?php $months = getMonths(); ?>
                            <select v-model="filter_month" class="form-control brawnz-search-bar-in"
                                @change="getCommissionList()">
                                <option value="">{{ __('All Months') }}</option>
                                @foreach ($months as $month)
                                    <option value="{{ $month }}">{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <?php $years = getYears(); ?>
                            <select v-model="filter_year" class="form-control brawnz-search-bar-in"
                                @change="getCommissionList()">
                                <option value="">{{ __('All Years') }}</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-primary btn_dark-x mr-2"
                            v-on:click="getCommissionList()">
                            <i class="fa fa-search"></i> {{ __('Search') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                            v-on:click="resetFilter()">
                            <i class="fa fa-refresh"></i> {{ __('Reset') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="fbox-shadow mt-3 fd-card respo-margin-top">
            <div class="m-3" style="overflow: auto">
                <div class="list-toolbar">
                    <div>
                        <p class="list-title">{{ __('Commission Batches') }}</p>
                        <p class="list-meta mb-0" v-if="pagination.total">
                            @{{ pagination.total }} {{ __('result(s)') }}
                        </p>
                    </div>
                    <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">
                        @can('commission_create')
                            <template v-if="passwordRowId === 'new'">
                                <div class="password-gate">
                                    <input type="password" class="form-control"
                                        v-model="passwordInput"
                                        placeholder="{{ __('Password') }}"
                                        @keyup.enter="submitNewPassword()">
                                    <button type="button" class="btn_dark-x"
                                        v-on:click="submitNewPassword()">
                                        {{ __('OK') }}
                                    </button>
                                    <button type="button" class="brownz-btn-yellow-d"
                                        v-on:click="cancelPasswordInput">
                                        {{ __('Cancel') }}
                                    </button>
                                </div>
                            </template>
                            <button v-else type="button" class="btn_dark-x"
                                v-on:click="showPasswordInput('new')">
                                <i class="fa fa-plus"></i> {{ __('New') }}
                            </button>
                        @endcan
                    </div>
                </div>

                <table class="table brownz-table-list-v1 commission-table" v-if="commissionList.length">
                    <thead>
                        <tr>
                            <th style="width: 56px;">#</th>
                            <th>{{ __('Period') }}</th>
                            <th>{{ __('Employees') }}</th>
                            <th>{{ __('Total Commission') }}</th>
                            <th>{{ __('Paid') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Voucher') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(data, index) in commissionList" :key="data.commission_master_id">
                            <td>@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</td>
                            <td>
                                <span class="period-main">
                                    @{{ formatDateDisplay(data.start_date) }} – @{{ formatDateDisplay(data.end_date) }}
                                </span>
                                <span class="period-sub">@{{ formatMonthYear(data.start_date) }}</span>
                            </td>
                            <td>@{{ data.employees_count || 0 }}</td>
                            <td class="amount-cell">₹ @{{ Number(data.total_commission_amount || 0).toFixed(2) }}</td>
                            <td class="amount-cell">₹ @{{ Number(data.total_paid_amount || 0).toFixed(2) }}</td>
                            <td>
                                <span class="status-pill" :class="paymentStatusClass(data.payment_summary)">
                                    @{{ data.payment_summary || '—' }}
                                </span>
                            </td>
                            <td>
                                <div class="batch-voucher-links">
                                    <a v-if="batchDue(data) > 0"
                                        :href="batchPaymentVoucherUrl(data)"
                                        class="voucher-link is-action"
                                        title="{{ __('Create payment voucher for all due commissions in this batch') }}">
                                        <i class="fa fa-file-text-o"></i> {{ __('Voucher') }}
                                    </a>
                                    <template v-if="data.payment_vouchers && data.payment_vouchers.length">
                                        <a v-for="voucher in data.payment_vouchers"
                                            :key="'batch-voucher-' + voucher.id"
                                            :href="voucher.view_url"
                                            class="voucher-link is-view"
                                            target="_blank"
                                            :title="voucher.voucher_no || ('Voucher #' + voucher.id)">
                                            @{{ voucher.voucher_no || ('#' + voucher.id) }}
                                        </a>
                                    </template>
                                    <span v-if="batchDue(data) <= 0 && !(data.payment_vouchers && data.payment_vouchers.length)">—</span>
                                </div>
                            </td>
                            <td>
                                <div class="manage-actions">
                                    @can('commission_edit')
                                        <template v-if="passwordRowId === data.commission_master_id">
                                            <div class="password-gate">
                                                <input type="password" class="form-control"
                                                    v-model="passwordInput"
                                                    placeholder="{{ __('Password') }}"
                                                    @keyup.enter="submitEditPassword(data)">
                                                <button type="button" class="btn_dark-x"
                                                    v-on:click="submitEditPassword(data)">
                                                    {{ __('OK') }}
                                                </button>
                                                <button type="button" class="brownz-btn-yellow-d"
                                                    v-on:click="cancelPasswordInput">
                                                    {{ __('Cancel') }}
                                                </button>
                                            </div>
                                        </template>
                                        <a v-else href="javascript:void(0)" class="manage-btn"
                                            title="{{ __('Edit') }}"
                                            v-on:click="showPasswordInput(data.commission_master_id)">
                                            <i aria-hidden="true" class="fa fa-pencil-square-o"></i>
                                        </a>
                                    @endcan
                                    @can('commission_view')
                                        <a href="javascript:void(0)" class="manage-btn" title="{{ __('View') }}"
                                            v-on:click="popupModal(data, true)">
                                            <i aria-hidden="true" class="fa fa-eye"></i>
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="empty-state" v-else>
                    <div>{{ __('No commission batches found for the selected filters.') }}</div>
                </div>
            </div>

            <nav aria-label="pagination">
                <ul class="pagination justify-content-end">
                    <li class="page-item" v-if="pagination.current_page > 1">
                        <a class="page-link" href="#" aria-label="Previous"
                            v-on:click.prevent="changePage(pagination.current_page - 1)">
                            <span aria-hidden="true">«</span>
                        </a>
                    </li>
                    <li class="page-item" v-for="page in pagesNumber"
                        v-bind:class="[ page == isActived ? 'active' : '']">
                        <a class="page-link" href="#" v-on:click.prevent="changePage(page)">@{{ page }}</a>
                    </li>
                    <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                        <a class="page-link" href="#" aria-label="Next"
                            v-on:click.prevent="changePage(pagination.current_page + 1)">
                            <span aria-hidden="true">»</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

                {{-- Generate / View Commission Modal --}}
                <div class="modal fade brownz-custom-popup" id="commissionModal" tabindex="-1"
                    aria-labelledby="commissionModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div>
                                    <h4 class="modal-title">@{{ modelHeading }}</h4>
                                    <p class="commission-subtitle" v-if="start_date && end_date">
                                        {{ __('Period') }}:
                                        @{{ formatDateDisplay(start_date) }} {{ __('To') }}
                                        @{{ formatDateDisplay(end_date) }}
                                    </p>
                                </div>
                                <button type="button" class="bootbox-close-button close" v-on:click="hide"
                                    data-dismiss="modal" aria-label="Close">
                                    ×
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="overlap-alert" v-if="overlapMessage">
                                    <i class="fa fa-exclamation-triangle"></i> @{{ overlapMessage }}
                                </div>

                                <form class="commission-section" @submit.prevent="validateBeforeSubmit"
                                    v-if="!formSubmitted && !viewMode" method="post">
                                    <p class="commission-section-title">{{ __('Date Range') }}</p>
                                    <div class="row align-items-end">
                                        <div class="col-md-3 col-sm-12 form-group">
                                            <label for="start_date" class="form-label">
                                                {{ __('Start Date') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="form-control" v-model="start_date"
                                                v-validate.initial="start_date" data-rules="required"
                                                id="start_date" @change="overlapMessage = ''">
                                            <span v-if="formErrors['start_date']"
                                                class="error text-danger">@{{ formErrors['start_date'] }}</span>
                                            <p class="text-danger mb-0" v-if="errors.has('start_date')">
                                                @{{ errors.first('start_date') }}
                                            </p>
                                        </div>

                                        <div class="col-md-3 col-sm-12 form-group">
                                            <label for="end_date" class="form-label">
                                                {{ __('End Date') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="form-control" v-model="end_date"
                                                v-validate.initial="end_date" data-rules="required"
                                                id="end_date" @change="overlapMessage = ''">
                                            <span v-if="formErrors['end_date']"
                                                class="error text-danger">@{{ formErrors['end_date'] }}</span>
                                            <p class="text-danger mb-0" v-if="errors.has('end_date')">
                                                @{{ errors.first('end_date') }}
                                            </p>
                                        </div>

                                        <div class="col-md-3 col-sm-12 form-group">
                                            <label for="auth_password" class="form-label">
                                                {{ __('Your Password') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="password" class="form-control" v-model="auth_password"
                                                id="auth_password" autocomplete="current-password"
                                                placeholder="{{ __('Confirm with password') }}">
                                        </div>

                                        <div class="col-md-3 col-sm-12 form-group text-md-right">
                                            <button type="submit" v-bind:disabled="submit_button_flag"
                                                class="btn_dark-x">
                                                <i class="fa fa-check"></i> {{ __('Generate') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <div v-if="allCommissionList.length">
                                    <div class="table-responsive">
                                        <table class="table brownz-table-list-v1 modal-commission-table mb-0">
                                            <thead class="bg-white sticky-top" style="top: -4px">
                                                <tr>
                                                    <th style="width: 48px;">#</th>
                                                    <th>{{ __('Employee') }}</th>
                                                    <th>{{ __('Commission Amount') }}</th>
                                                    <th>{{ __('Paid') }}</th>
                                                    <th>{{ __('Due') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                    <th>{{ __('Voucher') }}</th>
                                                    <th v-if="viewMode" class="text-center">{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(data, index) in allCommissionList" :key="index"
                                                    :class="{ 'is-zero': Number(data.commissionInfo || 0) <= 0 }">
                                                    <td>@{{ index + 1 }}</td>
                                                    <td>@{{ data.name || '—' }}</td>
                                                    <td>
                                                        <input type="number" step="0.01"
                                                            v-model="data.commissionInfo"
                                                            class="form-control amount-input"
                                                            :disabled="viewMode" />
                                                    </td>
                                                    <td class="amount-cell">
                                                        ₹ @{{ parseFloat(data.paid_amount || 0).toFixed(2) }}
                                                    </td>
                                                    <td class="amount-cell">
                                                        ₹ @{{ commissionDue(data).toFixed(2) }}
                                                    </td>
                                                    <td>
                                                        <span class="status-pill"
                                                            :class="paymentStatusClass(data.payment_status)">
                                                            @{{ data.payment_status || 'unpaid' }}
                                                        </span>
                                                    </td>
                                                    <td class="amount-cell">
                                                        <template v-if="commissionVouchers(data).length">
                                                            <a v-for="voucher in commissionVouchers(data)"
                                                                :key="'row-voucher-' + voucher.id"
                                                                :href="voucher.view_url"
                                                                class="voucher-link"
                                                                target="_blank"
                                                                :title="voucher.voucher_no || ('Voucher #' + voucher.id)">
                                                                @{{ voucher.voucher_no || ('#' + voucher.id) }}
                                                            </a>
                                                        </template>
                                                        <span v-else>—</span>
                                                    </td>
                                                    <td v-if="viewMode" class="text-center">
                                                        <template
                                                            v-if="passwordRowId === ('pay-' + data.commission_id)">
                                                            <div class="password-gate" style="justify-content: center;">
                                                                <input type="password" class="form-control"
                                                                    v-model="passwordInput"
                                                                    placeholder="{{ __('Password') }}"
                                                                    @keyup.enter="submitPayPassword(data)">
                                                                <button type="button" class="btn_dark-x"
                                                                    v-on:click="submitPayPassword(data)">
                                                                    {{ __('OK') }}
                                                                </button>
                                                                <button type="button" class="brownz-btn-yellow-d"
                                                                    v-on:click="cancelPasswordInput">
                                                                    {{ __('Cancel') }}
                                                                </button>
                                                            </div>
                                                        </template>
                                                        <div v-else-if="commissionDue(data) > 0"
                                                            class="commission-row-actions">
                                                            <a :href="paymentVoucherUrl(data)"
                                                                class="btn brownz-btn-yellow-d"
                                                                style="padding: 6px 12px; font-size: 13px;"
                                                                title="{{ __('Create payment voucher') }}">
                                                                <i class="fa fa-file-text-o"></i>
                                                                {{ __('Voucher') }}
                                                            </a>
                                                            <button type="button" class="btn_dark-x"
                                                                style="padding: 6px 12px; font-size: 13px;"
                                                                v-on:click="showPasswordInput('pay-' + data.commission_id)">
                                                                {{ __('Pay') }}
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="summary-bar">
                                        <div>
                                            <div class="label">{{ __('Employees with commission') }}</div>
                                            <div class="value">@{{ eligibleCommissionCount }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="label">{{ __('Total Commission Amount') }}</div>
                                            <div class="value">₹ @{{ totalCommissionAmount.toFixed(2) }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-empty" v-else-if="!viewMode">
                                    {{ __('Choose a date range and click Generate to calculate commissions.') }}
                                </div>
                                <div class="modal-empty" v-else>
                                    {{ __('No commission rows found for this batch.') }}
                                </div>
                            </div>

                            <div class="modal-footer" v-if="!viewMode">
                                <button class="brownz-btn-yellow-d" type="button" v-on:click="hide">
                                    {{ __('Cancel') }}
                                </button>
                                <button type="button" v-on:click="saveAllCommissionData"
                                    v-bind:disabled="submit_button_flag || totalCommissionAmount <= 0"
                                    class="btn_dark-x">
                                    <i class="fa fa-check"></i> {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pay Commission Modal --}}
                <div class="modal fade brownz-custom-popup" id="commissionPayModal" tabindex="-1"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div>
                                    <h4 class="modal-title">{{ __('Pay Commission') }}</h4>
                                    <p class="pay-subtitle" v-if="pay_employee_name">
                                        @{{ pay_employee_name }}
                                    </p>
                                </div>
                                <button type="button" class="bootbox-close-button close" v-on:click="hidePayModal"
                                    data-dismiss="modal" aria-label="Close">×</button>
                            </div>
                            <div class="modal-body">
                                <div class="due-hero">
                                    <div class="label">{{ __('Due Amount') }}</div>
                                    <div class="value">₹ @{{ Number(pay_due || 0).toFixed(2) }}</div>
                                </div>

                                <div class="pay-section">
                                    <p class="pay-section-title">{{ __('Payment Details') }}</p>
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label">{{ __('Payment Amount') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0.01" class="form-control"
                                                v-model="pay_amount">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label">{{ __('Payment Date') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" v-model="pay_date">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label">{{ __('Payment Mode') }}
                                                <span class="text-danger">*</span></label>
                                            <select class="form-control" v-model="pay_mode">
                                                <option value="bank">{{ __('Bank') }}</option>
                                                <option value="cash">{{ __('Cash') }}</option>
                                            </select>
                                            <small class="text-muted d-block mt-1">
                                                {{ __('Available') }} @{{ pay_mode === 'cash' ? 'Cash' : 'Bank' }}:
                                                <strong>@{{ Number(payAvailableBalance).toFixed(2) }}</strong>
                                            </small>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label">{{ __('Your Password') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" v-model="auth_password"
                                                autocomplete="current-password"
                                                placeholder="{{ __('Confirm with password') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="brownz-btn-yellow-d" type="button" v-on:click="hidePayModal">
                                    {{ __('Cancel') }}
                                </button>
                                <button type="button" class="btn_dark-x" v-on:click="submitPayCommission"
                                    v-bind:disabled="pay_submit_flag">
                                    <i class="fa fa-check"></i> {{ __('Pay') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

    </div>

    <script>
        Vue.use(window["vue-js-modal"].default);
        var app = new Vue({
            el: '#app',

            data: {
                loading: false,
                base_url: "{{ '/' }}",

                allCommissionList: [],
                commissionList: [],
                masterList: [],

                commission_master_id: '',
                start_date: '',
                end_date: '',
                modelHeading: '',
                overlapMessage: '',
                auth_password: '',
                passwordInput: '',
                passwordRowId: null,
                cash_balance: {{ json_encode(app(\App\Service\AccountingService::class)->getCashOrBankBalance('cash')) }},
                bank_balance: {{ json_encode(app(\App\Service\AccountingService::class)->getCashOrBankBalance('bank')) }},

                pay_commission_id: '',
                pay_employee_name: '',
                pay_due: 0,
                pay_amount: '',
                pay_mode: 'bank',
                pay_date: '',
                pay_submit_flag: false,

                filter_end_date: '',
                filter_start_date: '',
                filter_month: '',
                filter_year: '',

                viewMode: false,
                formSubmitted: false,
                submit_button_flag: false,

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
                },

                totalCommissionAmount() {
                    return this.allCommissionList.reduce((sum, item) => {
                        let tot = sum + (Number(item.commissionInfo) || 0);
                        return Math.round(tot * 100) / 100;
                    }, 0);
                },

                eligibleCommissionCount() {
                    return this.allCommissionList.filter(item => (Number(item.commissionInfo) || 0) > 0).length;
                },

                payAvailableBalance() {
                    var mode = String(this.pay_mode || 'bank').toLowerCase();
                    if (mode === 'cash') {
                        return Math.max(0, parseFloat(this.cash_balance || 0));
                    }
                    return Math.max(0, parseFloat(this.bank_balance || 0));
                },
            },

            methods: {
                formatMonthYear(date) {
                    return date ? moment(date).format('MMM YYYY') : '—';
                },

                formatDateDisplay(date) {
                    return date ? moment(date).format('DD-MM-YYYY') : '—';
                },

                paymentStatusClass(status) {
                    const value = (status || 'unpaid').toLowerCase();
                    if (value === 'paid') return 'is-paid';
                    if (value === 'partial') return 'is-partial';
                    return 'is-unpaid';
                },

                commissionDue(row) {
                    const total = parseFloat(row.commission_amount || row.commissionInfo || 0);
                    const paid = parseFloat(row.paid_amount || 0);
                    return Math.max(0, total - paid);
                },

                batchDue(row) {
                    if (row.total_due_amount !== undefined && row.total_due_amount !== null) {
                        return Math.max(0, parseFloat(row.total_due_amount) || 0);
                    }

                    const total = parseFloat(row.total_commission_amount || 0);
                    const paid = parseFloat(row.total_paid_amount || 0);
                    return Math.max(0, total - paid);
                },

                batchPaymentVoucherUrl(row) {
                    const baseUrl = @json(route('tab.stock.stock.voucher.details', ['id' => 0, 'tab_name' => 'edit']));
                    return baseUrl + '?commission_master_id=' + encodeURIComponent(row.commission_master_id);
                },

                paymentVoucherUrl(row) {
                    const baseUrl = @json(route('tab.stock.stock.voucher.details', ['id' => 0, 'tab_name' => 'edit']));
                    return baseUrl + '?commission_id=' + encodeURIComponent(row.commission_id);
                },

                commissionVouchers(row) {
                    if (row.payment_vouchers && row.payment_vouchers.length) {
                        return row.payment_vouchers;
                    }

                    const vouchers = [];
                    const seen = {};

                    if (row.payments && row.payments.length) {
                        row.payments.forEach(function(payment) {
                            const group = payment.transaction_group;
                            if (group && group.id && !seen[group.id]) {
                                seen[group.id] = true;
                                vouchers.push({
                                    id: group.id,
                                    voucher_no: group.voucher_no,
                                    view_url: '/stock/stock-voucher/tab/' + group.id + '/info',
                                });
                            }
                        });
                    }

                    if (row.payment_voucher && row.payment_voucher.id && !seen[row.payment_voucher.id]) {
                        vouchers.push({
                            id: row.payment_voucher.id,
                            voucher_no: row.payment_voucher.voucher_no,
                            view_url: '/stock/stock-voucher/tab/' + row.payment_voucher.id + '/info',
                        });
                    }

                    return vouchers;
                },

                applyDefaultGenerateDates() {
                    this.start_date = moment().startOf('month').format('YYYY-MM-DD');
                    this.end_date = moment().format('YYYY-MM-DD');
                },

                validateBeforeSubmit(e) {
                    this.$validator.validateAll();
                    if (!this.errors.any()) {
                        if (!this.auth_password) {
                            this.overlapMessage = 'Password is required to generate commission.';
                            Vue.$toast.error(this.overlapMessage, 'Danger Alert');
                            return;
                        }
                        this.submitForm()
                    }
                },
                submitForm() {
                    this.addEditSubmitForm()
                },

                showPasswordInput(rowId) {
                    this.passwordInput = '';
                    this.passwordRowId = rowId;
                },

                cancelPasswordInput() {
                    this.passwordInput = '';
                    this.passwordRowId = null;
                },

                submitEditPassword(data) {
                    if (!this.passwordInput) {
                        Vue.$toast.error('Password is required.', 'Danger Alert');
                        return;
                    }

                    axios.post("{{ route('commission.auth-check') }}", {
                        password: this.passwordInput,
                        _token: "{{ csrf_token() }}",
                    }).then((response) => {
                        if (response.data.valid) {
                            const unlockedPassword = this.passwordInput;
                            this.cancelPasswordInput();
                            this.popupModal(data, false, unlockedPassword);
                        } else {
                            Vue.$toast.error(response.data.statusText || 'Incorrect password.', 'Danger Alert');
                        }
                    }).catch((xhr) => {
                        const msg = (xhr.response && xhr.response.data && xhr.response.data.statusText)
                            ? xhr.response.data.statusText
                            : 'Incorrect password.';
                        Vue.$toast.error(msg, 'Danger Alert');
                    });
                },

                submitNewPassword() {
                    if (!this.passwordInput) {
                        Vue.$toast.error('Password is required.', 'Danger Alert');
                        return;
                    }

                    axios.post("{{ route('commission.auth-check') }}", {
                        password: this.passwordInput,
                        _token: "{{ csrf_token() }}",
                    }).then((response) => {
                        if (response.data.valid) {
                            const unlockedPassword = this.passwordInput;
                            this.cancelPasswordInput();
                            this.popupModal(0, false, unlockedPassword);
                        } else {
                            Vue.$toast.error(response.data.statusText || 'Incorrect password.', 'Danger Alert');
                        }
                    }).catch((xhr) => {
                        const msg = (xhr.response && xhr.response.data && xhr.response.data.statusText)
                            ? xhr.response.data.statusText
                            : 'Incorrect password.';
                        Vue.$toast.error(msg, 'Danger Alert');
                    });
                },

                submitPayPassword(row) {
                    if (!this.passwordInput) {
                        Vue.$toast.error('Password is required.', 'Danger Alert');
                        return;
                    }

                    axios.post("{{ route('commission.auth-check') }}", {
                        password: this.passwordInput,
                        _token: "{{ csrf_token() }}",
                    }).then((response) => {
                        if (response.data.valid) {
                            const unlockedPassword = this.passwordInput;
                            this.cancelPasswordInput();
                            this.openPayModal(row, unlockedPassword);
                        } else {
                            Vue.$toast.error(response.data.statusText || 'Incorrect password.', 'Danger Alert');
                        }
                    }).catch((xhr) => {
                        const msg = (xhr.response && xhr.response.data && xhr.response.data.statusText)
                            ? xhr.response.data.statusText
                            : 'Incorrect password.';
                        Vue.$toast.error(msg, 'Danger Alert');
                    });
                },

                openPayModal(row, unlockedPassword) {
                    const due = Math.max(0, parseFloat(row.commission_amount || row.commissionInfo || 0) -
                        parseFloat(row.paid_amount || 0));
                    if (due <= 0) {
                        Vue.$toast.error('Nothing pending to pay.', 'Danger Alert');
                        return;
                    }

                    this.pay_commission_id = row.commission_id;
                    this.pay_employee_name = row.name || row.emp_name || '';
                    this.pay_due = due;
                    this.pay_amount = due.toFixed(2);
                    this.pay_mode = 'bank';
                    this.pay_date = moment().format('YYYY-MM-DD');
                    this.auth_password = unlockedPassword || '';
                    this.pay_submit_flag = false;
                    $('#commissionPayModal').modal('show');
                    this.$nextTick(function() {
                        var payModal = document.getElementById('commissionPayModal');
                        if (payModal) {
                            payModal.style.zIndex = 1065;
                        }
                        var backdrops = document.querySelectorAll('.modal-backdrop');
                        if (backdrops.length) {
                            backdrops[backdrops.length - 1].style.zIndex = 1060;
                        }
                    });
                },

                hidePayModal() {
                    $('#commissionPayModal').modal('hide');
                    this.pay_commission_id = '';
                    this.pay_employee_name = '';
                    this.pay_due = 0;
                    this.pay_amount = '';
                    this.pay_mode = 'bank';
                    this.pay_date = '';
                    this.auth_password = '';
                    this.pay_submit_flag = false;
                },

                submitPayCommission() {
                    const paymentAmount = parseFloat(this.pay_amount);
                    if (isNaN(paymentAmount) || paymentAmount <= 0) {
                        Vue.$toast.error('Invalid payment amount.', 'Danger Alert');
                        return;
                    }

                    if (['cash', 'bank'].indexOf(String(this.pay_mode || '').toLowerCase()) === -1) {
                        Vue.$toast.error('Payment mode must be cash or bank.', 'Danger Alert');
                        return;
                    }

                    if (!this.pay_date) {
                        Vue.$toast.error('Payment date is required.', 'Danger Alert');
                        return;
                    }

                    if (!this.auth_password) {
                        Vue.$toast.error('Password is required to pay commission.', 'Danger Alert');
                        return;
                    }

                    if (paymentAmount > this.payAvailableBalance + 0.0001) {
                        Vue.$toast.error(
                            (this.pay_mode === 'cash' ? 'Cash' : 'Bank') +
                            ' has insufficient balance. Available: ' +
                            Number(this.payAvailableBalance).toFixed(2),
                            'Danger Alert'
                        );
                        return;
                    }

                    this.pay_submit_flag = true;

                    axios.post("{{ route('commission.pay.web') }}", {
                        commission_id: this.pay_commission_id,
                        payment_amount: paymentAmount,
                        payment_mode: this.pay_mode,
                        payment_date: this.pay_date,
                        auth_password: this.auth_password,
                        _token: "{{ csrf_token() }}",
                    }).then((response) => {
                        this.pay_submit_flag = false;
                        if (response.data.errorcode === 0) {
                            Vue.$toast.success(response.data.errormessage || 'Paid successfully', 'Success Alert');
                            this.hidePayModal();
                            this.fetchCommissionByMasterId(this.commission_master_id).then(() => {
                                if (this.masterList && Array.isArray(this.masterList.commissions)) {
                                    this.allCommissionList = this.masterList.commissions.map(
                                        commission => ({
                                            ...commission,
                                            name: commission.emp_name,
                                            commissionInfo: commission.commission_amount,
                                        }));
                                }
                            });
                            this.getCommissionList(this.pagination.current_page);
                        } else {
                            Vue.$toast.error(response.data.errormessage || 'Payment failed', 'Danger Alert');
                        }
                    }).catch((err) => {
                        this.pay_submit_flag = false;
                        const message = err.response?.data?.errormessage
                            || (err.response?.data?.errors?.auth_password
                                ? err.response.data.errors.auth_password[0]
                                : null)
                            || err.response?.data?.message
                            || 'Payment failed';
                        Vue.$toast.error(message, 'Danger Alert');
                    });
                },

                changePage: function(page) {
                    this.pagination.current_page = page;
                    app.getCommissionList(page);
                },

                show: function() {
                    $('#commissionModal').modal('show');
                },

                hide: function() {
                    $('#commissionModal').modal('hide');
                    this.overlapMessage = '';
                    this.auth_password = '';
                },

                addEditSubmitForm: function() {
                    this.submit_button_flag = true;
                    this.overlapMessage = '';
                    let url = "{{ route('commission.generate') }}";

                    let formData = new FormData();
                    formData.append('commission_master_id', this.commission_master_id || '');
                    formData.append('start_date', this.start_date);
                    formData.append('end_date', this.end_date);
                    formData.append('auth_password', this.auth_password || '');
                    formData.append('_token', "{{ csrf_token() }}");

                    axios.post(url, formData)
                        .then(response => {
                            if (response.data.errorcode === 0) {
                                this.loading = false;
                                this.submit_button_flag = false;
                                this.formErrors = {};
                                this.overlapMessage = '';
                                app.allCommissionList = response.data.data?.data || [];
                            } else {
                                this.loading = false;
                                this.submit_button_flag = false;
                                this.allCommissionList = [];
                                this.overlapMessage = response.data.errormessage ||
                                    'Unable to generate commission for this date range.';
                                Vue.$toast.error(this.overlapMessage, 'Danger Alert');
                            }
                        })
                        .catch(err => {
                            this.submit_button_flag = false;
                            this.formErrors = err.response?.data?.errors || {};
                            const message = err.response?.data?.errormessage
                                || (err.response?.data?.errors?.auth_password
                                    ? err.response.data.errors.auth_password[0]
                                    : null)
                                || err.response?.data?.message
                                || 'Something went wrong while generating.';
                            this.overlapMessage = message;
                            Vue.$toast.error(message, 'Danger Alert');
                        });
                },

                saveAllCommissionData: function() {
                    const eligibleCommissions = (this.allCommissionList || []).filter(item => {
                        return item.emp_id && (Number(item.commissionInfo) || 0) > 0;
                    });

                    if (!eligibleCommissions.length) {
                        Vue.$toast.error(
                            'No employees with commission amount to save. Set service commission % and use invoiced sales.',
                            'Danger Alert'
                        );
                        return;
                    }

                    if (!this.auth_password) {
                        this.overlapMessage = 'Password is required to save commission.';
                        Vue.$toast.error(this.overlapMessage, 'Danger Alert');
                        return;
                    }

                    this.submit_button_flag = true;
                    this.overlapMessage = '';
                    let url = "{{ route('commission.save') }}";

                    let payload = {
                        commission_master_id: this.commission_master_id || null,
                        auth_password: this.auth_password,
                        commissions: eligibleCommissions,
                        _token: "{{ csrf_token() }}",
                    };

                    axios.post(url, payload)
                        .then(response => {
                            if (response.data.errorcode === 0) {
                                this.loading = false;
                                this.start_date = '';
                                this.end_date = '';
                                this.allCommissionList = [];
                                this.commission_master_id = '';
                                this.auth_password = '';
                                this.overlapMessage = '';

                                Vue.$toast.success(response.data.errormessage, 'Success Alert');
                                this.submit_button_flag = false;
                                this.formErrors = {};
                                $('#commissionModal').modal('hide');
                                app.getCommissionList(app.pagination.current_page);

                            } else {
                                this.loading = false;
                                this.submit_button_flag = false;
                                this.overlapMessage = response.data.errormessage || 'Something went wrong.';
                                Vue.$toast.error(this.overlapMessage, 'Danger Alert');
                            }
                        })
                        .catch(err => {
                            this.submit_button_flag = false;
                            this.formErrors = err.response?.data?.errors || {};
                            const message = err.response?.data?.errormessage
                                || (err.response?.data?.errors?.auth_password
                                    ? err.response.data.errors.auth_password[0]
                                    : null)
                                || 'Something went wrong while saving.';
                            this.overlapMessage = message;
                            Vue.$toast.error(message, 'Danger Alert');
                        });
                },

                popupModal: function(data, isView, unlockedPassword) {
                    app.viewMode = !!isView;
                    app.overlapMessage = '';
                    app.auth_password = unlockedPassword || '';

                    if (data && data.commission_master_id > 0) {
                        app.commission_master_id = data.commission_master_id;
                        app.start_date = data.start_date;
                        app.end_date = data.end_date;

                        this.formSubmitted = false;
                        this.formErrors = {};
                        this.modelHeading = isView ? "View Commission" : "Edit Commission";

                        this.fetchCommissionByMasterId(data.commission_master_id)
                            .then(() => {
                                if (this.masterList && Array.isArray(this.masterList.commissions)) {
                                    this.allCommissionList = this.masterList.commissions.map(commission =>
                                        ({
                                            ...commission,
                                            name: commission.emp_name,
                                            commissionInfo: commission.commission_amount,
                                        }));
                                } else {
                                    this.allCommissionList = [];
                                }

                                $('#commissionModal').modal('show');
                            })
                            .catch(error => {
                                console.error("Error fetching commission data:", error);
                            });

                    } else {
                        app.commission_master_id = '';
                        app.allCommissionList = [];
                        app.auth_password = unlockedPassword || '';
                        app.applyDefaultGenerateDates();
                        app.formSubmitted = false;
                        app.formErrors = {};
                        app.modelHeading = "Generate Commission";
                        $('#commissionModal').modal('show');
                    }
                },

                fetchCommissionByMasterId: function(masterId) {
                    let url = `api/commissions/${masterId}`;

                    return axios.get(url, {})
                        .then(response => {
                            if (Array.isArray(response.data.data.data) && response.data.data.data.length >
                                0) {
                                this.masterList = response.data.data.data[0];
                            } else {
                                this.masterList = null;
                            }
                        })
                        .catch(error => {
                            console.error("Error fetching commission data:", error);
                            throw error;
                        });
                },

                getCommissionList: function(page = 1) {
                    url = "api/commission/list";

                    axios.post(url, {
                        "page": page,
                        "page_length": this.page_length,
                        "start_date": this.filter_start_date,
                        "end_date": this.filter_end_date,
                        "month": this.filter_month,
                        "year": this.filter_year,
                    }).then(function(response) {
                        app.commissionList = response.data.data.data.data;
                        app.pagination = response.data.data.pagination;
                        app.loading = false;
                    });
                },

                resetFilter: function() {
                    app.filter_start_date = '';
                    app.filter_end_date = '';
                    app.filter_month = '';
                    app.filter_year = '';
                    app.getCommissionList();
                },
            },
            mounted() {
                this.getCommissionList();
            }
        });
    </script>
@endsection
