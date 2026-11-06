<style>
  #vue-list-tab-all .voucher-table th,
  #vue-list-tab-all .voucher-table td {
    vertical-align: middle;
  }

  #vue-list-tab-all .voucher-no {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
    text-decoration: none;
  }

  #vue-list-tab-all .voucher-no:hover {
    color: #52A8D9;
    text-decoration: underline;
  }

  #vue-list-tab-all .type-pill,
  #vue-list-tab-all .booking-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    text-transform: capitalize;
  }

  #vue-list-tab-all .type-pill {
    color: #1D1D1D;
    background: #F0F0F3;
  }

  #vue-list-tab-all .booking-pill {
    color: #9A6700;
    background: rgba(230, 205, 127, 0.35);
    margin-left: 6px;
  }

  #vue-list-tab-all .date-text {
    white-space: nowrap;
    color: #4a4a4a;
  }

  #vue-list-tab-all .narration-text {
    display: block;
    max-width: 280px;
    color: #4a4a4a;
    font-size: 13px;
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  #vue-list-tab-all .amount-cell {
    font-variant-numeric: tabular-nums;
    font-weight: 600;
    white-space: nowrap;
    color: #1D1D1D;
  }

  #vue-list-tab-all .source-pill {
    display: inline-flex;
    align-items: center;
    font-size: 11px;
    font-weight: 600;
    border-radius: 999px;
    padding: 4px 10px;
    margin-top: 4px;
    color: #1D1D1D;
    background: rgba(82, 168, 217, 0.15);
    line-height: 1.2;
  }

  #vue-list-tab-all .created-by-text {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
    font-size: 13px;
  }

  #vue-list-tab-all .created-at-text {
    display: block;
    font-size: 11px;
    color: #6c757d;
    margin-top: 2px;
  }

  #vue-list-tab-all .manage-actions {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    flex-wrap: nowrap;
  }

  #vue-list-tab-all .manage-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    border: none;
    background: #F0F0F3;
    color: #1D1D1D;
    text-decoration: none !important;
    flex: 0 0 auto;
    cursor: pointer;
  }

  #vue-list-tab-all .manage-btn:hover {
    background: #1D1D1D;
    color: #E6CD7F;
  }

  #vue-list-tab-all .manage-btn.is-disabled {
    opacity: 0.45;
    cursor: not-allowed;
  }

  #vue-list-tab-all .password-inline {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 260px;
  }

  #vue-list-tab-all .password-inline .form-control {
    height: 34px;
    border-radius: 999px;
    background: #F0F0F3;
    border: none;
    box-shadow: none;
  }

  #vue-list-tab-all .empty-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }

  #vue-list-tab-all .empty-state i {
    font-size: 28px;
    margin-bottom: 10px;
    color: #b7b7b7;
  }

  #vue-list-tab-all .tab-pagination {
    margin-top: 16px;
  }

  #vue-list-tab-all .password-error {
    color: #D0342C;
    font-size: 12px;
    margin-top: 4px;
  }
</style>

<div id="vue-list-tab-all" class="m-n1">
  <template v-if="paginator.db_rows.length">
    <div class="table-responsive">
      <table class="table brownz-table-list-v1 voucher-table">
        <thead>
          <tr>
            <th style="width: 56px;">{{ __('SL#') }}</th>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Voucher') }}</th>
            <th>{{ __('Type') }}</th>
            <th>{{ __('Narration') }}</th>
            <th class="text-right">{{ __('Amount') }}</th>
            <th>{{ __('Created By') }}</th>
            <th class="hidden-print no-sort text-center" style="width: 160px;">{{ __('Manage') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in paginator.db_rows" :key="item.id || index">
            <td>@{{ index + paginator.sequence_start }}</td>
            <td>
              <span class="date-text">@{{ item.date_display || formatDate(item.date) }}</span>
            </td>
            <td>
              <a class="voucher-no" v-bind:href="item.view_url">
                @{{ item.voucher_no || ('#' + item.id) }}
              </a>
              <span v-if="item.is_booking_voucher" class="booking-pill">{{ __('Booking') }}</span>
            </td>
            <td>
              <span class="type-pill">@{{ item.voucher_type || '—' }}</span>
            </td>
            <td>
              <span class="narration-text" :title="item.description || ''">
                @{{ item.description_display || '—' }}
              </span>
              <span v-if="item.reference_label" class="source-pill">@{{ item.reference_label }}</span>
            </td>
            <td class="text-right">
              <span class="amount-cell">@{{ formatAmount(item.total_amount) }}</span>
            </td>
            <td>
              <span class="created-by-text">@{{ item.created_by_name || '—' }}</span>
              <span class="created-at-text">@{{ dt_format(item.created_at) }}</span>
            </td>
            <td class="hidden-print text-center">
              <div v-if="passwordRowId === item.id" class="password-inline">
                <input type="password"
                  v-model="passwordInput"
                  placeholder="{{ __('Password') }}"
                  class="form-control form-control-sm"
                  autocomplete="current-password"
                  v-on:keyup.enter="submitPassword(item)">
                <button type="button" class="manage-btn" title="{{ __('Confirm') }}"
                  v-on:click="submitPassword(item)" :disabled="passwordSubmitting">
                  <i class="fa fa-check" aria-hidden="true"></i>
                </button>
                <button type="button" class="manage-btn" title="{{ __('Cancel') }}"
                  v-on:click="cancelPasswordInput">
                  <i class="fa fa-times" aria-hidden="true"></i>
                </button>
              </div>
              <div v-else class="manage-actions">
                <a class="manage-btn" v-bind:href="item.view_url" title="{{ __('View') }}">
                  <i class="fa fa-eye" aria-hidden="true"></i>
                </a>
                <button v-if="!item.is_booking_voucher && item.can_edit"
                  type="button"
                  class="manage-btn"
                  title="{{ __('Edit (password required)') }}"
                  v-on:click="showPasswordInput(item.id)">
                  <i class="fa fa-pencil" aria-hidden="true"></i>
                </button>
                <button v-else
                  type="button"
                  class="manage-btn is-disabled"
                  :title="item.is_booking_voucher ? '{{ __('Booking vouchers cannot be edited') }}' : '{{ __('No edit permission') }}'"
                  disabled>
                  <i class="fa fa-lock" aria-hidden="true"></i>
                </button>
              </div>
              <div class="password-error" v-if="passwordRowId === item.id && passwordError">
                @{{ passwordError }}
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="tab-pagination" v-html="paginator.links"></div>
  </template>

  <template v-else>
    <div class="empty-state">
      <div><i class="fa fa-file-text-o" aria-hidden="true"></i></div>
      <div><strong>{{ __('No vouchers found') }}</strong></div>
      <div>{{ __('Try adjusting search, type, or date filters.') }}</div>
    </div>
  </template>
</div>

<script>
  var vc_moment_date_offset = new Date("2021-10-05T17:28:50.403+03:00").getTime() - Date.now();
  var vc_jscalendar_moment_format = "DD/MM/YYYY";
</script>
<script>
  var vue_list_tab_all = null;
  jQuery(function ($) {
    vue_list_tab_all = new Vue({
      el: "#vue-list-tab-all",
      data: function () {
        return Object.assign({
          passwordInput: '',
          passwordRowId: null,
          passwordError: '',
          passwordSubmitting: false,
        }, {!! json_encode($db_results['vue_data']['vue_list_tab_all']) !!});
      },
      methods: {
        formatDate: function (val) {
          if (!val) return '—';
          if (window.moment) {
            return moment(val).format('DD MMM YYYY');
          }
          return val;
        },
        formatAmount: function (val) {
          var amount = parseFloat(val);
          if (isNaN(amount)) {
            return '—';
          }
          return amount.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          });
        },
        dt_format: function (val) {
          if (!val) return '—';
          if (window.moment) {
            return moment(val).format('DD-MMM-YYYY hh:mm A');
          }
          return val;
        },
        showPasswordInput: function (rowId) {
          this.passwordInput = '';
          this.passwordError = '';
          this.passwordRowId = rowId;
        },
        cancelPasswordInput: function () {
          this.passwordInput = '';
          this.passwordError = '';
          this.passwordRowId = null;
          this.passwordSubmitting = false;
        },
        submitPassword: function (item) {
          if (!this.passwordInput) {
            this.passwordError = 'Password is required.';
            return;
          }

          this.passwordSubmitting = true;
          this.passwordError = '';

          $.ajax({
            url: "{{ route('voucher.auth-check') }}",
            method: 'POST',
            data: {
              password: this.passwordInput,
              voucher_id: item.id,
              _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
              if (response.valid) {
                this.cancelPasswordInput();
                window.location.href = item.edit_url;
              } else {
                this.passwordError = response.statusText || 'Invalid password.';
                this.passwordInput = '';
              }
            },
            error: (xhr) => {
              const msg = (xhr.responseJSON && xhr.responseJSON.statusText)
                ? xhr.responseJSON.statusText
                : 'Unable to verify password.';
              this.passwordError = msg;
              this.passwordInput = '';
            },
            complete: () => {
              this.passwordSubmitting = false;
            }
          });
        }
      }
    });
  });
</script>
