@extends('layouts.vcapp')

@php
    $defaultStart = now()->startOfMonth()->toDateString();
    $defaultEnd = now()->toDateString();
    $startDate = request('start_date', $defaultStart);
    $endDate = request('end_date', $defaultEnd);
@endphp

<style>
    .expense-list-filters .filters-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
    }

    .expense-list-filters .filters-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .expense-list-filters .filters-hint {
        font-size: 12px;
        color: #6c757d;
    }

    .expense-list-filters .filter-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 6px;
    }

    .expense-list-filters .search-box-bar-container {
        display: flex;
        align-items: center;
        background-color: #FFFFFF;
        box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;
        border-radius: 26px;
        width: 100%;
        padding-left: 16px;
        min-height: 44px;
    }

    .expense-list-filters .brawnz-search-bar-in {
        background-color: transparent;
        border: none;
        box-shadow: none;
        outline: none;
        padding: 10px 16px 10px 8px;
        font-size: 14px;
        width: 100%;
        height: 44px;
    }

    .expense-list-filters select.brawnz-search-bar-in {
        padding-right: 28px;
        appearance: auto;
    }

    .expense-list-filters .expense-filter-actions {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 8px;
        min-height: 44px;
    }

    .expense-list-filters .expense-filter-actions .btn {
        white-space: nowrap;
    }
</style>

@section('content')
    <div class="fbox-shadow mt-2 fd-card respo-margin-top expense-list-filters">
        <div class="card-body">
            <div class="filters-header">
                <div>
                    <h5 class="filters-title">{{ __('Expenses') }}</h5>
                    <div class="filters-hint">{{ __('Filter expenses by date, account, type, or name') }}</div>
                </div>
            </div>

            <form class="voco-ajax-form tab-search-form" method="get" action="{{ $tab_meta_data[$tab_name]['tab_url'] }}">
                <input type="hidden" id="tab_search" name="tab_search" value="0">

                <div class="row align-items-end br-row-gap-2">
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

                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="filter-label" for="account_id">{{ __('Account') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <select name="account_id"
                                id="account_id"
                                class="form-control brawnz-search-bar-in"
                                aria-label="{{ __('Account') }}">
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

                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="filter-label" for="account_type">{{ __('Account Type') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <select name="account_type"
                                id="account_type"
                                class="form-control brawnz-search-bar-in"
                                aria-label="{{ __('Account Type') }}">
                                <option value="">{{ __('All Types') }}</option>
                                <option value="cash" {{ request('account_type') === 'cash' ? 'selected' : '' }}>
                                    {{ __('Cash') }}
                                </option>
                                <option value="bank" {{ request('account_type') === 'bank' ? 'selected' : '' }}>
                                    {{ __('Bank') }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-8 mb-3">
                        <label class="filter-label" for="expense_search">{{ __('Search') }}</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <p class="m-0"><i class="fa fa-search" aria-hidden="true"></i></p>
                            <input type="text"
                                id="expense_search"
                                class="form-control brawnz-search-bar-in"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('Search by account name...') }}">
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-4 mb-3">
                        <label class="filter-label d-none d-md-block">&nbsp;</label>
                        <div class="expense-filter-actions">
                            <button type="submit" class="btn btn-sm btn-primary btn_dark-x">
                                <i class="fa fa-search"></i> {{ __('Search') }}
                            </button>
                            <button type="button"
                                id="refresh-btn"
                                class="btn btn-sm btn-secondary brownz-btn-yellow-d">
                                <i class="fa fa-refresh" aria-hidden="true"></i> {{ __('Reset') }}
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
                window.location.href = "{{ route('tab.expenses.expense.list') }}";
            });
        });
    </script>
@endpush
