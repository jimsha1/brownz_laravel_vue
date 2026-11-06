<form v-if="!booking_info.canceled_by" v-bind:action="urls.item_form_save_url" class="voco-ajax-form" method="post" data-hook-success="bookingServiceSaved">
  @csrf
  <input type="hidden" name="booking_id" v-model="booking_id">
  <input type="hidden" name="line_item_type" value="item">

  <div class="form-group">
    <select class="form-control" id="add_price_id" name="add_price_id" data-placeholder="Search for items">
      <option value=""></option>
    </select>
  </div>
</form>
