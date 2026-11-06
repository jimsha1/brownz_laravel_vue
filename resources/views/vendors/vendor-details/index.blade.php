@extends('layouts.vcapp')

@section('content')
<style>
  .vendor-details-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .vendor-details-header .vendor-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .vendor-details-header .vendor-meta {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
  }

  .vendor-details-header .status-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .vendor-details-header .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
    text-transform: capitalize;
  }

  .vendor-details-header .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .vendor-details-header .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  .vendor-details-header .status-pill.is-credit {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  .vendor-details-header .status-pill.is-clear {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }
</style>

@if(!empty($vendor_info))
  @php
    $isActive = (int) ($vendor_info->is_active ?? 0) === 1;
    $balance = (float) ($vendor_info->balance ?? 0);
  @endphp
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="vendor-details-header">
        <div>
          <h5 class="vendor-title">{{ $vendor_info->name ?? __('Vendor') }}</h5>
          <p class="vendor-meta">
            @if(!empty($vendor_info->phone))
              {{ $vendor_info->phone }}
            @endif
            @if(!empty($vendor_info->email))
              @if(!empty($vendor_info->phone)) · @endif
              {{ $vendor_info->email }}
            @endif
          </p>
        </div>
        <div class="status-pills">
          <span class="status-pill {{ $isActive ? 'is-active' : 'is-inactive' }}">
            {{ $isActive ? __('Active') : __('Inactive') }}
          </span>
          <span class="status-pill {{ $balance > 0 ? 'is-credit' : 'is-clear' }}">
            {{ __('Balance') }}: {{ number_format($balance, 2) }}
          </span>
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
    ['text' => __('Vendors'), 'url' => route('tab.vendors.vendor.list')],
    ['text' => $vendor_info->name ?? __('Vendor')],
]);
?>;
    });
</script>
@endif
@endpush
