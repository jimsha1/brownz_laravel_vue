@extends('layouts.vcapp')

@section('content')
<style>
  .service-report-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .service-report-filters .filter-control {
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

  .service-report-filters select.filter-control {
    appearance: auto;
  }

  .service-report-filters .filter-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  @media (min-width: 992px) {
    .service-report-filters .filter-actions {
      justify-content: flex-end;
    }
  }

  .service-report-list .list-toolbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 14px;
  }

  .service-report-list .list-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0;
  }

  .service-report-list .list-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .service-report-list .export-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .service-report-list .service-name {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
  }

  .service-report-list .service-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .service-report-list .amount-cell {
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
  }

  .service-report-list .totals-row td {
    background: #F7F7F9;
    font-weight: 600;
  }

  .service-report-list .empty-state,
  .service-report-list .loading-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }

  .service-report-modal .modal-content {
    border-radius: 16px;
    border: none;
    overflow: hidden;
  }

  .service-report-modal .modal-header {
    border: 0;
    padding: 18px 20px 8px;
    align-items: flex-start;
  }

  .service-report-modal .modal-title {
    color: #1D1D1D;
    font-weight: 600;
    margin: 0;
  }

  .service-report-modal .summary-date {
    margin: 4px 0 0;
    font-size: 13px;
    color: #6c757d;
  }

  .service-report-modal .modal-body {
    padding: 8px 20px 16px;
  }

  .service-report-modal .filter-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 14px;
  }

  .service-report-modal .filter-chip {
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

  .service-report-modal .filter-chip span {
    color: #6c757d;
    font-weight: 500;
  }

  .service-report-modal .summary-hero {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 14px;
  }

  @media (max-width: 575px) {
    .service-report-modal .summary-hero {
      grid-template-columns: 1fr;
    }
  }

  .service-report-modal .summary-hero-card {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 14px 16px;
  }

  .service-report-modal .summary-hero-card .label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
  }

  .service-report-modal .summary-hero-card .value {
    display: block;
    margin-top: 4px;
    font-size: 1.35rem;
    font-weight: 600;
    color: #1D1D1D;
    font-variant-numeric: tabular-nums;
  }

  .service-report-modal .summary-table-wrap {
    max-height: 360px;
    overflow: auto;
    border-radius: 14px;
    background: #fff;
  }

  .service-report-modal .summary-table-wrap .table {
    margin: 0;
  }

  .service-report-modal .service-name-cell {
    font-weight: 600;
    color: #1D1D1D;
  }

  .service-report-modal .count-pill {
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

  .service-report-modal .modal-footer {
    border: 0;
    padding: 8px 20px 18px;
    display: flex;
    justify-content: space-between;
    gap: 8px;
    flex-wrap: wrap;
  }

  .service-report-modal .empty-state,
  .service-report-modal .loading-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }
</style>

<div id="app">
  <div class="fbox-shadow mt-2 fd-card respo-margin-top service-report-filters">
    <div class="card-body">
      <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
        {{ __('Service Report') }}
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
          <label class="filter-label" for="filter_service_name">{{ __('Service Name') }}</label>
          <input type="text" id="filter_service_name" class="form-control filter-control"
            v-model="filter_service_name"
            @keyup.enter="getServiceList(1)"
            placeholder="{{ __('Search service...') }}">
        </div>

        <div class="col-lg-3 col-md-6">
          <label class="filter-label" for="filter_booking_code">{{ __('Booking Code') }}</label>
          <input type="text" id="filter_booking_code" class="form-control filter-control"
            v-model="filter_booking_code"
            @keyup.enter="getServiceList(1)"
            placeholder="{{ __('Search booking code...') }}">
        </div>

        <div class="col-lg-9 col-md-6">
          <label class="filter-label d-none d-lg-block">&nbsp;</label>
          <div class="filter-actions">
            <button type="button" class="btn btn-sm btn-primary btn_dark-x" @click="getServiceList(1)">
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

  <div class="fbox-shadow mt-2 fd-card service-report-list">
    <div class="card-body">
      <div class="list-toolbar">
        <div>
          <h5 class="list-title">{{ __('Service List') }}</h5>
          <span class="list-meta" v-if="!loading && pagination.total">
            {{ __('Showing') }} @{{ pagination.from || 0 }}–@{{ pagination.to || 0 }}
            {{ __('of') }} @{{ pagination.total }}
            · {{ __('Total') }}: @{{ formatAmount(filtered_total) }}
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
        <div>{{ __('Loading services...') }}</div>
      </div>

      <template v-else-if="ServiceList.length">
        <div class="table-responsive">
          <table class="table brownz-table-list-v1 m-0">
            <thead>
              <tr>
                <th style="width: 56px;">{{ __('SL#') }}</th>
                <th>{{ __('Booking Date') }}</th>
                <th>{{ __('Booking Code') }}</th>
                <th>{{ __('Beautician') }}</th>
                <th>{{ __('Service') }}</th>
                <th>{{ __('Service Amount') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(data, index) in ServiceList" :key="data.booking_service_id || index">
                <td>@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</td>
                <td>@{{ formatDate(data.appointment_date) }}</td>
                <td>@{{ (data.booking && data.booking.booking_code) ? data.booking.booking_code : '—' }}</td>
                <td>@{{ (data.beautician && data.beautician.name) ? data.beautician.name : '—' }}</td>
                <td>
                  <span class="service-name">@{{ data.service_name || '—' }}</span>
                </td>
                <td class="amount-cell">@{{ formatAmount(data.line_total) }}</td>
              </tr>
              <tr class="totals-row">
                <td colspan="5" class="text-right">{{ __('Total Service Amount') }}</td>
                <td class="amount-cell">@{{ formatAmount(filtered_total) }}</td>
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
        <div><i class="fa fa-scissors" aria-hidden="true"></i></div>
        <div><strong>{{ __('No services found') }}</strong></div>
        <div>{{ __('Try adjusting date, beautician, service, or booking filters.') }}</div>
      </div>
    </div>
  </div>

  <div class="modal fade brownz-custom-popup service-report-modal" id="serviceSummaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <h5 class="modal-title">{{ __('Service Summary') }}</h5>
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
                <span class="label">{{ __('Total Services') }}</span>
                <span class="value">@{{ totalCount }}</span>
              </div>
              <div class="summary-hero-card">
                <span class="label">{{ __('Total Amount') }}</span>
                <span class="value">₹ @{{ formatAmount(totalAmount) }}</span>
              </div>
            </div>

            <div class="summary-table-wrap">
              <table class="table brownz-table-list-v1 m-0">
                <thead>
                  <tr>
                    <th style="width: 56px;">{{ __('SL#') }}</th>
                    <th>{{ __('Service Name') }}</th>
                    <th class="text-center">{{ __('Count') }}</th>
                    <th class="text-right">{{ __('Amount') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in summaryData" :key="item.service_name + '-' + index">
                    <td>@{{ index + 1 }}</td>
                    <td>
                      <span class="service-name-cell">@{{ item.service_name || '—' }}</span>
                    </td>
                    <td class="text-center">
                      <span class="count-pill">@{{ item.count }}</span>
                    </td>
                    <td class="text-right amount-cell">@{{ formatAmount(item.total_service_price) }}</td>
                  </tr>
                  <tr class="totals-row">
                    <td colspan="2" class="text-right">{{ __('Total') }}</td>
                    <td class="text-center">@{{ totalCount }}</td>
                    <td class="text-right amount-cell">₹ @{{ formatAmount(totalAmount) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </template>

          <div v-else class="empty-state">
            <div><i class="fa fa-pie-chart" aria-hidden="true"></i></div>
            <div><strong>{{ __('No summary data') }}</strong></div>
            <div>{{ __('No completed services found for the selected filters.') }}</div>
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
      ServiceList: [],
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
      filter_service_name: '',
      filter_booking_code: '',
      filter_start_date: monthRange.start,
      filter_end_date: monthRange.end,
      filtered_total: 0,
      summaryData: [],
      totalCount: 0,
      totalAmount: 0
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
        if ((this.filter_service_name || '').trim()) {
          chips.push({ label: '{{ __('Service') }}', value: this.filter_service_name.trim() });
        }
        if ((this.filter_booking_code || '').trim()) {
          chips.push({ label: '{{ __('Booking') }}', value: this.filter_booking_code.trim() });
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
      syncBeautician: function () {
        this.beautician_id = $('#beautician_id').val() || '';
        var selectedText = $('#beautician_id option:selected').text();
        this.beauticianLabel = this.beautician_id ? String(selectedText || '').trim() : '';
      },
      changePage: function (page) {
        this.getServiceList(page);
      },
      getServiceList: function (page) {
        var self = this;
        page = page || 1;
        if (self.loading) {
          return;
        }

        self.loading = true;
        self.pagination.current_page = page;
        self.syncBeautician();

        axios.post("{{ route('service.vue-list') }}", {
          page: page,
          page_length: self.page_length,
          beautician_id: self.beautician_id,
          service_name: (self.filter_service_name || '').trim(),
          booking_code: (self.filter_booking_code || '').trim(),
          start_date: self.filter_start_date,
          end_date: self.filter_end_date
        }).then(function (response) {
          if (response.data && Number(response.data.errorcode) === 0) {
            self.ServiceList = (response.data.data && response.data.data.data) ? response.data.data.data : [];
            self.pagination = response.data.pagination || self.pagination;
            self.filtered_total = response.data.filtered_total ?? 0;
          } else {
            self.ServiceList = [];
            self.filtered_total = 0;
            if (Vue.$toast) {
              Vue.$toast.error((response.data && response.data.errormessage) || '{{ __('Failed to load services.') }}');
            }
          }
        }).catch(function () {
          self.ServiceList = [];
          self.filtered_total = 0;
          if (Vue.$toast) {
            Vue.$toast.error('{{ __('Failed to load services.') }}');
          }
        }).finally(function () {
          self.loading = false;
        });
      },
      resetFilter: function () {
        var range = currentMonthRange();
        this.filter_service_name = '';
        this.filter_booking_code = '';
        this.filter_start_date = range.start;
        this.filter_end_date = range.end;
        this.beautician_id = '';
        this.beauticianLabel = '';
        $('#beautician_id').val('').trigger('change');
        this.getServiceList(1);
      },
      openSummary: function () {
        this.syncBeautician();
        this.getSummaryData();
        $('#serviceSummaryModal').modal('show');
      },
      getSummaryData: function () {
        var self = this;
        self.syncBeautician();
        self.summaryLoading = true;

        axios.post("{{ route('service.summary') }}", {
          start_date: self.filter_start_date,
          end_date: self.filter_end_date,
          beautician_id: self.beautician_id,
          service_name: (self.filter_service_name || '').trim(),
          booking_code: (self.filter_booking_code || '').trim()
        }).then(function (response) {
          if (response.data && Number(response.data.errorcode) === 0) {
            self.summaryData = response.data.data || [];
            self.totalCount = response.data.total_count || 0;
            self.totalAmount = response.data.total_service_price || 0;
          } else {
            self.summaryData = [];
            self.totalCount = 0;
            self.totalAmount = 0;
          }
        }).catch(function () {
          self.summaryData = [];
          self.totalCount = 0;
          self.totalAmount = 0;
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
          + '&beautician_id=' + encodeURIComponent(this.beautician_id || '')
          + '&service_name=' + encodeURIComponent((this.filter_service_name || '').trim())
          + '&booking_code=' + encodeURIComponent((this.filter_booking_code || '').trim());
      },
      exportToPDF: function () {
        window.open("{{ route('service-report.export-to-pdf') }}" + this.exportQuery(), '_self');
      },
      exportToExcel: function () {
        window.open("{{ route('service-report.export-to-excel') }}" + this.exportQuery(), '_self');
      },
      printSummary: function () {
        this.syncBeautician();
        var url = "{{ route('service-report-summary.export-to-pdf') }}"
          + '?start_date=' + encodeURIComponent(this.filter_start_date || '')
          + '&end_date=' + encodeURIComponent(this.filter_end_date || '')
          + '&beautician_id=' + encodeURIComponent(this.beautician_id || '')
          + '&service_name=' + encodeURIComponent((this.filter_service_name || '').trim())
          + '&booking_code=' + encodeURIComponent((this.filter_booking_code || '').trim());
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
      this.getServiceList(1);
    }
  });
</script>
@endpush
