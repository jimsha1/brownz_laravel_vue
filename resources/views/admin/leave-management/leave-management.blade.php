@extends('layouts.vcapp')

@section('content')
<style>
  .leave-management-page .section-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 16px;
  }

  .leave-management-page .filter-label,
  .leave-management-page .form-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .leave-management-page .filter-control,
  .leave-management-page .create-control {
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

  .leave-management-page select.filter-control,
  .leave-management-page select.create-control {
    appearance: auto;
  }

  .leave-management-page .filter-actions,
  .leave-management-page .create-actions {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  .leave-management-page .filter-actions .btn,
  .leave-management-page .create-actions .btn {
    white-space: nowrap;
  }

  .leave-management-page .list-toolbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 14px;
  }

  .leave-management-page .list-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .leave-management-page .employee-name {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
  }

  .leave-management-page .employee-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .leave-management-page .status-pill,
  .leave-management-page .mode-pill,
  .leave-management-page .type-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    text-transform: capitalize;
  }

  .leave-management-page .status-pill.is-approved {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .leave-management-page .status-pill.is-pending {
    color: #9A6700;
    background: rgba(230, 205, 127, 0.35);
  }

  .leave-management-page .status-pill.is-rejected {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  .leave-management-page .mode-pill,
  .leave-management-page .type-pill {
    color: #1D1D1D;
    background: #F0F0F3;
  }

  .leave-management-page .manage-actions {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    flex-wrap: nowrap;
  }

  .leave-management-page .manage-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    min-width: 34px;
    padding: 0;
    border-radius: 50%;
    background: #F0F0F3;
    color: #1D1D1D;
    border: none;
    cursor: pointer;
    font-size: 13px;
    text-decoration: none !important;
    flex: 0 0 auto;
  }

  .leave-management-page .manage-btn.is-status {
    background: #1D1D1D;
    color: #E6CD7F;
  }

  .leave-management-page .manage-btn.is-delete {
    background: rgba(208, 52, 44, 0.12);
    color: #D0342C;
  }

  .leave-management-page .manage-btn:hover {
    background: #1D1D1D;
    color: #E6CD7F;
  }

  .leave-management-page .manage-col {
    width: 110px;
    white-space: nowrap;
  }

  .leave-management-page .empty-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }

  .leave-management-page .empty-state i {
    font-size: 28px;
    margin-bottom: 10px;
    color: #b7b7b7;
  }

  .leave-status-modal .modal-content {
    border: none;
    border-radius: 16px;
    overflow: hidden;
  }

  .leave-status-modal .modal-header {
    border: 0;
    padding: 16px 18px 8px;
  }

  .leave-status-modal .modal-title {
    color: #1D1D1D;
    font-weight: 600;
  }

  .leave-status-modal .modal-body {
    padding: 8px 18px 4px;
  }

  .leave-status-modal .modal-footer {
    border: 0;
    padding: 8px 18px 16px;
    gap: 8px;
  }

  .leave-status-modal .summary-box {
    background: #F7F7F9;
    border-radius: 12px;
    padding: 12px 14px;
    margin-bottom: 14px;
  }

  .leave-status-modal .summary-box .label {
    display: block;
    font-size: 12px;
    color: #6c757d;
  }

  .leave-status-modal .summary-box .value {
    font-weight: 600;
    color: #1D1D1D;
  }

  .leave-status-modal .form-control {
    height: 38px;
    border: none;
    border-radius: 999px;
    background: #F0F0F3;
    padding: 8px 14px;
  }
</style>

<div class="leave-management-page">
  {{-- Create Leave --}}
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <h5 class="section-title">{{ __('Add Leave') }}</h5>

      <form class="voco-ajax-form leave-create-form" method="post"
        action="{{ route('admin.leave.management.save') }}"
        data-hook-success="leaveCreateSaved">
        @csrf
        <div class="row align-items-end br-row-gap-2">
          <div class="col-lg-3 col-md-6">
            <label class="form-label" for="create_emp_id">{{ __('Employee') }}</label>
            <select class="form-control create-control voco-select2" id="create_emp_id" name="employee"
              data-placeholder="{{ __('Select employee') }}">
              <option value=""></option>
              {{ $employees_dropdown }}
            </select>
          </div>

          <div class="col-lg-3 col-md-6">
            <label class="form-label" for="leave_dates">{{ __('Leave Dates') }}</label>
            <input id="leave_dates" class="form-control create-control" type="text" name="leave_dates"
              placeholder="{{ __('Select dates') }}">
          </div>

          <div class="col-lg-3 col-md-6">
            <label class="form-label" for="leave_mode">{{ __('Leave Mode') }}</label>
            <select class="form-control create-control voco-select2" id="leave_mode" name="leave_mode"
              data-placeholder="{{ __('Select mode') }}">
              <option value=""></option>
              <option value="paid">{{ __('Paid') }}</option>
              <option value="unpaid">{{ __('Unpaid') }}</option>
              <option value="week_off">{{ __('Week off') }}</option>
            </select>
          </div>

          <div class="col-lg-3 col-md-6">
            <label class="form-label" for="leave_type">{{ __('Leave Type') }}</label>
            <select class="form-control create-control voco-select2" id="leave_type" name="leave_type"
              data-placeholder="{{ __('Select type') }}">
              <option value=""></option>
              <option value="full">{{ __('Full Leave') }}</option>
              <option value="first_half">{{ __('First Half') }}</option>
              <option value="second_half">{{ __('Second Half') }}</option>
            </select>
          </div>

          <div class="col-lg-6 col-md-8">
            <label class="form-label" for="create_reason">{{ __('Reason') }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control create-control" id="create_reason" name="reason"
              placeholder="{{ __('Enter leave reason') }}">
          </div>

          <div class="col-lg-6 col-md-4">
            <label class="form-label d-none d-lg-block">&nbsp;</label>
            <div class="create-actions justify-content-lg-end">
              <button type="submit" class="btn btn-sm btn-primary btn_dark-x">
                <i class="fa fa-check"></i> {{ __('Save') }}
              </button>
              <button type="button" id="resetFormBtn" class="btn btn-sm btn-secondary brownz-btn-yellow-d">
                <i class="fa fa-undo"></i> {{ __('Reset') }}
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Filters + List --}}
  <div class="fbox-shadow mt-2 fd-card">
    <div class="card-body">
      <h5 class="section-title mb-3">{{ __('Leave List') }}</h5>

      <form method="get" action="{{ route('admin.leave.management.list') }}" class="leave-list-filters mb-3">
        <div class="row align-items-end br-row-gap-2">
          <div class="col-lg-2 col-md-6">
            <label class="filter-label" for="start_date">{{ __('Start Date') }}</label>
            <input type="date" id="start_date" name="start_date" class="form-control filter-control"
              value="{{ $startDate ?? now()->startOfMonth()->toDateString() }}">
          </div>

          <div class="col-lg-2 col-md-6">
            <label class="filter-label" for="end_date">{{ __('End Date') }}</label>
            <input type="date" id="end_date" name="end_date" class="form-control filter-control"
              value="{{ $endDate ?? now()->endOfMonth()->toDateString() }}">
          </div>

          <div class="col-lg-3 col-md-6">
            <label class="filter-label" for="filter_employee_id">{{ __('Employee') }}</label>
            <select id="filter_employee_id" name="employee_id" class="form-control filter-control">
              <option value="">{{ __('All Employees') }}</option>
              {{ $employees_dropdown }}
            </select>
          </div>

          <div class="col-lg-2 col-md-6">
            <label class="filter-label" for="filter_leave_status">{{ __('Status') }}</label>
            @php $selectedStatus = $status ?? 'all'; @endphp
            <select id="filter_leave_status" name="leave_status" class="form-control filter-control">
              <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>{{ __('All Status') }}</option>
              <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
              <option value="approved" {{ $selectedStatus === 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
              <option value="rejected" {{ $selectedStatus === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
            </select>
          </div>

          <div class="col-lg-3 col-md-6">
            <label class="filter-label" for="search_q">{{ __('Search') }}</label>
            <div class="search-box-bar-container jus-heigh-search-b">
              <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
              <input placeholder="{{ __('Name, code, or reason...') }}" class="form-control brawnz-search-bar-in"
                type="text" id="search_q" name="search_q" value="{{ request('search_q') }}">
            </div>
          </div>

          <div class="col-lg-12">
            <div class="filter-actions">
              <button type="submit" class="btn btn-sm btn-primary btn_dark-x">
                <i class="fa fa-search"></i> {{ __('Search') }}
              </button>
              <a href="{{ route('admin.leave.management.list') }}" class="btn btn-sm btn-secondary brownz-btn-yellow-d">
                <i class="fa fa-refresh"></i> {{ __('Reset') }}
              </a>
            </div>
          </div>
        </div>
      </form>

      <div class="list-toolbar">
        <div>
          <span class="list-meta">
            {{ __('Showing') }} {{ $db_leaves->firstItem() ?: 0 }}–{{ $db_leaves->lastItem() ?: 0 }}
            {{ __('of') }} {{ $db_leaves->total() }}
          </span>
        </div>
      </div>

      @if ($db_leaves->count())
        <div class="table-responsive">
          <table class="table brownz-table-list-v1 m-0">
            <thead>
              <tr>
                <th style="width: 56px;">{{ __('SL#') }}</th>
                <th>{{ __('Employee') }}</th>
                <th>{{ __('Leave Date') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Mode') }}</th>
                <th>{{ __('Reason') }}</th>
                <th>{{ __('Status') }}</th>
                <th class="text-center manage-col">{{ __('Manage') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($db_leaves as $key => $leave)
                @php
                  $typeLabel = $leave->leave_type == \App\Models\LeaveManagement::LEAVE_TYPE_FULL
                    ? __('Full Leave')
                    : ($leave->leave_type == \App\Models\LeaveManagement::LEAVE_TYPE_FIRST_HALF
                      ? __('First Half')
                      : __('Second Half'));
                  $modeLabel = str_replace('_', ' ', $leave->leave_mode ?: '—');
                  $statusClass = 'is-'.($leave->leave_status ?: 'pending');
                @endphp
                <tr>
                  <td>{{ $key + $db_leaves->firstItem() }}</td>
                  <td>
                    <span class="employee-name">{{ $leave->name ?: ($leave->employee_name ?: '—') }}</span>
                    <span class="employee-meta">
                      {{ $leave->emp_code ?: '—' }}
                      @if (!empty($leave->beautician_code))
                        · {{ $leave->beautician_code }}
                      @endif
                    </span>
                  </td>
                  <td>{{ \Carbon\Carbon::parse($leave->leave_date)->format('d M, Y') }}</td>
                  <td><span class="type-pill">{{ $typeLabel }}</span></td>
                  <td><span class="mode-pill">{{ __(ucwords($modeLabel)) }}</span></td>
                  <td>{{ $leave->reason ?: '—' }}</td>
                  <td>
                    <span class="status-pill {{ $statusClass }}">{{ __(ucfirst($leave->leave_status ?: 'pending')) }}</span>
                  </td>
                  <td class="text-center manage-col">
                    <div class="manage-actions">
                      @can('leave_approve')
                        @if (($leave->leave_status ?: 'pending') === 'pending')
                          <button type="button"
                            class="manage-btn is-status change-status-btn"
                            title="{{ __('Change Status') }}"
                            data-leave-id="{{ $leave->leave_id }}"
                            data-emp-id="{{ $leave->emp_id }}"
                            data-employee-name="{{ $leave->name ?: ($leave->employee_name ?: '') }}"
                            data-leave-date="{{ \Carbon\Carbon::parse($leave->leave_date)->format('d M, Y') }}"
                            data-leave-type="{{ $typeLabel }}"
                            data-current-status="{{ $leave->leave_status ?: 'pending' }}"
                            data-reason="{{ $leave->reason }}">
                            <i class="fa fa-check" aria-hidden="true"></i>
                          </button>
                        @endif
                      @endcan

                      <form class="delete-form d-inline" method="post"
                        action="{{ route('admin.leave.management.delete', $leave->leave_id) }}">
                        @csrf
                        <input type="hidden" name="leave_id" value="{{ $leave->leave_id }}">
                        <button type="submit" class="manage-btn is-delete" title="{{ __('Delete') }}">
                          <i class="fa fa-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $db_leaves->links() }}
        </div>
      @else
        <div class="empty-state">
          <div><i class="fa fa-calendar" aria-hidden="true"></i></div>
          <div><strong>{{ __('No leave records found') }}</strong></div>
          <div>{{ __('Try adjusting date, employee, or status filters.') }}</div>
        </div>
      @endif
    </div>
  </div>
</div>

{{-- Change Status Modal --}}
<div class="modal fade leave-status-modal brownz-custom-popup" id="changeStatusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Change Leave Status') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="changeStatusForm">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="leave_id" id="modal_leave_id">
          <input type="hidden" name="emp_id" id="modal_emp_id">

          <div class="summary-box">
            <span class="label">{{ __('Employee') }}</span>
            <div class="value" id="modal_employee_name">—</div>
            <div class="mt-2">
              <span class="label">{{ __('Leave Date') }}</span>
              <div class="value" id="modal_leave_date">—</div>
            </div>
            <div class="mt-2">
              <span class="label">{{ __('Type') }}</span>
              <div class="value" id="modal_leave_type">—</div>
            </div>
          </div>

          <div class="row">
            <div class="form-group col-md-6">
              <label class="form-label" for="modal_leave_status">{{ __('Status') }}</label>
              <select id="modal_leave_status" class="form-control" name="leave_status" required>
                <option value="" disabled>{{ __('Select Status') }}</option>
                <option value="approved">{{ __('Approved') }}</option>
                <option value="rejected">{{ __('Rejected') }}</option>
              </select>
            </div>

            <div class="form-group col-md-6">
              <label class="form-label" for="modal_reason">{{ __('Reason / Note') }}</label>
              <input type="text" name="reason" id="modal_reason" class="form-control"
                placeholder="{{ __('Optional note') }}">
            </div>
          </div>
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-sm brownz-btn-yellow-d" data-dismiss="modal">
            {{ __('Cancel') }}
          </button>
          <button type="submit" class="btn btn-sm btn_dark-x">
            {{ __('Update Status') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
  var leaveDatesPicker = null;

  function resetLeaveCreateForm() {
    var form = document.querySelector('.leave-create-form');
    if (!form) {
      return;
    }

    form.reset();
    $('#create_emp_id').val(null).trigger('change');
    $('#leave_mode').val(null).trigger('change');
    $('#leave_type').val(null).trigger('change');
    $('#create_reason').val('');

    if (leaveDatesPicker) {
      leaveDatesPicker.clear();
    } else {
      $('#leave_dates').val('');
    }
  }

  function leaveCreateSaved() {
    resetLeaveCreateForm();
  }

  jQuery(function ($) {
    var selectedEmployee = @json((string) ($employeeId ?? ''));
    if (selectedEmployee) {
      $('#filter_employee_id').val(selectedEmployee);
    }

    leaveDatesPicker = flatpickr("#leave_dates", {
      mode: "multiple",
      dateFormat: "Y-m-d",
      allowInput: true,
      minDate: "today"
    });

    $('#resetFormBtn').on('click', function () {
      resetLeaveCreateForm();
    });

    $('body').on('submit', '.delete-form', function (e) {
      e.preventDefault();
      e.stopPropagation();

      if (!confirm("{{ __('Are you sure you want to delete this record?') }}")) {
        return;
      }

      var $form = $(this);
      $.ajax({
        method: "POST",
        url: $form.attr('action'),
        data: $form.serialize()
      }).done(function (data) {
        if (data && data.gotourl) {
          window.location.href = data.gotourl;
        } else {
          location.reload();
        }
      }).fail(function () {
        alert("{{ __('Failed to delete leave record.') }}");
      });
    });

    $(".change-status-btn").on("click", function () {
      var $btn = $(this);
      $("#modal_leave_id").val($btn.data("leave-id"));
      $("#modal_emp_id").val($btn.data("emp-id"));
      $("#modal_employee_name").text($btn.data("employee-name") || '—');
      $("#modal_leave_date").text($btn.data("leave-date") || '—');
      $("#modal_leave_type").text($btn.data("leave-type") || '—');
      $("#modal_leave_status").val('');
      $("#modal_reason").val($btn.data("reason") || '');
      $("#changeStatusModal").modal("show");
    });

    $("#changeStatusForm").on("submit", function (e) {
      e.preventDefault();

      var leaveId = $("#modal_leave_id").val();
      if (!leaveId) {
        return;
      }

      $.ajax({
        url: "{{ url('/admin/leave-management/update-status') }}/" + leaveId,
        type: "POST",
        data: $(this).serialize(),
        success: function (response) {
          if (response && response.gotourl) {
            window.location.href = response.gotourl;
          } else {
            location.reload();
          }
        },
        error: function (xhr) {
          var message = "{{ __('Error updating status!') }}";
          if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
          }
          alert(message);
        }
      });
    });
  });
</script>
@endsection
