<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
@php
    $refund_permisson = 0;
@endphp
@can('service_type')
    @php
        $refund_permisson = 1;
    @endphp
@endcan
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

    /* Split Service modal */
    .split-modal-content {
        border-radius: 16px;
        overflow: hidden;
    }
    .split-summary-card {
        background: #f7f4ea;
        border: 1px solid #eadfb8;
        border-radius: 12px;
        padding: 14px 16px;
    }
    .split-summary-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #8a7d5a;
        margin-bottom: 2px;
    }
    .split-summary-service {
        font-size: 16px;
        font-weight: 700;
        color: #222;
    }
    .split-summary-amount,
    .split-summary-share {
        font-size: 20px;
        font-weight: 700;
        color: #1f1f1f;
        line-height: 1.2;
    }
    .split-section-title {
        font-size: 14px;
    }
    .split-count-badge {
        background: #222;
        color: #E6CD7F;
        border-radius: 999px;
        padding: 3px 10px;
        font-size: 12px;
        font-weight: 600;
    }
    .split-rows-head {
        display: grid;
        grid-template-columns: 1fr 110px 36px;
        gap: 8px;
        padding: 0 4px 6px;
        font-size: 12px;
        color: #777;
        font-weight: 600;
    }
    .split-view-rows + .split-rows-head,
    #open-split-modal .split-rows-head {
        grid-template-columns: 1fr 110px;
    }
    .split-rows-body {
        max-height: 280px;
        overflow: auto;
    }
    .split-row {
        display: grid;
        grid-template-columns: minmax(180px, 1fr) 110px 36px;
        gap: 8px;
        align-items: center;
        margin-bottom: 8px;
        background: #fff;
        border: 1px solid #e8e8e8;
        border-radius: 10px;
        padding: 8px;
    }
    .split-row-readonly {
        grid-template-columns: minmax(180px, 1fr) 110px;
    }
    .split-beautician-select {
        border: 1px solid #ddd !important;
        border-radius: 8px !important;
        height: 38px !important;
        color: #222 !important;
        background: #fff !important;
        width: 100% !important;
        min-width: 180px !important;
        max-width: 100%;
        display: block !important;
        opacity: 1 !important;
        appearance: auto !important;
        -webkit-appearance: menulist !important;
        padding: 4px 8px !important;
        font-size: 13px !important;
        line-height: 1.3 !important;
        z-index: 2;
        position: relative;
    }
    .split-share-box {
        background: #222;
        color: #E6CD7F;
        border-radius: 8px;
        text-align: center;
        font-weight: 700;
        font-size: 13px;
        padding: 8px 6px;
        white-space: nowrap;
        position: relative;
    }
    .split-share-box .price-field {
        display: none !important;
    }
    .split-remove-btn {
        border: 0;
        background: transparent;
        color: #c0392b;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        cursor: pointer;
    }
    .split-remove-btn:hover {
        background: rgba(192, 57, 43, 0.08);
    }
    .split-add-btn {
        width: 100%;
        border: 1px dashed #c9b56a;
        background: #fff8e1;
        color: #5c4d1e;
        border-radius: 10px;
        padding: 10px 12px;
        font-weight: 600;
        cursor: pointer;
    }
    .split-add-btn:hover {
        background: #E6CD7F;
    }
    .split-help-text {
        font-size: 12px;
        color: #777;
    }
    .split-empty-state {
        text-align: center;
        color: #888;
        padding: 18px 8px;
        border: 1px dashed #ddd;
        border-radius: 10px;
    }
    .split-action-btn {
        background: #E6CD7F !important;
        color: #0c0d0c !important;
        border-radius: 8px;
    }
    .split-action-btn.split-action-done {
        background: rgba(39, 174, 96, 0.18) !important;
        color: #1e7e34 !important;
        border: 1px solid rgba(39, 174, 96, 0.35);
    }
    .split-preview-chips {
        margin-top: 10px;
        clear: both;
    }
    .split-preview-title {
        font-size: 11px;
        color: #777;
        margin-bottom: 4px;
    }
    .split-preview-list {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .split-preview-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f4f4f4;
        border: 1px solid #e3e3e3;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
    }
    .split-preview-chip em {
        font-style: normal;
        font-weight: 700;
        color: #333;
    }

    .split-payment-breakdown {
        padding: 8px 0 4px;
        border-top: 1px dashed #e0e0e0;
    }

    .split-payment-breakdown-title {
        font-size: 12px;
        font-weight: 600;
        color: #555;
        margin-bottom: 6px;
    }

    .split-payment-breakdown-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .split-payment-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #eef6fb;
        border: 1px solid #cfe3f0;
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 12px;
    }

    .split-payment-chip em {
        font-style: normal;
        font-weight: 700;
        color: #1d1d1d;
    }

    .split-payment-chip-due {
        background: #fff8e6;
        border-color: #f0d78c;
    }

    .split-payment-modal-summary {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 14px;
    }
</style>

<style>
    .toCapitalFirst {
        text-transform: capitalize;
    }

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
            background-color: #1d1d1d;
        }
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #1d1d1d;
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
        color: #1d1d1d;
    }

    .week .day.selected span {
        background: #1d1d1d;
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

    .brow-tab-item .nav-link.active {
        background-color: #1D1D1D !important;
        color: #fff !important;
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
        background-color: #1d1d1d;
    }

    .service-list-wrapper .custom-selection .checkmark,
    .sub-services i {
        border-color: #1d1d1d;
        color: #1d1d1d;
    }

    .custom-selection {
        font-size: 14px;
        padding: 0;
        margin: 10px 0;
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
        left: auto;
        right: 10px;
        top: 10px;
    }

    .custom-selection.beautician-custom-radio input~.checkmark:lang(ar) {
        right: auto;
        left: 10px;
        top: 10px;
    }

    .custom-selection.beautician-custom-radio .checkmark:after {
        left: 9px;
        top: 3px;
        width: 8px;
        height: 15px;
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
        background: #1e6789;
        border-color: #c8ced3;
        border-bottom-color: #fff;
    }

    /* .custom-selection .checkmark:after {
         width: 7px;
         height: 14px;
     } */

    .bigdrop {
        width: 350px !important;
    }

    #select2-drop {
        width: 400px !important;
    }

    /* new ashmil design changes =================================================== */
    .nav-tabs .nav-item a {
        font-size: 16px;
        color: #1D1D1D;
        border-radius: 24px;
    }

    .nav-tabs .nav-item:first-child {
        background-color: #F0F0F3;
        border-bottom-right-radius: 0px;
        border-top-right-radius: 0px;
        border-bottom-left-radius: 24px;
        border-top-left-radius: 24px;
    }

    .nav-tabs .nav-item:nth-child(2) a {
        border-radius: 24px;
    }

    .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active:focus {
        color: #fff;
        background-color: #1D1D1D;
    }

    .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active:focus {
        color: #fff;
        background-color: #1D1D1D;
    }

    .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active {
        color: #fff;
        background-color: #1D1D1D;
    }

    .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active i {
        color: #E6CD7F;
    }

    .nav-tabs .nav-link.active .nav-item:nth-child(2) a {
        border-radius: 24px !important;
    }

    .nav-tabs .nav-link.active .nav-item:first-child a {
        border-radius: 24px !important;
    }

    .nav-tabs .nav-link:hover,
    .nav-tabs .nav-link:focus {
        border-color: transparent !important;
    }

    .nav-tabs .nav-item:nth-child(2) {
        /* background-color: #F0F0F3; */
        border-top-right-radius: 24px;
        border-bottom-right-radius: 24px;
        border-top-left-radius: 0px;
        border-bottom-left-radius: 0px;
    }

    .btn_dark-x {
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn_dark-x:focus {
        box-shadow: none;
        outline: none;
        border: none;
    }

    .btn_dark-x:active {
        box-shadow: none;
        outline: none;
        border: none;
        background-color: #1D1D1D !important;
    }

    .btn_dark-x:hover {
        text-decoration: none;
        background-color: #E6CD7F;
        color: #1D1D1D !important;
    }

    .btn_dark-x:active {
        text-decoration: none;
        background-color: #1D1D1D;
        color: #fff !important;
    }

    .btn_dark-x i {
        color: #E6CD7F;
    }

    .btn_dark-x:hover.btn_dark-x i {
        color: #1d1d1d;
    }

    .brownz-x-table thead tr th {
        background-color: #fff !important;
        border: none;
        color: #1D1D1D !important;
        font-size: 14px;
        font-weight: 500;
    }

    .brownz-close-btn {
        font-size: 14px;
        color: #D0342C !important;
        background-color: rgba(208, 52, 44, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        width: 25px;
        height: 25px;
        padding: 0;
        font-weight: 100;
        cursor: pointer;
        text-decoration: none
    }

    .brownz-close-btn:focus {
        color: #D0342C !important;
        background-color: rgba(208, 52, 44, 0.3);
        outline: none;
        border: none
    }

    .brownz-close-btn:hover {
        color: #D0342C !important;
        background-color: rgba(208, 52, 44, 0.3);
        text-decoration: none
    }

    .brownz-close-btn i {
        transform: translate(0px, -1px);
    }

    .brownz-x-table table tbody tr td {
        color: #1D1D1D;
        font-size: 14px;
        vertical-align: middle;
    }

    .brownz-x-table table tbody tr td input {
        background-color: #fff;
        border: none;
        outline: none;
        height: 37px;
        border-radius: 50px;
        padding-left: 15px;
        font-size: 14px;
    }

    .bold-text {
        font-weight: 700;
    }

    .date-template-brownz {
        margin: 0;
        background-color: #fff;
        box-shadow: rgba(0, 0, 0, 0.12) 0px 5px 15px 0px;
        width: max-content;
        padding: 10px 12px;
        border-radius: 50px;
        margin-left: 20px
    }

    .date-template-brownz i {
        color: #D0342C;
        font-size: 15px;
        padding-left: 10px;
        padding-right: 5px
    }

    .date-template-brownz i:first-child {
        padding-left: 0
    }

    .brownz-btn-yellow {
        background-color: #E6CD7F;
        padding: 10px 12px;
        border-radius: 35px;
        color: #1D1D1D;
        font-size: 14px;
        border: none;
        font-weight: 500;
    }

    .brownz-btn-yellow:hover {
        background-color: #1D1D1D;
        color: #ffff !important;
    }

    .input-group-append {
        margin-left: -30px
    }

    .brownz-discount-mod-c .input-group-append .input-group-text {
        background-color: #E6CD7F;
        color: #1D1D1D;
        width: 37px;
        height: 37px;
        font-size: 14px;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50px;
        border: none
    }

    /* .bg-even-model tr:nth-child(6n-5),
    .bg-even-model tr:nth-child(6n-4),
    .bg-even-model tr:nth-child(6n-3) {
        background-color: rgba(230, 205, 127, 0.20)
    }

    .bg-even-model tr:nth-child(6n-2),
    .bg-even-model tr:nth-child(6n-1),
    .bg-even-model tr:nth-child(6n) {
        background-color: rgba(255, 234, 169, 0.15);
    } */

    .bg-even-model {
        background-color: rgba(230, 205, 127, 0.20);
    }

    .first-row-table {
        border-top: 4px solid #fff;
    }

    .first-row-table:first-child {
        border-top: 0px solid #fff;
    }

    .table-borderless {
        margin-bottom: 0;
    }

    .over-scroll-table-de {
        height: 58vh;
        overflow: auto;
    }

    .voco-in-custom-brownz .select2-container--default .select2-selection--single {
        width: 100%;
        padding: 8px 15px;
        font-weight: 400;
        font-size: 14px;
        border: none;
        border-radius: 26px;
        box-sizing: border-box;
        background-color: #f0f0f0;
        outline: none;
        box-shadow: none;
        height: auto;
    }

    .voco-in-custom-brownz .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 50%;
        right: 20px;
        transform: translateY(-50%);
    }

    .voco-in-custom-brownz .select2-container--default {
        width: 100% !important;
    }

    .brownz-btn-yellow-d {
        . border: none;
        background-color: #E6CD7F;
        padding: 10px 16px;
        border-radius: 50px;
        font-family: "Roboto", sans-serif !important;
        min-width: max-content;
        color: #1D1D1D !important;
        font-size: 16px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        box-shadow: none;
    }

    .brownz-btn-yellow-d:hover {
        background-color: #1D1D1D;
        color: #fff !important;
    }

    .brownz-btn-yellow-d:focus {
        outline: none;
        border: none;
        box-shadow: none
    }

    .browz-gray-input-custom {
        background-color: #F0F0F3;
        color: #1D1D1D;
        font-size: 14px;
        font-weight: 400;
        padding: 10px 15px;
        border-radius: 26px;
        max-width: 100%;
        width: 100%;
        border: none;
        outline: none;
        height: auto;
        padding-right: 5px;
    }

    .browz-gray-input-custom:focus {
        background-color: #F0F0F3;
    }

    .amount-paid-editable {
        background-color: #ffffff !important;
        border: 1px solid #d0d0d0 !important;
        cursor: text;
        pointer-events: auto !important;
    }

    .amount-paid-editable:focus {
        background-color: #ffffff !important;
        border-color: #E6CD7F !important;
        box-shadow: 0 0 0 2px rgba(230, 205, 127, 0.35);
    }

    .discount--container-custom-brow input {
        background-color: #F0F0F3;
        border: none;
        outline: none;
        height: 37px;
        border-radius: 50px !important;
        padding-left: 15px;
        font-size: 14px;
    }

    .discount--container-custom-brow input:focus {
        background-color: #f0f0f3;
    }

    .discount--container-custom-brow .input-group-append {
        background-color: #E6CD7F;
        color: #1D1D1D;
        width: 37px;
        height: 37px;
        font-size: 14px;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50px;
        position: absolute;
        right: -5px;
        z-index: 7;
    }

    .discount--container-custom-brow .input-group-append .input-group-text {
        background-color: transparent;
        border: none;
    }

    .btn-red-brownz {
        background-color: rgba(208, 52, 44, 0.2);
        border: none;
        outline: none;
        box-shadow: none;
        padding: 0;
        margin: 0;
        border-radius: 40px;
        color: #D0342C;
        font-size: 16px;
        font-weight: 500;
        padding: 10px 16px;
    }

    .btn-red-brownz:hover {
        background-color: rgba(208, 52, 44, 0.3);
        color: #D0342C;
    }

    .btn-blue-brownz {
        background-color: rgba(82, 168, 217, 0.2) !important;
        border: none;
        outline: none;
        box-shadow: none;
        padding: 0;
        margin: 0;
        border-radius: 40px;
        color: #52A8D9 !important;
        font-size: 16px;
        font-weight: 500;
        padding: 10px 16px;
    }

    .btn-blue-brownz {
        background-color: rgba(82, 168, 217, 0.3);
        color: #52A8D9;
    }

    .btn-green-brownz {
        background-color: #199900b0 !important;
        border: none;
        outline: none;
        box-shadow: none;
        padding: 0;
        margin: 0;
        border-radius: 40px;
        color: #fff !important;
        font-size: 16px;
        font-weight: 500;
        padding: 10px 16px;
    }

    .btn-green-brownz:hover {
        background-color: rgba(25, 153, 0, 0.2) !important;
        color: #199900 !important;
    }

    .brownz-tab-123 {
        padding: 0 20px
    }

    .brownz-tab-123 .brwnz-tab-d {
        width: 100%;
        justify-content: space-between;
        flex-wrap: nowrap;
        padding-top: 40px;
    }

    .popup-head-bootst {
        font-size: 24px;
        font-weight: 500;
        color: #1D1D1D;
        margin: 0;
        padding: 0;
    }

    .brownz-tab-123 .brwnz-tab-d .circle-btn-123 {
        background-color: transparent;
        border: none;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-end;
        color: #979696;
        font-size: 14px;
        font-weight: 400;
        gap: 15px;
        padding: 0;
        position: relative;
        width: 100%;
    }

    .brownz-tab-123 .brwnz-tab-d .circle-btn-123:focus {
        background-color: transparent;
        color: #979696;
    }

    .brownz-tab-123 .brwnz-tab-d .circle-btn-123 b {
        background-color: #e5e5e5 !important;
        border: none;
        outline: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 500;
        margin: 0;
        width: 40px;
        height: 40px;
        position: absolute;
        z-index: 9;
    }

    .brownz-tab-123 .brwnz-tab-d .circle-btn-123 span {
        width: max-content;
    }

    .brownz-tab-123 .brwnz-tab-d .add-service-btn-red b {
        left: 50%;
        transform: translateX(-50%);
        top: -50px;
    }

    .brownz-tab-123 .brwnz-tab-d .timeslot-btn-blue b {}

    .brownz-tab-123 .brwnz-tab-d .add-service-btn-red.active b {
        box-shadow: 2px 2px 20px #d0342c30;
        background-color: #f4cecd !important;
        color: #D0342C;

    }

    .brownz-tab-123 .brwnz-tab-d .beautician-btn-blue.active b {
        box-shadow: 2px 2px 20px #20a8d82e;
        background-color: #d7eff8 !important;
        color: #20A8D8;
    }

    .brownz-tab-123 .brwnz-tab-d .circle-btn-123::after {
        content: "";
        width: 0%;
        height: 2px;
        background-color: #199900;
        position: absolute;
        left: -25px;
        top: -30px;
        transition: all 0.4s linear;
        z-index: 1;
    }

    .brownz-tab-123 .brwnz-tab-d .circle-btn-123::before {
        content: "";
        width: 100%;
        height: 2px;
        background-color: #f0f0f3;
        position: absolute;
        left: -25px;
        top: -30px;
        z-index: 0;
    }

    .brownz-tab-123 .brwnz-tab-d .add-service-btn-red::before {
        display: none
    }

    .brownz-tab-123 .brwnz-tab-d .add-service-btn-red {
        width: max-content;
    }

    .brownz-tab-123 .brwnz-tab-d .timeslot-btn-blue.active b {
        box-shadow: 2px 2px 20px #1999002e;
        background-color: #d6edd1 !important;
        color: #199900;
    }

    .brownz-tab-123 .brwnz-tab-d .circle-btn-123.service-active b {
        box-shadow: 2px 2px 20px #d0342c30;
        background-color: #f4cecd !important;
        color: #D0342C
    }

    .brownz-tab-123 .brwnz-tab-d .beautician-btn-blue.clcik b {
        box-shadow: 2px 2px 20px #20a8d82e;
        background-color: #d7eff8 !important;
        color: #20A8D8;
    }

    .brownz-tab-123 .brwnz-tab-d .beautician-btn-blue.clcik::after {
        width: 100%;
        transition: 0.2s all linear;
    }

    .brownz-tab-123 .brwnz-tab-d .circle-btn-123.service-active::after {
        display: none;
    }

    .brownz-tab-123 .brwnz-tab-d .circle-btn-123.slot-active::after {
        width: 100%;
        transition: 0.2s all linear;
    }

    .brownz-tab-123 .brwnz-tab-d .timeslot-btn-blue.slot-active b {
        box-shadow: 2px 2px 20px #1999002e;
        background-color: #d6edd1 !important;
        color: #199900;
    }

    .brownz-tab-123 .brwnz-tab-d .beautician-btn-blue b {
        right: 14px;
        top: -50px;
    }

    .brownz-tab-123 .brwnz-tab-d .timeslot-btn-blue b {
        right: 14px;
        top: -50px;
    }

    .brownz-tab-123 .brwnz-tab-d .add-service-btn-red.slot-active::after {
        display: none;
    }

    .tab-panel-br-add-service {
        background-color: #F0F0F3;
        border-radius: 24px;
        padding-top: 10px !important;
    }

    .brawnz-search-bar-in {
        background-color: transparent;
        border: none;
        box-shadow: none;
        outline: none;
        padding: 10px 20px;
        padding-left: 10px;
        font-size: 14px;
        width: 100%;
    }

    .brawnz-search-bar-in:focus {
        background-color: transparent;
        border: none;
        box-shadow: none;
        outline: none;
        padding: 10px 20px;
        padding-left: 10px;
        font-size: 14px;
        width: 100%;
    }

    .search-box-bar-container {
        display: flex;
        align-items: center;
        background-color: #FFFFFF;
        box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;
        border-radius: 26px;
        max-width: 300px;
        width: 100%;
        padding-left: 20px;
    }

    .search-box-bar-container p {
        margin: 0;
    }

    .search-box-bar-container p i {
        font-size: 16px;
        color: #D0342C;
    }

    .tags-search-cd-brw {
        background-color: #fff;
        box-shadow: none;
        border-radius: 28px;
    }

    .tags-search-cd-brw .nav-tabs .nav-link {
        background-color: #F0F0F3;
        font-size: 14px;
        font-weight: 400;
        color: #1D1D1D;
        padding: 8px 12px;
        border-radius: 30px;
        display: block;
        border: none;
    }

    .tags-search-cd-brw .nav-tabs .nav-link:focus {
        background-color: #F0F0F3;
    }

    .bg-nwe-services-brownz {
        border-radius: 28px;
    }

    .bg-nwe-services-brownz .sub-services h6 {
        margin: 0;
        margin-left: 10px;
        color: #1D1D1D;
    }

    .bg-nwe-services-brownz .sub-services:nth-child(odd) {
        background-color: rgba(230, 205, 127, 0.2);
    }

    .bg-nwe-services-brownz .sub-services:nth-child(even) {
        background-color: rgba(255, 234, 169, 0.2);
    }

    .bg-nwe-services-brownz .sub-services {
        border: none;
        border-radius: 20px;
        padding-left: 15px;
    }

    .bg-nwe-services-brownz .sub-service-list:nth-child(even) {
        background-color: rgba(230, 205, 127, 0.2);
    }

    .bg-nwe-services-brownz .sub-service-list:nth-child(odd) {
        background-color: rgba(255, 234, 169, 0.2);
    }

    .bg-nwe-services-brownz .sub-service-list {
        border-radius: 10px;
        padding: 10px !important;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

    .bg-nwe-services-brownz .sub-service-list .brownz-custom-radio-tick {
        position: relative;
    }

    .bg-nwe-services-brownz .sub-service-list .brownz-custom-radio-tick .checkmark {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        left: 11px;
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



    .bg-nwe-services-brownz .sub-service-list .brownz-custom-radio-tick .checkmark::after {
        top: 25%;
        left: 42%;
        width: 10px;
        height: 19px;
        border-width: 0 4px 4px 0;
    }

    .custom-selection input:checked~.checkmark::before {
        display: block;
        content: "";
        background-color: #E6CD7F;
        width: 45px;
        height: 45px;
        border-radius: 50%;
    }

    .bg-nwe-services-brownz .sub-service-list .brownz-custom-radio-tick .checkmark:has(.bg-nwe-services-brownz .sub-service-list .brownz-custom-radio-tick .checkmark::after) {
        background-color: #199900;
    }

    .bg-nwe-services-brownz .sub-service-list .custom-selection {
        background-color: #fff;
        border-radius: 50px;
        border: none;
        padding: 15px !important;
        padding-left: 65px !important;
        margin-bottom: 5px;
    }

    .bg-nwe-services-brownz .sub-service-list .custom-selection .service-item-list-info h6 {
        margin: 0;
    }

    .bg-nwe-services-brownz .sub-services i {
        border: none;
        background-color: #E6CD7F;
        color: #fff;
        font-size: 20px;
    }

    .box-shadow-none {
        box-shadow: none;
    }

    .click-script-class-active.sub-service-click {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;

    }

    .beautician-card-select-radio-brownz {
        border: none;
    }

    .beautician-card-select-radio-brownz .service-item-list-info {
        width: 100%;
        background-color: #fff;
        border-radius: 15px;
        padding: 15px;
        cursor: pointer;
        padding-right: 60px;
        gap: 12px;
    }

    .beautician-card-select-radio-brownz .service-item-list-info p {
        margin: 0;
        color: #979696;
        font-size: 14px;
        font-weight: 400;
    }

    .beautician-card-select-radio-brownz .service-item-list-info .checkmark {
        background-color: transparent !important;
        top: 50% !important;
        left: auto !important;
        right: 25px !important;
        transform: translateY(-50%) !important;

    }

    .beautician-card-select-radio-brownz.custom-selection:hover input~.checkmark {
        background-color: transparent;
    }

    .beautician-card-select-radio-brownz .service-item-list-info .checkmark::before {
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


    .beautician-card-select-radio-brownz .service-item-list-info .checkmark::after {
        left: 8px;
        top: 1px;
        width: 10px;
        height: 19px;
        border-width: 0 4px 4px 0;
    }

    .browz-changes-calender {
        background-color: #fff;
        border-radius: 24px;
        box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;
        overflow: hidden;
        padding: 20px;
    }

    .browz-changes-calender .prev-button,
    .browz-changes-calender .next-button {
        width: 40px;
        height: 40px;
        margin: 0;
        cursor: pointer;
        font-size: 1.2rem;
        color: #979696;
        background-color: #fff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: rgba(17, 17, 26, 0.05) 0px 1px 0px, rgba(17, 17, 26, 0.1) 0px 0px 8px;
        font-size: 0;
        position: relative;
    }

    .browz-changes-calender .prev-button::before {
        position: absolute;
        content: "\2039";
        color: #979696;
        font-size: 40px;
        top: -13px;
        border: none;
        background-image: none;
        background-position: center;
        background-repeat: no-repeat;
    }

    .browz-changes-calender .prev-button::before {
        position: absolute;
        content: "\2039";
        color: #979696;
        font-size: 40px;
        top: -13px;
        border: none;
        background-image: none;
        background-position: center;
        background-repeat: no-repeat;
    }

    .browz-changes-calender .next-button::before {
        position: absolute;
        content: "\2039";
        color: #979696;
        font-size: 40px;
        top: -13px;
        border: none;
        background-image: none;
        background-position: center;
        background-repeat: no-repeat;
        transform: rotateY(180deg);
    }

    .browz-changes-calender .month-label {
        color: #979696;
        margin: 5px;
        font-size: 18px;
        font-weight: 400;
        word-spacing: 0.5rem;
        text-transform: capitalize;
    }

    .browz-changes-calender .year-label {
        color: #979696;
        margin: 5px;
        font-size: 18px;
        font-weight: 400;
        word-spacing: 0.5rem;
    }

    .browz-changes-calender .week .day.header {
        color: #1D1D1D;
        font-weight: 500;
        font-size: 16px;
    }

    .browz-changes-calender .week .day.selected span {
        background-color: rgba(82, 168, 217, 0.2);
        color: #52A8D9;
    }

    .browz-changes-calender .day.today span::after {
        border-bottom: 2px solid #4dbd74;
    }

    .brownz-time-slot-times-select .slot-custom-radio p {
        height: 50px;
        background-color: #ffff;
        color: #979696;
        border-radius: 10px;
        font-weight: 400;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
        transition: 0.2s;
        border: none;
    }

    .brownz-time-slot-times-select .slot-custom-radio {
        border: none;
    }

    .brownz-time-slot-times-select .custom-selection.slot-custom-radio input:checked~.service-item-list-info {
        background-color: transparent;
    }

    .brownz-time-slot-times-select .custom-selection.slot-custom-radio input:checked~.service-item-list-info p {
        color: #52A8D9;
        background-color: rgba(82, 168, 217, 0.2);
        box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 22px;
    }

    .brownz-popup-form-v2 .modal-body .form-group label {
        font-size: 14px;
        color: #1D1D1D;
        margin-bottom: 8px;
        font-weight: 400;
    }

    .brownz-popup-form-v2 .modal-body .form-group input {
        background-color: #F0F0F3;
        color: #1D1D1D;
        font-size: 14px;
        font-weight: 400;
        padding: 10px 20px;
        border-radius: 26px;
        max-width: 100%;
        width: 100%;
        border: none;
        outline: none;
        height: auto;
    }

    .brownz-popup-form-v2 .modal-body .date_of_birth_ig {
        display: flex;
        flex-wrap: nowrap;
        position: relative;
    }

    .brownz-popup-form-v2 .modal-body .date_of_birth_ig .input-group-append {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
    }

    .brownz-popup-form-v2 .modal-body .date_of_birth_ig .input-group-append .input-group-text {
        background-color: transparent;
        border: none;
        padding: 0;
        cursor: pointer;
    }

    .close-btn-default {
        cursor: pointer;
    }

    .close-btn-default:focus {
        outline: none;
    }

    .respo-margin-bottom-vd {
        margin-bottom: 110px;
    }

    @media(max-width: 400px) {
        .respo-margin-bottom-vd {
            margin-bottom: 165px;
        }
    }

    @media screen and (min-device-width: 1200px) and (max-device-width: 1250px) {
        .just-adjust-p-input-d {
            padding-left: 8px !important;
            padding-right: 8px !important;
        }
    }

    @media screen and (min-device-width: 992px) and (max-device-width: 1200px) {
        .just-adjust-p-input-d {
            padding-left: 8px !important;
            padding-right: 8px !important;
        }
    }

    /* toggle d*/
    .tog-btn-container {
        position: relative;
        z-index: 0;
    }

    .tog-btn-container .toggle-discount {
        background-color: #E6CD7F;
        color: #1D1D1D;
        width: 37px;
        height: 37px;
        font-size: 14px;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50px;
        border: none;
        position: absolute;
        z-index: 99;
        cursor: pointer;
        outline: none;
    }

    .tog-btn-container .toggle-discount.toggle-active {
        left: calc(100% - 37px);
        transition: 0.5s all linear;
    }

    .tog-btn-container .toggle-discount {
        left: calc(37px - 0);
        transition: 0.5s all linear;
    }

    .tog-btn-container .form-control.input-padding-tog {
        padding-left: 45px
    }


    .tog-btn-container .form-control.input-padding-tog {
        display: none
    }

    .tog-btn-container .form-control {
        display: block;
    }

    .tog-btn-container .form-control.input-price-active {
        display: none
    }

    .tog-btn-container #applied_discount_percent {
        text-align: left;
    }

    .tog-btn-container #applied_price_percent {
        text-align: right;
    }
</style>
<style>
    .booking-flag {
        padding: 5px 12px;
        color: #fff;
        font-size: 12px;
        font-weight: 500;
        border-radius: 26px;
        text-transform: capitalize;
    }

    .booking-flag.green-flag {
        background-color: rgba(25, 153, 0, 5);
    }

    .booking-flag.red-flag {
        background-color: rgba(208, 52, 44, 5);
    }

    .booking-flag.orange-flag {
        background-color: rgba(255, 166, 39, 5);
    }

    .booking-flag.black-flag {
        background-color: #1D1D1D;
    }
</style>

<style>
    .booking-flag {
        padding: 5px 12px;
        color: #fff;
        font-size: 12px;
        font-weight: 500;
        border-radius: 26px;
    }

    .booking-flag.green-flag {
        background-color: rgba(25, 153, 0, 5);
    }

    .booking-flag.red-flag {
        background-color: rgba(208, 52, 44, 5);
    }

    .booking-flag.orange-flag {
        background-color: rgba(255, 166, 39, 5);
    }

    .text-black {
        background-color: #199900;
        padding: 10px 12px;
        border-radius: 35px;
        color: #1D1D1D;
        font-size: 14px;
        border: none;
        font-weight: 500;
    }
</style>
{{-- quick service (28-05-2025) --}}
<style>
    .quick-service-popup-right-side {
        background-color: #F0F0F3;
        padding: 15px;
        border-radius: 20px;
        height: 100%;
    }

    .quick-service-popup-right-side table {
        background-color: #fff;
        border: none;
        border-radius: 12px;
        overflow: hidden;
        padding-bottom: 14px;
        margin: 0;
    }

    .quick-service-popup-right-side .table th {
        border-color: transparent;
    }

    .service-used-products { font-size: 12px; margin-top: 8px; }
    .service-used-products.is-panel {
        max-width: 100%;
        background: #fff;
        border: 1px solid #efe6cf;
        border-radius: 12px;
        padding: 8px 12px 10px;
    }
    .used-products-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 4px;
    }
    .used-products-title {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #1d1d1d;
    }
    .service-used-products .used-product-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 8px 0;
        border-top: 1px solid #f3f0e6;
    }
    .service-used-products .used-product-card:first-of-type { border-top: 0; }
    .used-product-info { min-width: 0; flex: 1 1 auto; }
    .used-product-name {
        font-size: 13px;
        font-weight: 600;
        color: #1d1d1d;
        line-height: 1.3;
    }
    .used-product-stock {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        margin-top: 3px;
        font-size: 11px;
        color: #6b6456;
    }
    .used-product-left {
        background: #f3f7ee;
        color: #2f6b3a;
        border-radius: 999px;
        padding: 1px 8px;
        font-weight: 600;
    }
    .used-product-left.is-short {
        background: #fdecea;
        color: #b42318;
    }
    .used-product-controls {
        display: flex;
        align-items: center;
        gap: 6px;
        flex: 0 0 auto;
    }
    .service-used-products .used-product-qty {
        width: 72px;
        max-width: 72px;
        height: 32px;
        border: 1px solid #e4dcc4;
        border-radius: 8px;
        text-align: center;
        font-size: 13px;
        padding: 0 6px;
        background: #fff;
    }
    .used-product-qty.is-short { border-color: #e7b2ab; background: #fff8f7; }
    .used-product-uom {
        min-width: 22px;
        font-size: 11px;
        font-weight: 700;
        color: #6b6456;
    }
    .used-product-remove {
        border: 0;
        background: transparent;
        color: #c0392b;
        padding: 0 2px;
        line-height: 1;
        font-size: 14px;
    }
    .used-product-add-btn {
        border: 0;
        background: transparent;
        color: #1d1d1d;
        font-size: 12px;
        font-weight: 600;
        padding: 0;
    }
    .used-product-add { position: relative; margin-top: 6px; }
    .used-product-search {
        width: 100%;
        height: 34px;
        border: 1px solid #e4dcc4;
        border-radius: 8px;
        padding: 0 10px;
        font-size: 13px;
    }
    .used-product-hits {
        position: absolute;
        z-index: 30;
        left: 0;
        right: 0;
        top: calc(100% + 4px);
        max-height: 220px;
        overflow: auto;
        background: #fff;
        border: 1px solid #efe6cf;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }
    .used-product-hit {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
        text-align: left;
        border: 0;
        border-bottom: 1px solid #f6f3ea;
        background: #fff;
        padding: 8px 10px;
    }
    .used-product-hit:hover { background: #fffaf0; }
    .used-product-hit-name { font-size: 13px; font-weight: 600; color: #1d1d1d; }
    .used-product-hit-meta { font-size: 11px; color: #6b6456; }

    /* Moved out of Vue template (service-modal) — Vue forbids <style> inside templates */
    .nav-tabs .nav-link.active:focus {
        background: #155e91;
        border-color: #c8ced3;
        border-bottom-color: #fff;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4/dist/flatpickr.min.js"></script>
<div style="
margin-top: -13px;
" id="vue-details-tab-edit" class="has-vue-form">

    @if (Session::has('message'))
        <div class="alert alert-primary" role="alert">
            {{ Session::get('message') }}
        </div>
    @endif
    <template class="mb-5">
        <div v-if="booking_info.canceled_by" class="alert alert-danger">
            {{ __('This booking has canceled!') }}
        </div>

        @include('salon.booking-details.ajax-tabs.partials-tab-edit.booking-form')
        {{-- @include('salon.booking-details.ajax-tabs.partials-tab-edit.booking-service-search') --}}
        <div class="row ">

            @include('salon.booking-details.ajax-tabs.partials-tab-edit.booking-service-existing')
            <!-- shammas2 -->
            @include('salon.booking-details.ajax-tabs.partials-tab-edit.booking-service-summary')
        </div>
        @include('salon.booking-details.ajax-tabs.partials-tab-edit.booking-service-footer')
        @include('salon.booking-details.ajax-tabs.partials-tab-edit.products-modal')
        @include('salon.booking-details.ajax-tabs.partials-tab-edit.client-update-modal')
        </form>
        <!-- Modal -->
        @include('salon.booking-details.ajax-tabs.partials-tab-edit.service-modal')
        @include('salon.booking-details.ajax-tabs.partials-tab-edit.split-payment-modal')

        {{-- products modal --}}
</div>
</template>

</div>


<script type="application/json" id="split-beauticians-json">@json($split_beauticians ?? [])</script>
<script>
    (function() {
        try {
            var el = document.getElementById('split-beauticians-json');
            window.SPLIT_BEAUTICIANS = el ? JSON.parse(el.textContent || '[]') : [];
        } catch (e) {
            window.SPLIT_BEAUTICIANS = [];
        }
    })();
</script>
<script>
    var vue_details_tab_edit = null;
    var expired_date = "";
    var all_booking_info = [];
    var invoice_window = "";
    var rfund_permission = '{{ $refund_permisson }}';
    var selected_date_calender = new Date();
    jQuery(function($) {
        vue_details_tab_edit = new Vue({
            el: "#vue-details-tab-edit",
            data: <?php echo json_encode(
                array_merge($details_edit_vue, [
                    'popup_client_type' => null,
                    'all_shifts' => [],
                    'amount_taken_flag' => 0,
                    'is_editing_amount_paid' => false,
                    // Completed booking: which payment row has Payment Mode dropdown unlocked
                    'editing_payment_id' => null,
                    'used_product_search' => '',
                    'used_product_hits' => [],
                    'used_product_for' => null,
                    'amount_taken_input' => '',
                    'beatician_html_id' => 'beatician_html_id',
                    'onChange' => 0,
                    'shift_id' => '',
                    'bu' => 'bu',
                    'booking_service_id' => '',
                    'grand_total' => '',
                    'grand_tax_total' => '',
                    'grand_sub_total' => '',
                    'grand_total_1' => '',
                    'summary_discount_total' => 0,
                    'roundOffApplied' => false,
                    'amount_taken' => 0,
                    'new_duration' => [],
                    'duration' => '',
                    'qty' => 'qty',
                    'duration_minutes' => '',
                    'time_slot_form' => 'time_slot_form',
                    'beauticians' => [],
                    'price' => '',
                    'beautician' => '',
                    'time_slot' => [],
                    'discount_percent' => 'discount_percent_',
                    'discount_percent_item' => 'discount_percent_item_',
                    'beautician_id' => '',
                    'changing_price_var' => '',
                    'total_price_var' => '',
                    'total_price_var_discount' => '',
                    'tax_var' => '',
                    'time_selected' => 0,
                    'btn_slot_id' => '',
                    'price_html_id' => 'price_',
                    'quantity_html_id' => 'quantity_',
                    'net_html_id' => 'net_',
                    'total_price_discount' => 'discount_total_',
                    'line_discount_form' => 'line_discount_form_',
                    'tax_html_id' => 'tax_',
                    'total_html_id' => 'total_',
                    'total_item_html_id' => 'total_item_',
                    'tax_item_html_id' => 'tax_item_',
                    'net_item_html_id' => 'net_item_',
                    'time_slot_btn_id' => 'slot_btn_',
                    'service_id_var' => 'service_id_var_',
                    'service_id_html' => '',
                    'service_id' => '',
                    'refund_permission' => '',
                    'temp_beatician_id' => '',
                    'booking_serv_id' => '',
                    'change_product_cat_id' => '',
                    'selected_service_id' => '',
                    'search_query' => '',
                    'search_query_beautician' => '',
                    'search_beautician_list' => [],
                    'search_product_name' => '',
                    'current_stage' => 0,
                    'selected_serv_id' => 0,
                    'selected_beautician_id' => 0,
                    'check_qty' => 'check_qty_',
                    'selected_shift_id' => 0,
                    'selected_time_slot' => null,
                    'is_reschedule' => false,
                    'is_contains_pending_items' => false,
                    'current_checked_qty_item_code' => '',
                    'selected_product_id' => 0,
                    'selected_product_beautician_id' => 0,
                    'product_price_id' => 0,
                    'product_quantity' => 0,
                    'search_service_list' => [],
                    'selectedServices' => [],
                    'selectedServiceIds' => [],
                    'selectedBeautician' => '',
                    'selectedServiceDetails' => [],
                    'selectedProducts' => [],
                    'selectedProductDetails' => [],
                    'selectedProductBeautician' => '',
                    'quick_items_list' => [],
                    'quick_product_cat_id' => 0,
                    'quickProductSearch' => '',
                    'split_beauticians' => $split_beauticians ?? [],
                    'start_date' => date('Y-m-d H:i:s'),
                    'searchName' => '',
                    'start_date' => date('Y-m-d H:i:s'),
                    'appliedDiscountPercentage' => '',
                    'loading' => false,
                    'consultation_id' => null,
                    'consultation_status' => null,
                    // After first finalize (booking_code), Update stays off until payment/cart changes.
                    'booking_needs_update' => false,
                    '_finalized_edit_auth_password' => null,
                    'split_payment' => [
                        'cash' => '',
                        'card' => '',
                        'online' => '',
                        'credit' => '',
                        'card_type' => '',
                        'cash_tendered' => '',
                    ],
                    'split_payment_saving' => false,
                    'split_payment_mode' => 'new',
                    '_rebalance_auth_password' => null,

                    // Client Info
                    'client_name' => '',
                    'client_mobile' => '',
                    'client_age' => '',
                    'date' => '',
                    'client_phone' => '',
                    'stylist_name' => '',
                    'designation' => '',

                    // Primary Analysis
                    'primary_analysis' => [
                        'scalp_condition' => [],
                        'hair_texture' => [],
                        'hair_density' => [],
                        'hair_condition' => [],
                        'hair_elasticity' => [],
                        'hair_porosity' => [],
                        'grey_percentage' => [],
                    ],
                    // History (Hair)
                    'hair_history' => [
                        'hair_type' => [['type' => 'Virgin', 'description' => '', 'checked' => false], ['type' => 'Henna', 'description' => '', 'checked' => false], ['type' => 'Chemically Treated', 'description' => '', 'checked' => false], ['type' => 'Coloured', 'description' => '', 'checked' => false], ['type' => 'Keratin', 'description' => '', 'checked' => false]],
                        'allergies' => [['type' => 'Hair Dye', 'description' => '', 'checked' => false], ['type' => 'Chemical Solution', 'description' => '', 'checked' => false], ['type' => 'Blonder/Lightener', 'description' => '', 'checked' => false], ['type' => 'Other', 'description' => '', 'checked' => false]],
                        'medical_condition' => [['type' => 'Pregnant', 'description' => '', 'checked' => false], ['type' => 'Medicine Conception', 'description' => '', 'checked' => false]],
                        'home_care' => [['type' => 'Shampoo/Conditioner/Serum', 'description' => '', 'checked' => false]],
                        'oiling_habit' => [['type' => 'Frequency/Type of Oil', 'description' => '', 'checked' => false]],
                        'styling_product' => [['type' => 'Heating Tools/Styling Creams', 'description' => '', 'checked' => false]],
                    ],

                    'virgin_model' => '',

                    // Client Desire
                    'client_desire' => [
                        'service_needed' => [
                            'description1' => '',
                            'description2' => '',
                        ],
                        'desired_result' => [
                            'description1' => '',
                            'description2' => '',
                        ],
                        'technique' => [
                            'description1' => '',
                            'description2' => '',
                        ],
                        'patch_test' => [
                            'description1' => '',
                            'description2' => '',
                        ],
                        'product_used' => [
                            'description1' => '',
                            'description2' => '',
                        ],
                        'after_care' => [
                            'description1' => '',
                            'description2' => '',
                        ],
                    ],

                    // Declaration
                    'declaration' => [
                        'accuracy' => 0,
                        'risks' => 0,
                        'patch_test' => 0,
                        'results' => 0,
                        'aftercare' => 0,
                        'liability' => 0,
                        'cost' => 0,
                    ],

                    // Photo permission
                    'photo_permission' => '',

                    // Signatures
                    'signatures' => [
                        'client_signature' => '',
                        'stylist_signature' => '',
                        'manager_signature' => '',
                    ],
                    // skin
                    // skin Analysis
                    'skin_analysis' => [
                        'skin_type' => [],
                        'skin_concern' => [],
                        'skin_allergy' => [],
                        'skin_sensitivity' => [],
                        'skin_routine' => [],
                    ],
                    // Life style
                    'life_style' => [
                        'water_intakes' => [],
                        'sleep' => [],
                        'stress' => [],
                        'sun_exposure' => [],
                        'smoke' => [],
                        'drinks' => [],
                    ],
                    // Life style
                    'history' => [
                        'medication' => [],
                        'hormonal_issues' => [],
                        'past_treatments' => [],
                        'chronic_illness' => [],
                        'pregnant' => [],
                    ],
                    'conclusion' => [
                        'recommended_service' => [
                            'description' => '',
                        ],
                        'after_care' => [
                            'description' => '',
                        ],
                        'follow_up_plan' => [
                            'description' => '',
                        ],
                    ],
                    // Photo permission
                    'skin_photo_permission' => '',

                    'virgin' => false,
                    'virgin_desc' => '',
                    'henna' => false,
                    'henna_desc' => '',
                    'chemically_treated' => false,
                    'chemically_treated_desc' => '',
                    'coloured' => false,
                    'coloured_desc' => '',
                    'keratin' => false,
                    'keratin_desc' => '',
                    //
                    'hair_dye' => false,
                    'hair_dye_desc' => '',
                    'chemical_solution' => false,
                    'chemical_solution_desc' => '',
                    'blonder' => false,
                    'blonder_desc' => '',
                    'other' => false,
                    'other_desc' => '',
                    //
                    'medical_condition' => false,
                    'medical_condition_desc' => '',
                    'medicine_conception' => false,
                    'medicine_conception_desc' => '',
                    //
                    'home_care' => false,
                    'home_care_desc' => '',
                    //
                    'oiling_habit' => false,
                    'oiling_habit_desc' => '',
                    //
                    'use_of_styling_product' => false,
                    'use_of_styling_product_desc' => '',
                ]),
            ); ?>,



            watch: {
                selectedBeautician(newVal) {
                    const catid = (this.all_service_category && this.all_service_category.length)
                        ? this.all_service_category[0].category_id
                        : 0;

                    if (newVal) {
                        this.selected_beautician_id = newVal;
                        this.fetchServices(newVal, catid);
                    }
                },

                start_date(newVal, oldVal) {
                    // When date changes in Quick Services, update beauticians list to exclude those on leave
                    // Only update if Quick Services modal is visible
                    if (newVal && $('#quickServiceLookup').is(':visible')) {
                        // Use setTimeout to ensure modal is fully rendered
                        setTimeout(() => {
                            this.updateBeauticiansForDate(newVal);
                        }, 100);
                    }
                },

                selectedServices(newVal, oldVal) {
                    // Handle added services
                    const added = newVal.filter(id => !oldVal.includes(id));
                    added.forEach(id => {
                        const service = this.findServiceById(id);
                        if (service) {
                            const name = this.getServiceName(id);
                            const price = this.getServicePrice(id);
                            this.selectedServiceDetails.push({
                                service_id: id,
                                name: name,
                                price: price
                            });
                        }
                    });

                    // Handle removed services
                    const removed = oldVal.filter(id => !newVal.includes(id));
                    removed.forEach(id => {
                        this.selectedServiceDetails = this.selectedServiceDetails.filter(
                            service => service.service_id !== id
                        );
                    });

                },

                selectedProducts(newVal, oldVal) {
                    const prev = (oldVal || []).map(String);
                    const curr = (newVal || []).map(String);
                    const added = curr.filter(id => !prev.includes(id));
                    added.forEach(priceId => {
                        const product = this.findQuickProductByPriceId(priceId);
                        if (product && !this.selectedProductDetails.some(p => String(p.price_id) === String(priceId))) {
                            this.selectedProductDetails.push(product);
                        }
                    });
                    const removed = prev.filter(id => !curr.includes(id));
                    removed.forEach(priceId => {
                        this.selectedProductDetails = this.selectedProductDetails.filter(
                            product => String(product.price_id) !== String(priceId)
                        );
                    });
                },

                suggestedTakenAmount(newVal) {
                    // Do not overwrite while user is typing a split/partial amount
                    if (this.is_editing_amount_paid || this.amount_taken_flag === 1) {
                        return;
                    }
                    if (this.amount_taken === 0 || this.amount_taken === '' || this.amount_taken === null || isNaN(this.amount_taken)) {
                        var val = newVal !== null ? newVal : 0;
                        this.amount_taken = isNaN(val) || val === null ? 0 : val;
                        this.syncAmountTakenInput();
                    }
                }

            },
            mounted: function() {
                // Ensure amount_taken is never NaN
                if (isNaN(this.amount_taken) || this.amount_taken === null || this.amount_taken === '') {
                    this.amount_taken = 0;
                }
                this.syncAmountTakenInput();

                var splitUpdateKey = 'booking_needs_update_' + this.booking_id;
                if (sessionStorage.getItem(splitUpdateKey)) {
                    this.booking_needs_update = true;
                    sessionStorage.removeItem(splitUpdateKey);
                }

                var finalizeNoticeRaw = sessionStorage.getItem('booking_finalize_notice');
                if (finalizeNoticeRaw) {
                    sessionStorage.removeItem('booking_finalize_notice');
                    try {
                        var finalizeNotice = JSON.parse(finalizeNoticeRaw);
                        if (finalizeNotice && finalizeNotice.message && typeof js_notify === 'function') {
                            js_notify(finalizeNotice.message, finalizeNotice.type || 'success');
                        }
                    } catch (e) {}
                }

                this.$nextTick(() => {
                    // Bind beautician select2 only inside Quick Services modal
                    // (do not bind all .voco-select2 — that also catches #client_id)
                    $('#quickServiceLookup').find('select.voco-select2').select2().on('change', (e) => {
                        this.selectedBeautician = e.target.value;
                    });
                    $('#quickProductLookup').find('select.voco-select2-quick-product').select2({
                        placeholder: 'Beautician',
                        allowClear: true,
                        width: '100%'
                    }).on('change', (e) => {
                        this.selectedProductBeautician = e.target.value;
                    });
                });


                $('#vc-breadcrumb').hide();
                if (<?= $check_float_amount ?> == 0) {
                    if (confirm('cashier not assigned to the till')) {
                        window.location.href = "{{ route('float_amt', ['id' => 0]) }}";
                    } else {
                        window.location.href = "{{ route('float_amt', ['id' => 0]) }}";
                    }
                }

                if (this.booking_info.booking_type == "Draft") {

                    var return_amount = this.booking_info.booking_total;
                    $('#amount_taken').val('0');
                    $('#amount_given').val(return_amount);
                }
                this.$nextTick(function() {


                    this.initSelect2();
                    if (this.all_db_services && this.all_db_services.length) {
                        this.refreshSelect2Services();
                    }
                    if (this.all_db_items && this.all_db_items.length) {
                        this.refreshSelect2Items();
                    }
                    this.grandTotal_all_raw();
                    refreshFlatPicker();
                    this.refreshDiscountFunction();
                    if (typeof syncDiscountToggleState === 'function') {
                        syncDiscountToggleState();
                    }
                    if (this.booking_info.client_id) {
                        const selectedOption = new Option(
                            this.booking_info.client_name,
                            this.booking_info.client_id,
                            true,
                            true
                        );
                        $("#client_id").append(selectedOption).trigger("change");
                    } else {
                        $("#client_id").select2("open");
                    }
                })

                this.start_date = this.getCurrentDateTime();
                // Attach modal close event listener (off first to avoid duplicate handlers on remount)
                $('#hairLookup').off('hidden.bs.modal.consultation').on('hidden.bs.modal.consultation', () => {
                    this.resetHairModal();
                });
                $('#skinLookup').off('hidden.bs.modal.consultation').on('hidden.bs.modal.consultation', () => {
                    this.resetSkinModal();
                });
                $('#quickServiceLookup').off('hidden.bs.modal.consultation shown.bs.modal.consultation')
                    .on('hidden.bs.modal.consultation', () => {
                        this.resetQuickServiceModal();
                    })
                    .on('shown.bs.modal.consultation', () => {
                        this.resetQuickServiceModal();
                        // Load beauticians filtered by current date when modal opens
                        this.$nextTick(() => {
                            this.loadBeauticiansForQuickService();
                        });
                    });
                $('#quickProductLookup').off('hidden.bs.modal.quickproduct shown.bs.modal.quickproduct')
                    .on('hidden.bs.modal.quickproduct', () => {
                        this.resetQuickProductModal();
                    })
                    .on('shown.bs.modal.quickproduct', () => {
                        this.$nextTick(() => {
                            this.loadQuickProductGroups();
                        });
                    });
            },

            updated: function() {
                this.$nextTick(function() {
                    if (this.all_db_services && this.all_db_services.length) {
                        this.refreshSelect2Services();
                    }
                    if (this.all_db_items && this.all_db_items.length) {
                        this.refreshSelect2Items();
                    }
                    applySelect2();
                    refreshFlatPicker();
                    this.refreshDiscountFunction();
                })
            },
            filters: {
                time_hm: function(value) {
                    return value ? value.substring(0, 5) : '';
                }
            },

            methods: {

                getType(type) {
                },

                getType(type) {
                },

                getTypeDesc(type) {
                    let desc = '';
                    if (type === 'virgin') desc = this.virgin_desc;
                    if (type === 'henna') desc = this.henna_desc;
                    if (type === 'chemical') desc = this.chemical_desc;
                },

                generatePayload() {
                    let payload = [];

                    if (this.virgin_model) payload.push({
                        type: "virgin",
                        desc: this.virgin_desc
                    });
                    if (this.henna_model) payload.push({
                        type: "henna",
                        desc: this.henna_desc
                    });
                    if (this.chemical_model) payload.push({
                        type: "chemical",
                        desc: this.chemical_desc
                    });

                    return payload;
                },

                resetQuickServiceModal() {
                    this.selectedBeautician = '';
                    this.selected_beautician_id = 0;
                    this.searchName = '';
                    this.selectedServices = [];
                    this.selectedServiceDetails = [];
                    this.all_services = [];
                    this.selected_service_id = '';
                    this.loading = false;
                    this.start_date = this.getCurrentDateTime();

                    // Reset only the Quick Services beautician dropdown —
                    // never clear all .voco-select2 (that also clears #client_id
                    // and triggers tab-saver with an empty client_id)
                    this.$nextTick(() => {
                        const $beauticianSelect = $('#quickServiceLookup').find('select.voco-select2').first();
                        if ($beauticianSelect.length) {
                            $beauticianSelect.val(null).trigger('change');
                        }
                    });
                },
                resetQuickProductModal() {
                    this.selectedProductBeautician = '';
                    this.selectedProducts = [];
                    this.selectedProductDetails = [];
                    this.quick_items_list = [];
                    this.quick_product_cat_id = 0;
                    this.quickProductSearch = '';
                    this.loading = false;
                    this.$nextTick(() => {
                        const $beauticianSelect = $('#quickProductLookup').find('select.voco-select2-quick-product').first();
                        if ($beauticianSelect.length) {
                            $beauticianSelect.val(null).trigger('change');
                        }
                    });
                },
                loadBeauticiansForQuickService() {
                    // Load beauticians filtered by current date when modal opens
                    if (this.start_date) {
                        this.updateBeauticiansForDate(this.start_date);
                    }
                },

                resetHairModal() {
                    this.consultation_id = null;
                    this.consultation_status = null;
                    this.client_name = '';
                    this.client_mobile = '';
                    this.client_age = '';
                    this.date = '';
                    this.client_phone = '';
                    this.stylist_name = '';
                    this.designation = '';
                    this.photo_permission = '';

                    // Reset primary analysis
                    this.primary_analysis = {
                        scalp_condition: [],
                        hair_texture: [],
                        hair_density: [],
                        hair_condition: [],
                        hair_elasticity: [],
                        hair_porosity: [],
                        grey_percentage: []
                    };

                    // Reset hair history to match initial structure
                    this.hair_history = {
                        hair_type: [{
                                type: 'Virgin',
                                description: '',
                                checked: false
                            },
                            {
                                type: 'Henna',
                                description: '',
                                checked: false
                            },
                            {
                                type: 'Chemically Treated',
                                description: '',
                                checked: false
                            },
                            {
                                type: 'Coloured',
                                description: '',
                                checked: false
                            },
                            {
                                type: 'Keratin',
                                description: '',
                                checked: false
                            }
                        ],
                        allergies: [{
                                type: 'Hair Dye',
                                description: '',
                                checked: false
                            },
                            {
                                type: 'Chemical Solution',
                                description: '',
                                checked: false
                            },
                            {
                                type: 'Blonder/Lightener',
                                description: '',
                                checked: false
                            },
                            {
                                type: 'Other',
                                description: '',
                                checked: false
                            }
                        ],
                        medical_condition: [{
                                type: 'Pregnant',
                                description: '',
                                checked: false
                            },
                            {
                                type: 'Medicine Conception',
                                description: '',
                                checked: false
                            }
                        ],
                        home_care: [{
                            type: 'Shampoo/Conditioner/Serum',
                            description: '',
                            checked: false
                        }],
                        oiling_habit: [{
                            type: 'Frequency/Type of Oil',
                            description: '',
                            checked: false
                        }],
                        styling_product: [{
                            type: 'Heating Tools/Styling Creams',
                            description: '',
                            checked: false
                        }]
                    };

                    // Reset client desire
                    this.client_desire = {
                        service_needed: {
                            description1: '',
                            description2: ''
                        },
                        desired_result: {
                            description1: '',
                            description2: ''
                        },
                        technique: {
                            description1: '',
                            description2: ''
                        },
                        patch_test: {
                            description1: '',
                            description2: ''
                        },
                        product_used: {
                            description1: '',
                            description2: ''
                        },
                        after_care: {
                            description1: '',
                            description2: ''
                        }
                    };

                    // Do not clear .voco-select2 here — #client_id also has that class,
                    // and clearing it triggers tab-saver with an empty client_id.
                },
                resetSkinModal() {
                    this.consultation_id = null;
                    this.consultation_status = null;
                    this.client_name = '';
                    this.client_age = '';
                    this.date = '';
                    this.client_mobile = '';
                    this.client_phone = '';
                    this.stylist_name = '';
                    this.designation = '';

                    // skin analysis
                    this.skin_analysis = {
                        skin_type: [],
                        skin_concern: [],
                        skin_allergy: [],
                        skin_sensitivity: [],
                        skin_routine: [],
                    };

                    // life style
                    this.life_style = {
                        water_intakes: [],
                        sleep: [],
                        stress: [],
                        sun_exposure: [],
                        smoke: [],
                        drinks: [],
                    };

                    // history
                    this.history = {
                        medication: [],
                        hormonal_issues: [],
                        past_treatments: [],
                        chronic_illness: [],
                        pregnant: [],
                    };

                    // conclusion
                    this.conclusion = {
                        recommended_service: {
                            description: ''
                        },
                        after_care: {
                            description: ''
                        },
                        follow_up_plan: {
                            description: ''
                        },
                    };

                    this.skin_photo_permission = '';

                    // hair-related flags
                    this.virgin = this.henna = this.chemically_treated = this.coloured =
                        this.keratin = this.hair_dye = this.chemical_solution = this.blonder =
                        this.other = this.medical_condition = this.medicine_conception =
                        this.home_care = this.oiling_habit = this.use_of_styling_product = false;

                    this.virgin_desc = this.henna_desc = this.chemically_treated_desc =
                        this.coloured_desc = this.keratin_desc = this.hair_dye_desc =
                        this.chemical_solution_desc = this.blonder_desc = this.other_desc =
                        this.medical_condition_desc = this.medicine_conception_desc =
                        this.home_care_desc = this.oiling_habit_desc =
                        this.use_of_styling_product_desc = '';
                },

                getCurrentDateTime() {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    return `${year}-${month}-${day}T${hours}:${minutes}`;
                },

                searchService1: function() {
                    $('#sub-services').addClass('sub-service-click');
                    $('#sub_servs_0').css('display', 'block');


                    const searchQuery = $('#filterServ1').val().trim();
                    vue_details_tab_edit.search_query = searchQuery;
                    vue_details_tab_edit.selected_service_id = 0;

                    // Fallback if beautician is not selected
                    const beauticianId = vue_details_tab_edit.selected_beautician_id || 0;

                    vue_details_tab_edit.fetchServices(beauticianId, 0, searchQuery);
                },



                findServiceById(serviceId) {
                    // Find service in all_services (including sub-services)
                    for (let category of this.all_services) {
                        // if (category.service_id === serviceId) {
                        //     return category;
                        // }
                        for (let subService of category.services) {
                            if (subService.service_id === serviceId) {
                                return subService;
                            }
                        }
                    }
                    return null;
                },
                getServiceName(serviceId) {
                    const service = this.findServiceById(serviceId);
                    if (!service) return 'Unknown Service';

                    if (service.category && service.category.parent) {
                        return `${service.category.parent.name}/${service.name}`;
                    }

                    return service.name;
                },
                getServicePrice(serviceId) {
                    const service = this.findServiceById(serviceId);
                    if (!service) return '₹0';
                    return service.service_base_price ? `₹${service.service_base_price}` :
                        `₹${service.price}`;
                },
                removeService(serviceId) {
                    this.selectedServices = this.selectedServices.filter(id => id !== serviceId);
                    // selectedServiceDetails is updated via the watcher
                },

                changeServiceCategorys(catid, searchName = '') {
                    // alert(catid);
                    $('#filterServ').val("");
                    vue_details_tab_edit.selected_service_id = catid;
                    // vue_details_tab_edit.selected_cat_id = catid;
                    vue_details_tab_edit.search_query = searchName;

                    vue_details_tab_edit.fetchServices(vue_details_tab_edit.selected_beautician_id,
                        catid, searchName);
                },
                fetchServices(beauticianId, catid, name = '') {
                    vue_details_tab_edit.selected_beautician_id = beauticianId;

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('salon.quick.booking.get.service') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            beautician_id: vue_details_tab_edit.selected_beautician_id,
                            catid: catid || 0,
                            name: name || ''
                        },
                        dataType: "json",
                        success: function(data) {
                            vue_details_tab_edit.all_services = data || [];
                        },
                        error: function(xhr) {
                            vue_details_tab_edit.all_services = [];
                        }
                    });
                },
                updateBeauticiansForDate(date) {
                    // Only update if we're in Quick Services modal
                    if (!$('#quickServiceLookup').is(':visible')) {
                        return;
                    }

                    const self = this;
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('salon.quick.booking.get.beauticians') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            date: date
                        },
                        dataType: "json",
                        success: function(data) {
                            // Get the currently selected beautician
                            const currentSelected = self.selectedBeautician;

                            // Find the beautician select dropdown in Quick Services modal
                            // Try multiple selectors to find the dropdown
                            let $beauticianSelect = $('#quickServiceLookup').find('select[v-model="selectedBeautician"]');

                            // If not found, try finding by class or other attributes
                            if ($beauticianSelect.length === 0) {
                                $beauticianSelect = $('#quickServiceLookup').find('select.voco-select2').first();
                            }

                            if ($beauticianSelect.length === 0) {
                                return; // Dropdown not found
                            }

                            // Clear and update the beautician dropdown
                            $beauticianSelect.empty();
                            $beauticianSelect.append('<option value=""></option>');

                            // Add filtered beauticians from API
                            data.forEach(function(beautician) {
                                const option = new Option(beautician.text, beautician.id, false, false);
                                $beauticianSelect.append(option);
                            });

                            // Reinitialize Select2 and re-bind change handler
                            $beauticianSelect.select2('destroy');
                            $beauticianSelect.select2({
                                placeholder: "Beautician",
                                allowClear: true
                            }).on('change', function(e) {
                                self.selectedBeautician = e.target.value;
                            });

                            // If the previously selected beautician is still available, keep it selected
                            if (currentSelected && data.some(b => b.id == currentSelected || b.beautician_id == currentSelected)) {
                                $beauticianSelect.val(currentSelected).trigger('change');
                            } else {
                                // Clear selection if beautician is on leave
                                $beauticianSelect.val(null).trigger('change');
                                self.selectedBeautician = '';
                                self.selected_beautician_id = 0;
                                // Also clear services when beautician is removed
                                self.all_services = [];
                            }
                        },
                        error: function(xhr, status, error) {
                        }
                    });
                },


                manageSubmitButton1() {
                    if (!this.booking_info.client_id) {
                        alert('Please add client...!!');
                        return;
                    }
                    if (!this.selected_beautician_id && !this.selectedBeautician) {
                        alert('Please select a beautician.');
                        return;
                    }
                    if (this.selectedServices.length === 0) {
                        alert('Please select at least one service.');
                        return;
                    }
                    this.submitForm();
                },
                submitForm() {
                    const fullDateTime = this.start_date;

                    // Split the date and time parts
                    const [datePart, timePart] = fullDateTime.split('T');
                    const slotStart = (timePart || '00:00') + ':00';

                    this.loading = true;
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('salon.booking.quick.item.save') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            beautician_id: this.selectedBeautician || this.selected_beautician_id,
                            service_ids: this.selectedServices,
                            booking_id: this.booking_id,
                            line_item_type: 'service',
                            appointment_date: datePart,
                            slot_start: slotStart,
                            shift_id: '',
                            quantity: 1,
                            is_update: 0
                        },

                        success: (response) => {
                            this.loading = false;
                            if (response.booking_info) {
                                this.booking_info = response.booking_info;
                                if (response.booking_info.booking_id) {
                                    this.booking_id = response.booking_info.booking_id;
                                }
                                this.$nextTick(() => {
                                    this.grandTotal_all_raw();
                                });
                            }
                            $('#quickServiceLookup').modal('hide');
                            // Clear selections
                            this.selectedServices = [];
                            this.selectedServiceDetails = [];
                            this.selectedBeautician = '';
                            this.selected_beautician_id = 0;
                        },
                        error: (xhr) => {
                            this.loading = false;
                            let message = 'Something went wrong.';
                            try {
                                const json = JSON.parse(xhr.responseText);
                                if (json.js_notifications && json.js_notifications.error) {
                                    message = json.js_notifications.error;
                                } else if (json.message) {
                                    message = json.message;
                                }
                            } catch (e) {
                                // keep default message
                            }
                            alert(message);
                        }
                    });
                },



                showConsultationQr(response) {
                    let qrContainer = document.getElementById('qrCodeContainer');
                    qrContainer.innerHTML = `
                        <img src="${response.qr_code}" alt="QR Code" style="width:200px; height:200px; margin: 15px auto;" />
                        <p>Scan this code to continue the form</p>
                        <a href="${response.form_url}" target="_blank">Open Form Manually</a>
                    `;
                    $('#hairLookup').modal('hide');
                    $('#skinLookup').modal('hide');
                    $('#qrModal').modal('show');
                },

                openExistingConsultation(response) {
                    if (response.status === 'completed' && response.form_url) {
                        window.open(response.form_url, '_blank');
                        return;
                    }
                    this.showConsultationQr(response);
                },

                consultationSubmitError(xhr) {
                    const message = (xhr.responseJSON && xhr.responseJSON.error) ?
                        xhr.responseJSON.error :
                        (xhr.statusText || 'Unknown error');
                    alert('Something went wrong: ' + message);
                },

                shereHairSubmission() {
                    if (this.loading) {
                        return;
                    }
                    if (!this.booking_info || !this.booking_info.booking_id || !this.booking_info.client_id) {
                        alert('Please select a client before sharing the hair consultation.');
                        return;
                    }

                    this.loading = true;

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('hair.partial.submit') }}",
                        data: {
                            booking_id: this.booking_info.booking_id,
                            client_id: this.booking_info.client_id,
                            consultation_id: this.consultation_id,
                            client_name: this.client_name,
                            client_mobile: this.client_mobile,
                            client_age: this.client_age,
                            date: this.date,
                            client_phone: this.client_phone,
                            stylist_name: this.stylist_name,
                            designation: this.designation,
                            primary_analysis: this.primary_analysis,
                            history: this.hair_history,
                            client_desire: this.client_desire,
                            photo_permission: this.photo_permission,
                        },
                        success: (response) => {
                            this.loading = false;
                            this.consultation_id = response.form_id;
                            this.showConsultationQr(response);
                        },
                        error: (xhr) => {
                            this.loading = false;
                            this.consultationSubmitError(xhr);
                        }
                    });
                },

                shereSkinSubmission() {
                    if (this.loading) {
                        return;
                    }
                    if (!this.booking_info || !this.booking_info.booking_id || !this.booking_info.client_id) {
                        alert('Please select a client before sharing the skin consultation.');
                        return;
                    }

                    this.loading = true;

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('skin.partial.submit') }}",
                        data: {
                            booking_id: this.booking_info.booking_id,
                            client_id: this.booking_info.client_id,
                            consultation_id: this.consultation_id,
                            client_name: this.client_name,
                            client_mobile: this.client_mobile,
                            client_age: this.client_age,
                            date: this.date,
                            client_phone: this.client_phone,
                            stylist_name: this.stylist_name,
                            designation: this.designation,
                            skin_analysis: this.skin_analysis,
                            life_style: this.life_style,
                            history: this.history,
                            conclusion: this.conclusion,
                            skin_photo_permission: this.skin_photo_permission,
                        },
                        success: (response) => {
                            this.loading = false;
                            this.consultation_id = response.form_id;
                            this.showConsultationQr(response);
                        },
                        error: (xhr) => {
                            this.loading = false;
                            this.consultationSubmitError(xhr);
                        }
                    });
                },


                onPriceBlur(bserv) {

                    if (confirm(
                            "Are you sure you want to update the price for this service?")) {
                        // const basePrice = bserv.service_booking_price || 0;
                        // const taxRate = 0.05;
                        const basePrice = Number(bserv.service_booking_price) || 0;
                        const taxRate = Number(bserv.tax_percent || 0) / 100;

                        const taxExcludeAmount = basePrice / (1 + taxRate);

                        bserv.service_booking_price = parseFloat(taxExcludeAmount.toFixed(2));

                        // Call backend method to update
                        this.updateServicePrice(
                            bserv.booking_service_id,
                            bserv.service_booking_price,
                            bserv.tax_percent
                        );
                    } else {}
                },

                v_totalPrice: function(value1, value2) {

                    total_price = parseFloat(value1) + parseFloat(value2);
                    return total_price.toFixed(2);
                },
                v_totalNetTotal: function(total_amount, discount_amount, quantity) {
                    total_price = (parseFloat(total_amount) * parseInt(quantity)) - parseFloat(
                        discount_amount);

                    return total_price.toFixed(2);
                },
                v_amount: function(value) {
                    var parsed = parseFloat(value);
                    return isNaN(parsed) ? 0 : parsed;
                },
                v_fixed_amount: function(value) {
                    var parsed = parseFloat(value);
                    if (isNaN(parsed)) {
                        return '0.00';
                    }
                    return parsed.toFixed(2);
                },

                markBookingNeedsUpdate: function() {
                    this.booking_needs_update = true;
                },

                hasMultiBeauticianSplit: function(bserv) {
                    if (!bserv) {
                        return false;
                    }
                    if (bserv.split_draft && bserv.split_draft.length > 1) {
                        return true;
                    }
                    return Number(bserv.split_draft_status || 0) === 1
                        && bserv.split_draft
                        && bserv.split_draft.length > 0;
                },

                serviceBeauticianSummary: function(bserv) {
                    if (!bserv) {
                        return '';
                    }
                    if (this.hasMultiBeauticianSplit(bserv) && bserv.split_draft && bserv.split_draft.length) {
                        return bserv.split_draft.map(function(draft) {
                            if (draft.beautician) {
                                var name = draft.beautician.name_locale || draft.beautician.name || '';
                                var code = draft.beautician.beautician_code || '';
                                return code ? (name + ' (' + code + ')') : name;
                            }
                            return 'BT#' + draft.beautician_id;
                        }).join(', ');
                    }
                    if (bserv.beautician) {
                        var label = bserv.beautician.name_locale || bserv.beautician.name || '';
                        if (bserv.beautician.beautician_code) {
                            label += ' (' + bserv.beautician.beautician_code + ')';
                        }
                        return label;
                    }
                    return '';
                },

                isBookingFullyPaid: function() {
                    if (!this.booking_info) {
                        return false;
                    }
                    if (this.booking_info.payment_status === 'completed') {
                        return true;
                    }
                    var paid = this.v_amount(this.booking_info.payment_total);
                    var total = this.v_amount(this.grand_total || this.booking_info.booking_total);
                    if (isNaN(paid) || isNaN(total)) {
                        return false;
                    }
                    return total > 0 && paid === total;
                },
                v_totalTaxTotal(price, quantity, discount, tax_percent) {
                    const totalBeforeDiscount = parseFloat(price) * parseFloat(quantity);
                    const discountValue = parseFloat(discount) || 0;
                    const taxableAmount = totalBeforeDiscount - discountValue;
                    const tax = (taxableAmount * parseFloat(tax_percent)) / 100;
                    return tax.toFixed(2);
                },


                /*
                clientChanged: function(event){
                  var el = event.target;
                  var copy_props = ['name', 'mobile', 'email', 'telephone'];
                  for(var i in copy_props){
                    this.booking_info["client_" + copy_props[i]] = el.options[el.selectedIndex].dataset[copy_props[i]];
                  }
                },
                */

                focusedServiceBeauticians: function() {
                    for (var i in this.booking_info.booking_only_services) {
                        var bkservice = this.booking_info.booking_only_services[i];
                        if (bkservice.service_id == this.focused_active_service) {
                            return bkservice.service.beauticians;
                        }
                    }
                    return [];
                },
                initSelect2: function() {
                    $(".voco-select2").select2();
                },
                changePendingItemsStatus: function(status) {
                    this.is_contains_pending_items = status;
                },

                check_mobile_no: function() {
                    var value = String($('#mobile').val() || '').replace(/\D/g, '');
                    if (value !== $('#mobile').val()) {
                        $('#mobile').val(value);
                    }
                    $('#number_validation').text('');
                    $('.btn-voco-save').removeAttr('disabled');
                    if (value.length !== 10) {
                        $('.btn-voco-save').prop("disabled", true);
                        if ('<?= app()->getLocale() ?>' === 'en') {
                            $('#number_validation').text('Please enter a valid 10-digit mobile number');
                        } else {
                            $('#number_validation').text('الرجاء إدخال رقم جوال صالح من 10 أرقام');
                        }
                    }
                },

                filterProdcts: function() {

                    var search = $('#filter_prodcts').val();
                    $.ajax({
                        method: "POST",
                        url: "{{ route('salon.booking.filter.prodcts') }}",
                        data: {
                            search: search,
                            cat_id: vue_details_tab_edit.change_product_cat_id,
                        },
                    }).done(function(data) {

                        // data =  JSON.parse(data);
                        vue_details_tab_edit.all_items_list = data;
                    });
                },
                refreshSelect2Services: function() {
                    // var added_services = this.booking_info.booking_only_services;
                    var added_services = [];
                    var formatted = [];
                    for (var i in this.all_db_services) {
                        var item = this.all_db_services[i];
                        var exclude = false;
                        for (var j in added_services) {
                            if (item.service_id == added_services[j].service_id) {
                                exclude = true;
                                break;
                            }
                        }

                        if (exclude === false) {

                            formatted.push({
                                "id": item.service_id,
                                "text": item.name + " - SAR " + item.service_base_price,
                                "service_id": item.service_id,
                                "price_id": null
                            });
                        }
                    }

                    //var added_items = this.booking_info.booking_items;
                    var added_items = "";
                    // var formatted = [];
                    for (var i in this.all_db_items) {
                        var item = this.all_db_items[i];
                        var exclude = false;
                        for (var j in added_items) {
                            if (item.price_id == added_items[j].price_id) {
                                exclude = true;
                                break;
                            }
                        }

                        if (exclude === false) {

                            if (item.batch_no == "") {
                                batch_no = "";
                                expiry_date = "";
                            } else {
                                batch_no = item.batch_no;
                                expiry_date = item.expiry_date;
                            }

                            formatted.push({
                                "id": item.price_id,
                                "text": item.item_name + " - SAR " + item.price + " " +
                                    batch_no + " - bardcode - " + item.barcode,
                                "price_id": item.price_id,
                                "batch_no": batch_no,
                                "expiry_date": expiry_date,
                                "service_id": null
                            });
                        }
                    }

                    // if ($("#add_service_id").hasClass("select2-hidden-accessible")) {
                    //     $('#add_service_id').select2("destroy");
                    //     $('#add_service_id').html("");
                    // }

                    // $("#add_service_id").select2({
                    //     "data": formatted,
                    //     templateSelection: function(data, container) {
                    //         $(data.element).attr('data-service-id', data.service_id);
                    //         $(data.element).attr('data-price-id', data.price_id);
                    //         $(data.element).attr('data-batch-no', data.batch_no);
                    //         $(data.element).attr('data-expiry-date', data.expiry_date);
                    //         return data.text;
                    //     }
                    // });
                    // $("#add_service_id").val(null).trigger('change.select2');



                },
                refreshSelect2Items: function() {
                    var added_items = this.booking_info.booking_items;
                    var formatted = [];
                    for (var i in this.all_db_items) {
                        var item = this.all_db_items[i];
                        var exclude = false;
                        for (var j in added_items) {
                            if (item.price_id == added_items[j].price_id) {
                                exclude = true;
                                break;
                            }
                        }

                        if (exclude === false) {
                            formatted.push({
                                "id": item.price_id,
                                "text": item.item_name + " - SAR " + item.price,
                                "price_id": item.price_id,
                                "batch_no": batch_no,
                                "expiry_date": expiry_date
                            });
                        }
                    }

                    if ($("#add_price_id").hasClass("select2-hidden-accessible")) {
                        $('#add_price_id').select2("destroy");
                        $('#add_price_id').html("");
                    }

                    $("#add_price_id").select2({
                        "data": formatted,
                        templateSelection: function(data, container) {
                            $(data.element).attr('data-price-id', data.price_id);
                            $(data.element).attr('data-batch-no', data.batch_no);
                            $(data.element).attr('data-expiry-date', data
                                .expiry_date);
                            return data.text;
                        }
                    });
                    $("#add_price_id").val(null).trigger('change.select2');
                    this.grandTotal_all_raw();

                },
                paymentModeChanged: function(event, target_element) {
                    $("#add_payment_btn").attr("disabled", false);
                    var el = event.target;
                    var $target = $(target_element);
                    if (el.options[el.selectedIndex].value === "card") {

                        if (this.amount_taken > this.grand_total) {
                            var grandTotalValue = isNaN(this.grand_total) ? 0 : this.grand_total;
                            this.amount_taken = grandTotalValue;
                            $('#amount_given').val('given...');
                        }
                        $target.css('display', 'inline-block');
                    } else {
                        $target.css('display', 'none');
                    }
                },
                isPaymentFinalized: function() {
                    // Finalized = invoice/booking code after Update — not merely fully paid
                    return !!(this.booking_info && this.booking_info.booking_code);
                },
                canShowPaymentModeEdit: function(payment_info) {
                    // After booking code only: allow payment mode edit (no delete)
                    if (!this.isPaymentFinalized()) {
                        return false;
                    }
                    if (this.booking_info.canceled_by || this.booking_info.booking_type === 'Cancel') {
                        return false;
                    }
                    return !!this.can_void_completed_payment;
                },
                startEditPaymentMode: function(payment_info) {
                    this.editing_payment_id = payment_info.payment_id;
                },
                cancelEditPaymentMode: function() {
                    this.editing_payment_id = null;
                },
                updatePaymentMode: function(payment_info, event) {
                    var self = this;
                    var newMode = event.target.value;
                    var previousMode = payment_info.payment_mode;

                    if (!newMode || newMode === previousMode) {
                        return;
                    }

                    var doUpdate = function(authPassword) {
                        payment_info.payment_mode = newMode;
                        if (newMode !== 'card') {
                            payment_info.card_type = null;
                        }

                        $.ajax({
                            method: "POST",
                            url: self.urls.form_update_payment_mode_url,
                            data: {
                                booking_id: self.booking_id,
                                payment_id: payment_info.payment_id,
                                payment_mode: newMode,
                                card_type: payment_info.card_type || '',
                                auth_password: authPassword || ''
                            },
                        }).done(function(data) {
                            if (data && data.booking_info) {
                                self.booking_info = data.booking_info;
                            } else if (data && data.vue_data && data.vue_data.vue_details_tab_edit
                                && data.vue_data.vue_details_tab_edit.booking_info) {
                                self.booking_info = data.vue_data.vue_details_tab_edit.booking_info;
                            }
                            self.editing_payment_id = null;
                        }).fail(function() {
                            payment_info.payment_mode = previousMode;
                            event.target.value = previousMode;
                        });
                    };

                    // Completed booking: require password (same as previous void flow)
                    if (self.isPaymentFinalized()) {
                        self.promptAuthPassword(function(password) {
                            if (!password) {
                                event.target.value = previousMode;
                                return;
                            }
                            doUpdate(password);
                        });
                        return;
                    }

                    doUpdate('');
                },
                canShowPaymentVoid: function(payment_info) {
                    if (this.booking_info.canceled_by || this.booking_info.booking_type === 'Cancel') {
                        return false;
                    }

                    if (this.isPaymentFinalized()) {
                        return !!this.can_void_completed_payment;
                    }

                    var mode = (payment_info.payment_mode || '').toLowerCase();
                    if (mode === 'cash') {
                        return !!this.can_void_cash_transaction || !!this.can_void_completed_payment;
                    }
                    if (mode === 'card') {
                        return !!this.can_void_card_transaction || !!this.can_void_completed_payment;
                    }
                    return true;
                },
                promptAuthPassword: function(callback) {
                    bootbox.prompt({
                        title: "{{ __('Enter your password to continue') }}",
                        inputType: 'password',
                        buttons: {
                            cancel: { label: "{{ __('Cancel') }}" },
                            confirm: {
                                label: "{{ __('Confirm') }}",
                                className: 'btn-primary'
                            }
                        },
                        callback: function(result) {
                            callback(result);
                        }
                    });
                },
                requireFinalizedEditAuth: function(callback) {
                    var self = this;
                    if (!self.isPaymentFinalized()) {
                        callback('');
                        return;
                    }
                    if (self._finalized_edit_auth_password) {
                        callback(self._finalized_edit_auth_password);
                        return;
                    }
                    self.promptAuthPassword(function(password) {
                        if (!password) {
                            callback(null);
                            return;
                        }
                        self._finalized_edit_auth_password = password;
                        callback(password);
                    });
                },
                postFinalizedBookingEdit: function(ajaxOptions) {
                    var self = this;
                    var run = function(authPassword) {
                        var data = ajaxOptions.data || {};
                        data.auth_password = authPassword || '';
                        if (!data._token) {
                            data._token = $('meta[name="csrf-token"]').attr('content');
                        }
                        $.ajax($.extend({}, ajaxOptions, { data: data }));
                    };
                    if (self.isPaymentFinalized()) {
                        self.requireFinalizedEditAuth(function(password) {
                            if (password === null) {
                                return;
                            }
                            run(password);
                        });
                    } else {
                        run('');
                    }
                },
                appendAuthPasswordField: function($form, password) {
                    $form.find('input[name="auth_password"]').remove();
                    if (password) {
                        $('<input>', {
                            type: 'hidden',
                            name: 'auth_password',
                            value: password
                        }).appendTo($form);
                    }
                },
                appendEditReasonField: function($form, reason) {
                    var $field = $form.find('input[name="edit_reason"]');
                    if (!$field.length) {
                        $field = $('<input>', {
                            type: 'hidden',
                            name: 'edit_reason'
                        }).appendTo($form);
                    }
                    $field.val(reason || '');
                },
                promptFinalUpdateReason: function(callback) {
                    var self = this;
                    var needPassword = !self._finalized_edit_auth_password;
                    var passwordHtml = needPassword
                        ? '<label class="mt-3 d-block" for="booking_edit_auth_password">' +
                            "{{ __('Your Password') }} <span class=\"text-danger\">*</span></label>" +
                            '<input id="booking_edit_auth_password" type="password" class="form-control" autocomplete="current-password" placeholder="{{ __('Password') }}">'
                        : '';

                    bootbox.dialog({
                        title: "{{ __('Confirm booking update') }}",
                        message:
                            '<p class="mb-2">{{ __('Please enter a reason for this edit.') }}</p>' +
                            '<label class="d-block" for="booking_edit_reason">' +
                                "{{ __('Edit Reason') }} <span class=\"text-danger\">*</span></label>" +
                            '<textarea id="booking_edit_reason" class="form-control" rows="3" maxlength="1000" placeholder="{{ __('Why is this booking being updated?') }}"></textarea>' +
                            passwordHtml,
                        buttons: {
                            cancel: { label: "{{ __('Cancel') }}" },
                            confirm: {
                                label: "{{ __('Confirm & Update') }}",
                                className: 'btn-primary',
                                callback: function() {
                                    var reason = ($('#booking_edit_reason').val() || '').trim();
                                    if (!reason) {
                                        alert("{{ __('Please enter a reason for this edit.') }}");
                                        $('#booking_edit_reason').focus();
                                        return false;
                                    }
                                    var password = needPassword
                                        ? ($('#booking_edit_auth_password').val() || '')
                                        : (self._finalized_edit_auth_password || '');
                                    if (needPassword && !String(password).trim()) {
                                        alert("{{ __('Enter your password to continue') }}");
                                        $('#booking_edit_auth_password').focus();
                                        return false;
                                    }
                                    if (password) {
                                        self._finalized_edit_auth_password = password;
                                    }
                                    callback(reason, password);
                                }
                            }
                        }
                    }).on('shown.bs.modal', function() {
                        var reasonInput = document.getElementById('booking_edit_reason');
                        if (reasonInput) {
                            reasonInput.focus();
                        }
                    });
                },
                updatePaymentCardType: function(payment_info, event) {
                    var self = this;
                    var newCardType = event.target.value;
                    var previousCardType = payment_info.card_type;

                    var doUpdate = function(authPassword) {
                        payment_info.card_type = newCardType;

                        $.ajax({
                            method: "POST",
                            url: self.urls.form_update_payment_mode_url,
                            data: {
                                booking_id: self.booking_id,
                                payment_id: payment_info.payment_id,
                                payment_mode: payment_info.payment_mode,
                                card_type: newCardType,
                                auth_password: authPassword || ''
                            },
                        }).done(function(data) {
                            if (data && data.booking_info) {
                                self.booking_info = data.booking_info;
                            } else if (data && data.vue_data && data.vue_data.vue_details_tab_edit
                                && data.vue_data.vue_details_tab_edit.booking_info) {
                                self.booking_info = data.vue_data.vue_details_tab_edit.booking_info;
                            }
                            self.editing_payment_id = null;
                        }).fail(function() {
                            payment_info.card_type = previousCardType;
                            event.target.value = previousCardType;
                        });
                    };

                    if (self.isPaymentFinalized()) {
                        self.promptAuthPassword(function(password) {
                            if (!password) {
                                event.target.value = previousCardType;
                                return;
                            }
                            doUpdate(password);
                        });
                        return;
                    }

                    doUpdate('');
                },
                applyClientCredit: function() {
                    var self = this;
                    if (self.booking_info.has_client_credit_applied) {
                        return;
                    }
                    var credit = parseFloat(self.booking_info.available_credit || 0);
                    if (!credit || credit <= 0) {
                        return;
                    }

                    var due = parseFloat(self.grand_total || self.booking_info.booking_total || 0)
                        - parseFloat(self.booking_info.payment_total || 0);
                    var applyAmt = Math.min(credit, Math.max(due, 0));
                    if (applyAmt < 0.01) {
                        return;
                    }

                    // Prevent double-click re-apply while request is in flight
                    if (self._applying_client_credit) {
                        return;
                    }
                    self._applying_client_credit = true;

                    bootbox.confirm({
                        title: "{{ __('Apply Client Credit') }}",
                        message: "{{ __('Apply') }} <strong>" + applyAmt.toFixed(2) +
                            "</strong> {{ __('from previous credit toward this booking?') }}",
                        buttons: {
                            cancel: { label: "{{ __('Cancel') }}" },
                            confirm: {
                                label: "{{ __('Apply Credit') }}",
                                className: 'btn-primary'
                            }
                        },
                        callback: function(result) {
                            if (!result) {
                                self._applying_client_credit = false;
                                return;
                            }
                            $.ajax({
                                method: "POST",
                                url: self.urls.form_save_payment_url,
                                data: {
                                    booking_id: self.booking_id,
                                    tab_name: self.tab_name,
                                    grand_total: self.grand_total || self.booking_info.booking_total,
                                    apply_client_credit: 1,
                                    amount_taken: applyAmt,
                                    amount_given: 0,
                                    payment_mode: 'credit'
                                },
                            }).done(function(data) {
                                if (data && data.booking_info) {
                                    self.booking_info = data.booking_info;
                                } else if (data && data.vue_data && data.vue_data.vue_details_tab_edit
                                    && data.vue_data.vue_details_tab_edit.booking_info) {
                                    self.booking_info = data.vue_data.vue_details_tab_edit.booking_info;
                                }
                                self.amount_taken_flag = 0;
                                self.$nextTick(function() {
                                    self.grandTotal_all_raw();
                                });
                            }).always(function() {
                                self._applying_client_credit = false;
                            });
                        }
                    });
                },
                manageSubmitButton: function() {
                    // Show the loading spinner
                    $('#add_service_id').hide();
                    $('#loading_spinner').show();

                    setTimeout(function() {
                        // Hide the loading spinner
                        $('#loading_spinner').hide();

                    }, 2000); // Adjust delay as needed (2000ms = 2 seconds)
                },


                grandTotal_all_raw: function() {
                    var shouldAutoFillAmount = !this.is_editing_amount_paid && this.amount_taken_flag == 0;

                    all_booking_info = this.booking_info;
                    var taxTotal = 0;
                    var subTotal = 0;
                    var toNum = function(value) {
                        var n = parseFloat(value);
                        return isNaN(n) ? 0 : n;
                    };
                    var services = this.booking_info.booking_only_services || [];
                    var lineDiscountTotal = 0;
                    for (var i = 0; i < services.length; i++) {
                        var bserv = services[i];
                        var discount = this.calculateDiscount(
                            bserv.service_booking_price,
                            bserv.quantity,
                            bserv.individual_discount_percent,
                            bserv.discount_percent
                        );
                        lineDiscountTotal += toNum(discount);
                        var lineNetTotal = toNum(this.v_totalNetTotal(
                            bserv.service_booking_price,
                            discount,
                            bserv.quantity
                        ));
                        var linTaxTotal = toNum(this.v_totalTaxTotal(
                            bserv.service_booking_price,
                            bserv.quantity,
                            discount,
                            bserv.tax_percent
                        ));
                        taxTotal += linTaxTotal;
                        subTotal += lineNetTotal;
                    }

                    var items = this.booking_info.booking_items || [];
                    for (var j = 0; j < items.length; j++) {
                        var oItem = items[j];
                        var qty = toNum(oItem.quantity) || 1;
                        var lineItemNetTotal = 0;
                        var itemDiscountAmt = 0;
                        if (toNum(oItem.individual_discount_amt) > 0) {
                            itemDiscountAmt = toNum(oItem.individual_discount_amt);
                            lineItemNetTotal = toNum(this.v_totalNetTotal(
                                oItem.selling_price,
                                oItem.individual_discount_amt,
                                qty
                            ));
                        } else {
                            itemDiscountAmt = toNum(oItem.discount_amount);
                            lineItemNetTotal = toNum(this.v_totalNetTotal(
                                toNum(oItem.selling_price) - (toNum(oItem.discount_amount) / qty),
                                oItem.individual_discount_amt || 0,
                                qty
                            ));
                        }
                        lineDiscountTotal += itemDiscountAmt;
                        taxTotal += toNum(oItem.tax_amount);
                        subTotal += lineItemNetTotal;
                    }

                    vue_details_tab_edit.grand_sub_total = subTotal.toFixed(2);
                    this.grand_tax_total = taxTotal.toFixed(2);

                    var appliedDiscountPrice = toNum(this.booking_info.applied_discount_price);
                    var appliedRoundoffPrice = toNum(this.booking_info.applied_roundoff_price);
                    var paymentTotal = toNum(this.booking_info.payment_total);
                    this.summary_discount_total = Math.round((lineDiscountTotal + appliedDiscountPrice) * 100) / 100;

                    vue_details_tab_edit.grand_total = Math.round(
                            (subTotal + taxTotal - appliedDiscountPrice - appliedRoundoffPrice) * 100
                        ) / 100;

                    vue_details_tab_edit.grand_total_1 = (subTotal + taxTotal).toFixed(2);

                    // Only auto-fill remaining balance when user is not entering a split amount
                    if (shouldAutoFillAmount) {
                        var balance = (subTotal + taxTotal) - paymentTotal - appliedDiscountPrice - appliedRoundoffPrice;
                        var amountValue = Math.abs(balance);
                        vue_details_tab_edit.amount_taken = isNaN(amountValue) ? 0 : Number(amountValue.toFixed(2));
                        this.syncAmountTakenInput();
                    }
                },

                syncAmountTakenInput: function() {
                    if (this.is_editing_amount_paid) {
                        return;
                    }
                    var val = this.amount_taken;
                    if (val === null || val === '' || typeof val === 'undefined' || isNaN(val)) {
                        this.amount_taken_input = '';
                        return;
                    }
                    this.amount_taken_input = String(val);
                },

                onAmountPaidFocus: function(event) {
                    this.is_editing_amount_paid = true;
                    this.amount_taken_flag = 1;
                    if (event && event.target && typeof event.target.select === 'function') {
                        event.target.select();
                    }
                },

                onAmountPaidInput: function(el, target_element, type) {
                    this.is_editing_amount_paid = true;
                    this.amount_taken_flag = 1;

                    // Keep raw typed text (allows "200." while entering decimals)
                    var raw = (this.amount_taken_input || '').toString().replace(/[^\d.]/g, '');
                    var parts = raw.split('.');
                    if (parts.length > 2) {
                        raw = parts[0] + '.' + parts.slice(1).join('');
                    }
                    this.amount_taken_input = raw;

                    var parsed = parseFloat(raw);
                    this.amount_taken = (raw === '' || isNaN(parsed)) ? '' : parsed;
                    this.amountTakenChanged(el, target_element, type);
                },

                onAmountPaidBlur: function() {
                    this.is_editing_amount_paid = false;
                    var parsed = parseFloat(this.amount_taken_input);
                    if (this.amount_taken_input === '' || isNaN(parsed)) {
                        this.amount_taken = '';
                        this.amount_taken_input = '';
                        return;
                    }
                    this.amount_taken = Number(parsed.toFixed(2));
                    this.amount_taken_input = String(this.amount_taken);
                },

                remainingBalanceDue: function() {
                    var due = this.v_amount(this.grand_total || this.booking_info.booking_total) -
                        this.v_amount(this.booking_info.payment_total);
                    if (isNaN(due)) {
                        return 0;
                    }
                    return Number(Math.max(due, 0).toFixed(2));
                },

                prepareNextSplitPayment: function() {
                    var due = this.remainingBalanceDue();
                    if (due <= 0) {
                        this.amount_taken = 0;
                        this.amount_taken_input = '';
                        this.amount_taken_flag = 0;
                        this.is_editing_amount_paid = false;
                        $('#amount_given, #amount_given_refund').val('');
                        return;
                    }

                    this.amount_taken_flag = 0;
                    this.is_editing_amount_paid = false;
                    this.amount_taken = due;
                    this.syncAmountTakenInput();
                    $('#amount_given, #amount_given_refund').val('');

                    this.$nextTick(function() {
                        var $input = $('#amount_taken:visible, #amount_taken_refund:visible').first();
                        if ($input.length) {
                            $input.focus();
                            if ($input[0].select) {
                                $input[0].select();
                            }
                        }
                    });
                },

                fillRemainingBalance: function() {
                    this.prepareNextSplitPayment();
                },

                resetSplitPaymentForm: function() {
                    this.split_payment = {
                        cash: '',
                        card: '',
                        online: '',
                        credit: '',
                        card_type: '',
                        cash_tendered: '',
                    };
                },

                openSplitPaymentModal: function() {
                    this.split_payment_mode = 'new';
                    this._rebalance_auth_password = null;
                    this.resetSplitPaymentForm();
                    var due = this.remainingBalanceDue();
                    if (due > 0) {
                        this.split_payment.cash = String(Number(due.toFixed(2)));
                    }
                    $('#split-payment-modal').modal('show');
                },

                openRebalancePaymentModal: function() {
                    var self = this;
                    if (!this.isPaymentFinalized() || !this.can_void_completed_payment) {
                        return;
                    }

                    this.requireFinalizedEditAuth(function(password) {
                        if (password === null) {
                            return;
                        }

                        self.split_payment_mode = 'rebalance';
                        self._rebalance_auth_password = password;
                        self.resetSplitPaymentForm();

                        var payments = (self.booking_info.payments || []);
                        var modeTotals = { cash: 0, card: 0, online: 0, credit: 0 };
                        payments.forEach(function(payment) {
                            var mode = String(payment.payment_mode || '').toLowerCase();
                            var net = parseFloat(payment.amount_taken || 0) - parseFloat(payment.amount_given || 0);
                            if (isNaN(net) || net <= 0 || !modeTotals.hasOwnProperty(mode)) {
                                return;
                            }
                            modeTotals[mode] += net;
                        });

                        Object.keys(modeTotals).forEach(function(mode) {
                            if (modeTotals[mode] > 0) {
                                self.split_payment[mode] = String(Number(modeTotals[mode].toFixed(2)));
                            }
                        });

                        for (var i = 0; i < payments.length; i++) {
                            if (payments[i].payment_mode === 'card' && payments[i].card_type) {
                                self.split_payment.card_type = payments[i].card_type;
                                break;
                            }
                        }

                        $('#split-payment-modal').modal('show');
                    });
                },

                fillSplitPaymentBalance: function() {
                    if (this.split_payment_mode === 'rebalance') {
                        var total = this.splitPaymentTargetTotal;
                        this.split_payment = {
                            cash: total > 0 ? String(Number(total.toFixed(2))) : '',
                            card: '',
                            online: '',
                            credit: '',
                            card_type: '',
                            cash_tendered: this.split_payment.cash_tendered || '',
                        };
                        return;
                    }

                    var others = this.v_amount(this.split_payment.card)
                        + this.v_amount(this.split_payment.online)
                        + this.v_amount(this.split_payment.credit);
                    var due = this.remainingBalanceDue();
                    var cashFill = Math.max(due - others, 0);
                    this.split_payment.cash = cashFill > 0 ? String(Number(cashFill.toFixed(2))) : '';
                },

                submitSplitPayments: function() {
                    var self = this;
                    if (!this.canSaveSplitPayment || this.split_payment_saving) {
                        return;
                    }

                    var isRebalance = this.split_payment_mode === 'rebalance';
                    var url = isRebalance
                        ? self.urls.form_rebalance_payment_url
                        : self.urls.form_save_split_payment_url;

                    self.split_payment_saving = true;
                    $.ajax({
                        method: 'POST',
                        url: url,
                        data: {
                            _token: '{{ csrf_token() }}',
                            booking_id: self.booking_id,
                            tab_name: self.tab_name,
                            grand_total: self.grand_total || self.booking_info.booking_total,
                            cash: self.v_amount(self.split_payment.cash),
                            card: self.v_amount(self.split_payment.card),
                            online: self.v_amount(self.split_payment.online),
                            credit: self.v_amount(self.split_payment.credit),
                            card_type: self.split_payment.card_type,
                            cash_tendered: self.v_amount(self.split_payment.cash_tendered),
                            auth_password: isRebalance ? (self._rebalance_auth_password || '') : '',
                        },
                    }).done(function(data) {
                        if (data && data.js_notifications && (data.js_notifications.warning || data.js_notifications.error)) {
                            var warnType = data.js_notifications.error ? 'error' : 'warning';
                            var warnMsg = data.js_notifications.error || data.js_notifications.warning;
                            if (typeof js_notify === 'function') {
                                js_notify(warnMsg, warnType);
                            } else {
                                alert(warnMsg);
                            }
                            return;
                        }

                        $('#split-payment-modal').modal('hide');
                        self.split_payment_mode = 'new';
                        self._rebalance_auth_password = null;
                        if (typeof paymentItemSaved === 'function') {
                            paymentItemSaved(null, data);
                        }
                    }).fail(function(xhr) {
                        var msg = '{{ __('Could not save split payment.') }}';
                        if (xhr.responseJSON && xhr.responseJSON.js_notifications) {
                            msg = xhr.responseJSON.js_notifications.warning
                                || xhr.responseJSON.js_notifications.error
                                || xhr.responseJSON.js_notifications.info
                                || msg;
                        }
                        alert(msg);
                    }).always(function() {
                        self.split_payment_saving = false;
                    });
                },


                // grandTotal_all_raw: function() {

                //     if (vue_details_tab_edit.amount_taken_flag == 0) {
                //         all_booking_info = this.booking_info;
                //         var grandTotal = 0;
                //         var taxTotal = 0;
                //         var subTotal = 0;
                //         for (var i in this.booking_info.booking_only_services) {
                //             var service_id = this.booking_info.booking_only_services[i]
                //                 .booking_service_id;
                //             var html_id = "#total_" + service_id;
                //             var tax_html_id = "#tax_" + service_id;
                //             var net_html_id = '#net_' + service_id;
                //             var lineTotal = $(html_id).text();
                //             var linTaxTotal = $(tax_html_id).text();
                //             var lineNetTotal = $(net_html_id).text();

                //             grandTotal = parseFloat(lineTotal) + parseFloat(grandTotal);
                //             taxTotal = parseFloat(linTaxTotal) + parseFloat(taxTotal);
                //             subTotal = parseFloat(lineNetTotal) + parseFloat(subTotal);

                //         }

                //         for (var i in this.booking_info.booking_items) {
                //             var item_id = this.booking_info.booking_items[i]
                //                 .booking_item_id;
                //             var html_id = "#total_item_" + item_id;
                //             var tax_html_id = "#tax_" + item_id;
                //             var net_item_html_id = "#net_item_" + item_id;
                //             var qty = this.booking_info.booking_items[i]
                //                 .quantity;


                //             var lineTotal = $(html_id).text();
                //             var linTaxTotal = $(tax_html_id).text();
                //             var lineItemNetTotal = $(net_item_html_id).text();

                //             grandTotal = parseFloat(lineTotal * qty) + parseFloat(grandTotal);
                //             taxTotal = parseFloat(linTaxTotal * qty) + parseFloat(taxTotal);
                //             subTotal = parseFloat(lineItemNetTotal * qty) + parseFloat(subTotal);


                //         }


                //         vue_details_tab_edit.grand_sub_total = parseFloat(subTotal).toFixed(2);


                //         vatAmount = subTotal * (18 / 100);

                //         vue_details_tab_edit.grand_tax_total = parseFloat(vatAmount).toFixed(2);
                //         vue_details_tab_edit.grand_total = parseFloat(subTotal + vatAmount).toFixed(
                //             2);
                //         vue_details_tab_edit.grand_total_1 = parseFloat(subTotal + vatAmount)
                //             .toFixed(2);

                //         total = Number(subTotal + vatAmount) - this.booking_info.payment_total;
                //         total = parseFloat(total).toFixed(2);
                //         vue_details_tab_edit.amount_taken = Math.abs(total);

                //     }
                //     vue_details_tab_edit.amount_taken_flag = 0;
                // },


                roundoff(grandTotal, bookingInfo) {

                    let roundedValue = grandTotal - Math.round(grandTotal);

                    let url = '{{ route('store-roundoff') }}';

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            roundoffValue: roundedValue,
                            booking_id: bookingInfo.booking_id,
                        },
                        success: function(response) {
                            if (response.success) {
                                let data = response.roundoffValue;
                                location.reload();
                            }
                        },
                        error: function(xhr, status, error) {
                        }
                    });
                },


                // roundedGrandTotal() {
                //     let appliedDiscountPrice = this.booking_info.applied_discount_price || 0;
                //     let discountedTotal = this.grand_total_1 - appliedDiscountPrice;
                //     return this.roundOffApplied ? Math.round(discountedTotal) : discountedTotal;
                //     // return this.roundOffApplied ? Math.round(this.grand_total_1) : this.grand_total_1;
                // },


                updateinvoice: function(params) {
                    // Finalized and no pending edits: print only.
                    if (
                        this.booking_info.booking_code
                        && this.booking_info.payment_status === 'completed'
                        && !this.booking_needs_update
                    ) {
                        window.open(params, "",
                            "toolbar=no,status=no,menubar=no,location=center,scrollbars=no,resizable=no,height=500,width=657"
                        );
                        return;
                    }

                    $('#print_and_update').val(1);
                    $('#print_and_update_url').val(params);
                    $('#form1').trigger('submit');
                },
                refreshDiscountFunction: function() {
                    if (!this.is_editing_amount_paid) {
                        this.amount_taken_flag = 0;
                    }
                    this.grandTotal_all_raw();
                },
                onProductQtyInput: function(item, discount, booking_id, unit_price, tax_amont_1, id, html, flush) {
                    var self = this;
                    if (!this._productQtyTimers) {
                        this._productQtyTimers = {};
                    }

                    // Live recalculate Net/Tax/Total and summary while typing
                    this.previewProductQty(item, unit_price, html);

                    clearTimeout(this._productQtyTimers[id]);
                    var delay = flush ? 0 : 400;
                    this._productQtyTimers[id] = setTimeout(function() {
                        self.addQty(item, discount, booking_id, unit_price, tax_amont_1, id, html);
                    }, delay);
                },

                previewProductQty: function(item, unit_price, html) {
                    var qty = parseFloat($(html).val() || 0);
                    if (!(qty > 0)) {
                        return;
                    }

                    var individual_discount = item.individual_discount_percent;
                    var global_discount = item.discount_percent;
                    var unitPrice = parseFloat(unit_price) * qty;
                    var discount_amt = 0;
                    if (individual_discount > 0) {
                        discount_amt = (individual_discount * unitPrice) / 100;
                    } else {
                        discount_amt = (global_discount * unitPrice) / 100;
                    }

                    var discounted_price = unitPrice - discount_amt;
                    var tax_amount = (parseFloat(discounted_price) * item.tax_percent) / 100;
                    var lineTotal = discounted_price + parseFloat(tax_amount);

                    item.quantity = qty;
                    item.tax_amount = tax_amount;
                    if (individual_discount > 0) {
                        item.individual_discount_amt = discount_amt;
                    } else {
                        item.discount_amount = discount_amt;
                    }
                    item.line_total = lineTotal;
                    this.grandTotal_all_raw();
                },

                addQty: function(item, discount, booking_id, unit_price, tax_amont_1, id, html, ) {
                    var discount_amt = 0;
                    var individual_discount = item.individual_discount_percent;
                    var global_discount = item.discount_percent;
                    var discount_type = 'global';
                    var previousQty = item.quantity;
                    var previousTax = item.tax_amount;
                    var previousDiscount = item.discount_amount;
                    var previousIndividualDiscount = item.individual_discount_amt;
                    var previousLineTotal = item.line_total;

                    var qty = parseFloat($(html).val() || 0);
                    var unitPrice = parseFloat(unit_price) * qty;
                    if (individual_discount > 0) {
                        discount_type = 'individual';
                        discount_amt = (individual_discount * unitPrice) / 100;
                    } else {
                        discount_type = 'global';
                        discount_amt = (global_discount * unitPrice) / 100;
                    }

                    var discounted_price = unitPrice - discount_amt;
                    var tax_amount = (parseFloat(discounted_price) * item.tax_percent) / 100;
                    var lineTotal = discounted_price + parseFloat(tax_amount);

                    if (qty > 0) {
                        // Optimistic Vue update so Net/Tax/Total refresh live
                        item.quantity = qty;
                        item.tax_amount = tax_amount;
                        if (discount_type === 'individual') {
                            item.individual_discount_amt = discount_amt;
                        } else {
                            item.discount_amount = discount_amt;
                        }
                        item.line_total = lineTotal;
                        this.grandTotal_all_raw();

                        // Cancel any overlapping in-flight save for this line
                        if (!this._productQtyXhrs) {
                            this._productQtyXhrs = {};
                        }
                        if (this._productQtyXhrs[id]) {
                            this._productQtyXhrs[id].abort();
                        }

                        this._productQtyXhrs[id] = $.ajax({
                            method: "POST",
                            url: "{{ route('salon.booking.qty.save') }}",
                            data: {
                                booking_id: booking_id,
                                item_id: id,
                                tax_amount: tax_amount,
                                line_total: lineTotal,
                                discount_amt: discount_amt,
                                discount_type: discount_type,
                                qty: qty
                            },
                        }).done(function(data) {
                            // Revert optimistic UI if stock check (or other) failed
                            if (data && data.js_notifications && data.js_notifications.error) {
                                item.quantity = previousQty;
                                item.tax_amount = previousTax;
                                item.discount_amount = previousDiscount;
                                item.individual_discount_amt = previousIndividualDiscount;
                                item.line_total = previousLineTotal;
                                $(html).val(previousQty);
                            } else if (vue_details_tab_edit.booking_info && vue_details_tab_edit.booking_info.booking_code) {
                                vue_details_tab_edit.markBookingNeedsUpdate();
                            }

                            if (data && data.vue_data && data.vue_data.vue_details_tab_edit
                                && data.vue_data.vue_details_tab_edit.booking_info) {
                                vue_details_tab_edit.booking_info =
                                    data.vue_data.vue_details_tab_edit.booking_info;
                            }
                            vue_details_tab_edit.grandTotal_all_raw();
                        }).fail(function(xhr) {
                            if (xhr && xhr.statusText === 'abort') {
                                return;
                            }
                            item.quantity = previousQty;
                            item.tax_amount = previousTax;
                            item.discount_amount = previousDiscount;
                            item.individual_discount_amt = previousIndividualDiscount;
                            item.line_total = previousLineTotal;
                            $(html).val(previousQty);
                            vue_details_tab_edit.grandTotal_all_raw();
                        });
                    }
                },
                updateRefundQty: function(item, discount, booking_id, unit_price, tax_amont_1,
                    tax_percent, booking_item_id, html) {

                    var discount_amt = 0;
                    var individual_discount = item.individual_discount_percent;
                    var global_discount = item.discount_percent;

                    var refundedBookingId = item.refunded_booking_id;
                    var discount_type = 'global';

                    var qty = parseFloat($(html).val() || 0);
                    var unitPrice = parseFloat(unit_price) * qty;

                    if (individual_discount > 0) {
                        discount_type = 'individual';
                        discount_amt = (individual_discount * unitPrice) / 100;
                    } else {
                        discount_type = 'global';
                        discount_amt = (global_discount * unitPrice) / 100;
                    }

                    var discounted_price = unitPrice - discount_amt;
                    var tax_amount = (discounted_price * parseFloat(tax_percent)) / 100;
                    var lineTotal = discounted_price + tax_amount;

                    // ✅ Update Vue reactive item object
                    item.quantity = qty;
                    item.tax_amount = tax_amount; // <--- important
                    item.discount_amount = discount_amt;
                    item.line_total = lineTotal;

                    if (qty > 0) {
                        $.ajax({
                            method: "POST",
                            url: "{{ route('salon.booking.refund.qty.save') }}",
                            data: {
                                booking_id: booking_id,
                                booking_item_id: booking_item_id,
                                item_id: item.item_id,
                                tax_amount: tax_amount,
                                line_total: lineTotal,
                                discount_amt: discount_amt,
                                refunded_booking_id: refundedBookingId,
                                discount_type: discount_type,
                                qty: qty
                            },
                        }).done(function(data) {
                            // optional success handling
                        });
                    }
                },

                updateRefundServiceQty: function(bserv, html) {
                    var qty = parseFloat($(html).val() || 0);
                    if (qty <= 0) {
                        return;
                    }

                    var unitPrice = parseFloat(bserv.service_booking_price || 0) * qty;
                    var individual_discount = parseFloat(bserv.individual_discount_percent || 0);
                    var global_discount = parseFloat(bserv.discount_percent || 0);
                    var tax_percent = parseFloat(bserv.tax_percent || 0);
                    var discount_amt = 0;
                    var discount_type = 'global';

                    if (individual_discount > 0) {
                        discount_type = 'individual';
                        discount_amt = (individual_discount * unitPrice) / 100;
                    } else {
                        discount_amt = (global_discount * unitPrice) / 100;
                    }

                    var discounted_price = unitPrice - discount_amt;
                    var tax_amount = (discounted_price * tax_percent) / 100;
                    var lineTotal = discounted_price + tax_amount;

                    bserv.quantity = qty;
                    bserv.tax_amount = tax_amount;
                    if (discount_type === 'individual') {
                        bserv.individual_discount_amt = discount_amt;
                    } else {
                        bserv.discount_amount = discount_amt;
                    }
                    bserv.line_total = lineTotal;

                    $.ajax({
                        method: "POST",
                        url: "{{ route('salon.booking.refund.service.qty.save') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            booking_id: bserv.booking_id,
                            booking_service_id: bserv.booking_service_id,
                            service_id: bserv.service_id,
                            tax_amount: tax_amount,
                            line_total: lineTotal,
                            discount_amt: discount_amt,
                            refunded_booking_id: bserv.refunded_booking_id,
                            discount_type: discount_type,
                            qty: qty
                        },
                    }).done(function(data) {
                        if (data && data.vue_data && data.vue_data.vue_details_tab_edit) {
                            Object.assign(vue_details_tab_edit, data.vue_data.vue_details_tab_edit);
                        }
                    });
                },

                getRfundData: function() {
                    if (this.is_contains_pending_items) {
                        alert("You can't refund. Some items are not delivered.")
                    } else {
                        var servIDs = $("input[name='service[]']").map(function() {
                            if ($(this).is(':checked')) {
                                return $(this).val();
                            }

                        });
                        var itemIDs = $("input[name='item[]']").map(function() {
                            if ($(this).is(':checked')) {
                                return $(this).val();
                            }
                        });

                        if (!servIDs.get().length && !itemIDs.get().length) {
                            alert("Please select at least one item or service to refund.");
                            return;
                        }

                        $.ajax({
                            method: "POST",
                            url: "{{ route('salon.booking.refund.items') }}",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                serveRefundIds: servIDs.get(),
                                itemRefundIds: itemIDs.get(),
                            },
                        }).done(function(id) {
                            if (id && typeof id === 'object') {
                                var msg = (id.js_notifications && (id.js_notifications.error || id.js_notifications.warning))
                                    ? (id.js_notifications.error || id.js_notifications.warning)
                                    : 'Could not create refund booking.';
                                alert(msg);
                                return;
                            }
                            var url_page = "/salon/booking/tab/" + id + "/edit";
                            window.location.href = url_page;
                        }).fail(function(xhr) {
                            var msg = 'Could not create refund booking.';
                            if (xhr.responseJSON && xhr.responseJSON.js_notifications) {
                                msg = xhr.responseJSON.js_notifications.error
                                    || xhr.responseJSON.js_notifications.warning
                                    || msg;
                            }
                            alert(msg);
                        });
                    }
                },
                //========================== new added section ============================================
                updatePrice: function(booking_service_id) {
                    $.ajax({
                        method: "POST",
                        url: "{{ route('salon.booking.price.update') }}",
                        data: {
                            booking_service_id: booking_service_id,
                            service_id: vue_details_tab_edit.service_id,
                            booking_id: this.booking_id,
                            shift_id: $("#shift_data :selected").val()
                        },
                    }).done(function(data) {

                    });
                },
                getQty: function(item_code) {


                    // if(vue_details_tab_edit.current_checked_qty_item_code != ''){
                    //     old_html_id = '#check_qty_'+vue_details_tab_edit.current_checked_qty_item_code;

                    //     $(old_html_id).empty();

                    //     $(old_html_id).append('<label style="width: 18%;color: white; background: cadetblue; padding: 4px;"  @click="getQty('+ vue_details_tab_edit.current_checked_qty_item_code +')" class="btn btn-primary">{{ __('Check Qty') }}</label></div> </label>');

                    // }

                    vue_details_tab_edit.current_checked_qty_item_code =
                        item_code; // '#check_qty_'+item_code;
                    $.ajax({
                        method: "POST",
                        url: "",
                        data: {
                            item_code: item_code,
                        },
                    }).done(function(data) {

                        html_id = '#check_qty_' + item_code;
                        $(html_id).empty();
                        $(html_id).append(
                            '<span style="color: #20a8d8;">{{ __('Actual Qty:') }}' +
                            "  " + data.current_qty + '</span>');
                        $(html_id).append(' | ');
                        $(html_id).append(
                            '<span style="color: #20a8d8;">{{ __('Reserved Qty:') }}' +
                            "  " + data.reserved_qty + '</span>');
                        $(html_id).append(' | ');
                        $(html_id).append(
                            '<span style="color: #20a8d8;">{{ __('Available Qty:') }}' +
                            "  " + data.available_qty + '</span>');

                        // {"current_qty":3,"reserved_qty":2,"available_qty":1}
                    });
                },
                lineDiscount: function(obj, bserv, form_id, service_html, grand_total_html,
                    percentage_html_id) {
                    $(service_html).val(bserv.service_id);
                    $(grand_total_html).val(bserv.service_booking_price);
                    var percentage = $(percentage_html_id).val();

                    this.appliedDiscountPercentage = percentage;

                    $.ajax({
                        method: "POST",
                        url: "{{ route('salon.booking.discount.save') }}",
                        data: {
                            applied_discount_percent: percentage,
                            booking_id: this.booking_id,
                            service_id: bserv.service_id,
                            booking_service_id: bserv.booking_service_id,
                            booking_item_id: 0,
                            total_price: bserv.service_booking_price,
                            quantity: bserv.quantity,
                        },
                    }).done(function(data) {
                        if (typeof processFormAjax === 'function') {
                            processFormAjax($('<form></form>'), data);
                        }
                        if (typeof discountSaved === 'function') {
                            discountSaved(null, data);
                        }
                    });
                },
                lineDiscountItem: function(obj, bserv, form_id, service_html, grand_total_html,
                    percentage_html_id) {

                    $(service_html).val(bserv.item_id);
                    $(grand_total_html).val(bserv.selling_price);
                    var percentage = $(percentage_html_id).val();
                    $.ajax({
                        method: "POST",
                        url: "{{ route('salon.booking.discount.save') }}",
                        data: {
                            applied_discount_percent: percentage,
                            booking_id: this.booking_id,
                            item_id: bserv.item_id,
                            booking_item_id: bserv.booking_item_id,
                            total_price: bserv.selling_price,
                            quantity: bserv.quantity || 1,
                            booking_service_id: 0
                        },
                    }).done(function(data) {
                        if (typeof processFormAjax === 'function') {
                            processFormAjax($('<form></form>'), data);
                        }
                        if (typeof discountSaved === 'function') {
                            discountSaved(null, data);
                        }
                    });
                },
                getTimeSlotBtn: function(btn_id, form_id, slot_start) {
                    vue_details_tab_edit.btn_slot_id = btn_id;

                    var price = $(vue_details_tab_edit.changing_price_var).text();
                    var tax = $(vue_details_tab_edit.tax_var).text();
                    var total = parseFloat(price) + parseFloat(tax);
                    $(vue_details_tab_edit.total_price_var).text(total);
                    $(vue_details_tab_edit.total_price_var_discount).val(price);

                    if (vue_details_tab_edit.temp_beatician_id) {
                        vue_details_tab_edit.beautician_id = vue_details_tab_edit.temp_beatician_id;
                    }

                    if (vue_details_tab_edit.beautician_id == "") {
                        alert('beautician not selected');
                        return 0;
                    }

                    var self = this;
                    $.ajax({
                        method: "POST",
                        url: "{{ route('salon.booking.item.update') }}",
                        data: {
                            service_id: vue_details_tab_edit.service_id,
                            booking_id: vue_details_tab_edit.booking_id,
                            beautician_id: vue_details_tab_edit.beautician_id,
                            booking_service_id: vue_details_tab_edit.booking_service_id,
                            shift_id: $("#shift_data").val(),
                            appointment_date: $("#booking_date").val(),
                            slot_start: slot_start
                        },
                    }).done(function(data) {
                        if (data && data.booking_info) {
                            vueMergeBookingInfo(data);
                        }

                        if (vue_details_tab_edit.booking_info && vue_details_tab_edit.booking_info.booking_code) {
                            vue_details_tab_edit.markBookingNeedsUpdate();
                            sessionStorage.setItem(
                                'booking_needs_update_' + vue_details_tab_edit.booking_id,
                                '1'
                            );
                        }

                        self.updatePrice(vue_details_tab_edit.booking_service_id);
                        self.grandTotal_all_raw();
                        $('#beautician-slot-modal').modal('hide');

                        if (data && data.js_notifications && data.js_notifications.success && typeof js_notify === 'function') {
                            js_notify(data.js_notifications.success, 'success');
                        }
                    }).fail(function(xhr) {
                        var msg = '{{ __('Failed to update beautician.') }}';
                        if (xhr.responseJSON && xhr.responseJSON.js_notifications) {
                            msg = xhr.responseJSON.js_notifications.error
                                || xhr.responseJSON.js_notifications.warning
                                || msg;
                        }
                        alert(msg);
                    });
                },
                addTimeSlot: function(slot) {

                    // $('#add_service_id').show();
                    vue_details_tab_edit.selected_time_slot = slot;
                    // localStorage.setItem("time_slot", slot)

                },
                getShiftData: function(data, price_id, tax, total, discount_total,
                    service_html_id,
                    bu_html_id, booking_service_id, shift_id) {
                    var b_serv = [];
                    b_serv = data;
                    this.beauticians = [];
                    this.new_duration = [];
                    $("#shift_data").val();
                    $("#duration_minutes").val();
                    $('#beautician-slot-modal').modal('show');
                    vue_details_tab_edit.changing_price_var = price_id;
                    vue_details_tab_edit.total_price_var = total;
                    vue_details_tab_edit.tax_var = tax;
                    vue_details_tab_edit.total_price_var_discount = discount_total;
                    vue_details_tab_edit.service_id_html = service_html_id;
                    vue_details_tab_edit.shift_id = shift_id;
                    //if ( vue_details_tab_edit.service_id == "" ) {

                    $(vue_details_tab_edit.service_id_html).val(data.service_id);

                    vue_details_tab_edit.service_id = data.service_id;
                    vue_details_tab_edit.booking_service_id = booking_service_id;

                    // }
                    //=========================  call booked slot update section =====================================================

                    var bu = $(bu_html_id).text();
                    const newText = bu.split(/\s/).join('');
                    var services = [];
                    services = this.booking_info.booking_only_services;
                    if (newText != "") {

                        $.ajax({
                            method: "POST",
                            url: "{{ route('all.booking.existing') }}",
                            data: {
                                service: data.service_id,
                                is_pos: 1,
                                service_type: 3,
                                shift_id: vue_details_tab_edit.shift_id,
                                booking_service_id: vue_details_tab_edit
                                    .booking_service_id
                            },
                        }).done(function(data) {
                            $('#booking_date').val(data.appointment_date);
                            if (data.all_db_shifts.length > 0) {
                                vue_details_tab_edit.all_shifts = data.all_db_shifts;
                            }
                            this.db_shift = b_serv.db_shift;


                            if (data.beauticians.length > 0) {
                                //  bu_selcted_data = b_serv.beauticians[0];
                                vue_details_tab_edit.beautician_id = b_serv
                                    .beautician_id;
                                bu_selected = "#bu" + b_serv.beautician_id;
                                time_selected = b_serv.slot_start;
                                vue_details_tab_edit.time_selected = time_selected
                                    .slice(0,
                                        -3);

                                setTimeout(function() {
                                    $(bu_selected).addClass("bu-selected");
                                    //$(time_selected).addClass("bu-selected");

                                    $("#shift_data").val(b_serv.shift_id);
                                    if (data.new_duration.length > 0) {
                                        vue_details_tab_edit.new_duration = data
                                            .new_duration;
                                        vue_details_tab_edit.duration_minutes =
                                            vue_details_tab_edit.new_duration[0]
                                            [0];
                                    }

                                }, 1000);

                            }
                            vue_details_tab_edit.time_slot = data.time_slot;



                            vue_details_tab_edit.applyBeauticianList(data.beauticians);
                            $('input[name=time]').attr('checked', false);
                            if (!data.beautician_flag) {
                                $('input[name=beautician]').attr('checked', false);
                            }

                        });

                    } else {
                        //==========================  end section =============================================
                        var bu_selected_htm_id = "";
                        $.ajax({
                            method: "POST",
                            url: "{{ route('all.booking') }}",
                            data: {
                                service: data.service_id,
                                is_pos: 1,
                                service_type: 3,
                                shift_id: vue_details_tab_edit.shift_id
                            },
                        }).done(function(data) {

                            if (data.all_db_shifts.length > 0) {
                                vue_details_tab_edit.all_shifts = data.all_db_shifts;
                            }



                            if (data.new_duration.length > 0) {
                                vue_details_tab_edit.new_duration = data.new_duration;
                                vue_details_tab_edit.duration_minutes =
                                    vue_details_tab_edit
                                    .new_duration[0][0];
                            }
                            if (data.beauticians.length > 0) {
                                bu_selcted_data = data.beauticians[0];
                                vue_details_tab_edit.beautician_id = bu_selcted_data
                                    .beautician_id;

                                bu_selected = "#bu" + bu_selcted_data.beautician_id;
                                bu_selected_htm_id = "#bu" + bu_selcted_data
                                    .beautician_id;

                                setTimeout(function() {
                                    $(bu_selected).addClass("bu-selected");
                                    vue_details_tab_edit
                                        .focused_active_service =
                                        vue_details_tab_edit.service_id;
                                    vue_details_tab_edit.db_shift = data
                                        .db_shift;

                                }, 1000);

                            }
                            vue_details_tab_edit.time_slot = data.time_slot;
                            vue_details_tab_edit.applyBeauticianList(data.beauticians);
                            $('input[name=time]').attr('checked', false);
                            if (!data.beautician_flag) {
                                $('input[name=beautician]').attr('checked', false);
                            }

                        });
                        // this.getbookingData();
                        //alert(vue_details_tab_edit.beautician_id);
                        // vue_details_tab_edit.beauticians = vue_details_tab_edit.beauticians;
                        //bu_selected = "#bu"+ vue_details_tab_edit.beautician_id;


                        $(bu_selected_htm_id).addClass("bu-selected");
                        setTimeout(function() {
                            $(bu_selected_htm_id).addClass("bu-selected");
                            vue_details_tab_edit.focused_active_service =
                                vue_details_tab_edit.service_id;
                            vue_details_tab_edit.db_shift = data.db_shift;

                        }, 1000);
                    }

                },
                showAndHideSubcat: function(id) {

                    //id = '#sub_serv_'+id;
                    if ($("#sub_serv_" + id).is(':visible')) {
                        $("#sub_serv_" + id).hide();
                        $('#sub-service').removeClass('sub-service-click');
                    } else {
                        $("#sub_serv_" + id).show();
                        $('#sub-service').addClass('sub-service-click');

                    }

                },
                showAndHideSubcat1: function(id) {
                    if ($("#sub_servs_" + id).is(':visible')) {
                        $("#sub_servs_" + id).hide();
                        $('#sub-services').removeClass('sub-service-click');
                    } else {
                        $("#sub_servs_" + id).show();
                        $('#sub-services').addClass('sub-service-click');

                    }
                },

                showAndHideItem: function(id) {
                    if ($("#sub_item_" + id).is(':visible')) {
                        $("#sub_item_" + id).hide();
                    } else {
                        $("#sub_item_" + id).show();
                    }

                },
                beauticianClick: function() {


                    $('#slot_tab').addClass('slot-active');

                },
                beauticianClicks: function() {

                    $('#service_tab').addClass('service-active');

                },
                getBeauticianSearchQuery: function(event) {
                    if (event && event.target && typeof event.target.value !== 'undefined') {
                        return event.target.value;
                    }
                    return ($('#filterServiceBeautician').val() || $('#filterProductBeautician').val() || '');
                },
                getBeauticianDisplayName: function(elem) {
                    return (elem.name_locale || elem.name || '').toString().toLowerCase().trim();
                },
                normalizeBeauticianList: function(beauticians) {
                    return (beauticians || []).filter(function(bt) {
                        return !bt.user_status || bt.user_status === 'active';
                    });
                },
                applyBeauticianList: function(beauticians) {
                    this.search_beautician_list = this.normalizeBeauticianList(beauticians);
                    this.searchBeautician();
                },
                searchBeautician: function(event) {
                    var query = this.getBeauticianSearchQuery(event);
                    this.search_query_beautician = query;
                    var normalizedQuery = query.toLowerCase().trim();
                    this.beauticians = this.search_beautician_list.filter(function(elem) {
                        if (!normalizedQuery) {
                            return true;
                        }
                        return vue_details_tab_edit.getBeauticianDisplayName(elem).includes(normalizedQuery);
                    });
                },
                getProductLookup: function() {
                    $('#filterProduct').val("");
                    vue_details_tab_edit.search_product_name = null;
                    vue_details_tab_edit.all_items_list = [];

                    $('#product_tab').show();
                    $('#bookingProduct').show();

                    $('#product_tab').removeAttr('style');
                    $('#bookingProduct').removeAttr('style');

                    $('#product_tab').addClass('nav-item nav-link active');
                    $('#beautician_product_tab').removeClass('active');

                    $('#bookingProduct').addClass('nav-item nav-link active show');
                    $('#beautician_product').removeClass('nav-item nav-link active show');

                    vue_details_tab_edit.beauticians = [];
                    vue_details_tab_edit.selected_product_id = 0;
                    vue_details_tab_edit.selected_serv_id = 0;

                    if (!vue_details_tab_edit.all_item_groupes || !vue_details_tab_edit.all_item_groupes.length) {
                        alert('No product groups available.');
                        return;
                    }

                    var catid = vue_details_tab_edit.all_item_groupes[0].item_group_id;
                    vue_details_tab_edit.changeProductTab(catid);

                    $('#productLookup').modal('show');
                },
                getServiceLookup: function() {
                    vue_details_tab_edit.beauticians = [];
                    vue_details_tab_edit.time_slot = [];
                    $('#filterServ').val("");
                    vue_details_tab_edit.search_query = null;

                    if (!vue_details_tab_edit.all_service_category || !vue_details_tab_edit.all_service_category.length) {
                        alert('No service categories available.');
                        return;
                    }

                    var catid = vue_details_tab_edit.all_service_category[0].category_id;
                    vue_details_tab_edit.changeSeviceTab(catid);
                    $('#service_tab').show();
                    $('#bookingService').show();

                    $('#service_tab').removeAttr('style');
                    $('#bookingService').removeAttr('style');

                    $('#featured').show();
                    $('#is_update').val(0);

                    $('#service_tab').addClass('nav-item nav-link active');
                    $('#slot_tab').removeClass('active');
                    $('#beautician_tab').removeClass('active');

                    $('#bookingService').addClass('nav-item nav-link active show');
                    $('#slot').removeClass('nav-item nav-link active show');
                    $('#beautician').removeClass('nav-item nav-link active show');

                    vue_details_tab_edit.current_stage = 1;

                    vue_details_tab_edit.selected_serv_id = 0;
                    vue_details_tab_edit.selected_beautician_id = 0;
                    vue_details_tab_edit.selected_shift_id = 0;

                    vue_details_tab_edit.selected_time_slot = null;

                    vue_details_tab_edit.resetCalendar();

                    vue_details_tab_edit.is_reschedule = false;
                    $('#serviceLookup').modal('show');
                },

                getQuickServiceLookup: function() {
                    vue_details_tab_edit.services = [];
                    $('#filterServiceBeautician, #filterProductBeautician').val("");
                    vue_details_tab_edit.search_query = null;

                    // var catid = $('#first_serv_cat_id').val();
                    // vue_details_tab_edit.changeSeviceTab(catid);
                    $('#beautician_tab').show();
                    $('#beautician').show();

                    $('#beautician_tab').removeAttr('style');
                    $('#beautician').removeAttr('style');

                    $('#featured').show();
                    $('#is_update').val(0);

                    $('#service_tab').addClass('nav-item nav-link active');
                    $('#service_tab').removeClass('active');

                    $('#beautician').addClass('nav-item nav-link active show');
                    $('#bookingService1').removeClass('nav-item nav-link active show');

                    vue_details_tab_edit.current_stage = 1;

                    vue_details_tab_edit.selected_serv_id = 0;
                    vue_details_tab_edit.selected_beautician_id = 0;

                    vue_details_tab_edit.is_reschedule = false;
                    $('#quickServiceLookup').modal('show');
                },

                getQuickProductLookup: function() {
                    if (!vue_details_tab_edit.booking_info.client_id) {
                        alert('Please add client...!!');
                        return;
                    }
                    if (!vue_details_tab_edit.all_item_groupes || !vue_details_tab_edit.all_item_groupes.length) {
                        alert('No product groups available.');
                        return;
                    }
                    vue_details_tab_edit.resetQuickProductModal();
                    $('#quickProductLookup').modal('show');
                },

                loadQuickProductGroups: function() {
                    if (!this.all_item_groupes || !this.all_item_groupes.length) {
                        return;
                    }
                    const firstGroupId = this.all_item_groupes[0].item_group_id;
                    this.changeQuickProductCategory(firstGroupId);
                },

                changeQuickProductCategory: function(groupId) {
                    this.quick_product_cat_id = groupId;
                    this.quickProductSearch = '';
                    $('#filterQuickProduct').val('');
                    this.fetchQuickProducts(groupId, '');
                },

                searchQuickProduct: function() {
                    const name = ($('#filterQuickProduct').val() || this.quickProductSearch || '').trim();
                    this.quickProductSearch = name;
                    this.fetchQuickProducts(0, name);
                },

                fetchQuickProducts: function(groupId, name) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('salon.booking.get.product') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id: groupId || 0,
                            name: name || '',
                        },
                        dataType: "json",
                        success: (data) => {
                            this.quick_items_list = data || [];
                        },
                        error: (xhr) => {
                            this.quick_items_list = [];
                        }
                    });
                },

                showAndHideQuickItem: function(index) {
                    const $el = $('#quick_sub_item_' + index);
                    if ($el.is(':visible')) {
                        $el.hide();
                    } else {
                        $el.show();
                    }
                },

                findQuickProductByPriceId: function(priceId) {
                    for (let group of (this.quick_items_list || [])) {
                        for (let item of (group.items || [])) {
                            if (item.item_price && String(item.item_price.price_id) === String(priceId)) {
                                const selling = item.item_price.selling_price != null
                                    ? item.item_price.selling_price
                                    : item.selling_price;
                                return {
                                    price_id: item.item_price.price_id,
                                    item_id: item.item_id,
                                    name: item.item_name,
                                    price: '₹' + selling,
                                };
                            }
                        }
                    }
                    return null;
                },

                removeQuickProduct: function(priceId) {
                    this.selectedProducts = this.selectedProducts.filter(id => String(id) !== String(priceId));
                },

                manageQuickProductSubmit: function() {
                    if (!this.booking_info.client_id) {
                        alert('Please add client...!!');
                        return;
                    }
                    if (!this.selectedProducts.length) {
                        alert('Please select at least one product.');
                        return;
                    }
                    this.submitQuickProductForm();
                },

                submitQuickProductForm: function() {
                    this.loading = true;
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('salon.booking.quick.product.save') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            beautician_id: this.selectedProductBeautician || null,
                            price_ids: this.selectedProducts,
                            booking_id: this.booking_id,
                            line_item_type: 'item',
                            quantity: 1,
                        },
                        success: (response) => {
                            this.loading = false;
                            if (response.booking_info) {
                                this.booking_info = response.booking_info;
                                if (response.booking_info.booking_id) {
                                    this.booking_id = response.booking_info.booking_id;
                                }
                                this.$nextTick(() => {
                                    this.grandTotal_all_raw();
                                });
                            }
                            $('#quickProductLookup').modal('hide');
                            this.selectedProducts = [];
                            this.selectedProductDetails = [];
                            this.selectedProductBeautician = '';
                        },
                        error: (xhr) => {
                            this.loading = false;
                            let message = 'Something went wrong.';
                            try {
                                const json = JSON.parse(xhr.responseText);
                                if (json.js_notifications && json.js_notifications.error) {
                                    message = json.js_notifications.error;
                                } else if (json.message) {
                                    message = json.message;
                                }
                            } catch (e) {}
                            alert(message);
                        }
                    });
                },

                getTodayDate: function() {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                },

                ensureClientSelectSynced: function() {
                    const info = this.booking_info || {};
                    // After payment the Select2 is hidden; still trust booking_info.client_id
                    const clientId = info.client_id || $('#client_id').val();
                    if (!clientId) {
                        return null;
                    }
                    info.client_id = clientId;
                    if ($('#client_id').length && String($('#client_id').val() || '') !== String(clientId) && info.client_name) {
                        const selectedOption = new Option(info.client_name, clientId, true, true);
                        $('#client_id').empty().append(selectedOption);
                        $('#client_id').val(clientId).trigger('change.select2');
                    }
                    return clientId;
                },

                applyHairConsultation(response) {
                    this.consultation_id = response.form_id;
                    this.consultation_status = response.status;
                    this.client_name = response.client_name || this.booking_info.client_name || '';
                    this.client_age = response.client_age || '';
                    this.date = response.date || this.getTodayDate();
                    this.client_mobile = response.client_mobile || this.booking_info.client_mobile || '';
                    this.client_phone = response.client_phone || this.booking_info.client_telephone || '';
                    this.stylist_name = response.stylist_name || '';
                    this.designation = response.designation || '';
                    this.photo_permission = response.photo_permission || '';
                    if (response.primary_analysis) {
                        this.primary_analysis = Object.assign({}, this.primary_analysis, response.primary_analysis);
                    }
                    if (response.hair_history) {
                        this.hair_history = response.hair_history;
                    }
                    if (response.client_desire) {
                        this.client_desire = Object.assign({}, this.client_desire, response.client_desire);
                    }
                },

                applySkinConsultation(response) {
                    this.consultation_id = response.form_id;
                    this.consultation_status = response.status;
                    this.client_name = response.client_name || this.booking_info.client_name || '';
                    this.client_age = response.client_age || '';
                    this.date = response.date || this.getTodayDate();
                    this.client_mobile = response.client_mobile || this.booking_info.client_mobile || '';
                    this.client_phone = response.client_phone || this.booking_info.client_telephone || '';
                    this.stylist_name = response.stylist_name || '';
                    this.designation = response.designation || '';
                    this.skin_photo_permission = response.skin_photo_permission || '';
                    if (response.skin_analysis) {
                        this.skin_analysis = Object.assign({}, this.skin_analysis, response.skin_analysis);
                    }
                    if (response.life_style) {
                        this.life_style = Object.assign({}, this.life_style, response.life_style);
                    }
                    if (response.history) {
                        this.history = Object.assign({}, this.history, response.history);
                    }
                    if (response.conclusion) {
                        this.conclusion = Object.assign({}, this.conclusion, response.conclusion);
                    }
                },

                openNewHairConsultation() {
                    this.consultation_id = null;
                    this.consultation_status = null;
                    this.client_name = this.booking_info.client_name || '';
                    this.client_mobile = this.booking_info.client_mobile || '';
                    this.client_phone = this.booking_info.client_telephone || '';
                    this.date = this.getTodayDate();
                    $('#hairLookup').modal('show');
                },

                openNewSkinConsultation() {
                    this.consultation_id = null;
                    this.consultation_status = null;
                    this.client_name = this.booking_info.client_name || '';
                    this.client_mobile = this.booking_info.client_mobile || '';
                    this.client_phone = this.booking_info.client_telephone || '';
                    this.date = this.getTodayDate();
                    $('#skinLookup').modal('show');
                },

                getHairLookup: function() {
                    const clientId = vue_details_tab_edit.ensureClientSelectSynced();
                    if (!clientId) {
                        alert('Please select a client before opening hair consultation.');
                        return;
                    }
                    if (!vue_details_tab_edit.booking_info.booking_id) {
                        vue_details_tab_edit.openNewHairConsultation();
                        return;
                    }

                    vue_details_tab_edit.loading = true;
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('consultation.for.booking') }}",
                        data: {
                            booking_id: vue_details_tab_edit.booking_info.booking_id,
                            type: 'hair',
                        },
                        success: (response) => {
                            vue_details_tab_edit.loading = false;
                            if (response.exists) {
                                vue_details_tab_edit.applyHairConsultation(response);
                                if (response.status === 'completed') {
                                    vue_details_tab_edit.openExistingConsultation(response);
                                    return;
                                }
                                $('#hairLookup').modal('show');
                                return;
                            }
                            vue_details_tab_edit.openNewHairConsultation();
                        },
                        error: () => {
                            vue_details_tab_edit.loading = false;
                            vue_details_tab_edit.openNewHairConsultation();
                        }
                    });
                },

                getSkinLookup: function() {
                    const clientId = vue_details_tab_edit.ensureClientSelectSynced();
                    if (!clientId) {
                        alert('Please select a client before opening skin consultation.');
                        return;
                    }
                    if (!vue_details_tab_edit.booking_info.booking_id) {
                        vue_details_tab_edit.openNewSkinConsultation();
                        return;
                    }

                    vue_details_tab_edit.loading = true;
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('consultation.for.booking') }}",
                        data: {
                            booking_id: vue_details_tab_edit.booking_info.booking_id,
                            type: 'skin',
                        },
                        success: (response) => {
                            vue_details_tab_edit.loading = false;
                            if (response.exists) {
                                vue_details_tab_edit.applySkinConsultation(response);
                                if (response.status === 'completed') {
                                    vue_details_tab_edit.openExistingConsultation(response);
                                    return;
                                }
                                $('#skinLookup').modal('show');
                                return;
                            }
                            vue_details_tab_edit.openNewSkinConsultation();
                        },
                        error: () => {
                            vue_details_tab_edit.loading = false;
                            vue_details_tab_edit.openNewSkinConsultation();
                        }
                    });
                },

                resetCalendar: function() {
                    // first_date = new Date();
                    var currentDate = new Date();

                    var currentDateWithFormat = new Date().toJSON().slice(0, 10).replace(/-/g,
                        '/');
                    vue_details_tab_edit.selected_date = currentDateWithFormat;

                    $('#calendar-wrapper').updateCalendarOptions({
                        date: currentDateWithFormat
                    });
                },
                showAddServices: function() {

                    vue_details_tab_edit.current_stage = 1;
                },
                showAddBeauticians: function() {

                    vue_details_tab_edit.current_stage = 1;
                },
                resetServiceSearch: function() {
                    $('#filterServ').val("");
                    $('#quick_service_tab').addClass('service-active');
                    vue_details_tab_edit.current_stage = 2;
                    vue_details_tab_edit.services = vue_details_tab_edit.search_service_list;
                    $('#quick_beautician_tab').removeClass('show active');
                    $('#quickbeautician').removeClass('nav-item nav-link active show');
                },

                searchService: function() {
                    // vue_details_tab_edit.search_beautician_list = [];



                    vue_details_tab_edit.search_query_service = $('#filterServ').val();

                    var valObj = vue_details_tab_edit.search_service_list.filter(function(
                        elem) {
                        if (elem.name.toLowerCase().trim().includes(vue_details_tab_edit
                                .search_query_service.toLowerCase().trim()))
                            return elem;
                    });

                    vue_details_tab_edit.services = valObj;
                },
                changeClass: function() {
                    $('#slot_tab').addClass('slot-active');

                    vue_details_tab_edit.current_stage = 3;
                    $('#beautician_tab').removeClass('show active');
                    $('#beautician').removeClass('nav-item nav-link active show');
                },
                resetBeauticianSearch: function() {
                    $('#filterServiceBeautician, #filterProductBeautician').val("");
                    this.search_query_beautician = '';
                    $('#service_tab').addClass('service-active');
                    vue_details_tab_edit.current_stage = 2;
                    vue_details_tab_edit.beauticians = vue_details_tab_edit.search_beautician_list.slice();
                },
                // Sales Associate (Beautician)
                setSalesAssociateProduct(id, price_id, bath_no) {
                    vue_details_tab_edit.product_price_id = price_id;
                    vue_details_tab_edit.product_batch_no = bath_no;
                    vue_details_tab_edit.getbookingData(id, 'product');
                },
                setSalesAssociateBeautician: function(beautician_id) {
                    vue_details_tab_edit.selected_product_beautician_id = beautician_id;
                },

                // getbookingData: function(id, type, shift_id) {

                //     $('#beautician_tab').addClass('clcik');

                //     if (this.currentdate > this.selected_date) {
                //         alert('date expired');
                //         this.time_slot = [];
                //         return 0;
                //     }

                //     if (type == 'serv') {
                //         this.selected_serv_id = id;
                //         this.selected_beautician_id = 0;
                //         this.selected_shift_id = 0;
                //         this.selected_time_slot = null;
                //         this.resetCalendar();
                //     }

                //     if (type == 'beatician') {
                //         this.selected_beautician_id = id;
                //         this.selected_shift_id = shift_id;
                //         this.selected_time_slot = null;
                //         this.resetCalendar();
                //     }

                //     if (type == 'product') {
                //         this.selected_product_id = id;
                //     }

                //     this.shift_id = $("#shift_data :selected").val();
                //     this.duration_minutes = $("#duration_minutes :selected").val();

                //     let self = this;
                //     $.ajax({
                //         method: "POST",
                //         url: "{{ route('booking.change') }}",
                //         data: {
                //             service: this.selected_serv_id,
                //             is_pos: 1,
                //             service_type: 3,
                //             shift_id: this.selected_shift_id,
                //             duration: this.duration_minutes,
                //             price: this.price,
                //             date: this.selected_date,
                //             beautician: this.selected_beautician_id
                //         },
                //     }).done(function(data) {
                //         this.db_shift = data.db_shift;

                //         if (data.new_duration.length > 0 && type == 'beatician') {
                //             self.new_duration = data.new_duration;
                //         }

                //         if (data.beauticians.length > 0) {
                //             bu_selcted_data = data.beauticians[0];
                //         }

                //         if (type == 'beatician' || type == 'date') {
                //             self.time_slot = data.time_slot;
                //         }

                //         var query = $('#filterBeautician').val();
                //         if (query != null && query.length > 0) {
                //             // Not update beautician if search started
                //         } else {
                //             self.beauticians = data.beauticians.filter(bt => bt
                //                 .user_status === 'active');
                //             self.search_beautician_list = data.beauticians.filter(bt => bt
                //                 .user_status === 'active');
                //         }


                //         $('input[name=time]').attr('checked', false);
                //         if (!data.beautician_flag) {
                //             $('input[name=beautician]').attr('checked', false);
                //         }

                //         // Show beautician tab after service selection
                //         if (type == 'serv' && data.beauticians.length > 0) {
                //             $('#beautician_tab').tab('show');
                //             vue_details_tab_edit.current_stage = 2;
                //             vue_details_tab_edit.resetBeauticianSearch();
                //             $('#service_tab').removeClass('active');
                //             $('#bookingService').removeClass('show active');
                //             $('#beautician_tab').addClass('active');
                //             $('#beautician').addClass('show active');
                //         }

                //         // Show slot tab after beautician selection
                //         if (type == 'beatician' &&
                //             vue_details_tab_edit.selected_serv_id > 0 &&
                //             vue_details_tab_edit.selected_beautician_id > 0 &&
                //             vue_details_tab_edit.selected_shift_id > 0 &&
                //             data.time_slot.length > 0) {
                //             vue_details_tab_edit.changeClass();
                //             $('#beautician_tab').removeClass('active');
                //             $('#beautician').removeClass('show active');
                //             $('#slot_tab').addClass('active');
                //             $('#slot').addClass('show active');
                //         }
                //     });
                // },

                getbookingData: function(id, type, shift_id) {

                    $('#beautician_tab').addClass('clcik');

                    if (this.currentdate > this.selected_date) {
                        alert('date expired');
                        this.time_slot = [];
                        return 0;
                    }

                    if (type == 'serv') {
                        this.selected_serv_id = id;
                        this.selected_beautician_id = 0;
                        this.selected_shift_id = 0;
                        this.selected_time_slot = null;
                        this.resetCalendar();
                    }

                    if (type == 'beatician') {
                        this.selected_beautician_id = id;
                        this.selected_shift_id = shift_id;
                        this.selected_time_slot = null;
                        this.resetCalendar();
                    }

                    if (type == 'product') {
                        this.selected_product_id = id;
                        // Beautician is optional for products — stay on product tab so Submit is available immediately
                    }


                    this.shift_id = $("#shift_data :selected").val();
                    this.duration_minutes = $("#duration_minutes :selected").val();

                    let self = this;
                    $.ajax({
                        method: "POST",
                        url: "{{ route('booking.change') }}",
                        data: {
                            service: this.selected_serv_id,
                            is_pos: 1,
                            service_type: 3,
                            shift_id: this.selected_shift_id,
                            duration: this.duration_minutes,
                            price: this.price,
                            date: this.selected_date,
                            beautician: this.selected_beautician_id
                        },
                    }).done(function(data) {
                        this.db_shift = data.db_shift;

                        if (data.new_duration.length > 0 && type == 'beatician') {
                            self.new_duration = data.new_duration;
                        }

                        if (data.beauticians.length > 0) {
                            bu_selcted_data = data.beauticians[0];
                        }

                        if (type == 'beatician' || type == 'date') {
                            self.time_slot = data.time_slot;
                        }

                        self.applyBeauticianList(data.beauticians);

                        $('input[name=time]').attr('checked', false);
                        if (!data.beautician_flag) {
                            $('input[name=beautician]').attr('checked', false);
                        }

                        // Show beautician tab after service selection
                        if (type == 'serv' && data.beauticians.length > 0) {
                            $('#beautician_tab').tab('show');
                            vue_details_tab_edit.current_stage = 2;
                            vue_details_tab_edit.resetBeauticianSearch();
                            $('#service_tab').removeClass('active');
                            $('#bookingService').removeClass('show active');
                            $('#beautician_tab').addClass('active');
                            $('#beautician').addClass('show active');
                        }

                        // Show slot tab after beautician selection
                        if (type == 'beatician' &&
                            vue_details_tab_edit.selected_serv_id > 0 &&
                            vue_details_tab_edit.selected_beautician_id > 0 &&
                            vue_details_tab_edit.selected_shift_id > 0 &&
                            data.time_slot.length > 0) {
                            vue_details_tab_edit.changeClass();
                            $('#beautician_tab').removeClass('active');
                            $('#beautician').removeClass('show active');
                            $('#slot_tab').addClass('active');
                            $('#slot').addClass('show active');
                        }
                    });
                },

                // New function to fetch services by beautician
                fetchServicesByBeautician: function(beautician_id) {
                    let self = this;
                    $.ajax({
                        method: "POST",
                        url: "{{ route('services-by-beautician') }}",
                        data: {
                            beautician_id: beautician_id,
                            is_pos: 1,
                            service_type: 3,
                            date: this.selected_date
                        },
                    }).done(function(data) {
                        if (data.services && data.services.length > 0) {
                            self.services = data.services.filter(service => service
                                .status === 'active');
                        } else {
                            self.services = [];
                        }
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        self.services = []; // Clear services on error
                    });
                },




                convertTimeTo12hr: function(time) {
                    // Check correct time format and split into components
                    time = time.toString().match(/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [
                        time
                    ];

                    if (time.length > 1) { // If time format correct
                        time = time.slice(1); // Remove full string match value
                        time[5] = +time[0] < 12 ? ' AM' : ' PM'; // Set AM/PM
                        time[0] = +time[0] % 12 || 12; // Adjust hours
                    }
                    return time.join(''); // return adjusted time or original string
                },
                applyRescheduleResponse: function(data) {
                    if (data.new_duration && data.new_duration.length > 0) {
                        vue_details_tab_edit.new_duration = data.new_duration;
                    }
                    if (data.beauticians && data.beauticians.length > 0) {
                        bu_selcted_data = data.beauticians[0];
                    }
                    if (data.db_shift) {
                        vue_details_tab_edit.db_shift = data.db_shift;
                    }
                    vue_details_tab_edit.time_slot = data.time_slot || [];
                    vue_details_tab_edit.applyBeauticianList(data.beauticians || []);
                    $('input[name=time]').attr('checked', false);
                    if (!data.beautician_flag) {
                        $('input[name=beautician]').attr('checked', false);
                    }
                },
                reShedule: function(booking_id, is_booking, booking_serv_id, serv_id,
                    beautician,
                    shift_id, catid, date,
                    timeslot) {
                    var self = this;
                    var openReschedule = function() {
                    $('#bookingService').hide();
                    $('#service_tab').hide();
                    $('#featured').hide();

                    $('#beautician_tab').addClass('nav-item nav-link active');
                    $('#slot_tab').removeClass('active');

                    $('#beautician').addClass('nav-item nav-link active show');
                    $('#slot').removeClass('nav-item nav-link active show');

                    vue_details_tab_edit.selected_serv_id = serv_id;
                    vue_details_tab_edit.booking_serv_id = booking_serv_id;
                    vue_details_tab_edit.selected_beautician_id = beautician;
                    vue_details_tab_edit.selected_shift_id = shift_id;
                    vue_details_tab_edit.selected_time_slot = timeslot;
                    vue_details_tab_edit.is_reschedule = true;

                    $('#filterServiceBeautician, #filterProductBeautician').val('');
                    vue_details_tab_edit.search_query_beautician = '';

                    $('#calendar-wrapper').updateCalendarOptions({
                        date: date
                    });
                    vue_details_tab_edit.focused_active_booking_date = date;
                    vue_details_tab_edit.selected_date = date;
                    selected_date_calender = new Date(date);

                    $('#d_is_update').val(1);
                    $('#serviceLookup').modal('show');

                    var reschedulePayload = {
                        service: serv_id,
                        is_pos: 1,
                        service_type: 3,
                        beautician: beautician,
                        shift_id: shift_id,
                        category_id: catid,
                        date: date,
                        booking_service_id: booking_serv_id,
                        booking_id: booking_id,
                        is_booking: is_booking
                    };

                    $.ajax({
                        method: "POST",
                        url: "{{ route('all.booking.existing') }}",
                        data: reschedulePayload,
                    }).done(function(data) {
                        if (data.beauticians && data.beauticians.length > 0) {
                            vue_details_tab_edit.applyRescheduleResponse(data);
                            return;
                        }

                        $.ajax({
                            method: "POST",
                            url: "{{ route('booking.change') }}",
                            data: {
                                service: reschedulePayload.service,
                                is_pos: reschedulePayload.is_pos,
                                service_type: reschedulePayload.service_type,
                                shift_id: reschedulePayload.shift_id,
                                date: reschedulePayload.date,
                                beautician: reschedulePayload.beautician,
                            },
                        }).done(function(fallbackData) {
                            data.beauticians = fallbackData.beauticians || [];
                            if (!data.time_slot || !data.time_slot.length) {
                                data.time_slot = fallbackData.time_slot || [];
                            }
                            data.beautician_flag = fallbackData.beautician_flag || data.beautician_flag;
                            vue_details_tab_edit.applyRescheduleResponse(data);
                        });
                    });
                    };
                    self.requireFinalizedEditAuth(function(password) {
                        if (password === null) {
                            return;
                        }
                        openReschedule();
                    });
                },

                // split: function(item) {
                //         item
                //     });

                //     $('#modal_split_beautician_id').val(item.beautician_id);
                //     $('#modal_split_booking_id').val(item.booking_id);
                //     $('#modal_split_booking_service_id').val(item.booking_service_id);
                //     $('#modal_service_name').text(item.service_name);
                //     $('#modal_service_started_at').val(item.service_started_at);
                //     $('#modal_service_amount').text(item.line_total);
                //     $('#modal_commission_end_date').val(item.end_date);
                //     $('#modal_commission_start_date').val(item.start_date);
                //     $('#modal_commission_percentage').val(item.commission_percentage);
                //     $('#modal_service_id').val(item.service_id);
                //     $('#modal_commission_status').val(item.commission_status !== null ? item
                //         .commission_status : 1);
                //     $('#modal_split_price').val(item.line_total); // Hidden price

                //     $('#status-modal-split').modal('show');
                // },

                split: function(item) {
                    var self = this;
                    var openSplitModal = function() {
                    const discount = self.calculateDiscount(
                        item.service_booking_price,
                        item.quantity,
                        item.individual_discount_percent,
                        item.discount_percent
                    );

                    const netTotal = self.v_totalNetTotal(
                        item.service_booking_price,
                        discount,
                        item.quantity
                    );

                    const taxTotal = self.v_totalTaxTotal(
                        item.service_booking_price,
                        item.quantity,
                        discount,
                        item.tax_percent
                    );

                    const calculatedLineTotal = self.v_totalPrice(netTotal, taxTotal);
                    // Prefer DB line_total (same value used on payment/commission finalize)
                    const amount = parseFloat(
                        item.line_total != null && item.line_total !== ''
                            ? item.line_total
                            : calculatedLineTotal
                    ) || 0;

                    const $modal = $('#status-modal-split');
                    $modal.find('#modal_split_beautician_id').val(item.beautician_id || '');
                    $modal.find('#modal_split_booking_id').val(item.booking_id);
                    $modal.find('#modal_split_booking_service_id').val(item.booking_service_id);
                    $modal.find('#modal_service_name').text(item.service_name);
                    $modal.find('#modal_service_started_at').val(item.service_started_at);
                    $modal.find('#modal_service_amount').text(amount.toFixed(2));
                    $modal.find('#modal_commission_end_date').val(item.end_date);
                    $modal.find('#modal_commission_start_date').val(item.start_date);
                    $modal.find('#modal_commission_percentage').val(item.commission_percentage);
                    $modal.find('#modal_service_id').val(item.service_id);
                    $modal.find('#modal_commission_status').val(
                        item.commission_status !== null ? item.commission_status : 1
                    );

                    $modal.modal('show');
                    };
                    self.requireFinalizedEditAuth(function(password) {
                        if (password === null) {
                            return;
                        }
                        openSplitModal();
                    });
                },

                openSplit: function(item) {
                    window._viewSplitEditItem = item;
                    const $modal = $('#open-split-modal');
                    const $editBtn = $('#view_split_edit_btn');
                    var canEditCompletedSplit = this.isBookingCompleted
                        && item
                        && item.status !== 'refunded';
                    if (canEditCompletedSplit) {
                        $editBtn.show();
                    } else {
                        $editBtn.hide();
                    }
                    $modal.find('#view_split_beautician_id').val(item.beautician_id || '');
                    $modal.find('#view_split_booking_id').val(item.booking_id);
                    $modal.find('#view_split_booking_service_id').val(item.booking_service_id);
                    $modal.find('#modal_service_name1').text(item.service_name);
                    $modal.find('#modal_service_amount1').text(
                        parseFloat(item.line_total != null ? item.line_total : 0).toFixed(2)
                    );

                    var wrapper = $modal.find('#view_split_rows_body');
                    wrapper.empty();

                    function renderSplitRows(response) {
                        var list = (Array.isArray(window.SPLIT_BEAUTICIANS) ? window.SPLIT_BEAUTICIANS : []);
                        wrapper.empty();
                        (response || []).forEach(function(split) {
                            let price = parseFloat(split.price);
                            if (isNaN(price)) {
                                price = 0;
                            }
                            var $select = $('<select class="form-control split-beautician-select" disabled></select>');
                            $select.append($('<option>', { value: '', text: 'Select Beautician' }));
                            list.forEach(function(item) {
                                $select.append($('<option>', {
                                    value: String(item.id),
                                    text: item.text
                                }));
                            });
                            $select.val(String(split.beautician_id));
                            var $row = $('<div class="split-row split-row-readonly"></div>');
                            $row.append($select);
                            $row.append($('<div class="split-share-box"></div>').text('₹ ' + price.toFixed(2)));
                            wrapper.append($row);
                        });

                        if (!(response || []).length) {
                            wrapper.html('<div class="split-empty-state">{{ __('No split recorded for this service.') }}</div>');
                        }
                        $modal.modal('show');
                    }

                    function fallbackSingleBeauticianSplit() {
                        var fallbackPrice = parseFloat(
                            item.line_total != null && item.line_total !== ''
                                ? item.line_total
                                : 0
                        );
                        if (item.beautician_id) {
                            renderSplitRows([{
                                beautician_id: item.beautician_id,
                                price: fallbackPrice
                            }]);
                            return;
                        }
                        renderSplitRows([]);
                    }

                    // Prefer draft while unpaid; fall back to finalized split amounts after payment
                    $.ajax({
                        url: '{{ route('get.split.amount') }}',
                        type: 'get',
                        data: {
                            booking_id: item.booking_id,
                            service_id: item.service_id,
                            booking_service_id: item.booking_service_id,
                        },
                        success: function(response) {
                            if (response && response.length) {
                                renderSplitRows(response);
                                return;
                            }
                            $.ajax({
                                url: '{{ route('get.split.details') }}',
                                type: 'post',
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    booking_id: item.booking_id,
                                    service_id: item.service_id,
                                    booking_service_id: item.booking_service_id,
                                },
                                success: function(finalResponse) {
                                    if (finalResponse && finalResponse.length) {
                                        renderSplitRows(finalResponse);
                                        return;
                                    }
                                    fallbackSingleBeauticianSplit();
                                },
                                error: function() {
                                    fallbackSingleBeauticianSplit();
                                }
                            });
                        },
                        error: function() {
                            alert('Failed to fetch split details. Please try again.');
                        }
                    });
                },

                cancelOrder: function() {
                    if (vue_details_tab_edit.booking_info.payment_status == 'completed'
                        || vue_details_tab_edit.booking_info.booking_code) {
                        alert('Completed bookings cannot be cancelled. Use Refund for returns.');
                        return 0;
                    }
                    var isDraft = !vue_details_tab_edit.booking_info.booking_code;
                    var confirmMsg = isDraft
                        ? "Are you sure you want to delete this booking? Product stock will be restored."
                        : "Are you sure to cancel booking...!";
                    var msg = confirm(confirmMsg);
                    if (msg == true) {
                        $.ajax({
                            url: "{{ route('salon.booking.cancel.order') }}",
                            type: 'post',
                            data: {
                                booking_id: vue_details_tab_edit.booking_id,
                                soft_delete: isDraft ? 1 : 0,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(result) {
                                if (result && result.js_notifications && result.js_notifications.warning) {
                                    alert(result.js_notifications.warning);
                                    return;
                                }
                                if (result && result.deleted && result.redirect_url) {
                                    window.location.href = result.redirect_url;
                                    return;
                                }
                                if (result && result.vue_data && result.vue_data.vue_details_tab_edit &&
                                    result.vue_data.vue_details_tab_edit.booking_info) {
                                    vue_details_tab_edit.booking_info = result.vue_data.vue_details_tab_edit.booking_info;
                                }
                                window.location.href = "{{ route('tab.salon.booking.list') }}";
                            },
                            error: function() {
                                alert('Failed to cancel booking. Please try again.');
                            }
                        });
                    } else {
                        return 0;
                    }

                },
                amountTakenChanged: function(el, target_element, type) {
                    vue_details_tab_edit.amount_taken_flag = 1;

                    var total = "given....";
                    $("#add_payment_btn").attr("disabled", false);
                    g_total = vue_details_tab_edit.grand_total - this.booking_info
                        .payment_total;

                    var takenAmount = parseFloat(vue_details_tab_edit.amount_taken);
                    if (isNaN(takenAmount)) {
                        takenAmount = 0;
                    }

                    if (type == 'refund') {
                        g_total = g_total * -1;

                        total = 0;

                        if (takenAmount > parseFloat(g_total)) {
                            alert('You cannot enter greater than booking.')
                            $("#add_payment_btn").attr("disabled", true);
                        }

                        $(target_element).val(parseFloat(total).toFixed(2));
                        total = g_total;
                    } else {
                        if (takenAmount > parseFloat(g_total)) {
                            if ($("#payment_mode :selected").val() == "card") {
                                $("#add_payment_btn").attr("disabled", true);
                            }

                            total = parseFloat(vue_details_tab_edit.grand_total) - takenAmount;

                            total = parseFloat(total) - parseFloat(this.booking_info
                                .payment_total);
                            total = Math.abs(total);
                            $(target_element).val(parseFloat(total).toFixed(2));
                        } else {
                            $(target_element).val('');
                        }

                        total = vue_details_tab_edit.grand_total - this.booking_info
                            .payment_total;
                    }
                },

                calculateDiscount(price, quantity, individualDiscountPercent,
                    generalDiscountPercent) {
                    if (!price || !quantity) return 0;

                    const total = price * quantity;

                    let discount = 0;

                    // If individual discount is available, prioritize it
                    if (individualDiscountPercent && individualDiscountPercent > 0) {
                        discount = (total * individualDiscountPercent) / 100;
                    } else if (generalDiscountPercent && generalDiscountPercent > 0) {
                        discount = (total * generalDiscountPercent) / 100;
                    }

                    return parseFloat(discount.toFixed(2));
                },

                updateServiceQuantity: function(booking_service_id, service_booking_price,
                    tax_percent,
                    quantity, discount_percent, individual_discount_percent) {
                    var self = this;
                    self.grandTotal_all_raw();

                    if (!quantity || quantity < 1) {
                        return;
                    }

                    self.postFinalizedBookingEdit({
                        method: 'POST',
                        url: "{{ route('salon.service.quantity.update') }}",
                        data: {
                            booking_service_id: booking_service_id,
                            service_booking_price: service_booking_price,
                            tax_percent: tax_percent,
                            quantity: quantity,
                            discount_percent: discount_percent,
                            individual_discount_percent: individual_discount_percent,
                        },
                        success: function(data) {
                            if (data && data.booking_info) {
                                vue_details_tab_edit.booking_info = data.booking_info;
                            }
                            if (data && data.needs_update) {
                                vue_details_tab_edit.markBookingNeedsUpdate();
                            }
                            vue_details_tab_edit.grandTotal_all_raw();
                        },
                        error: function (xhr) {
                            var msg = 'Could not update service quantity.';
                            if (xhr.responseJSON && xhr.responseJSON.js_notifications && xhr.responseJSON.js_notifications.error) {
                                msg = xhr.responseJSON.js_notifications.error;
                            }
                            if (typeof js_notify === 'function') {
                                js_notify(msg, 'error');
                            } else {
                                alert(msg);
                            }
                        },
                    });
                },

                formatUsedQty: function (value) {
                    var n = Number(value);
                    if (!isFinite(n)) {
                        return '—';
                    }
                    return String(Math.round(n * 1000) / 1000);
                },

                usedStockLeft: function (line) {
                    if (!line || line.current_stock == null || line.current_stock === '') {
                        return null;
                    }
                    var onHand = Number(line.current_stock);
                    var accounted = Number(line.stock_accounted_qty || 0);
                    var typed = Number(line.quantity);
                    if (!isFinite(typed)) {
                        typed = 0;
                    }
                    return Math.round((onHand + accounted - typed) * 1000) / 1000;
                },

                notifyUsedProductError: function (xhr, fallback) {
                    var msg = fallback;
                    if (xhr && xhr.responseJSON && xhr.responseJSON.js_notifications && xhr.responseJSON.js_notifications.error) {
                        msg = xhr.responseJSON.js_notifications.error;
                    }
                    if (typeof js_notify === 'function') {
                        js_notify(msg, 'error');
                    } else {
                        alert(msg);
                    }
                },

                openUsedProductSearch: function (bserv) {
                    this.used_product_for = bserv.booking_service_id;
                    this.used_product_search = '';
                    this.used_product_hits = [];
                },

                searchUsedProducts: function (bserv) {
                    var self = this;
                    self.used_product_for = bserv.booking_service_id;
                    var term = (self.used_product_search || '').trim();
                    if (term.length < 1) {
                        self.used_product_hits = [];
                        return;
                    }
                    $.get("{{ route('salon.booking.used-product.search') }}", { q: term })
                        .done(function (data) {
                            if (self.used_product_for == bserv.booking_service_id) {
                                self.used_product_hits = (data && data.items) ? data.items : [];
                            }
                        });
                },

                addUsedProduct: function (bserv, hit) {
                    var self = this;
                    self.postFinalizedBookingEdit({
                        method: 'POST',
                        url: "{{ route('salon.booking.used-product.add') }}",
                        data: {
                            booking_service_id: bserv.booking_service_id,
                            item_id: hit.item_id,
                            quantity: 1,
                        },
                        success: function (data) {
                            if (data && data.booking_info) {
                                self.booking_info = data.booking_info;
                            }
                            self.used_product_search = '';
                            self.used_product_hits = [];
                            self.used_product_for = null;
                        },
                        error: function (xhr) {
                            self.notifyUsedProductError(xhr, 'Could not add product.');
                        },
                    });
                },

                updateUsedProductQty: function (line) {
                    var self = this;
                    var previous = line._previous_quantity;
                    self.postFinalizedBookingEdit({
                        method: 'POST',
                        url: "{{ route('salon.booking.used-product.quantity') }}",
                        data: {
                            booking_service_product_id: line.booking_service_product_id,
                            quantity: line.quantity,
                        },
                        success: function (data) {
                            if (data && data.booking_info) {
                                self.booking_info = data.booking_info;
                            }
                        },
                        error: function (xhr) {
                            if (previous != null) {
                                line.quantity = previous;
                            }
                            self.notifyUsedProductError(xhr, 'Could not update product quantity.');
                        },
                    });
                },

                removeUsedProduct: function (line) {
                    var self = this;
                    self.postFinalizedBookingEdit({
                        method: 'POST',
                        url: "{{ route('salon.booking.used-product.remove') }}",
                        data: {
                            booking_service_product_id: line.booking_service_product_id,
                        },
                        success: function (data) {
                            if (data && data.booking_info) {
                                self.booking_info = data.booking_info;
                            }
                        },
                        error: function (xhr) {
                            self.notifyUsedProductError(xhr, 'Could not remove product.');
                        },
                    });
                },

                updateServicePrice: function(booking_service_id, service_booking_price,
                    tax_percent) {
                    var self = this;
                    self.postFinalizedBookingEdit({
                        method: 'POST',
                        url: "{{ route('salon.service.price.update') }}",
                        data: {
                            booking_service_id: booking_service_id,
                            service_booking_price: service_booking_price,
                            tax_percent: tax_percent,
                        },
                        success: function(data) {
                            const bserv = vue_details_tab_edit.booking_info.booking_only_services.find(item =>
                                item
                                .booking_service_id === data.booking_service_id);
                            if (bserv) {
                                bserv.tax_amount = data.tax_amount;
                                bserv.line_total = data.line_total;
                                bserv.discount_amount = data.discount_amount;
                                bserv.individual_discount_amt = data
                                    .individual_discount_amt;
                            }
                            if (data.booking_info) {
                                vue_details_tab_edit.booking_info = data.booking_info;
                            } else if (data.booking_total != null) {
                                vue_details_tab_edit.booking_info.booking_total = data.booking_total;
                            }
                            if (data.needs_update) {
                                vue_details_tab_edit.markBookingNeedsUpdate();
                            }
                            vue_details_tab_edit.grandTotal_all_raw();
                        },
                    });
                },

                // updateQuantityPrice: function(booking_service_id, service_booking_price, quantity) {
                //     const newTotal = quantity * service_booking_price;
                //     this.service_booking_price = newTotal;
                // },

                getTimeSlot: function(e, flag) {

                    vue_details_tab_edit.shift_id = $("#shift_data :selected").val();
                    vue_details_tab_edit.duration_minutes = $("#duration_minutes :selected")
                        .val();
                    if (flag == 1) {
                        $("div").removeClass("bu-selected");
                        vue_details_tab_edit.beautician_id = e.beautician_id;
                    }
                    bu_selected = "#bu" + e.beautician_id;
                    vue_details_tab_edit.temp_beatician_id = e.beautician_id;
                    setTimeout(function() {
                        $(bu_selected).addClass("bu-selected")
                    }, 1000);
                    vue_details_tab_edit.beautician_id = e.beautician_id;

                    $.ajax({
                        method: "POST",
                        url: "{{ route('booking.change') }}",
                        data: {
                            service: vue_details_tab_edit.service_id,
                            is_pos: 1,
                            service_type: 3,
                            shift_id: vue_details_tab_edit.shift_id,
                            duration: vue_details_tab_edit.duration_minutes,
                            price: vue_details_tab_edit.price,
                            beautician: vue_details_tab_edit.beautician_id,
                            date: vue_details_tab_edit.focused_active_booking_date,

                        },

                    }).done(function(data) {

                        $(vue_details_tab_edit.changing_price_var).text(data.price);
                        if (data.all_db_shifts.length > 0) {
                            vue_details_tab_edit.all_shifts = data.all_db_shifts;
                        }
                        this.db_shift = data.db_shift;

                        if (data.new_duration.length > 0) {
                            vue_details_tab_edit.new_duration = data.new_duration;
                        }
                        if (data.beauticians.length > 0) {
                            bu_selcted_data = data.beauticians[0];
                            vue_details_tab_edit.beauticians = data.beauticians[0]
                                .beautician_id;

                        }

                        vue_details_tab_edit.time_slot = data.time_slot;

                        vue_details_tab_edit.beauticians = data.beauticians;
                        vue_details_tab_edit.beautician_id = data.beauticians[0]
                            .beautician_id;

                        $('input[name=time]').attr('checked', false);
                        if (!data.beautician_flag) {
                            $('input[name=beautician]').attr('checked', false);
                        }

                    });
                },
                // -==========================new addecd section

                isPaymentExceeded: function() {
                    return false;
                },
                changeProductTab: function(id) {

                    vue_details_tab_edit.change_product_cat_id = id;
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('salon.booking.get.product') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id: id || 0,
                            name: vue_details_tab_edit.search_product_name || '',
                        },
                        dataType: "json",
                        success: function(data) {
                            vue_details_tab_edit.all_items_list = data || [];
                        },
                        error: function(xhr) {
                            vue_details_tab_edit.all_items_list = [];
                        }
                    });
                },
                searchService: function() {
                    $('#sub-service').addClass('sub-service-click');
                    $('#sub_serv_0').css('display', 'block');
                    vue_details_tab_edit.search_query = $('#filterServ').val();
                    vue_details_tab_edit.selected_service_id = 0;
                    vue_details_tab_edit.changeSeviceTab(vue_details_tab_edit
                        .selected_service_id);
                },
                searchProduct: function() {
                    vue_details_tab_edit.search_product_name = $('#filterProduct').val();
                    vue_details_tab_edit.change_product_cat_id = 0;
                    vue_details_tab_edit.changeProductTab(vue_details_tab_edit
                        .change_product_cat_id);
                },
                changeProductCategory(id) {
                    $('#filterProduct').val("");
                    vue_details_tab_edit.change_product_cat_id = id;
                    vue_details_tab_edit.search_product_name = null;
                    vue_details_tab_edit.changeProductTab(vue_details_tab_edit
                        .change_product_cat_id);
                },
                changeServiceCategory(id) {
                    $('#filterServ').val("");
                    vue_details_tab_edit.selected_service_id = id;
                    vue_details_tab_edit.search_query = null;
                    vue_details_tab_edit.changeSeviceTab(vue_details_tab_edit
                        .selected_service_id);
                },
                changeSeviceTab: function(id) {
                    $('input:radio').removeAttr('checked');
                    vue_details_tab_edit.selected_service_id = id;

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('salon.booking.get.service') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id: vue_details_tab_edit.selected_service_id || 0,
                            name: vue_details_tab_edit.search_query || '',
                        },
                        dataType: "json",
                        success: function(data) {
                            vue_details_tab_edit.all_services = data || [];
                        },
                        error: function(xhr) {
                            vue_details_tab_edit.all_services = [];
                        }
                    });
                },

                selectedDate: function(date) {

                    $('#calendar-wrapper').updateCalendarOptions({
                        date: date
                    });
                    vue_details_tab_edit.focused_active_booking_date = date;
                    vue_details_tab_edit.selected_date = date;

                    if (vue_details_tab_edit.is_reschedule) {
                        return;
                    }

                    vue_details_tab_edit.getbookingData('null', 'date', 'null');
                },


            },
            computed: {
                isBookingCompleted: function() {
                    if (!this.booking_info || !this.booking_info.booking_only_services) {
                        return false;
                    }
                    return this.booking_info.booking_only_services.every(service => service.status === 'completed');
                },
                canPrintUpdate: function() {
                    return this.isBookingFullyPaid();
                },
                canSubmitUpdate: function() {
                    if (!this.isBookingFullyPaid()) {
                        return false;
                    }
                    // First finalize: paid but no booking code yet
                    if (!this.booking_info.booking_code) {
                        return true;
                    }
                    // After finalize: only if payment/cart was edited
                    return !!this.booking_needs_update;
                },
                popup_client_info: function() {
                    var modified = {};
                    for (var i in this.booking_info.real_client) {
                        modified[i] = (this.popup_client_type === "new") ? null : this
                            .booking_info
                            .real_client[i];
                    }
                    return modified;
                },
                computedTaxAmount() {
                    return this.bookingInfo.service_booking_price ?
                        (this.bookingInfo.service_booking_price * this.bookingInfo.tax_percent) /
                        100 :
                        0;
                },

                suggestedTakenAmount: function() {
                    var bamt = this.v_amount(this.grand_total);
                    if (isNaN(bamt) || !bamt) {
                        bamt = this.v_amount(this.booking_info.booking_total);
                    }
                    var paid = this.v_amount(this.booking_info.payment_total);
                    if (isNaN(bamt) || isNaN(paid)) {
                        return null;
                    }
                    return (bamt > 0 && bamt > paid) ? this.v_fixed_amount(bamt - paid) : null;
                },
                amountTakenDisplay: {
                    get: function() {
                        return this.amount_taken_input;
                    },
                    set: function(newVal) {
                        this.is_editing_amount_paid = true;
                        this.amount_taken_flag = 1;
                        var raw = (newVal === null || typeof newVal === 'undefined') ? '' : String(newVal);
                        raw = raw.replace(/[^\d.]/g, '');
                        var parts = raw.split('.');
                        if (parts.length > 2) {
                            raw = parts[0] + '.' + parts.slice(1).join('');
                        }
                        this.amount_taken_input = raw;
                        var parsed = parseFloat(raw);
                        this.amount_taken = (raw === '' || isNaN(parsed)) ? '' : parsed;
                    }
                },
                balanceDueDisplay: function() {
                    var due = this.v_amount(this.grand_total || (this.booking_info && this.booking_info.booking_total)) -
                        this.v_amount(this.booking_info && this.booking_info.payment_total);
                    if (isNaN(due) || due < 0) {
                        return '0.00';
                    }
                    return this.v_fixed_amount(due);
                },
                paymentBreakdownByMode: function() {
                    var payments = (this.booking_info && this.booking_info.payments) || [];
                    var totals = {};
                    var modeLabels = {};
                    var labels = {
                        cash: '{{ __('Cash') }}',
                        card: '{{ __('Card') }}',
                        online: '{{ __('Online') }}',
                        credit: '{{ __('Credit') }}',
                    };

                    payments.forEach(function(payment) {
                        var mode = String(payment.payment_mode || 'other').toLowerCase();
                        var net = parseFloat(payment.amount_taken || 0) - parseFloat(payment.amount_given || 0);
                        if (isNaN(net) || net <= 0) {
                            return;
                        }
                        totals[mode] = (totals[mode] || 0) + net;
                        if (!modeLabels[mode] && payment.payment_mode_locale) {
                            modeLabels[mode] = payment.payment_mode_locale;
                        }
                    });

                    return Object.keys(totals).map(function(mode) {
                        return {
                            mode: mode,
                            label: modeLabels[mode] || labels[mode] || mode.charAt(0).toUpperCase() + mode.slice(1),
                            amount: totals[mode],
                        };
                    }).sort(function(a, b) {
                        return b.amount - a.amount;
                    });
                },
                showPaymentBreakdown: function() {
                    return this.paymentBreakdownByMode.length > 0;
                },
                splitPaymentTargetTotal: function() {
                    if (this.split_payment_mode === 'rebalance') {
                        return this.v_amount(this.booking_info && this.booking_info.payment_total);
                    }

                    return this.remainingBalanceDue();
                },
                splitPaymentEnteredTotal: function() {
                    var sp = this.split_payment || {};
                    return this.v_amount(sp.cash)
                        + this.v_amount(sp.card)
                        + this.v_amount(sp.online)
                        + this.v_amount(sp.credit);
                },
                splitPaymentModalRemaining: function() {
                    var remaining = this.splitPaymentTargetTotal - this.splitPaymentEnteredTotal;
                    if (isNaN(remaining)) {
                        return 0;
                    }
                    return Number(Math.max(remaining, 0).toFixed(2));
                },
                canSaveSplitPayment: function() {
                    if (this.split_payment_saving) {
                        return false;
                    }
                    if (this.splitPaymentEnteredTotal < 0.01) {
                        return false;
                    }
                    if (this.splitPaymentModalRemaining >= 0.01) {
                        return false;
                    }
                    if (this.v_amount(this.split_payment.card) >= 0.01 && !this.split_payment.card_type) {
                        return false;
                    }

                    return true;
                },
                splitPaymentCashChange: function() {
                    var cash = this.v_amount(this.split_payment.cash);
                    var tendered = this.v_amount(this.split_payment.cash_tendered);
                    if (cash > 0 && tendered > cash) {
                        return Number((tendered - cash).toFixed(2));
                    }

                    return 0;
                },
                paymentTotalClass: function() {
                    return 'bg-succes';
                    //{
                    // 'bg-warning': this.v_amount(this.booking_info.payment_total) < this.v_amount(this.booking_info.booking_total),
                    // 'text-dark': this.v_amount(this.booking_info.payment_total) < this.v_amount(this.booking_info.booking_total),
                    // 'bg-success': this.v_amount(this.booking_info.payment_total) == this.v_amount(this.booking_info.booking_total),
                    // 'bg-danger': this.v_amount(this.booking_info.payment_total) > this.v_amount(this.booking_info.booking_total)
                    //}
                }
            }
        });
    });

    var bookingServiceSaved = function($form, data) {
        if (data.booking_info) {
            vueMergeBookingInfo(data);
            if (data.booking_info.booking_code || data.needs_update) {
                vue_details_tab_edit.markBookingNeedsUpdate();
                $('#serviceLookup').modal('hide');
                return;
            }
            location.reload();
        } else if (data.js_notifications.warning) {
            return 0;
        } else {
            alert("something went wrong...!!")
        }
    };

    var bookingServiceDeleted = function($form, data) {
        if (data.booking_info) {
            vueMergeBookingInfo(data);
        } else {
            alert("Failed to delete service. Try again later.")
        }
    };

    var popupSlotUpdated = function($form, data) {
        if (data.booking_info) {
            vueMergeBookingInfo(data);
            if (vue_details_tab_edit.booking_info && vue_details_tab_edit.booking_info.booking_code) {
                vue_details_tab_edit.markBookingNeedsUpdate();
                sessionStorage.setItem(
                    'booking_needs_update_' + vue_details_tab_edit.booking_id,
                    '1'
                );
            }
        } else {
            alert("Failed to update service. Try again later.")
        }
    };

    var paymentItemSaved = function($form, data) {
        var info = null;
        if (data.booking_info) {
            info = data.booking_info;
        } else if (data.vue_data && data.vue_data.vue_details_tab_edit && data.vue_data.vue_details_tab_edit.booking_info) {
            info = data.vue_data.vue_details_tab_edit.booking_info;
        }

        if (!info) {
            return;
        }

        vue_details_tab_edit.booking_info = info;
        if (typeof vue_details_tab_info !== 'undefined') {
            vue_details_tab_info.booking_info = info;
        }

        if (!data.rebalanced && vue_details_tab_edit.booking_info && vue_details_tab_edit.booking_info.booking_code) {
            vue_details_tab_edit.markBookingNeedsUpdate();
        }

        vue_details_tab_edit.amount_taken_flag = 0;
        vue_details_tab_edit.is_editing_amount_paid = false;
        vue_details_tab_edit.$nextTick(function() {
            vue_details_tab_edit.grandTotal_all_raw();
            if (typeof vue_details_tab_edit.prepareNextSplitPayment === 'function') {
                vue_details_tab_edit.prepareNextSplitPayment();
                if (vue_details_tab_edit.remainingBalanceDue() > 0 && typeof js_notify === 'function') {
                    js_notify('{{ __('Payment added. Continue with the next split payment.') }}', 'info');
                }
            } else if (typeof vue_details_tab_edit.syncAmountTakenInput === 'function') {
                vue_details_tab_edit.syncAmountTakenInput();
            }
        });
    };

    var discountSaved = function($form, data) {
        var info = null;
        if (data && data.booking_info) {
            info = data.booking_info;
        } else if (data && data.vue_data && data.vue_data.vue_details_tab_edit && data.vue_data.vue_details_tab_edit.booking_info) {
            info = data.vue_data.vue_details_tab_edit.booking_info;
        }

        if (info) {
            vue_details_tab_edit.booking_info = info;
            if (typeof vue_details_tab_info !== 'undefined') {
                vue_details_tab_info.booking_info = info;
            }
        }

        if (vue_details_tab_edit.booking_info && vue_details_tab_edit.booking_info.booking_code) {
            vue_details_tab_edit.markBookingNeedsUpdate();
        }

        vue_details_tab_edit.amount_taken_flag = 0;
        vue_details_tab_edit.is_editing_amount_paid = false;
        vue_details_tab_edit.$nextTick(function() {
            vue_details_tab_edit.grandTotal_all_raw();
            vue_details_tab_edit.syncAmountTakenInput();
            if (typeof syncDiscountToggleState === 'function') {
                syncDiscountToggleState();
            }
        });
    };

    var paymentItemDeleted = function($form, data) {
        if (data.booking_info) {
            vueMergeBookingInfo(data);
        } else if (data.vue_data && data.vue_data.vue_details_tab_edit && data.vue_data.vue_details_tab_edit.booking_info) {
            vueMergeBookingInfo({
                booking_info: data.vue_data.vue_details_tab_edit.booking_info
            });
        } else {
            alert("Failed to remove payment entry. Try again later.")
            return;
        }

        if (vue_details_tab_edit.booking_info && vue_details_tab_edit.booking_info.booking_code) {
            vue_details_tab_edit.markBookingNeedsUpdate();
        }

        vue_details_tab_edit.$nextTick(function() {
            if (typeof vue_details_tab_edit.prepareNextSplitPayment === 'function') {
                vue_details_tab_edit.prepareNextSplitPayment();
            }
        });
    };

    var vueMergeBookingInfo = function(data) {

        var info = data.booking_info;
        //vue_details_tab_edit.getbookingData(info);
        vue_details_tab_edit.booking_info = info;

        if (typeof vue_details_tab_info !== 'undefined') {
            vue_details_tab_info.booking_info = info;
        }

        if (info && info.booking_code) {
            vue_details_tab_edit.markBookingNeedsUpdate();
        }

        vue_details_tab_edit.amount_taken_flag = 0;
        vue_details_tab_edit.is_editing_amount_paid = false;
        vue_details_tab_edit.$nextTick(function() {
            vue_details_tab_edit.grandTotal_all_raw();
            if (typeof vue_details_tab_edit.syncAmountTakenInput === 'function') {
                vue_details_tab_edit.syncAmountTakenInput();
            }
        });

        //location.reload();
    };


    var grand_total_global = function() {
        var grandTotal = 0;
        for (var i in all_booking_info.booking_only_services) {
            var service_id = all_booking_info.booking_only_services[i].booking_service_id;
            var html_id = "#total_" + service_id;
            var lineTotal = $(html_id).text();
            grandTotal = parseFloat(lineTotal) + grandTotal;

        }
        vue_details_tab_edit.amount_taken = isNaN(grandTotal) ? 0 : grandTotal;
        vue_details_tab_edit.grand_total = grandTotal;
        vue_details_tab_edit.grand_total_1 = grandTotal;
    }





    var flatpickr_dob = null;

    function refreshFlatPicker() {

        if (flatpickr_dob) {
            flatpickr_dob.setDate(vue_details_tab_edit.popup_client_info.date_of_birth, true);
        } else {
            flatpickr_dob = flatpickr(".date_of_birth_ig", {
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                wrap: true
            });
        }
    }

    jQuery(function($) {
        var _originalAjaxSubmitForm = window.ajaxSubmitForm;
        window.ajaxSubmitForm = function($form, callback) {
            if (typeof vue_details_tab_edit !== 'undefined'
                && vue_details_tab_edit.isPaymentFinalized
                && vue_details_tab_edit.isPaymentFinalized()
            ) {
                vue_details_tab_edit.requireFinalizedEditAuth(function(password) {
                    if (password === null) {
                        return false;
                    }
                    vue_details_tab_edit.appendAuthPasswordField($form, password);
                    _originalAjaxSubmitForm($form, callback);
                });
                return;
            }
            _originalAjaxSubmitForm($form, callback);
        };

        $("body").on("click", ".booking-line-remove", function(e) {
            e.preventDefault();
            var $this = $(this);
            bootConfirm(null, "Confirm delete this entry?", function(result) {
                if (result) {
                    ajaxSubmitForm($($this.closest("form")[0]))
                }
            });
        });

        $("body").on("click", ".payment-remove", function(e) {
            e.preventDefault();
            var $this = $(this);
            var $form = $($this.closest("form")[0]);

            bootConfirm(null, "{{ __('Confirm delete this payment?') }}", function(result) {
                if (!result) {
                    return;
                }

                var submitVoid = function(password) {
                    $form.find(".js-auth-password").val(password || '');
                    ajaxSubmitForm($form);
                };

                if (vue_details_tab_edit.isPaymentFinalized()) {
                    vue_details_tab_edit.requireFinalizedEditAuth(function(password) {
                        if (password === null) {
                            return;
                        }
                        submitVoid(password);
                    });
                    return;
                }

                submitVoid('');
            });
        });

        // $("#client-quick-edit-modal").on("show.bs.modal", function(event) {
        //     vue_details_tab_edit.popup_client_type = $(event.relatedTarget).data("popup-type");

        //     $("#gender").val(vue_details_tab_edit.popup_client_info.gender).trigger("change");

        //     // remove previous form errors
        //     var $form = $("#client-quick-edit-modal .voco-ajax-form");
        //     $form.find(".invalid-feedback").remove();
        //     $form.find(".form-control").removeClass("is-invalid");
        //     if (vue_details_tab_edit.popup_client_info.mobile && vue_details_tab_edit.popup_client_info
        //         .mobile.length > 0) {
        //         setTimeout(() => {
        //             var mob = vue_details_tab_edit.popup_client_info.mobile.substring(4, 13); -
        //             $("#mobile").val(mob)
        //         }, 1000);
        //     } else {
        //         // If triggered from the "New Customer" button, get the search input value
        //         let searchInput = $(".select2-search__field").val();
        //         $("#mobile").val(searchInput || "");
        //     }

        //     // Set the name field value
        //     if (vue_details_tab_edit.popup_client_info.name && vue_details_tab_edit.popup_client_info
        //         .name.length > 0) {
        //         setTimeout(() => {
        //             $("#name").val(vue_details_tab_edit.popup_client_info.name);
        //         }, 1000);
        //     } else {
        //         // If triggered from the "New Customer" button, get the search input value
        //         let searchInput = $(".select2-search__field").val();
        //         $("#name").val(searchInput || "");
        //     }
        // });

        $("#client-quick-edit-modal").on("show.bs.modal", function(event) {
            const popupType = $(event.relatedTarget).data("popup-type");
            vue_details_tab_edit.popup_client_type = popupType;

            // Remove previous form errors
            const $form = $("#client-quick-edit-modal .voco-ajax-form");
            $form.find(".invalid-feedback").remove();
            $form.find(".form-control").removeClass("is-invalid");

            // Set the mobile field value (local 10 digits; strip +91/91 country code)
            const mobileValue = vue_details_tab_edit.popup_client_info.mobile || "";
            if (mobileValue.length > 0) {
                setTimeout(() => {
                    let digits = String(mobileValue).replace(/\D/g, '');
                    if (digits.length > 10 && digits.startsWith('91')) {
                        digits = digits.substring(digits.length - 10);
                    }
                    $("#mobile").val(digits.substring(0, 10));
                    vue_details_tab_edit.check_mobile_no();
                }, 1000);
            } else {
                // If triggered from the "New Customer" button, get the search input value
                const searchInput = $(".select2-search__field").val() || "";
                let digits = String(searchInput).replace(/\D/g, '');
                if (digits.length > 10 && digits.startsWith('91')) {
                    digits = digits.substring(digits.length - 10);
                }
                $("#mobile").val(digits.substring(0, 10));
                vue_details_tab_edit.check_mobile_no();
            }

            // Set the name field value
            const nameValue = vue_details_tab_edit.popup_client_info.name || "";
            if (nameValue.length > 0 && !/^\d+$/.test(nameValue)) {
                setTimeout(() => {
                    $("#name").val(nameValue);
                }, 1000);
            } else {
                // If triggered from the "New Customer" button, get the search input value
                const searchInput = $(".select2-search__field").val();
                if (!/^\d+$/.test(searchInput)) {
                    $("#name").val(searchInput || "");
                } else {
                    $("#name").val("");
                }
            }
        });

        $("body").on("click", "#client-quick-edit-modal .btn-voco-save", function() {

            ajaxSubmitForm($("#client-quick-edit-modal .voco-ajax-form"), function($form, data) {

                const selectedClientId = data.vue_data.vue_details_tab_edit.booking_info
                    .client_id;
                const selectedClientText = data.vue_data.vue_details_tab_edit.booking_info
                    .client_name;
                const selectedClientMobile = data.vue_data.vue_details_tab_edit.booking_info
                    .client_mobile;
                $('#client_id').select2("destroy");
                $('#client_id').html(
                    `<option value="${selectedClientId}" selected>${selectedClientText}(${selectedClientMobile})</option>`
                );

                // Reinitialize select2
                $('#client_id').select2();
                $("#client-quick-edit-modal").modal("hide");



                // window.location.reload();
                // this.$nextTick(function() {
                //     this.refreshSelect2Services();
                //     this.refreshSelect2Items();
                //     applySelect2();
                //     refreshFlatPicker();
                //     this.refreshDiscountFunction();
                //     grandTotal_all_raw();

                // })


            });
            // location.reload();
        });

        $("body").on("click", ".cancel-booking", function(e) {
            e.preventDefault();
            var $this = $(this);
            var dialog = bootbox.confirm({
                title: "Cancel Booking",
                message: "Are you sure to cancel this booking?",
                buttons: {
                    cancel: {
                        label: '<i class="fa fa-times"></i> No'
                    },
                    confirm: {
                        label: '<i class="fa fa-check"></i> Cancel Booking',
                        className: 'btn-danger'
                    }
                },
                callback: function(result) {
                    if (result) {
                        ajaxSubmitForm($($this.closest("form")[0]))
                    }
                }
            });

            dialog.on('shown.bs.modal', function() {
                dialog.find(".btn-default:first").focus();
            });
        });

        // When Given (change) > 0, ask: return cash or add to client credit
        $("body").on("click", ".js-booking-payment-form button[type='submit']", function(e) {
            var $form = $(this).closest("form");
            var givenRaw = $form.find('[name="amount_given"]').val();
            var given = parseFloat(givenRaw);

            if (isNaN(given) || given <= 0) {
                $form.find(".js-apply-change-to-credit").val("0");
                return;
            }

            e.preventDefault();
            e.stopImmediatePropagation();

            var givenText = given.toFixed(2);
            bootbox.dialog({
                title: "{{ __('Given Amount') }}",
                message: "{{ __('Given amount is') }} <strong>" + givenText +
                    "</strong>. {{ __('Return it to the client, or add it to client credit?') }}",
                buttons: {
                    returnCash: {
                        label: '<i class="fa fa-money"></i> {{ __('Return to Client') }}',
                        className: 'btn-secondary',
                        callback: function() {
                            $form.find(".js-apply-change-to-credit").val("0");
                            ajaxSubmitForm($form);
                        }
                    },
                    addCredit: {
                        label: '<i class="fa fa-user"></i> {{ __('Add to Client Credit') }}',
                        className: 'btn-primary',
                        callback: function() {
                            $form.find(".js-apply-change-to-credit").val("1");
                            ajaxSubmitForm($form);
                        }
                    }
                }
            });

            return false;
        });

        $("body").on("click", "#add_product_id", function() {

            if (vue_details_tab_edit.booking_info.client_name == "" || vue_details_tab_edit
                .booking_info
                .client_name === undefined) {

                return 0;
            }
            var exry_date = "";
            var todayDate = "";
            var $this = $(this);
            exry_date = new Date($(this).find(':selected').data('expiry-date'));
            todayDate = new Date();
            if (exry_date < todayDate) {

                alert("item is expired...!!");
                return;
            }
            $('#line_item_type').val('item');
            if ($(this).find(':selected').data('service-id')) {

                $('#d_service_id').val($this.val());
            } else if ($(this).find(':selected').data('price-id')) {

                $('#d_price_id').val($this.val());
                $('#d_bath_no').val($(this).find(':selected').data('batch-no'));

            }
            ajaxSubmitForm($($this.closest("form")[0]));
            //location.reload();

        });
        $("body").on("click", "#add_service_id", function() {
            var serv_id = vue_details_tab_edit.booking_serv_id;
            if (vue_details_tab_edit.booking_info.client_name == "" || vue_details_tab_edit
                .booking_info
                .client_name === undefined) {
                alert('Please add client...!!');
                return 0;
            }
            var exry_date = "";
            var todayDate = "";
            var $this = $(this);
            $('#line_item_type').val('service');

            // $('#d_service_id').val(localStorage.getItem("serv_id"));
            // $('#d_beatcian_id').val(localStorage.getItem("beautician_id"));
            // $('#d_shift_id').val(localStorage.getItem("shift_id"));
            // $('#d_time_slot_id').val(localStorage.getItem("time_slot"));

            // $('#d_date').val(localStorage.getItem("selected_date"));
            // $('#d_appointment_date').val(localStorage.getItem("selected_date"));

            $('#d_service_id').val(vue_details_tab_edit.selected_serv_id);
            $('#d_beatcian_id').val(vue_details_tab_edit.selected_beautician_id);
            $('#d_shift_id').val(vue_details_tab_edit.selected_shift_id);

            $('#d_time_slot_id').val(vue_details_tab_edit.selected_time_slot);

            $('#d_date').val(vue_details_tab_edit.selected_date);
            $('#d_appointment_date').val(vue_details_tab_edit.selected_date);

            $('#d_booking_serv_id').val(serv_id);
            ajaxSubmitForm($($this.closest("form")[0]));

        });

        // Quick Service submit is handled by Vue manageSubmitButton1 / submitForm.
        // Do not ajaxSubmitForm the booking client form from this button.

        // @TODO - merge selectors
        $("body").on("change", "#booking_date", function() {
            vue_details_tab_edit.getTimeSlot('text', 2);
            //vue_details_tab_edit.getbookingData(1);
            //ajaxSubmitForm($($(this).closest("form")[0]))

        });
        $(function() {
            $("#form1").submit(function(event) {
                var new_booking_url =
                    "{{ route('tab.salon.booking.details', ['booking_id' => '0', 'tab_name' => 'edit']) }}";
                event.preventDefault();

                var $form = $(this);
                var submitFinalUpdate = function() {
                    $.ajax({
                        url: "{{ route('salon.booking.final.update') }}",
                        type: 'post',
                        data: $form.serialize(),
                        success: function(result) {
                            if (typeof result === 'string') {
                                if (result === 'update') {
                                    window.location.href = new_booking_url;
                                    return;
                                }
                                window.open(result, "",
                                    "toolbar=no,status=no,menubar=no,location=center,scrollbars=no,resizable=no,height=500,width=657"
                                );
                                window.location.href = new_booking_url;
                                return;
                            }

                            if (result && result.js_notifications && result.js_notifications.error) {
                                alert(result.js_notifications.error);
                                return;
                            }

                            if (result && result.js_notifications) {
                                var noticeMsg = result.js_notifications.success
                                    || result.js_notifications.info
                                    || result.js_notifications.warning;
                                if (noticeMsg) {
                                    var noticeType = 'info';
                                    if (result.js_notifications.success) {
                                        noticeType = 'success';
                                    } else if (result.js_notifications.warning) {
                                        noticeType = 'warning';
                                    }
                                    sessionStorage.setItem('booking_finalize_notice', JSON.stringify({
                                        type: noticeType,
                                        message: noticeMsg
                                    }));
                                }
                            }

                            var redirectUrl = (result && result.redirect) ? result.redirect : new_booking_url;
                            if (result && result.print_url) {
                                window.open(result.print_url, "",
                                    "toolbar=no,status=no,menubar=no,location=center,scrollbars=no,resizable=no,height=500,width=657"
                                );
                            }
                            window.location.href = redirectUrl;
                        },
                        error: function(xhr) {
                            var msg = '{{ __('Could not update booking.') }}';
                            if (xhr.responseJSON && xhr.responseJSON.js_notifications && xhr.responseJSON.js_notifications.error) {
                                msg = xhr.responseJSON.js_notifications.error;
                            }
                            alert(msg);
                        }
                    });
                };

                if (vue_details_tab_edit.isPaymentFinalized()) {
                    vue_details_tab_edit.promptFinalUpdateReason(function(reason, password) {
                        if (!reason) {
                            return;
                        }
                        vue_details_tab_edit.appendAuthPasswordField($form, password);
                        $form.find('input[name="auth_password"]').val(password);
                        vue_details_tab_edit.appendEditReasonField($form, reason);
                        submitFinalUpdate();
                    });
                    return;
                }

                submitFinalUpdate();
            });


        });

        $("body").on("change", "#add_price_id", function() {

            ajaxSubmitForm($($(this).closest("form")[0]))
        });

        $("body").on("blur", "#applied_discount_percent", function() {
            var $form = $(this).closest("form");
            $form.find("#typeInput").val("global");
            // Prevent amount field from stacking when percentage is submitted
            $form.find("#applied_price_percent").prop("disabled", true);
            ajaxSubmitForm($form, function() {
                $form.find("#applied_price_percent").prop("disabled", false);
            });
        });

        $("body").on("blur", "#applied_price_percent", function() {
            var $form = $(this).closest("form");
            $form.find("#typeInput").val("price");
            // Prevent percentage field from stacking when amount is submitted
            $form.find("#applied_discount_percent").prop("disabled", true);
            ajaxSubmitForm($form, function() {
                $form.find("#applied_discount_percent").prop("disabled", false);
            });
        });

        $("body").on("change", "#client_id", function() {
            // Avoid tab-saver validation errors when Select2 is cleared unintentionally
            if (!$(this).val()) {
                return;
            }
            ajaxSubmitForm($($(this).closest("form")[0]))
        });

        $("body").on("click", ".view-slots", function(e) {
            e.preventDefault();
            var $trigger = $(this)
            $trigger.closest("table").find(".service-active").removeClass(
                "service-active anim-active");
            $trigger.closest("tr").addClass("service-active");
            vue_details_tab_edit.focused_active_service = $(this).data("service-id");
            vue_details_tab_edit.focused_active_booking_date = $(this).data("booking-date");
        });

        $("#beautician-slot-modal").on("hide.bs.modal", function(e) {
            $(".service-active").addClass("anim-active")
        })
    });


    jQuery(function() {
        $("body").on("click", ".notify_whatsapp", function(e) {
            e.preventDefault();
            var button = $(this);
            button.prop('disabled', true);
            $.ajax({
                url: button.data("url"),
                method: 'POST',
                dataType: 'json',
                data: {
                    booking_id: button.data("booking-id"),
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function(response) {
                if (response.js_notifications && typeof js_notify === 'function') {
                    js_notify(response.js_notifications.success, 'success');
                }
            }).fail(function(xhr) {
                var message = xhr.responseJSON && xhr.responseJSON.js_notifications
                    ? xhr.responseJSON.js_notifications.error
                    : 'Could not send the invoice on WhatsApp.';
                if (typeof js_notify === 'function') {
                    js_notify(message, 'error');
                } else {
                    alert(message);
                }
            }).always(function() {
                button.prop('disabled', false);
            });
        });
    });

    $(document).ready(function() {
        $('.voco-select2-client_id').select2({
            placeholder: 'Select Client',
            dropdownAutoWidth: true,
            ajax: {
                url: "{{ route('ajax-autocomplete-search') }}",
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                // text: item.name + ' (' + item.mobile + ')',
                                // id: item.client_id
                                text: `${item.name} (${item.mobile})`,
                                id: item.client_id,
                            };
                        })
                    };
                },
                cache: true
            },
            language: {
                noResults: function() {
                    return `
                    <button type="button"
                            id="newCustomerBtn"
                            data-target="#client-quick-edit-modal"
                            data-toggle="modal"
                            data-popup-type="new"
                            title=""
                            class="btn btn-dark w-100">
                        <i class="fa fa-plus"></i> {{ __('New customer') }}
                    </button>
                `;
                }
            },
            escapeMarkup: function(markup) {
                return markup; // Allows the button to render as HTML
            }
        });

        // Hide select2 dropdown when "New customer" button is clicked
        $(document).on('click', '#newCustomerBtn', function() {
            $('.voco-select2-client_id').select2('close');
        });
    });
</script>







<script src="{{ asset('web\calender-piker\calendar.min.js') }}"></script>
<script src="https://unpkg.com/codeflask/build/codeflask.min.js"></script>




<script>
    $(document).ready(function() {
        first_date = new Date();
        first_date = formatDate(first_date);
        // localStorage.setItem("selected_date", first_date);

        vue_details_tab_edit.selected_date = first_date;


        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2)
                month = '0' + month;
            if (day.length < 2)
                day = '0' + day;

            return [year, month, day].join('-');
        }
        $("#serv_date_piker").datepicker();

        var config = `
function selectDate(date) {
   $('#calendar-wrapper').updateCalendarOptions({

   date: date
 });

 date = formatDate(date);
 currentdate = formatDate(new Date(new Date));
 vue_details_tab_edit.currentdate = currentdate;
 vue_details_tab_edit.selected_date = date;
 localStorage.setItem("selected_date",date);
 vue_details_tab_edit.focused_active_booking_date = date;
 vue_details_tab_edit.getbookingData('null','date','null');
}

var defaultConfig = {
 weekDayLength: 1,
 date:   new Date(),
 onClickDate: selectDate,
 showYearDropdown: true,
 startOnMonday: true,
};

var calendar = $('#calendar-wrapper').calendar(defaultConfig);
`;

        eval(config);

    });
</script>


<script>
    function setDiscountToggleMode($form, mode) {
        const toggleButton = $form.find('#toggleDiscountButton');
        if (!toggleButton.length) {
            return;
        }

        const typeInput = $form.find('#typeInput');
        const discountInput = $form.find('#applied_discount_percent');
        const priceInput = $form.find('#applied_price_percent');

        if (mode === 'price') {
            toggleButton.text('₹');
            toggleButton.removeClass("toggle-active");
            discountInput.addClass("input-padding-tog");
            priceInput.removeClass("input-price-active");
            typeInput.val('price');
        } else {
            toggleButton.text('%');
            toggleButton.addClass("toggle-active");
            discountInput.removeClass("input-padding-tog");
            priceInput.addClass("input-price-active");
            typeInput.val('global');
        }
    }

    function syncDiscountToggleState() {
        $('.js-booking-discount-form').each(function() {
            const $form = $(this);
            const bookingInfo = (typeof vue_details_tab_edit !== 'undefined' && vue_details_tab_edit.booking_info)
                ? vue_details_tab_edit.booking_info
                : null;
            const amountDiscount = bookingInfo ? parseFloat(bookingInfo.applied_discount_price || 0) : 0;
            const percentDiscount = bookingInfo ? parseFloat(bookingInfo.applied_discount_percent || 0) : 0;

            if (amountDiscount > 0) {
                setDiscountToggleMode($form, 'price');
            } else if (percentDiscount > 0) {
                setDiscountToggleMode($form, 'global');
            } else {
                // Default to amount mode when no discount is set
                setDiscountToggleMode($form, 'price');
            }
        });
    }

    $(document).on('click', '#toggleDiscountButton', function() {
        const toggleButton = $(this);
        const form = toggleButton.closest('form');

        // Button label shows the mode you will switch INTO? Actually current code:
        // if showing '%', switch to price; else switch to percent.
        if (toggleButton.text().trim() === '%') {
            setDiscountToggleMode(form, 'price');
        } else {
            setDiscountToggleMode(form, 'global');
        }
    });

    // Set toggle state from existing booking discount on page load
    $(document).ready(function() {
        syncDiscountToggleState();
    });
</script>
<script>
    $(document).ready(function() {
        $('#save_btn').on('click', function() {

            $('#loading_spinner').show();

            setTimeout(function() {
                $('#loading_spinner').hide();
            }, 2000);
        });
    });
</script>

<script>
    $(document).ready(function() {
        $(document).on('click', '#view_split_edit_btn', function() {
            var item = window._viewSplitEditItem;
            $('#open-split-modal').modal('hide');
            if (item && typeof vue_details_tab_edit !== 'undefined' && typeof vue_details_tab_edit.split === 'function') {
                vue_details_tab_edit.split(item);
            }
        });

        if ($('#status-modal-split').length) {
            $('#status-modal-split').modal({
                backdrop: 'static',
                keyboard: false,
                show: false
            });
        }

        function getSplitBeauticianList() {
            var list = [];

            if (Array.isArray(window.SPLIT_BEAUTICIANS) && window.SPLIT_BEAUTICIANS.length) {
                list = window.SPLIT_BEAUTICIANS;
            } else if (window.vue_details_tab_edit && Array.isArray(vue_details_tab_edit.split_beauticians) && vue_details_tab_edit.split_beauticians.length) {
                list = vue_details_tab_edit.split_beauticians;
            } else {
                // Fallback: options already rendered for Quick Service
                $('#quickServiceLookup select.voco-select2 option, #split_beautician_options_source option').each(function() {
                    var id = String($(this).attr('value') || '');
                    var text = $.trim($(this).text() || '');
                    if (id !== '' && text !== '') {
                        list.push({ id: id, text: text });
                    }
                });
            }

            // De-dupe by id
            var seen = {};
            return list.filter(function(item) {
                var id = String(item.id || '');
                if (!id || seen[id]) return false;
                seen[id] = true;
                return true;
            });
        }

        function fillBeauticianSelect($select, selectedId) {
            var list = getSplitBeauticianList();
            var html = '<option value="">Select Beautician</option>';
            list.forEach(function(item) {
                html += '<option value="' + String(item.id).replace(/"/g, '&quot;') + '">' +
                    $('<div>').text(item.text || '').html() +
                    '</option>';
            });
            $select.html(html);

            if (selectedId !== undefined && selectedId !== null && String(selectedId) !== '' && String(selectedId) !== '0') {
                $select.val(String(selectedId));
                if (String($select.val()) !== String(selectedId)) {
                    $select.prop('selectedIndex', 0);
                }
            } else {
                $select.prop('selectedIndex', 0);
            }
        }

        function formatMoney(value) {
            var n = parseFloat(value);
            if (isNaN(n)) n = 0;
            return n.toFixed(2);
        }

        function buildSplitRow(beauticianId, canRemove) {
            var $row = $(
                '<div class="split-row">' +
                    '<select class="form-control split-beautician-select" name="beautician[]"></select>' +
                    '<div class="split-share-box">' +
                        '₹ <span class="split-share-value">0.00</span>' +
                        '<input type="hidden" class="price-field" name="price[]" value="0" />' +
                    '</div>' +
                    '<button type="button" class="split-remove-btn remove_field" title="Remove">' +
                        '<i class="fa fa-trash"></i>' +
                    '</button>' +
                '</div>'
            );
            fillBeauticianSelect($row.find('select.split-beautician-select'), beauticianId);
            if (!canRemove) {
                $row.find('.remove_field').css('visibility', 'hidden');
            }
            return $row;
        }

        $(document).on('shown.bs.modal', '#status-modal-split', function() {
            var $modal = $('#status-modal-split');
            var selectedBeauticianId = $modal.find('#modal_split_beautician_id').val();
            var commissionStartDate = $modal.find('#modal_commission_start_date').val();
            var commissionEndDate = $modal.find('#modal_commission_end_date').val();
            var commissionPercentage = $modal.find('#modal_commission_percentage').val();
            var commissionStatus = $modal.find('#modal_commission_status').val();
            var booking_service_id = $modal.find('#modal_split_booking_service_id').val();
            var booking_id = $modal.find('#modal_split_booking_id').val();
            var service_id = $modal.find('#modal_service_id').val();

            var selectedAmount = parseFloat($modal.find('#modal_service_amount').text()) || 0;
            var max_fields = 6;
            var wrapper = $modal.find('#split_rows_body');
            var add_button = $modal.find('.add_field_button');

            if (!getSplitBeauticianList().length) {
                alert('No beauticians available to split.');
            }

            wrapper.empty();

            function updatePrices() {
                var rows = wrapper.find('.split-row');
                var count = Math.max(1, rows.length);
                var equalShare = parseFloat((selectedAmount / count).toFixed(2));

                rows.each(function(index) {
                    var share = (index === count - 1)
                        ? parseFloat((selectedAmount - (equalShare * (count - 1))).toFixed(2))
                        : equalShare;
                    $(this).find("input[name='price[]']").val(share);
                    $(this).find('.split-share-value').text(formatMoney(share));
                });

                $modal.find('#modal_split_equal_share').text('₹ ' + formatMoney(equalShare));
                $modal.find('#modal_split_count_badge').text(count + (count === 1 ? ' person' : ' people'));
            }

            function refreshRemoveButtons() {
                var rows = wrapper.find('.split-row');
                rows.each(function(index) {
                    $(this).find('.remove_field').css('visibility', rows.length > 1 ? 'visible' : 'hidden');
                });
            }

            $.ajax({
                url: "{{ route('get.split.amount') }}",
                type: 'GET',
                data: {
                    booking_id: booking_id,
                    service_id: service_id,
                    booking_service_id: booking_service_id
                },
                success: function(response) {
                    if (response && response.length > 0) {
                        response.forEach(function(item, index) {
                            wrapper.append(buildSplitRow(item.beautician_id, index > 0));
                        });
                    } else {
                        wrapper.append(buildSplitRow(selectedBeauticianId, false));
                    }
                    refreshRemoveButtons();
                    updatePrices();
                },
                error: function() {
                    wrapper.append(buildSplitRow(selectedBeauticianId, false));
                    refreshRemoveButtons();
                    updatePrices();
                    alert('Could not load existing split. Starting fresh.');
                }
            });

            $(add_button).off('click.splitService').on('click.splitService', function(e) {
                e.preventDefault();
                if (wrapper.find('.split-row').length >= max_fields) {
                    alert('Maximum ' + max_fields + ' beauticians allowed.');
                    return;
                }
                wrapper.append(buildSplitRow('', true));
                refreshRemoveButtons();
                updatePrices();
            });

            $(wrapper).off('click.splitService', '.remove_field').on('click.splitService', '.remove_field', function() {
                $(this).closest('.split-row').remove();
                if (!wrapper.find('.split-row').length) {
                    wrapper.append(buildSplitRow(selectedBeauticianId, false));
                }
                refreshRemoveButtons();
                updatePrices();
            });

            $('#submitSplitForm').off('click.splitService').on('click.splitService', function(e) {
                e.preventDefault();

                var $btn = $(this);
                if ($btn.data('busy')) {
                    return;
                }

                var beauticians = [];
                var hasDuplicates = false;
                var hasEmpty = false;

                wrapper.find('.split-row').each(function() {
                    var value = $(this).find("select[name='beautician[]']").val();
                    if (!value) {
                        hasEmpty = true;
                        return;
                    }
                    if (beauticians.indexOf(value) !== -1) {
                        hasDuplicates = true;
                        return;
                    }
                    beauticians.push(value);
                });

                if (hasEmpty) {
                    alert('Please select a beautician for every row.');
                    return;
                }
                if (hasDuplicates) {
                    alert('Duplicate beautician entries are not allowed.');
                    return;
                }
                if (beauticians.length === 0) {
                    alert('Please select at least one beautician.');
                    return;
                }
                if (selectedAmount <= 0) {
                    alert('Invalid service amount for split.');
                    return;
                }

                var equalShare = parseFloat((selectedAmount / beauticians.length).toFixed(2));
                var prices = beauticians.map(function(_, index) {
                    if (index === beauticians.length - 1) {
                        return parseFloat((selectedAmount - (equalShare * (beauticians.length - 1))).toFixed(2));
                    }
                    return equalShare;
                });

                var data = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    beauticians: beauticians,
                    prices: prices,
                    booking_id: $modal.find('#modal_split_booking_id').val(),
                    beautician_id: $modal.find('#modal_split_beautician_id').val(),
                    service_name: $modal.find('#modal_service_name').text(),
                    service_started_at: $modal.find('#modal_service_started_at').val(),
                    line_total: selectedAmount,
                    start_date: commissionStartDate,
                    end_date: commissionEndDate,
                    commission_percentage: commissionPercentage,
                    commission_status: commissionStatus,
                    service_id: $modal.find('#modal_service_id').val(),
                    booking_service_id: $modal.find('#modal_split_booking_service_id').val(),
                };

                $btn.data('busy', true).prop('disabled', true);
                var originalHtml = $btn.html();
                $btn.html('<i class="fa fa-spinner fa-spin"></i> Saving...');

                var submitSplitSave = function(authPassword) {
                    data.auth_password = authPassword || '';
                    $.ajax({
                        url: "{{ route('api.split.draft') }}",
                        type: 'POST',
                        data: data,
                        success: function(response) {
                            if (response.errorcode == 0) {
                                var bookingId = $modal.find('#modal_split_booking_id').val();
                                if (bookingId) {
                                    sessionStorage.setItem('booking_needs_update_' + bookingId, '1');
                                }
                                $modal.modal('hide');
                                $('.modal-backdrop').remove();
                                location.reload();
                            } else {
                                alert('Error: ' + (response.errormessage || response.message || response.result || 'Unable to save split.'));
                            }
                        },
                        error: function(xhr) {
                            var msg = 'Failed to save split. Please try again.';
                            if (xhr.status === 419) {
                                msg = 'Session expired. Please refresh the page and try again.';
                            } else if (xhr.responseJSON && (xhr.responseJSON.errormessage || xhr.responseJSON.message)) {
                                msg = xhr.responseJSON.errormessage || xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.js_notifications && xhr.responseJSON.js_notifications.error) {
                                msg = xhr.responseJSON.js_notifications.error;
                            }
                            alert(msg);
                        },
                        complete: function() {
                            $btn.data('busy', false).prop('disabled', false).html(originalHtml);
                        }
                    });
                };

                if (vue_details_tab_edit && vue_details_tab_edit.isPaymentFinalized()) {
                    vue_details_tab_edit.requireFinalizedEditAuth(function(password) {
                        if (password === null) {
                            $btn.data('busy', false).prop('disabled', false).html(originalHtml);
                            return;
                        }
                        submitSplitSave(password);
                    });
                    return;
                }

                submitSplitSave('');
            });
        });
    });

    function resetForm() {
        clearModalFields();
    }

    function clearModalFields() {
        var $modal = $('#status-modal-split');
        $modal.modal('hide');

        $modal.find('#modal_split_booking_id').val('');
        $modal.find('#modal_split_beautician_id').val('');
        $modal.find('#modal_split_booking_service_id').val('');
        $modal.find('#modal_service_started_at').val('');
        $modal.find('#modal_commission_start_date').val('');
        $modal.find('#modal_commission_end_date').val('');
        $modal.find('#modal_commission_percentage').val('');
        $modal.find('#modal_commission_status').val('');
        $modal.find('#modal_service_id').val('');
        $modal.find('#modal_service_name').text('');
        $modal.find('#modal_service_amount').text('0.00');
        $modal.find('#modal_split_equal_share').text('₹ 0.00');
        $modal.find('#modal_split_count_badge').text('1');
        $modal.find('#split_rows_body').empty();
    }

    function clearViewSplitModal() {
        var $modal = $('#open-split-modal');
        $modal.modal('hide');
        $modal.find('#view_split_booking_id').val('');
        $modal.find('#view_split_beautician_id').val('');
        $modal.find('#view_split_booking_service_id').val('');
        $modal.find('#modal_service_name1').text('');
        $modal.find('#modal_service_amount1').text('0.00');
        $modal.find('#view_split_rows_body').empty();
    }
</script>
