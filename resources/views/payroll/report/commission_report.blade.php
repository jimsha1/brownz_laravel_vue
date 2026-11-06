@extends('payroll.layouts.base')

<style>
    .com-report-filters .filters-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        font-weight: 500;
        margin: 0 0 4px;
    }

    .com-report-filters .filters-hint {
        font-size: 12px;
        color: #6c757d;
        margin: 0 0 16px;
    }

    .com-report-filters .filter-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 6px;
    }

    .com-report-filters .filter-control {
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

    .com-report-filters .filter-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        min-height: 38px;
    }

    .com-report-filters .com-employee-select {
        width: 100%;
    }

    .com-report-filters .select2-container {
        width: 100% !important;
    }

    .com-report-filters .select2-container--default .select2-selection--single {
        height: 38px;
        border: none;
        border-radius: 999px;
        background: #F0F0F3;
        outline: none;
        box-shadow: none;
    }

    .com-report-filters .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 14px;
        padding-right: 28px;
        color: #1D1D1D;
        font-size: 14px;
    }

    .com-report-filters .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
        right: 10px;
    }

    .com-report-filters .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }

    .com-report-filters .select2-container--default .select2-selection--single .select2-selection__clear {
        margin-right: 6px;
    }

    @media (min-width: 992px) {
        .com-report-filters .filter-actions {
            justify-content: flex-end;
        }
    }

    .com-report-results .list-toolbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .com-report-results .list-title {
        color: #1D1D1D;
        font-size: 1.15rem;
        font-weight: 600;
        margin: 0;
    }

    .com-report-results .list-meta {
        display: block;
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    .com-report-results .export-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .com-report-results .export-btn {
        border-radius: 999px;
        font-weight: 600;
        font-size: 12px;
        padding: 8px 14px;
        border: none;
        cursor: pointer;
    }

    .com-report-results .export-btn.is-print {
        background: rgba(82, 168, 217, 0.15);
        color: #52A8D9;
    }

    .com-report-results .export-btn.is-pdf {
        background: rgba(208, 52, 44, 0.12);
        color: #D0342C;
    }

    .com-report-results .export-btn.is-excel {
        background: rgba(25, 153, 0, 0.15);
        color: #199900;
    }

    .com-report-results .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .com-report-results .summary-card {
        background: #F7F7F9;
        border-radius: 14px;
        padding: 14px 16px;
    }

    .com-report-results .summary-card .label {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 4px;
    }

    .com-report-results .summary-card .value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1D1D1D;
        font-variant-numeric: tabular-nums;
    }

    .com-report-results .emp-link {
        color: #52A8D9;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    .com-report-results .emp-link:hover {
        text-decoration: underline;
    }

    .com-report-results .amount-cell {
        font-variant-numeric: tabular-nums;
        font-weight: 600;
        white-space: nowrap;
    }

    .com-report-results .empty-state,
    .com-report-results .loading-state {
        text-align: center;
        padding: 36px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }

    .com-report-results .empty-state i,
    .com-report-results .loading-state i {
        font-size: 28px;
        margin-bottom: 10px;
        color: #b7b7b7;
    }

    .com-report-results .tab-pagination {
        margin-top: 16px;
    }
</style>

@section('content')
    <div id="app" class="com-report">
        <div class="fbox-shadow mt-2 fd-card respo-margin-top com-report-filters">
            <div class="card-body">
                <h5 class="filters-title">{{ __('Commission Report') }}</h5>
                <p class="filters-hint">
                    {{ __('Filter commission records by employee and date range. Dates default to the current month through today.') }}
                </p>

                <div class="row align-items-end g-2">
                    <div class="form-group col-lg-4 col-md-6 mb-3 mb-lg-0">
                        <label class="filter-label" for="filter_emp_id">{{ __('Employee') }}</label>
                        <select class="com-employee-select filter-control"
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
                                v-on:click="getCommissionList(1)">
                                <i class="fa fa-search"></i> {{ __('Search') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fbox-shadow mt-3 fd-card respo-margin-top com-report-results">
            <div class="card-body">
                <div class="list-toolbar">
                    <div>
                        <h5 class="list-title">{{ __('Commission Results') }}</h5>
                        <span class="list-meta">
                            @{{ formatDateDMY(filter_start_date) || '—' }} → @{{ formatDateDMY(filter_end_date) || '—' }}
                        </span>
                    </div>
                    <div class="export-actions" v-if="!loading && commissionList.length">
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
                    <div class="empty-state" v-if="!commissionList || !commissionList.length">
                        <div><i class="fa fa-money"></i></div>
                        <div><strong>{{ __('No commission records found') }}</strong></div>
                        <div>{{ __('Try adjusting the employee or date range.') }}</div>
                    </div>

                    <template v-else>
                        <div class="summary-cards">
                            <div class="summary-card">
                                <div class="label">{{ __('Records') }}</div>
                                <div class="value">@{{ pagination.total || commissionList.length }}</div>
                            </div>
                            <div class="summary-card">
                                <div class="label">{{ __('Total Commission') }}</div>
                                <div class="value">@{{ formatMoney(pageTotalCommission) }}</div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table brownz-table-list-v1">
                                <thead>
                                    <tr>
                                        <th style="width: 56px;">{{ __('SL#') }}</th>
                                        <th>{{ __('Employee Name') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th class="text-end">{{ __('Commission Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in commissionList" :key="'com-' + data.commission_id">
                                        <th scope="row">@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</th>
                                        <td>
                                            <a href="javascript:void(0)" class="emp-link"
                                                v-on:click="openModal(data.commission_id)">
                                                @{{ (data.employee && data.employee.name) || '—' }}
                                            </a>
                                        </td>
                                        <td>@{{ formatDate(data.start_date) }}</td>
                                        <td class="text-end amount-cell">@{{ formatMoney(data.commission_amount) }}</td>
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

        <div class="modal fade brownz-custom-popup" id="view-commission-popup" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Commission Info') }}</h4>
                        <button type="button" class="bootbox-close-button close" data-dismiss="modal"
                            aria-label="Close" v-on:click="hideModal()">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table brownz-table-list-v1 mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Service') }}</th>
                                        <th>{{ __('Percentage') }}</th>
                                        <th class="text-end">{{ __('Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="!commissionServicesList.length">
                                        <td colspan="3">{{ __('No service breakdown available.') }}</td>
                                    </tr>
                                    <tr v-for="service in commissionServicesList"
                                        :key="service.split_service_amount_id">
                                        <td>@{{ service.service_name || '—' }}</td>
                                        <td>@{{ service.commission_percentage || '—' }}</td>
                                        <td class="text-end">@{{ formatMoney(service.commission_amount) }}</td>
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
            commissionList: [],
            commissionServicesList: [],
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
            },
            pageTotalCommission: function() {
                return (this.commissionList || []).reduce(function(total, row) {
                    return total + (parseFloat(row.commission_amount) || 0);
                }, 0);
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
                }).off('change.com-report').on('change.com-report', (e) => {
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
                $('#view-commission-popup').modal('hide');
            },
            changePage: function(page) {
                this.pagination.current_page = page;
                this.getCommissionList(page);
            },
            getCommissionList: function(page) {
                page = page || 1;
                this.loading = true;

                axios.post("{{ route('commission.vue-list') }}", {
                    page: page,
                    page_length: this.page_length,
                    emp_id: this.filter_emp_id,
                    start_date: this.filter_start_date,
                    end_date: this.filter_end_date,
                }).then((response) => {
                    var payload = response.data && response.data.data ? response.data.data : {};
                    this.commissionList = this.extractList(payload);
                    this.pagination = payload.pagination || this.pagination;
                }).catch(() => {
                    this.commissionList = [];
                }).finally(() => {
                    this.loading = false;
                });
            },
            resetFilter: function() {
                this.applyDefaultDates();
                this.filter_emp_id = '';
                window.vocoDestroySelect2($('#filter_emp_id'));
                this.$nextTick(() => {
                    this.initSelect2();
                });
                this.getCommissionList(1);
            },
            exportQuery: function() {
                return '?emp_id=' + encodeURIComponent(this.filter_emp_id || '')
                    + '&start_date=' + encodeURIComponent(this.filter_start_date || '')
                    + '&end_date=' + encodeURIComponent(this.filter_end_date || '');
            },
            exportToPDF: function() {
                window.open("{{ route('commission-report.export-to-pdf') }}" + this.exportQuery(), '_self');
            },
            printReport: function() {
                var printWindow = window.open(
                    "{{ route('commission-report.print') }}" + this.exportQuery(),
                    '_blank',
                    'noopener,noreferrer'
                );

                if (!printWindow) {
                    alert('{{ __('Please allow popups to print the commission report.') }}');
                }
            },
            exportToExcel: function() {
                window.open("{{ route('commission-report.export-to-excel') }}" + this.exportQuery(), '_self');
            },
            openModal: function(commissionId) {
                axios.post('/api/view-commission-data', {
                    commission_id: commissionId,
                }).then(response => {
                    this.commissionServicesList = response.data.data.employee.user.split || [];
                    $('#view-commission-popup').modal('show');
                }).catch(() => {
                    this.commissionServicesList = [];
                });
            }
        },
        mounted: function() {
            this.applyDefaultDates();
            this.getCommissionList(1);
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
