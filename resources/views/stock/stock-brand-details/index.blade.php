@extends('layouts.vcapp')

<style>
  .stock-brand-details-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .stock-brand-details-header .item-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .stock-brand-details-header .item-meta {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
  }

  .stock-brand-details-header .status-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .stock-brand-details-header .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
  }

  .stock-brand-details-header .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .stock-brand-details-header .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }
</style>

@section('content')
@if(!empty($stock_brand_info) && !empty($stock_brand_info->brand_id))
  @php
    $isActive = (int) ($stock_brand_info->is_active ?? 0) === 1;
  @endphp
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="stock-brand-details-header">
        <div>
          <h5 class="item-title">{{ $stock_brand_info->brand_name ?? __('Stock Brand') }}</h5>
          <p class="item-meta">
            {{ __('Brand ID') }}: #{{ $stock_brand_info->brand_id }}
          </p>
        </div>
        <div class="status-pills">
          <span class="status-pill {{ $isActive ? 'is-active' : 'is-inactive' }}">
            {{ $isActive ? __('Active') : __('Inactive') }}
          </span>
        </div>
      </div>
    </div>
  </div>
@else
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="stock-brand-details-header">
        <div>
          <h5 class="item-title">{{ __('Create Stock Brand') }}</h5>
          <p class="item-meta">{{ __('Add a new brand for stock items') }}</p>
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
    ['text' => __('Stock Brands'), 'url' => route('tab.stock.stock.brand.list')],
    ['text' => !empty($stock_brand_info) && !empty($stock_brand_info->brand_name)
        ? $stock_brand_info->brand_name
        : __('New Brand')],
]);
?>;
    });
</script>
@endif
@endpush
