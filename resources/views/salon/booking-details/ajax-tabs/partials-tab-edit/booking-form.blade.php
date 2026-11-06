<div class="row">
    <div class="col-xl-8 col-lg-6 col-md-12 px-0 mt-3">

        <a v-if="!!booking_info.client_name   && booking_info.payment_status !='completed'"
            v-on:click="getQuickServiceLookup()" style="color: white;" class="btn btn-sm btn-primary btn_dark-x  mb-2"> <i
                class="fa fa-plus"></i>{{ __('Quick Service') }}</a>

        <a v-if="!!booking_info.client_name   && booking_info.payment_status !='completed'"
            v-on:click="getQuickProductLookup()" style="color: white;" class="btn btn-sm btn-primary btn_dark-x  mb-2"> <i
                class="fa fa-plus"></i>{{ __('Quick Product') }}</a>

        <a v-if="!!booking_info.client_name  && booking_info.payment_status !=='completed'"
            v-on:click="getServiceLookup()" style="color: white;" class="btn btn-sm btn-primary btn_dark-x mb-2 mr-2"> <i
                class="fa fa-plus"></i>{{ __('Service Lookup') }}</a>
        <a v-if="!!booking_info.client_name   && booking_info.payment_status !='completed'"
            v-on:click="getProductLookup()" style="color: white;" class="btn btn-sm btn-primary btn_dark-x  mb-2"> <i
                class="fa fa-plus"></i>{{ __('Product Lookup') }}</a>
        {{-- Consultations stay available after payment so existing forms can be reopened --}}
        <a v-if="!!booking_info.client_name"
            v-on:click="getHairLookup()" style="color: white;" class="btn btn-sm btn-primary btn_dark-x  mb-2"> <i
                class="fa fa-plus"></i>{{ __('Hair Consultation ') }}</a>

        <a v-if="!!booking_info.client_name"
            v-on:click="getSkinLookup()" style="color: white;" class="btn btn-sm btn-primary btn_dark-x  mb-2"> <i
                class="fa fa-plus"></i>{{ __('Skin Consultation ') }}</a>



    </div>
    @if (app()->getLocale() === 'en')
        <div class="col-xl-4 col-lg-6 col-md-12 respo-padding-right-0 mt-3">
            <div>
            @else
                <div class="col-md-4">
                    <div>
    @endif
    <div>
        <div class="input-group pull-right w-100">
            <form class="w-100" method="post" v-bind:action="urls.form_save_url">
                @csrf
                <input type="hidden" name="booking_id" v-model="booking_id">
                <input type="hidden" name="tab_name" v-model="tab_name">
                <div v-if="booking_info.payment_status !='completed'" class=" voco-in-custom-brownz">

                    <!-- <select v-bind:disabled="booking_info.canceled_by > 0" class="form-control voco-select2" id="client_id" name="client_id" data-placeholder="Client" v-model="booking_info.client_id">
                        <option value=""></option>
                        <option v-for="client in all_db_clients"  v-bind:value="client.client_id">@{{ client.name + ' (' + client.mobile + ')' }}</option>
                      </select>  -->
                    <select v-bind:disabled="booking_info.canceled_by > 0"
                        class="form-control voco-select2 voco-select2-client_id " id="client_id" name="client_id"
                        v-model="booking_info.client_id"></select>


                    <div class="mt-3 d-flex flex-wrap br-gap-2" style="justify-content: flex-end">
                        {{-- <button type="button" data-target="#client-quick-edit-modal" data-toggle="modal"
                            data-popup-type="new" title="" class="btn_dark-x btn ">
                            <i class="fa fa-plus"></i> {{ __('New customer') }}
                        </button> --}}
                        <div v-if="booking_info.client_id > 0" class="d-inline">
                            <button type="button" class="btn  brownz-btn-yellow-d"
                                data-target="#client-quick-edit-modal" data-toggle="modal" data-popup-type="edit"
                                title="">
                                <i class="fa fa-edit"></i>{{ __('Edit Customer') }}
                            </button>
                        </div>
                    </div>
                </div>

        </div>
    </div>
    {{-- <div class="col-5">
              <a class="btn btn-sm btn-primary pull-right" href="{{   route('tab.salon.booking.details', ['booking_id' => '0', 'tab_name' => 'edit']) }}" data-toggle="tooltip" title="" data-original-title="Create New Booking"> <i class="fa fa-plus"></i> Add</a>
              <a class="btn btn-sm btn-primary vctab-btn pull-right mr-2" href="{{  route('tab.salon.booking.list')  }}"> <i class="fa fa-backward"></i> Edit</a>
          </div> --}}
</div>

</div>
</div>
{{-- <form class="" method="post" v-bind:action="urls.form_save_url">
  @csrf
  <input type="hidden" name="booking_id" v-model="booking_id">
  <input type="hidden" name="tab_name" v-model="tab_name">
  <div v-if="booking_info.payment_status !='completed'"  class="row">
    <div class="form-group col-md-6">
      <label for="client_id">{{ __('Customer') }}</label>

      <div class="mt-1">
        <button type="button" class="btn btn-link p-1" data-target="#client-quick-edit-modal" data-toggle="modal" data-popup-type="new" title="">
          {{ __('Create new customer') }}
        </button>
        <div v-if="booking_info.client_id > 0" class="d-inline">
          <button type="button" class="btn btn-link p-1" data-target="#client-quick-edit-modal" data-toggle="modal" data-popup-type="edit" title="">
            {{ __('Edit this customer') }}
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- <div v-if="booking_info.client_id > 0" class="row">
    <vcinput-basic v-model="booking_info.client_name" id="client_name" label="{{ __('Name') }}" readonly></vcinput-basic>
    <vcinput-basic v-model="booking_info.client_mobile" id="client_mobile" label="{{ __('Mobile') }}" readonly></vcinput-basic>
  </div>

  <div v-if="booking_info.client_id > 0" class="row">
    <vcinput-basic v-model="booking_info.client_telephone" id="client_telephone" label="{{ __('Telephone') }}" readonly></vcinput-basic>
    <vcinput-basic v-model="booking_info.client_email" id="client_email" label="{{ __('Email') }}" readonly></vcinput-basic>
  </div> --}}
{{-- </form>  --}}
