@extends('layouts.vcapp')

@section('content')
<style>
  .acl-user-details-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .acl-user-details-header .user-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .acl-user-details-header .user-meta {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
  }

  .acl-user-details-header .user-meta span + span::before {
    content: "·";
    margin: 0 8px;
    color: #b0b0b0;
  }

  .acl-user-details-header .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    text-transform: capitalize;
    white-space: nowrap;
  }

  .acl-user-details-header .status-pill.is-active {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .acl-user-details-header .status-pill.is-inactive,
  .acl-user-details-header .status-pill.is-blocked,
  .acl-user-details-header .status-pill.is-deleted {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  .acl-user-details-header .status-pill.is-pending {
    color: #8a6d00;
    background: rgba(230, 205, 127, 0.45);
  }
</style>

@if(!empty($user_info))
  @php
    $status = strtolower((string) ($user_info->user_status ?: 'pending'));
  @endphp
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="acl-user-details-header">
        <div>
          <h5 class="user-title">{{ $user_info->name }}</h5>
          <p class="user-meta">
            <span>{{ $user_info->username ?: '—' }}</span>
            @if($user_info->email)
              <span>{{ $user_info->email }}</span>
            @endif
            @if($user_info->mobile)
              <span>{{ $user_info->mobile }}</span>
            @endif
          </p>
        </div>
        <span class="status-pill is-{{ $status }}">{{ $status ?: __('unknown') }}</span>
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
    ['text' => __('Users'), 'url' => route('tab.acl.user.list')],
    ['text' => $user_info ? $user_info->name : __('User')],
]);
?>;
    });
</script>
@endif
@endpush
