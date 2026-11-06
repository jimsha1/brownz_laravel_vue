<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BrownzApp</title>

    <link rel="stylesheet" type="text/css" media="all" href="{{ asset('css/app-custom.css') }}">
    <link rel="stylesheet" type="text/css" media="all" href="{{ asset('admin/css/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    @include('layouts.vue_script')
    <script>
        var vc_moment_date_offset = new Date("{{ (new DateTime())->format('Y-m-d\\TH:i:s.vP') }}").getTime() - Date.now();
        var vc_jscalendar_moment_format = "{{ config('vocomi.settings.format.jscalendar.format_moment') }}";
    </script>
    <style>
        .sidebar .nav-dropdown.open {
            background: rgba(230, 226, 226, 0.2);
        }

        .nav-dropdown.open .nav-dropdown-toggle {
            background-color: #909090;
        }

        .sidebar .nav-link.active {
            background-color: #6f7071;
            border-left: 3px solid blue !important;
        }
    </style>
    <style>
        .brownz-table-list-v1.table thead th {
            border: none;
            color: #1D1D1D;
        }

        .brownz-table-list-v1.table.table tr td {
            border: none;
        }

        .brownz-table-list-v1.table.table tr td a {
            color: #52A8D9;
            text-decoration: underline;
        }

        .brownz-table-list-v1.table.table tbody tr:nth-child(odd) {
            background-color: rgba(230, 205, 127, 0.2);
        }
        .brownz-table-list-v1.table.table tbody tr input{
            background-color: #fff;
        }

        .brownz-table-list-v1.table.table tbody tr:nth-child(even) {
            background-color: rgba(255, 234, 169, 0.2);
        }

        .m-n1 {
            margin: -1rem;
        }

        .brow-tab-item {
            background-color: #F0F0F3;
        }

        .brow-tab-item .nav-link {
            background-color: transparent;
            border: none;
            height: 100%;
            display: flex;
            align-items: center;
            gap: 5px
        }

        .brow-tab-item .nav-link.active {
            background-color: #1D1D1D !important;
            color: #fff !important;
        }

        .brow-tab-item .nav-link.active:focus {
            background-color: #1D1D1D !important;
            color: #ffff !important;
        }

        .brow-tab-item .nav-link.active i {
            color: #E6CD7F !important;
        }

        .brow-tab-item {
            border-radius: 24px !important;
            overflow: hidden !important;
        }

        .table.brownz-table-list-v1 {
            margin-top: 20px;
        }

        .search-box-bar-container.jus-heigh-search-b {
            max-width: 100%;
            padding-top: 5px;
            padding-bottom: 5px;
        }
/*
        .btn_dark-x {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        } */
        .btn_dark-x {
    border: none;
    background-color: #1D1D1D;
    padding: 10px 16px;
    border-radius: 50px;
    font-family: "Roboto", sans-serif !important;
    min-width: max-content;
    color: #fff !important;
    font-size: 16px;
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

        .br-row-gap-2 {
            row-gap: 1rem;
        }

        .br-gap-2 {
            gap: 0.6rem;
        }

        .respo-padding-right-0 {
            padding-right: 0;
        }

        @media(max-width: 991px) {
            .respo-padding-right-0 {
                padding-right: 0;
                padding-left: 0;
            }
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

        .search-box-bar-container.jus-heigh-search-b {
            max-width: 100%;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .search-box-bar-container p i {
            font-size: 16px;
            color: #D0342C;
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

        .brownz-custom-popup .modal-title {
            font-size: 24px;
            font-weight: 500;
            color: #1D1D1D;
            margin: 0;
            padding: 0;
        }

        .brownz-custom-popup .bootbox-close-button.close {
            font-size: 20px;
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
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            opacity: 1;
            margin: 0;
        }

        .brownz-custom-popup .bootbox-close-button.close:not(:disabled):not(.disabled):focus {
            border: none;
            outline: none;
        }

        .modal-content {
            border: none;
            border-radius: 24px;
        }

        .brownz-custom-popup .modal-header {
            border: none;
        }

        .brownz-custom-popup .modal-body {
            border: none;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .brownz-custom-popup .modal-footer {
            border: none;
        }

        .brownz-custom-popup .modal-body label {
            font-size: 14px;
            color: #1D1D1D;
            margin-bottom: 8px;
            font-weight: 400;
        }

        .brownz-custom-popup .modal-body input,.brownz-custom-popup .modal-body select,.brownz-custom-popup .modal-body .select2-container--default{
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
        .brownz-custom-popup textarea{
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
        .brownz-changes-rebove-border .select2-selection{
            border: none !important;
            outline: none !important;
            background-color: transparent !important;
        }
        .brownz-changes-rebove-border .select2-selection__choice{
            border: none !important;
            outline: none;
            background-color: rgba(82, 168, 217, 0.2) !important;
            color: #52A8D9 ;
        }
        .brownz-changes-rebove-border .select2-selection__choice .select2-selection__choice__remove{
            color: #959596 !important;
        }
        .brownz-changes-rebove-border .select2-container--default{
            padding: 4px 20px !important;
        }
        .brownz-custom-popup
        .brownz-custom-popup .modal-body .select2-container--default{
            padding: 8px 20px;
        }
        .brownz-custom-popup .modal-body .select2-container--default .select2-selection--single{
            border: none;
            outline: none;
            background-color: transparent;
        }
        .brownz-custom-popup .modal-body .select2-container--default .select2-selection--single .select2-selection__rendered{
            padding: 0;
        }
        .brownz-custom-popup .modal-body .select2-container--default .select2-selection--single .select2-selection__arrow{
            top: 50%;
            right: 15px;
            transform: translateY(-50%)
        }
        .brownz-custom-popup .input-group input{
            border-radius: 26px !important;
        }
        .brownz-custom-popup .input-group{
            position: relative;
        }
        .brownz-custom-popup .input-group .input-group-append{
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%)
        }
        .brownz-custom-popup .input-group .input-group-append .input-group-text{
            padding: 0;
            background-color: transparent;
            border: none;
        }

        .brownz-btn-yellow-d {
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
            border: none;
            outline: none;
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

        .brownz-btn-yellow-d:active {
            background-color: #1D1D1D !important;
            color: #fff !important;
        }

        .info-sec-tab-cont table tbody tr td {
            border: none;
            padding: 15px 12px;
            font-size: 15px;
            color: #1D1D1D;
            margin: 0;
        }

        .info-sec-tab-cont table tbody tr:nth-child(odd) {
            background-color: rgba(230, 205, 127, 0.2);
        }

        .info-sec-tab-cont table tbody tr:nth-child(even) {
            background-color: rgba(255, 234, 169, 0.2);
        }

        .badge-danger {
            font-size: 15px;
            font-weight: 500;
            color: #19a7d8;
            background-color: #add1db;
            padding: 10px 14px;
            width: max-content;
            border-radius: 8px;
            margin: 0;
            border: none;
        }
        .btn-danger{
            font-size: 15px !important;
            font-weight: 500;
            color: #D0342C !important;
            background-color: rgba(208, 52, 44, 0.2) !important;
            padding: 10px 14px !important;
            width: max-content;
            border-radius: 8px;
            margin: 0;
            border: none !important;
        }
        .btn-success{
            font-size: 15px !important;
            font-weight: 500;
            color: #199900 !important;
            background-color: rgba(25, 153, 0, 0.2) !important;
            padding: 10px 14px !important;
            width: max-content;
            border-radius: 8px;
            margin: 0;
            border: none !important;
        }
        .btn-success:hover{
            color: #fff !important;
        }

        .brw-form-la label {
            font-size: 14px;
            color: #1D1D1D;
            margin-bottom: 8px;
            font-weight: 400;
        }

        .brw-form-la input,.brw-form-la select,.brw-form-la textarea{
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

        .brw-form-la input:focus {
            background-color: #F0F0F3 !important;
        }
        .brw-form-la select:focus {
            background-color: #F0F0F3 !important;
        }
        .brw-form-la textarea:focus {
            background-color: #F0F0F3 !important;
        }
        .brw-form-la .select2-container--default{
            background-color: #F0F0F3;
            color: #1D1D1D;
            font-size: 14px;
            font-weight: 400;
            padding: 7px 20px;
            border-radius: 26px;
            max-width: 100%;
            width: 100%;
            border: none;
            outline: none;
            height: auto;
        }
        .brw-form-la .select2-container--default .select2-selection__arrow{
            top: 8px;
            right: 13px;
        }
        .brw-form-la .select2-container--default .select2-selection--single{
            border: none;
            outline: none;
            background-color: transparent;
        }
        .brownz-permission-container h3{
          color: #1D1D1D;
        }

        .brw-checkbox-browz-dds{
          display: block;
          position: relative;
          padding-left: 35px;
          margin-bottom: 12px;
          cursor: pointer;
          font-size: 18px;
          -webkit-user-select: none;
          -moz-user-select: none;
          -ms-user-select: none;
          user-select: none;
          color: #1D1D1D;
        }
        .brw-checkbox-browz-dds .brow-checkbox-dd{
          position: absolute;
          opacity: 0;
          cursor: pointer;
          height: 0;
          width: 0;
        }
        .brw-checkbox-browz-dds .brow-checkmark{
          position: absolute;
          top: 0;
          left: 0;
          height: 25px;
          width: 25px;
          background-color: #F0F0F3 !important;
          border: 1px solid #F0F0F3;
          border-radius: 5px;
        }
        .brw-checkbox-browz-dds .brow-checkmark.untick-white{
            background-color: #fff !important;
            border: 1px solid #fff;
        }
        .brw-checkbox-browz-dds .brow-checkbox-dd:checked ~ .brow-checkmark {
          background-color: #E6CD7F  !important ;
          border-color: #E6CD7F;
          border-radius: 5px;
        }
        .brow-checkmark:after {
            content: "";
            position: absolute;
            display: none;
          }

          /* Show the checkmark when checked */
          .brw-checkbox-browz-dds .brow-checkbox-dd:checked ~ .brow-checkmark:after {
            display: block;
          }

          /* Style the checkmark/indicator */
          .brw-checkbox-browz-dds .brow-checkmark:after {
            left: 9px;
            top: 5px;
            width: 5px;
            height: 10px;
            border: solid #fff ;
            border-width: 0 2.99px 2.99px 0;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
          }

          .badge-info{
            font-size: 15px;
            font-weight: 500;
            color: #52A8D9 !important;
            background-color: rgba(82, 168, 217, 0.2) ;
            padding: 10px 14px;
            width: max-content;
            border-radius: 8px;
            margin: 0;
          }
          .badge-success {
            font-size: 15px;
            font-weight: 500;
            color: #199900 !important;
            background-color: rgba(25, 153, 0, 0.2) ;
            padding: 10px 14px;
            width: max-content;
            border-radius: 8px;
            margin: 0;
        }
        .btn-success:hover{
            color: #fff !important;
        }
        .btn-warning{
            font-size: 15px;
            font-weight: 500;
            color: #ffc107 !important;
            background-color: rgb(255 193 7 / 20%) !important;
            padding: 10px 14px;
            width: max-content;
            border-radius: 8px;
            margin: 0;
            border: none;
        }
        .alert{
            border-radius: 24px;
            box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;
            border: none;
        }
        .alert-success{
            background-color: rgba(25, 153, 0, 0.2) !important;
            color: #199900 !important;
        }
        .badge-warning{
            font-size: 15px;
            font-weight: 500;
            color: #ffc107 !important;
            background-color: rgba(255, 193, 7, 0.2) ;
            padding: 10px 14px;
            width: max-content;
            border-radius: 8px;
            margin: 0;
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
        .edite-serve-appointment .nav-tabs .nav-link{
            background-color: #E6CD7F;
        padding: 10px 16px;
        border-radius: 50px;
        font-family: "Roboto", sans-serif !important;
        min-width: max-content;
        color: #1D1D1D !important;
        font-size: 16px;
        text-decoration: none;
        display: inline-flex
    ;
        align-items: center;
        gap: 5px;
        box-shadow: none;
        border: none;
        outline: none;
        }
        .edite-serve-appointment .nav-tabs .nav-link.active {
            background-color: #1D1D1D;
            padding: 10px 16px;
            border-radius: 50px;
            font-family: "Roboto", sans-serif !important;
            min-width: max-content;
            color: #E6CD7F !important;
            font-size: 16px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: none;
            border: none;
            outline: none;
        }
        .edite-serve-appointment .nav-tabs .nav-link.active:focus{
            background-color: #1D1D1D !important;
            color: #E6CD7F !important;
        }
        .edit-server-b{
            background-color: #F0F0F3;
            border-radius: 24px;
        }
        .edit-serv-beaut .service-item-list-info{
            width: 100%;
            background-color: #fff;
            border-radius: 15px;
            padding: 15px;
            cursor: pointer;
            padding-right: 60px;
            gap: 12px;
            border: none;
        }
        .edit-server-b .custom-selection{
            border: none;
        }
        .edit-server-b .custom-selection .checkmark{
            border-radius: 50%;
            right: 15px;
            top: 50% !important;
            transform: translateY(-50%)
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

    .browz-changes-calender .month-label{
        color: #979696;
        margin: 5px;
        font-size: 18px;
        font-weight: 400;
        word-spacing: 0.5rem;
        text-transform: capitalize;
    }
    .browz-changes-calender .year-label{
        color: #979696;
        margin: 5px;
        font-size: 18px;
        font-weight: 400;
        word-spacing: 0.5rem;
    }
    .browz-changes-calender .week .day.header{
        color: #1D1D1D;
        font-weight: 500;
        font-size: 16px;
    }
    .browz-changes-calender .week .day.selected span{
        background-color: rgba(82, 168, 217, 0.2);
        color: #52A8D9;
    }
    .browz-changes-calender .day.today span::after{
        border-bottom: 2px solid #4dbd74;
    }
    .brownz-time-slot-times-select .slot-custom-radio {
        border: none;
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
    .brownz-time-slot-times-select .custom-selection.slot-custom-radio input:checked~.service-item-list-info p {
        color: #52A8D9;
        background-color: rgba(82, 168, 217, 0.2);
        box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 22px;
    }
    .brownz-time-slot-times-select .custom-selection.slot-custom-radio input:checked~.service-item-list-info {
        background-color: transparent;
    }
    .brownz-close-btn {
        font-size: 14px;
        color: #D0342C !important;
        background-color: rgba(208, 52, 44, 0.2);
        border-radius: 50%;
        display: flex
    ;
        align-items: center;
        justify-content: center;
        border: none;
        width: 25px;
        height: 25px;
        padding: 0;
        font-weight: 100;
        cursor: pointer;
        text-decoration: none;
    }
    .brownz-close-btn:focus {
        color: #D0342C !important;
        background-color: rgba(208, 52, 44, 0.3);
        outline: none;
        border: none;
    }
    .dsds_hdv4 input~.checkmark{
        background-color: #E6CD7F !important;
    }
    .fc-today-button.fc-button.fc-state-default.fc-corner-left.fc-corner-right.fc-state-disabled{
        background-color: rgba(25, 153, 0, 0.2);
        background-image: none;
        color: rgba(25, 153, 0, 1);
        border: none;
        font-size: 16px;
        font-weight: 500;
        border-radius: 40px;
        padding: 10px 16px;
        height: auto;
    }
    .fc-button-group .fc-prev-button.fc-button.fc-state-default.fc-corner-left,.fc-button-group .fc-next-button.fc-button.fc-state-default.fc-corner-right{
        border: none;
        background-color: #fff;
        background-image: none;
        width: 40px;
        height: 40px;
        box-shadow: rgba(17, 17, 26, 0.05) 0px 1px 0px, rgba(17, 17, 26, 0.1) 0px 0px 8px;
        border-radius: 10px;
    }
    .fc-button-group .fc-prev-button.fc-button.fc-state-default.fc-corner-left{
        margin-right: 5px;
    }
    /* .pagination .page-item:first-child .page-link{
        border: 1px solid #1D1D1D;
        background-color: #fff;
    } */
    .page-link{
        background-color: #ffff;
        color: #1D1D1D;
        border: 1px solid #1d1d1d;
    }
    .page-item.active .page-link{
        background-color: #1D1D1D;
        border-color: #1d1d1d;
        color: #fff;
    }
    .page-link:hover {
        z-index: 2;
        color: #E6CD7F;
        text-decoration: none;
        background-color: #fff;
        border-color: #1d1d1d;
    }
    .btn_dark-x {
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .btn_dark-x {
        border: none;
        background-color: #1D1D1D;
        padding: 10px 16px;
        border-radius: 50px;
        font-family: "Roboto", sans-serif !important;
        min-width: max-content;
        color: #fff !important;
        font-size: 16px;
    }
    .btn_dark-x:disabled,.btn_dark-x[disabled]{
        opacity: 0.6
    }

    .btn_dark-x:disabled:hover{
        background-color: #1D1D1D;
        color: #fff !important;
    }

button:focus{
    outline: none !important;
}


    </style>
    <link rel="manifest" href="{{ asset('/images/fav/site.webmanifest') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('admin/images/fav_icon.jpg') }}">
    <link rel="stylesheet" href="{{ asset('components/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/vocomi/vocomi.css') }}">
    <link rel="stylesheet" type="text/css" media="all" href="{{ asset('css/voco-styles.css') }}">
    @if (Session::get('user_session_locale') === 'ar')
        <link rel="stylesheet" type="text/css" media="all" href="{{ asset('css/voco-rtl.css') }}">
    @endif
</head>

<body>

    <div id="mySidebar" class="main_sidebar">
        <div class="sidebar_dashbord-relative">
            <div class="sidebar-header">
                <a class="log-a" href="{{ route('dashboard') }}"><img src="{{ asset('admin/images/logo.png') }}"></a>
            </div>
            @include('layouts.theme.vocomi.default.partials.navigation-menu-admin1')
        </div>
    </div>

    <main id="main" class="main">
        <div class="position-relative pt-3 mb-5">
            <div class="top-bar-f">
                <button class="toggle-btn my-custom" onclick="toggleNav()">
                    <div class="nav-close-and-btn">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                    </div>
                </button>
                <div class="dropdown top-bar-dropdown">
                    <button class="btn dropdown-toggle" type="button" onclick="toggleDropdown()">
                        {{ auth()->user()->name }}
                    </button>
                    <ul class="dropdown-menu" id="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.change-password') }}"><i
                                    class="fa fa-unlock-alt"></i> Change Password</a></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('Logout-user') }}">
                                <i class="fa fa-sign-out"></i> Log Out
                            </a>
                        </li>
                    </ul>
                    <form id="logout-form" action="{{ route('Logout-user') }}" method="post" style="display: none">
                        @csrf
                    </form>
                </div>

            </div>
        </div>
        <div id="loader_frame"
            style="
            position: fixed;
            z-index: 9999;
            background-color: rgb(0 0 0 / 60%);
            width: 100%;

          ">
            <div id="loader"></div>
        </div>

        <div>
            <div class="animated fadeIn">
                {!! voco_flash()->getAsString() !!}

                <div class="flash-data"></div>

                @yield('content')
            </div>
        </div>

    </main>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    @include('layouts.theme.vocomi.default.partials.select2-helpers')
    @stack('after-jquery')
    <script src="{{ asset('admin/js/side-navbar.js') }}"></script>
    <script src="{{ asset('/js/ats-vue.js') }}"></script>
    <script src="{{ asset('/js/script.pack.js') }}"></script>
    <script src="{{ asset('/js/ats-sp.pack.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
