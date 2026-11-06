<style>
  #vue-details-tab-used-products.service-used-products {
    padding: 4px 0 8px;
  }

  #vue-details-tab-used-products .edit-section {
    background: #F7F7F9;
    border-radius: 14px;
    padding: 16px 16px 8px;
    margin-bottom: 16px;
  }

  #vue-details-tab-used-products .edit-section-title {
    font-size: 13px;
    font-weight: 600;
    color: #1D1D1D;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  #vue-details-tab-used-products .edit-hint {
    font-size: 12px;
    color: #6c757d;
    margin: -4px 0 14px;
  }

  #vue-details-tab-used-products .used-table th,
  #vue-details-tab-used-products .used-table td {
    vertical-align: middle;
  }

  #vue-details-tab-used-products .vc-form-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }
</style>

<div id="vue-details-tab-used-products" class="brw-form-la service-used-products">
    <form class="voco-ajax-form" method="post" v-bind:action="urls.form_save_url">
        @csrf
        <input type="hidden" name="service_id" v-model="service_id">
        <input type="hidden" name="tab_name" v-model="tab_name">

        <div class="edit-section">
            <p class="edit-section-title">{{ __('Products used') }} — @{{ service_name }}</p>
            <p class="edit-hint">{{ __('Set the products and quantity used each time this service is performed. Only products marked Sales Item = No are listed. Stock is reduced when the booking service is completed. The client is not charged for these products.') }}</p>

            <div class="row align-items-end mb-3">
                <vcinput-select2 id="used_product_item_id" label="{{ __('Product') }}"
                    placeholder="{{ __('Search product name or code') }}"
                    colclass="col-lg-5 col-md-6 mb-2"
                    data-allow-clear="true"
                    v-model="new_item_id"
                    v-bind:options="productOptions">
                </vcinput-select2>
                <div class="col-lg-3 col-md-3 mb-2">
                    <label for="used_product_qty">{{ __('Quantity per service') }}</label>
                    <input id="used_product_qty" type="number" min="0.001" step="0.001" class="form-control" v-model.number="new_quantity">
                </div>
                <div class="col-lg-3 col-md-3 mb-2">
                    <button type="button" class="btn btn-primary btn_dark-x" v-on:click="addProduct">
                        <i class="fa fa-plus"></i> {{ __('Add') }}
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-borderless used-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Unit') }}</th>
                            <th style="width: 140px;">{{ __('Qty per service') }}</th>
                            <th>{{ __('On hand') }}</th>
                            <th>{{ __('Est. cost') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="products.length === 0">
                            <td colspan="6" class="text-muted">{{ __('No products added for this service.') }}</td>
                        </tr>
                        <tr v-for="(row, index) in products" :key="row.item_id">
                            <td>
                                @{{ row.item_name }}
                                <input type="hidden" :name="'products[' + index + '][item_id]'" :value="row.item_id">
                            </td>
                            <td>@{{ row.uom_name || '—' }}</td>
                            <td>
                                <input type="number" min="0.001" step="0.001" class="form-control"
                                    :name="'products[' + index + '][quantity]'"
                                    v-model.number="row.quantity">
                            </td>
                            <td>@{{ Number(row.current_stock || 0).toFixed(3) }}</td>
                            <td>@{{ lineCost(row) }}</td>
                            <td>
                                <button type="button" class="btn btn-link text-danger py-0" v-on:click="removeProduct(index)">
                                    <i class="fa fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

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
    var vue_details_tab_used_products = null;
    jQuery(function ($) {
        vue_details_tab_used_products = new Vue({
            el: "#vue-details-tab-used-products",
            data: <?php echo json_encode($details_edit_vue); ?>,
            computed: {
                productOptions: function () {
                    return this.stock_items.map(function (item) {
                        var label = item.item_name || '';
                        if (item.item_code) {
                            label += ' (' + item.item_code + ')';
                        }
                        if (item.uom_name) {
                            label += ' — ' + item.uom_name;
                        }
                        return {
                            id: item.item_id,
                            text: label
                        };
                    });
                }
            },
            methods: {
                lineCost: function (row) {
                    var pack = Number(row.pack_size || 1);
                    if (pack <= 0) {
                        pack = 1;
                    }
                    var cost = (Number(row.quantity || 0) * Number(row.cost_price || 0)) / pack;
                    return cost.toFixed(2);
                },
                addProduct: function () {
                    var itemId = parseInt(this.new_item_id, 10);
                    var qty = Number(this.new_quantity);
                    if (!itemId) {
                        alert('{{ __('Select a product.') }}');
                        return;
                    }
                    if (!qty || qty <= 0) {
                        alert('{{ __('Enter a quantity greater than zero.') }}');
                        return;
                    }
                    var alreadyAdded = this.products.some(function (row) {
                        return parseInt(row.item_id, 10) === itemId;
                    });
                    if (alreadyAdded) {
                        alert('{{ __('This product is already added.') }}');
                        return;
                    }
                    var item = this.stock_items.find(function (row) {
                        return parseInt(row.item_id, 10) === itemId;
                    });
                    if (!item) {
                        return;
                    }
                    this.products.push({
                        item_id: item.item_id,
                        item_name: item.item_name,
                        item_code: item.item_code,
                        uom_name: item.uom_name,
                        quantity: qty,
                        current_stock: item.current_stock,
                        cost_price: item.cost_price,
                        pack_size: item.pack_size
                    });
                    this.new_item_id = '';
                    this.new_quantity = 1;
                    this.$nextTick(function () {
                        var $product = $('#used_product_item_id');
                        if ($product.length && $product.data('select2')) {
                            $product.val(null).trigger('change');
                        }
                    });
                },
                removeProduct: function (index) {
                    this.products.splice(index, 1);
                },
                cancelForm: function () {
                    window.location.href = this.urls.button_cancel_url;
                }
            }
        });
    });
</script>
