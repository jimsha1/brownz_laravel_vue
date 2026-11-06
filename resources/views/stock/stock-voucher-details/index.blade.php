@extends('layouts.vcapp')

<style>
  .voucher-details-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .voucher-details-header .voucher-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .voucher-details-header .voucher-meta {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
  }

  .voucher-details-header .status-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .voucher-details-header .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
  }

  .voucher-details-header .status-pill.is-type {
    color: #1D1D1D;
    background: #F0F0F3;
  }

  .voucher-details-header .status-pill.is-booking {
    color: #9A6700;
    background: rgba(230, 205, 127, 0.35);
  }
</style>

@section('content')
@if(!empty($stock_voucher_info) && !empty($stock_voucher_info->id))
  @php
    $isBooking = method_exists($stock_voucher_info, 'isBookingVoucher') && $stock_voucher_info->isBookingVoucher();
  @endphp
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="voucher-details-header">
        <div>
          <h5 class="voucher-title">{{ $stock_voucher_info->voucher_no ?: __('Voucher') }}</h5>
          <p class="voucher-meta">
            @if(!empty($stock_voucher_info->date))
              {{ \Carbon\Carbon::parse($stock_voucher_info->date)->format('d M Y') }}
            @endif
            @if(!empty($stock_voucher_info->description))
              · {{ \Illuminate\Support\Str::limit($stock_voucher_info->description, 80) }}
            @endif
          </p>
        </div>
        <div class="status-pills">
          @if(!empty($stock_voucher_info->voucher_type))
            <span class="status-pill is-type">{{ $stock_voucher_info->voucher_type }}</span>
          @endif
          @if($isBooking)
            <span class="status-pill is-booking">{{ __('Booking (view only)') }}</span>
          @endif
        </div>
      </div>
    </div>
  </div>
@else
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="voucher-details-header">
        <div>
          <h5 class="voucher-title">{{ __('Create Voucher') }}</h5>
          <p class="voucher-meta">{{ __('Create a balanced debit / credit voucher') }}</p>
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
    ['text' => __('Voucher'), 'url' => route('tab.stock.stock.voucher.list')],
    ['text' => ($stock_voucher_info && $stock_voucher_info->voucher_no)
        ? $stock_voucher_info->voucher_no
        : __('New Voucher')],
]);
?>;
    });
</script>
@endif
@endpush
