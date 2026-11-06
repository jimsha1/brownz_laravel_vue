<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables/expressions/functions directly inside html
 */
?>
<style>
  #vue-details-tab-edit.stock-item-edit {
    padding: 4px 0 8px;
    position: relative;
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
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  #vue-details-tab-edit .edit-hint {
    font-size: 12px;
    color: #6c757d;
    margin: 0 0 14px;
  }

  #vue-details-tab-edit .vc-form-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  #vue-details-tab-edit .field-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
  }

  #vue-details-tab-edit .field-help.is-danger {
    display: block;
    margin-top: 6px;
    color: #D0342C;
    font-size: 12px;
  }

  #vue-details-tab-edit .item-password-overlay {
    position: absolute;
    inset: 0;
    z-index: 20;
    background: rgba(29, 29, 29, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    border-radius: 12px;
  }

  #vue-details-tab-edit .item-password-card {
    width: 100%;
    max-width: 420px;
    background: #fff;
    border-radius: 16px;
    padding: 18px 18px 14px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.2);
  }

  #vue-details-tab-edit .item-password-card h6 {
    margin: 0 0 4px;
    color: #1D1D1D;
    font-size: 1.05rem;
    font-weight: 600;
  }

  #vue-details-tab-edit .item-password-card .hint {
    margin: 0 0 14px;
    color: #6c757d;
    font-size: 12px;
  }

  #vue-details-tab-edit .item-password-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 14px;
  }
</style>

<div id="vue-details-tab-edit" class="stock-item-edit has-vue-form brw-form-la">
    <template>
        <form class="voco-ajax-form"
            method="post"
            v-bind:action="urls.form_save_url"
            data-hook-before-send="validateStockItemBeforeSend">
            @csrf
            <input type="hidden" name="item_id" v-model="item_id">
            <input type="hidden" name="tab_name" v-model="tab_name">
            <input type="hidden" name="auth_password" v-model="auth_password">
            <input type="hidden" name="is_sales_item" :value="Number(stock_item_info.is_sales_item) === 1 ? 1 : 0">
            <input type="hidden" name="is_active" :value="Number(stock_item_info.is_active) === 1 ? 1 : 0">

            <div class="item-password-overlay" v-if="showPasswordConfirm">
                <div class="item-password-card">
                    <h6>{{ __('Confirm with password') }}</h6>
                    <p class="hint">
                        {{ __('Enter your login password to update this stock item. This cannot be skipped.') }}
                    </p>
                    <label class="field-label" for="stock_item_auth_password">{{ __('Your Password') }}</label>
                    <input id="stock_item_auth_password"
                        type="password"
                        class="form-control"
                        v-model="auth_password"
                        autocomplete="current-password"
                        placeholder="{{ __('Password') }}"
                        v-on:keydown.enter.prevent.stop="confirmPasswordAndSave">
                    <small class="field-help is-danger" v-if="passwordError">@{{ passwordError }}</small>
                    <div class="item-password-actions">
                        <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                            v-on:click="cancelPasswordConfirm">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-primary btn_dark-x"
                            v-on:click="confirmPasswordAndSave"
                            :disabled="passwordSubmitting || isSaving">
                            <i class="fa fa-lock"></i> {{ __('Confirm & Save') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="edit-section">
                <div class="edit-section-title">{{ __('Item Details') }}</div>
                <p class="edit-hint">{{ __('Basic identity, group, and brand for this stock item') }}</p>
                <div class="row">
                    <vcinput-basic v-if="isEditMode" v-model="stock_item_info.item_code" id="item_code"
                        label="{{ __('Item Code') }}"></vcinput-basic>

                    <vcinput-select2 v-model="stock_item_info.parent_item_group_id" id="parent_item_group_id"
                        label="{{ __('Group Name') }}" v-bind:options="dropdowns.parent_categories"
                        @input="onParentGroupChange"></vcinput-select2>

                    <vcinput-select2 v-model="stock_item_info.item_group_id" id="item_group_id"
                        label="{{ __('Subcategory') }}" v-bind:options="subcategories">
                    </vcinput-select2>

                    <vcinput-basic v-model="stock_item_info.item_name" id="item_name"
                        label="{{ __('Item Name') }}"></vcinput-basic>

                    <vcinput-basic v-model="stock_item_info.hsn_code" id="hsn_code"
                        label="{{ __('HSN/SAC') }}"></vcinput-basic>

                    <vcinput-basic v-model="stock_item_info.description" id="description"
                        label="{{ __('Description') }}"></vcinput-basic>

                    <vcinput-select2 v-model="stock_item_info.brand_id" id="brand_id" label="{{ __('Brand') }}"
                        v-bind:options="dropdowns.stock_brands"></vcinput-select2>
                </div>
            </div>

            <div class="edit-section">
                <div class="edit-section-title">{{ __('Pricing & Status') }}</div>
                <p class="edit-hint">{{ __('Stock is counted in the Stock UOM. Pack size is how many of those units are in one purchase or sale unit. Example: Stock UOM GM and one box of 500, enter 500. Use 1 when you buy and sell in the same unit. Unit cost and selling price stay the price of one pack. Changing pack size rescales the quantity already on hand so the number of packs stays the same.') }}</p>
                <div class="row">
                    <vcinput-select2 v-model="stock_item_info.stock_uom_id" id="stock_uom_id" label="{{ __('Stock UOM') }}"
                        v-bind:options="dropdowns.unit_of_measurements"></vcinput-select2>

                    <vcinput-basic-number v-model="stock_item_info.pack_size" id="pack_size"
                        label="{{ __('Pack Size') }}" step="0.001" min="0.001"></vcinput-basic-number>

                    <vcinput-select2 v-model="stock_item_info.tax_percent" id="tax_percent" label="{{ __('Tax') }}"
                        v-bind:options="dropdowns.global_taxes" data-allow-clear="true"
                        placeholder="{{ __('Tax') }}"></vcinput-select2>

                    <vcinput-basic-number v-model="stock_item_info.cost_price" id="cost_price"
                        label="{{ __('Unit Cost') }}" step="0.01" min="0"></vcinput-basic-number>

                    <vcinput-basic-number v-model="stock_item_info.selling_price" id="selling_price"
                        label="{{ __('Selling Price') }}" step="0.01" min="0"></vcinput-basic-number>

                    <div class="form-group col-md-6">
                        <label for="is_sales_item_select">{{ __('Sales Item') }}</label>
                        <select class="form-control" id="is_sales_item_select" v-model="stock_item_info.is_sales_item">
                            <option :value="1">{{ __('Yes') }}</option>
                            <option :value="0">{{ __('No') }}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="is_active_select">{{ __('Status') }}</label>
                        <select class="form-control" id="is_active_select" v-model="stock_item_info.is_active">
                            <option :value="1">{{ __('Active') }}</option>
                            <option :value="0">{{ __('Inactive') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="vc-form-buttons">
                <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d"
                    href="javascript:void(0)"
                    v-on:click.prevent="cancelForm">
                    <i class="fa fa-undo"></i> {{ __('Cancel') }}
                </a>

                <button type="submit" class="btn btn-primary btn_dark-x" :disabled="isSaving">
                    <i class="fa fa-check"></i> {{ __('Save') }}
                </button>
            </div>
        </form>
    </template>
</div>

<script>
    window.item_edit = @json($details_edit_vue);
    var vue_details_tab_edit = null;

    window.validateStockItemBeforeSend = function () {
        var app = vue_details_tab_edit;
        if (!app) {
            return false;
        }
        if (app.isSaving) {
            return false;
        }

        if (app.isEditMode) {
            var password = String(app.auth_password || '').trim();
            if (!password) {
                app.passwordError = '';
                app.showPasswordConfirm = true;
                app.$nextTick(function () {
                    var input = document.getElementById('stock_item_auth_password');
                    if (input) {
                        input.focus();
                    }
                });
                return false;
            }
        }

        app.isSaving = true;
        return true;
    };

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
                var editData = <?php echo json_encode($details_edit_vue); ?>;
                if (!editData.stock_item_info || typeof editData.stock_item_info !== 'object') {
                    editData.stock_item_info = {};
                }
                editData.stock_item_info.is_active = Number(editData.stock_item_info.is_active != null
                    ? editData.stock_item_info.is_active
                    : 1);
                editData.stock_item_info.is_sales_item = Number(editData.stock_item_info.is_sales_item != null
                    ? editData.stock_item_info.is_sales_item
                    : 1);
                // Select2 option ids are strings — keep bound values consistent
                if (editData.stock_item_info.parent_item_group_id != null && editData.stock_item_info.parent_item_group_id !== '') {
                    editData.stock_item_info.parent_item_group_id = String(editData.stock_item_info.parent_item_group_id);
                }
                if (editData.stock_item_info.item_group_id != null && editData.stock_item_info.item_group_id !== '') {
                    editData.stock_item_info.item_group_id = String(editData.stock_item_info.item_group_id);
                }
                editData.subcategories = Array.isArray(editData.subcategories) ? editData.subcategories : [];
                editData.auth_password = '';
                editData.showPasswordConfirm = false;
                editData.passwordError = '';
                editData.passwordSubmitting = false;
                editData.isSaving = false;
                editData.isHydrating = true;
                return editData;
            },
            computed: {
                isEditMode: function () {
                    return parseInt(this.item_id, 10) > 0;
                }
            },
            methods: {
                onParentGroupChange: function (parentId) {
                    // Ignore the initial Select2 emit while hydrating edit data
                    if (this.isHydrating) {
                        return;
                    }
                    this.fetchSubcategories(parentId, true);
                },
                cancelForm: function () {
                    var $modalEl = $('#vue-details-tab-edit').closest('#voco-ajax-modal');
                    if ($modalEl.length) {
                        $modalEl.modal('hide');
                        return;
                    }
                    if (this.urls && this.urls.button_cancel_url) {
                        window.location.href = this.urls.button_cancel_url;
                    }
                },
                cancelPasswordConfirm: function () {
                    if (this.isSaving) {
                        return;
                    }
                    this.showPasswordConfirm = false;
                    this.auth_password = '';
                    this.passwordError = '';
                    this.passwordSubmitting = false;
                },
                confirmPasswordAndSave: function () {
                    if (this.isSaving || this.passwordSubmitting) {
                        return;
                    }
                    var password = String(this.auth_password || '').trim();
                    this.passwordError = '';
                    if (!password) {
                        this.passwordError = '{{ __('Password is required.') }}';
                        return;
                    }
                    this.passwordSubmitting = true;
                    this.showPasswordConfirm = false;
                    var $form = $(this.$el).find('form.voco-ajax-form');
                    if ($form.length) {
                        $form.trigger('submit');
                    }
                },
                resetSavingState: function () {
                    this.isSaving = false;
                    this.passwordSubmitting = false;
                },
                applySubcategoryOptions: function (options, selectedId, autoSelectFirst) {
                    var self = this;
                    var list = Array.isArray(options) ? options : [];
                    self.subcategories = list.map(function (opt) {
                        return {
                            id: String(opt.id),
                            text: opt.text
                        };
                    });

                    var $select = $('#item_group_id');
                    if (!$select.length) {
                        return;
                    }

                    // Rebuild options without firing change (avoids wiping Vue model mid-load)
                    $select.find('option').remove();
                    $select.append(new Option('', '', false, false));
                    self.subcategories.forEach(function (opt) {
                        $select.append(new Option(opt.text, opt.id, false, false));
                    });

                    var nextVal = '';
                    if (autoSelectFirst) {
                        nextVal = self.subcategories.length ? self.subcategories[0].id : '';
                    } else if (selectedId != null && selectedId !== '') {
                        nextVal = String(selectedId);
                        var exists = self.subcategories.some(function (opt) {
                            return String(opt.id) === nextVal;
                        });
                        if (!exists) {
                            nextVal = '';
                        }
                    }

                    self.stock_item_info.item_group_id = nextVal || null;
                    $select.val(nextVal || null).trigger('change');
                },
                fetchSubcategories: function (parent_item_group_id, autoSelectFirst) {
                    var self = this;
                    var keepSelectedId = autoSelectFirst
                        ? null
                        : (self.stock_item_info.item_group_id || null);

                    if (!parent_item_group_id) {
                        self.applySubcategoryOptions([], null, false);
                        return;
                    }

                    $.ajax({
                        url: '{{ route('get.itemSubcategories') }}',
                        method: 'POST',
                        data: {
                            parent_id: parent_item_group_id,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            self.applySubcategoryOptions(response || [], keepSelectedId, !!autoSelectFirst);
                        }
                    });
                }
            },
            mounted: function () {
                var self = this;
                // Options are preloaded for edit mode; release hydrate guard after Select2 settles
                this.$nextTick(function () {
                    setTimeout(function () {
                        self.isHydrating = false;
                    }, 0);
                });
            }
        });

        $(document).off('ajaxError.stockItemEditPassword ajaxComplete.stockItemSave')
            .on('ajaxError.stockItemEditPassword', function (event, jqxhr) {
                if (!vue_details_tab_edit) {
                    return;
                }
                vue_details_tab_edit.resetSavingState();
                if (!vue_details_tab_edit.isEditMode) {
                    return;
                }
                var message = (jqxhr.responseJSON && (
                    jqxhr.responseJSON.message ||
                    (jqxhr.responseJSON.errors && jqxhr.responseJSON.errors.auth_password && jqxhr.responseJSON.errors.auth_password[0])
                )) || '';
                if (/password/i.test(message) || (jqxhr.responseJSON && jqxhr.responseJSON.errors && jqxhr.responseJSON.errors.auth_password)) {
                    vue_details_tab_edit.auth_password = '';
                    vue_details_tab_edit.passwordError = message || '{{ __('Incorrect password.') }}';
                    vue_details_tab_edit.showPasswordConfirm = true;
                    vue_details_tab_edit.$nextTick(function () {
                        var input = document.getElementById('stock_item_auth_password');
                        if (input) {
                            input.focus();
                        }
                    });
                }
            })
            .on('ajaxComplete.stockItemSave', function (event, xhr, settings) {
                if (!vue_details_tab_edit) {
                    return;
                }
                var saveUrl = vue_details_tab_edit.urls && vue_details_tab_edit.urls.form_save_url;
                if (saveUrl && settings && settings.url && String(settings.url).indexOf(saveUrl) !== -1) {
                    if (!xhr || xhr.status < 200 || xhr.status >= 300) {
                        vue_details_tab_edit.resetSavingState();
                    }
                }
            });
    });
</script>
