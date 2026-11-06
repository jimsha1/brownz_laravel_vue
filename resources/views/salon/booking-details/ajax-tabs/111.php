<?php
/*
* This blade is using VueJs for rendering values.
* Do not use server side PHP variables directly inside!
*/
?>
<style>
    .example::-webkit-scrollbar {
  display: none;
}
.example {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
    [dir="rtl"] .float-right {
        float: left !important
    }

    [dir="rtl"] .float-left {
        float: right !important
    }

    [dir="rtl"] .text-right {
        text-align: left !important
    }

    [dir="rtl"] .text-left {
        text-align: right !important
    }

    [dir="rtl"] .input-group>.form-control:not(:last-child),
    [dir="rtl"] .input-group>.custom-select:not(:last-child) {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-top-right-radius: .25rem;
        border-bottom-right-radius: .25rem;
    }

    [dir="rtl"] .input-group>.input-group-append>.btn,
    [dir="rtl"] .input-group>.input-group-append>.input-group-text,
    [dir="rtl"] .input-group>.input-group-prepend:not(:first-child)>.btn,
    [dir="rtl"] .input-group>.input-group-prepend:not(:first-child)>.input-group-text,
    [dir="rtl"] .input-group>.input-group-prepend:first-child>.btn:not(:first-child),
    [dir="rtl"] .input-group>.input-group-prepend:first-child>.input-group-text:not(:first-child) {
        border-top-left-radius: .25rem;
        border-bottom-left-radius: .25rem;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

</style>

<style>
    .anim-active td {
        /*background-color:#c6c6df;*/
        animation: activepulse 3s;
    }

    @keyframes activepulse {

        0%,
        100% {
            background-color: inherit;
        }

        50% {
            background-color: #9fd6f0;
        }
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #76c1ee;
    }

</style>
<div id="vue-details-tab-edit" class="has-vue-form">

    @if (Session::has('message'))
    <div class="alert alert-primary" role="alert">
        {{ Session::get('message') }}
    </div>
@endif
    <template>
        <div v-if="booking_info.canceled_by" class="alert alert-danger">
            {{ __('This booking has canceled!') }}
        </div>

        @include('salon.booking-details.ajax-tabs.partials-tab-edit.booking-form')

        <hr v-if="booking_id > 0" class="mb-4">

        <div class="row">
            <div v-if="booking_id > 0" class="col-sm-4">
                @include('salon.booking-details.ajax-tabs.partials-tab-edit.booking-service-search')
            </div>
            <div v-if="booking_id > 0" class="col-sm-4">
                <?php
                // @include('salon.booking-details.ajax-tabs.partials-tab-edit.booking-item-search')
                ?>
            </div>

            <div v-if="booking_info.booking_total > 0" class="col-sm-4">
                <h3 class="mb-2 text-right">
                    <span class="bg-success rounded px-2 py-1" v-model="grand_total_1" id="grand_total_2"
                        style="font-size: .70em;">
                        {{ __('Total:') }} @{{ grand_total_1 }}
                    </span>
                </h3>
            </div>
            {{----------------------------- refund section -------------------------------------}}
            <div v-if="booking_info.booking_total < 0" class="col-sm-4">
                <h3 class="mb-2 text-right">
                    <span class="bg-success rounded px-2 py-1" v-model="grand_total_1" id="grand_total_2"
                        style="font-size: .70em;">
                        {{ __('Total:') }} @{{ grand_total_1 }}
                    </span>
                </h3>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @include('salon.booking-details.ajax-tabs.partials-tab-edit.booking-service-existing')
            </div>
        </div>

        @include('salon.booking-details.ajax-tabs.partials-tab-edit.discount-form')

        <div v-if="booking_id > 0" class="row">
            <div class="col-sm-5">
                @include('salon.booking-details.ajax-tabs.partials-tab-edit.booking-cancel-delete-form')
            </div>
            <div v-if="booking_id > 0 && (booking_info.booking_total > 0 || booking_info.payment_total > 0)"
                class="col-sm-7">
                @include('salon.booking-details.ajax-tabs.partials-tab-edit.order-payments')
                @include('salon.booking-details.ajax-tabs.partials-tab-edit.action-buttons')
            </div>
            {{--------------------------- refund section  -------------------------------}}
            <div v-if="booking_id > 0 && (booking_info.booking_total < 0 )"
                class="col-sm-7">
                @include('salon.booking-details.ajax-tabs.partials-tab-edit.order-payments')
                @include('salon.booking-details.ajax-tabs.partials-tab-edit.action-buttons')
            </div>
        </div>

        @include('salon.booking-details.ajax-tabs.partials-tab-edit.beautician-slot-modal')
        @include('salon.booking-details.ajax-tabs.partials-tab-edit.client-update-modal')
    </template>
</div>

<script>
    var vue_details_tab_edit = null;
    var expired_date = "";
    var all_booking_info = [];
    var invoice_window = "";
    jQuery(function($) {

        vue_details_tab_edit = new Vue({
            el: "#vue-details-tab-edit",
            data: <?php echo json_encode(
                array_merge($details_edit_vue, [
                    'popup_client_type' => null,
                    'all_shifts' => [],
                    'amount_taken_flag' => 0,
                    'beatician_html_id' => 'beatician_html_id',
                    'onChange' => 0,
                    'shift_id' => '',
                    'bu' => 'bu',
                    'booking_service_id' => 'booking_service_id',
                    'grand_total' => '',
                    'grand_total_1' => '',
                    'amount_taken' => '',
                    'new_duration' => [],
                    'duration' => '',
                    'duration_minutes' => '',
                    'time_slot_form' => 'time_slot_form',
                    'beauticians' => [],
                    'price' => '',
                    'beautician' => '',
                    'time_slot' => [],
                    'discount_percent' => 'discount_percent_',
                    'discount_percent_item' => 'discount_percent_item_',
                    'beautician_id' => '',
                    'changing_price_var' => '',
                    'total_price_var' => '',
                    'total_price_var_discount' => '',
                    'tax_var' => '',
                    'time_selected' => 0,
                    'btn_slot_id' => '',
                    'price_html_id' => 'price_',
                    'total_price_discount' => 'discount_total_',
                    'line_discount_form' => 'line_discount_form_',
                    'tax_html_id' => 'tax_',
                    'total_html_id' => 'total_',
                    'total_item_html_id' => 'total_item_',
                    'time_slot_btn_id' => 'slot_btn_',
                    'service_id_var' => 'service_id_var_',
                    'service_id_html' => '',
                    'service_id' => '',
                    'temp_beatician_id' => '',
                ]),
            ); ?> ,

            mounted: function() {
                //invoice_window.close();
                console.log(this.booking_info);
                if ( <?= $check_float_amount ?> == 0) {
                    if (confirm('cashier not assigned to the till')) {
                        window.location.href = "{{ route('float_amt', ['id' => 0]) }}";
                    } else {
                        window.location.href = "{{ route('float_amt', ['id' => 0]) }}";
                    }
                }

                if (this.booking_info.booking_type == "Draft") {

                    var return_amount = this.booking_info.booking_total;
                    $('#amount_taken').val('0');
                    $('#amount_given').val(return_amount);
                }
                this.$nextTick(function() {
                    this.initSelect2();
                    this.refreshSelect2Services();
                    this.refreshSelect2Items();
                    this.grandTotal_all_raw();
                    refreshFlatPicker();
                    this.refreshDiscountFunction();
                    if (this.booking_info.client_id) {
                        $("#client_id").select2("focus");
                    } else {
                        $("#client_id").select2("open");
                    }
                })

            },

            updated: function() {
                this.$nextTick(function() {
                    this.refreshSelect2Services();
                    this.refreshSelect2Items();
                    applySelect2();
                    refreshFlatPicker();
                    this.refreshDiscountFunction();
                })
            },
            filters: {
                time_hm: function(value) {
                    return value ? value.substring(0, 5) : '';
                }
            },
            methods: {
                v_totalPrice: function(value1, value2) {

                    total_price = parseFloat(value1) + parseFloat(value2);
                    return total_price.toFixed(2);
                },
                v_totalNetTotal: function(value1, value2) {
                    total_price = parseFloat(value1) - parseFloat(value2);
                    return total_price.toFixed(2);
                },
                v_amount: function(value) {
                    return parseFloat(value);
                },
                v_fixed_amount: function(value) {
                    return parseFloat(value).toFixed(2);
                },
                /*
                clientChanged: function(event){
                  var el = event.target;
                  var copy_props = ['name', 'mobile', 'email', 'telephone'];
                  for(var i in copy_props){
                    this.booking_info["client_" + copy_props[i]] = el.options[el.selectedIndex].dataset[copy_props[i]];
                  }
                },
                */

                focusedServiceBeauticians: function() {
                    for (var i in this.booking_info.booking_only_services) {
                        var bkservice = this.booking_info.booking_only_services[i];
                        if (bkservice.service_id == this.focused_active_service) {
                            return bkservice.service.beauticians;
                        }
                    }
                    return [];
                },
                initSelect2: function() {
                    $(".voco-select2").select2();
                },


                check_mobile_no: function() {
    var value = String($('#mobile').val() || '').replace(/\D/g, '');
    if (value !== $('#mobile').val()) {
        $('#mobile').val(value);
    }
    $('#number_validation').text('');
    $('.btn-voco-save').removeAttr('disabled');
    if (value.length !== 10) {
      $('.btn-voco-save').prop("disabled", true);
            if ('<?= app()->getLocale() ?>' === 'en'){
  $('#number_validation').text('Please enter a valid 10-digit mobile number');
     }else{
        $('#number_validation').text('الرجاء إدخال رقم جوال صالح من 10 أرقام');
     }
    }
    },

                refreshSelect2Services: function() {
                    // var added_services = this.booking_info.booking_only_services;
                    var added_services = [];
                    var formatted = [];
                    for (var i in this.all_db_services) {
                        var item = this.all_db_services[i];
                        var exclude = false;
                        for (var j in added_services) {
                            if (item.service_id == added_services[j].service_id) {
                                exclude = true;
                                break;
                            }
                        }

                        if (exclude === false) {

                            formatted.push({
                                "id": item.service_id,
                                "text": item.name + " - SAR " + item.service_base_price,
                                "service_id": item.service_id,
                                "price_id": null
                            });
                        }
                    }

                    //var added_items = this.booking_info.booking_items;
                    var added_items = "";
                    // var formatted = [];
                    for (var i in this.all_db_items) {
                        var item = this.all_db_items[i];
                        var exclude = false;
                        for (var j in added_items) {
                            if (item.price_id == added_items[j].price_id) {
                                exclude = true;
                                break;
                            }
                        }

                        if (exclude === false) {

                            if (item.batch_no == "") {
                                batch_no = "";
                                expiry_date = "";
                            } else {
                                batch_no = item.batch_no;
                                expiry_date = item.expiry_date;
                            }

                            formatted.push({
                                "id": item.price_id,
                                "text": item.item_name + " - SAR " + item.price + " " +
                                    batch_no +" - bardcode - "+item.barcode,
                                "price_id": item.price_id,
                                "batch_no": batch_no,
                                "expiry_date": expiry_date,
                                "service_id": null
                            });
                        }
                    }

                    if ($("#add_service_id").hasClass("select2-hidden-accessible")) {
                        $('#add_service_id').select2("destroy");
                        $('#add_service_id').html("");
                    }

                    $("#add_service_id").select2({
                        "data": formatted,
                        templateSelection: function(data, container) {
                            $(data.element).attr('data-service-id', data.service_id);
                            $(data.element).attr('data-price-id', data.price_id);
                            $(data.element).attr('data-batch-no', data.batch_no);
                            $(data.element).attr('data-expiry-date', data.expiry_date);
                            return data.text;
                        }
                    });
                    $("#add_service_id").val(null).trigger('change.select2');



                },
                refreshSelect2Items: function() {
                    var added_items = this.booking_info.booking_items;
                    var formatted = [];
                    for (var i in this.all_db_items) {
                        var item = this.all_db_items[i];
                        var exclude = false;
                        for (var j in added_items) {
                            if (item.price_id == added_items[j].price_id) {
                                exclude = true;
                                break;
                            }
                        }

                        if (exclude === false) {
                            formatted.push({
                                "id": item.price_id,
                                "text": item.item_name + " - SAR " + item.price,
                                "price_id": item.price_id,
                                "batch_no": batch_no,
                                "expiry_date": expiry_date
                            });
                        }
                    }

                    if ($("#add_price_id").hasClass("select2-hidden-accessible")) {
                        $('#add_price_id').select2("destroy");
                        $('#add_price_id').html("");
                    }

                    $("#add_price_id").select2({
                        "data": formatted,
                        templateSelection: function(data, container) {
                            $(data.element).attr('data-price-id', data.price_id);
                            $(data.element).attr('data-batch-no', data.batch_no);
                            $(data.element).attr('data-expiry-date', data.expiry_date);
                            return data.text;
                        }
                    });
                    $("#add_price_id").val(null).trigger('change.select2');
                    this.grandTotal_all_raw();

                },
                paymentModeChanged: function(event, target_element) {
                    $("#add_payment_btn").attr("disabled", false);
                    var el = event.target;
                    var $target = $(target_element);
                    if (el.options[el.selectedIndex].value === "card") {

                        if (this.amount_taken > this.grand_total) {
                            this.amount_taken = this.grand_total;
                            $('#amount_given').val('given...');
                        }
                        $target.css('display', 'inline-block');
                    } else {
                        $target.css('display', 'none');
                    }
                },
                grandTotal_all_raw: function() {

                    if (vue_details_tab_edit.amount_taken_flag == 0) {
                        all_booking_info = this.booking_info;
                        var grandTotal = 0;
                        for (var i in this.booking_info.booking_only_services) {
                            var service_id = this.booking_info.booking_only_services[i]
                                .booking_service_id;
                            var html_id = "#total_" + service_id;
                            var lineTotal = $(html_id).text();
                            console.log(lineTotal);
                            grandTotal = parseFloat(lineTotal) + grandTotal;

                        }
                        for (var i in this.booking_info.booking_items) {
                            var item_id = this.booking_info.booking_items[i]
                                .booking_item_id;
                            var html_id = "#total_item_" + item_id;
                            var lineTotal = $(html_id).text();
                            console.log(lineTotal);
                            grandTotal = parseFloat(lineTotal) + grandTotal;

                        }
                        console.log('grandTotal');
                        console.log(grandTotal);
                       total =  grandTotal - this.booking_info.payment_total
                        vue_details_tab_edit.amount_taken = Math.abs(total);

                        vue_details_tab_edit.grand_total = grandTotal;
                        vue_details_tab_edit.grand_total_1 = grandTotal;

                        // $('#amount_taken').val(grandTotal);
                        // $('#amt').val(grandTotal);
                    }
                    // if (v_amount(booking_info.booking_total) > v_amount(booking_info.payment_total)){
                    // vue_details_tab_edit.amount_taken = 0;
                    // }
                    vue_details_tab_edit.amount_taken_flag = 0;
                },
                updateinvoice: function(params) {
                    invoice_window = window.open(params, "",
                        "toolbar=no,status=no,menubar=no,location=center,scrollbars=no,resizable=no,height=500,width=657"
                        );

                    var url_page = "/salon/booking/tab/0/edit";
                    window.location.href = url_page;

                },
                refreshDiscountFunction: function() {

                    this.booking_info = this.booking_info;
                },

                //========================== new added section ============================================
                updatePrice: function(booking_service_id) {
                    $.ajax({
                        method: "POST",
                        url: "{{ route('salon.booking.price.update') }}",
                        data: {
                            booking_service_id: booking_service_id,
                            service_id: vue_details_tab_edit.service_id,
                            booking_id: this.booking_id,
                            shift_id: $("#shift_data :selected").val()
                        },
                    }).done(function(data) {

                    });
                },
                lineDiscount: function(obj, bserv, form_id, service_html, grand_total_html,
                    percentage_html_id) {

                    $(service_html).val(bserv.service_id);
                    $(grand_total_html).val(bserv.service_booking_price);
                    var percentage = $(percentage_html_id).val();

                    $.ajax({
                        method: "POST",
                        url: "{{ route('salon.booking.discount.save') }}",
                        data: {
                            applied_discount_percent: percentage,
                            booking_id: this.booking_id,
                            service_id: bserv.service_id,
                            booking_service_id: bserv.booking_service_id,
                            booking_item_id: 0,
                            total_price: bserv.service_booking_price
                        },
                    }).done(function(data) {
                        //location.reload();
                        //alert("line discount added succesfuly");
                    });


                    // if ($(service_html).val() != "" &&   $(grand_total_html).val()!= ""){

                    // $(form_id).submit();
                    //ajaxSubmitForm$(form_id).submit();
                    //$(form_id).ajaxForm({url: "{{ route('salon.booking.discount.save') }}", type: 'post'})
                    //ajaxSubmitForm($(obj).closest("form")[0])
                    //location.reload();
                    //}
                    // vue_details_tab_info.refreshSelect2Items();

                },
                lineDiscountItem: function(obj, bserv, form_id, service_html, grand_total_html,
                    percentage_html_id) {

                    $(service_html).val(bserv.service_id);
                    $(grand_total_html).val(bserv.service_booking_price);
                    var percentage = $(percentage_html_id).val();
                    $.ajax({
                        method: "POST",
                        url: "{{ route('salon.booking.discount.save') }}",
                        data: {
                            applied_discount_percent: percentage,
                            booking_id: this.booking_id,
                            item_id: bserv.item_id,
                            booking_item_id: bserv.booking_item_id,
                            total_price: bserv.unit_price,
                            booking_service_id: 0
                        },
                    }).done(function(data) {
                        //location.reload();
                        //alert("line discount added succesfuly");
                    });


                    // if ($(service_html).val() != "" &&   $(grand_total_html).val()!= ""){

                    // $(form_id).submit();
                    //ajaxSubmitForm$(form_id).submit();
                    //$(form_id).ajaxForm({url: "{{ route('salon.booking.discount.save') }}", type: 'post'})
                    //ajaxSubmitForm($(obj).closest("form")[0])
                    //location.reload();
                    //}
                    // vue_details_tab_info.refreshSelect2Items();

                },
                getTimeSlotBtn: function(btn_id, form_id, slot_start) {



                    vue_details_tab_edit.btn_slot_id = btn_id;

                    vue_details_tab_edit.btn_slot_id = btn_id;
                    // document.getElementById(btn_id).disabled = true;
                    var price = $(vue_details_tab_edit.changing_price_var).text();

                    var tax = $(vue_details_tab_edit.tax_var).text();
                    var total = parseFloat(price) + parseFloat(tax);
                    console.log(total);
                    $(vue_details_tab_edit.total_price_var).text(total);
                    $(vue_details_tab_edit.total_price_var_discount).val(price);


                    if(vue_details_tab_edit.temp_beatician_id){
                        vue_details_tab_edit.beautician_id  = vue_details_tab_edit.temp_beatician_id;
                    }



                    if(vue_details_tab_edit.beautician_id == ""){
                        alert('beautician not selected');
                        return 0 ;
                    }else{
                    $.ajax({
                        method: "POST",
                        url: "{{ route('salon.booking.item.update') }}",
                        data: {
                            service_id: vue_details_tab_edit.service_id,
                            booking_id: vue_details_tab_edit.booking_id,
                            beautician_id: vue_details_tab_edit.beautician_id,
                            booking_service_id: vue_details_tab_edit.booking_service_id,
                            shift_id: $("#shift_data").val(),
                            appointment_date: $("#booking_date").val(),
                            slot_start: slot_start
                        },
                    })
                    }
                    var price = $(vue_details_tab_edit.changing_price_var).text();
                    // var tax =  parseInt(price) * 15/100;
                    // total_price = price - tax;
                    // alert(parseInt(total_price));
                    this.updatePrice(vue_details_tab_edit.booking_service_id);
                    this.grandTotal_all_raw();
                    location.reload();
                    //var item = $('#time_slot_form').serializeArray();
                    // form_id = '#'+form_id;


                    //console.log(form_id);
                    //console.log($('#'+form_id));
                    //console.log($('#beautician-slot-modal').find('#'+form_id));
                    //$('#'+form_id).submit();
                },
                getShiftData: function(data, price_id, tax, total, discount_total, service_html_id,
                    bu_html_id, booking_service_id, shift_id) {
                    var b_serv = [];
                    b_serv = data;
                    this.beauticians = [];
                    this.new_duration = [];
                    $("#shift_data").val();
                    $("#duration_minutes").val();
                    $('#beautician-slot-modal').modal('show');
                    vue_details_tab_edit.changing_price_var = price_id;
                    vue_details_tab_edit.total_price_var = total;
                    vue_details_tab_edit.tax_var = tax;
                    vue_details_tab_edit.total_price_var_discount = discount_total;
                    vue_details_tab_edit.service_id_html = service_html_id;
                    vue_details_tab_edit.shift_id = shift_id;
                    //if ( vue_details_tab_edit.service_id == "" ) {

                    $(vue_details_tab_edit.service_id_html).val(data.service_id);

                    vue_details_tab_edit.service_id = data.service_id;
                    vue_details_tab_edit.booking_service_id = booking_service_id;

                    // }
                    //=========================  call booked slot update section =====================================================

                    var bu = $(bu_html_id).text();
                    const newText = bu.split(/\s/).join('');
                    var services = [];
                    services = this.booking_info.booking_only_services;
                    if (newText != "") {

                        $.ajax({
                            method: "POST",
                            url: "{{ route('all.booking.existing') }}",
                            data: {
                                service: data.service_id,
                                is_pos: 1,
                                service_type: 3,
                                shift_id: vue_details_tab_edit.shift_id,
                                booking_service_id: vue_details_tab_edit.booking_service_id
                            },
                        }).done(function(data) {
                            $('#booking_date').val(data.appointment_date);
                            if (data.all_db_shifts.length > 0) {
                                vue_details_tab_edit.all_shifts = data.all_db_shifts;
                            }
                            this.db_shift = b_serv.db_shift;


                            if (data.beauticians.length > 0) {
                                //  bu_selcted_data = b_serv.beauticians[0];
                                vue_details_tab_edit.beautician_id = b_serv.beautician_id;
                                bu_selected = "#bu" + b_serv.beautician_id;
                                time_selected = b_serv.slot_start;
                                vue_details_tab_edit.time_selected = time_selected.slice(0,
                                    -3);

                                setTimeout(function() {
                                    $(bu_selected).addClass("bu-selected");
                                    //$(time_selected).addClass("bu-selected");

                                    $("#shift_data").val(b_serv.shift_id);
                                    if (data.new_duration.length > 0) {
                                        vue_details_tab_edit.new_duration = data
                                            .new_duration;
                                        vue_details_tab_edit.duration_minutes =
                                            vue_details_tab_edit.new_duration[0][0];
                                    }

                                }, 1000);

                            }
                            vue_details_tab_edit.time_slot = data.time_slot;
                            vue_details_tab_edit.beauticians = data.beauticians;
                            $('input[name=time]').attr('checked', false);
                            if (!data.beautician_flag) {
                                $('input[name=beautician]').attr('checked', false);
                            }

                        });

                    } else {
                        //==========================  end section =============================================
                        var bu_selected_htm_id = "";
                        $.ajax({
                            method: "POST",
                            url: "{{ route('all.booking') }}",
                            data: {
                                service: data.service_id,
                                is_pos: 1,
                                service_type: 3,
                                shift_id: vue_details_tab_edit.shift_id
                            },
                        }).done(function(data) {

                            if (data.all_db_shifts.length > 0) {
                                vue_details_tab_edit.all_shifts = data.all_db_shifts;
                            }



                            if (data.new_duration.length > 0) {
                                vue_details_tab_edit.new_duration = data.new_duration;
                                vue_details_tab_edit.duration_minutes = vue_details_tab_edit
                                    .new_duration[0][0];
                            }
                            if (data.beauticians.length > 0) {
                                bu_selcted_data = data.beauticians[0];
                                vue_details_tab_edit.beautician_id = bu_selcted_data.beautician_id;

                                bu_selected = "#bu" + bu_selcted_data.beautician_id;
                                bu_selected_htm_id = "#bu" + bu_selcted_data.beautician_id;

                                setTimeout(function() {
                                    $(bu_selected).addClass("bu-selected");
                                    vue_details_tab_edit.focused_active_service =  vue_details_tab_edit.service_id;
                                    vue_details_tab_edit.db_shift = data.db_shift;

                                }, 1000);

                            }
                            vue_details_tab_edit.time_slot = data.time_slot;
                            vue_details_tab_edit.beauticians = data.beauticians;
                            $('input[name=time]').attr('checked', false);
                            if (!data.beautician_flag) {
                                $('input[name=beautician]').attr('checked', false);
                            }

                        });
                        this.getbookingData();
                        //alert(vue_details_tab_edit.beautician_id);
                        // vue_details_tab_edit.beauticians = vue_details_tab_edit.beauticians;
                        //bu_selected = "#bu"+ vue_details_tab_edit.beautician_id;


                        $(bu_selected_htm_id).addClass("bu-selected");
                        setTimeout(function() {
                            $(bu_selected_htm_id).addClass("bu-selected");
                            vue_details_tab_edit.focused_active_service = vue_details_tab_edit.service_id;
                            vue_details_tab_edit.db_shift = data.db_shift;

                        }, 1000);
                    }

                },

                getbookingData: function(e) {

                    //vue_details_tab_edit.new_duration = [];
                    vue_details_tab_edit.shift_id = $("#shift_data :selected").val();
                    vue_details_tab_edit.duration_minutes = $("#duration_minutes :selected").val();
                    $.ajax({
                        method: "POST",
                        url: "{{ route('booking.change') }}",
                        data: {
                            service: vue_details_tab_edit.service_id,
                            is_pos: 1,
                            service_type: 3,
                            shift_id: vue_details_tab_edit.shift_id,
                            duration: vue_details_tab_edit.duration_minutes,
                            price: vue_details_tab_edit.price,
                            date: $('#booking_date').val(),
                            beautician: vue_details_tab_edit.beautician
                        },

                    }).done(function(data) {

                        if (data.all_db_shifts.length > 0) {
                            vue_details_tab_edit.all_shifts = data.all_db_shifts;
                        }
                        this.db_shift = data.db_shift;

                        if (data.new_duration.length > 0) {
                            vue_details_tab_edit.new_duration = data.new_duration;
                        }
                        if (data.beauticians.length > 0) {
                            bu_selcted_data = data.beauticians[0];
                        }
                        vue_details_tab_edit.time_slot = [];
                        vue_details_tab_edit.beauticians = data.beauticians;
                        $('input[name=time]').attr('checked', false);
                        if (!data.beautician_flag) {
                            $('input[name=beautician]').attr('checked', false);
                        }
                        vue_details_tab_edit.getTimeSlot('text', 2);
                    });
                },


                amountTakenChanged: function(el, target_element,type) {
                    vue_details_tab_edit.amount_taken_flag = 1;
                    // vue_details_tab_edit.amount_taken =  $(el).val();
                    // alert( vue_details_tab_edit.amount_taken);
                    // alert( vue_details_tab_edit.grand_total);
                    // vue_details_tab_edit.amount_taken = grandTotal;
                    var total = "given....";
                    console.log('vue_details_tab_edit.amount_taken');
                    console.log(vue_details_tab_edit.amount_taken);
                    console.log('vue_details_tab_edit.grand');
                    console.log(vue_details_tab_edit.grand_total);
                    $("#add_payment_btn").attr("disabled", false);
                    g_total = vue_details_tab_edit.grand_total - this.booking_info.payment_total;
                    if (parseFloat(vue_details_tab_edit.amount_taken) > parseFloat(g_total)) {
                        if ($("#payment_mode :selected").val() == "card") {
                            $("#add_payment_btn").attr("disabled", true);
                        }

                        total = parseFloat(vue_details_tab_edit.grand_total) - parseFloat(
                            vue_details_tab_edit.amount_taken);

                            total = parseFloat(total) - parseFloat(this.booking_info.payment_total);
                        total = Math.abs(total);
                    } else {

                        total = (total);
                    }

                    $('#amount_given').val(total);
                    total = vue_details_tab_edit.grand_total - this.booking_info.payment_total;
                    // alert(total);

                    //  if (vue_details_tab_edit.amount_taken <= total){

                    //    $("#add_payment_btn").attr("disabled", true);

                    //  }
                    // vue_details_tab_edit.grand_total = vue_details_tab_edit.grand_total;
                    //alert(total);

                    // vue_details_tab_edit.amount_taken = total;
                    // alert(vue_details_tab_edit.vue_details_tab_edit.amount_taken);
                    //$(el).val(vue_details_tab_edit.vue_details_tab_edit.amount_taken);

                    // var $target = $(target_element);
                    // var return_amount = this.booking_info.booking_total - this.booking_info.payment_total - this.v_amount($(el).val());

                    // if (return_amount < 0) {
                    //   $target.val(-1 * return_amount.toFixed(2));
                    // } else {
                    //   $target.val("");
                    // }
                },







                getTimeSlot: function(e, flag) {

                    vue_details_tab_edit.shift_id = $("#shift_data :selected").val();
                    vue_details_tab_edit.duration_minutes = $("#duration_minutes :selected").val();
                    if (flag == 1) {
                        $("div").removeClass("bu-selected");
                        vue_details_tab_edit.beautician_id = e.beautician_id;
                    }
                    bu_selected = "#bu" + e.beautician_id;
                    vue_details_tab_edit.temp_beatician_id = e.beautician_id;
                    setTimeout(function() {
                        $(bu_selected).addClass("bu-selected")
                    }, 1000);
                    vue_details_tab_edit.beautician_id = e.beautician_id;

                    $.ajax({
                        method: "POST",
                        url: "{{ route('booking.change') }}",
                        data: {
                            service: vue_details_tab_edit.service_id,
                            is_pos: 1,
                            service_type: 3,
                            shift_id: vue_details_tab_edit.shift_id,
                            duration: vue_details_tab_edit.duration_minutes,
                            price: vue_details_tab_edit.price,
                            beautician: vue_details_tab_edit.beautician_id,
                            date: vue_details_tab_edit.focused_active_booking_date,
                        },

                    }).done(function(data) {

                        $(vue_details_tab_edit.changing_price_var).text(data.price);
                        if (data.all_db_shifts.length > 0) {
                            vue_details_tab_edit.all_shifts = data.all_db_shifts;
                        }
                        this.db_shift = data.db_shift;

                        if (data.new_duration.length > 0) {
                            vue_details_tab_edit.new_duration = data.new_duration;
                        }
                        if (data.beauticians.length > 0) {
                            bu_selcted_data = data.beauticians[0];
                            vue_details_tab_edit.beauticians = data.beauticians[0].beautician_id;

                        }

                        vue_details_tab_edit.time_slot = data.time_slot;

                        vue_details_tab_edit.beauticians = data.beauticians;
                        vue_details_tab_edit.beautician_id = data.beauticians[0].beautician_id;

                        $('input[name=time]').attr('checked', false);
                        if (!data.beautician_flag) {
                            $('input[name=beautician]').attr('checked', false);
                        }

                    });
                },
                // -==========================new addecd section

                isPaymentExceeded: function() {
                    return false;
                }

            },
            computed: {
                popup_client_info: function() {
                    var modified = {};
                    for (var i in this.booking_info.real_client) {
                        modified[i] = (this.popup_client_type === "new") ? null : this.booking_info
                            .real_client[i];
                    }
                    return modified;
                },
                suggestedTakenAmount: function() {
                    var bamt = this.v_amount(this.booking_info.booking_total);
                    var paid = this.v_amount(this.booking_info.payment_total);
                    return (bamt > 0 && bamt > paid) ? this.v_fixed_amount(bamt - paid) : null;
                },
                paymentTotalClass: function() {
                    return 'bg-succes';
                    //{
                    // 'bg-warning': this.v_amount(this.booking_info.payment_total) < this.v_amount(this.booking_info.booking_total),
                    // 'text-dark': this.v_amount(this.booking_info.payment_total) < this.v_amount(this.booking_info.booking_total),
                    // 'bg-success': this.v_amount(this.booking_info.payment_total) == this.v_amount(this.booking_info.booking_total),
                    // 'bg-danger': this.v_amount(this.booking_info.payment_total) > this.v_amount(this.booking_info.booking_total)
                    //}
                }
            }
        });
    });

    var bookingServiceSaved = function($form, data) {

        if (data.booking_info) {
            vueMergeBookingInfo(data);

        } else {
            alert("Failed to save service. Try again later.")
        }
    };

    var bookingServiceDeleted = function($form, data) {
        if (data.booking_info) {
            vueMergeBookingInfo(data);
        } else {
            alert("Failed to delete service. Try again later.")
        }
    };

    var popupSlotUpdated = function($form, data) {
        if (data.booking_info) {
            vueMergeBookingInfo(data);
        } else {
            alert("Failed to update service. Try again later.")
        }
    };

    var paymentItemDeleted = function($form, data) {
        if (data.booking_info) {
            vueMergeBookingInfo(data);
        } else {
            alert("Failed to remove payment entry. Try again later.")
        }
    };

    var vueMergeBookingInfo = function(data) {

        var info = data.booking_info;
        //vue_details_tab_edit.getbookingData(info);
        vue_details_tab_edit.booking_info = info;

        if (typeof vue_details_tab_info !== 'undefined') {
            vue_details_tab_info.booking_info = info;

        }

        //location.reload();
    };


    var grand_total_global = function() {
        var grandTotal = 0;
        // console.log(this.booking_info.booking_only_services);
        console.log('legth of array');
        console.log(all_booking_info.booking_only_services.length);
        for (var i in all_booking_info.booking_only_services) {
            var service_id = all_booking_info.booking_only_services[i].booking_service_id;
            console.log('service_id');
            console.log(service_id);
            var html_id = "#total_" + service_id;
            var lineTotal = $(html_id).text();
            console.log('lineTotal');
            console.log(lineTotal);
            grandTotal = parseFloat(lineTotal) + grandTotal;
            console.log('grandTotal');
            console.log(grandTotal);

        }
        console.log('grandTotal');
        console.log(grandTotal);
        vue_details_tab_edit.amount_taken = grandTotal;
        vue_details_tab_edit.grand_total = grandTotal;
        vue_details_tab_edit.grand_total_1 = grandTotal;
    }





    var flatpickr_dob = null;

    function refreshFlatPicker() {

        if (flatpickr_dob) {
            flatpickr_dob.setDate(vue_details_tab_edit.popup_client_info.date_of_birth, true);
        } else {
            flatpickr_dob = flatpickr(".date_of_birth_ig", {
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                wrap: true
            });
        }
    }

    jQuery(function($) {
        $("body").on("click", ".booking-line-remove", function(e) {
            e.preventDefault();
            var $this = $(this);
            bootConfirm(null, "Confirm delete this entry?", function(result) {
                if (result) {
                    ajaxSubmitForm($($this.closest("form")[0]))
                }
            });
        });

        $("body").on("click", ".payment-remove", function(e) {
            e.preventDefault();
            var $this = $(this);
            bootConfirm(null, "Confirm delete this payment?", function(result) {
                if (result) {
                    ajaxSubmitForm($($this.closest("form")[0]))
                }
            });
        });

        $("#client-quick-edit-modal").on("show.bs.modal", function(event) {
            vue_details_tab_edit.popup_client_type = $(event.relatedTarget).data("popup-type");

            $("#gender").val(vue_details_tab_edit.popup_client_info.gender).trigger("change");

            // remove previous form errors
            var $form = $("#client-quick-edit-modal .voco-ajax-form");
            $form.find(".invalid-feedback").remove();
            $form.find(".form-control").removeClass("is-invalid");
            if (vue_details_tab_edit.popup_client_info.mobile > 0 ) {
               setTimeout(() => {
                    var digits = String(vue_details_tab_edit.popup_client_info.mobile).replace(/\D/g, '');
                    if (digits.length > 10 && digits.startsWith('91')) {
                        digits = digits.substring(digits.length - 10);
                    }
                    var mob = digits.substring(0, 10);
-                $("#mobile").val(mob)
                }, 1000);
            }
        });

        $("body").on("click", "#client-quick-edit-modal .btn-voco-save", function() {
            ajaxSubmitForm($("#client-quick-edit-modal .voco-ajax-form"), function($form, data) {
                $('#client_id').select2("destroy");
                $("#client-quick-edit-modal").modal("hide");
            });
        });

        $("body").on("click", ".cancel-booking", function(e) {
            e.preventDefault();
            var $this = $(this);
            var dialog = bootbox.confirm({
                title: "Cancel Booking",
                message: "Are you sure to cancel this booking?",
                buttons: {
                    cancel: {
                        label: '<i class="fa fa-times"></i> No'
                    },
                    confirm: {
                        label: '<i class="fa fa-check"></i> Cancel Booking',
                        className: 'btn-danger'
                    }
                },
                callback: function(result) {
                    if (result) {
                        ajaxSubmitForm($($this.closest("form")[0]))
                    }
                }
            });

            dialog.on('shown.bs.modal', function() {
                dialog.find(".btn-default:first").focus();
            });
        });

        $("body").on("change", "#add_service_id", function() {

            var exry_date = "";
            var todayDate = "";
            var $this = $(this);
            exry_date = new Date($(this).find(':selected').data('expiry-date'));
            todayDate = new Date();
            if (exry_date < todayDate) {
                alert("item is expired...!!");
                return;
            }
            if ($(this).find(':selected').data('service-id')) {
                $('#line_item_type').val('service');
                $('#d_service_id').val($this.val());
            } else if ($(this).find(':selected').data('price-id')) {
                $('#line_item_type').val('item');
                $('#d_price_id').val($this.val());
                $('#d_bath_no').val($(this).find(':selected').data('batch-no'));

            }
            ajaxSubmitForm($($this.closest("form")[0]));


            //location.reload();

        });

        // @TODO - merge selectors
        $("body").on("change", "#booking_date", function() {
            vue_details_tab_edit.getTimeSlot('text', 2);
            //vue_details_tab_edit.getbookingData(1);
            //ajaxSubmitForm($($(this).closest("form")[0]))

        });

        $("body").on("change", "#add_price_id", function() {
            ajaxSubmitForm($($(this).closest("form")[0]))
        });

        $("body").on("blur", "#applied_discount_percent", function() {
            ajaxSubmitForm($($(this).closest("form")[0]))
            //this.booking_info;
            //location.reload();
        });

        $("body").on("change", "#client_id", function() {
            ajaxSubmitForm($($(this).closest("form")[0]))
        });

        $("body").on("click", ".view-slots", function(e) {
            e.preventDefault();
            var $trigger = $(this)
            $trigger.closest("table").find(".service-active").removeClass("service-active anim-active");
            $trigger.closest("tr").addClass("service-active");
            vue_details_tab_edit.focused_active_service = $(this).data("service-id");
            vue_details_tab_edit.focused_active_booking_date = $(this).data("booking-date");
        });

        $("#beautician-slot-modal").on("hide.bs.modal", function(e) {
            $(".service-active").addClass("anim-active")
        })
    });


    jQuery(function() {
        $("body").on("click", ".notify_whatsapp", function(e) {
            e.preventDefault();
            $.getJSON($(this).data("url"), {
                booking_id: $(this).data("booking-id")
            });
        });
    });


    //===============================================================================================

</script>
