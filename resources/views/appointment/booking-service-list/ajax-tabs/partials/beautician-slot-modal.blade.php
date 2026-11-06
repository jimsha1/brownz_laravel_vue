{{-- \\<!-- beautician-slot selection modal -->
<div class="modal fade" id="beautician-slot-modal" tabindex="-1" role="dialog" aria-labelledby="beautician-slot-modal-label" aria-hidden="true">
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

         <vcinput-datepicker    v-model="focused_active_booking_date" id="booking_date"  ></vcinput-datepicker>
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
</div><!-- /.modal --> --}}


<style>
    .nav-tabs .nav-link.active:focus {
        background: #155e91;
        border-color: #c8ced3;
        border-bottom-color: #fff;
    }

</style>
<div class="modal fade" id="beautician-slot-modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
        <div class="modal-content ">
            <div class="modal-header border-0">
                <h5 style="    font-size: 24px;
    font-weight: 500;
    color: #1D1D1D;
    margin: 0;
    padding: 0;" class="modal-title" id="exampleModalCenterTitle">
                    {{ __('Booking Services') }}
                </h5>
                <button type="button" class=" brownz-close-btn" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <nav class="edite-serve-appointment">
                    <div style="border-bottom: 0px solid #c8ced3;" class="nav nav-tabs" id="nav-tab" role="tablist">
                        <div style="border-bottom: 0px solid #c8ced3;" class="nav nav-tabs" id="nav-tab"
                            role="tablist">
                            {{-- <a class="nav-item nav-link active" id="service_tab" data-toggle="tab" href="#bookingService"
                              role="tab" aria-controls="nav-home" aria-selected="true">Add Services</a> --}}
                            <a class="nav-item nav-link active " id="nav-contact-tab" data-toggle="tab" href="#beautician"
                                role="tab" aria-controls="nav-contact" aria-selected="false">Beautician</a>
                            <a v-on:click="changeClass()" class="nav-item nav-link" id="nav-profile-tab"
                                data-toggle="tab" href="#slot" role="tab" aria-controls="nav-profile"
                                aria-selected="false">Time Slot</a>

                        </div>
                    </div>
                </nav>

                <div class="tab-content service-tab edit-server-b" id="nav-tabContent" >
                    <div class="tab-pane fade" id="bookingService" role="tabpanel" aria-labelledby="service_tab">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group d-flex">
                                    <input type="text" class="form-control w-50" placeholder="Search Product" name=""
                                        id="" />
                                    <button class="btn btn-outline ml-2">Search</button>
                                </div>

                                <div class="form-wrapper">
                                    <nav class="tab-services">

                                        <div style="border-bottom: 0px solid #c8ced3;"
                                            class="nav nav-tabs nav-scroll-tabs" id="nav-tab" role="tablist">
                                            <a class="nav-item nav-link active" id="service_tab" data-toggle="tab"
                                                href="#featured" role="tab" aria-controls="nav-home"
                                                aria-selected="true"> </a>
                                        </div>
                                    </nav>
                                </div>

                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="featured" role="tabpanel"
                                        aria-labelledby="service_tab">



                                        <div class="tab-pane fade show active" id="featured" role="tabpanel"
                                            aria-labelledby="service_tab">
                                            <div class="service-list-wrapper">
                                                {{-- <h5>Featured111</h5> --}}
                                                <div class="form-wrapper">


                                                    <label class="custom-selection ">

                                                        <div class="service-item-list-info">
                                                            <div>
                                                                <h6></h6>

                                                            </div>

                                                            <div>
                                                                <h6></h6>
                                                            </div>

                                                        </div>
                                                        <input type="checkbox">
                                                        <span class="checkmark"></span>
                                                    </label>

                                                    <div class="sub-services">
                                                        <div>
                                                            <i class="fas fa-ellipsis-h"></i>
                                                            <h6> <span></span></h6>
                                                        </div>
                                                        <div>
                                                            <h6><span> </span></h6>
                                                        </div>
                                                    </div>
                                                    <div class="sub-service-list">
                                                        <label v-on:click="getbookingData(sub_serv.service_id,'serv')"
                                                            class="custom-selection">
                                                            <div class="service-item-list-info">
                                                                <div>
                                                                    <h6> <span> </span></h6>

                                                                </div>

                                                                <div>
                                                                    <h6><span> </span></h6>
                                                                </div>

                                                            </div>

                                                            <input type="radio" name="subservice" value="sub1">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                {{-- <div  v-for="serv in all_services" class="form-wrapper mb-3">
                                                  <label  v-on:click="getbookingData(serv.service_id,'serv')" class="custom-selection">
                                                      <div  class="service-item-list-info">
                                                          <div>
                                                              <h6>@{{serv.name}} <span>@{{serv.duration_minutes}}min </span></h6>
                                                          </div>

                                                          <div>
                                                              <h6><span>@{{serv.duration_minutes}}min  </span>SAR @{{serv.service_base_price}}</h6>
                                                          </div>
                                                      </div>

                                                      <input  type="radio" name="subservice" value="sub11">
                                                      <span class="checkmark"></span>
                                                  </label>
                                              </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="slot" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <div class="time-slot" id="timeSlote">
                            <div class="row">
                                <div class="col-lg-6">

                                    <form v-bind:action="urls.date_form_update_url" class="voco-ajax-form" method="post"
                                        data-hook-success="popupSlotUpdated" style="display:inline-block"
                                        id="date_update_new">
                                        @csrf
                                        <input type="hidden" name="booking_id" v-model="booking_info.booking_id">
                                        <input type="hidden" name="service_id" v-model="focused_active_service">

                                        <div id="selectedDateSlot">
                                            <input type="hidden" name="selected_date">
                                        </div>

                                    </form>

                                    <div class="calendar-wrapper browz-changes-calender" id="calendar-wrapper"></div>


                                </div>
                                <div class="col-lg-6">
                                    {{-- <h5>20 Febraury 2021</h5> --}}
                                    <div class="time-slot-wrapper brownz-time-slot-times-select" style="
                                    justify-content: unset">
                                        <label v-for="time in time_slot"  class="custom-selection slot-custom-radio">
                                            <input  :checked="time+':00' == selcted_time_slot"   type="radio" name="time" />
                                            <!-- <span class="checkmark"></span> -->
                                            <div v-on:click="updateTimeSlot(time)" class="service-item-list-info">
                                                <p>@{{ convertTimeTo12hr(time) }}</p>
                                            </div>
                                        </label>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade  show active" id="beautician" role="tabpanel"
                        aria-labelledby="nav-contact-tab">
                        <div class="form-group d-flex">
                            <div class="search-box-bar-container jus-heigh-search-b">
                                <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
                                <input type="text" class="form-control w-50 brawnz-search-bar-in" placeholder="Search Filter" @keyup="event => searchBeautician()" name="filterBeautician" id="filterBeautician" />
                            </div>
                            {{-- <button class="btn btn-outline ml-2">Search</button> --}}
                        </div>

                        <div class="row">
                            <div v-on:click="selectBeautician(bt)" v-for="(bt,index) in beauticians"
                                class="col-lg-6 col-sm-12 col-md-6">
                                {{-- <div  v-on:click="getbookingData( bt.beautician_id,'beatician',bt.shift_id)" v-for="bt in beauticians" class="col-lg-4 col-sm-12 col-md-3"> --}}

                                <label class="edit-serv-beaut custom-selection dsds_hdv4 beautician-custom-radio brow-custom">
                                    <div class="service-item-list-info">
                                        <div>
                                            <img v-bind:src="bt.photo_src_url" alt="" />
                                        </div>
                                        <p> @{{ bt . name_locale }}</p>
                                    </div>
                                    <input type="radio" name="beautician"
                                        :checked="bt.beautician_id == sel_beautician_id" />
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button v-on:click="bookingUpdate"v-if="selectedBt != null && (selected_time != null)" id="update_service" type="button" class="btn btn-primary submit-btn btn_dark-x">{{ __('Submit') }}</button>
            </div>
        </div>
    </div>
</div>





