@extends('layouts.vcapp')

<style>
  .service-list-filters .filters-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    margin-bottom: 16px;
    font-weight: 500;
  }

  .service-list-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .service-list-filters .search-box-bar-container {
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

  .service-list-filters .brawnz-search-bar-in {
    background-color: transparent;
    border: none;
    box-shadow: none;
    outline: none;
    padding: 10px 16px 10px 8px;
    font-size: 14px;
    width: 100%;
    min-width: 0;
    height: 44px;
  }

  .service-list-filters select.brawnz-search-bar-in {
    padding-right: 28px;
    appearance: auto;
    text-overflow: ellipsis;
  }

  .service-list-filters .service-filter-actions {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    gap: 8px;
    min-height: 44px;
  }

  .service-list-filters .service-filter-actions .btn {
    white-space: nowrap;
  }

  @media (min-width: 992px) {
    .service-list-filters .service-filter-actions {
      justify-content: flex-end;
    }
  }
</style>

@section('content')

<div class="fbox-shadow mt-2 fd-card respo-margin-top service-list-filters">
  <div class="card-body">
    <h5 class="filters-title">{{ __('Services') }}</h5>

    <form class="voco-ajax-form tab-search-form" method="get" action="{{ $tab_meta_data[$tab_name]['tab_url'] }}">
      <input type="hidden" id="tab_search" name="tab_search" value="0">

      <div class="row align-items-end br-row-gap-2">
        <div class="col-lg-4 col-md-6 mb-3">
          <label class="filter-label" for="service_search">{{ __('Search') }}</label>
          <div class="search-box-bar-container jus-heigh-search-b">
            <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
            <input type="text" id="service_search" class="form-control brawnz-search-bar-in" name="search"
              value="{{ request('search') }}"
              placeholder="{{ __('Name or category...') }}">
          </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
          @php $selectedCategory = request('category_id', 'all'); @endphp
          <label class="filter-label" for="category_id">{{ __('Category') }}</label>
          <div class="search-box-bar-container jus-heigh-search-b">
            <select id="category_id" name="category_id" class="form-control brawnz-search-bar-in">
              <option value="all" {{ $selectedCategory === 'all' ? 'selected' : '' }}>{{ __('All Categories') }}</option>
              @foreach (($parentCategories ?? []) as $parentCategory)
                <option value="{{ $parentCategory->category_id }}"
                  {{ (string) $selectedCategory === (string) $parentCategory->category_id ? 'selected' : '' }}>
                  {{ $parentCategory->name }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col-lg-2 col-md-6 mb-3">
          @php $selectedStatus = request('status', 'all'); @endphp
          <label class="filter-label" for="status">{{ __('Status') }}</label>
          <div class="search-box-bar-container jus-heigh-search-b">
            <select id="status" name="status" class="form-control brawnz-search-bar-in">
              <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>{{ __('All Status') }}</option>
              <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
              <option value="inactive" {{ $selectedStatus === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
            </select>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
          @php $selectedWebsite = request('show_on_website', 'all'); @endphp
          <label class="filter-label" for="show_on_website">{{ __('Website') }}</label>
          <div class="search-box-bar-container jus-heigh-search-b">
            <select id="show_on_website" name="show_on_website" class="form-control brawnz-search-bar-in">
              <option value="all" {{ $selectedWebsite === 'all' ? 'selected' : '' }}>{{ __('All Website') }}</option>
              <option value="yes" {{ $selectedWebsite === 'yes' ? 'selected' : '' }}>{{ __('Show on Website') }}</option>
              <option value="no" {{ $selectedWebsite === 'no' ? 'selected' : '' }}>{{ __('Hidden on Website') }}</option>
            </select>
          </div>
        </div>

        <div class="col-lg-12 mb-2">
          <div class="service-filter-actions">
            <button type="submit" class="btn btn-sm btn-primary btn_dark-x">
              <i class="fa fa-search"></i> {{ __('Search') }}
            </button>
            <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d" onclick="resetServiceFilters()">
              <i class="fa fa-refresh"></i> {{ __('Reset') }}
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
    function resetServiceFilters() {
        window.location.href = "{{ route('tab.master.service.list') }}";
    }
</script>
@endpush
