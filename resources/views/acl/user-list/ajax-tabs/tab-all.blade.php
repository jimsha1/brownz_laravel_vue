<style>
  #vue-list-tab-all .acl-user-table th {
    white-space: nowrap;
    vertical-align: middle;
  }

  #vue-list-tab-all .acl-user-table td {
    vertical-align: middle;
  }

  #vue-list-tab-all .user-identity-name {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
    text-decoration: none;
  }

  #vue-list-tab-all .user-identity-name:hover {
    color: #52A8D9;
    text-decoration: underline;
  }

  #vue-list-tab-all .user-identity-username {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  #vue-list-tab-all .user-contact {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #1D1D1D;
    white-space: nowrap;
  }

  #vue-list-tab-all .user-contact i {
    color: #9a9a9a;
    width: 14px;
    text-align: center;
  }

  #vue-list-tab-all .user-contact a {
    color: inherit;
    text-decoration: none;
  }

  #vue-list-tab-all .user-contact a:hover {
    color: #52A8D9;
    text-decoration: underline;
  }

  #vue-list-tab-all .role-chip {
    display: inline-block;
    font-size: 11px;
    font-weight: 500;
    color: #1D1D1D;
    background: #F0F0F3;
    border-radius: 999px;
    padding: 4px 10px;
    margin: 2px 4px 2px 0;
    text-transform: none;
  }

  #vue-list-tab-all .status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    line-height: 1.2;
  }

  #vue-list-tab-all .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  #vue-list-tab-all .status-pill.is-active:hover {
    color: #fff;
    background: #199900;
  }

  #vue-list-tab-all .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  #vue-list-tab-all .status-pill.is-inactive:hover {
    color: #fff;
    background: #D0342C;
  }

  #vue-list-tab-all .status-pill.is-pending {
    color: #8a6d00;
    background: rgba(230, 205, 127, 0.45);
    cursor: default;
  }

  #vue-list-tab-all .manage-actions {
    display: flex;
    align-items: center;
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

  #vue-list-tab-all .manage-btn.is-danger:hover {
    background: #D0342C;
    color: #fff;
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
      <table class="table brownz-table-list-v1 acl-user-table">
        <thead>
          <tr>
            <th style="width: 56px;">{{ __('SL#') }}</th>
            <th>{{ __('User') }}</th>
            <th>{{ __('Mobile') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Roles') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="hidden-print no-sort text-center" style="width: 100px;">{{ __('Manage') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in paginator.db_rows" :key="item.id">
            <td>@{{ index + paginator.sequence_start }}</td>
            <td>
              <a class="user-identity-name" v-bind:href="item.edit_url">@{{ item.name || '-' }}</a>
              <span class="user-identity-username">@{{ item.username || '-' }}</span>
            </td>
            <td>
              <div class="user-contact" v-if="item.mobile">
                <i class="fa fa-phone" aria-hidden="true"></i>
                <a :href="'tel:' + item.mobile">@{{ item.mobile }}</a>
              </div>
              <span v-else class="text-muted">—</span>
            </td>
            <td>
              <div class="user-contact" v-if="item.email">
                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                <a :href="'mailto:' + item.email">@{{ item.email }}</a>
              </div>
              <span v-else class="text-muted">—</span>
            </td>
            <td>
              <template v-if="item.role_names && item.role_names.length">
                <span v-for="name in item.role_names" :key="name" class="role-chip">@{{ name }}</span>
              </template>
              <span v-else class="text-muted">—</span>
            </td>
            <td>
              <a v-if="item.active_action"
                 href="javascript:void(0)"
                 class="status-pill is-active"
                 :title="'{{ __('Click to deactivate') }}'"
                 @click.prevent="runAction(item.active_action)">
                <i class="fa fa-check-circle"></i> {{ __('Active') }}
              </a>
              <a v-if="item.inactive_action"
                 href="javascript:void(0)"
                 class="status-pill is-inactive"
                 :title="'{{ __('Click to activate') }}'"
                 @click.prevent="runAction(item.inactive_action)">
                <i class="fa fa-ban"></i> {{ __('Inactive') }}
              </a>
              <span v-if="item.pending_action" class="status-pill is-pending" title="{{ __('Pending approval') }}">
                <i class="fa fa-clock-o"></i> {{ __('Pending') }}
              </span>
            </td>
            <td class="hidden-print text-center">
              <div class="manage-actions justify-content-center">
                <a class="manage-btn" v-bind:href="item.edit_url" title="{{ __('Edit user') }}">
                  <i class="fa fa-pencil" aria-hidden="true"></i>
                </a>
                <a v-if="item.can_delete"
                   class="manage-btn is-danger"
                   href="javascript:void(0)"
                   title="{{ __('Delete user') }}"
                   @click.prevent="runAction(item.delete_url)">
                  <i class="fa fa-trash" aria-hidden="true"></i>
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
      <div><strong>{{ __('No users found') }}</strong></div>
      <div>{{ __('Try adjusting search, role, or status filters.') }}</div>
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
      runAction: function (action) {
        if (!action) {
          return;
        }
        // Controllers pass JS snippets like: changestatus(1,0) / delete_user(1)
        // eslint-disable-next-line no-eval
        eval(action);
      }
    }
  });
});
</script>
