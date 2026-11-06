<!-- beautician-slot selection modal -->
{{-- <link rel="stylesheet" href="http://baheyaweb.cloudbery.com/web/plugins/bootstrap/bootstrap.min.css" type="text/css" media="all"> --}}
{{-- <link rel="stylesheet" href="http://baheyaweb.cloudbery.com/web/plugins/bootstrap/bootstrap-theme.min.css" type="text/css"> --}}

<div class="modal fade " id="beautician-slot-modal" tabindex="-1" role="dialog"
    aria-labelledby="beautician-slot-modal-label" aria-hidden="true">
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


                <div class="row ">

                    <div class="col-md-12">

                        <form v-bind:action="urls.date_form_update_url" class="voco-ajax-form" method="post"
                            data-hook-success="popupSlotUpdated" id="date_update_form1">
                            @csrf
                            <input type="hidden" name="booking_id" v-model="booking_info.booking_id">
                            <input type="hidden" name="service_id" v-model="focused_active_service">
                            <div class="row">

                                <div style="margin-top: 20px;" class="col-md-4">
                                    <vcinput-datepicker  v-model="focused_active_booking_date" id="booking_date">
                                    </vcinput-datepicker>
                                </div>

                                <div class="col-md-4">
                                    {{-- <div class="row">
                                        <label>Select Shift</label>
                                    </div> --}}

                                    <h6 style="width: 100% ;">Select Shift</h6>
                                    <select id="shift_data" class="form-select form-select-lg mb-3"
                                        v-on:change="getbookingData(this)" aria-label=".form-select-lg example"
                                        style="padding: 6px;">
                                        {{-- <option v-bind:value="0" selected>Select Shift</option> --}}
                                        <option v-bind:value="shift.shift_id" v-for="shift in all_shifts">
                                            @{{ shift . title }}</option>
                                    </select>

                                </div>
                                <div class="col-md-4">
                                    {{-- <div class="row">
                                    <label>Select Duration</label>
                                    </div> --}}
                                    <h6 style="width: 100% ;">Select Duration</h6>
                                    <select v-on:change="getbookingData(this)" v-model="duration_minutes"
                                        id="duration_minutes" name="duration" class="form-select form-select-lg mb-3"
                                        aria-label=".form-select-lg example" style="padding: 6px;">
                                        {{-- <option v-bind:value="0" selected>Select Duration</option> --}}
                                        <option v-bind:value="data[0]" v-for="data in new_duration">@{{ data[1] }}
                                        </option>

                                    </select>
                                </div>
                            </div>

                        </form>
                    </div>

                </div>

                <div class="table-responsive example">
                    <table class="table table-borderless w-auto">
                        <tbody>
                            <!-- <tr>
                <td>Date</td>
                <td colspan="10">
                  <form  v-bind:action="urls.date_form_update_url"
                      class="voco-ajax-form"
                      method="post"
                      data-hook-success="popupSlotUpdated"
                      style="display:inline-block"
                      id="date_update_form"
                    >
                      @csrf
                      <input type="hidden" name="booking_id" v-model="booking_id">
                      <input type="hidden" name="service_id" v-model="focused_active_service">

                <vcinput-datepicker  v-model="focused_active_booking_date" id="booking_date" ></vcinput-datepicker>
              </form>
              </td>
              <td></td>
              </tr> -->
                            <style>

                            </style>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div v-for="bt in beauticians" class="col-md-4" style="
                        text-align: center; padding-bottom:5px ">
                                            <div :id="bu+bt.beautician_id" class="prof">
                                                <img v-on:click="getTimeSlot(bt,1)" v-bind:src="bt.photo_src_url" alt=""
                                                    class="rounded" style="width: 100%">
                                                <br>
                                                @{{ bt . name_locale }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="beauticians.length > 0" class="col-md-6">
                                    <div class="form-group text-center">
                                        <div class="row" style="text-align: center; ">
                                            <h6 style="width: 100%;">
                                                {{ __('Pick Your Time') }}</h6>
                                        </div>
                                        <div style="margin-right: 10px;" class="row">
                                            <div v-for="(time,key) in time_slot" v-if="time.is_booked==false"
                                                class="col-md-2">

                                                {{-- <input id="picking_time" class="checkbox-tools picking_time" value="" type="radio" v-bind:value="time.date" name="time" v-bind:id="'tool-'+key"   > --}}

                                                <form :id="'time_slot_form'+key"
                                                    v-bind:action="urls.item_form_update_url" method="post"
                                                    data-hook-success="popupSlotUpdated" class="voco-ajax-form"
                                                    style="display: inline-block;">
                                                    @csrf
                                                    <input type="hidden" name="booking_id" v-model="booking_id">
                                                    <input type="hidden" name="service_id"
                                                        v-model="focused_active_service">
                                                    <input type="hidden" name="beautician_id" v-model="beautician_id">
                                                    <input type="hidden" name="slot_start" v-bind:value="time.date">

                                                    <button class="prof"
                                                        v-on:click="getTimeSlotBtn(time_slot_btn_id+time.date,time_slot_form+key,time.date)"
                                                        type="submit" class="btn btn-secondary" style="padding: 20px;">
                                                        @{{ time . date }}
                                                    </button>

                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            


                            {{-- ------------------------------ end new section --------------------------------- --}}

                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
