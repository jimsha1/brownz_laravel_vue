<style>
  #vue-list-tab-all .category-table th {
    white-space: nowrap;
    vertical-align: middle;
  }

  #vue-list-tab-all .category-table td {
    vertical-align: middle;
  }

  #vue-list-tab-all .category-name {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #1D1D1D;
    text-decoration: none;
  }

  #vue-list-tab-all .category-name:hover {
    color: #52A8D9;
    text-decoration: underline;
  }

  #vue-list-tab-all .category-name.is-parent {
    font-weight: 700;
  }

  #vue-list-tab-all .category-name.is-child {
    font-weight: 500;
    padding-left: 28px;
  }

  #vue-list-tab-all .category-child-marker {
    color: #52A8D9;
    font-size: 13px;
    line-height: 1;
  }

  #vue-list-tab-all tr.category-row-child td {
    background: #FAFBFC;
  }

  #vue-list-tab-all .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
  }

  #vue-list-tab-all .status-pill.is-active,
  #vue-list-tab-all .status-pill.is-yes {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  #vue-list-tab-all .status-pill.is-inactive,
  #vue-list-tab-all .status-pill.is-no {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  #vue-list-tab-all .manage-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
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
      <table class="table brownz-table-list-v1 category-table">
        <thead>
          <tr>
            <th style="width: 56px;">{{ __('SL#') }}</th>
            <th>{{ __('Category') }}</th>
            <th>{{ __('Show On Website') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="hidden-print no-sort text-center" style="width: 100px;">{{ __('Manage') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in paginator.db_rows"
            v-bind:key="item.category_id || index"
            v-bind:class="{ 'category-row-child': item.parent_id }">
            <td>@{{ index + paginator.sequence_start }}</td>
            <td>
              <a class="category-name"
                v-bind:class="{ 'is-parent': !item.parent_id, 'is-child': item.parent_id }"
                v-bind:href="item.edit_url">
                <span class="category-child-marker" v-if="item.parent_id">&#8627;</span>
                <span>@{{ item.name || '—' }}</span>
              </a>
            </td>
            <td>
              <span v-if="Number(item.show_on_website) === 1" class="status-pill is-yes">{{ __('Yes') }}</span>
              <span v-else class="status-pill is-no">{{ __('No') }}</span>
            </td>
            <td>
              <span v-if="Number(item.is_active) === 1" class="status-pill is-active">{{ __('Active') }}</span>
              <span v-else class="status-pill is-inactive">{{ __('Inactive') }}</span>
            </td>
            <td class="hidden-print text-center">
              <div class="manage-actions">
                <a class="manage-btn" v-bind:href="item.view_url" title="{{ __('View category') }}">
                  <i class="fa fa-eye" aria-hidden="true"></i>
                </a>
                <a class="manage-btn" v-bind:href="item.edit_url" title="{{ __('Edit category') }}">
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
      <div><i class="fa fa-th-large" aria-hidden="true"></i></div>
      <div><strong>{{ __('No categories found') }}</strong></div>
      <div>{{ __('Try adjusting search or filters, or create a new category.') }}</div>
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
