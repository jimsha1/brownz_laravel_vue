@extends('layouts.vcapp')

<style>
  .stock-item-group-details-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .stock-item-group-details-header .item-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .stock-item-group-details-header .item-meta {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
  }

  .stock-item-group-details-header .status-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .stock-item-group-details-header .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
  }

  .stock-item-group-details-header .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .stock-item-group-details-header .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }
</style>

@section('content')
@if(!empty($stock_item_group_info) && !empty($stock_item_group_info->item_group_id))
  @php
    $isActive = (int) ($stock_item_group_info->is_active ?? 0) === 1;
    $showWebsite = (int) ($stock_item_group_info->is_show_website ?? 0) === 1;
  @endphp
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="stock-item-group-details-header">
        <div>
          <h5 class="item-title">{{ $stock_item_group_info->group_name ?? __('Stock Item Group') }}</h5>
          <p class="item-meta">
            {{ __('Group ID') }}: #{{ $stock_item_group_info->item_group_id }}
          </p>
        </div>
        <div class="status-pills">
          <span class="status-pill {{ $isActive ? 'is-active' : 'is-inactive' }}">
            {{ $isActive ? __('Active') : __('Inactive') }}
          </span>
          <span class="status-pill {{ $showWebsite ? 'is-active' : 'is-inactive' }}">
            {{ __('Website') }}: {{ $showWebsite ? __('Yes') : __('No') }}
          </span>
        </div>
      </div>
    </div>
  </div>
@else
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="stock-item-group-details-header">
        <div>
          <h5 class="item-title">{{ __('Create Stock Item Group') }}</h5>
          <p class="item-meta">{{ __('Add a new item group or subcategory') }}</p>
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
    ['text' => __('Stock Item Groups'), 'url' => route('tab.stock.stock.item.group.list')],
    ['text' => !empty($stock_item_group_info) && !empty($stock_item_group_info->group_name)
        ? $stock_item_group_info->group_name
        : __('New Group')],
]);
?>;
    });
</script>
@endif
@endpush
