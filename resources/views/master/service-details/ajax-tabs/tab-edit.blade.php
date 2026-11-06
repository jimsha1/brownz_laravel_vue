<style>
    .new-brownz-design .multiselect__tags {
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

    .new-brownz-design .multiselect__single {
        background-color: transparent;
    }

    .new-brownz-design .multiselect__input {
        margin: 0;
        border-radius: 0 !important;
        background-color: #fff !important;
    }

    #vue-details-tab-edit.service-edit {
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

    #vue-details-tab-edit .edit-hint {
        font-size: 12px;
        color: #6c757d;
        margin: -4px 0 14px;
    }

    #vue-details-tab-edit .option-box {
        background: #fff;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 12px;
    }

    #vue-details-tab-edit .brw-checkbox-browz-dds {
        font-size: 14px;
        margin-bottom: 0;
    }

    #vue-details-tab-edit .vc-form-buttons {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    #voco-ajax-modal #vue-details-tab-edit .vc-form-buttons {
        display: flex !important;
    }
</style>
<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables/expressions/functions directly inside html
 */
?>
<div id="vue-details-tab-edit" class="has-vue-form brw-form-la service-edit">
    <form class="voco-ajax-form" method="post" v-bind:action="urls.form_save_url">
        @csrf
        <input type="hidden" name="service_id" v-model="service_id">
        <input type="hidden" name="tab_name" v-model="tab_name">

        <div class="edit-section">
            <p class="edit-section-title">{{ __('Service Details') }}</p>
            <p class="edit-hint">{{ __('Set category, branch, pricing, duration, and visibility.') }}</p>

            <div class="row">
                <vcinput-select2 v-model="service_info.parent_category_id" id="parent_category_id"
                    label="{{ __('Category') }}" v-bind:options="dropdowns.parent_categories"
                    @input="fetchSubcategories(service_info.parent_category_id)"></vcinput-select2>

                <vcinput-select2 v-model="service_info.category_id" id="category_id" label="{{ __('Subcategory') }}"
                    v-bind:options="subcategories">
                </vcinput-select2>
            </div>

            <div class="row">
                <vcinput-select2 v-model="service_info.outlet_id" id="outlet_id" label="{{ __('Branch') }}"
                    v-bind:options="dropdowns.all_branch"></vcinput-select2>

                <vcinput-basic v-model="service_info.name" id="name" label="{{ __('Name') }}"></vcinput-basic>
            </div>

            <div class="row">
                <vcinput-basic v-model="service_info.service_base_price" id="service_base_price"
                    label="{{ __('Service Base Price') }}"></vcinput-basic>

                <vcinput-select2 v-model="service_info.tax_percent" id="tax_percent" label="{{ __('Tax') }}"
                    v-bind:options="dropdowns.global_taxes"></vcinput-select2>
            </div>

            <div class="row">
                <div class="form-group col-md-6">
                    <label for="duration_minutes">{{ __('Duration') }}</label>
                    <select v-model="service_info.duration_minutes" class="form-control voco-select2"
                        id="duration_minutes" name="duration_minutes" data-placeholder="{{ __('Duration') }}">
                        <option v-for="option in durations_data" v-bind:value="option">@{{ option | vc_duration }}</option>
                    </select>
                </div>

                <vcinput-textarea v-model="service_info.short_description_en" id="short_description_en"
                    label="{{ __('Short Description') }}" rows="2"></vcinput-textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="option-box">
                        <input type="hidden" name="show_on_website" value="0">
                        <label class="brw-checkbox-browz-dds mb-0" for="show_on_website">
                            <input v-model="service_info.show_on_website"
                                class="brow-checkbox-dd"
                                type="checkbox"
                                id="show_on_website"
                                name="show_on_website"
                                value="1"
                                :true-value="1"
                                :false-value="0">
                            {{ __('Show On Website') }}
                            <span class="brow-checkmark"></span>
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="option-box">
                        <input type="hidden" name="is_active" value="0">
                        <label class="brw-checkbox-browz-dds mb-0" for="is_active">
                            <input v-model="service_info.is_active"
                                class="brow-checkbox-dd"
                                type="checkbox"
                                id="is_active"
                                name="is_active"
                                value="1"
                                :true-value="1"
                                :false-value="0">
                            {{ __('Is Active') }}
                            <span class="brow-checkmark"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <input id="count_shift" type="hidden" name="count_shift">
        <div class="vc-form-buttons">
            <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d"
                href="javascript:void(0)"
                v-on:click.prevent="cancelForm">
                <i class="fa fa-undo"></i> {{ __('Cancel') }}
            </a>

            <button type="submit" class="btn btn-primary btn_dark-x">
                <i class="fa fa-check"></i> {{ __('Save') }}
            </button>
        </div>
    </form>
</div>

<script>
    window.service_edit = @json($details_edit_vue);

    var vue_details_tab_edit = null;

    jQuery(function($) {
        var $modal = $('#vue-details-tab-edit').closest('#voco-ajax-modal');
        if ($modal.length) {
            $modal.find('.modal-footer').hide();
            $modal.one('hidden.bs.modal', function () {
                $(this).find('.modal-footer').show();
                $(this).find('.btn-voco-save').show();
            });
        }

        var editData = @json($details_edit_vue);
        editData.subcategories = [];
        if (editData.service_info) {
            editData.service_info.show_on_website = Number(editData.service_info.show_on_website) === 1 ? 1 : 0;
            editData.service_info.is_active = Number(editData.service_info.is_active) === 1 ? 1 : 0;
            editData.service_info.allow_online_booking = Number(editData.service_info.allow_online_booking) === 1 ? 1 : 0;
        }

        vue_details_tab_edit = new Vue({
            el: "#vue-details-tab-edit",
            data: function () {
                return editData;
            },
            methods: {
                cancelForm: function () {
                    var $modal = $('#vue-details-tab-edit').closest('#voco-ajax-modal');
                    if ($modal.length) {
                        $modal.modal('hide');
                        return;
                    }
                    window.location.href = this.urls.button_cancel_url;
                },
                fetchSubcategories: function(parent_category_id) {
                    vue_details_tab_edit.clearSubcategories();
                    $.ajax({
                        url: '{{ route('get.subcategories') }}',
                        method: 'POST',
                        data: {
                            parent_id: parent_category_id
                        },
                        success: function(response) {
                            vue_details_tab_edit.subcategories = response;
                            $('#category_id').select2({
                                data: vue_details_tab_edit.subcategories,
                            });

                            if (response && response.length > 0) {
                                const firstSubcategoryId = response[0].id;
                                vue_details_tab_edit.service_info.category_id = firstSubcategoryId;
                                $('#category_id').val(firstSubcategoryId).trigger('change');
                            } else {
                                vue_details_tab_edit.service_info.category_id = null;
                                $('#category_id').val(null).trigger('change');
                            }
                        }
                    });
                },
                fetchSubcategories1: function(parent_category_id) {
                    $.ajax({
                        url: '{{ route('get.subcategories') }}',
                        method: 'POST',
                        data: {
                            parent_id: parent_category_id
                        },
                        success: function(response) {
                            vue_details_tab_edit.subcategories = response;
                            $('#category_id').select2({
                                data: vue_details_tab_edit.subcategories,
                            });

                            const selectedCategory = vue_details_tab_edit.service_info.category_id;
                            if (selectedCategory) {
                                $('#category_id').val(selectedCategory).trigger('change');
                            }
                        }
                    });
                },
                clearSubcategories: function() {
                    this.subcategories = [];
                    var $select2Element = $('#category_id').select2();
                    $select2Element.empty().trigger('change');
                }
            },
            mounted: function() {
                if (window.service_edit.service_id > 0) {
                    this.fetchSubcategories1(window.service_edit.service_info.parent_category_id);
                }

                const selectedCategory = vue_details_tab_edit.service_info.category_id;
                if (selectedCategory) {
                    $('#category_id').val(selectedCategory).trigger('change');
                }
            }
        });
    });
</script>
