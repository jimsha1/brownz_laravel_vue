@extends('payroll.layouts.base')

<style>
    .att-report-filters .filters-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        font-weight: 500;
        margin: 0 0 4px;
    }

    .att-report-filters .filters-hint {
        font-size: 12px;
        color: #6c757d;
        margin: 0 0 16px;
    }

    .att-report-filters .filter-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 6px;
    }

    .att-report-filters .filter-control {
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

    .att-report-filters .filter-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        min-height: 38px;
    }

    .att-report-filters .att-employee-select {
        width: 100%;
    }

    .att-report-filters .select2-container {
        width: 100% !important;
    }

    .att-report-filters .select2-container--default .select2-selection--single {
        height: 38px;
        border: none;
        border-radius: 999px;
        background: #F0F0F3;
        outline: none;
        box-shadow: none;
    }

    .att-report-filters .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 14px;
        padding-right: 28px;
        color: #1D1D1D;
        font-size: 14px;
    }

    .att-report-filters .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
        right: 10px;
    }

    .att-report-filters .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }

    .att-report-filters .select2-container--default .select2-selection--single .select2-selection__clear {
        margin-right: 6px;
    }

    @media (min-width: 992px) {
        .att-report-filters .filter-actions {
            justify-content: flex-end;
        }
    }

    .att-report-results .list-toolbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .att-report-results .list-title {
        color: #1D1D1D;
        font-size: 1.15rem;
        font-weight: 600;
        margin: 0;
    }

    .att-report-results .list-meta {
        display: block;
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    .att-report-results .export-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .att-report-results .export-btn {
        border-radius: 999px;
        font-weight: 600;
        font-size: 12px;
        padding: 8px 14px;
        border: none;
        cursor: pointer;
    }

    .att-report-results .export-btn.is-print {
        background: rgba(82, 168, 217, 0.15);
        color: #52A8D9;
    }

    .att-report-results .export-btn.is-pdf {
        background: rgba(208, 52, 44, 0.12);
        color: #D0342C;
    }

    .att-report-results .export-btn.is-excel {
        background: rgba(25, 153, 0, 0.15);
        color: #199900;
    }

    .att-report-results .empty-state,
    .att-report-results .loading-state {
        text-align: center;
        padding: 36px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }

    .att-report-results .empty-state i,
    .att-report-results .loading-state i {
        font-size: 28px;
        margin-bottom: 10px;
        color: #b7b7b7;
    }

    .att-report-results .tab-pagination {
        margin-top: 16px;
    }

    .att-chip {
        display: inline-block;
        border-radius: 999px;
        padding: 2px 8px;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        white-space: nowrap;
        margin: 1px 2px 1px 0;
    }

    .att-chip.present {
        background: #e8f7ee;
        color: #1b7a3d;
    }

    .att-chip.absent {
        background: #fdecec;
        color: #b42318;
    }

    .att-chip.late {
        background: #fff4e5;
        color: #b54708;
    }

    .att-chip.overtime {
        background: #fdecec;
        color: #b42318;
    }

    .att-chip.missing {
        background: #f3f4f6;
        color: #6b7280;
    }

    .att-time-stack {
        display: flex;
        flex-direction: column;
        gap: 2px;
        line-height: 1.25;
    }

    .att-muted {
        color: #9ca3af;
        font-size: 0.78rem;
    }

    .att-emp-meta {
        font-size: 0.78rem;
        color: #6b7280;
    }
</style>

@section('content')
    <div id="app" class="att-report">
        <div class="fbox-shadow mt-2 fd-card respo-margin-top att-report-filters">
            <div class="card-body">
                <h5 class="filters-title">{{ __('Attendance Report') }}</h5>
                <p class="filters-hint">
                    {{ __('Filter attendance by employee, date range, and status. Dates default to the current month through today.') }}
                </p>

                <div class="row align-items-end g-2">
                    <div class="form-group col-lg-3 col-md-6 mb-3 mb-lg-0">
                        <label class="filter-label" for="filter_emp_id">{{ __('Employee') }}</label>
                        <select class="att-employee-select filter-control"
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
                        <label class="filter-label" for="filter_attendance_status">{{ __('Attendance Type') }}</label>
                        <select class="filter-control" id="filter_attendance_status" v-model="filter_attendance_status">
                            <option value="">{{ __('All') }}</option>
                            <option value="present">{{ __('Present') }}</option>
                            <option value="absent">{{ __('Absent / Leave') }}</option>
                            <option value="late">{{ __('Late') }}</option>
                            <option value="over_time">{{ __('Over Time') }}</option>
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
                                v-on:click="getAttendanceList(1)">
                                <i class="fa fa-search"></i> {{ __('Search') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fbox-shadow mt-3 fd-card respo-margin-top att-report-results">
            <div class="card-body">
                <div class="list-toolbar">
                    <div>
                        <h5 class="list-title">{{ __('Attendance Results') }}</h5>
                        <span class="list-meta">
                            @{{ formatDateDMY(filter_start_date) || '—' }} → @{{ formatDateDMY(filter_end_date) || '—' }}
                            <span v-if="!loading && pagination.total"> · @{{ pagination.total }} {{ __('records') }}</span>
                        </span>
                    </div>
                    <div class="export-actions" v-if="!loading && attendanceList.length">
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
                    <div class="empty-state" v-if="!attendanceList || !attendanceList.length">
                        <div><i class="fa fa-calendar-check-o"></i></div>
                        <div><strong>{{ __('No attendance records found') }}</strong></div>
                        <div>{{ __('Try adjusting the employee, date range, or attendance type.') }}</div>
                    </div>

                    <template v-else>
                        <div class="table-responsive">
                            <table class="table brownz-table-list-v1">
                                <thead>
                                    <tr>
                                        <th style="width: 56px;">{{ __('SL#') }}</th>
                                        <th>{{ __('Employee Name') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Shift') }}</th>
                                        <th>{{ __('Check in') }}</th>
                                        <th>{{ __('Check out') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in attendanceList"
                                        :key="'att-' + (data.attendence_id || index)">
                                        <th scope="row">
                                            @{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}
                                        </th>
                                        <td>
                                            <div>@{{ data.employee_name || '—' }}</div>
                                            <div class="att-emp-meta" v-if="data.employee_code">@{{ data.employee_code }}</div>
                                        </td>
                                        <td>@{{ formatDate(data.date) }}</td>
                                        <td>
                                            <span v-if="data.shift_start_time || data.shift_end_time">
                                                @{{ formatTime(data.shift_start_time) || '—' }} – @{{ formatTime(data.shift_end_time) || '—' }}
                                            </span>
                                            <span class="att-muted" v-else>{{ __('No shift') }}</span>
                                        </td>
                                        <td>
                                            <div class="att-time-stack">
                                                <span v-if="data.check_in">@{{ formatTime(data.check_in) }}</span>
                                                <span class="att-muted" v-else>—:—</span>
                                                <span class="att-chip late" v-if="data.is_late">{{ __('Late') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="att-time-stack">
                                                <span v-if="data.check_out">@{{ formatTime(data.check_out) }}</span>
                                                <span class="att-muted" v-else>—:—</span>
                                                <span class="att-chip overtime" v-if="data.is_overtime">{{ __('OT') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="att-chip" :class="statusChipClass(data)">
                                                @{{ statusLabel(data) }}
                                            </span>
                                        </td>
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
            attendanceList: [],
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
            filter_attendance_status: '',
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
                }).off('change.att-report').on('change.att-report', (e) => {
                    this.filter_emp_id = $(e.target).val() || '';
                });

                if (this.filter_emp_id) {
                    $select.val(this.filter_emp_id).trigger('change.select2');
                } else {
                    $select.val(null).trigger('change');
                }
            },
            formatTime: function(time) {
                if (!time) {
                    return '';
                }
                var parts = String(time).split(':');
                if (parts.length < 2) {
                    return time;
                }
                var hours = parseInt(parts[0], 10);
                if (isNaN(hours)) {
                    return time;
                }
                var minutes = parts[1];
                var amPm = hours >= 12 ? 'PM' : 'AM';
                var formattedHours = hours % 12 || 12;
                return formattedHours + ':' + minutes + ' ' + amPm;
            },
            formatDate: function(date) {
                if (!date) {
                    return '—';
                }
                if (typeof moment !== 'undefined') {
                    return moment(date).format('DD MMM YYYY');
                }
                var parts = String(date).split('-');
                if (parts.length < 3) {
                    return date;
                }
                return parts[2] + '-' + parts[1] + '-' + parts[0];
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
            statusLabel: function(data) {
                if (!data.attendance_status) {
                    return '{{ __('Not marked') }}';
                }
                return String(data.attendance_status).toUpperCase();
            },
            statusChipClass: function(data) {
                if (data.attendance_status === 'present') {
                    return 'present';
                }
                if (data.attendance_status === 'absent') {
                    return 'absent';
                }
                return 'missing';
            },
            extractList: function(payload) {
                if (!payload) {
                    return [];
                }
                if (Array.isArray(payload.data)) {
                    return payload.data;
                }
                if (payload.data && Array.isArray(payload.data.data)) {
                    return payload.data.data;
                }
                if (Array.isArray(payload)) {
                    return payload;
                }
                return [];
            },
            changePage: function(page) {
                this.pagination.current_page = page;
                this.getAttendanceList(page);
            },
            getAttendanceList: function(page) {
                page = page || 1;
                this.loading = true;

                axios.post("{{ route('attendance_vue_list') }}", {
                    page: page,
                    page_length: this.page_length,
                    emp_id: this.filter_emp_id,
                    start_date: this.filter_start_date,
                    end_date: this.filter_end_date,
                    attendanceType: this.filter_attendance_status,
                }).then((response) => {
                    var payload = response.data && response.data.data ? response.data.data : {};
                    this.attendanceList = this.extractList(payload);
                    this.pagination = payload.pagination || this.pagination;
                }).catch(() => {
                    this.attendanceList = [];
                }).finally(() => {
                    this.loading = false;
                });
            },
            resetFilter: function() {
                this.applyDefaultDates();
                this.filter_emp_id = '';
                this.filter_attendance_status = '';
                window.vocoDestroySelect2($('#filter_emp_id'));
                this.$nextTick(() => {
                    this.initSelect2();
                });
                this.getAttendanceList(1);
            },
            exportQuery: function() {
                return '?emp_id=' + encodeURIComponent(this.filter_emp_id || '')
                    + '&start_date=' + encodeURIComponent(this.filter_start_date || '')
                    + '&end_date=' + encodeURIComponent(this.filter_end_date || '')
                    + '&attendanceType=' + encodeURIComponent(this.filter_attendance_status || '');
            },
            printReport: function() {
                var printWindow = window.open(
                    "{{ route('attendance-report.print') }}" + this.exportQuery(),
                    '_blank',
                    'noopener,noreferrer'
                );

                if (!printWindow) {
                    alert('{{ __('Please allow popups to print the attendance report.') }}');
                }
            },
            exportToPDF: function() {
                window.open("{{ route('attendance-report.export-to-pdf') }}" + this.exportQuery(), '_self');
            },
            exportToExcel: function() {
                window.open("{{ route('attendance-report.export-to-excel') }}" + this.exportQuery(), '_self');
            },
        },
        mounted: function() {
            this.applyDefaultDates();
            this.getAttendanceList(1);
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
