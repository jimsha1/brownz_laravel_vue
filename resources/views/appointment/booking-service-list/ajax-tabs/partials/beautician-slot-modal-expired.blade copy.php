beautician-slot selection modal -->
<div class="modal fade" id="beautician-slot-modal-expired" tabindex="-1" role="dialog" aria-labelledby="beautician-slot-modal-label" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width:90vw">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="beautician-slot-modal-label">
          {{ __('Change beautician and time slot') }}
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

       <div   class="row ">
          <div class="col-md-1 text-right">
          <span >    
         {{ __('Date') }}
       </span>
         
         </div>
       <div class="col-md-8">

        <form v-bind:action="urls.date_form_update_url"
         class="voco-ajax-form"
         method="post"
         data-hook-success="popupSlotUpdated"
         id="date_update_form1"
         >
         @csrf
         <input type="hidden" name="booking_id" v-model="booking_info.booking_id">
         <input type="hidden" name="service_id" v-model="focused_active_service">

         <vcinput-datepicker    v-model="focused_active_booking_date" id="booking_date_expired"   name="booking_date" ></vcinput-datepicker>
         </form>
       </div>
       
     </div>

        <div class="table-responsive">
          <table class="table table-borderless w-auto">
            <tbody>
              <!-- <tr>
                <td>Date</td>
                <td colspan="10">
                  <form v-bind:action="urls.date_form_update_url"
                      class="voco-ajax-form"
                      method="post"
                      data-hook-success="popupSlotUpdated"
                      style="display:inline-block"
                      id="date_update_form1"
                    >
                      @csrf
                      <input type="hidden" name="booking_id" v-model="booking_info.booking_id">
                      <input type="hidden" name="service_id" v-model="focused_active_service">
                <vcinput-datepicker  v-model="focused_active_booking_date" id="booking_date" ></vcinput-datepicker>
              </form>
              </td>
              <td></td>
              </tr> -->
              <tr v-for="bt in beauticians">
                <td v-if="bt.slots">
                  <img v-bind:src="bt.photo_src_url" alt="" class="rounded">
                  <br>
                  @{{ bt.name }}
                </td>
                <td v-for="slot in bt.slots" class="px-1">
                  <h5>
                    <button v-if="bt.selected_slot_hms == slot.slot_time" class="btn btn-success" disabled>
                      @{{ slot.slot_time | time_hm }}
                    </button>
                    <form v-else-if="slot.available" v-bind:action="urls.item_form_update_url"
                      class="voco-ajax-form"
                      method="post"
                      data-hook-success="popupSlotUpdated"
                      style="display:inline-block"
                    >
                      @csrf
                      <input type="hidden" name="booking_id" v-model="booking_info.booking_id">
                      <input type="hidden" name="service_id" v-model="focused_active_service">
                      <input type="hidden" name="beautician_id" v-model="bt.beautician_id">
                      <input type="hidden" name="slot_start" v-bind:value="slot.slot_time">
                      <button type="submit" class="btn btn-secondary slot-submit-btn">
                        @{{ slot.slot_time | time_hm }}
                      </button>
                    </form>
                    <button v-else class="btn btn-warning" disabled>
                      @{{ slot.slot_time | time_hm }}
                    </button>
                  </h5>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal
