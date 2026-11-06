@extends('layouts.vcapp')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4/dist/flatpickr.min.css">


<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'UA-83201567-1');
</script>
<script>
    var vc_moment_date_offset = new Date("2021-10-05T17:28:50.403+03:00").getTime() - Date.now();
    var vc_jscalendar_moment_format = "DD/MM/YYYY";
</script>
<style>
    .example::-webkit-scrollbar {
        display: none;
    }

    .example {
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }

    [dir="rtl"] .float-right {
        float: left !important
    }

    [dir="rtl"] .float-left {
        float: right !important
    }

    [dir="rtl"] .text-right {
        text-align: left !important
    }

    [dir="rtl"] .text-left {
        text-align: right !important
    }

    [dir="rtl"] .input-group>.form-control:not(:last-child),
    [dir="rtl"] .input-group>.custom-select:not(:last-child) {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-top-right-radius: .25rem;
        border-bottom-right-radius: .25rem;
    }

    [dir="rtl"] .input-group>.input-group-append>.btn,
    [dir="rtl"] .input-group>.input-group-append>.input-group-text,
    [dir="rtl"] .input-group>.input-group-prepend:not(:first-child)>.btn,
    [dir="rtl"] .input-group>.input-group-prepend:not(:first-child)>.input-group-text,
    [dir="rtl"] .input-group>.input-group-prepend:first-child>.btn:not(:first-child),
    [dir="rtl"] .input-group>.input-group-prepend:first-child>.input-group-text:not(:first-child) {
        border-top-left-radius: .25rem;
        border-bottom-left-radius: .25rem;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

</style>

<style>
    td {
        font-size: 12px;
    }

    .form-group {
        margin-bottom: 0rem;
    }

    .anim-active td {
        /*background-color:#c6c6df;*/
        animation: activepulse 3s;
    }

    @keyframes activepulse {

        0%,
        100% {
            background-color: inherit;
        }

        50% {
            background-color: #9fd6f0;
        }
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #76c1ee;
    }

</style>
<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/style.css') }}" />
<link rel="stylesheet" href="{{ asset('css/responsive.css') }}" />

<style>
    .calendar-box {}

    .buttons-container {
        cursor: pointer;
    }

    .week {
        overflow: hidden;
    }

    .week.highlight {
        background: #efefef;
    }

    .day {
        display: inline-block;
        float: left;
        width: 14.28%;
        text-align: center;
        cursor: pointer;
        box-sizing: border-box;
    }

    .day.disabled span {
        color: #aaa;
    }

    .day.today span {
        font-weight: bold;
    }

    .months-container.hidden,
    .weeks-container.hidden {
        display: none;
    }

    .months-wrapper {
        overflow: hidden;
    }

    .months-wrapper .month {
        display: inline-block;
        float: left;
        width: 25%;
        text-align: center;
        cursor: pointer;
    }

    .months-wrapper .month.one-third {
        width: 33.33%;
    }

    .buttons-container {
        display: -webkit-box;
        display: -moz-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .buttons-container .label-container {
        display: inline-block;
        -webkit-box-flex: 1;
        -moz-box-flex: 1;
        -webkit-flex: 1;
        -ms-flex: 1;
        flex: 1;
        text-align: center;
        text-transform: uppercase;
        font-weight: bold;
    }

    .year-dropdown {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        border: none;
        font-size: inherit;
        font-weight: inherit;
        font-family: inherit;
        padding: 5px 20px;
    }

    .prev-button,
    .next-button {
        background: transparent;
        border: none;
        padding: 10px;
    }

    .week {
        margin: 10px 0;
    }

    .week.highlight {
        border-radius: 5px;
    }

    .weeks-wrapper.header {
        border-bottom: 1px solid #eee;
    }

    .week .day.header {
        font-weight: bold;
        text-transform: uppercase;
        font-size: 120%;
    }

    .day span {
        display: inline-block;
        width: 40px;
        height: 40px;
        line-height: 40px;
        border-radius: 50%;
        vertical-align: middle;
    }

    .day.today span {
        position: relative;
        display: inline-block;
        font-size: 110%;
    }

    /* weekend */

    .week:not(.start-on-monday) .day:first-child,
    .week:not(.start-on-monday) .day:last-child {
        color: orange;
    }

    /* sunday */

    .week:not(.start-on-monday) .day:first-child {
        color: red;
    }

    /* start on monday - weekend */
    /* .week.start-on-monday .day:nth-child(6), .week.start-on-monday .day:last-child { color: orange; } */
    /*
 start on monday - sunday */
    /* .week.start-on-monday .day:last-child { color: red; } */

    .day.today span::after {
        content: "";
        position: absolute;
        bottom: 7px;
        left: 50%;
        transform: translateX(-50%);
        border-bottom: 2px solid orange;
        width: 10px;
        height: 1px;
    }

    .day.sunday span {
        color: #ff8a80;
    }

    .week .day.highlight span {
        color: #52a8d9;
    }

    .week .day.selected span {
        background: #52a8d9;
        color: white;
    }

    .week .day[disabled="disabled"] span {
        color: #aaa;
        cursor: not-allowed;
    }

    .months-wrapper .month span {
        display: inline-block;
        padding: 10px;
        text-transform: capitalize;
        margin-bottom: 10px;
    }

    .special-buttons {
        text-align: center;
        border-top: 1px solid #eee;
        padding-top: 10px;
    }

    .today-button {
        margin: 0 auto;
        background: transparent;
        border: none;
        padding: 5px;
    }

</style>
<!-- End Calendar -->

<style>
    .gj-picker-bootstrap {
        display: block !important;
    }

    .modal .service-tab {
        min-height: 400px;
        max-height: 400px;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 0 15px;
        margin-top: 15px;
    }

    .scrtabs-tabs-fixed-container,
    .scrtabs-tabs-movable-container {
        width: auto !important;
    }

    .modal-dialog {
        max-width: 805px;
    }

    .custom-selection input~.checkmark {
        left: auto;
        right: 0;
        top: 15px;
    }

    .tab-services .nav-link.active,
    .custom-selection input:checked~.checkmark {
        background-color: #52a8d9;
    }

    .service-list-wrapper .custom-selection .checkmark,
    .sub-services i {
        border-color: #52a8d9;
        color: #52a8d9;
    }

    .custom-selection {
        font-size: 14px;
        padding: 0;
        margin: 15px 0;
    }

    /* .custom-selection .service-item-list-info {
         justify-content: flex-start
     } */
    /* .custom-selection .checkmark:after {
         left: 10px;
         top: 2px;

     } */

    .custom-selection img {
        max-width: 70px;
        margin-right: 5px;
    }

    .beautician-custom-radio {
        border: 1px solid #52a8d9;
        border-radius: 5px;
    }

    .beautician-custom-radio p {
        margin: 0;
        /* padding: 0 0 0 15px; */
    }
    .custom-selection.beautician-custom-radio input~.checkmark {
        display: none;
    }

    .custom-selection.beautician-custom-radio input~.checkmark {
        left: auto;
        right: 10px;
        top: 10px;
        display: block;
    }

    .custom-selection.beautician-custom-radio .checkmark:after {
        left: 9px;
        top: 3px;
        width: 8px;
        height: 15px;
    }

    .reshudil-beautician-sds.reds-c.beautician-custom-radio .checkmark:after {
        display: none;
    }

    .reshudil-beautician-sds.reds-c input:checked~.checkmark:after{
        display: block;
    }
    .reshudil-beautician-sds.reds-c input:checked~.checkmark::before{
        background-color: #E6CD7F;
    }

    .custom-selection.beautician-custom-radio .service-item-list-info {
        justify-content: flex-start;
    }

    .time-slot-wrapper {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .time-slot-wrapper label {
        flex: 0 0 30%;
    }

    .slot-custom-radio {
        border: 1px solid #52a8d9;
        border-radius: 2px;
        color: #52a8d9;
    }

    .slot-custom-radio p {
        margin: 0;
        padding: 5px 15px;
        width: 100%;
        text-align: center;
    }

    .custom-selection.slot-custom-radio {
        margin: 8px 5px 0 5px;
    }

    .custom-selection.slot-custom-radio input:checked~.service-item-list-info {
        background: #52a8d9;
        color: #ffffff;
    }

    .time-slot {
        margin: 15px 0;
    }

    .nav-tabs .nav-link {
        color: #212529;
    }

    .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active {
        color: #ffffff;
        background-color: #52a8d9;
        border-color: #dee2e6 #dee2e6 #fff;
    }

    .sticky-table {
        height: 300px;
        overflow-x: scroll;
    }

    .nav-tabs .nav-link.active:focus {
        background: #1D1D1D;
        border-color: #1D1D1D;
        border-bottom-color: #fff;
    }

    /* .custom-selection .checkmark:after {
         width: 7px;
         height: 14px;
     } */

     .brow-tab-item .nav-link.active {
        background-color: #1D1D1D !important;
        color: #fff !important;
    }
    .vd66f-ng-t.active{
    background-color: #1D1D1D !important;
  }

  .tab-panel-br-add-service {
    background-color: #F0F0F3;
    border-radius: 24px;
    padding-top: 10px !important;
}

.re-shedservice-item-list-info{
    width: 100%;
    background-color: #fff;
    border-radius: 15px;
    padding: 15px;
    cursor: pointer;
    padding-right: 60px;
    gap: 12px;
    justify-content: flex-start;
    display: flex;
    align-items: center;
}
.re-shedservice-item-list-info .checkmark{
    background-color: transparent !important;
    top: 50% !important;
    left: auto !important;
    right: 25px !important;
    transform: translateY(-50%) !important;
}
.re-shedservice-item-list-info .checkmark::before{
    content: "";
    position: absolute;
    right: -10px;
    top: 50%;
    transform: translateY(-50%);
    width: 45px;
    height: 45px;
    border: none;
    border-radius: 50%;
    background: #F0F0F3;
    background-image: none;
    background-position: center;
    background-repeat: no-repeat;
    transition: border-color 0.3s, background 0.3s;
}
.re-shedservice-item-list-info .checkmark::after{
    display: block;
    left: 8px;
    top: 1px;
    width: 10px;
    height: 19px;
    border-width: 0 4px 4px 0;
}
    

</style>


<div class="fbox-shadow mt-2 fd-card respo-margin-top">
  <div class="card-body">
    <form class="voco-ajax-form tab-search-form" method="get" action="{{ $tab_meta_data[$tab_name]['tab_url'] }}">
      <input type="hidden" id="tab_search" name="tab_search" value="0">

      <div class="row align-items-center br-row-gap-2">
        <div class="col-lg-3 col-sm-8">
            <label for="start_date">{{ __('Start Date') }}</label>
            <div class="search-box-bar-container jus-heigh-search-b">
                <input type="date" id="start_date" class="form-control brawnz-search-bar-in"
                    name="start_date"
                    value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}">
            </div>
        </div>
        <div class="col-lg-3 col-sm-8">
            <label for="end_date">{{ __('End Date') }}</label>
            <div class="search-box-bar-container jus-heigh-search-b">
                <input type="date" id="end_date" class="form-control brawnz-search-bar-in"
                    name="end_date"
                    value="{{ request('end_date', now()->endOfMonth()->toDateString()) }}">
            </div>
        </div>
        <div class="col-lg-3 col-sm-8">
            <label for="search">{{ __('Search') }}</label>
            <div class="search-box-bar-container jus-heigh-search-b ">
                <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
                <input type="text" id="search" class="form-control brawnz-search-bar-in" name="search"
                    value="{{ request('search') }}"
                    placeholder="{{ __('Search...') }}">
            </div>
        </div>
        <div class="col-lg-3 col-sm-4">
            <label class="d-block">&nbsp;</label>
            <button type="submit" class="btn btn-sm btn-primary btn_dark-x">
              {{ __('Search') }}
            </button>
          </div>
      </div>
    </form>
  </div>
</div>

@include('global-ajax-tab-frame.tabbed-frame')

@endsection

@push('inline-scripts')
@if($activate_via_js === true)
<script>
    jQuery(function ($) {
        $("#{{ 'vtab-' . $tab_name }}").tab("show");
    });
</script>
@endif
@endpush
