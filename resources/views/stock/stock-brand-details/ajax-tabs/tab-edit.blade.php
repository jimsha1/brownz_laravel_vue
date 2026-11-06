<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-edit.stock-brand-edit {
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

  #vue-details-tab-edit .vc-form-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  #vue-details-tab-edit .field-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  #vue-details-tab-edit .field-help.is-danger {
    display: block;
    margin-top: 6px;
    color: #D0342C;
    font-size: 12px;
  }

  #vue-details-tab-edit .brand-password-overlay {
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

  #vue-details-tab-edit .brand-password-card {
    width: 100%;
    max-width: 420px;
    background: #fff;
    border-radius: 16px;
    padding: 18px 18px 14px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.2);
  }

  #vue-details-tab-edit .brand-password-card h6 {
    margin: 0 0 4px;
    color: #1D1D1D;
    font-size: 1.05rem;
    font-weight: 600;
  }

  #vue-details-tab-edit .brand-password-card .hint {
    margin: 0 0 14px;
    color: #6c757d;
    font-size: 12px;
  }

  #vue-details-tab-edit .brand-password-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 14px;
  }
</style>

<div id="vue-details-tab-edit" class="stock-brand-edit has-vue-form brw-form-la">
    <template>
        <form class="voco-ajax-form"
            method="post"
            v-bind:action="urls.form_save_url"
            data-hook-before-send="validateStockBrandBeforeSend">
            @csrf
            <input type="hidden" name="brand_id" v-model="brand_id">
            <input type="hidden" name="tab_name" v-model="tab_name">
            <input type="hidden" name="auth_password" v-model="auth_password">
            <input type="hidden" name="is_active" :value="Number(stock_brand_info.is_active) === 1 ? 1 : 0">

            <div class="brand-password-overlay" v-if="showPasswordConfirm">
                <div class="brand-password-card">
                    <h6>{{ __('Confirm with password') }}</h6>
                    <p class="hint">
                        {{ __('Enter your login password to update this stock brand. This cannot be skipped.') }}
                    </p>
                    <label class="field-label" for="brand_auth_password">{{ __('Your Password') }}</label>
                    <input id="brand_auth_password"
                        type="password"
                        class="form-control"
                        v-model="auth_password"
                        autocomplete="current-password"
                        placeholder="{{ __('Password') }}"
                        v-on:keydown.enter.prevent.stop="confirmPasswordAndSave">
                    <small class="field-help is-danger" v-if="passwordError">@{{ passwordError }}</small>
                    <div class="brand-password-actions">
                        <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                            v-on:click="cancelPasswordConfirm">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-primary btn_dark-x"
                            v-on:click="confirmPasswordAndSave"
                            :disabled="passwordSubmitting || isSaving">
                            <i class="fa fa-lock"></i> {{ __('Confirm & Save') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="edit-section">
                <div class="edit-section-title">{{ __('Brand Details') }}</div>
                <p class="edit-hint">{{ __('Name used when assigning brands to stock items') }}</p>
                <div class="row">
                    <vcinput-basic v-model="stock_brand_info.brand_name" id="brand_name"
                        label="{{ __('Brand Name') }}"></vcinput-basic>
                </div>
            </div>

            <div class="edit-section">
                <div class="edit-section-title">{{ __('Status') }}</div>
                <p class="edit-hint">{{ __('Active brands appear in stock item dropdowns') }}</p>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="is_active_select">{{ __('Status') }}</label>
                        <select class="form-control" id="is_active_select" v-model="stock_brand_info.is_active">
                            <option :value="1">{{ __('Active') }}</option>
                            <option :value="0">{{ __('Inactive') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="vc-form-buttons">
                <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d"
                    href="javascript:void(0)"
                    v-on:click.prevent="cancelForm">
                    <i class="fa fa-undo"></i> {{ __('Cancel') }}
                </a>

                <button type="submit" class="btn btn-primary btn_dark-x" :disabled="isSaving">
                    <i class="fa fa-check"></i> {{ __('Save') }}
                </button>
            </div>
        </form>
    </template>
</div>

<script>
    var vue_details_tab_edit = null;

    window.validateStockBrandBeforeSend = function () {
        var app = vue_details_tab_edit;
        if (!app) {
            return false;
        }
        if (app.isSaving) {
            return false;
        }

        if (app.isEditMode) {
            var password = String(app.auth_password || '').trim();
            if (!password) {
                app.passwordError = '';
                app.showPasswordConfirm = true;
                app.$nextTick(function () {
                    var input = document.getElementById('brand_auth_password');
                    if (input) {
                        input.focus();
                    }
                });
                return false;
            }
        }

        app.isSaving = true;
        return true;
    };

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
                var editData = <?php echo json_encode($details_edit_vue); ?>;
                if (!editData.stock_brand_info || typeof editData.stock_brand_info !== 'object') {
                    editData.stock_brand_info = {};
                }
                editData.stock_brand_info.is_active = Number(
                    editData.stock_brand_info.is_active != null
                        ? editData.stock_brand_info.is_active
                        : 1
                );
                editData.auth_password = '';
                editData.showPasswordConfirm = false;
                editData.passwordError = '';
                editData.passwordSubmitting = false;
                editData.isSaving = false;
                return editData;
            },
            computed: {
                isEditMode: function () {
                    return parseInt(this.brand_id, 10) > 0;
                }
            },
            methods: {
                cancelForm: function () {
                    var $modalEl = $('#vue-details-tab-edit').closest('#voco-ajax-modal');
                    if ($modalEl.length) {
                        $modalEl.modal('hide');
                        return;
                    }
                    if (this.urls && this.urls.button_cancel_url) {
                        window.location.href = this.urls.button_cancel_url;
                    }
                },
                cancelPasswordConfirm: function () {
                    if (this.isSaving) {
                        return;
                    }
                    this.showPasswordConfirm = false;
                    this.auth_password = '';
                    this.passwordError = '';
                    this.passwordSubmitting = false;
                },
                confirmPasswordAndSave: function () {
                    if (this.isSaving || this.passwordSubmitting) {
                        return;
                    }
                    var password = String(this.auth_password || '').trim();
                    this.passwordError = '';
                    if (!password) {
                        this.passwordError = '{{ __('Password is required.') }}';
                        return;
                    }
                    this.passwordSubmitting = true;
                    this.showPasswordConfirm = false;
                    var $form = $(this.$el).find('form.voco-ajax-form');
                    if ($form.length) {
                        $form.trigger('submit');
                    }
                },
                resetSavingState: function () {
                    this.isSaving = false;
                    this.passwordSubmitting = false;
                }
            }
        });

        $(document).off('ajaxError.stockBrandEditPassword ajaxComplete.stockBrandSave')
            .on('ajaxError.stockBrandEditPassword', function (event, jqxhr) {
                if (!vue_details_tab_edit) {
                    return;
                }
                vue_details_tab_edit.resetSavingState();
                if (!vue_details_tab_edit.isEditMode) {
                    return;
                }
                var message = (jqxhr.responseJSON && (
                    jqxhr.responseJSON.message ||
                    (jqxhr.responseJSON.errors && jqxhr.responseJSON.errors.auth_password && jqxhr.responseJSON.errors.auth_password[0])
                )) || '';
                if (/password/i.test(message) || (jqxhr.responseJSON && jqxhr.responseJSON.errors && jqxhr.responseJSON.errors.auth_password)) {
                    vue_details_tab_edit.auth_password = '';
                    vue_details_tab_edit.passwordError = message || '{{ __('Incorrect password.') }}';
                    vue_details_tab_edit.showPasswordConfirm = true;
                    vue_details_tab_edit.$nextTick(function () {
                        var input = document.getElementById('brand_auth_password');
                        if (input) {
                            input.focus();
                        }
                    });
                }
            })
            .on('ajaxComplete.stockBrandSave', function (event, xhr, settings) {
                if (!vue_details_tab_edit) {
                    return;
                }
                var saveUrl = vue_details_tab_edit.urls && vue_details_tab_edit.urls.form_save_url;
                if (saveUrl && settings && settings.url && String(settings.url).indexOf(saveUrl) !== -1) {
                    if (!xhr || xhr.status < 200 || xhr.status >= 300) {
                        vue_details_tab_edit.resetSavingState();
                    }
                }
            });
    });
</script>
