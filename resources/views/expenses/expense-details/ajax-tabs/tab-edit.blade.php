<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
    #vue-details-tab-edit.expense-edit-form {
        margin: -0.25rem -0.15rem 0;
    }

    #vue-details-tab-edit .expense-edit-shell {
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    #vue-details-tab-edit .expense-edit-header {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #fff;
        border-bottom: 1px solid #ececf0;
        padding: 0 0 14px;
        margin-bottom: 14px;
    }

    #vue-details-tab-edit .expense-edit-title {
        margin: 0 0 4px;
        color: #1D1D1D;
        font-size: 1.15rem;
        font-weight: 600;
    }

    #vue-details-tab-edit .expense-edit-hint {
        margin: 0;
        font-size: 12px;
        color: #6c757d;
    }

    #vue-details-tab-edit .balance-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }

    #vue-details-tab-edit .balance-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 8px 12px;
        background: #F0F0F3;
        font-size: 12px;
        font-weight: 600;
        color: #1D1D1D;
    }

    #vue-details-tab-edit .balance-pill .value {
        font-variant-numeric: tabular-nums;
    }

    #vue-details-tab-edit .balance-pill.is-cash .value {
        color: #199900;
    }

    #vue-details-tab-edit .balance-pill.is-bank .value {
        color: #1e6789;
    }

    #vue-details-tab-edit .balance-pill.is-active {
        background: #1D1D1D;
        color: #fff;
    }

    #vue-details-tab-edit .balance-pill.is-active .value {
        color: #E6CD7F;
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

    #vue-details-tab-edit .field-shell .form-control,
    #vue-details-tab-edit .field-shell .select2-container {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        width: 100% !important;
        min-height: 44px;
        padding-left: 0;
        padding-right: 0;
    }

    #vue-details-tab-edit .field-shell textarea.form-control {
        min-height: 88px;
        padding-top: 12px;
        padding-bottom: 12px;
        resize: vertical;
    }

    #vue-details-tab-edit .select2-container .select2-selection--single {
        height: 44px !important;
        border: none !important;
        background: transparent !important;
        display: flex;
        align-items: center;
    }

    #vue-details-tab-edit .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 44px !important;
        padding-left: 0 !important;
        color: #1D1D1D;
    }

    #vue-details-tab-edit .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px !important;
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

    #vue-details-tab-edit .pay-mode-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    #vue-details-tab-edit .pay-mode-card {
        border: 1px solid #ececf0;
        border-radius: 14px;
        background: #F7F7F9;
        padding: 12px 14px;
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
        margin-bottom: 0;
        display: block;
        position: relative;
    }

    #vue-details-tab-edit .pay-mode-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    #vue-details-tab-edit .pay-mode-card:hover {
        border-color: #d0d0d6;
    }

    #vue-details-tab-edit .pay-mode-card.is-selected {
        background: #1D1D1D;
        border-color: #1D1D1D;
        color: #fff;
    }

    #vue-details-tab-edit .pay-mode-card .mode-title {
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 2px;
    }

    #vue-details-tab-edit .pay-mode-card .mode-balance {
        font-size: 12px;
        opacity: 0.85;
        font-variant-numeric: tabular-nums;
    }

    #vue-details-tab-edit .pay-mode-card.is-selected .mode-balance {
        color: #E6CD7F;
        opacity: 1;
    }

    #vue-details-tab-edit .expense-edit-footer {
        position: sticky;
        bottom: 0;
        z-index: 2;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 18px;
        padding-top: 14px;
        background: #fff;
        border-top: 1px solid #ececf0;
    }

    /* Keep form actions visible inside ajax modal (global rule hides .vc-form-buttons) */
    #voco-ajax-modal #vue-details-tab-edit .expense-edit-footer.vc-form-buttons {
        display: flex !important;
    }

    #vue-details-tab-edit .expense-edit-footer .btn {
        min-width: 104px;
    }

    #vue-details-tab-edit .expense-password-overlay {
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

    #vue-details-tab-edit .expense-password-card {
        width: 100%;
        max-width: 420px;
        background: #fff;
        border-radius: 16px;
        padding: 18px 18px 14px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.2);
    }

    #vue-details-tab-edit .expense-password-card h6 {
        margin: 0 0 4px;
        color: #1D1D1D;
        font-size: 1.05rem;
        font-weight: 600;
    }

    #vue-details-tab-edit .expense-password-card .hint {
        margin: 0 0 14px;
        color: #6c757d;
        font-size: 12px;
    }

    #vue-details-tab-edit .expense-password-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 14px;
    }

    #vue-details-tab-edit.has-vue-form {
        position: relative;
    }

    @media (max-width: 575.98px) {
        #vue-details-tab-edit .pay-mode-cards {
            grid-template-columns: 1fr;
        }

        #vue-details-tab-edit .expense-edit-footer {
            flex-direction: column-reverse;
            align-items: stretch;
        }
    }
</style>

<div id="vue-details-tab-edit" class="has-vue-form brw-form-la expense-edit-form">
    <template>
        <form class="voco-ajax-form expense-edit-shell" method="post" v-bind:action="urls.form_save_url"
            data-hook-before-send="validateExpensePaymentBalance">
            @csrf
            <input type="hidden" name="expense_id" v-model="expense_id">
            <input type="hidden" name="tab_name" v-model="tab_name">
            <input type="hidden" name="auth_password" v-model="auth_password">

            <div class="expense-password-overlay" v-if="showPasswordConfirm">
                <div class="expense-password-card">
                    <h6>{{ __('Confirm with password') }}</h6>
                    <p class="hint">
                        {{ __('Enter your login password to update this expense. This cannot be skipped.') }}
                    </p>

                    <label class="field-label" for="expense_auth_password">{{ __('Your Password') }}</label>
                    <div class="field-shell">
                        <input id="expense_auth_password"
                            type="password"
                            class="form-control"
                            v-model="auth_password"
                            autocomplete="current-password"
                            placeholder="{{ __('Password') }}"
                            v-on:keyup.enter="confirmPasswordAndSave">
                    </div>
                    <small class="field-help is-danger" v-if="passwordError">@{{ passwordError }}</small>

                    <div class="expense-password-actions">
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

            <div class="expense-edit-header">
                <h5 class="expense-edit-title">
                    @{{ isEditMode ? 'Edit Expense' : 'Create Expense' }}
                </h5>
                <p class="expense-edit-hint">
                    Choose the expense account, payment source, amount, and date.
                </p>

                <div class="balance-pills">
                    <span class="balance-pill is-cash"
                        :class="{ 'is-active': isPayMode('cash') }">
                        <span>Cash available</span>
                        <span class="value">@{{ formatMoney(displayCashBalance) }}</span>
                    </span>
                    <span class="balance-pill is-bank"
                        :class="{ 'is-active': isPayMode('bank') }">
                        <span>Bank available</span>
                        <span class="value">@{{ formatMoney(displayBankBalance) }}</span>
                    </span>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-6 mb-3">
                    <label class="field-label" for="account_id">Account Name</label>
                    <div class="field-shell">
                        <select v-model="expense_info.account_id" class="form-control voco-select2" id="account_id"
                            name="account_id" data-placeholder="Select Account">
                            <option value="">-- Select --</option>
                            <option v-for="acc in accounts" :value="acc.account_id" :key="acc.account_id">
                                @{{ acc.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-6 mb-3">
                    <label class="field-label">Pay From</label>
                    <div class="pay-mode-cards">
                        <label class="pay-mode-card"
                            :class="{ 'is-selected': isPayMode('cash') }">
                            <input class="pay-mode-input"
                                type="radio"
                                name="account_type"
                                value="cash"
                                v-model="expense_info.account_type">
                            <div class="mode-title">Cash</div>
                            <div class="mode-balance">@{{ formatMoney(displayCashBalance) }}</div>
                        </label>
                        <label class="pay-mode-card"
                            :class="{ 'is-selected': isPayMode('bank') }">
                            <input class="pay-mode-input"
                                type="radio"
                                name="account_type"
                                value="bank"
                                v-model="expense_info.account_type">
                            <div class="mode-title">Bank</div>
                            <div class="mode-balance">@{{ formatMoney(displayBankBalance) }}</div>
                        </label>
                    </div>
                    <small class="field-help" v-if="!expense_info.account_type">
                        Select Cash or Bank to pay this expense.
                    </small>
                    <small class="field-help is-danger" v-if="amountExceedsBalance">
                        Amount exceeds available @{{ paymentAccountLabel }} balance.
                    </small>
                </div>

                <div class="form-group col-md-6 mb-3">
                    <label class="field-label" for="amount">{{ __('Amount') }}</label>
                    <div class="field-shell">
                        <input type="number" class="form-control" id="amount" name="amount"
                            v-model="expense_info.amount" step="0.01" min="0.01" required
                            :max="availablePaymentBalance > 0 ? availablePaymentBalance : null"
                            placeholder="0.00">
                    </div>
                    <small class="field-help" v-if="expense_info.account_type">
                        Max payable from @{{ paymentAccountLabel }}:
                        <strong>@{{ formatMoney(availablePaymentBalance) }}</strong>
                    </small>
                </div>

                <div class="form-group col-md-6 mb-3">
                    <label class="field-label" for="date">{{ __('Date') }}</label>
                    <div class="field-shell">
                        <input type="date" class="form-control" name="date" v-model="expense_info.date" id="date" required>
                    </div>
                </div>

                <div class="form-group col-12 mb-2">
                    <label class="field-label" for="short_note">{{ __('Short Note') }}</label>
                    <div class="field-shell">
                        <textarea id="short_note"
                            name="short_note"
                            class="form-control"
                            v-model="expense_info.short_note"
                            rows="3"
                            maxlength="500"
                            placeholder="{{ __('Optional note for this expense') }}"></textarea>
                    </div>
                </div>
            </div>

            <div class="expense-edit-footer vc-form-buttons">
                <a class="btn btn-secondary btn-sm vc-form-btn-cancel brownz-btn-yellow-d"
                    href="javascript:void(0)"
                    v-on:click.prevent="cancelForm">
                    <i class="fa fa-undo"></i> {{ __('Cancel') }}
                </a>

                <button type="submit" class="btn btn-primary btn-sm btn_dark-x" :disabled="amountExceedsBalance">
                    <i class="fa" :class="isEditMode ? 'fa-lock' : 'fa-check'"></i>
                    @{{ isEditMode ? 'Save Changes' : 'Save' }}
                </button>
            </div>
        </form>
    </template>
</div>

<script>
    var vue_details_tab_edit = null;

    window.validateExpensePaymentBalance = function ($form) {
        if (!vue_details_tab_edit) {
            return true;
        }

        var app = vue_details_tab_edit;
        var mode = String(app.expense_info.account_type || '').toLowerCase();
        var amount = parseFloat(app.expense_info.amount || 0);

        if (!mode || !(amount > 0)) {
            // still allow required HTML/required validation to handle empty amount
        } else {
            var available = app.availablePaymentBalance;
            if (amount > available + 0.0001) {
                var label = mode === 'cash' ? 'Cash' : 'Bank';
                js_notify(
                    label + ' has insufficient balance. Available: ' +
                    Number(available).toFixed(2) + ', Required: ' + Number(amount).toFixed(2),
                    'error'
                );
                return false;
            }
        }

        // Existing expense updates require password confirmation
        if (app.isEditMode) {
            var password = String(app.auth_password || '').trim();
            if (!password) {
                app.passwordError = '';
                app.showPasswordConfirm = true;
                app.$nextTick(function () {
                    var input = document.getElementById('expense_auth_password');
                    if (input) {
                        input.focus();
                    }
                });
                return false;
            }
        }

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
                if (!editData.expense_info || typeof editData.expense_info !== 'object') {
                    editData.expense_info = {};
                }
                // Ensure reactive pay-from field for Vue 2 (new expenses may omit account_type)
                if (
                    editData.expense_info.account_type === undefined ||
                    editData.expense_info.account_type === null
                ) {
                    editData.expense_info.account_type = '';
                } else {
                    editData.expense_info.account_type = String(editData.expense_info.account_type)
                        .toLowerCase();
                }
                editData.auth_password = '';
                editData.showPasswordConfirm = false;
                editData.passwordError = '';
                editData.passwordSubmitting = false;
                return editData;
            },
            computed: {
                isEditMode: function () {
                    return parseInt(this.expense_id, 10) > 0;
                },
                paymentAccountLabel: function () {
                    if (this.isPayMode('cash')) {
                        return 'Cash';
                    }
                    if (this.isPayMode('bank')) {
                        return 'Bank';
                    }
                    return '';
                },
                displayCashBalance: function () {
                    var balance = parseFloat(this.cash_balance || 0);
                    if (
                        parseInt(this.expense_id, 10) > 0 &&
                        String(this.original_account_type || '').toLowerCase() === 'cash'
                    ) {
                        balance += parseFloat(this.original_amount || 0);
                    }
                    return Math.max(0, Math.round(balance * 100) / 100);
                },
                displayBankBalance: function () {
                    var balance = parseFloat(this.bank_balance || 0);
                    if (
                        parseInt(this.expense_id, 10) > 0 &&
                        String(this.original_account_type || '').toLowerCase() === 'bank'
                    ) {
                        balance += parseFloat(this.original_amount || 0);
                    }
                    return Math.max(0, Math.round(balance * 100) / 100);
                },
                availablePaymentBalance: function () {
                    if (this.isPayMode('cash')) {
                        return this.displayCashBalance;
                    }
                    if (this.isPayMode('bank')) {
                        return this.displayBankBalance;
                    }
                    return 0;
                },
                amountExceedsBalance: function () {
                    var amount = parseFloat(this.expense_info.amount || 0);
                    if (!this.expense_info.account_type || !(amount > 0)) {
                        return false;
                    }
                    return amount > this.availablePaymentBalance + 0.0001;
                }
            },
            methods: {
                isPayMode: function (mode) {
                    return String(this.expense_info.account_type || '').toLowerCase() === String(mode || '').toLowerCase();
                },
                formatMoney: function (value) {
                    return Number(value || 0).toFixed(2);
                },
                cancelForm: function () {
                    var $modal = $('#vue-details-tab-edit').closest('#voco-ajax-modal');
                    if ($modal.length) {
                        $modal.modal('hide');
                        return;
                    }
                    if (this.urls && this.urls.button_cancel_url) {
                        window.location.href = this.urls.button_cancel_url;
                    }
                },
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

        // Clear password after failed ajax save so user must re-enter
        $(document).on('ajaxError.expenseEditPassword', function (event, jqxhr) {
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
                    var input = document.getElementById('expense_auth_password');
                    if (input) {
                        input.focus();
                    }
                });
            }
        });
    });
</script>
