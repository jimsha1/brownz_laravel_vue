@extends('layouts.vcapp')

@section('content')
<style>
  .acl-role-details-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .acl-role-details-header .role-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0 0 4px;
  }

  .acl-role-details-header .role-desc {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
    max-width: 640px;
  }

  .acl-role-details-header .type-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    white-space: nowrap;
  }

  .acl-role-details-header .type-pill.is-system {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  .acl-role-details-header .type-pill.is-custom {
    color: #1D1D1D;
    background: #F0F0F3;
  }
</style>

@if(!empty($acl_role_info))
  <div class="fbox-shadow mt-2 fd-card respo-margin-top">
    <div class="card-body">
      <div class="acl-role-details-header">
        <div>
          <h5 class="role-title">{{ $acl_role_info->name }}</h5>
          <p class="role-desc">{{ $acl_role_info->description ?: __('No description') }}</p>
        </div>
        @if((int) $acl_role_info->system_defined === 1)
          <span class="type-pill is-system">{{ __('System') }}</span>
        @else
          <span class="type-pill is-custom">{{ __('Custom') }}</span>
        @endif
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
    ['text' => __('Roles'), 'url' => route('tab.acl.role.list')],
    ['text' => $acl_role_info ? $acl_role_info->name : __('New Role')],
]);
?>;
    });
</script>
@endif
@endpush
