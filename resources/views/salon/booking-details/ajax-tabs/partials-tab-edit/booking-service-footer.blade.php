<form id="form1" method="post" v-bind:action="urls.form_final_update_url">
    @csrf

    <input type="hidden" name="booking_id" v-model="booking_id">
    <input type="hidden" name="tab_name" v-model="tab_name">
    <input type="hidden" id="print_and_update" name="is_update" value="0">
    <input type="hidden" name="print_url" id="print_and_update_url" value="">
    <input type="hidden" name="auth_password" value="">
    <input type="hidden" name="edit_reason" value="">
    <div style="
    position: fixed;
    bottom: 0;
    width: 100%;
    z-index: 7;
    background-color: #F0F0F3;
    padding: 10px 0;
    width: -webkit-fill-available;
    margin-right: 0px;
    margin-left: -70px;
    justify-content: space-between;
    display: inline-flex;
    row-gap: 10px;
"
        class="row adjustable-bottom-footer">
        <div class="col-lg-8 col-md-8 col-sm-8 d-flex flex-wrap" style="row-gap: 10px">

            <label v-if="booking_info.payment_status != 'completed' && !booking_info.booking_code"
                v-on:click="cancelOrder" class="btn btn-sm btn-primary mr-2 brownz-btn-yellow-d m-0">
                {{ __('✖ Cancel') }}
            </label>
            {{-- <button class="btn btn-sm btn-primary mr-2" style="min-width: 100px;">New</button> --}}
            {{-- <button class="btn btn-sm btn-primary mr-2" style="min-width: 100px;">
      Re-Print
    </button> --}}
            {{-- <a v-bind:href="urls.booking_invoice_gift_url" target="_blank">
                <button :disabled="booking_info.payment_status !=='completed'" class="btn btn-sm btn-primary mr-2"
                    style="min-width: 100px;">
                    {{ __('Print Gift Coupon') }}

            </button></a> --}}

            <button type="button" id="print_and_update_btn" value="0"
                :disabled="!canPrintUpdate"
                v-on:click="updateinvoice(urls.booking_invoice_url)" class="btn  mr-2 btn_dark-x"
                style="min-width: 100px;">
                {{ __('Print Update') }}
            </button>
            <button :disabled="!canSubmitUpdate" type="submit"
                class="btn btn-sm btn-primary mr-2 btn-green-brownz" style="min-width: 100px;">
                {{ __('Update') }}

            </button>
            <label v-if="booking_info.payment_status == 'completed' && !!booking_info.booking_code && (booking_info.booking_items.length > 0 || booking_info.booking_only_services.length > 0)"
                v-on:click="getRfundData" class="btn btn-sm btn-primary mr-2 btn-blue-brownz"
                style="min-width: 100px;margin-top: 9px;">
                {{ __('Refund') }}
            </label>

            <!-- <button type="button" class="btn btn-link notify_whatsapp" v-bind:data-booking-id="booking_id" v-bind:data-url="urls.booking_notify_whatsapp_url">
                <i class="fa fa-whatsapp text-success" style="font-size:2.5rem"></i>
            </button> -->
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 text-right">
            <a href="{{ route('tab.salon.booking.list') }}">
                <label class="btn btn-sm btn-primary mr-2 btn-red-brownz" style="min-width: 100px;">
                    {{ __('Close') }}

                </label>
            </a>
        </div>
    </div>
</form>