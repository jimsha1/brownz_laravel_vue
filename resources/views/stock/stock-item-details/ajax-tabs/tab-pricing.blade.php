<?php
/*
 * This blade is using VueJs for rendering values.
 * Do not use server side PHP variables directly inside!
 */
?>
<div id="vue-details-tab-pricing" class="has-vue-form">
    <template>
        <div class="card" style="background-color:#a4bfc8">
            <div class="card-body">
                <form class="voco-ajax-form form-inline" method="post" v-bind:action="urls.form_save_url">
                    @csrf
                    <input type="hidden" name="item_id" v-model="item_id">
                    <input type="hidden" name="tab_name" v-model="tab_name">
                    <input type="hidden" name="price_id" v-model="price_id">


                    <input type="text" class="form-control mb-2 mr-sm-2" id="price_list_quantity" name="quantity"
                        placeholder="Quantity" value="0" required>

                    <input type="text" class="form-control mb-2 mr-sm-2" id="price_list_cost_price" name="cost_price"
                        placeholder="Cost Price" required>

                    <input type="text" class="form-control mb-2 mr-sm-2" id="price_list_selling_price"
                        name="selling_price" placeholder="Selling Price" required>

                    <button type="submit" class="btn btn-primary mb-2">
                        <i class="fa fa-check"></i> Save
                    </button>
                </form>
            </div>
        </div>

        <div class="price-list-panel">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('SL#') }}</th>
                            <th>Quantity</th>
                            <th>Cost Price</th>
                            <th>Selling Price</th>
                            <th class="hidden-print no-sort">Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in price_list">
                            <td>@{{ index + 1 }}</td>
                            <td>@{{ item.quantity }}</td>
                            <td>@{{ item.cost_price }}</td>
                            <td>@{{ item.selling_price }}</td>
                            <td class="hidden-print">
                                <a href="#" @click.prevent="editItem(item)">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </template>
</div>


<script>
    var vue_details_tab_pricing = null;
    jQuery(function($) {
        vue_details_tab_pricing = new Vue({
            el: "#vue-details-tab-pricing",
            data: {
                ...(<?php echo json_encode($db_results['vue_data']['vue_list_tab_pricing']); ?>),
                price_id: '',
                isDisabled: false,
            },
            methods: {
                editItem(item) {

                    this.isDisabled = true;
                    document.getElementById('price_list_quantity').value = item.quantity;
                    document.getElementById('price_list_cost_price').value = item.cost_price;
                    document.getElementById('price_list_selling_price').value = item.selling_price;

                    // Disable the quantity and cost_price fields
                    document.getElementById('price_list_quantity').readOnly = true;
                    document.getElementById('price_list_cost_price').readOnly = true;

                    this.item_id = item.item_id;
                    this.price_id = item.price_id;
                },
            },
            mounted() {
                console.log('Price list loaded:', this.price_list);
                if (this.price_list && this.price_list.length > 0) {
                    this.isDisabled = true;
                    const firstItem = this.price_list[0];

                    document.getElementById('price_list_quantity').value = firstItem.quantity;
                    document.getElementById('price_list_cost_price').value = firstItem.cost_price;
                    document.getElementById('price_list_selling_price').value = firstItem.selling_price;


                    // Disable the quantity and cost_price fields
                    document.getElementById('price_list_quantity').readOnly = true;
                    document.getElementById('price_list_cost_price').readOnly = true;

                    this.price_id = firstItem.price_id;
                } else {
                    console.log('Price list is empty or not available');
                }
            },
        });
    });
</script>
