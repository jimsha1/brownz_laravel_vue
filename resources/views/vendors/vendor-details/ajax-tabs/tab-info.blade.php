<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-info.vendor-info {
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
  }

  #vue-details-tab-info .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  #vue-details-tab-info .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  #vue-details-tab-info .gst-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 8px;
    background: #fff;
    border: 1px solid #e0e0e5;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 12px;
    letter-spacing: 0.04em;
    color: #1D1D1D;
  }
</style>

<div id="vue-details-tab-info" class="vendor-info info-sec-tab-cont">
  <template>
    <div>
      <div class="info-summary">
        <div class="summary-card">
          <span class="label">{{ __('Vendor ID') }}</span>
          <span class="value">#@{{ vendor_info.vendor_id }}</span>
        </div>
        <div class="summary-card">
          <span class="label">{{ __('Phone') }}</span>
          <span class="value" style="font-size: 1rem;">@{{ vendor_info.phone || '—' }}</span>
        </div>
        <div class="summary-card">
          <span class="label">{{ __('Status') }}</span>
          <span class="value" style="font-size: 1rem;">
            <span class="status-pill" :class="Number(vendor_info.is_active) === 1 ? 'is-active' : 'is-inactive'">
              <span v-if="Number(vendor_info.is_active) === 1">{{ __('Active') }}</span>
              <span v-else>{{ __('Inactive') }}</span>
            </span>
          </span>
        </div>
      </div>

      <div class="info-panel">
        <table class="table info-table">
          <tbody>
            <tr>
              <td class="info-label">{{ __('Name') }}</td>
              <td class="info-value">@{{ vendor_info.name || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Email') }}</td>
              <td class="info-value">@{{ vendor_info.email || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Address') }}</td>
              <td class="info-value">@{{ vendor_info.address || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('GST Number') }}</td>
              <td class="info-value">
                <span v-if="vendor_info.gst_number" class="gst-badge">@{{ vendor_info.gst_number }}</span>
                <span v-else>—</span>
              </td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Balance') }}</td>
              <td class="info-value">@{{ Number(vendor_info.balance || 0).toFixed(2) }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Created') }}</td>
              <td class="info-value">
                @{{ (vendor_info.created_user && vendor_info.created_user.name) || '—' }}
                <span class="text-muted font-italic" v-if="vendor_info.created_at">
                  · @{{ vendor_info.created_at | vc_datetime_expanded }}
                </span>
              </td>
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
