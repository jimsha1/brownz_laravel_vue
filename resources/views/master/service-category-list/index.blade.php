@extends('layouts.vcapp')

@section('content')

<div class="fbox-shadow mt-2 fd-card respo-margin-top">
  <div class="card-body">
    <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
      {{ __('Service Categories') }}
    </h5>

    <form class="voco-ajax-form tab-search-form category-list-filters" method="get" action="{{ $tab_meta_data[$tab_name]['tab_url'] }}">
      <input type="hidden" id="tab_search" name="tab_search" value="0">

      <div class="row align-items-end br-row-gap-2">
        <div class="col-xl-3 col-lg-4 col-md-6">
          <div class="search-box-bar-container jus-heigh-search-b">
            <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
            <input type="text" class="form-control brawnz-search-bar-in" name="search"
              value="{{ request('search') }}"
              placeholder="{{ __('Search by name...') }}">
          </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-6">
          @php $selectedParent = request('parent_id', 'all'); @endphp
          <div class="search-box-bar-container jus-heigh-search-b">
            <select name="parent_id" class="form-control brawnz-search-bar-in">
              <option value="all" {{ $selectedParent === 'all' ? 'selected' : '' }}>{{ __('All Categories') }}</option>
              <option value="root" {{ $selectedParent === 'root' ? 'selected' : '' }}>{{ __('Top level only') }}</option>
              @foreach (($parentCategories ?? []) as $parentCategory)
                <option value="{{ $parentCategory->category_id }}"
                  {{ (string) $selectedParent === (string) $parentCategory->category_id ? 'selected' : '' }}>
                  {{ $parentCategory->name }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
          @php $selectedStatus = request('status', 'all'); @endphp
          <div class="search-box-bar-container jus-heigh-search-b">
            <select name="status" class="form-control brawnz-search-bar-in">
              <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>{{ __('All Status') }}</option>
              <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
              <option value="inactive" {{ $selectedStatus === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
            </select>
          </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
          @php $selectedWebsite = request('show_on_website', 'all'); @endphp
          <div class="search-box-bar-container jus-heigh-search-b">
            <select name="show_on_website" class="form-control brawnz-search-bar-in">
              <option value="all" {{ $selectedWebsite === 'all' ? 'selected' : '' }}>{{ __('All Website') }}</option>
              <option value="yes" {{ $selectedWebsite === 'yes' ? 'selected' : '' }}>{{ __('Show on Website') }}</option>
              <option value="no" {{ $selectedWebsite === 'no' ? 'selected' : '' }}>{{ __('Hidden on Website') }}</option>
            </select>
          </div>
        </div>

        <div class="col-xl-2 col-lg-8 col-md-12">
          <div class="category-filter-actions">
            <button type="submit" class="btn btn-sm btn-primary btn_dark-x">
              <i class="fa fa-search"></i> {{ __('Search') }}
            </button>
            <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d" onclick="resetCategoryFilters()">
              <i class="fa fa-refresh"></i> {{ __('Reset') }}
            </button>
          </div>
        </div>
      </div>
    </form>

<style>
  .category-list-filters .category-filter-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  @media (min-width: 1200px) {
    .category-list-filters .category-filter-actions {
      justify-content: flex-end;
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
    function resetCategoryFilters() {
        window.location.href = "{{ route('tab.master.service.category.list') }}";
    }
</script>
@endpush
