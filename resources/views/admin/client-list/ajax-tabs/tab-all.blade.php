<style>
  #vue-list-tab-all .client-table th {
    white-space: nowrap;
    vertical-align: middle;
  }

  #vue-list-tab-all .client-table td {
    vertical-align: middle;
  }

  #vue-list-tab-all .client-name {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
    text-decoration: none;
  }

  #vue-list-tab-all .client-name:hover {
    color: #52A8D9;
    text-decoration: underline;
  }

  #vue-list-tab-all .client-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
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
  }

  #vue-list-tab-all .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  #vue-list-tab-all .status-pill.is-inactive,
  #vue-list-tab-all .status-pill.is-blocked,
  #vue-list-tab-all .status-pill.is-deleted {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  #vue-list-tab-all .status-pill.is-pending {
    color: #9A6700;
    background: rgba(230, 205, 127, 0.35);
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
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
    flex-wrap: wrap;
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
  }

  #vue-list-tab-all .manage-btn:hover {
    background: #1D1D1D;
    color: #E6CD7F;
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

  #vue-list-tab-all .manage-btn.is-pay:hover {
    background: #333;
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
</style>

<div id="vue-list-tab-all" class="m-n1">
  <template v-if="paginator.db_rows.length">
    <div class="table-responsive">
      <table class="table brownz-table-list-v1 client-table">
        <thead>
          <tr>
            <th style="width: 56px;">{{ __('SL#') }}</th>
            <th>{{ __('Client') }}</th>
            <th>{{ __('Mobile') }}</th>
            <th>{{ __('Address') }}</th>
            <th>{{ __('Gender') }}</th>
            <th>{{ __('Balance') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="hidden-print no-sort text-center" style="width: 140px;">{{ __('Manage') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in paginator.db_rows" :key="item.client_id || index">
            <td>@{{ index + paginator.sequence_start }}</td>
            <td>
              <a class="client-name" v-bind:href="item.view_url">@{{ item.name || '—' }}</a>
              <span class="client-meta" v-if="item.email">@{{ item.email }}</span>
            </td>
            <td>@{{ item.mobile || '—' }}</td>
            <td>@{{ item.address || '—' }}</td>
            <td>@{{ item.gender | vc_ucwords }}</td>
            <td>
              <span class="balance-pill" :class="item.has_credit ? 'is-credit' : 'is-clear'">
                @{{ Number(item.balance || 0).toFixed(2) }}
              </span>
            </td>
            <td>
              <span class="status-pill" :class="'is-' + (item.user_status || 'inactive')">
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
                <a v-if="item.has_credit" class="manage-btn is-pay" v-bind:href="item.payment_url" title="{{ __('Make Payment') }}">
                  {{ __('Pay') }}
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
      <div><i class="fa fa-users" aria-hidden="true"></i></div>
      <div><strong>{{ __('No clients found') }}</strong></div>
      <div>{{ __('Try adjusting search, status, or credit filters.') }}</div>
    </div>
  </template>
</div>

<script>
var vue_list_tab_all = null;
jQuery(function ($) {
  vue_list_tab_all = new Vue({
    el: "#vue-list-tab-all",
    data: <?php echo json_encode($db_results['vue_data']['vue_list_tab_all']); ?>
  });
});
</script>
