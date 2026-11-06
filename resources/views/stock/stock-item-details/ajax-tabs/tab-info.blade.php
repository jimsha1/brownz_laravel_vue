<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables/expressions/functions directly inside html
 */
?>
<style>
  #vue-details-tab-info.stock-item-info {
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

  #vue-details-tab-info .info-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 0 0 16px;
  }

  #vue-details-tab-info .info-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 999px;
    padding: 8px 14px;
    font-size: 12px;
    font-weight: 600;
    color: #1D1D1D;
    background: #F0F0F3;
    text-decoration: none;
  }

  #vue-details-tab-info .info-action-btn:hover {
    background: #1D1D1D;
    color: #fff;
    text-decoration: none;
  }
</style>

<div id="vue-details-tab-info" class="stock-item-info info-sec-tab-cont">
  <template>
    <div>
      <div class="info-summary">
        <div class="summary-card">
          <span class="label">{{ __('Item ID') }}</span>
          <span class="value">#@{{ stock_item_info.item_id }}</span>
        </div>
        <div class="summary-card">
          <span class="label">{{ __('Current Stock') }}</span>
          <span class="value">@{{ Number(stock_item_info.current_stock || 0).toFixed(2) }} <span v-if="stock_item_info.stock_uom">@{{ stock_item_info.stock_uom.uom_name }}</span></span>
        </div>
        <div class="summary-card">
          <span class="label">{{ __('Status') }}</span>
          <span class="value" style="font-size: 1rem;">
            <span class="status-pill" :class="Number(stock_item_info.is_active) === 1 ? 'is-active' : 'is-inactive'">
              <span v-if="Number(stock_item_info.is_active) === 1">{{ __('Active') }}</span>
              <span v-else>{{ __('Inactive') }}</span>
            </span>
          </span>
        </div>
      </div>

      <div class="info-actions" v-if="urls">
        <a class="info-action-btn" v-if="urls.stock_adjustment_url" v-bind:href="urls.stock_adjustment_url">
          <i class="fa fa-adjust"></i> {{ __('Adjust Stock') }}
        </a>
        <a class="info-action-btn" v-if="urls.stock_movements_url" v-bind:href="urls.stock_movements_url">
          <i class="fa fa-exchange"></i> {{ __('Stock Movements') }}
        </a>
      </div>

      <div class="info-panel">
        <table class="table info-table">
          <tbody>
            <tr>
              <td class="info-label">{{ __('Item Code') }}</td>
              <td class="info-value">@{{ stock_item_info.item_code || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Item Name') }}</td>
              <td class="info-value">@{{ stock_item_info.item_name || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('HSN/SAC') }}</td>
              <td class="info-value">@{{ stock_item_info.hsn_code || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Description') }}</td>
              <td class="info-value">@{{ stock_item_info.description || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Item Group') }}</td>
              <td class="info-value">@{{ (stock_item_info.category && stock_item_info.category.group_name) || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Brand') }}</td>
              <td class="info-value">@{{ (stock_item_info.brand && stock_item_info.brand.brand_name) || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Stock UOM') }}</td>
              <td class="info-value">@{{ (stock_item_info.stock_uom && stock_item_info.stock_uom.uom_name) || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Pack Size') }}</td>
              <td class="info-value">@{{ Number(stock_item_info.pack_size || 1) }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Tax Percent') }}</td>
              <td class="info-value">@{{ stock_item_info.tax_percent != null ? stock_item_info.tax_percent : '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Sales Item') }}</td>
              <td class="info-value">
                <span class="status-pill" :class="Number(stock_item_info.is_sales_item) === 1 ? 'is-active' : 'is-inactive'">
                  <span v-if="Number(stock_item_info.is_sales_item) === 1">{{ __('Yes') }}</span>
                  <span v-else>{{ __('No') }}</span>
                </span>
              </td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Created') }}</td>
              <td class="info-value">
                @{{ (stock_item_info.created_user && stock_item_info.created_user.name) || '—' }}
                <span class="text-muted font-italic" v-if="stock_item_info.created_at">
                  · @{{ stock_item_info.created_at | vc_datetime_expanded }}
                </span>
              </td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Updated') }}</td>
              <td class="info-value">
                @{{ (stock_item_info.updated_user && stock_item_info.updated_user.name) || '—' }}
                <span class="text-muted font-italic" v-if="stock_item_info.updated_at">
                  · @{{ stock_item_info.updated_at | vc_datetime_expanded }}
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
