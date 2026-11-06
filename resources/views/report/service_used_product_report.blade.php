@extends('layouts.vcapp')

@section('content')
<style>
  .used-product-report-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .used-product-report-filters .filter-control {
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

  .used-product-report-filters select.filter-control {
    padding-right: 28px;
  }

  .used-product-report-filters .filter-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  @media (min-width: 992px) {
    .used-product-report-filters .filter-actions {
      justify-content: flex-end;
    }
  }

  .used-product-report-list .list-toolbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 14px;
  }

  .used-product-report-list .list-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0;
  }

  .used-product-report-list .list-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .used-product-report-list .export-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .used-product-report-list .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 4px 10px;
    line-height: 1.2;
    white-space: nowrap;
  }

  .used-product-report-list .status-pill.is-used {
    color: #2f6b3a;
    background: #f3f7ee;
  }

  .used-product-report-list .status-pill.is-pending {
    color: #8A5A00;
    background: rgba(184, 122, 0, 0.16);
  }

  .used-product-report-list .ref-link {
    font-weight: 600;
    color: #1D1D1D;
  }

  .used-product-report-list .num {
    text-align: right;
    white-space: nowrap;
  }

  .used-product-report-list .empty-state,
  .used-product-report-list .loading-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }
</style>

<div id="app">
  <div class="fbox-shadow mt-2 fd-card respo-margin-top used-product-report-filters">
    <div class="card-body">
      <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-1">
        {{ __('Service Used Products') }}
      </h5>
      <p class="text-muted mb-3" style="font-size: 13px;">
        {{ __('Products consumed when a service is completed. The client is not charged for these products.') }}
      </p>

      <div class="row align-items-end br-row-gap-2">
        <div class="col-lg-2 col-md-4">
          <label class="filter-label" for="filter_start_date">{{ __('From Date') }}</label>
          <input type="date" id="filter_start_date" class="form-control filter-control" v-model="filter_start_date">
        </div>

        <div class="col-lg-2 col-md-4">
          <label class="filter-label" for="filter_end_date">{{ __('To Date') }}</label>
          <input type="date" id="filter_end_date" class="form-control filter-control" v-model="filter_end_date">
        </div>

        <div class="col-lg-2 col-md-4">
          <label class="filter-label" for="filter_status">{{ __('Status') }}</label>
          <select id="filter_status" class="form-control filter-control" v-model="filter_status">
            <option value="used">{{ __('Used') }}</option>
            <option value="pending">{{ __('Pending') }}</option>
            <option value="all">{{ __('All') }}</option>
          </select>
        </div>

        <div class="col-lg-2 col-md-4">
          <label class="filter-label" for="filter_beautician">{{ __('Beautician') }}</label>
          <select id="filter_beautician" class="form-control filter-control" v-model="filter_beautician_id">
            <option value="">{{ __('All') }}</option>
            {!! $beauticians_dropdown !!}
          </select>
        </div>

        <div class="col-lg-2 col-md-4">
          <label class="filter-label" for="filter_search">{{ __('Search') }}</label>
          <input type="text" id="filter_search" class="form-control filter-control"
            v-model="filter_search"
            @keyup.enter="getRows(1)"
            placeholder="{{ __('Booking, client, service, product') }}">
        </div>

        <div class="col-lg-2 col-md-4">
          <label class="filter-label d-none d-lg-block">&nbsp;</label>
          <div class="filter-actions">
            <button type="button" class="btn btn-sm btn-primary btn_dark-x" @click="getRows(1)">
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

  <div class="fbox-shadow mt-2 fd-card used-product-report-list">
    <div class="card-body">
      <div class="list-toolbar">
        <div>
          <h5 class="list-title">{{ __('Products used') }}</h5>
          <span class="list-meta" v-if="!loading && pagination.total">
            {{ __('Showing') }} @{{ pagination.from || 0 }}–@{{ pagination.to || 0 }}
            {{ __('of') }} @{{ pagination.total }}
            · {{ __('Est. cost') }} @{{ formatMoney(total_cost) }}
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
        <div>{{ __('Loading products used...') }}</div>
      </div>

      <template v-else-if="rows.length">
        <div class="table-responsive">
          <table class="table brownz-table-list-v1 m-0">
            <thead>
              <tr>
                <th style="width: 56px;">{{ __('SL#') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Booking') }}</th>
                <th>{{ __('Client') }}</th>
                <th>{{ __('Service') }}</th>
                <th>{{ __('Beautician') }}</th>
                <th>{{ __('Product') }}</th>
                <th class="num">{{ __('Qty') }}</th>
                <th>{{ __('Unit') }}</th>
                <th class="num">{{ __('Est. cost') }}</th>
                <th>{{ __('Status') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(row, index) in rows" :key="row.id">
                <td>@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</td>
                <td>@{{ row.date || '—' }}</td>
                <td>
                  <a v-if="row.view_url" class="ref-link" :href="row.view_url">@{{ row.booking_code || '—' }}</a>
                  <span v-else>@{{ row.booking_code || '—' }}</span>
                </td>
                <td>@{{ row.client_name || '—' }}</td>
                <td>@{{ row.service_name || '—' }}</td>
                <td>@{{ row.beautician_name || '—' }}</td>
                <td>@{{ row.item_name || '—' }}</td>
                <td class="num">@{{ row.quantity }}</td>
                <td>@{{ row.uom_name || '—' }}</td>
                <td class="num">@{{ formatMoney(row.estimated_cost) }}</td>
                <td>
                  <span class="status-pill" :class="row.status === 'Used' ? 'is-used' : 'is-pending'">@{{ row.status }}</span>
                </td>
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
        <div><i class="fa fa-cubes" aria-hidden="true"></i></div>
        <div><strong>{{ __('No products used in this period') }}</strong></div>
        <div>{{ __('Try another date, status, beautician, or search.') }}</div>
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
      rows: [],
      total_cost: 0,
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
      default_start_date: @json($start_date),
      default_end_date: @json($end_date),
      filter_start_date: @json($start_date),
      filter_end_date: @json($end_date),
      filter_status: 'used',
      filter_beautician_id: '',
      filter_search: ''
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
      formatMoney: function (value) {
        var n = Number(value);
        if (!isFinite(n)) {
          n = 0;
        }
        return n.toFixed(2);
      },
      changePage: function (page) {
        this.getRows(page);
      },
      getRows: function (page) {
        var self = this;
        page = page || 1;
        self.loading = true;
        self.pagination.current_page = page;

        axios.post("{{ route('service-used-product.vue-list') }}", {
          page: page,
          page_length: self.page_length,
          start_date: self.filter_start_date,
          end_date: self.filter_end_date,
          status: self.filter_status,
          beautician_id: self.filter_beautician_id,
          search: self.filter_search
        }).then(function (response) {
          if (response.data && Number(response.data.errorcode) === 0) {
            self.rows = (response.data.data && response.data.data.data) ? response.data.data.data : [];
            self.pagination = response.data.pagination || self.pagination;
            self.total_cost = response.data.total_cost || 0;
          } else {
            self.rows = [];
            self.total_cost = 0;
            if (Vue.$toast) {
              Vue.$toast.error((response.data && response.data.errormessage) || '{{ __('Failed to load the report.') }}');
            }
          }
        }).catch(function () {
          self.rows = [];
          self.total_cost = 0;
          if (Vue.$toast) {
            Vue.$toast.error('{{ __('Failed to load the report.') }}');
          }
        }).finally(function () {
          self.loading = false;
        });
      },
      resetFilter: function () {
        this.filter_start_date = this.default_start_date;
        this.filter_end_date = this.default_end_date;
        this.filter_status = 'used';
        this.filter_beautician_id = '';
        this.filter_search = '';
        this.getRows(1);
      },
      exportQuery: function () {
        return '?start_date=' + encodeURIComponent(this.filter_start_date || '')
          + '&end_date=' + encodeURIComponent(this.filter_end_date || '')
          + '&status=' + encodeURIComponent(this.filter_status || 'used')
          + '&beautician_id=' + encodeURIComponent(this.filter_beautician_id || '')
          + '&search=' + encodeURIComponent(this.filter_search || '');
      },
      exportToPDF: function () {
        window.open("{{ route('service-used-product-report.export-to-pdf') }}" + this.exportQuery(), '_self');
      },
      exportToExcel: function () {
        window.open("{{ route('service-used-product-report.export-to-excel') }}" + this.exportQuery(), '_self');
      }
    },
    mounted: function () {
      this.getRows(1);
    }
  });
</script>
@endpush
