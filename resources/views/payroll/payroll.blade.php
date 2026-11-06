@extends('payroll.layouts.base')

<style>
    .payroll-page .list-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
    }

    .payroll-page .list-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .payroll-page .list-meta {
        font-size: 12px;
        color: #6c757d;
    }

    .payroll-page .payroll-table th,
    .payroll-page .payroll-table td {
        vertical-align: middle;
    }

    .payroll-page .emp-name {
        display: block;
        font-weight: 600;
        color: #1D1D1D;
    }

    .payroll-page .emp-period {
        display: block;
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    .payroll-page .amount-cell {
        font-variant-numeric: tabular-nums;
        font-weight: 600;
        white-space: nowrap;
    }

    .payroll-page .info-stack {
        font-size: 12px;
        color: #4a4a4a;
        line-height: 1.45;
    }

    .payroll-page .info-stack b {
        color: #6c757d;
        font-weight: 500;
    }

    .payroll-page .period-separator td {
        background: #1D1D1D !important;
        color: #E6CD7F !important;
        border-radius: 8px;
        font-size: 13px;
        letter-spacing: 0.02em;
    }

    .payroll-page .manage-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
        justify-content: center;
    }

    .payroll-page .status-pill {
        display: inline-flex;
        align-items: center;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        padding: 6px 12px;
        text-transform: capitalize;
        line-height: 1.2;
    }

    .payroll-page .status-pill.is-paid {
        color: #199900;
        background: rgba(25, 153, 0, 0.15);
    }

    .payroll-page .status-pill.is-unpaid {
        color: #D0342C;
        background: rgba(208, 52, 44, 0.12);
    }

    .payroll-page .status-pill.is-partial {
        color: #8a6d00;
        background: rgba(230, 205, 127, 0.45);
    }

    .payroll-page .manage-btn {
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

    .payroll-page .manage-btn:hover {
        background: #1D1D1D;
        color: #E6CD7F;
    }

    .payroll-page .voucher-link {
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

    .payroll-page .voucher-link:hover {
        color: #52A8D9;
        text-decoration: none;
    }

    .payroll-page .voucher-link.is-action {
        background: rgba(82, 168, 217, 0.18);
    }

    .payroll-page .batch-voucher-links {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 4px;
    }

    .payroll-page .payroll-row-actions {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .payroll-page .period-main {
        display: block;
        font-weight: 600;
        color: #1D1D1D;
    }

    .payroll-page .period-sub {
        display: block;
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    .payroll-page .empty-state {
        text-align: center;
        padding: 36px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }

    .payroll-page .password-gate {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: 8px;
        min-width: 280px;
    }

    .payroll-page .password-gate .form-control {
        flex: 0 0 150px;
        width: 150px;
        max-width: 150px;
        height: 34px;
        padding: 4px 10px;
        font-size: 13px;
    }

    .payroll-page .password-gate .btn_dark-x,
    .payroll-page .password-gate .brownz-btn-yellow-d {
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

    #payrollModal .modal-dialog,
    #payrollEditModal .modal-dialog,
    #payRollScheduleModal .modal-dialog {
        max-width: min(920px, 96vw);
        margin: 0.5rem auto;
        max-height: calc(100vh - 1rem);
    }

    #payrollModal .modal-dialog.modal-xl {
        max-width: min(1280px, 98vw);
    }

    #payrollModal .payroll-batch-table-wrap {
        overflow-x: auto;
        overflow-y: visible;
        max-width: 100%;
        -webkit-overflow-scrolling: touch;
    }

    #payrollModal .modal-batch-table {
        font-size: 12px;
        margin-bottom: 0;
        min-width: 1080px;
    }

    #payrollModal .modal-batch-table th,
    #payrollModal .modal-batch-table td {
        padding: 8px 10px;
        vertical-align: middle;
    }

    #payrollModal .modal-batch-table .amount-cell {
        white-space: nowrap;
    }

    #payrollModal .modal-batch-table .col-employee {
        min-width: 110px;
        max-width: 150px;
        white-space: normal;
    }

    #payrollModal .modal-batch-table .col-action {
        position: sticky;
        right: 0;
        background: #fff;
        z-index: 2;
        box-shadow: -6px 0 8px -4px rgba(0, 0, 0, 0.08);
        min-width: 130px;
    }

    #payrollModal .modal-batch-table thead .col-action {
        z-index: 3;
    }

    #payrollModal .manage-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        justify-content: center;
        align-items: center;
    }

    #payrollModal .modal-content,
    #payrollEditModal .modal-content,
    #payRollScheduleModal .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        max-height: calc(100vh - 1rem);
        display: flex;
        flex-direction: column;
    }

    #payrollModal .modal-header,
    #payrollEditModal .modal-header,
    #payRollScheduleModal .modal-header {
        border-bottom: none;
        padding: 16px 20px 8px;
        align-items: flex-start;
        flex-shrink: 0;
    }

    #payrollModal .modal-title,
    #payrollEditModal .modal-title,
    #payRollScheduleModal .modal-title {
        color: #1D1D1D;
        font-size: 1.25rem;
        font-weight: 600;
    }

    #payrollModal .modal-body,
    #payrollEditModal .modal-body,
    #payRollScheduleModal .modal-body {
        padding: 8px 20px 12px;
        overflow-y: auto;
        overflow-x: hidden;
        flex: 1 1 auto;
        max-height: none;
        -webkit-overflow-scrolling: touch;
    }

    #payrollModal .payroll-section,
    #payrollEditModal .payroll-section,
    #payRollScheduleModal .payroll-section {
        background: #F7F7F9;
        border-radius: 14px;
        padding: 12px 12px 4px;
        margin-bottom: 10px;
    }

    #payrollModal .payroll-section-title,
    #payrollEditModal .payroll-section-title,
    #payRollScheduleModal .payroll-section-title {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #6c757d;
        margin: 0 0 8px;
    }

    #payrollModal .form-group,
    #payrollEditModal .form-group,
    #payRollScheduleModal .form-group {
        margin-bottom: 10px;
    }

    #payrollModal .form-label,
    #payrollEditModal .form-label,
    #payRollScheduleModal .form-label,
    #payrollEditModal label,
    #payRollScheduleModal label {
        font-size: 13px;
        color: #4a4a4a;
        font-weight: 500;
        margin-bottom: 4px;
    }

    #payrollModal .form-control,
    #payrollEditModal .form-control,
    #payRollScheduleModal .form-control {
        height: 38px;
        padding: 6px 12px;
        font-size: 13px;
    }

    #payRollScheduleModal textarea.form-control {
        height: auto;
        min-height: 58px;
        padding: 8px 12px;
    }

    #payrollModal .modal-footer,
    #payrollEditModal .modal-footer,
    #payRollScheduleModal .modal-footer {
        border-top: 1px solid #f0f0f0;
        padding: 12px 20px 14px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        flex-shrink: 0;
        background: #fff;
        margin: 0;
    }

    #payRollScheduleModal .modal-dialog {
        max-width: min(860px, 96vw);
    }

    #payRollScheduleModal.modal {
        overflow-x: hidden;
        overflow-y: auto;
    }

    #payRollScheduleModal .modal-content {
        max-height: calc(100vh - 1.25rem);
    }

    #payRollScheduleModal .modal-body {
        max-height: calc(100vh - 1.25rem - 140px);
    }

    #payRollScheduleModal .payment-history-title {
        font-size: 14px;
        font-weight: 600;
        color: #1D1D1D;
        margin: 10px 0 8px;
    }

    #payRollScheduleModal .table-responsive {
        max-height: min(200px, 28vh);
        overflow: auto;
        border: 1px solid #eee;
        border-radius: 12px;
        background: #fff;
    }

    #payRollScheduleModal .table-responsive thead th {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 1;
        box-shadow: 0 1px 0 #eee;
        font-size: 12px;
        white-space: nowrap;
    }

    #payRollScheduleModal .payroll-table th,
    #payRollScheduleModal .payroll-table td {
        padding: 8px 10px;
        font-size: 12px;
        vertical-align: middle;
    }

    @media (max-width: 768px) {
        #payrollModal .modal-dialog,
        #payrollEditModal .modal-dialog,
        #payRollScheduleModal .modal-dialog {
            max-width: 96vw;
            margin: 0.4rem auto;
            max-height: calc(100vh - 0.8rem);
        }

        #payrollModal .modal-body,
        #payrollEditModal .modal-body,
        #payRollScheduleModal .modal-body {
            padding: 6px 14px 10px;
        }

        #payrollModal .modal-footer,
        #payrollEditModal .modal-footer,
        #payRollScheduleModal .modal-footer {
            padding: 10px 14px 12px;
        }
    }
</style>

@section('content')
    <div id="app" class="payroll-page">

        <div class="fbox-shadow mt-2 fd-card respo-margin-top">
            <div class="card-body">
                <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
                    {{ __('Payroll') }}
                </h5>

                <div class="row align-items-center br-row-gap-2">
                    <div class="col-lg-3 col-md-6">
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <select v-model="filter_month" class="form-control brawnz-search-bar-in"
                                @change="getpayrollList(1)">
                                <option value="">{{ __('All Months') }}</option>
                                <?php $months = getMonths(); ?>
                                @foreach ($months as $month)
                                    <option value="{{ $month }}">{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <select v-model="filter_year" class="form-control brawnz-search-bar-in"
                                @change="getpayrollList(1)">
                                <option value="">{{ __('All Years') }}</option>
                                <?php $years = getYears(); ?>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-primary btn_dark-x mr-2"
                            v-on:click="getpayrollList(1)">
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
                        <p class="list-title">{{ __('Payroll Batches') }}</p>
                        <p class="list-meta mb-0" v-if="pagination.total">
                            @{{ pagination.total }} {{ __('result(s)') }}
                        </p>
                    </div>
                    <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">
                        @can('payroll_create')
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


                <table class="table brownz-table-list-v1 payroll-table" v-if="payrollBatchList.length">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ __('Period') }}</th>
                            <th scope="col">{{ __('Employees') }}</th>
                            <th scope="col">{{ __('Total Payable') }}</th>
                            <th scope="col">{{ __('Paid') }}</th>
                            <th scope="col">{{ __('Due') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col">{{ __('Voucher') }}</th>
                            <th scope="col">{{ __('Voucher Date') }}</th>
                            <th class="text-center" scope="col">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(data, index) in payrollBatchList" :key="data.payroll_master_id">
                            <td>@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</td>
                            <td>
                                <span class="period-main">@{{ data.month }} @{{ data.year }}</span>
                                <span class="period-sub">{{ __('Schedule') }}: @{{ formatDate(data.schedule_date) }}</span>
                            </td>
                            <td>@{{ data.employees_count || 0 }}</td>
                            <td class="amount-cell">₹ @{{ Number(data.total_payable_amount || 0).toFixed(2) }}</td>
                            <td class="amount-cell">₹ @{{ Number(data.total_paid_amount || 0).toFixed(2) }}</td>
                            <td class="amount-cell">₹ @{{ Number(data.total_due_amount || 0).toFixed(2) }}</td>
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
                                        title="{{ __('Create payment voucher for all due payroll in this batch') }}">
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
                            <td>@{{ formatDate(data.voucher_date) }}</td>
                            <td>
                                <div class="manage-actions">
                                    @can('payroll_create')
                                        <template v-if="passwordRowId === data.payroll_master_id">
                                            <div class="password-gate">
                                                <input type="password" class="form-control"
                                                    v-model="passwordInput"
                                                    placeholder="{{ __('Password') }}"
                                                    @keyup.enter="submitEditBatchPassword(data)">
                                                <button type="button" class="btn_dark-x"
                                                    v-on:click="submitEditBatchPassword(data)">
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
                                            v-on:click="showPasswordInput(data.payroll_master_id)">
                                            <i aria-hidden="true" class="fa fa-pencil-square-o"></i>
                                        </a>
                                    @endcan
                                    @can('payroll_create')
                                        <a href="javascript:void(0)" class="manage-btn" title="{{ __('View') }}"
                                            v-on:click="popupBatchModal(data, true)">
                                            <i aria-hidden="true" class="fa fa-eye"></i>
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="empty-state" v-else>
                    <div>{{ __('No payroll batches found for the selected filters.') }}</div>
                </div>

            </div>

            {{-- pagination --}}
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

        </div>

                    <div class="modal fade brownz-custom-popup" id="payrollModal" tabindex="-1"
                        aria-labelledby="payrollModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div>
                                        <h4 class="modal-title mb-1" id="payrollModalLabel">@{{ modelHeading }}</h4>
                                        <p class="mb-0 text-muted" style="font-size: 13px;" v-if="month && year">
                                            {{ __('Period') }}: @{{ month }} @{{ year }}
                                        </p>
                                    </div>
                                    <button type="button" class="bootbox-close-button close" v-on:click="hide"
                                        data-dismiss="modal" aria-label="Close">×</button>
                                </div>
                                <div class="modal-body">
                                    <div class="overlap-alert alert alert-warning py-2 px-3 mb-3" v-if="overlapMessage"
                                        style="border-radius: 12px;">
                                        <i class="fa fa-exclamation-triangle"></i> @{{ overlapMessage }}
                                    </div>

                                    <div class="payroll-section" v-if="!viewMode">
                                        <p class="payroll-section-title">{{ __('Period') }}</p>
                                        <div class="row">
                                            <div class="col-md-3 col-sm-12 form-group">
                                                <label for="month" class="form-label">{{ __('Month') }} <span
                                                        style="color:red">*</span></label>
                                                <?php $months = getMonths(); ?>
                                                <select v-model="month" class="form-control select-search" id="month"
                                                    :disabled="!!payroll_master_id">
                                                    <option value="">{{ __('-Select-') }}</option>
                                                    @foreach ($months as $month)
                                                        <option value="{{ $month }}">{{ $month }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-3 col-sm-12 form-group">
                                                <label for="year" class="form-label">{{ __('Year') }} <span
                                                        style="color:red">*</span></label>
                                                <?php $years = getYears(); ?>
                                                <select v-model="year" class="form-control select-search" id="year"
                                                    :disabled="!!payroll_master_id">
                                                    <option value="">{{ __('-Select-') }}</option>
                                                    @foreach ($years as $year)
                                                        <option value="{{ $year }}">{{ $year }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-3 col-sm-12 form-group">
                                                <label for="voucher_date" class="form-label">{{ __('Voucher Date') }}
                                                    <span style="color:red">*</span></label>
                                                <input type="date" class="form-control" v-model="voucher_date"
                                                    id="voucher_date">
                                            </div>

                                            <div class="col-md-3 col-sm-12 form-group">
                                                <label for="schedule_date" class="form-label">{{ __('Schedule Date') }}
                                                    <span style="color:red">*</span></label>
                                                <input type="date" class="form-control" v-model="schedule_date"
                                                    id="schedule_date">
                                            </div>

                                            <div class="col-md-4 col-sm-12 form-group">
                                                <label for="auth_password" class="form-label">{{ __('Your Password') }}
                                                    <span style="color:red">*</span></label>
                                                <input type="password" class="form-control" v-model="auth_password"
                                                    id="auth_password" autocomplete="current-password"
                                                    placeholder="{{ __('Confirm with password') }}">
                                            </div>

                                            <div class="col-md-4 col-sm-12 form-group d-flex align-items-end">
                                                <button type="button" v-on:click="generatePayrollPreview()"
                                                    v-bind:disabled="submit_button_flag" class="btn_dark-x">
                                                    <i class="fa fa-calculator"></i> {{ __('Generate') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="payrollPreviewList.length">
                                        <div class="table-responsive payroll-batch-table-wrap">
                                            <table class="table brownz-table-list-v1 mb-0 modal-batch-table">
                                                <thead class="bg-white sticky-top" style="top: -4px">
                                                    <tr>
                                                        <th>#</th>
                                                        <th class="col-employee">{{ __('Employee') }}</th>
                                                        <th>{{ __('Basic') }}</th>
                                                        <th>{{ __('Net') }}</th>
                                                        <th>{{ __('OT') }}</th>
                                                        <th>{{ __('Deduction') }}</th>
                                                        <th>{{ __('Payable') }}</th>
                                                        <th v-if="viewMode">{{ __('Paid') }}</th>
                                                        <th v-if="viewMode">{{ __('Due') }}</th>
                                                        <th v-if="viewMode">{{ __('Status') }}</th>
                                                        <th v-if="viewMode">{{ __('Voucher') }}</th>
                                                        <th v-if="viewMode" class="text-center col-action">{{ __('Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(row, index) in payrollPreviewList" :key="index">
                                                        <td>@{{ index + 1 }}</td>
                                                        <td class="col-employee">@{{ row.name || (row.employee && row.employee.name) || '—' }}</td>
                                                        <td class="amount-cell">₹ @{{ Number(row.basic_salary || 0).toFixed(2) }}</td>
                                                        <td class="amount-cell">₹ @{{ Number(row.net_salary || 0).toFixed(2) }}</td>
                                                        <td class="amount-cell">₹ @{{ Number(row.overtime_amount || 0).toFixed(2) }}</td>
                                                        <td class="amount-cell">₹ @{{ Number(row.deduction || 0).toFixed(2) }}</td>
                                                        <td class="amount-cell">₹ @{{ payrollTotal(row).toFixed(2) }}</td>
                                                        <td v-if="viewMode" class="amount-cell">
                                                            ₹ @{{ Number(row.paid_amount || 0).toFixed(2) }}
                                                        </td>
                                                        <td v-if="viewMode" class="amount-cell">
                                                            ₹ @{{ payrollDue(row).toFixed(2) }}
                                                        </td>
                                                        <td v-if="viewMode">
                                                            <span class="status-pill"
                                                                :class="paymentStatusClass(row.is_scheduled == 1 && payrollDue(row) <= 0 ? 'paid' : (row.paid_amount > 0 ? 'partial' : 'unpaid'))">
                                                                @{{ row.is_scheduled == 1 && payrollDue(row) <= 0 ? 'paid' : (row.paid_amount > 0 ? 'partial' : 'unpaid') }}
                                                            </span>
                                                        </td>
                                                        <td v-if="viewMode" class="amount-cell">
                                                            <template v-if="payrollVouchers(row).length">
                                                                <a v-for="voucher in payrollVouchers(row)"
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
                                                        <td v-if="viewMode" class="text-center col-action">
                                                            <div class="manage-actions">
                                                                @can('payroll_create')
                                                                    <template v-if="passwordRowId === row.payroll_id">
                                                                        <button type="button" class="btn_dark-x"
                                                                            style="padding: 4px 10px; font-size: 12px;"
                                                                            v-on:click="submitEditPassword(row)">
                                                                            {{ __('Edit') }}
                                                                        </button>
                                                                    </template>
                                                                    <button v-else-if="canEditPayroll(row)" type="button"
                                                                        class="brownz-btn-yellow-d"
                                                                        style="padding: 4px 10px; font-size: 12px;"
                                                                        v-on:click="showPasswordInput(row.payroll_id)">
                                                                        {{ __('Edit') }}
                                                                    </button>
                                                                @endcan
                                                                @can('payment_create')
                                                                    <div v-if="payrollDue(row) > 0" class="payroll-row-actions">
                                                                        <a :href="paymentVoucherUrl(row)"
                                                                            class="btn brownz-btn-yellow-d"
                                                                            style="padding: 4px 10px; font-size: 12px;"
                                                                            title="{{ __('Create payment voucher') }}">
                                                                            <i class="fa fa-file-text-o"></i>
                                                                            {{ __('Voucher') }}
                                                                        </a>
                                                                        <template v-if="passwordRowId === ('pay-' + row.payroll_id)">
                                                                            <button type="button" class="btn_dark-x"
                                                                                style="padding: 4px 10px; font-size: 12px;"
                                                                                v-on:click="submitPayPassword(row)">
                                                                                {{ __('Pay') }}
                                                                            </button>
                                                                        </template>
                                                                        <button v-else type="button" class="btn_dark-x"
                                                                            style="padding: 4px 10px; font-size: 12px;"
                                                                            v-on:click="showPasswordInput('pay-' + row.payroll_id)">
                                                                            {{ __('Pay') }}
                                                                        </button>
                                                                    </div>
                                                                    <button v-if="row.is_scheduled == 1" type="button"
                                                                        class="brownz-btn-yellow-d"
                                                                        style="padding: 4px 10px; font-size: 12px;"
                                                                        @click="downloadPayroll(row.payroll_id)">
                                                                        {{ __('Print') }}
                                                                    </button>
                                                                @endcan
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="summary-bar mt-3" style="background:#1D1D1D;color:#fff;border-radius:12px;padding:12px 16px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                                            <div>
                                                <div style="color:#E6CD7F;font-size:12px;">{{ __('Employees') }}</div>
                                                <div style="font-size:1.15rem;font-weight:700;">@{{ payrollPreviewList.length }}</div>
                                            </div>
                                            <div class="text-right">
                                                <div style="color:#E6CD7F;font-size:12px;">{{ __('Total Payable') }}</div>
                                                <div style="font-size:1.15rem;font-weight:700;">₹ @{{ totalPreviewPayable.toFixed(2) }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="empty-state" v-else-if="!viewMode">
                                        {{ __('Choose month/year and click Generate to preview payroll for all employees.') }}
                                    </div>
                                    <div class="empty-state" v-else>
                                        {{ __('No payroll rows found for this batch.') }}
                                    </div>
                                </div>
                                <div class="modal-footer" v-if="!viewMode">
                                    <button class="brownz-btn-yellow-d" type="button" v-on:click="hide">✖
                                        {{ __('Cancel') }}</button>
                                    <button type="button" v-on:click="savePayrollBatch()"
                                        v-bind:disabled="submit_button_flag || !payrollPreviewList.length"
                                        class="btn_dark-x">
                                        <i class="fa fa-check"></i> {{ __('Save') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Edit Generated Payroll Modal --}}
                    <div class="modal fade brownz-custom-popup" id="payrollEditModal" tabindex="-1"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div>
                                        <h4 class="modal-title mb-1">@{{ editModelHeading }}</h4>
                                        <p class="mb-0 text-muted" style="font-size: 13px;">
                                            {{ __('Adjust salary components before payment.') }}
                                        </p>
                                    </div>
                                    <button type="button" class="bootbox-close-button close" v-on:click="hideEdit"
                                        data-dismiss="modal" aria-label="Close">×</button>
                                </div>
                                <div class="modal-body">
                                    <div class="payroll-section">
                                        <p class="payroll-section-title">{{ __('Employee & Period') }}</p>
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>{{ __('Employee') }}</label>
                                                <input type="text" class="form-control" :value="edit_employee_name"
                                                    readonly>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label>{{ __('Month') }}</label>
                                                <input type="text" class="form-control" :value="edit_month" readonly>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label>{{ __('Year') }}</label>
                                                <input type="text" class="form-control" :value="edit_year" readonly>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>{{ __('Voucher Date') }} <span style="color:red">*</span></label>
                                                <input type="date" class="form-control" v-model="edit_voucher_date">
                                                <span v-if="editFormErrors['voucher_date']"
                                                    class="error text-danger">@{{ editFormErrors['voucher_date'][0] }}</span>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>{{ __('Schedule Date') }} <span style="color:red">*</span></label>
                                                <input type="date" class="form-control" v-model="edit_schedule_date">
                                                <span v-if="editFormErrors['schedule_date']"
                                                    class="error text-danger">@{{ editFormErrors['schedule_date'][0] }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="payroll-section">
                                        <p class="payroll-section-title">{{ __('Earnings') }}</p>
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label>{{ __('Basic Salary') }} <span style="color:red">*</span></label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    v-model="edit_basic_salary">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>{{ __('HRA') }}</label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    v-model="edit_HRA">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>{{ __('TA') }}</label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    v-model="edit_TA">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>{{ __('DA') }}</label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    v-model="edit_DA">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>{{ __('Special Allowance') }}</label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    v-model="edit_special_allowance">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>{{ __('OT Amount') }}</label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    v-model="edit_overtime_amount">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="payroll-section">
                                        <p class="payroll-section-title">{{ __('Deductions & Totals') }}</p>
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label>{{ __('TDS Amount') }}</label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    v-model="edit_TDS_amount">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>{{ __('Professional Tax') }}</label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    v-model="edit_professional_tax_amount">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>{{ __('Leave Deduction') }}</label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    v-model="edit_deduction">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>{{ __('Advance') }}</label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    v-model="edit_advance">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>{{ __('Net Salary') }}</label>
                                                <input type="text" class="form-control"
                                                    :value="Number(editComputedNet).toFixed(2)" readonly>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>{{ __('Payable Amount') }}</label>
                                                <input type="text" class="form-control"
                                                    :value="Number(editComputedPayable).toFixed(2)" readonly>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>{{ __('Your Password') }} <span style="color:red">*</span></label>
                                                <input type="password" class="form-control" v-model="auth_password"
                                                    autocomplete="current-password"
                                                    placeholder="{{ __('Confirm with password') }}">
                                                <span v-if="editFormErrors['auth_password']"
                                                    class="error text-danger">@{{ editFormErrors['auth_password'][0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="brownz-btn-yellow-d" type="button" v-on:click="hideEdit">✖
                                        {{ __('Cancel') }}</button>
                                    <button type="button" v-on:click="submitEditForm()"
                                        v-bind:disabled="edit_submit_button_flag" class="btn_dark-x">
                                        <i class="fa fa-check"></i> {{ __('Update') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade brownz-custom-popup" id="payRollScheduleModal" tabindex="-1"
                        aria-labelledby="payRollScheduleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div>
                                        <h4 class="modal-title mb-1" id="payRollScheduleModalLabel">@{{ modelHeading }}</h4>
                                        <p class="mb-0 text-muted" style="font-size: 13px;">
                                            {{ __('Record payment and review payment history.') }}
                                        </p>
                                    </div>
                                    <button type="button" class="bootbox-close-button close" v-on:click="hide1"
                                        data-dismiss="modal" aria-label="Close">×</button>
                                </div>
                                <div class="modal-body">
                                    <form id="payrollScheduleForm" @submit.prevent="validateBeforePayrollScheduleSubmit"
                                        v-if="!formScheduleSubmitted" method="post">
                                        <div class="payroll-section">
                                            <p class="payroll-section-title">{{ __('Payment Details') }}</p>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12 form-group">
                                                    <label for="payable_amount" class="form-label">{{ __('Payable Amount') }}
                                                        <span style="color:red">*</span></label>
                                                    <input type="text" class="form-control" v-model="payable_amount"
                                                        v-validate.initial="payable_amount" data-rules="required"
                                                        id="payable_amount" readonly>
                                                    <span v-if="formErrors['payable_amount']"
                                                        class="error text-danger">@{{ formErrors['payable_amount'] }}</span>
                                                    <p class="text-danger" v-if="errors.has('payable_amount')">
                                                        @{{ errors.first('payable_amount') }}</p>
                                                </div>

                                                <div class="col-md-6 col-sm-12 form-group">
                                                    <label for="payment_date" class="form-label">{{ __('Payment Date') }}
                                                        <span style="color:red">*</span></label>
                                                    <input type="date" class="form-control" v-model="payment_date"
                                                        v-validate.initial="payment_date" data-rules="required"
                                                        id="payment_date">
                                                    <span v-if="formErrors['payment_date']"
                                                        class="error text-danger">@{{ formErrors['payment_date'] }}</span>
                                                    <p class="text-danger" v-if="errors.has('payment_date')">
                                                        @{{ errors.first('payment_date') }}</p>
                                                </div>

                                                <div class="col-md-6 col-sm-12 form-group">
                                                    <label for="payment_amount" class="form-label">{{ __('Payment Amount') }}
                                                        <span style="color:red">*</span></label>
                                                    <input type="text" class="form-control" v-model="payment_amount"
                                                        v-validate.initial="payment_amount" data-rules="required"
                                                        id="payment_amount">
                                                    <span v-if="formErrors['payment_amount']"
                                                        class="error text-danger">@{{ formErrors['payment_amount'] }}</span>
                                                    <p class="text-danger" v-if="errors.has('payment_amount')">
                                                        @{{ errors.first('payment_amount') }}</p>
                                                </div>

                                                <div class="col-md-6 col-sm-12 form-group">
                                                    <label for="balance_amount" class="form-label">{{ __('Due Amount') }}
                                                        <span style="color:red">*</span></label>
                                                    <input type="text" class="form-control" v-model="balance_amount"
                                                        id="balance_amount" readonly>
                                                </div>

                                                <div class="col-md-6 col-sm-12 form-group">
                                                    <label for="payment_mode" class="form-label">{{ __('Payment Mode') }}
                                                        <span style="color:red">*</span></label>
                                                    <select class="form-control" v-model="payment_mode" id="payment_mode">
                                                        <option value="bank">{{ __('Bank') }}</option>
                                                        <option value="cash">{{ __('Cash') }}</option>
                                                    </select>
                                                    <small class="text-muted d-block mt-1">
                                                        {{ __('Available') }} @{{ payment_mode === 'cash' ? 'Cash' : 'Bank' }}:
                                                        <strong>@{{ Number(availablePaymentBalance).toFixed(2) }}</strong>
                                                    </small>
                                                    <small class="text-danger d-block" v-if="paymentExceedsBalance">
                                                        {{ __('Payment amount exceeds available balance.') }}
                                                    </small>
                                                </div>

                                                <div class="col-md-6 col-sm-12 form-group">
                                                    <label for="payment_auth_password" class="form-label">{{ __('Your Password') }}
                                                        <span style="color:red">*</span></label>
                                                    <input type="password" class="form-control" v-model="auth_password"
                                                        id="payment_auth_password" autocomplete="current-password"
                                                        placeholder="{{ __('Confirm with password') }}">
                                                    <span v-if="formErrors['auth_password']"
                                                        class="error text-danger">@{{ formErrors['auth_password'] }}</span>
                                                </div>

                                                <div class="col-md-12 col-sm-12 form-group">
                                                    <label for="narrations" class="form-label">{{ __('Narrations') }}</label>
                                                    <textarea class="form-control" v-model="narrations" id="narrations"
                                                        rows="2"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    <p class="payment-history-title">{{ __('Payment History') }}</p>
                                    <div class="table-responsive">
                                        <table class="table brownz-table-list-v1 payroll-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">{{ __('Payable Amount') }}</th>
                                                    <th scope="col">{{ __('Date') }}</th>
                                                    <th scope="col">{{ __('Amount') }}</th>
                                                    <th scope="col">{{ __('Balance') }}</th>
                                                    <th scope="col">{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-if="!paymentList.length">
                                                    <td colspan="6" class="text-center text-muted py-3">
                                                        {{ __('No payments recorded yet.') }}
                                                    </td>
                                                </tr>
                                                <tr v-for="(data, index) in paymentList" :key="data.payroll_payment_id">
                                                    <td>@{{ index + 1 }}</td>
                                                    <td class="amount-cell">@{{ data.payable_amount }}</td>
                                                    <td>@{{ data.payment_date }}</td>
                                                    <td class="amount-cell">@{{ data.payment_amount }}</td>
                                                    <td class="amount-cell">@{{ data.balance_amount }}</td>
                                                    <td class="hidden-print">
                                                        @can('payment_edit')
                                                            <a href="javascript:void(0)" v-on:click="paymentEdit(data)"
                                                                title="{{ __('Edit') }}">
                                                                <i aria-hidden="true" class="fa fa-pencil-square-o"></i>
                                                            </a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer" v-if="!formScheduleSubmitted">
                                    <button class="brownz-btn-yellow-d" type="button" v-on:click="hide1">✖
                                        {{ __('Cancel') }}</button>
                                    <button type="submit" form="payrollScheduleForm"
                                        v-bind:disabled="submit_button_flag" class="btn_dark-x">
                                        <i class="fa fa-check"></i> {{ __('Submit') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- End Schedule Modal --}}


    </div>



    <script>
        Vue.use(window["vue-js-modal"].default);
        var app = new Vue({
            el: '#app',

            data: {

                loading: false,

                base_url: "{{ '/' }}",

                payrollBatchList: [],
                payrollPreviewList: [],
                masterList: null,
                empList: [],
                paymentList: [],

                payroll_master_id: '',
                payroll_id: '',
                month: '',
                year: '',
                voucher_date: '',
                schedule_date: '',
                modelHeading: '',
                overlapMessage: '',
                viewMode: false,
                auth_password: '',
                passwordInput: '',
                passwordRowId: null,

                salary: '',
                advance: '',
                total_leave: '',
                approve_leave: '',
                deduction: '',
                payable_amount: '',

                payable_amount: '',
                payment_date: '',
                payment_amount: '',
                balance_amount: '',
                payroll_payment_id: '',
                narrations: '',
                payment_mode: 'bank',
                TDS_amount: '',
                professional_tax_amount: '',
                cash_balance: {{ json_encode(app(\App\Service\AccountingService::class)->getCashOrBankBalance('cash')) }},
                bank_balance: {{ json_encode(app(\App\Service\AccountingService::class)->getCashOrBankBalance('bank')) }},

                edit_payroll_id: '',
                edit_employee_name: '',
                edit_month: '',
                edit_year: '',
                edit_voucher_date: '',
                edit_schedule_date: '',
                edit_basic_salary: 0,
                edit_HRA: 0,
                edit_TA: 0,
                edit_DA: 0,
                edit_special_allowance: 0,
                edit_TDS_amount: 0,
                edit_professional_tax_amount: 0,
                edit_overtime_amount: 0,
                edit_deduction: 0,
                edit_advance: 0,
                editModelHeading: 'Edit Payroll',
                edit_submit_button_flag: false,
                editFormErrors: {},

                filter_emp_id: '',
                filter_end_date: '',
                filter_start_date: '',

                formSubmitted: false,
                submit_button_flag: false,
                formScheduleSubmitted: false,

                filter_month: '',
                filter_year: '',

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

            watch: {
                salary(newVal, oldVal) {
                    this.calculateTotals();
                },
                advance(newVal, oldVal) {
                    this.calculateTotals();
                },
                deduction(newVal, oldVal) {
                    this.calculateTotals();
                },
                payment_amount() {
                    this.calculateBalance();
                },
                payable_amount() {
                    this.calculateBalance();
                }
            },
            computed: {
                isActived: function() {
                    return app.pagination.current_page;
                },
                availablePaymentBalance: function() {
                    var mode = String(this.payment_mode || 'bank').toLowerCase();
                    if (mode === 'cash') {
                        return Math.max(0, parseFloat(this.cash_balance || 0));
                    }
                    return Math.max(0, parseFloat(this.bank_balance || 0));
                },
                paymentExceedsBalance: function() {
                    var amount = parseFloat(this.payment_amount || 0);
                    return amount > 0 && amount > this.availablePaymentBalance + 0.0001;
                },
                editComputedGross: function() {
                    return (
                        (parseFloat(this.edit_basic_salary) || 0) +
                        (parseFloat(this.edit_HRA) || 0) +
                        (parseFloat(this.edit_TA) || 0) +
                        (parseFloat(this.edit_DA) || 0) +
                        (parseFloat(this.edit_special_allowance) || 0)
                    );
                },
                editComputedNet: function() {
                    return Math.max(
                        0,
                        this.editComputedGross -
                        (parseFloat(this.edit_TDS_amount) || 0) -
                        (parseFloat(this.edit_professional_tax_amount) || 0)
                    );
                },
                editComputedPayable: function() {
                    var payableBeforeAdvance =
                        this.editComputedNet +
                        (parseFloat(this.edit_overtime_amount) || 0) -
                        (parseFloat(this.edit_deduction) || 0);

                    if (payableBeforeAdvance < 0) {
                        payableBeforeAdvance = 0;
                    }

                    return Math.max(
                        0,
                        payableBeforeAdvance - (parseFloat(this.edit_advance) || 0)
                    );
                },
                totalPreviewPayable: function() {
                    var self = this;
                    return (this.payrollPreviewList || []).reduce(function(sum, row) {
                        return sum + (self.payrollTotal(row) || 0);
                    }, 0);
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

            //

            methods: {

                paymentStatusClass(status) {
                    const value = (status || 'unpaid').toLowerCase();
                    if (value === 'paid') return 'is-paid';
                    if (value === 'partial') return 'is-partial';
                    return 'is-unpaid';
                },

                payrollTotal(row) {
                    if (row.total_payable_amount !== undefined && row.total_payable_amount !== null) {
                        const total = parseFloat(row.total_payable_amount) || 0;
                        if (total > 0) {
                            return total;
                        }
                    }

                    const paid = parseFloat(row.paid_amount || 0);
                    const due = parseFloat(
                        row.due_amount !== undefined && row.due_amount !== null
                            ? row.due_amount
                            : (row.payable_amount || 0)
                    );

                    if (paid > 0 || due > 0) {
                        return paid + due;
                    }

                    return Math.max(0, parseFloat(row.payable_amount || 0));
                },

                payrollDue(row) {
                    if (row.due_amount !== undefined && row.due_amount !== null) {
                        return Math.max(0, parseFloat(row.due_amount) || 0);
                    }

                    return Math.max(0, parseFloat(row.payable_amount || 0));
                },

                batchDue(row) {
                    if (row.total_due_amount !== undefined && row.total_due_amount !== null) {
                        return Math.max(0, parseFloat(row.total_due_amount) || 0);
                    }

                    return Math.max(0, parseFloat(row.total_payable_amount || 0) - parseFloat(row.total_paid_amount || 0));
                },

                batchPaymentVoucherUrl(row) {
                    const baseUrl = @json(route('tab.stock.stock.voucher.details', ['id' => 0, 'tab_name' => 'edit']));
                    return baseUrl + '?payroll_master_id=' + encodeURIComponent(row.payroll_master_id);
                },

                paymentVoucherUrl(row) {
                    const baseUrl = @json(route('tab.stock.stock.voucher.details', ['id' => 0, 'tab_name' => 'edit']));
                    return baseUrl + '?payroll_id=' + encodeURIComponent(row.payroll_id);
                },

                payrollVouchers(row) {
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

                    return vouchers;
                },

                submitForm() {
                    this.generatePayrollPreview();
                },

                generatePayrollPreview() {
                    if (!this.month || !this.year || !this.voucher_date || !this.schedule_date) {
                        Vue.$toast.error('Month, year, voucher date and schedule date are required.', 'Danger Alert');
                        return;
                    }

                    if (!this.auth_password) {
                        Vue.$toast.error('Password is required to generate payroll.', 'Danger Alert');
                        return;
                    }

                    this.submit_button_flag = true;
                    this.overlapMessage = '';

                    axios.post("{{ route('payroll.generate') }}", {
                        month: this.month,
                        year: this.year,
                        voucher_date: this.voucher_date,
                        schedule_date: this.schedule_date,
                        payroll_master_id: this.payroll_master_id || '',
                        auth_password: this.auth_password,
                        _token: "{{ csrf_token() }}",
                    }).then((response) => {
                        this.submit_button_flag = false;
                        if (response.data.errorcode === 0) {
                            this.payrollPreviewList = response.data.data?.data || [];
                            this.overlapMessage = '';
                        } else {
                            this.payrollPreviewList = [];
                            this.overlapMessage = response.data.errormessage || 'Unable to generate payroll preview.';
                            Vue.$toast.error(this.overlapMessage, 'Danger Alert');
                        }
                    }).catch((err) => {
                        this.submit_button_flag = false;
                        const message = err.response?.data?.errormessage
                            || err.response?.data?.message
                            || 'Something went wrong while generating.';
                        this.overlapMessage = message;
                        Vue.$toast.error(message, 'Danger Alert');
                    });
                },

                savePayrollBatch() {
                    if (!this.payrollPreviewList.length) {
                        Vue.$toast.error('Generate payroll preview before saving.', 'Danger Alert');
                        return;
                    }

                    if (!this.auth_password) {
                        Vue.$toast.error('Password is required to save payroll.', 'Danger Alert');
                        return;
                    }

                    this.submit_button_flag = true;
                    this.overlapMessage = '';

                    axios.post("{{ route('payroll.save') }}", {
                        payroll_master_id: this.payroll_master_id || null,
                        month: this.month,
                        year: this.year,
                        voucher_date: this.voucher_date,
                        schedule_date: this.schedule_date,
                        payrolls: this.payrollPreviewList,
                        auth_password: this.auth_password,
                        _token: "{{ csrf_token() }}",
                    }).then((response) => {
                        this.submit_button_flag = false;
                        if (response.data.errorcode === 0) {
                            Vue.$toast.success(response.data.errormessage, 'Success Alert');
                            this.hide();
                            this.getpayrollList(this.pagination.current_page);
                        } else {
                            this.overlapMessage = response.data.errormessage || 'Save failed.';
                            Vue.$toast.error(this.overlapMessage, 'Danger Alert');
                        }
                    }).catch((err) => {
                        this.submit_button_flag = false;
                        const message = err.response?.data?.errormessage
                            || err.response?.data?.message
                            || 'Something went wrong while saving.';
                        this.overlapMessage = message;
                        Vue.$toast.error(message, 'Danger Alert');
                    });
                },

                submitEditBatchPassword(data) {
                    if (!this.passwordInput) {
                        Vue.$toast.error('Password is required.', 'Danger Alert');
                        return;
                    }

                    axios.post("{{ route('payroll.auth-check') }}", {
                        password: this.passwordInput,
                        _token: "{{ csrf_token() }}",
                    }).then((response) => {
                        if (response.data.valid) {
                            const unlockedPassword = this.passwordInput;
                            this.cancelPasswordInput();
                            this.popupBatchModal(data, false, unlockedPassword);
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

                popupBatchModal(data, isView, unlockedPassword) {
                    this.viewMode = !!isView;
                    this.overlapMessage = '';
                    this.auth_password = unlockedPassword || '';
                    this.payroll_master_id = data.payroll_master_id;
                    this.month = data.month;
                    this.year = String(data.year);
                    this.voucher_date = data.voucher_date;
                    this.schedule_date = data.schedule_date;
                    this.modelHeading = isView ? 'View Payroll Batch' : 'Edit Payroll Batch';

                    this.fetchPayrollByMasterId(data.payroll_master_id).then(() => {
                        if (this.masterList && Array.isArray(this.masterList.payrolls)) {
                            this.payrollPreviewList = this.masterList.payrolls;
                        } else {
                            this.payrollPreviewList = [];
                        }
                        $('#payrollModal').modal('show');
                    });
                },

                fetchPayrollByMasterId(masterId) {
                    return axios.get('api/payrolls/' + masterId)
                        .then((response) => {
                            if (Array.isArray(response.data.data.data) && response.data.data.data.length > 0) {
                                this.masterList = response.data.data.data[0];
                            } else {
                                this.masterList = null;
                            }
                        });
                },

                refreshBatchModal() {
                    if (!this.payroll_master_id) {
                        return;
                    }

                    this.fetchPayrollByMasterId(this.payroll_master_id).then(() => {
                        if (this.masterList && Array.isArray(this.masterList.payrolls)) {
                            this.payrollPreviewList = this.masterList.payrolls;
                        }
                    });
                    this.getpayrollList(this.pagination.current_page);
                },


                showPasswordInput(rowId) {
                    this.passwordInput = '';
                    this.passwordRowId = rowId;
                },

                cancelPasswordInput() {
                    this.passwordInput = '';
                    this.passwordRowId = null;
                },

                submitNewPassword() {
                    if (!this.passwordInput) {
                        Vue.$toast.error('Password is required.', 'Danger Alert');
                        return;
                    }

                    axios.post("{{ route('payroll.auth-check') }}", {
                        password: this.passwordInput,
                        _token: "{{ csrf_token() }}",
                    }).then((response) => {
                        if (response.data.valid) {
                            const unlockedPassword = this.passwordInput;
                            this.cancelPasswordInput();
                            this.popupModal(0, unlockedPassword);
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

                validateBeforePayrollScheduleSubmit(e) {
                    console.log(this.errors);
                    this.$validator.validateAll();
                    if (!this.errors.any()) {
                        if (!this.auth_password) {
                            Vue.$toast.error('Password is required to submit payment.', 'Danger Alert');
                            return;
                        }
                        this.submitPayrollScheduleForm()
                    }
                },


                changePage: function(page) {
                    this.pagination.current_page = page;
                    app.getpayrollList(page);
                },

                handleChangePage(e) {
                    console.log(this.page_length)
                    app.getpayrollList();
                },

                // show modal
                show: function() {
                    $('#payrollModal').modal('show');
                },

                // close modal
                hide: function() {
                    $('#payrollModal').modal('hide');
                    app.auth_password = '';
                    app.overlapMessage = '';
                    app.payrollPreviewList = [];
                    app.payroll_master_id = '';
                    app.viewMode = false;
                },

                hide1: function() {
                    app.payable_amount = '';
                    app.payment_date = '';
                    app.narrations = '';
                    app.payment_amount = '';
                    app.balance_amount = '';
                    app.auth_password = '';
                    $('#payRollScheduleModal').modal('hide');
                },

                hideEdit: function() {
                    $('#payrollEditModal').modal('hide');
                    app.editFormErrors = {};
                    app.edit_submit_button_flag = false;
                    app.auth_password = '';
                },

                canEditPayroll: function(data) {
                    if (!data || !data.payroll_id) {
                        return false;
                    }
                    if (data.has_payments) {
                        return false;
                    }
                    // Fully settled / printable rows are not editable
                    if (parseInt(data.is_scheduled, 10) === 1) {
                        return false;
                    }
                    return true;
                },

                submitEditPassword: function(data) {
                    if (!this.passwordInput) {
                        Vue.$toast.error('Password is required.', 'Danger Alert');
                        return;
                    }

                    axios.post("{{ route('payroll.auth-check') }}", {
                        password: this.passwordInput,
                        _token: "{{ csrf_token() }}",
                    }).then((response) => {
                        if (response.data.valid) {
                            const unlockedPassword = this.passwordInput;
                            this.cancelPasswordInput();
                            this.popupEditModal(data, unlockedPassword);
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

                submitPayPassword: function(data) {
                    if (!this.passwordInput) {
                        Vue.$toast.error('Password is required.', 'Danger Alert');
                        return;
                    }

                    axios.post("{{ route('payroll.auth-check') }}", {
                        password: this.passwordInput,
                        _token: "{{ csrf_token() }}",
                    }).then((response) => {
                        if (response.data.valid) {
                            const unlockedPassword = this.passwordInput;
                            this.cancelPasswordInput();
                            this.popupScheduleModal(data, unlockedPassword);
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

                popupEditModal: function(data, unlockedPassword) {
                    if (!this.canEditPayroll(data)) {
                        Vue.$toast.error('Only unpaid payroll without payments can be edited.', 'Danger Alert');
                        return;
                    }

                    app.edit_payroll_id = data.payroll_id;
                    app.edit_employee_name = (data.employee && data.employee.name) ? data.employee.name : '';
                    app.edit_month = data.month;
                    app.edit_year = data.year;
                    app.edit_voucher_date = data.voucher_date;
                    app.edit_schedule_date = data.schedule_date;
                    app.edit_basic_salary = data.basic_salary || 0;
                    app.edit_HRA = data.HRA || 0;
                    app.edit_TA = data.TA || 0;
                    app.edit_DA = data.DA || 0;
                    app.edit_special_allowance = data.special_allowance || 0;
                    app.edit_TDS_amount = data.TDS_amount || 0;
                    app.edit_professional_tax_amount = data.professional_tax_amount || 0;
                    app.edit_overtime_amount = data.overtime_amount || 0;
                    app.edit_deduction = data.deduction || 0;
                    app.edit_advance = data.advance || 0;
                    app.auth_password = unlockedPassword || '';
                    app.editModelHeading = "Edit Payroll - " + app.edit_employee_name;
                    app.editFormErrors = {};
                    app.edit_submit_button_flag = false;

                    $('#payrollEditModal').modal('show');
                },

                submitEditForm: function() {
                    if (!app.auth_password) {
                        Vue.$toast.error('Password is required to update payroll.', 'Danger Alert');
                        return;
                    }

                    app.edit_submit_button_flag = true;
                    app.editFormErrors = {};

                    axios.post("{{ route('payroll.update') }}", {
                        payroll_id: app.edit_payroll_id,
                        voucher_date: app.edit_voucher_date,
                        schedule_date: app.edit_schedule_date,
                        basic_salary: app.edit_basic_salary,
                        HRA: app.edit_HRA,
                        TA: app.edit_TA,
                        DA: app.edit_DA,
                        special_allowance: app.edit_special_allowance,
                        TDS_amount: app.edit_TDS_amount,
                        professional_tax_amount: app.edit_professional_tax_amount,
                        overtime_amount: app.edit_overtime_amount,
                        deduction: app.edit_deduction,
                        advance: app.edit_advance,
                        auth_password: app.auth_password,
                        _token: "{{ csrf_token() }}",
                    }).then(function(response) {
                        app.edit_submit_button_flag = false;

                        if (response.data.errorcode == 0) {
                            Vue.$toast.success(response.data.errormessage, 'Success Alert');
                            app.auth_password = '';
                            $('#payrollEditModal').modal('hide');
                            app.refreshBatchModal();
                        } else {
                            Vue.$toast.error(response.data.errormessage || 'Something went wrong.', 'Danger Alert');
                        }
                    }).catch(function(err) {
                        app.edit_submit_button_flag = false;
                        if (err.response && err.response.data && err.response.data.errors) {
                            app.editFormErrors = err.response.data.errors;
                        }
                        var message = (err.response && err.response.data && err.response.data.errormessage)
                            || (err.response && err.response.data && err.response.data.errors && err.response.data.errors.auth_password
                                ? err.response.data.errors.auth_password[0]
                                : null)
                            || (err.response && err.response.data && err.response.data.message)
                            || 'Validation failed.';
                        Vue.$toast.error(message, 'Danger Alert');
                    });
                },

                formatDate(date) {
                    return moment(date).format('DD-MM-YYYY');
                },

                addEditSubmitForm: function() {
                    app.generatePayrollPreview();
                },

                popupModal: function(data, unlockedPassword) {
                    app.viewMode = false;
                    app.overlapMessage = '';
                    app.auth_password = unlockedPassword || '';
                    app.payrollPreviewList = [];

                    if (data && data.payroll_master_id > 0) {
                        app.popupBatchModal(data, false, unlockedPassword);
                        return;
                    }

                    var months = [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ];
                    var now = new Date();
                    var today = now.toISOString().slice(0, 10);

                    app.payroll_master_id = '';
                    app.voucher_date = today;
                    app.schedule_date = today;
                    app.month = months[now.getMonth()];
                    app.year = String(now.getFullYear());
                    app.payroll_id = '';
                    app.formSubmitted = false;
                    app.formErrors = {};
                    app.modelHeading = "Generate Payroll";
                    $('#payrollModal').modal('show');
                },

                closeAddEditForm: function() {

                    app.voucher_date = '';
                    app.schedule_date = '';
                    app.month = '';
                    app.year = '';
                    app.payroll_id = '';
                    app.auth_password = '';

                    app.formSubmitted = false;
                    app.formErrors = {};

                    $('#payrollModal').modal('hide');
                    app.getpayrollList();
                },

                getpayrollList: function(page = 1) {

                    url = "api/payroll-masters/list";

                    axios.post(url, {
                            page: page,
                            page_length: this.page_length,
                            month: this.filter_month,
                            year: this.filter_year
                        })
                        .then(function(response) {

                            app.payrollBatchList = response.data.data.data.data;
                            app.pagination = response.data.data.pagination;
                            app.loading = false;

                        });

                },

               getEmployeeList: function() {
                    url = "api/employee/list";

                    axios.post(url, {
                        status: 'active',
                        page_length: 500,
                    }).then(function(response) {
                        app.empList = response.data.data.data.data;
                    });
                },

                applyDefaultFilters: function() {
                    var months = [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ];
                    var now = new Date();
                    this.filter_month = months[now.getMonth()];
                    this.filter_year = String(now.getFullYear());
                },

                resetFilter: function() {
                    app.filter_month = '';
                    app.filter_year = '';
                    app.getpayrollList(1);
                },

                // Schedule Modal Open
                popupScheduleModal: function(data, unlockedPassword) {

                    app.payroll_id = data.payroll_id;
                    app.emp_id = data.emp_id;
                    app.year = data.year;
                    app.month = data.month;
                    app.voucher_date = data.voucher_date;
                    app.schedule_date = data.schedule_date;
                    app.salary = data.net_salary;
                    app.advance = data.advance;
                    app.total_leave = data.total_leave;
                    app.approve_leave = data.approve_leave;
                    app.deduction = data.deduction;
                    app.payable_amount = data.payable_amount;
                    app.TDS_amount = data.TDS_amount || '0';
                    app.professional_tax_amount = data.professional_tax_amount || '0';
                    app.payment_mode = (data.employee && data.employee.payment_mode) ? data.employee.payment_mode : 'bank';
                    app.payroll_payment_id = '';
                    app.payment_date = new Date().toISOString().slice(0, 10);
                    app.payment_amount = '';
                    app.balance_amount = '';
                    app.narrations = '';
                    app.auth_password = unlockedPassword || '';

                    app.formScheduleSubmitted = false;
                    app.formSubmitted = true;
                    app.modelHeading = "Pay Salary - " + ((data.employee && data.employee.name) ? data.employee.name : '');

                    app.getPaymentList();
                    $('#payRollScheduleModal').modal('show');
                },

                paymentEdit: function(data) {


                    app.payroll_id = data.payroll_id;
                    app.payroll_payment_id = data.payroll_payment_id;
                    app.emp_id = data.emp_id || (data.payroll ? data.payroll.emp_id : app.emp_id);
                    app.payable_amount = data.payable_amount;
                    app.payment_date = data.payment_date;
                    app.payment_amount = data.payment_amount;
                    app.balance_amount = data.balance_amount;
                    app.narrations = data.narrations;
                    app.payment_mode = data.payment_mode || 'bank';

                    app.formScheduleSubmitted = false;
                    app.formSubmitted = true;
                    var empName = (data.payroll && data.payroll.employee && data.payroll.employee.name)
                        ? data.payroll.employee.name
                        : ((data.employee && data.employee.name) ? data.employee.name : '');
                    app.modelHeading = " Edit Pay Salary - " + empName;

                    app.getPaymentList();
                    $('#payRollScheduleModal').modal('show');
                },

                submitPayrollScheduleForm: function() {
                    if (this.paymentExceedsBalance) {
                        alert(
                            (this.payment_mode === 'cash' ? 'Cash' : 'Bank') +
                            ' has insufficient balance. Available: ' +
                            Number(this.availablePaymentBalance).toFixed(2)
                        );
                        return;
                    }

                    if (!this.auth_password) {
                        Vue.$toast.error('Password is required to submit payment.', 'Danger Alert');
                        return;
                    }

                    app.submit_button_flag = true;
                    let url = "{{ route('payroll.payment') }}";

                    let formData = new FormData();
                    formData.append('payroll_id', this.payroll_id);
                    formData.append('payable_amount', this.payable_amount);
                    formData.append('payment_amount', this.payment_amount);
                    formData.append('payment_date', this.payment_date);
                    formData.append('balance_amount', this.balance_amount);
                    formData.append('narrations', this.narrations);
                    formData.append('emp_id', this.emp_id);
                    formData.append('payroll_payment_id', this.payroll_payment_id);
                    formData.append('payment_mode', this.payment_mode || 'bank');
                    formData.append('TDS_amount', this.TDS_amount || '0');
                    formData.append('professional_tax_amount', this.professional_tax_amount || '0');
                    formData.append('auth_password', this.auth_password || '');
                    formData.append('_token', "{{ csrf_token() }}");


                    axios.post(url, formData)
                        .then(function(response) {
                            console.log(response);
                            app.submit_button_flag = false;

                            if (response.data.errorcode == 0) {
                                app.loading = false;

                                // Reset only the fields you need
                                app.payable_amount = '';
                                app.payment_date = '';
                                app.narrations = '';
                                app.payment_amount = '';
                                app.balance_amount = '';
                                app.auth_password = '';

                                Vue.$toast.success(response.data.errormessage, 'Success Alert');
                                app.formScheduleSubmitted = false;
                                app.formErrors = {};

                                // Close modal
                                $('#payRollScheduleModal').modal('hide');

                                // Refresh list
                                app.getPaymentList();
                                app.refreshBatchModal();
                            } else {
                                app.loading = false;
                                Vue.$toast.error(response.data.errormessage || 'Something went wrong.', 'Danger Alert');
                            }
                        })
                        .catch(function(err) {
                            app.submit_button_flag = false;
                            app.formErrors = (err.response && err.response.data && err.response.data.errors)
                                ? err.response.data.errors
                                : {};
                            var message = (err.response && err.response.data && err.response.data.errormessage)
                                || (err.response && err.response.data && err.response.data.errors && err.response.data.errors.auth_password
                                    ? err.response.data.errors.auth_password[0]
                                    : null)
                                || 'Something went wrong while submitting payment.';
                            Vue.$toast.error(message, 'Danger Alert');
                        });
                },


                calculateTotals() {
                    let salary = Number(this.salary);
                    let advance = Number(this.advance);
                    let deduction = Number(this.deduction);

                    if (!isNaN(salary) && !isNaN(advance) && !isNaN(deduction)) {
                        this.payable_amount = salary - (advance + deduction);
                    } else {
                        this.payable_amount = 0;
                    }
                },

                calculateBalance() {
                    let payable = parseFloat(this.payable_amount) || 0;
                    let payment = parseFloat(this.payment_amount) || 0;
                    this.balance_amount = (payable - payment).toFixed(2);
                },

                calculateTotalBalance() {
                    if (!this.paymentList.length) return;

                    // Get the payable_amount from the first payroll entry (assuming all payments belong to the same payroll)
                    let totalPayable = parseFloat(this.paymentList[0].payroll.payable_amount) || 0;

                    // Calculate the total payments made
                    let totalPaid = this.paymentList.reduce((sum, item) => {
                        return sum + (parseFloat(item.payment_amount) || 0);
                    }, 0);

                    // Subtract total paid from total payable amount
                    this.payable_amount = (totalPayable - totalPaid).toFixed(2);
                    console.log(this.payable_amount);
                },


                scheduleSalary(data) {

                    url = "api/salary-scheduled/update";

                    axios.post(url, {

                        payroll_id: data.payroll_id,
                        is_scheduled: true

                    }).then(function(response) {
                        console.log(response);
                        if (response.data.errorcode == 0) {
                            app.loading = false;
                            Vue.$toast.success(response.data.errormessage, 'Success Alert');
                            app.formScheduleSubmitted = true;
                            app.formErrors = {};

                            // app.getpayrollList(app.pagination.current_page);
                            app.getPaymentList(app.pagination.current_page);
                        } else {
                            app.loading = false;
                            Vue.$toast.error('Something went wrong.', 'Danger Alert');

                            app.getpayrollList();
                        }

                    }).catch(function(err) {
                        app.submit_button_flag = false;
                        console.log(err.response.data.errors);
                        app.formErrors = err.response.data.errors;

                    });
                },

                downloadPayroll(payrollId) {
                    const url = '/download/' + payrollId;
                    const printWindow = window.open(url, '_blank', 'noopener,noreferrer');

                    if (!printWindow) {
                        Vue.$toast.error('Please allow popups to print the salary slip.', 'Print');
                    }
                },

                getPaymentList: function(page = 1) {

                    url = "api/payroll-payment/list";

                    axios.post(url, {

                        "payroll_id": this.payroll_id,

                    }).then(function(response) {
                        app.paymentList = response.data.data.data.data;
                        app.calculateTotalBalance();
                        app.loading = false;
                    });
                },

            },
            mounted() {
                this.getpayrollList();
            }
        });
    </script>
@endsection
