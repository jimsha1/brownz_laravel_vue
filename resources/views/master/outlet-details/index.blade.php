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
    ['text' => __('Outlets'), 'url' => route('tab.master.outlet.list')],
    ['text' => $outlet_info->name],
]);
?>;
    });
</script>
@endif
@endpush
