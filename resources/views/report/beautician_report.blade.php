@extends('layouts.vcapp')

@section('content')
<style>
  .beautician-report-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .beautician-report-filters .filter-control {
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

  .beautician-report-filters select.filter-control {
    appearance: auto;
  }

  .beautician-report-filters .filter-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  @media (min-width: 992px) {
    .beautician-report-filters .filter-actions {
      justify-content: flex-end;
    }
  }

  .beautician-report-list .list-toolbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 14px;
  }

  .beautician-report-list .list-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0;
  }

  .beautician-report-list .list-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .beautician-report-list .export-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .beautician-report-list .item-name {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
  }

  .beautician-report-list .amount-cell {
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
  }

  .beautician-report-list .type-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
  }

  .beautician-report-list .type-pill.is-service {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .beautician-report-list .type-pill.is-product {
    color: #1D4ED8;
    background: rgba(59, 130, 246, 0.12);
  }

  .beautician-report-list .totals-row td {
    background: #F7F7F9;
    font-weight: 600;
  }

  .beautician-report-list .empty-state,
  .beautician-report-list .loading-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }

  .beautician-report-modal .modal-dialog {
    max-width: min(1100px, calc(100vw - 1.5rem));
    margin: 0.75rem auto;
    max-height: calc(100vh - 1.5rem);
  }

  .beautician-report-modal .modal-content {
    border-radius: 16px;
    border: none;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 1.5rem);
  }

  .beautician-report-modal .modal-header {
    border: 0;
    padding: 16px 18px 8px;
    align-items: flex-start;
    flex-shrink: 0;
  }

  .beautician-report-modal .modal-title {
    color: #1D1D1D;
    font-weight: 600;
    margin: 0;
  }

  .beautician-report-modal .summary-date {
    margin: 4px 0 0;
    font-size: 13px;
    color: #6c757d;
  }

  .beautician-report-modal .modal-body {
    padding: 4px 18px 12px;
    overflow-y: auto !important;
    flex: 1 1 auto;
    min-height: 0;
    -webkit-overflow-scrolling: touch;
  }

  .beautician-report-modal .filter-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 12px;
  }

  .beautician-report-modal .filter-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #F0F0F3;
    color: #1D1D1D;
    border-radius: 999px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
  }

  .beautician-report-modal .filter-chip span {
    color: #6c757d;
    font-weight: 500;
  }

  .beautician-report-modal .summary-hero {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 12px;
  }

  @media (max-width: 991px) {
    .beautician-report-modal .summary-hero {
      grid-template-columns: 1fr 1fr;
    }
  }

  @media (max-width: 575px) {
    .beautician-report-modal .summary-hero {
      grid-template-columns: 1fr;
    }
  }

  .beautician-report-modal .summary-hero-card {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 12px 14px;
  }

  .beautician-report-modal .summary-hero-card .label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
  }

  .beautician-report-modal .summary-hero-card .value {
    display: block;
    margin-top: 4px;
    font-size: 1.15rem;
    font-weight: 600;
    color: #1D1D1D;
    font-variant-numeric: tabular-nums;
  }

  .beautician-report-modal .summary-table-wrap {
    max-height: none;
    overflow: visible;
    border-radius: 14px;
  }

  .beautician-report-modal .summary-table-wrap .table {
    margin-bottom: 0;
  }

  .beautician-report-modal .beautician-name-cell {
    font-weight: 600;
    color: #1D1D1D;
  }

  .beautician-report-modal .count-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 34px;
    padding: 4px 10px;
    border-radius: 999px;
    background: rgba(230, 205, 127, 0.35);
    color: #9A6700;
    font-size: 12px;
    font-weight: 600;
  }

  .beautician-report-modal .modal-footer {
    border: 0;
    padding: 8px 18px 16px;
    display: flex;
    justify-content: space-between;
    gap: 8px;
    flex-wrap: wrap;
    flex-shrink: 0;
  }

  .beautician-report-modal .empty-state,
  .beautician-report-modal .loading-state {
    text-align: center;
    padding: 28px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }
</style>

<div id="app">
  <div class="fbox-shadow mt-2 fd-card respo-margin-top beautician-report-filters">
    <div class="card-body">
      <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
        {{ __('Beautician Report') }}
      </h5>

      <div class="row align-items-end br-row-gap-2">
        <div class="col-lg-3 col-md-6">
          <label class="filter-label" for="filter_start_date">{{ __('Start Date') }}</label>
          <input type="date" id="filter_start_date" class="form-control filter-control" v-model="filter_start_date">
        </div>

        <div class="col-lg-3 col-md-6">
          <label class="filter-label" for="filter_end_date">{{ __('End Date') }}</label>
          <input type="date" id="filter_end_date" class="form-control filter-control" v-model="filter_end_date">
        </div>

        <div class="col-lg-3 col-md-6">
          <label class="filter-label" for="beautician_id">{{ __('Beautician') }}</label>
          <select id="beautician_id" class="form-control filter-control voco-select2" name="beautician"
            data-placeholder="{{ __('Beautician') }}">
            <option value=""></option>
            {{ $beauticians_dropdown }}
          </select>
        </div>

        <div class="col-lg-3 col-md-6">
          <label class="filter-label" for="filter_booking_code">{{ __('Booking Code') }}</label>
          <input type="text" id="filter_booking_code" class="form-control filter-control"
            v-model="filter_booking_code"
            @keyup.enter="getBeauticianList(1)"
            placeholder="{{ __('Search booking code...') }}">
        </div>

        <div class="col-lg-3 col-md-6">
          <label class="filter-label" for="filter_line_type">{{ __('Type') }}</label>
          <select id="filter_line_type" class="form-control filter-control" v-model="filter_line_type">
            <option value="">{{ __('All') }}</option>
            <option value="service">{{ __('Service') }}</option>
            <option value="product">{{ __('Product') }}</option>
          </select>
        </div>

        <div class="col-lg-9 col-md-6">
          <label class="filter-label d-none d-lg-block">&nbsp;</label>
          <div class="filter-actions">
            <button type="button" class="btn btn-sm btn-primary btn_dark-x" @click="getBeauticianList(1)">
              <i class="fa fa-search"></i> {{ __('Search') }}
            </button>
            <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d" @click="resetFilter()">
              <i class="fa fa-refresh"></i> {{ __('Reset') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="fbox-shadow mt-2 fd-card beautician-report-list">
    <div class="card-body">
      <div class="list-toolbar">
        <div>
          <h5 class="list-title">{{ __('Beautician List') }}</h5>
          <span class="list-meta" v-if="!loading && pagination.total">
            {{ __('Showing') }} @{{ pagination.from || 0 }}–@{{ pagination.to || 0 }}
            {{ __('of') }} @{{ pagination.total }}
            · {{ __('Split') }}: @{{ formatAmount(filtered_total) }}
            · {{ __('Commission') }}: @{{ formatAmount(filtered_commission_total) }}
          </span>
        </div>
        <div class="export-actions">
          <button type="button" class="btn btn-sm brownz-btn-yellow-d" @click="openSummary" :disabled="loading">
            <i class="fa fa-pie-chart"></i> {{ __('View Summary') }}
          </button>
          <button type="button" class="btn btn-sm btn-outline-secondary" @click="exportToPDF" :disabled="loading">
            <i class="fa fa-file-pdf-o"></i> {{ __('PDF') }}
          </button>
          <button type="button" class="btn btn-sm btn_dark-x" @click="exportToExcel" :disabled="loading">
            <i class="fa fa-file-excel-o"></i> {{ __('Excel') }}
          </button>
        </div>
      </div>

      <div v-if="loading" class="loading-state">
        <div><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>
        <div>{{ __('Loading beautician report...') }}</div>
      </div>

      <template v-else-if="BeauticianList.length">
        <div class="table-responsive">
          <table class="table brownz-table-list-v1 m-0">
            <thead>
              <tr>
                <th style="width: 56px;">{{ __('SL#') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Booking Code') }}</th>
                <th>{{ __('Beautician') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Service / Product') }}</th>
                <th>{{ __('Service Amount') }}</th>
                <th>{{ __('Split Amount') }}</th>
                <th>{{ __('Commission') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(data, index) in BeauticianList" :key="data.split_service_amount_id || index">
                <td>@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</td>
                <td>@{{ formatDate(data.report_date || data.created_at) }}</td>
                <td>@{{ (data.booking && data.booking.booking_code) ? data.booking.booking_code : '—' }}</td>
                <td>@{{ (data.beautician && data.beautician.name) ? data.beautician.name : '—' }}</td>
                <td>
                  <span class="type-pill" :class="isProductLine(data) ? 'is-product' : 'is-service'">
                    <span v-if="isProductLine(data)">{{ __('Product') }}</span>
                    <span v-else>{{ __('Service') }}</span>
                  </span>
                </td>
                <td>
                  <span class="item-name">@{{ data.service_name || '—' }}</span>
                </td>
                <td class="amount-cell">@{{ formatAmount(data.total_amount) }}</td>
                <td class="amount-cell">@{{ formatAmount(data.price) }}</td>
                <td class="amount-cell">@{{ formatCommissionAmount(data.commission_amount) }}</td>
              </tr>
              <tr class="totals-row">
                <td colspan="7" class="text-right">{{ __('Total') }}</td>
                <td class="amount-cell">@{{ formatAmount(filtered_total) }}</td>
                <td class="amount-cell">@{{ formatAmount(filtered_commission_total) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <nav aria-label="pagination" class="mt-3" v-if="pagination.last_page > 1">
          <ul class="pagination justify-content-end mb-0">
            <li class="page-item" v-if="pagination.current_page > 1">
              <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">«</a>
            </li>
            <li class="page-item" v-for="page in pagesNumber" :key="page"
              :class="{ active: page == isActived }">
              <a class="page-link" href="#" @click.prevent="changePage(page)">@{{ page }}</a>
            </li>
            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
              <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">»</a>
            </li>
          </ul>
        </nav>
      </template>

      <div v-else class="empty-state">
        <div><i class="fa fa-user" aria-hidden="true"></i></div>
        <div><strong>{{ __('No beautician records found') }}</strong></div>
        <div>{{ __('Try adjusting date, beautician, type, or booking filters.') }}</div>
      </div>
    </div>
  </div>

  <div class="modal fade brownz-custom-popup beautician-report-modal" id="beauticianSummaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <h5 class="modal-title">{{ __('Beautician Summary') }}</h5>
            <p class="summary-date">
              {{ __('Date Range') }}:
              @{{ formatDateRange(filter_start_date) }} {{ __('to') }} @{{ formatDateRange(filter_end_date) }}
            </p>
          </div>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="filter-chips" v-if="summaryFilterChips.length">
            <div class="filter-chip" v-for="(chip, index) in summaryFilterChips" :key="index">
              <span>@{{ chip.label }}</span>
              @{{ chip.value }}
            </div>
          </div>

          <div v-if="summaryLoading" class="loading-state">
            <div><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>
            <div>{{ __('Loading summary...') }}</div>
          </div>

          <template v-else-if="summaryData && summaryData.length">
            <div class="summary-hero">
              <div class="summary-hero-card">
                <span class="label">{{ __('Beauticians') }}</span>
                <span class="value">@{{ totalBeauticians }}</span>
              </div>
              <div class="summary-hero-card">
                <span class="label">{{ __('Total Lines') }}</span>
                <span class="value">@{{ totalServices }}</span>
              </div>
              <div class="summary-hero-card">
                <span class="label">{{ __('Total Split') }}</span>
                <span class="value">₹ @{{ formatAmount(totalAmount) }}</span>
              </div>
              <div class="summary-hero-card">
                <span class="label">{{ __('Total Commission') }}</span>
                <span class="value">₹ @{{ formatAmount(totalCommission) }}</span>
              </div>
            </div>

            <div class="summary-table-wrap">
              <table class="table brownz-table-list-v1 m-0">
                <thead>
                  <tr>
                    <th style="width: 56px;">{{ __('SL#') }}</th>
                    <th>{{ __('Beautician') }}</th>
                    <th class="text-center">{{ __('Lines') }}</th>
                    <th class="text-right">{{ __('Split Amount') }}</th>
                    <th class="text-right">{{ __('Commission') }}</th>
                    <th class="text-right">{{ __('Target') }}</th>
                    <th class="text-right">{{ __('Balance') }}</th>
                    <th>{{ __('Overtime') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in summaryData" :key="item.beautician_name + '-' + index">
                    <td>@{{ index + 1 }}</td>
                    <td>
                      <span class="beautician-name-cell">@{{ item.beautician_name || '—' }}</span>
                    </td>
                    <td class="text-center">
                      <span class="count-pill">@{{ item.number_of_services }}</span>
                    </td>
                    <td class="text-right amount-cell">@{{ formatAmount(item.sum_total_amount) }}</td>
                    <td class="text-right amount-cell">@{{ formatAmount(item.sum_commission_amount) }}</td>
                    <td class="text-right amount-cell">@{{ formatAmount(item.target_value) }}</td>
                    <td class="text-right amount-cell">@{{ formatAmount(item.balance_amount) }}</td>
                    <td>@{{ formatOvertime(item.total_overtime) }}</td>
                  </tr>
                  <tr class="totals-row">
                    <td colspan="2" class="text-right">{{ __('Total') }}</td>
                    <td class="text-center">@{{ totalServices }}</td>
                    <td class="text-right amount-cell">₹ @{{ formatAmount(totalAmount) }}</td>
                    <td class="text-right amount-cell">₹ @{{ formatAmount(totalCommission) }}</td>
                    <td colspan="3"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </template>

          <div v-else class="empty-state">
            <div><i class="fa fa-pie-chart" aria-hidden="true"></i></div>
            <div><strong>{{ __('No summary data') }}</strong></div>
            <div>{{ __('No beautician records found for the selected filters.') }}</div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-outline-secondary" @click="printSummary"
            :disabled="summaryLoading || !summaryData.length">
            <i class="fa fa-print"></i> {{ __('Print') }}
          </button>
          <button type="button" class="btn btn-sm brownz-btn-yellow-d" data-dismiss="modal">
            {{ __('Close') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vuejs/axios/axios.min.js') }}"></script>
<script src="{{ asset('assets/vuejs/ajax/libs/moment.min.js') }}"></script>
<link href="{{ asset('assets/vuejs/toast/vue_toast.css') }}" rel="stylesheet">
<script src="{{ asset('assets/vuejs/toast/vue_toast.js') }}"></script>
@endpush

@push('inline-scripts')
<script>
  var vc_moment_date_offset = new Date("2021-10-05T17:28:50.403+03:00").getTime() - Date.now();
  var vc_jscalendar_moment_format = "DD/MM/YYYY";

  function currentMonthRange() {
    var now = new Date();
    var year = now.getFullYear();
    var month = now.getMonth();
    var start = new Date(year, month, 1);
    var end = new Date(year, month + 1, 0);
    function pad(n) { return n < 10 ? '0' + n : String(n); }
    return {
      start: year + '-' + pad(month + 1) + '-' + pad(start.getDate()),
      end: year + '-' + pad(month + 1) + '-' + pad(end.getDate())
    };
  }

  var monthRange = currentMonthRange();

  if (typeof VueToast !== 'undefined') {
    Vue.use(VueToast, {
      position: "top-right",
      duration: 5000,
      dismissible: true
    });
  }

  var app = new Vue({
    el: '#app',
    data: {
      loading: false,
      summaryLoading: false,
      BeauticianList: [],
      page_length: '100',
      pagination: {
        total: 0,
        per_page: 100,
        from: 1,
        to: 0,
        current_page: 1,
        last_page: 1
      },
      offset: 4,
      beautician_id: '',
      beauticianLabel: '',
      filter_booking_code: '',
      filter_line_type: '',
      filter_start_date: monthRange.start,
      filter_end_date: monthRange.end,
      filtered_total: 0,
      filtered_commission_total: 0,
      summaryData: [],
      totalBeauticians: 0,
      totalServices: 0,
      totalAmount: 0,
      totalCommission: 0
    },
    computed: {
      isActived: function () {
        return this.pagination.current_page;
      },
      pagesNumber: function () {
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
      summaryFilterChips: function () {
        var chips = [];
        if (this.beauticianLabel) {
          chips.push({ label: '{{ __('Beautician') }}', value: this.beauticianLabel });
        }
        if ((this.filter_booking_code || '').trim()) {
          chips.push({ label: '{{ __('Booking') }}', value: this.filter_booking_code.trim() });
        }
        if (this.filter_line_type) {
          chips.push({
            label: '{{ __('Type') }}',
            value: this.filter_line_type === 'product' ? '{{ __('Product') }}' : '{{ __('Service') }}'
          });
        }
        return chips;
      }
    },
    methods: {
      formatAmount: function (value) {
        var amount = parseFloat(value);
        if (isNaN(amount)) {
          return '0.00';
        }
        return amount.toFixed(2);
      },
      formatCommissionAmount: function (amount) {
        var value = parseFloat(amount || 0);
        if (!value || value <= 0) {
          return '—';
        }
        return value.toFixed(2);
      },
      formatDate: function (val) {
        if (!val) {
          return '—';
        }
        return moment(val).format('Do MMM YYYY');
      },
      formatDateRange: function (val) {
        if (!val) {
          return '—';
        }
        return moment(val).format('DD-MM-YYYY');
      },
      formatOvertime: function (timeStr) {
        if (!timeStr || timeStr === '00:00:00') {
          return '—';
        }
        var parts = String(timeStr).split(':');
        if (parts.length < 2) {
          return timeStr;
        }
        var hour = parseInt(parts[0], 10) || 0;
        var min = parseInt(parts[1], 10) || 0;
        var result = [];
        if (hour > 0) {
          result.push(hour + (hour > 1 ? ' hrs' : ' hr'));
        }
        if (min > 0) {
          result.push(min + (min > 1 ? ' mins' : ' min'));
        }
        return result.length ? result.join(' ') : '—';
      },
      isProductLine: function (data) {
        if (data.line_type === 'product') {
          return true;
        }
        return !!(data.booking_item_id || data.item_id);
      },
      syncBeautician: function () {
        this.beautician_id = $('#beautician_id').val() || '';
        var selectedText = $('#beautician_id option:selected').text();
        this.beauticianLabel = this.beautician_id ? String(selectedText || '').trim() : '';
      },
      changePage: function (page) {
        this.getBeauticianList(page);
      },
      getBeauticianList: function (page) {
        var self = this;
        page = page || 1;
        if (self.loading) {
          return;
        }

        self.loading = true;
        self.pagination.current_page = page;
        self.syncBeautician();

        axios.post("{{ route('beautician.vue-list') }}", {
          page: page,
          page_length: self.page_length,
          booking_code: (self.filter_booking_code || '').trim(),
          start_date: self.filter_start_date,
          end_date: self.filter_end_date,
          beautician_id: self.beautician_id,
          line_type: self.filter_line_type || ''
        }).then(function (response) {
          if (response.data && Number(response.data.errorcode) === 0) {
            self.BeauticianList = (response.data.data && response.data.data.data) ? response.data.data.data : [];
            self.pagination = response.data.pagination || self.pagination;
            self.filtered_total = response.data.filtered_total ?? 0;
            self.filtered_commission_total = response.data.filtered_commission_total ?? 0;
          } else {
            self.BeauticianList = [];
            self.filtered_total = 0;
            self.filtered_commission_total = 0;
            if (Vue.$toast) {
              Vue.$toast.error((response.data && response.data.errormessage) || '{{ __('Failed to load beautician report.') }}');
            }
          }
        }).catch(function () {
          self.BeauticianList = [];
          self.filtered_total = 0;
          self.filtered_commission_total = 0;
          if (Vue.$toast) {
            Vue.$toast.error('{{ __('Failed to load beautician report.') }}');
          }
        }).finally(function () {
          self.loading = false;
        });
      },
      resetFilter: function () {
        var range = currentMonthRange();
        this.filter_booking_code = '';
        this.filter_line_type = '';
        this.filter_start_date = range.start;
        this.filter_end_date = range.end;
        this.beautician_id = '';
        this.beauticianLabel = '';
        $('#beautician_id').val('').trigger('change');
        this.getBeauticianList(1);
      },
      openSummary: function () {
        this.syncBeautician();
        this.getSummaryData();
        $('#beauticianSummaryModal').modal('show');
      },
      getSummaryData: function () {
        var self = this;
        self.syncBeautician();
        self.summaryLoading = true;

        axios.post("{{ route('beautician.summary') }}", {
          start_date: self.filter_start_date,
          end_date: self.filter_end_date,
          beautician_id: self.beautician_id,
          booking_code: (self.filter_booking_code || '').trim(),
          line_type: self.filter_line_type || ''
        }).then(function (response) {
          if (response.data && Number(response.data.errorcode) === 0) {
            self.summaryData = response.data.data || [];
            self.totalBeauticians = response.data.total_beauticians || self.summaryData.length || 0;
            self.totalServices = response.data.total_services || 0;
            self.totalAmount = response.data.total_amount || 0;
            self.totalCommission = response.data.total_commission || 0;
          } else {
            self.summaryData = [];
            self.totalBeauticians = 0;
            self.totalServices = 0;
            self.totalAmount = 0;
            self.totalCommission = 0;
          }
        }).catch(function () {
          self.summaryData = [];
          self.totalBeauticians = 0;
          self.totalServices = 0;
          self.totalAmount = 0;
          self.totalCommission = 0;
          if (Vue.$toast) {
            Vue.$toast.error('{{ __('Failed to load summary.') }}');
          }
        }).finally(function () {
          self.summaryLoading = false;
        });
      },
      exportQuery: function () {
        this.syncBeautician();
        return '?start_date=' + encodeURIComponent(this.filter_start_date || '')
          + '&end_date=' + encodeURIComponent(this.filter_end_date || '')
          + '&booking_code=' + encodeURIComponent((this.filter_booking_code || '').trim())
          + '&beautician_id=' + encodeURIComponent(this.beautician_id || '')
          + '&line_type=' + encodeURIComponent(this.filter_line_type || '');
      },
      exportToPDF: function () {
        window.open("{{ route('beautician-report.export-to-pdf') }}" + this.exportQuery(), '_self');
      },
      exportToExcel: function () {
        window.open("{{ route('beautician-report.export-to-excel') }}" + this.exportQuery(), '_self');
      },
      printSummary: function () {
        this.syncBeautician();
        var url = "{{ route('beautician-report-summary.export-to-pdf') }}"
          + '?start_date=' + encodeURIComponent(this.filter_start_date || '')
          + '&end_date=' + encodeURIComponent(this.filter_end_date || '')
          + '&beautician_id=' + encodeURIComponent(this.beautician_id || '')
          + '&booking_code=' + encodeURIComponent((this.filter_booking_code || '').trim())
          + '&line_type=' + encodeURIComponent(this.filter_line_type || '');
        window.open(url, '_self');
      }
    },
    mounted: function () {
      var self = this;
      this.$nextTick(function () {
        window.vocoInitSelect2($('#beautician_id'), {
          placeholder: '{{ __('Beautician') }}',
          allowClear: true,
          width: '100%'
        }).on('change', function () {
          self.beautician_id = $('#beautician_id').val() || '';
          var selectedText = $('#beautician_id option:selected').text();
          self.beauticianLabel = self.beautician_id ? String(selectedText || '').trim() : '';
        });
      });
      this.getBeauticianList(1);
    }
  });
</script>
@endpush
