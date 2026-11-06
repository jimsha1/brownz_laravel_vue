<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-info.purchase-info {
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
    margin-bottom: 16px;
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

  #vue-details-tab-info .products-panel {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 14px 16px;
  }

  #vue-details-tab-info .products-title {
    font-size: 13px;
    font-weight: 600;
    color: #1D1D1D;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    margin-bottom: 10px;
  }

  #vue-details-tab-info .products-table th,
  #vue-details-tab-info .products-table td {
    vertical-align: middle;
  }
</style>

<div id="vue-details-tab-info" class="purchase-info info-sec-tab-cont">
  <template>
    <div>
      <div class="info-summary">
        <div class="summary-card">
          <span class="label">{{ __('Purchase ID') }}</span>
          <span class="value">#@{{ stock_purchase_info.purchase_id }}</span>
        </div>
        <div class="summary-card">
          <span class="label">{{ __('Amount') }}</span>
          <span class="value">@{{ Number(stock_purchase_info.total_purchase_amount || 0).toFixed(2) }}</span>
        </div>
        <div class="summary-card">
          <span class="label">{{ __('Payment Method') }}</span>
          <span class="value" style="font-size: 1rem; text-transform: capitalize;">
            @{{ stock_purchase_info.payment_method || '—' }}
          </span>
        </div>
      </div>

      <div class="info-panel">
        <table class="table info-table">
          <tbody>
            <tr>
              <td class="info-label">{{ __('Purchase Ref.No') }}</td>
              <td class="info-value">@{{ stock_purchase_info.purchase_ref_no || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Vendor') }}</td>
              <td class="info-value">@{{ (stock_purchase_info.vendor && stock_purchase_info.vendor.name) || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Purchase Date') }}</td>
              <td class="info-value">@{{ stock_purchase_info.purchase_date || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Invoice No') }}</td>
              <td class="info-value">@{{ stock_purchase_info.purchase_invoice_no || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Description') }}</td>
              <td class="info-value">@{{ stock_purchase_info.purchase_description || '—' }}</td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Created') }}</td>
              <td class="info-value">
                @{{ (stock_purchase_info.created_user && stock_purchase_info.created_user.name) || '—' }}
                <span class="text-muted font-italic" v-if="stock_purchase_info.created_at">
                  · @{{ stock_purchase_info.created_at | vc_datetime_expanded }}
                </span>
              </td>
            </tr>
            <tr>
              <td class="info-label">{{ __('Updated') }}</td>
              <td class="info-value">
                @{{ (stock_purchase_info.updated_user && stock_purchase_info.updated_user.name) || '—' }}
                <span class="text-muted font-italic" v-if="stock_purchase_info.updated_at">
                  · @{{ stock_purchase_info.updated_at | vc_datetime_expanded }}
                </span>
              </td>
            </tr>
            <tr v-if="stock_purchase_info.edit_logs && stock_purchase_info.edit_logs.length">
              <td class="info-label">{{ __('Edit Reason') }}</td>
              <td class="info-value">
                <div v-for="(log, index) in stock_purchase_info.edit_logs" :key="log.id" :class="{ 'mt-2 pt-2 border-top': index > 0 }">
                  <div>@{{ log.edit_reason }}</div>
                  <div class="text-muted font-italic">
                    @{{ (log.edited_user && log.edited_user.name) ? log.edited_user.name : '' }}
                    <span v-if="log.created_at">
                      · @{{ log.created_at | vc_datetime_expanded }}
                    </span>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="products-panel" v-if="stock_purchase_info.product_rows && stock_purchase_info.product_rows.length">
        <div class="products-title">{{ __('Products') }}</div>
        <div class="table-responsive">
          <table class="table brownz-table-list-v1 products-table mb-0">
            <thead>
              <tr>
                <th>{{ __('Item') }}</th>
                <th>{{ __('HSN/SAC') }}</th>
                <th class="text-right">{{ __('Qty') }}</th>
                <th class="text-right">{{ __('Rate (Incl. of tax)') }}</th>
                <th class="text-right">{{ __('Rate') }}</th>
                <th class="text-right">{{ __('Disc. %') }}</th>
                <th class="text-right">{{ __('Amount') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(row, index) in stock_purchase_info.product_rows" :key="index">
                <td>@{{ (row.stock_item && row.stock_item.item_name) || ('#' + row.item_id) }}</td>
                <td>@{{ row.hsn_code || (row.stock_item && row.stock_item.hsn_code) || '—' }}</td>
                <td class="text-right">@{{ row.quantity }}</td>
                <td class="text-right">@{{ inclusiveRate(row).toFixed(2) }}</td>
                <td class="text-right">@{{ Number(row.cost_price || 0).toFixed(2) }}</td>
                <td class="text-right">@{{ discountLabel(row) }}</td>
                <td class="text-right">@{{ lineAmount(row).toFixed(2) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="mt-3 text-right" v-if="Number(stock_purchase_info.freight_amount) > 0">
          <div>{{ __('Freight') }}: @{{ Number(stock_purchase_info.freight_amount).toFixed(2) }}</div>
          <div v-if="Number(stock_purchase_info.freight_tax_percent) > 0">
            {{ __('Freight tax') }} @{{ Number(stock_purchase_info.freight_tax_percent).toFixed(2) }}%:
            @{{ (Number(stock_purchase_info.freight_amount) * Number(stock_purchase_info.freight_tax_percent) / 100).toFixed(2) }}
          </div>
        </div>
      </div>
    </div>
  </template>
</div>

<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
  var vue_details_tab_info = null;
  jQuery(function($) {
    vue_details_tab_info = new Vue({
      el: "#vue-details-tab-info",
      data: <?php echo json_encode($details_info_vue); ?>,
      methods: {
        inclusiveRate: function (row) {
          if (row.rate_incl_tax !== null && row.rate_incl_tax !== undefined && row.rate_incl_tax !== '') {
            return Number(row.rate_incl_tax) || 0;
          }
          var rate = Number(row.cost_price) || 0;
          var tax = Number(row.tax_percent) || 0;
          return Math.round(rate * (1 + tax / 100) * 100) / 100;
        },
        discountLabel: function (row) {
          if (row.discount_percent !== null && row.discount_percent !== undefined && row.discount_percent !== '') {
            return Number(row.discount_percent).toFixed(2);
          }
          return Number(row.discount || 0).toFixed(2);
        },
        lineAmount: function (row) {
          var qty = Number(row.quantity) || 0;
          var rate = Number(row.cost_price) || 0;
          if (row.discount_percent !== null && row.discount_percent !== undefined && row.discount_percent !== '') {
            var disc = Number(row.discount_percent) || 0;
            return Math.round(qty * rate * (1 - disc / 100) * 100) / 100;
          }
          return Number(row.purchase_total) || 0;
        }
      }
    });
  });
</script>
