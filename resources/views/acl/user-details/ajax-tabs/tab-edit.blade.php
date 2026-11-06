<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-edit.acl-user-edit {
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

  #vue-details-tab-edit .vc-form-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }
</style>

<div id="vue-details-tab-edit" class="has-vue-form brw-form-la acl-user-edit">
  <form class="voco-ajax-form" method="post" v-bind:action="urls.form_save_url">
    @csrf
    <input type="hidden" name="id" v-model="id">
    <input type="hidden" name="tab_name" v-model="tab_name">

    <div class="edit-section">
      <p class="edit-section-title">{{ __('Profile Details') }}</p>
      <p class="edit-hint">{{ __('Update basic account information for this user.') }}</p>

      <div class="row">
        <vcinput-basic v-model="user_info.name" id="name" label="{{ __('Name') }}"></vcinput-basic>
        <vcinput-basic v-model="user_info.username" id="username" label="{{ __('Username') }}"></vcinput-basic>
      </div>

      <div class="row">
        <vcinput-basic v-model="user_info.email" id="email" label="{{ __('Email') }}"></vcinput-basic>
        <div class="form-group col-md-6">
          <label for="gender">{{ __('Gender') }}</label>
          <select class="form-control voco-select2" id="gender" name="gender"
            data-placeholder="{{ __('Gender') }}" v-model="user_info.gender">
            <option value=""></option>
            {{ \Facades\App\Models\User::genderDropdown() }}
          </select>
        </div>
      </div>
    </div>

    <div class="vc-form-buttons">
      <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d" v-bind:href="urls.button_cancel_url">
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
jQuery(function ($) {
  vue_details_tab_edit = new Vue({
    el: "#vue-details-tab-edit",
    data: function () {
      return @json($details_edit_vue);
    }
  });
});
</script>
