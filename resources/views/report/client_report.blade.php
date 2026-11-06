@extends('layouts.vcapp')

@section('content')
<style>
  .client-report-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .client-report-filters .filter-control {
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

  .client-report-filters select.filter-control {
    appearance: auto;
  }

  .client-report-filters .filter-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  @media (min-width: 992px) {
    .client-report-filters .filter-actions {
      justify-content: flex-end;
    }
  }

  .client-report-list .list-toolbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 14px;
  }

  .client-report-list .list-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0;
  }

  .client-report-list .list-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .client-report-list .client-name {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
  }

  .client-report-list .client-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .client-report-list .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    text-transform: capitalize;
  }

  .client-report-list .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .client-report-list .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  .client-report-list .status-pill.is-pending {
    color: #9A6700;
    background: rgba(230, 205, 127, 0.35);
  }

  .client-report-list .status-pill.is-blocked,
  .client-report-list .status-pill.is-deleted {
    color: #6c757d;
    background: rgba(108, 117, 125, 0.12);
  }

  .client-report-list .export-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .client-report-list .manage-actions {
    display: flex;
    gap: 8px;
    justify-content: center;
  }

  .client-report-list .manage-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #F0F0F3;
    color: #1D1D1D;
    border: none;
    cursor: pointer;
  }

  .client-report-list .manage-btn:hover {
    background: #1D1D1D;
    color: #E6CD7F;
  }

  .client-report-list .empty-state,
  .client-report-list .loading-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }

  .client-report-modal .modal-content {
    border-radius: 16px;
    border: none;
  }

  .client-report-modal .modal-header {
    border: 0;
  }

  .client-report-modal .modal-title {
    color: #1D1D1D;
    font-weight: 600;
  }

  .client-report-modal .form-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .client-report-modal .form-control {
    height: 38px;
    border: none;
    outline: none;
    border-radius: 999px;
    background: #F0F0F3;
    padding: 8px 14px;
    font-size: 14px;
    color: #1D1D1D;
  }

  .client-report-modal select.form-control {
    appearance: auto;
  }

  .client-report-modal .mobile-prefix {
    display: inline-flex;
    align-items: center;
    height: 38px;
    padding: 0 12px;
    border-radius: 999px 0 0 999px;
    background: #E8E8EC;
    color: #6c757d;
    font-size: 13px;
    font-weight: 600;
  }

  .client-report-modal .mobile-input {
    border-radius: 0 999px 999px 0 !important;
  }
</style>

<div id="app">
  <div class="fbox-shadow mt-2 fd-card respo-margin-top client-report-filters">
    <div class="card-body">
      <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
        {{ __('Client Report') }}
      </h5>

      <div class="row align-items-end br-row-gap-2">
        <div class="col-lg-3 col-md-6">
          <label class="filter-label" for="filter_client_name">{{ __('Client Name') }}</label>
          <div class="search-box-bar-container jus-heigh-search-b">
            <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
            <input type="text" id="filter_client_name" class="form-control brawnz-search-bar-in"
              v-model="filter_client_name"
              @keyup.enter="getClientList(1)"
              placeholder="{{ __('Search by name...') }}">
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <label class="filter-label" for="filter_client_mobile">{{ __('Client Mobile') }}</label>
          <input type="text" id="filter_client_mobile" class="form-control filter-control"
            v-model="filter_client_mobile"
            @keyup.enter="getClientList(1)"
            placeholder="{{ __('Search by mobile...') }}">
        </div>

        <div class="col-lg-2 col-md-6">
          <label class="filter-label" for="filter_status">{{ __('Status') }}</label>
          <select id="filter_status" class="form-control filter-control" v-model="filter_status">
            <option value="all">{{ __('All Status') }}</option>
            <option value="active">{{ __('Active') }}</option>
            <option value="inactive">{{ __('Inactive') }}</option>
            <option value="pending">{{ __('Pending') }}</option>
            <option value="blocked">{{ __('Blocked') }}</option>
          </select>
        </div>

        <div class="col-lg-4 col-md-6">
          <label class="filter-label d-none d-lg-block">&nbsp;</label>
          <div class="filter-actions">
            <button type="button" class="btn btn-sm btn-primary btn_dark-x" @click="getClientList(1)">
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

  <div class="fbox-shadow mt-2 fd-card client-report-list">
    <div class="card-body">
      <div class="list-toolbar">
        <div>
          <h5 class="list-title">{{ __('Client List') }}</h5>
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
        <div>{{ __('Loading clients...') }}</div>
      </div>

      <template v-else-if="ClientList.length">
        <div class="table-responsive">
          <table class="table brownz-table-list-v1 m-0">
            <thead>
              <tr>
                <th style="width: 56px;">{{ __('SL#') }}</th>
                <th>{{ __('Client') }}</th>
                <th>{{ __('Username') }}</th>
                <th>{{ __('Mobile') }}</th>
                <th>{{ __('Status') }}</th>
                <th class="text-center" style="width: 72px;">{{ __('Action') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(data, index) in ClientList" :key="data.client_id || index">
                <td>@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</td>
                <td>
                  <span class="client-name">@{{ (data.user && data.user.name) ? data.user.name : '—' }}</span>
                  <span class="client-meta" v-if="data.user && data.user.email">@{{ data.user.email }}</span>
                </td>
                <td>
                  <span class="client-meta">@{{ (data.user && data.user.username) ? data.user.username : '—' }}</span>
                </td>
                <td>@{{ (data.user && data.user.mobile) ? data.user.mobile : '—' }}</td>
                <td>
                  <span class="status-pill" :class="statusPillClass(data.user && data.user.user_status)">
                    @{{ statusLabel(data.user && data.user.user_status) }}
                  </span>
                </td>
                <td class="text-center">
                  <div class="manage-actions">
                    <button type="button" class="manage-btn" title="{{ __('Edit') }}" @click="openEditModal(data)">
                      <i class="fa fa-pencil" aria-hidden="true"></i>
                    </button>
                  </div>
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
        <div><i class="fa fa-users" aria-hidden="true"></i></div>
        <div><strong>{{ __('No clients found') }}</strong></div>
        <div>{{ __('Try adjusting name, mobile, or status filters.') }}</div>
      </div>
    </div>
  </div>

  <div class="modal fade brownz-custom-popup client-report-modal" id="editClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Edit Client') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form @submit.prevent="saveClientDetails">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label" for="edit_name">{{ __('Name') }}</label>
                <input type="text" id="edit_name" class="form-control" v-model="editForm.name" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label" for="edit_username">{{ __('Username') }}</label>
                <input type="text" id="edit_username" class="form-control" v-model="editForm.username" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label" for="edit_email">{{ __('Email') }}</label>
                <input type="email" id="edit_email" class="form-control" v-model="editForm.email"
                  placeholder="{{ __('Optional') }}">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label" for="edit_mobile">{{ __('Mobile') }}</label>
                <div class="d-flex">
                  <span class="mobile-prefix">+91</span>
                  <input type="text" id="edit_mobile" class="form-control mobile-input"
                    v-model="editForm.mobile" maxlength="10" required
                    placeholder="{{ __('10 digit mobile') }}">
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label" for="edit_status">{{ __('Status') }}</label>
                <select id="edit_status" class="form-control" v-model="editForm.status" required>
                  <option value="active">{{ __('Active') }}</option>
                  <option value="inactive">{{ __('Inactive') }}</option>
                  <option value="pending">{{ __('Pending') }}</option>
                  <option value="blocked">{{ __('Blocked') }}</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary brownz-btn-yellow-d" data-dismiss="modal" :disabled="saving">
              {{ __('Cancel') }}
            </button>
            <button type="submit" class="btn btn-primary btn_dark-x" :disabled="saving">
              <i class="fa" :class="saving ? 'fa-spinner fa-spin' : 'fa-check'"></i>
              <span v-if="saving">{{ __('Saving...') }}</span>
              <span v-else>{{ __('Save') }}</span>
            </button>
          </div>
        </form>
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

  if (typeof axios !== 'undefined') {
    axios.defaults.headers.common['X-CSRF-TOKEN'] =
      document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  }

  var app = new Vue({
    el: '#app',
    data: {
      loading: false,
      saving: false,
      ClientList: [],
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
      filter_client_name: '',
      filter_client_mobile: '',
      filter_status: 'all',
      editForm: {
        client_id: null,
        name: '',
        username: '',
        email: '',
        mobile: '',
        status: 'active'
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
      statusLabel: function (status) {
        if (!status) {
          return '—';
        }
        return String(status).charAt(0).toUpperCase() + String(status).slice(1);
      },
      statusPillClass: function (status) {
        var value = String(status || '').toLowerCase();
        if (value === 'active') return 'is-active';
        if (value === 'inactive') return 'is-inactive';
        if (value === 'pending') return 'is-pending';
        if (value === 'blocked' || value === 'deleted') return 'is-' + value;
        return 'is-pending';
      },
      normalizeMobile: function (mobile) {
        var digits = String(mobile || '').replace(/\D+/g, '');
        if (digits.length > 10 && digits.indexOf('91') === 0) {
          digits = digits.slice(-10);
        }
        return digits.slice(0, 10);
      },
      changePage: function (page) {
        this.getClientList(page);
      },
      getClientList: function (page) {
        var self = this;
        page = page || 1;
        self.loading = true;
        self.pagination.current_page = page;

        axios.post("{{ route('client.vue-list') }}", {
          page: page,
          page_length: self.page_length,
          client_name: self.filter_client_name,
          mobile: self.filter_client_mobile,
          status: self.filter_status
        }).then(function (response) {
          if (response.data && Number(response.data.errorcode) === 0) {
            self.ClientList = (response.data.data && response.data.data.data) ? response.data.data.data : [];
            self.pagination = response.data.pagination || self.pagination;
          } else {
            self.ClientList = [];
            if (Vue.$toast) {
              Vue.$toast.error((response.data && response.data.errormessage) || '{{ __('Failed to load clients.') }}');
            }
          }
        }).catch(function () {
          self.ClientList = [];
          if (Vue.$toast) {
            Vue.$toast.error('{{ __('Failed to load clients.') }}');
          }
        }).finally(function () {
          self.loading = false;
        });
      },
      resetFilter: function () {
        this.filter_client_name = '';
        this.filter_client_mobile = '';
        this.filter_status = 'all';
        this.getClientList(1);
      },
      openEditModal: function (row) {
        var user = row.user || {};
        this.editForm = {
          client_id: row.client_id,
          name: user.name || '',
          username: user.username || '',
          email: user.email || '',
          mobile: this.normalizeMobile(user.mobile),
          status: (user.user_status || 'active').toLowerCase()
        };
        $('#editClientModal').modal('show');
      },
      saveClientDetails: function () {
        var self = this;
        if (!self.editForm.client_id) {
          return;
        }

        if (!/^\d{10}$/.test(String(self.editForm.mobile || ''))) {
          if (Vue.$toast) {
            Vue.$toast.error('{{ __('The mobile number must be exactly 10 digits.') }}');
          }
          return;
        }

        self.saving = true;
        axios.post("{{ route('client-report.update') }}", {
          client_id: self.editForm.client_id,
          name: self.editForm.name,
          username: self.editForm.username,
          email: self.editForm.email,
          mobile: self.editForm.mobile,
          status: self.editForm.status
        }).then(function (response) {
          if (response.data && Number(response.data.errorcode) === 0) {
            if (Vue.$toast) {
              Vue.$toast.success(response.data.errormessage || '{{ __('Client details updated successfully.') }}');
            }
            $('#editClientModal').modal('hide');
            self.getClientList(self.pagination.current_page);
          } else if (Vue.$toast) {
            Vue.$toast.error((response.data && response.data.errormessage) || '{{ __('Failed to update client.') }}');
          }
        }).catch(function (error) {
          var message = '{{ __('Failed to update client.') }}';
          if (error.response && error.response.data) {
            message = error.response.data.errormessage
              || (error.response.data.errors && Object.values(error.response.data.errors)[0] && Object.values(error.response.data.errors)[0][0])
              || message;
          }
          if (Vue.$toast) {
            Vue.$toast.error(message);
          }
        }).finally(function () {
          self.saving = false;
        });
      },
      exportQuery: function () {
        return '?client_name=' + encodeURIComponent(this.filter_client_name || '')
          + '&mobile=' + encodeURIComponent(this.filter_client_mobile || '')
          + '&status=' + encodeURIComponent(this.filter_status || 'all');
      },
      exportToPDF: function () {
        window.open("{{ route('client-report.export-to-pdf') }}" + this.exportQuery(), '_self');
      },
      exportToExcel: function () {
        window.open("{{ route('client-report.export-to-excel') }}" + this.exportQuery(), '_self');
      }
    },
    mounted: function () {
      this.getClientList(1);
    }
  });
</script>
@endpush
