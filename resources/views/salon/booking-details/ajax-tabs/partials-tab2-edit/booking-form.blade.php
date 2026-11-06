

<div class="row">
  <div class="col-md-8">
      <a data-toggle="modal" style="color: white;" class="btn btn-sm btn-primary" data-target="#serviceLookup"> <i class="fa fa-plus"></i>Service Lookup</a>
      <a data-toggle="modal" style="color: white;" class="btn btn-sm btn-primary" data-target="#productLookup"> <i class="fa fa-plus"></i> Product Lookup</a>
  </div>
  <div class="col-md-4">
      <div class="row">
          <div class="col-7">
              <div class="input-group pull-right">
                <form class="" method="post" v-bind:action="urls.form_save_url">
                  @csrf
                  <input type="hidden" name="booking_id" v-model="booking_id">
                  <input type="hidden" name="tab_name" v-model="tab_name">
                  <div v-if="booking_info.payment_status !='completed'"  class="row">

                      <select v-bind:disabled="booking_info.canceled_by > 0" class="form-control voco-select2" id="client_id" name="client_id" data-placeholder="Client" v-model="booking_info.client_id">
                        <option value=""></option>
                        <option v-for="client in all_db_clients" v-bind:value="client.client_id">@{{ client.name + ' (' + client.mobile + ')' }}</option>
                      </select>


                  </div>
              </div>
          </div>
          <div class="col-5">
              <a class="btn btn-sm btn-primary pull-right btn_dark-x" href="{{   route('tab.salon.booking.details', ['booking_id' => '0', 'tab_name' => 'edit']) }}" data-toggle="tooltip" title="" data-original-title="Create New Booking"> <i class="fa fa-plus"></i> Add</a>
              <a class="btn btn-sm btn-primary vctab-btn pull-right mr-2 btn_dark-x" href="{{  route('tab.salon.booking.list')  }}"> <i class="fa fa-backward"></i> Edit</a>
          </div>
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
