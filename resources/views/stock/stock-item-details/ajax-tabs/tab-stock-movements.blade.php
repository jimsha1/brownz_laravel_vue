<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-stock-movements.stock-movements-tab {
    padding: 4px 0 8px;
  }

  #vue-details-tab-stock-movements .movements-panel {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 16px;
  }

  #vue-details-tab-stock-movements .panel-title {
    font-size: 13px;
    font-weight: 600;
    color: #1D1D1D;
    margin: 0 0 6px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  #vue-details-tab-stock-movements .panel-hint {
    font-size: 12px;
    color: #6c757d;
    margin: 0 0 14px;
  }

  #vue-details-tab-stock-movements .movements-table {
    margin-bottom: 0;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
  }

  #vue-details-tab-stock-movements .movements-table thead th {
    border-top: none;
    border-bottom: 1px solid #e8e8ec;
    color: #6c757d;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    padding: 12px 14px;
    background: #fff;
  }

  #vue-details-tab-stock-movements .movements-table td {
    border-top: none;
    border-bottom: 1px solid #f0f0f3;
    vertical-align: middle;
    padding: 12px 14px;
    color: #1D1D1D;
  }

  #vue-details-tab-stock-movements .movements-table tr:last-child td {
    border-bottom: none;
  }

  #vue-details-tab-stock-movements .opening-pill {
    display: inline-flex;
    align-items: center;
    margin-left: 6px;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 600;
    color: #1e6789;
    background: rgba(82, 168, 217, 0.18);
  }

  #vue-details-tab-stock-movements .opening-pill.is-adjustment {
    color: #7a4b12;
    background: rgba(240, 173, 78, 0.22);
  }

  #vue-details-tab-stock-movements .qty-pill {
    display: inline-flex;
    min-width: 42px;
    justify-content: center;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 600;
    font-variant-numeric: tabular-nums;
    background: #F0F0F3;
    color: #1D1D1D;
  }

  #vue-details-tab-stock-movements .qty-pill.is-in {
    color: #199900;
    background: rgba(25, 153, 0, 0.12);
  }

  #vue-details-tab-stock-movements .qty-pill.is-out {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  #vue-details-tab-stock-movements .manage-btn {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #F0F0F3;
    color: #1D1D1D;
    text-decoration: none;
  }

  #vue-details-tab-stock-movements .manage-btn:hover {
    background: #1D1D1D;
    color: #fff;
    text-decoration: none;
  }

  #vue-details-tab-stock-movements .empty-state {
    text-align: center;
    padding: 28px 12px;
    color: #6c757d;
    font-size: 13px;
  }
</style>

<div id="vue-details-tab-stock-movements" class="stock-movements-tab">
  <template>
    <div class="movements-panel">
      <div class="panel-title">{{ __('Stock Movements') }}</div>
      <p class="panel-hint">{{ __('All stock in and stock out activity for this item.') }}</p>

      <div class="table-responsive">
        <table class="table movements-table">
          <thead>
            <tr>
              <th>{{ __('SL#') }}</th>
              <th>{{ __('Stock In') }}</th>
              <th>{{ __('Stock Out') }}</th>
              <th>{{ __('Note') }}</th>
              <th class="hidden-print text-center">{{ __('Manage') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!movements || movements.length === 0">
              <td colspan="5" class="empty-state">
                {{ __('No stock movements yet for this item.') }}
              </td>
            </tr>
            <tr v-for="(item, index) in movements" :key="item.stock_movement_id || index">
              <td>@{{ index + 1 }}</td>
              <td>
                <span class="qty-pill" :class="{ 'is-in': Number(item.stock_in) > 0 }">
                  @{{ Number(item.stock_in || 0) }}
                </span>
                <span v-if="isOpeningStock(item)" class="opening-pill">{{ __('Opening Stock') }}</span>
                <span v-else-if="isStockAdjustment(item)" class="opening-pill is-adjustment">{{ __('Adjustment') }}</span>
              </td>
              <td>
                <span class="qty-pill" :class="{ 'is-out': Number(item.stock_out) > 0 }">
                  @{{ Number(item.stock_out || 0) }}
                </span>
              </td>
              <td>@{{ item.description || '—' }}</td>
              <td class="hidden-print text-center">
                <a v-if="isOpeningStock(item)"
                  class="manage-btn"
                  v-bind:href="urls.opening_stock_url"
                  title="{{ __('Edit Opening Stock') }}">
                  <i class="fa fa-pencil" aria-hidden="true"></i>
                </a>
                <a v-else-if="isStockAdjustment(item)"
                  class="manage-btn"
                  v-bind:href="urls.stock_adjustment_url"
                  title="{{ __('New Stock Adjustment') }}">
                  <i class="fa fa-adjust" aria-hidden="true"></i>
                </a>
                <span v-else class="text-muted">—</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>
</div>

<script>
  var vue_details_tab_stock_movements = null;
  jQuery(function ($) {
    vue_details_tab_stock_movements = new Vue({
      el: "#vue-details-tab-stock-movements",
      data: <?php echo json_encode($details_movements_vue); ?>,
      methods: {
        isOpeningStock: function (item) {
          return item && item.description
            && String(item.description).toLowerCase().indexOf('opening stock for item:') !== -1;
        },
        isStockAdjustment: function (item) {
          return item && item.description
            && String(item.description).toLowerCase().indexOf('stock adjustment for item:') !== -1;
        }
      }
    });
  });
</script>
