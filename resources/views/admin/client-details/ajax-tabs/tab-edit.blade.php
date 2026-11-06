<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-edit.client-edit {
    padding: 4px 0 8px;
  }

  #vue-details-tab-edit .edit-section {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 16px 16px 6px;
    margin-bottom: 16px;
  }

  #vue-details-tab-edit .edit-section-title {
    font-size: 13px;
    font-weight: 600;
    color: #1D1D1D;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  #vue-details-tab-edit #number_validation {
    display: block;
    margin: -4px 0 10px;
    padding-left: 15px;
    font-size: 12px;
  }

  #vue-details-tab-edit .vc-form-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  #vue-details-tab-edit #loading_spinner {
    font-size: 12px;
    color: #6c757d;
  }

  #voco-ajax-modal #vue-details-tab-edit .vc-form-buttons {
    display: flex !important;
  }
</style>

<div id="vue-details-tab-edit" class="client-edit has-vue-form brw-form-la">
    <template>
        <form class="voco-ajax-form" method="post" v-bind:action="client_info.form_save_url">
            @csrf
            <input type="hidden" name="client_id" v-model="client_id">
            <input type="hidden" name="tab_name" v-model="tab_name">

            <div class="edit-section">
                <div class="edit-section-title">{{ __('Basic Details') }}</div>
                <div class="row">
                    <vcinput-basic v-model="client_info.name" id="name" label="{{ __('Name') }}"></vcinput-basic>

                    <div class="form-group col-md-6">
                        <label for="client_type">{{ __('Client Type') }}</label>
                        <select class="form-control voco-select2" id="client_type" name="client_type"
                            data-placeholder="{{ __('Client Type') }}" v-model="client_info.client_type">
                            <option value=""></option>
                            {{ \Facades\App\Models\Client::categoryDropdown() }}
                        </select>
                    </div>
                </div>

                <div class="row">
                    <vcinput-basic v-model="client_info.email" id="email" label="{{ __('Email') }}"></vcinput-basic>
                    <vcinput-datepicker v-model="client_info.date_of_birth" id="date_of_birth"
                        label="{{ __('Date Of Birth') }}"></vcinput-datepicker>
                </div>

                <div class="row">
                    <vcinput-basic-number v-model="mobile" id="mobile" label="{{ __('Mobile') }}"></vcinput-basic-number>
                    <vcinput-basic-number v-model="client_info.telephone" id="telephone"
                        label="{{ __('Telephone') }}"></vcinput-basic-number>
                </div>
                <div class="row">
                    <span id="number_validation" style="color: red;"></span>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="gender">{{ __('Gender') }}</label>
                        <select class="form-control voco-select2" id="gender" name="gender"
                            data-placeholder="{{ __('Gender') }}" v-model="client_info.gender">
                            <option value=""></option>
                            {{ \Facades\App\Models\User::genderDropdown() }}
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="user_status">{{ __('Status') }}</label>
                        <select class="form-control voco-select2" id="user_status" name="user_status"
                            data-placeholder="{{ __('Status') }}" v-model="client_info.user_status">
                            <option value="active">{{ __('Active') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                            <option value="pending">{{ __('Pending') }}</option>
                            <option value="blocked">{{ __('Blocked') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="edit-section">
                <div class="edit-section-title">{{ __('Address') }}</div>
                <div class="row">
                    <vcinput-basic v-model="client_info.address" id="address" label="{{ __('Address') }}"></vcinput-basic>
                    <vcinput-basic v-model="client_info.city" id="city" label="{{ __('City') }}"></vcinput-basic>
                </div>
                <div class="row">
                    <vcinput-basic v-model="client_info.state" id="state" label="{{ __('State') }}"></vcinput-basic>
                    <vcinput-basic v-model="client_info.zip" id="zip" label="{{ __('Zip') }}"></vcinput-basic>
                </div>
            </div>

            <div class="vc-form-buttons text-right">
                <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d"
                    href="javascript:void(0)"
                    v-on:click.prevent="cancelForm">
                    <i class="fa fa-undo"></i> {{ __('Cancel') }}
                </a>

                <button id="save_btn" type="submit" class="btn btn-primary btn_dark-x">
                    <i class="fa fa-check"></i> {{ __('Save') }}
                </button>
                <div id="loading_spinner" style="display: none;">
                    {{ __('Processing...') }}
                </div>
            </div>
        </form>
    </template>
</div>

<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
    jQuery(function ($) {
        var $modal = $('#vue-details-tab-edit').closest('#voco-ajax-modal');
        if ($modal.length) {
            $modal.find('.modal-footer').hide();
            $modal.one('hidden.bs.modal', function () {
                $(this).find('.modal-footer').show();
                $(this).find('.btn-voco-save').show();
            });
        }

        vue_details_tab_edit = new Vue({
            el: "#vue-details-tab-edit",
            data: {!! json_encode($details_edit_vue) !!},
            methods: {
                cancelForm: function () {
                    var $modalEl = $('#vue-details-tab-edit').closest('#voco-ajax-modal');
                    if ($modalEl.length) {
                        $modalEl.modal('hide');
                        return;
                    }
                    if (this.client_info && this.client_info.button_cancel_url) {
                        window.location.href = this.client_info.button_cancel_url;
                    }
                }
            }
        });

        $("#date_of_birth").datepicker({
            format: "{{ config('vocomi.settings.format.jscalendar.format_js') }}"
        });

        $('#mobile').on('input', function () {
            var value = $(this).val().replace(/\D/g, '').substring(0, 10);
            $(this).val(value);
            $('#number_validation').text('');
            $('#save_btn').prop("disabled", false);

            if (value.length !== 10) {
                $('#save_btn').prop("disabled", true);
                $('#number_validation').text("{{ app()->getLocale() === 'en' ? 'Please enter a valid 10-digit mobile number' : 'الرجاء إدخال رقم جوال صالح من 10 أرقام' }}");
            }
        });

        $('#save_btn').on('click', function () {
            $('#loading_spinner').show();
            setTimeout(function () {
                $('#loading_spinner').hide();
            }, 2000);
        });
    });
</script>
