<div class="col-xl-8 col-lg-7 mt-2 px-0 " style="    position: sticky;
top: 68px;
height: 100%;">
    <div class="table-responsive tableFixHead brownz-x-table" style="height: 500px;
  overflow: auto;">
        <table class="table table-borderless">
            <thead class="thead-light">
                <tr>
                    <th 
                        v-if="booking_info.booking_type != 'Refund'  && booking_info.payment_status == 'completed' && booking_info.booking_type != 'Refunded'">
                    </th>
                    <th>{{ __('Item1') }}</th>
                    <th>{{ __('Quantity') }}</th>
                    <th>{{ __('Price') }}</th>
                    <th>{{ __('Discount') }}</th>
                    <th>{{ __('Net') }}</th>
                    <th>{{ __('Tax') }}</th>
                    <th>{{ __('Total') }}</th>

                    <!-- <th v-if="!booking_info.canceled_by" class="text-center">{{ __('Remove') }}</th> -->
                </tr>
            </thead>
            <tbody class="bg-even-model">
                <template
                    v-if="booking_info.booking_items.length > 0 &&  booking_info.booking_type != 'Refund' && booking_info.booking_type != 'Refunded'"
                    v-for="o_item in booking_info.booking_items">
                    <tr class="first-row-table">

                        <td v-if="booking_info.booking_type != 'Refund'  && booking_info.payment_status == 'completed' && o_item.status != 'refunded'">
                            <input v-if="o_item.quantity != o_item.refunded_qty" name="item[]" :value="o_item.booking_item_id" type="checkbox">
                        </td>

                        <td v-if="o_item.quantity == o_item.refunded_qty"></td>
                        <td>@{{ o_item.item_name_locale }} </td>
                        <td>
                            <input type="number"
                                :id="qty + o_item.booking_item_id" class="form-control"
                                v-bind:value="o_item.quantity"
                                :disabled="booking_info.canceled_by > 0"
                                v-on:input="onProductQtyInput(o_item,o_item.discount_percent,o_item.booking_id,o_item.selling_price,o_item.tax_amount,o_item.booking_item_id,'#'+qty+o_item.booking_item_id)"
                                v-on:change="onProductQtyInput(o_item,o_item.discount_percent,o_item.booking_id,o_item.selling_price,o_item.tax_amount,o_item.booking_item_id,'#'+qty+o_item.booking_item_id, true)"
                                placeholder="{{ __('qty1') }}" style="max-width:90px"
                                min="1">
                        </td>

                        <td>@{{ o_item.selling_price }}</td>
                        <td v-if="o_item.individual_discount_amt == 0">@{{ v_fixed_amount(o_item.discount_amount / o_item.quantity) }}</td>
                        <td v-if="o_item.individual_discount_amt > 0">@{{ v_fixed_amount(o_item.individual_discount_amt / o_item.quantity) }}</td>

                        <td v-if="o_item.individual_discount_amt == 0"
                            :id="net_item_html_id + o_item.booking_item_id">
                            @{{ v_totalNetTotal(o_item.selling_price - (o_item.discount_amount / o_item.quantity), o_item.individual_discount_amt, o_item.quantity) }}</td>
                        <td v-if="o_item.individual_discount_amt > 0" :id="net_item_html_id + o_item.booking_item_id">
                            @{{ v_totalNetTotal(o_item.selling_price, o_item.individual_discount_amt, o_item.quantity) }}</td>

                        <td :id="tax_item_html_id + o_item.booking_item_id">@{{ o_item.tax_amount }}</td>

                        <td colspan="3" v-if="o_item.line_total >= 0" :id="total_item_html_id + o_item.booking_item_id">
                            @{{ o_item.line_total }}</td>
                        <td colspan="3" v-if="o_item.line_total < 0" :id="total_item_html_id + o_item.booking_item_id">

                    </tr>
                    <tr>
                        {{-- <td
                            v-if="booking_info.booking_type != 'Refund'  && isBookingCompleted_type != 'Refunded'">
                        </td> --}}
                        <td :colspan="(booking_info.booking_type != 'Refund' && booking_info.payment_status == 'completed' && booking_info.booking_type != 'Refunded') ? 2 : 1">
                            <p class="mb-0"><span :id="beatician_html_id + o_item.booking_item_id"
                                    v-if="o_item.beautician">
                                    @{{ o_item.beautician.name_locale }}
                                    (@{{ o_item.beautician.beautician_code }})

                                </span>
                                <span v-else class="text-muted font-italic">
                                    {{ __('Unassigned') }}
                                </span>
                            </p>

                        </td>
                        <td colspan="5">
                            <div v-if="!isBookingCompleted"
                                class="row pull-left brownz-discount-mod-c">
                                <form id="line_discount_form+o_item.booking_item_id"
                                    class="form-inline voco-ajax-form float-right" method="post"
                                    v-bind:action="urls.discount_save_url">
                                    @csrf
                                    <input type="hidden" name="booking_id" v-model="booking_id">
                                    <input type="hidden" name="tab_name" v-model="tab_name">
                                    <input type="hidden" :id="service_id_var + o_item.booking_item_id"
                                        name="item_id">
                                    <input type="hidden" :id="total_price_discount + o_item.booking_item_id"
                                        name="total_price">
                                    <input v-if="o_item.individual_discount_percent > 0"
                                        v-bind:disabled="booking_info.canceled_by > 0" type="text"
                                        :id="discount_percent_item + o_item.booking_item_id"
                                        class="form-control"
                                        v-bind:value="o_item.individual_discount_percent"
                                        v-on:change="lineDiscountItem($event,o_item,'#'+line_discount_form+o_item.booking_item_id,'#'+service_id_var+o_item.booking_item_id,'#'+total_price_discount+o_item.booking_item_id,'#'+discount_percent_item+o_item.booking_item_id)"
                                        name="applied_discount_percent" placeholder="{{ __('Discount') }}"
                                        style="max-width:90px">
                                    <input v-if="o_item.individual_discount_percent == 0"
                                        v-bind:disabled="booking_info.canceled_by > 0" type="text"
                                        :id="discount_percent_item + o_item.booking_item_id"
                                        class="form-control" v-bind:value="o_item.discount_percent"
                                        v-on:change="lineDiscountItem($event,o_item,'#'+line_discount_form+o_item.booking_item_id,'#'+service_id_var+o_item.booking_item_id,'#'+total_price_discount+o_item.booking_item_id,'#'+discount_percent_item+o_item.booking_item_id)"
                                        name="applied_discount_percent" placeholder="{{ __('Discount') }}"
                                        style="max-width:90px">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </form>
                            </div>
                        </td>


                        <td colspan="4" class="text-right">
                            <template v-if="!booking_info.canceled_by && booking_info.payment_status != 'completed'">
                                <form v-bind:action="urls.item_form_delete_url" class="voco-ajax-form" method="post"
                                    data-hook-success="bookingServiceDeleted" style="display:inline-block">
                                    @csrf
                                    <input type="hidden" name="booking_id" v-model="booking_id">
                                    <input type="hidden" name="item_id" v-model="o_item.item_id">
                                    <input type="hidden" name="price_id" v-model="o_item.price_id">
                                    <input type="hidden" name="booking_item_id" v-model="o_item.booking_item_id">
                                    <button type="button"
                                        class="btn btn-link text-danger py-0 booking-line-remove brownz-close-btn">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </form>
                            </template>
                        </td>

                    </tr>
                </template>
                <template
                    v-if="booking_info.booking_only_services.length > 0 && booking_info.booking_type != 'Refund' && booking_info.booking_type != 'Refunded'"
                    v-for="bserv in booking_info.booking_only_services">
                    <tr class="first-row-table">
                        <td v-if="booking_info.booking_type != 'Refund' && booking_info.payment_status == 'completed' && booking_info.booking_type != 'Refunded' && bserv.status != 'refunded' && Number(bserv.quantity || 1) > Number(bserv.refunded_qty || 0)">
                            <input name="service[]" :value="bserv.booking_service_id" type="checkbox">
                        </td>
                        <td v-else-if="booking_info.payment_status == 'completed' && (bserv.status == 'refunded' || Number(bserv.refunded_qty || 0) >= Number(bserv.quantity || 1))"></td>
                        <td  class="bold-text">@{{ bserv.service_name_locale }}</td>

                        <td :id="quantity_html_id + bserv.quantity">
                            <input type="number" v-model.number="bserv.quantity"
                                :disabled="booking_info.canceled_by > 0"
                                v-on:change="updateServiceQuantity(bserv.booking_service_id, bserv.service_booking_price, bserv.tax_percent, bserv.quantity,bserv.discount_percent,bserv.individual_discount_percent)"
                                class="form-control" style="width: 100px;" placeholder="Enter Quantity" />
                        </td>

                        <td :id="price_html_id + bserv.booking_service_id">
                            <input :disabled="booking_info.canceled_by > 0" type="number"
                                v-model.number="bserv.service_booking_price" {{-- v-on:input="updateServicePrice(bserv.booking_service_id, bserv.service_booking_price, bserv.tax_percent)" --}}
                                @blur="onPriceBlur(bserv)" class="form-control" style="width: 100px;"
                                placeholder="Enter Price" />
                        </td>

                        <td>
                            @{{ v_fixed_amount(calculateDiscount(bserv.service_booking_price, bserv.quantity, bserv.individual_discount_percent, bserv.discount_percent)) }}
                        </td>


                        <td :id="net_html_id + bserv.booking_service_id">
                            @{{ v_totalNetTotal(
    bserv.service_booking_price,
    calculateDiscount(
        bserv.service_booking_price,
        bserv.quantity,
        bserv.individual_discount_percent,
        bserv.discount_percent
    ),
    bserv.quantity
) }}
                        </td>



                        <td  :id="tax_html_id + bserv.booking_service_id">
                            @{{ v_totalTaxTotal(
    bserv.service_booking_price,
    bserv.quantity,
    calculateDiscount(
        bserv.service_booking_price,
        bserv.quantity,
        bserv.individual_discount_percent,
        bserv.discount_percent
    ),
    bserv.tax_percent
) }}
                        </td>




                        <td colspan="3" :id="total_html_id + bserv.booking_service_id">
                            @{{ v_totalPrice(
    v_totalNetTotal(
        bserv.service_booking_price,
        calculateDiscount(
            bserv.service_booking_price,
            bserv.quantity,
            bserv.individual_discount_percent,
            bserv.discount_percent
        ),
        bserv.quantity
    ),
    v_totalTaxTotal(
        bserv.service_booking_price,
        bserv.quantity,
        calculateDiscount(
            bserv.service_booking_price,
            bserv.quantity,
            bserv.individual_discount_percent,
            bserv.discount_percent
        ),
        bserv.tax_percent
    )
) }}
                        </td>


                    </tr>
                    <tr>
                        <td
                            v-if="booking_info.booking_type != 'Refund'  && booking_info.payment_status == 'completed' && booking_info.booking_type != 'Refunded'">
                        </td>
                        <td :colspan="(booking_info.booking_type != 'Refund' && booking_info.payment_status == 'completed' && booking_info.booking_type != 'Refunded') ? 9 : 9">
                            <p class="mb-0">
                                <span :id="beatician_html_id + bserv.booking_service_id" v-if="serviceBeauticianSummary(bserv)">
                                    @{{ serviceBeauticianSummary(bserv) }}
                                </span>
                                <span v-else class="text-muted font-italic">
                                    {{ __('Unassigned') }}
                                </span>
                                <span class="date-template-brownz"><i class="fa fa-calendar" aria-hidden="true"></i>
                                    @{{ bserv.appointment_date | vc_date_expanded }}
                                    <i class="fa fa-clock-o" aria-hidden="true"></i> @{{ convertTimeTo12hr(bserv.slot_start) }}</span>

                                <span class="btn btn-sm btn-primary brownz-btn-yellow"
                                    v-if="isBookingCompleted && hasMultiBeauticianSplit(bserv)"
                                    @click="split(bserv)">
                                    <i class="fa fa-edit"></i> {{ __('Edit Split') }}
                                </span>

                                <span class="btn btn-sm btn-primary brownz-btn-yellow"
                                    v-if="isBookingCompleted && hasMultiBeauticianSplit(bserv)"
                                    @click="openSplit(bserv)">
                                    {{ __('View Split') }}
                                </span>

                                <span class="btn btn-sm btn-primary brownz-btn-yellow ml-1"
                                    v-if="isBookingCompleted && bserv.status != 'refunded' && !hasMultiBeauticianSplit(bserv)"
                                    @click="split(bserv)"
                                    data-toggle="tooltip"
                                    title="{{ __('Split this service among multiple beauticians') }}">
                                    <i class="fa fa-users"></i> {{ __('Split Service') }}
                                </span>

                                <a class="btn btn-sm btn-primary brownz-btn-yellow ml-1"
                                    v-if="isBookingCompleted && bserv.status != 'refunded' && !hasMultiBeauticianSplit(bserv)"
                                    v-on:click="reShedule(booking_info.booking_id, booking_info.is_booking, bserv.booking_service_id, bserv.service_id, (bserv['beautician'] != null ? (bserv['beautician']['beautician_id'] != null ? bserv['beautician']['beautician_id']: 0) : 0) ,bserv.shift_id, bserv['service']['category_id'],bserv.appointment_date, bserv.slot_start)"
                                    data-toggle="tooltip" title="{{ __('Change beautician and time slot') }}">
                                    <i class="fa fa-edit"></i> {{ __('Change Beautician') }}
                                </a>

                                <span class="btn btn-sm btn-primary brownz-btn-yellow"
                                    v-if="isBookingCompleted && !hasMultiBeauticianSplit(bserv)"
                                    @click="openSplit(bserv)">
                                    {{ __('View Split') }}
                                </span>

                            </p>

                            <div class="service-used-products"
                                v-if="bserv.status != 'refunded'"
                                :class="{ 'is-panel': (bserv.used_products && bserv.used_products.length) || used_product_for == bserv.booking_service_id }">
                                <div class="used-products-head" v-if="(bserv.used_products && bserv.used_products.length) || used_product_for == bserv.booking_service_id">
                                    <span class="used-products-title">{{ __('Products used') }}</span>
                                    <button type="button" class="used-product-add-btn"
                                        v-if="!(booking_info.canceled_by > 0) && used_product_for != bserv.booking_service_id"
                                        v-on:click="openUsedProductSearch(bserv)">
                                        + {{ __('Add product') }}
                                    </button>
                                </div>
                                <div class="used-product-card" v-for="p in (bserv.used_products || [])" :key="p.booking_service_product_id">
                                    <div class="used-product-info">
                                        <div class="used-product-name">@{{ p.item_name }}</div>
                                        <div class="used-product-stock" v-if="p.current_stock != null">
                                            <span>{{ __('On hand') }} @{{ formatUsedQty(p.current_stock) }} @{{ p.uom_name || '' }}</span>
                                            <span class="used-product-left" :class="{ 'is-short': usedStockLeft(p) < 0 }">
                                                {{ __('After use') }} @{{ formatUsedQty(usedStockLeft(p)) }} @{{ p.uom_name || '' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="used-product-controls">
                                        <input type="number" min="0" step="0.001" class="used-product-qty"
                                            :class="{ 'is-short': usedStockLeft(p) < 0 }"
                                            v-model.number="p.quantity"
                                            :disabled="booking_info.canceled_by > 0"
                                            v-on:focus="p._previous_quantity = p.quantity"
                                            v-on:change="updateUsedProductQty(p)">
                                        <span class="used-product-uom">@{{ p.uom_name || '' }}</span>
                                        <button type="button" class="used-product-remove"
                                            v-if="!(booking_info.canceled_by > 0)"
                                            v-on:click="removeUsedProduct(p)"
                                            title="{{ __('Remove') }}">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="used-product-add-btn"
                                    v-if="!(booking_info.canceled_by > 0) && used_product_for != bserv.booking_service_id && !(bserv.used_products && bserv.used_products.length)"
                                    v-on:click="openUsedProductSearch(bserv)">
                                    + {{ __('Add product') }}
                                </button>
                                <div class="used-product-add" v-if="!(booking_info.canceled_by > 0) && used_product_for == bserv.booking_service_id">
                                    <input type="text" class="used-product-search" autofocus
                                        placeholder="{{ __('Search product name or code') }}"
                                        v-model="used_product_search"
                                        v-on:input="searchUsedProducts(bserv)">
                                    <div class="used-product-hits" v-if="used_product_hits.length">
                                        <button type="button" class="used-product-hit"
                                            v-for="hit in used_product_hits"
                                            :key="hit.item_id"
                                            v-on:click="addUsedProduct(bserv, hit)">
                                            <span class="used-product-hit-name">@{{ hit.item_name }}</span>
                                            <span class="used-product-hit-meta">
                                                <span v-if="hit.item_code">@{{ hit.item_code }} · </span>
                                                {{ __('On hand') }} @{{ formatUsedQty(hit.current_stock) }} @{{ hit.uom_name || '' }}
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="split-preview-chips mt-2"
                                v-if="isBookingCompleted && hasMultiBeauticianSplit(bserv)">
                                <div class="split-preview-title">{{ __('Split among') }}:</div>
                                <div class="split-preview-list">
                                    <span class="split-preview-chip"
                                        v-for="(draft, dIdx) in bserv.split_draft" :key="dIdx">
                                        <strong>@{{ draft.beautician ? (draft.beautician.name_locale || draft.beautician.name) : ('BT#' + draft.beautician_id) }}</strong>
                                        <em>₹ @{{ Number(draft.price || 0).toFixed(2) }}</em>
                                    </span>
                                </div>
                            </div>

                        </td>
                    </tr>
                    <tr v-if="!isBookingCompleted">
                        <td colspan="3">
                            <a class="btn btn-sm btn-primary brownz-btn-yellow"
                                v-if="bserv.status != 'refunded' && !hasMultiBeauticianSplit(bserv)"
                                v-on:click="reShedule(booking_info.booking_id, booking_info.is_booking, bserv.booking_service_id, bserv.service_id, (bserv['beautician'] != null ? (bserv['beautician']['beautician_id'] != null ? bserv['beautician']['beautician_id']: 0) : 0) ,bserv.shift_id, bserv['service']['category_id'],bserv.appointment_date, bserv.slot_start)"
                                data-toggle="tooltip" title="Re Shedule" data-original-title="Re Shedule"> <i
                                    class="fa fa-edit"></i> {{ __('Re Schedule') }}</a>
                        </td>
                        <td colspan="5">
                            <div v-if="!isBookingCompleted"
                                class="row pull-left brownz-discount-mod-c">
                                <form style="height: 42px" id="line_discount_form+bserv.booking_service_id"
                                    class="form-inline voco-ajax-form float-right" method="post"
                                    v-bind:action="urls.discount_save_url">
                                    @csrf
                                    <input type="hidden" name="booking_id" v-model="booking_id">
                                    <input type="hidden" name="tab_name" v-model="tab_name">
                                    <input type="hidden" :id="service_id_var + bserv.booking_service_id"
                                        name="service_id">
                                    <input type="hidden" :id="total_price_discount + bserv.booking_service_id"
                                        name="total_price">
                                    <input type="hidden" :id="'quantity_' + bserv.booking_service_id" name="quantity"
                                        :value="bserv.quantity">

                                    <input v-if="bserv.individual_discount_percent > 0"
                                        v-bind:disabled="booking_info.canceled_by > 0" type="text"
                                        :id="discount_percent + bserv.booking_service_id"
                                        class="form-control" v-bind:value="bserv.individual_discount_percent"
                                        v-on:change="lineDiscount($event,bserv,'#'+line_discount_form+bserv.booking_service_id,'#'+service_id_var+bserv.booking_service_id,'#'+total_price_discount+bserv.booking_service_id,'#'+discount_percent+bserv.booking_service_id,'#quantity_' + bserv.booking_service_id)"
                                        name="applied_discount_percent" placeholder="{{ __('Discount') }}"
                                        style="max-width:90px">
                                    <input v-if="bserv.individual_discount_percent == 0"
                                        v-bind:disabled="booking_info.canceled_by > 0" type="text"
                                        :id="discount_percent + bserv.booking_service_id"
                                        class="form-control" v-bind:value="bserv.discount_percent"
                                        v-on:change="lineDiscount($event,bserv,'#'+line_discount_form+bserv.booking_service_id,'#'+service_id_var+bserv.booking_service_id,'#'+total_price_discount+bserv.booking_service_id,'#'+discount_percent+bserv.booking_service_id)"
                                        name="applied_discount_percent" placeholder="{{ __('Discount') }}"
                                        style="max-width:90px">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </form>
                            </div>


                            <div style="margin-left: 90px">
                                <a class="btn btn-sm text-black split-action-btn"
                                    :class="{ 'split-action-done': hasMultiBeauticianSplit(bserv) }"
                                    v-if="bserv && bserv.status !== 'refunded' && !isBookingCompleted"
                                    @click="split(bserv)" data-toggle="tooltip"
                                    :title="hasMultiBeauticianSplit(bserv) ? 'Edit Split' : 'Split'"
                                    data-original-title="Split">
                                    <i class="fa" :class="hasMultiBeauticianSplit(bserv) ? 'fa-check-circle' : 'fa-edit'"></i>
                                    <span v-if="hasMultiBeauticianSplit(bserv)">
                                        {{ __('Split') }} (@{{ bserv.split_draft.length }})
                                    </span>
                                    <span v-else>{{ __('Split') }}</span>
                                </a>
                            </div>

                            <div class="split-preview-chips"
                                v-if="hasMultiBeauticianSplit(bserv)">
                                <div class="split-preview-title">{{ __('Split among') }}:</div>
                                <div class="split-preview-list">
                                    <span class="split-preview-chip"
                                        v-for="(draft, dIdx) in bserv.split_draft" :key="dIdx">
                                        <strong>@{{ draft.beautician ? (draft.beautician.name_locale || draft.beautician.name) : ('BT#' + draft.beautician_id) }}</strong>
                                        <em>₹ @{{ Number(draft.price || 0).toFixed(2) }}</em>
                                    </span>
                                </div>
                            </div>

                        </td>

                        <td v-if="!booking_info.canceled_by && booking_info.payment_status != 'completed'"
                            class="text-right">
                            <form v-bind:action="urls.item_form_delete_url" class="voco-ajax-form" method="post"
                                data-hook-success="bookingServiceDeleted" style="display:inline-block">
                                @csrf
                                <input type="hidden" name="booking_id" v-model="booking_id">
                                <input type="hidden" name="service_id" v-model="bserv.service_id">
                                <input type="hidden" name="booking_service_id" v-model="bserv.booking_service_id ">
                                <button type="button"
                                    class="btn btn-link text-danger py-0 booking-line-remove brownz-close-btn">
                                    <i class="fa fa-times"></i>
                                </button>
                            </form>

                            <div class="m-2">
                                <span v-if="bserv.status === 'draft'" class="booking-flag orange-flag"
                                    v-text="bserv.status"></span>
                                <span v-if="bserv.status === 'start'" class="booking-flag green-flag"
                                    v-text="bserv.status"></span>
                                <span v-if="bserv.status === 'completed'" class="booking-flag black-flag"
                                    v-text="bserv.status"></span>
                            </div>


                        </td>

                    </tr>

                </template>

                <template
                    v-if="booking_info.booking_items.length > 0 &&  (booking_info.booking_type == 'Refund' ||  booking_info.booking_type == 'Refunded')"
                    v-for="o_item in booking_info.booking_items">
                    <tr>


                        <td>@{{ o_item.item_name_locale }}</td>

                        <td><input v-bind:disabled="booking_info.payment_status == 'completed'" type="number"
                                :id="qty + o_item.booking_item_id" class="form-control"
                                v-bind:value="o_item.quantity"
                                v-on:keyup="updateRefundQty(o_item,o_item.discount_percent,o_item.booking_id,o_item.selling_price,o_item.tax_amount,o_item.tax_percent,o_item.booking_item_id,'#'+qty+o_item.booking_item_id)"
                                v-on:change="updateRefundQty(o_item,o_item.discount_percent,o_item.booking_id,o_item.selling_price,o_item.tax_amount,o_item.tax_percent,o_item.booking_item_id,'#'+qty+o_item.booking_item_id)"
                                placeholder="{{ __('qty') }}" style="max-width:90px">
                        </td>
                        <td>@{{ o_item.selling_price }}</td>
                        <td v-if="o_item.individual_discount_amt == 0">@{{ v_fixed_amount(o_item.discount_amount / o_item.quantity) * -1 }}</td>
                        <td v-if="o_item.individual_discount_amt > 0">@{{ v_fixed_amount(o_item.individual_discount_amt / o_item.quantity) * -1 }}</td>
                        <td v-if="o_item.individual_discount_amt == 0"
                            :id="net_item_html_id + o_item.booking_item_id">@{{ v_totalNetTotal(o_item.selling_price - (o_item.discount_amount / o_item.quantity), (o_item.individual_discount_amt / o_item.quantity), o_item.quantity) * -1 }}</td>
                        <td v-if="o_item.individual_discount_amt > 0" :id="net_item_html_id + o_item.booking_item_id">
                            @{{ v_totalNetTotal(o_item.selling_price, (o_item.individual_discount_amt / o_item.quantity)) * -1 }}</td>

                        {{-- <td :id="tax_html_id + o_item.item_id">@{{ o_item.tax_amount > 0 ? v_fixed_amount(o_item.tax_amount / o_item.quantity) * -1 : 0.00 }}</td> --}}
                        {{-- <td :id="'tax_item_' + o_item.booking_item_id">
                            @{{ o_item.tax_amount > 0 ? v_fixed_amount(o_item.tax_amount / o_item.quantity) * -1 : 0.00 }}
                        </td> --}}

                        <td :id="'tax_item_' + o_item.booking_item_id">
                            @{{ o_item.tax_amount > 0 ? v_fixed_amount(o_item.tax_amount) * -1 : 0.00 }}
                        </td>

                        <td v-if="o_item.individual_discount_amt == 0"
                            :id="total_item_html_id + o_item.booking_item_id">@{{ v_totalPrice((o_item.selling_price * o_item.quantity) - o_item.discount_amount - o_item.individual_discount_amt, o_item.tax_amount, o_item.quantity) * -1 }}</td>
                        <td v-if="o_item.individual_discount_amt > 0"
                            :id="total_item_html_id + o_item.booking_item_id">
                            @{{ v_totalPrice((o_item.selling_price * o_item.quantity) - o_item.individual_discount_amt, o_item.tax_amount) * -1 }}</td>

                        <td v-if="!booking_info.canceled_by && booking_info.payment_status != 'completed'"
                            class="text-center">

                        </td>


                    </tr>

                </template>

                <template
                    v-if="booking_info.booking_only_services.length > 0 && (booking_info.booking_type == 'Refund' ||  booking_info.booking_type == 'Refunded')"
                    v-for="bserv in booking_info.booking_only_services">
                    <tr>
                        <td>@{{ bserv.service_name_locale }}</td>
                        <td>
                            <input v-bind:disabled="booking_info.payment_status == 'completed'" type="number"
                                :id="'svc_qty_' + bserv.booking_service_id" class="form-control"
                                v-bind:value="bserv.quantity"
                                min="1"
                                v-on:change="updateRefundServiceQty(bserv, '#' + 'svc_qty_' + bserv.booking_service_id)"
                                placeholder="{{ __('qty') }}" style="max-width:90px">
                        </td>
                        <td :id="price_html_id + bserv.booking_service_id">@{{ bserv.service_booking_price }}</td>

                        <td v-if="bserv.individual_discount_amt == 0">@{{ v_fixed_amount(bserv.discount_amount) * -1 }}</td>
                        <td v-if="bserv.individual_discount_amt > 0">@{{ v_fixed_amount(bserv.individual_discount_amt) * -1 }}</td>
                        <td v-if="bserv.individual_discount_amt == 0" :id="net_html_id + bserv.booking_service_id">
                            @{{ v_totalNetTotal(bserv.service_booking_price - bserv.discount_amount, bserv.individual_discount_amt, bserv.quantity) * -1 }}</td>
                        <td v-if="bserv.individual_discount_amt > 0" :id="net_html_id + bserv.booking_service_id">
                            @{{ v_totalNetTotal(bserv.service_booking_price, bserv.individual_discount_amt, bserv.quantity) * -1 }}</td>
                        <td :id="tax_html_id + bserv.booking_service_id">@{{ bserv.tax_amount > 0 ? v_fixed_amount(bserv.tax_amount) * -1 : 0.00 }}</td>
                        <td v-if="bserv.individual_discount_amt == 0" :id="total_html_id + bserv.booking_service_id">
                            @{{ v_totalPrice(bserv.service_booking_price - bserv.discount_amount - bserv.individual_discount_amt, bserv.tax_amount) * -1 }}</td>
                        <td v-if="bserv.individual_discount_amt > 0" :id="total_html_id + bserv.booking_service_id">
                            @{{ v_totalPrice(bserv.service_booking_price - bserv.individual_discount_amt, bserv.tax_amount) * -1 }}</td>


                    </tr>
                    <tr style="border-bottom: 2px solid #f1f1f1;">
                        <td colspan="8">
                            <p class="mb-0"><span :id="beatician_html_id + bserv.booking_service_id"
                                    v-if="bserv.beautician">
                                    @{{ bserv.beautician.name_locale }}
                                    (@{{ bserv.beautician.beautician_code }}),

                                </span>
                                <span v-else class="text-muted font-italic">
                                    {{ __('Unassigned') }}
                                </span>Date: @{{ bserv.appointment_date | vc_date_expanded }}, Time: @{{ bserv.slot_start }}
                            </p>

                        </td>

                    </tr>
                </template>







            </tbody>
        </table>
    </div>
</div>
