@extends('payroll.layouts.base')

<style>
    .incentive-terms-page .list-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
    }

    .incentive-terms-page .list-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .incentive-terms-page .list-meta {
        font-size: 12px;
        color: #6c757d;
    }

    .incentive-terms-page .terms-table th,
    .incentive-terms-page .terms-table td {
        vertical-align: middle;
    }

    .incentive-terms-page .term-title {
        display: block;
        font-weight: 600;
        color: #1D1D1D;
    }

    .incentive-terms-page .percent-chip {
        display: inline-flex;
        align-items: center;
        font-size: 13px;
        font-weight: 600;
        color: #1D1D1D;
        background: #F0F0F3;
        border-radius: 999px;
        padding: 6px 12px;
        font-variant-numeric: tabular-nums;
    }

    .incentive-terms-page .status-pill {
        display: inline-flex;
        align-items: center;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        padding: 6px 12px;
        text-transform: capitalize;
        line-height: 1.2;
    }

    .incentive-terms-page .status-pill.is-active {
        color: #199900;
        background: rgba(25, 153, 0, 0.15);
    }

    .incentive-terms-page .status-pill.is-inactive {
        color: #D0342C;
        background: rgba(208, 52, 44, 0.12);
    }

    .incentive-terms-page .manage-btn {
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

    .incentive-terms-page .manage-btn:hover {
        background: #1D1D1D;
        color: #E6CD7F;
    }

    .incentive-terms-page .empty-state {
        text-align: center;
        padding: 36px 16px;
        background: #F7F7F9;
        border-radius: 16px;
        color: #6c757d;
    }

    #incentiveTermsModal .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }

    #incentiveTermsModal .modal-header {
        border-bottom: none;
        padding: 18px 20px 8px;
        align-items: flex-start;
    }

    #incentiveTermsModal .modal-title {
        color: #1D1D1D;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 2px;
    }

    #incentiveTermsModal .modal-subtitle {
        margin: 0;
        font-size: 13px;
        color: #6c757d;
    }

    #incentiveTermsModal .modal-body {
        padding: 8px 20px 12px;
    }

    #incentiveTermsModal .terms-section {
        background: #F7F7F9;
        border-radius: 14px;
        padding: 14px 14px 6px;
        margin-bottom: 12px;
    }

    #incentiveTermsModal .terms-section-title {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #6c757d;
        margin: 0 0 10px;
    }

    #incentiveTermsModal .form-label {
        font-size: 13px;
        color: #4a4a4a;
        font-weight: 500;
        margin-bottom: 4px;
    }

    #incentiveTermsModal .form-group {
        margin-bottom: 12px;
    }

    #incentiveTermsModal .modal-footer {
        border-top: none;
        padding: 4px 0 8px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }
</style>

@section('content')
    <div id="app" class="incentive-terms-page">

        <div class="fbox-shadow mt-2 fd-card respo-margin-top">
            <div class="card-body">
                <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
                    {{ __('Incentive Terms') }}
                </h5>

                <div class="row align-items-center br-row-gap-2">
                    <div class="col-lg-4 col-md-6">
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <p class="m-0"><i aria-hidden="true" class="fa fa-search"></i></p>
                            <input type="text" class="form-control brawnz-search-bar-in"
                                v-model="filter_search"
                                @keyup.enter="getIncentiveTermsList()"
                                placeholder="{{ __('Search title / percentage') }}">
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <select class="form-control brawnz-search-bar-in" v-model="filter_status"
                                @change="getIncentiveTermsList()">
                                <option value="all">{{ __('All Status') }}</option>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-primary btn_dark-x mr-2"
                            v-on:click="getIncentiveTermsList()">
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
                        <p class="list-title">{{ __('Terms List') }}</p>
                        <p class="list-meta mb-0" v-if="pagination.total">
                            @{{ pagination.total }} {{ __('result(s)') }}
                        </p>
                    </div>
                    <div class="d-flex justify-content-end">
                        @can('incentive_term_create')
                            <button type="button" class="btn_dark-x" v-on:click="popupModal(0)">
                                <i class="fa fa-plus"></i> {{ __('New') }}
                            </button>
                        @endcan
                    </div>
                </div>

                {{-- Add / Edit Modal --}}
                <div class="modal fade brownz-custom-popup" id="incentiveTermsModal" tabindex="-1"
                    aria-labelledby="incentiveTermsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div>
                                    <h4 class="modal-title">@{{ modelHeading }}</h4>
                                    <p class="modal-subtitle">
                                        {{ __('Define incentive title, percentage and status') }}
                                    </p>
                                </div>
                                <button type="button" class="bootbox-close-button close" v-on:click="hide"
                                    data-dismiss="modal" aria-label="Close">
                                    ×
                                </button>
                            </div>

                            <div class="modal-body">
                                <form @submit.prevent="validateBeforeSubmit" v-if="!formSubmitted" method="post">
                                    <div class="terms-section">
                                        <p class="terms-section-title">{{ __('Incentive Details') }}</p>
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12 form-group">
                                                <label for="incentive_title" class="form-label">
                                                    {{ __('Incentive Title') }} <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" v-model="incentive_title"
                                                    autocomplete="off" id="incentive_title"
                                                    v-validate.initial="incentive_title" data-rules="required"
                                                    placeholder="{{ __('e.g. Monthly Target Bonus') }}">
                                                <span v-if="formErrors['incentive_title']"
                                                    class="error text-danger">@{{ formErrors['incentive_title'] }}</span>
                                                <p class="text-danger mb-0" v-if="errors.has('incentive_title')">
                                                    @{{ errors.first('incentive_title') }}
                                                </p>
                                            </div>

                                            <div class="col-md-6 col-sm-12 form-group">
                                                <label for="incentive_percentage" class="form-label">
                                                    {{ __('Incentive Percentage') }} <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    v-model="incentive_percentage" id="incentive_percentage"
                                                    v-validate.initial="incentive_percentage" data-rules="required"
                                                    placeholder="{{ __('e.g. 5') }}">
                                                <span v-if="formErrors['incentive_percentage']"
                                                    class="error text-danger">@{{ formErrors['incentive_percentage'] }}</span>
                                                <p class="text-danger mb-0" v-if="errors.has('incentive_percentage')">
                                                    @{{ errors.first('incentive_percentage') }}
                                                </p>
                                            </div>

                                            <div class="col-md-6 col-sm-12 form-group">
                                                <label for="status" class="form-label">
                                                    {{ __('Status') }} <span class="text-danger">*</span>
                                                </label>
                                                <select v-model="status" class="form-control" id="status">
                                                    <option value="active">{{ __('Active') }}</option>
                                                    <option value="inactive">{{ __('Inactive') }}</option>
                                                </select>
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
                </div>

                <table class="table brownz-table-list-v1 terms-table" v-if="incentiveTermsList.length">
                    <thead>
                        <tr>
                            <th style="width: 56px;">#</th>
                            <th>{{ __('Incentive Title') }}</th>
                            <th>{{ __('Percentage') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-center" style="width: 80px;">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(data, index) in incentiveTermsList" :key="data.incentive_terms_id">
                            <td>@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</td>
                            <td>
                                <span class="term-title">@{{ data.incentive_title || '—' }}</span>
                            </td>
                            <td>
                                <span class="percent-chip">@{{ formatPercentage(data.incentive_percentage) }}</span>
                            </td>
                            <td>
                                <span class="status-pill"
                                    :class="data.status === 'active' ? 'is-active' : 'is-inactive'">
                                    @{{ data.status || '—' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @can('incentive_term_edit')
                                    <a href="javascript:void(0)" class="manage-btn" title="{{ __('Edit') }}"
                                        v-on:click="popupModal(data)">
                                        <i aria-hidden="true" class="fa fa-pencil-square-o"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="empty-state" v-else>
                    {{ __('No incentive terms found for the selected filters.') }}
                </div>
            </div>

            <nav aria-label="pagination">
                <ul class="pagination justify-content-end">
                    <li class="page-item" v-if="pagination.current_page > 1">
                        <a class="page-link" href="#" aria-label="Previous"
                            v-on:click.prevent="changePage(pagination.current_page - 1)">
                            <span aria-hidden="true">«</span>
                        </a>
                    </li>
                    <li class="page-item" v-for="page in pagesNumber"
                        v-bind:class="[ page == isActived ? 'active' : '']">
                        <a class="page-link" href="#" v-on:click.prevent="changePage(page)">@{{ page }}</a>
                    </li>
                    <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                        <a class="page-link" href="#" aria-label="Next"
                            v-on:click.prevent="changePage(pagination.current_page + 1)">
                            <span aria-hidden="true">»</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <script>
        Vue.use(window["vue-js-modal"].default);
        var app = new Vue({
            el: '#app',

            data: {
                loading: false,
                base_url: "{{ '/' }}",
                incentiveTermsList: [],

                incentive_terms_id: '',
                incentive_title: '',
                incentive_percentage: '',
                status: 'active',
                modelHeading: '',

                filter_search: '',
                filter_status: 'active',

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
            },

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

            methods: {
                formatPercentage(value) {
                    const num = Number(value);
                    if (isNaN(num)) {
                        return '—';
                    }
                    return num.toFixed(2) + '%';
                },

                validateBeforeSubmit(e) {
                    this.$validator.validateAll();
                    if (!this.errors.any()) {
                        this.submitForm()
                    }
                },
                submitForm() {
                    this.addEditSubmitForm()
                },

                changePage: function(page) {
                    this.pagination.current_page = page;
                    app.getIncentiveTermsList(page);
                },

                show: function() {
                    $('#incentiveTermsModal').modal('show');
                },

                hide: function() {
                    $('#incentiveTermsModal').modal('hide');
                },

                addEditSubmitForm: function() {
                    app.submit_button_flag = true;
                    url = "api/incentive-terms/store";

                    let formData = new FormData();
                    formData.append('incentive_terms_id', this.incentive_terms_id);
                    formData.append('incentive_title', this.incentive_title);
                    formData.append('incentive_percentage', this.incentive_percentage);
                    formData.append('status', this.status || 'active');

                    axios.post(url, formData).then(function(response) {
                        if (response.data.errorcode == 0) {
                            app.loading = false;
                            Vue.$toast.success(response.data.errormessage, 'Success Alert');
                            app.submit_button_flag = false;
                            app.formSubmitted = true;
                            app.formErrors = {};
                            $('#incentiveTermsModal').modal('hide');
                            app.getIncentiveTermsList(app.pagination.current_page);
                        } else {
                            app.loading = false;
                            Vue.$toast.error(response.data.errormessage || 'Something went wrong.',
                                'Danger Alert');
                            app.submit_button_flag = false;
                        }
                    }).catch(function(err) {
                        app.submit_button_flag = false;
                        app.formErrors = (err.response && err.response.data && err.response.data.errors)
                            ? err.response.data.errors
                            : {};
                        Vue.$toast.error('Please check the form and try again.', 'Danger Alert');
                    });
                },

                popupModal: function(data) {
                    if (data && data.incentive_terms_id > 0) {
                        app.incentive_terms_id = data.incentive_terms_id;
                        app.incentive_title = data.incentive_title;
                        app.incentive_percentage = data.incentive_percentage;
                        app.status = data.status || 'active';
                        app.formSubmitted = false;
                        app.formErrors = {};
                        app.modelHeading = "Edit Incentive Terms";
                        $('#incentiveTermsModal').modal('show');
                    } else {
                        app.incentive_terms_id = '';
                        app.incentive_title = '';
                        app.incentive_percentage = '';
                        app.status = 'active';
                        app.formSubmitted = false;
                        app.formErrors = {};
                        app.modelHeading = "Add Incentive Terms";
                        $('#incentiveTermsModal').modal('show');
                    }
                },

                getIncentiveTermsList: function(page = 1) {
                    url = "api/incentive-terms/list";

                    axios.post(url, {
                        "page": page,
                        "page_length": this.page_length,
                        "search": this.filter_search,
                        "status": this.filter_status,
                    }).then(function(response) {
                        app.incentiveTermsList = response.data.data.data.data;
                        app.pagination = response.data.data.pagination;
                        app.loading = false;
                    });
                },

                resetFilter: function() {
                    app.filter_search = '';
                    app.filter_status = 'active';
                    app.getIncentiveTermsList();
                },
            },
            mounted() {
                this.getIncentiveTermsList();
            }
        });
    </script>
@endsection
