<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-info.client-info {
    margin: 0;
    padding: 4px 0 8px;
  }

  #vue-details-tab-info .info-summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 16px;
  }

  @media (max-width: 991px) {
    #vue-details-tab-info .info-summary {
      grid-template-columns: 1fr;
    }
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
    text-transform: capitalize;
  }

  #vue-details-tab-info .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  #vue-details-tab-info .status-pill.is-inactive,
  #vue-details-tab-info .status-pill.is-blocked,
  #vue-details-tab-info .status-pill.is-deleted {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  #vue-details-tab-info .status-pill.is-pending {
    color: #9A6700;
    background: rgba(230, 205, 127, 0.35);
  }
</style>

<div id="vue-details-tab-info" class="client-info info-sec-tab-cont">
  <template>
    <div>
      <div class="info-summary">
        <div class="summary-card">
          <span class="label">{{ __('Client ID') }}</span>
          <span class="value">#@{{ client_info.client_id }}</span>
        </div>
        <div class="summary-card">
          <span class="label">{{ __('Mobile') }}</span>
          <span class="value" style="font-size: 1rem;">@{{ client_info.mobile || '—' }}</span>
        </div>
        <div class="summary-card">
          <span class="label">{{ __('Status') }}</span>
          <span class="value" style="font-size: 1rem;">
            <span class="status-pill" :class="'is-' + (client_info.user_status || 'inactive')">
              @{{ client_info.user_status || '—' }}
            </span>
          </span>
        </div>
      </div>

      <div class="info-panel">
        <table class="table info-table">
          <tbody>
            <tr>
              <td class="info-label">{{ __('Name') }}</td>
              <td class="info-value">@{{ client_info.name || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Username') }}</td>
              <td class="info-value">@{{ client_info.username || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Email') }}</td>
              <td class="info-value">@{{ client_info.email || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Telephone') }}</td>
              <td class="info-value">@{{ client_info.telephone || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Date Of Birth') }}</td>
              <td class="info-value">@{{ client_info.date_of_birth | vc_date_expanded }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Address') }}</td>
              <td class="info-value">@{{ client_info.address || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('City') }}</td>
              <td class="info-value">@{{ client_info.city || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('State') }}</td>
              <td class="info-value">@{{ client_info.state || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Zip') }}</td>
              <td class="info-value">@{{ client_info.zip || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Gender') }}</td>
              <td class="info-value">@{{ client_info.gender | vc_ucwords }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
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
