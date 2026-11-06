<div id="vue-list-tab-expired" class="m-n1">
    <template v-if="paginator.db_rows.length">
        <div class="table-responsive">
            <table class="table brownz-table-list-v1">
                <thead>
                    <tr>
                        <th>{{ __('SL#') }}</th>
                        <th>{{ __('Booking') }}</th>
                        <th>{{ __('Client') }}</th>
                        <th>{{ __('Beautician') }}</th>
                        <th>{{ __('Service') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Time') }}</th>
                        <th>{{ __('Action') }}</th>
                        <!-- <th>{{ __('Duration') }}</th> -->
                        <!-- <th>{{ __('Slot End') }}</th> -->
                        <!-- <th>{{ __('Service Start Time') }}</th> -->
                        <!-- <th>{{ __('Service End Time') }}</th> -->
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in paginator.db_rows">
                        <td>@{{ index + paginator . sequence_start }}</td>
                        <td>@{{ item.booking_code ? item.booking_code : 'Draft' }}</td>
                        <td>@{{ item . client_name }}</td>
                        <td>@{{ item . beautician_name }}</td>
                        <td>@{{ item . service_name_locale }}</td>
                        <td>@{{ (item . appointment_date) | vc_date_expanded }}</td>
                        <td>@{{ convertTimeTo12hr(item.slot_start) }}</td>
                        <td>
                            <form class=" view-slot-form-expired"
                                action="{{ route('report.appointment.booking.details') }}" method="POST" id="">
                                <input type="hidden" name="booking_service_id" v-bind:value="item.booking_service_id">
                                <input type="hidden" name="selected_beautician_id" v-bind:value="item.beautician_id">
                                <button type="submit" class="badge badge-danger"
                                    v-bind:data-service-id="item.service_id"
                                    v-bind:data-booking-date="item.appointment_date" data-toggle=""
                                    data-target="#beautician-slot-modal-expired" data-toggle="tooltip"
                                    title="{{ __('Change beautician and time slot') }}">
                                    <i class="fa fa-clock-o"></i>

                                </button>
                            </form>
                        </td>
                        <!-- <td>@{{ (item . duration_minutes) | vc_duration_short }}</td> -->
                        <!-- <td>@{{ item . slot_end }}</td> -->
                        <!-- <td>@{{ item . service_started_at }}</td> -->
                        <!-- <td>@{{ item . service_finished_at }}</td> -->
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="tab-pagination" v-html="paginator.links">
        </div>

    </template>
    <template v-else>
        <div class="alert alert-danger">
            {{ __('No results found.') }}
        </div>
    </template>

    @include('appointment.booking-service-list.ajax-tabs.partials.beautician-slot-modal-expired')
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4/dist/flatpickr.min.css">
<script src="{{ asset('web\calender-piker\calendar.min.js') }}"></script>
<script src="https://unpkg.com/codeflask/build/codeflask.min.js"></script>
<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
    var selectedDate = "";
    var vue_list_tab_expired = null;
    jQuery(function($) {
        vue_list_tab_expired = new Vue({
            el: "#vue-list-tab-expired",
            data: <?php echo json_encode(
    array_merge($db_results['vue_data']['vue_list_tab_expired'], [
        'selected_beautician_id' => null,
        'time_slot' => [],
        'update_booking_id' => '',
        'update_service_id' => '',
        'selected_appointment_date' => null,
        'selected_time' => null,
        'selectedBt' => null,
    ]),
); ?>,
            methods: {
                convertTimeTo12hr: function(time) {
                    // Check correct time format and split into components
                    time = time.toString().match(/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [
                        time
                    ];

                    if (time.length > 1) { // If time format correct
                        time = time.slice(1); // Remove full string match value
                        time[5] = +time[0] < 12 ? ' AM' : ' PM'; // Set AM/PM
                        time[0] = +time[0] % 12 || 12; // Adjust hours
                    }
                    return time.join(''); // return adjusted time or original string
                },
                focusedServiceBeauticians: function() {
                    for (var i in this.booking_info.booking_only_services) {
                        var bkservice = this.booking_info.booking_only_services[i];
                        if (bkservice.service_id == this.focused_active_service) {
                            return bkservice.service.beauticians;
                        }
                    }
                    return [];
                },
                selectBeautician: function(bt) {
                    vue_list_tab_expired.selectedBt = bt;
                    vue_list_tab_expired.this_beautician_id = bt.beautician_id;
                    vue_list_tab_expired.this_shift_id = bt.shift_id;
                    console.log(vue_list_tab_expired.this_shift_id);
                    console.log('===============shift_id=================');
                    console.log(bt);
                    // console.log(vue_list_tab_expired.this_shift_id);
                    var selected_date_ajax = $('#timeSlote-1').find("input[name=selected_date]")
                        .val();
                    if (selected_date_ajax == undefined || selected_date_ajax == "") {
                        var d = new Date();
                        selected_date_ajax = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d
                            .getDate();
                    }
                    vue_list_tab_expired.getTimeSlots(selected_date_ajax);
                    vue_list_tab_expired.selected_appointment_date = selected_date_ajax;
                    console.log(vue_list_tab_expired.selectedBt.name_locale);
                },
                getTimeSlots: function(selectedDate) {
                    $('#timeSlote-1').find("input[name=selected_date]")
                        .val(selectedDate);
                    $.ajax({
                        method: "POST",
                        url: "{{ route('booking.change') }}",
                        data: {
                            service: vue_list_tab_expired.this_booking_service_id,
                            is_pos: 1,
                            service_type: 3,
                            shift_id: vue_list_tab_expired.this_shift_id,
                            duration: vue_list_tab_expired.duration_minutes,
                            price: vue_list_tab_expired.price,
                            date: selectedDate,
                            beautician: vue_list_tab_expired.this_beautician_id,
                        },

                    }).done(function(data) {
                        console.log('=============time slot===========');
                        console.log(data);
                        vue_list_tab_expired.time_slot = data.time_slot;

                    });
                },
                updateTimeSlot: function(time, btId) {

                    vue_list_tab_expired.selected_time = time;
                    var selected_date_ajax = $('#timeSlote-1').find("input[name=selected_date]").val();


                    if (selected_date_ajax == undefined || selected_date_ajax === "") {
                        var d = new Date();
                        selected_date_ajax = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d
                            .getDate();
                    }
                    vue_list_tab_expired.selected_appointment_date = selected_date_ajax;
                    // salon.booking.item.update
                    //=============================================
                    /*$.ajax({
                        method: "POST",
                        url: "{{ route('salon.booking.item.update') }}",
                        data: {
                            service_id: vue_list_tab_expired.update_service_id,
                            beautician_id: vue_list_tab_expired.this_beautician_id,
                            booking_id: vue_list_tab_expired.update_booking_id,
                            booking_service_id: vue_list_tab_expired
                                .this_booking_service_id,
                            appointment_date: selected_date_ajax,
                            slot_start: time
                        }
                    }).done(function(data) {
                   // location.reload();
                    }).fail(function(data) {
                        alert('time slot failed..!!');
                    });
                    //=====================================================*/
                },
                // salon.booking.item.update
                //=============================================
                bookingUpdate: function() {
                    console.log(vue_list_tab_expired.selected_time);
                    console.log(vue_list_tab_expired.selected_appointment_date);
                    $.ajax({
                        method: "POST",
                        url: "{{ route('salon.booking.item.update') }}",
                        data: {
                            service_id: vue_list_tab_expired.update_service_id,
                            beautician_id: vue_list_tab_expired.this_beautician_id,
                            booking_id: vue_list_tab_expired.update_booking_id,
                            slot_start: vue_list_tab_expired.selected_time,
                            appointment_date: vue_list_tab_expired.selected_appointment_date,
                            booking_service_id: vue_list_tab_expired
                                .this_booking_service_id
                        }
                    }).done(function(data) {
                        if(data.status == 'success'){
                            location.reload();
                        }
                    }).fail(function(data) {
                        alert('time slot failed..!!');
                    });
                },
                //=============================================
                convertTimeTo12hr: function(time) {
                    // Check correct time format and split into components
                    time = time.toString().match(/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [
                        time
                    ];

                    if (time.length > 1) { // If time format correct
                        time = time.slice(1); // Remove full string match value
                        time[5] = +time[0] < 12 ? ' AM' : ' PM'; // Set AM/PM
                        time[0] = +time[0] % 12 || 12; // Adjust hours
                    }
                    return time.join(''); // return adjusted time or original string
                },
            },
            filters: {
                time_hm: function(value) {
                    return value ? value.substring(0, 5) : '';
                }
            },
        });


        // var popupSlotUpdated = function($form, data){
        //   if(data.booking_info) {
        //     vue_list_tab_expired.booking_info = data.booking_info;

        //     for (var i in data.booking_info.booking_only_services) {
        //       var bkservice = data.booking_info.booking_only_services[i];
        //       if(bkservice.service_id == vue_list_tab_expired.focused_active_service) {
        //         // return bkservice.service.beauticians;
        //         vue_list_tab_expired.beauticians = bkservice.service.beauticians;

        //       }
        //     }
        //     vue_list_tab_expired.$forceUpdate();

        //   }else{
        //     alert("Failed to update service. Try again later.")
        //   }
        // };



        $("body").on("change", "#booking_date_expired", function() {
            var $this = $(this);
            ajaxSubmitForm($($this.closest("form")[0]))
        });

        // $("body").on("click", ".slot-submit-btn", function(){
        //   var $this = $(this);
        //   ajaxSubmitForm($($this.closest("form")[0]),popupSlotUpdated)
        // });

        $("body").on("submit", ".view-slot-form-expired", function(e) {
            e.preventDefault();
            var $this = $(this);

            vue_list_tab_expired.selected_beautician_id = $this.find(
                'input[name=selected_beautician_id]').val();
                vue_list_tab_expired.this_beautician_id =  vue_list_tab_expired.selected_beautician_id;
            var this_booking_service_id = $this.find('input[name=booking_service_id]').val();
            vue_list_tab_expired.this_booking_service_id = $this.find('input[name=booking_service_id]')
                .val();
            // vue_list_tab_expired.modal_load = true;
            vue_list_tab_expired.beauticians = [];
            vue_list_tab_expired.selectedBt = null;
            vue_list_tab_expired.selected_time = null;

            $('#beautician-slot-modal-expired').modal('show');

            $.ajax({
                    method: "POST",
                    url: $(this).attr('action'),
                    data: $(this).serialize()
                })
                .done(function(data) {


                    //  vue_list_tab_expired.this_booking_service_id = this_booking_service_id;
                    vue_list_tab_expired.sel_beautician_id = vue_list_tab_expired
                        .this_beautician_id;

                    vue_list_tab_expired.update_service_id = data.service_id;
                    vue_list_tab_expired.update_booking_id = data.booking_info.booking_id;


                    // console.log(data.service_id);
                    vue_list_tab_expired.booking_info = data.booking_info;
                    vue_list_tab_expired.focused_active_service = data.service_id;
                    vue_list_tab_expired.focused_active_booking_date = data.booking_date;
                    vue_list_tab_expired.beauticians = [];

                    for (var i in data.booking_info.booking_only_services) {
                        var bkservice = data.booking_info.booking_only_services[i];
                        if (bkservice.service_id == data.service_id) {
                            // return bkservice.service.beauticians;
                            vue_list_tab_expired.beauticians = bkservice.service.beauticians;
                            vue_list_tab_expired.selectedBt = vue_list_tab_expired.beauticians
                                .find(item => item.beautician_id == vue_list_tab_expired
                                    .this_beautician_id);
                        }

                        vue_list_tab_expired.$forceUpdate()
                        // vue_list_tab_expired.modal_load = false;

                    }
                    var d = new Date();
                    selected_date_ajax = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d
                        .getDate();
                    vue_list_tab_expired.getTimeSlots(selected_date_ajax);
                }).fail(function(data) {});
        });
        $("#beautician-slot-modal-expired").on("hidden.bs.modal", function(e) {
            $.ajax({
                    method: "GET",
                    url: "{{ route('report.appointment.list', 'expired') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "pgajax": 'y'
                    }
                })
                .done(function(data) {
                    vue_list_tab_expired.paginator = data.vue_data.vue_list_tab_expired.paginator;
                    vue_list_tab_expired.$forceUpdate();

                }).fail(function(data) {

                });

        })

    });


    var popupSlotUpdated = function($form, data) {
        if (data.booking_info) {
            vue_list_tab_expired.booking_info = data.booking_info;

            for (var i in data.booking_info.booking_only_services) {
                var bkservice = data.booking_info.booking_only_services[i];
                if (bkservice.service_id == vue_list_tab_expired.focused_active_service) {
                    // return bkservice.service.beauticians;
                    vue_list_tab_expired.beauticians = bkservice.service.beauticians;

                }
            }
            vue_list_tab_expired.$forceUpdate();

        } else {
            alert("Failed to update service. Try again later.")
        }
    };
    $(document).ready(function() {
        var d = new Date();
        selected_date_ajax = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d
        $('<input>').attr({
            type: 'hidden',
            id: 'selected_date',
            name: 'selected_date',
            value: selected_date_ajax
        }).appendTo('#timeSlote-1');

        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2)
                month = '0' + month;
            if (day.length < 2)
                day = '0' + day;

            return [year, month, day].join('-');
        }
        $("#serv_date_piker").datepicker();

        var config = `
function selectDate(date) {

   $('#calendar-wrapper-1').updateCalendarOptions({
   date: date
 });
  date = formatDate(date);
  $('#timeSlote-1').find("input[name=selected_date]").remove();
  $('<input>').attr({
    type: 'hidden',
    id: 'selected_date',
    name: 'selected_date',
    value: date
}).appendTo('#timeSlote-1');

$('#selectedDateSlot').find("input[name=selected_date]").remove();
  $('<input>').attr({
    type: 'hidden',
    id: 'selected_date',
    name: 'selected_date',
    value: date,
}).appendTo('#selectedDateSlot');

vue_list_tab_expired.getTimeSlots(date);
}


var defaultConfig = {
 weekDayLength: 1,
 date:   new Date(),
 onClickDate: selectDate,
 showYearDropdown: true,
 startOnMonday: true,
};

var calendar = $('#calendar-wrapper-1').calendar(defaultConfig);
console.log(calendar.getSelectedDate());
`;

        eval(config);

    });
</script>
