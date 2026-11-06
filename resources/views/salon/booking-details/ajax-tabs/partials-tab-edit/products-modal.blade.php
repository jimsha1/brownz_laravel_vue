<div class="modal fade" id="productLookup" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">
                    {{ __('Products') }}
                </h5>
                <button type="button" class="brownz-close-btn" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">

                <nav>
                    <div style="border-bottom: 0px solid #c8ced3;" class="nav nav-tabs" id="nav-tab" role="tablist">
                        <div style="border-bottom: 0px solid #c8ced3;" class="nav nav-tabs" id="nav-tab"
                            role="tablist">

                            <a class="nav-item nav-link active" id="product_tab" data-toggle="tab"
                                href="#bookingProduct" role="tab" aria-controls="nav-home" aria-selected="true">
                                <b>1.</b> {{ __('Add Products') }}
                            </a>
                            <a v-bind:style= "selected_product_id > 0 ? '' : 'pointer-events: none; background-color: #D3D3D3;'"
                                v-on:click="resetBeauticianSearch()" class="nav-item nav-link"
                                id="beautician_product_tab" data-toggle="tab" href="#beauticianProduct" role="tab"
                                aria-controls="nav-contact" aria-selected="false">
                                <b>2.</b> {{ __('Beautician (optional)') }}
                            </a>

                        </div>
                    </div>
                </nav>

                <div class="tab-content service-tab tab-panel-br-add-service" style="background-color: #F0F0F3"
                    id="nav-tabContent">

                    <div class="tab-pane fade show active" role="tabpanel" id="bookingProduct"
                        aria-labelledby="service_tab_3">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group d-flex">
                                    <div class="search-box-bar-container">
                                        <p><i class="fa fa-search" aria-hidden="true"></i></p>
                                        <input class="brawnz-search-bar-in form-control w-50" type="text"
                                            @keyup="event => searchProduct()" placeholder="{{ __('Search Product') }}"
                                            name="filterProduct" id="filterProduct" />
                                    </div>
                                </div>

                                <div class="form-wrapper tags-search-cd-brw" style="margin-top: 10px;">
                                    <nav class="tab-services">
                                        @if (app()->getLocale() === 'en')
                                            <div style="border-bottom: 0px; row-gap: 10px;"
                                                class="nav nav-tabs nav-scroll-tabs" id="nav-tab" role="tablist">
                                                <a v-on:click='changeProductCategory(item_groupe.item_group_id)'
                                                    v-for="item_groupe in all_item_groupes"
                                                    class="nav-item nav-link active" id="service_tab_3"
                                                    data-toggle="tab" href="#featured" role="tab"
                                                    aria-controls="nav-home" aria-selected="true"
                                                    v-bind:style="change_product_cat_id == item_groupe.item_group_id ? 'background-color:#d7eff8; color: #20A8D8;' : ''">
                                                    {{-- v-bind:style="change_product_cat_id == item_groupe.item_group_id ? 'background-color:#1e6789;' : ''"> --}}
                                                    @{{ item_groupe.group_name }} </a>
                                            </div>
                                        @else
                                            <div style="border-bottom: 0px solid #c8ced3;"
                                                class="nav nav-tabs nav-scroll-tabs" id="nav-tab" role="tablist">
                                                <a v-on:click='changeProductCategory(item_groupe.item_group_id)'
                                                    v-for="item_groupe in all_item_groupes"
                                                    class="nav-item nav-link active" id="service_tab_3"
                                                    data-toggle="tab" href="#featured" role="tab"
                                                    aria-controls="nav-home" aria-selected="true"
                                                    v-bind:style="change_product_cat_id == item_groupe.item_group_id ? 'background-color:#1e6789;' : ''">
                                                    @{{ item_groupe.groupe_name_ar ? item_groupe.groupe_name_ar : item_groupe.group_name }} </a>
                                            </div>
                                        @endif
                                    </nav>
                                </div>

                                <div class="tab-content bg-nwe-services-brownz bg-white p-3" id="nav-tabContent"
                                    style="margin-top: 10px;">
                                    <div class="tab-pane fade show active p-0 " id="featured" role="tabpanel"
                                        aria-labelledby="service_tab_1">
                                        <div class="tab-pane fade show active" id="featured" role="tabpanel"
                                            aria-labelledby="service_tab_3" style="padding: 0rem;">
                                            <div class="service-list-wrapper">
                                                <div v-for="(item, index) in all_items_list" class="form-wrapper"
                                                    style="margin-top: 10px; background-color: rgba(230, 205, 127, 0.2);">

                                                    <!-- Group Header -->
                                                    <div v-on:click="showAndHideItem(index)"
                                                        v-if="item.items.length > 0"
                                                        class=" click-script-class-active" id="sub-service">
                                                        <div class="sub-services"
                                                            style="background-color: transparent; align-items: center; justify-content: flex-start;">
                                                            <i class="fa fa-plus"></i>
                                                            @if (app()->getLocale() === 'en')
                                                                <h6>@{{ item.group_name }}</h6>
                                                            @else
                                                                <h6>@{{ item.groupe_name_ar ? item.groupe_name_ar : item.group_name }}</h6>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Sub Items -->
                                                    <div v-bind:id="'sub_item_' + index" v-if="item.items.length > 0"
                                                        class="sub-service-list"
                                                        style="background-color: transparent;">
                                                        <div>
                                                            <label
                                                                v-for="subItem in item.items.filter(i => Number(i.current_stock) >= (Number(i.pack_size) > 0 ? Number(i.pack_size) : 1))"
                                                                class="custom-selection brownz-custom-radio-tick"
                                                                v-on:click="setSalesAssociateProduct(subItem.item_id, (subItem.item_price && subItem.item_price.price_id) ? subItem.item_price.price_id : null, subItem.current_stock)">

                                                                <!-- Item Info -->
                                                                <div id="add_product_id"
                                                                    class="service-item-list-info">
                                                                    <div>
                                                                        <h6>
                                                                            @if (app()->getLocale() === 'en')
                                                                                @{{ subItem.item_name }}<br>
                                                                            @else
                                                                                @{{ subItem.item_name_ar ? subItem.item_name_ar : subItem.item_name }}<br><br>
                                                                            @endif
                                                                            <span>{{ __('Qty') }}:
                                                                                @{{ subItem.current_stock }} @{{ subItem.uom_name || '' }}</span>
                                                                        </h6>
                                                                    </div>

                                                                    <div>
                                                                        <h6>
                                                                            {{ __('₹') }} @{{ (subItem.item_price && subItem.item_price.selling_price != null) ? subItem.item_price.selling_price : subItem.selling_price }}
                                                                        </h6>
                                                                    </div>
                                                                </div>

                                                                <!-- Radio Input -->
                                                                <input type="radio" :name="'subservice_' + index"
                                                                    value="sub1"
                                                                    :checked="subItem.item_id == selected_product_id">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="beauticianProduct" role="tabpanel"
                        aria-labelledby="nav-contact-tab">
                        <div class="form-group d-flex">
                            <div class="search-box-bar-container">
                                <p><i aria-hidden="true" class="fa fa-search"></i></p>
                                <input class="brawnz-search-bar-in" type="text"
                                    @keyup="searchBeautician($event)" class="form-control w-50"
                                    placeholder="{{ __('Search Beautician') }}" name="filterProductBeautician"
                                    id="filterProductBeautician" />
                            </div>
                        </div>

                        <div class="row">

                            <div v-on:click="setSalesAssociateBeautician(bt.beautician_id)" v-for="bt in beauticians"
                                class="col-lg-6 col-sm-6 col-md-6">

                                <label
                                    class="custom-selection beautician-custom-radio beautician-card-select-radio-brownz">
                                    <div class="service-item-list-info">
                                        {{-- <input type="radio" name="beautician"
                                            :checked="bt.beautician_id == selected_product_beautician_id" /> --}}
                                        <input type="radio" name="beautician" :value="bt.beautician_id"
                                            v-model="selected_product_beautician_id" />
                                        <span class="checkmark"></span>
                                        <div>
                                            {{-- <img v-bind:src="bt.photo_src_url" alt="" /> --}}
                                            <img v-bind:src="'/storage/' + bt.profile_photo_path" alt=""
                                                style="border-radius: 50%;height: 60px;width: 60px;object-fit: cover;">
                                        </div>
                                        <p>
                                            @{{ bt.name_locale }}
                                        </p>
                                    </div>

                                </label>
                            </div>

                        </div>
                    </div>

                </div>



            </div>

            <div class="modal-footer">

                <form v-bind:action="urls.item_form_save_url" class="voco-ajax-form" method="post"
                    data-hook-success="bookingServiceSaved">
                    @csrf

                    <input type="hidden" name="booking_id" v-model="booking_id">
                    <input type="hidden" name="beautician_id" v-model="selected_product_beautician_id">
                    <input type="hidden" name="line_item_type" id="line_item_type" value="item">
                    <input type="hidden" name="service_id" id="d_service_id_1">
                    <input type="hidden" name="price_id" v-model="product_price_id">
                    <input type="hidden" name="quantity" v-model="product_quantity">
                    <input type="hidden" name="item_id" v-model="selected_product_id">
                    <button v-if="selected_product_id > 0" id="add_product_id"
                        type="button" class="btn_dark-x submit-btn"><i
                            class="fa fa-check"></i>{{ __('Submit') }}</button>
                    <div id="loading_spinner" style="display: none;">Processing...</div>
                </form>

            </div>
        </div>
    </div>
</div>
