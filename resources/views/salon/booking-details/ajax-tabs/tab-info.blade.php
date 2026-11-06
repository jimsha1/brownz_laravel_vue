<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  .bg-even-info tr:nth-child(odd){
    background-color: rgba(230, 205, 127, 0.2);
  }
  .bg-even-info tr:nth-child(even){
    background-color: rgba(255, 234, 169, 0.2);
  }
  .bg-even-info tr td{
    border: none;
    padding: 15px 12px;
    font-size: 15px;
    color: #1D1D1D;
    margin: 0;
  }
  .info-bottom-table-bz thead tr th{
    border: none !important;
    font-size: 16px;
    font-weight: 400;
    color: #1D1D1D;
  }
  .info-bottom-table-bz tbody td{
    border: none !important;
    font-size: 14px;
    color: #979696;
  }
  .badge-warning{
    font-size: 15px;
    font-weight: 500;
    color: #52A8D9;
    background-color: rgba(82, 168, 217, 0.2);
    padding: 10px 14px;
    width: max-content;
    border-radius: 8px;
    margin: 0;
  }

</style>
<div id="vue-details-tab-info" class="info-sec-tab-cont" style="margin: 0px -1rem;">
  <template>
    <table class="table">
      <tbody class="bg-even-info">
        <tr>
          <td style="width:20%">{{ __('ID') }}</td>
          <td>@{{ '#' + booking_info.booking_id }}</td>
        </tr>
        <tr>
          <td>{{ __('Booking Code') }}</td>
          <td>@{{ booking_info.booking_code?booking_info.booking_code:'Draft' }}</td>
        </tr>
        <tr>
          <td>{{ __('Client') }}</td>
          <td>@{{ booking_info.client_name }}</td>
        </tr>
        {{-- <tr>
          <td>{{ __('Booking Source') }}</td>
          <td>@{{ booking_info.booking_source }}</td>
        </tr> --}}
        <tr>
          <td>{{ __('Booking Amount') }}</td>
          <td>@{{ booking_info.booking_total }}</td>
        </tr>
        <tr>
          <td>{{ __('Payment Status') }}</td>
          <td>
            <span v-if="booking_info.payment_status==='pending'" class="badge badge-warning">@{{ booking_info.payment_status | vc_ucwords }}</span>
            <span v-else-if="booking_info.payment_status==='completed'" class="badge badge-success">@{{ booking_info.payment_status | vc_ucwords }}</span>
            <span v-else class="badge badge-secondary">@{{ booking_info.payment_status | vc_ucwords }}</span>
          </td>
        </tr>
        <tr>
          <td>{{ __('Created') }}</td>
          <td>
            @{{ booking_info.created_user.name }}
            on
            <span class="text-muted font-italic">
              @{{ booking_info.created_at | vc_datetime_expanded }}
            </span>
          </td>
        </tr>
        <tr>
          <td>{{ __('Updated') }}</td>
          <td>
            @{{ booking_info.updated_user.name }}
            on
            <span class="text-muted font-italic">
              @{{ booking_info.updated_at | vc_datetime_expanded }}
            </span>
          </td>
        </tr>
        <tr v-if="booking_info.edit_logs && booking_info.edit_logs.length">
          <td>{{ __('Edit Reason') }}</td>
          <td>
            <div v-for="(log, index) in booking_info.edit_logs" :key="log.id" :class="{ 'mt-2 pt-2 border-top': index > 0 }">
              <div>@{{ log.edit_reason }}</div>
              <div class="text-muted font-italic">
                @{{ (log.edited_user && log.edited_user.name) ? log.edited_user.name : '' }}
                <span v-if="log.created_at">
                  on @{{ log.created_at | vc_datetime_expanded }}
                </span>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

@include('salon.booking-details.ajax-tabs.partials-tab-info.booking-services')

  </template>
</div>
<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
var vue_details_tab_info = null;
jQuery(function ($) {
  vue_details_tab_info = new Vue({
    el: "#vue-details-tab-info",
    data: <?php echo json_encode($details_info_vue); ?>,
    filters: {
      time_hm: function (value) {
        return value ? value.substring(0, 5) : '';
      }
    },
    methods:{
      v_amount: function(value){
        return parseFloat(value);
      },
      v_fixed_amount: function(value){
        return parseFloat(value).toFixed(2);
      }
    }
  });
});
</script>
