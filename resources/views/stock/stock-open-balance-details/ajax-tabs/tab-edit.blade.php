<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-edit.open-balance-edit {
    padding: 4px 0 8px;
    position: relative;
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
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  #vue-details-tab-edit .edit-hint {
    font-size: 12px;
    color: #6c757d;
    margin: 0 0 14px;
  }

  #vue-details-tab-edit .form-label-soft {
    font-size: 13px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  #vue-details-tab-edit .field-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  #vue-details-tab-edit .field-shell {
    display: flex;
    align-items: center;
    background: #FFFFFF;
    box-shadow: rgba(17, 17, 26, 0.1) 0 0 16px;
    border-radius: 16px;
    width: 100%;
    min-height: 44px;
    padding: 0 14px;
  }

  #vue-details-tab-edit .field-shell .form-control {
    border: none !important;
    box-shadow: none !important;
    background: transparent !important;
    width: 100%;
    min-height: 44px;
    padding-left: 0;
    padding-right: 0;
  }

  #vue-details-tab-edit .field-help {
    display: block;
    margin-top: 6px;
    font-size: 12px;
    color: #6c757d;
  }

  #vue-details-tab-edit .field-help.is-danger {
    color: #D0342C;
    font-weight: 600;
  }

  #vue-details-tab-edit .vc-form-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  #vue-details-tab-edit .open-balance-password-overlay {
    position: absolute;
    inset: 0;
    z-index: 20;
    background: rgba(29, 29, 29, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    border-radius: 12px;
  }

  #vue-details-tab-edit .open-balance-password-card {
    width: 100%;
    max-width: 420px;
    background: #fff;
    border-radius: 16px;
    padding: 18px 18px 14px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.2);
  }

  #vue-details-tab-edit .open-balance-password-card h6 {
    margin: 0 0 4px;
    color: #1D1D1D;
    font-size: 1.05rem;
    font-weight: 600;
  }

  #vue-details-tab-edit .open-balance-password-card .hint {
    margin: 0 0 14px;
    color: #6c757d;
    font-size: 12px;
  }

  #vue-details-tab-edit .open-balance-password-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 14px;
  }
</style>

<div id="vue-details-tab-edit" class="open-balance-edit has-vue-form brw-form-la">
  <template>
    <form class="voco-ajax-form"
      method="post"
      v-bind:action="urls.form_save_url"
      data-hook-before-send="validateOpenBalanceEditPassword">
      @csrf
      <input type="hidden" name="open_balance_id" v-model="open_balance_id">
      <input type="hidden" name="tab_name" v-model="tab_name">
      <input type="hidden" name="auth_password" v-model="auth_password">

      <div class="open-balance-password-overlay" v-if="showPasswordConfirm">
        <div class="open-balance-password-card">
          <h6>{{ __('Confirm with password') }}</h6>
          <p class="hint">
            {{ __('Enter your login password to update this opening balance. This cannot be skipped.') }}
          </p>

          <label class="field-label" for="open_balance_auth_password">{{ __('Your Password') }}</label>
          <div class="field-shell">
            <input id="open_balance_auth_password"
              type="password"
              class="form-control"
              v-model="auth_password"
              autocomplete="current-password"
              placeholder="{{ __('Password') }}"
              v-on:keyup.enter="confirmPasswordAndSave">
          </div>
          <small class="field-help is-danger" v-if="passwordError">@{{ passwordError }}</small>

          <div class="open-balance-password-actions">
            <button type="button"
              class="btn btn-sm btn-secondary brownz-btn-yellow-d"
              v-on:click="cancelPasswordConfirm">
              {{ __('Cancel') }}
            </button>
            <button type="button"
              class="btn btn-sm btn-primary btn_dark-x"
              v-on:click="confirmPasswordAndSave"
              :disabled="passwordSubmitting">
              <i class="fa fa-lock"></i> {{ __('Confirm & Save') }}
            </button>
          </div>
        </div>
      </div>

      <div class="edit-section">
        <p class="edit-section-title">{{ __('Opening Balance Details') }}</p>
        <p class="edit-hint">{{ __('Select the account, amount, and opening date.') }}</p>

        <div class="row">
          <vcinput-select2 v-model="stock_open_balance_info.account_id" id="account_id"
            label="{{ __('Account Name') }}" style="width: 100%"
            v-bind:options="dropdowns.accounts"></vcinput-select2>

          <vcinput-basic-number v-model="stock_open_balance_info.amount" id="amount"
            label="{{ __('Amount') }}"></vcinput-basic-number>

          <div class="form-group col-md-6">
            <label class="form-label-soft" for="opening_date">{{ __('Opening Balance Date') }}</label>
            <input type="date"
              id="opening_date"
              name="opening_date"
              class="form-control"
              v-model="stock_open_balance_info.opening_date"
              required>
          </div>
        </div>
      </div>

      <div class="vc-form-buttons">
        <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d"
          v-bind:href="urls.button_cancel_url">
          <i class="fa fa-undo"></i> {{ __('Cancel') }}
        </a>

        <button type="submit" class="btn btn-primary btn_dark-x">
          <i class="fa" :class="isEditMode ? 'fa-lock' : 'fa-check'"></i>
          @{{ isEditMode ? 'Save Changes' : 'Save' }}
        </button>
      </div>
    </form>
  </template>
</div>

<script>
  var vue_details_tab_edit = null;

  window.validateOpenBalanceEditPassword = function ($form) {
    if (!vue_details_tab_edit) {
      return true;
    }

    var app = vue_details_tab_edit;
    if (!app.isEditMode) {
      return true;
    }

    var password = String(app.auth_password || '').trim();
    if (!password) {
      app.passwordError = '';
      app.showPasswordConfirm = true;
      app.$nextTick(function () {
        var input = document.getElementById('open_balance_auth_password');
        if (input) {
          input.focus();
        }
      });
      return false;
    }

    return true;
  };

  jQuery(function ($) {
    vue_details_tab_edit = new Vue({
      el: "#vue-details-tab-edit",
      data: function () {
        var editData = <?php echo json_encode($details_edit_vue); ?>;
        if (!editData.stock_open_balance_info || typeof editData.stock_open_balance_info !== 'object') {
          editData.stock_open_balance_info = {};
        }
        if (!editData.stock_open_balance_info.opening_date) {
          var today = new Date();
          var month = String(today.getMonth() + 1).padStart(2, '0');
          var day = String(today.getDate()).padStart(2, '0');
          editData.stock_open_balance_info.opening_date = today.getFullYear() + '-' + month + '-' + day;
        }
        editData.auth_password = '';
        editData.showPasswordConfirm = false;
        editData.passwordError = '';
        editData.passwordSubmitting = false;
        return editData;
      },
      computed: {
        isEditMode: function () {
          return parseInt(this.open_balance_id, 10) > 0;
        }
      },
      methods: {
        cancelPasswordConfirm: function () {
          this.showPasswordConfirm = false;
          this.auth_password = '';
          this.passwordError = '';
          this.passwordSubmitting = false;
        },
        confirmPasswordAndSave: function () {
          var password = String(this.auth_password || '').trim();
          this.passwordError = '';
          if (!password) {
            this.passwordError = 'Password is required.';
            return;
          }

          this.passwordSubmitting = true;
          this.showPasswordConfirm = false;

          var $form = $(this.$el).find('form.voco-ajax-form');
          if ($form.length) {
            $form.trigger('submit');
          }

          var self = this;
          this.$nextTick(function () {
            self.passwordSubmitting = false;
          });
        }
      }
    });

    $(document).on('ajaxError.openBalanceEditPassword', function (event, jqxhr) {
      if (!vue_details_tab_edit || !vue_details_tab_edit.isEditMode) {
        return;
      }
      var message = (jqxhr.responseJSON && (
        jqxhr.responseJSON.message ||
        (jqxhr.responseJSON.errors && jqxhr.responseJSON.errors.auth_password && jqxhr.responseJSON.errors.auth_password[0])
      )) || '';
      if (/password/i.test(message) || (jqxhr.responseJSON && jqxhr.responseJSON.errors && jqxhr.responseJSON.errors.auth_password)) {
        vue_details_tab_edit.auth_password = '';
        vue_details_tab_edit.passwordError = message || 'Incorrect password.';
        vue_details_tab_edit.showPasswordConfirm = true;
        vue_details_tab_edit.$nextTick(function () {
          var input = document.getElementById('open_balance_auth_password');
          if (input) {
            input.focus();
          }
        });
      }
    });
  });
</script>
