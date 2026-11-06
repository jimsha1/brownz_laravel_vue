<style>
  #vue-list-tab-all .open-balance-table th,
  #vue-list-tab-all .open-balance-table td {
    vertical-align: middle;
  }

  #vue-list-tab-all .account-name {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
    text-decoration: none;
  }

  #vue-list-tab-all .account-name:hover {
    color: #52A8D9;
    text-decoration: underline;
  }

  #vue-list-tab-all .amount-pill {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    min-width: 96px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    font-variant-numeric: tabular-nums;
    color: #1D1D1D;
    background: #F0F0F3;
  }

  #vue-list-tab-all .date-text {
    white-space: nowrap;
    color: #4a4a4a;
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
</style>

<div id="vue-list-tab-all" class="m-n1">
  <template v-if="paginator.db_rows.length">
    <div class="table-responsive">
      <table class="table brownz-table-list-v1 open-balance-table">
        <thead>
          <tr>
            <th style="width: 56px;">{{ __('SL#') }}</th>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Account Name') }}</th>
            <th class="text-right">{{ __('Amount') }}</th>
            <th class="hidden-print no-sort text-center" style="width: 110px;">{{ __('Manage') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in paginator.db_rows" :key="item.open_balance_id || index">
            <td>@{{ index + paginator.sequence_start }}</td>
            <td>
              <span class="date-text">@{{ item.opening_date_display || formatDate(item.opening_date) || '—' }}</span>
            </td>
            <td>
              <a class="account-name" v-bind:href="item.view_url || item.edit_url">
                @{{ item.account_name || (item.account && item.account.name) || '—' }}
              </a>
            </td>
            <td class="text-right">
              <span class="amount-pill">@{{ item.amount_display || item.amount || '0.00' }}</span>
            </td>
            <td class="hidden-print text-center">
              <div class="manage-actions">
                <a class="manage-btn" v-bind:href="item.view_url" title="{{ __('View') }}">
                  <i class="fa fa-eye" aria-hidden="true"></i>
                </a>
                <a v-if="item.can_edit" class="manage-btn" v-bind:href="item.edit_url" title="{{ __('Edit') }}">
                  <i class="fa fa-pencil" aria-hidden="true"></i>
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
      <div><i class="fa fa-inbox" aria-hidden="true"></i></div>
      <div><strong>{{ __('No opening balances found') }}</strong></div>
      <div>{{ __('Try adjusting the date range or account search.') }}</div>
    </div>
  </template>
</div>

<script>
var vue_list_tab_all = null;
jQuery(function ($) {
  vue_list_tab_all = new Vue({
    el: "#vue-list-tab-all",
    data: <?php echo json_encode($db_results['vue_data']['vue_list_tab_all']); ?>,
    methods: {
      formatDate: function (date) {
        if (!date) return '';
        var d = new Date(date);
        if (isNaN(d.getTime())) return date;
        var day = String(d.getDate()).padStart(2, '0');
        var month = String(d.getMonth() + 1).padStart(2, '0');
        var year = d.getFullYear();
        return day + '-' + month + '-' + year;
      }
    }
  });
});
</script>
