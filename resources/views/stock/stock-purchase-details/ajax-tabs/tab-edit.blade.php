<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
    #vue-details-tab-edit.purchase-edit {
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

    #vue-details-tab-edit .add-row-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        color: #1D1D1D;
        margin: 8px 0 0;
    }

    #vue-details-tab-edit .add-row-link:hover {
        color: #52A8D9;
        text-decoration: none;
    }

    #vue-details-tab-edit .totals-card {
        background: #fff;
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: rgba(17, 17, 26, 0.08) 0 0 16px;
    }

    #vue-details-tab-edit .totals-row {
        display: flex;
        justify-content: space-between;
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 8px;
    }

    #vue-details-tab-edit .totals-row.is-grand {
        color: #1D1D1D;
        font-weight: 700;
        font-size: 1.05rem;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #e8e8ec;
    }

    #vue-details-tab-edit .freight-input {
        width: 120px;
        height: 36px;
        text-align: right;
        background: #F0F0F3;
        border: none;
        border-radius: 10px;
        padding: 6px 10px;
        color: #1D1D1D;
    }

    #vue-details-tab-edit .payment-input {
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

    #vue-details-tab-edit .vc-form-buttons {
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

    #vue-details-tab-edit .purchase-password-overlay {
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

    #vue-details-tab-edit .purchase-password-card {
        width: 100%;
        max-width: 420px;
        background: #fff;
        border-radius: 16px;
        padding: 18px 18px 14px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.2);
    }

    #vue-details-tab-edit .purchase-password-card h6 {
        margin: 0 0 4px;
        color: #1D1D1D;
        font-size: 1.05rem;
        font-weight: 600;
    }

    #vue-details-tab-edit .purchase-password-card .hint {
        margin: 0 0 14px;
        color: #6c757d;
        font-size: 12px;
    }

    #vue-details-tab-edit .purchase-password-card textarea.form-control {
        min-height: 84px;
        resize: vertical;
    }

    #vue-details-tab-edit .purchase-password-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 14px;
    }

    #vue-details-tab-edit .products-wrap {
        margin: 0 -4px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    #vue-details-tab-edit .products-table {
        table-layout: fixed;
        width: 100%;
        min-width: 1120px;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    #vue-details-tab-edit .products-table thead th {
        white-space: nowrap;
        font-size: 12px;
        font-weight: 700;
        color: #1D1D1D;
        border-top: none;
        border-bottom: 1px solid #e8e8ec;
        padding: 8px 6px;
        vertical-align: middle;
    }

    #vue-details-tab-edit .products-table tbody td {
        vertical-align: middle;
        padding: 8px 6px;
        border-top: none;
    }

    /* Column mix: Product dominates, numbers stay compact and aligned */
    #vue-details-tab-edit .products-table .col-sl { width: 36px; }
    #vue-details-tab-edit .products-table .col-product { width: 220px; min-width: 180px; }
    #vue-details-tab-edit .products-table .col-hsn { width: 108px; }
    #vue-details-tab-edit .products-table .col-qty { width: 76px; }
    #vue-details-tab-edit .products-table .col-incl { width: 118px; }
    #vue-details-tab-edit .products-table .col-cost { width: 100px; }
    #vue-details-tab-edit .products-table .col-per { width: 64px; }
    #vue-details-tab-edit .products-table .col-tax { width: 72px; }
    #vue-details-tab-edit .products-table .col-discount { width: 76px; }
    #vue-details-tab-edit .products-table .col-total { width: 110px; }
    #vue-details-tab-edit .products-table .col-action { width: 40px; }

    #vue-details-tab-edit .products-table th.is-num,
    #vue-details-tab-edit .products-table td.is-num {
        text-align: right;
    }

    #vue-details-tab-edit .products-table th.is-center,
    #vue-details-tab-edit .products-table td.is-center {
        text-align: center;
    }

    #vue-details-tab-edit .products-table .form-group {
        margin: 0 !important;
        float: none !important;
        width: 100% !important;
        max-width: none !important;
        padding: 0 !important;
        position: relative;
    }

    #vue-details-tab-edit .products-table .line-input,
    #vue-details-tab-edit .products-table .form-control,
    #vue-details-tab-edit .products-table input[type="number"],
    #vue-details-tab-edit .products-table input[type="text"] {
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

    #vue-details-tab-edit .products-table .line-input.is-num {
        text-align: right;
        font-variant-numeric: tabular-nums;
        padding-right: 12px !important;
    }

    /* Hide spin buttons so number values fit cleanly */
    #vue-details-tab-edit .products-table input[type="number"]::-webkit-outer-spin-button,
    #vue-details-tab-edit .products-table input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    #vue-details-tab-edit .products-table input[type="number"] {
        -moz-appearance: textfield;
        appearance: textfield;
    }

    #vue-details-tab-edit .products-table .custom-table-select .select2-container,
    #vue-details-tab-edit .products-table .select2-container {
        width: 100% !important;
        min-width: 0 !important;
        max-width: none !important;
        height: 40px !important;
        padding: 0 8px !important;
        box-sizing: border-box !important;
        background: #F0F0F3 !important;
        border-radius: 12px !important;
    }

    #vue-details-tab-edit .products-table .select2-selection--single {
        height: 40px !important;
        border: none !important;
        background: transparent !important;
        outline: none !important;
    }

    #vue-details-tab-edit .products-table .select2-selection__rendered {
        line-height: 40px !important;
        padding-left: 4px !important;
        padding-right: 24px !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #1D1D1D !important;
        font-size: 13px !important;
    }

    #vue-details-tab-edit .products-table .select2-selection__arrow {
        top: 7px !important;
        right: 4px !important;
        height: 26px !important;
    }

    #vue-details-tab-edit .products-table .row-remove {
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

    #vue-details-tab-edit .products-table .row-remove:hover {
        background: #D0342C;
        color: #fff;
    }
</style>

<div id="vue-details-tab-edit" class="purchase-edit has-vue-form brw-form-la">
    <template>
        <form class="voco-ajax-form"
            method="post"
            v-bind:action="urls.form_save_url"
            data-hook-before-send="validatePurchaseBeforeSend">
            @csrf
            <input type="hidden" name="purchase_id" v-model="purchase_id">
            <input type="hidden" name="tab_name" v-model="tab_name">
            <input type="hidden" name="product_rows" :value="JSON.stringify(product_rows)">
            <input type="hidden" name="payment_rows" :value="JSON.stringify(payment_rows)">
            <input type="hidden" name="auth_password" v-model="auth_password">
            <input type="hidden" name="edit_reason" v-model="edit_reason">

            <div class="purchase-password-overlay" v-if="showPasswordConfirm">
                <div class="purchase-password-card">
                    <h6>{{ __('Confirm purchase update') }}</h6>
                    <p class="hint">
                        {{ __('Enter an edit reason and your login password to update this purchase. This cannot be skipped.') }}
                    </p>
                    <label class="field-label" for="purchase_edit_reason">
                        {{ __('Edit Reason') }} <span class="text-danger">*</span>
                    </label>
                    <textarea id="purchase_edit_reason"
                        class="form-control"
                        rows="3"
                        maxlength="1000"
                        v-model="edit_reason"
                        placeholder="{{ __('Why is this purchase being updated?') }}"></textarea>
                    <small class="field-help is-danger" v-if="reasonError">@{{ reasonError }}</small>
                    <label class="field-label mt-3" for="purchase_auth_password">
                        {{ __('Your Password') }} <span class="text-danger">*</span>
                    </label>
                    <input id="purchase_auth_password"
                        type="password"
                        class="form-control"
                        v-model="auth_password"
                        autocomplete="current-password"
                        placeholder="{{ __('Password') }}"
                        v-on:keyup.enter="confirmPasswordAndSave">
                    <small class="field-help is-danger" v-if="passwordError">@{{ passwordError }}</small>
                    <div class="purchase-password-actions">
                        <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                            v-on:click="cancelPasswordConfirm">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-primary btn_dark-x"
                            v-on:click="confirmPasswordAndSave"
                            :disabled="passwordSubmitting">
                            <i class="fa fa-lock"></i> {{ __('Confirm & Save') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="edit-section">
                <div class="edit-section-title">{{ __('Purchase Details') }}</div>
                <p class="edit-hint">{{ __('Supplier, date, invoice, and short description') }}</p>
                <div class="row align-items-end">
                    <div class="form-group col-12 col-md-6">
                        <label for="purchase_ref_no">{{ __('Purchase Ref.No') }}</label>
                        <input type="text" class="form-control payment-input" id="purchase_ref_no"
                            :value="stock_purchase_info.purchase_ref_no" readonly>
                    </div>

                    <vcinput-select2 v-model="stock_purchase_info.vendor_id" id="vendor_id"
                        label="{{ __('Supplier Name') }}" style="width: 100%"
                        v-bind:options="dropdowns.vendors"></vcinput-select2>

                    <vcinput-datepicker v-model="stock_purchase_info.purchase_date" id="purchase_date"
                        label="{{ __('Purchase Date') }}"></vcinput-datepicker>

                    <vcinput-basic v-model="stock_purchase_info.purchase_invoice_no" id="purchase_invoice_no"
                        label="{{ __('Invoice Number') }}">
                    </vcinput-basic>

                    <vcinput-textarea v-model="stock_purchase_info.purchase_description" id="purchase_description"
                        label="{{ __('Short Description') }}" rows="2"></vcinput-textarea>
                </div>
            </div>

            <div class="edit-section">
                <div class="edit-section-title">{{ __('Products') }}</div>
                <p class="edit-hint">{{ __('Type the rate including tax from the bill. Rate is the taxable price. Amount is that rate after discount. GST is added below, then rounded to the nearest rupee.') }}</p>
                <div class="products-wrap">
                    <table class="table brownz-table-list-v1 products-table">
                        <thead>
                            <tr>
                                <th class="col-sl is-center">#</th>
                                <th class="col-product">{{ __('Product') }}</th>
                                <th class="col-hsn">{{ __('HSN/SAC') }}</th>
                                <th class="col-qty is-num">{{ __('Qty') }}</th>
                                <th class="col-incl is-num">{{ __('Rate (Incl. of tax)') }}</th>
                                <th class="col-cost is-num">{{ __('Rate') }}</th>
                                <th class="col-per">{{ __('per') }}</th>
                                <th class="col-tax is-num">{{ __('Tax %') }}</th>
                                <th class="col-discount is-num">{{ __('Disc. %') }}</th>
                                <th class="col-total is-num">{{ __('Amount') }}</th>
                                <th class="col-action"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, index) in product_rows" :key="index">
                                <td class="is-center">@{{ index + 1 }}</td>
                                <td>
                                    <vcinput-select2 class="custom-table-select" colclass="mb-0"
                                        v-model="row.item_id"
                                        :id="'stock_item_' + index" :options="dropdowns.stock_items"
                                        placeholder="{{ __('Select product') }}" @input="onProductChange(row.item_id, index)">
                                    </vcinput-select2>
                                    <small class="text-muted" v-if="packNote(row)">@{{ packNote(row) }}</small>
                                </td>
                                <td>
                                    <input type="text" class="form-control line-input" v-model="row.hsn_code" maxlength="20">
                                </td>
                                <td class="is-num">
                                    <input type="number" class="form-control line-input is-num" v-model="row.quantity" min="0" step="0.01">
                                </td>
                                <td class="is-num">
                                    <input type="number" class="form-control line-input is-num" v-model="row.rate_incl_tax" min="0" step="0.01" @input="onInclusiveRateChange(row, $event)">
                                </td>
                                <td class="is-num">
                                    <input type="number" class="form-control line-input is-num" v-model="row.cost_price" min="0" step="0.01" @input="onTaxableRateChange(row, $event)">
                                </td>
                                <td>@{{ uomName(row) }}</td>
                                <td class="is-num">
                                    <input type="number" class="form-control line-input is-num" v-model="row.tax" min="0" step="0.01" @input="onTaxChange(row, $event)">
                                </td>
                                <td class="is-num">
                                    <input type="number" class="form-control line-input is-num" v-model="row.discount_percent" min="0" max="100" step="0.01" @input="onDiscountInput(row)">
                                </td>
                                <td class="is-num">
                                    <input type="text" class="form-control line-input is-num" :value="lineTaxable(row).toFixed(2)" readonly>
                                </td>
                                <td class="is-center">
                                    <a v-if="product_rows.length > 1" href="javascript:void(0)" class="row-remove"
                                        @click="removeRow(index)" title="{{ __('Remove') }}">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <a href="javascript:void(0)" class="add-row-link" @click="addRow">
                    <i class="fa fa-plus"></i> {{ __('Add Product') }}
                </a>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="edit-section mb-0">
                        <div class="edit-section-title">{{ __('Payments') }}</div>
                        <p class="edit-hint">{{ __('Paid amount must match grand total') }}</p>
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
                                                :disabled="row.disabled"
                                                @change="onPaymentAmountChange(index)">
                                        </td>
                                        <td class="border-0">
                                            <select class="form-control payment-input"
                                                v-model="row.payment_mode" :disabled="row.disabled">
                                                {{ \Facades\App\Models\PurchasePayment::paymentModeDropdown() }}
                                            </select>
                                        </td>
                                        <td class="border-0 text-center" style="vertical-align: middle;">
                                            <a v-if="!row.disabled && payment_rows.length > 1" href="javascript:void(0)"
                                                class="text-danger" @click="removeRow1(index)">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="javascript:void(0)" class="add-row-link" @click="addRow1">
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
                        <div class="totals-row" v-if="!isGstDocument">
                            <span>{{ __('Sub total') }}</span><span>@{{ subtotal.toFixed(2) }}</span>
                        </div>
                        <div class="totals-row" v-if="!isGstDocument">
                            <span>{{ __('Tax Total') }}</span><span>@{{ taxTotal.toFixed(2) }}</span>
                        </div>
                        <div class="totals-row" v-if="!isGstDocument">
                            <span>{{ __('Discount Total') }}</span><span>@{{ discountTotal.toFixed(2) }}</span>
                        </div>
                        <div class="totals-row">
                            <span>{{ __('Freight') }}</span>
                            <input type="number" name="freight_amount" class="freight-input"
                                v-model="stock_purchase_info.freight_amount" min="0" step="0.01">
                        </div>
                        <div class="totals-row">
                            <span>{{ __('Freight tax %') }}</span>
                            <input type="number" name="freight_tax_percent" class="freight-input"
                                v-model="stock_purchase_info.freight_tax_percent" min="0" max="100" step="0.01">
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
                        <div class="totals-row is-grand">
                            <span>{{ __('Grand Total') }}</span><span>@{{ grandTotal.toFixed(2) }}</span>
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
                    :disabled="!isTotalPaidEqualToGrandTotal">
                    <i class="fa fa-check"></i> {{ __('Save') }}
                </button>
            </div>
        </form>
    </template>
</div>

<script>
    var vc_moment_date_offset = new Date("2021-10-05T17:28:50.403+03:00").getTime() - Date.now();
    var vc_jscalendar_moment_format = "DD/MM/YYYY";
    var vue_details_tab_edit = null;

    window.validatePurchaseBeforeSend = function () {
        var app = vue_details_tab_edit;
        if (!app) {
            return false;
        }

        if (!app.isTotalPaidEqualToGrandTotal) {
            if (typeof js_notify === 'function') {
                js_notify('{{ __('Paid amount must equal grand total.') }}', 'error');
            }
            return false;
        }

        if (app.isEditMode) {
            var password = String(app.auth_password || '').trim();
            var reason = String(app.edit_reason || '').trim();
            if (!password || !reason) {
                app.passwordError = '';
                app.reasonError = '';
                app.showPasswordConfirm = true;
                app.$nextTick(function () {
                    var reasonInput = document.getElementById('purchase_edit_reason');
                    var passwordInput = document.getElementById('purchase_auth_password');
                    if (!reason && reasonInput) {
                        reasonInput.focus();
                    } else if (passwordInput) {
                        passwordInput.focus();
                    }
                });
                return false;
            }
        }

        return true;
    };

    jQuery(function($) {
        vue_details_tab_edit = new Vue({
            el: "#vue-details-tab-edit",
            data: function() {
                return Object.assign(
                    <?php echo json_encode($details_edit_vue); ?>, {
                        product_rows: <?php echo json_encode(
                            $details_edit_vue['product_rows'] ?? [
                                [
                                    'purchase_item_id' => null,
                                    'item_id' => null,
                                    'hsn_code' => '',
                                    'quantity' => 1,
                                    'rate_incl_tax' => 0,
                                    'cost_price' => 0,
                                    'tax' => 0,
                                    'discount_percent' => 0,
                                    'discount' => 0,
                                    'pricing_mode' => 'gst',
                                    'total' => 0,
                                    'errorMessage'=> '',
                                ],
                            ],
                        ); ?>,
                        payment_rows: <?php echo json_encode(
                            $details_edit_vue['payment_rows'] ?? [
                                [
                                    'payment_id' => null,
                                    'amount_taken' => 0.0,
                                    'payment_mode' => 'cash',
                                    'disabled' => false,
                                ],
                            ],
                        ); ?>,
                        remainingAmount: 0,
                        auth_password: '',
                        edit_reason: '',
                        showPasswordConfirm: false,
                        passwordError: '',
                        reasonError: '',
                        passwordSubmitting: false,
                    }
                );
            },
            computed: {
                isEditMode: function () {
                    return parseInt(this.purchase_id, 10) > 0;
                },
                isGstDocument: function () {
                    return this.product_rows.some(function (row) {
                        return row.pricing_mode !== 'legacy';
                    });
                },
                subtotal: function () {
                    var self = this;
                    return this.product_rows.reduce(function (sum, row) {
                        return self.roundMoney(sum + self.lineGross(row));
                    }, 0);
                },
                taxableTotal: function () {
                    var self = this;
                    return this.product_rows.reduce(function (sum, row) {
                        return self.roundMoney(sum + self.lineTaxable(row));
                    }, 0);
                },
                taxTotal: function () {
                    var self = this;
                    return this.product_rows.reduce(function (sum, row) {
                        return self.roundMoney(sum + self.lineTax(row));
                    }, 0);
                },
                discountTotal: function () {
                    var self = this;
                    return this.product_rows.reduce(function (sum, row) {
                        return self.roundMoney(sum + self.lineDiscount(row));
                    }, 0);
                },
                cgstTotal: function () {
                    return this.roundMoney(this.combinedTax / 2);
                },
                sgstTotal: function () {
                    return this.roundMoney(this.combinedTax - this.cgstTotal);
                },
                freightAmount: function () {
                    return this.roundMoney(this.stock_purchase_info && this.stock_purchase_info.freight_amount);
                },
                freightTaxPercent: function () {
                    var percent = parseFloat(this.stock_purchase_info && this.stock_purchase_info.freight_tax_percent) || 0;
                    if (percent < 0) {
                        percent = 0;
                    }
                    if (percent > 100) {
                        percent = 100;
                    }
                    return percent;
                },
                freightTax: function () {
                    return this.roundMoney(this.freightAmount * this.freightTaxPercent / 100);
                },
                combinedTax: function () {
                    return this.roundMoney(this.taxTotal + this.freightTax);
                },
                exactTotal: function () {
                    if (!this.isGstDocument) {
                        return this.roundMoney(this.subtotal - this.discountTotal + this.freightAmount + this.combinedTax);
                    }
                    return this.roundMoney(this.taxableTotal + this.freightAmount + this.combinedTax);
                },
                grandTotal: function () {
                    if (!this.isGstDocument) {
                        return this.exactTotal;
                    }
                    return Math.round(this.exactTotal);
                },
                roundOff: function () {
                    return this.roundMoney(this.grandTotal - this.exactTotal);
                },
                isTotalPaidEqualToGrandTotal: function () {
                    var totalPaid = this.payment_rows.reduce(function (sum, row) {
                        return sum + (parseFloat(row.amount_taken) || 0);
                    }, 0);
                    return Math.abs(totalPaid - this.grandTotal) < 0.01;
                },
            },
            methods: {
                cancelPasswordConfirm: function () {
                    this.showPasswordConfirm = false;
                    this.auth_password = '';
                    this.edit_reason = '';
                    this.passwordError = '';
                    this.reasonError = '';
                    this.passwordSubmitting = false;
                },
                confirmPasswordAndSave: function () {
                    var password = String(this.auth_password || '').trim();
                    var reason = String(this.edit_reason || '').trim();
                    this.passwordError = '';
                    this.reasonError = '';
                    if (!reason) {
                        this.reasonError = '{{ __('Please enter a reason for this edit.') }}';
                        var reasonInput = document.getElementById('purchase_edit_reason');
                        if (reasonInput) {
                            reasonInput.focus();
                        }
                        return;
                    }
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

                    var self = this;
                    this.$nextTick(function () {
                        self.passwordSubmitting = false;
                    });
                },
                addRow: function () {
                    this.product_rows.push({
                        purchase_item_id: null,
                        item_id: null,
                        hsn_code: '',
                        quantity: 1,
                        rate_incl_tax: 0,
                        cost_price: 0,
                        tax: 0,
                        discount_percent: 0,
                        discount: 0,
                        pricing_mode: 'gst',
                        total: 0,
                    });
                },
                removeRow: function (index) {
                    this.product_rows.splice(index, 1);
                },
                roundMoney: function (value) {
                    return parseFloat((parseFloat(value) || 0).toFixed(2));
                },
                getLastEditablePaymentIndex: function () {
                    for (var i = this.payment_rows.length - 1; i >= 0; i--) {
                        if (!this.payment_rows[i].disabled) {
                            return i;
                        }
                    }
                    return -1;
                },
                getTotalPaid: function () {
                    return this.payment_rows.reduce(function (sum, row) {
                        return sum + (parseFloat(row.amount_taken) || 0);
                    }, 0);
                },
                syncPaymentsToGrandTotal: function () {
                    var grandTotal = this.roundMoney(this.grandTotal);

                    if (this.payment_rows.length === 0) {
                        this.payment_rows.push({
                            payment_id: null,
                            amount_taken: grandTotal,
                            payment_mode: 'cash',
                            disabled: false,
                        });
                        this.remainingAmount = 0;
                        return;
                    }

                    if (this.payment_rows.length === 1 && !this.payment_rows[0].disabled) {
                        this.$set(this.payment_rows[0], 'amount_taken', grandTotal);
                        this.remainingAmount = 0;
                        return;
                    }

                    var lastEditableIndex = this.getLastEditablePaymentIndex();
                    if (lastEditableIndex >= 0) {
                        var sumExceptLast = this.payment_rows.reduce(function (sum, row, index) {
                            return index === lastEditableIndex ? sum : sum + (parseFloat(row.amount_taken) || 0);
                        }, 0);
                        var remainder = Math.max(0, this.roundMoney(grandTotal - sumExceptLast));
                        this.$set(this.payment_rows[lastEditableIndex], 'amount_taken', remainder);
                    }

                    this.remainingAmount = Math.max(0, this.roundMoney(grandTotal - this.getTotalPaid()));
                },
                onPaymentAmountChange: function (index) {
                    var row = this.payment_rows[index];
                    if (!row || row.disabled) {
                        return;
                    }

                    var amount = this.roundMoney(row.amount_taken);
                    var grandTotal = this.roundMoney(this.grandTotal);
                    var lastEditableIndex = this.getLastEditablePaymentIndex();

                    if (amount <= 0) {
                        this.syncPaymentsToGrandTotal();
                        return;
                    }

                    if (index === lastEditableIndex && amount < grandTotal - 0.009) {
                        this.$set(this.payment_rows[index], 'amount_taken', amount);
                        this.$set(this.payment_rows[index], 'disabled', true);

                        var remainder = this.roundMoney(grandTotal - amount);
                        this.payment_rows.push({
                            payment_id: null,
                            amount_taken: remainder,
                            payment_mode: row.payment_mode || 'cash',
                            disabled: false,
                        });
                    } else if (index === lastEditableIndex) {
                        this.$set(this.payment_rows[index], 'amount_taken', amount);
                        this.syncPaymentsToGrandTotal();
                    }

                    this.remainingAmount = Math.max(0, this.roundMoney(grandTotal - this.getTotalPaid()));
                },
                addRow1: function () {
                    var lastEditableIndex = this.getLastEditablePaymentIndex();
                    if (lastEditableIndex >= 0) {
                        this.$set(this.payment_rows[lastEditableIndex], 'disabled', true);
                    }

                    var totalPaid = this.getTotalPaid();
                    this.remainingAmount = Math.max(0, this.roundMoney(this.grandTotal - totalPaid));

                    this.payment_rows.push({
                        payment_id: null,
                        amount_taken: this.remainingAmount,
                        payment_mode: 'cash',
                        disabled: false,
                    });
                },
                removeRow1: function (index) {
                    if (this.payment_rows.length <= 1) {
                        return;
                    }

                    this.payment_rows.splice(index, 1);

                    if (this.payment_rows.length === 1) {
                        this.$set(this.payment_rows[0], 'disabled', false);
                        this.$set(this.payment_rows[0], 'amount_taken', this.roundMoney(this.grandTotal));
                    } else {
                        for (var i = 0; i < this.payment_rows.length - 1; i++) {
                            this.$set(this.payment_rows[i], 'disabled', true);
                        }
                        this.$set(this.payment_rows[this.payment_rows.length - 1], 'disabled', false);
                        this.syncPaymentsToGrandTotal();
                    }

                    this.remainingAmount = Math.max(0, this.roundMoney(this.grandTotal - this.getTotalPaid()));
                },
                calculateTotal: function (row) {
                    return this.roundMoney(this.lineTaxable(row) + this.lineTax(row));
                },
                inputNumber: function (row, field, event) {
                    if (event && event.target) {
                        row[field] = event.target.value;
                    }
                },
                lineGross: function (row) {
                    var qty = parseFloat(row.quantity) || 0;
                    var rate = parseFloat(row.cost_price) || 0;
                    return this.roundMoney(qty * rate);
                },
                lineDiscount: function (row) {
                    if (row.pricing_mode === 'legacy') {
                        return this.roundMoney(row.discount);
                    }
                    return this.roundMoney(this.lineGross(row) - this.lineTaxable(row));
                },
                lineTaxable: function (row) {
                    var qty = parseFloat(row.quantity) || 0;
                    var rate = parseFloat(row.cost_price) || 0;
                    if (row.pricing_mode === 'legacy') {
                        return this.roundMoney((qty * rate) - (parseFloat(row.discount) || 0));
                    }
                    var disc = parseFloat(row.discount_percent) || 0;
                    return this.roundMoney(qty * rate * (1 - (disc / 100)));
                },
                lineTax: function (row) {
                    var tax = parseFloat(row.tax) || 0;
                    if (row.pricing_mode === 'legacy') {
                        return this.roundMoney(this.lineGross(row) * tax / 100);
                    }
                    return this.roundMoney(this.lineTaxable(row) * tax / 100);
                },
                selectedItem: function (row) {
                    return (this.dropdowns.stock_items || []).find(function (item) {
                        return String(item.id) === String(row.item_id);
                    });
                },
                uomName: function (row) {
                    var selected = this.selectedItem(row);
                    return selected && selected.uom_name ? selected.uom_name : '';
                },
                activateGstPricing: function (row) {
                    if (row.pricing_mode !== 'legacy') {
                        return;
                    }
                    var gross = this.lineGross(row);
                    var discount = parseFloat(row.discount) || 0;
                    if (!(parseFloat(row.discount_percent) > 0) && gross > 0) {
                        row.discount_percent = this.roundMoney((discount / gross) * 100);
                    }
                    row.pricing_mode = 'gst';
                },
                onInclusiveRateChange: function (row, event) {
                    this.inputNumber(row, 'rate_incl_tax', event);
                    this.activateGstPricing(row);
                    var incl = parseFloat(row.rate_incl_tax) || 0;
                    var tax = parseFloat(row.tax) || 0;
                    var divisor = 1 + (tax / 100);
                    row.cost_price = divisor > 0 ? this.roundMoney(incl / divisor) : this.roundMoney(incl);
                },
                onTaxableRateChange: function (row, event) {
                    this.inputNumber(row, 'cost_price', event);
                    this.activateGstPricing(row);
                    var rate = parseFloat(row.cost_price) || 0;
                    var tax = parseFloat(row.tax) || 0;
                    row.rate_incl_tax = this.roundMoney(rate * (1 + (tax / 100)));
                },
                onTaxChange: function (row, event) {
                    this.inputNumber(row, 'tax', event);
                    this.activateGstPricing(row);
                    if ((parseFloat(row.rate_incl_tax) || 0) > 0) {
                        var incl = parseFloat(row.rate_incl_tax) || 0;
                        var tax = parseFloat(row.tax) || 0;
                        var divisor = 1 + (tax / 100);
                        row.cost_price = divisor > 0 ? this.roundMoney(incl / divisor) : this.roundMoney(incl);
                        return;
                    }
                    this.onTaxableRateChange(row);
                },
                onDiscountInput: function (row) {
                    this.activateGstPricing(row);
                },
                packNote: function (row) {
                    var selected = this.selectedItem(row);
                    if (!selected) {
                        return '';
                    }
                    var pack = Number(selected.pack_size || 1);
                    var qty = Number(row.quantity || 0);
                    var uom = selected.uom_name || '';
                    if (!(pack > 1)) {
                        return uom ? ('{{ __('Stock unit') }}: ' + uom) : '';
                    }
                    var base = Math.round(qty * pack * 1000) / 1000;
                    return '{{ __('Stock in') }}: ' + base + (uom ? ' ' + uom : '') + ' (' + qty + ' × ' + pack + ')';
                },
                onProductChange: function (stockItemId, index) {
                    var selected = this.dropdowns.stock_items.find(function (item) {
                        return String(item.id) === String(stockItemId);
                    });

                    if (selected) {
                        var rate = parseFloat(selected.cost_price) || 0;
                        var tax = parseFloat(selected.tax) || 0;
                        this.$set(this.product_rows, index, {
                            ...this.product_rows[index],
                            hsn_code: selected.hsn_code || '',
                            cost_price: rate,
                            tax: tax,
                            rate_incl_tax: this.roundMoney(rate * (1 + (tax / 100))),
                            pricing_mode: 'gst'
                        });
                    } else {
                        this.$set(this.product_rows, index, {
                            ...this.product_rows[index],
                            hsn_code: '',
                            cost_price: 0,
                            tax: 0,
                            rate_incl_tax: 0,
                            pricing_mode: 'gst'
                        });
                    }
                },
                initializePaymentRows: function () {
                    if (this.payment_rows.length === 0) {
                        this.syncPaymentsToGrandTotal();
                        return;
                    }

                    for (var i = 0; i < this.payment_rows.length; i++) {
                        if (typeof this.payment_rows[i].disabled === 'undefined') {
                            this.$set(this.payment_rows[i], 'disabled', i < this.payment_rows.length - 1);
                        }
                        this.$set(
                            this.payment_rows[i],
                            'amount_taken',
                            this.roundMoney(this.payment_rows[i].amount_taken)
                        );
                    }

                    this.syncPaymentsToGrandTotal();
                },
            },
            watch: {
                grandTotal: function () {
                    this.syncPaymentsToGrandTotal();
                },
            },
            mounted: function () {
                if (this.product_rows.length === 0) {
                    this.addRow();
                }
                this.initializePaymentRows();
            }
        });

        $(document).off('ajaxError.purchaseEditPassword').on('ajaxError.purchaseEditPassword', function (event, jqxhr) {
            if (!vue_details_tab_edit || !vue_details_tab_edit.isEditMode) {
                return;
            }
            var errors = jqxhr.responseJSON && jqxhr.responseJSON.errors ? jqxhr.responseJSON.errors : {};
            var message = (jqxhr.responseJSON && (
                jqxhr.responseJSON.message ||
                (errors.auth_password && errors.auth_password[0]) ||
                (errors.edit_reason && errors.edit_reason[0])
            )) || '';
            var isPasswordError = /password/i.test(message) || !!errors.auth_password;
            var isReasonError = /reason/i.test(message) || !!errors.edit_reason;
            if (isPasswordError || isReasonError) {
                if (isPasswordError) {
                    vue_details_tab_edit.auth_password = '';
                    vue_details_tab_edit.passwordError = message || '{{ __('Incorrect password.') }}';
                }
                if (isReasonError) {
                    vue_details_tab_edit.reasonError = (errors.edit_reason && errors.edit_reason[0])
                        || message
                        || '{{ __('Please enter a reason for this edit.') }}';
                }
                vue_details_tab_edit.showPasswordConfirm = true;
                vue_details_tab_edit.$nextTick(function () {
                    var focusId = isReasonError && !isPasswordError
                        ? 'purchase_edit_reason'
                        : 'purchase_auth_password';
                    var input = document.getElementById(focusId);
                    if (input) {
                        input.focus();
                    }
                });
            }
        });
    });
</script>
