@extends('payroll.layouts.base')

<style>
    .sal-report-filters .filters-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        font-weight: 500;
        margin: 0 0 4px;
    }

    .sal-report-filters .filters-hint {
        font-size: 12px;
        color: #6c757d;
        margin: 0 0 16px;
    }

    .sal-report-filters .filter-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 6px;
    }

    .sal-report-filters .filter-control {
        height: 38px;
        border: none;
        outline: none;
        border-radius: 999px;
        background: #F0F0F3;
        padding: 8px 14px;
        font-size: 14px;
        color: #1D1D1D;
        width: 100%;
    }

    .sal-report-filters .filter-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        min-height: 38px;
    }

    .sal-report-filters .sal-employee-select {
        width: 100%;
    }

    .sal-report-filters .select2-container {
        width: 100% !important;
    }

    .sal-report-filters .select2-container--default .select2-selection--single {
        height: 38px;
        border: none;
        border-radius: 999px;
        background: #F0F0F3;
        outline: none;
        box-shadow: none;
    }

    .sal-report-filters .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 14px;
        padding-right: 28px;
        color: #1D1D1D;
        font-size: 14px;
    }

    .sal-report-filters .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
        right: 10px;
    }

    .sal-report-filters .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }

    .sal-report-filters .select2-container--default .select2-selection--single .select2-selection__clear {
        margin-right: 6px;
    }

    @media (min-width: 992px) {
        .sal-report-filters .filter-actions {
            justify-content: flex-end;
        }
    }

    .sal-report-results .list-toolbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .sal-report-results .list-title {
        color: #1D1D1D;
        font-size: 1.15rem;
        font-weight: 600;
        margin: 0;
    }

    .sal-report-results .list-meta {
        display: block;
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    .sal-report-results .export-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .sal-report-results .export-btn {
        border-radius: 999px;
        font-weight: 600;
        font-size: 12px;
        padding: 8px 14px;
        border: none;
        cursor: pointer;
    }

    .sal-report-results .export-btn.is-print {
        background: rgba(82, 168, 217, 0.15);
        color: #52A8D9;
    }

    .sal-report-results .export-btn.is-pdf {
        background: rgba(208, 52, 44, 0.12);
        color: #D0342C;
    }

    .sal-report-results .export-btn.is-excel {
        background: rgba(25, 153, 0, 0.15);
        color: #199900;
    }

    .sal-report-results .amount-cell {
        font-variant-numeric: tabular-nums;
        font-weight: 600;
        white-space: nowrap;
    }

    .sal-report-results .empty-state,
    .sal-report-results .loading-state {
        text-align: center;
        padding: 36px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }

    .sal-report-results .empty-state i,
    .sal-report-results .loading-state i {
        font-size: 28px;
        margin-bottom: 10px;
        color: #b7b7b7;
    }

    .sal-report-results .tab-pagination {
        margin-top: 16px;
    }
</style>

@section('content')
    <div id="app" class="sal-report">
        <div class="fbox-shadow mt-2 fd-card respo-margin-top sal-report-filters">
            <div class="card-body">
                <h5 class="filters-title">{{ __('Salary Report') }}</h5>
                <p class="filters-hint">
                    {{ __('Filter salary payments by employee, date range, and pending balance. Dates default to the current month through today.') }}
                </p>

                <div class="row align-items-end g-2">
                    <div class="form-group col-lg-3 col-md-6 mb-3 mb-lg-0">
                        <label class="filter-label" for="filter_emp_id">{{ __('Employee') }}</label>
                        <select class="sal-employee-select filter-control"
                            id="filter_emp_id"
                            v-model="filter_emp_id"
                            data-placeholder="{{ __('Select Employee') }}">
                            <option value=""></option>
                            {!! $employees_dropdown !!}
                        </select>
                    </div>

                    <div class="form-group col-lg-2 col-md-6 mb-3 mb-lg-0">
                        <label class="filter-label" for="filter_start_date">{{ __('Start Date') }}</label>
                        <input type="date" class="filter-control" id="filter_start_date" v-model="filter_start_date">
                    </div>

                    <div class="form-group col-lg-2 col-md-6 mb-3 mb-lg-0">
                        <label class="filter-label" for="filter_end_date">{{ __('End Date') }}</label>
                        <input type="date" class="filter-control" id="filter_end_date" v-model="filter_end_date">
                    </div>

                    <div class="form-group col-lg-2 col-md-6 mb-3 mb-lg-0">
                        <label class="filter-label" for="filter_balance_amount">{{ __('Salary Filter') }}</label>
                        <select class="filter-control" id="filter_balance_amount" v-model="filter_balance_amount">
                            <option value="">{{ __('All Employees') }}</option>
                            <option value="1">{{ __('Pending Salary') }}</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-3 col-md-12 mb-0">
                        <label class="filter-label d-none d-lg-block">&nbsp;</label>
                        <div class="filter-actions">
                            <a href="javascript:void(0)" class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                                v-on:click="resetFilter()">
                                <i class="fa fa-refresh"></i> {{ __('Reset') }}
                            </a>
                            <a href="javascript:void(0)" class="btn btn-sm btn-primary btn_dark-x"
                                v-on:click="getSalaryList(1)">
                                <i class="fa fa-search"></i> {{ __('Search') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fbox-shadow mt-3 fd-card respo-margin-top sal-report-results">
            <div class="card-body">
                <div class="list-toolbar">
                    <div>
                        <h5 class="list-title">{{ __('Salary Results') }}</h5>
                        <span class="list-meta">
                            @{{ formatDateDMY(filter_start_date) || '—' }} → @{{ formatDateDMY(filter_end_date) || '—' }}
                        </span>
                    </div>
                    <div class="export-actions" v-if="!loading && salaryList.length">
                        <button type="button" class="export-btn is-print" v-on:click="printReport()">
                            <i class="fa fa-print"></i> {{ __('Print') }}
                        </button>
                        <button type="button" class="export-btn is-pdf" v-on:click="exportToPDF()">
                            <i class="fa fa-file-pdf-o"></i> {{ __('PDF') }}
                        </button>
                        <button type="button" class="export-btn is-excel" v-on:click="exportToExcel()">
                            <i class="fa fa-file-excel-o"></i> {{ __('Excel') }}
                        </button>
                    </div>
                </div>

                <div class="loading-state" v-if="loading">
                    <div><i class="fa fa-spinner fa-spin"></i></div>
                    <div>{{ __('Loading…') }}</div>
                </div>

                <template v-else>
                    <div class="empty-state" v-if="!salaryList || !salaryList.length">
                        <div><i class="fa fa-money"></i></div>
                        <div><strong>{{ __('No salary records found') }}</strong></div>
                        <div>{{ __('Try adjusting the employee, date range, or salary filter.') }}</div>
                    </div>

                    <template v-else>
                        <div class="table-responsive">
                            <table class="table brownz-table-list-v1">
                                <thead>
                                    <tr>
                                        <th style="width: 56px;">{{ __('SL#') }}</th>
                                        <th>{{ __('Voucher Date') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th class="text-end">{{ __('Net Salary') }}</th>
                                        <th class="text-end">{{ __('Advance Salary') }}</th>
                                        <th class="text-end">{{ __('Paid Amount') }}</th>
                                        <th class="text-end">{{ __('Pending Salary') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in salaryList" :key="'sal-' + data.payroll_payment_id">
                                        <th scope="row">@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</th>
                                        <td>@{{ formatDate(data.payment_date) }}</td>
                                        <td>@{{ employeeName(data) }}</td>
                                        <td class="text-end amount-cell">@{{ formatMoney(payrollField(data, 'net_salary')) }}</td>
                                        <td class="text-end amount-cell">@{{ formatMoney(payrollField(data, 'advance')) }}</td>
                                        <td class="text-end amount-cell">@{{ formatMoney(data.payment_amount) }}</td>
                                        <td class="text-end amount-cell">@{{ formatMoney(data.balance_amount) }}</td>
                                    </tr>
                                </tbody>
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
                                <li class="page-item" v-for="page in pagesNumber"
                                    :class="{ active: page == isActived }" :key="'page-' + page">
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
                </template>
            </div>
        </div>
    </div>
@endsection

@push('after-jquery')
<script>
    function pad2(n) {
        return n < 10 ? '0' + n : String(n);
    }

    function localYmd(dateObj) {
        return dateObj.getFullYear() + '-' + pad2(dateObj.getMonth() + 1) + '-' + pad2(dateObj.getDate());
    }

    function currentMonthToToday() {
        var now = new Date();
        return {
            start: localYmd(new Date(now.getFullYear(), now.getMonth(), 1)),
            end: localYmd(now)
        };
    }

    var monthRange = currentMonthToToday();

    var app = new Vue({
        el: '#app',
        data: {
            loading: false,
            salaryList: [],
            page_length: '100',
            pagination: {
                total: 0,
                per_page: 2,
                from: 1,
                to: 0,
                current_page: 1,
                last_page: 1
            },
            offset: 4,
            filter_end_date: monthRange.end,
            filter_start_date: monthRange.start,
            filter_emp_id: '',
            filter_balance_amount: '',
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
            }
        },
        methods: {
            applyDefaultDates: function() {
                var range = currentMonthToToday();
                this.filter_start_date = range.start;
                this.filter_end_date = range.end;
            },
            initSelect2: function() {
                var $select = $('#filter_emp_id');
                if (!$select.length) {
                    return;
                }

                window.vocoDestroySelect2($select);
                window.vocoInitSelect2($select, {
                    placeholder: '{{ __('Select Employee') }}',
                    allowClear: true,
                    width: '100%'
                }).off('change.sal-report').on('change.sal-report', (e) => {
                    this.filter_emp_id = $(e.target).val() || '';
                });

                if (this.filter_emp_id) {
                    $select.val(this.filter_emp_id).trigger('change.select2');
                } else {
                    $select.val(null).trigger('change');
                }
            },
            formatDate: function(date) {
                if (!date) {
                    return '—';
                }
                if (typeof moment !== 'undefined') {
                    return moment(date).format('DD MMM YYYY');
                }
                return date;
            },
            formatDateDMY: function(dateString) {
                if (!dateString) {
                    return '';
                }
                var parts = String(dateString).split('-');
                if (parts.length < 3) {
                    return dateString;
                }
                return parts[2] + '-' + parts[1] + '-' + parts[0];
            },
            formatMoney: function(value) {
                var num = parseFloat(value);
                if (isNaN(num)) {
                    return '0.00';
                }
                return num.toFixed(2);
            },
            employeeName: function(row) {
                if (row && row.payroll && row.payroll.employee && row.payroll.employee.name) {
                    return row.payroll.employee.name;
                }
                return '—';
            },
            payrollField: function(row, field) {
                if (row && row.payroll && row.payroll[field] !== undefined && row.payroll[field] !== null) {
                    return row.payroll[field];
                }
                return '0.00';
            },
            extractList: function(payload) {
                if (!payload) {
                    return [];
                }
                if (payload.data && Array.isArray(payload.data.data)) {
                    return payload.data.data;
                }
                if (Array.isArray(payload.data)) {
                    return payload.data;
                }
                if (Array.isArray(payload)) {
                    return payload;
                }
                return [];
            },
            changePage: function(page) {
                this.pagination.current_page = page;
                this.getSalaryList(page);
            },
            getSalaryList: function(page) {
                page = page || 1;
                this.loading = true;

                axios.post("{{ route('salary.vue-list') }}", {
                    page: page,
                    page_length: this.page_length,
                    emp_id: this.filter_emp_id,
                    start_date: this.filter_start_date,
                    end_date: this.filter_end_date,
                    balance_amount: this.filter_balance_amount,
                }).then((response) => {
                    var payload = response.data && response.data.data ? response.data.data : {};
                    this.salaryList = this.extractList(payload);
                    this.pagination = payload.pagination || this.pagination;
                }).catch(() => {
                    this.salaryList = [];
                }).finally(() => {
                    this.loading = false;
                });
            },
            resetFilter: function() {
                this.applyDefaultDates();
                this.filter_emp_id = '';
                this.filter_balance_amount = '';
                window.vocoDestroySelect2($('#filter_emp_id'));
                this.$nextTick(() => {
                    this.initSelect2();
                });
                this.getSalaryList(1);
            },
            exportQuery: function() {
                return '?emp_id=' + encodeURIComponent(this.filter_emp_id || '')
                    + '&start_date=' + encodeURIComponent(this.filter_start_date || '')
                    + '&end_date=' + encodeURIComponent(this.filter_end_date || '')
                    + '&balance_amount=' + encodeURIComponent(this.filter_balance_amount || '');
            },
            exportToPDF: function() {
                window.open("{{ route('salary-report.export-to-pdf') }}" + this.exportQuery(), '_self');
            },
            printReport: function() {
                var printWindow = window.open(
                    "{{ route('salary-report.print') }}" + this.exportQuery(),
                    '_blank',
                    'noopener,noreferrer'
                );

                if (!printWindow) {
                    alert('{{ __('Please allow popups to print the salary report.') }}');
                }
            },
            exportToExcel: function() {
                window.open("{{ route('salary-report.export-to-excel') }}" + this.exportQuery(), '_self');
            },
        },
        mounted: function() {
            this.applyDefaultDates();
            this.getSalaryList(1);
            this.$nextTick(() => {
                this.initSelect2();
            });
        },
        beforeDestroy: function() {
            window.vocoDestroySelect2($('#filter_emp_id'));
        }
    });
</script>
@endpush
