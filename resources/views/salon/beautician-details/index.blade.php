@extends('layouts.vcapp')

@section('content')
<style>
  .beautician-details-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .beautician-details-header .header-left {
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .beautician-details-header .header-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    object-fit: cover;
    background: #F0F0F3;
  }

  .beautician-details-header .beautician-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .beautician-details-header .beautician-meta {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
  }

  .beautician-details-header .status-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .beautician-details-header .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
  }

  .beautician-details-header .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .beautician-details-header .status-pill.is-inactive {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }
</style>

@if(!empty($beautician_info))
  @php
    $isActive = ($beautician_info->status ?? null) === \App\Models\Beautician::STATUS_ACTIVE
      && (($beautician_info->employee_status ?? 'active') === 'active')
      && (($beautician_info->user_status ?? 'active') === 'active');
    $photoUrl = beautician_photo_url($beautician_info->profile_photo_path ?? null, 56, 56, 1);
  @endphp
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="beautician-details-header">
        <div class="header-left">
          <img class="header-avatar" src="{{ $photoUrl }}" alt="">
          <div>
            <h5 class="beautician-title">{{ $beautician_info->name ?? __('Beautician') }}</h5>
            <p class="beautician-meta">
              {{ $beautician_info->beautician_code ?? '—' }}
              @if(!empty($beautician_info->mobile))
                · {{ $beautician_info->mobile }}
              @endif
            </p>
          </div>
        </div>
        <div class="status-pills">
          @if($isActive)
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
    ['text' => __('Beauticians'), 'url' => route('tab.salon.beautician.list')],
    ['text' => $beautician_info->beautician_code ?? __('Beautician')],
]);
?>;
    });
</script>
@endif
@endpush
