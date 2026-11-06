<style>
  #vue-list-tab-all .list-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 14px;
  }

  #vue-list-tab-all .list-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  #vue-list-tab-all .stat-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 999px;
    background: #F7F7F9;
    color: #1D1D1D;
    font-size: 12px;
    font-weight: 600;
  }

  #vue-list-tab-all .stat-chip.is-credit {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.1);
  }

  #vue-list-tab-all .active-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
  }

  #vue-list-tab-all .filter-chip {
    display: inline-flex;
    align-items: center;
    padding: 5px 10px;
    border-radius: 999px;
    background: #fff;
    border: 1px solid #e0e0e5;
    color: #4a4a4a;
    font-size: 11px;
    font-weight: 600;
  }

  #vue-list-tab-all .vendor-table th,
  #vue-list-tab-all .vendor-table td {
    vertical-align: middle;
  }

  #vue-list-tab-all .vendor-name {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
    text-decoration: none;
  }

  #vue-list-tab-all .vendor-name:hover {
    color: #52A8D9;
    text-decoration: underline;
  }

  #vue-list-tab-all .vendor-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  #vue-list-tab-all .vendor-meta.is-muted {
    color: #9aa0a6;
    font-style: italic;
  }

  #vue-list-tab-all .gst-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 8px;
    background: #fff;
    border: 1px solid #e0e0e5;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 11px;
    letter-spacing: 0.04em;
    color: #1D1D1D;
    white-space: nowrap;
  }

  #vue-list-tab-all .address-text {
    display: block;
    max-width: 220px;
    color: #4a4a4a;
    font-size: 13px;
    line-height: 1.4;
  }

  #vue-list-tab-all .status-pill,
  #vue-list-tab-all .balance-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    text-transform: capitalize;
    font-variant-numeric: tabular-nums;
  }

  #vue-list-tab-all .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  #vue-list-tab-all .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  #vue-list-tab-all .balance-pill.is-credit {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  #vue-list-tab-all .balance-pill.is-clear {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
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
    background: #F0F0F3;
    color: #1D1D1D;
    text-decoration: none !important;
    flex: 0 0 auto;
  }

  #vue-list-tab-all .manage-btn.is-pay {
    width: auto;
    padding: 0 12px;
    border-radius: 999px;
    background: #1D1D1D;
    color: #E6CD7F;
    font-size: 12px;
    font-weight: 600;
  }

  #vue-list-tab-all .manage-btn:hover {
    background: #1D1D1D;
    color: #E6CD7F;
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

  @media (max-width: 991px) {
    #vue-list-tab-all .address-text {
      max-width: 160px;
    }
  }
</style>

<div id="vue-list-tab-all" class="m-n1">
  <template v-if="paginator.db_rows.length">
    <div class="list-toolbar">
      <div class="list-stats">
        <span class="stat-chip">
          <i class="fa fa-users" aria-hidden="true"></i>
          @{{ summary.total }} {{ __('vendors') }}
        </span>
        <span class="stat-chip is-credit" v-if="summary.with_credit_count > 0">
          <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
          @{{ summary.with_credit_count }} {{ __('with balance') }} · @{{ Number(summary.total_credit || 0).toFixed(2) }}
        </span>
        <span class="stat-chip" v-if="paginator.total > paginator.count">
          {{ __('Showing') }} @{{ paginator.sequence_start }}–@{{ paginator.sequence_start + paginator.count - 1 }} {{ __('of') }} @{{ paginator.total }}
        </span>
      </div>

      <div class="active-filters" v-if="activeFilterChips.length">
        <span class="filter-chip" v-for="chip in activeFilterChips" :key="chip">@{{ chip }}</span>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table brownz-table-list-v1 vendor-table">
        <thead>
          <tr>
            <th style="width: 56px;">{{ __('SL#') }}</th>
            <th>{{ __('Vendor') }}</th>
            <th>{{ __('Phone') }}</th>
            <th class="d-none d-lg-table-cell">{{ __('GST Number') }}</th>
            <th class="d-none d-xl-table-cell">{{ __('Address') }}</th>
            <th class="text-right">{{ __('Balance') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="hidden-print no-sort text-center" style="width: 140px;">{{ __('Manage') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in paginator.db_rows" :key="item.vendor_id || index">
            <td>@{{ index + paginator.sequence_start }}</td>
            <td>
              <a class="vendor-name" v-bind:href="item.view_url">@{{ item.name || '—' }}</a>
              <span class="vendor-meta" v-if="item.email">@{{ item.email }}</span>
              <span class="vendor-meta is-muted" v-else>{{ __('No email') }}</span>
              <span class="vendor-meta d-lg-none" v-if="item.gst_number">GST: @{{ item.gst_number }}</span>
            </td>
            <td>@{{ item.phone || '—' }}</td>
            <td class="d-none d-lg-table-cell">
              <span v-if="item.gst_number" class="gst-badge">@{{ item.gst_number }}</span>
              <span v-else class="text-muted">—</span>
            </td>
            <td class="d-none d-xl-table-cell">
              <span class="address-text" v-if="item.address_short">@{{ item.address_short }}</span>
              <span v-else class="text-muted">—</span>
            </td>
            <td class="text-right">
              <span class="balance-pill" :class="item.has_credit ? 'is-credit' : 'is-clear'">
                @{{ Number(item.balance || 0).toFixed(2) }}
              </span>
            </td>
            <td>
              <span class="status-pill" :class="Number(item.is_active) === 1 ? 'is-active' : 'is-inactive'">
                @{{ item.status_label }}
              </span>
            </td>
            <td class="hidden-print text-center">
              <div class="manage-actions">
                <a class="manage-btn" v-bind:href="item.view_url" title="{{ __('View') }}">
                  <i class="fa fa-eye" aria-hidden="true"></i>
                </a>
                <a class="manage-btn" v-bind:href="item.edit_url" title="{{ __('Edit') }}">
                  <i class="fa fa-pencil" aria-hidden="true"></i>
                </a>
                <a v-if="item.has_credit" class="manage-btn is-pay" v-bind:href="item.payment_url" title="{{ __('Pay') }}">
                  {{ __('Pay') }}
                </a>
                <a v-if="item.has_credit" class="manage-btn" v-bind:href="item.voucher_pay_url" title="{{ __('Pay via Voucher') }}">
                  <i class="fa fa-file-text-o" aria-hidden="true"></i>
                </a>
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
      <div><i class="fa fa-truck" aria-hidden="true"></i></div>
      <div><strong>{{ __('No vendors found') }}</strong></div>
      <div>{{ __('Try adjusting search, status, or balance filters.') }}</div>
    </div>
  </template>
</div>

<script>
var vue_list_tab_all = null;
jQuery(function ($) {
  vue_list_tab_all = new Vue({
    el: "#vue-list-tab-all",
    data: <?php echo json_encode($db_results['vue_data']['vue_list_tab_all']); ?>,
    computed: {
      activeFilterChips: function () {
        var chips = [];
        var filters = this.filters || {};

        if (filters.search) {
          chips.push('{{ __('Search') }}: ' + filters.search);
        }

        if (filters.status && filters.status !== 'active') {
          var statusLabel = filters.status === 'all'
            ? '{{ __('All Status') }}'
            : (filters.status === 'inactive' ? '{{ __('Inactive') }}' : '{{ __('Active') }}');
          chips.push('{{ __('Status') }}: ' + statusLabel);
        }

        if (filters.credit_balance === 'yes') {
          chips.push('{{ __('With Outstanding Balance') }}');
        }

        return chips;
      }
    }
  });
});
</script>
