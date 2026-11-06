<div id="vue-list-tab-all" class="m-n1">
    <style>
        .check-in-status-badge {
            font-size: 11px;
            font-weight: 500;
            padding: 4px 8px;
        }

        .check-in-status-badge .fa {
            margin-right: 4px;
        }
    </style>
    <template v-if="paginator.db_rows.length">
        <div class="table-responsive">
            <table class="table brownz-table-list-v1">
                <thead>
                    <tr>
                        <th>{{ __('SL#') }}</th>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Client') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Booking Date') }}</th>
                        <th>{{ __('Booking Slot') }}</th>
                         <th>{{ __('Type') }}</th>
                        <th>{{ __('Check In') }}</th>

                        <th class="hidden-print no-sort">{{ __('Manage') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <input id="count_array" type="hidden" v-bind:value="paginator.db_rows.length">
                    <tr v-for="(item, index) in paginator.db_rows"
                        v-if="item.booking_code!='draft' && item.booking_type !='Cancel'">
                        <div>
                            <td>@{{ index + paginator.sequence_start }}</td>
                            {{-- <td>
                                <a v-bind:href="item.edit_url"
                                    :style="{
                                        color: item.is_booking == 1 ? 'green' : (item.booking_code ? '#52A8D9' : 'red')
                                    }">
                                    @{{ item.is_booking == 1 ? 'Adv' : (item.booking_code ? item.booking_code : 'Draft') }}
                                </a>

                            </td> --}}
                            <td>
                                <a v-bind:href="item.edit_url"
                                    :style="{
                                        color: item.is_booking == 1 ?
                                            'green' :
                                            (item.booking_code ? '#52A8D9' : 'red')
                                    }">
                                    @{{ item.is_booking == 1 ?
    'Adv' :
    (
        (item.booking_code ? item.booking_code : 'Draft') +
        (item.cons_code ? ` (${item.cons_code})` : '')
    ) }}
                                </a>
                            </td>

                            <td>@{{ item.client_name }}(@{{ item.client_mobile }})</td>
                            <td>@{{ item.booking_total }}</td>
                            <td v-if="item.booking_services">@{{ item.booking_services.appointment_date }}</td>
                            <td v-else>@{{ dt_format(item.created_at) }}</td>
                            <td v-if="item.booking_services">@{{ convertTimeTo12hr(item.booking_services.slot_start) }}</td>
                            <td v-else>N/A</td>
                            <td>@{{ item.booking_type }}</td>
                            <td>
                                <template v-if="isFinalizedBooking(item)">
                                    <span class="badge check-in-status-badge"
                                        :class="checkInFinalizedBadgeClass(item)"
                                        :title="checkInFinalizedTitle(item)">
                                        <i class="fa" :class="checkInFinalizedIcon(item)" aria-hidden="true"></i>
                                        @{{ checkInFinalizedLabel(item) }}
                                    </span>
                                </template>
                                <template v-else-if="canUndoCheckIn(item)">
                                    <input type="checkbox" checked
                                        title="{{ __('Click to undo check-in') }}"
                                        @click="undoCheckInCustomer(item.booking_id, $event)"
                                        style="margin-right: 10px;" />
                                </template>
                                <template v-else-if="item.check_availability == 1">
                                    <input type="checkbox" checked disabled style="margin-right: 10px;"
                                        title="{{ __('Checked in — service in progress') }}" />
                                </template>
                                <template v-else-if="canCheckIn(item)">
                                    <input type="checkbox" @click="checkInCustomer(item.booking_id, $event)"
                                        style="margin-right: 10px;" />
                                </template>
                                <template v-else>
                                    <span class="badge badge-secondary check-in-status-badge"
                                        title="{{ __('Check-in not available') }}">
                                        {{ __('Not applicable') }}
                                    </span>
                                </template>
                            </td>

                            <td class="hidden-print">

                                <a v-bind:href="item.edit_url">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                </a>
                                <button v-on:click="cancelOrder(item.booking_id)"
                                    style="padding: 0 ; padding-right:14px; margin-left: 10px; outline: none; background-color: transparent; border: none;">
                                    <i style="color: #D0342C" class="fa fa-trash-o"></i>
                                </button>

                            </td>
                        </div>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="tab-pagination next-page-per" v-html="paginator.links">
        </div>
    </template>
    <template v-else>
        <div class="alert alert-danger">
            {{ __('No results found.') }}
        </div>
    </template>
</div>
<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
    var vue_list_tab_all = null;
    var format_date_id = "foramt_date_";
    jQuery(function($) {
        vue_list_tab_all = new Vue({
            el: "#vue-list-tab-all",
            data: <?php echo json_encode($db_results['vue_data']['vue_list_tab_all']); ?>,
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
                dt_format: function(val) {
                    var localTime = moment(val).toDate();
                    localTime = moment(localTime).format('YYYY-MM-DD');
                    return localTime;
                    // console.log(localTime);
                },
                cancelOrder: function(booking_id) {

                    if (!booking_id) {
                        alert('Invalid booking ID.');
                        return;
                    }

                    // Find the booking item using booking_id
                    const booking = this.paginator.db_rows.find(item => item.booking_id ===
                        booking_id);
                    if (booking && booking.payment_status === 'completed') {
                        alert('Void all payments before cancelling this order.');
                        return;
                    }

                    if (confirm("Are you sure you want to delete this booking? Product stock will be restored.")) {
                        $.ajax({
                            url: "{{ route('salon.booking.cancel.order') }}",
                            type: 'POST',
                            data: {
                                booking_id: booking_id,
                                soft_delete: 1,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(result) {
                                if (result && result.js_notifications && result.js_notifications.warning) {
                                    alert(result.js_notifications.warning);
                                    return;
                                }
                                location.reload();
                            },
                            error: function() {
                                alert('Failed to delete booking. Please try again.');
                            }
                        });
                    }
                },

                canCheckIn: function(item) {
                    if (!item || item.check_availability == 1) {
                        return false;
                    }

                    return !item.booking_code && parseFloat(item.payment_total || 0) === 0;
                },
                isFinalizedBooking: function(item) {
                    if (!item) {
                        return false;
                    }

                    return !!(item.booking_code || parseFloat(item.payment_total || 0) > 0);
                },
                checkInFinalizedLabel: function(item) {
                    if (item.booking_type === 'Refund') {
                        return '{{ __('Refunded') }}';
                    }

                    if (item.payment_status === 'completed') {
                        return '{{ __('Completed') }}';
                    }

                    var paid = parseFloat(item.payment_total || 0);
                    var total = parseFloat(item.booking_total || 0);
                    if (item.booking_code && paid > 0 && Math.abs(paid - total) < 0.01) {
                        return '{{ __('Completed') }}';
                    }

                    return '{{ __('Finalized') }}';
                },
                checkInFinalizedBadgeClass: function(item) {
                    if (item.booking_type === 'Refund') {
                        return 'badge-warning';
                    }

                    if (item.payment_status === 'completed') {
                        return 'badge-success';
                    }

                    var paid = parseFloat(item.payment_total || 0);
                    var total = parseFloat(item.booking_total || 0);
                    if (item.booking_code && paid > 0 && Math.abs(paid - total) < 0.01) {
                        return 'badge-success';
                    }

                    return 'badge-primary';
                },
                checkInFinalizedIcon: function(item) {
                    if (item.booking_type === 'Refund') {
                        return 'fa-reply';
                    }

                    return 'fa-check-circle';
                },
                checkInFinalizedTitle: function(item) {
                    if (item.booking_type === 'Refund') {
                        return '{{ __('Refund completed — check-in not required') }}';
                    }

                    return '{{ __('Booking finalized — check-in not required') }}';
                },
                canUndoCheckIn: function(item) {
                    if (!item || item.check_availability != 1) {
                        return false;
                    }

                    if (item.booking_code || parseFloat(item.payment_total || 0) !== 0) {
                        return false;
                    }

                    if (item.booking_services && item.booking_services.service_started_at) {
                        return false;
                    }

                    return true;
                },
                checkInCustomer: function(booking_id, event) {
                    if (!booking_id) {
                        alert('Invalid booking ID.');
                        event.target.checked = false;
                        return;
                    }

                    const booking = this.paginator.db_rows.find(item => item.booking_id === booking_id);
                    if (!booking || !this.canCheckIn(booking)) {
                        alert('Check-in is only available for draft bookings without payment.');
                        event.target.checked = false;
                        return;
                    }

                    if (confirm("Are you sure you want to check in this customer?")) {
                        $.ajax({
                            url: "{{ route('salon.booking.check.availability') }}",
                            type: 'POST',
                            data: {
                                booking_id: booking_id,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function(result) {
                                if (result && result.js_notifications && result.js_notifications.warning) {
                                    alert(result.js_notifications.warning);
                                    event.target.checked = false;
                                    return;
                                }
                                location.reload();
                            },
                            error: function() {
                                alert('Failed to check in customer. Please try again.');
                                event.target.checked = false;
                            }
                        });
                    } else {
                        event.target.checked = false;
                    }
                },
                undoCheckInCustomer: function(booking_id, event) {
                    event.preventDefault();

                    if (!booking_id) {
                        alert('Invalid booking ID.');
                        event.target.checked = true;
                        return;
                    }

                    const booking = this.paginator.db_rows.find(item => item.booking_id === booking_id);
                    if (!booking || !this.canUndoCheckIn(booking)) {
                        alert('Check-in cannot be undone for this booking.');
                        event.target.checked = true;
                        return;
                    }

                    if (confirm("Are you sure you want to undo check-in for this customer?")) {
                        $.ajax({
                            url: "{{ route('salon.booking.undo.check.in') }}",
                            type: 'POST',
                            data: {
                                booking_id: booking_id,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function(result) {
                                if (result && result.js_notifications && result.js_notifications.warning) {
                                    alert(result.js_notifications.warning);
                                    event.target.checked = true;
                                    return;
                                }
                                location.reload();
                            },
                            error: function() {
                                alert('Failed to undo check-in. Please try again.');
                                event.target.checked = true;
                            }
                        });
                    } else {
                        event.target.checked = true;
                    }
                },

            },


        });
    });
    $('document').ready(function() {
        var count_array = $('#count_array').val()
        for (let index = 0; index < count_array; index++) {
            var id = '#format_date_' + index;
            var db_Date = $(id).text();
            var localTime = moment(db_Date).toDate();
            localTime = moment(localTime).format('YYYY-MM-DD HH:mm:ss');
            $(id).text(localTime);


        }

    });
</script>
