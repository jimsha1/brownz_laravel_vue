<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-info.acl-user-info {
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
    text-transform: capitalize;
  }

  #vue-details-tab-info .info-panel {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 6px 4px;
    margin-bottom: 14px;
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

  #vue-details-tab-info .status-pill,
  #vue-details-tab-info .role-chip {
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
    color: #8a6d00;
    background: rgba(230, 205, 127, 0.45);
  }

  #vue-details-tab-info .role-chip {
    background: #EDEDF1;
    color: #1D1D1D;
    margin: 2px 6px 2px 0;
    font-weight: 500;
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

<div id="vue-details-tab-info" class="acl-user-info info-sec-tab-cont">
  <div class="info-summary">
    <div class="summary-card">
      <span class="label">{{ __('User ID') }}</span>
      <span class="value">#@{{ user_info.id }}</span>
    </div>
    <div class="summary-card">
      <span class="label">{{ __('Status') }}</span>
      <span class="value">@{{ user_info.user_status || '—' }}</span>
    </div>
    <div class="summary-card">
      <span class="label">{{ __('Roles') }}</span>
      <span class="value">@{{ (role_names || []).length }}</span>
    </div>
  </div>

  <div class="info-panel">
    <table class="table info-table">
      <tbody>
        <tr>
          <td class="info-label">{{ __('Name') }}</td>
          <td class="info-value">@{{ user_info.name || '—' }}</td>
        </tr>
        <tr>
          <td class="info-label">{{ __('Username') }}</td>
          <td class="info-value">@{{ user_info.username || '—' }}</td>
        </tr>
        <tr>
          <td class="info-label">{{ __('Email') }}</td>
          <td class="info-value">@{{ user_info.email || '—' }}</td>
        </tr>
        <tr>
          <td class="info-label">{{ __('Mobile') }}</td>
          <td class="info-value">@{{ user_info.mobile || '—' }}</td>
        </tr>
        <tr>
          <td class="info-label">{{ __('Gender') }}</td>
          <td class="info-value" style="text-transform: capitalize;">@{{ user_info.gender || '—' }}</td>
        </tr>
        <tr>
          <td class="info-label">{{ __('Status') }}</td>
          <td class="info-value">
            <span class="status-pill" :class="'is-' + String(user_info.user_status || 'pending').toLowerCase()">
              @{{ user_info.user_status || '—' }}
            </span>
          </td>
        </tr>
        <tr>
          <td class="info-label">{{ __('Roles') }}</td>
          <td class="info-value">
            <template v-if="role_names && role_names.length">
              <span v-for="name in role_names" :key="name" class="role-chip">@{{ formatRoleName(name) }}</span>
            </template>
            <span v-else class="text-muted">—</span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<script>
var vue_details_tab_info = null;
jQuery(function ($) {
  vue_details_tab_info = new Vue({
    el: "#vue-details-tab-info",
    data: function () {
      return @json($details_info_vue);
    },
    methods: {
      formatRoleName: function (name) {
        return String(name || '')
          .replace(/_/g, ' ')
          .replace(/\b\w/g, function (char) {
            return char.toUpperCase();
          });
      }
    }
  });
});
</script>
