<div id="vue-list-tab-on-going" class="m-n1 my-appt-list">
  <template v-if="paginator.db_rows.length">
    <div class="table-responsive">
      <table class="table brownz-table-list-v1 m-0">
        <thead>
          <tr>
            <th style="width: 56px;">{{ __('SL#') }}</th>
            <th>{{ __('Booking') }}</th>
            <th>{{ __('Client') }}</th>
            <th>{{ __('Service') }}</th>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Time') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="text-center" style="width: 110px;">{{ __('Action') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in paginator.db_rows" :key="item.booking_service_id">
            <td>@{{ index + paginator.sequence_start }}</td>
            <td>
              <span class="booking-code" :class="{ 'is-draft': !item.booking_code }">
                @{{ item.booking_code ? item.booking_code : 'Draft' }}
              </span>
            </td>
            <td>
              <span class="client-name">@{{ item.client_name || '—' }}</span>
              <span class="client-meta" v-if="item.client_mobile">@{{ item.client_mobile }}</span>
            </td>
            <td>
              <div class="cell-clamp" :title="item.service_name_locale">@{{ item.service_name_locale }}</div>
            </td>
            <td>@{{ item.appointment_date | vc_date_expanded }}</td>
            <td>@{{ item.slot_start || '—' }}</td>
            <td><span class="status-pill is-ongoing">{{ __('In Progress') }}</span></td>
            <td class="text-center">
              <div class="manage-actions">
                <button type="button" class="manage-btn is-finish"
                  data-toggle="modal"
                  v-bind:data-target="'#finishModal-' + item.booking_service_id"
                  :title="'{{ __('Finish') }}'">
                  <i class="fa fa-check" aria-hidden="true"></i>
                  <span>{{ __('Finish') }}</span>
                </button>
              </div>

              <div class="modal fade brownz-custom-popup"
                v-bind:id="'finishModal-' + item.booking_service_id"
                tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content" style="border-radius: 16px; border: none;">
                    <form action="{{ route('my.account.booking.service.stop') }}"
                      data-hook-success="hide_finish_modal_ongoing"
                      class="voco-ajax-form"
                      method="post"
                      v-bind:data-modal-id="'#finishModal-' + item.booking_service_id">
                      @csrf
                      <div class="modal-header border-0">
                        <h5 class="modal-title" style="color:#1D1D1D;font-weight:600;">{{ __('Finish Service') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body pt-0">
                        <input type="hidden" name="booking_id" v-model="item.booking_id">
                        <input type="hidden" name="booking_service_id" v-model="item.booking_service_id">
                        <input type="hidden" name="from_tab" value="on-going">
                        <label>{{ __('Remarks') }}</label>
                        <textarea name="booking_remarks" class="form-control" rows="3"
                          placeholder="{{ __('Remarks (optional)') }}"></textarea>
                      </div>
                      <div class="modal-footer border-0">
                        <button type="button" class="btn brownz-btn-yellow-d" data-dismiss="modal">
                          {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn_dark-x">
                          <i class="fa fa-check"></i> {{ __('Finish') }}
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="tab-pagination mt-3" v-html="paginator.links"></div>
  </template>
  <template v-else>
    <div class="empty-state">
      <div><i class="fa fa-hourglass-half" aria-hidden="true"></i></div>
      <div><strong>{{ __('No in-progress appointments') }}</strong></div>
      <div>{{ __('Start a service from Upcoming to see it here.') }}</div>
    </div>
  </template>
</div>

<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
var vue_list_tab_on_going = null;
jQuery(function ($) {
  vue_list_tab_on_going = new Vue({
    el: "#vue-list-tab-on-going",
    data: <?php echo json_encode($db_results['vue_data']['vue_list_tab_on_going']); ?>
  });
});

var hide_finish_modal_ongoing = function ($form) {
  var modalId = $form.attr('data-modal-id');
  if (modalId) {
    $(modalId).modal('hide');
  }
  window.location.reload();
};
</script>
