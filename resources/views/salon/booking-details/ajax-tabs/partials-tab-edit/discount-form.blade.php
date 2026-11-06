<div v-if="booking_info.booking_total > 0" class="row">
    <div class="col-12">

        {{-- Editable discount while booking is open --}}
        <form v-if="booking_info.booking_only_services.length > 0 && booking_info.payment_status != 'completed'"
            class="form-inline voco-ajax-form float-right js-booking-discount-form" method="post"
            v-bind:action="urls.discount_save_url" data-hook-success="discountSaved">
            @csrf
            <input type="hidden" name="booking_id" v-model="booking_id">
            <input type="hidden" name="tab_name" v-model="tab_name">
            <input type="hidden" name="grand_total" v-model="grand_total">
            <input type="hidden" id="typeInput" name="type" value="global">

            <label class="mr-2 " for="applied_discount_percent">{{ __('Discount') }}</label>
            <div>
                <div class="input-group  discount--container-custom-brow tog-btn-container">
                    <input v-bind:disabled="booking_info.canceled_by > 0" type="text" class="form-control"
                        v-bind:value="booking_info.booking_only_services[0].show_discount_persent"
                        id="applied_discount_percent" name="applied_discount_percent" placeholder="{{ __('Discount') }}"
                        style="max-width:90px">

                    <input v-bind:disabled="booking_info.canceled_by > 0" type="text"
                        class="form-control input-price-active" v-bind:value="booking_info.applied_discount_price"
                        id="applied_price_percent" name="applied_price_percent" placeholder="{{ __('Price') }}"
                        style="max-width:90px">
                    <button type="button" class="toggle-discount" id="toggleDiscountButton">
                        ₹
                    </button>
                </div>
            </div>
        </form>

        <form
            v-if="booking_info.booking_only_services.length == 0 &&  booking_info.booking_items.length > 0 && booking_info.payment_status != 'completed'"
            class="form-inline voco-ajax-form float-right js-booking-discount-form" method="post"
            v-bind:action="urls.discount_save_url" data-hook-success="discountSaved">
            @csrf
            <input type="hidden" name="booking_id" v-model="booking_id">
            <input type="hidden" name="tab_name" v-model="tab_name">
            <input type="hidden" name="grand_total" v-model="grand_total">
            <input type="hidden" id="typeInput" name="type" value="global">

            <label class="mr-2 " for="applied_discount_percent">{{ __('Discount') }}</label>
            <div>
                <div class="input-group  discount--container-custom-brow tog-btn-container">
                    <input v-bind:disabled="booking_info.canceled_by > 0" type="text" class="form-control"
                        v-bind:value="booking_info.booking_items[0].show_discount_persent"
                        id="applied_discount_percent" name="applied_discount_percent" placeholder="{{ __('Discount') }}"
                        style="max-width:90px">

                    <input v-bind:disabled="booking_info.canceled_by > 0" type="text"
                        class="form-control input-price-active" v-bind:value="booking_info.applied_discount_price"
                        id="applied_price_percent" name="applied_price_percent" placeholder="{{ __('Price') }}"
                        style="max-width:90px">
                    <button type="button" class="toggle-discount" id="toggleDiscountButton">
                        ₹
                    </button>
                </div>
            </div>
        </form>

        {{-- Readonly discount after booking is completed --}}
        <div v-if="booking_info.payment_status == 'completed' && v_amount(summary_discount_total) > 0"
            class="form-inline float-right">
            <label class="mr-2 mb-0">{{ __('Discount') }}</label>
            <div class="input-group discount--container-custom-brow">
                <input type="text" class="form-control" readonly
                    v-bind:value="v_fixed_amount(summary_discount_total)"
                    style="max-width:110px; background:#f5f5f5;">
                <div class="input-group-append">
                    <span class="input-group-text">₹</span>
                </div>
            </div>
        </div>
    </div>
</div>
