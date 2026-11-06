
<form v-if="!booking_info.canceled_by && v_amount(booking_info.booking_total) == v_amount(booking_info.payment_total) && booking_info.booking_type != 'Draft'" method="post" v-bind:action="urls.form_final_update_url">
  @csrf
  
  
  <input type="hidden" name="booking_id" v-model="booking_id">
  <input type="hidden" name="tab_name" v-model="tab_name">

  <div class="row">
    <div class="col pl-1 text-right">
      <a class="btn btn-primary mr-2" v-bind:href="urls.button_cancel_url">
        <i class="fa fa-backward"></i> {{ __('Back to List') }}
      </a>

      {{-- <a class="btn btn-primary mr-2" v-bind:href="urls.booking_invoice_url" target="_blank" > --}}
        <button class="btn btn-primary mr-2" :disabled="!canPrintUpdate" v-on:click="updateinvoice(urls.booking_invoice_url)" >
        <i class="fa fa-print"></i> {{ __('Print Update') }}
        </button>
      <a class="btn btn-primary mr-2" v-bind:href="urls.booking_invoice_gift_url" target="_blank">
        <i class="fa fa-print"></i> {{ __('Print Gift') }}
      </a>

      <button type="submit" class="btn btn-primary" :disabled="!canSubmitUpdate">
        <i class="fa fa-check-circle"></i> {{ __('Update') }}
      </button>

      <button type="button" class="btn btn-link notify_whatsapp" v-bind:data-booking-id="booking_id" v-bind:data-url="urls.booking_notify_whatsapp_url">
        <i class="fa fa-whatsapp text-success" style="font-size:2.5rem"></i>
      </button>
    </div>
  </div>
</form>


<form v-if="!booking_info.canceled_by && parseInt(this.booking_info.booking_total) + parseInt(this.booking_info.payment_total) == 0 && booking_info.booking_type == 'Draft'" method="post" v-bind:action="urls.form_final_update_url">
  @csrf
 
  <input type="hidden" name="booking_id" v-model="booking_id">
  <input type="hidden" name="tab_name" v-model="tab_name">

  <div class="row">
    <div class="col pl-1 text-right">
      <a class="btn btn-primary mr-2" v-bind:href="urls.button_cancel_url">
        <i class="fa fa-backward"></i> {{ __('Back to List') }}
      </a>

      <a class="btn btn-primary mr-2" :class="{ disabled: !canPrintUpdate }" v-on:click="canPrintUpdate && updateinvoice(urls.booking_invoice_url)" >
        <i class="fa fa-print"></i> {{ __('Print Update') }}
      </a>
      <a class="btn btn-primary mr-2" v-bind:href="urls.booking_invoice_gift_url" target="_blank">
        <i class="fa fa-print"></i> {{ __('Print Gift') }}
      </a>

      <button type="submit" class="btn btn-primary" :disabled="!canSubmitUpdate">
        <i class="fa fa-check-circle"></i> {{ __('Update') }}
      </button>

      <button type="button" class="btn btn-link notify_whatsapp" v-bind:data-booking-id="booking_id" v-bind:data-url="urls.booking_notify_whatsapp_url">
        <i class="fa fa-whatsapp text-success" style="font-size:2.5rem"></i>
      </button>
    </div>
  </div>
</form>
