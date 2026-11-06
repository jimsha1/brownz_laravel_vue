@extends('layouts.vcapp')

@section('content')
<style>
  .xout-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .xout-filters .filter-control {
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

  .xout-filters select.filter-control {
    appearance: auto;
  }

  .xout-filters .filter-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  @media (min-width: 992px) {
    .xout-filters .filter-actions {
      justify-content: flex-end;
    }
  }

  .xout-help {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 14px 16px;
    color: #6c757d;
    font-size: 13px;
  }

  .xout-help strong {
    color: #1D1D1D;
  }
</style>

@if (Session::has('error'))
  <div class="alert alert-danger mt-2" role="alert">{{ Session::get('error') }}</div>
@endif

<div class="fbox-shadow mt-2 fd-card respo-margin-top xout-filters">
  <div class="card-body">
    <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-1">
      {{ __('X-Out Lookup') }}
    </h5>
    <p style="color:#6c757d;font-size:14px;margin-bottom:16px;">
      {{ __('Preview cash collections for a date range and optional cashier.') }}
    </p>

    <div class="row align-items-end br-row-gap-2">
      <div class="col-lg-3 col-md-6">
        <label class="filter-label" for="start_date">{{ __('Start Date') }}</label>
        <input type="date" name="start_date" id="start_date"
          class="form-control filter-control"
          value="{{ $start_date ?? now()->startOfMonth()->toDateString() }}" required>
      </div>

      <div class="col-lg-3 col-md-6">
        <label class="filter-label" for="end_date">{{ __('End Date') }}</label>
        <input type="date" name="end_date" id="end_date"
          class="form-control filter-control"
          value="{{ $end_date ?? now()->endOfMonth()->toDateString() }}" required>
      </div>

      <div class="col-lg-3 col-md-6">
        <label class="filter-label" for="user_id">{{ __('Cashier') }}</label>
        <select name="user_id" id="user_id" class="form-control filter-control">
          <option value="0" {{ (string) ($selected_user_id ?? '0') === '0' ? 'selected' : '' }}>
            {{ __('All Cashiers') }}
          </option>
          @foreach ($user as $u)
            <option value="{{ $u->id }}" {{ (string) ($selected_user_id ?? '0') === (string) $u->id ? 'selected' : '' }}>
              {{ $u->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-lg-3 col-md-6">
        <label class="filter-label d-none d-lg-block">&nbsp;</label>
        <div class="filter-actions">
          <button type="button" onclick="openInvoice()" class="btn btn-sm btn-primary btn_dark-x">
            <i class="fa fa-eye"></i> {{ __('Preview') }}
          </button>
          <a href="{{ route('x.out') }}" class="btn btn-sm btn-secondary brownz-btn-yellow-d">
            <i class="fa fa-refresh"></i> {{ __('Reset') }}
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="fbox-shadow mt-2 fd-card">
  <div class="card-body">
    <div class="xout-help">
      <div><strong>{{ __('How it works') }}</strong></div>
      <div class="mt-1">
        {{ __('X-Out totals payments taken in the selected period (cash is net of change). Refunds are matched to the cashier who processed them. Choose Preview to open the printable report.') }}
      </div>
    </div>
  </div>
</div>

<script>
  function openInvoice() {
    var user_id = $('#user_id').val();
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();

    if (!start_date || !end_date) {
      alert('{{ __('Please select start date and end date.') }}');
      return;
    }

    if (end_date < start_date) {
      alert('{{ __('End date must be on or after start date.') }}');
      return;
    }

    var url = "{{ route('print.xout.bill') }}";
    var goto_url = url
      + '?start_date=' + encodeURIComponent(start_date)
      + '&end_date=' + encodeURIComponent(end_date)
      + '&user_id=' + encodeURIComponent(user_id);

    window.open(
      goto_url,
      "",
      "toolbar=no,status=no,menubar=no,location=center,scrollbars=yes,resizable=yes,height=640,width=480"
    );
  }
</script>
@endsection
