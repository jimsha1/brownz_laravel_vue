








<div v-if="booking_info.booking_total > 0" class="table-responsive">
  <table class="table table-sm">
    <thead>
      <tr>
        <th>{{ __('Item') }}</th>
        <th>{{ __('Quantity') }}</th>
        <th>{{ __('Price') }}</th>
        <th >{{ __('Discount') }}</th>
        {{-- <th>{{ __('Individual Discount') }}</th> --}}

        <th>{{ __('Net') }}</th>
        <th>{{ __('Tax') }}</th>
        <th>{{ __('Total') }}</th>
        <th v-if="!booking_info.canceled_by && booking_info.payment_status != 'completed'" class="text-center">{{ __('Remove') }}</th>
      </tr>
    </thead>
    <tbody>
      <template  v-if="booking_info.booking_only_services.length > 0"  v-for="bserv in booking_info.booking_only_services">

        <tr>

          <td>@{{ bserv.service_name_locale }}</td>
          <td>1</td>
          <td :id="price_html_id+bserv.booking_service_id">@{{ bserv.service_booking_price }}</td>
          <td  v-if="bserv.individual_discount_amt == 0">@{{ v_fixed_amount(bserv.discount_amount) }}</td>
          <td v-if="bserv.individual_discount_amt > 0">@{{ v_fixed_amount(bserv.individual_discount_amt) }}</td>
          <td v-if="bserv.individual_discount_amt == 0">@{{ v_totalNetTotal(bserv.service_booking_price - bserv.discount_amount,bserv.individual_discount_amt) }}</td>
          <td v-if="bserv.individual_discount_amt > 0">@{{ v_totalNetTotal(bserv.service_booking_price,bserv.individual_discount_amt) }}</td>
          <td :id="tax_html_id+bserv.booking_service_id">@{{ bserv.tax_amount > 0 ? v_fixed_amount(bserv.tax_amount) : '--' }}</td>
          <td v-if="bserv.individual_discount_amt == 0" :id="total_html_id+bserv.booking_service_id">@{{ v_totalPrice(bserv.service_booking_price - bserv.discount_amount - bserv.individual_discount_amt,bserv.tax_amount)  }}</td>
          <td v-if="bserv.individual_discount_amt > 0" :id="total_html_id+bserv.booking_service_id">@{{ v_totalPrice(bserv.service_booking_price -  bserv.individual_discount_amt,bserv.tax_amount)  }}</td>
          {{-- <td :id="total_html_id+bserv.booking_service_id">@{{ v_fixed_amount(bserv.line_total) - v_fixed_amount(bserv.discount_amount) }}</td> --}}
          <td v-if="!booking_info.canceled_by && booking_info.payment_status != 'completed'" class="text-center">
            <form v-bind:action="urls.item_form_delete_url" class="voco-ajax-form" method="post" data-hook-success="bookingServiceDeleted" style="display:inline-block">
              @csrf
              <input type="hidden" name="booking_id" v-model="booking_id">
              <input type="hidden" name="service_id" v-model="bserv.service_id">
              <input type="hidden" name="booking_service_id" v-model="bserv.booking_service_id ">
              <button type="button" class="btn btn-link text-danger py-0 booking-line-remove">
                <i class="fa fa-times"></i>
              </button>
            </form>
          </td>
        </tr>
        <tr>
          <td class="border-top-0">
            {{ __('Date:') }} @{{ bserv.appointment_date | vc_date_expanded }}
          </td>
          <td v-bind:colspan="booking_info.applied_discount_percent > 0 ? 3 : 2" class="border-top-0">
            {{ __('Time:') }}
            <span v-if="bserv.slot_start">
              @{{ bserv.slot_start | time_hm }}
            </span>
            <span v-else class="text-muted font-italic">
              {{ __('Unallocated') }}
            </span>
          </td>
          <td   v-bind:colspan="booking_info.applied_discount_percent > 0 ? 3 : 2" class="border-top-0">
            {{ __('Beautician:') }}
            <span :id="beatician_html_id+bserv.booking_service_id" v-if="bserv.beautician">
              @{{ bserv.beautician.name_locale }}
              (@{{ bserv.beautician.beautician_code }})
            </span>
            <span v-else class="text-muted font-italic">
              {{ __('Unassigned') }}
            </span>
          </td>
          <!--
          <td class="border-top-0">@{{ bserv.duration_minutes | vc_duration_short }}</td>
          -->
          <td class="border-top-0 text-center" v-if="!booking_info.canceled_by && booking_info.payment_status != 'completed'">
            <button v-if="booking_info.payment_status != 'completed'" type="button" class="badge badge-danger" v-bind:data-service-id="bserv.service_id" v-bind:data-booking-date="bserv.appointment_date" v-on:click="getShiftData(bserv,'#'+price_html_id+bserv.booking_service_id,'#'+tax_html_id+bserv.booking_service_id,'#'+total_html_id+bserv.booking_service_id,'#'+total_price_discount+bserv.booking_service_id,'#'+service_id_var+bserv.booking_service_id,'#'+beatician_html_id+bserv.booking_service_id,bserv.booking_service_id,bserv.shift_id)"    data-toggle="tooltip" title="{{ __('Change beautician and time slot') }}">
              <i class="fa fa-clock-o"></i>
            </button>
            {{-- <div v-if="service_id > 0 || bserv.discount_amount > 0" class="row"> --}}
              <div v-if="booking_info.payment_status != 'completed'"  class="row pull-right">
              <form id="line_discount_form+bserv.booking_service_id" class="form-inline voco-ajax-form float-right" method="post" v-bind:action="urls.discount_save_url">
                @csrf
                <input type="hidden" name="booking_id" v-model="booking_id">
                <input type="hidden" name="tab_name" v-model="tab_name">
                <input type="hidden" :id="service_id_var+bserv.booking_service_id" name="service_id" >
                <input type="hidden" :id="total_price_discount+bserv.booking_service_id" name="total_price">
            <input v-if="bserv.individual_discount_percent > 0" v-bind:disabled="booking_info.canceled_by > 0" type="text" :id="discount_percent+bserv.booking_service_id" class="form-control pull-right" v-bind:value="bserv.individual_discount_percent" v-on:change="lineDiscount($event,bserv,'#'+line_discount_form+bserv.booking_service_id,'#'+service_id_var+bserv.booking_service_id,'#'+total_price_discount+bserv.booking_service_id,'#'+discount_percent+bserv.booking_service_id)"  name="applied_discount_percent" placeholder="{{ __('Discount') }}" style="max-width:90px">
            <input  v-if="bserv.individual_discount_percent == 0" v-bind:disabled="booking_info.canceled_by > 0" type="text" :id="discount_percent+bserv.booking_service_id" class="form-control pull-right" v-bind:value="bserv.discount_percent" v-on:change="lineDiscount($event,bserv,'#'+line_discount_form+bserv.booking_service_id,'#'+service_id_var+bserv.booking_service_id,'#'+total_price_discount+bserv.booking_service_id,'#'+discount_percent+bserv.booking_service_id)"  name="applied_discount_percent" placeholder="{{ __('Discount') }}" style="max-width:90px">
            <div class="input-group-append">
              <span class="input-group-text">%</span>
            </div>
              </form>
            </div>
          </td>
        </tr>

      </template>
      <template v-if="booking_info.booking_items.length > 0" v-for="o_item in booking_info.booking_items">
      <tr >
        <td>@{{ o_item.item_name_locale }}</td>
        <td>@{{ o_item.quantity }}</td>
        <td>@{{ o_item.unit_price }}</td>
        <td  v-if="o_item.individual_discount_amt == 0">@{{ v_fixed_amount(o_item.discount_amount) }}</td>
          <td v-if="o_item.individual_discount_amt > 0">@{{ v_fixed_amount(o_item.individual_discount_amt) }}</td>
          <td v-if="o_item.individual_discount_amt == 0">@{{ v_totalNetTotal(o_item.unit_price - o_item.discount_amount,o_item.individual_discount_amt) }}</td>
          <td v-if="o_item.individual_discount_amt > 0">@{{ v_totalNetTotal(o_item.unit_price,o_item.individual_discount_amt) }}</td>

        <td>@{{ o_item.tax_amount > 0 ? v_fixed_amount(o_item.tax_amount) : '--' }}</td>
        <td v-if="o_item.individual_discount_amt == 0" :id="total_item_html_id+o_item.booking_item_id">@{{ v_totalPrice(o_item.unit_price - o_item.discount_amount - o_item.individual_discount_amt,o_item.tax_amount)  }}</td>
          <td v-if="o_item.individual_discount_amt > 0" :id="total_item_html_id+o_item.booking_item_id">@{{ v_totalPrice(o_item.unit_price -  o_item.individual_discount_amt,o_item.tax_amount)  }}</td>
          {{-- <td :id="total_html_id+bserv.booking_service_id">@{{ v_fixed_amount(bserv.line_total) - v_fixed_amount(bserv.discount_amount) }}</td> --}}

          <td v-if="!booking_info.canceled_by && booking_info.payment_status != 'completed'"  class="text-center">
          <form  v-bind:action="urls.item_form_delete_url"
            class="voco-ajax-form"
            method="post"
            data-hook-success="bookingServiceDeleted"
            style="display:inline-block"
          >
            @csrf
            <input type="hidden" name="booking_id" v-model="booking_id">
            <input type="hidden" name="item_id" v-model="o_item.item_id">
            <input type="hidden" name="price_id" v-model="o_item.price_id">
            <input type="hidden" name="booking_item_id" v-model="o_item.booking_item_id">
            <button type="button" class="btn btn-link text-danger py-0 booking-line-remove">
              <i class="fa fa-times"></i>
            </button>
          </form>
        </td>


      </tr>
      <tr><td class="border-top-0">

      </td> <td colspan="2" class="border-top-0">

        </td> <td colspan="2" class="border-top-0">
      </td> <td class="border-top-0 text-center">


             <div v-if="booking_info.payment_status != 'completed'" class="row pull-right">
              <form id="line_discount_form+o_item.booking_item_id" class="form-inline voco-ajax-form float-right" method="post" v-bind:action="urls.discount_save_url">
                @csrf
                <input type="hidden" name="booking_id" v-model="booking_id">
                <input type="hidden" name="tab_name" v-model="tab_name">
                <input type="hidden" :id="service_id_var+o_item.booking_item_id" name="item_id" >
                <input type="hidden" :id="total_price_discount+o_item.booking_item_id" name="total_price">
            <input v-if="o_item.individual_discount_percent > 0" v-bind:disabled="booking_info.canceled_by > 0" type="text" :id="discount_percent_item+o_item.booking_item_id" class="form-control pull-right" v-bind:value="o_item.individual_discount_percent" v-on:change="lineDiscountItem($event,o_item,'#'+line_discount_form+o_item.booking_item_id,'#'+service_id_var+o_item.booking_item_id,'#'+total_price_discount+o_item.booking_item_id,'#'+discount_percent_item+o_item.booking_item_id)"  name="applied_discount_percent" placeholder="{{ __('Discount') }}" style="max-width:90px">
            <input  v-if="o_item.individual_discount_percent == 0" v-bind:disabled="booking_info.canceled_by > 0" type="text" :id="discount_percent_item+o_item.booking_item_id" class="form-control pull-right" v-bind:value="o_item.discount_percent" v-on:change="lineDiscountItem($event,o_item,'#'+line_discount_form+o_item.booking_item_id,'#'+service_id_var+o_item.booking_item_id,'#'+total_price_discount+o_item.booking_item_id,'#'+discount_percent_item+o_item.booking_item_id)"  name="applied_discount_percent" placeholder="{{ __('Discount') }}" style="max-width:90px">
            <div class="input-group-append">
              <span class="input-group-text">%</span>
            </div>
              </form></div></td></tr>
      </template>
    </tbody>
  </table>
</div>

{{------------------------- refund section -------------------------------------------}}
<div v-if="booking_info.booking_total < 0" class="table-responsive">
  <table class="table table-sm">
    <thead>
      <tr>
        <th>{{ __('Item') }}</th>
        <th>{{ __('Quantity') }}</th>
        <th>{{ __('Price') }}</th>
        <th >{{ __('Discount') }}</th>
        {{-- <th>{{ __('Individual Discount') }}</th> --}}

        <th>{{ __('Net') }}</th>
        <th>{{ __('Tax') }}</th>
        <th>{{ __('Total') }}</th>
        <th v-if="!booking_info.canceled_by && booking_info.payment_status != 'completed'" class="text-center">{{ __('Remove') }}</th>
      </tr>
    </thead>
    <tbody>
      <template  v-if="booking_info.booking_only_services.length > 0"  v-for="bserv in booking_info.booking_only_services">

        <tr>

          <td>@{{ bserv.service_name_locale }}</td>
          <td>-1</td>
          <td :id="price_html_id+bserv.booking_service_id">@{{ bserv.service_booking_price }}</td>
          <td  v-if="bserv.individual_discount_amt == 0">@{{ v_fixed_amount(bserv.discount_amount) * -1 }}</td>
          <td v-if="bserv.individual_discount_amt > 0">@{{ v_fixed_amount(bserv.individual_discount_amt) * -1 }}</td>
          <td v-if="bserv.individual_discount_amt == 0">@{{ v_totalNetTotal(bserv.service_booking_price - bserv.discount_amount,bserv.individual_discount_amt) * -1  }}</td>
          <td v-if="bserv.individual_discount_amt > 0">@{{ v_totalNetTotal(bserv.service_booking_price,bserv.individual_discount_amt) * -1   }}</td>
          <td :id="tax_html_id+bserv.booking_service_id">@{{ bserv.tax_amount > 0 ? v_fixed_amount(bserv.tax_amount) * -1  : '--' }}</td>
          <td v-if="bserv.individual_discount_amt == 0" :id="total_html_id+bserv.booking_service_id">@{{ v_totalPrice(bserv.service_booking_price - bserv.discount_amount - bserv.individual_discount_amt,bserv.tax_amount) *-1   }}</td>
          <td v-if="bserv.individual_discount_amt > 0" :id="total_html_id+bserv.booking_service_id">@{{ v_totalPrice(bserv.service_booking_price -  bserv.individual_discount_amt,bserv.tax_amount) *-1 }}</td>
          {{-- <td :id="total_html_id+bserv.booking_service_id">@{{ v_fixed_amount(bserv.line_total) - v_fixed_amount(bserv.discount_amount) }}</td> --}}
          <td v-if="!booking_info.canceled_by && booking_info.payment_status != 'completed'" class="text-center">
            <form v-bind:action="urls.item_form_delete_url" class="voco-ajax-form" method="post" data-hook-success="bookingServiceDeleted" style="display:inline-block">
              @csrf
              <input type="hidden" name="booking_id" v-model="booking_id">
              <input type="hidden" name="service_id" v-model="bserv.service_id">
              <input type="hidden" name="booking_service_id" v-model="bserv.booking_service_id ">
              {{-- <button type="button" class="btn btn-link text-danger py-0 booking-line-remove">
                <i class="fa fa-times"></i>
              </button> --}}
            </form>
          </td>
        </tr>
        <tr>
          <td class="border-top-0">
            {{ __('Date:') }} @{{ bserv.appointment_date | vc_date_expanded }}
          </td>
          <td v-bind:colspan="booking_info.applied_discount_percent > 0 ? 3 : 2" class="border-top-0">
            {{ __('Time:') }}
            <span v-if="bserv.slot_start">
              @{{ bserv.slot_start | time_hm }}
            </span>
            <span v-else class="text-muted font-italic">
              {{ __('Unallocated') }}
            </span>
          </td>
          <td   v-bind:colspan="booking_info.applied_discount_percent > 0 ? 3 : 2" class="border-top-0">
            {{ __('Beautician:') }}
            <span :id="beatician_html_id+bserv.booking_service_id" v-if="bserv.beautician">
              @{{ bserv.beautician.name_locale }}
              (@{{ bserv.beautician.beautician_code }})
            </span>
            <span v-else class="text-muted font-italic">
              {{ __('Unassigned') }}
            </span>
          </td>
          <!--
          <td class="border-top-0">@{{ bserv.duration_minutes | vc_duration_short }}</td>
          -->
          <td class="border-top-0 text-center" v-if="!booking_info.canceled_by && booking_info.payment_status != 'completed'">
            <button v-if="booking_info.payment_status != 'completed'" type="button" class="badge badge-danger" v-bind:data-service-id="bserv.service_id" v-bind:data-booking-date="bserv.appointment_date" v-on:click="getShiftData(bserv,'#'+price_html_id+bserv.booking_service_id,'#'+tax_html_id+bserv.booking_service_id,'#'+total_html_id+bserv.booking_service_id,'#'+total_price_discount+bserv.booking_service_id,'#'+service_id_var+bserv.booking_service_id,'#'+beatician_html_id+bserv.booking_service_id,bserv.booking_service_id,bserv.shift_id)"    data-toggle="tooltip" title="{{ __('Change beautician and time slot') }}">
              <i class="fa fa-clock-o"></i>
            </button>
            {{-- <div v-if="service_id > 0 || bserv.discount_amount > 0" class="row"> --}}
              <div v-if="booking_info.payment_status != 'completed'"  class="row pull-right">
              <form id="line_discount_form+bserv.booking_service_id" class="form-inline voco-ajax-form float-right" method="post" v-bind:action="urls.discount_save_url">
                @csrf
                <input type="hidden" name="booking_id" v-model="booking_id">
                <input type="hidden" name="tab_name" v-model="tab_name">
                <input type="hidden" :id="service_id_var+bserv.booking_service_id" name="service_id" >
                <input type="hidden" :id="total_price_discount+bserv.booking_service_id" name="total_price">
            <input v-if="bserv.individual_discount_percent > 0" v-bind:disabled="booking_info.canceled_by > 0" type="text" :id="discount_percent+bserv.booking_service_id" class="form-control pull-right" v-bind:value="bserv.individual_discount_percent" v-on:change="lineDiscount($event,bserv,'#'+line_discount_form+bserv.booking_service_id,'#'+service_id_var+bserv.booking_service_id,'#'+total_price_discount+bserv.booking_service_id,'#'+discount_percent+bserv.booking_service_id)"  name="applied_discount_percent" placeholder="{{ __('Discount') }}" style="max-width:90px">
            <input  v-if="bserv.individual_discount_percent == 0" v-bind:disabled="booking_info.canceled_by > 0" type="text" :id="discount_percent+bserv.booking_service_id" class="form-control pull-right" v-bind:value="bserv.discount_percent" v-on:change="lineDiscount($event,bserv,'#'+line_discount_form+bserv.booking_service_id,'#'+service_id_var+bserv.booking_service_id,'#'+total_price_discount+bserv.booking_service_id,'#'+discount_percent+bserv.booking_service_id)"  name="applied_discount_percent" placeholder="{{ __('Discount') }}" style="max-width:90px">
            <div class="input-group-append">
              <span class="input-group-text">%</span>
            </div>
              </form>
            </div>
          </td>
        </tr>

      </template>
      <template v-if="booking_info.booking_items.length > 0" v-for="o_item in booking_info.booking_items">
      <tr >
        <td>@{{ o_item.item_name_locale }}</td>
        <td>@{{ o_item.quantity }}</td>
        <td>@{{ o_item.unit_price }}</td>
        <td  v-if="o_item.individual_discount_amt == 0">@{{ v_fixed_amount(o_item.discount_amount) }}</td>
          <td v-if="o_item.individual_discount_amt > 0">@{{ v_fixed_amount(o_item.individual_discount_amt) }}</td>
          <td v-if="o_item.individual_discount_amt == 0">@{{ v_totalNetTotal(o_item.unit_price - o_item.discount_amount,o_item.individual_discount_amt) }}</td>
          <td v-if="o_item.individual_discount_amt > 0">@{{ v_totalNetTotal(o_item.unit_price,o_item.individual_discount_amt) }}</td>

        <td>@{{ o_item.tax_amount > 0 ? v_fixed_amount(o_item.tax_amount) : '--' }}</td>
        <td v-if="o_item.individual_discount_amt == 0" :id="total_item_html_id+o_item.booking_item_id">@{{ v_totalPrice(o_item.unit_price - o_item.discount_amount - o_item.individual_discount_amt,o_item.tax_amount)  }}</td>
          <td v-if="o_item.individual_discount_amt > 0" :id="total_item_html_id+o_item.booking_item_id">@{{ v_totalPrice(o_item.unit_price -  o_item.individual_discount_amt,o_item.tax_amount)  }}</td>
          {{-- <td :id="total_html_id+bserv.booking_service_id">@{{ v_fixed_amount(bserv.line_total) - v_fixed_amount(bserv.discount_amount) }}</td> --}}

        <td v-if="!booking_info.canceled_by" class="text-center">
          <form v-bind:action="urls.item_form_delete_url"
            class="voco-ajax-form"
            method="post"
            data-hook-success="bookingServiceDeleted"
            style="display:inline-block"
          >
            @csrf
            <input type="hidden" name="booking_id" v-model="booking_id">
            <input type="hidden" name="item_id" v-model="o_item.item_id">
            <input type="hidden" name="price_id" v-model="o_item.price_id">
            <input type="hidden" name="booking_item_id" v-model="o_item.booking_item_id">
            <button type="button" class="btn btn-link text-danger py-0 booking-line-remove">
              <i class="fa fa-times"></i>
            </button>
          </form>
        </td>


      </tr>
      <tr><td class="border-top-0">

      </td> <td colspan="2" class="border-top-0">

        </td> <td colspan="2" class="border-top-0">
      </td> <td class="border-top-0 text-center">


             <div v-if="booking_info.payment_status != 'completed'" class="row pull-right">
              <form id="line_discount_form+o_item.booking_item_id" class="form-inline voco-ajax-form float-right" method="post" v-bind:action="urls.discount_save_url">
                @csrf
                <input type="hidden" name="booking_id" v-model="booking_id">
                <input type="hidden" name="tab_name" v-model="tab_name">
                <input type="hidden" :id="service_id_var+o_item.booking_item_id" name="item_id" >
                <input type="hidden" :id="total_price_discount+o_item.booking_item_id" name="total_price">
            <input v-if="o_item.individual_discount_percent > 0" v-bind:disabled="booking_info.canceled_by > 0" type="text" :id="discount_percent_item+o_item.booking_item_id" class="form-control pull-right" v-bind:value="o_item.individual_discount_percent" v-on:change="lineDiscountItem($event,o_item,'#'+line_discount_form+o_item.booking_item_id,'#'+service_id_var+o_item.booking_item_id,'#'+total_price_discount+o_item.booking_item_id,'#'+discount_percent_item+o_item.booking_item_id)"  name="applied_discount_percent" placeholder="{{ __('Discount') }}" style="max-width:90px">
            <input  v-if="o_item.individual_discount_percent == 0" v-bind:disabled="booking_info.canceled_by > 0" type="text" :id="discount_percent_item+o_item.booking_item_id" class="form-control pull-right" v-bind:value="o_item.discount_percent" v-on:change="lineDiscountItem($event,o_item,'#'+line_discount_form+o_item.booking_item_id,'#'+service_id_var+o_item.booking_item_id,'#'+total_price_discount+o_item.booking_item_id,'#'+discount_percent_item+o_item.booking_item_id)"  name="applied_discount_percent" placeholder="{{ __('Discount') }}" style="max-width:90px">
            <div class="input-group-append">
              <span class="input-group-text">%</span>
            </div>
              </form></div></td></tr>
      </template>
    </tbody>
  </table>
</div>




<hr>
