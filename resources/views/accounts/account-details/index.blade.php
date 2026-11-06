@extends('layouts.vcapp')

@section('content')
<style>
  .account-details-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .account-details-header .account-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .account-details-header .account-meta {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
    text-transform: capitalize;
  }

  .account-details-header .status-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .account-details-header .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
    text-transform: capitalize;
  }

  .account-details-header .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .account-details-header .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  .account-details-header .status-pill.is-balance {
    color: #1D1D1D;
    background: #F0F0F3;
  }

  .account-details-header .status-pill.is-system {
    color: #8A6D00;
    background: rgba(230, 205, 127, 0.35);
  }
</style>

@if(!empty($account_info))
  @php
    $isActive = (int) ($account_info->is_active ?? 0) === 1;
    $isSystem = (int) ($account_info->is_system_defined ?? 0) === 1;
    $balance = (float) ($account_info->balance ?? 0);
    $metaParts = array_filter([
      $account_info->code ?? null,
      $account_info->type ?? null,
      $account_info->category ?? null,
    ]);
  @endphp
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="account-details-header">
        <div>
          <h5 class="account-title">{{ $account_info->name ?? __('Account') }}</h5>
          @if(count($metaParts))
            <p class="account-meta">{{ implode(' · ', $metaParts) }}</p>
          @endif
        </div>
        <div class="status-pills">
          <span class="status-pill {{ $isActive ? 'is-active' : 'is-inactive' }}">
            {{ $isActive ? __('Active') : __('Inactive') }}
          </span>
          <span class="status-pill is-balance">
            {{ __('Balance') }}: {{ number_format($balance, 2) }}
          </span>
          @if($isSystem)
            <span class="status-pill is-system">{{ __('System Defined') }}</span>
          @endif
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
    ['text' => __('Accounts'), 'url' => route('tab.accounts.account.list')],
    ['text' => $account_info->name ?? __('Account')],
]);
?>;
    });
</script>
@endif
@endpush
