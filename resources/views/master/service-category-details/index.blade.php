@extends('layouts.vcapp')

@section('content')
<style>
  .service-category-details-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .service-category-details-header .category-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .service-category-details-header .category-meta {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
  }

  .service-category-details-header .status-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .service-category-details-header .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
  }

  .service-category-details-header .status-pill.is-active,
  .service-category-details-header .status-pill.is-yes {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .service-category-details-header .status-pill.is-inactive,
  .service-category-details-header .status-pill.is-no {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }
</style>

@if(!empty($service_category_info))
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="service-category-details-header">
        <div>
          <h5 class="category-title">{{ $service_category_info->name }}</h5>
          <p class="category-meta">
            {{ $service_category_info->tagline_en ?: __('No tagline') }}
          </p>
        </div>
        <div class="status-pills">
          @if((int) $service_category_info->show_on_website === 1)
            <span class="status-pill is-yes">{{ __('Website: Yes') }}</span>
          @else
            <span class="status-pill is-no">{{ __('Website: No') }}</span>
          @endif
          @if((int) $service_category_info->is_active === 1)
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
    ['text' => __('Service Categories'), 'url' => route('tab.master.service.category.list')],
    ['text' => $service_category_info ? $service_category_info->name : __('New Category')],
]);
?>;
    });
</script>
@endif
@endpush
