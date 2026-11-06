<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-outlets.acl-user-outlets {
    padding: 4px 0 8px;
  }

  #vue-details-tab-outlets .outlets-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 14px;
  }

  #vue-details-tab-outlets .outlets-toolbar-title {
    margin: 0;
    color: #1D1D1D;
    font-size: 1.05rem;
    font-weight: 600;
  }

  #vue-details-tab-outlets .outlets-toolbar-meta {
    margin: 2px 0 0;
    font-size: 12px;
    color: #6c757d;
  }

  #vue-details-tab-outlets .outlets-count-pill {
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

  #vue-details-tab-outlets .outlets-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 16px;
  }

  #vue-details-tab-outlets .outlet-card {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 14px 16px 8px;
  }

  #vue-details-tab-outlets .outlet-card-title {
    margin: 0 0 10px;
    font-size: 14px;
    font-weight: 700;
    color: #1D1D1D;
  }

  #vue-details-tab-outlets .brw-checkbox-browz-dds {
    font-size: 14px;
    margin-bottom: 8px;
  }

  #vue-details-tab-outlets .vc-form-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  #vue-details-tab-outlets .empty-state {
    text-align: center;
    padding: 28px 16px;
    background: #F7F7F9;
    border-radius: 14px;
    color: #6c757d;
    margin-bottom: 16px;
  }

  @media (max-width: 991.98px) {
    #vue-details-tab-outlets .outlets-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div id="vue-details-tab-outlets" class="has-vue-form acl-user-outlets">
  <form class="voco-ajax-form" method="post" v-bind:action="form_save_url">
    @csrf
    <input type="hidden" name="id" v-model="id">
    <input type="hidden" name="tab_name" v-model="tab_name">

    <div class="outlets-toolbar">
      <div>
        <h3 class="outlets-toolbar-title">{{ __('Assigned Outlets') }}</h3>
        <p class="outlets-toolbar-meta">{{ __('Choose which outlets this user can access.') }}</p>
      </div>
      <div class="outlets-count-pill">
        <i class="fa fa-check-circle"></i>
        @{{ selectedCount }} {{ __('selected') }}
      </div>
    </div>

    <div class="outlets-grid" v-if="db_all_outlets && db_all_outlets.length">
      <div class="outlet-card" v-for="outlet in db_all_outlets" :key="outlet.outlet_id || outlet.id">
        <p class="outlet-card-title">@{{ outlet.name || '—' }}</p>
        <label class="brw-checkbox-browz-dds">
          <input v-model="assigned_outlets"
            class="brow-checkbox-dd"
            type="checkbox"
            name="outlets[]"
            v-bind:value="outlet.outlet_id">
          {{ __('Assign this outlet') }}
          <span class="brow-checkmark"></span>
        </label>
      </div>
    </div>

    <div class="empty-state" v-else>
      {{ __('No outlets available.') }}
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
    var vue_details_tab_outlets = null;
    jQuery(function ($) {
        vue_details_tab_outlets = new Vue({
            el: "#vue-details-tab-outlets",
            data: function () {
                return @json($details_outlets_vue);
            },
            computed: {
                selectedCount: function () {
                    return (this.assigned_outlets || []).length;
                }
            }
        });
    });
</script>
