@extends('layouts.vcapp')

@php
    $selectedStatus = request('status', 'active');
    $selectedCredit = request('credit_balance', '');
@endphp

<style>
  .vendor-list-filters .filters-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 16px;
  }

  .vendor-list-filters .filters-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    margin-bottom: 4px;
    font-weight: 500;
  }

  .vendor-list-filters .filters-hint {
    font-size: 12px;
    color: #6c757d;
    margin: 0;
  }

  .vendor-list-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .vendor-list-filters .search-box-bar-container {
    display: flex;
    align-items: center;
    background-color: #FFFFFF;
    box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;
    border-radius: 26px;
    width: 100%;
    padding-left: 16px;
    min-height: 44px;
  }

  .vendor-list-filters .search-box-bar-container .fa-search {
    color: #9aa0a6;
    font-size: 14px;
  }

  .vendor-list-filters .brawnz-search-bar-in {
    background-color: transparent;
    border: none;
    box-shadow: none;
    outline: none;
    padding: 10px 16px 10px 8px;
    font-size: 14px;
    width: 100%;
    height: 44px;
  }

  .vendor-list-filters select.brawnz-search-bar-in {
    padding-right: 28px;
    appearance: auto;
    cursor: pointer;
  }

  .vendor-list-filters .filter-row {
    margin-left: -8px;
    margin-right: -8px;
  }

  .vendor-list-filters .filter-row > [class*="col-"] {
    padding-left: 8px;
    padding-right: 8px;
  }

  .vendor-list-filters .filter-actions {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    min-height: 44px;
  }

  .vendor-list-filters .filter-actions .btn {
    white-space: nowrap;
    min-width: 110px;
  }

  .vendor-list-filters .filter-actions-row {
    margin-top: 4px;
    padding-top: 4px;
  }

  /* Single "All" tab is redundant — keep only the action button row */
  .vendor-list-filters + .fbox-shadow .vc-ajax-nav-tabs > .brow-tab-item:first-child {
    display: none;
  }

  .vendor-list-filters + .fbox-shadow .vc-ajax-nav-tabs {
    border-bottom: none;
    min-height: 42px;
  }
</style>

@section('content')
<div class="fbox-shadow mt-2 fd-card respo-margin-top vendor-list-filters">
  <div class="card-body">
    <div class="filters-header">
      <div>
        <h5 class="filters-title">{{ __('Vendors') }}</h5>
        <p class="filters-hint">{{ __('Search and filter suppliers by contact details, status, or outstanding balance.') }}</p>
      </div>
    </div>

    <form class="voco-ajax-form tab-search-form" method="get" action="{{ $tab_meta_data[$tab_name]['tab_url'] }}">
      <input type="hidden" id="tab_search" name="tab_search" value="0">

      <div class="row align-items-end filter-row br-row-gap-2">
        <div class="col-lg-5 col-md-12">
          <label class="filter-label" for="vendor_search">{{ __('Search') }}</label>
          <div class="search-box-bar-container">
            <i class="fa fa-search" aria-hidden="true"></i>
            <input type="text"
              id="vendor_search"
              class="form-control brawnz-search-bar-in"
              name="search"
              value="{{ request('search') }}"
              placeholder="{{ __('Name, phone, email, address, or GST...') }}">
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <label class="filter-label" for="vendor_status">{{ __('Status') }}</label>
          <div class="search-box-bar-container">
            <select id="vendor_status" name="status" class="form-control brawnz-search-bar-in vendor-filter-select" aria-label="{{ __('Status') }}">
              <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>{{ __('All Status') }}</option>
              <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
              <option value="inactive" {{ $selectedStatus === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
            </select>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <label class="filter-label" for="vendor_credit_balance">{{ __('Outstanding Balance') }}</label>
          <div class="search-box-bar-container">
            <select id="vendor_credit_balance" name="credit_balance" class="form-control brawnz-search-bar-in vendor-filter-select" aria-label="{{ __('Outstanding Balance') }}">
              <option value="" {{ $selectedCredit === '' ? 'selected' : '' }}>{{ __('All Vendors') }}</option>
              <option value="yes" {{ $selectedCredit === 'yes' ? 'selected' : '' }}>{{ __('With Outstanding Balance') }}</option>
            </select>
          </div>
        </div>
      </div>

      <div class="row filter-row filter-actions-row">
        <div class="col-12">
          <div class="filter-actions">
            <button type="button" id="vendor-reset-btn" class="btn btn-sm btn-secondary brownz-btn-yellow-d">
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
@if($activate_via_js === true)
<script>
    jQuery(function ($) {
        $("#{{ 'vtab-' . $tab_name }}").tab("show");
    });
</script>
@endif
<script>
    jQuery(function ($) {
        $('#vendor-reset-btn').on('click', function () {
            window.location.href = "{{ route('tab.vendors.vendor.list') }}";
        });

        $('.vendor-list-filters .vendor-filter-select').on('change', function () {
            var $form = $(this).closest('form');
            $form.find('[name="tab_search"]').val(1);
            $form.trigger('submit');
        });
    });
</script>
@endpush
