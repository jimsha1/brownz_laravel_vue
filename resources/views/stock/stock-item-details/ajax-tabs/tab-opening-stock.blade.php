<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
    #vue-details-tab-opening-stock.opening-stock-tab {
        padding: 4px 0 8px;
        position: relative;
    }

    #vue-details-tab-opening-stock .edit-section {
        background: #F7F7F9;
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 16px;
    }

    #vue-details-tab-opening-stock .edit-section-title {
        font-size: 13px;
        font-weight: 600;
        color: #1D1D1D;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    #vue-details-tab-opening-stock .edit-hint {
        font-size: 12px;
        color: #6c757d;
        margin: 0 0 14px;
    }

    #vue-details-tab-opening-stock .field-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 6px;
    }

    #vue-details-tab-opening-stock .field-help.is-danger {
        display: block;
        margin-top: 6px;
        color: #D0342C;
        font-size: 12px;
    }

    #vue-details-tab-opening-stock .qty-row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 12px;
    }

    #vue-details-tab-opening-stock .qty-field {
        flex: 1 1 220px;
        max-width: 280px;
    }

    #vue-details-tab-opening-stock .qty-input {
        background-color: #F0F0F3;
        color: #1D1D1D;
        font-size: 14px;
        font-weight: 500;
        padding: 10px 15px;
        border-radius: 26px;
        width: 100%;
        border: none;
        outline: none;
        height: auto;
    }

    #vue-details-tab-opening-stock .qty-input:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    #vue-details-tab-opening-stock .lock-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 8px 12px;
        background: rgba(208, 52, 44, 0.1);
        color: #D0342C;
        font-size: 12px;
        font-weight: 600;
    }

    #vue-details-tab-opening-stock .lock-pill.is-unlocked {
        background: rgba(25, 153, 0, 0.12);
        color: #199900;
    }

    #vue-details-tab-opening-stock .vc-form-buttons {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 14px;
    }

    #vue-details-tab-opening-stock .movements-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #1D1D1D;
        margin-top: 4px;
    }

    #vue-details-tab-opening-stock .movements-link:hover {
        color: #52A8D9;
        text-decoration: none;
    }

    #vue-details-tab-opening-stock .opening-password-overlay {
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

    #vue-details-tab-opening-stock .opening-password-card {
        width: 100%;
        max-width: 420px;
        background: #fff;
        border-radius: 16px;
        padding: 18px 18px 14px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.2);
    }

    #vue-details-tab-opening-stock .opening-password-card h6 {
        margin: 0 0 4px;
        color: #1D1D1D;
        font-size: 1.05rem;
        font-weight: 600;
    }

    #vue-details-tab-opening-stock .opening-password-card .hint {
        margin: 0 0 14px;
        color: #6c757d;
        font-size: 12px;
    }

    #vue-details-tab-opening-stock .opening-password-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 14px;
    }
</style>

<div id="vue-details-tab-opening-stock" class="opening-stock-tab has-vue-form">
    <template>
        <div>
            <div class="opening-password-overlay" v-if="needsUnlock && !isUnlocked && showUnlockOverlay">
                <div class="opening-password-card">
                    <h6>{{ __('Unlock Opening Stock') }}</h6>
                    <p class="hint">
                        {{ __('Enter your login password to edit the existing opening stock quantity.') }}
                    </p>
                    <label class="field-label" for="opening_unlock_password">{{ __('Your Password') }}</label>
                    <input id="opening_unlock_password"
                        type="password"
                        class="form-control"
                        v-model="unlock_password"
                        autocomplete="current-password"
                        placeholder="{{ __('Password') }}"
                        v-on:keydown.enter.prevent.stop="unlockForm">
                    <small class="field-help is-danger" v-if="unlockError">@{{ unlockError }}</small>
                    <div class="opening-password-actions">
                        <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                            v-on:click="showUnlockOverlay = false">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-primary btn_dark-x"
                            v-on:click="unlockForm"
                            :disabled="unlockSubmitting">
                            <i class="fa fa-unlock"></i> {{ __('Unlock') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="opening-password-overlay" v-if="isFormInteractive && showPasswordConfirm">
                <div class="opening-password-card">
                    <h6>{{ __('Confirm with password') }}</h6>
                    <p class="hint">
                        {{ __('Enter your login password to save opening stock. This cannot be skipped.') }}
                    </p>
                    <label class="field-label" for="opening_auth_password">{{ __('Your Password') }}</label>
                    <input id="opening_auth_password"
                        type="password"
                        class="form-control"
                        v-model="auth_password"
                        autocomplete="current-password"
                        placeholder="{{ __('Password') }}"
                        v-on:keydown.enter.prevent.stop="confirmPasswordAndSave">
                    <small class="field-help is-danger" v-if="passwordError">@{{ passwordError }}</small>
                    <div class="opening-password-actions">
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

            <form class="voco-ajax-form"
                method="post"
                v-bind:action="urls.form_save_url"
                data-hook-before-send="validateOpeningStockBeforeSend">
                @csrf
                <input type="hidden" name="item_id" v-model="item_id">
                <input type="hidden" name="tab_name" v-model="tab_name">
                <input type="hidden" name="open_stock_id" v-model="open_stock_id">
                <input type="hidden" name="auth_password" v-model="auth_password">

                <fieldset :disabled="!isFormInteractive" style="border: 0; padding: 0; margin: 0; min-width: 0;">
                    <div class="edit-section">
                        <div class="edit-section-title">{{ __('Opening Quantity') }}</div>
                        <p class="edit-hint" v-if="needsUnlock && !isUnlocked">
                            {{ __('Opening stock already exists. Unlock with your password to edit.') }}
                        </p>
                        <p class="edit-hint" v-else-if="needsUnlock && isUnlocked">
                            {{ __('Unlocked. Change the quantity, then confirm with your password to save.') }}
                        </p>
                        <p class="edit-hint" v-else>
                            {{ __('Set the opening quantity for this item. Password confirmation is required on save.') }}
                        </p>
                        <p class="edit-hint" v-if="uom_name">
                            {{ __('Enter the quantity in') }} @{{ uom_name }}.
                            <span v-if="Number(pack_size) > 1">{{ __('One pack is') }} @{{ Number(pack_size) }} @{{ uom_name }}.</span>
                        </p>

                        <div class="qty-row">
                            <div class="qty-field">
                                <label class="field-label" for="price_list_quantity">{{ __('Quantity') }}</label>
                                <input type="number"
                                    class="qty-input"
                                    id="price_list_quantity"
                                    name="quantity"
                                    min="0"
                                    step="any"
                                    placeholder="0"
                                    v-model="quantity"
                                    required
                                    :disabled="!isFormInteractive">
                            </div>

                            <span class="lock-pill" v-if="needsUnlock" :class="{ 'is-unlocked': isUnlocked }">
                                <i class="fa" :class="isUnlocked ? 'fa-unlock' : 'fa-lock'"></i>
                                <span v-if="isUnlocked">{{ __('Unlocked') }}</span>
                                <span v-else>{{ __('Locked') }}</span>
                            </span>

                            <button type="button"
                                class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                                v-if="needsUnlock && !isUnlocked"
                                v-on:click="openUnlockOverlay">
                                <i class="fa fa-unlock"></i> {{ __('Unlock') }}
                            </button>
                        </div>

                        <a class="movements-link" v-if="urls.stock_movements_url" v-bind:href="urls.stock_movements_url">
                            <i class="fa fa-exchange"></i> {{ __('View stock movements') }}
                        </a>

                        <div class="vc-form-buttons">
                            <button type="submit"
                                class="btn btn-primary btn_dark-x"
                                :disabled="!isFormInteractive || isSaving">
                                <i class="fa fa-check"></i> {{ __('Save') }}
                            </button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </template>
</div>

<script>
    var vue_details_tab_pricing = null;

    window.validateOpeningStockBeforeSend = function () {
        var app = vue_details_tab_pricing;
        if (!app) {
            return false;
        }
        if (!app.isFormInteractive || app.isSaving) {
            return false;
        }

        var password = String(app.auth_password || '').trim();
        if (!password) {
            app.passwordError = '';
            app.showPasswordConfirm = true;
            app.$nextTick(function () {
                var input = document.getElementById('opening_auth_password');
                if (input) {
                    input.focus();
                }
            });
            return false;
        }

        app.isSaving = true;
        return true;
    };

    jQuery(function($) {
        vue_details_tab_pricing = new Vue({
            el: "#vue-details-tab-opening-stock",
            data: function () {
                var payload = <?php echo json_encode($db_results['vue_data']['vue_list_tab_opening_stock']); ?>;
                return Object.assign({}, payload, {
                    quantity: 0,
                    open_stock_id: null,
                    needsUnlock: false,
                    isUnlocked: false,
                    showUnlockOverlay: false,
                    unlock_password: '',
                    unlockError: '',
                    unlockSubmitting: false,
                    auth_password: '',
                    showPasswordConfirm: false,
                    passwordError: '',
                    passwordSubmitting: false,
                    isSaving: false,
                });
            },
            computed: {
                isFormInteractive: function () {
                    if (this.needsUnlock) {
                        return this.isUnlocked;
                    }
                    return true;
                }
            },
            methods: {
                openUnlockOverlay: function () {
                    this.showUnlockOverlay = true;
                    this.unlock_password = '';
                    this.unlockError = '';
                    this.$nextTick(function () {
                        var input = document.getElementById('opening_unlock_password');
                        if (input) {
                            input.focus();
                        }
                    });
                },
                unlockForm: function () {
                    var self = this;
                    var password = String(this.unlock_password || '').trim();
                    this.unlockError = '';
                    if (!password) {
                        this.unlockError = '{{ __('Password is required.') }}';
                        return;
                    }
                    if (this.unlockSubmitting) {
                        return;
                    }

                    this.unlockSubmitting = true;
                    $.ajax({
                        url: this.urls.auth_check_url,
                        method: 'POST',
                        data: {
                            password: password,
                            item_id: this.item_id,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response && response.valid) {
                                self.isUnlocked = true;
                                self.showUnlockOverlay = false;
                                self.unlock_password = '';
                                self.unlockError = '';
                            } else {
                                self.unlockError = (response && response.statusText)
                                    || '{{ __('Incorrect password.') }}';
                            }
                        },
                        error: function (xhr) {
                            self.unlockError = (xhr.responseJSON && xhr.responseJSON.statusText)
                                || '{{ __('Error while checking the password.') }}';
                        },
                        complete: function () {
                            self.unlockSubmitting = false;
                        }
                    });
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
                },
                seedOpeningValues: function () {
                    var opening = this.opening_stock;
                    if (opening && opening.stock_movement_id) {
                        this.needsUnlock = true;
                        this.isUnlocked = false;
                        this.showUnlockOverlay = true;
                        this.quantity = opening.stock_in || 0;
                        this.item_id = opening.item_id || this.item_id;
                        this.open_stock_id = opening.stock_movement_id || null;
                        return;
                    }

                    this.needsUnlock = false;
                    this.isUnlocked = true;
                    this.showUnlockOverlay = false;
                    this.open_stock_id = null;
                    this.quantity = 0;
                }
            },
            mounted: function () {
                this.seedOpeningValues();
            }
        });

        $(document).off('ajaxComplete.openingStock').on('ajaxComplete.openingStock', function (event, jqxhr, settings) {
            if (!vue_details_tab_pricing) {
                return;
            }
            var saveUrl = vue_details_tab_pricing.urls && vue_details_tab_pricing.urls.form_save_url;
            if (!saveUrl || !settings || !settings.url || settings.url.indexOf(saveUrl) === -1) {
                return;
            }

            vue_details_tab_pricing.resetSavingState();

            if (jqxhr.status >= 400) {
                var message = (jqxhr.responseJSON && (
                    jqxhr.responseJSON.message
                    || (jqxhr.responseJSON.errors && jqxhr.responseJSON.errors.auth_password && jqxhr.responseJSON.errors.auth_password[0])
                )) || '';
                if (/password/i.test(message) || (jqxhr.responseJSON && jqxhr.responseJSON.errors && jqxhr.responseJSON.errors.auth_password)) {
                    vue_details_tab_pricing.auth_password = '';
                    vue_details_tab_pricing.passwordError = message || '{{ __('Incorrect password.') }}';
                    vue_details_tab_pricing.showPasswordConfirm = true;
                    vue_details_tab_pricing.$nextTick(function () {
                        var input = document.getElementById('opening_auth_password');
                        if (input) {
                            input.focus();
                        }
                    });
                }
            }
        });
    });
</script>
