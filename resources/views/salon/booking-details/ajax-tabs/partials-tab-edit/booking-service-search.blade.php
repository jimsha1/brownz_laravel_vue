 <form v-if="booking_info.payment_status != 'completed'" v-bind:action="urls.item_form_save_url" class="voco-ajax-form" method="post" data-hook-success="bookingServiceSaved">
  @csrf
  <input type="hidden" name="booking_id" v-model="booking_id">
  <input type="hidden" name="line_item_type" id="line_item_type" value="service">
  <input type="hidden" name="service_id" id="d_service_id">
  <input type="hidden" name="price_id" id="d_price_id">
  <input type="hidden" name="bath_no" id="d_bath_no">

  <div class="form-group">
    <select class="form-control" id="add_service_id" name="add_service_id" data-placeholder="{{ __('Search for services and items') }}">
      <option value=""></option>
    </select>
  </div>
</form>
