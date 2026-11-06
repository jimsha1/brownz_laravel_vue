@extends('layouts.vcapp')

@php
    $defaultStart = now()->startOfMonth()->toDateString();
    $defaultEnd = now()->toDateString();
    $startDate = request('start_date', $defaultStart);
    $endDate = request('end_date', $defaultEnd);
@endphp

<style>
    .open-balance-filters .filters-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }

    .open-balance-filters .filters-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 4px;
        font-weight: 500;
    }

    .open-balance-filters .filters-hint {
        font-size: 12px;
        color: #6c757d;
        margin: 0;
    }

    .open-balance-filters .filter-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 6px;
    }

    .open-balance-filters .search-box-bar-container {
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

    .open-balance-filters .search-box-bar-container .fa-search {
        color: #9aa0a6;
        font-size: 14px;
        flex-shrink: 0;
    }

    .open-balance-filters .brawnz-search-bar-in {
        background-color: transparent;
        border: none;
        box-shadow: none;
        outline: none;
        padding: 10px 16px 10px 8px;
        font-size: 14px;
        width: 100%;
        height: 44px;
    }

    .open-balance-filters input[type="date"].brawnz-search-bar-in {
        padding-right: 12px;
        color-scheme: light;
    }

    .open-balance-filters input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: 0.7;
    }

    .open-balance-filters .filter-row {
        margin-left: -8px;
        margin-right: -8px;
    }

    .open-balance-filters .filter-row > [class*="col-"] {
        padding-left: 8px;
        padding-right: 8px;
    }

    .open-balance-filters .filter-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        min-height: 44px;
    }

    .open-balance-filters .filter-actions .btn {
        white-space: nowrap;
        min-width: 110px;
        justify-content: center;
    }

    .open-balance-filters .filter-actions-row {
        margin-top: 4px;
        padding-top: 4px;
    }

    /* Single "All" tab is redundant — keep the New button row */
    .open-balance-filters + .fbox-shadow .vc-ajax-nav-tabs > .brow-tab-item:first-child {
        display: none;
    }

    .open-balance-filters + .fbox-shadow .vc-ajax-nav-tabs {
        border-bottom: none;
        min-height: 42px;
    }
</style>

@section('content')
    <div class="fbox-shadow mt-2 fd-card respo-margin-top open-balance-filters">
        <div class="card-body">
            <div class="filters-header">
                <div>
                    <h5 class="filters-title">{{ __('Opening Balances') }}</h5>
                    <p class="filters-hint">{{ __('Filter by date or account name') }}</p>
                </div>
            </div>

            <form class="voco-ajax-form tab-search-form" method="get" action="{{ $tab_meta_data[$tab_name]['tab_url'] }}">
                <input type="hidden" id="tab_search" name="tab_search" value="0">

                <div class="row align-items-end filter-row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="filter-label" for="start_date">{{ __('Start Date') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <input type="date"
                                id="start_date"
                                class="form-control brawnz-search-bar-in"
                                name="start_date"
                                value="{{ $startDate }}">
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="filter-label" for="end_date">{{ __('End Date') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <input type="date"
                                id="end_date"
                                class="form-control brawnz-search-bar-in"
                                name="end_date"
                                value="{{ $endDate }}">
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-12 mb-3">
                        <label class="filter-label" for="open_balance_search">{{ __('Search') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input type="text"
                                id="open_balance_search"
                                class="form-control brawnz-search-bar-in"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('Search by account name...') }}">
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
    @if ($activate_via_js === true)
        <script>
            jQuery(function($) {
                $("#{{ 'vtab-' . $tab_name }}").tab("show");
            });
        </script>
    @endif
    <script>
        jQuery(function($) {
            $('#refresh-btn').on('click', function() {
                window.location.href = "{{ route('tab.stock.open.balance.list') }}";
            });
        });
    </script>
@endpush
