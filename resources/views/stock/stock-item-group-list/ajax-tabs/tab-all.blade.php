<style>
  #vue-list-tab-all .group-table th,
  #vue-list-tab-all .group-table td {
    vertical-align: middle;
  }

  #vue-list-tab-all .group-name {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
    text-decoration: none;
  }

  #vue-list-tab-all .group-name:hover {
    color: #52A8D9;
    text-decoration: underline;
  }

  #vue-list-tab-all .group-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  #vue-list-tab-all .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
  }

  #vue-list-tab-all .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  #vue-list-tab-all .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  #vue-list-tab-all .manage-actions {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
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
      <table class="table brownz-table-list-v1 group-table">
        <thead>
          <tr>
            <th style="width: 56px;">{{ __('SL#') }}</th>
            <th>{{ __('Group') }}</th>
            <th>{{ __('Show On Website') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="hidden-print no-sort text-center" style="width: 110px;">{{ __('Manage') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in paginator.db_rows" :key="item.item_group_id || index">
            <td>@{{ index + paginator.sequence_start }}</td>
            <td>
              <a class="group-name" v-bind:href="item.view_url">@{{ item.group_name || '—' }}</a>
              <span class="group-meta" v-if="item.parent && item.parent.group_name">
                {{ __('Parent') }}: @{{ item.parent.group_name }}
              </span>
              <span class="group-meta" v-else>{{ __('Top-level group') }}</span>
            </td>
            <td>
              <span class="status-pill" :class="Number(item.is_show_website) === 1 ? 'is-active' : 'is-inactive'">
                <span v-if="Number(item.is_show_website) === 1">{{ __('Yes') }}</span>
                <span v-else>{{ __('No') }}</span>
              </span>
            </td>
            <td>
              <span class="status-pill" :class="Number(item.is_active) === 1 ? 'is-active' : 'is-inactive'">
                <span v-if="Number(item.is_active) === 1">{{ __('Active') }}</span>
                <span v-else>{{ __('Inactive') }}</span>
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
      <div><i class="fa fa-folder-open-o" aria-hidden="true"></i></div>
      <div><strong>{{ __('No stock item groups found') }}</strong></div>
      <div>{{ __('Try adjusting search or status filters.') }}</div>
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
