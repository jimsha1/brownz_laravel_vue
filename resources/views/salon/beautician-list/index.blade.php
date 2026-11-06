@extends('layouts.vcapp')

@section('content')

<div class="fbox-shadow mt-2 fd-card respo-margin-top">
  <div class="card-body">
    <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
      {{ __('Beauticians') }}
    </h5>

    <form class="voco-ajax-form tab-search-form beautician-list-filters" method="get" action="{{ $tab_meta_data[$tab_name]['tab_url'] }}">
      <input type="hidden" id="tab_search" name="tab_search" value="0">

      <div class="row align-items-end br-row-gap-2">
        <div class="col-lg-5 col-md-6">
          <label class="filter-label" for="beautician_search">{{ __('Search') }}</label>
          <div class="search-box-bar-container jus-heigh-search-b">
            <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
            <input type="text" id="beautician_search" class="form-control brawnz-search-bar-in" name="search"
              value="{{ request('search') }}"
              placeholder="{{ __('Search by name, code, or mobile...') }}">
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <label class="filter-label" for="beautician_status">{{ __('Status') }}</label>
          @php $selectedStatus = request('status', 'active'); @endphp
          <div class="search-box-bar-container jus-heigh-search-b">
            <select id="beautician_status" name="status" class="form-control brawnz-search-bar-in">
              <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>{{ __('All Status') }}</option>
              <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
              <option value="inactive" {{ $selectedStatus === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
            </select>
          </div>
        </div>

        <div class="col-lg-4 col-md-12">
          <label class="filter-label d-none d-lg-block">&nbsp;</label>
          <div class="beautician-filter-actions">
            <button type="submit" class="btn btn-sm btn-primary btn_dark-x">
              <i class="fa fa-search"></i> {{ __('Search') }}
            </button>
            <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d" onclick="resetBeauticianFilters()">
              <i class="fa fa-refresh"></i> {{ __('Reset') }}
            </button>
          </div>
        </div>
      </div>
    </form>

<style>
  .beautician-list-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .beautician-list-filters .beautician-filter-actions {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  .beautician-list-filters .beautician-filter-actions .btn {
    white-space: nowrap;
  }

  @media (min-width: 992px) {
    .beautician-list-filters .beautician-filter-actions {
      justify-content: flex-start;
    }
  }
</style>
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
    function resetBeauticianFilters() {
        window.location.href = "{{ route('tab.salon.beautician.list') }}";
    }
</script>
@endpush
