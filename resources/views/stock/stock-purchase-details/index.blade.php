@extends('layouts.vcapp')

<style>
  .purchase-details-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .purchase-details-header .purchase-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .purchase-details-header .purchase-meta {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
  }

  .purchase-details-header .status-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .purchase-details-header .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
  }

  .purchase-details-header .status-pill.is-amount {
    color: #1D1D1D;
    background: #F0F0F3;
  }

  .purchase-details-header .status-pill.is-date {
    color: #1e6789;
    background: rgba(82, 168, 217, 0.18);
  }

  .purchase-details-header .status-pill.is-return {
    color: #8A6D00;
    background: rgba(230, 205, 127, 0.35);
  }
</style>

@section('content')
@if(!empty($purchase_info) && !empty($purchase_info->purchase_id))
  @php
    $isReturn = !empty($purchase_info->parent_id) || ($purchase_info->purchase_type ?? '') === 'refund';
    $amount = (float) ($purchase_info->total_purchase_amount ?? 0);
  @endphp
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="purchase-details-header">
        <div>
          <h5 class="purchase-title">
            {{ $purchase_info->purchase_ref_no ?: (__('Purchase') . ' #' . $purchase_info->purchase_id) }}
          </h5>
          <p class="purchase-meta">
            {{ optional($purchase_info->vendor)->name ?? __('Purchase') }}
            @if(!empty($purchase_info->purchase_invoice_no))
              · {{ __('Invoice') }}: {{ $purchase_info->purchase_invoice_no }}
            @endif
          </p>
        </div>
        <div class="status-pills">
          @if($isReturn)
            <span class="status-pill is-return">{{ __('Return') }}</span>
          @endif
          <span class="status-pill is-amount">
            {{ __('Amount') }}: {{ number_format($amount, 2) }}
          </span>
          @if(!empty($purchase_info->purchase_date))
            <span class="status-pill is-date">
              {{ \Carbon\Carbon::parse($purchase_info->purchase_date)->format('d M Y') }}
            </span>
          @endif
        </div>
      </div>
    </div>
  </div>
@else
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="purchase-details-header">
        <div>
          <h5 class="purchase-title">{{ __('Create Purchase') }}</h5>
          <p class="purchase-meta">{{ __('Add supplier, products, and payment details') }}</p>
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
    ['text' => __('Purchases'), 'url' => route('tab.stock.stock.purchase.list')],
    ['text' => !empty($purchase_info->purchase_id)
        ? ($purchase_info->purchase_ref_no ?: (__('Purchase') . ' #' . $purchase_info->purchase_id))
        : __('New Purchase')],
]);
?>;
    });
</script>
@endif
@endpush
