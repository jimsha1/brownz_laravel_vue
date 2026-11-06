
<div id="vue-list-tab-all" class="m-n1">
  <template v-if="paginator.db_rows.length">
    <div class="table-responsive">
      <table class="table brownz-table-list-v1">
        <thead>
          <tr>
            <th>{{ __('SL#') }}</th>
            <th>{{ __('Booking') }}</th>
            <th>{{ __('Client') }}</th>
            <th>{{ __('Beautician') }}</th>
            <th>{{ __('Service') }}</th>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Time') }}</th>
            <!-- <th>{{ __('Duration') }}</th> -->
            <!-- <th>{{ __('Slot End') }}</th> -->
            <!-- <th>{{ __('Service Start Time') }}</th> -->
            <!-- <th>{{ __('Service End Time') }}</th> -->
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in paginator.db_rows">
            <td>@{{ index + paginator.sequence_start }}</td>
            <td>@{{ item.booking_code?item.booking_code:'Draft' }}</td>
            <td>@{{ item.client_name }}</td>
            <td>@{{ item.beautician_name }}</td>
            <td>@{{ item.service_name_locale }}</td>
            <td>@{{ item.appointment_date | vc_date_expanded }}</td>
            <td>@{{ convertTimeTo12hr(item.slot_start) }}</td>
            <!-- <td>@{{ item.duration_minutes | vc_duration_short }}</td> -->
            <!-- <td>@{{ item.slot_end }}</td> -->
            <!-- <td>@{{ item.service_started_at }}</td> -->
            <!-- <td>@{{ item.service_finished_at }}</td> -->
          </tr>
        </tbody>
      </table>
    </div>

    <div class="tab-pagination" v-html="paginator.links">
    </div>
  </template>
  <template v-else>
    <div class="alert alert-danger">
      {{ __('No results found.') }}
    </div>
  </template>
</div>

<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
var vue_list_tab_all = null;
jQuery(function ($) {
  vue_list_tab_all = new Vue({
    el: "#vue-list-tab-all",
    data: <?php echo json_encode($db_results['vue_data']['vue_list_tab_all']); ?>,
    methods:{
      convertTimeTo12hr: function(time) {
                    // Check correct time format and split into components
                    time = time.toString().match(/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [
                        time
                    ];

                    if (time.length > 1) { // If time format correct
                        time = time.slice(1); // Remove full string match value
                        time[5] = +time[0] < 12 ? ' AM' : ' PM'; // Set AM/PM
                        time[0] = +time[0] % 12 || 12; // Adjust hours
                    }
                    return time.join(''); // return adjusted time or original string
                },
    }
  });
});
</script>
