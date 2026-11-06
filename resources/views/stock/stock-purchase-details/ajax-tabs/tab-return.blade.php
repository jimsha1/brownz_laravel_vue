<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
    #vue-details-tab-return.purchase-return {
        padding: 4px 0 8px;
        position: relative;
    }

    #vue-details-tab-return .field-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 6px;
    }

    #vue-details-tab-return .field-help.is-danger {
        display: block;
        margin-top: 6px;
        color: #D0342C;
        font-size: 12px;
    }

    #vue-details-tab-return .return-password-overlay {
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

    #vue-details-tab-return .return-password-card {
        width: 100%;
        max-width: 420px;
        background: #fff;
        border-radius: 16px;
        padding: 18px 18px 14px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.2);
    }

    #vue-details-tab-return .return-password-card h6 {
        margin: 0 0 4px;
        color: #1D1D1D;
        font-size: 1.05rem;
        font-weight: 600;
    }

    #vue-details-tab-return .return-password-card .hint {
        margin: 0 0 14px;
        color: #6c757d;
        font-size: 12px;
    }

    #vue-details-tab-return .return-password-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 14px;
    }

    #vue-details-tab-return .edit-section {
        background: #F7F7F9;
        border-radius: 14px;
        padding: 16px 16px 6px;
        margin-bottom: 16px;
    }

    #vue-details-tab-return .edit-section-title {
        font-size: 13px;
        font-weight: 600;
        color: #1D1D1D;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    #vue-details-tab-return .edit-hint {
        font-size: 12px;
        color: #6c757d;
        margin: 0 0 14px;
    }

    #vue-details-tab-return .add-row-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        color: #1D1D1D;
        margin: 8px 0 0;
    }

    #vue-details-tab-return .add-row-link:hover {
        color: #52A8D9;
        text-decoration: none;
    }

    #vue-details-tab-return .totals-card {
        background: #fff;
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: rgba(17, 17, 26, 0.08) 0 0 16px;
    }

    #vue-details-tab-return .totals-row {
        display: flex;
        justify-content: space-between;
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 8px;
    }

    #vue-details-tab-return .totals-row.is-grand {
        color: #1D1D1D;
        font-weight: 700;
        font-size: 1.05rem;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #e8e8ec;
    }

    #vue-details-tab-return .payment-input {
        background-color: #F0F0F3;
        color: #1D1D1D;
        font-size: 14px;
        font-weight: 400;
        padding: 10px 15px;
        border-radius: 26px;
        width: 100%;
        border: none;
        outline: none;
        height: auto;
    }

    #vue-details-tab-return .vc-form-buttons {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        background: #fff;
        position: sticky;
        bottom: 0;
        padding: 14px 0;
    }

    #vue-details-tab-return .products-wrap {
        margin: 0 -4px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    #vue-details-tab-return .products-table {
        table-layout: fixed;
        width: 100%;
        min-width: 1180px;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    #vue-details-tab-return .products-table thead th {
        white-space: nowrap;
        font-size: 12px;
        font-weight: 700;
        color: #1D1D1D;
        border-top: none;
        border-bottom: 1px solid #e8e8ec;
        padding: 8px 6px;
        vertical-align: middle;
    }

    #vue-details-tab-return .products-table tbody td {
        vertical-align: middle;
        padding: 8px 6px;
        border-top: none;
    }

    #vue-details-tab-return .products-table .col-sl { width: 36px; }
    #vue-details-tab-return .products-table .col-product { width: 200px; min-width: 180px; }
    #vue-details-tab-return .products-table .col-hsn { width: 100px; }
    #vue-details-tab-return .products-table .col-avail { width: 80px; }
    #vue-details-tab-return .products-table .col-qty { width: 90px; }
    #vue-details-tab-return .products-table .col-incl { width: 110px; }
    #vue-details-tab-return .products-table .col-cost { width: 90px; }
    #vue-details-tab-return .products-table .col-tax { width: 70px; }
    #vue-details-tab-return .products-table .col-discount { width: 80px; }
    #vue-details-tab-return .products-table .col-total { width: 100px; }
    #vue-details-tab-return .products-table .col-action { width: 40px; }

    #vue-details-tab-return .products-table th.is-num,
    #vue-details-tab-return .products-table td.is-num {
        text-align: right;
    }

    #vue-details-tab-return .products-table th.is-center,
    #vue-details-tab-return .products-table td.is-center {
        text-align: center;
    }

    #vue-details-tab-return .products-table .form-group {
        margin: 0 !important;
        float: none !important;
        width: 100% !important;
        max-width: none !important;
        padding: 0 !important;
    }

    #vue-details-tab-return .products-table .line-input,
    #vue-details-tab-return .products-table .form-control,
    #vue-details-tab-return .products-table input[type="number"],
    #vue-details-tab-return .products-table input[type="text"] {
        width: 100% !important;
        max-width: none !important;
        min-width: 0 !important;
        box-sizing: border-box;
        height: 40px !important;
        padding: 8px 10px !important;
        border-radius: 12px !important;
        border: none !important;
        background: #F0F0F3 !important;
        font-size: 13px !important;
        line-height: 1.2 !important;
    }

    #vue-details-tab-return .products-table .line-input.is-num {
        text-align: right;
        font-variant-numeric: tabular-nums;
        padding-right: 12px !important;
    }

    #vue-details-tab-return .products-table input[type="number"]::-webkit-outer-spin-button,
    #vue-details-tab-return .products-table input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    #vue-details-tab-return .products-table input[type="number"] {
        -moz-appearance: textfield;
        appearance: textfield;
    }

    #vue-details-tab-return .products-table .custom-table-select .select2-container,
    #vue-details-tab-return .products-table .select2-container {
        width: 100% !important;
        min-width: 0 !important;
        max-width: none !important;
        height: 40px !important;
        padding: 0 8px !important;
        box-sizing: border-box !important;
        background: #F0F0F3 !important;
        border-radius: 12px !important;
    }

    #vue-details-tab-return .products-table .select2-selection--single {
        height: 40px !important;
        border: none !important;
        background: transparent !important;
        outline: none !important;
    }

    #vue-details-tab-return .products-table .select2-selection__rendered {
        line-height: 40px !important;
        padding-left: 4px !important;
        padding-right: 24px !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #1D1D1D !important;
        font-size: 13px !important;
    }

    #vue-details-tab-return .products-table .select2-selection__arrow {
        top: 7px !important;
        right: 4px !important;
        height: 26px !important;
    }

    #vue-details-tab-return .products-table .row-remove {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        color: #D0342C;
        background: rgba(208, 52, 44, 0.08);
        text-decoration: none !important;
    }

    #vue-details-tab-return .products-table .row-remove:hover {
        background: #D0342C;
        color: #fff;
    }
</style>
<div id="vue-details-tab-return" class="purchase-return has-vue-form brw-form-la">
    <template>
        <form class="voco-ajax-form"
            method="post"
            v-bind:action="urls.form_save_url"
            data-hook-before-send="validateReturnBeforeSend">
            @csrf
            <input type="hidden" name="purchase_id" v-model="purchase_id">
            <input type="hidden" name="tab_name" v-model="tab_name">
            <input type="hidden" name="return_rows" :value="JSON.stringify(return_rows)">
            <input type="hidden" name="payment_rows" :value="JSON.stringify(payment_rows)">
            <input type="hidden" name="auth_password" v-model="auth_password">

            <div class="return-password-overlay" v-if="!isUnlocked">
                <div class="return-password-card">
                    <h6>{{ __('Unlock Purchase Return') }}</h6>
                    <p class="hint">
                        {{ __('Enter your login password to open this return form.') }}
                    </p>
                    <label class="field-label" for="return_unlock_password">{{ __('Your Password') }}</label>
                    <input id="return_unlock_password"
                        type="password"
                        class="form-control"
                        v-model="unlock_password"
                        autocomplete="current-password"
                        placeholder="{{ __('Password') }}"
                        v-on:keyup.enter="unlockReturnForm">
                    <small class="field-help is-danger" v-if="unlockError">@{{ unlockError }}</small>
                    <div class="return-password-actions">
                        <a class="btn btn-sm btn-secondary brownz-btn-yellow-d" v-bind:href="urls.button_cancel_url">
                            {{ __('Cancel') }}
                        </a>
                        <button type="button" class="btn btn-sm btn-primary btn_dark-x"
                            v-on:click="unlockReturnForm"
                            :disabled="unlockSubmitting">
                            <i class="fa fa-unlock"></i> {{ __('Unlock') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="return-password-overlay" v-if="isUnlocked && showPasswordConfirm">
                <div class="return-password-card">
                    <h6>{{ __('Confirm with password') }}</h6>
                    <p class="hint">
                        {{ __('Enter your login password to save this return. This cannot be skipped.') }}
                    </p>
                    <label class="field-label" for="return_auth_password">{{ __('Your Password') }}</label>
                    <input id="return_auth_password"
                        type="password"
                        class="form-control"
                        v-model="auth_password"
                        autocomplete="current-password"
                        placeholder="{{ __('Password') }}"
                        v-on:keydown.enter.prevent.stop="confirmPasswordAndSave">
                    <small class="field-help is-danger" v-if="passwordError">@{{ passwordError }}</small>
                    <div class="return-password-actions">
                        <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                            v-on:click="cancelPasswordConfirm"
                            :disabled="isSaving">
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

            <fieldset :disabled="!isUnlocked" style="border: 0; padding: 0; margin: 0; min-width: 0;">
            <div class="edit-section">
                <div class="edit-section-title">{{ __('Purchase Return') }}</div>
                <p class="edit-hint">{{ __('Return quantity is the number of packs. Stock on hand is reduced by that quantity times the item pack size.') }}</p>
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label for="return_date">{{ __('Return Date') }}</label>
                        <vcinput-jscalendar id="return_date" name="return_date" v-model="return_date"
                            class="form-control"></vcinput-jscalendar>
                    </div>
                </div>
            </div>

            <div class="edit-section">
                <div class="edit-section-title">{{ __('Return Items') }}</div>
                <div class="products-wrap">
                    <table class="table brownz-table-list-v1 products-table">
                        <thead>
                            <tr>
                                <th class="col-sl is-center">#</th>
                                <th class="col-product">{{ __('Product') }}</th>
                                <th class="col-hsn">{{ __('HSN/SAC') }}</th>
                                <th class="col-avail is-num">{{ __('Avail') }}</th>
                                <th class="col-qty is-num">{{ __('Return Qty') }}</th>
                                <th class="col-incl is-num">{{ __('Rate (Incl. of tax)') }}</th>
                                <th class="col-cost is-num">{{ __('Rate') }}</th>
                                <th class="col-tax is-num">{{ __('Tax %') }}</th>
                                <th class="col-discount is-num">{{ __('Disc. %') }}</th>
                                <th class="col-total is-num">{{ __('Amount') }}</th>
                                <th class="col-action"></th>
                            </tr>
                        </thead>

                        <tbody id="returnTableBody">
                            <tr v-for="(row, index) in return_rows" :key="index">
                                <td class="is-center">@{{ index + 1 }}</td>
                                <td>
                                    <vcinput-select2 class="custom-table-select" colclass="mb-0"
                                        v-model="row.item_id"
                                        :id="'return_item_' + index" :options="dropdowns.stock_items"
                                        placeholder="{{ __('Select product') }}" @input="onReturnProductChange(row.item_id, index)">
                                    </vcinput-select2>
                                </td>
                                <td>
                                    <input type="text" class="form-control line-input" :value="row.hsn_code || ''" readonly>
                                </td>
                                <td class="is-num">
                                    <input type="text" class="form-control line-input is-num" :value="row.purchase_quantity || 0" readonly>
                                </td>
                                <td class="is-num">
                                    <input type="number" class="form-control line-input is-num" v-model.number="row.quantity"
                                        :max="row.purchase_quantity || 0" min="0" step="0.01"
                                        :title="'Max Return: ' + (row.purchase_quantity || 0)"
                                        :placeholder="'Max: ' + (row.purchase_quantity || 0)"
                                        @input="onReturnQtyChange(row)">
                                </td>
                                <td class="is-num">
                                    <input type="number" class="form-control line-input is-num" :value="row.rate_incl_tax" readonly>
                                </td>
                                <td class="is-num">
                                    <input type="number" class="form-control line-input is-num" :value="row.cost_price" readonly>
                                </td>
                                <td class="is-num">
                                    <input type="number" class="form-control line-input is-num" :value="row.tax" readonly>
                                </td>
                                <td class="is-num">
                                    <input type="number" class="form-control line-input is-num" :value="discountDisplay(row)" readonly>
                                </td>
                                <td class="is-num">
                                    <input type="text" class="form-control line-input is-num" :value="lineTaxable(row).toFixed(2)" readonly>
                                </td>
                                <td class="is-center">
                                    <a href="javascript:void(0)" class="row-remove"
                                        @click="removeReturnRow(index)" title="{{ __('Remove') }}">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="edit-section mb-0">
                        <div class="edit-section-title">{{ __('Settlement') }}</div>
                        <p class="edit-hint">{{ __('Refund / credit settlement must match return total') }}</p>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th class="border-0">{{ __('Amount Paid') }}</th>
                                        <th class="border-0">{{ __('Payment Mode') }}</th>
                                        <th class="border-0"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, index) in payment_rows" :key="index">
                                        <td class="border-0">
                                            <input class="payment-input form-control" type="number"
                                                v-model.number="row.amount_taken" min="0" step="0.01"
                                                :disabled="row.disabled">
                                        </td>
                                        <td class="border-0">
                                            <select class="form-control payment-input"
                                                v-model="row.payment_mode" :disabled="row.disabled">
                                                {{ \Facades\App\Models\PurchasePayment::paymentModeDropdown() }}
                                            </select>
                                        </td>
                                        <td class="border-0 text-center" style="vertical-align: middle;">
                                            <a v-if="!row.disabled && payment_rows.length > 1" href="javascript:void(0)"
                                                class="text-danger" @click="removePaymentRow(index)">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="javascript:void(0)" class="add-row-link" @click="addPaymentRow">
                                <i class="fa fa-plus"></i> {{ __('Add') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="totals-card">
                        <div class="totals-row" v-if="isGstDocument">
                            <span>{{ __('Taxable amount') }}</span><span>@{{ taxableTotal.toFixed(2) }}</span>
                        </div>
                        <div class="totals-row" v-if="isGstDocument">
                            <span>{{ __('CGST') }}</span><span>@{{ cgstTotal.toFixed(2) }}</span>
                        </div>
                        <div class="totals-row" v-if="isGstDocument">
                            <span>{{ __('SGST') }}</span><span>@{{ sgstTotal.toFixed(2) }}</span>
                        </div>
                        <div class="totals-row" v-if="isGstDocument">
                            <span>{{ __('Round off') }}</span><span>@{{ roundOff.toFixed(2) }}</span>
                        </div>
                        <div class="totals-row" v-if="!isGstDocument">
                            <span>{{ __('Sub total') }}</span><span>@{{ returnSubtotal.toFixed(2) }}</span>
                        </div>
                        <div class="totals-row" v-if="!isGstDocument">
                            <span>{{ __('Tax Total') }}</span><span>@{{ returnTaxTotal.toFixed(2) }}</span>
                        </div>
                        <div class="totals-row" v-if="!isGstDocument">
                            <span>{{ __('Discount Total') }}</span><span>@{{ returnDiscountTotal.toFixed(2) }}</span>
                        </div>
                        <div class="totals-row is-grand">
                            <span>{{ __('Grand Total') }}</span><span>@{{ returnGrandTotal.toFixed(2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="vc-form-buttons">
                <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d"
                    v-bind:href="urls.button_cancel_url">
                    <i class="fa fa-undo"></i> {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary btn_dark-x"
                    :disabled="!isUnlocked || !isTotalPaidEqualToGrandTotal || isSaving">
                    <i class="fa fa-check"></i> {{ __('Save Returns') }}
                </button>
            </div>
            </fieldset>
        </form>
    </template>
</div>

<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
    var vc_moment_date_offset = new Date("2021-10-05T17:28:50.403+03:00").getTime() - Date.now();
    var vc_jscalendar_moment_format = "DD/MM/YYYY";
    var vue_details_tab_return = null;

    window.validateReturnBeforeSend = function () {
        var app = vue_details_tab_return;
        if (!app || !app.isUnlocked) {
            return false;
        }

        // Prevent double ajax submit (Enter + Confirm, or double click)
        if (app.isSaving) {
            return false;
        }

        if (!app.isTotalPaidEqualToGrandTotal) {
            if (typeof js_notify === 'function') {
                js_notify('{{ __('Settlement must equal return total, with at least one return qty.') }}', 'error');
            }
            return false;
        }

        var password = String(app.auth_password || '').trim();
        if (!password) {
            app.passwordError = '';
            app.showPasswordConfirm = true;
            app.$nextTick(function () {
                var input = document.getElementById('return_auth_password');
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
        vue_details_tab_return = new Vue({
            el: "#vue-details-tab-return",
            data: function() {
                return Object.assign(
                    <?php echo json_encode($details_return_vue); ?>, {
                        return_rows: <?php echo json_encode(
                            $details_return_vue['return_rows'] ?? [],
                        ); ?>,
                        payment_rows: <?php echo json_encode(
                            $details_return_vue['payment_rows'] ?? [
                                [
                                    'payment_id' => null,
                                    'amount_taken' => 0.0,
                                    'payment_mode' => 'cash',
                                    'disabled' => false,
                                ],
                            ],
                        ); ?>,
                        remainingAmount: 0,
                        isUnlocked: false,
                        unlock_password: '',
                        unlockError: '',
                        unlockSubmitting: false,
                        auth_password: '',
                        showPasswordConfirm: false,
                        passwordError: '',
                        passwordSubmitting: false,
                        isSaving: false,
                    }
                );
            },

            methods: {
                unlockReturnForm: function () {
                    var password = String(this.unlock_password || '').trim();
                    this.unlockError = '';
                    if (!password) {
                        this.unlockError = '{{ __('Password is required.') }}';
                        return;
                    }

                    var self = this;
                    this.unlockSubmitting = true;

                    $.ajax({
                        url: "{{ route('purchase.auth-check') }}",
                        method: 'POST',
                        data: {
                            password: password,
                            purpose: 'return',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.valid) {
                                self.isUnlocked = true;
                                self.unlock_password = '';
                                self.unlockError = '';
                            } else {
                                self.unlockError = response.statusText || '{{ __('Incorrect password.') }}';
                            }
                        },
                        error: function (xhr) {
                            self.unlockError = (xhr.responseJSON && xhr.responseJSON.statusText)
                                ? xhr.responseJSON.statusText
                                : '{{ __('Incorrect password.') }}';
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

                    // Keep buttons locked until ajax settles; do not clear passwordSubmitting here.
                },
                resetSavingState: function () {
                    this.isSaving = false;
                    this.passwordSubmitting = false;
                },
                addReturnRow() {
                    this.return_rows.push({
                        item_id: null,
                        hsn_code: '',
                        quantity: 0,
                        purchase_quantity: 0,
                        original_quantity: 0,
                        original_discount: 0,
                        cost_price: 0,
                        rate_incl_tax: 0,
                        tax: 0,
                        discount: 0,
                        discount_percent: null,
                        pricing_mode: 'legacy',
                        total: 0,
                    });
                },
                removeReturnRow(index) {
                    this.return_rows.splice(index, 1);
                },
                roundMoney(value) {
                    return parseFloat((parseFloat(value) || 0).toFixed(2));
                },
                lineGross(row) {
                    const qty = parseFloat(row.quantity) || 0;
                    const rate = parseFloat(row.cost_price) || 0;
                    return this.roundMoney(qty * rate);
                },
                lineTaxable(row) {
                    const qty = parseFloat(row.quantity) || 0;
                    const rate = parseFloat(row.cost_price) || 0;
                    if (row.pricing_mode === 'legacy' || row.discount_percent === null || row.discount_percent === '') {
                        return this.roundMoney((qty * rate) - (parseFloat(row.discount) || 0));
                    }
                    const disc = parseFloat(row.discount_percent) || 0;
                    return this.roundMoney(qty * rate * (1 - (disc / 100)));
                },
                lineTax(row) {
                    const tax = parseFloat(row.tax) || 0;
                    if (row.pricing_mode === 'legacy' || row.discount_percent === null || row.discount_percent === '') {
                        return this.roundMoney(this.lineGross(row) * tax / 100);
                    }
                    return this.roundMoney(this.lineTaxable(row) * tax / 100);
                },
                lineDiscount(row) {
                    if (row.pricing_mode === 'legacy' || row.discount_percent === null || row.discount_percent === '') {
                        return this.roundMoney(row.discount);
                    }
                    return this.roundMoney(this.lineGross(row) - this.lineTaxable(row));
                },
                discountDisplay(row) {
                    if (row.pricing_mode === 'legacy' || row.discount_percent === null || row.discount_percent === '') {
                        return this.roundMoney(row.discount).toFixed(2);
                    }
                    return this.roundMoney(row.discount_percent).toFixed(2);
                },
                onReturnQtyChange(row) {
                    let qty = parseFloat(row.quantity) || 0;
                    const maxQty = parseFloat(row.purchase_quantity) || 0;
                    if (qty < 0) qty = 0;
                    if (maxQty > 0 && qty > maxQty) qty = maxQty;
                    row.quantity = qty;

                    if (row.pricing_mode === 'legacy' || row.discount_percent === null || row.discount_percent === '') {
                        const originalQty = parseFloat(row.original_quantity) || 0;
                        const originalDiscount = parseFloat(row.original_discount) || 0;
                        if (originalQty > 0) {
                            row.discount = Number(((originalDiscount * qty) / originalQty).toFixed(2));
                        } else {
                            row.discount = 0;
                        }
                    }
                },
                calculateReturnTotal(row) {
                    return this.roundMoney(this.lineTaxable(row) + this.lineTax(row));
                },
                onReturnProductChange(stockItemId, index) {
                    const selected = this.dropdowns.stock_items.find(item => String(item.id) === String(
                        stockItemId));
                    const purchaseItem = this.purchase_items[stockItemId];

                    const duplicate = this.return_rows.some((row, i) =>
                        i !== index
                        && String(row.item_id) === String(stockItemId)
                        && stockItemId
                        && String(row.purchase_item_id || '') === String((this.return_rows[index] && this.return_rows[index].purchase_item_id) || '')
                    );
                    if (duplicate) {
                        alert('This product is already selected in another return row.');
                        this.$set(this.return_rows, index, {
                            ...this.return_rows[index],
                            item_id: null,
                            hsn_code: '',
                            cost_price: 0,
                            rate_incl_tax: 0,
                            tax: 0,
                            purchase_quantity: 0,
                            original_quantity: 0,
                            original_discount: 0,
                            quantity: 0,
                            discount: 0,
                            discount_percent: null,
                            pricing_mode: 'legacy',
                        });
                        return;
                    }

                    if (selected) {
                        const originalQty = purchaseItem ? parseFloat(purchaseItem.quantity) || 0 : 0;
                        const availableQty = purchaseItem
                            ? parseFloat(purchaseItem.available_quantity != null
                                ? purchaseItem.available_quantity
                                : purchaseItem.quantity) || 0
                            : 0;
                        const originalDiscount = purchaseItem ? parseFloat(purchaseItem.discount) || 0 : 0;
                        const discountPercent = purchaseItem && purchaseItem.discount_percent !== null && purchaseItem.discount_percent !== undefined
                            ? parseFloat(purchaseItem.discount_percent)
                            : null;
                        const rate = purchaseItem
                            ? parseFloat(purchaseItem.cost_price) || 0
                            : parseFloat(selected.cost_price) || 0;
                        const tax = purchaseItem
                            ? parseFloat(purchaseItem.tax_percent) || 0
                            : parseFloat(selected.tax) || 0;

                        const updateData = {
                            ...this.return_rows[index],
                            hsn_code: purchaseItem && purchaseItem.hsn_code
                                ? purchaseItem.hsn_code
                                : (selected.hsn_code || ''),
                            cost_price: rate,
                            rate_incl_tax: purchaseItem && purchaseItem.rate_incl_tax != null
                                ? parseFloat(purchaseItem.rate_incl_tax) || 0
                                : this.roundMoney(rate * (1 + (tax / 100))),
                            tax: tax,
                            original_quantity: originalQty,
                            original_discount: originalDiscount,
                            purchase_quantity: availableQty,
                            quantity: 0,
                            discount: 0,
                            discount_percent: discountPercent,
                            pricing_mode: discountPercent === null ? 'legacy' : 'gst',
                        };

                        this.$set(this.return_rows, index, updateData);
                    } else {
                        this.$set(this.return_rows, index, {
                            ...this.return_rows[index],
                            hsn_code: '',
                            cost_price: 0,
                            rate_incl_tax: 0,
                            tax: 0,
                            purchase_quantity: 0,
                            original_quantity: 0,
                            original_discount: 0,
                            discount: 0,
                            discount_percent: null,
                            pricing_mode: 'legacy',
                            quantity: 0,
                        });
                    }
                },
                redistributePayments(grandTotal) {
                    const total = Math.max(0, parseFloat(grandTotal) || 0);
                    if (!this.payment_rows.length) {
                        return;
                    }

                    const shareSum = this.payment_rows.reduce((sum, row) => sum + (parseFloat(row.share) || 0), 0);
                    if (shareSum > 0.000001 && this.payment_rows.every(row => row.share != null)) {
                        let allocated = 0;
                        this.payment_rows.forEach((row, index) => {
                            if (index === this.payment_rows.length - 1) {
                                this.$set(row, 'amount_taken', Number((total - allocated).toFixed(2)));
                            } else {
                                const amount = Number((total * (parseFloat(row.share) || 0)).toFixed(2));
                                allocated += amount;
                                this.$set(row, 'amount_taken', amount);
                            }
                            this.$set(row, 'disabled', false);
                        });
                        return;
                    }

                    if (this.payment_rows.length === 1) {
                        this.$set(this.payment_rows[0], 'amount_taken', Number(total.toFixed(2)));
                        this.$set(this.payment_rows[0], 'disabled', false);
                        return;
                    }

                    const lastNonDisabledIndex = this.payment_rows.reduce((lastIndex, row, index) => {
                        return row.disabled ? lastIndex : index;
                    }, -1);

                    if (lastNonDisabledIndex >= 0) {
                        const sumExceptLast = this.payment_rows
                            .filter((row, index) => index !== lastNonDisabledIndex)
                            .reduce((sum, row) => sum + (parseFloat(row.amount_taken) || 0), 0);
                        const newLastAmount = total - sumExceptLast;
                        this.$set(this.payment_rows[lastNonDisabledIndex], 'amount_taken', Math.max(0, Number(
                            newLastAmount.toFixed(2))));
                        this.$set(this.payment_rows[lastNonDisabledIndex], 'disabled', false);
                    }
                },
                addPaymentRow() {
                    const totalPaid = this.payment_rows.reduce((sum, row) => sum + (parseFloat(row
                        .amount_taken) || 0), 0);
                    this.remainingAmount = Math.max(0, this.returnGrandTotal - totalPaid);

                    if (this.payment_rows.length > 0) {
                        this.$set(this.payment_rows, this.payment_rows.length - 1, {
                            ...this.payment_rows[this.payment_rows.length - 1],
                            disabled: true,
                            share: null,
                        });
                    }

                    this.$set(this.payment_rows, this.payment_rows.length, {
                        payment_id: null,
                        amount_taken: this.remainingAmount > 0 ? Number(this.remainingAmount.toFixed(2)) : 0,
                        payment_mode: 'cash',
                        share: null,
                        disabled: false
                    });
                },
                removePaymentRow(index) {
                    if (this.payment_rows.length <= 1) {
                        return;
                    }

                    this.payment_rows.splice(index, 1);

                    let totalPaid = this.payment_rows.reduce((sum, row) => sum + (parseFloat(row
                        .amount_taken) || 0), 0);
                    this.remainingAmount = Math.max(0, this.returnGrandTotal - totalPaid);

                    if (this.payment_rows.length === 1) {
                        this.$set(this.payment_rows[0], 'amount_taken', Number(this.returnGrandTotal.toFixed(2)));
                        this.$set(this.payment_rows[0], 'disabled', false);
                        this.$set(this.payment_rows[0], 'share', 1);
                    } else {
                        const lastRowIndex = this.payment_rows.length - 1;
                        let sumExceptLast = this.payment_rows.slice(0, lastRowIndex).reduce(
                            (sum, row) => sum + (parseFloat(row.amount_taken) || 0), 0
                        );
                        let newLastAmount = this.returnGrandTotal - sumExceptLast;
                        this.$set(this.payment_rows[lastRowIndex], 'amount_taken', Number(Math.max(0, newLastAmount)
                            .toFixed(2)));
                        this.$set(this.payment_rows[lastRowIndex], 'disabled', false);
                        this.$set(this.payment_rows[lastRowIndex], 'share', null);
                    }
                },
                amountTakenChanged() {
                    const totalPaid = this.payment_rows.reduce((sum, row) => sum + (parseFloat(row
                        .amount_taken) || 0), 0);
                    this.remainingAmount = Math.max(0, this.returnGrandTotal - totalPaid);
                },
            },
            computed: {
                isGstDocument() {
                    return this.return_rows.some(row => row.pricing_mode === 'gst' || (row.discount_percent !== null && row.discount_percent !== '' && row.discount_percent !== undefined));
                },
                returnSubtotal() {
                    return this.return_rows.reduce((sum, row) => {
                        return this.roundMoney(sum + this.lineGross(row));
                    }, 0);
                },
                taxableTotal() {
                    return this.return_rows.reduce((sum, row) => {
                        return this.roundMoney(sum + this.lineTaxable(row));
                    }, 0);
                },
                returnTaxTotal() {
                    return this.return_rows.reduce((sum, row) => {
                        return this.roundMoney(sum + this.lineTax(row));
                    }, 0);
                },
                returnDiscountTotal() {
                    return this.return_rows.reduce((sum, row) => {
                        return this.roundMoney(sum + this.lineDiscount(row));
                    }, 0);
                },
                cgstTotal() {
                    return this.roundMoney(this.returnTaxTotal / 2);
                },
                sgstTotal() {
                    return this.roundMoney(this.returnTaxTotal - this.cgstTotal);
                },
                exactTotal() {
                    if (!this.isGstDocument) {
                        return this.roundMoney(this.returnSubtotal + this.returnTaxTotal - this.returnDiscountTotal);
                    }
                    return this.roundMoney(this.taxableTotal + this.returnTaxTotal);
                },
                returnGrandTotal() {
                    if (!this.isGstDocument) {
                        return this.exactTotal;
                    }
                    return Math.round(this.exactTotal);
                },
                roundOff() {
                    return this.roundMoney(this.returnGrandTotal - this.exactTotal);
                },
                creditSettlementTotal() {
                    return this.payment_rows.reduce((sum, row) => {
                        if (String(row.payment_mode || '').toLowerCase() !== 'credit') {
                            return sum;
                        }
                        return sum + (parseFloat(row.amount_taken) || 0);
                    }, 0);
                },
                isTotalPaidEqualToGrandTotal() {
                    const totalPaid = this.payment_rows.reduce((sum, row) => sum + (parseFloat(row
                        .amount_taken) || 0), 0);
                    const paymentsMatch = Math.abs(totalPaid - this.returnGrandTotal) < 0.01;
                    const hasReturnQty = this.return_rows.some(row => (parseFloat(row.quantity) || 0) > 0);
                    const maxCredit = parseFloat(this.max_credit_settlement);
                    const creditOk = isNaN(maxCredit) || this.creditSettlementTotal <= maxCredit + 0.01;
                    return paymentsMatch && hasReturnQty && creditOk && this.returnGrandTotal > 0;
                },
            },
            watch: {
                returnGrandTotal(newGrandTotal) {
                    this.redistributePayments(newGrandTotal);
                    this.amountTakenChanged();
                },
                'payment_rows': {
                    handler() {
                        this.amountTakenChanged();
                    },
                    deep: true
                }
            },
            mounted() {
                if (this.payment_rows.length === 0) {
                    this.addPaymentRow();
                }
                this.redistributePayments(this.returnGrandTotal);
                this.amountTakenChanged();
                this.$nextTick(function () {
                    var input = document.getElementById('return_unlock_password');
                    if (input) {
                        input.focus();
                    }
                });
            }
        });

        $(document).off('ajaxError.purchaseReturnPassword ajaxComplete.purchaseReturnSave')
            .on('ajaxError.purchaseReturnPassword', function (event, jqxhr) {
            if (!vue_details_tab_return || !vue_details_tab_return.isUnlocked) {
                return;
            }
            vue_details_tab_return.resetSavingState();
            var message = (jqxhr.responseJSON && (
                jqxhr.responseJSON.message ||
                (jqxhr.responseJSON.errors && jqxhr.responseJSON.errors.auth_password && jqxhr.responseJSON.errors.auth_password[0])
            )) || '';
            if (/password/i.test(message) || (jqxhr.responseJSON && jqxhr.responseJSON.errors && jqxhr.responseJSON.errors.auth_password)) {
                vue_details_tab_return.auth_password = '';
                vue_details_tab_return.passwordError = message || '{{ __('Incorrect password.') }}';
                vue_details_tab_return.showPasswordConfirm = true;
                vue_details_tab_return.$nextTick(function () {
                    var input = document.getElementById('return_auth_password');
                    if (input) {
                        input.focus();
                    }
                });
            }
        }).on('ajaxComplete.purchaseReturnSave', function (event, xhr, settings) {
            if (!vue_details_tab_return) {
                return;
            }
            var saveUrl = vue_details_tab_return.urls && vue_details_tab_return.urls.form_save_url;
            if (saveUrl && settings && settings.url && String(settings.url).indexOf(saveUrl) !== -1) {
                // Keep lock on success redirect; unlock only after failed request without navigation.
                if (!xhr || xhr.status < 200 || xhr.status >= 300) {
                    vue_details_tab_return.resetSavingState();
                }
            }
        });
    });
</script>
