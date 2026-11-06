@extends('layouts.vcapp')

<style>
  .stock-item-details-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .stock-item-details-header .item-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .stock-item-details-header .item-meta {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
  }

  .stock-item-details-header .status-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .stock-item-details-header .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
  }

  .stock-item-details-header .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .stock-item-details-header .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  .stock-item-details-header .status-pill.is-stock {
    color: #1D1D1D;
    background: #F0F0F3;
  }
</style>

@section('content')
@if(!empty($stock_item_info) && !empty($stock_item_info->item_id))
  @php
    $isActive = (int) ($stock_item_info->is_active ?? 0) === 1;
    $stock = (float) ($stock_item_info->current_stock ?? 0);
    $uomName = optional($stock_item_info->stockUom)->uom_name ?? '';
    $packSize = (float) ($stock_item_info->pack_size ?? 1);
    if ($packSize <= 0) {
        $packSize = 1;
    }
    $stockLabel = number_format($stock, 2).($uomName !== '' ? ' '.$uomName : '');
    if ($packSize > 1) {
        $stockLabel .= ' ('.number_format($stock / $packSize, 2).' '.__('packs').')';
    }
  @endphp
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="stock-item-details-header">
        <div>
          <h5 class="item-title">{{ $stock_item_info->item_name ?? __('Stock Item') }}</h5>
          <p class="item-meta">
            {{ $stock_item_info->item_code ?? '' }}
          </p>
        </div>
        <div class="status-pills">
          <span class="status-pill {{ $isActive ? 'is-active' : 'is-inactive' }}">
            {{ $isActive ? __('Active') : __('Inactive') }}
          </span>
          <span class="status-pill is-stock">
            {{ __('Stock') }}: {{ $stockLabel }}
          </span>
        </div>
      </div>
    </div>
  </div>
@else
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="stock-item-details-header">
        <div>
          <h5 class="item-title">{{ __('Create Stock Item') }}</h5>
          <p class="item-meta">{{ __('Add a new inventory item') }}</p>
        </div>
      </div>
    </div>
  </div>
@endif

@include('global-ajax-tab-frame.tabbed-frame')
@endsection

@push('inline-scripts')
@if($activate_via_js === true)
<script>
    jQuery(function ($) {
        $("#{{ 'vtab-' . $tab_name }}").tab("show");

        v_breadcrumb.breadcrumb_links = <?php
echo json_encode([
    ['text' => __('Dashboard'), 'url' => url('/')],
    ['text' => __('Stock Items'), 'url' => route('tab.stock.stock.item.list')],
    ['text' => !empty($stock_item_info->item_id)
        ? ($stock_item_info->item_name ?: $stock_item_info->item_code)
        : __('New Item')],
]);
?>;
    });
</script>
@endif
@endpush
