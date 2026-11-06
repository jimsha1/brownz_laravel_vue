<style>
  #vue-list-tab-all .beautician-table th {
    white-space: nowrap;
    vertical-align: middle;
  }

  #vue-list-tab-all .beautician-table td {
    vertical-align: middle;
  }

  #vue-list-tab-all .beautician-cell {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  #vue-list-tab-all .beautician-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
    background: #F0F0F3;
    flex-shrink: 0;
  }

  #vue-list-tab-all .beautician-name {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
    text-decoration: none;
  }

  #vue-list-tab-all .beautician-name:hover {
    color: #52A8D9;
    text-decoration: underline;
  }

  #vue-list-tab-all .beautician-code {
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
      <table class="table brownz-table-list-v1 beautician-table">
        <thead>
          <tr>
            <th style="width: 56px;">{{ __('SL#') }}</th>
            <th>{{ __('Beautician') }}</th>
            <th>{{ __('Mobile') }}</th>
            <th>{{ __('Gender') }}</th>
            <th>{{ __('Join Date') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="hidden-print no-sort text-center" style="width: 110px;">{{ __('Manage') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in paginator.db_rows" :key="item.beautician_id || index">
            <td>@{{ index + paginator.sequence_start }}</td>
            <td>
              <div class="beautician-cell">
                <a v-bind:href="item.view_url">
                  <img class="beautician-avatar"
                    v-bind:src="item.photo_src_url || ('/storage/' + item.profile_photo_path)"
                    alt="">
                </a>
                <div>
                  <a class="beautician-name" v-bind:href="item.view_url">@{{ item.name || '—' }}</a>
                  <span class="beautician-code">@{{ item.beautician_code || '—' }}</span>
                </div>
              </div>
            </td>
            <td>@{{ item.mobile || '—' }}</td>
            <td>@{{ item.gender | vc_ucwords }}</td>
            <td>@{{ item.join_date | vc_date_expanded }}</td>
            <td>
              <span class="status-pill" :class="item.is_active ? 'is-active' : 'is-inactive'">
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
      <div><i class="fa fa-user" aria-hidden="true"></i></div>
      <div><strong>{{ __('No beauticians found') }}</strong></div>
      <div>{{ __('Try adjusting search or status filters.') }}</div>
    </div>
  </template>
</div>

<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
var vue_list_tab_all = null;
jQuery(function ($) {
  vue_list_tab_all = new Vue({
    el: "#vue-list-tab-all",
    data: <?php echo json_encode($db_results['vue_data']['vue_list_tab_all']); ?>
  });
});
</script>
