<h3 class="mb-2">
  <i v-if="booking_info.payment_status==='completed'" class="fa fa-check-circle text-success"></i>
  {{ __('Payments') }}
  <span v-if="booking_info.payment_total > 0" v-bind:class="paymentTotalClass" class="rounded px-2 py-1 float-right" style="font-size: .75em;">
    {{ __('Total:') }} @{{ v_fixed_amount(booking_info.payment_total) }}
  </span>
</h3>

<div v-if="isPaymentExceeded()" class="alert alert-danger">
  {{ __('Payment exceeded!') }}
</div>
<div v-if="booking_info.booking_source != 'Website'">
<table v-if="booking_info.payments.length > 0 || booking_info.void_payments.length > 0" class="table table-sm">
  <thead>
    <tr>
      <th colspan="2">{{ __('Amount Paid') }}</th>
      <th colspan="1">{{ __('Given') }}</th>
      <th>{{ __('Payment Mode') }}</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr v-for="payment_info in booking_info.payments">
      <td colspan="2">
        @{{ v_fixed_amount(payment_info.amount_taken) }}
      </td>
      <td  colspan="1">
        @{{ payment_info.amount_given }}
      </td>
      <td>
        @{{ payment_info.payment_mode_locale }}
        <span v-if="payment_info.card_type">
          -
          @{{ payment_info.card_type_locale }}
        </span>
      </td>
      
        <td>
         
        <form v-if="!booking_info.booking_code" v-bind:action="urls.form_delete_payment_url" class="voco-ajax-form" method="post" data-hook-success="paymentItemDeleted" style="display:inline-block">
          @csrf
          <input type="hidden" name="booking_id" v-model="booking_id">
          <input type="hidden" name="payment_id" v-model="payment_info.payment_id">
          <button type="button" class="btn btn-link text-danger py-0 payment-remove">
            <i class="fa fa-trash-o"></i>
          </button>
        </form>
      </td>
    </tr>
    <tr v-for="payment_info in booking_info.void_payments">
      <td colspan="2" class="text-secondary font-italic" style="text-decoration:line-through">
        @{{ v_fixed_amount(payment_info.amount_taken - payment_info.amount_given) }}
      </td>
      <td class="text-secondary font-italic" style="text-decoration:line-through">
        @{{ payment_info.payment_mode_locale }}
        <span v-if="payment_info.card_type">- @{{ payment_info.card_type_locale }}</span>
      </td>
      <td class="text-secondary font-italic">{{ __('Voided') }}</td>
    </tr>
  </tbody>
</table>
</div>
<table v-if="booking_info.booking_source == 'Website'" class="table table-sm">
  <thead>
    <tr>
      <th colspan="2">{{ __('Amount Paid') }}</th>
      <th colspan="1">{{ __('Given') }}</th>
      <th>{{ __('Payment Mode') }}</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr v-for="payment_info in booking_info.payments">
      <td colspan="2">
        @{{ v_fixed_amount(payment_info.amount_taken - payment_info.amount_given) }}
      </td>
      <td  colspan="1">
        @{{ payment_info.amount_given }}
      </td>
      <td>
        @{{ payment_info.payment_mode_locale }}
        <span v-if="payment_info.card_type">
          -
          @{{ payment_info.card_type_locale }}
        </span>
      </td>
      {{-- <td v-if="booking_info.payment_status != 'completed'"> --}}
        <td>
        <form  v-if="!booking_info.booking_code"  v-bind:action="urls.form_delete_payment_url" class="voco-ajax-form" method="post" data-hook-success="paymentItemDeleted" style="display:inline-block">
          @csrf
          <input type="hidden" name="booking_id" v-model="booking_id">
          <input type="hidden" name="payment_id" v-model="payment_info.payment_id">
          <button type="button" class="btn btn-link text-danger py-0 payment-remove">
            <i class="fa fa-trash-o"></i>
          </button>
        </form>
      </td>
    </tr>
    <tr v-for="payment_info in booking_info.void_payments">
      <td colspan="2" class="text-secondary font-italic" style="text-decoration:line-through">
        @{{ v_fixed_amount(payment_info.amount_taken - payment_info.amount_given) }}
      </td>
      <td class="text-secondary font-italic" style="text-decoration:line-through">
        @{{ payment_info.payment_mode_locale }}
        <span v-if="payment_info.card_type">- @{{ payment_info.card_type_locale }}</span>
      </td>
      <td class="text-secondary font-italic">{{ __('Voided') }}</td>
    </tr>
  </tbody>
</table>

<form v-if="!booking_info.canceled_by && booking_info.payment_status != 'completed' && v_amount(booking_info.booking_total) > v_amount(booking_info.payment_total) && booking_info.booking_type != 'Draft'&&booking_info.booking_type != 'Refund'" class="voco-ajax-form" method="post" v-bind:action="urls.form_save_payment_url">
  @csrf
  <input type="hidden" name="booking_id" v-model="booking_id"> 
  <input type="hidden" name="tab_name" v-model="tab_name">
  <input type="hidden" name="grand_total" v-model="grand_total" >
  

  <div class="row">
    <div class="col pr-1">
      <input type="text" class="form-control" v-model="amount_taken" id="amount_taken" name="amount_taken"  placeholder="{{ __('Taken...') }}" style="width:100px" v-bind:value="suggestedTakenAmount" v-on:input="amountTakenChanged('#amount_taken', '#amount_given','payment')">
      
    </div>

    <div class="col px-1">
      <input type="text" class="form-control" id="amount_given" name="amount_given"  placeholder="{{ __('Given...') }}" style="width:100px" readonly>
    </div>

    <div class="col px-1">
      <select v-on:change="paymentModeChanged($event, '#card_type')" class="form-control" id="payment_mode" name="payment_mode" data-placeholder="{{ __('Payment Mode') }}" style="display:inline-block;width:auto;">
          {{ \Facades\App\Models\BookingPayment::paymentModeDropdown() }}
        </select>
    </div>

    <div class="col px-1">
      <select class="form-control" id="card_type" name="card_type" data-placeholder="{{ __('Card Type') }}" style="display:inline-block;width:auto;display:none">
          {{ \Facades\App\Models\BookingPayment::cardTypeDropdown() }}
        </select>
    </div>

    <div class="col pl-1">
      <button type="submit" id="add_payment_btn" class="btn btn-primary btn-sm">
        <i class="fa fa-check"></i> {{ __('Add Payment') }}
      </button>
    </div>
  </div>
</form>

<form v-if="!booking_info.canceled_by && booking_info.payment_status != 'completed' && parseInt(this.booking_info.booking_total) + parseInt(this.booking_info.payment_total) != 0 && booking_info.booking_type == 'Draft'&& booking_info.booking_type != 'Refund'" class="voco-ajax-form" method="post" v-bind:action="urls.form_save_payment_url">
  @csrf
  <input type="hidden" name="booking_id" v-model="booking_id">
  <input type="hidden" name="tab_name" v-model="tab_name">
  <input type="text" name="grand_total" v-model="amount_taken" >

  <div class="row">
    <div class="col pr-1">
      <input type="text" class="form-control" id="amount_taken"  name="amount_taken"    placeholder="{{ __('Taken...') }}" style="width:100px" v-bind:value="suggestedTakenAmount" v-on:input="amountTakenChanged('#amount_taken', '#amount_given','payment')">
    
    </div>

    <div class="col px-1">
      <input type="text" class="form-control" id="amount_given" name="amount_given" value="" placeholder="{{ __('Given...') }}" style="width:100px" readonly>
    </div>

    <div class="col px-1">
      <select v-on:change="paymentModeChanged($event, '#card_type')" class="form-control" id="payment_mode" name="payment_mode" data-placeholder="{{ __('Payment Mode') }}" style="display:inline-block;width:auto;">
          {{ \Facades\App\Models\BookingPayment::paymentModeDropdown() }}
        </select>
    </div>

    <div class="col px-1">
      <select class="form-control" id="card_type" name="card_type" data-placeholder="{{ __('Card Type') }}" style="display:inline-block;width:auto;display:none">
          {{ \Facades\App\Models\BookingPayment::cardTypeDropdown() }}
        </select>
    </div>

    <div class="col pl-1">
      <button id="add_payment_btn" type="submit" class="btn btn-primary btn-sm">
        <i class="fa fa-check"></i> {{ __('Add Payment') }}
      </button>
    </div>
  </div>
</form>

<form v-if="!booking_info.canceled_by && booking_info.booking_type == 'Refund' && this.booking_info.payment_status != 'completed'" class="voco-ajax-form" method="post" v-bind:action="urls.form_save_payment_url">
  @csrf
  <input type="hidden" name="booking_id" v-model="booking_id"> 
  <input type="hidden" name="tab_name" v-model="tab_name">
  <input type="hidden" name="grand_total" v-model="grand_total" >

  <div class="row">
    <div class="col pr-1">
      <input type="text" class="form-control" v-model="amount_taken" id="amount_taken" name="amount_taken"  placeholder="{{ __('Taken...') }}" style="width:100px" v-bind:value="suggestedTakenAmount" v-on:input="amountTakenChanged('#amount_taken', '#amount_given','refund')">
      
    </div>

    <div class="col px-1">
      <input type="text" class="form-control" id="amount_given" name="amount_given"  placeholder="{{ __('Given...') }}" style="width:100px" readonly>
    </div>

    <div class="col px-1">
      <select v-on:change="paymentModeChanged($event, '#card_type')" class="form-control" id="payment_mode" name="payment_mode" data-placeholder="{{ __('Payment Mode') }}" style="display:inline-block;width:auto;">
          {{ \Facades\App\Models\BookingPayment::paymentModeDropdown() }}
        </select>
    </div>

    <div class="col px-1">
      <select class="form-control" id="card_type" name="card_type" data-placeholder="{{ __('Card Type') }}" style="display:inline-block;width:auto;display:none">
          {{ \Facades\App\Models\BookingPayment::cardTypeDropdown() }}
        </select>
    </div>

    <div class="col pl-1">
      <button type="submit" id="add_payment_btn" class="btn btn-primary btn-sm">
        <i class="fa fa-check"></i> {{ __('Add Payment') }}
      </button>
    </div>
  </div>
</form>
