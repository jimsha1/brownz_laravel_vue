<div id="vue-list-tab-on-going" class="m-n1">
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
                        <th>{{ __('Manage') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in paginator.db_rows" :key="item.booking_service_id">
                        <td>@{{ index + paginator.sequence_start }}</td>
                        <td :style="{ color: item.booking_code ? '#52A8D9' : 'red' }">@{{ item.booking_code ? item.booking_code : 'Draft' }}</td>
                        <td>@{{ item.client_name }}(@{{item.client_mobile}})</td>
                        <td>@{{ item.beautician_name }}</td>
                        <td>@{{ item.service_name_locale }}</td>
                        <td>@{{ item.appointment_date | vc_date_expanded }}</td>
                        <td>@{{ item.slot_start }}</td>
                        <td>
                            <div class="manage-actions" v-if="item.service_started_at">
                                <button type="button" class="btn btn-success btn-sm py-1 btn-green-brownz"
                                    data-toggle="modal"
                                    v-bind:data-target="'#finishModal-' + item.booking_service_id">
                                    <i class="fa fa-check-circle"></i> Finish
                                </button>
                            </div>

                            <div class="modal fade brownz-custom-popup"
                                v-bind:id="'finishModal-' + item.booking_service_id"
                                tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content" style="border-radius: 16px; border: none;">
                                        <form action="{{ route('my.account.booking.service.stop') }}"
                                            data-hook-success="hide_finish_modal_report_ongoing"
                                            class="voco-ajax-form"
                                            method="post"
                                            v-bind:data-modal-id="'#finishModal-' + item.booking_service_id">
                                            @csrf
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title" style="color:#1D1D1D;font-weight:600;">{{ __('Finish Service') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body pt-0">
                                                <input type="hidden" name="booking_id" v-model="item.booking_id">
                                                <input type="hidden" name="booking_service_id" v-model="item.booking_service_id">
                                                <input type="hidden" name="from_tab" value="on-going">
                                                <label>{{ __('Remarks') }}</label>
                                                <textarea name="booking_remarks" class="form-control" rows="3"
                                                    placeholder="{{ __('Remarks (optional)') }}"></textarea>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn brownz-btn-yellow-d" data-dismiss="modal">
                                                    {{ __('Cancel') }}
                                                </button>
                                                <button type="submit" class="btn btn_dark-x">
                                                    <i class="fa fa-check"></i> {{ __('Finish') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
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
</div>

<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
    var vue_list_tab_on_going = null;
    jQuery(function($) {
        vue_list_tab_on_going = new Vue({
            el: "#vue-list-tab-on-going",
            data: <?php echo json_encode($db_results['vue_data']['vue_list_tab_on_going']); ?>,
            methods: {
                convertTimeTo12hr: function(time) {
                    time = time.toString().match(/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [
                        time
                    ];

                    if (time.length > 1) {
                        time = time.slice(1);
                        time[5] = +time[0] < 12 ? ' AM' : ' PM';
                        time[0] = +time[0] % 12 || 12;
                    }
                    return time.join('');
                },
            }
        });
    });

    var hide_finish_modal_report_ongoing = function($form) {
        var modalId = $form.attr('data-modal-id');
        if (modalId) {
            $(modalId).modal('hide');
        }
        window.location.href = "{{ route('report.appointment.list', 'on-going') }}";
    };
</script>
