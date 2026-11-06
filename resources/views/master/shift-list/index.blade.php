@extends('layouts.vcapp')

@section('content')

<div class="fbox-shadow mt-2 fd-card respo-margin-top">
  <div class="card-body">
    <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
      {{ __('Shifts') }}
    </h5>

    <form class="voco-ajax-form tab-search-form shift-list-filters" method="get" action="{{ $tab_meta_data[$tab_name]['tab_url'] }}">
      <input type="hidden" id="tab_search" name="tab_search" value="0">

      <div class="row align-items-end br-row-gap-2">
        <div class="col-xl-4 col-lg-4 col-md-6">
          <div class="search-box-bar-container jus-heigh-search-b">
            <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
            <input type="text" class="form-control brawnz-search-bar-in" name="search"
              value="{{ request('search') }}"
              placeholder="{{ __('Search by title...') }}">
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6">
          @php $selectedStatus = request('status', 'all'); @endphp
          <div class="search-box-bar-container jus-heigh-search-b">
            <select name="status" class="form-control brawnz-search-bar-in">
              <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>{{ __('All Status') }}</option>
              <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
              <option value="inactive" {{ $selectedStatus === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
            </select>
          </div>
        </div>

        <div class="col-xl-5 col-lg-5 col-md-12">
          <div class="shift-filter-actions">
            <button type="submit" class="btn btn-sm btn-primary btn_dark-x">
              <i class="fa fa-search"></i> {{ __('Search') }}
            </button>
            <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d" onclick="resetShiftFilters()">
              <i class="fa fa-refresh"></i> {{ __('Reset') }}
            </button>
          </div>
        </div>
      </div>
    </form>

<style>
  .shift-list-filters .shift-filter-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  @media (min-width: 1200px) {
    .shift-list-filters .shift-filter-actions {
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
    function resetShiftFilters() {
        window.location.href = "{{ route('tab.master.shift.list') }}";
    }
</script>
@endpush
