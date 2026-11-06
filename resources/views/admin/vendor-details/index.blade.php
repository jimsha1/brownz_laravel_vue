@extends('layouts.vcapp')

@section('content')
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
    ['text' => __('Vendors'), 'url' => route('tab.admin.vendor.list')],
    ['text' => $vendor_info->name],
]);
?>;
    });
</script>
@endif
@endpush
