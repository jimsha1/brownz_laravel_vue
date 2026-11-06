<div class="col-xl-4 col-lg-5 respo-padding-right-0">
    <div class="d-block mt-3">
        <div class="form-group">
            {{-- <label for="">{{ __('Name') }}</label> --}}
            <input v-model="booking_info.client_name" class="form-control browz-gray-input-custom" disabled type="text"
                placeholder="Name" name="" id="">
        </div>

        <div class="form-group mt-3">
            {{-- <label for="">{{ __('Mobile') }}</label> --}}
            <input dir="ltr" lang="en" v-model="booking_info.client_mobile"
                class="form-control browz-gray-input-custom" disabled placeholder="Mobile" type="text" name=""
                id="">
        </div>
    </div>

    <div class="d-flex justify-content-end mt-3">
        @include('salon.booking-details.ajax-tabs.partials-tab-edit.discount-form')
        {{-- <div class="form-group d-flex justify-content-end align-items-center">
            <label for="" class="m-0 mr-2">Discount</label>
            <input type="text" class="form-control" placeholder="0" aria-label="Recipient's username" aria-describedby="basic-addon2">
            <div class="input-group-append">
                <span class="input-group-text" id="basic-addon2">%</span>
            </div>
        </div> --}}
    </div>
    <table class="table mb-0">
        <tbody>
            <tr>
                <td style="
                padding-left: 0;
                color: #1D1D1D;
                font-size: 16px;
                font-weight: 400;
                margin: 0;
                border: none;
              "
                    colspan="2">
                    {{ __('Summary') }}
                </td>
            </tr>

            <tr style="margin: 0;
    color: #979696;
    font-size: 14px;
    font-weight: 400;">
                <td style="padding: 10px 0 !important; font-size: 14px; border: none;">{{ __('Sub total') }}</td>
                <td style="padding: 10px 0 !important; font-size: 14px; border: none;" class="text-right">
                    {{ __('₹') }} @{{ v_fixed_amount(grand_sub_total) }}</td>
            </tr>

            <tr
                style="margin: 0;
            color: #979696;
            font-size: 14px;
            font-weight: 400;">
                <td style="padding: 10px 0 !important;  font-size: 14px; border: none;">{{ __('Tax') }}</td>
                <td style="padding: 10px 0 !important;  font-size: 14px; border: none;" class="text-right">
                    {{ __('₹') }} @{{ v_fixed_amount(grand_tax_total) }}</td>
            </tr>

            <tr v-if="v_amount(summary_discount_total) > 0"
                style="margin: 0;
            color: #979696;
            font-size: 14px;
            font-weight: 400;">
                <td style="padding: 10px 0 !important;  font-size: 14px; border: none;">{{ __('Discount') }}</td>
                <td style="padding: 10px 0 !important;  font-size: 14px; border: none;" class="text-right">
                    {{ __('₹') }} @{{ v_fixed_amount(summary_discount_total) }}</td>
            </tr>

            <tr style="border-top: 1px solid #F0F0F3; ">
                <td style=" padding-left: 0; padding-right: 0;  border: none;">
                    <h6 style=" margin-bottom: 0;    font-size: 18px;color: #0d0e0d;font-weight: 500;"><b
                            style=" text-align: center;vertical-align: center;">{{ __('Net Total') }}</b>
                    </h6>
                </td>
                <td style=" padding-left: 0; padding-right: 0;text-align: center;vertical-align: center;
                 font-size: 18px; color: #0c0d0c; font-weight: 500; border: none; "
                    class="text-right">
                    <h5><b> @{{ v_fixed_amount(grand_total_1) }}</b></h5>
                </td>

            </tr>

            <tr style="border-top: 1px solid #F0F0F3;">
                <td style="padding-left: 0; padding-right: 0; border: none; vertical-align: middle;">
                    <h6 style="margin-bottom: 0; font-size: 14px; color: rgb(151, 150, 150); font-weight: 200;">
                        <b style="text-align: center; vertical-align: center;">{{ __('Round off') }}</b>
                    </h6>
                </td>
                <td style="padding-left: 0; padding-right: 0; text-align: center; vertical-align: center;
                           font-size: 18px; color: #0c0d0c; font-weight: 500; border: none;"
                    class="text-right">
                    <div style="display: flex;     justify-content: flex-end;
    align-items: center; gap: 15px;">
                        <h5 style="margin-bottom: 0; font-size: 14px; color: rgb(151, 150, 150); font-weight: 200;">
                            <b>@{{ booking_info.applied_roundoff_price }}</b>
                        </h5>
                        <button id="vue-details-tab-edit" @click="roundoff(grand_total_1,booking_info)"
                            style="background-color: #E6CD7F;
    color: #1D1D1D;width: 37px;height: 37px;font-size: 14px;
    margin: 0;display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50px;
    border: none;
    cursor: pointer;
    outline: none;">
                            <i class="fa fa-refresh" aria-hidden="true"></i>
                        </button>
                    </div>
                </td>
            </tr>


            <tr style="border-top: 1px solid #F0F0F3; ">
                <td style=" padding-left: 0; padding-right: 0;  border: none;">
                    <h6 style=" margin-bottom: 0;    font-size: 18px;color: #199900;font-weight: 500;"><b
                            style=" text-align: center;vertical-align: center;">{{ __('Grand Total') }}</b>
                    </h6>
                </td>
                <td style=" padding-left: 0; padding-right: 0;text-align: center;vertical-align: center;
                 font-size: 18px; color: #199900; font-weight: 500; border: none; "
                    class="text-right">
                    <h5><b> @{{ v_fixed_amount(grand_total) }}</b></h5>
                </td>
            </tr>

            <tr v-if="!booking_info.canceled_by && booking_info.payment_status != 'completed' && (booking_info.available_credit || 0) > 0 && !booking_info.has_client_credit_applied">
                <td style="padding-left: 0; padding-right: 0; border: none; vertical-align: middle;">
                    <h6 style="margin-bottom: 0; font-size: 14px; color: #0d6efd; font-weight: 500;">
                        {{ __('Available Credit') }}
                    </h6>
                </td>
                <td style="padding-left: 0; padding-right: 0; border: none;" class="text-right">
                    <span class="mr-2 font-weight-bold text-primary">
                        {{ __('₹') }} @{{ v_fixed_amount(booking_info.available_credit) }}
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-primary"
                        v-on:click="applyClientCredit()">
                        {{ __('Apply Credit') }}
                    </button>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="d-flex justify-content-end">
        <div class="row">
            <div class="col-12">


                {{-- <form class="form-inline voco-ajax-form float-right" method="post" action="{{route('total.discount.price')}}">
                    @csrf
                    <input type="hidden" name="booking_id" v-model="booking_id">
                    <input type="hidden" name="tab_name" v-model="tab_name">
                    <input type="hidden" name="grand_total" v-model="grand_total">

                    <label class="mr-2 mb-3" for="applied_discount_price">{{ __('Discount') }}</label>
                    <div>
                        <div class="input-group mb-3 discount--container-custom-brow">
                            <input v-bind:disabled="booking_info.canceled_by > 0" type="text" class="form-control"
                                id="applied_discount_percent" name="applied_discount_percent"
                                placeholder="{{ __('Discount') }}" style="max-width:90px">
                        </div>

                    </div>
                </form> --}}

            </div>
        </div>
    </div>

    {{-- <div class="d-flex justify-content-between mt-2">
        <p style=" margin-bottom: 0;    font-size: 18px;
        color: #D0342C ;
        font-weight: 500;">Pay Amount</p>
        <p style=" margin-bottom: 0;    font-size: 18px;
        color: #D0342C ;
        font-weight: 500;">Pay</p>
    </div> --}}

    {{-- <div class="form-group d-flex justify-content-between align-items-center mt-2">
        <h6>Payments</h6>
        <h6 style="color: #f11313">254.50</h6>
    </div> --}}

    <div v-if="showPaymentBreakdown" class="split-payment-breakdown mb-2">
        <div class="split-payment-breakdown-title">{{ __('Payment split') }}</div>
        <div class="split-payment-breakdown-chips">
            <span class="split-payment-chip" v-for="row in paymentBreakdownByMode" v-bind:key="row.mode">
                <strong>@{{ row.label }}</strong>
                <em>{{ __('₹') }} @{{ v_fixed_amount(row.amount) }}</em>
            </span>
            <span v-if="remainingBalanceDue() > 0" class="split-payment-chip split-payment-chip-due">
                <strong>{{ __('Due') }}</strong>
                <em>{{ __('₹') }} @{{ v_fixed_amount(remainingBalanceDue()) }}</em>
            </span>
        </div>
    </div>

    <table class="table" style="margin-bottom: 5px;">
        <tbody>
            <tr>
                <td colspan="1"
                    style="width:33.3%;
            background-color: #ffff;
            border-radius: 0;
            border: 0;
            font-size: 14px;
            color: #1D1D1D;
            font-weight: 400;
            padding-left: 0;
            padding-bottom:0;
          ">
                    {{ __('Amount Paid') }}
                </td>
                <td colspan="1"
                    style="width:33.3%;background-color: #ffff;
            border-radius: 0;
            border: 0;
            font-size: 14px;
            color: #1D1D1D;
            font-weight: 400;
            padding-left: 0;
            padding-bottom:0;
          ">
                    {{ __('Given') }}
                </td>
                <td colspan="1"
                    style="width:33.3%;background-color: #ffff;
            border-radius: 0;
            border: 0;
            font-size: 14px;
            color: #1D1D1D;
            font-weight: 400;
            padding-left: 14px;
            padding-bottom:0;
            padding-right: 0;
          ">
                    {{ __('Payment Mode') }}
                </td>
                {{-- <td colspan="1" style="background-color: #ffff;
            border-radius: 0;
            border: 0;
            font-size: 14px;
            color: #1D1D1D;
            font-weight: 400;
            padding-left: 0;
            padding-bottom:0;
          "> --}}

                </td>
            </tr>

            <tr v-for="payment_info in booking_info.payments">
                <td>
                    @{{ v_fixed_amount(payment_info.amount_taken - payment_info.amount_given) }}
                </td>
                <td>
                    @{{ v_fixed_amount(payment_info.amount_given) }}
                    <small v-if="payment_info.prepaid_applied == 1 || payment_info.prepaid_applied === true"
                        class="text-primary d-block">({{ __('Previous Credit') }})</small>
                    <small v-else-if="payment_info.change_as_credit == 1 || payment_info.change_as_credit === true"
                        class="text-success d-block">({{ __('Client Credit') }})</small>
                    <small v-else-if="payment_info.amount_given > 0"
                        class="text-muted d-block">({{ __('Returned') }})</small>
                </td>
                <td>
                    {{-- Completed booking: mode unlocked only after edit icon click --}}
                    <template v-if="!booking_info.canceled_by && booking_info.booking_type != 'Cancel'
                        && canShowPaymentModeEdit(payment_info)
                        && editing_payment_id == payment_info.payment_id">
                        <select class="form-control form-control-sm browz-gray-input-custom"
                            style="display:inline-block;width:auto;min-width:110px;max-width:140px;"
                            :value="payment_info.payment_mode"
                            v-on:change="updatePaymentMode(payment_info, $event)">
                            {{ \Facades\App\Models\BookingPayment::paymentModeDropdown() }}
                        </select>
                        <select class="form-control form-control-sm browz-gray-input-custom mt-1"
                            style="display:inline-block;width:auto;min-width:90px;max-width:120px;"
                            v-if="payment_info.payment_mode == 'card'"
                            :value="payment_info.card_type"
                            v-on:change="updatePaymentCardType(payment_info, $event)">
                            {{ \Facades\App\Models\BookingPayment::cardTypeDropdown() }}
                        </select>
                    </template>
                    <template v-else>
                        @{{ payment_info.payment_mode_locale }}
                        <span v-if="payment_info.card_type">
                            -
                            @{{ payment_info.card_type_locale }}
                        </span>
                    </template>
                </td>
                <td>
                    {{-- Finalized booking: edit mode and/or remove row --}}
                    <template v-if="!booking_info.canceled_by && booking_info.booking_type != 'Cancel'
                        && isPaymentFinalized() && canShowPaymentModeEdit(payment_info)">
                        <button type="button"
                            v-if="editing_payment_id != payment_info.payment_id"
                            class="btn btn-link text-primary py-0"
                            title="{{ __('Edit payment mode') }}"
                            v-on:click="startEditPaymentMode(payment_info)">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <button type="button"
                            v-else
                            class="btn btn-link text-secondary py-0"
                            title="{{ __('Cancel') }}"
                            v-on:click="cancelEditPaymentMode()">
                            <i class="fa fa-times"></i>
                        </button>
                    </template>
                    <form v-if="!booking_info.canceled_by && booking_info.booking_type != 'Cancel' && canShowPaymentVoid(payment_info)"
                        v-bind:action="urls.form_delete_payment_url"
                        class="voco-ajax-form js-void-payment-form" method="post" data-hook-success="paymentItemDeleted"
                        style="display:inline-block">
                        @csrf
                        <input type="hidden" name="booking_id" v-model="booking_id">
                        <input type="hidden" name="payment_id" v-model="payment_info.payment_id">
                        <input type="hidden" name="auth_password" value="" class="js-auth-password">
                        <button type="button"
                            class="btn btn-link text-danger py-0 payment-remove"
                            title="{{ __('Remove payment') }}">
                            <i class="fa fa-trash-o"></i>
                        </button>
                    </form>
                    {{-- Draft booking: remove only (no mode edit) --}}
                    <form v-else-if="!booking_info.canceled_by && booking_info.booking_type != 'Cancel' && !isPaymentFinalized() && canShowPaymentVoid(payment_info)"
                        v-bind:action="urls.form_delete_payment_url"
                        class="voco-ajax-form js-void-payment-form" method="post" data-hook-success="paymentItemDeleted"
                        style="display:inline-block">
                        @csrf
                        <input type="hidden" name="booking_id" v-model="booking_id">
                        <input type="hidden" name="payment_id" v-model="payment_info.payment_id">
                        <input type="hidden" name="auth_password" value="" class="js-auth-password">
                        <button type="button"
                            class="btn btn-link text-danger py-0 payment-remove"
                            title="{{ __('Remove payment') }}">
                            <i class="fa fa-trash-o"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <tr v-for="payment_info in booking_info.void_payments">
                <td class="text-secondary font-italic" style="text-decoration:line-through">
                    @{{ v_fixed_amount(payment_info.amount_taken - payment_info.amount_given) }}
                </td>
                <td class="text-secondary font-italic" style="text-decoration:line-through">
                    @{{ v_fixed_amount(payment_info.amount_given) }}
                </td>
                <td class="text-secondary font-italic">
                    @{{ payment_info.payment_mode_locale }}
                    <span v-if="payment_info.card_type">- @{{ payment_info.card_type_locale }}</span>
                    <small class="d-block">{{ __('Voided') }}</small>
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div v-if="isPaymentFinalized() && booking_info.payment_status === 'completed' && can_void_completed_payment"
        class="mb-2 text-right">
        <button type="button" class="btn btn-outline-primary btn-sm btn_dark-x" v-on:click="openRebalancePaymentModal">
            <i class="fa fa-exchange"></i> {{ __('Rebalance Payments') }}
        </button>
        <small class="d-block text-muted mt-1" style="font-size: 11px;">
            {{ __('Change payment split to full cash, card, online, or a new combination.') }}
        </small>
    </div>

    <div class="form-group d-flex justify-content-between align-items-center">
        {{-- <div class="d-flex">
            <input type="text" class="form-control" placeholder="0" name="" id="">
            <input type="text" class="form-control" disabled="" placeholder="Given..." name="" id="">
            <input type="text" class="form-control" placeholder="Cash" name="" id="">
        </div>
        <div>
            <button class="ml-2 btn btn-primary">
          Add Payment
        </button> --}}

        @include('salon.booking-details.ajax-tabs.partials-tab-edit.order-payments')

    </div>

</div>
</div>
