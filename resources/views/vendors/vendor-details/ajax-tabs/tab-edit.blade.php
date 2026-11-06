<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-edit.vendor-edit {
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
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  #vue-details-tab-edit .edit-section-hint {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 14px;
  }

  #vue-details-tab-edit .field-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  #vue-details-tab-edit .field-shell .form-control {
    border-radius: 10px;
  }

  #vue-details-tab-edit .field-help {
    display: block;
    margin-top: 6px;
    font-size: 12px;
    color: #6c757d;
  }

  #vue-details-tab-edit .field-help.is-danger {
    color: #D0342C;
  }

  #vue-details-tab-edit .gst-input {
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  }

  #vue-details-tab-edit .vc-form-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    padding-top: 4px;
  }

  /* Keep form actions visible inside ajax modal (global rule hides .vc-form-buttons) */
  #voco-ajax-modal #vue-details-tab-edit .vc-form-buttons {
    display: flex !important;
  }
</style>

<div id="vue-details-tab-edit" class="vendor-edit has-vue-form brw-form-la">
    <template>
        <form class="voco-ajax-form" method="post" v-bind:action="urls.form_save_url">
            @csrf
            <input type="hidden" name="vendor_id" v-model="vendor_id">
            <input type="hidden" name="tab_name" v-model="tab_name">

            <div class="edit-section">
                <div class="edit-section-title">{{ __('Contact Details') }}</div>
                <p class="edit-section-hint">{{ __('Primary contact information used for purchases and communication.') }}</p>

                <div class="row">
                    <vcinput-basic v-model="vendor_info.name" id="name" label="{{ __('Vendor Name') }}"></vcinput-basic>
                    <vcinput-basic-email v-model="vendor_info.email" id="email" label="{{ __('Email') }}"></vcinput-basic-email>
                </div>

                <div class="row">
                    <vcinput-basic-number v-model="vendor_info.phone" id="phone" label="{{ __('Phone') }}"></vcinput-basic-number>

                    <div class="form-group col-md-6">
                        <label class="field-label" for="is_active_select">{{ __('Status') }}</label>
                        <div class="field-shell">
                            <select class="form-control" id="is_active_select" name="is_active" v-model="vendor_info.is_active">
                                <option :value="1">{{ __('Active') }}</option>
                                <option :value="0">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <small class="field-help" :class="phoneError ? 'is-danger' : ''">@{{ phoneHelpText }}</small>
                    </div>
                </div>
            </div>

            <div class="edit-section">
                <div class="edit-section-title">{{ __('Address & Tax') }}</div>
                <p class="edit-section-hint">{{ __('Optional billing details for invoices and tax records.') }}</p>

                <div class="row">
                    <vcinput-textarea
                        v-model="vendor_info.address"
                        id="address"
                        label="{{ __('Address') }}"
                        rows="3"
                        colclass="col-12"
                        placeholder="{{ __('Street, city, state, and postal code') }}">
                    </vcinput-textarea>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="field-label" for="gst_number">{{ __('GST Number') }}</label>
                        <div class="field-shell">
                            <input
                                type="text"
                                class="form-control gst-input"
                                id="gst_number"
                                name="gst_number"
                                v-model="vendor_info.gst_number"
                                maxlength="15"
                                autocomplete="off"
                                placeholder="{{ __('15-character GSTIN') }}"
                                v-on:input="normalizeGstNumber">
                        </div>
                        <small class="field-help" :class="gstError ? 'is-danger' : ''">@{{ gstHelpText }}</small>
                    </div>
                </div>
            </div>

            <div class="vc-form-buttons">
                <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d"
                    href="javascript:void(0)"
                    v-on:click.prevent="cancelForm">
                    <i class="fa fa-undo"></i> {{ __('Cancel') }}
                </a>

                <button type="submit" class="btn btn-primary btn_dark-x" :disabled="!canSubmit">
                    <i class="fa fa-check"></i> {{ __('Save') }}
                </button>
            </div>
        </form>
    </template>
</div>

<script>
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

        vue_details_tab_edit = new Vue({
            el: "#vue-details-tab-edit",
            data: function () {
                return Object.assign(<?php echo json_encode($details_edit_vue); ?>, {
                    phoneError: false,
                    gstError: false,
                });
            },
            computed: {
                phoneHelpText: function () {
                    if (this.phoneError) {
                        return '{{ __('Please enter a valid 10-digit phone number.') }}';
                    }
                    return '{{ __('10-digit mobile number.') }}';
                },
                gstHelpText: function () {
                    if (this.gstError) {
                        return '{{ __('GST number must be exactly 15 letters and numbers.') }}';
                    }
                    return '{{ __('Optional. Example: 22AAAAA0000A1Z5') }}';
                },
                canSubmit: function () {
                    var phone = String(this.vendor_info.phone || '').replace(/\D/g, '');
                    var gst = String(this.vendor_info.gst_number || '');
                    return phone.length === 10 && (gst.length === 0 || gst.length === 15);
                }
            },
            watch: {
                'vendor_info.phone': function (value) {
                    var digits = String(value || '').replace(/\D/g, '').substring(0, 10);
                    if (digits !== value) {
                        this.vendor_info.phone = digits;
                    }
                    this.phoneError = digits.length > 0 && digits.length !== 10;
                },
                'vendor_info.gst_number': function (value) {
                    var gst = String(value || '').toUpperCase().replace(/[^0-9A-Z]/g, '').substring(0, 15);
                    if (gst !== value) {
                        this.vendor_info.gst_number = gst;
                    }
                    this.gstError = gst.length > 0 && gst.length !== 15;
                }
            },
            methods: {
                normalizeGstNumber: function () {
                    this.vendor_info.gst_number = String(this.vendor_info.gst_number || '')
                        .toUpperCase()
                        .replace(/[^0-9A-Z]/g, '')
                        .substring(0, 15);
                },
                cancelForm: function () {
                    var $modalEl = $('#vue-details-tab-edit').closest('#voco-ajax-modal');
                    if ($modalEl.length) {
                        $modalEl.modal('hide');
                        return;
                    }
                    window.location.href = this.urls.button_cancel_url;
                }
            }
        });
    });
</script>
