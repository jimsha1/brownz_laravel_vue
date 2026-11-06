<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-info.shift-info {
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
    font-size: 1.05rem;
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
    vertical-align: top;
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

  @media (max-width: 767px) {
    #vue-details-tab-info .info-summary {
      grid-template-columns: 1fr;
    }
  }
</style>

<div id="vue-details-tab-info" class="shift-info info-sec-tab-cont">
  <template>
    <div>
      <div class="info-summary">
        <div class="summary-card">
          <span class="label">{{ __('Shift ID') }}</span>
          <span class="value">#@{{ shift_info.shift_id }}</span>
        </div>
        <div class="summary-card">
          <span class="label">{{ __('Timing') }}</span>
          <span class="value" style="font-size: 1rem;">
            @{{ shift_info.start_time || '—' }} – @{{ shift_info.end_time || '—' }}
          </span>
        </div>
        <div class="summary-card">
          <span class="label">{{ __('Status') }}</span>
          <span class="value" style="font-size: 1rem;">
            <span v-if="Number(shift_info.is_active) === 1" class="status-pill is-active">{{ __('Active') }}</span>
            <span v-else class="status-pill is-inactive">{{ __('Inactive') }}</span>
          </span>
        </div>
      </div>

      <div class="info-panel">
        <table class="table info-table">
          <tbody>
            <tr>
              <td class="info-label">{{ __('Title') }}</td>
              <td class="info-value">@{{ shift_info.title || '-' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Start Time') }}</td>
              <td class="info-value">@{{ shift_info.start_time || '-' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('End Time') }}</td>
              <td class="info-value">@{{ shift_info.end_time || '-' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Status') }}</td>
              <td class="info-value">
                <span v-if="Number(shift_info.is_active) === 1" class="status-pill is-active">{{ __('Active') }}</span>
                <span v-else class="status-pill is-inactive">{{ __('Inactive') }}</span>
              </td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Updated') }}</td>
              <td class="info-value">
                <span>@{{ updatedByLabel }}</span>
                <span v-if="updated_at_label" class="text-muted font-italic">
                  {{ __('on') }} @{{ updated_at_label }}
                </span>
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
    data: <?php echo json_encode($details_info_vue); ?>,
    computed: {
      updatedByLabel: function () {
        if (this.shift_info && this.shift_info.updated_user && this.shift_info.updated_user.name) {
          return this.shift_info.updated_user.name;
        }
        return '-';
      }
    }
  });
});
</script>
