<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-info.open-balance-info {
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
</style>

<div id="vue-details-tab-info" class="open-balance-info info-sec-tab-cont">
  <template>
    <div>
      <div class="info-summary">
        <div class="summary-card">
          <span class="label">{{ __('Opening Balance ID') }}</span>
          <span class="value">#@{{ stock_open_balance_info.open_balance_id }}</span>
        </div>
        <div class="summary-card">
          <span class="label">{{ __('Amount') }}</span>
          <span class="value">@{{ formatAmount(stock_open_balance_info.amount) }}</span>
        </div>
        <div class="summary-card">
          <span class="label">{{ __('Opening Date') }}</span>
          <span class="value" style="font-size: 1rem;">@{{ formatDate(stock_open_balance_info.opening_date) }}</span>
        </div>
      </div>

      <div class="info-panel">
        <table class="table info-table">
          <tbody>
            <tr>
              <td class="info-label">{{ __('Account Name') }}</td>
              <td class="info-value">
                @{{ (stock_open_balance_info.account && stock_open_balance_info.account.name) || '—' }}
              </td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Created') }}</td>
              <td class="info-value">
                @{{ (stock_open_balance_info.created_user && stock_open_balance_info.created_user.name) || '—' }}
                <span class="text-muted font-italic" v-if="stock_open_balance_info.created_at">
                  on @{{ stock_open_balance_info.created_at | vc_datetime_expanded }}
                </span>
              </td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Updated') }}</td>
              <td class="info-value">
                @{{ (stock_open_balance_info.updated_user && stock_open_balance_info.updated_user.name) || '—' }}
                <span class="text-muted font-italic" v-if="stock_open_balance_info.updated_at">
                  on @{{ stock_open_balance_info.updated_at | vc_datetime_expanded }}
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
      data: <?php echo json_encode($details_info_vue); ?>,
      methods: {
        formatAmount: function (value) {
          return Number(value || 0).toFixed(2);
        },
        formatDate: function (value) {
          if (!value) return '—';
          if (window.moment) {
            return moment(value).format('DD MMM YYYY');
          }
          return value;
        }
      }
    });
  });
</script>
