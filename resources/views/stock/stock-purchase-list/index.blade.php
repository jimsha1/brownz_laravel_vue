@extends('layouts.vcapp')

@php
    $defaultStart = now()->startOfMonth()->toDateString();
    $defaultEnd = now()->toDateString();
    $startDate = request('start_date', $defaultStart);
    $endDate = request('end_date', $defaultEnd);
@endphp

<style>
  .purchase-list-filters .filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
  }

  .purchase-list-filters .filters-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    margin-bottom: 0;
    font-weight: 500;
  }

  .purchase-list-filters .filters-hint {
    font-size: 12px;
    color: #6c757d;
  }

  .purchase-list-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .purchase-list-filters .search-box-bar-container {
    display: flex;
    align-items: center;
    background-color: #FFFFFF;
    box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;
    border-radius: 26px;
    width: 100%;
    max-width: none;
    padding-left: 16px;
    min-height: 44px;
  }

  .purchase-list-filters .brawnz-search-bar-in {
    background-color: transparent;
    border: none;
    box-shadow: none;
    outline: none;
    padding: 10px 16px 10px 8px;
    font-size: 14px;
    width: 100%;
    height: 44px;
  }

  .purchase-list-filters .filter-actions {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    gap: 8px;
    min-height: 44px;
  }

  .purchase-list-filters .filter-actions .btn {
    white-space: nowrap;
  }
</style>

@section('content')
  <div class="fbox-shadow mt-2 fd-card respo-margin-top purchase-list-filters">
    <div class="card-body">
      <div class="filters-header">
        <div>
          <h5 class="filters-title">{{ __('Purchases') }}</h5>
          <div class="filters-hint">{{ __('Filter by date, vendor, invoice, or reference') }}</div>
        </div>
      </div>

      <form class="voco-ajax-form tab-search-form" method="get" action="{{ $tab_meta_data[$tab_name]['tab_url'] }}">
        <input type="hidden" id="tab_search" name="tab_search" value="0">

        <div class="row align-items-end br-row-gap-2">
          <div class="col-lg-2 col-md-6 mb-3">
            <label class="filter-label" for="start_date">{{ __('Start Date') }}</label>
            <div class="search-box-bar-container jus-heigh-search-b">
              <input type="date" id="start_date" class="form-control brawnz-search-bar-in"
                name="start_date" value="{{ $startDate }}">
            </div>
          </div>

          <div class="col-lg-2 col-md-6 mb-3">
            <label class="filter-label" for="end_date">{{ __('End Date') }}</label>
            <div class="search-box-bar-container jus-heigh-search-b">
              <input type="date" id="end_date" class="form-control brawnz-search-bar-in"
                name="end_date" value="{{ $endDate }}">
            </div>
          </div>

          <div class="col-lg-5 col-md-8 mb-3">
            <label class="filter-label" for="purchase_search">{{ __('Search') }}</label>
            <div class="search-box-bar-container jus-heigh-search-b">
              <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
              <input type="text" id="purchase_search" class="form-control brawnz-search-bar-in" name="search"
                value="{{ request('search') }}"
                placeholder="{{ __('Vendor, invoice, or ref no...') }}">
            </div>
          </div>

          <div class="col-lg-3 col-md-4 mb-3">
            <label class="filter-label d-none d-lg-block">&nbsp;</label>
            <div class="filter-actions">
              <button type="button" id="refresh-btn" class="btn btn-sm btn-secondary brownz-btn-yellow-d">
                <i class="fa fa-refresh" aria-hidden="true"></i> {{ __('Reset') }}
              </button>
              <button type="submit" class="btn btn-sm btn-primary btn_dark-x">
                <i class="fa fa-search" aria-hidden="true"></i> {{ __('Search') }}
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  @include('global-ajax-tab-frame.tabbed-frame')
@endsection

@push('inline-scripts')
@if ($activate_via_js === true)
<script>
    jQuery(function($) {
        $("#{{ 'vtab-' . $tab_name }}").tab("show");
    });
</script>
@endif
<script>
    jQuery(function($) {
        $('#refresh-btn').on('click', function () {
            window.location.href = "{{ route('tab.stock.stock.purchase.list') }}";
        });
    });
</script>
@endpush
