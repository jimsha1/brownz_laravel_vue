@extends('layouts.vcapp')

@section('content')
<style>
  .account-list-filters .filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
  }

  .account-list-filters .filters-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    margin-bottom: 0;
    font-weight: 500;
  }

  .account-list-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .account-list-filters .search-box-bar-container {
    display: flex;
    align-items: center;
    background-color: #FFFFFF;
    box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;
    border-radius: 26px;
    width: 100%;
    padding-left: 16px;
    min-height: 44px;
  }

  .account-list-filters .brawnz-search-bar-in {
    background-color: transparent;
    border: none;
    box-shadow: none;
    outline: none;
    padding: 10px 16px 10px 8px;
    font-size: 14px;
    width: 100%;
    height: 44px;
  }

  .account-list-filters select.brawnz-search-bar-in {
    padding-right: 28px;
    appearance: auto;
  }

  .account-list-filters .select2-container {
    width: 100% !important;
  }

  .account-list-filters .select2-container .select2-selection--single {
    height: 44px;
    border: none;
    background: transparent;
    display: flex;
    align-items: center;
  }

  .account-list-filters .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 44px;
    padding-left: 8px;
    color: #1D1D1D;
  }

  .account-list-filters .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 44px;
    right: 10px;
  }

  .account-list-filters .select2-container--default .select2-selection--single .select2-selection__clear {
    margin-right: 22px;
  }

  .account-list-filters .filter-actions {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    min-height: 44px;
    margin-top: 4px;
  }

  .account-list-filters .filter-actions .btn {
    white-space: nowrap;
    min-width: 110px;
  }

  .account-list-filters .filter-row {
    margin-left: -8px;
    margin-right: -8px;
  }

  .account-list-filters .filter-row > [class*="col-"] {
    padding-left: 8px;
    padding-right: 8px;
  }

  .account-list-filters .filter-actions-row {
    margin-top: 4px;
    padding-top: 4px;
  }
</style>

<div class="fbox-shadow mt-2 fd-card respo-margin-top account-list-filters">
  <div class="card-body">
    <div class="filters-header">
      <h5 class="filters-title">{{ __('Accounts') }}</h5>
    </div>

    <form class="voco-ajax-form tab-search-form" method="get" action="{{ $tab_meta_data[$tab_name]['tab_url'] }}">
      <input type="hidden" id="tab_search" name="tab_search" value="0">

      <div class="row align-items-end filter-row br-row-gap-2">
        <div class="col-lg-2 col-md-4 col-sm-6">
          <label class="filter-label" for="type">{{ __('Type') }}</label>
          <div class="search-box-bar-container pr-2">
            <select name="type" id="type" class="form-control account-filter-select" aria-label="{{ __('Type') }}"
              data-placeholder="{{ __('All Types') }}">
              <option value="">{{ __('All Types') }}</option>
              @isset($account_types)
                @foreach ($account_types as $account_type)
                  <option value="{{ $account_type }}"
                    {{ request('type') === $account_type ? 'selected' : '' }}>
                    {{ __(ucfirst($account_type)) }}
                  </option>
                @endforeach
              @endisset
            </select>
          </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
          <label class="filter-label" for="account_id">{{ __('Account') }}</label>
          <div class="search-box-bar-container pr-2">
            <select name="account_id" id="account_id" class="form-control account-filter-select" aria-label="{{ __('Account') }}"
              data-placeholder="{{ __('All Accounts') }}">
              <option value="">{{ __('All Accounts') }}</option>
              @isset($accounts)
                @foreach ($accounts as $account)
                  <option value="{{ $account->account_id }}"
                    {{ (string) request('account_id') === (string) $account->account_id ? 'selected' : '' }}>
                    {{ $account->name }}
                  </option>
                @endforeach
              @endisset
            </select>
          </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
          <label class="filter-label" for="code">{{ __('Code') }}</label>
          <div class="search-box-bar-container">
            <input type="text" id="code" class="form-control brawnz-search-bar-in" name="code"
              value="{{ request('code') }}" placeholder="{{ __('Code') }}">
          </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
          <label class="filter-label" for="category">{{ __('Category') }}</label>
          <div class="search-box-bar-container pr-2">
            <select name="category" id="category" class="form-control account-filter-select" aria-label="{{ __('Category') }}"
              data-placeholder="{{ __('All Categories') }}">
              <option value="">{{ __('All Categories') }}</option>
              @isset($account_categories)
                @foreach ($account_categories as $account_category)
                  <option value="{{ $account_category }}"
                    {{ request('category') === $account_category ? 'selected' : '' }}>
                    {{ $account_category }}
                  </option>
                @endforeach
              @endisset
            </select>
          </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
          <label class="filter-label" for="status">{{ __('Status') }}</label>
          <div class="search-box-bar-container pr-2">
            <select name="status" id="status" class="form-control account-filter-select" aria-label="{{ __('Status') }}"
              data-placeholder="{{ __('All Status') }}">
              <option value="">{{ __('All Status') }}</option>
              <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
              <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
            </select>
          </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
          <label class="filter-label" for="is_system_defined">{{ __('System Defined') }}</label>
          <div class="search-box-bar-container pr-2">
            <select name="is_system_defined" id="is_system_defined" class="form-control account-filter-select" aria-label="{{ __('System Defined') }}"
              data-placeholder="{{ __('All') }}">
              <option value="">{{ __('All') }}</option>
              <option value="1" {{ request('is_system_defined') === '1' ? 'selected' : '' }}>{{ __('System Defined') }}</option>
              <option value="0" {{ request('is_system_defined') === '0' ? 'selected' : '' }}>{{ __('Not System Defined') }}</option>
            </select>
          </div>
        </div>
      </div>

      <div class="row filter-row filter-actions-row">
        <div class="col-12">
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
@if($activate_via_js === true)
<script>
    jQuery(function ($) {
        $("#{{ 'vtab-' . $tab_name }}").tab("show");
    });
</script>
@endif
<script>
    jQuery(function ($) {
        $('.account-list-filters .account-filter-select').each(function () {
            window.vocoInitSelect2($(this), {
                placeholder: $(this).data('placeholder') || '',
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: 0
            });
        });

        $('#refresh-btn').on('click', function () {
            window.location.href = "{{ route('tab.accounts.account.list') }}";
        });
    });
</script>
@endpush
