<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<div id="vue-details-tab-info" class="info-sec-tab-cont" style="margin: 0px -1rem;">
  <template>
    <table class="table">
      <tbody>
        <tr>
          <td style="width:20%">ID</td>
          <td>@{{ '#' + expense_info.expense_id }}</td>
        </tr>
        <tr>
          <td>Account Name</td>
          <td>@{{ expense_info.account.name }}</td>
        </tr>
        <tr>
          <td>Account Type</td>
          <td>@{{ expense_info.account_type }}</td>
        </tr>
        <tr>
          <td>Amount</td>
          <td>@{{ expense_info.amount }}</td>
        </tr>
        <tr>
          <td>Created</td>
          <td>
            @{{ expense_info.created_user.name }}
            on
            <span class="text-muted font-italic">
              @{{ expense_info.created_at | vc_datetime_expanded }}
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </template>
</div>
<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
var vue_details_tab_info = null;
jQuery(function ($) {
  vue_details_tab_info = new Vue({
    el: "#vue-details-tab-info",
    data: <?php echo json_encode($details_info_vue); ?>
  });
});
</script>
