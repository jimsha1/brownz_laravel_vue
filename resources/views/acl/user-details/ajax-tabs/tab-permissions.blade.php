<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-permissions.acl-user-permissions {
    padding: 4px 0 8px;
  }

  #vue-details-tab-permissions .perm-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 14px;
  }

  #vue-details-tab-permissions .perm-toolbar-title {
    margin: 0;
    color: #1D1D1D;
    font-size: 1.05rem;
    font-weight: 600;
  }

  #vue-details-tab-permissions .perm-toolbar-meta {
    margin: 2px 0 0;
    font-size: 12px;
    color: #6c757d;
  }

  #vue-details-tab-permissions .perm-count-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #1D1D1D;
    color: #E6CD7F;
    border-radius: 999px;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 600;
  }

  #vue-details-tab-permissions .super-admin-banner {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    background: rgba(25, 153, 0, 0.12);
    color: #146600;
    border-radius: 14px;
    padding: 16px 18px;
  }

  #vue-details-tab-permissions .super-admin-banner i {
    font-size: 22px;
    margin-top: 2px;
  }

  #vue-details-tab-permissions .super-admin-banner strong {
    display: block;
    margin-bottom: 4px;
    color: #1D1D1D;
  }

  #vue-details-tab-permissions .perm-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
  }

  #vue-details-tab-permissions .perm-group {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 14px 16px 10px;
  }

  #vue-details-tab-permissions .perm-group-title {
    margin: 0 0 10px;
    font-size: 14px;
    font-weight: 700;
    color: #1D1D1D;
    padding-bottom: 8px;
    border-bottom: 1px solid #e4e4ea;
  }

  #vue-details-tab-permissions .perm-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 10px;
    color: #1D1D1D;
    font-size: 14px;
  }

  #vue-details-tab-permissions .perm-item i {
    margin-top: 2px;
  }

  #vue-details-tab-permissions .perm-item.is-granted i {
    color: #199900;
  }

  #vue-details-tab-permissions .perm-item.is-denied {
    color: #6c757d;
  }

  #vue-details-tab-permissions .perm-item.is-denied i {
    color: #D0342C;
  }

  @media (max-width: 991.98px) {
    #vue-details-tab-permissions .perm-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div id="vue-details-tab-permissions" class="has-vue-form acl-user-permissions">
  <div v-if="is_super_admin" class="super-admin-banner">
    <i class="fa fa-check-circle" aria-hidden="true"></i>
    <div>
      <strong>{{ __('Super Admin') }}</strong>
      <div>{{ __('This user has the Super Admin role and full access to the system.') }}</div>
    </div>
  </div>

  <div v-else>
    <div class="perm-toolbar">
      <div>
        <h3 class="perm-toolbar-title">{{ __('Effective Permissions') }}</h3>
        <p class="perm-toolbar-meta">{{ __('Read-only view of permissions granted via roles.') }}</p>
      </div>
      <div class="perm-count-pill">
        <i class="fa fa-check-circle"></i>
        @{{ grantedCount }} {{ __('granted') }}
      </div>
    </div>

    <div class="perm-grid">
      <div class="perm-group" v-for="(groupPermissions, groupName) in db_all_permissions" :key="'group-' + groupName">
        <h4 class="perm-group-title">@{{ formatGroupName(groupName) }}</h4>
        <div class="perm-item"
          v-for="permission in groupPermissions"
          :key="permission.id || permission.name"
          :class="hasPermission(permission.name) ? 'is-granted' : 'is-denied'">
          <i class="fa"
            :class="hasPermission(permission.name) ? 'fa-check-circle' : 'fa-times-circle'"
            aria-hidden="true"></i>
          <span>@{{ permission.description || permission.name }}</span>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
var vue_details_tab_permissions = null;
jQuery(function ($) {
  vue_details_tab_permissions = new Vue({
    el: "#vue-details-tab-permissions",
    data: function () {
      return @json($details_permissions_vue);
    },
    computed: {
      grantedCount: function () {
        return (this.assigned_permissions || []).length;
      }
    },
    methods: {
      formatGroupName: function (group) {
        return String(group || '')
          .replace(/_/g, ' ')
          .replace(/\b\w/g, function (char) {
            return char.toUpperCase();
          });
      },
      hasPermission: function (name) {
        return (this.assigned_permissions || []).indexOf(name) !== -1;
      }
    }
  });
});
</script>
