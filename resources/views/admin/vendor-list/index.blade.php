@extends('layouts.vcapp')

@section('content')

<div class="card card-primary fbox-shadow mt-5 fd-card respo-margin-top">
  <div class="card-body">
    <form class="voco-ajax-form tab-search-form" method="get" action="{{ $tab_meta_data[$tab_name]['tab_url'] }}">
      <input type="hidden" id="tab_search" name="tab_search" value="0">

      <div class="row align-items-center br-row-gap-2">
        <div class="col-lg-3 col-sm-8">
          <div class="search-box-bar-container jus-heigh-search-b ">
            <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
            <input type="text" class="form-control brawnz-search-bar-in" name="search" placeholder="{{ __('Search...') }}">
          </div>
        </div>
        <div class="col-lg-3 col-sm-4">
          <button type="submit" class="btn btn-sm btn-primary btn_dark-x">
            {{ __('Search') }}
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
@endpush
