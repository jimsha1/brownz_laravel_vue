<style>
  #vue-details-tab-password.acl-user-password {
    padding: 4px 0 8px;
  }

  #vue-details-tab-password .edit-section {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 16px 16px 6px;
    margin-bottom: 16px;
  }

  #vue-details-tab-password .edit-section-title {
    font-size: 13px;
    font-weight: 600;
    color: #1D1D1D;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  #vue-details-tab-password .edit-hint {
    font-size: 12px;
    color: #6c757d;
    margin: -4px 0 14px;
  }

  #vue-details-tab-password .vc-form-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }
</style>

<div id="vue-details-tab-password" class="brw-form-la acl-user-password">
  <form class="voco-ajax-form" method="post" v-bind:action="urls.form_save_url">
    @csrf
    <input type="hidden" name="id" v-model="id">
    <input type="hidden" name="tab_name" v-model="tab_name">

    <div class="edit-section">
      <p class="edit-section-title">{{ __('Change Password') }}</p>
      <p class="edit-hint">{{ __('Set a new password for this user. Minimum 8 characters.') }}</p>

      <div class="row">
        <vcinput-basic id="password" label="{{ __('Password') }}" type="password"></vcinput-basic>
        <vcinput-basic id="confirm-password" label="{{ __('Confirm Password') }}" type="password"></vcinput-basic>
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
    var vue_details_tab_password = null;
    jQuery(function ($) {
        vue_details_tab_password = new Vue({
            el: "#vue-details-tab-password",
            data: function () {
                return @json($details_edit_vue);
            }
        });
    });
</script>
