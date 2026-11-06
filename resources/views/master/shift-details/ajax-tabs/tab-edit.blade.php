<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-edit.shift-edit {
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

  #vue-details-tab-edit .option-box {
    background: #fff;
    border-radius: 12px;
    padding: 12px 14px;
    margin-bottom: 12px;
  }

  #vue-details-tab-edit .brw-checkbox-browz-dds {
    font-size: 14px;
    margin-bottom: 0;
  }

  #vue-details-tab-edit .form-label-soft {
    font-size: 13px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  #vue-details-tab-edit .vc-form-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  #voco-ajax-modal #vue-details-tab-edit .vc-form-buttons {
    display: flex !important;
  }
</style>

<div id="vue-details-tab-edit" class="has-vue-form brw-form-la shift-edit">
  <form class="voco-ajax-form" method="post" v-bind:action="urls.form_save_url">
    @csrf
    <input type="hidden" name="shift_id" v-model="shift_id">
    <input type="hidden" name="tab_name" v-model="tab_name">

    <div class="edit-section">
      <p class="edit-section-title">{{ __('Shift Details') }}</p>
      <p class="edit-hint">{{ __('Set the shift title, timing, and active status.') }}</p>

      <div class="row">
        <div class="form-group col-md-12">
          <label class="form-label-soft" for="title">{{ __('Title') }}</label>
          <input v-model="shift_info.title" id="title" class="form-control" type="text"
            placeholder="{{ __('Title') }}" name="title">
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-6">
          <label class="form-label-soft" for="start_time">{{ __('Start Time') }}</label>
          <input v-model="shift_info.start_time" id="start_time" class="form-control" type="time" name="start_time">
        </div>
        <div class="form-group col-md-6">
          <label class="form-label-soft" for="end_time">{{ __('End Time') }}</label>
          <input v-model="shift_info.end_time" id="end_time" class="form-control" type="time" name="end_time">
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="option-box">
            <input type="hidden" name="is_active" value="0">
            <label class="brw-checkbox-browz-dds mb-0" for="is_active">
              <input v-model="shift_info.is_active"
                class="brow-checkbox-dd"
                type="checkbox"
                id="is_active"
                name="is_active"
                value="1"
                :true-value="1"
                :false-value="0">
              {{ __('Is Active') }}
              <span class="brow-checkmark"></span>
            </label>
          </div>
        </div>
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

        var editData = @json($details_edit_vue);
        if (editData.shift_info) {
            editData.shift_info.is_active = Number(editData.shift_info.is_active) === 1 ? 1 : 0;
        }

        vue_details_tab_edit = new Vue({
            el: "#vue-details-tab-edit",
            data: function () {
                return editData;
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
