<div class="modal fade" id="client-quick-edit-modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg brownz-popup-form-v2" role="document">
    <div class="modal-content">
      <div class="modal-header border-0 px-4">
        <h5 class="modal-title popup-head-bootst">{{ __('Client Details') }}</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close-btn-default">✖</button>
      </div>

      <div class="modal-body px-4">
        <form class="voco-ajax-form" method="post" v-bind:action="urls.booking_client_update_url">
          @csrf
          <input type="hidden" name="client_id" v-bind:value="popup_client_info.client_id">
          <input type="hidden" name="booking_id" v-bind:value="booking_info.booking_id">

          <div class="row">

            <vcinput-basic v-bind:value="popup_client_info.name" id="name" label="{{ __('Name') }}"></vcinput-basic>

            <div class="form-group col-sm-6">
              <label for="gender">{{ __('Mobile') }}</label>
            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="10" minlength="10" v-on:keyup='check_mobile_no()' placeholder="10-digit mobile" name="mobile" id="mobile" label="{{ __('Mobile') }}" class="form-control">
              <span id="number_validation" class="text-danger"></span>

            </div>
          </div>

          <div class="row mt-3">
            <vcinput-basic v-bind:value="popup_client_info.email" id="email" label="{{ __('Email') }}"></vcinput-basic>
            <div class="form-group col-sm-6  voco-in-custom-brownz">
              <label for="gender">{{ __('Gender') }}</label>
              <select class="form-control voco-select2" id="gender" name="gender" data-minimum-results-for-search="-1" data-placeholder="{{ __('Gender') }}" v-bind:value="popup_client_info.gender" style="width:100%">
                <option value=""></option>
                {{ \Facades\App\Models\User::genderDropdown() }}
              </select>
            </div>
          </div>

          <div class="row mt-3">
            <div class="form-group col-sm-6">
              <label for="date_of_birth">{{ __('Date of birth') }}</label>
              <div class="input-group date_of_birth_ig">
                <input
                  type="text"
                  class="form-control"
                  id="date_of_birth"
                  name="date_of_birth"
                  placeholder="{{ __('Date of birth') }}"
                  v-bind:value="popup_client_info.date_of_birth"
                  autocomplete="off"
                  data-input
                >
                <div class="input-group-append" data-toggle>
                  <span class="input-group-text">
                    <i class="fa fa-calendar"></i>
                  </span>
                </div>
              </div>
            </div>

            <vcinput-basic v-bind:value="popup_client_info.telephone" id="telephone" label="{{ __('Telephone') }}"></vcinput-basic>
          </div>

          <div class="row mt-3">
            <vcinput-basic v-bind:value="popup_client_info.address" id="address" label="{{ __('Address') }}"></vcinput-basic>
            <vcinput-basic v-bind:value="popup_client_info.city" id="city" label="{{ __('City') }}"></vcinput-basic>
          </div>

          <div class="row mt-3">
            <vcinput-basic v-bind:value="popup_client_info.state" id="state" label="{{ __('State') }}"></vcinput-basic>
            <vcinput-basic v-bind:value="popup_client_info.zip" id="zip" label="{{ __('Zip') }}"></vcinput-basic>
          </div>
        </form>
      </div><!-- /.modal-body -->
      <div class="modal-footer border-0 px-4">
        <button type="button" class="btn brownz-btn-yellow-d" data-dismiss="modal">✖ {{ __('Cancel') }}</button>
        <button id="save_btn" type="button" class="btn btn_dark-x btn-voco-save"><i class="fa fa-check"></i> {{ __('Save') }}</button>
        <div id="loading_spinner" style="display: none;">
            Processing...
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
