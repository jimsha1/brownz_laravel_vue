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
          <td>@{{ '#' + outlet_info.outlet_id }}</td>
        </tr>
        <tr>
          <td>Name</td>
          <td>@{{ outlet_info.name }}</td>
        </tr>
        <tr>
          <td>Phone</td>
          <td>@{{ outlet_info.phone }}</td>
        </tr>
        <tr>
          <td>Email</td>
          <td>@{{ outlet_info.email }}</td>
        </tr>
        <tr>
          <td>Location</td>
          <td>@{{ outlet_info.location }}</td>
        </tr>
        <tr>
          <td>Address</td>
          <td>@{{ outlet_info.address }}</td>
        </tr>
        <tr>
          <td>City</td>
          <td>@{{ outlet_info.city }}</td>
        </tr>
        <tr>
          <td>Created</td>
          <td>
            @{{ outlet_info.created_user.name }}
            on
            <span class="text-muted font-italic">
              @{{ outlet_info.created_at | vc_datetime_expanded }}
            </span>
          </td>
        </tr>
        <tr>
          <td>Updated</td>
          <td>
            @{{ outlet_info.updated_user.name }}
            on
            <span class="text-muted font-italic">
              @{{ outlet_info.updated_at | vc_datetime_expanded }}
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
