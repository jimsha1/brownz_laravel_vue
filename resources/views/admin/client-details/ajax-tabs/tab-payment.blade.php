<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-list-tab-payment.client-payment {
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

<div id="vue-list-tab-payment" class="client-payment has-vue-form">
    <template>
        <div>
            <div class="payment-summary">
                <div class="summary-card">
                    <span class="label">{{ __('Outstanding Balance') }}</span>
                    <span class="value">@{{ Number(balance_amount || 0).toFixed(2) }}</span>
                </div>
                <div class="summary-card">
                    <span class="label">{{ __('Max Payable') }}</span>
                    <span class="value">@{{ Number(max_payable || 0).toFixed(2) }}</span>
                </div>
            </div>

            <form class="voco-ajax-form" method="POST" :action="form_save_url">
                @csrf
                <input type="hidden" name="client_id" :value="client_id">
                <input type="hidden" name="tab_name" :value="tab_name">

                <div class="payment-panel">
                    <div class="payment-panel-title">{{ __('Collect Payment') }}</div>
                    <div class="row align-items-end brw-form-la">
                        <div class="form-group col-md-3">
                            <label for="balance_amount">{{ __('Balance Amount') }}</label>
                            <input type="number" id="balance_amount" class="form-control" name="balance_amount"
                                :value="balance_amount" readonly>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="amount">{{ __('Amount') }}</label>
                            <input type="number" id="amount" class="form-control" name="amount" v-model="amount"
                                step="0.01" :min="0.01" :max="max_payable" required
                                :disabled="max_payable < 0.01">
                            <small class="text-muted" v-if="max_payable >= 0.01">
                                {{ __('Max') }}: @{{ Number(max_payable).toFixed(2) }}
                            </small>
                            <small class="text-danger" v-else>
                                {{ __('No outstanding credit to collect.') }}
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

                    <button type="submit" class="btn btn-primary btn_dark-x"
                        :disabled="max_payable < 0.01 || !amount || !payment_mode || !payment_date">
                        <i class="fa fa-check"></i> {{ __('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </template>
</div>

<script>
    jQuery(function($) {
        new Vue({
            el: "#vue-list-tab-payment",
            data: Object.assign({
                amount: '',
                payment_mode: '',
            }, {!! json_encode($details_payment_vue) !!})
        });
    });
</script>
