
<div id="vue-list-tab-all" class="m-n1">
  <template v-if="paginator.db_rows.length">
    <div class="table-responsive">
      <table class="table brownz-table-list-v1">
        <thead>
          <tr>
            <th>{{ __('SL#') }}</th>
            <th>{{ __('Code') }}</th>
            <th>{{ __('Client') }}</th>
            <th>{{ __('Amount') }}</th>
            <th>{{ __('Date') }}</th>
            <!-- <th>{{ __('Payment') }}</th> -->
            <th>{{ __('Source') }}</th>
            <th>{{ __('Type') }}</th>
            <th class="hidden-print no-sort">{{ __('Manage') }}</th>
          </tr>
        </thead>
        <tbody>
          <input id="count_array" type="hidden" v-bind:value="paginator.db_rows.length">
          <tr v-for="(item, index) in paginator.db_rows" v-if="item.booking_code!='draft'">
          <div  >
            <td>@{{ index + paginator.sequence_start }}</td>
            <td>
              <a v-bind:href="item.edit_url">
                @{{ item.booking_code?item.booking_code:'Draft' }}
              </a>
            </td>
            
            <td>@{{ item.client_name }}</td>
            <td>@{{  item.booking_total }}</td>
          <td :id="format_date_id+index">@{{  item.created_at }}</td>
            <!-- <td>
              <span v-if="item.payment_status==='pending'" class="badge badge-warning">@{{ item.payment_status | vc_ucwords }}</span>
              <span v-else-if="item.payment_status==='completed'" class="badge badge-success">@{{ item.payment_status | vc_ucwords }}</span>
              <span v-else class="badge badge-secondary">@{{ item.payment_status | vc_ucwords }}</span>
            </td> -->
            <td>@{{ item.booking_source | vc_ucwords}}</td>
            <td>@{{ item.booking_type }}</td>
            <td class="hidden-print">
              <a v-bind:href="item.edit_url">
                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
              </a>
            </td>
             </div>
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

<script>
var vue_list_tab_all = null;
var format_date_id = "foramt_date_";
jQuery(function ($) {
  vue_list_tab_all = new Vue({
    el: "#vue-list-tab-all",
    data: <?php echo json_encode($db_results['vue_data']['vue_list_tab_all']); ?>
  });
});
$('document').ready(function(){
  var count_array = $('#count_array').val()
for (let index = 0; index < count_array ; index++) {
  var id = '#format_date_'+index;
  var db_Date = $(id).text();
  var localTime  = moment(db_Date).toDate();
    localTime = moment(localTime).format('YYYY-MM-DD HH:mm:ss');
    $(id).text(localTime);
 
  
}

});
</script>
