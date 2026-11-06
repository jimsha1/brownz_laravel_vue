<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-list-tab-payment.vendor-payment {
    padding: 4px 0 8px;
  }

  #vue-list-tab-payment .payment-summary {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 16px;
  }

  @media (max-width: 767px) {
    #vue-list-tab-payment .payment-summary {
      grid-template-columns: 1fr;
    }
  }

  #vue-list-tab-payment .summary-card {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 14px 16px;
  }

  #vue-list-tab-payment .summary-card .label {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 4px;
  }

  #vue-list-tab-payment .summary-card .value {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1D1D1D;
  }

  #vue-list-tab-payment .transactions-panel {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 14px;
    margin-top: 16px;
  }

  #vue-list-tab-payment .transactions-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 10px;
  }

  #vue-list-tab-payment .transactions-title h6 {
    margin: 0;
    font-size: 13px;
    font-weight: 600;
    color: #1D1D1D;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  #vue-list-tab-payment .transactions-meta {
    font-size: 12px;
    color: #6c757d;
    font-weight: 600;
  }

  #vue-list-tab-payment .transactions-table {
    margin-bottom: 0;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
  }

  #vue-list-tab-payment .transactions-table th,
  #vue-list-tab-payment .transactions-table td {
    vertical-align: middle;
    white-space: nowrap;
  }

  #vue-list-tab-payment .mode-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    text-transform: capitalize;
    background: #F0F0F3;
    color: #1D1D1D;
  }

  #vue-list-tab-payment .mode-pill.is-cash {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  #vue-list-tab-payment .mode-pill.is-bank,
  #vue-list-tab-payment .mode-pill.is-online {
    color: #1D4ED8;
    background: rgba(59, 130, 246, 0.12);
  }

  #vue-list-tab-payment .amount-cell {
    font-weight: 600;
    color: #1D1D1D;
    font-variant-numeric: tabular-nums;
  }

  #vue-list-tab-payment .empty-transactions {
    text-align: center;
    padding: 24px 12px;
    color: #6c757d;
    background: #fff;
    border-radius: 12px;
  }

  #vue-list-tab-payment .payment-panel {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 16px 16px 6px;
  }

  #vue-list-tab-payment .payment-panel-title {
    font-size: 13px;
    font-weight: 600;
    color: #1D1D1D;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  #vue-list-tab-payment .form-control {
    border-radius: 999px;
    border: none;
    background: #fff;
    height: 38px;
  }

  #vue-list-tab-payment .vc-form-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 14px;
  }
</style>

<div id="vue-list-tab-payment" class="vendor-payment has-vue-form">
    <template>
        <div>
            <div class="payment-summary">
                <div class="summary-card">
                    <span class="label">{{ __('Credit Amount') }}</span>
                    <span class="value">@{{ Number(credit_amount || 0).toFixed(2) }}</span>
                </div>
                <div class="summary-card">
                    <span class="label">{{ __('Max Payable') }}</span>
                    <span class="value">@{{ Number(effectiveMaxPayable || 0).toFixed(2) }}</span>
                </div>
            </div>

            <form class="voco-ajax-form" method="POST" :action="form_save_url"
                data-hook-before-send="validateVendorPaymentBalance">
                @csrf
                <input type="hidden" name="vendor_id" :value="vendor_id">
                <input type="hidden" name="tab_name" :value="tab_name">

                <div class="payment-panel">
                    <div class="payment-panel-title">{{ __('Pay Vendor') }}</div>
                    <div class="row align-items-end brw-form-la">
                        <div class="form-group col-md-3">
                            <label for="balance_amount">{{ __('Credit Amount') }}</label>
                            <input type="number" id="balance_amount" class="form-control" name="balance_amount"
                                :value="credit_amount" readonly>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="amount">{{ __('Amount') }}</label>
                            <input type="number" id="amount" class="form-control" name="amount" v-model="amount"
                                step="0.01" :min="0.01" :max="effectiveMaxPayable" required
                                :disabled="max_payable < 0.01">
                            <small class="text-muted" v-if="max_payable >= 0.01">
                                {{ __('Max') }}: @{{ Number(effectiveMaxPayable).toFixed(2) }}
                            </small>
                            <small class="text-danger" v-else>
                                {{ __('No outstanding credit to pay.') }}
                            </small>
                            <small class="text-danger d-block" v-if="amountExceedsCashBank">
                                {{ __('Amount exceeds available') }} @{{ paymentAccountLabel }} {{ __('balance') }}
                                (@{{ Number(availablePaymentBalance).toFixed(2) }}).
                            </small>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="payment_mode">{{ __('Payment Mode') }}</label>
                            <select class="form-control" id="payment_mode" name="payment_mode" v-model="payment_mode" required
                                :disabled="max_payable < 0.01">
                                <option value="">-{{ __('Select') }}-</option>
                                <option value="cash">{{ __('Cash') }}</option>
                                <option value="bank">{{ __('Bank') }}</option>
                            </select>
                            <small class="text-muted d-block mt-1" v-if="payment_mode">
                                {{ __('Available') }} @{{ paymentAccountLabel }}:
                                <strong>@{{ Number(availablePaymentBalance).toFixed(2) }}</strong>
                            </small>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="payment_date">{{ __('Payment Date') }}</label>
                            <input type="date" id="payment_date" class="form-control" name="payment_date"
                                v-model="payment_date" required :disabled="max_payable < 0.01">
                        </div>
                    </div>
                </div>

                <div class="vc-form-buttons">
                    <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d" :href="button_cancel_url">
                        <i class="fa fa-undo"></i> {{ __('Cancel') }}
                    </a>

                    <a v-if="max_payable >= 0.01 && voucher_pay_url"
                        class="btn btn-outline-primary btn_dark-x"
                        :href="voucher_pay_url">
                        <i class="fa fa-file-text-o"></i> {{ __('Pay via Voucher') }}
                    </a>

                    <button type="submit" class="btn btn-primary btn_dark-x"
                        :disabled="max_payable < 0.01 || !amount || !payment_mode || !payment_date || amountExceedsCashBank">
                        <i class="fa fa-check"></i> {{ __('Save') }}
                    </button>
                </div>
            </form>

            <div class="transactions-panel">
                <div class="transactions-title">
                    <h6>{{ __('Payment Transactions') }}</h6>
                    <span class="transactions-meta" v-if="(transactions || []).length">
                        {{ __('Showing') }} @{{ (transactions || []).length }} {{ __('latest') }}
                    </span>
                </div>

                <template v-if="(transactions || []).length">
                    <div class="table-responsive">
                        <table class="table brownz-table-list-v1 transactions-table">
                            <thead>
                                <tr>
                                    <th style="width: 56px;">{{ __('SL#') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Purchase') }}</th>
                                    <th>{{ __('Mode') }}</th>
                                    <th class="text-right">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(tx, index) in transactions" :key="tx.payment_id || index">
                                    <td>@{{ index + 1 }}</td>
                                    <td>@{{ formatTxDate(tx.created_at || tx.payment_date) }}</td>
                                    <td>
                                        <span v-if="tx.purchase_id">#@{{ tx.purchase_id }}</span>
                                        <span v-else>{{ __('Manual') }}</span>
                                    </td>
                                    <td>
                                        <span class="mode-pill" :class="'is-' + (tx.payment_mode || '')">
                                            @{{ tx.payment_mode || '—' }}
                                        </span>
                                    </td>
                                    <td class="text-right amount-cell">
                                        @{{ Number(tx.amount || 0).toFixed(2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
                <div v-else class="empty-transactions">
                    <strong>{{ __('No payment transactions yet') }}</strong>
                    <div>{{ __('Paid cash/bank transactions will appear here.') }}</div>
                </div>

                <div v-if="(voucher_links || []).length" class="mt-3">
                    <h6 style="font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.02em;">
                        {{ __('Payment Vouchers') }}
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li v-for="voucher in voucher_links" :key="voucher.id" class="mb-1">
                            <a :href="voucher.view_url">
                                @{{ voucher.voucher_no || ('#' + voucher.id) }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    var vue_vendor_payment = null;

    window.validateVendorPaymentBalance = function ($form) {
        if (!vue_vendor_payment) {
            return true;
        }
        var app = vue_vendor_payment;
        var amount = parseFloat(app.amount || 0);
        var mode = String(app.payment_mode || '').toLowerCase();
        if (!mode || !(amount > 0)) {
            return true;
        }
        if (amount > app.availablePaymentBalance + 0.0001) {
            js_notify(
                app.paymentAccountLabel + ' has insufficient balance. Available: ' +
                Number(app.availablePaymentBalance).toFixed(2) +
                ', Required: ' + Number(amount).toFixed(2),
                'error'
            );
            return false;
        }
        return true;
    };

    jQuery(function($) {
        vue_vendor_payment = new Vue({
            el: "#vue-list-tab-payment",
            data: Object.assign({
                amount: '',
                payment_mode: '',
                transactions: [],
            }, {!! json_encode($details_payment_vue) !!}),
            computed: {
                paymentAccountLabel: function () {
                    return String(this.payment_mode || '').toLowerCase() === 'cash' ? 'Cash' : 'Bank';
                },
                availablePaymentBalance: function () {
                    var mode = String(this.payment_mode || '').toLowerCase();
                    if (mode === 'cash') {
                        return Math.max(0, parseFloat(this.cash_balance || 0));
                    }
                    if (mode === 'bank') {
                        return Math.max(0, parseFloat(this.bank_balance || 0));
                    }
                    return 0;
                },
                effectiveMaxPayable: function () {
                    var creditMax = parseFloat(this.max_payable || 0);
                    if (!this.payment_mode) {
                        return creditMax;
                    }
                    return Math.max(0, Math.min(creditMax, this.availablePaymentBalance));
                },
                amountExceedsCashBank: function () {
                    var amount = parseFloat(this.amount || 0);
                    var mode = String(this.payment_mode || '').toLowerCase();
                    if (!mode || !(amount > 0)) {
                        return false;
                    }
                    return amount > this.availablePaymentBalance + 0.0001;
                }
            },
            methods: {
                formatTxDate: function (value) {
                    if (!value) {
                        return '—';
                    }
                    var date = new Date(value);
                    if (isNaN(date.getTime())) {
                        return value;
                    }
                    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    var day = date.getDate();
                    var month = months[date.getMonth()];
                    var year = date.getFullYear();
                    return (day < 10 ? '0' + day : day) + ' ' + month + ', ' + year;
                }
            }
        });
    });
</script>
