<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
    #vue-details-tab-edit.voucher-edit {
        position: relative;
        padding: 4px 0 8px;
    }

    #vue-details-tab-edit .edit-section {
        background: #F7F7F9;
        border-radius: 14px;
        padding: 16px 16px 8px;
        margin-bottom: 14px;
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
        margin: 0 0 12px;
    }

    #vue-details-tab-edit .form-label-soft {
        font-size: 13px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 6px;
    }

    #vue-details-tab-edit .ledger-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        font-weight: 700;
        color: #1D1D1D;
    }

    #vue-details-tab-edit .ledger-total {
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        font-variant-numeric: tabular-nums;
    }

    #vue-details-tab-edit .balance-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        background: #fff;
        border-radius: 12px;
        padding: 10px 14px;
        margin-bottom: 14px;
    }

    #vue-details-tab-edit .balance-bar.is-ok {
        border: 1px solid rgba(25, 153, 0, 0.25);
    }

    #vue-details-tab-edit .balance-bar.is-bad {
        border: 1px solid rgba(208, 52, 44, 0.25);
    }

    #vue-details-tab-edit .vc-form-buttons {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    #voco-ajax-modal #vue-details-tab-edit .vc-form-buttons {
        display: flex !important;
    }

    #vue-details-tab-edit .row-action {
        cursor: pointer;
        display: inline-flex;
        margin-top: 8px;
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

    #vue-details-tab-edit .field-help.is-danger {
        color: #D0342C;
        font-weight: 600;
        font-size: 12px;
        margin-top: 6px;
        display: block;
    }

    #vue-details-tab-edit .voucher-password-overlay {
        position: fixed;
        inset: 0;
        z-index: 2000;
        background: rgba(29, 29, 29, 0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    #vue-details-tab-edit .voucher-password-card {
        width: 100%;
        max-width: 420px;
        max-height: calc(100vh - 32px);
        overflow-y: auto;
        background: #fff;
        border-radius: 16px;
        padding: 18px 18px 14px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.2);
    }

    #vue-details-tab-edit .voucher-password-card h6 {
        margin: 0 0 4px;
        color: #1D1D1D;
        font-size: 1.05rem;
        font-weight: 600;
    }

    #vue-details-tab-edit .voucher-password-card .hint {
        margin: 0 0 14px;
        color: #6c757d;
        font-size: 12px;
    }

    #vue-details-tab-edit .voucher-password-card textarea.form-control {
        min-height: 84px;
        resize: vertical;
    }

    #vue-details-tab-edit .voucher-password-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 14px;
    }

    #vue-details-tab-edit .ledger-account-select .select2-container {
        width: 100% !important;
    }

    #vue-details-tab-edit .ledger-account-select .select2-selection--single {
        min-height: 38px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    #vue-details-tab-edit .ledger-account-select .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
        color: #1D1D1D;
    }

    #vue-details-tab-edit .ledger-account-select .select2-selection__arrow {
        height: 36px;
    }

    #vue-details-tab-edit .ledger-row {
        align-items: center;
        margin-bottom: 10px;
    }

    #vue-details-tab-edit .ledger-row-head {
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 8px;
    }

    #vue-details-tab-edit .ledger-row-head .col-action {
        text-align: center;
    }

    #vue-details-tab-edit .ledger-amount-input,
    #vue-details-tab-edit .ledger-remarks-input {
        height: 38px;
        min-height: 38px;
    }

    #vue-details-tab-edit .ledger-amount-input {
        text-align: right;
        font-variant-numeric: tabular-nums;
        padding-right: 10px;
    }

    #vue-details-tab-edit .ledger-row .row-action {
        margin-top: 0;
        justify-content: center;
        width: 100%;
    }

    #vue-details-tab-edit .ledger-row .col-action {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #vue-details-tab-edit .ledger-balance-hint {
        display: block;
        margin-top: 4px;
        font-size: 11px;
        font-weight: 600;
        color: #6c757d;
        font-variant-numeric: tabular-nums;
        line-height: 1.45;
    }

    #vue-details-tab-edit .ledger-balance-hint.is-danger {
        color: #D0342C;
    }

    #vue-details-tab-edit .ledger-balance-hint.is-ok {
        color: #199900;
    }
</style>

<div id="vue-details-tab-edit" class="voucher-edit has-vue-form brw-form-la">
    <template>
        <form class="voco-ajax-form"
            method="post"
            v-bind:action="urls.form_save_url"
            data-hook-before-send="validateVoucherBeforeSend">
            @csrf
            <input type="hidden" name="id" v-model="id">
            <input type="hidden" name="tab_name" v-model="tab_name">
            <input type="hidden" name="sectionDr" :value="JSON.stringify(sectionDr)">
            <input type="hidden" name="sectionCr" :value="JSON.stringify(sectionCr)">
            <input type="hidden" name="auth_password" v-model="auth_password">
            <input type="hidden" name="edit_reason" v-model="edit_reason">
            <input type="hidden" name="commission_id" v-model="commission_id">
            <input type="hidden" name="commission_master_id" v-model="commission_master_id">
            <input type="hidden" name="incentive_id" v-model="incentive_id">
            <input type="hidden" name="incentive_master_id" v-model="incentive_master_id">
            <input type="hidden" name="payroll_id" v-model="payroll_id">
            <input type="hidden" name="payroll_master_id" v-model="payroll_master_id">
            <input type="hidden" name="vendor_id" v-model="vendor_id">

            <div class="alert alert-warning py-2 px-3 mb-3" v-if="vendor_prefill && vendor_prefill.error" style="border-radius: 12px;">
                <strong>{{ __('Vendor payment') }}:</strong>
                @{{ vendor_prefill.vendor_name }}
                · @{{ vendor_prefill.error }}
            </div>

            <div class="alert alert-info py-2 px-3 mb-3" v-else-if="vendor_prefill" style="border-radius: 12px;">
                <strong>{{ __('Vendor payment') }}:</strong>
                @{{ vendor_prefill.vendor_name }}
                · {{ __('Due') }}: @{{ Number(vendor_prefill.due_amount || 0).toFixed(2) }}
            </div>

            <div class="alert alert-info py-2 px-3 mb-3" v-if="commission_batch_prefill" style="border-radius: 12px;">
                <strong>{{ __('Commission batch payment') }}:</strong>
                @{{ commission_batch_prefill.batch_label }}
                · @{{ commission_batch_prefill.employees_count }} {{ __('employees') }}
                · {{ __('Due') }}: @{{ Number(commission_batch_prefill.due_amount || 0).toFixed(2) }}
            </div>

            <div class="alert alert-info py-2 px-3 mb-3" v-else-if="commission_prefill" style="border-radius: 12px;">
                <strong>{{ __('Commission payment') }}:</strong>
                @{{ commission_prefill.employee_name }}
                · {{ __('Due') }}: @{{ Number(commission_prefill.due_amount || 0).toFixed(2) }}
            </div>

            <div class="alert alert-info py-2 px-3 mb-3" v-if="incentive_batch_prefill" style="border-radius: 12px;">
                <strong>{{ __('Incentive batch payment') }}:</strong>
                @{{ incentive_batch_prefill.batch_label }}
                · @{{ incentive_batch_prefill.employees_count }} {{ __('employees') }}
                · {{ __('Due') }}: @{{ Number(incentive_batch_prefill.due_amount || 0).toFixed(2) }}
            </div>

            <div class="alert alert-info py-2 px-3 mb-3" v-else-if="incentive_prefill" style="border-radius: 12px;">
                <strong>{{ __('Incentive payment') }}:</strong>
                @{{ incentive_prefill.employee_name }}
                · {{ __('Due') }}: @{{ Number(incentive_prefill.due_amount || 0).toFixed(2) }}
            </div>

            <div class="alert alert-info py-2 px-3 mb-3" v-if="payroll_batch_prefill" style="border-radius: 12px;">
                <strong>{{ __('Payroll batch payment') }}:</strong>
                @{{ payroll_batch_prefill.batch_label }}
                · @{{ payroll_batch_prefill.employees_count }} {{ __('employees') }}
                · {{ __('Due') }}: @{{ Number(payroll_batch_prefill.due_amount || 0).toFixed(2) }}
            </div>

            <div class="alert alert-info py-2 px-3 mb-3" v-else-if="payroll_prefill" style="border-radius: 12px;">
                <strong>{{ __('Payroll payment') }}:</strong>
                @{{ payroll_prefill.employee_name }}
                · {{ __('Due') }}: @{{ Number(payroll_prefill.due_amount || 0).toFixed(2) }}
            </div>

            <div class="edit-section">
                <p class="edit-section-title">{{ __('Voucher Details') }}</p>
                <p class="edit-hint">{{ __('Set date, type, and narration for this voucher.') }}</p>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="form-label-soft" for="date">{{ __('Date') }}</label>
                        <input type="date" name="date" v-model="stock_voucher_info.date" id="date" class="form-control" required>
                    </div>

                    <div class="form-group col-md-4">
                        <label class="form-label-soft" for="voucher_type">{{ __('Voucher Type') }}</label>
                        <select v-model="stock_voucher_info.voucher_type" class="form-control"
                            id="voucher_type" name="voucher_type" required>
                            <option value="">-- Select --</option>
                            <option v-for="type in dropdowns.voucherTypeList" :value="type.id" :key="type.id">
                                @{{ type.text }}
                            </option>
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label class="form-label-soft" for="description">{{ __('Narration') }}</label>
                        <input type="text" name="description" v-model="stock_voucher_info.description"
                            id="description" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="edit-section">
                <div class="ledger-title">
                    <span>{{ __('Dr (Debit)') }}</span>
                    <span class="ledger-total">{{ __('Total') }}: @{{ totalDebit.toFixed(2) }}</span>
                </div>
                <div class="row ledger-row-head d-none d-md-flex">
                    <div class="col-md-4">{{ __('Account') }}</div>
                    <div class="col-md-3">{{ __('Amount') }}</div>
                    <div class="col-md-3">{{ __('Remarks') }}</div>
                    <div class="col-md-1 col-action"></div>
                    <div class="col-md-1 col-action"></div>
                </div>
                <div class="row ledger-row" v-for="(input, index) in sectionDr" :key="'sectionDrInput-' + index">
                    <div class="col-md-4 col-sm-12 mb-2 mb-md-0">
                        <vcinput-select2 class="ledger-account-select" colclass="mb-0"
                            v-model="input.account_id"
                            :id="'dr_account_' + index"
                            :options="dropdowns.accounts"
                            placeholder="{{ __('Select Account') }}"
                            style="width: 100%"
                            v-on:input="fetchAccountBalance($event)"
                            required>
                        </vcinput-select2>
                        <small class="ledger-balance-hint"
                            v-if="input.account_id && getStoredBalance(input.account_id) !== undefined"
                            :class="ledgerBalanceHintClass(input.account_id, 'debit', input.debit_amount)">
                            @{{ formatLedgerBalanceHint(input.account_id, 'debit', input.debit_amount) }}
                        </small>
                    </div>
                    <div class="col-md-3 col-sm-12 mb-2 mb-md-0">
                        <input type="number" step="0.01" min="0.01"
                            class="form-control ledger-amount-input"
                            v-model="input.debit_amount" placeholder="{{ __('Amount') }}"
                            v-on:input="differenceOfDebitAndCredit()">
                    </div>
                    <div class="col-md-3 col-sm-12 mb-2 mb-md-0">
                        <input type="text" class="form-control ledger-remarks-input"
                            v-model="input.remarks" placeholder="{{ __('Remarks') }}">
                    </div>
                    <div class="col-md-1 col-sm-6 col-action">
                        <span class="row-action" v-on:click="addFieldDr()" title="{{ __('Add debit row') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path fill="none" d="M0 0h24v24H0z" />
                                <path fill="green"
                                    d="M11 11V7h2v4h4v2h-4v4h-2v-4H7v-2h4zm1 11C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16z" />
                            </svg>
                        </span>
                    </div>
                    <div class="col-md-1 col-sm-6 col-action">
                        <span class="row-action" v-show="sectionDr.length > 1" v-on:click="removeFieldDr(index)"
                            title="{{ __('Remove debit row') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path fill="none" d="M0 0h24v24H0z" />
                                <path fill="#D0342C"
                                    d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm0-9.414l2.828-2.829 1.415 1.415L13.414 12l2.829 2.828-1.415 1.415L12 13.414l-2.828 2.829-1.415-1.415L10.586 12 7.757 9.172l1.415-1.415L12 10.586z" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            <div class="edit-section">
                <div class="ledger-title">
                    <span>{{ __('To (Cr / Credit)') }}</span>
                    <span class="ledger-total">{{ __('Total') }}: @{{ totalCredit.toFixed(2) }}</span>
                </div>
                <div class="row ledger-row-head d-none d-md-flex">
                    <div class="col-md-4">{{ __('Account') }}</div>
                    <div class="col-md-3">{{ __('Amount') }}</div>
                    <div class="col-md-3">{{ __('Remarks') }}</div>
                    <div class="col-md-1 col-action"></div>
                    <div class="col-md-1 col-action"></div>
                </div>
                <div class="row ledger-row" v-for="(input, index) in sectionCr" :key="'sectionCrInput-' + index">
                    <div class="col-md-4 col-sm-12 mb-2 mb-md-0">
                        <vcinput-select2 class="ledger-account-select" colclass="mb-0"
                            v-model="input.account_id"
                            :id="'cr_account_' + index"
                            :options="dropdowns.accounts"
                            placeholder="{{ __('Select Account') }}"
                            style="width: 100%"
                            v-on:input="fetchAccountBalance($event)"
                            required>
                        </vcinput-select2>
                        <small class="ledger-balance-hint"
                            v-if="input.account_id && getStoredBalance(input.account_id) !== undefined"
                            :class="ledgerBalanceHintClass(input.account_id, 'credit', input.credit_amount)">
                            @{{ formatLedgerBalanceHint(input.account_id, 'credit', input.credit_amount) }}
                        </small>
                    </div>
                    <div class="col-md-3 col-sm-12 mb-2 mb-md-0">
                        <input type="number" step="0.01" min="0.01"
                            class="form-control ledger-amount-input"
                            v-model="input.credit_amount" placeholder="{{ __('Amount') }}"
                            v-on:input="differenceOfDebitAndCredit()">
                    </div>
                    <div class="col-md-3 col-sm-12 mb-2 mb-md-0">
                        <input type="text" class="form-control ledger-remarks-input"
                            v-model="input.remarks" placeholder="{{ __('Remarks') }}">
                    </div>
                    <div class="col-md-1 col-sm-6 col-action">
                        <span class="row-action" v-on:click="addFieldCr()" title="{{ __('Add credit row') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path fill="none" d="M0 0h24v24H0z" />
                                <path fill="green"
                                    d="M11 11V7h2v4h4v2h-4v4h-2v-4H7v-2h4zm1 11C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16z" />
                            </svg>
                        </span>
                    </div>
                    <div class="col-md-1 col-sm-6 col-action">
                        <span class="row-action" v-show="sectionCr.length > 1" v-on:click="removeFieldCr(index)"
                            title="{{ __('Remove credit row') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path fill="none" d="M0 0h24v24H0z" />
                                <path fill="#D0342C"
                                    d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm0-9.414l2.828-2.829 1.415 1.415L13.414 12l2.829 2.828-1.415 1.415L12 13.414l-2.828 2.829-1.415-1.415L10.586 12 7.757 9.172l1.415-1.415L12 10.586z" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            <div class="balance-bar" :class="isBalanced ? 'is-ok' : 'is-bad'">
                <div>
                    <strong>{{ __('Difference') }}:</strong>
                    <span :class="{ 'text-danger': !isBalanced, 'text-success': isBalanced }">
                        @{{ Number(diff_amount).toFixed(2) }}
                    </span>
                </div>
                <small class="text-danger" v-if="!isBalanced">
                    {{ __('Debits and credits must be equal before saving.') }}
                </small>
                <small class="text-danger" v-else-if="hasInsufficientCashBank">
                    {{ __('Cash or bank balance is insufficient for this payment.') }}
                </small>
                <small class="text-success" v-else>
                    {{ __('Voucher is balanced.') }}
                </small>
            </div>

            <div class="vc-form-buttons">
                <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d"
                    v-bind:href="urls.button_cancel_url">
                    <i class="fa fa-undo"></i> {{ __('Cancel') }}
                </a>

                <button type="submit" class="btn btn-primary btn_dark-x" :disabled="!isBalanced || hasInsufficientCashBank">
                    <i class="fa" :class="isEditMode ? 'fa-lock' : 'fa-check'"></i>
                    @{{ isEditMode ? 'Save Changes' : 'Save' }}
                </button>
            </div>
        </form>

        <div class="voucher-password-overlay" v-if="showPasswordConfirm">
            <div class="voucher-password-card">
                <h6>{{ __('Confirm voucher update') }}</h6>
                <p class="hint">
                    {{ __('Enter an edit reason and your login password to update this voucher. This cannot be skipped.') }}
                </p>
                <label class="field-label" for="voucher_edit_reason">
                    {{ __('Edit Reason') }} <span class="text-danger">*</span>
                </label>
                <textarea id="voucher_edit_reason"
                    class="form-control"
                    rows="3"
                    maxlength="1000"
                    v-model="edit_reason"
                    placeholder="{{ __('Why is this voucher being updated?') }}"></textarea>
                <small class="field-help is-danger" v-if="reasonError">@{{ reasonError }}</small>
                <label class="field-label mt-3" for="voucher_auth_password">
                    {{ __('Your Password') }} <span class="text-danger">*</span>
                </label>
                <div class="field-shell">
                    <input id="voucher_auth_password"
                        type="password"
                        class="form-control"
                        v-model="auth_password"
                        autocomplete="current-password"
                        placeholder="{{ __('Password') }}"
                        v-on:keyup.enter="confirmPasswordAndSave">
                </div>
                <small class="field-help is-danger" v-if="passwordError">@{{ passwordError }}</small>
                <div class="voucher-password-actions">
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
    </template>
</div>

<script>
    var vue_details_tab_edit = null;

    window.validateVoucherBeforeSend = function ($form) {
        if (!vue_details_tab_edit) {
            return true;
        }

        var app = vue_details_tab_edit;
        if (!app.isBalanced) {
            if (typeof js_notify === 'function') {
                js_notify('Debits and credits must be equal before saving.', 'error');
            } else {
                alert('Debits and credits must be equal before saving.');
            }
            return false;
        }

        var balanceError = app.getCashBankBalanceError();
        if (balanceError) {
            if (typeof js_notify === 'function') {
                js_notify(balanceError, 'error');
            } else {
                alert(balanceError);
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
                    var reasonInput = document.getElementById('voucher_edit_reason');
                    var passwordInput = document.getElementById('voucher_auth_password');
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
            data: function() {
                return Object.assign(
                    <?php echo json_encode($details_edit_vue); ?>, {
                        sectionDr: <?php echo json_encode(
                            isset($sectionDr) ? $sectionDr : ($details_edit_vue['sectionDr'] ?? [
                                [
                                    'account_id' => '',
                                    'debit_amount' => '',
                                    'remarks' => '',
                                ],
                            ]),
                        ); ?>,
                        sectionCr: <?php echo json_encode(
                            isset($sectionCr) ? $sectionCr : ($details_edit_vue['sectionCr'] ?? [
                                [
                                    'account_id' => '',
                                    'credit_amount' => '',
                                    'remarks' => '',
                                ],
                            ]),
                        ); ?>,
                        diff_amount: 0,
                        auth_password: '',
                        edit_reason: '',
                        showPasswordConfirm: false,
                        passwordError: '',
                        reasonError: '',
                        passwordSubmitting: false,
                        accountBalances: {},
                    }
                );
            },
            computed: {
                isEditMode: function() {
                    return parseInt(this.id, 10) > 0;
                },
                isBalanced: function() {
                    return Math.abs(parseFloat(this.diff_amount) || 0) < 0.01
                        && this.totalDebit > 0
                        && this.totalCredit > 0;
                },
                totalDebit: function() {
                    return this.sectionDr.reduce(function(acc, ele) {
                        return acc + (parseFloat(ele.debit_amount) || 0);
                    }, 0);
                },
                totalCredit: function() {
                    return this.sectionCr.reduce(function(acc, ele) {
                        return acc + (parseFloat(ele.credit_amount) || 0);
                    }, 0);
                },
                totalCashCredit: function() {
                    return this.sumSectionAmount(this.sectionCr, 'credit_amount', this.cash_account_id);
                },
                totalBankCredit: function() {
                    return this.sumSectionAmount(this.sectionCr, 'credit_amount', this.bank_account_id);
                },
                totalCashDebit: function() {
                    return this.sumSectionAmount(this.sectionDr, 'debit_amount', this.cash_account_id);
                },
                totalBankDebit: function() {
                    return this.sumSectionAmount(this.sectionDr, 'debit_amount', this.bank_account_id);
                },
                availableCashBalance: function() {
                    var balance = this.getStoredBalance(this.cash_account_id);
                    if (balance === undefined) {
                        return 0;
                    }
                    if (this.isEditMode) {
                        balance += parseFloat(this.original_cash_credit || 0);
                    }
                    return Math.round(balance * 100) / 100;
                },
                availableBankBalance: function() {
                    var balance = this.getStoredBalance(this.bank_account_id);
                    if (balance === undefined) {
                        return 0;
                    }
                    if (this.isEditMode) {
                        balance += parseFloat(this.original_bank_credit || 0);
                    }
                    return Math.round(balance * 100) / 100;
                },
                projectedCashBalance: function() {
                    return Math.round((this.availableCashBalance - this.totalCashCredit + this.totalCashDebit) * 100) / 100;
                },
                projectedBankBalance: function() {
                    return Math.round((this.availableBankBalance - this.totalBankCredit + this.totalBankDebit) * 100) / 100;
                },
                hasInsufficientCashBank: function() {
                    if (this.totalCashCredit > 0 && this.projectedCashBalance < -0.0001) {
                        return true;
                    }
                    if (this.totalBankCredit > 0 && this.projectedBankBalance < -0.0001) {
                        return true;
                    }
                    return false;
                }
            },
            mounted: function() {
                this.differenceOfDebitAndCredit();
                if (this.cash_account_id) {
                    this.$set(
                        this.accountBalances,
                        this.normalizeAccountId(this.cash_account_id),
                        parseFloat(this.cash_balance || 0)
                    );
                }
                if (this.bank_account_id) {
                    this.$set(
                        this.accountBalances,
                        this.normalizeAccountId(this.bank_account_id),
                        parseFloat(this.bank_balance || 0)
                    );
                }
                var self = this;
                this.$nextTick(function() {
                    setTimeout(function() {
                        self.prefetchSelectedAccountBalances();
                    }, 350);
                });
            },
            methods: {
                normalizeAccountId: function(accountId) {
                    return String(accountId || '').trim();
                },
                getStoredBalance: function(accountId) {
                    var id = this.normalizeAccountId(accountId);
                    if (!id) {
                        return undefined;
                    }

                    if (this.accountBalances[id] !== undefined && this.accountBalances[id] !== null) {
                        return parseFloat(this.accountBalances[id]);
                    }

                    if (this.isCashAccount(id)) {
                        return parseFloat(this.cash_balance || 0);
                    }

                    if (this.isBankAccount(id)) {
                        return parseFloat(this.bank_balance || 0);
                    }

                    return undefined;
                },
                prefetchSelectedAccountBalances: function() {
                    var self = this;
                    var accountIds = [];

                    if (this.cash_account_id) {
                        accountIds.push(this.cash_account_id);
                    }
                    if (this.bank_account_id) {
                        accountIds.push(this.bank_account_id);
                    }

                    this.sectionDr.forEach(function(row) {
                        if (row.account_id) {
                            accountIds.push(row.account_id);
                        }
                    });
                    this.sectionCr.forEach(function(row) {
                        if (row.account_id) {
                            accountIds.push(row.account_id);
                        }
                    });

                    accountIds.filter(function(id, index, list) {
                        return list.indexOf(id) === index;
                    }).forEach(function(accountId) {
                        self.fetchAccountBalance(accountId);
                    });
                },
                fetchAccountBalance: function(accountId) {
                    var id = this.normalizeAccountId(accountId);
                    if (!id || !this.urls || !this.urls.account_balance_url) {
                        return;
                    }

                    var self = this;
                    $.ajax({
                        url: this.urls.account_balance_url + '/' + id,
                        method: 'GET',
                        dataType: 'json'
                    }).done(function(data) {
                        if (data && data.balance !== undefined) {
                            self.$set(self.accountBalances, id, data.balance);
                        }
                    }).fail(function() {
                        if (!self.isCashOrBankAccount(id)) {
                            self.$set(self.accountBalances, id, null);
                        }
                    });
                },
                formatLedgerBalance: function(accountId) {
                    var balance = this.getStoredBalance(accountId);
                    if (balance === null || balance === undefined) {
                        return '—';
                    }

                    return Number(balance).toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },
                formatMoney: function(value) {
                    return Number(value || 0).toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },
                isCashAccount: function(accountId) {
                    return String(accountId || '') === String(this.cash_account_id || '');
                },
                isBankAccount: function(accountId) {
                    return String(accountId || '') === String(this.bank_account_id || '');
                },
                isCashOrBankAccount: function(accountId) {
                    return this.isCashAccount(accountId) || this.isBankAccount(accountId);
                },
                getAvailableBalanceForAccount: function(accountId) {
                    if (this.isCashAccount(accountId)) {
                        return this.availableCashBalance;
                    }
                    if (this.isBankAccount(accountId)) {
                        return this.availableBankBalance;
                    }

                    var balance = this.getStoredBalance(accountId);
                    return balance === undefined ? 0 : balance;
                },
                getProjectedBalanceForAccount: function(accountId) {
                    if (this.isCashAccount(accountId)) {
                        return this.projectedCashBalance;
                    }
                    if (this.isBankAccount(accountId)) {
                        return this.projectedBankBalance;
                    }

                    var balance = this.getStoredBalance(accountId);
                    return balance === undefined ? 0 : balance;
                },
                sumSectionAmount: function(rows, amountField, accountId) {
                    var targetId = this.normalizeAccountId(accountId);
                    if (!targetId) {
                        return 0;
                    }

                    return rows.reduce(function(acc, row) {
                        if (String(row.account_id || '').trim() !== targetId) {
                            return acc;
                        }
                        return acc + (parseFloat(row[amountField]) || 0);
                    }, 0);
                },
                formatLedgerBalanceHint: function(accountId, side, amount) {
                    var available = this.getAvailableBalanceForAccount(accountId);
                    var current = this.getStoredBalance(accountId);
                    if (current === undefined) {
                        current = 0;
                    }
                    var projected = this.getProjectedBalanceForAccount(accountId);
                    var label = this.isCashAccount(accountId) ? 'Cash' : (this.isBankAccount(accountId) ? 'Bank' : 'Balance');

                    if (this.isCashOrBankAccount(accountId)) {
                        if (side === 'credit' && (parseFloat(amount) || 0) > 0) {
                            return label + ' available: ' + this.formatMoney(available) +
                                ' · After payment: ' + this.formatMoney(projected);
                        }
                        if (side === 'debit' && (parseFloat(amount) || 0) > 0) {
                            return label + ' available: ' + this.formatMoney(available) +
                                ' · After receipt: ' + this.formatMoney(projected);
                        }
                        return label + ' available: ' + this.formatMoney(available) +
                            ' · Current: ' + this.formatMoney(current);
                    }

                    return 'Ledger balance: ' + this.formatMoney(current);
                },
                ledgerBalanceHintClass: function(accountId, side, amount) {
                    if (!this.isCashOrBankAccount(accountId)) {
                        return '';
                    }

                    if (side === 'credit' && this.getProjectedBalanceForAccount(accountId) < -0.0001) {
                        return 'is-danger';
                    }

                    if (side === 'credit' && (parseFloat(amount) || 0) > 0 && this.getProjectedBalanceForAccount(accountId) >= 0) {
                        return 'is-ok';
                    }

                    return '';
                },
                getCashBankBalanceError: function() {
                    if (this.totalCashCredit > 0 && this.projectedCashBalance < -0.0001) {
                        return 'Cash has insufficient balance. Available: ' +
                            this.formatMoney(this.availableCashBalance) +
                            ', Required: ' + this.formatMoney(this.totalCashCredit);
                    }
                    if (this.totalBankCredit > 0 && this.projectedBankBalance < -0.0001) {
                        return 'Bank has insufficient balance. Available: ' +
                            this.formatMoney(this.availableBankBalance) +
                            ', Required: ' + this.formatMoney(this.totalBankCredit);
                    }
                    return '';
                },
                addFieldDr: function() {
                    this.sectionDr.push({
                        account_id: "",
                        debit_amount: "",
                        remarks: ""
                    });
                },
                removeFieldDr: function(index) {
                    this.sectionDr.splice(index, 1);
                    this.differenceOfDebitAndCredit();
                },
                addFieldCr: function() {
                    this.sectionCr.push({
                        account_id: "",
                        credit_amount: "",
                        remarks: ""
                    });
                },
                removeFieldCr: function(index) {
                    this.sectionCr.splice(index, 1);
                    this.differenceOfDebitAndCredit();
                },
                differenceOfDebitAndCredit: function() {
                    var debit = this.sectionDr.reduce(function(acc, ele) {
                        return acc + (parseFloat(ele.debit_amount) || 0);
                    }, 0);
                    var credit = this.sectionCr.reduce(function(acc, ele) {
                        return acc + (parseFloat(ele.credit_amount) || 0);
                    }, 0);
                    this.diff_amount = Math.round((debit - credit) * 100) / 100;
                },
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
                        var reasonInput = document.getElementById('voucher_edit_reason');
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
                    var $form = $(this.$el).find('form.voco-ajax-form');
                    var self = this;
                    this.$nextTick(function () {
                        if ($form.length) {
                            $form.trigger('submit');
                        }
                        self.passwordSubmitting = false;
                    });
                }
            }
        });

        $(document).off('ajaxError.voucherEditPassword').on('ajaxError.voucherEditPassword', function (event, jqxhr) {
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
                        ? 'voucher_edit_reason'
                        : 'voucher_auth_password';
                    var input = document.getElementById(focusId);
                    if (input) {
                        input.focus();
                    }
                });
            }
        });
    });
</script>
