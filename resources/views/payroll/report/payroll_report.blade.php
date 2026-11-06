@extends('payroll.layouts.base')

<style>
    .pay-report-filters .filters-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        font-weight: 500;
        margin: 0 0 4px;
    }

    .pay-report-filters .filters-hint {
        font-size: 12px;
        color: #6c757d;
        margin: 0 0 16px;
    }

    .pay-report-filters .filter-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 6px;
    }

    .pay-report-filters .filter-control {
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

    .pay-report-filters .filter-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        min-height: 38px;
    }

    .pay-report-filters .pay-employee-select {
        width: 100%;
    }

    .pay-report-filters .select2-container {
        width: 100% !important;
    }

    .pay-report-filters .select2-container--default .select2-selection--single {
        height: 38px;
        border: none;
        border-radius: 999px;
        background: #F0F0F3;
        outline: none;
        box-shadow: none;
    }

    .pay-report-filters .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 14px;
        padding-right: 28px;
        color: #1D1D1D;
        font-size: 14px;
    }

    .pay-report-filters .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
        right: 10px;
    }

    .pay-report-filters .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }

    .pay-report-filters .select2-container--default .select2-selection--single .select2-selection__clear {
        margin-right: 6px;
    }

    @media (min-width: 992px) {
        .pay-report-filters .filter-actions {
            justify-content: flex-end;
        }
    }

    .pay-report-results .list-toolbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .pay-report-results .list-title {
        color: #1D1D1D;
        font-size: 1.15rem;
        font-weight: 600;
        margin: 0;
    }

    .pay-report-results .list-meta {
        display: block;
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    .pay-report-results .export-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .pay-report-results .export-btn {
        border-radius: 999px;
        font-weight: 600;
        font-size: 12px;
        padding: 8px 14px;
        border: none;
        cursor: pointer;
    }

    .pay-report-results .export-btn.is-print {
        background: rgba(82, 168, 217, 0.15);
        color: #52A8D9;
    }

    .pay-report-results .export-btn.is-pdf {
        background: rgba(208, 52, 44, 0.12);
        color: #D0342C;
    }

    .pay-report-results .export-btn.is-excel {
        background: rgba(25, 153, 0, 0.15);
        color: #199900;
    }

    .pay-report-results .amount-cell {
        font-variant-numeric: tabular-nums;
        font-weight: 600;
        white-space: nowrap;
    }

    .pay-report-results .emp-link {
        color: #52A8D9;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    .pay-report-results .emp-link:hover {
        text-decoration: underline;
    }

    .pay-report-results .empty-state,
    .pay-report-results .loading-state {
        text-align: center;
        padding: 36px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }

    .pay-report-results .empty-state i,
    .pay-report-results .loading-state i {
        font-size: 28px;
        margin-bottom: 10px;
        color: #b7b7b7;
    }

    .pay-report-results .tab-pagination {
        margin-top: 16px;
    }
</style>

@section('content')
    <div id="app" class="pay-report">
        <div class="fbox-shadow mt-2 fd-card respo-margin-top pay-report-filters">
            <div class="card-body">
                <h5 class="filters-title">{{ __('Payroll Report') }}</h5>
                <p class="filters-hint">
                    {{ __('Filter payroll records by employee and payroll period. Dates default to the current month through today.') }}
                </p>

                <div class="row align-items-end g-2">
                    <div class="form-group col-lg-4 col-md-6 mb-3 mb-lg-0">
                        <label class="filter-label" for="filter_emp_id">{{ __('Employee') }}</label>
                        <select class="pay-employee-select filter-control"
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

                    <div class="form-group col-lg-4 col-md-12 mb-0">
                        <label class="filter-label d-none d-lg-block">&nbsp;</label>
                        <div class="filter-actions">
                            <a href="javascript:void(0)" class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                                v-on:click="resetFilter()">
                                <i class="fa fa-refresh"></i> {{ __('Reset') }}
                            </a>
                            <a href="javascript:void(0)" class="btn btn-sm btn-primary btn_dark-x"
                                v-on:click="getPayrollList(1)">
                                <i class="fa fa-search"></i> {{ __('Search') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fbox-shadow mt-3 fd-card respo-margin-top pay-report-results">
            <div class="card-body">
                <div class="list-toolbar">
                    <div>
                        <h5 class="list-title">{{ __('Payroll Results') }}</h5>
                        <span class="list-meta">
                            @{{ formatDateDMY(filter_start_date) || '—' }} → @{{ formatDateDMY(filter_end_date) || '—' }}
                        </span>
                    </div>
                    <div class="export-actions" v-if="!loading && payrollList.length">
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
                    <div class="empty-state" v-if="!payrollList || !payrollList.length">
                        <div><i class="fa fa-file-text-o"></i></div>
                        <div><strong>{{ __('No payroll records found') }}</strong></div>
                        <div>{{ __('Try adjusting the employee or date range.') }}</div>
                    </div>

                    <template v-else>
                        <div class="table-responsive">
                            <table class="table brownz-table-list-v1">
                                <thead>
                                    <tr>
                                        <th style="width: 56px;">{{ __('SL#') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th class="text-end">{{ __('Salary') }}</th>
                                        <th class="text-end">{{ __('Advance') }}</th>
                                        <th class="text-end">{{ __('Leave') }}</th>
                                        <th class="text-end">{{ __('Deduction') }}</th>
                                        <th class="text-end">{{ __('Payable Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in payrollList" :key="'pay-' + data.payroll_id">
                                        <th scope="row">
                                            @{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}
                                        </th>
                                        <td>
                                            <a href="javascript:void(0)" class="emp-link"
                                                v-on:click="openModal(data.payroll_id)">
                                                @{{ employeeName(data) }}
                                            </a>
                                        </td>
                                        <td>@{{ formatPeriod(data) }}</td>
                                        <td class="text-end amount-cell">@{{ formatMoney(data.basic_salary) }}</td>
                                        <td class="text-end amount-cell">@{{ formatMoney(data.advance) }}</td>
                                        <td class="text-end amount-cell">@{{ formatMoney(data.approve_leave) }}</td>
                                        <td class="text-end amount-cell">@{{ formatMoney(data.deduction) }}</td>
                                        <td class="text-end amount-cell">@{{ formatMoney(data.payable_amount) }}</td>
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

        <div class="modal fade brownz-custom-popup" id="view-payroll-popup" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Payroll Info') }}</h4>
                        <button type="button" class="bootbox-close-button close" data-dismiss="modal"
                            aria-label="Close" v-on:click="hideModal()">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table brownz-table-list-v1 mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('TA') }}</th>
                                        <th>{{ __('DA') }}</th>
                                        <th>{{ __('HRA') }}</th>
                                        <th>{{ __('SA') }}</th>
                                        <th>{{ __('Professional Tax') }}</th>
                                        <th>{{ __('TDS') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>@{{ payrollsList.TA || '—' }}</td>
                                        <td>@{{ payrollsList.DA || '—' }}</td>
                                        <td>@{{ payrollsList.HRA || '—' }}</td>
                                        <td>@{{ payrollsList.special_allowance || '—' }}</td>
                                        <td>@{{ payrollsList.professional_tax_amount || '—' }}</td>
                                        <td>@{{ payrollsList.TDS_amount || '—' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="brownz-btn-yellow-d" v-on:click="hideModal()">
                            ✖ {{ __('Close') }}
                        </button>
                    </div>
                </div>
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
            payrollList: [],
            payrollsList: {
                TA: '',
                DA: '',
                HRA: '',
                special_allowance: '',
                professional_tax_amount: '',
                TDS_amount: '',
            },
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
                }).off('change.pay-report').on('change.pay-report', (e) => {
                    this.filter_emp_id = $(e.target).val() || '';
                });

                if (this.filter_emp_id) {
                    $select.val(this.filter_emp_id).trigger('change.select2');
                } else {
                    $select.val(null).trigger('change');
                }
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
                if (row && row.employee && row.employee.name) {
                    return row.employee.name;
                }
                return row.emp_name || '—';
            },
            formatPeriod: function(row) {
                if (row && row.month && row.year) {
                    return row.month + ' ' + row.year;
                }
                return '—';
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
            hideModal: function() {
                $('#view-payroll-popup').modal('hide');
            },
            changePage: function(page) {
                this.pagination.current_page = page;
                this.getPayrollList(page);
            },
            getPayrollList: function(page) {
                page = page || 1;
                this.loading = true;

                axios.post("{{ route('payroll_vue_list') }}", {
                    page: page,
                    page_length: this.page_length,
                    emp_id: this.filter_emp_id,
                    start_date: this.filter_start_date,
                    end_date: this.filter_end_date,
                }).then((response) => {
                    var payload = response.data && response.data.data ? response.data.data : {};
                    this.payrollList = this.extractList(payload);
                    this.pagination = payload.pagination || this.pagination;
                }).catch(() => {
                    this.payrollList = [];
                }).finally(() => {
                    this.loading = false;
                });
            },
            openModal: function(payrollId) {
                axios.post('/api/view-payroll-data', {
                    payroll_id: payrollId,
                }).then((response) => {
                    this.payrollsList = response.data.result || {};
                    $('#view-payroll-popup').modal('show');
                }).catch(() => {
                    this.payrollsList = {};
                });
            },
            resetFilter: function() {
                this.applyDefaultDates();
                this.filter_emp_id = '';
                window.vocoDestroySelect2($('#filter_emp_id'));
                this.$nextTick(() => {
                    this.initSelect2();
                });
                this.getPayrollList(1);
            },
            exportQuery: function() {
                return '?emp_id=' + encodeURIComponent(this.filter_emp_id || '')
                    + '&start_date=' + encodeURIComponent(this.filter_start_date || '')
                    + '&end_date=' + encodeURIComponent(this.filter_end_date || '');
            },
            printReport: function() {
                var printWindow = window.open(
                    "{{ route('payroll-report.print') }}" + this.exportQuery(),
                    '_blank',
                    'noopener,noreferrer'
                );

                if (!printWindow) {
                    alert('{{ __('Please allow popups to print the payroll report.') }}');
                }
            },
            exportToPDF: function() {
                window.open("{{ route('payroll-report.export-to-pdf') }}" + this.exportQuery(), '_self');
            },
            exportToExcel: function() {
                window.open("{{ route('payroll-report.export-to-excel') }}" + this.exportQuery(), '_self');
            },
        },
        mounted: function() {
            this.applyDefaultDates();
            this.getPayrollList(1);
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
