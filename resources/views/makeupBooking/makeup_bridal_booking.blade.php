@extends('layouts.vcapp')

@section('content')
<style>
  .makeup-booking-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .makeup-booking-filters .filter-control {
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

  .makeup-booking-filters select.filter-control {
    appearance: auto;
  }

  .makeup-booking-filters .filter-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  @media (min-width: 992px) {
    .makeup-booking-filters .filter-actions {
      justify-content: flex-end;
    }
  }

  .makeup-booking-list .list-toolbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 14px;
  }

  .makeup-booking-list .list-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0;
  }

  .makeup-booking-list .client-name {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
  }

  .makeup-booking-list .client-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .makeup-booking-list .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
  }

  .makeup-booking-list .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .makeup-booking-list .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  .makeup-booking-list .manage-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
  }

  .makeup-booking-list .manage-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #F0F0F3;
    color: #1D1D1D;
    border: none;
    cursor: pointer;
  }

  .makeup-booking-list .manage-btn:hover {
    background: #1D1D1D;
    color: #E6CD7F;
  }

  .makeup-booking-list .empty-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }

  .makeup-booking-list .cell-clamp {
    max-width: 180px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .makeup-booking-modal .edit-section {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 14px 14px 4px;
    margin-bottom: 12px;
  }

  .makeup-booking-modal .edit-section-title {
    font-size: 13px;
    font-weight: 600;
    color: #1D1D1D;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  .makeup-booking-modal .edit-hint {
    font-size: 12px;
    color: #6c757d;
    margin: -2px 0 12px;
  }

  .makeup-booking-modal .form-control,
  .makeup-booking-modal .form-select-border-width {
    border-radius: 999px;
    background: #fff;
    border: none;
    height: 42px;
    padding: 8px 16px;
  }

  .makeup-booking-modal textarea.form-control {
    border-radius: 16px;
    height: auto;
    min-height: 72px;
  }

  .makeup-booking-modal .modal-dialog {
    max-width: 960px;
    width: calc(100% - 1.5rem);
    margin: 1.25rem auto !important;
    top: 0 !important;
    transform: none !important;
  }

  .makeup-booking-modal .modal-content {
    max-height: calc(100vh - 2.5rem);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .makeup-booking-modal .modal-header {
    flex: 0 0 auto;
  }

  .makeup-booking-modal .makeup-booking-modal-form {
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
    min-height: 0;
    overflow: hidden;
  }

  .makeup-booking-modal .modal-body {
    overflow-y: auto !important;
    flex: 1 1 auto;
    min-height: 0;
    padding-bottom: 8px;
    -webkit-overflow-scrolling: touch;
  }

  .makeup-booking-modal .modal-footer {
    border-top: 1px solid #EEE;
    gap: 8px;
    flex: 0 0 auto;
    background: #fff;
  }

  .makeup-booking-modal .service-picker {
    background: #fff;
    border-radius: 16px;
    padding: 10px;
  }

  .makeup-booking-modal .service-picker-toolbar {
    display: flex;
    gap: 8px;
    margin-bottom: 8px;
  }

  .makeup-booking-modal .service-picker-search {
    flex: 1;
    height: 36px !important;
    max-width: 100% !important;
    border-radius: 999px !important;
    background: #F0F0F3 !important;
    border: none !important;
    padding: 8px 14px !important;
  }

  .makeup-booking-modal .service-picker-list {
    max-height: 150px;
    overflow-y: auto;
    display: grid;
    grid-template-columns: 1fr;
    gap: 6px;
  }

  @media (min-width: 768px) {
    .makeup-booking-modal .picker-row .service-picker-list {
      max-height: 180px;
    }
  }

  .makeup-booking-modal .service-picker-item {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    padding: 8px 10px;
    border-radius: 10px;
    background: #F7F7F9;
    cursor: pointer;
    font-weight: 500;
    color: #1D1D1D;
    line-height: 1.3;
  }

  .makeup-booking-modal .service-picker-item:hover {
    background: #F0F0F3;
  }

  .makeup-booking-modal .service-picker-item.is-checked {
    background: rgba(230, 205, 127, 0.35);
  }

  /* Override global modal input styles that break checkboxes */
  .makeup-booking-modal .service-picker-item input[type="checkbox"],
  .makeup-booking-modal.brownz-custom-popup .modal-body .service-picker-item input[type="checkbox"] {
    margin: 0 !important;
    width: 16px !important;
    height: 16px !important;
    min-width: 16px !important;
    max-width: 16px !important;
    padding: 0 !important;
    border-radius: 4px !important;
    flex: 0 0 16px !important;
    background: #fff !important;
    accent-color: #1D1D1D;
    appearance: auto !important;
    -webkit-appearance: checkbox !important;
  }

  .makeup-booking-modal .service-picker-name {
    flex: 1;
    min-width: 0;
    word-break: break-word;
  }

  .makeup-booking-modal .service-picker-meta {
    display: block;
    font-size: 11px;
    color: #6c757d;
    font-weight: 500;
    margin-top: 2px;
  }

  .makeup-booking-modal .service-picker-empty,
  .makeup-booking-modal .service-picker-count {
    font-size: 12px;
    color: #6c757d;
    margin: 8px 0 0;
  }

  .makeup-booking-modal .service-picker-empty {
    display: none;
    padding: 12px 4px;
  }
</style>

@php
  $filterSearch = $filter_search ?? request('search', '');
  $filterStatus = $filter_status ?? request('status', 'all');
  $filterDate = $filter_date ?? request('date', '');
  $filterBeautician = $filter_beautician ?? request('beautician', '');
  $serviceMap = $serviceMap ?? collect();
  $bookingEditMap = [];
@endphp

<div class="fbox-shadow mt-2 fd-card respo-margin-top makeup-booking-filters">
  <div class="card-body">
    <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
      {{ __('Makeup Booking') }}
    </h5>

    <form method="GET" action="{{ route('makeup.booking.bridal') }}">
      <div class="row align-items-end br-row-gap-2">
        <div class="col-lg-3 col-md-6">
          <label class="filter-label" for="search">{{ __('Search') }}</label>
          <div class="search-box-bar-container jus-heigh-search-b">
            <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
            <input type="text" class="form-control brawnz-search-bar-in" name="search" id="search"
              value="{{ $filterSearch }}"
              placeholder="{{ __('Name, contact, place...') }}">
          </div>
        </div>

        <div class="col-lg-2 col-md-6">
          <label class="filter-label" for="date_filter">{{ __('Booked Date') }}</label>
          <input type="date" name="date" id="date_filter" class="form-control filter-control"
            value="{{ $filterDate }}">
        </div>

        <div class="col-lg-2 col-md-6">
          <label class="filter-label" for="status_filter">{{ __('Status') }}</label>
          <select name="status" id="status_filter" class="form-control filter-control">
            <option value="all" {{ $filterStatus === 'all' ? 'selected' : '' }}>{{ __('All Status') }}</option>
            <option value="active" {{ $filterStatus === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
            <option value="inactive" {{ $filterStatus === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
          </select>
        </div>

        <div class="col-lg-2 col-md-6">
          <label class="filter-label" for="beautician_filter">{{ __('Beautician') }}</label>
          <select name="beautician" id="beautician_filter" class="form-control filter-control">
            <option value="">{{ __('All Beauticians') }}</option>
            @foreach ($beauticians as $beautician)
              <option value="{{ $beautician->name }}" {{ $filterBeautician === $beautician->name ? 'selected' : '' }}>
                {{ $beautician->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-lg-3 col-md-12">
          <label class="filter-label d-none d-lg-block">&nbsp;</label>
          <div class="filter-actions">
            <button type="submit" class="btn btn-sm btn-primary btn_dark-x">
              <i class="fa fa-search"></i> {{ __('Search') }}
            </button>
            <a href="{{ route('makeup.booking.bridal') }}" class="btn btn-sm btn-secondary brownz-btn-yellow-d">
              <i class="fa fa-refresh"></i> {{ __('Reset') }}
            </a>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="fbox-shadow mt-2 fd-card makeup-booking-list">
  <div class="card-body">
    <div class="list-toolbar">
      <div>
        <h5 class="list-title">{{ __('Booking List') }}</h5>
      </div>
      <button type="button" class="btn btn-primary btn_dark-x" data-toggle="modal" data-target="#createBookingModal">
        <i class="fa fa-plus"></i> {{ __('New Booking') }}
      </button>
    </div>

    @if ($makeupBookings->count())
      <div class="table-responsive">
        <table class="table brownz-table-list-v1 m-0">
          <thead>
            <tr>
              <th style="width: 56px;">{{ __('SL#') }}</th>
              <th>{{ __('Client') }}</th>
              <th>{{ __('Contact') }}</th>
              <th>{{ __('Booked Date') }}</th>
              <th>{{ __('Services') }}</th>
              <th>{{ __('Time') }}</th>
              <th>{{ __('Beautician') }}</th>
              <th>{{ __('Status') }}</th>
              <th class="text-center" style="width: 90px;">{{ __('Action') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($makeupBookings as $key => $booking)
              @php
                $serviceIds = is_array($booking->makeup_service_name)
                  ? array_values(array_filter($booking->makeup_service_name, fn ($id) => $id !== null && $id !== ''))
                  : [];
                $serviceNames = collect($serviceIds)
                  ->map(fn ($id) => $serviceMap[$id] ?? null)
                  ->filter()
                  ->implode(', ');
                $bookedDateRaw = $booking->makeup_date_booked
                  ? \Carbon\Carbon::parse($booking->makeup_date_booked)->format('Y-m-d')
                  : '';
                $bookedDateLabel = $bookedDateRaw
                  ? \Carbon\Carbon::parse($bookedDateRaw)->format('d-m-Y')
                  : 'â€”';
                $beauticianNames = is_array($booking->beautician_name)
                  ? array_values(array_filter($booking->beautician_name, fn ($name) => trim((string) $name) !== ''))
                  : [];
                $beauticianLabel = collect($beauticianNames)->implode(', ');
                $bookingEditMap[$booking->id] = [
                  'id' => $booking->id,
                  'client_name' => (string) $booking->makeup_client_name,
                  'contact' => (string) $booking->makeup_contact_number,
                  'email' => (string) $booking->makeup_client_email,
                  'place' => (string) $booking->makeup_place,
                  'date' => $bookedDateRaw,
                  'time' => (string) $booking->makeup_time_slot,
                  'address' => (string) $booking->makeup_clent_address,
                  'services' => array_map('strval', $serviceIds),
                  'beauticians' => array_values($beauticianNames),
                  'status' => (string) $booking->status,
                ];
              @endphp
              <tr>
                <td>{{ $key + 1 }}</td>
                <td>
                  <span class="client-name">{{ $booking->makeup_client_name }}</span>
                  <span class="client-meta">
                    {{ $booking->makeup_place ?: 'â€”' }}
                    @if ($booking->makeup_client_email)
                      Â· {{ $booking->makeup_client_email }}
                    @endif
                  </span>
                </td>
                <td>{{ $booking->makeup_contact_number }}</td>
                <td>{{ $bookedDateLabel }}</td>
                <td>
                  <div class="cell-clamp" title="{{ $serviceNames }}">{{ $serviceNames ?: 'â€”' }}</div>
                </td>
                <td>{{ $booking->makeup_time_slot ?: 'â€”' }}</td>
                <td>
                  <div class="cell-clamp" title="{{ $beauticianLabel }}">{{ $beauticianLabel ?: 'â€”' }}</div>
                </td>
                <td>
                  @if ($booking->status === 'active')
                    <span class="status-pill is-active">{{ __('Active') }}</span>
                  @else
                    <span class="status-pill is-inactive">{{ __('Inactive') }}</span>
                  @endif
                </td>
                <td class="text-center">
                  <div class="manage-actions">
                    <button type="button"
                      class="manage-btn edit-btn"
                      title="{{ __('Edit') }}"
                      data-toggle="modal"
                      data-target="#editBookingModal"
                      data-id="{{ $booking->id }}">
                      <i class="fa fa-pencil" aria-hidden="true"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="empty-state">
        <div><i class="fa fa-calendar" aria-hidden="true"></i></div>
        <div><strong>{{ __('No makeup bookings found') }}</strong></div>
        <div>{{ __('Try adjusting filters, or create a new booking.') }}</div>
      </div>
    @endif
  </div>
</div>

{{-- Create Modal --}}
<div class="modal fade brownz-custom-popup makeup-booking-modal" id="createBookingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content" style="border-radius: 16px; border: none;">
      <div class="modal-header border-0">
        <h5 class="modal-title" style="color:#1D1D1D;font-weight:600;">{{ __('New Makeup Booking') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('makeup.booking.bridal.store') }}" method="POST" id="createBookingForm" class="makeup-booking-modal-form">
        @csrf
        <div class="modal-body pt-0">
          <div class="edit-section">
            <p class="edit-section-title">{{ __('Client Details') }}</p>
            <p class="edit-hint">{{ __('Enter client contact and location details.') }}</p>
            <div class="row">
              <div class="form-group col-md-4">
                <label for="create_client_name">{{ __('Client Name') }}</label>
                <input type="text" id="create_client_name" class="form-control" name="client_name"
                  placeholder="{{ __('Client name') }}" required>
              </div>
              <div class="form-group col-md-4">
                <label for="create_contact_number">{{ __('Contact Number') }}</label>
                <input type="text" id="create_contact_number" class="form-control" name="contact_number"
                  placeholder="{{ __('Contact number') }}" required>
              </div>
              <div class="form-group col-md-4">
                <label for="create_place">{{ __('Place') }}</label>
                <input type="text" id="create_place" class="form-control" name="place"
                  placeholder="{{ __('Place') }}" required>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12">
                <label for="create_email">{{ __('Email') }}</label>
                <input type="email" id="create_email" class="form-control" name="email"
                  placeholder="{{ __('Email (optional)') }}">
              </div>
            </div>
          </div>

          <div class="edit-section">
            <p class="edit-section-title">{{ __('Booking Details') }}</p>
            <p class="edit-hint">{{ __('Set date, time, services, beautician, and status.') }}</p>
            <div class="row">
              <div class="form-group col-md-4">
                <label for="create_date">{{ __('Date') }}</label>
                <input type="date" id="create_date" class="form-control" name="date" required>
              </div>
              <div class="form-group col-md-4">
                <label for="create_time_slot">{{ __('Time Slot') }}</label>
                <input type="time" id="create_time_slot" class="form-control" name="time_slot" required>
              </div>
              <div class="form-group col-md-4">
                <label for="create_status">{{ __('Status') }}</label>
                <select id="create_status" class="form-control" name="status" required>
                  <option value="active" selected>{{ __('Active') }}</option>
                  <option value="inactive">{{ __('Inactive') }}</option>
                </select>
              </div>
            </div>
            <div class="row picker-row">
              <div class="form-group col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <label class="mb-0">{{ __('Beauticians') }}</label>
                  <span class="service-picker-count mb-0" data-service-count-for="#create_beautician_picker">
                    {{ __('0 beauticians selected') }}
                  </span>
                </div>
                <div class="service-picker" id="create_beautician_picker">
                  <div class="service-picker-toolbar">
                    <input type="text" class="form-control service-picker-search"
                      placeholder="{{ __('Search beauticians...') }}" autocomplete="off">
                  </div>
                  <div class="service-picker-list">
                    @foreach ($beauticians as $beautician)
                      <label class="service-picker-item" data-service-name="{{ strtolower($beautician->name) }}">
                        <input type="checkbox" name="beauticians[]" value="{{ $beautician->name }}">
                        <span class="service-picker-name">{{ $beautician->name }}</span>
                      </label>
                    @endforeach
                  </div>
                  <div class="service-picker-empty">{{ __('No beauticians match your search.') }}</div>
                </div>
              </div>
              <div class="form-group col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <label class="mb-0">{{ __('Services') }}</label>
                  <span class="service-picker-count mb-0" data-service-count-for="#create_service_picker">
                    {{ __('0 services selected') }}
                  </span>
                </div>
                <div class="service-picker" id="create_service_picker">
                  <div class="service-picker-toolbar">
                    <input type="text" class="form-control service-picker-search"
                      placeholder="{{ __('Search services...') }}" autocomplete="off">
                  </div>
                  <div class="service-picker-list">
                    @foreach ($services as $service)
                      @if ($service->status === 'active')
                        <label class="service-picker-item" data-service-name="{{ strtolower($service->service_name) }}">
                          <input type="checkbox" name="services[]" value="{{ $service->id }}">
                          <span class="service-picker-name">{{ $service->service_name }}</span>
                        </label>
                      @endif
                    @endforeach
                  </div>
                  <div class="service-picker-empty">{{ __('No services match your search.') }}</div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="create_address">{{ __('Address') }}</label>
              <textarea class="form-control" id="create_address" rows="2" name="address"
                placeholder="{{ __('Address') }}" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn brownz-btn-yellow-d" data-dismiss="modal">
            <i class="fa fa-undo"></i> {{ __('Cancel') }}
          </button>
          <button type="submit" class="btn btn_dark-x">
            <i class="fa fa-check"></i> {{ __('Book') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade brownz-custom-popup makeup-booking-modal" id="editBookingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content" style="border-radius: 16px; border: none;">
      <div class="modal-header border-0">
        <h5 class="modal-title" style="color:#1D1D1D;font-weight:600;">{{ __('Edit Makeup Booking') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('makeup.booking.bridal.update') }}" method="POST" id="editBookingForm" class="makeup-booking-modal-form">
        @csrf
        <input type="hidden" name="id" id="edit_booking_id" value="">
        <div class="modal-body pt-0">
          <div class="edit-section">
            <p class="edit-section-title">{{ __('Client Details') }}</p>
            <p class="edit-hint">{{ __('Update client contact and location details.') }}</p>
            <div class="row">
              <div class="form-group col-md-4">
                <label for="edit_client_name">{{ __('Client Name') }}</label>
                <input type="text" id="edit_client_name" class="form-control" name="client_name"
                  placeholder="{{ __('Client name') }}" required>
              </div>
              <div class="form-group col-md-4">
                <label for="edit_contact_number">{{ __('Contact Number') }}</label>
                <input type="text" id="edit_contact_number" class="form-control" name="contact_number"
                  placeholder="{{ __('Contact number') }}" required>
              </div>
              <div class="form-group col-md-4">
                <label for="edit_place">{{ __('Place') }}</label>
                <input type="text" id="edit_place" class="form-control" name="place"
                  placeholder="{{ __('Place') }}" required>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12">
                <label for="edit_email">{{ __('Email') }}</label>
                <input type="email" id="edit_email" class="form-control" name="email"
                  placeholder="{{ __('Email (optional)') }}">
              </div>
            </div>
          </div>

          <div class="edit-section">
            <p class="edit-section-title">{{ __('Booking Details') }}</p>
            <p class="edit-hint">{{ __('Update date, time, services, beautician, and status.') }}</p>
            <div class="row">
              <div class="form-group col-md-4">
                <label for="edit_date">{{ __('Date') }}</label>
                <input type="date" id="edit_date" class="form-control" name="date" required>
              </div>
              <div class="form-group col-md-4">
                <label for="edit_time_slot">{{ __('Time Slot') }}</label>
                <input type="time" id="edit_time_slot" class="form-control" name="time_slot" required>
              </div>
              <div class="form-group col-md-4">
                <label for="edit_status">{{ __('Status') }}</label>
                <select id="edit_status" class="form-control" name="status" required>
                  <option value="active">{{ __('Active') }}</option>
                  <option value="inactive">{{ __('Inactive') }}</option>
                </select>
              </div>
            </div>
            <div class="row picker-row">
              <div class="form-group col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <label class="mb-0">{{ __('Beauticians') }}</label>
                  <span class="service-picker-count mb-0" data-service-count-for="#edit_beautician_picker">
                    {{ __('0 beauticians selected') }}
                  </span>
                </div>
                <div class="service-picker" id="edit_beautician_picker">
                  <div class="service-picker-toolbar">
                    <input type="text" class="form-control service-picker-search"
                      placeholder="{{ __('Search beauticians...') }}" autocomplete="off">
                  </div>
                  <div class="service-picker-list">
                    @foreach ($beauticians as $beautician)
                      <label class="service-picker-item" data-service-name="{{ strtolower($beautician->name) }}">
                        <input type="checkbox" name="beauticians[]" value="{{ $beautician->name }}">
                        <span class="service-picker-name">{{ $beautician->name }}</span>
                      </label>
                    @endforeach
                  </div>
                  <div class="service-picker-empty">{{ __('No beauticians match your search.') }}</div>
                </div>
              </div>
              <div class="form-group col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <label class="mb-0">{{ __('Services') }}</label>
                  <span class="service-picker-count mb-0" data-service-count-for="#edit_service_picker">
                    {{ __('0 services selected') }}
                  </span>
                </div>
                <div class="service-picker" id="edit_service_picker">
                  <div class="service-picker-toolbar">
                    <input type="text" class="form-control service-picker-search"
                      placeholder="{{ __('Search services...') }}" autocomplete="off">
                  </div>
                  <div class="service-picker-list">
                    @foreach ($services as $service)
                      <label class="service-picker-item" data-service-name="{{ strtolower($service->service_name) }}">
                        <input type="checkbox" name="services[]" value="{{ $service->id }}">
                        <span class="service-picker-name">
                          {{ $service->service_name }}
                          @if ($service->status === 'inactive')
                            <span class="service-picker-meta">{{ __('Inactive') }}</span>
                          @endif
                        </span>
                      </label>
                    @endforeach
                  </div>
                  <div class="service-picker-empty">{{ __('No services match your search.') }}</div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="edit_address">{{ __('Address') }}</label>
              <textarea class="form-control" id="edit_address" rows="2" name="address"
                placeholder="{{ __('Address') }}" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn brownz-btn-yellow-d" data-dismiss="modal">
            <i class="fa fa-undo"></i> {{ __('Cancel') }}
          </button>
          <button type="submit" class="btn btn_dark-x">
            <i class="fa fa-check"></i> {{ __('Update') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  const bookingEditMap = @json($bookingEditMap);

  function convertTo24Hour(time12h) {
    if (!time12h) {
      return '';
    }

    var value = String(time12h).trim();
    if (!value) {
      return '';
    }

    if (/^\d{1,2}:\d{2}(:\d{2})?$/.test(value) && !/AM|PM/i.test(value)) {
      var rawParts = value.split(':');
      return String(rawParts[0]).padStart(2, '0') + ':' + String(rawParts[1]).padStart(2, '0');
    }

    var match = value.match(/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i);
    if (!match) {
      return '';
    }

    var hours = parseInt(match[1], 10);
    var minutes = match[2];
    var modifier = match[3].toUpperCase();

    if (modifier === 'PM' && hours !== 12) {
      hours += 12;
    }
    if (modifier === 'AM' && hours === 12) {
      hours = 0;
    }

    return String(hours).padStart(2, '0') + ':' + minutes;
  }

  function normalizePickerValue(value) {
    return String(value || '').replace(/\s+/g, ' ').trim().toLowerCase();
  }

  function updateServicePickerCount($picker) {
    var count = $picker.find('input[type="checkbox"]:checked').length;
    var isBeautician = String($picker.attr('id') || '').indexOf('beautician') !== -1;
    var label;

    if (isBeautician) {
      label = count === 1
        ? '{{ __(':count beautician selected') }}'.replace(':count', count)
        : '{{ __(':count beauticians selected') }}'.replace(':count', count);
    } else {
      label = count === 1
        ? '{{ __(':count service selected') }}'.replace(':count', count)
        : '{{ __(':count services selected') }}'.replace(':count', count);
    }

    $('[data-service-count-for="#' + $picker.attr('id') + '"]').text(label);
  }

  function setServicePickerValues($picker, selectedIds) {
    var selected = (selectedIds || []).map(String).map(function (v) { return v.trim(); }).filter(Boolean);
    var selectedNormalized = selected.map(normalizePickerValue);
    var checkboxName = $picker.find('input[type="checkbox"]').first().attr('name')
      || (String($picker.attr('id') || '').indexOf('beautician') !== -1 ? 'beauticians[]' : 'services[]');
    var $list = $picker.find('.service-picker-list');

    $picker.find('.service-picker-item.is-dynamic').remove();

    $picker.find('input[type="checkbox"]').each(function () {
      var checked = selectedNormalized.indexOf(normalizePickerValue(this.value)) !== -1;
      this.checked = checked;
      $(this).closest('.service-picker-item').toggleClass('is-checked', checked);
    });

    selected.forEach(function (value) {
      var valueNorm = normalizePickerValue(value);
      var matched = false;
      $picker.find('input[type="checkbox"]').each(function () {
        if (normalizePickerValue(this.value) === valueNorm) {
          matched = true;
          return false;
        }
      });
      if (!matched) {
        var $item = $('<label class="service-picker-item is-checked is-dynamic"></label>')
          .attr('data-service-name', valueNorm);
        $item.append(
          $('<input type="checkbox">').attr('name', checkboxName).val(value).prop('checked', true)
        );
        $item.append($('<span class="service-picker-name"></span>').text(value));
        $list.prepend($item);
      }
    });

    $picker.find('.service-picker-item.is-checked').prependTo($list);
    $picker.find('.service-picker-search').val('');
    $picker.find('.service-picker-item').show();
    $picker.find('.service-picker-empty').hide();
    updateServicePickerCount($picker);
    $list.scrollTop(0);
  }

  function fillEditBookingForm(bookingId) {
    var data = bookingEditMap[String(bookingId)] || bookingEditMap[bookingId];
    if (!data) {
      return;
    }

    $('#edit_booking_id').val(data.id || '');
    $('#edit_client_name').val(data.client_name || '');
    $('#edit_contact_number').val(data.contact || '');
    $('#edit_email').val(data.email || '');
    $('#edit_place').val(data.place || '');
    $('#edit_date').val(data.date || '');
    $('#edit_time_slot').val(convertTo24Hour(data.time));
    $('#edit_address').val(data.address || '');
    $('#edit_status').val(data.status || 'active');

    setServicePickerValues($('#edit_beautician_picker'), data.beauticians || []);
    setServicePickerValues($('#edit_service_picker'), data.services || []);
  }

  jQuery(function ($) {
    function bindServicePicker($picker) {
      $picker.on('change', 'input[type="checkbox"]', function () {
        $(this).closest('.service-picker-item').toggleClass('is-checked', this.checked);
        updateServicePickerCount($picker);
      });

      $picker.on('input', '.service-picker-search', function () {
        var term = String($(this).val() || '').toLowerCase().trim();
        var visible = 0;
        $picker.find('.service-picker-item').each(function () {
          var name = String($(this).attr('data-service-name') || '');
          var show = !term || name.indexOf(term) !== -1;
          $(this).toggle(show);
          if (show) {
            visible++;
          }
        });
        $picker.find('.service-picker-empty').toggle(visible === 0);
      });

      updateServicePickerCount($picker);
    }

    bindServicePicker($('#create_service_picker'));
    bindServicePicker($('#edit_service_picker'));
    bindServicePicker($('#create_beautician_picker'));
    bindServicePicker($('#edit_beautician_picker'));

    $('#createBookingForm').on('submit', function (e) {
      if (!$('#create_beautician_picker input[type="checkbox"]:checked').length) {
        e.preventDefault();
        alert('{{ __('Please select at least one beautician.') }}');
        return;
      }
      if (!$('#create_service_picker input[type="checkbox"]:checked').length) {
        e.preventDefault();
        alert('{{ __('Please select at least one service.') }}');
      }
    });

    $('#editBookingForm').on('submit', function (e) {
      if (!$('#edit_beautician_picker input[type="checkbox"]:checked').length) {
        e.preventDefault();
        alert('{{ __('Please select at least one beautician.') }}');
        return;
      }
      if (!$('#edit_service_picker input[type="checkbox"]:checked').length) {
        e.preventDefault();
        alert('{{ __('Please select at least one service.') }}');
      }
    });

    $('#createBookingModal').on('shown.bs.modal', function () {
      $(this).find('.modal-body').scrollTop(0);
      $('#create_status').val('active');
      setServicePickerValues($('#create_beautician_picker'), []);
      setServicePickerValues($('#create_service_picker'), []);
    });

    $('#createBookingModal').on('hidden.bs.modal', function () {
      document.getElementById('createBookingForm').reset();
      $('#create_status').val('active');
      setServicePickerValues($('#create_beautician_picker'), []);
      setServicePickerValues($('#create_service_picker'), []);
    });

    $('#editBookingModal').on('hidden.bs.modal', function () {
      document.getElementById('editBookingForm').reset();
      setServicePickerValues($('#edit_beautician_picker'), []);
      setServicePickerValues($('#edit_service_picker'), []);
    });

    $(document).on('click', '.edit-btn', function () {
      fillEditBookingForm($(this).attr('data-id'));
    });

    $('#editBookingModal').on('shown.bs.modal', function () {
      $(this).find('.modal-body').scrollTop(0);
      fillEditBookingForm($('#edit_booking_id').val());
    });
  });
</script>
@endsection
