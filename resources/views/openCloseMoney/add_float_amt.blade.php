@extends('layouts.vcapp')

@section('content')
<style>
  .float-amt-page .page-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .float-amt-page .page-hint {
    margin: 0 0 16px;
    color: #6c757d;
    font-size: 14px;
  }

  .float-amt-page .status-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .float-amt-page .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
  }

  .float-amt-page .status-pill.is-open {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .float-amt-page .status-pill.is-closed {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  .float-amt-page .summary-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
    margin-top: 4px;
  }

  .float-amt-page .summary-card {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 14px 16px;
  }

  .float-amt-page .summary-card .label {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 4px;
  }

  .float-amt-page .summary-card .value {
    font-size: 1.05rem;
    font-weight: 700;
    color: #1D1D1D;
    word-break: break-word;
  }

  .float-amt-page .action-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
  }

  @media (max-width: 991px) {
    .float-amt-page .summary-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 575px) {
    .float-amt-page .summary-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="float-amt-page">
  @if (Session::has('error'))
    <div class="alert alert-danger mt-2" role="alert">{{ Session::get('error') }}</div>
  @endif
  @if (Session::has('message'))
    <div class="alert alert-success mt-2" role="alert">{{ Session::get('message') }}</div>
  @endif

  @if ($open_amount == true)
    <div class="fbox-shadow mt-2 fd-card respo-margin-top">
      <div class="card-body">
        <div class="status-header mb-3">
          <div>
            <h5 class="page-title">{{ __('Open Register') }}</h5>
            <p class="page-hint mb-0">{{ __('No active float session. Enter an opening amount to start the register.') }}</p>
          </div>
          <span class="status-pill is-closed">{{ __('Register Closed') }}</span>
        </div>

        <div class="brw-form-la">
          <form method="post" action="{{ route('float_amt') }}">
            @csrf
            <div class="row">
              <div class="form-group col-md-6">
                <label for="amonut">{{ __('Opening Amount') }}</label>
                <input required autocomplete="off" type="number" step="0.01" min="0" name="amonut"
                  placeholder="{{ __('Amount') }}" id="amonut" class="form-control" value="">
              </div>
              <input type="hidden" value="{{ $type }}" name="type">
            </div>
            <div class="action-row">
              <button type="submit" id="open_save_btn" class="btn btn-primary btn_dark-x">
                <i class="fa fa-check"></i> {{ __('Open Register') }}
              </button>
              <a href="{{ route('re.print.bill') }}" class="btn brownz-btn-yellow-d">
                <i class="fa fa-history"></i> {{ __('Z-Out Lookup') }}
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif

  @if ($amonut_close_btn == true)
    <div class="fbox-shadow mt-2 fd-card respo-margin-top">
      <div class="card-body">
        <div class="status-header mb-3">
          <div>
            <h5 class="page-title">{{ __('Register Session') }}</h5>
            <p class="page-hint mb-0">{{ __('Your till is open. Review the session details below before closing.') }}</p>
          </div>
          <span class="status-pill is-open">{{ __('Register Open') }}</span>
        </div>

        @if (!empty($session_summary))
          <div class="summary-grid mb-3">
            <div class="summary-card">
              <span class="label">{{ __('Cashier') }}</span>
              <span class="value">{{ $session_summary['cashier_name'] }}</span>
            </div>
            <div class="summary-card">
              <span class="label">{{ __('Float ID') }}</span>
              <span class="value">#{{ $session_summary['float_amt_id'] }}</span>
            </div>
            <div class="summary-card">
              <span class="label">{{ __('Open Amount') }}</span>
              <span class="value">{{ $session_summary['open_amount'] }}</span>
            </div>
            <div class="summary-card">
              <span class="label">{{ __('Opened At') }}</span>
              <span class="value" style="font-size: 0.95rem;">{{ $session_summary['opened_at'] ?: '—' }}</span>
            </div>
          </div>
        @endif

        <div class="action-row">
          <form method="post" action="{{ route('close.float_button') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary btn_dark-x">
              <i class="fa fa-lock"></i> {{ __('Close Register') }}
            </button>
          </form>
          <a href="{{ route('re.print.bill') }}" class="btn brownz-btn-yellow-d">
            <i class="fa fa-history"></i> {{ __('Z-Out Lookup') }}
          </a>
        </div>
      </div>
    </div>
  @endif

  @if ($is_denomination == true)
    @include('openCloseMoney.partials.close-register-form', [
        'is_force_close' => false,
        'page_title' => __('Close Register'),
        'page_hint' => __('Enter cash denomination counts to close the register.'),
        'redirect_after_close' => url('/float_amt'),
        'float_amt_id' => $float_amonunt[0]->float_amt_id ?? null,
        'cashier_name' => $session_summary['cashier_name'] ?? (Auth::user()->name ?? null),
    ])
  @endif
</div>
@endsection
