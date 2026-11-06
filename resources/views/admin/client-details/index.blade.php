@extends('layouts.vcapp')

@section('content')
<style>
  .client-details-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .client-details-header .client-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .client-details-header .client-meta {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
  }

  .client-details-header .status-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .client-details-header .status-pill {
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

  .client-details-header .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .client-details-header .status-pill.is-inactive,
  .client-details-header .status-pill.is-blocked,
  .client-details-header .status-pill.is-deleted {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  .client-details-header .status-pill.is-pending {
    color: #9A6700;
    background: rgba(230, 205, 127, 0.35);
  }

  .client-details-header .status-pill.is-credit {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  .client-details-header .status-pill.is-clear {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }
</style>

@if(!empty($client_info))
  @php
    $status = $client_info->user_status ?: 'inactive';
    $balance = (float) ($client_info->balance ?? 0);
  @endphp
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="client-details-header">
        <div>
          <h5 class="client-title">{{ $client_info->name ?? __('Client') }}</h5>
          <p class="client-meta">
            @if(!empty($client_info->mobile))
              {{ $client_info->mobile }}
            @endif
            @if(!empty($client_info->email))
              @if(!empty($client_info->mobile)) · @endif
              {{ $client_info->email }}
            @endif
          </p>
        </div>
        <div class="status-pills">
          <span class="status-pill is-{{ $status }}">{{ __(ucfirst($status)) }}</span>
          <span class="status-pill {{ $balance > 0 ? 'is-credit' : 'is-clear' }}">
            {{ __('Balance') }}: {{ number_format($balance, 2) }}
          </span>
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
    ['text' => __('Clients'), 'url' => route('tab.admin.client.list')],
    ['text' => $client_info->name ?? __('Client')],
]);
?>;
    });
</script>
@endif
@endpush
