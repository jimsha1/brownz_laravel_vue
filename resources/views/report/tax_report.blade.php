@extends('layouts.vcapp')

@section('content')
<style>
  .tax-report-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .tax-report-filters .filter-control {
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

  .tax-report-filters .filter-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  @media (min-width: 992px) {
    .tax-report-filters .filter-actions {
      justify-content: flex-end;
    }
  }

  .tax-report-summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin-top: 14px;
  }

  @media (max-width: 767px) {
    .tax-report-summary {
      grid-template-columns: 1fr;
    }
  }

  .tax-report-summary .summary-card {
    background: #F7F7F9;
    border-radius: 16px;
    padding: 14px 16px;
  }

  .tax-report-summary .summary-label {
    display: block;
    font-size: 12px;
    color: #6c757d;
    font-weight: 600;
  }

  .tax-report-summary .summary-value {
    display: block;
    margin-top: 4px;
    font-size: 1.25rem;
    font-weight: 600;
    color: #1D1D1D;
    font-variant-numeric: tabular-nums;
  }

  .tax-report-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 14px;
  }

  .tax-report-tabs .tab-btn {
    border: none;
    background: #F0F0F3;
    color: #1D1D1D;
    border-radius: 999px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
  }

  .tax-report-tabs .tab-btn.is-active {
    background: #1D1D1D;
    color: #E6CD7F;
  }

  .tax-report-list .list-toolbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 14px;
  }

  .tax-report-list .list-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0;
  }

  .tax-report-list .list-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .tax-report-list .export-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .tax-report-list .amount-cell {
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
  }

  .tax-report-list .type-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
  }

  .tax-report-list .type-pill.is-sale {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .tax-report-list .type-pill.is-refund,
  .tax-report-list .type-pill.is-return {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  .tax-report-list .totals-row td {
    background: #F7F7F9;
    font-weight: 600;
  }

  .tax-report-list .empty-state,
  .tax-report-list .loading-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }
</style>

<div id="app">
  <div class="fbox-shadow mt-2 fd-card respo-margin-top tax-report-filters">
    <div class="card-body">
      <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
        {{ __('Tax Report') }}
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

        <div class="col-lg-3 col-md-6" v-if="tax_type === 'output'">
          <label class="filter-label" for="filter_type">{{ __('Type') }}</label>
          <select id="filter_type" class="form-control filter-control" v-model="filter_type">
            <option value="All">{{ __('All') }}</option>
            <option value="Sale">{{ __('Sales') }}</option>
            <option value="Refund">{{ __('Refund') }}</option>
          </select>
        </div>

        <div class="col-lg-3 col-md-6" v-if="tax_type === 'output'">
          <label class="filter-label" for="filter_booking_code">{{ __('Booking Code') }}</label>
          <input type="text" id="filter_booking_code" class="form-control filter-control"
            v-model="filter_booking_code"
            @keyup.enter="getTaxList(1)"
            placeholder="{{ __('Search booking code...') }}">
        </div>

        <div class="col-lg-3 col-md-6" v-if="tax_type === 'input'">
          <label class="filter-label" for="filter_type">{{ __('Type') }}</label>
          <select id="filter_type" class="form-control filter-control" v-model="filter_type">
            <option value="All">{{ __('All') }}</option>
            <option value="Purchase">{{ __('Purchase') }}</option>
            <option value="Return">{{ __('Return') }}</option>
          </select>
        </div>

        <div class="col-lg-3 col-md-6" v-if="tax_type === 'input'">
          <label class="filter-label" for="filter_purchase_ref">{{ __('Purchase Ref / Invoice') }}</label>
          <input type="text" id="filter_purchase_ref" class="form-control filter-control"
            v-model="filter_purchase_ref"
            @keyup.enter="getTaxList(1)"
            placeholder="{{ __('Search purchase ref...') }}">
        </div>

        <div class="col-lg-3 col-md-6" v-if="tax_type === 'input'">
          <label class="filter-label" for="filter_vendor_name">{{ __('Vendor Name') }}</label>
          <input type="text" id="filter_vendor_name" class="form-control filter-control"
            v-model="filter_vendor_name"
            @keyup.enter="getTaxList(1)"
            placeholder="{{ __('Search vendor...') }}">
        </div>

        <div class="col-lg-3 col-md-6">
          <label class="filter-label d-none d-lg-block">&nbsp;</label>
          <div class="filter-actions">
            <button type="button" class="btn btn-sm btn-primary btn_dark-x" @click="getTaxList(1)">
              <i class="fa fa-search"></i> {{ __('Search') }}
            </button>
            <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d" @click="resetFilter()">
              <i class="fa fa-refresh"></i> {{ __('Reset') }}
            </button>
          </div>
        </div>
      </div>

      <div class="tax-report-summary">
        <div class="summary-card">
          <span class="summary-label">{{ __('Output Tax (Sales)') }}</span>
          <span class="summary-value">@{{ formatAmount(summary.output_tax) }}</span>
        </div>
        <div class="summary-card">
          <span class="summary-label">{{ __('Input Tax (Purchases)') }}</span>
          <span class="summary-value">@{{ formatAmount(summary.input_tax) }}</span>
        </div>
        <div class="summary-card">
          <span class="summary-label">{{ __('Net GST Payable') }}</span>
          <span class="summary-value">@{{ formatAmount(summary.net_tax) }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="fbox-shadow mt-2 fd-card tax-report-list">
    <div class="card-body">
      <div class="tax-report-tabs">
        <button type="button" class="tab-btn" :class="{ 'is-active': tax_type === 'output' }" @click="setTaxType('output')">
          {{ __('Output Tax') }}
        </button>
        <button type="button" class="tab-btn" :class="{ 'is-active': tax_type === 'input' }" @click="setTaxType('input')">
          {{ __('Input Tax') }}
        </button>
      </div>

      <div class="list-toolbar">
        <div>
          <h5 class="list-title">
            <span v-if="tax_type === 'output'">{{ __('Output Tax List') }}</span>
            <span v-else>{{ __('Input Tax List') }}</span>
          </h5>
          <span class="list-meta" v-if="!loading && pagination.total">
            {{ __('Showing') }} @{{ pagination.from || 0 }}–@{{ pagination.to || 0 }}
            {{ __('of') }} @{{ pagination.total }}
          </span>
        </div>
        <div class="export-actions">
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
        <div>{{ __('Loading tax report...') }}</div>
      </div>

      <template v-else-if="TaxList.length">
        <div class="table-responsive" v-if="tax_type === 'output'">
          <table class="table brownz-table-list-v1 m-0">
            <thead>
              <tr>
                <th style="width: 56px;">{{ __('SL#') }}</th>
                <th>{{ __('Booking No') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Client') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('GST') }}(%)</th>
                <th>{{ __('Service Tax') }}</th>
                <th>{{ __('Item Tax') }}</th>
                <th>{{ __('Output Tax') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(data, index) in TaxList" :key="data.id || index">
                <td>@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</td>
                <td>@{{ data.ref_no || '—' }}</td>
                <td>@{{ data.date || '—' }}</td>
                <td>@{{ data.party || '—' }}</td>
                <td>
                  <span class="type-pill" :class="data.is_refund ? 'is-refund' : 'is-sale'">
                    @{{ data.booking_type || '—' }}
                  </span>
                </td>
                <td>@{{ data.tax_percent != null ? data.tax_percent : '—' }}</td>
                <td class="amount-cell">@{{ formatAmount(data.service_tax) }}</td>
                <td class="amount-cell">@{{ formatAmount(data.item_tax) }}</td>
                <td class="amount-cell">@{{ formatAmount(data.tax_amount) }}</td>
              </tr>
              <tr class="totals-row">
                <td colspan="6" class="text-right">{{ __('Total') }}</td>
                <td class="amount-cell">@{{ formatAmount(totals.service_tax) }}</td>
                <td class="amount-cell">@{{ formatAmount(totals.item_tax) }}</td>
                <td class="amount-cell">@{{ formatAmount(totals.tax_amount) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="table-responsive" v-else>
          <table class="table brownz-table-list-v1 m-0">
            <thead>
              <tr>
                <th style="width: 56px;">{{ __('SL#') }}</th>
                <th>{{ __('Purchase Ref') }}</th>
                <th>{{ __('Invoice No') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Vendor') }}</th>
                <th>{{ __('GST') }}(%)</th>
                <th>{{ __('Taxable Base') }}</th>
                <th>{{ __('Input Tax') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(data, index) in TaxList" :key="data.id || index">
                <td>@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</td>
                <td>@{{ data.ref_no || '—' }}</td>
                <td>@{{ data.invoice_no || '—' }}</td>
                <td>@{{ data.date || '—' }}</td>
                <td>@{{ data.party || '—' }}</td>
                <td>@{{ data.tax_percent != null ? data.tax_percent : '—' }}</td>
                <td class="amount-cell">@{{ formatAmount(data.taxable_base) }}</td>
                <td class="amount-cell">@{{ formatAmount(data.tax_amount) }}</td>
              </tr>
              <tr class="totals-row">
                <td colspan="6" class="text-right">{{ __('Total') }}</td>
                <td class="amount-cell">@{{ formatAmount(totals.taxable_base) }}</td>
                <td class="amount-cell">@{{ formatAmount(totals.tax_amount) }}</td>
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
        <div><i class="fa fa-percent" aria-hidden="true"></i></div>
        <div><strong>{{ __('No tax records found') }}</strong></div>
        <div>{{ __('Try adjusting the date range or search filters.') }}</div>
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
      tax_type: 'output',
      TaxList: [],
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
      filter_start_date: monthRange.start,
      filter_end_date: monthRange.end,
      filter_booking_code: '',
      filter_purchase_ref: '',
      filter_vendor_name: '',
      filter_type: 'All',
      summary: {
        output_tax: 0,
        input_tax: 0,
        net_tax: 0
      },
      totals: {
        service_tax: 0,
        item_tax: 0,
        tax_amount: 0,
        taxable_base: 0
      }
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
      setTaxType: function (type) {
        if (this.tax_type === type) {
          return;
        }
        this.tax_type = type;
        this.filter_type = 'All';
        this.getTaxList(1);
      },
      changePage: function (page) {
        this.getTaxList(page);
      },
      getTaxList: function (page) {
        var self = this;
        page = page || 1;
        self.loading = true;
        self.pagination.current_page = page;

        axios.post("{{ route('tax.vue-list') }}", {
          page: page,
          page_length: self.page_length,
          tax_type: self.tax_type,
          start_date: self.filter_start_date,
          end_date: self.filter_end_date,
          booking_code: self.filter_booking_code,
          purchase_ref: self.filter_purchase_ref,
          vendor_name: self.filter_vendor_name,
          type: self.filter_type || 'All'
        }).then(function (response) {
          if (response.data && Number(response.data.errorcode) === 0) {
            self.TaxList = (response.data.data && response.data.data.data) ? response.data.data.data : [];
            self.pagination = response.data.pagination || self.pagination;
            self.summary = response.data.summary || self.summary;
            self.totals = response.data.totals || {
              service_tax: 0,
              item_tax: 0,
              tax_amount: 0,
              taxable_base: 0
            };
          } else {
            self.TaxList = [];
            if (Vue.$toast) {
              Vue.$toast.error((response.data && response.data.errormessage) || '{{ __('Failed to load tax report.') }}');
            }
          }
        }).catch(function () {
          self.TaxList = [];
          if (Vue.$toast) {
            Vue.$toast.error('{{ __('Failed to load tax report.') }}');
          }
        }).finally(function () {
          self.loading = false;
        });
      },
      resetFilter: function () {
        var range = currentMonthRange();
        this.filter_start_date = range.start;
        this.filter_end_date = range.end;
        this.filter_booking_code = '';
        this.filter_purchase_ref = '';
        this.filter_vendor_name = '';
        this.filter_type = 'All';
        this.getTaxList(1);
      },
      exportQuery: function () {
        return '?tax_type=' + encodeURIComponent(this.tax_type || 'output')
          + '&start_date=' + encodeURIComponent(this.filter_start_date || '')
          + '&end_date=' + encodeURIComponent(this.filter_end_date || '')
          + '&booking_code=' + encodeURIComponent(this.filter_booking_code || '')
          + '&purchase_ref=' + encodeURIComponent(this.filter_purchase_ref || '')
          + '&vendor_name=' + encodeURIComponent(this.filter_vendor_name || '')
          + '&type=' + encodeURIComponent(this.filter_type || 'All');
      },
      exportToPDF: function () {
        window.open("{{ route('tax-report.export-to-pdf') }}" + this.exportQuery(), '_self');
      },
      exportToExcel: function () {
        window.open("{{ route('tax-report.export-to-excel') }}" + this.exportQuery(), '_self');
      }
    },
    mounted: function () {
      this.getTaxList(1);
    }
  });
</script>
@endpush
