@extends('payroll.layouts.base')
<style>
    .wh-report-filters .filters-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        font-weight: 500;
        margin: 0 0 4px;
    }

    .wh-report-filters .filters-hint {
        font-size: 12px;
        color: #6c757d;
        margin: 0 0 16px;
    }

    .wh-report-filters .filter-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 6px;
    }

    .wh-report-filters .filter-control {
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

    .wh-report-filters .filter-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        min-height: 38px;
    }

    .wh-report-filters .wh-employee-select {
        width: 100%;
    }

    .wh-report-filters .select2-container {
        width: 100% !important;
    }

    .wh-report-filters .select2-container--default .select2-selection--single {
        height: 38px;
        border: none;
        border-radius: 999px;
        background: #F0F0F3;
        outline: none;
        box-shadow: none;
    }

    .wh-report-filters .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 14px;
        padding-right: 28px;
        color: #1D1D1D;
        font-size: 14px;
    }

    .wh-report-filters .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
        right: 10px;
    }

    .wh-report-filters .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }

    .wh-report-filters .select2-container--default .select2-selection--single .select2-selection__clear {
        margin-right: 6px;
    }

    @media (min-width: 992px) {
        .wh-report-filters .filter-actions {
            justify-content: flex-end;
        }
    }

    .wh-report-results .list-toolbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .wh-report-results .list-title {
        color: #1D1D1D;
        font-size: 1.15rem;
        font-weight: 600;
        margin: 0;
    }

    .wh-report-results .list-meta {
        display: block;
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    .wh-report-results .export-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .wh-report-results .export-btn {
        border-radius: 999px;
        font-weight: 600;
        font-size: 12px;
        padding: 8px 14px;
        border: none;
        cursor: pointer;
    }

    .wh-report-results .export-btn.is-print {
        background: rgba(82, 168, 217, 0.15);
        color: #52A8D9;
    }

    .wh-report-results .wh-nav-tabs {
        border-bottom: 1px solid #e8e8ec;
        margin-bottom: 16px;
    }

    .wh-report-results .wh-nav-tabs .nav-link {
        cursor: pointer;
        border: none;
        border-radius: 999px;
        color: #6c757d;
        font-weight: 600;
        padding: 8px 14px;
        margin-right: 6px;
        margin-bottom: 8px;
    }

    .wh-report-results .wh-nav-tabs .nav-link.active {
        background: #1D1D1D;
        color: #fff;
    }

    .wh-report-results .wh-table th,
    .wh-report-results .wh-table td {
        vertical-align: middle;
    }

    .wh-report-results .emp-name {
        font-weight: 600;
        color: #1D1D1D;
    }

    .wh-chip {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.02em;
        white-space: nowrap;
        line-height: 1.2;
    }

    .wh-chip.on-time {
        background: rgba(25, 153, 0, 0.15);
        color: #199900;
    }

    .wh-chip.early {
        background: rgba(82, 168, 217, 0.18);
        color: #1e6789;
    }

    .wh-chip.late {
        background: rgba(230, 205, 127, 0.45);
        color: #9A6700;
    }

    .wh-chip.missing {
        background: #F0F0F3;
        color: #6c757d;
    }

    .wh-chip.overtime {
        background: rgba(208, 52, 44, 0.12);
        color: #D0342C;
    }

    .wh-chip.working {
        background: rgba(25, 153, 0, 0.15);
        color: #199900;
    }

    .wh-time-stack {
        display: flex;
        flex-direction: column;
        gap: 4px;
        line-height: 1.2;
    }

    .wh-time-main {
        font-weight: 600;
        color: #1D1D1D;
        font-variant-numeric: tabular-nums;
    }

    .wh-muted {
        color: #9ca3af;
    }

    .wh-ot {
        font-weight: 600;
        color: #D0342C;
        font-variant-numeric: tabular-nums;
    }

    .wh-summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .wh-summary-card {
        background: #F7F7F9;
        border-radius: 14px;
        padding: 14px 16px;
    }

    .wh-summary-card .label {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 4px;
    }

    .wh-summary-card .value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1D1D1D;
        font-variant-numeric: tabular-nums;
    }

    .wh-services-wrap {
        background: #F7F7F9;
        border-radius: 12px;
        padding: 12px;
        margin: 4px 0 8px;
    }

    .wh-empty,
    .wh-loading {
        text-align: center;
        padding: 36px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }

    .wh-empty i,
    .wh-loading i {
        font-size: 28px;
        margin-bottom: 10px;
        color: #b7b7b7;
    }

    .wh-btn-services {
        white-space: nowrap;
        border-radius: 999px;
        font-weight: 600;
    }
</style>

@section('content')
    <div id="app" class="wh-report">
        <div class="fbox-shadow mt-2 fd-card respo-margin-top wh-report-filters">
            <div class="card-body">
                <h5 class="filters-title">{{ __('Working Hours Report') }}</h5>
                <p class="filters-hint">
                    {{ __('Working hours are based on completed service durations. Refunds are excluded. Overtime uses scheduled shift end (early check-in does not shorten overtime).') }}
                </p>

                <div class="row align-items-end g-2">
                    <div class="form-group col-lg-3 col-md-6 mb-3 mb-lg-0" v-show="activeTab === 'daily'">
                        <label class="filter-label" for="filter_date">{{ __('Date') }}</label>
                        <input type="date" class="filter-control" v-model="filter_date" id="filter_date">
                    </div>

                    <div class="form-group col-lg-2 col-md-6 mb-3 mb-lg-0" v-show="activeTab === 'summary'">
                        <label class="filter-label" for="filter_start_date">{{ __('Start Date') }}</label>
                        <input type="date" class="filter-control" v-model="filter_start_date" id="filter_start_date">
                    </div>

                    <div class="form-group col-lg-2 col-md-6 mb-3 mb-lg-0" v-show="activeTab === 'summary'">
                        <label class="filter-label" for="filter_end_date">{{ __('End Date') }}</label>
                        <input type="date" class="filter-control" v-model="filter_end_date" id="filter_end_date">
                    </div>

                    <div class="form-group col-lg-4 col-md-6 mb-3 mb-lg-0">
                        <label class="filter-label" for="filter_beautician_id">{{ __('Employee') }}</label>
                        <select class="wh-employee-select filter-control"
                            v-model="filter_beautician_id"
                            id="filter_beautician_id"
                            data-placeholder="{{ __('Select Employee') }}">
                            <option value=""></option>
                            {!! $beauticians_dropdown !!}
                        </select>
                    </div>

                    <div class="form-group col-lg-4 col-md-12 mb-0">
                        <label class="filter-label d-none d-lg-block">&nbsp;</label>
                        <div class="filter-actions">
                            <a v-on:click="resetFilter()" class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                                href="javascript:void(0)">
                                <i class="fa fa-refresh"></i> {{ __('Reset') }}
                            </a>
                            <a v-on:click="generateReport()" class="btn btn-sm btn-primary btn_dark-x"
                                href="javascript:void(0)">
                                <i class="fa fa-search"></i> {{ __('Generate') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fbox-shadow mt-3 fd-card respo-margin-top wh-report-results">
            <div class="card-body">
                <div class="list-toolbar">
                    <div>
                        <h5 class="list-title">
                            <span v-if="activeTab === 'daily'">{{ __('Daily Working Hours') }}</span>
                            <span v-else>{{ __('Working Hours Summary') }}</span>
                        </h5>
                        <span class="list-meta" v-if="activeTab === 'daily'">
                            {{ __('For') }} @{{ formatDateDMY(filter_date) || '—' }}
                        </span>
                        <span class="list-meta" v-else>
                            @{{ formatDateDMY(filter_start_date) || '—' }} → @{{ formatDateDMY(filter_end_date) || '—' }}
                        </span>
                    </div>
                    <div class="export-actions" v-if="!loading && hasPrintableResults">
                        <button type="button" class="export-btn is-print" v-on:click="printReport()">
                            <i class="fa fa-print"></i> {{ __('Print') }}
                        </button>
                    </div>
                </div>

                <ul class="nav nav-tabs wh-nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" :class="{ active: activeTab === 'daily' }"
                            @click.prevent="switchTab('daily')" href="#">
                            {{ __('Daily') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{ active: activeTab === 'summary' }"
                            @click.prevent="switchTab('summary')" href="#">
                            {{ __('Summary') }}
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane" :class="{ active: activeTab === 'daily' }" v-show="activeTab === 'daily'">
                        <div class="wh-loading" v-if="loading">
                            <div><i class="fa fa-spinner fa-spin"></i></div>
                            <div>{{ __('Loading…') }}</div>
                        </div>

                        <template v-else>
                            <div class="wh-empty" v-if="!workingHoursList || !workingHoursList.length">
                                <div><i class="fa fa-clock-o"></i></div>
                                <div><strong>{{ __('No working hours found') }}</strong></div>
                                <div>{{ __('Try another date or employee filter.') }}</div>
                            </div>

                            <div class="table-responsive" v-else>
                                <table class="table brownz-table-list-v1 wh-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 56px;">{{ __('SL#') }}</th>
                                            <th>{{ __('Employee') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Shift') }}</th>
                                            <th>{{ __('Check In') }}</th>
                                            <th>{{ __('Check Out') }}</th>
                                            <th>{{ __('Working Hours') }}</th>
                                            <th>{{ __('Free Time') }}</th>
                                            <th>{{ __('Over Time') }}</th>
                                            <th>{{ __('Services') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-for="(data, index) in workingHoursList">
                                            <tr :key="'row-' + index">
                                                <td>@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</td>
                                                <td><span class="emp-name">@{{ data.employee_name || '—' }}</span></td>
                                                <td>@{{ formatDateDMY(data.appointment_date) }}</td>
                                                <td>
                                                    <span class="wh-time-main">
                                                        @{{ formatTime(data.start_time) || '—' }} – @{{ formatTime(data.end_time) || '—' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="wh-time-stack">
                                                        <span class="wh-time-main" v-if="data.check_in">@{{ formatTime(data.check_in) }}</span>
                                                        <span class="wh-muted" v-else>—:—</span>
                                                        <span class="wh-chip" :class="checkInChipClass(data)">
                                                            @{{ checkInChipLabel(data) }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="wh-time-stack">
                                                        <span class="wh-time-main" v-if="data.check_out">@{{ formatTime(data.check_out) }}</span>
                                                        <span class="wh-muted" v-else>—:—</span>
                                                        <span class="wh-chip late"
                                                            v-if="data.check_out_delay && data.check_out_delay !== '00:00:00'">
                                                            +@{{ data.check_out_delay }} after shift
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="wh-time-main">@{{ data.total_working_time || '00:00:00' }}</td>
                                                <td>@{{ data.free_time || '00:00:00' }}</td>
                                                <td>
                                                    <span :class="{ 'wh-ot': data.over_time && data.over_time !== '00:00:00' }">
                                                        @{{ data.over_time || '00:00:00' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-secondary wh-btn-services"
                                                        @click="toggleServices(index)"
                                                        :disabled="!(data.services && data.services.length)">
                                                        <span v-if="data.showServices">{{ __('Hide') }}</span>
                                                        <span v-else>{{ __('Show') }}</span>
                                                        @{{ (data.services && data.services.length) || 0 }}
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr v-if="data.showServices" :key="'svc-' + index">
                                                <td></td>
                                                <td colspan="9" style="padding: 0;">
                                                    <div class="wh-services-wrap">
                                                        <table class="table mb-0 table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th>{{ __('Service') }}</th>
                                                                    <th>{{ __('Start') }}</th>
                                                                    <th>{{ __('End') }}</th>
                                                                    <th>{{ __('Duration') }}</th>
                                                                    <th>{{ __('Status') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(service, serviceIndex) in data.services"
                                                                    :key="serviceIndex">
                                                                    <td>@{{ service.service_name }}</td>
                                                                    <td>@{{ formatTime(service.start_time) }}</td>
                                                                    <td>@{{ formatTime(service.end_time) }}</td>
                                                                    <td>@{{ service.duration }}</td>
                                                                    <td>
                                                                        <span class="wh-chip"
                                                                            :class="service.status === 'overtime' ? 'overtime' : 'working'">
                                                                            <span v-if="service.status === 'overtime'">{{ __('Overtime') }}</span>
                                                                            <span v-else>{{ __('Working') }}</span>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <nav aria-label="pagination" class="mt-3" v-if="pagination.last_page > 1">
                                <ul class="pagination justify-content-end mb-0">
                                    <li class="page-item" v-if="pagination.current_page > 1">
                                        <a class="page-link" href="#"
                                            @click.prevent="changePage(pagination.current_page - 1)">«</a>
                                    </li>
                                    <li class="page-item" v-for="page in pagesNumber"
                                        v-bind:class="[ page == isActived ? 'active' : '']" :key="page">
                                        <a class="page-link" href="#" @click.prevent="changePage(page)">@{{ page }}</a>
                                    </li>
                                    <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                        <a class="page-link" href="#"
                                            @click.prevent="changePage(pagination.current_page + 1)">»</a>
                                    </li>
                                </ul>
                            </nav>
                        </template>
                    </div>

                    <div class="tab-pane" :class="{ active: activeTab === 'summary' }"
                        v-show="activeTab === 'summary'">
                        <div class="wh-loading" v-if="loading">
                            <div><i class="fa fa-spinner fa-spin"></i></div>
                            <div>{{ __('Loading…') }}</div>
                        </div>

                        <template v-else>
                            <div class="wh-empty" v-if="!workingHoursSummary.length">
                                <div><i class="fa fa-clock-o"></i></div>
                                <div><strong>{{ __('No summary data found') }}</strong></div>
                                <div>{{ __('Try adjusting the date range or employee filter.') }}</div>
                            </div>

                            <template v-else>
                                <div class="wh-summary-cards">
                                    <div class="wh-summary-card">
                                        <div class="label">{{ __('Employees') }}</div>
                                        <div class="value">@{{ summaryTotals.employees || workingHoursSummary.length }}</div>
                                    </div>
                                    <div class="wh-summary-card">
                                        <div class="label">{{ __('Working Hours') }}</div>
                                        <div class="value">@{{ summaryTotals.total_working_hours || '—' }}</div>
                                    </div>
                                    <div class="wh-summary-card">
                                        <div class="label">{{ __('Free Time') }}</div>
                                        <div class="value">@{{ summaryTotals.total_free_time || '—' }}</div>
                                    </div>
                                    <div class="wh-summary-card">
                                        <div class="label">{{ __('Over Time') }}</div>
                                        <div class="value wh-ot">@{{ summaryTotals.total_over_time || '—' }}</div>
                                    </div>
                                    <div class="wh-summary-card">
                                        <div class="label">{{ __('Services') }}</div>
                                        <div class="value">@{{ summaryTotals.total_services || 0 }}</div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table brownz-table-list-v1 wh-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 56px;">{{ __('SL#') }}</th>
                                                <th>{{ __('Employee') }}</th>
                                                <th>{{ __('Days') }}</th>
                                                <th>{{ __('Working Hours') }}</th>
                                                <th>{{ __('Free Time') }}</th>
                                                <th>{{ __('Over Time') }}</th>
                                                <th>{{ __('Services') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(data, index) in workingHoursSummary" :key="index">
                                                <td>@{{ index + 1 }}</td>
                                                <td><span class="emp-name">@{{ data.employee_name || '—' }}</span></td>
                                                <td>@{{ data.total_days || '—' }}</td>
                                                <td class="wh-time-main">@{{ data.total_working_hours }}</td>
                                                <td>@{{ data.total_free_time }}</td>
                                                <td>
                                                    <span :class="{ 'wh-ot': data.total_over_time && data.total_over_time !== '00:00:00' }">
                                                        @{{ data.total_over_time }}
                                                    </span>
                                                </td>
                                                <td>@{{ data.total_services || 0 }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                        </template>
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
            workingHoursList: [],
            workingHoursSummary: [],
            summaryTotals: {},
            activeTab: 'daily',
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
            filter_date: monthRange.end,
            filter_start_date: monthRange.start,
            filter_end_date: monthRange.end,
            filter_beautician_id: '',
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
            hasPrintableResults: function() {
                if (this.activeTab === 'daily') {
                    return this.workingHoursList && this.workingHoursList.length;
                }
                return this.workingHoursSummary && this.workingHoursSummary.length;
            }
        },
        methods: {
            changePage: function(page) {
                this.pagination.current_page = page;
                this.getWorkingHoursList(page);
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
            getWorkingHoursList: function(page) {
                page = page || 1;
                this.loading = true;
                axios.post("{{ route('workin_hours.vue-list') }}", {
                    page: page,
                    page_length: this.page_length,
                    start_date: this.filter_date,
                    end_date: this.filter_date,
                    beautician_id: this.filter_beautician_id,
                }).then((response) => {
                    var payload = response.data && response.data.data ? response.data.data : {};
                    this.workingHoursList = this.extractList(payload);
                    this.pagination = payload.pagination || this.pagination;
                }).catch((error) => {
                    console.error('Error fetching working hours list:', error);
                    this.workingHoursList = [];
                }).finally(() => {
                    this.loading = false;
                });
            },
            getWorkingHoursSummary: function() {
                this.loading = true;
                axios.post("{{ route('workin_hours_summary.vue-list') }}", {
                    start_date: this.filter_start_date,
                    end_date: this.filter_end_date,
                    beautician_id: this.filter_beautician_id,
                }).then((response) => {
                    var payload = response.data && response.data.data ? response.data.data : {};
                    this.workingHoursSummary = this.extractList(payload);
                    this.summaryTotals = payload.totals || {};
                }).catch((error) => {
                    console.error('Error fetching working hours summary:', error);
                    this.workingHoursSummary = [];
                    this.summaryTotals = {};
                }).finally(() => {
                    this.loading = false;
                });
            },
            applyDefaultDates: function() {
                var range = currentMonthToToday();
                this.filter_date = range.end;
                this.filter_start_date = range.start;
                this.filter_end_date = range.end;
            },
            resetFilter: function() {
                this.applyDefaultDates();
                this.filter_beautician_id = '';
                window.vocoDestroySelect2($('#filter_beautician_id'));
                this.$nextTick(() => {
                    this.initSelect2();
                });
                this.generateReport();
            },
            switchTab: function(tab) {
                if (this.activeTab === tab) {
                    return;
                }
                window.vocoDestroySelect2($('#filter_beautician_id'));
                this.activeTab = tab;
                this.$nextTick(() => {
                    this.initSelect2();
                });
                this.generateReport();
            },
            generateReport: function() {
                if (this.activeTab === 'daily') {
                    this.getWorkingHoursList(1);
                } else {
                    this.getWorkingHoursSummary();
                }
            },
            exportQuery: function() {
                var query = '?tab=' + encodeURIComponent(this.activeTab || 'daily')
                    + '&beautician_id=' + encodeURIComponent(this.filter_beautician_id || '');

                if (this.activeTab === 'summary') {
                    query += '&start_date=' + encodeURIComponent(this.filter_start_date || '')
                        + '&end_date=' + encodeURIComponent(this.filter_end_date || '');
                } else {
                    query += '&start_date=' + encodeURIComponent(this.filter_date || '');
                }

                return query;
            },
            printReport: function() {
                var printWindow = window.open(
                    "{{ route('working-hours-report.print') }}" + this.exportQuery(),
                    '_blank',
                    'noopener,noreferrer'
                );

                if (!printWindow) {
                    alert('{{ __('Please allow popups to print the working hours report.') }}');
                }
            },
            toggleServices: function(index) {
                this.$set(
                    this.workingHoursList[index],
                    'showServices',
                    !this.workingHoursList[index].showServices
                );
            },
            isZeroDuration: function(value) {
                return !value || value === '00:00:00';
            },
            checkInChipClass: function(data) {
                if (!data.check_in) {
                    return 'missing';
                }
                if (!this.isZeroDuration(data.early_check_in_time)) {
                    return 'early';
                }
                if (!this.isZeroDuration(data.check_in_delay)) {
                    return 'late';
                }
                return 'on-time';
            },
            checkInChipLabel: function(data) {
                if (!data.check_in) {
                    return '{{ __('No check-in') }}';
                }
                if (!this.isZeroDuration(data.early_check_in_time)) {
                    return '{{ __('Early') }} ' + data.early_check_in_time;
                }
                if (!this.isZeroDuration(data.check_in_delay)) {
                    return '{{ __('Late') }} ' + data.check_in_delay;
                }
                return '{{ __('On time') }}';
            },
            formatTime: function(timeString) {
                if (!timeString) return '';
                var parts = String(timeString).split(':');
                if (parts.length < 2) return timeString;
                var hours = parseInt(parts[0], 10);
                var minute = parts[1];
                if (isNaN(hours)) return timeString;
                var suffix = hours >= 12 ? 'PM' : 'AM';
                var hours12 = ((hours + 11) % 12 + 1);
                return hours12 + ':' + minute + ' ' + suffix;
            },
            formatDateDMY: function(dateString) {
                if (!dateString) return '';
                var parts = String(dateString).split('-');
                if (parts.length < 3) return dateString;
                return parts[2] + '-' + parts[1] + '-' + parts[0];
            },
            initSelect2: function() {
                var $select = $('#filter_beautician_id');
                if (!$select.length) {
                    return;
                }

                window.vocoDestroySelect2($select);
                window.vocoInitSelect2($select, {
                    placeholder: '{{ __('Select Employee') }}',
                    allowClear: true,
                    width: '100%'
                }).off('change.wh').on('change.wh', (e) => {
                    this.filter_beautician_id = $(e.target).val() || '';
                });

                if (this.filter_beautician_id) {
                    $select.val(this.filter_beautician_id).trigger('change.select2');
                } else {
                    $select.val(null).trigger('change');
                }
            }
        },
        mounted: function() {
            this.applyDefaultDates();
            this.activeTab = 'daily';
            this.getWorkingHoursList(1);
            this.$nextTick(() => {
                this.initSelect2();
            });
        },
        beforeDestroy: function() {
            window.vocoDestroySelect2($('#filter_beautician_id'));
        }
    });
</script>
@endpush
