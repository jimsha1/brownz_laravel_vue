<div id="vue-list-tab-all" class="m-n1 my-appt-list">
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
            <td>
              <span v-if="item.service_finished_at" class="status-pill is-completed">{{ __('Completed') }}</span>
              <span v-else-if="item.service_started_at" class="status-pill is-ongoing">{{ __('In Progress') }}</span>
              <span v-else-if="item.appointment_date < todays_date" class="status-pill is-expired">{{ __('Expired') }}</span>
              <span v-else class="status-pill is-upcoming">{{ __('Upcoming') }}</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="tab-pagination mt-3" v-html="paginator.links"></div>
  </template>
  <template v-else>
    <div class="empty-state">
      <div><i class="fa fa-calendar" aria-hidden="true"></i></div>
      <div><strong>{{ __('No appointments found') }}</strong></div>
      <div>{{ __('Try adjusting date range or search.') }}</div>
    </div>
  </template>
</div>

<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
var vue_list_tab_all = null;
jQuery(function ($) {
  vue_list_tab_all = new Vue({
    el: "#vue-list-tab-all",
    data: Object.assign(
      { todays_date: '{{ date('Y-m-d') }}' },
      <?php echo json_encode($db_results['vue_data']['vue_list_tab_all']); ?>
    )
  });
});
</script>
