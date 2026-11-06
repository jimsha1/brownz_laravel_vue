@extends('layouts.vcapp')

@section('content')
<style>
  .edit-log-report-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .edit-log-report-filters .filter-control {
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

  .edit-log-report-filters select.filter-control {
    padding-right: 28px;
  }

  .edit-log-report-filters .filter-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  @media (min-width: 992px) {
    .edit-log-report-filters .filter-actions {
      justify-content: flex-end;
    }
  }

  .edit-log-report-list .list-toolbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 14px;
  }

  .edit-log-report-list .list-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0;
  }

  .edit-log-report-list .list-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .edit-log-report-list .export-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .edit-log-report-list .type-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
  }

  .edit-log-report-list .type-pill.is-booking {
    color: #1D4E89;
    background: rgba(29, 78, 137, 0.12);
  }

  .edit-log-report-list .type-pill.is-purchase {
    color: #8A5A00;
    background: rgba(184, 122, 0, 0.16);
  }

  .edit-log-report-list .type-pill.is-voucher {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .edit-log-report-list .reason-cell {
    min-width: 220px;
    white-space: normal;
  }

  .edit-log-report-list .ref-link {
    font-weight: 600;
    color: #1D1D1D;
  }

  .edit-log-report-list .empty-state,
  .edit-log-report-list .loading-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }
</style>

<div id="app">
  <div class="fbox-shadow mt-2 fd-card respo-margin-top edit-log-report-filters">
    <div class="card-body">
      <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
        {{ __('Edit Log Report') }}
      </h5>

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
          <label class="filter-label" for="filter_source_type">{{ __('Type') }}</label>
          <select id="filter_source_type" class="form-control filter-control" v-model="filter_source_type">
            <option value="all">{{ __('All') }}</option>
            <option value="booking">{{ __('Booking') }}</option>
            <option value="purchase">{{ __('Purchase') }}</option>
            <option value="voucher">{{ __('Stock Voucher') }}</option>
          </select>
        </div>

        <div class="col-lg-2 col-md-4">
          <label class="filter-label" for="filter_edited_by">{{ __('Edited By') }}</label>
          <select id="filter_edited_by" class="form-control filter-control" v-model="filter_edited_by">
            <option value="">{{ __('All') }}</option>
            @foreach ($editors as $editor)
              <option value="{{ $editor->id }}">{{ $editor->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-lg-2 col-md-4">
          <label class="filter-label" for="filter_search">{{ __('Search') }}</label>
          <input type="text" id="filter_search" class="form-control filter-control"
            v-model="filter_search"
            @keyup.enter="getEditLogs(1)"
            placeholder="{{ __('Reference or reason') }}">
        </div>

        <div class="col-lg-2 col-md-4">
          <label class="filter-label d-none d-lg-block">&nbsp;</label>
          <div class="filter-actions">
            <button type="button" class="btn btn-sm btn-primary btn_dark-x" @click="getEditLogs(1)">
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

  <div class="fbox-shadow mt-2 fd-card edit-log-report-list">
    <div class="card-body">
      <div class="list-toolbar">
        <div>
          <h5 class="list-title">{{ __('Edit Logs') }}</h5>
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
        <div>{{ __('Loading edit logs...') }}</div>
      </div>

      <template v-else-if="editLogs.length">
        <div class="table-responsive">
          <table class="table brownz-table-list-v1 m-0">
            <thead>
              <tr>
                <th style="width: 56px;">{{ __('SL#') }}</th>
                <th>{{ __('Edited At') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Reference') }}</th>
                <th>{{ __('Edit Reason') }}</th>
                <th>{{ __('Edited By') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(row, index) in editLogs" :key="row.source_type + '-' + row.id">
                <td>@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</td>
                <td>@{{ row.edited_at || '—' }}</td>
                <td>
                  <span class="type-pill" :class="typeClass(row.source_type)">@{{ row.source_label || '—' }}</span>
                </td>
                <td>
                  <a v-if="row.view_url" class="ref-link" :href="row.view_url">@{{ row.reference_no || '—' }}</a>
                  <span v-else>@{{ row.reference_no || '—' }}</span>
                </td>
                <td class="reason-cell">@{{ row.edit_reason || '—' }}</td>
                <td>@{{ row.edited_by_name || '—' }}</td>
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
        <div><i class="fa fa-history" aria-hidden="true"></i></div>
        <div><strong>{{ __('No edit logs found') }}</strong></div>
        <div>{{ __('Try adjusting the date, type, user, or search filters.') }}</div>
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
      editLogs: [],
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
      filter_start_date: '',
      filter_end_date: '',
      filter_source_type: 'all',
      filter_edited_by: '',
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
      typeClass: function (source) {
        if (source === 'booking') {
          return 'is-booking';
        }
        if (source === 'purchase') {
          return 'is-purchase';
        }
        if (source === 'voucher') {
          return 'is-voucher';
        }
        return '';
      },
      changePage: function (page) {
        this.getEditLogs(page);
      },
      getEditLogs: function (page) {
        var self = this;
        page = page || 1;
        self.loading = true;
        self.pagination.current_page = page;

        axios.post("{{ route('edit-log.vue-list') }}", {
          page: page,
          page_length: self.page_length,
          start_date: self.filter_start_date,
          end_date: self.filter_end_date,
          source_type: self.filter_source_type,
          edited_by: self.filter_edited_by,
          search: self.filter_search
        }).then(function (response) {
          if (response.data && Number(response.data.errorcode) === 0) {
            self.editLogs = (response.data.data && response.data.data.data) ? response.data.data.data : [];
            self.pagination = response.data.pagination || self.pagination;
          } else {
            self.editLogs = [];
            if (Vue.$toast) {
              Vue.$toast.error((response.data && response.data.errormessage) || '{{ __('Failed to load edit logs.') }}');
            }
          }
        }).catch(function () {
          self.editLogs = [];
          if (Vue.$toast) {
            Vue.$toast.error('{{ __('Failed to load edit logs.') }}');
          }
        }).finally(function () {
          self.loading = false;
        });
      },
      resetFilter: function () {
        this.filter_start_date = '';
        this.filter_end_date = '';
        this.filter_source_type = 'all';
        this.filter_edited_by = '';
        this.filter_search = '';
        this.getEditLogs(1);
      },
      exportQuery: function () {
        return '?start_date=' + encodeURIComponent(this.filter_start_date || '')
          + '&end_date=' + encodeURIComponent(this.filter_end_date || '')
          + '&source_type=' + encodeURIComponent(this.filter_source_type || 'all')
          + '&edited_by=' + encodeURIComponent(this.filter_edited_by || '')
          + '&search=' + encodeURIComponent(this.filter_search || '');
      },
      exportToPDF: function () {
        window.open("{{ route('edit-log-report.export-to-pdf') }}" + this.exportQuery(), '_self');
      },
      exportToExcel: function () {
        window.open("{{ route('edit-log-report.export-to-excel') }}" + this.exportQuery(), '_self');
      }
    },
    mounted: function () {
      this.getEditLogs(1);
    }
  });
</script>
@endpush
