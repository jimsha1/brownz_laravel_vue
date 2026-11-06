<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-info.acl-role-info {
    margin: 0;
    padding: 4px 0 8px;
  }

  #vue-details-tab-info .info-summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 16px;
  }

  #vue-details-tab-info .summary-card {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 14px 16px;
  }

  #vue-details-tab-info .summary-card .label {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 4px;
  }

  #vue-details-tab-info .summary-card .value {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1D1D1D;
    font-variant-numeric: tabular-nums;
  }

  #vue-details-tab-info .info-panel {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 6px 4px;
  }

  #vue-details-tab-info .info-table {
    margin-bottom: 0;
  }

  #vue-details-tab-info .info-table td {
    border-top: none;
    border-bottom: 1px solid #e8e8ec;
    vertical-align: middle;
    padding: 12px 16px;
  }

  #vue-details-tab-info .info-table tr:last-child td {
    border-bottom: none;
  }

  #vue-details-tab-info .info-label {
    width: 28%;
    color: #6c757d;
    font-weight: 600;
    font-size: 13px;
  }

  #vue-details-tab-info .info-value {
    color: #1D1D1D;
    font-weight: 500;
  }

  #vue-details-tab-info .type-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
  }

  #vue-details-tab-info .type-pill.is-system {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  #vue-details-tab-info .type-pill.is-custom {
    color: #1D1D1D;
    background: #EDEDF1;
  }

  @media (max-width: 767.98px) {
    #vue-details-tab-info .info-summary {
      grid-template-columns: 1fr;
    }

    #vue-details-tab-info .info-label {
      width: 40%;
    }
  }
</style>

<div id="vue-details-tab-info" class="acl-role-info info-sec-tab-cont">
  <template>
    <div class="info-summary">
      <div class="summary-card">
        <span class="label">{{ __('Role ID') }}</span>
        <span class="value">#@{{ acl_role_info.id }}</span>
      </div>
      <div class="summary-card">
        <span class="label">{{ __('Permissions') }}</span>
        <span class="value">@{{ acl_role_info.permissions_count || 0 }}</span>
      </div>
      <div class="summary-card">
        <span class="label">{{ __('Users') }}</span>
        <span class="value">@{{ acl_role_info.users_count || 0 }}</span>
      </div>
    </div>

    <div class="info-panel">
      <table class="table info-table">
        <tbody>
          <tr>
            <td class="info-label">{{ __('Name') }}</td>
            <td class="info-value">@{{ acl_role_info.name || '—' }}</td>
          </tr>
          <tr>
            <td class="info-label">{{ __('Description') }}</td>
            <td class="info-value">@{{ acl_role_info.description || '—' }}</td>
          </tr>
          <tr>
            <td class="info-label">{{ __('Guard') }}</td>
            <td class="info-value">@{{ acl_role_info.guard_name || '—' }}</td>
          </tr>
          <tr>
            <td class="info-label">{{ __('Type') }}</td>
            <td class="info-value">
              <span v-if="Number(acl_role_info.system_defined) === 1" class="type-pill is-system">
                {{ __('System Defined') }}
              </span>
              <span v-else class="type-pill is-custom">
                {{ __('Custom') }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
</div>

<script>
var vue_details_tab_info = null;
jQuery(function ($) {
  vue_details_tab_info = new Vue({
    el: "#vue-details-tab-info",
    data: <?php echo json_encode($details_info_vue); ?>
  });
});
</script>
