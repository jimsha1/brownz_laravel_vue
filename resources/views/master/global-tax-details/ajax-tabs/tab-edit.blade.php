<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-edit.global-tax-edit {
    padding: 4px 0 8px;
  }

  #vue-details-tab-edit .edit-section {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 16px 16px 6px;
    margin-bottom: 16px;
  }

  #vue-details-tab-edit .edit-section-title {
    font-size: 13px;
    font-weight: 600;
    color: #1D1D1D;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  #vue-details-tab-edit .edit-hint {
    font-size: 12px;
    color: #6c757d;
    margin: -4px 0 14px;
  }

  #vue-details-tab-edit .active-box {
    background: #fff;
    border-radius: 12px;
    padding: 12px 14px;
    margin-bottom: 12px;
  }

  #vue-details-tab-edit .brw-checkbox-browz-dds {
    font-size: 14px;
    margin-bottom: 0;
  }

  #vue-details-tab-edit .vc-form-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  /* Keep form actions visible inside ajax modal (global rule hides .vc-form-buttons) */
  #voco-ajax-modal #vue-details-tab-edit .vc-form-buttons {
    display: flex !important;
  }
</style>

<div id="vue-details-tab-edit" class="has-vue-form brw-form-la global-tax-edit">
  <form class="voco-ajax-form" method="post" v-bind:action="urls.form_save_url">
    @csrf
    <input type="hidden" name="tax_id" v-model="tax_id">
    <input type="hidden" name="tab_name" v-model="tab_name">

    <div class="edit-section">
      <p class="edit-section-title">{{ __('Tax Details') }}</p>
      <p class="edit-hint">{{ __('Set the tax name, percentage rate, and active status.') }}</p>

      <div class="row">
        <vcinput-basic v-model="global_tax_info.name" id="name" label="{{ __('Name') }}"></vcinput-basic>
        <vcinput-basic v-model="global_tax_info.tax" id="tax" label="{{ __('Tax (%)') }}"></vcinput-basic>
      </div>

      <div class="active-box">
        <input type="hidden" name="is_active" value="0">
        <label class="brw-checkbox-browz-dds mb-0" for="is_active">
          <input v-model="global_tax_info.is_active"
            class="brow-checkbox-dd"
            type="checkbox"
            id="is_active"
            name="is_active"
            value="1"
            true-value="1"
            false-value="0">
          {{ __('Is Active') }}
          <span class="brow-checkmark"></span>
        </label>
      </div>
    </div>

    <div class="vc-form-buttons">
      <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d"
        href="javascript:void(0)"
        v-on:click.prevent="cancelForm">
        <i class="fa fa-undo"></i> {{ __('Cancel') }}
      </a>

      <button type="submit" class="btn btn-primary btn_dark-x">
        <i class="fa fa-check"></i> {{ __('Save') }}
      </button>
    </div>
  </form>
</div>

<script>
    var vue_details_tab_edit = null;
    jQuery(function($) {
        var $modal = $('#vue-details-tab-edit').closest('#voco-ajax-modal');
        if ($modal.length) {
            $modal.find('.modal-footer').hide();
            $modal.one('hidden.bs.modal', function () {
                $(this).find('.modal-footer').show();
                $(this).find('.btn-voco-save').show();
            });
        }

        vue_details_tab_edit = new Vue({
            el: "#vue-details-tab-edit",
            data: function () {
                return @json($details_edit_vue);
            },
            methods: {
                cancelForm: function () {
                    var $modal = $('#vue-details-tab-edit').closest('#voco-ajax-modal');
                    if ($modal.length) {
                        $modal.modal('hide');
                        return;
                    }
                    window.location.href = this.urls.button_cancel_url;
                }
            }
        });
    });
</script>
