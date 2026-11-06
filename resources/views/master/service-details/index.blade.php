@extends('layouts.vcapp')

@section('content')
<style>
  .service-details-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .service-details-header .service-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .service-details-header .service-meta {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
  }

  .service-details-header .status-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .service-details-header .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
  }

  .service-details-header .status-pill.is-active,
  .service-details-header .status-pill.is-yes {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .service-details-header .status-pill.is-inactive,
  .service-details-header .status-pill.is-no {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }
</style>

@if(!empty($service_info))
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="service-details-header">
        <div>
          <h5 class="service-title">{{ $service_info->name }}</h5>
          <p class="service-meta">
            @php
              $service_info->loadMissing('category.parent');
              $categoryLabel = optional($service_info->category)->name;
              if (optional(optional($service_info->category)->parent)->name) {
                  $categoryLabel = $service_info->category->parent->name . ' » ' . $categoryLabel;
              }
            @endphp
            {{ $categoryLabel ?: __('No category') }}
            @if(!empty($service_info->service_base_price))
              · {{ __('Price') }}: {{ $service_info->service_base_price }}
            @endif
          </p>
        </div>
        <div class="status-pills">
          @php
            $websiteEnabled = (int) ($service_info->getAttributes()['show_on_website'] ?? 0) === 1;
            $serviceActive = (int) ($service_info->getAttributes()['is_active'] ?? 0) === 1;
          @endphp
          @if($websiteEnabled)
            <span class="status-pill is-yes">{{ __('Website: Yes') }}</span>
          @else
            <span class="status-pill is-no">{{ __('Website: No') }}</span>
          @endif
          @if($serviceActive)
            <span class="status-pill is-active">{{ __('Active') }}</span>
          @else
            <span class="status-pill is-inactive">{{ __('Inactive') }}</span>
          @endif
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
    ['text' => __('Services'), 'url' => route('tab.master.service.list')],
    ['text' => $service_info ? $service_info->name : __('New Service')],
]);
?>;
    });
</script>
@endif
@endpush
