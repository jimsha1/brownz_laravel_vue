<div id="vue-list-tab-upcoming" class="m-n1">
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
                        <th>{{ __('Check-In') }}</th>
                        <th>{{ __('Manage') }}</th>
                        <!-- <th>{{ __('Slot End') }}</th> -->
                        <!-- <th>{{ __('Service Start Time') }}</th> -->
                        <!-- <th>{{ __('Service End Time') }}</th> -->
                    </tr>
                </thead>
                <tbody>
                    <tr v-if=" item.status !== 'start'&& item.status !== 'completed' && !item.canceled_by"
                        v-for="(item, index) in paginator.db_rows">

                        <td>@{{ index + paginator.sequence_start }}</td>
                        <td :style="{ color: item.booking_code ? '#52A8D9' : 'red' }">@{{ item.booking_code ? item.booking_code : 'Draft' }}</td>
                        <td>@{{ item.client_name }}(@{{item.client_mobile}})</td>
                        <td>
                            <span v-if="item.beautician_id > 0">@{{ item.beautician_name }}</span>
                            <span v-else class="badge badge-warning text-wrap"
                                title="{{ __('Assign a beautician before starting the service.') }}">
                                {{ __('No beautician') }}
                            </span>
                        </td>
                        <td>@{{ item.service_name_locale }}</td>
                        <td>@{{ (item.appointment_date) | vc_date_expanded }}</td>
                        <td>@{{ convertTimeTo12hr(item.slot_start) }}</td>
                        <td style="display: flex;align-items: center;">
                            <form v-if="!item.client_checkin_at && item.appointment_date == todays_date && item.beautician_id > 0"
                                action="{{ route('admin.appointment.client.check.in') }}" class="voco-ajax-form"
                                method="post" style="display:inline-block">
                                @csrf
                                <input type="hidden" name="booking_id" v-model="item.booking_id">
                                <input type="hidden" name="type" v-model="check_in">
                                <input type="hidden" name="booking_service_id" v-model="item.booking_service_id">
                                <button type="button" class="btn btn-link btn-sm py-1 btn-check-in-client"
                                    title="Check-in Client" data-toggle="tooltip">
                                    <i class="fa fa-sign-in" style="color: #E6CD7F;"></i>
                                </button>
                            </form>

                            <form v-if="item.client_checkin_at && !item.service_started_at && item.appointment_date == todays_date && item.beautician_id > 0"
                                action="{{ route('admin.appointment.client.undo.check.in') }}" class="voco-ajax-form"
                                method="post" style="display:inline-block">
                                @csrf
                                <input type="hidden" name="type" v-model="undo_check_in">
                                <input type="hidden" name="booking_id" v-model="item.booking_id">
                                <input type="hidden" name="booking_service_id" v-model="item.booking_service_id">
                                <button type="button" class="btn btn-danger btn-sm py-1 btn-undo-check-in-client"
                                    title="Undo check-in" data-toggle="tooltip">
                                    <i class="fa fa-times" style="color: #E6CD7F;"></i>
                                </button>
                            </form>

                            <span v-if="item.client_checkin_at"
                                class="badge badge-success">{{ __('Checked-in') }}</span>
                        </td>
                        <td>
                            <form v-if="!item.service_started_at && item.appointment_date == todays_date && item.beautician_id > 0"
                                action="{{ route('my.account.booking.service.start') }}" class="voco-ajax-form"
                                method="post" style="display:inline-block">
                                @csrf
                                <input type="hidden" name="booking_id" v-model="item.booking_id">
                                <input type="hidden" name="booking_service_id" v-model="item.booking_service_id">
                                <button type="submit" class="btn btn-primary btn-sm py-1 btn_dark-x">
                                    <i class="fa fa-clock-o"></i> Start
                                </button>
                            </form>

                            <div v-if="!item.service_started_at && item.appointment_date == todays_date && !item.beautician_id"
                                class="assign-beautician-hint">
                                <small class="text-warning d-block mb-1">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    {{ __('Assign a beautician first.') }}
                                </small>
                                <form class="view-slot-form d-inline-block"
                                    action="{{ route('report.appointment.booking.details') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="booking_service_id" v-bind:value="item.booking_service_id">
                                    <input type="hidden" name="beautician_id" v-bind:value="item.beautician_id || 0">
                                    <input type="hidden" name="selected_beautician_id" v-bind:value="item.beautician_id || 0">
                                    <input type="hidden" name="shift_id" v-bind:value="item.shift_id || 0">
                                    <button type="submit" class="btn btn-outline-warning btn-sm py-1"
                                        title="{{ __('Assign beautician and time slot') }}">
                                        <i class="fa fa-user-plus"></i> {{ __('Assign') }}
                                    </button>
                                </form>
                            </div>

                            <form v-if="item.service_started_at && item.beautician_id > 0"
                                action="{{ route('my.account.booking.service.stop') }}" class="voco-ajax-form"
                                method="post" style="display:inline-block">
                                @csrf
                                <input type="hidden" name="booking_id" v-model="item.booking_id">
                                <input type="hidden" name="booking_service_id" v-model="item.booking_service_id">
                                <button type="button" class="btn btn-success btn-sm py-1" data-toggle="modal"
                                    v-bind:data-target="'#finishModal-'+ item.booking_service_id">
                                    <i class="fa fa-check-circle"></i> Finish
                                </button>
                            </form>
                        </td>
                        <!-- <td>@{{ (item.duration_minutes) | vc_duration_short }}</td> -->
                        <!-- <td>@{{ item.slot_end }}</td> -->
                        <!-- <td>@{{ item.service_started_at }}</td> -->
                        <!-- <td>@{{ item.service_finished_at }}</td> -->
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="tab-pagination" v-html="paginator.links">
        </div>

        @include('appointment.booking-service-list.ajax-tabs.partials.beautician-slot-modal')

    </template>
    <template v-else>
        <div class="alert alert-danger">
            {{ __('No results found.') }}
        </div>
    </template>
</div>
<style>
    .assign-beautician-hint {
        max-width: 150px;
        line-height: 1.3;
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4/dist/flatpickr.min.css">
<script src="{{ asset('web\calender-piker\calendar.min.js') }}"></script>
<script src="https://unpkg.com/codeflask/build/codeflask.min.js"></script>
<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
    var vue_list_tab_upcoming = null;
    var date = '';
    jQuery(function($) {
        vue_list_tab_upcoming = new Vue({
            el: "#vue-list-tab-upcoming",
            methods: {
                selectBeautician: function(bt) {
                    vue_list_tab_upcoming.selectedBt = bt;
                    vue_list_tab_upcoming.this_beautician_id = bt.beautician_id;
                    vue_list_tab_upcoming.this_shift_id = bt.shift_id;
                    console.log(vue_list_tab_upcoming.this_shift_id);
                    console.log('===============shift_id=================');
                    console.log(bt);
                    vue_list_tab_upcoming.selcted_time_slot = null;
                    // console.log(vue_list_tab_upcoming.this_shift_id);
                    var selected_date_ajax = $('#timeSlote').find("input[name=selected_date]")
                .val();
                    if (selected_date_ajax == undefined) {
                        var d = new Date();
                        selected_date_ajax = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d
                            .getDate();
                    }
                    vue_list_tab_upcoming.getTimeSlots(selected_date_ajax);
                    vue_list_tab_upcoming.selected_appointment_date = selected_date_ajax;
                    console.log(vue_list_tab_upcoming.selectedBt.name_locale);
                },
                updateTimeSlot: function(time) {
                    vue_list_tab_upcoming.selected_time = time;
                    console.log(vue_list_tab_upcoming);
                    var selected_date_ajax = $('#timeSlote').find("input[name=selected_date]")
                        .val();
                    if (selected_date_ajax == undefined) {
                        var d = new Date();
                        selected_date_ajax = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d
                            .getDate();
                    }
                    vue_list_tab_upcoming.selected_appointment_date = selected_date_ajax;
                    // salon.booking.item.update
                    //=============================================
                    /*$.ajax({
                        method: "POST",
                        url: "{{ route('salon.booking.item.update') }}",
                        data: {
                            service_id: vue_list_tab_upcoming.update_service_id,
                            beautician_id: vue_list_tab_upcoming.this_beautician_id,
                            booking_id: vue_list_tab_upcoming.update_booking_id,
                            slot_start: time,
                            appointment_date: selected_date_ajax,
                            booking_service_id: vue_list_tab_upcoming
                                .this_booking_service_id
                        }
                    }).done(function(data) {
                        if(data.status == 'success'){
                            location.reload();
                        }
                    }).fail(function(data) {
                        alert('time slot failed..!!');
                    });

                    //=====================================================*/
                },
                // salon.booking.item.update
                //=============================================
                bookingUpdate: function() {
                    console.log(vue_list_tab_upcoming.selected_time);
                    $.ajax({
                        method: "POST",
                        url: "{{ route('salon.booking.item.update') }}",
                        data: {
                            service_id: vue_list_tab_upcoming.update_service_id,
                            beautician_id: vue_list_tab_upcoming.this_beautician_id,
                            booking_id: vue_list_tab_upcoming.update_booking_id,
                            slot_start: vue_list_tab_upcoming.selected_time,
                            appointment_date: vue_list_tab_upcoming
                                .selected_appointment_date,
                            booking_service_id: vue_list_tab_upcoming
                                .this_booking_service_id
                        }
                    }).done(function(data) {
                        if (data.status == 'success') {
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
                searchBeautician: function() {
                    console.log("On Search");
                },
                getTimeSlots: function(selectedDate) {
                    $('#timeSlote').find("input[name=selected_date]")
                        .val(selectedDate);
                    $.ajax({
                        method: "POST",
                        url: "{{ route('booking.change') }}",
                        data: {
                            service: vue_list_tab_upcoming.this_booking_service_id,
                            is_reshedule: 1,
                            is_pos: 1,
                            service_type: 3,
                            shift_id: vue_list_tab_upcoming.this_shift_id,
                            duration: vue_list_tab_upcoming.duration_minutes,
                            price: vue_list_tab_upcoming.price,
                            date: selectedDate,
                            beautician: vue_list_tab_upcoming.this_beautician_id,
                        },

                    }).done(function(data) {
                        console.log("HASH 123");
                        console.log(data);

                        // if (data.time_slot.length > 0) {
                        console.log(data.time_slot);
                        vue_list_tab_upcoming.time_slot = data.time_slot;
                        vue_list_tab_upcoming.alreadyBooked = data.alreadyBooked;
                        console.log(data.time_slot);
                        // }
                        // this.db_shift = data.db_shift;

                        // if (data.new_duration.length > 0 && type == 'beatician')  {
                        //     vue_details_tab_edit.new_duration = data.new_duration;
                        // }
                        // if (data.beauticians.length > 0) {
                        //     bu_selcted_data = data.beauticians[0];
                        // }
                        // vue_details_tab_edit.time_slot = data.time_slot;
                        // vue_details_tab_edit.beauticians = data.beauticians;
                        // console.log('====================================');
                        // console.log(vue_details_tab_edit.beauticians);
                        // console.log(vue_details_tab_edit.time_slot);
                        // console.log('====================================');
                        // $('input[name=time]').attr('checked', false);
                        // if (!data.beautician_flag) {
                        //     $('input[name=beautician]').attr('checked', false);
                        // }
                        //vue_details_tab_edit.getTimeSlot('text', 2);
                    });
                },
            },
            data: <?php echo json_encode(
                array_merge($db_results['vue_data']['vue_list_tab_upcoming'], [
                    'time_slot' => [],
                    'update_booking_id' => '',
                    'update_service_id' => '',
                    'selected_appointment_date' => null,
                    'selected_time' => null,
                    'selectedBt' => null,
                    'check_in' => 'check_in',
                    'undo_check_in' => 'undo_check_in',
                ]),
            ); ?>,
            filters: {
                time_hm: function(value) {
                    return value ? value.substring(0, 5) : '';
                }
            },
        });

        $("body").on("click", ".btn-check-in-client", function(e) {

            e.preventDefault();
            var $this = $(this);
            bootConfirm(null, "Confirm client check-in?", function(result) {
                if (result) {
                    ajaxSubmitForm($($this.closest("form")[0]))
                    // window.location.reload();
                }
            });
        });


        $("body").on("click", ".btn-undo-check-in-client", function(e) {


            e.preventDefault();
            var $this = $(this);
            bootConfirm(null, "Confirm client undo-check-in?", function(result) {

                if (result) {
                    ajaxSubmitForm($($this.closest("form")[0]))
                    window.location.reload();
                }
            });
        });


        $("body").on("change", "#booking_date", function() {
            var $this = $(this);
            ajaxSubmitForm($($this.closest("form")[0]))
        });


        function test() {
            alert("Test");
        }

        $("body").on("submit", ".view-slot-form", function(e) {

            e.preventDefault();

            var $this = $(this);

            vue_list_tab_upcoming.selcted_time_slot = $this.find('input[name=selected_time_slot]')
        .val();
            vue_list_tab_upcoming.this_beautician_id = $this.find('input[name=beautician_id]').val();
            vue_list_tab_upcoming.this_shift_id = $this.find('input[name=shift_id]').val();
            vue_list_tab_upcoming.this_booking_service_id = $this.find('input[name=booking_service_id]')
                .val();
            // vue_list_tab_upcoming.modal_load = true;
            vue_list_tab_upcoming.beauticians = [];
            vue_list_tab_upcoming.selectedBt = null;
            vue_list_tab_upcoming.selected_time = null;
            $('#beautician-slot-modal').modal('show');

            $.ajax({
                    method: "POST",
                    url: $(this).attr('action'),
                    data: $(this).serialize()
                })
                .done(function(data) {

                    //  vue_list_tab_upcoming.this_booking_service_id = this_booking_service_id;
                    vue_list_tab_upcoming.sel_beautician_id = vue_list_tab_upcoming
                        .this_beautician_id;

                    vue_list_tab_upcoming.update_service_id = data.service_id;
                    vue_list_tab_upcoming.update_booking_id = data.booking_info.booking_id;


                    // console.log(data.service_id);
                    vue_list_tab_upcoming.booking_info = data.booking_info;
                    vue_list_tab_upcoming.focused_active_service = data.service_id;
                    vue_list_tab_upcoming.focused_active_booking_date = data.booking_date;
                    vue_list_tab_upcoming.beauticians = [];

                    for (var i in data.booking_info.booking_only_services) {
                        var bkservice = data.booking_info.booking_only_services[i];
                        if (bkservice.service_id == data.service_id) {
                            // return bkservice.service.beauticians;
                            vue_list_tab_upcoming.beauticians = bkservice.service.beauticians;
                            vue_list_tab_upcoming.selectedBt = vue_list_tab_upcoming.beauticians
                                .find(item => item.beautician_id == vue_list_tab_upcoming
                                    .this_beautician_id);
                        }

                        vue_list_tab_upcoming.$forceUpdate()
                        // vue_list_tab_upcoming.modal_load = false;

                    }

                    var d = new Date();
                    selected_date_ajax = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d
                        .getDate();
                    vue_list_tab_upcoming.getTimeSlots(selected_date_ajax);
                }).fail(function(data) {});
        });

        $("#beautician-slot-modal").on("hidden.bs.modal", function(e) {

            $.ajax({
                    method: "GET",
                    url: "{{ route('report.appointment.list', 'upcoming') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "pgajax": 'y'
                    }
                })
                .done(function(data) {
                    vue_list_tab_upcoming.paginator = data.vue_data.vue_list_tab_upcoming.paginator;
                    vue_list_tab_upcoming.$forceUpdate();

                }).fail(function(data) {

                });

        })

    });


    var popupSlotUpdated = function($form, data) {
        //this is updating time slot section
        if (data.booking_info) {
            vue_list_tab_upcoming.booking_info = data.booking_info;

            for (var i in data.booking_info.booking_only_services) {
                var bkservice = data.booking_info.booking_only_services[i];
                if (bkservice.service_id == vue_list_tab_upcoming.focused_active_service) {
                    // return bkservice.service.beauticians;
                    vue_list_tab_upcoming.beauticians = bkservice.service.beauticians;

                }
            }
            vue_list_tab_upcoming.$forceUpdate();

        } else {
            alert("Failed to update service. Try again later.")
        }
    };

    $(document).ready(function() {

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

   $('#calendar-wrapper').updateCalendarOptions({
   date: date
 });
  date = formatDate(date);
  $('#timeSlote').find("input[name=selected_date]").remove();
  $('<input>').attr({
    type: 'hidden',
    id: 'selected_date',
    name: 'selected_date',
    value: date
}).appendTo('#timeSlote');

$('#selectedDateSlot').find("input[name=selected_date]").remove();
  $('<input>').attr({
    type: 'hidden',
    id: 'selected_date',
    name: 'selected_date',
    value: date,
}).appendTo('#selectedDateSlot');

vue_list_tab_upcoming.getTimeSlots(date);
}


var defaultConfig = {
 weekDayLength: 1,
 date:   new Date(),
 onClickDate: selectDate,
 showYearDropdown: true,
 startOnMonday: true,
};

var calendar = $('#calendar-wrapper').calendar(defaultConfig);
console.log(calendar.getSelectedDate());
`;

        eval(config);

    });
</script>
