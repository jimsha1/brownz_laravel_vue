@extends('layouts.vcapp')

@section('content')
<div class="fbox-shadow mt-2 fd-card respo-margin-top">
  <div class="card-body">
    <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
      {{ __('Clients') }}
    </h5>

    <form class="voco-ajax-form tab-search-form client-list-filters" method="get" action="{{ $tab_meta_data[$tab_name]['tab_url'] }}">
      <input type="hidden" id="tab_search" name="tab_search" value="0">

      <div class="row align-items-end br-row-gap-2">
        <div class="col-lg-4 col-md-6">
          <label class="filter-label" for="client_search_input">{{ __('Search') }}</label>
          <div class="search-box-bar-container jus-heigh-search-b">
            <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
            <input type="text" class="form-control brawnz-search-bar-in" name="search"
              id="client_search_input"
              value="{{ request('search') }}"
              placeholder="{{ __('Search by name, mobile, or email...') }}">
          </div>
        </div>

        <div class="col-lg-2 col-md-6">
          <label class="filter-label" for="client_status">{{ __('Status') }}</label>
          @php $selectedStatus = request('status', 'active'); @endphp
          <div class="search-box-bar-container jus-heigh-search-b">
            <select id="client_status" name="status" class="form-control brawnz-search-bar-in">
              <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>{{ __('All Status') }}</option>
              <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
              <option value="inactive" {{ $selectedStatus === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
              <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
              <option value="blocked" {{ $selectedStatus === 'blocked' ? 'selected' : '' }}>{{ __('Blocked') }}</option>
            </select>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <label class="filter-label" for="client_credit_balance">{{ __('Credit Balance') }}</label>
          @php $selectedCredit = request('credit_balance', ''); @endphp
          <div class="search-box-bar-container jus-heigh-search-b">
            <select id="client_credit_balance" name="credit_balance" class="form-control brawnz-search-bar-in">
              <option value="" {{ $selectedCredit === '' ? 'selected' : '' }}>{{ __('All Clients') }}</option>
              <option value="yes" {{ $selectedCredit === 'yes' ? 'selected' : '' }}>{{ __('Credit Balance Clients') }}</option>
            </select>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <label class="filter-label d-none d-lg-block">&nbsp;</label>
          <div class="client-filter-actions">
            <button type="submit" class="btn btn-sm btn-primary btn_dark-x">
              <i class="fa fa-search"></i> {{ __('Search') }}
            </button>
            <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d" onclick="resetClientFilters()">
              <i class="fa fa-refresh"></i> {{ __('Reset') }}
            </button>
          </div>
        </div>
      </div>
    </form>

<style>
  .client-list-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .client-list-filters .client-filter-actions {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  .client-list-filters .client-filter-actions .btn {
    white-space: nowrap;
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
    function resetClientFilters() {
        window.location.href = "{{ route('tab.admin.client.list') }}";
    }
</script>
@endpush
