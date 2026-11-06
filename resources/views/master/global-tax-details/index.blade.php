@extends('layouts.vcapp')

@section('content')
<style>
  .global-tax-details-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .global-tax-details-header .tax-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .global-tax-details-header .tax-meta {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
  }

  .global-tax-details-header .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
  }

  .global-tax-details-header .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .global-tax-details-header .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }
</style>

@if(!empty($global_tax_info))
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="global-tax-details-header">
        <div>
          <h5 class="tax-title">{{ $global_tax_info->name }}</h5>
          <p class="tax-meta">
            {{ __('Tax Rate') }}:
            {{ number_format((float) $global_tax_info->tax, 2) }}%
          </p>
        </div>
        @if((int) $global_tax_info->is_active === 1)
          <span class="status-pill is-active">{{ __('Active') }}</span>
        @else
          <span class="status-pill is-inactive">{{ __('Inactive') }}</span>
        @endif
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
    ['text' => __('Global Tax'), 'url' => route('tab.master.global.tax.list')],
    ['text' => $global_tax_info ? $global_tax_info->name : __('New Tax')],
]);
?>;
    });
</script>
@endif
@endpush
