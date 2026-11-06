<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
    #vue-details-tab-stock-adjustment.stock-adjustment-tab {
        padding: 4px 0 8px;
        position: relative;
    }

    #vue-details-tab-stock-adjustment .edit-section {
        background: #F7F7F9;
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 16px;
    }

    #vue-details-tab-stock-adjustment .edit-section-title {
        font-size: 13px;
        font-weight: 600;
        color: #1D1D1D;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    #vue-details-tab-stock-adjustment .edit-hint {
        font-size: 12px;
        color: #6c757d;
        margin: 0 0 14px;
    }

    #vue-details-tab-stock-adjustment .field-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 6px;
    }

    #vue-details-tab-stock-adjustment .field-help.is-danger {
        display: block;
        margin-top: 6px;
        color: #D0342C;
        font-size: 12px;
    }

    #vue-details-tab-stock-adjustment .qty-row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 12px;
    }

    #vue-details-tab-stock-adjustment .qty-field {
        flex: 1 1 180px;
        max-width: 240px;
    }

    #vue-details-tab-stock-adjustment .qty-field.is-wide {
        flex: 1 1 100%;
        max-width: none;
    }

    #vue-details-tab-stock-adjustment .qty-input,
    #vue-details-tab-stock-adjustment .reason-input {
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

    #vue-details-tab-stock-adjustment .reason-input {
        border-radius: 16px;
        min-height: 42px;
    }

    #vue-details-tab-stock-adjustment .direction-pills {
        display: inline-flex;
        background: #F0F0F3;
        border-radius: 999px;
        padding: 4px;
        gap: 4px;
    }

    #vue-details-tab-stock-adjustment .direction-pill {
        border: none;
        background: transparent;
        border-radius: 999px;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        cursor: pointer;
    }

    #vue-details-tab-stock-adjustment .direction-pill.is-increase.is-active {
        background: rgba(25, 153, 0, 0.18);
        color: #199900;
    }

    #vue-details-tab-stock-adjustment .direction-pill.is-decrease.is-active {
        background: rgba(208, 52, 44, 0.16);
        color: #D0342C;
    }

    #vue-details-tab-stock-adjustment .stock-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 14px;
    }

    #vue-details-tab-stock-adjustment .preview-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        background: #fff;
        color: #1D1D1D;
    }

    #vue-details-tab-stock-adjustment .preview-chip.is-result {
        background: rgba(82, 168, 217, 0.16);
        color: #1e6789;
    }

    #vue-details-tab-stock-adjustment .preview-chip.is-danger {
        background: rgba(208, 52, 44, 0.12);
        color: #D0342C;
    }

    #vue-details-tab-stock-adjustment .vc-form-buttons {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 14px;
    }

    #vue-details-tab-stock-adjustment .movements-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #1D1D1D;
        margin-top: 4px;
    }

    #vue-details-tab-stock-adjustment .movements-link:hover {
        color: #52A8D9;
        text-decoration: none;
    }

    #vue-details-tab-stock-adjustment .history-table {
        margin-bottom: 0;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
    }

    #vue-details-tab-stock-adjustment .history-table thead th {
        border-top: none;
        border-bottom: 1px solid #e8e8ec;
        color: #6c757d;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        padding: 12px 14px;
        background: #fff;
    }

    #vue-details-tab-stock-adjustment .history-table td {
        border-top: none;
        border-bottom: 1px solid #f0f0f3;
        vertical-align: middle;
        padding: 12px 14px;
        color: #1D1D1D;
    }

    #vue-details-tab-stock-adjustment .history-table tr:last-child td {
        border-bottom: none;
    }

    #vue-details-tab-stock-adjustment .qty-pill {
        display: inline-flex;
        min-width: 42px;
        justify-content: center;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
        background: #F0F0F3;
        color: #1D1D1D;
    }

    #vue-details-tab-stock-adjustment .qty-pill.is-in {
        color: #199900;
        background: rgba(25, 153, 0, 0.12);
    }

    #vue-details-tab-stock-adjustment .qty-pill.is-out {
        color: #D0342C;
        background: rgba(208, 52, 44, 0.12);
    }

    #vue-details-tab-stock-adjustment .empty-state {
        text-align: center;
        padding: 20px 12px;
        color: #6c757d;
        font-size: 13px;
    }

    #vue-details-tab-stock-adjustment .opening-password-overlay {
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

    #vue-details-tab-stock-adjustment .opening-password-card {
        width: 100%;
        max-width: 420px;
        background: #fff;
        border-radius: 16px;
        padding: 18px 18px 14px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.2);
    }

    #vue-details-tab-stock-adjustment .opening-password-card h6 {
        margin: 0 0 4px;
        color: #1D1D1D;
        font-size: 1.05rem;
        font-weight: 600;
    }

    #vue-details-tab-stock-adjustment .opening-password-card .hint {
        margin: 0 0 14px;
        color: #6c757d;
        font-size: 12px;
    }

    #vue-details-tab-stock-adjustment .opening-password-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 14px;
    }
</style>

<div id="vue-details-tab-stock-adjustment" class="stock-adjustment-tab has-vue-form">
    <template>
        <div>
            <div class="opening-password-overlay" v-if="showPasswordConfirm">
                <div class="opening-password-card">
                    <h6>{{ __('Confirm with password') }}</h6>
                    <p class="hint">
                        {{ __('Enter your login password to save this stock adjustment. This cannot be skipped.') }}
                    </p>
                    <label class="field-label" for="adjustment_auth_password">{{ __('Your Password') }}</label>
                    <input id="adjustment_auth_password"
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
                data-hook-before-send="validateStockAdjustmentBeforeSend">
                @csrf
                <input type="hidden" name="item_id" v-model="item_id">
                <input type="hidden" name="tab_name" v-model="tab_name">
                <input type="hidden" name="direction" v-model="direction">
                <input type="hidden" name="auth_password" v-model="auth_password">

                <div class="edit-section">
                    <div class="edit-section-title">{{ __('Stock Adjustment') }}</div>
                    <p class="edit-hint">
                        {{ __('Increase or decrease on-hand quantity. Each save creates a new stock movement. Value is picked up automatically through Stock in Hand and closing stock.') }}
                        <span v-if="uom_name">{{ __('Enter the quantity in') }} @{{ uom_name }}.</span>
                    </p>

                    <div class="qty-row">
                        <div class="qty-field">
                            <span class="field-label">{{ __('Type') }}</span>
                            <div class="direction-pills">
                                <button type="button"
                                    class="direction-pill is-increase"
                                    :class="{ 'is-active': direction === 'increase' }"
                                    v-on:click="direction = 'increase'">
                                    {{ __('Increase') }}
                                </button>
                                <button type="button"
                                    class="direction-pill is-decrease"
                                    :class="{ 'is-active': direction === 'decrease' }"
                                    v-on:click="direction = 'decrease'">
                                    {{ __('Decrease') }}
                                </button>
                            </div>
                        </div>

                        <div class="qty-field">
                            <label class="field-label" for="adjustment_quantity">{{ __('Quantity') }}</label>
                            <input type="number"
                                class="qty-input"
                                id="adjustment_quantity"
                                name="quantity"
                                min="0.01"
                                step="any"
                                placeholder="0"
                                v-model="quantity"
                                required>
                        </div>
                    </div>

                    <div class="stock-preview">
                        <span class="preview-chip">
                            {{ __('Current') }}: @{{ Number(current_stock || 0) }}<span v-if="uom_name"> @{{ uom_name }}</span>
                        </span>
                        <span class="preview-chip is-result" :class="{ 'is-danger': resultingStock < 0 }">
                            {{ __('After save') }}: @{{ resultingStock }}
                        </span>
                    </div>
                    <small class="field-help is-danger" v-if="resultingStock < 0">
                        {{ __('Cannot decrease more than the current stock.') }}
                    </small>

                    <div class="qty-field is-wide" style="margin-top: 14px;">
                        <label class="field-label" for="adjustment_reason">{{ __('Reason') }}</label>
                        <input type="text"
                            class="reason-input"
                            id="adjustment_reason"
                            name="reason"
                            maxlength="255"
                            placeholder="{{ __('e.g. Physical count, damaged, found extra') }}"
                            v-model="reason"
                            required>
                    </div>

                    <a class="movements-link" v-if="urls.stock_movements_url" v-bind:href="urls.stock_movements_url">
                        <i class="fa fa-exchange"></i> {{ __('View stock movements') }}
                    </a>

                    <div class="vc-form-buttons">
                        <button type="submit"
                            class="btn btn-primary btn_dark-x"
                            :disabled="isSaving || resultingStock < 0">
                            <i class="fa fa-check"></i> {{ __('Save Adjustment') }}
                        </button>
                    </div>
                </div>
            </form>

            <div class="edit-section">
                <div class="edit-section-title">{{ __('Recent Adjustments') }}</div>
                <p class="edit-hint">{{ __('Latest quantity corrections for this item.') }}</p>
                <div class="table-responsive">
                    <table class="table history-table">
                        <thead>
                            <tr>
                                <th>{{ __('In') }}</th>
                                <th>{{ __('Out') }}</th>
                                <th>{{ __('Note') }}</th>
                                <th>{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!adjustments || adjustments.length === 0">
                                <td colspan="4" class="empty-state">
                                    {{ __('No stock adjustments yet for this item.') }}
                                </td>
                            </tr>
                            <tr v-for="(row, index) in adjustments" :key="row.stock_movement_id || index">
                                <td>
                                    <span class="qty-pill" :class="{ 'is-in': Number(row.stock_in) > 0 }">
                                        @{{ Number(row.stock_in || 0) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="qty-pill" :class="{ 'is-out': Number(row.stock_out) > 0 }">
                                        @{{ Number(row.stock_out || 0) }}
                                    </span>
                                </td>
                                <td>@{{ row.description || '—' }}</td>
                                <td>
                                    <span v-if="row.created_at">@{{ row.created_at | vc_datetime_expanded }}</span>
                                    <span v-else>—</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </template>
</div>

<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
    var vue_details_tab_stock_adjustment = null;

    window.validateStockAdjustmentBeforeSend = function () {
        var app = vue_details_tab_stock_adjustment;
        if (!app) {
            return false;
        }
        if (app.isSaving || app.resultingStock < 0) {
            return false;
        }

        var qty = Number(app.quantity || 0);
        if (!(qty > 0)) {
            return false;
        }
        if (!String(app.reason || '').trim()) {
            return false;
        }

        var password = String(app.auth_password || '').trim();
        if (!password) {
            app.passwordError = '';
            app.showPasswordConfirm = true;
            app.$nextTick(function () {
                var input = document.getElementById('adjustment_auth_password');
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
        vue_details_tab_stock_adjustment = new Vue({
            el: "#vue-details-tab-stock-adjustment",
            data: function () {
                var payload = <?php echo json_encode($db_results['vue_data']['vue_list_tab_stock_adjustment']); ?>;
                return Object.assign({}, payload, {
                    direction: 'increase',
                    quantity: '',
                    reason: '',
                    auth_password: '',
                    showPasswordConfirm: false,
                    passwordError: '',
                    passwordSubmitting: false,
                    isSaving: false,
                });
            },
            computed: {
                resultingStock: function () {
                    var current = Number(this.current_stock || 0);
                    var qty = Number(this.quantity || 0);
                    if (!(qty > 0)) {
                        return current;
                    }
                    if (this.direction === 'decrease') {
                        return Math.round((current - qty) * 1000) / 1000;
                    }
                    return Math.round((current + qty) * 1000) / 1000;
                }
            },
            methods: {
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

        $(document).off('ajaxComplete.stockAdjustment').on('ajaxComplete.stockAdjustment', function (event, jqxhr, settings) {
            if (!vue_details_tab_stock_adjustment) {
                return;
            }
            var saveUrl = vue_details_tab_stock_adjustment.urls && vue_details_tab_stock_adjustment.urls.form_save_url;
            if (!saveUrl || !settings || !settings.url || settings.url.indexOf(saveUrl) === -1) {
                return;
            }

            vue_details_tab_stock_adjustment.resetSavingState();

            if (jqxhr.status >= 400) {
                var message = (jqxhr.responseJSON && (
                    jqxhr.responseJSON.message
                    || (jqxhr.responseJSON.errors && jqxhr.responseJSON.errors.auth_password && jqxhr.responseJSON.errors.auth_password[0])
                )) || '';
                if (/password/i.test(message) || (jqxhr.responseJSON && jqxhr.responseJSON.errors && jqxhr.responseJSON.errors.auth_password)) {
                    vue_details_tab_stock_adjustment.auth_password = '';
                    vue_details_tab_stock_adjustment.passwordError = message || '{{ __('Incorrect password.') }}';
                    vue_details_tab_stock_adjustment.showPasswordConfirm = true;
                    vue_details_tab_stock_adjustment.$nextTick(function () {
                        var input = document.getElementById('adjustment_auth_password');
                        if (input) {
                            input.focus();
                        }
                    });
                }
            }
        });
    });
</script>
