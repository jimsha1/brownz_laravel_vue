@extends('layouts.vcapp')

@section('content')
<style>
  .shift-details-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .shift-details-header .shift-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .shift-details-header .shift-meta {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
  }

  .shift-details-header .status-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .shift-details-header .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
  }

  .shift-details-header .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .shift-details-header .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }
</style>

@if(!empty($shift_info))
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="shift-details-header">
        <div>
          <h5 class="shift-title">{{ $shift_info->title }}</h5>
          <p class="shift-meta">
            {{ $shift_info->start_time ?: '—' }}
            {{ __('to') }}
            {{ $shift_info->end_time ?: '—' }}
          </p>
        </div>
        <div class="status-pills">
          @php
            $shiftActive = (int) ($shift_info->getAttributes()['is_active'] ?? $shift_info->is_active ?? 0) === 1;
          @endphp
          @if($shiftActive)
            <span class="status-pill is-active">{{ __('Active') }}</span>
          @else
            <span class="status-pill is-inactive">{{ __('Inactive') }}</span>
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
    ['text' => __('Shifts'), 'url' => route('tab.master.shift.list')],
    ['text' => $shift_info ? $shift_info->title : __('New Shift')],
]);
?>;
    });
</script>
@endif
@endpush
