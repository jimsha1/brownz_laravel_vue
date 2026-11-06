<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables/expressions/functions directly inside html
 */
?>
<style>
  #vue-details-tab-edit.beautician-edit {
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

  #vue-details-tab-edit .photo-section {
    text-align: center;
    padding-bottom: 12px;
  }

  #vue-details-tab-edit .photo-section img {
    width: 140px;
    height: 140px;
    object-fit: cover;
    border-radius: 50%;
    background: #fff;
  }

  #vue-details-tab-edit .vc-form-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }
</style>

<div id="vue-details-tab-edit" class="beautician-edit has-vue-form">
  <template>
    <div v-if="beautician_id > 0" class="edit-section photo-section">
      <div class="edit-section-title">{{ __('Profile Photo') }}</div>
      <form v-bind:action="urls.form_update_photo_url" ref="photoChooserForm" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="beautician_id" v-model="beautician_id">
        <input v-on:change="$refs.photoChooserForm.submit()" ref="photoChooser" type="file" name="profile_photo_path" style="display:none">
      </form>
      <img v-bind:src="beautician_info.photo_src_url" alt="">
      <div class="mt-3">
        <button v-on:click="$refs.photoChooser.click()" type="button" class="btn btn-link btn-blue-brownz">
          {{ __('Change photo') }}
        </button>
      </div>
    </div>

    <form class="voco-ajax-form brw-form-la" method="post" v-bind:action="urls.form_save_url">
      @csrf
      <input type="hidden" name="beautician_id" v-model="beautician_id">
      <input type="hidden" name="tab_name" v-model="tab_name">

      <div class="edit-section">
        <div class="edit-section-title">{{ __('Basic Details') }}</div>
        <div class="row">
          <vcinput-basic class="col-md-12" v-model="beautician_info.name" id="name" label="{{ __('Name') }}"></vcinput-basic>
        </div>

        <div v-if="beautician_id == 0" class="row">
          <vcinput-basic v-model="beautician_info.username" id="username" label="{{ __('Username') }}"></vcinput-basic>
          <vcinput-basic v-model="beautician_info.password" id="password" label="{{ __('Password') }}"></vcinput-basic>
        </div>

        <div class="row">
          <vcinput-basic-email v-model="beautician_info.email" id="email" label="{{ __('Email') }}"></vcinput-basic-email>
          <vcinput-basic-number v-model="beautician_info.mobile" id="mobile" label="{{ __('Mobile') }}" type="number"></vcinput-basic-number>
        </div>

        <div class="row">
          <vcinput-basic-number v-model="beautician_info.telephone" id="telephone" label="{{ __('Telephone') }}"></vcinput-basic-number>
          <vcinput-datepicker v-model="beautician_info.join_date" id="join_date" label="{{ __('Join Date') }}"></vcinput-datepicker>
        </div>

        <div class="row">
          <div class="form-group col-md-6">
            <label for="gender">{{ __('Gender') }}</label>
            <select class="form-control voco-select2" id="gender" name="gender" data-placeholder="{{ __('Gender') }}" v-model="beautician_info.gender">
              <option value=""></option>
              {{ \Facades\App\Models\User::genderDropdown() }}
            </select>
          </div>

          <div class="form-group col-md-6">
            <label for="shif_id">{{ __('Shift') }}</label>
            <select class="form-control voco-select2" id="shif_id" name="shift_id" data-placeholder="{{ __('Shift') }}" v-model="beautician_info.shift_id">
              <option value="">{{ __('Select Shift') }}</option>
              <option v-for="item in dropdowns.shift" v-bind:value="item.shift_id">@{{ item.title }}</option>
            </select>
          </div>
        </div>
      </div>

      <div v-if="beautician_id > 0" class="edit-section">
        <div class="edit-section-title">{{ __('Bio') }}</div>
        <div class="row">
          <vcinput-textarea v-model="beautician_info.short_bio_en" id="short_bio_en" label="{{ __('Short Bio') }}" colclass="col"></vcinput-textarea>
        </div>
        <div class="row">
          <vcinput-textarea v-model="beautician_info.long_bio_en" id="long_bio_en" label="{{ __('Long Bio') }}" rows="5" colclass="col"></vcinput-textarea>
        </div>
      </div>

      <div class="vc-form-buttons text-right">
        <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d mr-2" v-bind:href="urls.button_cancel_url">
          <i class="fa fa-undo"></i> {{ __('Cancel') }}
        </a>

        <button type="submit" class="btn btn-primary btn_dark-x">
          <i class="fa fa-check"></i> {{ __('Save') }}
        </button>
      </div>
    </form>
  </template>
</div>

<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
var vue_details_tab_edit = null;
jQuery(function ($) {
  vue_details_tab_edit = new Vue({
    el: "#vue-details-tab-edit",
    data: <?php echo json_encode($details_edit_vue); ?>
  });
});
</script>
