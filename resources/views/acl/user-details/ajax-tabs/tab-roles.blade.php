<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-roles.acl-user-roles {
    padding: 4px 0 8px;
  }

  #vue-details-tab-roles .roles-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 14px;
  }

  #vue-details-tab-roles .roles-toolbar-title {
    margin: 0;
    color: #1D1D1D;
    font-size: 1.05rem;
    font-weight: 600;
  }

  #vue-details-tab-roles .roles-toolbar-meta {
    margin: 2px 0 0;
    font-size: 12px;
    color: #6c757d;
  }

  #vue-details-tab-roles .roles-count-pill {
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

  #vue-details-tab-roles .roles-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 16px;
  }

  #vue-details-tab-roles .role-card {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 14px 16px 8px;
  }

  #vue-details-tab-roles .role-card-title {
    margin: 0 0 4px;
    font-size: 14px;
    font-weight: 700;
    color: #1D1D1D;
  }

  #vue-details-tab-roles .role-card-desc {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 10px;
    min-height: 18px;
  }

  #vue-details-tab-roles .type-pill {
    display: inline-flex;
    font-size: 11px;
    font-weight: 600;
    border-radius: 999px;
    padding: 4px 10px;
    margin-bottom: 8px;
  }

  #vue-details-tab-roles .type-pill.is-system {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  #vue-details-tab-roles .type-pill.is-custom {
    color: #1D1D1D;
    background: #EDEDF1;
  }

  #vue-details-tab-roles .brw-checkbox-browz-dds {
    font-size: 14px;
    margin-bottom: 8px;
  }

  #vue-details-tab-roles .vc-form-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  #vue-details-tab-roles .empty-state {
    text-align: center;
    padding: 28px 16px;
    background: #F7F7F9;
    border-radius: 14px;
    color: #6c757d;
    margin-bottom: 16px;
  }

  @media (max-width: 991.98px) {
    #vue-details-tab-roles .roles-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div id="vue-details-tab-roles" class="has-vue-form acl-user-roles">
  <form class="voco-ajax-form" method="post" v-bind:action="form_save_url">
    @csrf
    <input type="hidden" name="id" v-model="id">
    <input type="hidden" name="tab_name" v-model="tab_name">

    <div class="roles-toolbar">
      <div>
        <h3 class="roles-toolbar-title">{{ __('Assigned Roles') }}</h3>
        <p class="roles-toolbar-meta">{{ __('Select one or more roles for this user.') }}</p>
      </div>
      <div class="roles-count-pill">
        <i class="fa fa-check-circle"></i>
        @{{ selectedCount }} {{ __('selected') }}
      </div>
    </div>

    <div class="roles-grid" v-if="db_all_roles && db_all_roles.length">
      <div class="role-card" v-for="role in db_all_roles" :key="role.id || role.name">
        <span v-if="Number(role.system_defined) === 1" class="type-pill is-system">{{ __('System') }}</span>
        <span v-else class="type-pill is-custom">{{ __('Custom') }}</span>
        <p class="role-card-title">@{{ formatRoleName(role.name) }}</p>
        <span class="role-card-desc">@{{ role.description || '—' }}</span>
        <label class="brw-checkbox-browz-dds">
          <input v-model="assigned_roles"
            class="brow-checkbox-dd"
            type="checkbox"
            name="roles[]"
            v-bind:value="role.name">
          {{ __('Assign this role') }}
          <span class="brow-checkmark"></span>
        </label>
      </div>
    </div>

    <div class="empty-state" v-else>
      {{ __('No roles available.') }}
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
    var vue_details_tab_roles = null;
    jQuery(function ($) {
        vue_details_tab_roles = new Vue({
            el: "#vue-details-tab-roles",
            data: function () {
                return @json($details_roles_vue);
            },
            computed: {
                selectedCount: function () {
                    return (this.assigned_roles || []).length;
                }
            },
            methods: {
                formatRoleName: function (name) {
                    return String(name || '')
                        .replace(/_/g, ' ')
                        .replace(/\b\w/g, function (char) {
                            return char.toUpperCase();
                        });
                }
            }
        });
    });
</script>
