<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-permissions.acl-role-permissions {
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

  #vue-details-tab-permissions .perm-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 16px;
  }

  #vue-details-tab-permissions .perm-group {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 14px 16px 8px;
  }

  #vue-details-tab-permissions .perm-group-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e4e4ea;
  }

  #vue-details-tab-permissions .perm-group-title {
    margin: 0;
    font-size: 14px;
    font-weight: 700;
    color: #1D1D1D;
    text-transform: capitalize;
  }

  #vue-details-tab-permissions .perm-group-actions {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  #vue-details-tab-permissions .perm-group-count {
    font-size: 12px;
    color: #6c757d;
    font-weight: 600;
  }

  #vue-details-tab-permissions .perm-select-all {
    border: none;
    background: #EDEDF1;
    color: #1D1D1D;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
  }

  #vue-details-tab-permissions .perm-select-all:hover {
    background: #1D1D1D;
    color: #E6CD7F;
  }

  #vue-details-tab-permissions .brw-checkbox-browz-dds {
    font-size: 14px;
    margin-bottom: 10px;
  }

  #vue-details-tab-permissions .vc-form-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  @media (max-width: 991.98px) {
    #vue-details-tab-permissions .perm-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div id="vue-details-tab-permissions" class="has-vue-form acl-role-permissions brownz-permission-container">
  <form class="voco-ajax-form" method="post" v-bind:action="form_save_url">
    @csrf
    <input type="hidden" name="id" v-model="id">
    <input type="hidden" name="tab_name" v-model="tab_name">

    <div class="perm-toolbar">
      <div>
        <h3 class="perm-toolbar-title">{{ __('Assigned Permissions') }}</h3>
        <p class="perm-toolbar-meta">{{ __('Select the permissions this role should have.') }}</p>
      </div>
      <div class="perm-count-pill">
        <i class="fa fa-check-circle"></i>
        @{{ selectedCount }} {{ __('selected') }}
      </div>
    </div>

    <div class="perm-grid">
      <div class="perm-group" v-for="(groupPermissions, groupName) in db_all_permissions" :key="'group-' + groupName">
        <div class="perm-group-header">
          <h4 class="perm-group-title">@{{ formatGroupName(groupName) }}</h4>
          <div class="perm-group-actions">
            <span class="perm-group-count">
              @{{ groupSelectedCount(groupPermissions) }}/@{{ groupPermissions.length }}
            </span>
            <button type="button" class="perm-select-all" v-on:click.prevent="toggleGroup(groupPermissions)">
              <span v-if="isGroupFullySelected(groupPermissions)">{{ __('Clear') }}</span>
              <span v-else>{{ __('Select all') }}</span>
            </button>
          </div>
        </div>

        <div v-for="permission in groupPermissions" :key="permission.id || permission.name">
          <label class="brw-checkbox-browz-dds">
            <input v-model="assigned_permissions"
              class="brow-checkbox-dd"
              type="checkbox"
              name="permissions[]"
              v-bind:value="permission.name">
            @{{ permission.description || permission.name }}
            <span class="brow-checkmark"></span>
          </label>
        </div>
      </div>
    </div>

    <div class="vc-form-buttons">
      <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d" v-bind:href="button_cancel_url">
        <i class="fa fa-undo"></i> {{ __('Cancel') }}
      </a>

      <button type="submit" class="btn btn-primary btn_dark-x">
        <i class="fa fa-check"></i> {{ __('Save') }}
      </button>
    </div>
  </form>
</div>

<script>
    var vue_details_tab_permissions = null;
    jQuery(function ($) {
        if (vue_details_tab_permissions) {
            vue_details_tab_permissions.$destroy();
            vue_details_tab_permissions = null;
        }

        vue_details_tab_permissions = new Vue({
            el: "#vue-details-tab-permissions",
            data: function () {
                return @json($details_permissions_vue);
            },
            computed: {
                selectedCount: function () {
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
                groupNames: function (groupPermissions) {
                    return (groupPermissions || []).map(function (permission) {
                        return permission.name;
                    });
                },
                groupSelectedCount: function (groupPermissions) {
                    var assigned = this.assigned_permissions || [];
                    return this.groupNames(groupPermissions).filter(function (name) {
                        return assigned.indexOf(name) !== -1;
                    }).length;
                },
                isGroupFullySelected: function (groupPermissions) {
                    var names = this.groupNames(groupPermissions);
                    if (!names.length) {
                        return false;
                    }
                    var assigned = this.assigned_permissions || [];
                    return names.every(function (name) {
                        return assigned.indexOf(name) !== -1;
                    });
                },
                toggleGroup: function (groupPermissions) {
                    var names = this.groupNames(groupPermissions);
                    var assigned = (this.assigned_permissions || []).slice();

                    if (this.isGroupFullySelected(groupPermissions)) {
                        this.assigned_permissions = assigned.filter(function (name) {
                            return names.indexOf(name) === -1;
                        });
                        return;
                    }

                    names.forEach(function (name) {
                        if (assigned.indexOf(name) === -1) {
                            assigned.push(name);
                        }
                    });
                    this.assigned_permissions = assigned;
                }
            }
        });
    });
</script>
