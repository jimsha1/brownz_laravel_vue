<style>
  #vue-list-tab-all .item-table th,
  #vue-list-tab-all .item-table td {
    vertical-align: middle;
  }

  #vue-list-tab-all .report-summary {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 16px;
  }

  @media (max-width: 991px) {
    #vue-list-tab-all .report-summary {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  #vue-list-tab-all .summary-card {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 14px 16px;
  }

  #vue-list-tab-all .summary-card .label {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 4px;
  }

  #vue-list-tab-all .summary-card .value {
    font-size: 1.15rem;
    font-weight: 700;
    color: #1D1D1D;
    font-variant-numeric: tabular-nums;
  }

  #vue-list-tab-all .summary-card.is-out .value {
    color: #D0342C;
  }

  #vue-list-tab-all .summary-card.is-low .value {
    color: #8a5a00;
  }

  #vue-list-tab-all .summary-card.is-ok .value {
    color: #199900;
  }

  #vue-list-tab-all .item-name {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
    text-decoration: none;
  }

  #vue-list-tab-all .item-name:hover {
    color: #52A8D9;
    text-decoration: underline;
  }

  #vue-list-tab-all .item-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  #vue-list-tab-all .status-pill,
  #vue-list-tab-all .amount-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
  }

  #vue-list-tab-all .status-pill.is-active,
  #vue-list-tab-all .status-pill.is-ok {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  #vue-list-tab-all .status-pill.is-inactive,
  #vue-list-tab-all .status-pill.is-out {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  #vue-list-tab-all .status-pill.is-low {
    color: #8a5a00;
    background: rgba(240, 173, 78, 0.28);
  }

  #vue-list-tab-all .amount-pill {
    color: #1D1D1D;
    background: #F0F0F3;
    font-variant-numeric: tabular-nums;
  }

  #vue-list-tab-all .history-line {
    display: block;
    font-size: 12px;
    color: #1D1D1D;
    font-variant-numeric: tabular-nums;
  }

  #vue-list-tab-all .history-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  #vue-list-tab-all .history-btn {
    margin-top: 6px;
    border: none;
    background: transparent;
    color: #1e6789;
    font-size: 12px;
    font-weight: 600;
    padding: 0;
    cursor: pointer;
  }

  #vue-list-tab-all .history-btn:hover {
    text-decoration: underline;
  }

  #vue-list-tab-all .history-row td {
    background: #F7F7F9;
    padding: 12px 16px 16px;
  }

  #vue-list-tab-all .history-table {
    margin-bottom: 8px;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
  }

  #vue-list-tab-all .history-table th,
  #vue-list-tab-all .history-table td {
    font-size: 12px;
    padding: 8px 12px;
    border-top: none;
    border-bottom: 1px solid #f0f0f3;
  }

  #vue-list-tab-all .history-link {
    font-size: 12px;
    font-weight: 600;
  }

  #vue-list-tab-all .manage-actions {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  #vue-list-tab-all .manage-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #F0F0F3;
    color: #1D1D1D;
    text-decoration: none !important;
  }

  #vue-list-tab-all .manage-btn:hover {
    background: #1D1D1D;
    color: #E6CD7F;
  }

  #vue-list-tab-all .empty-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }

  #vue-list-tab-all .empty-state i {
    font-size: 28px;
    margin-bottom: 10px;
    color: #b7b7b7;
  }

  #vue-list-tab-all .tab-pagination {
    margin-top: 16px;
  }
</style>

<div id="vue-list-tab-all" class="m-n1">
  <div class="report-summary" v-if="summary">
    <div class="summary-card">
      <span class="label">{{ __('Items') }}</span>
      <span class="value">@{{ summary.items }}</span>
    </div>
    <div class="summary-card">
      <span class="label">{{ __('Inventory value') }}</span>
      <span class="value">@{{ Number(summary.inventory_value || 0).toFixed(2) }}</span>
    </div>
    <div class="summary-card is-ok">
      <span class="label">{{ __('In stock') }}</span>
      <span class="value">@{{ summary.in_stock }}</span>
    </div>
    <div class="summary-card is-low">
      <span class="label">{{ __('Low stock') }}</span>
      <span class="value">@{{ summary.low_stock }}</span>
    </div>
    <div class="summary-card is-out">
      <span class="label">{{ __('Out of stock') }}</span>
      <span class="value">@{{ summary.out_of_stock }}</span>
    </div>
  </div>

  <template v-if="paginator.db_rows.length">
    <div class="table-responsive">
      <table class="table brownz-table-list-v1 item-table">
        <thead>
          <tr>
            <th style="width: 56px;">{{ __('SL#') }}</th>
            <th>{{ __('Item') }}</th>
            <th class="text-right">{{ __('Current Stock') }}</th>
            <th>{{ __('Stock Level') }}</th>
            <th>{{ __('Inventory History') }}</th>
            <th class="text-right">{{ __('Value') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="hidden-print no-sort text-center" style="width: 110px;">{{ __('Manage') }}</th>
          </tr>
        </thead>
        <tbody>
          <template v-for="(item, index) in paginator.db_rows" :key="item.item_id || index">
            <tr>
              <td>@{{ index + paginator.sequence_start }}</td>
              <td>
                <a class="item-name" v-bind:href="item.view_url">@{{ item.item_name || '—' }}</a>
                <span class="item-meta">@{{ item.item_code || '—' }}</span>
                <span class="item-meta">
                  <span v-if="item.category && item.category.parent">@{{ item.category.parent.group_name || '—' }} » </span>
                  @{{ (item.category && item.category.group_name) || '—' }}
                  <span v-if="item.brand && item.brand.brand_name"> · @{{ item.brand.brand_name }}</span>
                </span>
              </td>
              <td class="text-right">
                <span class="amount-pill">@{{ Number(item.current_stock || 0).toFixed(2) }}<span v-if="item.stock_uom && item.stock_uom.uom_name"> @{{ item.stock_uom.uom_name }}</span></span>
              </td>
              <td>
                <span class="status-pill" :class="levelClass(item.stock_level)">@{{ item.stock_level_label }}</span>
                <span class="item-meta">@{{ Number(item.packs_available || 0).toFixed(2) }} {{ __('packs') }}</span>
              </td>
              <td>
                <span class="history-line">{{ __('In') }} @{{ Number(item.stock_in_total || 0).toFixed(2) }} · {{ __('Out') }} @{{ Number(item.stock_out_total || 0).toFixed(2) }}</span>
                <span class="history-meta" v-if="item.movement_count">
                  @{{ item.movement_count }} {{ __('movements') }}
                  <span v-if="item.last_movement_at"> · @{{ item.last_movement_at }}</span>
                </span>
                <span class="history-meta" v-else>{{ __('No movements yet') }}</span>
                <button type="button" class="history-btn" v-if="item.movement_count" @click="toggleHistory(item.item_id)">
                  <i class="fa" :class="open_item_id === item.item_id ? 'fa-chevron-up' : 'fa-history'"></i>
                  <span v-if="open_item_id === item.item_id">{{ __('Hide history') }}</span>
                  <span v-else>{{ __('Show history') }}</span>
                </button>
              </td>
              <td class="text-right">
                <span class="amount-pill">@{{ Number(item.inventory_value || 0).toFixed(2) }}</span>
                <span class="item-meta">{{ __('Cost') }} @{{ Number(item.cost_price || 0).toFixed(2) }}</span>
              </td>
              <td>
                <span class="status-pill" :class="Number(item.is_active) === 1 ? 'is-active' : 'is-inactive'">
                  <span v-if="Number(item.is_active) === 1">{{ __('Active') }}</span>
                  <span v-else>{{ __('Inactive') }}</span>
                </span>
              </td>
              <td class="hidden-print text-center">
                <div class="manage-actions">
                  <a class="manage-btn" v-bind:href="item.view_url" title="{{ __('View') }}">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                  </a>
                  <a class="manage-btn" v-bind:href="item.edit_url" title="{{ __('Edit') }}">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                  </a>
                </div>
              </td>
            </tr>
            <tr v-if="open_item_id === item.item_id" :key="'history-' + item.item_id" class="history-row">
              <td colspan="8">
                <table class="table history-table">
                  <thead>
                    <tr>
                      <th>{{ __('When') }}</th>
                      <th>{{ __('Type') }}</th>
                      <th class="text-right">{{ __('Stock In') }}</th>
                      <th class="text-right">{{ __('Stock Out') }}</th>
                      <th>{{ __('Note') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="movement in item.inventory_history" :key="movement.stock_movement_id">
                      <td>@{{ movement.happened_at || '—' }}</td>
                      <td>@{{ movement.kind }}</td>
                      <td class="text-right">@{{ Number(movement.stock_in || 0).toFixed(2) }}</td>
                      <td class="text-right">@{{ Number(movement.stock_out || 0).toFixed(2) }}</td>
                      <td>@{{ movement.description || '—' }}</td>
                    </tr>
                  </tbody>
                </table>
                <a class="history-link" v-bind:href="item.movements_url">{{ __('Open full stock movements') }}</a>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div class="tab-pagination" v-html="paginator.links"></div>
  </template>

  <template v-else>
    <div class="empty-state">
      <div><i class="fa fa-cubes" aria-hidden="true"></i></div>
      <div><strong>{{ __('No stock items found') }}</strong></div>
      <div>{{ __('Try adjusting search, status, or stock level.') }}</div>
    </div>
  </template>
</div>

<script>
var vue_list_tab_all = null;
jQuery(function ($) {
  var reportData = <?php echo json_encode($db_results['vue_data']['vue_list_tab_all']); ?>;
  reportData.open_item_id = null;
  vue_list_tab_all = new Vue({
    el: "#vue-list-tab-all",
    data: reportData,
    methods: {
      toggleHistory: function (itemId) {
        this.open_item_id = this.open_item_id === itemId ? null : itemId;
      },
      levelClass: function (level) {
        if (level === 'out') {
          return 'is-out';
        }
        if (level === 'low') {
          return 'is-low';
        }
        return 'is-ok';
      }
    }
  });
});
</script>
