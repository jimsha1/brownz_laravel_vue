@extends('layouts.vcapp')

@section('content')
<style>
  .vendor-report-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .vendor-report-filters .filter-control {
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

  .vendor-report-filters .filter-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  @media (min-width: 992px) {
    .vendor-report-filters .filter-actions {
      justify-content: flex-end;
    }
  }

  .vendor-report-list .list-toolbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 14px;
  }

  .vendor-report-list .list-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0;
  }

  .vendor-report-list .list-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .vendor-report-list .vendor-name {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
  }

  .vendor-report-list .vendor-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .vendor-report-list .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
  }

  .vendor-report-list .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .vendor-report-list .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  .vendor-report-list .export-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .vendor-report-list .amount-cell {
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
  }

  .vendor-report-list .totals-row td {
    background: #F7F7F9;
    font-weight: 600;
  }

  .vendor-report-list .empty-state,
  .vendor-report-list .loading-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }
</style>

<div id="app">
  <div class="fbox-shadow mt-2 fd-card respo-margin-top vendor-report-filters">
    <div class="card-body">
      <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
        {{ __('Vendor Report') }}
      </h5>

      <div class="row align-items-end br-row-gap-2">
        <div class="col-lg-3 col-md-6">
          <label class="filter-label" for="filter_vendor_name">{{ __('Vendor Name') }}</label>
          <div class="search-box-bar-container jus-heigh-search-b">
            <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
            <input type="text" id="filter_vendor_name" class="form-control brawnz-search-bar-in"
              v-model="filter_vendor_name"
              @keyup.enter="getVendorList(1)"
              placeholder="{{ __('Search by name...') }}">
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <label class="filter-label" for="filter_vendor_email">{{ __('Vendor Email') }}</label>
          <input type="text" id="filter_vendor_email" class="form-control filter-control"
            v-model="filter_vendor_email"
            @keyup.enter="getVendorList(1)"
            placeholder="{{ __('Search by email...') }}">
        </div>

        <div class="col-lg-3 col-md-6">
          <label class="filter-label" for="filter_vendor_phone">{{ __('Vendor Phone') }}</label>
          <input type="text" id="filter_vendor_phone" class="form-control filter-control"
            v-model="filter_vendor_phone"
            @keyup.enter="getVendorList(1)"
            placeholder="{{ __('Search by phone...') }}">
        </div>

        <div class="col-lg-3 col-md-6">
          <label class="filter-label d-none d-lg-block">&nbsp;</label>
          <div class="filter-actions">
            <button type="button" class="btn btn-sm btn-primary btn_dark-x" @click="getVendorList(1)">
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

  <div class="fbox-shadow mt-2 fd-card vendor-report-list">
    <div class="card-body">
      <div class="list-toolbar">
        <div>
          <h5 class="list-title">{{ __('Vendor List') }}</h5>
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
        <div>{{ __('Loading vendors...') }}</div>
      </div>

      <template v-else-if="VendorList.length">
        <div class="table-responsive">
          <table class="table brownz-table-list-v1 m-0">
            <thead>
              <tr>
                <th style="width: 56px;">{{ __('SL#') }}</th>
                <th>{{ __('Vendor') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Phone') }}</th>
                <th>{{ __('Purchase Amount') }}</th>
                <th>{{ __('Balance (Credit)') }}</th>
                <th>{{ __('Status') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(data, index) in VendorList" :key="data.vendor_id || index">
                <td>@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</td>
                <td>
                  <span class="vendor-name">@{{ data.name || '—' }}</span>
                </td>
                <td>
                  <span class="vendor-meta">@{{ data.email || '—' }}</span>
                </td>
                <td>@{{ data.phone || '—' }}</td>
                <td class="amount-cell">@{{ formatBalance(data.total_purchase_amount) }}</td>
                <td class="amount-cell">@{{ formatBalance(data.credit_amount) }}</td>
                <td>
                  <span class="status-pill" :class="isVendorActive(data.is_active) ? 'is-active' : 'is-inactive'">
                    <span v-if="isVendorActive(data.is_active)">{{ __('Active') }}</span>
                    <span v-else>{{ __('Inactive') }}</span>
                  </span>
                </td>
              </tr>
              <tr class="totals-row">
                <td colspan="4" class="text-right">{{ __('Total') }}</td>
                <td class="amount-cell">@{{ formatBalance(totals.total_purchase_amount) }}</td>
                <td class="amount-cell">@{{ formatBalance(totals.total_credit_amount) }}</td>
                <td></td>
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
        <div><i class="fa fa-truck" aria-hidden="true"></i></div>
        <div><strong>{{ __('No vendors found') }}</strong></div>
        <div>{{ __('Try adjusting name, email, or phone filters.') }}</div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vuejs/axios/axios.min.js') }}"></script>
<link href="{{ asset('assets/vuejs/toast/vue_toast.css') }}" rel="stylesheet">
<script src="{{ asset('assets/vuejs/toast/vue_toast.js') }}"></script>
@endpush

@push('inline-scripts')
<script>
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
      VendorList: [],
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
      filter_vendor_name: '',
      filter_vendor_email: '',
      filter_vendor_phone: '',
      totals: {
        total_purchase_amount: 0,
        total_credit_amount: 0
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
      isVendorActive: function (value) {
        return Number(value) === 1;
      },
      changePage: function (page) {
        this.getVendorList(page);
      },
      getVendorList: function (page) {
        var self = this;
        page = page || 1;
        self.loading = true;
        self.pagination.current_page = page;

        axios.post("{{ route('vendor.vue-list') }}", {
          page: page,
          page_length: self.page_length,
          vendor_name: self.filter_vendor_name,
          vendor_email: self.filter_vendor_email,
          vendor_phone: self.filter_vendor_phone
        }).then(function (response) {
          if (response.data && Number(response.data.errorcode) === 0) {
            self.VendorList = (response.data.data && response.data.data.data) ? response.data.data.data : [];
            self.pagination = response.data.pagination || self.pagination;
            self.totals = response.data.totals || {
              total_purchase_amount: 0,
              total_credit_amount: 0
            };
          } else {
            self.VendorList = [];
            self.totals = { total_purchase_amount: 0, total_credit_amount: 0 };
            if (Vue.$toast) {
              Vue.$toast.error((response.data && response.data.errormessage) || '{{ __('Failed to load vendors.') }}');
            }
          }
        }).catch(function () {
          self.VendorList = [];
          self.totals = { total_purchase_amount: 0, total_credit_amount: 0 };
          if (Vue.$toast) {
            Vue.$toast.error('{{ __('Failed to load vendors.') }}');
          }
        }).finally(function () {
          self.loading = false;
        });
      },
      resetFilter: function () {
        this.filter_vendor_name = '';
        this.filter_vendor_email = '';
        this.filter_vendor_phone = '';
        this.getVendorList(1);
      },
      formatBalance: function (balance) {
        var value = parseFloat(balance);
        if (isNaN(value)) {
          return '0.00';
        }
        return value.toFixed(2);
      },
      exportQuery: function () {
        return '?vendor_name=' + encodeURIComponent(this.filter_vendor_name || '')
          + '&vendor_email=' + encodeURIComponent(this.filter_vendor_email || '')
          + '&vendor_phone=' + encodeURIComponent(this.filter_vendor_phone || '');
      },
      exportToPDF: function () {
        window.open("{{ route('vendor-report.export-to-pdf') }}" + this.exportQuery(), '_self');
      },
      exportToExcel: function () {
        window.open("{{ route('vendor-report.export-to-excel') }}" + this.exportQuery(), '_self');
      }
    },
    mounted: function () {
      this.getVendorList(1);
    }
  });
</script>
@endpush
