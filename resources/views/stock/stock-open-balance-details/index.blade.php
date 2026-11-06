@extends('layouts.vcapp')

<style>
  .open-balance-details-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .open-balance-details-header .ob-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .open-balance-details-header .ob-meta {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
  }

  .open-balance-details-header .status-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .open-balance-details-header .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
  }

  .open-balance-details-header .status-pill.is-amount {
    color: #1D1D1D;
    background: #F0F0F3;
  }

  .open-balance-details-header .status-pill.is-date {
    color: #1e6789;
    background: rgba(82, 168, 217, 0.18);
  }
</style>

@section('content')
@if(!empty($open_balance_info) && !empty($open_balance_info->open_balance_id))
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="open-balance-details-header">
        <div>
          <h5 class="ob-title">
            {{ $open_balance_info->account->name ?? __('Opening Balance') }}
          </h5>
          <p class="ob-meta">
            {{ __('Opening Balance') }} #{{ $open_balance_info->open_balance_id }}
          </p>
        </div>
        <div class="status-pills">
          <span class="status-pill is-amount">
            {{ __('Amount') }}: {{ number_format((float) ($open_balance_info->amount ?? 0), 2) }}
          </span>
          @if(!empty($open_balance_info->opening_date))
            <span class="status-pill is-date">
              {{ \Carbon\Carbon::parse($open_balance_info->opening_date)->format('d M Y') }}
            </span>
          @endif
        </div>
      </div>
    </div>
  </div>
@else
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="open-balance-details-header">
        <div>
          <h5 class="ob-title">{{ __('Create Opening Balance') }}</h5>
          <p class="ob-meta">{{ __('Record a new account opening balance') }}</p>
        </div>
      </div>
    </div>
  </div>
@endif

@include('global-ajax-tab-frame.tabbed-frame')
@endsection

@push('inline-scripts')
@if($activate_via_js === true)
<script>
    jQuery(function ($) {
        $("#{{ 'vtab-' . $tab_name }}").tab("show");

        v_breadcrumb.breadcrumb_links = <?php
echo json_encode([
    ['text' => __('Dashboard'), 'url' => url('/')],
    ['text' => __('Opening Balances'), 'url' => route('tab.stock.open.balance.list')],
    ['text' => !empty($open_balance_info->open_balance_id)
        ? (($open_balance_info->account->name ?? __('Opening Balance')).' #'.$open_balance_info->open_balance_id)
        : __('New Opening Balance')],
]);
?>;
    });
</script>
@endif
@endpush
