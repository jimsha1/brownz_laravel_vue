<div v-if="booking_info.booking_total > 0" class="table-responsive">
  <table class="table table-sm info-bottom-table-bz" style="overflow: auto; min-width: 840px; width: 100%; ">
    <thead>
      <tr>
        <th style="width: 150px;">{{ __('Item') }}</th>
        <th>{{ __('Service Type') }}</th>
        <th>{{ __('Quantity') }}</th>
        <th>{{ __('Price') }}</th>
        <th v-if="booking_info.applied_discount_percent > 0">{{ __('Discount') }}</th>
        <th v-if="booking_info.applied_discount_percent > 0">{{ __('Net') }}</th>
        <th style="width: 250px;">{{ __('Tax') }}</th>
        <th>{{ __('Total') }}</th>
      </tr>
    </thead>
    <tbody>
      <template v-for="bserv in booking_info.booking_only_services">
        <tr>
          <td>@{{ bserv.service_name_locale }}</td>
          <td>
            @{{ bserv.service_type }}
            <div v-if="bserv.service_type === 'Home' && bserv.latitude && bserv.longitude">
              @{{ bserv.latitude }} / @{{ bserv.longitude }}
            </div>
          </td>
          <td>1</td>
          <td>@{{ bserv.service_booking_price }}</td>
          <td v-if="booking_info.applied_discount_percent > 0">@{{ v_fixed_amount(bserv.discount_amount) }}</td>
          <td v-if="booking_info.applied_discount_percent > 0">@{{ v_fixed_amount(bserv.service_booking_price - bserv.discount_amount) }}</td>
          <td>@{{ bserv.tax_amount > 0 ? v_fixed_amount(bserv.tax_amount) : '--' }}</td>
          <td>@{{ v_fixed_amount(bserv.line_total) }}</td>
        </tr>
        <tr>
          <td class="border-top-0" colspan="2">
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
          <td v-bind:colspan="booking_info.applied_discount_percent > 0 ? 3 : 2" class="border-top-0">
            {{ __('Beautician:') }}
            <span v-if="bserv.beautician">
              @{{ bserv.beautician.name_locale }}
              (@{{ bserv.beautician.beautician_code }})
            </span>
            <span v-else class="text-muted font-italic">
              {{ __('Unassigned') }}
            </span>
          </td>
        </tr>
      </template>
      <tr v-for="o_item in booking_info.booking_items">
        <td colspan="2">@{{ o_item.item_name_locale }}</td>
        <td>@{{ o_item.quantity }}</td>
        <td>@{{ o_item.unit_price }}</td>
        <td v-if="booking_info.applied_discount_percent > 0">@{{ v_fixed_amount(o_item.discount_amount) }}</td>
        <td v-if="booking_info.applied_discount_percent > 0">@{{ v_fixed_amount(o_item.unit_price - o_item.discount_amount) }}</td>
        <td>@{{ o_item.tax_amount > 0 ? v_fixed_amount(o_item.tax_amount) : '--' }}</td>
        <td>@{{ v_fixed_amount(o_item.line_total) }}</td>
      </tr>
    </tbody>
  </table>
</div>
