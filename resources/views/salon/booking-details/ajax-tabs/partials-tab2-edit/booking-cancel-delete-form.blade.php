<div v-if="!booking_info.canceled_by && (booking_info.booking_items.length > 0 || booking_info.booking_only_services.length > 0 || booking_info.payment_total > 0)" class="alert alert-danger text-center mt-4">
  <h3>
    {{ __('Advanced') }}
  </h3>

  <p v-if="booking_info.payment_total > 0">
    {{ __('Void all payments before cancelling this order.') }}
  </p>
  <p v-else>
    {{ __('Click the button below to cancel this order and deallocate the resources.') }}
  </p>

  <form class="voco-ajax-form" method="post" v-bind:action="urls.form_cancel_booking_url">
    @csrf
    <input type="hidden" name="booking_id" v-model="booking_id">

    <button v-bind:disabled="booking_info.payment_total > 0" type="submit" class="btn btn-danger cancel-booking">
      <i class="fa fa-ban"></i> {{ __('Cancel Order') }}
    </button>
  </form>
</div>

<div v-if="booking_info.canceled_by" class="alert alert-danger text-center mt-4">
  <h3>
    {{ __('Canceled') }}
  </h3>

  <p>
    {{ __('This booking has canceled!') }}
  </p>
</div>
