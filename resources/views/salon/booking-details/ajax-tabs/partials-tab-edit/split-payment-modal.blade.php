<div class="modal fade" id="split-payment-modal" tabindex="-1" role="dialog" aria-labelledby="split-payment-modal-label"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 520px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="split-payment-modal-label">
                    <span v-if="split_payment_mode === 'rebalance'">{{ __('Rebalance Payments') }}</span>
                    <span v-else>{{ __('Split Payment') }}</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="split-payment-modal-summary mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span v-if="split_payment_mode === 'rebalance'">{{ __('Total Paid') }}</span>
                        <span v-else>{{ __('Balance Due') }}</span>
                        <strong>{{ __('₹') }} @{{ v_fixed_amount(splitPaymentTargetTotal) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ __('Split Total') }}</span>
                        <strong>{{ __('₹') }} @{{ v_fixed_amount(splitPaymentEnteredTotal) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between"
                        v-bind:class="{ 'text-success': splitPaymentModalRemaining < 0.01, 'text-danger': splitPaymentModalRemaining >= 0.01 && splitPaymentEnteredTotal > 0 }">
                        <span>{{ __('Remaining') }}</span>
                        <strong>{{ __('₹') }} @{{ v_fixed_amount(splitPaymentModalRemaining) }}</strong>
                    </div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-4 col-form-label mb-0">{{ __('Cash') }}</label>
                    <div class="col-8">
                        <input type="text" inputmode="decimal" class="form-control browz-gray-input-custom"
                            v-model="split_payment.cash" placeholder="0.00">
                    </div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-4 col-form-label mb-0">{{ __('Card') }}</label>
                    <div class="col-8">
                        <input type="text" inputmode="decimal" class="form-control browz-gray-input-custom"
                            v-model="split_payment.card" placeholder="0.00">
                    </div>
                </div>

                <div class="form-group row align-items-center" v-if="v_amount(split_payment.card) > 0">
                    <label class="col-4 col-form-label mb-0">{{ __('Card Type') }}</label>
                    <div class="col-8">
                        <select class="form-control browz-gray-input-custom" v-model="split_payment.card_type">
                            <option value="">{{ __('Select card type') }}</option>
                            {{ \Facades\App\Models\BookingPayment::cardTypeDropdown() }}
                        </select>
                    </div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-4 col-form-label mb-0">{{ __('Online') }}</label>
                    <div class="col-8">
                        <input type="text" inputmode="decimal" class="form-control browz-gray-input-custom"
                            v-model="split_payment.online" placeholder="0.00">
                    </div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-4 col-form-label mb-0">{{ __('Credit') }}</label>
                    <div class="col-8">
                        <input type="text" inputmode="decimal" class="form-control browz-gray-input-custom"
                            v-model="split_payment.credit" placeholder="0.00">
                    </div>
                </div>

                <div class="form-group row align-items-center" v-if="v_amount(split_payment.cash) > 0">
                    <label class="col-4 col-form-label mb-0">{{ __('Cash Tendered') }}</label>
                    <div class="col-8">
                        <input type="text" inputmode="decimal" class="form-control browz-gray-input-custom"
                            v-model="split_payment.cash_tendered" placeholder="{{ __('Optional — for change') }}">
                        <small v-if="splitPaymentCashChange > 0" class="text-muted">
                            {{ __('Change') }}: {{ __('₹') }} @{{ v_fixed_amount(splitPaymentCashChange) }}
                        </small>
                    </div>
                </div>

                <p v-if="split_payment_mode === 'rebalance'" class="text-muted mb-0" style="font-size: 12px;">
                    {{ __('Change how the paid amount is split across modes. Total must stay the same.') }}
                </p>
                <p v-else class="text-muted mb-0" style="font-size: 12px;">
                    {{ __('Enter amounts across payment modes. Split total must equal balance due.') }}
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" v-on:click="fillSplitPaymentBalance">
                    <span v-if="split_payment_mode === 'rebalance'">{{ __('All Cash') }}</span>
                    <span v-else>{{ __('Fill balance in Cash') }}</span>
                </button>
                <button type="button" class="btn btn-primary btn-sm btn_dark-x"
                    v-bind:disabled="!canSaveSplitPayment" v-on:click="submitSplitPayments">
                    <i class="fa fa-spinner fa-spin" v-if="split_payment_saving"></i>
                    <span v-if="split_payment_mode === 'rebalance'">{{ __('Save Rebalance') }}</span>
                    <span v-else>{{ __('Save Split Payment') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>
