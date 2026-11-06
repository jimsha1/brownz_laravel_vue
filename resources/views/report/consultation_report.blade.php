@extends('layouts.vcapp')

<style>
    body {
        font-family: Arial, sans-serif;
        background: #f5f5f5;
    }

    .page {
        width: 210mm;
        /* A4 width */
        min-height: 297mm;
        /* A4 height */
        margin: 12mm auto;
        padding: 12mm;
        background: #fff;
        box-shadow: 0 0 0.5rem rgba(0, 0, 0, .15);
    }

    .toolbar {
        position: sticky;
        top: 0;
        background: #fff;
        padding: 8px 0;
        z-index: 999;
    }

    @page {
        size: A4;
        margin: 12mm;
    }

    @media print {
        body {
            margin: 0;
        }

        .toolbar {
            display: none !important;
        }

        .page {
            box-shadow: none;
            margin: 0;
            width: auto;
            min-height: auto;
            padding: 0;
        }
    }

    input,
    textarea,
    select {
        border: none;
        border-bottom: 1px solid #444;
        padding: 2px 0;
        outline: 0;
        background: transparent;
    }

    label {
        font-weight: 500;
    }

    .new-form-table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }

    .new-form-table th,
    .new-form-table td {
        border: 0px solid transparent;
        padding: 4px 6px;
        font-size: 14px;
        vertical-align: middle;
    }

    .new-form-table th {
        background: transparent;
        text-align: left;
        padding-left: 0;
        /* width: 180px; */
    }

    .brw-checkbox-browz-dds {
        display: inline-block;
        margin-right: 10px;
        cursor: pointer;
    }

    .brow-checkbox-dd {
        margin-right: 5px;
    }

    .cleint-info-container label {
        font-size: 12px;
        width: 90px;
        display: flex;
        justify-content: space-between;
    }

    .cleint-info-container h6 {
        margin: 0;
        padding-left: 10px;
        font-size: 12px;
    }

    .sub-head-p {
        font-size: 14px;
        color: #1d1d1d;
        margin-bottom: 0;
        font-weight: 600;
    }



    /* check box */
    /* The container */
    .check-le {
        display: block;
        position: relative;
        padding-left: 21px;
        margin-bottom: 0px;
        cursor: pointer;
        font-size: 12px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* Hide the browser's default checkbox */
    .check-le input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    /* Create a custom checkbox */
    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 16px;
        width: 16px;
        background-color: #eee;
        border-radius: 4px;
    }

    /* On mouse-over, add a grey background color */
    .check-le:hover input~.checkmark {
        background-color: #ccc;
    }

    /* When the checkbox is checked, add a blue background */
    .check-le input:checked~.checkmark {
        background-color: #E6CD7F;
    }

    /* Create the checkmark/indicator (hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the checkmark when checked */
    .check-le input:checked~.checkmark:after {
        display: block;
    }

    /* Style the checkmark/indicator */
    .check-le .checkmark:after {
        left: 6px;
        top: 2px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    .new-form-table th {
        font-size: 12px;
        font-weight: 400;
    }

    .new-form-table td {
        color: #444;
    }

    .new-form-table tr {
        border-color: #e2e2e244;
    }

    @media print {
        .check-le .checkmark {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }

    hr {
        border-color: #939292;
    }


    .check-le {
        display: flex;
        align-items: center;
        gap: 3px;
        /* small space between box and text */
    }

    .new-form-table tr:last-child {
        border-color: #e7e7e7;
    }

    .iny-s {
        margin: 0;
        font-size: 12px;
        color: #1d1d1d;
    }

    .listing-ddd li {
        font-size: 12px;
    }

    .sign-container b {
        font-size: 12px;
    }

    .sign-container p {
        font-size: 12px;
        margin-bottom: 5px;
    }

    .sign-container {
        object-fit: contain;
    }

    header {
        background-color: #000;
        padding: 12px;
        margin-bottom: 15px;
    }

    @media print {
        header {
            background-color: #1d1d1d !important;
            -webkit-print-color-adjust: exact;
            /* For Chrome, Safari */
            print-color-adjust: exact;
            /* For Firefox */
            color: #fff !important;
            /* make text white */
        }
    }

    header img {
        width: 80%;
        object-fit: contain;
    }

    header p {
        margin: 0;
        color: #fff;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 5px;
    }

    header p i {
        font-size: 12px;
        color: #1d1d1d;
        width: 18px;
        height: 18px;
        display: flex !important;
        justify-content: center;
        align-items: center;
        background-color: #E6CD7F;
        border-radius: 50%;

    }

    .listing-ddd {
        line-height: 1.9;
    }


    @media print {
        footer .yellow {
            background-color: #E6CD7F;
            padding: 10px;
            -webkit-print-color-adjust: exact;
            /* For Chrome, Safari */
            print-color-adjust: exact;
            /* For Firefox */
        }

        footer .bottom {
            background-color: #1d1d1d;
            padding: 20px;
            -webkit-print-color-adjust: exact;
            /* For Chrome, Safari */
            print-color-adjust: exact;
            /* For Firefox */
        }

        footer {
            position: relative;
            width: 100%;
            margin-top: 100%;
            /* spacing from content */
            /* forces footer to start on a new page */
        }

        footer .yellow {
            background: yellow;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        footer .bottom {
            background: #000;
            height: 10px;
        }
    }

    @media print {

        body,
        html {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .print-wrapper {
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1;
            /* takes all remaining space */
        }

        footer {
            margin-top: 50%;
            /* pushes footer to bottom of last page */
            width: 100%;
        }

        footer .yellow {
            background: #E6CD7F;
            text-align: center;
            padding: 5px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        footer .bottom {
            background: #1d1d1d;
            height: 30px;
        }
    }

    .print-btn {
        border: none;
        background-color: #1D1D1D;
        padding: 10px 16px;
        border-radius: 50px;
        font-family: "Roboto", sans-serif !important;
        min-width: max-content;
        color: #fff !important;
        font-size: 16px;
        transition: 0.2s;
    }

    .print-btn:hover {
        text-decoration: none;
        background-color: #E6CD7F;
        color: #1D1D1D !important;
        transition: 0.2s;
    }

    .brownz-custom-popup .modal-body input,
    .brownz-custom-popup .modal-body select,
    .brownz-custom-popup .modal-body .select2-container--default {
        width: 0 !important;
    }

    /* hair consultation */
    body {
        font-family: Arial, sans-serif;
        background: #f5f5f5;
    }

    .page {
        width: 210mm;
        /* A4 width */
        min-height: 297mm;
        /* A4 height */
        margin: 12mm auto;
        padding: 12mm;
        background: #fff;
        box-shadow: 0 0 0.5rem rgba(0, 0, 0, .15);
    }

    .toolbar {
        position: sticky;
        top: 0;
        background: #fff;
        padding: 8px 0;
        z-index: 999;
    }

    @page {
        size: A4;
        margin: 12mm;
    }

    @media print {
        body {
            margin: 0;
        }

        .toolbar {
            display: none !important;
        }

        .page {
            box-shadow: none;
            margin: 0;
            width: auto;
            min-height: auto;
            padding: 0;
        }
    }

    input,
    textarea,
    select {
        border: none;
        border-bottom: 1px solid #444;
        padding: 2px 0;
        outline: 0;
        background: transparent;
    }

    label {
        font-weight: 500;
    }

    .new-form-table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }

    .new-form-table th,
    .new-form-table td {
        border: 0px solid transparent;
        padding: 4px 6px;
        font-size: 14px;
        vertical-align: middle;
    }

    .new-form-table th {
        background: transparent;
        text-align: left;
        padding-left: 0;
        /* width: 180px; */
    }

    .brw-checkbox-browz-dds {
        display: inline-block;
        margin-right: 10px;
        cursor: pointer;
    }

    .brow-checkbox-dd {
        margin-right: 5px;
    }

    .cleint-info-container label {
        font-size: 12px;
        width: 90px;
        display: flex;
        justify-content: space-between;
    }

    .cleint-info-container h6 {
        margin: 0;
        padding-left: 10px;
        font-size: 12px;
    }

    .sub-head-p {
        font-size: 14px;
        color: #1d1d1d;
        margin-bottom: 0;
        font-weight: 600;
    }



    /* check box */
    /* The container */
    .check-le {
        display: block;
        position: relative;
        padding-left: 21px;
        margin-bottom: 0px;
        cursor: pointer;
        font-size: 12px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* Hide the browser's default checkbox */
    .check-le input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    /* Create a custom checkbox */
    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 16px;
        width: 16px;
        background-color: #eee;
        border-radius: 4px;
    }

    /* On mouse-over, add a grey background color */
    .check-le:hover input~.checkmark {
        background-color: #ccc;
    }

    /* When the checkbox is checked, add a blue background */
    .check-le input:checked~.checkmark {
        background-color: #E6CD7F;
    }

    /* Create the checkmark/indicator (hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the checkmark when checked */
    .check-le input:checked~.checkmark:after {
        display: block;
    }

    /* Style the checkmark/indicator */
    .check-le .checkmark:after {
        left: 6px;
        top: 2px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    .new-form-table th {
        font-size: 12px;
        font-weight: 400;
    }

    .new-form-table td {
        color: #444;
    }

    .new-form-table tr {
        border-color: #e2e2e244;
    }

    @media print {
        .check-le .checkmark {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }

    hr {
        border-color: #939292;
    }


    .check-le {
        display: flex;
        align-items: center;
        gap: 3px;
        /* small space between box and text */
    }

    .new-form-table tr:last-child {
        border-color: #e7e7e7;
    }

    .iny-s {
        margin: 0;
        font-size: 12px;
        color: #1d1d1d;
    }

    .listing-ddd li {
        font-size: 12px;
    }

    .sign-container b {
        font-size: 12px;
    }

    .sign-container p {
        font-size: 12px;
        margin-bottom: 5px;
    }

    .sign-container {
        object-fit: contain;
    }

    header {
        background-color: #000;
        padding: 12px;
        margin-bottom: 15px;
    }

    @media print {
        header {
            background-color: #1d1d1d !important;
            -webkit-print-color-adjust: exact;
            /* For Chrome, Safari */
            print-color-adjust: exact;
            /* For Firefox */
            color: #fff !important;
            /* make text white */
        }
    }

    header img {
        width: 80%;
        object-fit: contain;
    }

    header p {
        margin: 0;
        color: #fff;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 5px;
    }

    header p i {
        font-size: 12px;
        color: #1d1d1d;
        width: 18px;
        height: 18px;
        display: flex !important;
        justify-content: center;
        align-items: center;
        background-color: #E6CD7F;
        border-radius: 50%;

    }

    .listing-ddd {
        line-height: 1.9;
    }


    @media print {
        footer .yellow {
            background-color: #E6CD7F;
            padding: 10px;
            -webkit-print-color-adjust: exact;
            /* For Chrome, Safari */
            print-color-adjust: exact;
            /* For Firefox */
        }

        footer .bottom {
            background-color: #1d1d1d;
            padding: 20px;
            -webkit-print-color-adjust: exact;
            /* For Chrome, Safari */
            print-color-adjust: exact;
            /* For Firefox */
        }

        footer {
            position: relative;
            width: 100%;
            margin-top: 100%;
            /* spacing from content */
            /* forces footer to start on a new page */
        }

        footer .yellow {
            background: yellow;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        footer .bottom {
            background: #000;
            height: 10px;
        }
    }

    @media print {

        body,
        html {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .print-wrapper {
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1;
            /* takes all remaining space */
        }

        footer {
            margin-top: 50%;
            /* pushes footer to bottom of last page */
            width: 100%;
        }

        footer .yellow {
            background: #E6CD7F;
            text-align: center;
            padding: 5px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        footer .bottom {
            background: #1d1d1d;
            height: 30px;
        }
    }

    .print-btn {
        border: none;
        background-color: #1D1D1D;
        padding: 10px 16px;
        border-radius: 50px;
        font-family: "Roboto", sans-serif !important;
        min-width: max-content;
        color: #fff !important;
        font-size: 16px;
        transition: 0.2s;
    }

    .print-btn:hover {
        text-decoration: none;
        background-color: #E6CD7F;
        color: #1D1D1D !important;
        transition: 0.2s;
    }

    .brownz-custom-popup .modal-body input {
        width: 100% !important;
    }
</style>

</style>

<style>
  .consultation-report-filters .filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  .consultation-report-filters .filter-control {
    height: 38px;
    border: none;
    outline: none;
    border-radius: 999px;
    background: #F0F0F3;
    padding: 8px 14px;
    font-size: 14px;
    color: #1D1D1D;
    width: 100%;
  }

  .consultation-report-filters select.filter-control {
    appearance: auto;
  }

  .consultation-report-filters .filter-actions {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    gap: 8px;
    min-height: 38px;
  }

  .consultation-report-filters .filter-actions .btn {
    white-space: nowrap;
    flex: 0 0 auto;
  }

  .consultation-report-filters .filter-row + .filter-row {
    margin-top: 12px;
  }

  @media (min-width: 992px) {
    .consultation-report-filters .filter-actions {
      justify-content: flex-start;
    }
  }

  .consultation-report-list .list-toolbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 14px;
  }

  .consultation-report-list .list-title {
    color: #1D1D1D;
    font-size: 1.53125rem;
    font-weight: 500;
    margin: 0;
  }

  .consultation-report-list .list-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .consultation-report-list .client-name {
    display: block;
    font-weight: 600;
    color: #1D1D1D;
  }

  .consultation-report-list .client-meta {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
  }

  .consultation-report-list .type-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
    text-transform: capitalize;
  }

  .consultation-report-list .type-pill.is-skin {
    color: #9A6700;
    background: rgba(230, 205, 127, 0.35);
  }

  .consultation-report-list .type-pill.is-hair {
    color: #1D4ED8;
    background: rgba(59, 130, 246, 0.12);
  }

  .consultation-report-list .status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 6px 12px;
    line-height: 1.2;
  }

  .consultation-report-list .status-pill.is-approved {
    color: #199900;
    background: rgba(25, 153, 0, 0.15);
  }

  .consultation-report-list .status-pill.is-pending {
    color: #D0342C;
    background: rgba(208, 52, 44, 0.12);
  }

  .consultation-report-list .manage-actions {
    display: inline-flex;
    gap: 6px;
    justify-content: center;
    align-items: center;
    flex-wrap: nowrap;
    white-space: nowrap;
  }

  .consultation-report-list td.action-cell {
    white-space: nowrap;
    width: 132px;
    min-width: 132px;
  }

  .consultation-report-list .manage-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    flex: 0 0 32px;
    border-radius: 50%;
    background: #F0F0F3;
    color: #1D1D1D;
    border: none;
    cursor: pointer;
  }

  .consultation-report-list .manage-btn:hover {
    background: #1D1D1D;
    color: #E6CD7F;
  }

  .consultation-report-list .manage-btn.is-approve {
    background: rgba(25, 153, 0, 0.12);
    color: #199900;
  }

  .consultation-report-list .manage-btn.is-approve:hover {
    background: #199900;
    color: #fff;
  }

  .consultation-report-list .empty-state,
  .consultation-report-list .loading-state {
    text-align: center;
    padding: 36px 16px;
    background: #F7F7F9;
    border-radius: 16px;
    color: #6c757d;
  }

  /* Fit skin/hair consultation modals to viewport */
  .v--modal-overlay .v--modal-box.brownz-custom-popup {
    width: min(900px, calc(100vw - 1.5rem)) !important;
    max-width: calc(100vw - 1.5rem) !important;
    max-height: calc(100vh - 1.5rem) !important;
    height: auto !important;
    overflow: hidden !important;
    display: flex !important;
    flex-direction: column !important;
    padding: 0 !important;
    margin: 0.75rem auto !important;
  }

  .brownz-custom-popup > div,
  .brownz-custom-popup .modal-content {
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 1.5rem);
    overflow: hidden;
    border-radius: 16px;
  }

  .brownz-custom-popup .modal-header {
    flex-shrink: 0;
    border: 0;
    padding: 16px 18px 10px;
    align-items: flex-start;
    background: #fff;
  }

  .brownz-custom-popup .modal-header .modal-title {
    color: #1D1D1D;
    font-weight: 600;
    font-size: 1.1rem;
    margin: 0;
    line-height: 1.3;
  }

  .brownz-custom-popup .modal-header-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin-top: 6px;
  }

  .brownz-custom-popup .modal-mode-label {
    font-size: 12px;
    color: #6c757d;
    font-weight: 500;
  }

  .brownz-custom-popup .modal-type-badge,
  .brownz-custom-popup .modal-status-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.2;
  }

  .brownz-custom-popup .modal-type-badge.is-skin {
    background: rgba(111, 66, 193, 0.12);
    color: #6f42c1;
  }

  .brownz-custom-popup .modal-type-badge.is-hair {
    background: rgba(230, 205, 127, 0.45);
    color: #9A6700;
  }

  .brownz-custom-popup .modal-status-badge.is-view {
    background: #F0F0F3;
    color: #5c5c5c;
  }

  .brownz-custom-popup .modal-status-badge.is-approve {
    background: rgba(40, 167, 69, 0.12);
    color: #1e7e34;
  }

  .brownz-custom-popup .modal-header .close {
    padding: 0;
    margin: 0 0 0 auto;
    font-size: 1.5rem;
    font-weight: 400;
    line-height: 1;
    color: #6c757d;
    opacity: 1;
    align-self: flex-start;
  }

  .brownz-custom-popup .modal-header .close:hover {
    color: #1D1D1D;
  }

  .brownz-custom-popup .modal-footer {
    flex-shrink: 0;
    border: 0;
    border-top: 1px solid #EEE;
    padding: 12px 18px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    background: #fff;
  }

  .brownz-custom-popup .modal-footer .btn-approve {
    min-width: 110px;
    border-radius: 999px;
    font-weight: 600;
    padding: 8px 18px;
    background: #1D1D1D;
    border-color: #1D1D1D;
    color: #fff;
  }

  .brownz-custom-popup .modal-footer .btn-approve:hover:not(:disabled) {
    background: #333;
    border-color: #333;
    color: #fff;
  }

  .brownz-custom-popup .modal-footer .btn-approve:disabled {
    opacity: 0.65;
    cursor: not-allowed;
  }

  .brownz-custom-popup .modal-body {
    flex: 1 1 auto !important;
    min-height: 0 !important;
    max-height: none !important;
    overflow-y: auto !important;
    -webkit-overflow-scrolling: touch;
    background: #F7F7F9;
  }

  .brownz-custom-popup .page {
    width: 100% !important;
    min-height: auto !important;
    margin: 0 auto !important;
    box-shadow: none !important;
    padding: 16px !important;
    background: #fff;
    border-radius: 12px;
  }

  @media (max-width: 575px) {
    .v--modal-overlay .v--modal-box.brownz-custom-popup {
      width: calc(100vw - 0.75rem) !important;
      max-width: calc(100vw - 0.75rem) !important;
      max-height: calc(100vh - 0.75rem) !important;
      margin: 0.375rem auto !important;
    }

    .brownz-custom-popup .modal-header,
    .brownz-custom-popup .modal-footer {
      padding-left: 14px;
      padding-right: 14px;
    }
  }
</style>

@section('content')
    <div id="app">
        <div class="fbox-shadow mt-2 fd-card respo-margin-top consultation-report-filters">
            <div class="card-body">
                <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
                    {{ __('Consultation Report') }}
                </h5>

                <div class="row align-items-end br-row-gap-2 filter-row">
                    <div class="col-lg-3 col-md-6">
                        <label class="filter-label" for="filter_start_date">{{ __('Start Date') }}</label>
                        <input type="date" id="filter_start_date" class="form-control filter-control"
                            v-model="filter_start_date">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="filter-label" for="filter_end_date">{{ __('End Date') }}</label>
                        <input type="date" id="filter_end_date" class="form-control filter-control"
                            v-model="filter_end_date">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="filter-label" for="filter_client_name">{{ __('Client Name') }}</label>
                        <input type="text" id="filter_client_name" class="form-control filter-control"
                            v-model="filter_client_name"
                            @keyup.enter="getConsultationList(1)"
                            placeholder="{{ __('Search by name...') }}">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="filter-label" for="filter_client_mobile">{{ __('Client Mobile') }}</label>
                        <input type="text" id="filter_client_mobile" class="form-control filter-control"
                            v-model="filter_client_mobile"
                            @keyup.enter="getConsultationList(1)"
                            placeholder="{{ __('Search by mobile...') }}">
                    </div>
                </div>

                <div class="row align-items-end br-row-gap-2 filter-row">
                    <div class="col-lg-3 col-md-6">
                        <label class="filter-label" for="filter_type">{{ __('Type') }}</label>
                        <select id="filter_type" class="form-control filter-control" v-model="filter_type">
                            <option value="">{{ __('All Types') }}</option>
                            <option value="skin">{{ __('Skin') }}</option>
                            <option value="hair">{{ __('Hair') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="filter-label" for="filter_status">{{ __('Status') }}</label>
                        <select id="filter_status" class="form-control filter-control" v-model="filter_status">
                            <option value="">{{ __('All') }}</option>
                            <option value="approved">{{ __('Approved') }}</option>
                            <option value="pending">{{ __('Pending') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <label class="filter-label d-none d-lg-block">&nbsp;</label>
                        <div class="filter-actions">
                            <button type="button" class="btn btn-sm btn-primary btn_dark-x" @click="getConsultationList(1)">
                                <i class="fa fa-search"></i> {{ __('Search') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d" @click="resetFilter()">
                                <i class="fa fa-refresh"></i> {{ __('Reset') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fbox-shadow mt-2 fd-card consultation-report-list">
            <div class="card-body">
                <div class="list-toolbar">
                    <div>
                        <h5 class="list-title">{{ __('Consultation List') }}</h5>
                        <span class="list-meta" v-if="!loading && pagination.total">
                            {{ __('Showing') }} @{{ pagination.from || 0 }}–@{{ pagination.to || 0 }}
                            {{ __('of') }} @{{ pagination.total }}
                        </span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="exportToPDF" :disabled="loading">
                            <i class="fa fa-file-pdf-o"></i> {{ __('PDF') }}
                        </button>
                    </div>
                </div>

                <div v-if="loading" class="loading-state">
                    <div><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>
                    <div>{{ __('Loading consultations...') }}</div>
                </div>

                <template v-else-if="ConsultationList.length">
                    <div class="table-responsive">
                        <table class="table brownz-table-list-v1 m-0">
                            <thead>
                                <tr>
                                    <th style="width: 56px;">{{ __('SL#') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Client') }}</th>
                                    <th>{{ __('Beautician') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-center action-cell">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, index) in ConsultationList" :key="data.id || index">
                                    <td>@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</td>
                                    <td>@{{ formatDate(data.date) }}</td>
                                    <td>
                                        <span class="type-pill" :class="data.type === 'hair' ? 'is-hair' : 'is-skin'">
                                            @{{ data.type || '—' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="client-name">@{{ data.customer_name || '—' }}</span>
                                        <span class="client-meta" v-if="data.customer_mobile">@{{ data.customer_mobile }}</span>
                                    </td>
                                    <td>@{{ data.beautician_name || '—' }}</td>
                                    <td>
                                        <span class="status-pill"
                                            :class="data.approve_status === 'approved' ? 'is-approved' : 'is-pending'">
                                            <span v-if="data.approve_status === 'approved'">{{ __('Approved') }}</span>
                                            <span v-else>{{ __('Pending') }}</span>
                                        </span>
                                    </td>
                                    <td class="text-center action-cell">
                                        <div class="manage-actions">
                                            <button type="button"
                                                v-if="data.approve_status != 'approved'"
                                                class="manage-btn is-approve"
                                                title="{{ __('Approve') }}"
                                                @click="editConsultation(data)">
                                                <i class="fa fa-check" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="manage-btn" title="{{ __('View') }}"
                                                @click="viewConsultation(data)">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>
                                            <button type="button"
                                                v-if="data.type === 'skin'"
                                                class="manage-btn"
                                                title="{{ __('Print') }}"
                                                @click="printSkinConsultation(data)">
                                                <i class="fa fa-print" aria-hidden="true"></i>
                                            </button>
                                            <button type="button"
                                                v-if="data.type === 'hair'"
                                                class="manage-btn"
                                                title="{{ __('Print') }}"
                                                @click="printHairConsultation(data)">
                                                <i class="fa fa-print" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <nav aria-label="pagination" class="mt-3" v-if="pagination.last_page > 1">
                        <ul class="pagination justify-content-end mb-0">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">«</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page"
                                :class="{ active: page == isActived }">
                                <a class="page-link" href="#" @click.prevent="changePage(page)">@{{ page }}</a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">»</a>
                            </li>
                        </ul>
                    </nav>
                </template>

                <div v-else class="empty-state">
                    <div><i class="fa fa-file-text-o" aria-hidden="true"></i></div>
                    <div><strong>{{ __('No consultations found') }}</strong></div>
                    <div>{{ __('Try adjusting date, client, mobile, type, or status filters.') }}</div>
                </div>
            </div>

            <!-- Skin Form Modal (View) -->
            <modal name="skinModal" width="900" height="auto" :scrollable="true" :adaptive="true"
                class="brownz-custom-popup m-0">
                <div class="">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div>
                                <h4 class="modal-title">{{ __('Skin Consultation') }}</h4>
                                <div class="modal-header-meta">
                                    <span class="modal-status-badge is-view">{{ __('View') }}</span>
                                    <span class="modal-type-badge is-skin">{{ __('Skin') }}</span>
                                </div>
                            </div>
                            <button type="button" class="bootbox-close-button close" @click="$modal.hide('skinModal')"
                                aria-label="Close">×</button>
                        </div>
                        <div class="modal-body p-3">
                            <div class="page">
                                <header>
                                    <div class="row align-items-center">
                                        <div class="col-4">
                                            <img src="{{ asset('admin/images/logo.png?id=4') }}" alt="logo">

                                        </div>
                                        <div class="col-8">
                                            <div class="row justify-content-end">
                                                <div class="col-4">
                                                    <p><i class="fa fa-phone" aria-hidden="true"></i> +91 9645 333 949</p>
                                                    <p><i class="fa fa-phone" aria-hidden="true"></i> +91 9745 333 949</p>
                                                </div>
                                                <div class="col-6">
                                                    <p><i class="fa fa-map-marker" aria-hidden="true"></i> Manjeri |
                                                        Perinthalmanna</p>
                                                    <p><i class="fa fa-envelope-o" aria-hidden="true"></i>
                                                        brownzbeautysalon@gmail.com</p>
                                                </div>
                                                <div class="col-4">
                                                    <p class="mb-0"><i class="fa fa-instagram" aria-hidden="true"></i>
                                                        brownzbeautysalon</p>
                                                </div>
                                                <div class="col-6"></div>
                                            </div>
                                        </div>
                                    </div>
                                </header>
                                <h4 class="text-center mb-4 mt-4" style="font-size: 18px;">CONSULTATION FORM FOR SKIN</h4>
                                <form method="POST" action="#">
                                    <!-- Client Info -->
                                    <div class="row mb-2 g-2 cleint-info-container">
                                        <div class="col-6 d-flex align-items-center">
                                            <label for="client_name">Client Name <span>:</span></label>
                                            <h6>@{{ selectedConsultation.customer_name || 'N/A' }}</h6>
                                        </div>
                                        <div class="col-3 d-flex align-items-center">
                                            <label for="age">Age <span>:</span></label>
                                            <h6>@{{ selectedConsultation.customer_age || 'N/A' }}</h6>
                                        </div>
                                        <div class="col-3 d-flex align-items-center">
                                            <label for="date">Date <span>:</span></label>
                                            <h6>@{{ selectedConsultation.date || 'N/A' }}</h6>
                                        </div>
                                        <div class="col-6 d-flex align-items-center">
                                            <label for="phone1">Phone 1 <span>:</span></label>
                                            <h6>@{{ selectedConsultation.customer_phone || 'N/A' }}</h6>
                                        </div>
                                        <div class="col-6 d-flex align-items-center">
                                            <label for="phone2">Phone 2 <span>:</span></label>
                                            <h6>@{{ selectedConsultation.customer_mobile || 'N/A' }}</h6>
                                        </div>
                                        <div class="col-6 d-flex align-items-center">
                                            <label for="stylist_name">Stylist Name <span>:</span></label>
                                            <h6>@{{ selectedConsultation.beautician_name || 'N/A' }}</h6>
                                        </div>
                                        <div class="col-6 d-flex align-items-center">
                                            <label for="designation">Designation <span>:</span></label>
                                            <h6>@{{ selectedConsultation.beautician_designation || 'N/A' }}</h6>
                                        </div>
                                    </div>
                                    <hr class="mt-0 mb-1">
                                    <h5 class="sub-head-p mt-5">Skin Analysis</h5>
                                    <div class="table-responsive" style="overflow: hidden">
                                        <table class="table table-bordered new-form-table mt-2">
                                            <!-- Skin Type -->
                                            <tr>
                                                <th>Skin Type</th>
                                                <td v-for="option in ['Normal', 'Dry', 'Oily', 'Combination']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox" :checked="isChecked('skin_type', option)"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <!-- Skin Concern -->
                                            <tr>
                                                <th>Skin Concern</th>
                                                <td
                                                    v-for="option in ['Acne', 'Pigmentation', 'Dullness', 'Wrinkles', 'Pore', 'Other']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox" :checked="isChecked('skin_concern', option)"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <!-- Skin Allergy -->
                                            <tr>
                                                <th>Skin Allergy</th>
                                                <td v-for="option in ['Yes', 'No']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox" :checked="isChecked('skin_allergy', option)"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <!-- Skin Sensitivity -->
                                            <tr>
                                                <th>Skin Sensitivity</th>
                                                <td v-for="option in ['Yes', 'No']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox"
                                                            :checked="isChecked('skin_sensitivity', option)" disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <!-- Skin Routine -->
                                            <tr>
                                                <th>Skin Routine</th>
                                                <td
                                                    v-for="option in ['Cleanser', 'Moisturiser', 'Sunscreen', 'Serum', 'Toner', 'Night Care']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox" :checked="isChecked('skin_routine', option)"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <h5 class="sub-head-p mt-3">Lifestyle</h5>
                                    <div class="table-responsive" style="overflow: hidden">
                                        <table class="table table-bordered new-form-table mt-2">
                                            <!-- Water Intakes -->
                                            <tr>
                                                <th>Water Intakes</th>
                                                <td v-for="option in ['Low', 'Medium', 'High']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox"
                                                            :checked="isChecked('water_intakes', option)" disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <!-- Sleep -->
                                            <tr>
                                                <th>Sleep</th>
                                                <td v-for="option in ['Good', 'Moderate', 'Poor']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox" :checked="isChecked('sleep', option)"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <!-- Stress -->
                                            <tr>
                                                <th>Stress</th>
                                                <td v-for="option in ['Low', 'Moderate', 'High']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox" :checked="isChecked('stress', option)"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <!-- Sun Exposure -->
                                            <tr>
                                                <th>Sun Exposure</th>
                                                <td v-for="option in ['Yes', 'No']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox"
                                                            :checked="isChecked('sun_exposure', option)" disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <!-- Smoke -->
                                            <tr>
                                                <th>Smoke</th>
                                                <td v-for="option in ['Yes', 'No']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox" :checked="isChecked('smoke', option)"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <!-- Drinks -->
                                            <tr>
                                                <th>Drinks</th>
                                                <td v-for="option in ['Yes', 'No']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox" :checked="isChecked('drinks', option)"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <h5 class="sub-head-p mt-3">History</h5>
                                    <div class="table-responsive" style="overflow: hidden">
                                        <table class="table table-bordered new-form-table mt-2">
                                            <!-- Medication -->
                                            <tr>
                                                <th>Medication</th>
                                                <td v-for="option in ['Yes', 'No']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox" :checked="isChecked('medication', option)"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <!-- Hormonal Issues -->
                                            <tr>
                                                <th>Hormonal Issues</th>
                                                <td v-for="option in ['Yes', 'No']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox"
                                                            :checked="isChecked('hormonal_issues', option)" disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <!-- Past Treatments -->
                                            <tr>
                                                <th>Past Treatments</th>
                                                <td v-for="option in ['Peel', 'Botox', 'Laser', 'Other']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox"
                                                            :checked="isChecked('past_treatments', option)" disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <!-- Chronic Illness -->
                                            <tr>
                                                <th>Chronic Illness</th>
                                                <td v-for="option in ['Diabetes', 'Thyroid', 'Other']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox"
                                                            :checked="isChecked('chronic_illness', option)" disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <!-- Pregnant -->
                                            <tr>
                                                <th>Pregnant</th>
                                                <td v-for="option in ['Yes', 'No']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox" :checked="isChecked('pregnant', option)"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!-- Conclusion -->
                                    <h5 class="sub-head-p mt-3">Conclusion</h5>
                                    <table class="table new-form-table mt-1 mb-5">
                                        <tr>
                                            <th style="width: 200px;">Recommended Service</th>
                                            <td>
                                                <p class="iny-s">@{{ getConclusion('recommended_service') || 'N/A' }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 200px;">After Care</th>
                                            <td>
                                                <p class="iny-s">@{{ getConclusion('after_care') || 'N/A' }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 200px;">Follow Up Plan</th>
                                            <td>
                                                <p class="iny-s">@{{ getConclusion('follow_up_plan') || 'N/A' }}</p>
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="py-2"></div>
                                    <h5 class="mb-1 sub-head-p">After Care Guidelines</h5>
                                    <ul class="listing-ddd mb-2">
                                        <li>Avoid sun exposure and always apply sunscreen.</li>
                                        <li>Skip make-up, hot water, or harsh products for 24-48 hrs.</li>
                                        <li>Keep skin and body hydrated with water plus moisturizer.</li>
                                        <li>Use soothing gel/cream [aloe vera, calming lotion] if redness or irritation
                                            occurs.</li>
                                        <li>Follow gentle home-care routine as advised by the therapist.</li>
                                        <li>Maintain regular salon visits for best results.</li>
                                    </ul>
                                    <h5 class="mb-1 sub-head-p">Declaration</h5>
                                    <p style="font-size: 13px; margin: 0; margin-top: 8px;">I
                                        <b>@{{ selectedConsultation.customer_name || 'N/A' }}</b> hereby declare that;
                                    </p>
                                    <ol class="listing-ddd mb-2">
                                        <li>The information I have provided regarding my skin is true to the best of my
                                            knowledge.</li>
                                        <li>I understand that results of skin services may vary depending upon my skin
                                            condition and after care practice.</li>
                                        <li>I have disclosed any allergies, sensitivities, or health issues that may affect
                                            the treatment.</li>
                                        <li>I consent to undergo the recommended skin services and understand the procedure
                                            has been explained to me by the therapist.</li>
                                        <li>I agree to follow the suggested <b>after care instruction</b> for the best
                                            results and to avoid any adverse reactions.</li>
                                        <li>I release the <b>salon/therapist</b> from responsibility in case of any reaction
                                            arising due to undisclosed medical history, allergies, or home-care negligence.
                                        </li>
                                    </ol>
                                    <!-- Photo Permission -->
                                    <h4 class="mb-3" style="font-size: 16px">Permission to Use Photos/Videos</h4>
                                    <div class="d-flex align-items-center mb-2">
                                        <label class="check-le me-2">Yes
                                            <input type="checkbox" :checked="selectedConsultation.use_photo === 1"
                                                disabled>
                                            <span class="checkmark"></span>
                                        </label>
                                        <label class="check-le">No
                                            <input type="checkbox" :checked="selectedConsultation.use_photo === 0"
                                                disabled>
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <ul class="listing-ddd">
                                        <li>Salon will use the photos and videos respectfully and professionally.</li>
                                        <li>These images and videos may be used publicly or digitally to showcase the work
                                            and services of <b>BROWNZ</b> salon.</li>
                                    </ul>
                                    <!-- Signatures -->
                                    <div class="row g-2 sign-container">
                                        <div class="col-4">
                                            <p><b>Client Name:</b> @{{ selectedConsultation.customer_name || 'N/A' }}</p>
                                            <img v-if="selectedConsultation.customer_sign"
                                                :src="'/storage/' + selectedConsultation.customer_sign" height="70px"
                                                alt="Customer Signature">
                                            <p v-else>No Signature</p>
                                        </div>
                                        <div class="col-4">
                                            <p><b>Stylist Name:</b> @{{ selectedConsultation.beautician_name || 'N/A' }}</p>
                                            <img v-if="selectedConsultation.customer_sign"
                                                :src="'/storage/' + selectedConsultation.beautician_sign" height="70px"
                                                alt="Stylist Signature">
                                            <p v-else>No Signature</p>
                                        </div>
                                        <div class="col-4">
                                            <p><b>Manager Name:</b> @{{ selectedConsultation.manager_name || 'N/A' }}</p>
                                            <img v-if="selectedConsultation.customer_sign"
                                                :src="'/storage/' + selectedConsultation.manager_sign" height="70px"
                                                alt="Manager Signature">
                                            <p v-else>No Signature</p>
                                        </div>
                                    </div>
                                </form>
                                <div class="print-wrapper">
                                    <div class="content"></div>
                                    <footer>
                                        <div class="yellow"></div>
                                        <div class="bottom"></div>
                                    </footer>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-sm brownz-btn-yellow-d" type="button"
                                @click="$modal.hide('skinModal')">{{ __('Close') }}</button>
                        </div>
                    </div>
                </div>
            </modal>

            <!-- Hair Form Modal (View) -->
            <modal name="hairModal" width="900" height="auto" :scrollable="true" :adaptive="true"
                class="brownz-custom-popup m-0">
                <div class="">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div>
                                <h4 class="modal-title">{{ __('Hair Consultation') }}</h4>
                                <div class="modal-header-meta">
                                    <span class="modal-status-badge is-view">{{ __('View') }}</span>
                                    <span class="modal-type-badge is-hair">{{ __('Hair') }}</span>
                                </div>
                            </div>
                            <button type="button" class="bootbox-close-button close" @click="$modal.hide('hairModal')"
                                aria-label="Close">×</button>
                        </div>
                        <div class="modal-body p-3">
                            <div class="page">
                                <header>
                                    <div class="row align-items-center">
                                        <div class="col-4">
                                            <img src="{{ asset('admin/images/logo.png?id=4') }}" alt="logo">
                                        </div>
                                        <div class="col-8">
                                            <div class="row justify-content-end">
                                                <div class="col-4">
                                                    <p><i class="fa fa-phone" aria-hidden="true"></i> +91 9645 333 949</p>
                                                    <p><i class="fa fa-phone" aria-hidden="true"></i> +91 9745 333 949</p>
                                                </div>
                                                <div class="col-6">
                                                    <p><i class="fa fa-map-marker" aria-hidden="true"></i> Manjeri |
                                                        Perinthalmanna</p>
                                                    <p><i class="fa fa-envelope-o" aria-hidden="true"></i>
                                                        brownzbeautysalon@gmail.com</p>
                                                </div>
                                                <div class="col-4">
                                                    <p class="mb-0"><i class="fa fa-instagram" aria-hidden="true"></i>
                                                        brownzbeautysalon</p>
                                                </div>
                                                <div class="col-6"></div>
                                            </div>
                                        </div>
                                    </div>
                                </header>
                                <h4 class="text-center mb-4 mt-4" style="font-size: 18px;">CONSULTATION FORM FOR HAIR</h4>
                                <form method="POST" action="#">
                                    <!-- Client Info -->
                                    <div class="row mb-2 g-2 cleint-info-container">
                                        <div class="col-6 d-flex align-items-center">
                                            <label for="client_name">Client Name <span>:</span></label>
                                            <h6>@{{ selectedConsultation.customer_name || 'N/A' }}</h6>
                                        </div>
                                        <div class="col-3 d-flex align-items-center">
                                            <label for="age">Age <span>:</span></label>
                                            <h6>@{{ selectedConsultation.customer_age || 'N/A' }}</h6>
                                        </div>
                                        <div class="col-3 d-flex align-items-center">
                                            <label for="date">Date <span>:</span></label>
                                            <h6>@{{ selectedConsultation.date || 'N/A' }}</h6>
                                        </div>
                                        <div class="col-6 d-flex align-items-center">
                                            <label for="phone1">Phone 1 <span>:</span></label>
                                            <h6>@{{ selectedConsultation.customer_phone || 'N/A' }}</h6>
                                        </div>
                                        <div class="col-6 d-flex align-items-center">
                                            <label for="phone2">Phone 2 <span>:</span></label>
                                            <h6>@{{ selectedConsultation.customer_mobile || 'N/A' }}</h6>
                                        </div>
                                        <div class="col-6 d-flex align-items-center">
                                            <label for="stylist_name">Stylist Name <span>:</span></label>
                                            <h6>@{{ selectedConsultation.beautician_name || 'N/A' }}</h6>
                                        </div>
                                        <div class="col-6 d-flex align-items-center">
                                            <label for="designation">Designation <span>:</span></label>
                                            <h6>@{{ selectedConsultation.beautician_designation || 'N/A' }}</h6>
                                        </div>
                                    </div>
                                    <hr class="mt-0 mb-1">
                                    <!-- Primary Analysis -->
                                    <h5 class="sub-head-p">Primary Analysis</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered new-form-table mt-1 mb-2">
                                            <tr>
                                                <th>Scalp Condition</th>
                                                <td
                                                    v-for="option in ['Normal', 'Oily', 'Dry', 'Dandruff', 'Sensitive', 'Hair loss']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox"
                                                            :checked="isHairAnalysisChecked('scalp_condition', option)"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Hair Texture</th>
                                                <td v-for="option in ['Wavy', 'Curly', 'Straight', 'Coily']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox"
                                                            :checked="isHairAnalysisChecked('hair_texture', option)"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Hair Density</th>
                                                <td v-for="option in ['Fine', 'Medium', 'Thick']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox"
                                                            :checked="isHairAnalysisChecked('hair_density', option)"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Hair Condition</th>
                                                <td
                                                    v-for="option in ['Healthy', 'Dry/damaged', 'Sensitized', 'Split ends']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox"
                                                            :checked="isHairAnalysisChecked('hair_condition', option)"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Hair Elasticity</th>
                                                <td v-for="option in ['Healthy', 'Weak']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox"
                                                            :checked="isHairAnalysisChecked('hair_elasticity', option)"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Hair Porosity</th>
                                                <td v-for="option in ['Low', 'Medium', 'High']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox"
                                                            :checked="isHairAnalysisChecked('hair_porosity', option)"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>% of Grey</th>
                                                <td v-for="option in ['30%', '50%', '70%', '100%']">
                                                    <label class="check-le">@{{ option }}
                                                        <input type="checkbox"
                                                            :checked="isHairAnalysisChecked('grey_percentage', option)"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!-- History -->
                                    <h5 class="sub-head-p">History</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered new-form-table mt-1 mb-2">
                                            <tr>
                                                <th rowspan="5">Hair Type</th>
                                                <td>
                                                    <label class="check-le">Virgin
                                                        <input type="checkbox"
                                                            :checked="isHairHistoryChecked('hair_type', 'Virgin')"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; font-size: 12px;">@{{ getHairHistoryDescription('hair_type', 'Virgin') }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label class="check-le">Henna
                                                        <input type="checkbox"
                                                            :checked="isHairHistoryChecked('hair_type', 'Henna')" disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; font-size: 12px;">@{{ getHairHistoryDescription('hair_type', 'Henna') }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label class="check-le">Chemically Treated
                                                        <input type="checkbox"
                                                            :checked="isHairHistoryChecked('hair_type', 'Chemically Treated')"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; font-size: 12px;">@{{ getHairHistoryDescription('hair_type', 'Chemically Treated') }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label class="check-le">Coloured
                                                        <input type="checkbox"
                                                            :checked="isHairHistoryChecked('hair_type', 'Coloured')"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; font-size: 12px;">@{{ getHairHistoryDescription('hair_type', 'Coloured') }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label class="check-le">Keratin
                                                        <input type="checkbox"
                                                            :checked="isHairHistoryChecked('hair_type', 'Keratin')"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; font-size: 12px;">@{{ getHairHistoryDescription('hair_type', 'Keratin') }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th rowspan="4">Allergies / Sensitivities</th>
                                                <td>
                                                    <label class="check-le">Hair Dye
                                                        <input type="checkbox"
                                                            :checked="isHairHistoryChecked('allergies', 'Hair Dye')"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; font-size: 12px;">@{{ getHairHistoryDescription('allergies', 'Hair Dye') }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label class="check-le">Chemical Solution
                                                        <input type="checkbox"
                                                            :checked="isHairHistoryChecked('allergies', 'Chemical solution')"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; font-size: 12px;">@{{ getHairHistoryDescription('allergies', 'Chemical solution') }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label class="check-le">Blonder/Lightener
                                                        <input type="checkbox"
                                                            :checked="isHairHistoryChecked('allergies', 'Blonder/lightener')"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; font-size: 12px;">@{{ getHairHistoryDescription('allergies', 'Blonder/lightener') }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label class="check-le">Other
                                                        <input type="checkbox"
                                                            :checked="isHairHistoryChecked('allergies', 'Other')" disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; font-size: 12px;">@{{ getHairHistoryDescription('allergies', 'Other') }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th rowspan="2">Medical Condition</th>
                                                <td>
                                                    <label class="check-le">Pregnant
                                                        <input type="checkbox"
                                                            :checked="isHairHistoryChecked('medical_condition', 'Pregnant')"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; font-size: 12px;">@{{ getHairHistoryDescription('medical_condition', 'Pregnant') }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label class="check-le">Medicine Conception
                                                        <input type="checkbox"
                                                            :checked="isHairHistoryChecked('medical_condition',
                                                                'Medicine conception')"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; font-size: 12px;">@{{ getHairHistoryDescription('medical_condition', 'Medicine conception') }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Home Care</th>
                                                <td>
                                                    <label class="check-le">Shampoo/Conditioner/Serum
                                                        <input type="checkbox"
                                                            :checked="isHairHistoryChecked('home_care',
                                                                'Shampoo/conditioner/serum')"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; font-size: 12px;">@{{ getHairHistoryDescription('home_care', 'Shampoo/conditioner/serum') }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Oiling Habit</th>
                                                <td>
                                                    <label class="check-le">Frequency/Type of Oil
                                                        <input type="checkbox"
                                                            :checked="isHairHistoryChecked('oiling_habit',
                                                                'Frequency/Type of oil')"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; font-size: 12px;">@{{ getHairHistoryDescription('oiling_habit', 'Frequency/Type of oil') }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Use of Styling Product</th>
                                                <td>
                                                    <label class="check-le">Heating Tools/Styling Creams
                                                        <input type="checkbox"
                                                            :checked="isHairHistoryChecked('styling_product',
                                                                'Heating tools/styling creams')"
                                                            disabled>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <p style="margin: 0; font-size: 12px;">@{{ getHairHistoryDescription('styling_product', 'Heating tools/styling creams') }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!-- Client Desire -->
                                    <h5 class="sub-head-p mb-3">Client Desire</h5>
                                    <table class="table table-bordered new-form-table mt-1 mb-2 border-0">
                                        <tr>
                                            <th style="width: 100px;">Service Needed</th>
                                            <td>
                                                <p class="iny-s">@{{ getClientDesire('service_needed', 'description1') || 'N/A' }}</p>
                                            </td>
                                            <td>
                                                <p class="iny-s">@{{ getClientDesire('service_needed', 'description2') || 'N/A' }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 100px;">Desired Result</th>
                                            <td>
                                                <p class="iny-s">@{{ getClientDesire('desired_result', 'description1') || 'N/A' }}</p>
                                            </td>
                                            <td>
                                                <p class="iny-s">@{{ getClientDesire('desired_result', 'description2') || 'N/A' }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 100px;">Technique</th>
                                            <td>
                                                <p class="iny-s">@{{ getClientDesire('technique', 'description1') || 'N/A' }}</p>
                                            </td>
                                            <td>
                                                <p class="iny-s">@{{ getClientDesire('technique', 'description2') || 'N/A' }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 100px;">Patch Test</th>
                                            <td>
                                                <p class="iny-s">@{{ getClientDesire('patch_test', 'description1') || 'N/A' }}</p>
                                            </td>
                                            <td>
                                                <p class="iny-s">@{{ getClientDesire('patch_test', 'description2') || 'N/A' }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 100px;">Product Used</th>
                                            <td>
                                                <p class="iny-s">@{{ getClientDesire('product_used', 'description1') || 'N/A' }}</p>
                                            </td>
                                            <td>
                                                <p class="iny-s">@{{ getClientDesire('product_used', 'description2') || 'N/A' }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 100px;">After Care</th>
                                            <td>
                                                <p class="iny-s">@{{ getClientDesire('after_care', 'description1') || 'N/A' }}</p>
                                            </td>
                                            <td>
                                                <p class="iny-s">@{{ getClientDesire('after_care', 'description2') || 'N/A' }}</p>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- Declaration -->
                                    <h5 class="mb-1 sub-head-p">Declaration</h5>
                                    <ul class="listing-ddd mb-2">
                                        <li>I <b>@{{ selectedConsultation.customer_name || 'N/A' }}</b> confirm the accuracy of the information
                                            provided above.</li>
                                        <li>I <b>@{{ selectedConsultation.customer_name || 'N/A' }}</b> understand that chemical services involve
                                            potential risks including allergic reactions, irritation, hair breakage, or
                                            unsatisfactory results.</li>
                                        <li>I <b>@{{ selectedConsultation.customer_name || 'N/A' }}</b> agree to have a patch/strand test performed if
                                            recommended by the stylist.</li>
                                        <li>I <b>@{{ selectedConsultation.customer_name || 'N/A' }}</b> understand that results vary based on hair
                                            condition, previous services, and aftercare.</li>
                                        <li>I <b>@{{ selectedConsultation.customer_name || 'N/A' }}</b> will follow the recommended post-service care
                                            provided by the stylist. I understand the failure to follow the instructions may
                                            affect the results or lead to damage.</li>
                                        <li>I <b>@{{ selectedConsultation.customer_name || 'N/A' }}</b> release <b style="color: #E6CD7F;">BROWNZ</b>,
                                            its employees, and affiliates from liability in the case of an undesired result,
                                            reaction, or damage due to disclosed or undisclosed conditions.</li>
                                        <li>I have been informed about the cost of service before availing it.</li>
                                    </ul>
                                    <!-- Contraindications -->
                                    <h4 class="mb-1" style="font-size: 16px; color:#444;">Contraindications: <b>please
                                            read carefully</b></h4>
                                    <ol class="listing-ddd mb-2">
                                        <li>The purpose, procedure, and expected results of the service have been clearly
                                            explained to me by the stylist.</li>
                                        <li>Post colour cannot do straightening/smoothening for at least 8-10 days.</li>
                                        <li>In case of pre-lightening to achieve desired result (colour change) may take 2-3
                                            sittings depending on colour coating.</li>
                                        <li>Any colour after pre-lightening will last up to 10-12 washes.</li>
                                        <li>Certain services cannot be done on persons having Asthma, High Blood Pressure,
                                            undergoing Chemotherapy, experiencing exceptional hair loss, infection on scalp,
                                            Alopecia, nits/lice, any specific allergies, or pregnant women and feeding
                                            mothers.</li>
                                    </ol>
                                    <!-- Photo Permission -->
                                    <h4 class="mb-1" style="font-size: 16px">Permission to Use Photos/Videos</h4>
                                    <div class="d-flex align-items-center mb-2">
                                        <label class="check-le me-2">Yes
                                            <input type="checkbox" :checked="selectedConsultation.use_photo === 1"
                                                disabled>
                                            <span class="checkmark"></span>
                                        </label>
                                        <label class="check-le">No
                                            <input type="checkbox" :checked="selectedConsultation.use_photo === 0"
                                                disabled>
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <ul class="listing-ddd">
                                        <li>Salon will use the photos and videos respectfully and professionally.</li>
                                        <li>These images and videos may be used publicly or digitally to showcase the work
                                            and services of <b>BROWNZ</b> salon.</li>
                                    </ul>
                                    <!-- Signatures -->
                                    <div class="row g-2 sign-container">
                                        <div class="col-4">
                                            <p><b>Client Name:</b> @{{ selectedConsultation.customer_name || 'N/A' }}</p>
                                            <img v-if="selectedConsultation.customer_sign"
                                                :src="'/storage/' + selectedConsultation.customer_sign" height="70px"
                                                alt="Customer Signature">
                                            <p v-else>No Signature</p>
                                        </div>
                                        <div class="col-4">
                                            <p><b>Stylist Name:</b> @{{ selectedConsultation.beautician_name || 'N/A' }}</p>
                                            <img v-if="selectedConsultation.customer_sign"
                                                :src="'/storage/' + selectedConsultation.beautician_sign" height="70px"
                                                alt="Stylist Signature">
                                            <p v-else>No Signature</p>
                                        </div>
                                        <div class="col-4">
                                            <p><b>Manager Name:</b> @{{ selectedConsultation.manager_name || 'N/A' }}</p>
                                            <img v-if="selectedConsultation.customer_sign"
                                                :src="'/storage/' + selectedConsultation.manager_sign" height="70px"
                                                alt="Manager Signature">
                                            <p v-else>No Signature</p>
                                        </div>
                                    </div>
                                </form>
                                <div class="print-wrapper">
                                    <div class="content"></div>
                                    <footer>
                                        <div class="yellow"></div>
                                        <div class="bottom"></div>
                                    </footer>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-sm brownz-btn-yellow-d" type="button"
                                @click="$modal.hide('hairModal')">{{ __('Close') }}</button>
                        </div>
                    </div>
                </div>
            </modal>




            <!-- Edit Hair Form Modal (Approve) -->
            <modal name="EditHairModal" width="900" height="auto" :scrollable="true" :adaptive="true"
                class="brownz-custom-popup m-0">
                <div class="">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div>
                                <h4 class="modal-title">{{ __('Hair Consultation') }}</h4>
                                <div class="modal-header-meta">
                                    <span class="modal-status-badge is-approve">{{ __('Approve') }}</span>
                                    <span class="modal-type-badge is-hair">{{ __('Hair') }}</span>
                                </div>
                            </div>
                            <button type="button" class="bootbox-close-button close"
                                @click="$modal.hide('EditHairModal')" aria-label="Close">×</button>
                        </div>
                        <div class="modal-body p-3">
                            <div class="page">

                                <h4 class="text-center mb-4 mt-4" style="font-size: 18px;">CONSULTATION FORM FOR HAIR</h4>

                                <form id="consultationForm">
                                    <!-- Client Info -->
                                    <div class="row mb-4" style="row-gap: 16px">
                                        <div class="col-md-6">
                                            <label for="client_name">Client Name</label>
                                            <input type="text" class="browz-gray-input-custom" id="client_name"
                                                name="client_name" v-model="client_name" readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="age">Age</label>
                                            <input type="number" class="browz-gray-input-custom" id="age"
                                                name="age" v-model="client_age">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="date">Date</label>
                                            <input type="date" class="browz-gray-input-custom" id="date"
                                                name="date" v-model="date">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="phone1">Phone 1</label>
                                            <input type="text" class="browz-gray-input-custom" id="phone1"
                                                name="phone1" v-model="client_mobile" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="phone2">Phone 2</label>
                                            <input type="text" class="browz-gray-input-custom" id="phone2"
                                                name="phone2" v-model="client_phone">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="stylist_name">Stylist Name</label>
                                            <input type="text" class="browz-gray-input-custom" id="stylist_name"
                                                name="stylist_name" v-model="stylist_name">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="designation">Designation</label>
                                            <input type="text" class="browz-gray-input-custom" id="designation"
                                                name="designation" v-model="designation">
                                        </div>
                                    </div>

                                    <hr class="pb-3">

                                    <h5 class="mb-3 mt-3">Primary Analysis</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered new-form-table">
                                            <!-- Scalp Condition -->
                                            <tr>
                                                <th>Scalp Condition</th>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Normal"
                                                            v-model="primary_analysis.scalp_condition"> Normal
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Oily"
                                                            v-model="primary_analysis.scalp_condition"> Oily
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Dry"
                                                            v-model="primary_analysis.scalp_condition"> Dry
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Dandruff"
                                                            v-model="primary_analysis.scalp_condition"> Dandruff
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Sensitive"
                                                            v-model="primary_analysis.scalp_condition"> Sensitive
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Hair loss"
                                                            v-model="primary_analysis.scalp_condition"> Hair loss
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>

                                            <!-- Hair Texture -->
                                            <tr>
                                                <th>Hair Texture</th>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Wavy"
                                                            v-model="primary_analysis.hair_texture"> Wavy
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Curly"
                                                            v-model="primary_analysis.hair_texture"> Curly
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Straight"
                                                            v-model="primary_analysis.hair_texture"> Straight
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Coily"
                                                            v-model="primary_analysis.hair_texture"> Coily
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td colspan="2"></td>
                                            </tr>

                                            <!-- Hair Density -->
                                            <tr>
                                                <th>Hair Density</th>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Fine"
                                                            v-model="primary_analysis.hair_density"> Fine
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Medium"
                                                            v-model="primary_analysis.hair_density"> Medium
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Thick"
                                                            v-model="primary_analysis.hair_density"> Thick
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td colspan="3"></td>
                                            </tr>

                                            <!-- Hair Condition -->
                                            <tr>
                                                <th>Hair Condition</th>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Healthy"
                                                            v-model="primary_analysis.hair_condition"> Healthy
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox"
                                                            value="Dry/damaged" v-model="primary_analysis.hair_condition">
                                                        Dry/damaged
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox"
                                                            value="Sensitized" v-model="primary_analysis.hair_condition">
                                                        Sensitized
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox"
                                                            value="Split ends" v-model="primary_analysis.hair_condition">
                                                        Split ends
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td colspan="2"></td>
                                            </tr>

                                            <!-- Hair Elasticity -->
                                            <tr>
                                                <th>Hair Elasticity</th>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Healthy"
                                                            v-model="primary_analysis.hair_elasticity"> Healthy
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Weak"
                                                            v-model="primary_analysis.hair_elasticity"> Weak
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td colspan="4"></td>
                                            </tr>

                                            <!-- Hair Porosity -->
                                            <tr>
                                                <th>Hair Porosity</th>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Low"
                                                            v-model="primary_analysis.hair_porosity"> Low
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="Medium"
                                                            v-model="primary_analysis.hair_porosity"> Medium
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="High"
                                                            v-model="primary_analysis.hair_porosity"> High
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td colspan="3"></td>
                                            </tr>

                                            <!-- % of Grey -->
                                            <tr>
                                                <th>% of Grey</th>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="30%"
                                                            v-model="primary_analysis.grey_percentage"> 30%
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="50%"
                                                            v-model="primary_analysis.grey_percentage"> 50%
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="70%"
                                                            v-model="primary_analysis.grey_percentage"> 70%
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox" value="100%"
                                                            v-model="primary_analysis.grey_percentage"> 100%
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <hr>
                                    <!-- History -->
                                    <h5 class="mb-3 mt-3">History</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered new-form-table" cellspacing="0"
                                            cellpadding="6" width="100%">
                                            <!-- Hair Type -->
                                            <tr>
                                                <th style="border-bottom: 1px solid #0000001a;" rowspan="5">Hair Type
                                                </th>
                                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox"
                                                            v-model="hair_history.hair_type[0].checked"> Virgin
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                                        v-model="hair_history.hair_type[0].description"></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox"
                                                            v-model="hair_history.hair_type[1].checked"> Henna
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                                        v-model="hair_history.hair_type[1].description"></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox"
                                                            v-model="hair_history.hair_type[2].checked"> Chemically Treated
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                                        v-model="hair_history.hair_type[2].description"></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox"
                                                            v-model="hair_history.hair_type[3].checked"> Coloured
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                                        v-model="hair_history.hair_type[3].description"></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border-bottom: 1px solid #0000001a;">
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox"
                                                            v-model="hair_history.hair_type[4].checked"> Keratin
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td style="border-bottom: 1px solid #0000001a;">
                                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                                        v-model="hair_history.hair_type[4].description"></textarea>
                                                </td>
                                            </tr>
                                            <!-- Allergies -->
                                            <tr>
                                                <th style="border-bottom: 1px solid #0000001a;" rowspan="4">
                                                    Allergies/Sensitivities
                                                </th>
                                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox"
                                                            v-model="hair_history.allergies[0].checked"> Hair Dye
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                                        v-model="hair_history.allergies[0].description"></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox"
                                                            v-model="hair_history.allergies[1].checked"> Chemical Solution
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                                        v-model="hair_history.allergies[1].description"></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox"
                                                            v-model="hair_history.allergies[2].checked"> Blonder/Lightener
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                                        v-model="hair_history.allergies[2].description"></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border-bottom: 1px solid #0000001a;">
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox"
                                                            v-model="hair_history.allergies[3].checked"> Other
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td style="border-bottom: 1px solid #0000001a;">
                                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                                        v-model="hair_history.allergies[3].description"></textarea>
                                                </td>
                                            </tr>
                                            <!-- Medical Condition -->
                                            <tr>
                                                <th style="border-bottom: 1px solid #0000001a;" rowspan="2">Medical
                                                    Condition</th>
                                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox"
                                                            v-model="hair_history.medical_condition[0].checked"> Pregnant
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                                        v-model="hair_history.medical_condition[0].description"></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border-bottom: 1px solid #0000001a;">
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox"
                                                            v-model="hair_history.medical_condition[1].checked"> Medicine
                                                        Conception
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td style="border-bottom: 1px solid #0000001a;">
                                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                                        v-model="hair_history.medical_condition[1].description"></textarea>
                                                </td>
                                            </tr>
                                            <!-- Home Care -->
                                            <tr>
                                                <th style="border-bottom: 1px solid #0000001a;">Home Care</th>
                                                <td style="border-bottom: 1px solid #0000001a;">
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox"
                                                            v-model="hair_history.home_care[0].checked">
                                                        Shampoo/Conditioner/Serum
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td style="border-bottom: 1px solid #0000001a;">
                                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                                        v-model="hair_history.home_care[0].description"></textarea>
                                                </td>
                                            </tr>
                                            <!-- Oiling Habit -->
                                            <tr>
                                                <th style="border-bottom: 1px solid #0000001a;">Oiling Habit</th>
                                                <td style="border-bottom: 1px solid #0000001a;">
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox"
                                                            v-model="hair_history.oiling_habit[0].checked"> Frequency/Type
                                                        of Oil
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td style="border-bottom: 1px solid #0000001a;">
                                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                                        v-model="hair_history.oiling_habit[0].description"></textarea>
                                                </td>
                                            </tr>
                                            <!-- Use of Styling Product -->
                                            <tr>
                                                <th style="border-bottom: 1px solid #0000001a;">Use of Styling Product</th>
                                                <td style="border-bottom: 1px solid #0000001a;">
                                                    <label class="brw-checkbox-browz-dds">
                                                        <input class="brow-checkbox-dd" type="checkbox"
                                                            v-model="hair_history.styling_product[0].checked"> Heating
                                                        Tools/Styling Creams
                                                        <span class="brow-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td style="border-bottom: 1px solid #0000001a;">
                                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                                        v-model="hair_history.styling_product[0].description"></textarea>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <h5 class="mb-3 mt-3">Client desire</h5>

                                    <table class=" table table-bordered new-form-table border-0" cellspacing="0"
                                        cellpadding="6" width="100%">
                                        <tr>
                                            <th>Service Needed</th>
                                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                                    v-model="client_desire.service_needed.description1"></td>
                                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                                    v-model="client_desire.service_needed.description2"></td>
                                        </tr>
                                        <tr>
                                            <th>Desired Result</th>
                                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                                    v-model="client_desire.desired_result.description1"></td>
                                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                                    v-model="client_desire.desired_result.description2"></td>
                                        </tr>
                                        <tr>
                                            <th>Technique</th>
                                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                                    v-model="client_desire.technique.description1"></td>
                                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                                    v-model="client_desire.technique.description2"></td>
                                        </tr>
                                        <tr>
                                            <th>Patch Test</th>
                                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                                    v-model="client_desire.patch_test.description1"></td>
                                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                                    v-model="client_desire.patch_test.description2"></td>
                                        </tr>
                                        <tr>
                                            <th>Product Used</th>
                                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                                    v-model="client_desire.product_used.description1"></td>
                                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                                    v-model="client_desire.product_used.description2"></td>
                                        </tr>
                                        <tr>
                                            <th>After Care</th>
                                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                                    v-model="client_desire.after_care.description1"></td>
                                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                                    v-model="client_desire.after_care.description2"></td>
                                        </tr>
                                    </table>

                                    <hr>
                                </form>
                                <div class="print-wrapper">
                                    <div class="content"></div>
                                    <footer>
                                        <div class="yellow"></div>
                                        <div class="bottom"></div>
                                    </footer>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-sm brownz-btn-yellow-d" type="button"
                                @click="$modal.hide('EditHairModal')">{{ __('Close') }}</button>
                            <button v-if="selectedConsultation.status != 'approved'"
                                type="button"
                                class="btn btn-sm btn-approve"
                                @click="approveHairConsultation(selectedConsultation.id)">
                                {{ __('Approve') }}
                            </button>
                            <button v-else type="button" class="btn btn-sm btn-approve" disabled>
                                {{ __('Approved') }}
                            </button>
                        </div>
                    </div>
                </div>
            </modal>


            <!-- Edit Skin Form Modal (Approve) -->
            <modal name="EditSkinModal" width="900" height="auto" :scrollable="true" :adaptive="true"
                class="brownz-custom-popup m-0">
                <div class="">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div>
                                <h4 class="modal-title">{{ __('Skin Consultation') }}</h4>
                                <div class="modal-header-meta">
                                    <span class="modal-status-badge is-approve">{{ __('Approve') }}</span>
                                    <span class="modal-type-badge is-skin">{{ __('Skin') }}</span>
                                </div>
                            </div>
                            <button type="button" class="bootbox-close-button close"
                                @click="$modal.hide('EditSkinModal')" aria-label="Close">×</button>
                        </div>
                        <div class="modal-body p-3">
                            <div class="page">

                                <h4 class="text-center mb-4 mt-4" style="font-size: 18px;">CONSULTATION FORM FOR SKIN</h4>


                                <div class="modal-body">
                                    <form id="consultationForm">
                                        <!-- Client Info -->
                                        <div class="row mb-4" style="row-gap: 16px">
                                            <div class="col-md-6">
                                                <label for="client_name">Client Name</label>
                                                <input type="text" class="browz-gray-input-custom" id="client_name"
                                                    name="client_name" v-model="client_name" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="age">Age</label>
                                                <input type="number" class="browz-gray-input-custom" id="age"
                                                    name="age" v-model="client_age">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="date">Date</label>
                                                <input type="date" class="browz-gray-input-custom" id="date"
                                                    name="date" v-model="date">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="phone1">Phone 1</label>
                                                <input type="text" class="browz-gray-input-custom" id="phone1"
                                                    name="phone1" v-model="client_mobile" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="phone2">Phone 2</label>
                                                <input type="text" class="browz-gray-input-custom" id="phone2"
                                                    name="phone2" v-model="client_phone">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="stylist_name">Stylist Name</label>
                                                <input type="text" class="browz-gray-input-custom"
                                                    id="stylist_name" name="stylist_name" v-model="stylist_name">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="designation">Designation</label>
                                                <input type="text" class="browz-gray-input-custom" id="designation"
                                                    name="designation" v-model="designation">
                                            </div>
                                        </div>

                                        <hr>

                                        <h5 class="mb-3 mt-3">Skin Analysis</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered new-form-table">
                                                <tr>
                                                    <th>Skin type</th>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_type" value="Normal">
                                                            Normal <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_type" value="Dry">
                                                            Dry <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_type" value="Oily">
                                                            Oily <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_type" value="Combination">
                                                            Combination <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_type" value="Pore">
                                                            Pore <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_type" value="Other">
                                                            Other <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th>Skin concern</th>
                                                    <td>

                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_concern" value="Acne">
                                                            Acne <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_concern"
                                                                value="Pigmentation">
                                                            Pigmentation <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_concern" value="Dullness">
                                                            Dullness <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_concern" value="Wrinkles">
                                                            Wrinkles <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_concern" value="Pore">
                                                            Pore <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_concern" value="Other">
                                                            Other <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th>Skin allergy</th>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_allergy" value="Yes">
                                                            Yes <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_allergy" value="No">
                                                            No <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td colspan="4"></td>
                                                </tr>

                                                <tr>
                                                    <th>Skin sensitivity</th>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_sensitivity" value="Yes">
                                                            Yes <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_sensitivity" value="No">
                                                            No <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td colspan="4"></td>
                                                </tr>

                                                <tr>
                                                    <th>Skin routine</th>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_routine" value="Cleanser">
                                                            Cleanser <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_routine"
                                                                value="Moisturiser">
                                                            Moisturiser <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_routine" value="Sunscreen">
                                                            Sunscreen <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_routine" value="Serum">
                                                            Serum <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_routine" value="Toner">
                                                            Toner <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="skin_analysis.skin_routine" value="Night Care">
                                                            Night Care <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                </tr>
                                            </table>


                                        </div>

                                        <hr>

                                        <h5 class="mb-3 mt-3">Life style</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered new-form-table">
                                                <tr>
                                                    <th>Water intakes</th>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="life_style.water_intakes" value="Low">
                                                            Low <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="life_style.water_intakes" value="Medium">
                                                            Medium <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="life_style.water_intakes" value="High">
                                                            High <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th>Sleep</th>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="life_style.sleep" value="Good">
                                                            Good <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="life_style.sleep" value="Moderate">
                                                            Moderate <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="life_style.sleep" value="Poor">
                                                            Poor <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th>Stress</th>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="life_style.stress" value="Low">
                                                            Low <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="life_style.stress" value="Moderate">
                                                            Moderate <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="life_style.stress" value="High">
                                                            High <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th>Sun exposure</th>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="life_style.sun_exposure" value="Yes">
                                                            Yes <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="life_style.sun_exposure" value="No">
                                                            No <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td></td>
                                                </tr>

                                                <tr>
                                                    <th>Smoke</th>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="life_style.smoke" value="Yes">
                                                            Yes <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="life_style.smoke" value="No">
                                                            No <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td></td>
                                                </tr>

                                                <tr>
                                                    <th>Drinks</th>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="life_style.drinks" value="Yes">
                                                            Yes <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="life_style.drinks" value="No">
                                                            No <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </div>

                                        <hr>

                                        <h5 class="mb-3 mt-3">History</h5>
                                        <div class="table-resposive">
                                            <table class="table table-bordered new-form-table border-0">
                                                <tr>
                                                    <th>Medication</th>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="history.medication" value="yes">
                                                            Yes <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="history.medication" value="no">
                                                            No <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>

                                                <tr>
                                                    <th>Hormonal Issues</th>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="history.hormonal_issues" value="yes">
                                                            Yes <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="history.hormonal_issues" value="no">
                                                            No <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>

                                                <tr>
                                                    <th>Past Treatments</th>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="history.past_treatments" value="peel">
                                                            Peel <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="history.past_treatments" value="botox">
                                                            Botox <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="history.past_treatments" value="laser">
                                                            Laser <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="history.past_treatments" value="other">
                                                            Other <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th>Chronic Illness</th>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="history.chronic_illness" value="diabetes">
                                                            Diabetes <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="history.chronic_illness" value="thyroid">
                                                            Thyroid <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="history.chronic_illness" value="other">
                                                            Other <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td></td>

                                                </tr>

                                                <tr>
                                                    <th>Pregnant</th>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="history.pregnant" value="yes">
                                                            Yes <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="brw-checkbox-browz-dds">
                                                            <input class="brow-checkbox-dd" type="checkbox"
                                                                v-model="history.pregnant" value="no">
                                                            No <span class="brow-checkmark"></span>
                                                        </label>
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </div>


                                        <h5 class="mb-3 mt-3">Conclusion</h5>
                                        <table class=" table table-bordered new-form-table border-0" cellspacing="0"
                                            cellpadding="6" width="100%">
                                            <tr>
                                                <th style="width: 200px">Recommended service</th>
                                                <td><input type="text" class="browz-gray-input-custom"
                                                        v-model="conclusion.recommended_service.description"
                                                        style="width:100%;"></td>

                                            </tr>
                                            <tr>
                                                <th style="width: 200px">After care </th>
                                                <td><input type="text" class="browz-gray-input-custom"
                                                        v-model="conclusion.after_care.description" style="width:100%;">
                                                </td>

                                            </tr>
                                            <tr>
                                                <th style="width: 200px">Follow up plan</th>
                                                <td><input type="text" class="browz-gray-input-custom"
                                                        v-model="conclusion.follow_up_plan.description"
                                                        style="width:100%;"></td>
                                            </tr>
                                        </table>


                                    </form>
                                </div>
                                <div class="print-wrapper">
                                    <div class="content"></div>
                                    <footer>
                                        <div class="yellow"></div>
                                        <div class="bottom"></div>
                                    </footer>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-sm brownz-btn-yellow-d" type="button"
                                @click="$modal.hide('EditSkinModal')">{{ __('Close') }}</button>
                            <button v-if="selectedConsultation.status != 'approved'"
                                type="button"
                                class="btn btn-sm btn-approve"
                                @click="approveSkinConsultation(selectedConsultation.id)">
                                {{ __('Approve') }}
                            </button>
                            <button v-else type="button" class="btn btn-sm btn-approve" disabled>
                                {{ __('Approved') }}
                            </button>
                        </div>
                    </div>
                </div>
            </modal>





        </div>
    </div>

    <script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
    <script>
        var vc_moment_date_offset = new Date("2021-10-05T17:28:50.403+03:00").getTime() - Date.now();
        var vc_jscalendar_moment_format = "DD/MM/YYYY";

        function currentMonthRange() {
            var now = new Date();
            var year = now.getFullYear();
            var month = now.getMonth();
            var start = new Date(year, month, 1);
            var end = new Date(year, month + 1, 0);
            function pad(n) { return n < 10 ? '0' + n : String(n); }
            return {
                start: year + '-' + pad(month + 1) + '-' + pad(start.getDate()),
                end: year + '-' + pad(month + 1) + '-' + pad(end.getDate())
            };
        }

        var monthRange = currentMonthRange();
    </script>
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] =
            document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        Vue.use(window["vue-js-modal"].default);
        var app = new Vue({
            el: '#app',
            data: {
                loading: false,
                base_url: "{{ '/' }}",
                ConsultationList: [],
                selectedConsultation: '',
                showSkinForm: false,
                showHairForm: false,
                search: '',
                page_length: '100',
                pagination: {
                    total: 0,
                    per_page: 100,
                    from: 1,
                    to: 0,
                    current_page: 1,
                    last_page: 1
                },
                offset: 4,
                formErrors: {},
                filter_client_name: '',
                filter_client_mobile: '',
                filter_type: '',
                filter_status: '',
                filter_start_date: monthRange.start,
                filter_end_date: monthRange.end,
                // Client Info
                'client_name': '',
                'client_mobile': '',
                'client_age': '',
                'date': '',
                'client_phone': '',
                'stylist_name': '',
                'designation': '',

                primary_analysis: {
                    scalp_condition: [],
                    hair_texture: [],
                    hair_density: [],
                    hair_condition: [],
                    hair_elasticity: [],
                    hair_porosity: [],
                    grey_percentage: [],
                },

                hair_history: {

                    hair_type: [{
                            type: 'Virgin',
                            checked: false,
                            description: ''
                        },
                        {
                            type: 'Henna',
                            checked: false,
                            description: ''
                        },
                        {
                            type: 'Chemically Treated',
                            checked: false,
                            description: ''
                        },
                        {
                            type: 'Coloured',
                            checked: false,
                            description: ''
                        },
                        {
                            type: 'Keratin',
                            checked: false,
                            description: ''
                        },
                    ],

                    allergies: [{
                            type: 'Hair Dye',
                            checked: false,
                            description: ''
                        },
                        {
                            type: 'Chemical Solution',
                            checked: false,
                            description: ''
                        },
                        {
                            type: 'Blonder/Lightener',
                            checked: false,
                            description: ''
                        },
                        {
                            type: 'Other',
                            checked: false,
                            description: ''
                        },
                    ],

                    medical_condition: [{
                            type: 'Pregnant',
                            checked: false,
                            description: ''
                        },
                        {
                            type: 'Medicine Conception',
                            checked: false,
                            description: ''
                        },
                    ],

                    home_care: [{
                        type: 'Shampoo/Conditioner/Serum',
                        checked: false,
                        description: ''
                    }, ],

                    oiling_habit: [{
                        type: 'Frequency/Type of Oil',
                        checked: false,
                        description: ''
                    }, ],

                    styling_product: [{
                        type: 'Styling Product',
                        checked: false,
                        description: ''
                    }, ],
                },
                client_desire: {
                    service_needed: {
                        description1: '',
                        description2: '',
                    },
                    desired_result: {
                        description1: '',
                        description2: '',
                    },
                    technique: {
                        description1: '',
                        description2: '',
                    },
                    patch_test: {
                        description1: '',
                        description2: '',
                    },
                    product_used: {
                        description1: '',
                        description2: '',
                    },
                    after_care: {
                        description1: '',
                        description2: '',
                    },
                },

                skin_analysis: {
                    skin_type: [],
                    skin_concern: [],
                    skin_allergy: [],
                    skin_sensitivity: [],
                    skin_routine: [],
                },
                life_style: {
                    water_intakes: [],
                    sleep: [],
                    stress: [],
                    sun_exposure: [],
                    smoke: [],
                    drinks: [],
                },
                history: {
                    medication: [],
                    hormonal_issues: [],
                    past_treatments: [],
                    chronic_illness: [],
                    pregnant: [],
                },
                conclusion: {
                    recommended_service: {
                        description: '',
                    },
                    after_care: {
                        description: '',
                    },
                    follow_up_plan: {
                        description: '',
                    },
                },
            },
            computed: {
                isActived: function() {
                    return this.pagination.current_page;
                },
                pagesNumber: function() {
                    if (!this.pagination.to) {
                        return [];
                    }
                    var from = this.pagination.current_page - this.offset;
                    if (from < 1) {
                        from = 1;
                    }
                    var to = from + (this.offset * 2);
                    if (to >= this.pagination.last_page) {
                        to = this.pagination.last_page;
                    }
                    var pagesArray = [];
                    while (from <= to) {
                        pagesArray.push(from);
                        from++;
                    }
                    return pagesArray;
                }
            },
            methods: {
                formatDate(d) {
                    if (!d) return '';
                    const dt = new Date(d);
                    const day = String(dt.getDate()).padStart(2, '0');
                    const month = String(dt.getMonth() + 1).padStart(2, '0');
                    const year = dt.getFullYear();
                    return `${day}-${month}-${year}`;
                },
                safeJsonArray(value) {
                    if (Array.isArray(value)) {
                        return value;
                    }
                    if (value == null || value === '') {
                        return [];
                    }
                    if (typeof value === 'object') {
                        return Array.isArray(value) ? value : [];
                    }
                    try {
                        const parsed = JSON.parse(value);
                        return Array.isArray(parsed) ? parsed : [];
                    } catch (e) {
                        return [];
                    }
                },
                viewConsultation(data) {

                    this.selectedConsultation = data;
                    if (data.type === 'skin') {
                        this.$modal.show('skinModal');
                    } else if (data.type === 'hair') {
                        this.$modal.show('hairModal');
                    } else {
                        this.$toast.error('Invalid consultation type');
                    }
                },
                editConsultation(data) {
                    this.selectedConsultation = data;
                    if (data.type === 'skin') {
                        app.client_name = data.customer_name || '';
                        app.client_mobile = data.customer_mobile || '';
                        app.client_age = data.customer_age || '';
                        app.date = data.date || '';
                        app.client_phone = data.customer_phone || '';
                        app.stylist_name = data.beautician_name || '';
                        app.designation = data.beautician_designation || '';

                        app.skin_analysis = {
                            skin_type: data.skin_analyses?.find(item => item.skin_analyses_title ===
                                    'skin_type') ?
                                this.safeJsonArray(data.skin_analyses.find(item => item.skin_analyses_title ===
                                    'skin_type').skin_analyses_values) : [],
                            skin_concern: data.skin_analyses?.find(item => item.skin_analyses_title ===
                                    'skin_concern') ?
                                this.safeJsonArray(data.skin_analyses.find(item => item.skin_analyses_title ===
                                    'skin_concern').skin_analyses_values) : [],
                            skin_allergy: data.skin_analyses?.find(item => item.skin_analyses_title ===
                                    'skin_allergy') ?
                                this.safeJsonArray(data.skin_analyses.find(item => item.skin_analyses_title ===
                                    'skin_allergy').skin_analyses_values) : [],
                            skin_sensitivity: data.skin_analyses?.find(item => item.skin_analyses_title ===
                                    'skin_sensitivity') ?
                                this.safeJsonArray(data.skin_analyses.find(item => item.skin_analyses_title ===
                                    'skin_sensitivity').skin_analyses_values) : [],
                            skin_routine: data.skin_analyses?.find(item => item.skin_analyses_title ===
                                    'skin_routine') ?
                                this.safeJsonArray(data.skin_analyses.find(item => item.skin_analyses_title ===
                                    'skin_routine').skin_analyses_values) : [],
                        };
                        app.life_style = {
                            water_intakes: data.skin_lifestyles?.find(item => item.skin_lifestyle_title ===
                                    'water_intakes') ?
                                this.safeJsonArray(data.skin_lifestyles.find(item => item.skin_lifestyle_title ===
                                    'water_intakes').skin_lifestyle_values) : [],
                            sleep: data.skin_lifestyles?.find(item => item.skin_lifestyle_title ===
                                    'sleep') ?
                                this.safeJsonArray(data.skin_lifestyles.find(item => item.skin_lifestyle_title ===
                                    'sleep').skin_lifestyle_values) : [],
                            stress: data.skin_lifestyles?.find(item => item.skin_lifestyle_title ===
                                    'stress') ?
                                this.safeJsonArray(data.skin_lifestyles.find(item => item.skin_lifestyle_title ===
                                    'stress').skin_lifestyle_values) : [],
                            sun_exposure: data.skin_lifestyles?.find(item => item.skin_lifestyle_title ===
                                    'sun_exposure') ?
                                this.safeJsonArray(data.skin_lifestyles.find(item => item.skin_lifestyle_title ===
                                    'sun_exposure').skin_lifestyle_values) : [],
                            smoke: data.skin_lifestyles?.find(item => item.skin_lifestyle_title ===
                                    'smoke') ?
                                this.safeJsonArray(data.skin_lifestyles.find(item => item.skin_lifestyle_title ===
                                    'smoke').skin_lifestyle_values) : [],
                            drinks: data.skin_lifestyles?.find(item => item.skin_lifestyle_title ===
                                    'drinks') ?
                                this.safeJsonArray(data.skin_lifestyles.find(item => item.skin_lifestyle_title ===
                                    'drinks').skin_lifestyle_values) : [],
                        };
                        app.history = {
                            medication: data.skin_histories?.find(item => item.skin_history_title ===
                                    'medication') ?
                                this.safeJsonArray(data.skin_histories.find(item => item.skin_history_title ===
                                    'medication').skin_history_values) : [],
                            hormonal_issues: data.skin_histories?.find(item => item.skin_history_title ===
                                    'hormonal_issues') ?
                                this.safeJsonArray(data.skin_histories.find(item => item.skin_history_title ===
                                    'hormonal_issues').skin_history_values) : [],
                            past_treatments: data.skin_histories?.find(item => item.skin_history_title ===
                                    'past_treatments') ?
                                this.safeJsonArray(data.skin_histories.find(item => item.skin_history_title ===
                                    'past_treatments').skin_history_values) : [],
                            chronic_illness: data.skin_histories?.find(item => item.skin_history_title ===
                                    'chronic_illness') ?
                                this.safeJsonArray(data.skin_histories.find(item => item.skin_history_title ===
                                    'chronic_illness').skin_history_values) : [],
                            pregnant: data.skin_histories?.find(item => item.skin_history_title ===
                                    'pregnant') ?
                                this.safeJsonArray(data.skin_histories.find(item => item.skin_history_title ===
                                    'pregnant').skin_history_values) : [],
                        };
                        app.conclusion = {
                            recommended_service: {
                                description: data.skin_conclusions?.find(item => item.skin_conclusion_title ===
                                        'recommended_service') ?
                                    data.skin_conclusions.find(item => item.skin_conclusion_title ===
                                        'recommended_service').skin_conclusion_description || '' : '',
                            },
                            after_care: {
                                description: data.skin_conclusions?.find(item => item.skin_conclusion_title ===
                                        'after_care') ?
                                    data.skin_conclusions.find(item => item.skin_conclusion_title ===
                                        'after_care').skin_conclusion_description || '' : '',
                            },
                            follow_up_plan: {
                                description: data.skin_conclusions?.find(item => item.skin_conclusion_title ===
                                        'follow_up_plan') ?
                                    data.skin_conclusions.find(item => item.skin_conclusion_title ===
                                        'follow_up_plan').skin_conclusion_description || '' : '',
                            },
                        };
                        this.$modal.show('EditSkinModal');
                    } else if (data.type === 'hair') {
                        app.client_name = data.customer_name || '';
                        app.client_mobile = data.customer_mobile || '';
                        app.client_age = data.customer_age || '';
                        app.date = data.date || '';
                        app.client_phone = data.customer_phone || '';
                        app.stylist_name = data.beautician_name || '';
                        app.designation = data.beautician_designation || '';


                        app.primary_analysis = {
                            scalp_condition: data.hair_analyses?.find(item => item.hair_analyses_title ===
                                    'scalp_condition') ?
                                this.safeJsonArray(data.hair_analyses.find(item => item.hair_analyses_title ===
                                    'scalp_condition').hair_analyses_values) : [],
                            hair_texture: data.hair_analyses?.find(item => item.hair_analyses_title ===
                                    'hair_texture') ?
                                this.safeJsonArray(data.hair_analyses.find(item => item.hair_analyses_title ===
                                    'hair_texture').hair_analyses_values) : [],
                            hair_density: data.hair_analyses?.find(item => item.hair_analyses_title ===
                                    'hair_density') ?
                                this.safeJsonArray(data.hair_analyses.find(item => item.hair_analyses_title ===
                                    'hair_density').hair_analyses_values) : [],
                            hair_condition: data.hair_analyses?.find(item => item.hair_analyses_title ===
                                    'hair_condition') ?
                                this.safeJsonArray(data.hair_analyses.find(item => item.hair_analyses_title ===
                                    'hair_condition').hair_analyses_values) : [],
                            hair_elasticity: data.hair_analyses?.find(item => item.hair_analyses_title ===
                                    'hair_elasticity') ?
                                this.safeJsonArray(data.hair_analyses.find(item => item.hair_analyses_title ===
                                    'hair_elasticity').hair_analyses_values) : [],
                            hair_porosity: data.hair_analyses?.find(item => item.hair_analyses_title ===
                                    'hair_porosity') ?
                                this.safeJsonArray(data.hair_analyses.find(item => item.hair_analyses_title ===
                                    'hair_porosity').hair_analyses_values) : [],
                            grey_percentage: data.hair_analyses?.find(item => item.hair_analyses_title ===
                                    'grey_percentage') ?
                                this.safeJsonArray(data.hair_analyses.find(item => item.hair_analyses_title ===
                                    'grey_percentage').hair_analyses_values) : [],
                        };



                        (data.hair_histories || []).forEach(item => {

                            let values = [];
                            let descriptions = [];

                            try {
                                values = this.safeJsonArray(item.hair_history_values);
                            } catch (e) {
                                values = [];
                            }

                            try {
                                descriptions = this.safeJsonArray(item.hair_history_description);
                            } catch (e) {
                                descriptions = [];
                            }

                            let target = app.hair_history[item.hair_history_title];

                            if (target && Array.isArray(target)) {

                                target.forEach((historyItem) => {

                                    historyItem.checked = false;
                                    historyItem.description = '';

                                    let valueIndex = values.findIndex(value => {

                                        return (
                                            String(value || '').toLowerCase().trim() ===
                                            String(historyItem.type || '').toLowerCase()
                                            .trim()
                                        );
                                    });
                                    if (valueIndex !== -1) {

                                        historyItem.checked = true;

                                        historyItem.description =
                                            descriptions[valueIndex] || '';
                                    }
                                });
                            }
                        });


                        app.client_desire = {
                            service_needed: {
                                description1: '',
                                description2: '',
                            },
                            desired_result: {
                                description1: '',
                                description2: '',
                            },
                            technique: {
                                description1: '',
                                description2: '',
                            },
                            patch_test: {
                                description1: '',
                                description2: '',
                            },
                            product_used: {
                                description1: '',
                                description2: '',
                            },
                            after_care: {
                                description1: '',
                                description2: '',
                            },
                        };

                        if (data.hair_client_desires && data.hair_client_desires.length) {

                            data.hair_client_desires.forEach(item => {

                                if (app.client_desire[item.hair_client_desires_title]) {

                                    app.client_desire[item.hair_client_desires_title] = {
                                        description1: item.hair_client_desires_description1 || '',
                                        description2: item.hair_client_desires_description2 || '',
                                    };

                                }

                            });

                        }



                        this.$modal.show('EditHairModal');
                    } else {
                        this.$toast.error('Invalid consultation type');
                    }
                },




                approveHairConsultation(id) {
                    if (!id) return;
                    let url = this.base_url + 'hair-consultation/approve/' + id;
                    axios.post(url, {

                        client_name: this.client_name,
                        client_mobile: this.client_mobile,
                        client_age: this.client_age,
                        date: this.date,
                        client_phone: this.client_phone,
                        stylist_name: this.stylist_name,
                        designation: this.designation,
                        primary_analysis: this.primary_analysis,
                        hair_history: this.hair_history,
                        client_desire: this.client_desire,

                    }).then((response) => {

                        if (response.data.success) {

                            this.$toast.success(response.data.message ||
                                'Consultation updated successfully');

                            this.$modal.hide('EditHairModal');

                            this.getConsultationList(this.pagination.current_page);

                        } else {

                            this.$toast.error(response.data.message || 'Failed to update consultation');
                        }

                    }).catch((error) => {

                        console.error('Update error:', error);

                        this.$toast.error('An error occurred while updating consultation');

                    })
                },


                approveSkinConsultation(id) {

                    if (!id) return;

                    let url = this.base_url + 'skin-consultation/approve/' + id;

                    axios.post(url, {

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

                    }).then((response) => {

                        if (response.data.success) {

                            this.$toast.success(
                                response.data.message || 'Consultation updated successfully'
                            );

                            this.$modal.hide('EditSkinModal');

                            this.getConsultationList(this.pagination.current_page);

                        } else {

                            this.$toast.error(
                                response.data.message || 'Failed to update consultation'
                            );
                        }

                    }).catch((error) => {

                        console.error('Update error:', error);

                        this.$toast.error(
                            'An error occurred while updating consultation'
                        );

                    });
                },

                changePage: function(page) {
                    this.pagination.current_page = page;
                    this.getConsultationList(page);
                },
                getConsultationList: function(page = 1) {
                    let url = "{{ route('consultation.vue-list') }}";
                    this.loading = true;
                    this.pagination.current_page = page;
                    axios.post(url, {
                        "page": page,
                        "page_length": this.page_length,
                        "search": this.search,
                        "client_name": this.filter_client_name,
                        "mobile": this.filter_client_mobile,
                        "type": this.filter_type,
                        "approve_status": this.filter_status,
                        "start_date": this.filter_start_date,
                        "end_date": this.filter_end_date,
                    }).then(function(response) {
                        const payload = response.data || {};
                        const pager = payload.data || {};
                        app.ConsultationList = pager.data || [];
                        app.pagination = payload.pagination || {
                            total: 0,
                            per_page: 100,
                            from: 1,
                            to: 0,
                            current_page: 1,
                            last_page: 1
                        };
                        app.loading = false;
                    }).catch(function(error) {
                        console.error('Consultation list error:', error);
                        app.ConsultationList = [];
                        app.loading = false;
                        if (app.$toast) {
                            app.$toast.error('Failed to load consultation list');
                        }
                    });
                },
                exportToPDF: function() {
                    const url = "{{ route('consultation-report.export-to-pdf') }}" +
                        "?client_name=" + encodeURIComponent(this.filter_client_name || '') +
                        "&mobile=" + encodeURIComponent(this.filter_client_mobile || '') +
                        "&type=" + encodeURIComponent(this.filter_type || '') +
                        "&approve_status=" + encodeURIComponent(this.filter_status || '') +
                        "&start_date=" + encodeURIComponent(this.filter_start_date || '') +
                        "&end_date=" + encodeURIComponent(this.filter_end_date || '');
                    window.open(url, '_blank');
                },
                resetFilter: function() {
                    var range = currentMonthRange();
                    this.filter_client_name = '';
                    this.filter_client_mobile = '';
                    this.filter_type = '';
                    this.filter_status = '';
                    this.filter_start_date = range.start;
                    this.filter_end_date = range.end;
                    this.getConsultationList(1);
                },
                isChecked(title, option) {
                    if (!this.selectedConsultation) return false;
                    let values = [];
                    if (title.includes('skin_') && this.selectedConsultation.skin_analyses) {
                        const analysis = this.selectedConsultation.skin_analyses.find(item => item
                            .skin_analyses_title === title);
                        values = this.safeJsonArray(analysis ? analysis.skin_analyses_values : []);
                    } else if (title.includes('water_') || title.includes('sleep') || title.includes(
                            'stress') ||
                        title.includes('sun_exposure') || title.includes('smoke') || title.includes(
                            'drinks')) {
                        const lifestyle = (this.selectedConsultation.skin_lifestyles || []).find(item => item
                            .skin_lifestyle_title === title);
                        values = this.safeJsonArray(lifestyle ? lifestyle.skin_lifestyle_values : []);
                    } else {
                        const history = (this.selectedConsultation.skin_histories || []).find(item => item
                            .skin_history_title === title);
                        values = this.safeJsonArray(history ? history.skin_history_values : []);
                    }
                    return values.map(v => String(v).toLowerCase()).includes(String(option).toLowerCase());
                },
                getConclusion(title) {
                    if (!this.selectedConsultation || !this.selectedConsultation.skin_conclusions)
                        return 'N/A';
                    const conclusion = this.selectedConsultation.skin_conclusions.find(item => item
                        .skin_conclusion_title === title);
                    return conclusion ? conclusion.skin_conclusion_description : 'N/A';
                },

                printSkinConsultation: function(data) {
                    var url = "{{ url('/consultation/print/skin') }}/" + data.id + "?autoprint=1";
                    var printWindow = window.open(url, "_blank");
                    if (!printWindow) {
                        this.$toast.error("{{ __('Please allow pop-ups to print this consultation.') }}");
                    }
                },

                printHairConsultation: function(data) {
                    var url = "{{ url('/consultation/print/hair') }}/" + data.id + "?autoprint=1";
                    var printWindow = window.open(url, "_blank");
                    if (!printWindow) {
                        this.$toast.error("{{ __('Please allow pop-ups to print this consultation.') }}");
                    }
                },

                // New methods for hair consultation
                isHairAnalysisChecked(title, option) {
                    if (!this.selectedConsultation || !this.selectedConsultation.hair_analyses)
                        return false;
                    const analysis = this.selectedConsultation.hair_analyses.find(item => item
                        .hair_analyses_title === title);
                    const values = this.safeJsonArray(analysis ? analysis.hair_analyses_values : []);
                    return values.map(v => String(v).toLowerCase()).includes(String(option).toLowerCase());
                },
                isHairHistoryChecked(title, option) {
                    if (!this.selectedConsultation || !this.selectedConsultation.hair_histories)
                        return false;
                    const history = this.selectedConsultation.hair_histories.find(item => item
                        .hair_history_title === title);
                    let values = [];
                    if (history) {
                        values = this.safeJsonArray(history.hair_history_values);
                        if (Array.isArray(values) && values.length > 0 && typeof values[0] === 'object') {
                            values = values.filter(item => item.selected === true || item.selected === 'true' || item.checked === true)
                                .map(item => item.type);
                        }
                    }
                    return values.map(v => String(v).toLowerCase()).includes(String(option).toLowerCase());
                },
                getHairHistoryDescription(title, option) {
                    if (!this.selectedConsultation || !this.selectedConsultation.hair_histories)
                        return '';
                    const history = this.selectedConsultation.hair_histories.find(item => item
                        .hair_history_title === title);
                    if (!history) return '';
                    const values = this.safeJsonArray(history.hair_history_values);
                    if (Array.isArray(values) && values.length > 0 && typeof values[0] === 'object') {
                        const match = values.find(item => String(item.type || '').toLowerCase() === String(option).toLowerCase());
                        return match ? (match.description || '') : '';
                    }
                    const descriptions = this.safeJsonArray(history.hair_history_description);
                    const idx = values.findIndex(v => String(v).toLowerCase() === String(option).toLowerCase());
                    return idx >= 0 ? (descriptions[idx] || '') : '';
                },
                getClientDesire(title, descriptionField) {
                    if (!this.selectedConsultation || !this.selectedConsultation.hair_client_desires)
                        return 'N/A';
                    const desire = this.selectedConsultation.hair_client_desires.find(item => item
                        .hair_client_desires_title === title);
                    return desire ? desire['hair_client_desires_' + descriptionField] || 'N/A' : 'N/A';
                }

            },
            mounted() {
                this.getConsultationList();
            }
        });
    </script>
@endsection

<script src="{{ asset('js/vue.min.js') }}"></script>
<script src="{{ asset('assets/vuejs/axios/axios.min.js') }}"></script>
<script src="{{ asset('assets/vuejs/ajax/libs/moment.min.js') }}"></script>
<link href="{{ asset('assets/vuejs/multi_select/vue-multiselect.min.css') }}" rel="stylesheet">
<script src="{{ asset('assets/vuejs/multi_select/vue-multiselect.min.js') }}"></script>
<link href="{{ asset('assets/vuejs/toast/vue_toast.css') }}" rel="stylesheet">
<script src="{{ asset('assets/vuejs/toast/vue_toast.js') }}"></script>
<script>
    Vue.use(VueToast, {
        position: "top-right",
        duration: 5000,
        dismissible: true
    });
</script>
<script src="{{ asset('assets/vuejs/vee_validate/veeValidate.js') }}"></script>
<script>
    Vue.use(VeeValidate);
</script>
<script>
    Vue.component("vue-multiselect", window.VueMultiselect.default);
</script>
<script src="{{ asset('assets/vuejs/infinite_scroll/vue-infinite-scroll.js') }}"></script>
<script src="{{ asset('assets/vuejs/dist/vue-js-modal.js?11') }}"></script>
