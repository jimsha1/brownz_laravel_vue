@extends('layouts.vcapp')

@section('content')
<style>
  .makeup-service-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .makeup-service-filters .filter-control {
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

  .makeup-service-filters select.filter-control {
    appearance: auto;
  }

  .makeup-service-filters .filter-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  @media (min-width: 992px) {
    .makeup-service-filters .filter-actions {
      justify-content: flex-end;
    }
  }

  .makeup-service-list .list-toolbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 14px;
  }

  .makeup-service-list .list-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0;
  }

  .makeup-service-list .service-name {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
  }

  .makeup-service-list .service-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .makeup-service-list .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
  }

  .makeup-service-list .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .makeup-service-list .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  .makeup-service-list .manage-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
  }

  .makeup-service-list .manage-btn {
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

  .makeup-service-list .manage-btn:hover {
    background: #1D1D1D;
    color: #E6CD7F;
  }

  .makeup-service-list .empty-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }

  .makeup-service-modal .edit-section {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 16px 16px 6px;
  }

  .makeup-service-modal .edit-section-title {
    font-size: 13px;
    font-weight: 600;
    color: #1D1D1D;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  .makeup-service-modal .edit-hint {
    font-size: 12px;
    color: #6c757d;
    margin: -4px 0 14px;
  }

  .makeup-service-modal .form-control,
  .makeup-service-modal .form-select-border-width {
    border-radius: 999px;
    background: #fff;
    border: none;
    height: 42px;
    padding: 8px 16px;
  }

  .makeup-service-modal .modal-footer {
    border-top: none;
    gap: 8px;
  }
</style>

@php
  $filterSearch = $filter_search ?? request('search', '');
  $filterStatus = $filter_status ?? request('status', 'all');
@endphp

<div class="fbox-shadow mt-2 fd-card respo-margin-top makeup-service-filters">
  <div class="card-body">
    <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
      {{ __('Makeup Services') }}
    </h5>

    <form method="GET" action="{{ route('makeup.booking.service') }}">
      <div class="row align-items-end br-row-gap-2">
        <div class="col-lg-5 col-md-6">
          <label class="filter-label" for="search">{{ __('Search') }}</label>
          <div class="search-box-bar-container jus-heigh-search-b">
            <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
            <input type="text" class="form-control brawnz-search-bar-in" name="search" id="search"
              value="{{ $filterSearch }}"
              placeholder="{{ __('Search by name or price...') }}">
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <label class="filter-label" for="status_filter">{{ __('Status') }}</label>
          <select name="status" id="status_filter" class="form-control filter-control">
            <option value="all" {{ $filterStatus === 'all' ? 'selected' : '' }}>{{ __('All Status') }}</option>
            <option value="active" {{ $filterStatus === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
            <option value="inactive" {{ $filterStatus === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
          </select>
        </div>

        <div class="col-lg-4 col-md-12">
          <label class="filter-label d-none d-lg-block">&nbsp;</label>
          <div class="filter-actions">
            <button type="submit" class="btn btn-sm btn-primary btn_dark-x">
              <i class="fa fa-search"></i> {{ __('Search') }}
            </button>
            <a href="{{ route('makeup.booking.service') }}" class="btn btn-sm btn-secondary brownz-btn-yellow-d">
              <i class="fa fa-refresh"></i> {{ __('Reset') }}
            </a>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="fbox-shadow mt-2 fd-card makeup-service-list">
  <div class="card-body">
    <div class="list-toolbar">
      <div>
        <h5 class="list-title">{{ __('Service List') }}</h5>
      </div>
      <button type="button" class="btn btn-primary btn_dark-x" data-toggle="modal" data-target="#createServiceModal">
        <i class="fa fa-plus"></i> {{ __('New') }}
      </button>
    </div>

    @if ($makeupServices->count())
      <div class="table-responsive">
        <table class="table brownz-table-list-v1 m-0">
          <thead>
            <tr>
              <th style="width: 56px;">{{ __('SL#') }}</th>
              <th>{{ __('Service') }}</th>
              <th>{{ __('Base Price') }}</th>
              <th>{{ __('Tax') }}</th>
              <th>{{ __('Duration') }}</th>
              <th>{{ __('Status') }}</th>
              <th class="text-center" style="width: 90px;">{{ __('Action') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($makeupServices as $key => $service)
              @php
                $hours = floor(((int) $service->duration_minutes) / 60);
                $minutes = ((int) $service->duration_minutes) % 60;
                $formattedDuration =
                    ($hours > 0 ? $hours . 'h' : '') .
                    ($hours > 0 && $minutes > 0 ? ' ' : '') .
                    ($minutes > 0 ? $minutes . 'm' : ($hours > 0 ? '' : '—'));
                $taxLabel = $taxs->first(function ($tax) use ($service) {
                    return abs((float) $tax->tax - (float) $service->tax) < 0.0001;
                });
                $taxValue = rtrim(rtrim(number_format((float) $service->tax, 4, '.', ''), '0'), '.') ?: '0';
                $priceValue = number_format((float) $service->service_base_price, 2, '.', '');
              @endphp
              <tr>
                <td>{{ $key + 1 }}</td>
                <td>
                  <span class="service-name">{{ $service->service_name }}</span>
                  <span class="service-meta">ID: #{{ $service->id }}</span>
                </td>
                <td>{{ $priceValue }}</td>
                <td>
                  @if ((float) $service->tax == 0.0)
                    {{ __('No Tax') }} (0%)
                  @elseif ($taxLabel)
                    {{ $taxLabel->name }} ({{ $taxValue }}%)
                  @else
                    {{ $taxValue }}%
                  @endif
                </td>
                <td>{{ $formattedDuration }}</td>
                <td>
                  @if ($service->status === 'active')
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
                      data-target="#editServiceModal"
                      data-id="{{ $service->id }}"
                      data-service-name="{{ $service->service_name }}"
                      data-service-base-price="{{ $priceValue }}"
                      data-tax="{{ $taxValue }}"
                      data-duration-minutes="{{ (int) $service->duration_minutes }}"
                      data-status="{{ $service->status }}">
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
        <div><i class="fa fa-scissors" aria-hidden="true"></i></div>
        <div><strong>{{ __('No makeup services found') }}</strong></div>
        <div>{{ __('Try adjusting search or status, or create a new service.') }}</div>
      </div>
    @endif
  </div>
</div>

{{-- Create Modal --}}
<div class="modal fade brownz-custom-popup makeup-service-modal" id="createServiceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius: 16px; border: none;">
      <div class="modal-header border-0">
        <h5 class="modal-title" style="color:#1D1D1D;font-weight:600;">{{ __('New Makeup Service') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('makeup.booking.service.store') }}" method="POST" id="createServiceForm">
        @csrf
        <div class="modal-body pt-0">
          <div class="edit-section">
            <p class="edit-section-title">{{ __('Service Details') }}</p>
            <p class="edit-hint">{{ __('Set name, pricing, tax, duration, and status.') }}</p>

            <div class="row">
              <div class="form-group col-md-6">
                <label for="create_service_name">{{ __('Service Name') }}</label>
                <input type="text" id="create_service_name" class="form-control" name="service_name"
                  placeholder="{{ __('Service name') }}" required>
              </div>
              <div class="form-group col-md-6">
                <label for="create_service_base_price">{{ __('Service Base Price') }}</label>
                <input type="number" step="0.01" min="0" id="create_service_base_price" class="form-control"
                  name="service_base_price" placeholder="{{ __('Base price') }}" required>
              </div>
            </div>

            <div class="row">
              <div class="form-group col-md-4">
                <label for="create_tax">{{ __('Tax') }}</label>
                <select id="create_tax" class="form-control" name="tax" required>
                  <option value="" disabled selected>{{ __('Select a Tax') }}</option>
                  <option value="0">{{ __('No Tax') }} (0%)</option>
                  @foreach ($taxs as $tax)
                    @php
                      $optionTax = rtrim(rtrim(number_format((float) $tax->tax, 4, '.', ''), '0'), '.') ?: '0';
                    @endphp
                    @if ((float) $optionTax !== 0.0)
                      <option value="{{ $optionTax }}">{{ $tax->name }} ({{ $optionTax }}%)</option>
                    @endif
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-4">
                <label for="create_duration_minutes">{{ __('Duration') }}</label>
                <select class="form-control" id="create_duration_minutes" name="duration_minutes" required>
                  <option value="" disabled selected>{{ __('Select Duration') }}</option>
                  @foreach (($duration_data ?? []) as $duration)
                    <option value="{{ (int) $duration }}">{{ (int) $duration }} {{ __('min') }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-4">
                <label for="create_status">{{ __('Status') }}</label>
                <select id="create_status" class="form-control" name="status" required>
                  <option value="active" selected>{{ __('Active') }}</option>
                  <option value="inactive">{{ __('Inactive') }}</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn brownz-btn-yellow-d" data-dismiss="modal">
            <i class="fa fa-undo"></i> {{ __('Cancel') }}
          </button>
          <button type="submit" class="btn btn_dark-x">
            <i class="fa fa-check"></i> {{ __('Save') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade brownz-custom-popup makeup-service-modal" id="editServiceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius: 16px; border: none;">
      <div class="modal-header border-0">
        <h5 class="modal-title" style="color:#1D1D1D;font-weight:600;">{{ __('Edit Makeup Service') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('makeup.booking.service.update') }}" method="POST" id="editServiceForm">
        @csrf
        <input type="hidden" name="id" id="edit_booking_id" value="">
        <div class="modal-body pt-0">
          <div class="edit-section">
            <p class="edit-section-title">{{ __('Service Details') }}</p>
            <p class="edit-hint">{{ __('Update name, pricing, tax, duration, and status.') }}</p>

            <div class="row">
              <div class="form-group col-md-6">
                <label for="edit_service_name">{{ __('Service Name') }}</label>
                <input type="text" id="edit_service_name" class="form-control" name="service_name"
                  placeholder="{{ __('Service name') }}" required>
              </div>
              <div class="form-group col-md-6">
                <label for="edit_service_base_price">{{ __('Service Base Price') }}</label>
                <input type="number" step="0.01" min="0" id="edit_service_base_price" class="form-control"
                  name="service_base_price" placeholder="{{ __('Base price') }}" required>
              </div>
            </div>

            <div class="row">
              <div class="form-group col-md-4">
                <label for="edit_tax">{{ __('Tax') }}</label>
                <select id="edit_tax" class="form-control" name="tax" required>
                  <option value="" disabled>{{ __('Select a Tax') }}</option>
                  <option value="0">{{ __('No Tax') }} (0%)</option>
                  @foreach ($taxs as $tax)
                    @php
                      $optionTax = rtrim(rtrim(number_format((float) $tax->tax, 4, '.', ''), '0'), '.') ?: '0';
                    @endphp
                    @if ((float) $optionTax !== 0.0)
                      <option value="{{ $optionTax }}">{{ $tax->name }} ({{ $optionTax }}%)</option>
                    @endif
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-4">
                <label for="edit_duration_minutes">{{ __('Duration') }}</label>
                <select class="form-control" id="edit_duration_minutes" name="duration_minutes" required>
                  <option value="" disabled>{{ __('Select Duration') }}</option>
                  @foreach (($duration_data ?? []) as $duration)
                    <option value="{{ (int) $duration }}">{{ (int) $duration }} {{ __('min') }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-4">
                <label for="edit_status">{{ __('Status') }}</label>
                <select id="edit_status" class="form-control" name="status" required>
                  <option value="active">{{ __('Active') }}</option>
                  <option value="inactive">{{ __('Inactive') }}</option>
                </select>
              </div>
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
  const durationData = (@json($duration_data ?? [])).map(function (d) {
    return parseInt(d, 10);
  }).filter(function (d) {
    return !isNaN(d) && d > 0;
  });

  function formatDuration(duration) {
    const total = parseInt(duration, 10) || 0;
    const hours = Math.floor(total / 60);
    const minutes = total % 60;
    let label = '';
    if (hours > 0) {
      label += hours + (hours === 1 ? ' hour' : ' hours');
    }
    if (hours > 0 && minutes > 0) {
      label += ' and ';
    }
    if (minutes > 0) {
      label += minutes + (minutes === 1 ? ' minute' : ' minutes');
    }
    return label || (total + ' minutes');
  }

  function normalizeTaxValue(value) {
    if (value === undefined || value === null || value === '') {
      return '';
    }
    const num = parseFloat(value);
    if (isNaN(num)) {
      return String(value);
    }
    return String(parseFloat(num.toFixed(4)));
  }

  function fillDurationSelect($select, selectedValue) {
    const selected = (selectedValue === undefined || selectedValue === null || selectedValue === '')
      ? ''
      : String(parseInt(selectedValue, 10));

    $select.empty();
    $select.append(
      $('<option></option>')
        .val('')
        .text('{{ __('Select Duration') }}')
        .prop('disabled', true)
    );

    const values = durationData.slice();
    if (selected && values.indexOf(parseInt(selected, 10)) === -1) {
      values.push(parseInt(selected, 10));
      values.sort(function (a, b) { return a - b; });
    }

    values.forEach(function (duration) {
      $select.append(
        $('<option></option>')
          .val(String(duration))
          .text(formatDuration(duration))
      );
    });

    if (selected) {
      $select.val(selected);
    } else {
      $select.val('');
    }
  }

  function setTaxSelect($select, taxValue) {
    const normalized = normalizeTaxValue(taxValue);
    if (!normalized) {
      $select.val('');
      return;
    }

    let matchedValue = null;
    $select.find('option').each(function () {
      const optionVal = $(this).attr('value');
      if (optionVal === '' || optionVal === undefined) {
        return;
      }
      if (normalizeTaxValue(optionVal) === normalized) {
        matchedValue = optionVal;
        return false;
      }
    });

    if (matchedValue !== null) {
      $select.val(matchedValue);
      return;
    }

    // Orphaned/inactive tax rate still on the service — keep it selectable
    $select.append(
      $('<option></option>')
        .val(normalized)
        .text(normalized + '%')
        .prop('selected', true)
    );
  }

  jQuery(function ($) {
    fillDurationSelect($('#create_duration_minutes'));
    fillDurationSelect($('#edit_duration_minutes'));

    $('#createServiceModal').on('shown.bs.modal', function () {
      fillDurationSelect($('#create_duration_minutes'));
      $('#create_status').val('active');
    });

    $('#createServiceModal').on('hidden.bs.modal', function () {
      const form = document.getElementById('createServiceForm');
      form.reset();
      $('#create_status').val('active');
      fillDurationSelect($('#create_duration_minutes'));
    });

    $('#editServiceModal').on('hidden.bs.modal', function () {
      const form = document.getElementById('editServiceForm');
      form.reset();
      fillDurationSelect($('#edit_duration_minutes'));
    });

    $(document).on('click', '.edit-btn', function () {
      const $btn = $(this);
      const id = $btn.attr('data-id');
      const serviceName = $btn.attr('data-service-name');
      const basePrice = $btn.attr('data-service-base-price');
      const tax = $btn.attr('data-tax');
      const duration = $btn.attr('data-duration-minutes');
      const status = $btn.attr('data-status');

      $('#edit_booking_id').val(id || '');
      $('#edit_service_name').val(serviceName || '');
      $('#edit_service_base_price').val(basePrice || '');
      $('#edit_status').val(status || 'active');

      fillDurationSelect($('#edit_duration_minutes'), duration);
      setTaxSelect($('#edit_tax'), tax);
    });

    // Re-apply values when modal finishes opening (Bootstrap sometimes resets selects)
    $('#editServiceModal').on('shown.bs.modal', function () {
      const $btn = $('.edit-btn[data-id="' + $('#edit_booking_id').val() + '"]').first();
      if (!$btn.length) {
        return;
      }
      fillDurationSelect($('#edit_duration_minutes'), $btn.attr('data-duration-minutes'));
      setTaxSelect($('#edit_tax'), $btn.attr('data-tax'));
      $('#edit_service_name').val($btn.attr('data-service-name') || '');
      $('#edit_service_base_price').val($btn.attr('data-service-base-price') || '');
      $('#edit_status').val($btn.attr('data-status') || 'active');
    });
  });
</script>
@endsection
