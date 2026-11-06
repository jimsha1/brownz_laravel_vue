<style>
  #vue-list-tab-all .account-table th,
  #vue-list-tab-all .account-table td {
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

  #vue-list-tab-all .account-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
    text-transform: capitalize;
  }

  #vue-list-tab-all .status-pill,
  #vue-list-tab-all .balance-pill,
  #vue-list-tab-all .system-pill {
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

  #vue-list-tab-all .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  #vue-list-tab-all .balance-pill {
    color: #1D1D1D;
    background: #F0F0F3;
    font-variant-numeric: tabular-nums;
  }

  #vue-list-tab-all .system-pill.is-system {
    color: #8A6D00;
    background: rgba(230, 205, 127, 0.35);
  }

  #vue-list-tab-all .system-pill.is-custom {
    color: #6c757d;
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
</style>

<div id="vue-list-tab-all" class="m-n1">
  <template v-if="paginator.db_rows.length">
    <div class="table-responsive">
      <table class="table brownz-table-list-v1 account-table">
        <thead>
          <tr>
            <th style="width: 56px;">{{ __('SL#') }}</th>
            <th>{{ __('Account') }}</th>
            <th>{{ __('Code') }}</th>
            <th>{{ __('Category') }}</th>
            <th>{{ __('Balance') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('System') }}</th>
            <th class="hidden-print no-sort text-center" style="width: 110px;">{{ __('Manage') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in paginator.db_rows" :key="item.account_id || index">
            <td>@{{ index + paginator.sequence_start }}</td>
            <td>
              <a class="account-name" v-bind:href="item.view_url">@{{ item.name || '—' }}</a>
              <span class="account-meta" v-if="item.type">@{{ item.type }}</span>
            </td>
            <td>@{{ item.code || '—' }}</td>
            <td>@{{ item.category || '—' }}</td>
            <td>
              <span class="balance-pill">
                @{{ Number(item.balance || 0).toFixed(2) }}
              </span>
            </td>
            <td>
              <span class="status-pill" :class="Number(item.is_active) === 1 ? 'is-active' : 'is-inactive'">
                <span v-if="Number(item.is_active) === 1">{{ __('Active') }}</span>
                <span v-else>{{ __('Inactive') }}</span>
              </span>
            </td>
            <td>
              <span class="system-pill" :class="Number(item.is_system_defined) === 1 ? 'is-system' : 'is-custom'">
                <span v-if="Number(item.is_system_defined) === 1">{{ __('Yes') }}</span>
                <span v-else>{{ __('No') }}</span>
              </span>
            </td>
            <td class="hidden-print text-center">
              <div class="manage-actions">
                <a class="manage-btn" v-bind:href="item.view_url" title="{{ __('View') }}">
                  <i class="fa fa-eye" aria-hidden="true"></i>
                </a>
                <a v-if="Number(item.is_system_defined) !== 1" class="manage-btn" v-bind:href="item.edit_url" title="{{ __('Edit') }}">
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
      <div><i class="fa fa-book" aria-hidden="true"></i></div>
      <div><strong>{{ __('No accounts found') }}</strong></div>
      <div>{{ __('Try adjusting type, code, category, or status filters.') }}</div>
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
