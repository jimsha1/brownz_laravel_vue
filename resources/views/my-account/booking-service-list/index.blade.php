@extends('layouts.vcapp')

@section('content')
<style>
  .my-appt-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .my-appt-filters .filter-control {
    height: 38px;
    border: none;
    outline: none;
    border-radius: 999px;
    background: #F0F0F3;
    padding: 8px 14px;
    font-size: 14px;
    color: #1D1D1D;
    width: 100%;
  }

  .my-appt-filters .filter-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  @media (min-width: 992px) {
    .my-appt-filters .filter-actions {
      justify-content: flex-end;
    }
  }

  .my-appt-list .client-name {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
  }

  .my-appt-list .client-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .my-appt-list .booking-code {
    font-weight: 600;
    color: #52A8D9;
  }

  .my-appt-list .booking-code.is-draft {
    color: #D0342C;
  }

  .my-appt-list .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
  }

  .my-appt-list .status-pill.is-upcoming {
    color: #0B6E99;
    background: rgba(82, 168, 217, 0.15);
  }

  .my-appt-list .status-pill.is-ongoing {
    color: #9A6700;
    background: rgba(230, 205, 127, 0.35);
  }

  .my-appt-list .status-pill.is-completed {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .my-appt-list .status-pill.is-expired {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  .my-appt-list .manage-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
  }

  .my-appt-list .manage-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 34px;
    height: 34px;
    border-radius: 999px;
    background: #F0F0F3;
    color: #1D1D1D;
    border: none;
    cursor: pointer;
    padding: 0 12px;
    font-size: 12px;
    font-weight: 600;
    gap: 6px;
  }

  .my-appt-list .manage-btn:hover {
    background: #1D1D1D;
    color: #E6CD7F;
  }

  .my-appt-list .manage-btn.is-finish {
    background: rgba(25, 153, 0, 0.12);
    color: #199900;
  }

  .my-appt-list .manage-btn.is-finish:hover {
    background: #199900;
    color: #fff;
  }

  .my-appt-list .empty-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }

  .my-appt-list .cell-clamp {
    max-width: 180px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
</style>

@php
  $filterStart = request('start_date', now()->startOfMonth()->toDateString());
  $filterEnd = request('end_date', now()->endOfMonth()->toDateString());
  $filterSearch = request('search', '');
@endphp

<div class="fbox-shadow mt-2 fd-card respo-margin-top my-appt-filters">
  <div class="card-body">
    <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
      {{ __('My Appointments') }}
    </h5>

    <form class="voco-ajax-form tab-search-form" method="get" action="{{ $tab_meta_data[$tab_name]['tab_url'] }}">
      <input type="hidden" id="tab_search" name="tab_search" value="0">

      <div class="row align-items-end br-row-gap-2">
        <div class="col-lg-2 col-md-6">
          <label class="filter-label" for="start_date">{{ __('Start Date') }}</label>
          <input type="date" id="start_date" class="form-control filter-control"
            name="start_date" value="{{ $filterStart }}">
        </div>

        <div class="col-lg-2 col-md-6">
          <label class="filter-label" for="end_date">{{ __('End Date') }}</label>
          <input type="date" id="end_date" class="form-control filter-control"
            name="end_date" value="{{ $filterEnd }}">
        </div>

        <div class="col-lg-4 col-md-6">
          <label class="filter-label" for="search">{{ __('Search') }}</label>
          <div class="search-box-bar-container jus-heigh-search-b">
            <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
            <input type="text" id="search" class="form-control brawnz-search-bar-in" name="search"
              value="{{ $filterSearch }}"
              placeholder="{{ __('Booking, client, service...') }}">
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <label class="filter-label d-none d-lg-block">&nbsp;</label>
          <div class="filter-actions">
            <button type="submit" class="btn btn-sm btn-primary btn_dark-x">
              <i class="fa fa-search"></i> {{ __('Search') }}
            </button>
            <a href="{{ route('tab.my.account.booking.service.list', ['tab_name' => $tab_name ?? 'upcoming']) }}"
              class="btn btn-sm btn-secondary brownz-btn-yellow-d">
              <i class="fa fa-refresh"></i> {{ __('Reset') }}
            </a>
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
@endpush
