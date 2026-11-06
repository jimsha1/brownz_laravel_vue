@extends('layouts.vcapp')

@section('content')

<div class="fbox-shadow mt-2 fd-card respo-margin-top">
  <div class="card-body">
    <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
      {{ __('Roles') }}
    </h5>

    <form class="voco-ajax-form tab-search-form" method="get" action="{{ $tab_meta_data[$tab_name]['tab_url'] }}">
      <input type="hidden" id="tab_search" name="tab_search" value="0">

      <div class="row align-items-center br-row-gap-2">
        <div class="col-lg-4 col-md-6 col-sm-8">
          <div class="search-box-bar-container jus-heigh-search-b">
            <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
            <input type="text" class="form-control brawnz-search-bar-in" name="search"
              value="{{ request('search') }}"
              placeholder="{{ __('Search by name or description...') }}">
          </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-4 d-flex flex-wrap gap-2">
          <button type="submit" class="btn btn-sm btn-primary btn_dark-x mr-2">
            <i class="fa fa-search"></i> {{ __('Search') }}
          </button>
          <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d" onclick="resetRoleFilters()">
            <i class="fa fa-refresh"></i> {{ __('Reset') }}
          </button>
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
    function resetRoleFilters() {
        window.location.href = "{{ route('tab.acl.role.list') }}";
    }
</script>
@endpush
