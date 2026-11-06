@extends('payroll.layouts.base')

<style>
    .advance-page .list-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
    }

    .advance-page .list-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .advance-page .list-meta {
        font-size: 12px;
        color: #6c757d;
    }

    .advance-page .advance-table th,
    .advance-page .advance-table td {
        vertical-align: middle;
    }

    .advance-page .emp-name {
        display: block;
        font-weight: 600;
        color: #1D1D1D;
    }

    .advance-page .emp-period {
        display: block;
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    .advance-page .amount-cell {
        font-variant-numeric: tabular-nums;
        font-weight: 600;
        white-space: nowrap;
    }

    .advance-page .mode-pill {
        display: inline-flex;
        align-items: center;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        padding: 6px 12px;
        text-transform: capitalize;
        background: #F0F0F3;
        color: #1D1D1D;
    }

    .advance-page .mode-pill.is-cash {
        color: #8a6d00;
        background: rgba(230, 205, 127, 0.45);
    }

    .advance-page .mode-pill.is-bank {
        color: #1D5F8A;
        background: rgba(82, 168, 217, 0.18);
    }

    .advance-page .manage-btn {
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

    .advance-page .manage-btn:hover {
        background: #1D1D1D;
        color: #E6CD7F;
    }

    .advance-page .empty-state {
        text-align: center;
        padding: 36px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }

    .advance-page .list-summary {
        display: flex;
        justify-content: flex-end;
        margin-top: 10px;
        font-weight: 600;
        color: #1D1D1D;
        font-variant-numeric: tabular-nums;
    }

    #salaryModal .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }

    #salaryModal .modal-header {
        border-bottom: none;
        padding: 18px 20px 8px;
        align-items: flex-start;
    }

    #salaryModal .modal-title {
        color: #1D1D1D;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 2px;
    }

    #salaryModal .modal-subtitle {
        margin: 0;
        font-size: 13px;
        color: #6c757d;
    }

    #salaryModal .modal-body {
        padding: 8px 20px 12px;
    }

    #salaryModal .advance-section {
        background: #F7F7F9;
        border-radius: 14px;
        padding: 14px 14px 6px;
        margin-bottom: 12px;
    }

    #salaryModal .advance-section-title {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #6c757d;
        margin: 0 0 10px;
    }

    #salaryModal .form-label {
        font-size: 13px;
        color: #4a4a4a;
        font-weight: 500;
        margin-bottom: 4px;
    }

    #salaryModal .form-group {
        margin-bottom: 12px;
    }

    #salaryModal .salary-hint {
        background: #fff;
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 13px;
        color: #4a4a4a;
        margin-bottom: 10px;
    }

    #salaryModal .modal-footer {
        border-top: none;
        padding: 4px 0 8px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }
</style>

@section('content')
    <div id="app" class="advance-page">

        <div class="fbox-shadow mt-2 fd-card respo-margin-top">
            <div class="card-body">
                <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
                    {{ __('Salary Advance') }}
                </h5>

                <div class="row align-items-center br-row-gap-2">
                    <div class="col-lg-2 col-md-6">
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <input type="date" class="form-control brawnz-search-bar-in"
                                v-model="filter_start_date" title="{{ __('Date From') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <input type="date" class="form-control brawnz-search-bar-in"
                                v-model="filter_end_date" title="{{ __('Date To') }}">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <select v-model="filter_emp_id" class="form-control brawnz-search-bar-in"
                                @change="getSalaryList(1)">
                                <option value="">{{ __('All Employees') }}</option>
                                <option v-for="data in empList" :key="data.emp_id" :value="data.emp_id">
                                    @{{ data.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <select v-model="filter_payment_mode" class="form-control brawnz-search-bar-in"
                                @change="getSalaryList(1)">
                                <option value="">{{ __('All Modes') }}</option>
                                <option value="bank">{{ __('Bank') }}</option>
                                <option value="cash">{{ __('Cash') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-primary btn_dark-x mr-2"
                            v-on:click="getSalaryList(1)">
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
                        <p class="list-title">{{ __('Advance List') }}</p>
                        <p class="list-meta mb-0" v-if="pagination.total">
                            @{{ pagination.total }} {{ __('result(s)') }}
                        </p>
                    </div>
                    <div class="d-flex justify-content-end">
                        @can('adv_salary_create')
                            <button type="button" class="btn_dark-x" v-on:click="popupModal(0)">
                                <i class="fa fa-plus"></i> {{ __('New') }}
                            </button>
                        @endcan
                    </div>
                </div>

                {{-- Add / Edit Modal --}}
                <div class="modal fade brownz-custom-popup" id="salaryModal" tabindex="-1"
                    aria-labelledby="salaryAdvanceModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div>
                                    <h4 class="modal-title">@{{ modelHeading }}</h4>
                                    <p class="modal-subtitle">
                                        {{ __('Record employee salary advance against cash or bank') }}
                                    </p>
                                </div>
                                <button type="button" class="bootbox-close-button close" v-on:click="hide"
                                    data-dismiss="modal" aria-label="Close">
                                    ×
                                </button>
                            </div>

                            <div class="modal-body">
                                <form @submit.prevent="validateBeforeSubmit" v-if="!formSubmitted" method="post">
                                    <div class="advance-section">
                                        <p class="advance-section-title">{{ __('Period & Employee') }}</p>
                                        <div class="row">
                                            <div class="col-md-4 col-sm-12 form-group">
                                                <label for="month" class="form-label">
                                                    {{ __('Month') }} <span class="text-danger">*</span>
                                                </label>
                                                <?php $months = getMonths(); ?>
                                                <select v-model="month" class="form-control"
                                                    v-validate.initial="month" data-rules="required" id="month">
                                                    <option value="">{{ __('Select') }}</option>
                                                    @foreach ($months as $month)
                                                        <option value="{{ $month }}">{{ $month }}</option>
                                                    @endforeach
                                                </select>
                                                <span v-if="formErrors['month']"
                                                    class="error text-danger">@{{ formErrors['month'] }}</span>
                                                <p class="text-danger mb-0" v-if="errors.has('month')">
                                                    @{{ errors.first('month') }}
                                                </p>
                                            </div>

                                            <div class="col-md-4 col-sm-12 form-group">
                                                <label for="year" class="form-label">
                                                    {{ __('Year') }} <span class="text-danger">*</span>
                                                </label>
                                                <?php $years = getYears(); ?>
                                                <select v-model="year" class="form-control"
                                                    v-validate.initial="year" data-rules="required" id="year">
                                                    <option value="">{{ __('Select') }}</option>
                                                    @foreach ($years as $year)
                                                        <option value="{{ $year }}">{{ $year }}</option>
                                                    @endforeach
                                                </select>
                                                <span v-if="formErrors['year']"
                                                    class="error text-danger">@{{ formErrors['year'] }}</span>
                                                <p class="text-danger mb-0" v-if="errors.has('year')">
                                                    @{{ errors.first('year') }}
                                                </p>
                                            </div>

                                            <div class="col-md-4 col-sm-12 form-group">
                                                <label for="voucher_date" class="form-label">
                                                    {{ __('Voucher Date') }} <span class="text-danger">*</span>
                                                </label>
                                                <input type="date" class="form-control" v-model="voucher_date"
                                                    v-validate.initial="voucher_date" data-rules="required"
                                                    id="voucher_date" @change="syncPeriodFromVoucherDate">
                                                <span v-if="formErrors['voucher_date']"
                                                    class="error text-danger">@{{ formErrors['voucher_date'] }}</span>
                                                <p class="text-danger mb-0" v-if="errors.has('voucher_date')">
                                                    @{{ errors.first('voucher_date') }}
                                                </p>
                                            </div>

                                            <div class="col-md-12 col-sm-12 form-group">
                                                <label for="emp_id" class="form-label">
                                                    {{ __('Employee') }} <span class="text-danger">*</span>
                                                </label>
                                                <select v-model="emp_id" class="form-control"
                                                    v-validate.initial="emp_id" data-rules="required" id="emp_id"
                                                    @change="getEmployeeSalary">
                                                    <option value="">{{ __('Select employee') }}</option>
                                                    <option v-for="data in empList" :key="data.emp_id"
                                                        :value="data.emp_id">
                                                        @{{ data.name }} <span v-if="data.emp_code">(@{{ data.emp_code }})</span>
                                                    </option>
                                                </select>
                                                <span v-if="formErrors['emp_id']"
                                                    class="error text-danger">@{{ formErrors['emp_id'] }}</span>
                                                <p class="text-danger mb-0" v-if="errors.has('emp_id')">
                                                    @{{ errors.first('emp_id') }}
                                                </p>
                                                <p class="text-danger mb-0" v-if="emp_id && !employeeSalary">
                                                    {{ __('No active salary found for this employee. Please define salary first.') }}
                                                </p>
                                                <div class="salary-hint" v-if="employeeSalary">
                                                    {{ __('Basic Salary') }}:
                                                    <strong>₹ @{{ Number(employeeSalary.basic_salary || 0).toFixed(2) }}</strong>
                                                    <span class="mx-2">·</span>
                                                    {{ __('Target') }}:
                                                    <strong>₹ @{{ Number(employeeSalary.target_value || 0).toFixed(2) }}</strong>
                                                    <div class="mt-1" v-if="employeeSalary.advance_ledger && employeeSalary.advance_ledger.name">
                                                        {{ __('Posts to ledger') }}:
                                                        <strong>@{{ employeeSalary.advance_ledger.name }}</strong>
                                                        <span v-if="employeeSalary.advance_ledger.code">(@{{ employeeSalary.advance_ledger.code }})</span>
                                                        <span class="mx-2">·</span>
                                                        {{ __('Already outstanding') }}:
                                                        <strong>₹ @{{ Number(employeeSalary.advance_ledger.outstanding || 0).toFixed(2) }}</strong>
                                                    </div>
                                                    <div class="mt-1" v-else>
                                                        {{ __('No salary advance ledger is linked yet. Saving will create one named after this employee.') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="advance-section">
                                        <p class="advance-section-title">{{ __('Payment Details') }}</p>
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12 form-group">
                                                <label for="advance_amount" class="form-label">
                                                    {{ __('Advance Amount') }} <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" min="0.01" class="form-control"
                                                    v-model="advance_amount"
                                                    v-validate.initial="advance_amount" data-rules="required"
                                                    id="advance_amount" placeholder="0.00">
                                                <span v-if="formErrors['advance_amount']"
                                                    class="error text-danger">@{{ formErrors['advance_amount'] }}</span>
                                                <p class="text-danger mb-0" v-if="errors.has('advance_amount')">
                                                    @{{ errors.first('advance_amount') }}
                                                </p>
                                            </div>

                                            <div class="col-md-6 col-sm-12 form-group">
                                                <label for="payment_mode" class="form-label">
                                                    {{ __('Payment Mode') }} <span class="text-danger">*</span>
                                                </label>
                                                <select v-model="payment_mode" class="form-control"
                                                    v-validate.initial="payment_mode" data-rules="required"
                                                    id="payment_mode">
                                                    <option value="">{{ __('Select') }}</option>
                                                    <option value="bank">{{ __('Bank') }}</option>
                                                    <option value="cash">{{ __('Cash') }}</option>
                                                </select>
                                                <span v-if="formErrors['payment_mode']"
                                                    class="error text-danger">@{{ formErrors['payment_mode'] }}</span>
                                                <p class="text-danger mb-0" v-if="errors.has('payment_mode')">
                                                    @{{ errors.first('payment_mode') }}
                                                </p>
                                                <small class="text-muted d-block mt-1" v-if="payment_mode">
                                                    {{ __('Available') }}
                                                    @{{ payment_mode === 'cash' ? 'Cash' : 'Bank' }}:
                                                    <strong>₹ @{{ Number(availablePaymentBalance).toFixed(2) }}</strong>
                                                </small>
                                                <small class="text-danger d-block" v-if="advanceExceedsBalance">
                                                    {{ __('Advance amount exceeds available balance.') }}
                                                </small>
                                            </div>

                                            <div class="col-md-12 col-sm-12 form-group">
                                                <label for="narrations" class="form-label">
                                                    {{ __('Narrations') }}
                                                </label>
                                                <textarea class="form-control" id="narrations" v-model="narrations" rows="2"
                                                    placeholder="{{ __('Optional notes') }}"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button class="brownz-btn-yellow-d" type="button" v-on:click="hide">
                                            {{ __('Cancel') }}
                                        </button>
                                        <button type="submit" v-bind:disabled="submit_button_flag || advanceExceedsBalance"
                                            class="btn_dark-x">
                                            <i class="fa fa-check"></i> {{ __('Submit') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table brownz-table-list-v1 advance-table" v-if="salaryList.length">
                    <thead>
                        <tr>
                            <th style="width: 56px;">#</th>
                            <th>{{ __('Voucher Date') }}</th>
                            <th>{{ __('Employee') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Payment Mode') }}</th>
                            <th>{{ __('Narrations') }}</th>
                            <th class="text-center" style="width: 80px;">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(data, index) in salaryList" :key="data.emp_salary_advance_id">
                            <td>@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</td>
                            <td>@{{ formatDateDisplay(data.voucher_date) }}</td>
                            <td>
                                <span class="emp-name">@{{ employeeName(data) }}</span>
                                <span class="emp-period" v-if="data.month || data.year">
                                    @{{ data.month || '' }} @{{ data.year || '' }}
                                </span>
                            </td>
                            <td class="amount-cell">₹ @{{ Number(data.advance_amount || 0).toFixed(2) }}</td>
                            <td>
                                <span class="mode-pill"
                                    :class="data.payment_mode === 'cash' ? 'is-cash' : 'is-bank'">
                                    @{{ data.payment_mode || '—' }}
                                </span>
                            </td>
                            <td>@{{ data.narrations || '—' }}</td>
                            <td class="text-center">
                                @can('adv_salary_edit')
                                    <a href="javascript:void(0)" class="manage-btn" title="{{ __('Edit') }}"
                                        v-on:click="popupModal(data)">
                                        <i aria-hidden="true" class="fa fa-pencil-square-o"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="list-summary" v-if="salaryList.length">
                    {{ __('Filtered Total') }}: ₹ @{{ Number(filteredTotalAmount || 0).toFixed(2) }}
                </div>

                <div class="empty-state" v-else>
                    {{ __('No salary advances found for the selected filters.') }}
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
    </div>

    <script>
        Vue.use(window["vue-js-modal"].default);
        var app = new Vue({
            el: '#app',

            data: {
                loading: false,
                base_url: "{{ '/' }}",

                salaryList: [],
                empList: [],
                filteredTotalAmount: 0,

                emp_salary_advance_id: '',
                emp_id: '',
                year: '',
                month: '',
                voucher_date: '',
                advance_amount: '',
                payment_mode: 'bank',
                narrations: '',
                modelHeading: '',
                employeeSalary: null,
                original_advance_amount: 0,
                original_payment_mode: '',
                cash_balance: {{ json_encode(app(\App\Service\AccountingService::class)->getCashOrBankBalance('cash')) }},
                bank_balance: {{ json_encode(app(\App\Service\AccountingService::class)->getCashOrBankBalance('bank')) }},

                filter_emp_id: '',
                filter_start_date: '',
                filter_end_date: '',
                filter_payment_mode: '',

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
                availablePaymentBalance: function() {
                    var mode = String(this.payment_mode || '').toLowerCase();
                    var balance = 0;
                    if (mode === 'cash') {
                        balance = Math.max(0, parseFloat(this.cash_balance || 0));
                    } else if (mode === 'bank') {
                        balance = Math.max(0, parseFloat(this.bank_balance || 0));
                    } else {
                        return 0;
                    }
                    if (this.emp_salary_advance_id &&
                        String(this.original_payment_mode || '').toLowerCase() === mode) {
                        balance += Math.max(0, parseFloat(this.original_advance_amount || 0));
                    }
                    return balance;
                },
                advanceExceedsBalance: function() {
                    var amount = parseFloat(this.advance_amount || 0);
                    var mode = String(this.payment_mode || '').toLowerCase();
                    if (!mode || !(amount > 0)) {
                        return false;
                    }
                    return amount > this.availablePaymentBalance + 0.0001;
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
                formatDateDisplay(date) {
                    return date ? moment(date).format('DD-MM-YYYY') : '—';
                },

                employeeName(data) {
                    if (data.employee && data.employee.name) {
                        return data.employee.name;
                    }
                    if (data.employee && data.employee.user && data.employee.user.name) {
                        return data.employee.user.name;
                    }
                    return '—';
                },

                applyDefaultFilterDates() {
                    this.filter_start_date = moment().startOf('month').format('YYYY-MM-DD');
                    this.filter_end_date = moment().endOf('month').format('YYYY-MM-DD');
                },

                applyDefaultFormDates() {
                    this.voucher_date = moment().format('YYYY-MM-DD');
                    this.month = moment().format('MMMM');
                    this.year = moment().format('YYYY');
                    this.payment_mode = 'bank';
                },

                syncPeriodFromVoucherDate() {
                    if (!this.voucher_date) {
                        return;
                    }
                    this.month = moment(this.voucher_date).format('MMMM');
                    this.year = moment(this.voucher_date).format('YYYY');
                },

                validateBeforeSubmit(e) {
                    this.$validator.validateAll();

                    if (!this.employeeSalary && this.emp_id) {
                        Vue.$toast.error(
                            'No active salary found for this employee. Please define salary first.',
                            'Error'
                        );
                        return false;
                    }

                    if (!this.payment_mode) {
                        Vue.$toast.error('Payment mode is required.', 'Error');
                        return false;
                    }

                    if (!(parseFloat(this.advance_amount) > 0)) {
                        Vue.$toast.error('Advance amount must be greater than zero.', 'Error');
                        return false;
                    }

                    if (this.advanceExceedsBalance) {
                        Vue.$toast.error(
                            (this.payment_mode === 'cash' ? 'Cash' : 'Bank') +
                            ' has insufficient balance. Available: ' +
                            Number(this.availablePaymentBalance).toFixed(2),
                            'Error'
                        );
                        return false;
                    }

                    if (!this.errors.any()) {
                        this.submitForm()
                    }
                },
                submitForm() {
                    this.addEditSubmitForm()
                },

                changePage: function(page) {
                    this.pagination.current_page = page;
                    app.getSalaryList(page);
                },

                show: function() {
                    $('#salaryModal').modal('show');
                },

                hide: function() {
                    $('#salaryModal').modal('hide');
                },

                addEditSubmitForm: function() {
                    app.submit_button_flag = true;
                    url = "api/salary-advance/store";

                    let formData = new FormData();
                    formData.append('emp_salary_advance_id', this.emp_salary_advance_id);
                    formData.append('emp_id', this.emp_id);
                    formData.append('month', this.month);
                    formData.append('year', this.year);
                    formData.append('voucher_date', this.voucher_date);
                    formData.append('advance_amount', this.advance_amount);
                    formData.append('payment_mode', this.payment_mode);
                    formData.append('narrations', this.narrations || '');

                    axios.post(url, formData).then(function(response) {
                        if (response.data.errorcode == 0) {
                            app.loading = false;
                            Vue.$toast.success(response.data.errormessage, 'Success Alert');
                            app.submit_button_flag = false;
                            app.formSubmitted = true;
                            app.formErrors = {};
                            $('#salaryModal').modal('hide');
                            app.getSalaryList(app.pagination.current_page);
                        } else {
                            app.loading = false;
                            Vue.$toast.error(response.data.errormessage || 'Something went wrong.',
                                'Danger Alert');
                            app.submit_button_flag = false;
                        }
                    }).catch(function(err) {
                        app.submit_button_flag = false;
                        if (err.response && err.response.data) {
                            if (err.response.data.errormessage) {
                                Vue.$toast.error(err.response.data.errormessage, 'Error');
                            } else if (err.response.data.message) {
                                Vue.$toast.error(err.response.data.message, 'Error');
                            }
                            if (err.response.data.errors) {
                                app.formErrors = err.response.data.errors;
                            }
                        }
                    });
                },

                popupModal: function(data) {
                    if (data && data.emp_salary_advance_id > 0) {
                        app.emp_salary_advance_id = data.emp_salary_advance_id;
                        app.emp_id = data.emp_id;
                        app.year = data.year;
                        app.month = data.month;
                        app.voucher_date = data.voucher_date;
                        app.advance_amount = data.advance_amount;
                        app.payment_mode = data.payment_mode || 'bank';
                        app.narrations = data.narrations;
                        app.original_advance_amount = data.advance_amount || 0;
                        app.original_payment_mode = data.payment_mode || '';
                        app.formSubmitted = false;
                        app.formErrors = {};
                        app.modelHeading = "Edit Salary Advance";
                        app.getEmployeeSalary();
                        $('#salaryModal').modal('show');
                    } else {
                        app.emp_salary_advance_id = '';
                        app.emp_id = '';
                        app.advance_amount = '';
                        app.narrations = '';
                        app.employeeSalary = null;
                        app.original_advance_amount = 0;
                        app.original_payment_mode = '';
                        app.applyDefaultFormDates();
                        app.formSubmitted = false;
                        app.formErrors = {};
                        app.modelHeading = "Add Salary Advance";
                        $('#salaryModal').modal('show');
                    }
                },

                getSalaryList: function(page = 1) {
                    if (this.filter_start_date && this.filter_end_date &&
                        this.filter_start_date > this.filter_end_date) {
                        Vue.$toast.error('Date From cannot be after Date To.', 'Error');
                        return;
                    }

                    url = "api/salary-advance/list";

                    axios.post(url, {
                        "page": page,
                        "page_length": this.page_length,
                        "emp_id": this.filter_emp_id,
                        "filter_start_date": this.filter_start_date,
                        "filter_end_date": this.filter_end_date,
                        "filter_payment_mode": this.filter_payment_mode,
                    }).then(function(response) {
                        app.salaryList = response.data.data.data.data;
                        app.pagination = response.data.data.pagination;
                        app.filteredTotalAmount = (response.data.data.summary &&
                            response.data.data.summary.total_amount) || 0;
                        app.loading = false;
                    });
                },

                resetFilter: function() {
                    app.filter_emp_id = '';
                    app.filter_payment_mode = '';
                    app.applyDefaultFilterDates();
                    app.getSalaryList(1);
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

                getEmployeeSalary: function() {
                    if (!this.emp_id) {
                        this.employeeSalary = null;
                        return;
                    }

                    url = "api/salary/get-by-employee";

                    axios.post(url, {
                        emp_id: this.emp_id
                    }).then(function(response) {
                        if (response.data.errorcode == 0 && response.data.data && response.data.data
                            .data) {
                            app.employeeSalary = response.data.data.data;
                        } else {
                            app.employeeSalary = null;
                        }
                    }).catch(function() {
                        app.employeeSalary = null;
                    });
                },
            },
            mounted() {
                this.applyDefaultFilterDates();
                this.getSalaryList();
                this.getEmployeeList();
            }
        });
    </script>
@endsection
