<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<style>
  #vue-list-tab-services.beautician-services {
    padding: 4px 0 8px;
  }

  #vue-list-tab-services .services-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 12px;
  }

  #vue-list-tab-services .services-search {
    flex: 1;
    min-width: 220px;
    height: 38px;
    border: none;
    outline: none;
    border-radius: 999px;
    background: #F0F0F3;
    padding: 8px 14px;
    font-size: 14px;
    color: #1D1D1D;
  }

  #vue-list-tab-services .services-meta {
    font-size: 12px;
    color: #6c757d;
    font-weight: 600;
  }

  #vue-list-tab-services .services-panel {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 10px;
    max-height: min(560px, calc(100vh - 280px));
    overflow-y: auto;
  }

  #vue-list-tab-services .category-group {
    background: #fff;
    border-radius: 12px;
    margin-bottom: 10px;
    overflow: hidden;
  }

  #vue-list-tab-services .category-group:last-child {
    margin-bottom: 0;
  }

  #vue-list-tab-services .category-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    background: #F0F0F3;
    cursor: pointer;
    user-select: none;
  }

  #vue-list-tab-services .category-header:hover {
    background: #E8E8EC;
  }

  #vue-list-tab-services .category-toggle {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    color: #1D1D1D;
    flex-shrink: 0;
  }

  #vue-list-tab-services .category-title {
    flex: 1;
    min-width: 0;
    font-weight: 600;
    color: #1D1D1D;
    line-height: 1.3;
  }

  #vue-list-tab-services .category-parent {
    display: block;
    font-size: 11px;
    color: #6c757d;
    font-weight: 500;
    margin-bottom: 1px;
  }

  #vue-list-tab-services .category-count {
    font-size: 11px;
    font-weight: 600;
    color: #6c757d;
    background: #fff;
    border-radius: 999px;
    padding: 4px 8px;
    white-space: nowrap;
  }

  #vue-list-tab-services .category-select-all {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin: 0;
    font-size: 12px;
    font-weight: 600;
    color: #52A8D9;
    cursor: pointer;
    white-space: nowrap;
  }

  #vue-list-tab-services .category-body {
    padding: 8px;
  }

  #vue-list-tab-services .service-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin: 0 0 6px;
    padding: 10px 12px;
    border-radius: 10px;
    background: #F7F7F9;
    cursor: pointer;
    color: #1D1D1D;
    font-weight: 500;
    line-height: 1.35;
  }

  #vue-list-tab-services .service-item:last-child {
    margin-bottom: 0;
  }

  #vue-list-tab-services .service-item:hover {
    background: #F0F0F3;
  }

  #vue-list-tab-services .service-item.is-checked {
    background: rgba(230, 205, 127, 0.35);
  }

  #vue-list-tab-services .service-item input {
    margin-top: 3px;
  }

  #vue-list-tab-services .empty-state {
    text-align: center;
    padding: 28px 12px;
    color: #6c757d;
  }

  #vue-list-tab-services .vc-form-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 14px;
  }
</style>

<div id="vue-list-tab-services" class="beautician-services has-vue-form">
    <template>
        <form class="voco-ajax-form" method="post" v-bind:action="form_save_url">
            @csrf
            <input type="hidden" name="beautician_id" v-model="beautician_id">
            <input type="hidden" name="tab_name" v-model="tab_name">

            <div class="services-toolbar">
                <input type="text" class="services-search" v-model="service_search"
                    placeholder="{{ __('Search services or categories...') }}">
                <label class="brw-checkbox-browz-dds mb-0">
                    <input class="brow-checkbox-dd" type="checkbox" v-model="selectAll" @change="toggleSelectAll">
                    <span style="color: #52A8D9">{{ __('Select All') }}</span>
                </label>
                <span class="services-meta">
                    @{{ assigned_services.length }} / @{{ (db_all_services || []).length }} {{ __('selected') }}
                </span>
            </div>

            <div class="services-panel">
                <template v-if="groupedServices.length">
                    <div class="category-group" v-for="group in groupedServices" :key="group.key">
                        <div class="category-header" @click="toggleCategory(group.key)">
                            <span class="category-toggle">
                                <i class="fa" :class="isExpanded(group.key) ? 'fa-minus' : 'fa-plus'" aria-hidden="true"></i>
                            </span>
                            <div class="category-title">
                                <span class="category-parent" v-if="group.parent_name">@{{ group.parent_name }}</span>
                                @{{ group.name }}
                            </div>
                            <span class="category-count">
                                @{{ selectedCountInGroup(group) }} / @{{ group.services.length }}
                            </span>
                            <label class="category-select-all" @click.stop>
                                <input type="checkbox"
                                    :checked="isGroupFullySelected(group)"
                                    :indeterminate.prop="isGroupPartiallySelected(group)"
                                    @change="toggleGroupSelection(group, $event.target.checked)">
                                <span>{{ __('All') }}</span>
                            </label>
                        </div>

                        <div class="category-body" v-show="isExpanded(group.key)">
                            <label class="service-item"
                                v-for="service in group.services"
                                :key="service.service_id"
                                :class="{ 'is-checked': isServiceSelected(service.service_id) }">
                                <input class="brow-checkbox-dd"
                                    type="checkbox"
                                    name="services[]"
                                    v-bind:value="service.service_id"
                                    :checked="isServiceSelected(service.service_id)"
                                    @change="toggleService(service.service_id, $event.target.checked)">
                                <span>@{{ formatServiceName(service.name) }}</span>
                            </label>
                        </div>
                    </div>
                </template>
                <div v-else class="empty-state">
                    <strong>{{ __('No matching services') }}</strong>
                </div>
            </div>

            <div class="vc-form-buttons">
                <a class="btn btn-secondary vc-form-btn-cancel brownz-btn-yellow-d"
                    v-bind:href="button_cancel_url">
                    <i class="fa fa-undo"></i> {{ __('Cancel') }}
                </a>

                <button type="submit" class="btn btn-primary btn_dark-x">
                    <i class="fa fa-check"></i> {{ __('Save') }}
                </button>
            </div>
        </form>
    </template>
</div>

<script>
    jQuery(function($) {
        new Vue({
            el: "#vue-list-tab-services",
            data: Object.assign({
                service_search: '',
                selectAll: false,
                expandedCategories: {},
                categoriesInitialized: false,
            }, <?php echo json_encode($details_service_vue); ?>),
            computed: {
                filteredServices() {
                    var list = this.db_all_services || [];
                    var term = (this.service_search || '').toString().trim().toLowerCase();
                    if (!term) {
                        return list;
                    }
                    return list.filter(function(service) {
                        var name = (service.name || '').toString().toLowerCase();
                        var category = service.category && service.category.name
                            ? service.category.name.toString().toLowerCase()
                            : '';
                        var parent = service.category && service.category.parent && service.category.parent.name
                            ? service.category.parent.name.toString().toLowerCase()
                            : '';
                        return name.indexOf(term) !== -1
                            || category.indexOf(term) !== -1
                            || parent.indexOf(term) !== -1;
                    });
                },
                groupedServices() {
                    var groupsMap = {};
                    var groups = [];

                    this.filteredServices.forEach(function(service) {
                        var category = service.category || null;
                        var parentName = category && category.parent && category.parent.name
                            ? category.parent.name
                            : '';
                        var categoryName = category && category.name
                            ? category.name
                            : "{{ __('Uncategorized') }}";
                        var categoryId = category && category.category_id
                            ? category.category_id
                            : 'none';
                        var key = String(categoryId);

                        if (!groupsMap[key]) {
                            groupsMap[key] = {
                                key: key,
                                name: categoryName,
                                parent_name: parentName,
                                sort_name: (parentName ? parentName + ' ' : '') + categoryName,
                                services: []
                            };
                            groups.push(groupsMap[key]);
                        }

                        groupsMap[key].services.push(service);
                    });

                    groups.sort(function(a, b) {
                        return a.sort_name.localeCompare(b.sort_name);
                    });

                    return groups;
                }
            },
            watch: {
                assigned_services(newVal) {
                    var total = (this.db_all_services || []).length;
                    this.selectAll = total > 0 && newVal.length === total;
                },
                groupedServices: {
                    immediate: true,
                    handler(groups) {
                        if (!groups || !groups.length) {
                            return;
                        }

                        var nextExpanded = Object.assign({}, this.expandedCategories);
                        var searching = !!(this.service_search || '').toString().trim();
                        var self = this;

                        groups.forEach(function(group, index) {
                            if (searching) {
                                nextExpanded[group.key] = true;
                            } else if (typeof nextExpanded[group.key] === 'undefined') {
                                var hasSelected = self.selectedCountInGroup(group) > 0;
                                nextExpanded[group.key] = hasSelected || index < 2;
                            }
                        });

                        this.expandedCategories = nextExpanded;
                        this.categoriesInitialized = true;
                    }
                }
            },
            methods: {
                formatServiceName(name) {
                    return (name || '').toString().replace(/_/g, ' ');
                },
                normalizeId(id) {
                    return Number(id);
                },
                isServiceSelected(serviceId) {
                    var id = this.normalizeId(serviceId);
                    return (this.assigned_services || []).some(function(selected) {
                        return Number(selected) === id;
                    });
                },
                toggleService(serviceId, checked) {
                    var id = this.normalizeId(serviceId);
                    var current = (this.assigned_services || []).map(Number);
                    if (checked) {
                        if (current.indexOf(id) === -1) {
                            current.push(id);
                        }
                    } else {
                        current = current.filter(function(item) {
                            return item !== id;
                        });
                    }
                    this.assigned_services = current;
                },
                isExpanded(key) {
                    return !!this.expandedCategories[key];
                },
                toggleCategory(key) {
                    this.$set(this.expandedCategories, key, !this.expandedCategories[key]);
                },
                selectedCountInGroup(group) {
                    var self = this;
                    return (group.services || []).filter(function(service) {
                        return self.isServiceSelected(service.service_id);
                    }).length;
                },
                isGroupFullySelected(group) {
                    return (group.services || []).length > 0
                        && this.selectedCountInGroup(group) === group.services.length;
                },
                isGroupPartiallySelected(group) {
                    var selected = this.selectedCountInGroup(group);
                    return selected > 0 && selected < (group.services || []).length;
                },
                toggleGroupSelection(group, checked) {
                    var current = (this.assigned_services || []).map(Number);
                    var ids = (group.services || []).map(function(service) {
                        return Number(service.service_id);
                    });

                    if (checked) {
                        ids.forEach(function(id) {
                            if (current.indexOf(id) === -1) {
                                current.push(id);
                            }
                        });
                    } else {
                        current = current.filter(function(id) {
                            return ids.indexOf(id) === -1;
                        });
                    }

                    this.assigned_services = current;
                },
                toggleSelectAll() {
                    if (this.selectAll) {
                        this.assigned_services = (this.db_all_services || []).map(function(service) {
                            return Number(service.service_id);
                        });
                    } else {
                        this.assigned_services = [];
                    }
                }
            },
            mounted() {
                this.assigned_services = (this.assigned_services || []).map(Number);
                var total = (this.db_all_services || []).length;
                this.selectAll = total > 0 && this.assigned_services.length === total;
            }
        });
    });
</script>
