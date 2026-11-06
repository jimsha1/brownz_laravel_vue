<style>
  #vue-list-tab-all .expense-table th,
  #vue-list-tab-all .expense-table td {
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

  #vue-list-tab-all .type-pill,
  #vue-list-tab-all .amount-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    text-transform: capitalize;
  }

  #vue-list-tab-all .type-pill.is-cash {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  #vue-list-tab-all .type-pill.is-bank {
    color: #1e6789;
    background: rgba(82, 168, 217, 0.18);
  }

  #vue-list-tab-all .type-pill.is-other {
    color: #6c757d;
    background: #F0F0F3;
  }

  #vue-list-tab-all .amount-pill {
    justify-content: flex-end;
    min-width: 96px;
    font-variant-numeric: tabular-nums;
    color: #1D1D1D;
    background: #F0F0F3;
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

  #vue-list-tab-all .date-text {
    white-space: nowrap;
    color: #4a4a4a;
  }
</style>

<div id="vue-list-tab-all" class="m-n1">
  <template v-if="paginator.db_rows.length">
    <div class="table-responsive">
      <table class="table brownz-table-list-v1 expense-table">
        <thead>
          <tr>
            <th style="width: 56px;">{{ __('SL#') }}</th>
            <th>{{ __('Account Name') }}</th>
            <th>{{ __('Account Type') }}</th>
            <th class="text-right">{{ __('Amount') }}</th>
            <th>{{ __('Expense Date') }}</th>
            <th class="hidden-print no-sort text-center" style="width: 110px;">{{ __('Manage') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in paginator.db_rows" :key="item.expense_id || index">
            <td>@{{ index + paginator.sequence_start }}</td>
            <td>
              <a class="account-name" v-bind:href="item.view_url || item.edit_url">
                @{{ item.account_name || (item.account && item.account.name) || '—' }}
              </a>
            </td>
            <td>
              <span class="type-pill"
                :class="{
                  'is-cash': (item.account_type || '').toLowerCase() === 'cash',
                  'is-bank': (item.account_type || '').toLowerCase() === 'bank',
                  'is-other': !['cash', 'bank'].includes((item.account_type || '').toLowerCase())
                }">
                @{{ item.account_type_label || item.account_type || '—' }}
              </span>
            </td>
            <td class="text-right">
              <span class="amount-pill">@{{ item.amount_display || item.amount || '0.00' }}</span>
            </td>
            <td>
              <span class="date-text">@{{ item.expense_date_display || '—' }}</span>
            </td>
            <td class="hidden-print text-center">
              <div class="manage-actions">
                <a class="manage-btn" v-bind:href="item.view_url" title="{{ __('View') }}">
                  <i class="fa fa-eye" aria-hidden="true"></i>
                </a>
                <a class="manage-btn" v-bind:href="item.edit_url" title="{{ __('Edit') }}">
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
      <div><i class="fa fa-money" aria-hidden="true"></i></div>
      <div><strong>{{ __('No expenses found') }}</strong></div>
      <div>{{ __('Try adjusting the date range, account, type, or search.') }}</div>
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
