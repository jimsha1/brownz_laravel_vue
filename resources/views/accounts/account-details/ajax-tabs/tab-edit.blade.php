<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-details-tab-edit.account-edit {
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
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  #vue-details-tab-edit .system-alert {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    background: rgba(230, 205, 127, 0.35);
    color: #8A6D00;
    border-radius: 14px;
    padding: 12px 14px;
    margin-bottom: 16px;
    font-size: 13px;
    font-weight: 600;
  }

  #vue-details-tab-edit .system-alert i {
    margin-top: 2px;
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

  #vue-details-tab-edit .field-shell .form-control {
    border-radius: 10px;
  }

  #vue-details-tab-edit .field-help.is-danger {
    display: block;
    margin-top: 6px;
    color: #D0342C;
    font-size: 12px;
  }

  #vue-details-tab-edit .category-field {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  #vue-details-tab-edit .category-field select {
    flex: 1 1 auto;
    min-width: 0;
  }

  #vue-details-tab-edit .category-action {
    flex: 0 0 auto;
    width: 38px;
    height: 38px;
    border-radius: 10px;
    border: none;
    background: #1D1D1D;
    color: #E6CD7F;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  #vue-details-tab-edit .category-action.is-delete {
    background: #F0F0F3;
    color: #D0342C;
  }

  #vue-details-tab-edit .category-action:disabled {
    opacity: 0.45;
    cursor: not-allowed;
  }

  #vue-details-tab-edit .category-add-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
  }

  #vue-details-tab-edit .category-add-row .form-control {
    flex: 1 1 auto;
  }

  #vue-details-tab-edit .account-password-overlay {
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

  #vue-details-tab-edit .account-password-card {
    width: 100%;
    max-width: 420px;
    background: #fff;
    border-radius: 16px;
    padding: 18px 18px 14px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.2);
  }

  #vue-details-tab-edit .account-password-card h6 {
    margin: 0 0 4px;
    color: #1D1D1D;
    font-size: 1.05rem;
    font-weight: 600;
  }

  #vue-details-tab-edit .account-password-card .hint {
    margin: 0 0 14px;
    color: #6c757d;
    font-size: 12px;
  }

  #vue-details-tab-edit .account-password-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 14px;
  }
</style>

<div id="vue-details-tab-edit" class="account-edit has-vue-form brw-form-la">
    <template>
        <form class="voco-ajax-form"
            method="post"
            v-bind:action="urls.form_save_url"
            data-hook-before-send="validateAccountBeforeSend">
            @csrf
            <input type="hidden" name="account_id" v-model="account_id">
            <input type="hidden" name="tab_name" v-model="tab_name">
            <input type="hidden" name="auth_password" v-model="auth_password">

            <div class="account-password-overlay" v-if="showPasswordConfirm">
                <div class="account-password-card">
                    <h6>{{ __('Confirm with password') }}</h6>
                    <p class="hint">
                        {{ __('Enter your login password to update this account. This cannot be skipped.') }}
                    </p>

                    <label class="field-label" for="account_auth_password">{{ __('Your Password') }}</label>
                    <div class="field-shell">
                        <input id="account_auth_password"
                            type="password"
                            class="form-control"
                            v-model="auth_password"
                            autocomplete="current-password"
                            placeholder="{{ __('Password') }}"
                            v-on:keyup.enter="confirmPasswordAndSave">
                    </div>
                    <small class="field-help is-danger" v-if="passwordError">@{{ passwordError }}</small>

                    <div class="account-password-actions">
                        <button type="button"
                            class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                            v-on:click="cancelPasswordConfirm">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button"
                            class="btn btn-sm btn-primary btn_dark-x"
                            v-on:click="confirmPasswordAndSave"
                            :disabled="passwordSubmitting">
                            <i class="fa fa-lock"></i> {{ __('Confirm & Save') }}
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="is_system_defined" class="system-alert">
                <i class="fa fa-lock" aria-hidden="true"></i>
                <span>{{ __('This is a system defined ledger and cannot be edited.') }}</span>
            </div>

            <fieldset :disabled="is_system_defined">
                <div class="edit-section">
                    <div class="edit-section-title">{{ __('Account Details') }}</div>
                    <div class="row">
                        <vcinput-basic v-model="account_info.name" id="name" label="{{ __('Name') }}"
                            :disabled="is_system_defined"></vcinput-basic>

                        <div class="form-group col-md-6">
                            <label for="type">{{ __('Type') }}</label>
                            <select class="form-control voco-select2" id="type" name="type"
                                data-placeholder="{{ __('Type') }}" v-model="account_info.type"
                                :disabled="is_system_defined">
                                <option value=""></option>
                                {{ \Facades\App\Models\Account::accountTypeDropdown() }}
                            </select>
                        </div>

                        <vcinput-basic v-model="account_info.balance" id="balance" label="{{ __('Balance (read-only)') }}"
                            :disabled="true"></vcinput-basic>

                        <div class="form-group col-md-6">
                            <label for="is_active_select">{{ __('Status') }}</label>
                            <select class="form-control" id="is_active_select" name="is_active"
                                v-model="account_info.is_active" :disabled="is_system_defined">
                                <option :value="1">{{ __('Active') }}</option>
                                <option :value="0">{{ __('Inactive') }}</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="account_category">{{ __('Category') }}</label>
                            <div class="category-field">
                                <select class="form-control" id="account_category" name="category"
                                    v-model="account_info.category" :disabled="is_system_defined">
                                    <option value="">{{ __('Select Category') }}</option>
                                    <option v-for="cat in filteredCategories" :key="cat.category_id" :value="cat.name">
                                        @{{ cat.name }}
                                    </option>
                                </select>
                                <button type="button" class="category-action" title="{{ __('Add category') }}"
                                    v-on:click="startAddCategory" :disabled="is_system_defined || categorySaving">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="category-action is-delete"
                                    :title="deleteCategoryTitle"
                                    v-on:click="deleteCategory"
                                    :disabled="!canDeleteCategory || categorySaving">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="category-add-row" v-if="showAddCategory">
                                <input type="text" class="form-control" v-model="newCategoryName"
                                    maxlength="100"
                                    placeholder="{{ __('New category name') }}"
                                    v-on:keydown.enter.prevent="saveCategory">
                                <button type="button" class="btn btn-sm btn-primary btn_dark-x"
                                    v-on:click="saveCategory" :disabled="categorySaving">
                                    {{ __('Add') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                                    v-on:click="cancelAddCategory" :disabled="categorySaving">
                                    {{ __('Cancel') }}
                                </button>
                            </div>
                            <small class="field-help is-danger" v-if="categoryError">@{{ categoryError }}</small>
                        </div>
                    </div>
                </div>
            </fieldset>

            <div class="vc-form-buttons">
                <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d" v-bind:href="urls.button_cancel_url">
                    <i class="fa fa-undo"></i> {{ __('Cancel') }}
                </a>

                <button v-if="!is_system_defined" type="submit" class="btn btn-primary btn_dark-x">
                    <i class="fa fa-check"></i> {{ __('Save') }}
                </button>
            </div>
        </form>
    </template>
</div>

<script>
    var vue_details_tab_edit = null;

    window.validateAccountBeforeSend = function () {
        var app = vue_details_tab_edit;
        if (!app || app.is_system_defined) {
            return false;
        }

        // Existing account updates require password confirmation
        if (app.isEditMode) {
            var password = String(app.auth_password || '').trim();
            if (!password) {
                app.passwordError = '';
                app.showPasswordConfirm = true;
                app.$nextTick(function () {
                    var input = document.getElementById('account_auth_password');
                    if (input) {
                        input.focus();
                    }
                });
                return false;
            }
        }

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

        $('#vue-details-tab-edit').on('change', '#type', function () {
            if (!vue_details_tab_edit || !vue_details_tab_edit.account_info) {
                return;
            }
            vue_details_tab_edit.account_info.type = $(this).val() || '';
        });

        vue_details_tab_edit = new Vue({
            el: "#vue-details-tab-edit",
            data: function () {
                return Object.assign(
                    <?php echo json_encode($details_edit_vue); ?>,
                    {
                        auth_password: '',
                        showPasswordConfirm: false,
                        passwordError: '',
                        passwordSubmitting: false,
                        newCategoryName: '',
                        showAddCategory: false,
                        categoryError: '',
                        categorySaving: false,
                    }
                );
            },
            computed: {
                isEditMode: function () {
                    return parseInt(this.account_id, 10) > 0;
                },
                filteredCategories: function () {
                    var type = this.account_info && this.account_info.type ? this.account_info.type : '';
                    return (this.categories || []).filter(function (cat) {
                        return cat.type === type;
                    });
                },
                selectedCategory: function () {
                    var name = this.account_info && this.account_info.category ? this.account_info.category : '';
                    if (!name) {
                        return null;
                    }
                    var match = this.filteredCategories.filter(function (cat) {
                        return cat.name === name;
                    });
                    return match.length ? match[0] : null;
                },
                canDeleteCategory: function () {
                    return !this.is_system_defined && this.selectedCategory && Number(this.selectedCategory.is_system) !== 1;
                },
                deleteCategoryTitle: function () {
                    if (this.selectedCategory && Number(this.selectedCategory.is_system) === 1) {
                        return '{{ __('System categories cannot be deleted.') }}';
                    }
                    return '{{ __('Delete category') }}';
                }
            },
            watch: {
                'account_info.type': function () {
                    var name = this.account_info.category || '';
                    if (!name) {
                        return;
                    }
                    var stillThere = this.filteredCategories.some(function (cat) {
                        return cat.name === name;
                    });
                    if (!stillThere) {
                        this.account_info.category = '';
                    }
                }
            },
            methods: {
                startAddCategory: function () {
                    this.categoryError = '';
                    if (!this.account_info.type) {
                        this.categoryError = '{{ __('Select a type before adding a category.') }}';
                        return;
                    }
                    this.showAddCategory = true;
                    var self = this;
                    this.$nextTick(function () {
                        var input = self.$el.querySelector('.category-add-row input');
                        if (input) {
                            input.focus();
                        }
                    });
                },
                cancelAddCategory: function () {
                    this.showAddCategory = false;
                    this.newCategoryName = '';
                    this.categoryError = '';
                },
                categoryAjaxError: function (xhr) {
                    var body = xhr.responseJSON || {};
                    if (body.errors) {
                        var firstKey = Object.keys(body.errors)[0];
                        if (firstKey && body.errors[firstKey] && body.errors[firstKey][0]) {
                            return body.errors[firstKey][0];
                        }
                    }
                    return body.message || '{{ __('Unable to update the category.') }}';
                },
                rememberCategory: function (category) {
                    if (!category) {
                        return;
                    }
                    var exists = (this.categories || []).some(function (cat) {
                        return Number(cat.category_id) === Number(category.category_id);
                    });
                    if (!exists) {
                        this.categories.push(category);
                    }
                    this.account_info.category = category.name;
                },
                saveCategory: function () {
                    var name = String(this.newCategoryName || '').trim();
                    this.categoryError = '';
                    if (!this.account_info.type) {
                        this.categoryError = '{{ __('Select a type before adding a category.') }}';
                        return;
                    }
                    if (!name) {
                        this.categoryError = '{{ __('Category name is required.') }}';
                        return;
                    }

                    var self = this;
                    this.categorySaving = true;
                    $.ajax({
                        url: this.urls.category_store_url,
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            name: name,
                            type: this.account_info.type
                        }
                    }).done(function (response) {
                        self.rememberCategory(response.category);
                        self.newCategoryName = '';
                        self.showAddCategory = false;
                    }).fail(function (xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.category) {
                            self.rememberCategory(xhr.responseJSON.category);
                            self.showAddCategory = false;
                            self.newCategoryName = '';
                        }
                        self.categoryError = self.categoryAjaxError(xhr);
                    }).always(function () {
                        self.categorySaving = false;
                    });
                },
                deleteCategory: function () {
                    var category = this.selectedCategory;
                    if (!category || Number(category.is_system) === 1) {
                        return;
                    }
                    if (!window.confirm('{{ __('Delete this category?') }}')) {
                        return;
                    }

                    var self = this;
                    this.categoryError = '';
                    this.categorySaving = true;
                    $.ajax({
                        url: this.urls.category_delete_url + '/' + category.category_id,
                        method: 'DELETE',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    }).done(function () {
                        self.categories = (self.categories || []).filter(function (cat) {
                            return Number(cat.category_id) !== Number(category.category_id);
                        });
                        self.account_info.category = '';
                    }).fail(function (xhr) {
                        self.categoryError = self.categoryAjaxError(xhr);
                    }).always(function () {
                        self.categorySaving = false;
                    });
                },
                cancelPasswordConfirm: function () {
                    this.showPasswordConfirm = false;
                    this.auth_password = '';
                    this.passwordError = '';
                    this.passwordSubmitting = false;
                },
                confirmPasswordAndSave: function () {
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

                    var self = this;
                    this.$nextTick(function () {
                        self.passwordSubmitting = false;
                    });
                }
            }
        });

        $(document).off('ajaxError.accountEditPassword').on('ajaxError.accountEditPassword', function (event, jqxhr) {
            if (!vue_details_tab_edit || !vue_details_tab_edit.isEditMode) {
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
                    var input = document.getElementById('account_auth_password');
                    if (input) {
                        input.focus();
                    }
                });
            }
        });
    });
</script>
