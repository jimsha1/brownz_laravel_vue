<div v-if="booking_info.booking_total > 0" class="row">
  <div class="col-12">

<form v-if="booking_info.booking_only_services.length > 0 && booking_info.payment_status != 'completed'" class="form-inline voco-ajax-form float-right" method="post" v-bind:action="urls.discount_save_url">
  @csrf
  <input type="hidden" name="booking_id" v-model="booking_id">
  <input type="hidden" name="tab_name" v-model="tab_name">
  <input type="hidden" name="grand_total" v-model="grand_total">
  <input type="hidden" name="type" value="global">

  <label class="mr-2 mb-3" for="applied_discount_percent">{{ __('Discount') }}</label>
  <div >
  <div class="input-group mb-3">
    <input v-bind:disabled="booking_info.canceled_by > 0" type="text" class="form-control" v-bind:value="booking_info.booking_only_services[0].show_discount_persent" id="applied_discount_percent" name="applied_discount_percent" placeholder="{{ __('Discount') }}" style="max-width:90px">
    <div class="input-group-append">
      <span class="input-group-text">%</span>
    </div>
  </div>

  </div>
</form>
<form v-if="booking_info.booking_only_services.length == 0 &&  booking_info.booking_items.length > 0 && booking_info.payment_status != 'completed'"  class="form-inline voco-ajax-form float-right" method="post" v-bind:action="urls.discount_save_url">
  @csrf
  <input type="hidden" name="booking_id" v-model="booking_id">
  <input type="hidden" name="tab_name" v-model="tab_name">
  <input type="hidden" name="grand_total" v-model="grand_total">
  <input type="hidden" name="type" value="global">

  <label class="mr-2 mb-3" for="applied_discount_percent">{{ __('Discount') }}</label>
  <div >
  <div class="input-group mb-3">
    <input v-bind:disabled="booking_info.canceled_by > 0" type="text" class="form-control" v-bind:value="booking_info.booking_items[0].show_discount_persent" id="applied_discount_percent" name="applied_discount_percent" placeholder="{{ __('Discount') }}" style="max-width:90px">
    <div class="input-group-append">
      <span class="input-group-text">%</span>
    </div>
  </div>

  </div>
</form>
  </div>
</div>
