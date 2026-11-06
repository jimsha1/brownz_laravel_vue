@extends('layouts.vcapp')

@section('content')
<style>
  .zout-lookup-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .zout-lookup-filters .filter-control {
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

  .zout-lookup-filters select.filter-control {
    appearance: auto;
  }

  .zout-lookup-filters .filter-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  @media (min-width: 992px) {
    .zout-lookup-filters .filter-actions {
      justify-content: flex-end;
    }
  }

  .zout-list .cashier-name {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
  }

  .zout-list .float-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .zout-list .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
  }

  .zout-list .status-pill.is-open {
    color: #B56A00;
    background: rgba(230, 205, 127, 0.45);
  }

  .zout-list .status-pill.is-closed {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .zout-list .manage-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
    flex-wrap: wrap;
  }

  .zout-list .manage-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-width: 34px;
    height: 34px;
    padding: 0 12px;
    border-radius: 999px;
    background: #F0F0F3;
    color: #1D1D1D;
    border: none;
    text-decoration: none !important;
    font-size: 12px;
    font-weight: 600;
    line-height: 1;
    cursor: pointer;
  }

  .zout-list .manage-btn.icon-only {
    width: 34px;
    padding: 0;
    border-radius: 50%;
  }

  .zout-list .manage-btn:hover {
    background: #1D1D1D;
    color: #E6CD7F;
  }

  .zout-list .manage-btn.is-danger:hover {
    background: #D0342C;
    color: #fff;
  }

  .zout-list .empty-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }

  .zout-list .empty-state i {
    font-size: 28px;
    margin-bottom: 10px;
    color: #b7b7b7;
  }

  .zout-list .zout-pagination {
    display: flex;
    justify-content: flex-end;
    margin-top: 16px;
  }

  .zout-list th,
  .zout-list td {
    vertical-align: middle;
  }
</style>

@if (Session::has('error'))
  <div class="alert alert-danger mt-2" role="alert">{{ Session::get('error') }}</div>
@endif
@if (Session::has('message'))
  <div class="alert alert-success mt-2" role="alert">{{ Session::get('message') }}</div>
@endif

<div class="fbox-shadow mt-2 fd-card respo-margin-top zout-lookup-filters">
  <div class="card-body">
    <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
      {{ __('Z-Out Lookup') }}
    </h5>

    <form class="brw-form-la" method="get" action="{{ route('re.print.bill') }}">
      <div class="row align-items-end br-row-gap-2">
        <div class="col-lg-3 col-md-6">
          <label class="filter-label" for="name">{{ __('Cashier') }}</label>
          <div class="search-box-bar-container jus-heigh-search-b">
            <p class="m-0"><i aria-hidden="true" class="fa fa-user"></i></p>
            <input type="text" class="form-control brawnz-search-bar-in" name="name"
              id="name"
              value="{{ $filter_name ?? '' }}"
              placeholder="{{ __('Search by name...') }}">
          </div>
        </div>

        <div class="col-lg-2 col-md-6">
          <label class="filter-label" for="start_date">{{ __('Start Date') }}</label>
          <input type="date" class="form-control filter-control" name="start_date"
            id="start_date"
            value="{{ $filter_start_date ?? now()->startOfMonth()->toDateString() }}">
        </div>

        <div class="col-lg-2 col-md-6">
          <label class="filter-label" for="end_date">{{ __('End Date') }}</label>
          <input type="date" class="form-control filter-control" name="end_date"
            id="end_date"
            value="{{ $filter_end_date ?? now()->endOfMonth()->toDateString() }}">
        </div>

        <div class="col-lg-2 col-md-6">
          <label class="filter-label" for="is_closed">{{ __('Status') }}</label>
          <select name="is_closed" id="is_closed" class="form-control filter-control">
            <option value="" {{ ($filter_is_close ?? '') === '' || ($filter_is_close ?? '') === 'All' ? 'selected' : '' }}>
              {{ __('All Status') }}
            </option>
            <option value="0" {{ (string) ($filter_is_close ?? '') === '0' ? 'selected' : '' }}>
              {{ __('Open') }}
            </option>
            <option value="1" {{ (string) ($filter_is_close ?? '') === '1' ? 'selected' : '' }}>
              {{ __('Closed') }}
            </option>
          </select>
        </div>

        <div class="col-lg-3 col-md-12">
          <label class="filter-label d-none d-lg-block">&nbsp;</label>
          <div class="filter-actions">
            <button type="submit" class="btn btn-sm btn-primary btn_dark-x">
              <i class="fa fa-search"></i> {{ __('Search') }}
            </button>
            <a href="{{ route('re.print.bill') }}" class="btn btn-sm btn-secondary brownz-btn-yellow-d">
              <i class="fa fa-refresh"></i> {{ __('Reset') }}
            </a>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="fbox-shadow mt-2 fd-card zout-list">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <h5 style="color: #1D1D1D; font-size: 1.25rem;" class="mb-0">
        {{ __('Z-Out List') }}
      </h5>
      @if ($close_amt_records->total())
        <div style="font-size: 12px; color: #6c757d;">
          {{ __('Showing') }}
          {{ $close_amt_records->firstItem() }}–{{ $close_amt_records->lastItem() }}
          {{ __('of') }}
          {{ $close_amt_records->total() }}
        </div>
      @endif
    </div>

    @if ($close_amt_records->count())
      <div class="table-responsive">
        <table class="table brownz-table-list-v1 m-0">
          <thead>
            <tr>
              <th style="width: 56px;">{{ __('SL#') }}</th>
              <th>{{ __('Cashier') }}</th>
              <th>{{ __('Open Date') }}</th>
              <th>{{ __('Close Date') }}</th>
              <th>{{ __('Status') }}</th>
              <th class="text-center" style="width: 220px;">{{ __('Action') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($close_amt_records as $close_amt)
              <tr>
                <td>{{ $close_amt_records->firstItem() + $loop->index }}</td>
                <td>
                  <span class="cashier-name">{{ $close_amt->name }}</span>
                  <span class="float-meta">{{ __('Float ID') }}: #{{ $close_amt->float_amt_id }}</span>
                  @if (!empty($close_amt->edited_at))
                    <span class="float-meta">
                      {{ __('Count edited') }}
                      @if (!empty($close_amt->edited_by_name))
                        {{ __('by') }} {{ $close_amt->edited_by_name }}
                      @endif
                      · {{ date('d/m/Y g:i A', strtotime($close_amt->edited_at)) }}
                    </span>
                    @if (!empty($close_amt->edit_reason))
                      <span class="float-meta">{{ __('Reason') }}: {{ $close_amt->edit_reason }}</span>
                    @endif
                  @endif
                </td>
                <td>{{ date('d/m/Y g:i A', strtotime($close_amt->added_date)) }}</td>
                <td>
                  @if ((int) $close_amt->is_closed === 0)
                    —
                  @else
                    {{ date('d/m/Y g:i A', strtotime($close_amt->updated_date)) }}
                  @endif
                </td>
                <td>
                  @if ((int) $close_amt->is_closed === 0)
                    <span class="status-pill is-open">{{ __('Open') }}</span>
                  @else
                    <span class="status-pill is-closed">{{ __('Closed') }}</span>
                  @endif
                </td>
                <td class="text-center">
                  <div class="manage-actions">
                    @canany(['force_close', 'former_zout'])
                      @if ((int) $close_amt->is_closed === 0)
                        <button type="button"
                          class="manage-btn is-danger"
                          title="{{ __('Force Close') }}"
                          onclick="openForceCloseModal(this)"
                          data-user-id="{{ $close_amt->user_id }}"
                          data-float-amt-id="{{ $close_amt->float_amt_id }}"
                          data-cashier-name="{{ e($close_amt->name) }}">
                          <i class="fa fa-lock" aria-hidden="true"></i>
                          {{ __('Force Close') }}
                        </button>
                      @endif
                    @endcanany
                    @if ((int) $close_amt->is_closed === 1)
                      @can('edit_closed_count')
                        <button type="button"
                          class="manage-btn"
                          title="{{ __('Edit closed count') }}"
                          onclick="openEditClosedModal(this)"
                          data-user-id="{{ $close_amt->user_id }}"
                          data-float-amt-id="{{ $close_amt->float_amt_id }}"
                          data-cashier-name="{{ e($close_amt->name) }}">
                          <i class="fa fa-pencil" aria-hidden="true"></i>
                          {{ __('Edit') }}
                        </button>
                      @endcan
                      <button type="button"
                        class="manage-btn icon-only"
                        title="{{ __('Print bill') }}"
                        onclick="openInvoice('{{ route('close.float.print.bill', ['id' => $close_amt->user_id, 'float_amt_id' => $close_amt->float_amt_id]) }}')">
                        <i class="fa fa-print" aria-hidden="true"></i>
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="zout-pagination">
        {!! $close_amt_records->links() !!}
      </div>
    @else
      <div class="empty-state">
        <div><i class="fa fa-inbox" aria-hidden="true"></i></div>
        <div><strong>{{ __('No records found') }}</strong></div>
        <div>{{ __('Try adjusting the cashier, date range, or status filters.') }}</div>
      </div>
    @endif
  </div>
</div>

<script>
  function openInvoice(response) {
    window.open(
      response,
      "",
      "toolbar=no,status=no,menubar=no,location=center,scrollbars=no,resizable=no,height=500,width=657"
    );
  }

  function openForceCloseModal(button) {
    var $modal = $('#forceClosePasswordModal');
    $('#force_close_user_id').val($(button).data('user-id'));
    $('#force_close_float_amt_id').val($(button).data('float-amt-id'));
    $('#force_close_cashier_label').text($(button).data('cashier-name') || '—');
    $('#force_close_float_label').text('#' + ($(button).data('float-amt-id') || '—'));
    $('#force_close_auth_password').val('');
    $('#force_close_password_error').addClass('d-none').text('');
    $('#force_close_submit_btn').prop('disabled', false).html(
      '<i class="fa fa-lock"></i> {{ __('Confirm & Continue') }}'
    );
    $modal.modal('show');
    setTimeout(function () {
      $('#force_close_auth_password').trigger('focus');
    }, 300);
  }

  function openEditClosedModal(button) {
    var $modal = $('#editClosedPasswordModal');
    $('#edit_closed_user_id').val($(button).data('user-id'));
    $('#edit_closed_float_amt_id').val($(button).data('float-amt-id'));
    $('#edit_closed_cashier_label').text($(button).data('cashier-name') || '—');
    $('#edit_closed_float_label').text('#' + ($(button).data('float-amt-id') || '—'));
    $('#edit_closed_auth_password').val('');
    $('#edit_closed_password_error').addClass('d-none').text('');
    $('#edit_closed_submit_btn').prop('disabled', false).html(
      '<i class="fa fa-pencil"></i> {{ __('Confirm & Edit') }}'
    );
    $modal.modal('show');
    setTimeout(function () {
      $('#edit_closed_auth_password').trigger('focus');
    }, 300);
  }

  jQuery(function ($) {
    $('#forceClosePasswordForm').on('submit', function (e) {
      e.preventDefault();

      var $form = $(this);
      var password = $('#force_close_auth_password').val();
      var $error = $('#force_close_password_error');
      var $btn = $('#force_close_submit_btn');

      $error.addClass('d-none').text('');

      if (!password) {
        $error.removeClass('d-none').text('{{ __('Password is required.') }}');
        return;
      }

      $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ __('Checking...') }}');

      $.ajax({
        url: "{{ route('close.float_button') }}",
        type: 'POST',
        data: $form.serialize(),
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        success: function (response) {
          if (response && response.gotourl) {
            window.location.href = response.gotourl;
            return;
          }
          $error.removeClass('d-none').text('{{ __('Unable to continue. Please try again.') }}');
          $btn.prop('disabled', false).html('<i class="fa fa-lock"></i> {{ __('Confirm & Continue') }}');
        },
        error: function (xhr) {
          var message = '{{ __('Incorrect password.') }}';
          if (xhr.responseJSON) {
            if (xhr.responseJSON.errors && xhr.responseJSON.errors.auth_password) {
              message = xhr.responseJSON.errors.auth_password[0];
            } else if (xhr.responseJSON.message) {
              message = xhr.responseJSON.message;
            }
          }
          $error.removeClass('d-none').text(message);
          $btn.prop('disabled', false).html('<i class="fa fa-lock"></i> {{ __('Confirm & Continue') }}');
          $('#force_close_auth_password').trigger('focus').trigger('select');
        }
      });
    });

    $('#editClosedPasswordForm').on('submit', function (e) {
      e.preventDefault();

      var $form = $(this);
      var password = $('#edit_closed_auth_password').val();
      var $error = $('#edit_closed_password_error');
      var $btn = $('#edit_closed_submit_btn');

      $error.addClass('d-none').text('');

      if (!password) {
        $error.removeClass('d-none').text('{{ __('Password is required.') }}');
        return;
      }

      $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ __('Checking...') }}');

      $.ajax({
        url: "{{ route('edit.closed.float') }}",
        type: 'POST',
        data: $form.serialize(),
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        success: function (response) {
          if (response && response.gotourl) {
            window.location.href = response.gotourl;
            return;
          }
          $error.removeClass('d-none').text('{{ __('Unable to continue. Please try again.') }}');
          $btn.prop('disabled', false).html('<i class="fa fa-pencil"></i> {{ __('Confirm & Edit') }}');
        },
        error: function (xhr) {
          var message = '{{ __('Incorrect password.') }}';
          if (xhr.responseJSON) {
            if (xhr.responseJSON.errors && xhr.responseJSON.errors.auth_password) {
              message = xhr.responseJSON.errors.auth_password[0];
            } else if (xhr.responseJSON.message) {
              message = xhr.responseJSON.message;
            }
          }
          $error.removeClass('d-none').text(message);
          $btn.prop('disabled', false).html('<i class="fa fa-pencil"></i> {{ __('Confirm & Edit') }}');
          $('#edit_closed_auth_password').trigger('focus').trigger('select');
        }
      });
    });
  });
</script>

{{-- Force Close password gate --}}
<div class="modal fade" id="forceClosePasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 16px; border: none;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title" style="color:#1D1D1D;font-size:1.25rem;font-weight:600;">
          {{ __('Confirm Force Close') }}
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="forceClosePasswordForm" method="post" action="{{ route('close.float_button') }}">
        @csrf
        <input type="hidden" name="force_close" value="1">
        <input type="hidden" name="id" id="force_close_user_id" value="">
        <input type="hidden" name="float_amt_id" id="force_close_float_amt_id" value="">

        <div class="modal-body pt-2">
          <p style="color:#6c757d;font-size:14px;margin-bottom:14px;">
            {{ __('Enter your password to continue force-closing this register.') }}
          </p>

          <div class="force-close-meta mb-3">
            <div><strong>{{ __('Cashier') }}:</strong> <span id="force_close_cashier_label">—</span></div>
            <div><strong>{{ __('Float ID') }}:</strong> <span id="force_close_float_label">—</span></div>
          </div>

          <div class="form-group mb-2">
            <label for="force_close_auth_password">{{ __('Your Password') }}</label>
            <input type="password"
              class="form-control"
              id="force_close_auth_password"
              name="auth_password"
              autocomplete="current-password"
              placeholder="{{ __('Password') }}">
          </div>
          <div id="force_close_password_error" class="text-danger small d-none"></div>
        </div>

        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn brownz-btn-yellow-d" data-dismiss="modal">
            {{ __('Cancel') }}
          </button>
          <button type="submit" id="force_close_submit_btn" class="btn btn_dark-x">
            <i class="fa fa-lock"></i> {{ __('Confirm & Continue') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Edit closed count password gate --}}
<div class="modal fade" id="editClosedPasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 16px; border: none;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title" style="color:#1D1D1D;font-size:1.25rem;font-weight:600;">
          {{ __('Edit Closed Count') }}
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="editClosedPasswordForm" method="post" action="{{ route('edit.closed.float') }}">
        @csrf
        <input type="hidden" name="confirm_edit" value="1">
        <input type="hidden" name="id" id="edit_closed_user_id" value="">
        <input type="hidden" name="float_amt_id" id="edit_closed_float_amt_id" value="">

        <div class="modal-body pt-2">
          <p style="color:#6c757d;font-size:14px;margin-bottom:14px;">
            {{ __('Enter your password to edit the cash count. Sales, open time, and close time will not change.') }}
          </p>

          <div class="force-close-meta mb-3">
            <div><strong>{{ __('Cashier') }}:</strong> <span id="edit_closed_cashier_label">—</span></div>
            <div><strong>{{ __('Float ID') }}:</strong> <span id="edit_closed_float_label">—</span></div>
          </div>

          <div class="form-group mb-2">
            <label for="edit_closed_auth_password">{{ __('Your Password') }}</label>
            <input type="password"
              class="form-control"
              id="edit_closed_auth_password"
              name="auth_password"
              autocomplete="current-password"
              placeholder="{{ __('Password') }}">
          </div>
          <div id="edit_closed_password_error" class="text-danger small d-none"></div>
        </div>

        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn brownz-btn-yellow-d" data-dismiss="modal">
            {{ __('Cancel') }}
          </button>
          <button type="submit" id="edit_closed_submit_btn" class="btn btn_dark-x">
            <i class="fa fa-pencil"></i> {{ __('Confirm & Edit') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  #forceClosePasswordModal .force-close-meta,
  #editClosedPasswordModal .force-close-meta {
    background: #F7F7F9;
    border-radius: 12px;
    padding: 12px 14px;
    font-size: 13px;
    color: #1D1D1D;
  }
  #forceClosePasswordModal .force-close-meta > div + div,
  #editClosedPasswordModal .force-close-meta > div + div {
    margin-top: 4px;
  }
  #forceClosePasswordModal .form-control,
  #editClosedPasswordModal .form-control {
    border-radius: 999px;
    background: #F0F0F3;
    border: none;
    height: 42px;
    padding: 8px 16px;
  }
</style>
@endsection
