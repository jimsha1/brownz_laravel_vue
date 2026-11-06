<div class="modal fade" id="serviceLookup" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title popup-head-bootst" id="exampleModalCenterTitle">
                    {{ __('Booking Services') }}
                </h5>
                <button type="button" class="brownz-close-btn" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <nav class="brownz-tab-123">
                    <div style="border-bottom: 0px solid #c8ced3;" class="nav nav-tabs" id="nav-tab" role="tablist">
                        <div style="border-bottom: 0px solid #c8ced3;" class="nav nav-tabs brwnz-tab-d" id="nav-tab"
                            role="tablist">

                            <a v-on:click="showAddServices()"
                                class="nav-item nav-link active circle-btn-123 add-service-btn-red" id="service_tab"
                                data-toggle="tab" href="#bookingService" role="tab" aria-controls="nav-home"
                                aria-selected="true">
                                <b v-if="is_reschedule">0</b><b v-else>1</b> <span>{{ __('Add Services') }}</span>
                            </a>
                            <a v-bind:style= "selected_serv_id > 0 ? '' : 'pointer-events: none;'"
                                v-on:click="resetBeauticianSearch()"
                                class="nav-item nav-link circle-btn-123 beautician-btn-blue " id="beautician_tab"
                                data-toggle="tab" href="#beautician" role="tab" aria-controls="nav-contact"
                                aria-selected="false">
                                <b v-if="is_reschedule">1</b><b v-else>2</b> <span>{{ __('Beautician') }}</span>
                            </a>
                            <a v-bind:style= "(selected_beautician_id > 0 && selected_shift_id > 0 && selected_serv_id > 0)  ? '' : 'pointer-events: none;'"
                                v-on:click="changeClass()" class="nav-item nav-link circle-btn-123 timeslot-btn-blue"
                                id="slot_tab" data-toggle="tab" href="#slot" role="tab"
                                aria-controls="nav-profile" aria-selected="false">
                                <b v-if="is_reschedule">2</b><b v-else>3</b> <span>{{ __('Time Slot') }}</span>
                            </a>

                        </div>
                    </div>
                </nav>

                <div class="tab-content service-tab tab-panel-br-add-service" id="nav-tabContent">

                    <div class="tab-pane fade show active " id="bookingService" role="tabpanel"
                        aria-labelledby="service_tab">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group d-flex">
                                    <div class="search-box-bar-container">
                                        <p><i class="fa fa-search" aria-hidden="true"></i></p>
                                        <input class="brawnz-search-bar-in form-control w-50" type="text"
                                            @keyup="searchService" placeholder="{{ __('Search Service') }}"
                                            name="filterServ" id="filterServ" />
                                    </div>
                                </div>

                                <div class="form-wrapper tags-search-cd-brw" style="margin-top: 10px;">
                                    <nav class="tab-services">
                                        <input id="first_serv_cat_id" type="hidden"   v-if="all_service_category.length > 0"
                                            v-model="all_service_category[0].category_id">
                                        <div style="border-bottom: 0px; row-gap: 10px;"
                                            class="nav nav-tabs nav-scroll-tabs" id="nav-tab" role="tablist">
                                            <a v-on:click='changeServiceCategory(serv_cat.category_id)'
                                                v-for="serv_cat in all_service_category"
                                                class="nav-item nav-link active" id="service_tab_1" data-toggle="tab"
                                                href="#featured" role="tab" aria-controls="nav-home"
                                                aria-selected="true"
                                                v-bind:style="selected_service_id == serv_cat.category_id ? 'background-color:#d7eff8; color: #20A8D8;' : ''">

                                                @{{ serv_cat.name }}

                                            </a>
                                        </div>
                                    </nav>
                                </div>

                                <div class="tab-content bg-nwe-services-brownz" id="nav-tabContent"
                                    style="margin-top: 10px;">
                                    <div class="tab-pane fade show active p-0 " id="featured" role="tabpanel"
                                        aria-labelledby="service_tab_1">
                                        <div class="tab-pane fade show active " id="featured" role="tabpanel"
                                            aria-labelledby="service_tab_1">
                                            <div class="service-list-wrapper ">
                                                {{-- <h5>Featured111</h5> --}}

                                                <div v-for="(serv,index) in all_services"
                                                    class="form-wrapper box-shadow-none">

                                                    <label v-on:click="getbookingData(serv.service_id,'serv')"
                                                        v-if="serv.services.length == 0" class="custom-selection">

                                                        <div class="service-item-list-info">
                                                            <div>
                                                                <h6>
                                                                    @{{ serv.name }}

                                                                    <span>@{{ serv.duration_minutes }}{{ __('min') }}
                                                                    </span>
                                                                </h6>

                                                            </div>

                                                            <div>
                                                                <h6><span>@{{ serv.duration_minutes }}{{ __('min') }}
                                                                    </span>{{ __('₹') }} @{{ serv.service_base_total }}
                                                                </h6>
                                                            </div>

                                                        </div>
                                                        <input type="checkbox">
                                                        <span class="checkmark"></span>
                                                    </label>

                                                    <div v-on:click="showAndHideSubcat(index)"
                                                        v-if="serv.services.length > 0"
                                                        class="sub-services click-script-class-active"
                                                        id="sub-service">
                                                        <div>
                                                            <i class="fa fa-plus"></i>
                                                            <h6>
                                                                @{{ serv.name }}
                                                            </h6>
                                                        </div>

                                                    </div>
                                                    <div v-bind:id="'sub_serv_' + index"
                                                        v-if="serv.services.length > 0" class="sub-service-list">
                                                        <label v-for="sub_serv in serv.services"
                                                            v-on:click="getbookingData(sub_serv.service_id,'serv')"
                                                            class="custom-selection brownz-custom-radio-tick">
                                                            <div class="service-item-list-info">
                                                                <div>
                                                                    <h6>

                                                                        @{{ sub_serv.category && sub_serv.category.parent ? sub_serv.category.parent.name + '/' : '' }}@{{ sub_serv.name }}
                                                                        <span>@{{ sub_serv.duration || sub_serv.duration_minutes }}{{ __('min') }}
                                                                        </span>
                                                                    </h6>

                                                                </div>

                                                                <div>
                                                                    <h6><span>@{{ sub_serv.duration || sub_serv.duration_minutes }}{{ __('min') }}
                                                                        </span>{{ __('₹') }}
                                                                        @{{ sub_serv.price != null ? sub_serv.price : sub_serv.service_base_total }}</h6>
                                                                </div>

                                                            </div>

                                                            <input type="radio" name="subservice" value="sub1"
                                                                :checked="sub_serv.service_id == selected_serv_id">
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

                    <div class="tab-pane fade" id="slot" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <div class="time-slot">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <div class="calendar-wrapper browz-changes-calender" id="calendar-wrapper"></div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    {{-- <h5>20 Febraury 2021</h5> --}}
                                    <div class="time-slot-wrapper brownz-time-slot-times-select"
                                        style="
                                    justify-content: unset">

                                        <label v-for="time in time_slot" class="custom-selection slot-custom-radio">
                                            <input o type="radio" name="time1"
                                                :checked="time == selected_time_slot" />
                                            <!-- <span class="checkmark"></span> -->
                                            <div v-on:click="addTimeSlot(time)" class="service-item-list-info">
                                                <p>@{{ convertTimeTo12hr(time) }}</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="beautician" role="tabpanel" aria-labelledby="nav-contact-tab">
                        <div class="form-group d-flex ">
                            <div class="search-box-bar-container">
                                <p><i aria-hidden="true" class="fa fa-search"></i></p>
                                <input class="brawnz-search-bar-in" type="text"
                                    @keyup="searchBeautician($event)" class="form-control w-50"
                                    placeholder="{{ __('Search Beautician') }}" name="filterServiceBeautician"
                                    id="filterServiceBeautician" />
                            </div>


                        </div>

                        <div class="row mt-3">

                            {{-- <div v-on:click="getbookingData( bt.beautician_id,'beatician',bt.shift_id,bt.shift_id)" --}}
                            <div v-on:click="getbookingData( bt.beautician_id,'beatician',bt.shift_id)"
                                v-for="bt in beauticians" class="col-lg-6 col-sm-6 col-md-6">

                                <label
                                    class="custom-selection beautician-custom-radio beautician-card-select-radio-brownz">
                                    <div class="service-item-list-info" v-on:click="beauticianClick()">
                                        <input type="radio" name="beautician"
                                            :checked="bt.beautician_id == selected_beautician_id" />
                                        <span class="checkmark"></span>
                                        <div>

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

            <div class="modal-footer pt-0 border-0">
                <form v-bind:action="urls.item_form_save_url" class="voco-ajax-form" method="post"
                    data-hook-success="bookingServiceSaved">
                    @csrf
                    <input type="hidden" name="booking_id" v-model="booking_id">
                    <input type="hidden" name="beautician_id" id="d_beatcian_id">
                    <input type="hidden" name="slot_start" id="d_time_slot_id">
                    <input type="hidden" name="line_item_type" id="line_item_type" value="service">
                    <input type="hidden" name="service_id" id="d_service_id">
                    <input type="hidden" name="shift_id" id="d_shift_id">
                    <input type="hidden" name="date" id="d_date">
                    <input type="hidden" name="appointment_date" id="d_appointment_date">
                    <input type="hidden" name="booking_service_id" id="d_booking_serv_id">
                    <input type="hidden" name="is_update" id="d_is_update" value="0">
                    <input type="hidden" name="quantity" id="d_quantity" value="1">
                    <button
                        v-if="selected_serv_id > 0 && selected_beautician_id > 0 && selected_shift_id > 0 && selected_time_slot != null"
                        id="add_service_id" type="button" v-on:click="manageSubmitButton()"
                        class="btn_dark-x submit-btn"><i class="fa fa-check"></i> {{ __('Submit') }}</button>
                    <div id="loading_spinner" style="display: none;">
                        Processing...
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="quickServiceLookup" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document" style="max-width: 1140px;">
        <div class="modal-content" style="max-height: 850px">
            <div class="modal-header pb-0 border-0">
                <h5 class="modal-title popup-head-bootst" id="exampleModalCenterTitle">
                    {{ __('Quick Services') }}
                </h5>
                <button type="button" class="brownz-close-btn" @click="resetQuickServiceModal" data-dismiss="modal"
                    aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card-quick-beautician brw-form-la">
                            <div class="row">
                                <div class="col-lg-6">
                                    <input id="first_serv_cat_id" type="hidden"
                                        v-model="all_service_category[0].category_id">
                                    <select class="form-control voco-select2" style="width: 100%;"
                                        v-model="selectedBeautician" data-placeholder="Beautician" required>
                                        <option value=""></option>
                                        {{ $beauticians_dropdown }}
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <input type="datetime-local" id="start_date"
                                        class="form-control brawnz-search-bar-in" v-model="start_date"
                                        name="start_date" :min="getCurrentDateTime()">
                                </div>
                            </div>
                        </div>
                        <div class="service-tab tab-panel-br-add-service overflow-hidden">
                            <div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <div class="form-group d-flex">
                                            <div class="search-box-bar-container">
                                                <p><i class="fa fa-search" aria-hidden="true"></i></p>
                                                <input class="brawnz-search-bar-in form-control w-50" type="text"
                                                    v-model="searchName" @keyup="searchService1"
                                                    placeholder="{{ __('Search Service') }}" name="filterServ1"
                                                    id="filterServ1" />
                                            </div>
                                        </div>

                                        <div class="form-wrapper tags-search-cd-brw" style="margin-top: 10px;">
                                            <nav class="tab-services">
                                                <input id="first_serv_cat_id" type="hidden"
                                                    v-model="all_service_category[0].category_id">
                                                <div style="border-bottom: 0px; row-gap: 10px;"
                                                    class="nav nav-tabs nav-scroll-tabs" id="nav-tab"
                                                    role="tablist">
                                                    <a v-on:click='changeServiceCategorys(serv_cat.category_id, searchName)'
                                                        v-for="serv_cat in all_service_category"
                                                        class="nav-item nav-link active" id="service_tab_1"
                                                        data-toggle="tab" href="#featured" role="tab"
                                                        aria-controls="nav-home" aria-selected="true"
                                                        v-bind:style="selected_service_id == serv_cat.category_id ? 'background-color:#d7eff8; color: #20A8D8;' : ''">

                                                        @{{ serv_cat.name }}

                                                    </a>
                                                </div>
                                            </nav>
                                        </div>

                                        <div class="tab-content bg-nwe-services-brownz" id="nav-tabContent"
                                            style="margin-top: 10px;">
                                            <div class="tab-pane fade show active p-0 " id="featured"
                                                role="tabpanel" aria-labelledby="service_tab_1">
                                                <div class="tab-pane fade show active " id="featured"
                                                    role="tabpanel" aria-labelledby="service_tab_1">
                                                    <div class="service-list-wrapper"
                                                        style="overflow: auto; height: 221px; border-radius: 20px;">

                                                        <div v-for="(serv,index) in all_services"
                                                            class="form-wrapper box-shadow-none mb-2">

                                                            <label v-if="serv.services.length == 0"
                                                                class="custom-selection">

                                                                <div class="service-item-list-info">
                                                                    <div>
                                                                        <h6>
                                                                            @{{ serv.name }}


                                                                            <span>@{{ serv.duration_minutes }}{{ __('min') }}
                                                                            </span>
                                                                        </h6>

                                                                    </div>

                                                                    <div>
                                                                        <h6><span>@{{ serv.duration_minutes }}{{ __('min') }}
                                                                            </span>{{ __('₹') }}
                                                                            @{{ serv.service_base_total }}
                                                                        </h6>
                                                                    </div>

                                                                </div>
                                                                {{-- <input type="checkbox" :value="serv.service_id" v-model="selectedServices">
                                                                <span class="checkmark"></span> --}}
                                                            </label>

                                                            <div v-on:click="showAndHideSubcat1(index)"
                                                                v-if="serv.services.length > 0"
                                                                class="sub-services click-script-class-active"
                                                                id="sub-services">
                                                                <div>
                                                                    <i class="fa fa-plus"></i>
                                                                    <h6>
                                                                        @{{ serv.name }}
                                                                    </h6>
                                                                </div>

                                                            </div>


                                                            <div v-bind:id="'sub_servs_' + index"
                                                                v-if="serv.services.length > 0"
                                                                class="sub-service-list">
                                                                <label v-for="sub_serv in serv.services"
                                                                    :key="sub_serv.service_id"
                                                                    class="custom-selection brownz-custom-radio-tick">

                                                                    <div class="service-item-list-info">
                                                                        <div>
                                                                            <h6>
                                                                                @{{ sub_serv.category && sub_serv.category.parent ? sub_serv.category.parent.name + '/' : '' }}@{{ sub_serv.name }}
                                                                                <span>@{{ sub_serv.duration || sub_serv.duration_minutes }}{{ __('min') }}</span>
                                                                            </h6>
                                                                        </div>
                                                                        <div>
                                                                            <h6><span>@{{ sub_serv.duration || sub_serv.duration_minutes }}{{ __('min') }}</span>
                                                                                {{ __('₹') }}
                                                                                @{{ sub_serv.price != null ? sub_serv.price : sub_serv.service_base_total }}
                                                                            </h6>
                                                                        </div>
                                                                    </div>

                                                                    <input type="checkbox" :value="sub_serv.service_id"
                                                                        v-model="selectedServices">
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
                    </div>

                    <div class="col-lg-4">
                        <div class="quick-service-popup-right-side">
                            <h6 style="font-size: 16px">Selected Services</h6>
                            <div class="mt-3" style="max-height: 392px; overflow: auto; border-radius: 20px;">
                                <table class="table m-0" style="width:100%; border-collapse: collapse;">
                                    <thead class="post">
                                        <tr style="background: transparent;">
                                            <th style="padding: 10px; border: 1px solid #ffffff;">#</th>
                                            <th style="padding: 10px; border: 1px solid #ffffff;">Service</th>
                                            <th style="padding: 10px; border: 1px solid #ffffff;">Price</th>
                                            <th style="padding: 10px; border: 1px solid #ffffff;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(service, idx) in selectedServiceDetails" :key="service.service_id">
                                            <td style="padding: 8px 10px; border-bottom: 1px solid #ffffff;">
                                                @{{ idx + 1 }}</td>
                                            <td style="padding: 8px 10px; border-bottom: 1px solid #ffffff;">
                                                @{{ service.name }}
                                            </td>
                                            <td style="padding: 8px 10px; border-bottom: 1px solid #ffffff;">
                                                @{{ service.price }}
                                            </td>
                                            <td style="padding: 8px 10px; border-bottom: 1px solid #ffffff;">
                                                <button type="button" class="btn btn-link text-danger py-0"
                                                    @click="removeService(service.service_id)">
                                                    <i class="fa fa-trash-o"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="selectedServiceDetails.length === 0">
                                            <td colspan="4" style="padding: 8px 10px; text-align: center;">No
                                                services selected</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer pt-0 border-0">


                <button v-if="(selectedBeautician > 0 || selected_beautician_id > 0) && !loading" id="add_quick_service_id"
                    @click="manageSubmitButton1" type="button" class="btn_dark-x submit-btn"><i
                        class="fa fa-check"></i>
                    {{ __('Submit') }}
                </button>
                <div v-if="loading" id="loading_spinner" class="d-flex align-items-center" style="padding: 10px;">
                    <i class="fa fa-spinner fa-spin mr-2" style="font-size: 18px; color: #20A8D8;"></i>
                    <span>{{ __('Processing...') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Product (mirrors Quick Service) --}}
<div class="modal fade" id="quickProductLookup" tabindex="-1" role="dialog"
    aria-labelledby="quickProductTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document" style="max-width: 1140px;">
        <div class="modal-content" style="max-height: 850px">
            <div class="modal-header pb-0 border-0">
                <h5 class="modal-title popup-head-bootst" id="quickProductTitle">
                    {{ __('Quick Products') }}
                </h5>
                <button type="button" class="brownz-close-btn" @click="resetQuickProductModal" data-dismiss="modal"
                    aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card-quick-beautician brw-form-la">
                            <div class="row">
                                <div class="col-lg-12">
                                    <select class="form-control voco-select2-quick-product" style="width: 100%;"
                                        v-model="selectedProductBeautician" data-placeholder="Beautician (optional)">
                                        <option value=""></option>
                                        {{ $beauticians_dropdown }}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="service-tab tab-panel-br-add-service overflow-hidden">
                            <div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <div class="form-group d-flex">
                                            <div class="search-box-bar-container">
                                                <p><i class="fa fa-search" aria-hidden="true"></i></p>
                                                <input class="brawnz-search-bar-in form-control w-50" type="text"
                                                    v-model="quickProductSearch" @keyup="searchQuickProduct"
                                                    placeholder="{{ __('Search Product') }}" name="filterQuickProduct"
                                                    id="filterQuickProduct" />
                                            </div>
                                        </div>

                                        <div class="form-wrapper tags-search-cd-brw" style="margin-top: 10px;">
                                            <nav class="tab-services">
                                                <div style="border-bottom: 0px; row-gap: 10px;"
                                                    class="nav nav-tabs nav-scroll-tabs" id="nav-tab-quick-product"
                                                    role="tablist">
                                                    <a v-on:click='changeQuickProductCategory(item_groupe.item_group_id)'
                                                        v-for="item_groupe in all_item_groupes"
                                                        class="nav-item nav-link active"
                                                        data-toggle="tab" href="#quickProductFeatured" role="tab"
                                                        aria-selected="true"
                                                        v-bind:style="quick_product_cat_id == item_groupe.item_group_id ? 'background-color:#d7eff8; color: #20A8D8;' : ''">
                                                        @{{ item_groupe.group_name }}
                                                    </a>
                                                </div>
                                            </nav>
                                        </div>

                                        <div class="tab-content bg-nwe-services-brownz" id="nav-tabContent-quick-product"
                                            style="margin-top: 10px;">
                                            <div class="tab-pane fade show active p-0 " id="quickProductFeatured"
                                                role="tabpanel">
                                                <div class="service-list-wrapper"
                                                    style="overflow: auto; height: 280px; border-radius: 20px;">
                                                    <div v-for="(item, index) in quick_items_list"
                                                        class="form-wrapper box-shadow-none mb-2">
                                                        <div v-on:click="showAndHideQuickItem(index)"
                                                            v-if="item.items && item.items.length > 0"
                                                            class="sub-services click-script-class-active">
                                                            <div>
                                                                <i class="fa fa-plus"></i>
                                                                <h6>@{{ item.group_name }}</h6>
                                                            </div>
                                                        </div>

                                                        <div v-bind:id="'quick_sub_item_' + index"
                                                            v-if="item.items && item.items.length > 0"
                                                            class="sub-service-list">
                                                            <label
                                                                v-for="subItem in item.items.filter(i => Number(i.current_stock) >= (Number(i.pack_size) > 0 ? Number(i.pack_size) : 1) && i.item_price && i.item_price.price_id)"
                                                                :key="subItem.item_id"
                                                                class="custom-selection brownz-custom-radio-tick">
                                                                <div class="service-item-list-info">
                                                                    <div>
                                                                        <h6>
                                                                            @{{ subItem.item_name }}
                                                                            <span>{{ __('Qty') }}:
                                                                                @{{ subItem.current_stock }} @{{ subItem.uom_name || '' }}</span>
                                                                        </h6>
                                                                    </div>
                                                                    <div>
                                                                        <h6>
                                                                            {{ __('₹') }}
                                                                            @{{ (subItem.item_price && subItem.item_price.selling_price != null) ? subItem.item_price.selling_price : subItem.selling_price }}
                                                                        </h6>
                                                                    </div>
                                                                </div>
                                                                <input type="checkbox"
                                                                    :value="subItem.item_price.price_id"
                                                                    v-model="selectedProducts">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div v-if="!quick_items_list.length"
                                                        class="p-3 text-center text-muted">
                                                        {{ __('No products found') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="quick-service-popup-right-side">
                            <h6 style="font-size: 16px">Selected Products</h6>
                            <div class="mt-3" style="max-height: 392px; overflow: auto; border-radius: 20px;">
                                <table class="table m-0" style="width:100%; border-collapse: collapse;">
                                    <thead class="post">
                                        <tr style="background: transparent;">
                                            <th style="padding: 10px; border: 1px solid #ffffff;">#</th>
                                            <th style="padding: 10px; border: 1px solid #ffffff;">Product</th>
                                            <th style="padding: 10px; border: 1px solid #ffffff;">Price</th>
                                            <th style="padding: 10px; border: 1px solid #ffffff;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(product, idx) in selectedProductDetails" :key="product.price_id">
                                            <td style="padding: 8px 10px; border-bottom: 1px solid #ffffff;">
                                                @{{ idx + 1 }}</td>
                                            <td style="padding: 8px 10px; border-bottom: 1px solid #ffffff;">
                                                @{{ product.name }}
                                            </td>
                                            <td style="padding: 8px 10px; border-bottom: 1px solid #ffffff;">
                                                @{{ product.price }}
                                            </td>
                                            <td style="padding: 8px 10px; border-bottom: 1px solid #ffffff;">
                                                <button type="button" class="btn btn-link text-danger py-0"
                                                    @click="removeQuickProduct(product.price_id)">
                                                    <i class="fa fa-trash-o"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="selectedProductDetails.length === 0">
                                            <td colspan="4" style="padding: 8px 10px; text-align: center;">No
                                                products selected</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer pt-0 border-0">
                <button v-if="selectedProducts.length && !loading"
                    @click="manageQuickProductSubmit" type="button" class="btn_dark-x submit-btn">
                    <i class="fa fa-check"></i> {{ __('Submit') }}
                </button>
                <div v-if="loading" class="d-flex align-items-center" style="padding: 10px;">
                    <i class="fa fa-spinner fa-spin mr-2" style="font-size: 18px; color: #20A8D8;"></i>
                    <span>{{ __('Processing...') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>



{{-- Hidden source of beautician options for Split modals (avoids broken JS strings) --}}
<select id="split_beautician_options_source" class="d-none" aria-hidden="true">
    <option value="">{{ __('Select Beautician') }}</option>
    {{ $beauticians_dropdown }}
</select>

<div class="modal fade" id="status-modal-split" tabindex="-1" role="dialog" data-backdrop="static"
    data-keyboard="false" aria-labelledby="splitServiceTitle" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered" style="max-width: 560px;" role="document">
        <div class="modal-content split-modal-content">
            <div class="modal-header border-0 px-4 pb-0">
                <h5 class="modal-title popup-head-bootst" id="splitServiceTitle">{{ __('Split Service') }}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close-btn-default"
                    onclick="resetForm()">✖</button>
            </div>
            <div class="modal-body brownz-split-d px-4">
                <input type="hidden" name="booking_id" id="modal_split_booking_id">
                <input type="hidden" name="beautician_id" id="modal_split_beautician_id">
                <input type="hidden" name="booking_service_id" id="modal_split_booking_service_id">
                <input type="hidden" name="booking_service_started_at" id="modal_service_started_at">
                <input type="hidden" name="commission_start_date" id="modal_commission_start_date">
                <input type="hidden" name="commission_end_date" id="modal_commission_end_date">
                <input type="hidden" name="commission_percentage" id="modal_commission_percentage">
                <input type="hidden" name="commission_status" id="modal_commission_status">
                <input type="hidden" name="service_id" id="modal_service_id">

                <div class="split-summary-card mb-3">
                    <div class="split-summary-label">{{ __('Service') }}</div>
                    <div class="split-summary-service" id="modal_service_name">—</div>
                    <div class="d-flex justify-content-between align-items-end mt-2">
                        <div>
                            <div class="split-summary-label">{{ __('Total amount') }}</div>
                            <div class="split-summary-amount">₹ <span id="modal_service_amount">0.00</span></div>
                        </div>
                        <div class="text-right">
                            <div class="split-summary-label">{{ __('Equal share') }}</div>
                            <div class="split-summary-share" id="modal_split_equal_share">₹ 0.00</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong class="split-section-title">{{ __('Beauticians') }}</strong>
                    <span class="split-count-badge" id="modal_split_count_badge">1</span>
                </div>

                <div class="split-rows-head">
                    <span>{{ __('Beautician') }}</span>
                    <span>{{ __('Share') }}</span>
                    <span></span>
                </div>

                <div class="input_fields_wrap split-rows-body" id="split_rows_body">
                    {{-- rows injected by JS --}}
                </div>

                <button type="button" id="splitButton"
                    class="add_field_button split-add-btn mt-2">
                    <i class="fa fa-plus"></i> {{ __('Add Beautician') }}
                </button>

                <p class="split-help-text mt-3 mb-0">
                    {{ __('Amount is divided equally among selected beauticians.') }}
                    {{ __('On completed bookings, click Update after saving so commission is recalculated.') }}
                </p>
            </div>
            <div class="modal-footer border-0 px-4 pt-0">
                <button onclick="resetForm()" type="button" class="btn btn-secondary brownz-btn-yellow-d"
                    data-dismiss="modal">{{ __('Close') }}</button>
                <button type="button" class="btn btn_dark-x" id="submitSplitForm">
                    <i class="fa fa-check-circle"></i> {{ __('Save Split') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="open-split-modal" tabindex="-1" role="dialog" aria-labelledby="viewSplitTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;" role="document">
        <div class="modal-content split-modal-content">
            <div class="modal-header border-0 px-4 pb-0">
                <h5 class="modal-title popup-head-bootst" id="viewSplitTitle">{{ __('View Split') }}</h5>
                <button type="button" class="close-btn-default"
                    onclick="clearViewSplitModal();" data-dismiss="modal" aria-label="Close">✖</button>
            </div>
            <div class="modal-body brownz-split-d px-4">
                <input type="hidden" name="booking_id" id="view_split_booking_id">
                <input type="hidden" name="beautician_id" id="view_split_beautician_id">
                <input type="hidden" name="booking_service_id" id="view_split_booking_service_id">

                <div class="split-summary-card mb-3">
                    <div class="split-summary-label">{{ __('Service') }}</div>
                    <div class="split-summary-service" id="modal_service_name1">—</div>
                    <div class="mt-2">
                        <div class="split-summary-label">{{ __('Total amount') }}</div>
                        <div class="split-summary-amount">₹ <span id="modal_service_amount1">0.00</span></div>
                    </div>
                </div>

                <div class="split-rows-head">
                    <span>{{ __('Beautician') }}</span>
                    <span>{{ __('Share') }}</span>
                </div>
                <div class="input_fields_wrap split-rows-body split-view-rows" id="view_split_rows_body">
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pt-0">
                <button type="button" class="btn btn-secondary brownz-btn-yellow-d"
                    onclick="clearViewSplitModal();">
                    {{ __('Close') }}
                </button>
                <button type="button" class="btn btn_dark-x" id="view_split_edit_btn" style="display:none;">
                    <i class="fa fa-edit"></i> {{ __('Edit Split') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="hairLookup" tabindex="-1" role="dialog" aria-labelledby="hairConsultationTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 1140px;">
        <div class="modal-content p-3" style="max-height: 850px; overflow-y: auto;">
            <div class="modal-header pb-0 border-0">
                <h5 class="modal-title popup-head-bootst" id="hairConsultationTitle">
                    Hair Consultation Form
                </h5>
                <button type="button" class="brownz-close-btn" @click="resetHairModal" data-dismiss="modal"
                    aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <form id="hairConsultationForm">
                    <!-- Client Info -->
                    <div class="row mb-4" style="row-gap: 16px">
                        <div class="col-md-6">
                            <label for="hair_client_name">Client Name</label>
                            <input type="text" class="browz-gray-input-custom" id="hair_client_name"
                                name="client_name" v-model="client_name" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="hair_age">Age</label>
                            <input type="number" class="browz-gray-input-custom" id="hair_age" name="age"
                                v-model="client_age">
                        </div>
                        <div class="col-md-3">
                            <label for="hair_date">Date</label>
                            <input type="date" class="browz-gray-input-custom" id="hair_date" name="date"
                                v-model="date">
                        </div>
                        <div class="col-md-6">
                            <label for="hair_phone1">Phone 1</label>
                            <input type="text" class="browz-gray-input-custom" id="hair_phone1" name="phone1"
                                v-model="client_mobile" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="hair_phone2">Phone 2</label>
                            <input type="text" class="browz-gray-input-custom" id="hair_phone2" name="phone2"
                                v-model="client_phone">
                        </div>
                        <div class="col-md-6">
                            <label for="hair_stylist_name">Stylist Name</label>
                            <input type="text" class="browz-gray-input-custom" id="hair_stylist_name"
                                name="stylist_name" v-model="stylist_name">
                        </div>
                        <div class="col-md-6">
                            <label for="hair_designation">Designation</label>
                            <input type="text" class="browz-gray-input-custom" id="hair_designation"
                                name="designation" v-model="designation">
                        </div>
                    </div>

                    <hr class="pb-3">

                    <h5 class="mb-3 mt-3">Primary Analysis</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered new-form-table">
                            <!-- Scalp Condition -->
                            <tr>
                                <th>Scalp Condition</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Normal"
                                            v-model="primary_analysis.scalp_condition"> Normal
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Oily"
                                            v-model="primary_analysis.scalp_condition"> Oily
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Dry"
                                            v-model="primary_analysis.scalp_condition"> Dry
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Dandruff"
                                            v-model="primary_analysis.scalp_condition"> Dandruff
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Sensitive"
                                            v-model="primary_analysis.scalp_condition"> Sensitive
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Hair loss"
                                            v-model="primary_analysis.scalp_condition"> Hair loss
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                            </tr>

                            <!-- Hair Texture -->
                            <tr>
                                <th>Hair Texture</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Wavy"
                                            v-model="primary_analysis.hair_texture"> Wavy
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Curly"
                                            v-model="primary_analysis.hair_texture"> Curly
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Straight"
                                            v-model="primary_analysis.hair_texture"> Straight
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Coily"
                                            v-model="primary_analysis.hair_texture"> Coily
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td colspan="2"></td>
                            </tr>

                            <!-- Hair Density -->
                            <tr>
                                <th>Hair Density</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Fine"
                                            v-model="primary_analysis.hair_density"> Fine
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Medium"
                                            v-model="primary_analysis.hair_density"> Medium
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Thick"
                                            v-model="primary_analysis.hair_density"> Thick
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td colspan="3"></td>
                            </tr>

                            <!-- Hair Condition -->
                            <tr>
                                <th>Hair Condition</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Healthy"
                                            v-model="primary_analysis.hair_condition"> Healthy
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Dry/damaged"
                                            v-model="primary_analysis.hair_condition"> Dry/damaged
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Sensitized"
                                            v-model="primary_analysis.hair_condition"> Sensitized
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Split ends"
                                            v-model="primary_analysis.hair_condition"> Split ends
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td colspan="2"></td>
                            </tr>

                            <!-- Hair Elasticity -->
                            <tr>
                                <th>Hair Elasticity</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Healthy"
                                            v-model="primary_analysis.hair_elasticity"> Healthy
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Weak"
                                            v-model="primary_analysis.hair_elasticity"> Weak
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td colspan="4"></td>
                            </tr>

                            <!-- Hair Porosity -->
                            <tr>
                                <th>Hair Porosity</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Low"
                                            v-model="primary_analysis.hair_porosity"> Low
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="Medium"
                                            v-model="primary_analysis.hair_porosity"> Medium
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="High"
                                            v-model="primary_analysis.hair_porosity"> High
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td colspan="3"></td>
                            </tr>

                            <!-- % of Grey -->
                            <tr>
                                <th>% of Grey</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="30%"
                                            v-model="primary_analysis.grey_percentage"> 30%
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="50%"
                                            v-model="primary_analysis.grey_percentage"> 50%
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="70%"
                                            v-model="primary_analysis.grey_percentage"> 70%
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" value="100%"
                                            v-model="primary_analysis.grey_percentage"> 100%
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </table>
                    </div>


                    <hr>
                    <!-- History -->
                    <h5 class="mb-3 mt-3">History</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered new-form-table" cellspacing="0" cellpadding="6"
                            width="100%">
                            <!-- Hair Type -->
                            <tr>
                                <th style="border-bottom: 1px solid #0000001a;" rowspan="5">Hair Type</th>
                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="hair_history.hair_type[0].checked"> Virgin
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                        v-model="hair_history.hair_type[0].description"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="hair_history.hair_type[1].checked"> Henna
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                        v-model="hair_history.hair_type[1].description"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="hair_history.hair_type[2].checked"> Chemically Treated
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                        v-model="hair_history.hair_type[2].description"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="hair_history.hair_type[3].checked"> Coloured
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                        v-model="hair_history.hair_type[3].description"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td style="border-bottom: 1px solid #0000001a;">
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="hair_history.hair_type[4].checked"> Keratin
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td style="border-bottom: 1px solid #0000001a;">
                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                        v-model="hair_history.hair_type[4].description"></textarea>
                                </td>
                            </tr>
                            <!-- Allergies -->
                            <tr>
                                <th style="border-bottom: 1px solid #0000001a;" rowspan="4">Allergies/Sensitivities
                                </th>
                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="hair_history.allergies[0].checked"> Hair Dye
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                        v-model="hair_history.allergies[0].description"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="hair_history.allergies[1].checked"> Chemical Solution
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                        v-model="hair_history.allergies[1].description"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="hair_history.allergies[2].checked"> Blonder/Lightener
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                        v-model="hair_history.allergies[2].description"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td style="border-bottom: 1px solid #0000001a;">
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="hair_history.allergies[3].checked"> Other
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td style="border-bottom: 1px solid #0000001a;">
                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                        v-model="hair_history.allergies[3].description"></textarea>
                                </td>
                            </tr>
                            <!-- Medical Condition -->
                            <tr>
                                <th style="border-bottom: 1px solid #0000001a;" rowspan="2">Medical Condition</th>
                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="hair_history.medical_condition[0].checked"> Pregnant
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td style="padding-top: 10px; padding-bottom: 4px;">
                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                        v-model="hair_history.medical_condition[0].description"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td style="border-bottom: 1px solid #0000001a;">
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="hair_history.medical_condition[1].checked"> Medicine Conception
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td style="border-bottom: 1px solid #0000001a;">
                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                        v-model="hair_history.medical_condition[1].description"></textarea>
                                </td>
                            </tr>
                            <!-- Home Care -->
                            <tr>
                                <th style="border-bottom: 1px solid #0000001a;">Home Care</th>
                                <td style="border-bottom: 1px solid #0000001a;">
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="hair_history.home_care[0].checked"> Shampoo/Conditioner/Serum
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td style="border-bottom: 1px solid #0000001a;">
                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                        v-model="hair_history.home_care[0].description"></textarea>
                                </td>
                            </tr>
                            <!-- Oiling Habit -->
                            <tr>
                                <th style="border-bottom: 1px solid #0000001a;">Oiling Habit</th>
                                <td style="border-bottom: 1px solid #0000001a;">
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="hair_history.oiling_habit[0].checked"> Frequency/Type of Oil
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td style="border-bottom: 1px solid #0000001a;">
                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                        v-model="hair_history.oiling_habit[0].description"></textarea>
                                </td>
                            </tr>
                            <!-- Use of Styling Product -->
                            <tr>
                                <th style="border-bottom: 1px solid #0000001a;">Use of Styling Product</th>
                                <td style="border-bottom: 1px solid #0000001a;">
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="hair_history.styling_product[0].checked"> Heating Tools/Styling Creams
                                        <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td style="border-bottom: 1px solid #0000001a;">
                                    <textarea rows="1" class="browz-gray-input-custom" style="width:100%; min-width:120px"
                                        v-model="hair_history.styling_product[0].description"></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <h5 class="mb-3 mt-3">Client desire</h5>

                    <table class=" table table-bordered new-form-table border-0" cellspacing="0" cellpadding="6"
                        width="100%">
                        <tr>
                            <th>Service Needed</th>
                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                    v-model="client_desire.service_needed.description1"></td>
                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                    v-model="client_desire.service_needed.description2"></td>
                        </tr>
                        <tr>
                            <th>Desired Result</th>
                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                    v-model="client_desire.desired_result.description1"></td>
                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                    v-model="client_desire.desired_result.description2"></td>
                        </tr>
                        <tr>
                            <th>Technique</th>
                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                    v-model="client_desire.technique.description1"></td>
                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                    v-model="client_desire.technique.description2"></td>
                        </tr>
                        <tr>
                            <th>Patch Test</th>
                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                    v-model="client_desire.patch_test.description1"></td>
                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                    v-model="client_desire.patch_test.description2"></td>
                        </tr>
                        <tr>
                            <th>Product Used</th>
                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                    v-model="client_desire.product_used.description1"></td>
                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                    v-model="client_desire.product_used.description2"></td>
                        </tr>
                        <tr>
                            <th>After Care</th>
                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                    v-model="client_desire.after_care.description1"></td>
                            <td><input type="text" class="browz-gray-input-custom" style="width:100%;"
                                    v-model="client_desire.after_care.description2"></td>
                        </tr>
                    </table>

                    <hr>

                    <h5 class="my-3">Declaration</h5>

                    <ul class="listing-ddd">
                        <li>I <input v-model="client_name"
                                style="border: none; border-bottom: 1px solid #4444; width: 100px; background-color: #fff; ">
                            confirm the accuracy of the information provided above.</li>
                        <li>I <input v-model="client_name"
                                style="border: none; border-bottom: 1px solid #4444; width: 100px; background-color: #fff; ">
                            understand that chemical services involve potential risks including but not limited to
                            allergic reactions, irritation, hair breakage, or unsatisfactory results.</li>
                        <li>I <input v-model="client_name"
                                style="border: none; border-bottom: 1px solid #4444; width: 100px; background-color: #fff; ">
                            agree to have a patch/strand test performed if recommended by the stylist.</li>
                        </li>
                        <li>I <input v-model="client_name"
                                style="border: none; border-bottom: 1px solid #4444; width: 100px; background-color: #fff; ">
                            understand that results vary based on hair condition, previous services, and aftercare.</li>
                        <li>I <input v-model="client_name"
                                style="border: none; border-bottom: 1px solid #4444; width: 100px; background-color: #fff; ">
                            will follow the recommended post-service care provided by the stylist. I understand the
                            failure to follow the instructions may be affect the results or lead to damage</li>
                        <li>I <input v-model="client_name"
                                style="border: none; border-bottom: 1px solid #4444; width: 100px; background-color: #fff; ">
                            release <b>BROWNZ</b> , its employees, and affiliates from liability in the case of an
                            undesired result, reaction, or damage due to disclosed or undisclosed conditions.</li>
                        <li>I have been informed about the cost of service before availing it</li>
                    </ul>


                    <h4 class="my-3" style="font-size: 16px; color:#444;">Contraindications : please read carefully
                    </h4>

                    <ol class="listing-ddd">
                        <li>The purpose , procedure , and expected results of the service have been clearly explained to
                            me by the stylist </li>
                        <li>Post colour cannot do straightening /smoothening for at least 8 - 10 days </li>
                        <li>In case of pre-lightening to achieve desired result (colour change ) may take 2-3 sittings
                            depending on colour coating </li>
                        <li>Any colour after pre-lightening will last up to 10 -12 washes </li>
                        <li>Certain services can not be done on person having Asthma , High Blood pressure , undergoing
                            Chemo therapy , experiencing exceptional hair loss , Infection on scalp , Alopecia ,
                            Nits/lice , any specific allergies or pregnant women and feeding mothers</li>
                    </ol>

                    {{-- <h4 class="my-3" style="font-size: 16px">Permission to use photos / videos </h4>
                    <div class="d-flex align-items-center">
                        <label class="brw-checkbox-browz-dds mr-4">
                            <input class="brow-checkbox-dd" type="radio" value="1" v-model="photo_permission"
                                name="photo_permission"> Yes
                            <span class="brow-checkmark"></span>
                        </label>

                        <label class="brw-checkbox-browz-dds">
                            <input class="brow-checkbox-dd" type="radio" value="0" v-model="photo_permission"
                                name="photo_permission"> No
                            <span class="brow-checkmark"></span>
                        </label>
                    </div>

                    <ul class="listing-ddd">
                        <li>Salon will use the photos and videos Respectfully and Professionally</li>

                        <li>These images and videos may be used publicly or digitally to showcase the work and services
                            of <b>BROWNZ</b> salon</li>

                    </ul>

                    <div class="row row-gap-4">
                        <div class="col-12 col-md-8">
                            <div class="row ">
                                <div class="col-sm-6 col-12">
                                    <p class="d-flex"><b>Client name :</b><input
                                            style="border: none; border-bottom: 1px solid #4444; width: auto; background-color: #fff; ">
                                    </p>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <p>Signature </p>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <p class="d-flex"><b>Stylist name :</b><input
                                            style="border: none; border-bottom: 1px solid #4444; width: auto; background-color: #fff; ">
                                    </p>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <p>Signature </p>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <p class="d-flex"><b>Manager name :</b><input
                                            style="border: none; border-bottom: 1px solid #4444; width: auto; background-color: #fff; ">
                                    </p>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <p>Signature </p>

                                </div>
                            </div>
                        </div>
                    </div> --}}

                </form>
            </div>

            <div class="modal-footer pt-0 border-0">
                <button @click="shereHairSubmission" type="button" class="btn_dark-x submit-btn"
                    :disabled="loading">
                    <i class="fa fa-check"></i> @{{ loading ? 'Sharing...' : 'Share' }}
                </button>

                <div id="hair_loading_spinner" v-show="loading">Processing...</div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="skinLookup" tabindex="-1" role="dialog" aria-labelledby="skinConsultationTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 1140px;">
        <div class="modal-content p-3" style="max-height: 850px; overflow-y: auto;">
            <div class="modal-header pb-0 border-0">
                <h5 class="modal-title popup-head-bootst" id="skinConsultationTitle">
                    Skin Consultation Form
                </h5>
                <button type="button" class="brownz-close-btn" @click="resetSkinModal"
                    data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <form id="skinConsultationForm">
                    <!-- Client Info -->
                    <div class="row mb-4" style="row-gap: 16px">
                        <div class="col-md-6">
                            <label for="skin_client_name">Client Name</label>
                            <input type="text" class="browz-gray-input-custom" id="skin_client_name"
                                name="client_name" v-model="client_name" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="skin_age">Age</label>
                            <input type="number" class="browz-gray-input-custom" id="skin_age" name="age"
                                v-model="client_age">
                        </div>
                        <div class="col-md-3">
                            <label for="skin_date">Date</label>
                            <input type="date" class="browz-gray-input-custom" id="skin_date" name="date"
                                v-model="date">
                        </div>
                        <div class="col-md-6">
                            <label for="skin_phone1">Phone 1</label>
                            <input type="text" class="browz-gray-input-custom" id="skin_phone1" name="phone1"
                                v-model="client_mobile" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="skin_phone2">Phone 2</label>
                            <input type="text" class="browz-gray-input-custom" id="skin_phone2" name="phone2"
                                v-model="client_phone">
                        </div>
                        <div class="col-md-6">
                            <label for="skin_stylist_name">Stylist Name</label>
                            <input type="text" class="browz-gray-input-custom" id="skin_stylist_name"
                                name="stylist_name" v-model="stylist_name">
                        </div>
                        <div class="col-md-6">
                            <label for="skin_designation">Designation</label>
                            <input type="text" class="browz-gray-input-custom" id="skin_designation"
                                name="designation" v-model="designation">
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-3 mt-3">Skin Analysis</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered new-form-table">
                            <tr>
                                <th>Skin type</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_type" value="Normal">
                                        Normal <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_type" value="Dry">
                                        Dry <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_type" value="Oily">
                                        Oily <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_type" value="Combination">
                                        Combination <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_type" value="Pore">
                                        Pore <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_type" value="Other">
                                        Other <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                            </tr>

                            <tr>
                                <th>Skin concern</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_concern" value="Acne">
                                        Acne <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_concern" value="Pigmentation">
                                        Pigmentation <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_concern" value="Dullness">
                                        Dullness <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_concern" value="Wrinkles">
                                        Wrinkles <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_concern" value="Pore">
                                        Pore <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_concern" value="Other">
                                        Other <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                            </tr>

                            <tr>
                                <th>Skin allergy</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_allergy" value="Yes">
                                        Yes <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_allergy" value="No">
                                        No <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td colspan="4"></td>
                            </tr>

                            <tr>
                                <th>Skin sensitivity</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_sensitivity" value="Yes">
                                        Yes <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_sensitivity" value="No">
                                        No <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td colspan="4"></td>
                            </tr>

                            <tr>
                                <th>Skin routine</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_routine" value="Cleanser">
                                        Cleanser <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_routine" value="Moisturiser">
                                        Moisturiser <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_routine" value="Sunscreen">
                                        Sunscreen <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_routine" value="Serum">
                                        Serum <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_routine" value="Toner">
                                        Toner <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="skin_analysis.skin_routine" value="Night Care">
                                        Night Care <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                            </tr>
                        </table>


                    </div>

                    <hr>

                    <h5 class="mb-3 mt-3">Life style</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered new-form-table">
                            <tr>
                                <th>Water intakes</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="life_style.water_intakes" value="Low">
                                        Low <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="life_style.water_intakes" value="Medium">
                                        Medium <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="life_style.water_intakes" value="High">
                                        High <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                            </tr>

                            <tr>
                                <th>Sleep</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" v-model="life_style.sleep"
                                            value="Good">
                                        Good <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" v-model="life_style.sleep"
                                            value="Moderate">
                                        Moderate <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" v-model="life_style.sleep"
                                            value="Poor">
                                        Poor <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                            </tr>

                            <tr>
                                <th>Stress</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="life_style.stress" value="Low">
                                        Low <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="life_style.stress" value="Moderate">
                                        Moderate <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="life_style.stress" value="High">
                                        High <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                            </tr>

                            <tr>
                                <th>Sun exposure</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="life_style.sun_exposure" value="Yes">
                                        Yes <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="life_style.sun_exposure" value="No">
                                        No <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td></td>
                            </tr>

                            <tr>
                                <th>Smoke</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" v-model="life_style.smoke"
                                            value="Yes">
                                        Yes <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" v-model="life_style.smoke"
                                            value="No">
                                        No <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td></td>
                            </tr>

                            <tr>
                                <th>Drinks</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="life_style.drinks" value="Yes">
                                        Yes <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="life_style.drinks" value="No">
                                        No <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td></td>
                            </tr>
                        </table>
                    </div>

                    <hr>

                    <h5 class="mb-3 mt-3">History</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered new-form-table border-0">
                            <tr>
                                <th>Medication</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="history.medication" value="yes">
                                        Yes <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="history.medication" value="no">
                                        No <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <th>Hormonal Issues</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="history.hormonal_issues" value="yes">
                                        Yes <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="history.hormonal_issues" value="no">
                                        No <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <th>Past Treatments</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="history.past_treatments" value="peel">
                                        Peel <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="history.past_treatments" value="botox">
                                        Botox <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="history.past_treatments" value="laser">
                                        Laser <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="history.past_treatments" value="other">
                                        Other <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                            </tr>

                            <tr>
                                <th>Chronic Illness</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="history.chronic_illness" value="diabetes">
                                        Diabetes <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="history.chronic_illness" value="thyroid">
                                        Thyroid <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox"
                                            v-model="history.chronic_illness" value="other">
                                        Other <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td></td>

                            </tr>

                            <tr>
                                <th>Pregnant</th>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" v-model="history.pregnant"
                                            value="yes">
                                        Yes <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="brw-checkbox-browz-dds">
                                        <input class="brow-checkbox-dd" type="checkbox" v-model="history.pregnant"
                                            value="no">
                                        No <span class="brow-checkmark"></span>
                                    </label>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                    </div>


                    <h5 class="mb-3 mt-3">Conclusion</h5>
                    <table class=" table table-bordered new-form-table border-0" cellspacing="0" cellpadding="6"
                        width="100%">
                        <tr>
                            <th style="width: 200px">Recommended service</th>
                            <td><input type="text" class="browz-gray-input-custom"
                                    v-model="conclusion.recommended_service.description" style="width:100%;"></td>

                        </tr>
                        <tr>
                            <th style="width: 200px">After care </th>
                            <td><input type="text" class="browz-gray-input-custom"
                                    v-model="conclusion.after_care.description" style="width:100%;"></td>

                        </tr>
                        <tr>
                            <th style="width: 200px">Follow up plan</th>
                            <td><input type="text" class="browz-gray-input-custom"
                                    v-model="conclusion.follow_up_plan.description" style="width:100%;"></td>
                        </tr>
                    </table>

                    <hr>

                    <h5 class="my-3">After care guidelines</h5>
                    <ul class="listing-ddd">
                        <li>Avoid sun exposure and always apply sunscreen.</li>
                        <li>Skip make-up ,hot water , or harsh products for 24-48 hrs.</li>
                        <li>Keep skin and body hydrated with water plus moisturizer.</li>
                        <li>Use soothing gel /cream[aloe vera , calming lotion] if redness or irritation occurs.</li>
                        <li>Follow gentle home-care routine as advised by the therapist.</li>
                        <li>Maintain regular salon visits for best results.</li>
                    </ul>

                    <h5 class="my-3">Declaration</h5>
                    <p style="font-size: 16px">I<input
                            style="border: none; border-bottom: 1px solid #4444; width: 100px; background-color: #fff;" v-model="client_name">
                        her by declare that ;</p>
                    <ol class="listing-ddd">
                        <li>The information I have provided regarding my skin is true to the best of my knowledge.</li>
                        <li>I understand that results of skin services may vary depending upon my skin condition and
                            after care practice.</li>
                        <li>I have disclosed any allergies, sensitivities , or health issues that may affect the
                            treatment.</li>
                        <li>I consent to undergo the recommended skin services and understand the procedure has been
                            explained to me by the therapist.</li>
                        <li>I agree to follow the suggested <b>after care instruction</b> for the best results and to
                            avoid any adverse reactions.</li>
                        <li>I release <b>the salon / therapist</b> from responsibility in case of any reaction arising
                            due to undisclosed medical history, allergies, or home-care negligence</li>
                    </ol>

                    {{-- <h5 class="my-3">Permission to use photos / videos </h5>
                    <div class="d-flex align-items-center">
                        <label class="brw-checkbox-browz-dds mr-4">
                            <input class="brow-checkbox-dd" type="radio" value="1"
                                v-model="skin_photo_permission" name="photo_permission"> Yes
                            <span class="brow-checkmark"></span>
                        </label>

                        <label class="brw-checkbox-browz-dds">
                            <input class="brow-checkbox-dd" type="radio" value="0"
                                v-model="skin_photo_permission" name="photo_permission"> No
                            <span class="brow-checkmark"></span>
                        </label>
                    </div>

                    <p style="font-size: 16px">Salon will use the photos and videos Respectfully and Professionally
                    </p>
                    <ul class="listing-ddd">
                        <li style="font-size: 16px;">These images and videos may be used publicly or digitally to
                            showcase the work and services of <b>BROWNZ</b> salon</li>
                    </ul> --}}

                    {{-- <div class="row row-gap-4">
                        <div class="col-12 col-md-8">
                            <div class="row ">
                                <div class="col-sm-6 col-12">
                                    <p class="d-flex"><b>Client name :</b><input
                                            style="border: none; border-bottom: 1px solid #4444; width: auto; background-color: #fff; ">
                                    </p>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <p>Signature </p>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <p class="d-flex"><b>Stylist name :</b><input
                                            style="border: none; border-bottom: 1px solid #4444; width: auto; background-color: #fff; ">
                                    </p>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <p>Signature </p>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <p class="d-flex"><b>Manager name :</b><input
                                            style="border: none; border-bottom: 1px solid #4444; width: auto; background-color: #fff; ">
                                    </p>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <p>Signature </p>

                                </div>
                            </div>
                        </div>
                    </div> --}}

                </form>
            </div>

            <div class="modal-footer pt-0 border-0">
                <button @click="shereSkinSubmission" type="button" class="btn_dark-x submit-btn"
                    :disabled="loading">
                    <i class="fa fa-check"></i> @{{ loading ? 'Sharing...' : 'Share' }}
                </button>

                <div id="skin_loading_spinner" v-show="loading">Processing...</div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4 text-center">
            <h5>Continue on Another Device</h5>
            <div id="qrCodeContainer"></div>
        </div>
    </div>
</div>
