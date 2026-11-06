@extends('layouts.vcapp')

@section('content')
    <style>
        .search-box-bar-container {
            display: flex;
            align-items: center;
            background-color: #FFFFFF;
            box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;
            border-radius: 26px;
            max-width: 300px;
            width: 100%;
            padding-left: 20px;
        }

        .search-box-bar-container p {
            margin: 0;
        }

        .search-box-bar-container p i {
            font-size: 16px;
            color: #D0342C;
        }

        .brawnz-search-bar-in {
            background-color: transparent;
            border: none;
            box-shadow: none;
            outline: none;
            padding: 10px 20px;
            padding-left: 10px;
            font-size: 14px;
            width: 100%;
        }

        .brawnz-search-bar-in:focus {
            background-color: transparent;
            border: none;
            box-shadow: none;
            outline: none;
            padding: 10px 20px;
            padding-left: 10px;
            font-size: 14px;
            width: 100%;
        }

        select.form-control:focus {
            box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px !important;
        }
    </style>
    <div class="fbox-shadow mt-5 fd-card respo-margin-top">
        <div class="card-body">

            <form class="voco-ajax-form tab-search-form" method="get" action="{{ $tab_meta_data[$tab_name]['tab_url'] }}">
                <input type="hidden" id="tab_search" name="tab_search" value="0">

                <div class="row align-items-center br-row-gap-2">
                    <div class="col-lg-3 col-sm-8">
                        <label for="start_date">Start Date</label>
                        <div class="search-box-bar-container jus-heigh-search-b" id="start_date_wrap">
                            <input type="date" id="start_date" class="form-control brawnz-search-bar-in"
                                name="start_date"
                                value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}">
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-8">
                        <label for="end_date">End Date</label>
                        <div class="search-box-bar-container jus-heigh-search-b" id="end_date_wrap">
                            <input type="date" id="end_date" class="form-control brawnz-search-bar-in" name="end_date"
                                value="{{ request('end_date', now()->endOfMonth()->toDateString()) }}">
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-8">
                        <label for="start_time">Slot</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <input type="time" id="slot_start" name="slot_start"
                                class="form-control brawnz-search-bar-in">
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-8">
                        <label for="search">Search</label>
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input type="text" id="search" class="form-control brawnz-search-bar-in" name="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('Search...') }}">
                        </div>
                        <p id="date-filter-note" class="mb-0 mt-1" style="display:none; font-size: 12px; color: #8a6d3b;">
                            {{ __('Dates are ignored while searching.') }}
                        </p>
                    </div>



                    <div class="col-lg-3 col-sm-8">
                        <label for="name">{{ __('Check In') }}</label>
                        <select name="check_availability"
                            class="search-box-bar-container jus-heigh-search-b form-control"style="height: 44px; border: none;"
                            aria-label="Default select example">
                            <option value="">All</option>
                            <option value="1" {{ request('check_availability') == '1' ? 'selected' : '' }}>Check In
                            </option>
                            <option value="0" {{ request('check_availability') == '0' ? 'selected' : '' }}>Not Check In
                            </option>
                        </select>
                    </div>

                    <div class="col-lg-3 col-sm-4 mt-4">
                        <button type="submit" class="btn btn-sm btn-primary btn_dark-x">
                            {{ __('Search') }}
                        </button>
                        <button type="button" id="refresh-btn"
                            class="btn btn-secondary btn-sm btn-icon-text brownz-btn-yellow-d mr-2">
                            <i class="fa fa-refresh" aria-hidden="true"></i> {{ __('Refresh') }}
                        </button>
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

                function syncDateFilterWithSearch() {
                    var hasSearch = $.trim($('#search').val()) !== '';
                    $('#start_date, #end_date').prop('disabled', hasSearch);
                    $('#start_date_wrap, #end_date_wrap').css('opacity', hasSearch ? '0.55' : '1');
                    $('#date-filter-note').toggle(hasSearch);
                }

                $('#search').on('input', syncDateFilterWithSearch);
                syncDateFilterWithSearch();

                // Refresh button click — reset filters to last one month
                $('#refresh-btn').on('click', function() {
                    $('#start_date, #end_date').prop('disabled', false);
                    $('#start_date').val('{{ now()->subMonth()->toDateString() }}');
                    $('#end_date').val('{{ now()->toDateString() }}');
                    $('#slot_start').val('');
                    $('#search').val('');
                    $('select[name="check_availability"]').val('');
                    syncDateFilterWithSearch();

                    $('.tab-search-form').submit();
                });
            });
        </script>
    @endif

@endpush
