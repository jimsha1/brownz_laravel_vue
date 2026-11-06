<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-info.global-tax-info {
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
    font-size: 1.15rem;
    font-weight: 700;
    color: #1D1D1D;
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

  #vue-details-tab-info .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
  }

  #vue-details-tab-info .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  #vue-details-tab-info .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }
</style>

<div id="vue-details-tab-info" class="global-tax-info info-sec-tab-cont">
  <template>
    <div>
      <div class="info-summary">
        <div class="summary-card">
          <span class="label">{{ __('Tax ID') }}</span>
          <span class="value">#@{{ global_tax_info.tax_id }}</span>
        </div>
        <div class="summary-card">
          <span class="label">{{ __('Tax Rate') }}</span>
          <span class="value">@{{ global_tax_info.tax }}%</span>
        </div>
        <div class="summary-card">
          <span class="label">{{ __('Status') }}</span>
          <span class="value" style="font-size: 1rem;">
            <span v-if="global_tax_info.is_active == 1" class="status-pill is-active">{{ __('Active') }}</span>
            <span v-else class="status-pill is-inactive">{{ __('Inactive') }}</span>
          </span>
        </div>
      </div>

      <div class="info-panel">
        <table class="table info-table">
          <tbody>
            <tr>
              <td class="info-label">{{ __('Name') }}</td>
              <td class="info-value">@{{ global_tax_info.name }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Tax Rate') }}</td>
              <td class="info-value">@{{ global_tax_info.tax }}%</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Status') }}</td>
              <td class="info-value">
                <span v-if="global_tax_info.is_active == 1" class="status-pill is-active">{{ __('Active') }}</span>
                <span v-else class="status-pill is-inactive">{{ __('Inactive') }}</span>
              </td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Created') }}</td>
              <td class="info-value">
                <span v-if="global_tax_info.created_user">
                  @{{ global_tax_info.created_user.name }}
                  <span v-if="created_at_label">
                    {{ __('on') }}
                    <span class="text-muted font-italic">@{{ created_at_label }}</span>
                  </span>
                </span>
                <span v-else>-</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
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
