@extends('payroll.layouts.base')
<style>
    .raduis-cds .v--modal-box {
        border-radius: 28px;
    }

    .vue-multis-d-brownz .multiselect__input {
        background-color: transparent !important;
        padding: 0;
    }

    .vue-multis-d-brownz .multiselect__tags {
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

    .vue-multis-d-brownz .multiselect__input,
    .vue-multis-d-brownz .multiselect__single {
        padding: 0;
        background-color: transparent;
        margin: 0;
    }

    .employee-page .employee-filters .search-box-bar-container {
        margin-bottom: 0;
    }

    .employee-page .list-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
    }

    .employee-page .list-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .employee-page .list-meta {
        font-size: 12px;
        color: #6c757d;
    }

    .employee-page .employee-table th,
    .employee-page .employee-table td {
        vertical-align: middle;
    }

    .employee-page .emp-identity-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .employee-page .emp-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        background: #F0F0F3;
        flex-shrink: 0;
    }

    .employee-page .emp-identity-name {
        display: block;
        font-weight: 600;
        color: #1D1D1D;
    }

    .employee-page .emp-identity-meta {
        display: block;
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    .employee-page .emp-contact {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #1D1D1D;
        white-space: nowrap;
        font-size: 13px;
    }

    .employee-page .emp-contact i {
        color: #9a9a9a;
        width: 14px;
        text-align: center;
    }

    .employee-page .role-chip {
        display: inline-block;
        font-size: 11px;
        font-weight: 500;
        color: #1D1D1D;
        background: #F0F0F3;
        border-radius: 999px;
        padding: 4px 10px;
        margin: 2px 4px 2px 0;
    }

    .employee-page .status-pill {
        display: inline-flex;
        align-items: center;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        padding: 6px 12px;
        text-transform: capitalize;
        line-height: 1.2;
    }

    .employee-page .status-pill.is-active {
        color: #199900;
        background: rgba(25, 153, 0, 0.15);
    }

    .employee-page .status-pill.is-inactive {
        color: #D0342C;
        background: rgba(208, 52, 44, 0.12);
    }

    .employee-page .emp-ledgers {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 180px;
    }

    .employee-page .emp-ledger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 6px 8px;
        border-radius: 10px;
        background: #F7F7F9;
        color: #1D1D1D;
        text-decoration: none !important;
    }

    .employee-page .emp-ledger:hover {
        background: #F0F0F3;
        color: #1D1D1D;
    }

    .employee-page .emp-ledger-copy {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .employee-page .emp-ledger-label {
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #6c757d;
        line-height: 1.2;
    }

    .employee-page .emp-ledger-name {
        font-size: 12px;
        color: #1D1D1D;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 160px;
    }

    .employee-page .emp-ledger-balance {
        font-size: 12px;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
        color: #1D1D1D;
    }

    .employee-page .manage-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .employee-page .manage-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #F0F0F3;
        color: #1D1D1D;
        text-decoration: none !important;
    }

    .employee-page .manage-btn:hover {
        background: #1D1D1D;
        color: #E6CD7F;
    }

    .employee-page .empty-state {
        text-align: center;
        padding: 36px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }

    .employee-page .empty-state i {
        font-size: 28px;
        margin-bottom: 10px;
        color: #b7b7b7;
    }

    .salary-modal.v--modal-box,
    .v--modal-box.salary-modal {
        max-width: min(1100px, 96vw) !important;
        width: min(1100px, 96vw) !important;
        max-height: min(92vh, 900px) !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column;
        border-radius: 20px !important;
    }

    .salary-modal .modal-dialog {
        margin: 0;
        max-width: 100%;
        width: 100%;
        height: 100%;
    }

    .salary-modal .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        max-height: min(92vh, 900px);
        height: 100%;
        box-shadow: none;
    }

    .salary-modal .modal-header {
        border-bottom: none;
        padding: 16px 20px 10px;
        align-items: flex-start;
        flex-shrink: 0;
    }

    .salary-modal .modal-title {
        color: #1D1D1D;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .salary-modal .salary-subtitle {
        margin: 0;
        font-size: 13px;
        color: #6c757d;
    }

    .salary-modal .modal-body {
        padding: 4px 20px 16px;
        overflow-y: auto;
        overflow-x: hidden;
        flex: 1 1 auto;
        max-height: calc(min(92vh, 900px) - 72px);
        -webkit-overflow-scrolling: touch;
    }

    .salary-modal .salary-section {
        background: #F7F7F9;
        border-radius: 14px;
        padding: 12px 12px 4px;
        margin-bottom: 10px;
    }

    .salary-modal .salary-section-title {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #6c757d;
        margin: 0 0 8px;
    }

    .salary-modal .form-group {
        margin-bottom: 10px;
    }

    .salary-modal .form-label {
        font-size: 13px;
        color: #4a4a4a;
        margin-bottom: 4px;
    }

    .salary-modal .form-control {
        height: 38px;
        padding: 6px 12px;
        font-size: 13px;
    }

    .salary-modal .modal-footer {
        border-top: none;
        padding: 4px 0 10px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        flex-shrink: 0;
        position: sticky;
        bottom: 0;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.75) 0%, #fff 40%);
        z-index: 2;
    }

    .salary-modal .salary-history {
        margin-top: 4px;
        padding-top: 2px;
    }

    .salary-modal .salary-history-title {
        font-size: 14px;
        font-weight: 600;
        color: #1D1D1D;
        margin-bottom: 8px;
    }

    .salary-modal .salary-history .table-responsive {
        max-height: 280px;
        overflow: auto;
        border: 1px solid #eee;
        border-radius: 12px;
        background: #fff;
    }

    .salary-modal .salary-table {
        margin-bottom: 0;
        min-width: 980px;
    }

    .salary-modal .salary-table thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: #fff;
        box-shadow: 0 1px 0 #eee;
        white-space: nowrap;
        font-size: 12px;
    }

    .salary-modal .salary-table th,
    .salary-modal .salary-table td {
        vertical-align: middle;
        padding: 8px 10px;
        font-size: 12px;
    }

    .salary-modal .amount-cell {
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
        font-weight: 600;
        color: #1D1D1D;
    }

    .salary-modal .net-amount {
        font-size: 13px;
        font-weight: 700;
        color: #1D1D1D;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .salary-modal .comp-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 2px 8px;
        font-size: 11px;
        color: #4a4a4a;
        min-width: 140px;
    }

    .salary-modal .comp-grid span {
        color: #6c757d;
    }

    .salary-modal .time-cell {
        white-space: nowrap;
        font-size: 11px;
        color: #4a4a4a;
        line-height: 1.35;
    }

    .salary-modal .salary-empty {
        text-align: center;
        padding: 20px 12px;
        background: #F7F7F9;
        border-radius: 14px;
        color: #6c757d;
        font-size: 13px;
    }

    @media (max-width: 768px) {
        .salary-modal.v--modal-box,
        .v--modal-box.salary-modal {
            max-width: 96vw !important;
            width: 96vw !important;
            max-height: 94vh !important;
        }

        .salary-modal .modal-body {
            padding: 4px 14px 14px;
            max-height: calc(94vh - 64px);
        }

        .salary-modal .salary-history .table-responsive {
            max-height: 220px;
        }
    }

    .employee-modal .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }

    .employee-modal .modal-header {
        border-bottom: none;
        padding: 18px 20px 8px;
        align-items: flex-start;
    }

    .employee-modal .modal-title {
        color: #1D1D1D;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .employee-modal .emp-subtitle {
        margin: 0;
        font-size: 13px;
        color: #6c757d;
    }

    .employee-modal .modal-body {
        padding: 8px 20px 12px;
    }

    .employee-modal .emp-section {
        background: #F7F7F9;
        border-radius: 14px;
        padding: 14px 14px 6px;
        margin-bottom: 12px;
    }

    .employee-modal .emp-section-title {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #6c757d;
        margin: 0 0 10px;
    }

    .employee-modal .form-group {
        margin-bottom: 12px;
    }

    .employee-modal .form-label {
        font-size: 13px;
        color: #4a4a4a;
        margin-bottom: 4px;
        font-weight: 500;
    }

    .employee-modal .modal-footer {
        border-top: none;
        padding: 4px 0 10px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .employee-modal .photo-preview {
        margin-top: 10px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        overflow: hidden;
        background: #fff;
        border: 1px solid #e6e6ea;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9a9a9a;
    }

    .employee-modal .photo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .employee-modal .photo-preview .photo-placeholder {
        font-size: 22px;
    }
</style>
@section('content')
    <div id="app" class="employee-page">

        <div class="fbox-shadow mt-2 fd-card respo-margin-top employee-filters">
            <div class="card-body">
                <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3" id="voco-ajax-modal-label">
                    {{ __('Employee Management') }}
                </h5>

                <div class="row align-items-center br-row-gap-2">
                    <div class="col-lg-3 col-md-6">
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
                            <input type="text" class="form-control brawnz-search-bar-in" v-model="filter_name"
                                @keyup.enter="getEmployeeList()"
                                placeholder="{{ __('Search name / code / mobile') }}">
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <select class="form-control brawnz-search-bar-in" v-model="filter_role"
                                @change="getEmployeeList()">
                                <option value="">{{ __('All Roles') }}</option>
                                <option v-for="role in roleList" :key="role.id" :value="role.name">
                                    @{{ role.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <select class="form-control brawnz-search-bar-in" v-model="filter_status"
                                @change="getEmployeeList()">
                                <option value="all">{{ __('All Status') }}</option>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-primary btn_dark-x mr-2"
                            v-on:click="getEmployeeList()">
                            <i class="fa fa-search"></i> {{ __('Search') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                            v-on:click="resetFilter()">
                            <i class="fa fa-refresh"></i> {{ __('Reset') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="fbox-shadow mt-3 fd-card respo-margin-top">

            <div class="m-3" style="overflow: auto">
                <div class="list-toolbar">
                    <div>
                        <p class="list-title">{{ __('Employees') }}</p>
                        <p class="list-meta mb-0" v-if="pagination.total">
                            @{{ pagination.total }} {{ __('result(s)') }}
                        </p>
                    </div>
                    <div class="d-flex justify-content-end">
                        @can('emp_create')
                            <button type="button" class="btn_dark-x" v-on:click="popupModal(0)">
                                <i class="fa fa-plus"></i> {{ __('New') }}
                            </button>
                        @endcan
                    </div>
                </div>

                    {{-- Employee Modal --}}
                    <modal width="920" height="auto" :scrollable="true" :adaptive="true"
                        class="brownz-custom-popup raduis-cds employee-modal" name="employeeModal">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div>
                                        <h4 class="modal-title">@{{ modelHeading }}</h4>
                                        <p class="emp-subtitle" v-if="emp_id && emp_code">
                                            {{ __('Code') }}: @{{ emp_code }}
                                        </p>
                                    </div>
                                    <button type="button" class="bootbox-close-button close" v-on:click="hide"
                                        data-dismiss="modal" aria-label="Close">
                                        ×
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form @submit.prevent="validateBeforeSubmit" v-if="!formSubmitted" method="post">
                                        <div class="emp-section">
                                            <p class="emp-section-title">{{ __('Account & Role') }}</p>
                                            <div class="row">
                                                <div class="col-md-4 col-sm-12 vue-multis-d-brownz form-group">
                                                    <label for="role_ids" class="form-label">
                                                        {{ __('Roles') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <vue-multiselect track-by="id" label="name" v-model="role_ids"
                                                        :options="roleList" :multiple="true"
                                                        placeholder="{{ __('Select roles') }}">
                                                    </vue-multiselect>
                                                    <span v-if="formErrors['role_ids']"
                                                        class="error text-danger">@{{ formErrors['role_ids'] }}</span>
                                                    <p class="text-danger" v-if="errors.has('role_ids')">
                                                        @{{ errors.first('role_ids') }}
                                                    </p>
                                                </div>

                                                <div v-if="emp_id == 0" class="col-md-4 col-sm-12 form-group">
                                                    <label for="username" class="form-label">
                                                        {{ __('User Name') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" v-model="username"
                                                        v-validate.initial="username" data-rules="required"
                                                        id="username" placeholder="{{ __('User Name') }}">
                                                    <span v-if="formErrors['username']"
                                                        class="error text-danger">@{{ formErrors['username'] }}</span>
                                                    <p class="text-danger" v-if="errors.has('username')">
                                                        @{{ errors.first('username') }}
                                                    </p>
                                                </div>

                                                <div v-if="emp_id == 0" class="col-md-4 col-sm-12 form-group">
                                                    <label for="password" class="form-label">
                                                        {{ __('Password') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="password" class="form-control" v-model="password"
                                                        v-validate.initial="password" data-rules="required"
                                                        id="password" placeholder="{{ __('Password') }}">
                                                    <span v-if="formErrors['password']"
                                                        class="error text-danger">@{{ formErrors['password'] }}</span>
                                                    <p class="text-danger" v-if="errors.has('password')">
                                                        @{{ errors.first('password') }}
                                                    </p>
                                                </div>

                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="status" class="form-label">
                                                        {{ __('Status') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <select v-model="status" class="form-control">
                                                        <option value="active">{{ __('Active') }}</option>
                                                        <option value="inactive">{{ __('Inactive') }}</option>
                                                    </select>
                                                    <span v-if="formErrors['status']"
                                                        class="error text-danger">@{{ formErrors['status'] }}</span>
                                                </div>

                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="shift" class="form-label">
                                                        {{ __('Work Shift') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <select v-model="shift" class="form-control"
                                                        v-validate.initial="shift" data-rules="required">
                                                        <option value="">{{ __('Select shift') }}</option>
                                                        <option v-for="data in shiftList" :key="data.shift_id"
                                                            v-bind:value="data.shift_id">
                                                            @{{ data.title }}
                                                        </option>
                                                    </select>
                                                    <span v-if="formErrors['shift']"
                                                        class="error text-danger">@{{ formErrors['shift'] }}</span>
                                                    <p class="text-danger" v-if="errors.has('shift')">
                                                        @{{ errors.first('shift') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="emp-section">
                                            <p class="emp-section-title">{{ __('Personal Details') }}</p>
                                            <div class="row">
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="name" class="form-label">
                                                        {{ __('Name') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" v-model="name"
                                                        v-validate.initial="name" data-rules="required"
                                                        placeholder="{{ __('Name') }}" id="name">
                                                    <span v-if="formErrors['name']"
                                                        class="error text-danger">@{{ formErrors['name'] }}</span>
                                                    <p class="text-danger" v-if="errors.has('name')">
                                                        @{{ errors.first('name') }}
                                                    </p>
                                                </div>

                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="designation" class="form-label">
                                                        {{ __('Designation') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" v-model="designation"
                                                        v-validate.initial="designation" data-rules="required"
                                                        id="designation" placeholder="{{ __('Designation') }}">
                                                    <span v-if="formErrors['designation']"
                                                        class="error text-danger">@{{ formErrors['designation'] }}</span>
                                                    <p class="text-danger" v-if="errors.has('designation')">
                                                        @{{ errors.first('designation') }}
                                                    </p>
                                                </div>

                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="joing_date" class="form-label">
                                                        {{ __('Joining Date') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="date" class="form-control" v-model="joing_date"
                                                        v-validate.initial="joing_date" data-rules="required"
                                                        id="joing_date">
                                                    <span v-if="formErrors['joing_date']"
                                                        class="error text-danger">@{{ formErrors['joing_date'] }}</span>
                                                    <p class="text-danger" v-if="errors.has('joing_date')">
                                                        @{{ errors.first('joing_date') }}
                                                    </p>
                                                </div>

                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="mobile" class="form-label">
                                                        {{ __('Mobile Number') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" inputmode="numeric" pattern="[0-9]*"
                                                        maxlength="10" class="form-control" v-model="mobile"
                                                        v-validate.initial="mobile" data-rules="required|digits:10"
                                                        id="mobile" placeholder="{{ __('10-digit mobile number') }}"
                                                        @input="onMobileInput">
                                                    <span v-if="formErrors['mobile']"
                                                        class="error text-danger">@{{ formErrors['mobile'] }}</span>
                                                    <p class="text-danger" v-if="errors.has('mobile')">
                                                        @{{ errors.first('mobile') }}
                                                    </p>
                                                </div>

                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="email" class="form-label">
                                                        {{ __('Email') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="email" class="form-control" v-model="email"
                                                        id="email" placeholder="{{ __('Enter Email') }}"
                                                        v-validate.initial="email" data-rules="required"
                                                        @blur="validateEmail">
                                                    <span v-if="formErrors['email']"
                                                        class="error text-danger">@{{ formErrors['email'] }}</span>
                                                    <p class="text-danger" v-if="errors.has('email')">
                                                        @{{ errors.first('email') }}
                                                    </p>
                                                </div>

                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="dob" class="form-label">{{ __('DOB') }}</label>
                                                    <input type="date" class="form-control" v-model="dob" id="dob">
                                                </div>

                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="gender" class="form-label">{{ __('Gender') }}</label>
                                                    <select v-model="gender" class="form-control">
                                                        <option value="">{{ __('Select') }}</option>
                                                        {{ \Facades\App\Models\User::genderDropdown() }}
                                                    </select>
                                                </div>

                                                <div class="col-md-8 col-sm-12 form-group">
                                                    <label for="address" class="form-label">{{ __('Address') }}</label>
                                                    <textarea class="form-control" id="address" v-model="address" rows="2"
                                                        placeholder="{{ __('Address') }}"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="emp-section">
                                            <p class="emp-section-title">{{ __('Bank & Payroll') }}</p>
                                            <div class="row">
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="acc_no" class="form-label">{{ __('Account No') }}</label>
                                                    <input type="text" class="form-control" v-model="acc_no"
                                                        id="acc_no" placeholder="{{ __('Enter Account No') }}">
                                                </div>
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="ifsc_code" class="form-label">{{ __('IFSC Code') }}</label>
                                                    <input type="text" class="form-control" v-model="ifsc_code"
                                                        id="ifsc_code" placeholder="{{ __('Enter IFSC Code') }}">
                                                </div>
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="branch" class="form-label">{{ __('Branch') }}</label>
                                                    <input type="text" class="form-control" v-model="branch"
                                                        id="branch" placeholder="{{ __('Enter Branch') }}">
                                                </div>
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="payment_mode" class="form-label">
                                                        {{ __('Payment Mode') }}
                                                    </label>
                                                    <select v-model="payment_mode" class="form-control">
                                                        <option value="">{{ __('Select') }}</option>
                                                        <option value="bank">{{ __('Bank') }}</option>
                                                        <option value="cash">{{ __('Cash') }}</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="hourly_basic_pay" class="form-label">
                                                        {{ __('Hourly Basic Pay') }}
                                                    </label>
                                                    <input type="number" class="form-control"
                                                        v-model="hourly_basic_pay" id="hourly_basic_pay"
                                                        placeholder="{{ __('Enter Hourly Pay') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="emp-section">
                                            <p class="emp-section-title">{{ __('Photo & Location') }}</p>
                                            <div class="row">
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="image" class="form-label">
                                                        {{ __('Profile Photo') }}
                                                    </label>
                                                    <input type="file" @change="uploadImage" name="image"
                                                        class="form-control" accept="image/*">
                                                    <div class="photo-preview">
                                                        <img v-if="imageSrc" :src="imageSrc" alt="{{ __('Profile') }}"
                                                            v-on:error="imageSrc = ''">
                                                        <i v-else class="fa fa-user photo-placeholder"
                                                            aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="branch_latitude" class="form-label">
                                                        {{ __('Branch Latitude') }}
                                                    </label>
                                                    <input type="number" step="any" class="form-control"
                                                        v-model="branch_latitude" id="branch_latitude"
                                                        placeholder="{{ __('Optional') }}">
                                                </div>
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="branch_longitude" class="form-label">
                                                        {{ __('Branch Longitude') }}
                                                    </label>
                                                    <input type="number" step="any" class="form-control"
                                                        v-model="branch_longitude" id="branch_longitude"
                                                        placeholder="{{ __('Optional') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="brownz-btn-yellow-d" type="button" v-on:click="hide">
                                                {{ __('Cancel') }}
                                            </button>
                                            <button type="submit" v-bind:disabled="submit_button_flag"
                                                class="btn_dark-x">
                                                <i class="fa fa-check"></i> {{ __('Submit') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </modal>

                    {{-- Salary Modal --}}
                    <modal width="1100" height="auto" :max-height="900" :scrollable="true" :adaptive="true"
                        class="brownz-custom-popup salary-modal" name="salaryModal">
                        <div class="modal-content">
                                <div class="modal-header">
                                    <div>
                                        <h4 class="modal-title" id="modelHeading">@{{ modelHeading }}</h4>
                                        <p class="salary-subtitle" v-if="salary_employee_name">
                                            {{ __('Employee') }}: @{{ salary_employee_name }}
                                            <span v-if="salary_employee_code"> · @{{ salary_employee_code }}</span>
                                        </p>
                                    </div>
                                    <button type="button" class="bootbox-close-button close" v-on:click="hide1"
                                        data-dismiss="modal" aria-label="Close">
                                        ×
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form @submit.prevent="validateSalaryBeforeSubmit" method="post">
                                        <div class="salary-section">
                                            <p class="salary-section-title">{{ __('Earnings') }}</p>
                                            <div class="row">
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="basic_salary" class="form-label">
                                                        {{ __('Basic Salary') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" class="form-control" v-model="basic_salary"
                                                        v-validate.initial="basic_salary" data-rules="required"
                                                        id="basic_salary" placeholder="{{ __('Enter Basic Salary') }}">
                                                    <span v-if="formErrors['basic_salary']"
                                                        class="error text-danger">@{{ formErrors['basic_salary'] }}</span>
                                                    <p class="text-danger" v-if="errors.has('basic_salary')">
                                                        @{{ errors.first('basic_salary') }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="TA" class="form-label">{{ __('TA') }}</label>
                                                    <input type="number" class="form-control" v-model="TA" id="TA"
                                                        placeholder="0">
                                                </div>
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="DA" class="form-label">{{ __('DA') }}</label>
                                                    <input type="number" class="form-control" v-model="DA" id="DA"
                                                        placeholder="0">
                                                </div>
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="HRA" class="form-label">{{ __('HRA') }}</label>
                                                    <input type="number" class="form-control" v-model="HRA" id="HRA"
                                                        placeholder="0">
                                                </div>
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="special_allowance" class="form-label">
                                                        {{ __('Special Allowance') }}
                                                    </label>
                                                    <input type="number" class="form-control" v-model="special_allowance"
                                                        id="special_allowance" placeholder="0">
                                                </div>
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="target_value" class="form-label">
                                                        {{ __('Target Value') }}
                                                    </label>
                                                    <input type="number" class="form-control" v-model="target_value"
                                                        id="target_value" placeholder="0">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="salary-section">
                                            <p class="salary-section-title">{{ __('Deductions & Status') }}</p>
                                            <div class="row">
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="TDS" class="form-label">{{ __('TDS (%)') }}</label>
                                                    <input type="number" class="form-control" v-model="TDS" id="TDS"
                                                        placeholder="0">
                                                </div>
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="professional_tax" class="form-label">
                                                        {{ __('Professional Tax (%)') }}
                                                    </label>
                                                    <input type="number" class="form-control" v-model="professional_tax"
                                                        id="professional_tax" placeholder="0">
                                                </div>
                                                <div class="col-md-4 col-sm-12 form-group">
                                                    <label for="salary_status" class="form-label">
                                                        {{ __('Status') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <select v-model="salary_status" class="form-control"
                                                        v-validate.initial="salary_status" data-rules="required">
                                                        <option value="active">{{ __('Active') }}</option>
                                                        <option value="inactive">{{ __('Inactive') }}</option>
                                                    </select>
                                                    <span v-if="formErrors['salary_status']"
                                                        class="error text-danger">@{{ formErrors['salary_status'] }}</span>
                                                    <p class="text-danger" v-if="errors.has('salary_status')">
                                                        @{{ errors.first('salary_status') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="brownz-btn-yellow-d clear-fields" type="button"
                                                v-on:click="hide1">{{ __('Cancel') }}</button>
                                            <button type="submit" v-bind:disabled="submit_button_flag"
                                                class="btn_dark-x">
                                                <i class="fa fa-check"></i> {{ __('Submit') }}
                                            </button>
                                        </div>
                                    </form>

                                    <div class="salary-history">
                                        <p class="salary-history-title">{{ __('Salary History') }}</p>

                                        <div class="table-responsive" v-if="salaryList.length">
                                            <table class="table brownz-table-list-v1 salary-table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 48px;">#</th>
                                                        <th>{{ __('Basic') }}</th>
                                                        <th>{{ __('Components') }}</th>
                                                        <th>{{ __('Prof. Tax') }}</th>
                                                        <th>{{ __('Target') }}</th>
                                                        <th>{{ __('Net Salary') }}</th>
                                                        <th>{{ __('Status') }}</th>
                                                        <th>{{ __('Added Time') }}</th>
                                                        <th>{{ __('Updated Time') }}</th>
                                                        <th class="hidden-print text-center" style="width: 56px;">
                                                            {{ __('Edit') }}
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(data, index) in salaryList" :key="data.salary_id">
                                                        <td>@{{ index + 1 }}</td>
                                                        <td class="amount-cell">₹ @{{ data.basic_salary }}</td>
                                                        <td>
                                                            <div class="comp-grid">
                                                                <div><span>TA</span> ₹ @{{ data.TA }}</div>
                                                                <div><span>DA</span> ₹ @{{ data.DA }}</div>
                                                                <div><span>HRA</span> ₹ @{{ data.HRA }}</div>
                                                                <div><span>Allow.</span> ₹ @{{ data.special_allowance }}</div>
                                                                <div><span>TDS</span> ₹ @{{ data.TDS_amount }}</div>
                                                            </div>
                                                        </td>
                                                        <td class="amount-cell">₹ @{{ data.professional_tax_amount }}</td>
                                                        <td class="amount-cell">₹ @{{ data.target_value }}</td>
                                                        <td class="net-amount">₹ @{{ data.net_salary }}</td>
                                                        <td>
                                                            <span class="status-pill"
                                                                :class="data.status === 'active' ? 'is-active' : 'is-inactive'">
                                                                @{{ data.status }}
                                                            </span>
                                                        </td>
                                                        <td class="time-cell">
                                                            @{{ formatDateTime(data.created_at) }}
                                                        </td>
                                                        <td class="time-cell">
                                                            @{{ formatDateTime(data.updated_at) }}
                                                        </td>
                                                        <td class="hidden-print text-center">
                                                            @can('salary_edit')
                                                                <a href="javascript:void(0)" class="manage-btn"
                                                                    title="{{ __('Edit') }}"
                                                                    v-on:click="salaryEdit(data)">
                                                                    <i aria-hidden="true"
                                                                        class="fa fa-pencil-square-o"></i>
                                                                </a>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="salary-empty" v-else>
                                            {{ __('No salary records yet for this employee.') }}
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </modal>

                <table class="table brownz-table-list-v1 employee-table" v-if="employeeList.length">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 56px;">#</th>
                            <th scope="col">{{ __('Employee') }}</th>
                            <th scope="col">{{ __('Designation') }}</th>
                            <th scope="col">{{ __('Roles') }}</th>
                            <th scope="col">{{ __('Join Date') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col">{{ __('Ledger') }}</th>
                            <th scope="col" class="hidden-print text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(data, index) in employeeList" :key="data.emp_id">
                            <td>@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</td>
                            <td>
                                <div class="emp-identity-cell">
                                    <img class="emp-avatar"
                                        v-if="data.user && data.user.photo_src_url"
                                        v-bind:src="data.user.photo_src_url"
                                        alt="">
                                    <img class="emp-avatar"
                                        v-else-if="data.user && data.user.profile_photo_path"
                                        v-bind:src="'/storage/' + data.user.profile_photo_path"
                                        alt="">
                                    <div>
                                        <span class="emp-identity-name">@{{ data.name || '—' }}</span>
                                        <span class="emp-identity-meta">@{{ data.emp_code || '—' }}</span>
                                        <div class="emp-contact mt-1" v-if="data.user && data.user.mobile">
                                            <i class="fa fa-phone" aria-hidden="true"></i>
                                            <a :href="'tel:' + data.user.mobile">@{{ data.user.mobile }}</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>@{{ data.designation || '—' }}</td>
                            <td>
                                <template v-if="data.assigned_roles && data.assigned_roles.length">
                                    <span class="role-chip" v-for="(roleName, rIndex) in data.assigned_roles"
                                        :key="rIndex">@{{ roleName }}</span>
                                </template>
                                <span v-else class="text-muted">—</span>
                            </td>
                            <td>@{{ data.join_date || '—' }}</td>
                            <td>
                                <span class="status-pill"
                                    :class="data.status === 'active' ? 'is-active' : 'is-inactive'">
                                    @{{ data.status || '—' }}
                                </span>
                            </td>
                            <td>
                                <div class="emp-ledgers" v-if="data.ledgers && data.ledgers.length">
                                    <a class="emp-ledger" v-for="ledger in data.ledgers" :key="ledger.type"
                                        :href="ledger.view_url" :title="ledger.name">
                                        <span class="emp-ledger-copy">
                                            <span class="emp-ledger-label">@{{ ledger.label }}</span>
                                            <span class="emp-ledger-name">@{{ ledger.name }}</span>
                                        </span>
                                        <span class="emp-ledger-balance">₹ @{{ Number(ledger.balance || 0).toFixed(2) }}</span>
                                    </a>
                                </div>
                                <span v-else class="text-muted">—</span>
                            </td>
                            <td class="hidden-print">
                                <div class="manage-actions justify-content-center">
                                    @can('emp_edit')
                                        <a href="javascript:void(0)" class="manage-btn" title="{{ __('Edit') }}"
                                            v-on:click="popupModal(data)">
                                            <i aria-hidden="true" class="fa fa-pencil-square-o"></i>
                                        </a>
                                    @endcan
                                    @can('salary_create')
                                        <button type="button" class="brownz-btn-yellow-d"
                                            v-on:click="salaryPopup(data)" style="padding: 5px 10px">
                                            <i class="fa fa-plus"></i> {{ __('Salary') }}
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="empty-state" v-else>
                    <i class="fa fa-users" aria-hidden="true"></i>
                    <div>{{ __('No employees found for the selected filters.') }}</div>
                </div>

            </div>

            {{-- pagination --}}
            <nav aria-label="pagination">
                <ul class="pagination justify-content-end">
                    <li class="page-item" v-if="pagination.current_page > 1">
                        <a class="page-link" href="#" aria-label="Previous"
                            @click.prevent="changePage(pagination.current_page - 1)">
                            <span aria-hidden="true">«</span>
                        </a>
                    </li>
                    <li class="page-item" v-for="page in pagesNumber"
                        v-bind:class="[ page == isActived ? 'active' : '']">
                        <a class="page-link" href="#" @click.prevent="changePage(page)">@{{ page }}</a>
                    </li>
                    <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                        <a class="page-link" href="#" aria-label="Next"
                            @click.prevent="changePage(pagination.current_page + 1)">
                            <span aria-hidden="true">»</span>
                        </a>
                    </li>
                </ul>
            </nav>
            {{-- end --}}

        </div>
    </div>



    <script>
        Vue.use(window["vue-js-modal"].default);
        var app = new Vue({
            el: '#app',

            data: {

                loading: false,

                base_url: "{{ '/' }}",

                employeeList: [],
                shiftList: [],
                roleList: [],
                role_ids: [],
                salaryList: [],

                emp_id: '',
                username: '',
                password: '',
                name: '',
                designation: '',
                shift: '',
                joing_date: '',
                mobile: '',
                email: '',
                dob: '',
                gender: '',
                status: 'active',
                acc_no: '',
                ifsc_code: '',
                branch: '',
                payment_mode: '',
                address: '',
                emp_code: '',
                role_ids: '',
                hourly_basic_pay: '',
                modelHeading: '',

                salary_id: '',
                basic_salary: '',
                TA: '',
                DA: '',
                HRA: '',
                special_allowance: '',
                TDS: '',
                professional_tax: '',
                target_value: '',
                // incentive_percentage: '',
                salary_status: 'active',
                salary_employee_name: '',
                salary_employee_code: '',


                filter_name: '',
                filter_status: 'active',
                filter_role: '',

                image: '',
                imageSrc: '',

                //
                formSubmitted: false,
                submit_button_flag: false,

                page_length: '100',

                pagination: {
                    total: 0,
                    per_page: 2,
                    from: 1,
                    to: 0,
                    current_page: 1
                },
                offset: 4,
                formErrors: {},
                branch_longitude: '',
                branch_latitude: '',
            },

            //

            computed: {
                isActived: function() {
                    return app.pagination.current_page;
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
                },
            },

            //

            methods: {

                formatDateTime(value) {
                    if (!value) {
                        return '—';
                    }
                    if (typeof moment !== 'undefined') {
                        return moment(value).format('DD-MM-YYYY hh:mm A');
                    }
                    var d = new Date(value);
                    if (isNaN(d.getTime())) {
                        return value;
                    }
                    return d.toLocaleString();
                },

                validateSalaryBeforeSubmit(e) {
                    console.log(this.errors);
                    this.$validator.validateAll();
                    if (!this.errors.any()) {
                        this.submitSalaryForm()
                    }
                },
                validateBeforeSubmit(e) {
                    console.log(this.errors);
                    this.$validator.validateAll();
                    if (!this.errors.any()) {
                        this.submitForm()
                    }
                },
                submitForm() {
                    this.addEditSubmitForm()
                },
                submitSalaryForm() {
                    this.addEditSubmitSalaryForm()
                },
                changePage: function(page) {
                    this.pagination.current_page = page;
                    app.getEmployeeList(page);
                },

                validateEmail() {
                    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                    if (!this.email || !emailPattern.test(this.email.trim())) {
                        this.emailError = "Please enter a valid email address";
                    } else {
                        this.emailError = "";
                    }
                },

                handleChangePage(e) {
                    console.log(this.page_length)
                    app.getEmployeeList();
                },

                // show modal
                show: function() {
                    app.$modal.show('employeeModal');
                },

                // close modal
                hide: function() {
                    app.$modal.hide('employeeModal');

                },

                hide1: function() {
                    app.$modal.hide('salaryModal');

                },

                uploadImage(e) {
                    const image = e.target.files[0];
                    const reader = new FileReader();
                    reader.readAsDataURL(image);
                    reader.onload = e => {
                        app.imageSrc = e.target.result;
                        // console.log(this.imageSrc);
                    }
                    app.image = e.target.files[0];
                },



                addEditSubmitForm: function() {

                    app.submit_button_flag = true;
                    url = "api/employee/store";

                    const config = {
                        headers: {
                            'content-type': 'multipart/form-data'
                        }
                    }

                    let formData = new FormData();
                    formData.append('emp_id', this.emp_id);
                    formData.append('name', this.name);
                    formData.append('designation', this.designation);
                    formData.append('shift', this.shift);
                    formData.append('joing_date', this.joing_date);
                    formData.append('mobile', this.normalizeEmployeeMobile(this.mobile));
                    formData.append('email', this.email);
                    formData.append('dob', this.dob);
                    formData.append('gender', this.gender);
                    formData.append('status', this.status);
                    formData.append('acc_no', this.acc_no);
                    formData.append('ifsc_code', this.ifsc_code);
                    formData.append('branch', this.branch);
                    formData.append('payment_mode', this.payment_mode);
                    formData.append('address', this.address);
                    formData.append('id', this.id);
                    formData.append('beautician_id', this.beautician_id);
                    formData.append('image', this.image);
                    formData.append(
                        'branch_longitude',
                        (this.branch_longitude === null || this.branch_longitude === undefined || this.branch_longitude === '')
                            ? ''
                            : this.branch_longitude
                    );
                    formData.append(
                        'branch_latitude',
                        (this.branch_latitude === null || this.branch_latitude === undefined || this.branch_latitude === '')
                            ? ''
                            : this.branch_latitude
                    );
                    formData.append('emp_code', this.emp_code);
                    formData.append('hourly_basic_pay', this.hourly_basic_pay);

                    if (!this.emp_id) {
                        formData.append('username', this.username);
                        formData.append('password', this.password);
                    }

                    if (Array.isArray(this.role_ids)) {
                        this.role_ids.forEach((role, index) => {
                            if (typeof role === "object" && role !== null && role.id) {
                                formData.append(`role_ids[${index}]`, role.id);
                            } else if (role) {
                                formData.append(`role_ids[${index}]`, role);
                            }
                        });
                    }

                    axios.post(url, formData, config).then(function(response) {

                        console.log(response);
                        if (response.data.errorcode == 0) {
                            app.loading = false;

                            Vue.$toast.success(response.data.errormessage, 'Success Alert');
                            app.submit_button_flag = false;
                            app.formSubmitted = true;
                            app.formErrors = {};
                            app.image = '';
                            app.imageSrc = '';
                            app.emp_id = '';
                            app.emp_code = '';
                            app.mobile = '';
                            app.email = '';
                            app.id = '';
                            app.beautician_id = '';
                            app.emp_id = '';


                            app.$modal.hide('employeeModal');
                            app.getEmployeeList(app.pagination.current_page);
                        } else {
                            app.loading = false;

                            Vue.$toast.error('Something went wrong.', 'Danger Alert');
                            app.submit_button_flag = false;
                            app.$modal.hide('employeeModal');
                            app.getEmployeeList();
                        }

                    }).catch(function(err) {
                        app.submit_button_flag = false;
                        console.log(err.response.data.errors);
                        app.formErrors = err.response.data.errors;
                    });


                },

                addEditSubmitSalaryForm: function() {

                    app.submit_button_flag = true;
                    url = "api/salary/store";

                    let formData = new FormData();
                    formData.append('salary_id', this.salary_id);
                    formData.append('emp_id', this.emp_id);
                    formData.append('basic_salary', app.basic_salary);
                    formData.append('TA', this.TA);
                    formData.append('DA', this.DA);
                    formData.append('HRA', this.HRA);
                    formData.append('special_allowance', this.special_allowance);
                    formData.append('TDS', this.TDS);
                    formData.append('professional_tax', this.professional_tax);
                    formData.append('target_value', this.target_value);
                    // formData.append('incentive_percentage', this.incentive_percentage);
                    formData.append('salary_status', this.salary_status);

                    axios.post(url, formData).then(function(response) {

                        console.log(response);
                        if (response.data.errorcode == 0) {
                            app.loading = false;

                            app.basic_salary = '';
                            app.TA = '';
                            app.DA = '';
                            app.HRA = '';
                            app.special_allowance = '';
                            app.TDS = '';
                            app.professional_tax = '';
                            app.target_value = '';
                            // app.incentive_percentage = '';
                            app.salary_status = 'active';

                            Vue.$toast.success(response.data.errormessage, 'Success Alert');
                            app.submit_button_flag = false;
                            app.formErrors = {};

                            app.getSalaryList(app.pagination.current_page);
                        } else {
                            app.loading = false;
                            Vue.$toast.error('Something went wrong.', 'Danger Alert');
                            app.basic_salary = '';
                            app.TA = '';
                            app.DA = '';
                            app.HRA = '';
                            app.special_allowance = '';
                            app.TDS = '';
                            app.professional_tax = '';
                            app.target_value = '';
                            // app.incentive_percentage = '';
                            app.salary_status = 'active';
                            app.submit_button_flag = false;
                            app.$modal.hide('salaryModal');
                            app.getSalaryList();
                        }

                    }).catch(function(err) {
                        app.submit_button_flag = false;
                        console.log(err.response.data.errors);
                        app.formErrors = err.response.data.errors;
                    });

                },

                salaryEdit: function(data) {

                    if (data.salary_id > 0) {
                        app.salary_id = data.salary_id;
                        app.emp_id = data.emp_id;
                        app.basic_salary = data.basic_salary;
                        app.TA = data.TA;
                        app.DA = data.DA;
                        app.HRA = data.HRA;
                        app.special_allowance = data.special_allowance;
                        app.TDS = data.TDS;
                        app.professional_tax = data.professional_tax;
                        app.target_value = data.target_value;
                        app.salary_status = data.status || 'active';
                        app.salary_employee_name = (data.employee && data.employee.name)
                            ? data.employee.name
                            : (app.salary_employee_name || '');
                        app.salary_employee_code = (data.employee && data.employee.emp_code)
                            ? data.employee.emp_code
                            : (app.salary_employee_code || '');
                        app.formSubmitted = false;
                        app.formErrors = {};

                        app.modelHeading = "Edit Salary";

                        app.$modal.show('salaryModal');

                    }
                },

                popupModal: function(data) {
                    if (data && data.emp_id > 0) {
                        app.emp_id = data.emp_id;
                        app.role_ids = (data.user && Array.isArray(data.user.roles))
                            ? data.user.roles.map(role => ({
                                id: role.id,
                                name: role.name
                            }))
                            : [];

                        app.shift = data.user ? data.user.shift_id : '';
                        app.name = data.name;
                        app.designation = data.designation;
                        app.joing_date = data.join_date;
                        app.username = data.user ? data.user.username : '';
                        app.password = '';
                        app.email = data.user ? (data.user.email ?? '') : '';
                        app.dob = data.user ? (data.user.date_of_birth ?? '') : '';
                        app.mobile = app.normalizeEmployeeMobile(data.user ? data.user.mobile : '');
                        app.address = data.user ? (data.user.address ?? '') : '';
                        app.gender = data.user ? (data.user.gender ?? '') : '';
                        app.status = data.status || 'active';
                        app.acc_no = data.acc_no;
                        app.ifsc_code = data.ifsc_code;
                        app.branch = data.branch_name;
                        app.payment_mode = data.payment_mode || '';
                        app.id = data.user ? data.user.id : '';
                        app.emp_code = data.emp_code;
                        app.hourly_basic_pay = data.hourly_basic_pay;

                        app.image = (data.user && data.user.profile_photo_path) ? data.user.profile_photo_path : '';
                        app.imageSrc = (data.user && data.user.photo_src_url)
                            ? data.user.photo_src_url
                            : ((data.user && data.user.profile_photo_path)
                                ? '/storage/' + data.user.profile_photo_path
                                : '');

                        app.formSubmitted = false;
                        app.formErrors = {};
                        app.branch_latitude = (data.latitude === null || data.latitude === undefined) ? '' : data.latitude;
                        app.branch_longitude = (data.longitude === null || data.longitude === undefined) ? '' : data.longitude;

                        app.modelHeading = "Edit Employee";

                        app.$modal.show('employeeModal');

                    } else {
                        app.emp_id = '';
                        app.role_ids = [];
                        app.username = '';
                        app.password = '';
                        app.name = '';
                        app.designation = '';
                        app.shift = '';
                        app.joing_date = '';
                        app.mobile = '';
                        app.email = '';
                        app.dob = '';
                        app.gender = '';
                        app.status = 'active';
                        app.acc_no = '';
                        app.ifsc_code = '';
                        app.branch = '';
                        app.payment_mode = '';
                        app.address = '';
                        app.hourly_basic_pay = '';
                        app.image = '';
                        app.imageSrc = '';
                        app.branch_latitude = '';
                        app.branch_longitude = '';
                        app.emp_code = '';
                        app.id = '';

                        app.formSubmitted = false;
                        app.formErrors = {};
                        app.modelHeading = "Add Employee";

                        app.$modal.show('employeeModal');
                    }
                },

                normalizeEmployeeMobile: function(mobile) {
                    let digits = String(mobile || '').replace(/\D/g, '');
                    if (digits.length > 10 && digits.startsWith('91')) {
                        digits = digits.substring(digits.length - 10);
                    }
                    return digits.substring(0, 10);
                },

                onMobileInput: function() {
                    this.mobile = this.normalizeEmployeeMobile(this.mobile);
                },

                closeAddEditForm: function() {

                    app.emp_id = '';
                    app.role_ids = [];
                    app.username = '';
                    app.password = '';
                    app.name = '';
                    app.designation = '';
                    app.shift = '';
                    app.joing_date = '';
                    app.mobile = '';
                    app.email = '';
                    app.dob = '';
                    app.gender = '';
                    app.status = 'active';
                    app.acc_no = '';
                    app.ifsc_code = '';
                    app.branch = '';
                    app.payment_mode = '';
                    app.address = '';
                    app.hourly_basic_pay = '';
                    app.emp_code = '';
                    app.image = '';
                    app.imageSrc = '';
                    app.id = '';
                    app.branch_latitude = '';
                    app.branch_longitude = '';

                    app.formSubmitted = false;
                    app.formErrors = {};

                    app.$modal.hide('employeeModal');
                    app.getEmployeeList();
                },

                getEmployeeList: function(page = 1) {

                    url = "api/employee/list";

                    axios.post(url, {
                        "page": page,
                        "page_length": this.page_length,
                        "name": this.filter_name,
                        "status": this.filter_status,
                        "role": this.filter_role,

                    }).then(function(response) {
                        console.log(response.data);
                        app.employeeList = response.data.data.data.data;
                        app.pagination = response.data.data.pagination;

                        app.loading = false;
                    });

                },

                getSalaryList: function(page = 1) {

                    url = "api/salary/list";

                    axios.post(url, {

                        "emp_id": this.emp_id,

                    }).then(function(response) {
                        app.salaryList = response.data.data.data.data;
                        app.pagination = response.data.data.pagination;

                        app.loading = false;
                    });
                },

                salaryPopup: function(data) {
                    app.emp_id = data.emp_id;
                    app.salary_id = '';
                    app.basic_salary = '';
                    app.TA = '';
                    app.DA = '';
                    app.HRA = '';
                    app.special_allowance = '';
                    app.TDS = '';
                    app.professional_tax = '';
                    app.target_value = '';
                    app.salary_status = 'active';
                    app.salary_employee_name = data.name || '';
                    app.salary_employee_code = data.emp_code || '';
                    app.formSubmitted = false;
                    app.formErrors = {};
                    app.modelHeading = "Add Salary";
                    app.getSalaryList();
                    app.$modal.show('salaryModal');
                },

                resetFilter: function() {
                    app.filter_name = '';
                    app.filter_status = 'active';
                    app.filter_role = '';
                    app.getEmployeeList();
                },

                getRoleList: function() {
                    url = "api/role/list";

                    axios.post(url, {

                    }).then(function(response) {
                        app.roleList = response.data.data;
                    });
                },
                getShiftList: function() {
                    url = "api/shift/list";

                    axios.post(url, {

                    }).then(function(response) {
                        app.shiftList = response.data.data;
                    });
                },

            },
            mounted() {
                this.getEmployeeList();
                this.getRoleList();
                this.getShiftList();
            }
        });
    </script>
@endsection
