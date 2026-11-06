<style>
    .input-group {
        column-gap: 15px;
    }

    .remove_field {
        color: red;
        cursor: pointer;
    }

    .input-group {
        padding-bottom: 15px;
    }

    .brownz-split-d select {
        background-color: #F0F0F3;
        color: #1D1D1D;
        font-size: 14px;
        font-weight: 400;
        padding: 10px 20px;
        border-radius: 26px !important;
        max-width: 100%;
        width: 100%;
        border: none;
        outline: none;
        height: auto;
    }

    .brownz-split-d select:focus {
        background-color: #F0F0F3;
    }

    .brownz-split-d input {
        padding: 4px 20px;
        background-color: #F0F0F3;
        color: #1D1D1D;
        font-size: 14px;
        font-weight: 400;
        padding: 10px 20px;
        border-radius: 26px !important;
        max-width: 100%;
        width: 100%;
        border: none;
        outline: none;
        height: 100%;
    }

    .remove_field {
        color: #1D1D1D;
    }

    .remove_field i {
        font-size: 20px;
    }

    .brownz-split-d .input-group {
        align-items: baseline;
    }

    .old-new-toggilebutton {
        background-color: #F0F0F3;
        color: #1d1d1d;
        padding: .5rem 1rem;
        border: none;
        border-radius: 26px;
        font-size: 14px;
        outline: none;
        cursor: pointer;
    }

    .old-new-toggilebutton:focus {
        outline: none;
        border: none;
    }

    .old-new-toggilebutton.old-new-d-active {
        color: #fff;
        background-color: #1d1d1d;
    }

    .old-new-toggilebutton.old-new-d-active i {
        color: #E6CD7F;
    }

    .old-new-toggilebutton i {
        color: #1d1d1d;
    }

    .postio-old-new-btn-container {
        position: absolute;
        right: 60px;
        top: 273px;
    }

    @media(max-width:1060px) {
        .postio-old-new-btn-container {
            position: unset;
            margin-top: 10px;
        }
    }

    .brw-checkbox-browz-dds .brow-checkmark {
        border-color: #E6CD7F;
        background-color: #fff !important;
    }

    .sticky-bottom-submbit-continer {
        position: sticky;
        bottom: 0;
        left: 0;
        background-color: #fff;
        width: 100%;
        padding: 10px 0;
        text-align: end;
    }

    .split-view-summary {
        background: #F7F7F9;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 16px;
    }

    .split-view-summary .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 6px;
    }

    .split-view-summary .summary-row:last-child {
        margin-bottom: 0;
    }

    .split-view-table {
        width: 100%;
        margin-bottom: 0;
    }

    .split-view-table th,
    .split-view-table td {
        vertical-align: middle !important;
        padding: 10px 12px;
    }

    .split-view-table tfoot th {
        border-top: 1px solid #ddd;
        font-weight: 600;
    }

    .split-names-hint {
        display: block;
        font-size: 11px;
        color: #6c757d;
        margin-top: 2px;
        line-height: 1.3;
        max-width: 220px;
    }

    .badge-split-view {
        background-color: #1D1D1D !important;
        color: #E6CD7F !important;
        cursor: pointer;
    }

    #open-split-modal .modal-content,
    #status-modal-split .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }

    #open-split-modal .modal-header,
    #status-modal-split .modal-header {
        border-bottom: none;
        padding: 16px 20px 8px;
        align-items: flex-start;
    }

    #open-split-modal .modal-body,
    #status-modal-split .modal-body {
        padding: 8px 20px 16px;
    }

    #open-split-modal .modal-footer,
    #status-modal-split .modal-footer {
        border-top: none;
        padding: 0 20px 16px;
    }

    .split-modal-subtitle {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 0;
    }

    .split-edit-remaining {
        font-size: 13px;
        margin-top: 8px;
    }

    .split-edit-remaining.is-valid {
        color: #199900;
    }

    .split-edit-remaining.is-invalid {
        color: #dc3545;
    }
</style>
<div id="vue-list-tab-completed" class="m-n1">
    <template v-if="paginator.db_rows.length">
        <div class="d-flex justify-content-end postio-old-new-btn-container">
            {{-- <button id="old-btn" class="old-new-toggilebutton old-new-d-active" style="margin-right: 6px"
                v-on:click="toggleDataBtn('old')"><i class="fa fa-file-o" aria-hidden="true"></i> Old</button> --}}
            {{-- <button id="new-btn" class="old-new-toggilebutton" v-on:click="toggleDataBtn('new')"><i
                    class="fa fa-file-o" aria-hidden="true"></i> New</button> --}}
        </div>


        <div v-if="isOldData" id="old-table">
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
                            {{-- @canany(['approve_reject_service', 'reschedule_rejected_service', 'refund_rejected_service']) --}}
                            <th>{{ __('Status') }}</th>
                            {{-- @endcanany --}}
                            <!-- <th>{{ __('Duration') }}</th> -->
                            <!-- <th>{{ __('Slot End') }}</th> -->
                            <!-- <th>{{ __('Service Start Time') }}</th> -->
                            <!-- <th>{{ __('Service End Time') }}</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in paginator.db_rows">
                            <td>@{{ index + paginator.sequence_start }}</td>
                            <td :style="{ color: item.booking_code ? '#52A8D9' : 'red' }">@{{ item.booking_code ? item.booking_code : 'Draft' }}</td>
                            <td>@{{ item.client_name }}(@{{ item.client_mobile }})</td>
                            <td>
                                <div>@{{ item.beautician_name }}</div>
                                <small v-if="item.split_amount_status === 'completed' && splitBeauticianSummary(item)"
                                    class="split-names-hint">
                                    {{ __('Split:') }} @{{ splitBeauticianSummary(item) }}
                                </small>
                            </td>
                            <td>@{{ item.service_name_locale }}</td>

                            <td>@{{ (item.appointment_date) | vc_date_expanded }}</td>
                            <td>@{{ convertTimeTo12hr(item.slot_start) }}</td>
                            {{-- @canany(['approve_reject_service', 'reschedule_rejected_service', 'refund_rejected_service']) --}}
                            <td>
                                <div v-if="item.booking_code">
                                    {{-- @can('approve_reject_service') --}}
                                    <button type="button" v-if="!item.rejected_at && !item.approved_at"
                                        class="btn btn-success btn-sm py-1" data-toggle="modal"
                                        v-on:click="approve_btn(item)">
                                        <i class="fa fa-check-circle"></i> {{ __('Approve') }}
                                    </button>
                                    <button type="button" v-if="!item.rejected_at && !item.approved_at"
                                        v-on:click="reject_btn(item)" class="btn btn-danger btn-sm py-1">
                                        <i class="fa fa-clock-o"></i> {{ __('Reject') }}
                                    </button>
                                    {{-- @endcan --}}
                                    <span v-if="item.approved_at"
                                        class="badge badge-success">{{ __('Approved') }}</span>
                                    <span v-if="item.rejected_at"
                                        class="badge badge-danger">{{ __('Rejected') }}</span>
                                    {{-- <span v-if="item.status=='refunded'" class="badge badge-danger">{{ __('Refunded') }}</span> --}}


                                    <button type="button"
                                        v-if="item.approved_at && item.split_amount_status === 'incompleted'"
                                        class="btn btn-warning btn-sm py-1" data-toggle="modal"
                                        v-on:click="split_btn(item)">
                                        <i class="fa fa-check-circle"></i> {{ __('Split Amount') }}
                                    </button>

                                    <span v-if="item.split_amount_status === 'completed'" class="badge badge-split-view"
                                        v-on:click="openSplitModal(item)"
                                        title="{{ __('View split details') }}">
                                        <i class="fa fa-eye"></i> {{ __('Split View') }}
                                    </span>


                                    {{-- @can('reschedule_rejected_service') --}}
                                    <form style="display:inline-block"
                                        v-if="item.rejected_at && item.status!='refunded'"
                                        class=" view-slot-form-completed"
                                        action="{{ route('report.appointment.booking.details') }}" method="POST"
                                        id="">
                                        <input type="hidden" name="booking_service_id"
                                            v-bind:value="item.booking_service_id">

                                        <input type="hidden" v-bind:value="item.beautician_id" name="beautician_id">
                                        <input type="hidden" v-bind:value="item.beautician_id"
                                            name="selected_beautician_id">

                                        <input type="hidden" v-bind:value="item.shift_id" name="shift_id">
                                        <button type="submit" class="badge badge-danger"
                                            v-bind:data-service-id="item.service_id"
                                            v-bind:data-booking-date="item.appointment_date" data-toggle="tooltip"
                                            title="{{ __('Change beautician and time slot') }}">
                                            <i class="fa fa-clock-o"></i> {{ __('Reschedule') }}
                                        </button>
                                    </form>
                                    {{-- @endcan --}}

                                    {{-- @can('refund_rejected_service') --}}
                                    {{-- <button type="button" v-if="item.rejected_at && item.status!='refunded'"
                                    v-on:click="cancel_btn(item)" class="btn btn-danger btn-sm py-1">
                                    <i class="fa fa-close"></i> {{ __('Refund') }}
                                </button> --}}
                                    {{-- @endcan --}}
                                </div>
                            </td>
                            {{-- @endcanany --}}
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
        </div>
        <div v-if="isNewData" id="new-table">
            <div class="table-responsive">
                <table class="table brownz-table-list-v1">
                    <thead>
                        <tr>
                            <th><label class="brw-checkbox-browz-dds" style="height: 13px;"><input type="checkbox"
                                        name="permissions[]" class="brow-checkbox-dd" value="create_user">
                                    <span class="brow-checkmark"></span></label></th>
                            <th>{{ __('SL#') }}</th>
                            <th>{{ __('Booking') }}</th>
                            <th>{{ __('Client') }}</th>
                            <th>{{ __('Beautician') }}</th>
                            <th>{{ __('Service') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Time') }}</th>
                            {{-- @canany(['approve_reject_service', 'reschedule_rejected_service', 'refund_rejected_service']) --}}
                            <th>{{ __('Status') }}</th>
                            {{-- @endcanany --}}
                            <!-- <th>{{ __('Duration') }}</th> -->
                            <!-- <th>{{ __('Slot End') }}</th> -->
                            <!-- <th>{{ __('Service Start Time') }}</th> -->
                            <!-- <th>{{ __('Service End Time') }}</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in paginator.db_rows">
                            <td>
                                <label class="brw-checkbox-browz-dds" style="height: 13px;">
                                    <input type="checkbox" v-model="selectedRows" @change="toggleSelection(item)"
                                        class="brow-checkbox-dd" :value="item">
                                    <span class="brow-checkmark"></span></label>

                            </td>
                            <td>@{{ index + paginator.sequence_start }}</td>
                            <td :style="{ color: item.booking_code ? '#52A8D9' : 'red' }">@{{ item.booking_code ? item.booking_code : 'Draft' }}</td>
                            <td>@{{ item.client_name }}(@{{ item.client_mobile }})</td>
                            <td>
                                <div>@{{ item.beautician_name }}</div>
                                <small v-if="item.split_amount_status === 'completed' && splitBeauticianSummary(item)"
                                    class="split-names-hint">
                                    {{ __('Split:') }} @{{ splitBeauticianSummary(item) }}
                                </small>
                            </td>
                            <td>@{{ item.service_name_locale }}</td>

                            <td>@{{ (item.appointment_date) | vc_date_expanded }}</td>
                            <td>@{{ convertTimeTo12hr(item.slot_start) }}</td>
                            {{-- @canany(['approve_reject_service', 'reschedule_rejected_service', 'refund_rejected_service']) --}}
                            <td>
                                <div v-if="item.booking_code">
                                    {{-- @can('approve_reject_service') --}}
                                    <button type="button" v-if="!item.rejected_at && !item.approved_at"
                                        class="btn btn-success btn-sm py-1" data-toggle="modal"
                                        v-on:click="approve_btn(item)">
                                        <i class="fa fa-check-circle"></i> {{ __('Approve') }}
                                    </button>
                                    <button type="button" v-if="!item.rejected_at && !item.approved_at"
                                        v-on:click="reject_btn(item)" class="btn btn-danger btn-sm py-1">
                                        <i class="fa fa-clock-o"></i> {{ __('Reject') }}
                                    </button>
                                    {{-- @endcan --}}
                                    <span v-if="item.approved_at"
                                        class="badge badge-success">{{ __('Approved') }}</span>
                                    <span v-if="item.rejected_at"
                                        class="badge badge-danger">{{ __('Rejected') }}</span>


                                    <button type="button"
                                        v-if="item.approved_at && item.split_amount_status === 'incompleted'"
                                        class="btn btn-warning btn-sm py-1" data-toggle="modal"
                                        v-on:click="split_btn(item)">
                                        <i class="fa fa-check-circle"></i> {{ __('Split Amount') }}
                                    </button>

                                    <span v-if="item.split_amount_status === 'completed'" class="badge badge-split-view"
                                        v-on:click="openSplitModal(item)"
                                        title="{{ __('View split details') }}">
                                        <i class="fa fa-eye"></i> {{ __('Split View') }}
                                    </span>

                                    @can('split_edit')
                                        <span v-if="item.split_amount_status === 'completed'" class="badge badge-danger"
                                            v-on:click="split_btn(item)"
                                            style="cursor: pointer; background-color:rgba(254, 216, 177, 0.8);"
                                            title="{{ __('Edit split amounts') }}">
                                            {{ __('Edit Split') }}
                                        </span>
                                    @endcan

                                    <form style="display:inline-block"
                                        v-if="item.rejected_at && item.status!='refunded'"
                                        class=" view-slot-form-completed"
                                        action="{{ route('report.appointment.booking.details') }}" method="POST"
                                        id="">
                                        <input type="hidden" name="booking_service_id"
                                            v-bind:value="item.booking_service_id">

                                        <input type="hidden" v-bind:value="item.beautician_id" name="beautician_id">
                                        <input type="hidden" v-bind:value="item.beautician_id"
                                            name="selected_beautician_id">

                                        <input type="hidden" v-bind:value="item.shift_id" name="shift_id">
                                        <button type="submit" class="badge badge-danger"
                                            v-bind:data-service-id="item.service_id"
                                            v-bind:data-booking-date="item.appointment_date" data-toggle="tooltip"
                                            title="{{ __('Change beautician and time slot') }}">
                                            <i class="fa fa-clock-o"></i> {{ __('Reschedule') }}
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="sticky-bottom-submbit-continer">
                <button class="btn_dark-x" v-on:click="submitSelection()">Submit</button>
            </div>
            <div class="tab-pagination" v-html="paginator.links">
            </div>
        </div>

    </template>
    <template v-else>
        <div class="alert alert-danger">
            {{ __('No results found.') }}
        </div>
    </template>

    @include('appointment.booking-service-list.ajax-tabs.partials.beautician-slot-modal-completed')

    <!-- Modal -->
    <div class="modal fade" id="status-modal-reject" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <form class="brw-form-la" action="{{ route('report.appointment.reject') }}"
                    data-hook-success="hide_reject_modal" class="voco-ajax-form" method="post"
                    style="display:inline-block">
                    @csrf
                    <div class="modal-body">

                        <input type="hidden" name="booking_id" id="modal_reject_booking_id">
                        <input type="hidden" name="booking_service_id" id="modal_reject_booking_service_id">
                        <textarea name="booking_remarks" id="modal_reject_booking_remarks" class="form-control"
                            placeholder="{{ __('Remarks') }}"></textarea>


                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary btn-sm py-1 btn-danger mr-2"
                            data-dismiss="modal">{{ __('Close') }}</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                        <button type="submit" class="btn btn-danger btn-sm py-1">
                            <i class="fa fa-check-circle"></i> {{ __('Reject') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade brownz-custom-popup" id="status-modal-split" tabindex="-1" role="dialog"
        aria-labelledby="splitEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title popup-head-bootst" id="splitEditModalLabel">{{ __('Split Amount') }}</h5>
                        <p class="split-modal-subtitle">{{ __('Assign the service total across beauticians.') }}</p>
                    </div>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="brownz-close-btn"
                        onclick="resetSplitEditModal();">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="modal-body brownz-split-d">
                    <input type="hidden" name="booking_id" id="modal_split_booking_id">
                    <input type="hidden" name="beautician_id" id="modal_split_beautician_id">
                    <input type="hidden" name="booking_service_id" id="modal_split_booking_service_id">
                    <input type="hidden" name="booking_service_started_at" id="modal_service_started_at">
                    <input type="hidden" name="commission_start_date" id="modal_commission_start_date">
                    <input type="hidden" name="commission_end_date" id="modal_commission_end_date">
                    <input type="hidden" name="commission_percentage" id="modal_commission_percentage">
                    <input type="hidden" name="commission_status" id="modal_commission_status">
                    <input type="hidden" name="service_id" id="modal_service_id">

                    <div class="split-view-summary">
                        <div class="summary-row">
                            <span>{{ __('Service') }}</span>
                            <strong id="modal_service_name">—</strong>
                        </div>
                        <div class="summary-row">
                            <span>{{ __('Service Amount') }}</span>
                            <strong id="modal_service_amount">0.00</strong>
                        </div>
                        <div class="summary-row">
                            <span>{{ __('Assigned Beautician') }}</span>
                            <strong id="modal_split_assigned_beautician">—</strong>
                        </div>
                    </div>

                    <div class="input_fields_wrap">
                        <div class="text-left ml-10">
                            <button type="button" class="add_field_button brownz-btn-yellow-d mb-3">
                                {{ __('Add Beautician') }}
                            </button>
                        </div>
                    </div>
                    <p id="split-edit-remaining" class="split-edit-remaining mb-0"></p>

                </div>
                <div class="modal-footer">
                    <button onclick="resetSplitEditModal()" type="button" class="btn btn-secondary brownz-btn-yellow-d"
                        data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn_dark-x" id="submitSplitForm">
                        <i class="fa fa-check-circle"></i> {{ __('Submit') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade brownz-custom-popup" id="open-split-modal" tabindex="-1" role="dialog"
        aria-labelledby="splitViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title popup-head-bootst" id="splitViewModalLabel">{{ __('Split View') }}</h5>
                        <p class="split-modal-subtitle">
                            {{ __('Review how this service amount was split between beauticians.') }}
                        </p>
                    </div>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="brownz-close-btn"
                        onclick="closeSplitViewModal();">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="modal-body brownz-split-d">
                    <div class="split-view-summary">
                        <div class="summary-row">
                            <span>{{ __('Booking') }}</span>
                            <strong id="modal_split_booking_code">—</strong>
                        </div>
                        <div class="summary-row">
                            <span>{{ __('Service') }}</span>
                            <strong id="modal_service_name1">—</strong>
                        </div>
                        <div class="summary-row">
                            <span>{{ __('Service Amount') }}</span>
                            <strong id="modal_service_amount1">0.00</strong>
                        </div>
                        <div class="summary-row">
                            <span>{{ __('Assigned Beautician') }}</span>
                            <strong id="modal_assigned_beautician">—</strong>
                        </div>
                        <div class="summary-row">
                            <span>{{ __('Commission Rate') }}</span>
                            <strong id="modal_split_commission_rate">—</strong>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered split-view-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 48px;">#</th>
                                    <th>{{ __('Beautician') }}</th>
                                    <th class="text-right">{{ __('Split Amount') }}</th>
                                    <th class="text-right">{{ __('Commission') }}</th>
                                </tr>
                            </thead>
                            <tbody id="open-split-modal-rows">
                                <tr>
                                    <td colspan="4" class="text-center text-muted">{{ __('Loading...') }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">{{ __('Total Split') }}</th>
                                    <th class="text-right" id="open-split-modal-total">0.00</th>
                                    <th class="text-right" id="open-split-modal-commission-total">0.00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <p id="open-split-modal-mismatch" class="text-danger small mt-2 mb-0" style="display:none;"></p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary brownz-btn-yellow-d"
                        onclick="closeSplitViewModal();">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="status-modal-approve" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <!-- <div class="modal-header">
<h5 class="modal-title" id="exampleModalLabel">Comment</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
  <span aria-hidden="true">&times;</span>
</button>
</div> -->
                <form class="brw-form-la" action="{{ route('report.appointment.approve') }}"
                    data-hook-success="hide_approve_modal" class="voco-ajax-form" method="post"
                    style="display:inline-block">
                    @csrf
                    <div class="modal-body">

                        <input type="hidden" name="booking_id" id="modal_approve_booking_id">
                        <input type="hidden" name="booking_service_id" id="modal_approve_booking_service_id">
                        <textarea name="booking_remarks" id="modal_approve_booking_remarks" class="form-control"
                            placeholder="{{ __('Remarks') }}"></textarea>


                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary btn-sm py-1 btn-danger"
                            data-dismiss="modal">{{ __('Close') }}</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                        <button type="submit" class="btn btn-success btn-sm py-1 ">
                            <i class="fa fa-check-circle"></i> {{ __('Approve') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".brownz-custom-radio-tick .checkmark").forEach(checkmark => {
                const observer = new MutationObserver(() => {
                    if (window.getComputedStyle(checkmark, "::after").content !== "none") {
                        checkmark.style.backgroundColor =
                            "#199900"; // Change color when ::after is present
                    } else {
                        checkmark.style.backgroundColor = "#E6CD7F"; // Default color
                    }
                });

                observer.observe(checkmark, {
                    attributes: true,
                    childList: true,
                    subtree: true
                });
            });
        });
    </script>


    <div class="modal fade" id="status-modal-cancel" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('report.appointment.refund') }}" data-hook-success="hide_cancel_modal"
                    class="voco-ajax-form" method="post" style="display:inline-block">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="booking_id" id="modal_cancel_booking_id">
                        <input type="hidden" name="booking_service_id" id="modal_cancel_booking_service_id">
                        <textarea name="booking_remarks" id="modal_cancel_booking_remarks" class="form-control"
                            placeholder="{{ __('Remarks') }}"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm py-1"
                            data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-success btn-sm py-1">
                            <i class="fa fa-check-circle"></i> {{ __('Refund') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>



{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4/dist/flatpickr.min.css"> --}}
<script src="{{ asset('web\calender-piker\calendar.min.js') }}"></script>
<script src="https://unpkg.com/codeflask/build/codeflask.min.js"></script>
<script src="{{ asset('components/momentjs/moment.min.js') }}"></script>
<script>
    var vue_list_tab_completed = null;
    jQuery(function($) {
        vue_list_tab_completed = new Vue({
            el: "#vue-list-tab-completed",
            data: <?php echo json_encode(
                array_merge($db_results['vue_data']['vue_list_tab_completed'], [
                    'selected_beautician_id' => null,
                    'time_slot' => [],
                    'update_booking_id' => '',
                    'update_service_id' => '',
                    'selected_time' => null,
                    'isOldData' => false,
                    'isNewData' => true,
                    'selectedRows' => [],
                ]),
            ); ?>,
            methods: {
                toggleSelection(item) {
                    console.log("Before Selection Change:", JSON.parse(JSON.stringify(this
                        .selectedRows)));
                    if (!this.selectedRows) {
                        this.selectedRows = [];
                    }
                    console.log("After Selection Change:", JSON.parse(JSON.stringify(this
                        .selectedRows)));
                },
                submitSelection() {
                    console.log("Selected Items List INSIDE SUBMIT:", this
                        .selectedRows);
                    if (this.selectedRows.length === 0) {
                        alert("Please select at least one booking.");
                        return;
                    }

                    const selectedBookings = JSON.parse(JSON.stringify(this.selectedRows));

                    $.ajax({
                        method: "POST",
                        url: "{{ route('salon.booking.approve.data.store') }}",
                        data: {
                            selectedBookings: selectedBookings,
                        },
                        dataType: "json",
                        success: function(response) {
                            console.log("Response:", response);

                            vue_list_tab_completed.selectedRows = [];
                            window.location.href = "/report/appointments/completed";
                            // alert("Data submitted successfully!");
                        },
                    });
                },


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

                split_btn: function(item) {
                    $('#modal_split_beautician_id').val(item.beautician_id);
                    $('#modal_split_booking_id').val(item.booking_id);
                    $('#modal_split_booking_service_id').val(item.booking_service_id);
                    $('#modal_service_name').text(item.service_name || item.service_name_locale || '—');
                    $('#modal_split_assigned_beautician').text(item.beautician_name || '—');
                    $('#modal_service_started_at').val(item.service_started_at);
                    $('#modal_service_amount').text(parseFloat(item.line_total || 0).toFixed(2));
                    $('#modal_commission_end_date').val(item.end_date);
                    $('#modal_commission_start_date').val(item.start_date);
                    $('#modal_commission_percentage').val(item.commission_percentage);
                    $('#modal_service_id').val(item.service_id);
                    $('#modal_commission_status').val(item.commission_status !== null ? item
                        .commission_status : 1);

                    $('#status-modal-split').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $('#status-modal-split').modal('show');
                },

                openSplitModal: function(item) {
                    var serviceAmount = parseFloat(item.line_total || 0);
                    if (isNaN(serviceAmount)) {
                        serviceAmount = 0;
                    }

                    var commissionRate = parseFloat(item.commission_percentage || 0);
                    var commissionLabel = isNaN(commissionRate) || commissionRate <= 0
                        ? '—'
                        : commissionRate.toFixed(2) + '%';

                    $('#modal_split_booking_code').text(item.booking_code || '—');
                    $('#modal_service_name1').text(item.service_name || item.service_name_locale || '—');
                    $('#modal_service_amount1').text(serviceAmount.toFixed(2));
                    $('#modal_assigned_beautician').text(item.beautician_name || '—');
                    $('#modal_split_commission_rate').text(commissionLabel);
                    $('#open-split-modal-rows').html(
                        '<tr><td colspan="4" class="text-center text-muted">{{ __("Loading...") }}</td></tr>'
                    );
                    $('#open-split-modal-total').text('0.00');
                    $('#open-split-modal-commission-total').text('0.00');
                    $('#open-split-modal-mismatch').hide().text('');

                    $('#open-split-modal').modal({
                        backdrop: 'static',
                        keyboard: true
                    });
                    $('#open-split-modal').modal('show');

                    $.ajax({
                        url: '{{ route('get.split.details') }}',
                        type: 'POST',
                        data: {
                            booking_id: item.booking_id,
                            service_id: item.service_id,
                            booking_service_id: item.booking_service_id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            var rows = Array.isArray(response) ? response : [];
                            var totalSplit = 0;
                            var totalCommission = 0;
                            var html = '';

                            if (!rows.length) {
                                html = '<tr><td colspan="4" class="text-center text-muted">{{ __("No split records found") }}</td></tr>';
                            } else {
                                rows.forEach(function(split, index) {
                                    var price = parseFloat(split.price || 0);
                                    var commission = parseFloat(split.commission_amount || 0);
                                    if (isNaN(price)) {
                                        price = 0;
                                    }
                                    if (isNaN(commission)) {
                                        commission = 0;
                                    }
                                    totalSplit += price;
                                    totalCommission += commission;

                                    var name = (split.user && split.user.name)
                                        ? split.user.name
                                        : ((split.beautician && split.beautician.name)
                                            ? split.beautician.name
                                            : '—');
                                    var code = (split.beautician && split.beautician.beautician_code)
                                        ? ' (' + split.beautician.beautician_code + ')'
                                        : '';

                                    html += '<tr>' +
                                        '<td>' + (index + 1) + '</td>' +
                                        '<td>' + name + code + '</td>' +
                                        '<td class="text-right">' + price.toFixed(2) + '</td>' +
                                        '<td class="text-right">' + commission.toFixed(2) + '</td>' +
                                        '</tr>';
                                });
                            }

                            $('#open-split-modal-rows').html(html);
                            $('#open-split-modal-total').text(totalSplit.toFixed(2));
                            $('#open-split-modal-commission-total').text(totalCommission.toFixed(2));

                            if (rows.length && Math.abs(totalSplit - serviceAmount) > 0.01) {
                                $('#open-split-modal-mismatch')
                                    .text('{{ __("Warning: split total does not match service amount.") }}')
                                    .show();
                            }
                        },
                        error: function() {
                            $('#open-split-modal-rows').html(
                                '<tr><td colspan="4" class="text-center text-danger">{{ __("Failed to fetch split details.") }}</td></tr>'
                            );
                        }
                    });
                },

                splitBeauticianSummary: function(item) {
                    if (!item || !item.split_amounts || !item.split_amounts.length) {
                        return '';
                    }

                    return item.split_amounts.map(function(split) {
                        var name = (split.user && split.user.name) ? split.user.name : '—';
                        var price = parseFloat(split.price || 0);
                        if (isNaN(price)) {
                            price = 0;
                        }
                        return name + ' (' + price.toFixed(2) + ')';
                    }).join(', ');
                },


                approve_btn: function(item) {
                    $('#modal_approve_booking_id').val(item.booking_id);
                    $('#modal_approve_booking_service_id').val(item.booking_service_id);
                    $('#modal_approve_booking_remarks').val('');
                    $('#status-modal-approve').modal('show');
                },

                reject_btn: function(item) {
                    $('#modal_reject_booking_id').val(item.booking_id);
                    $('#modal_reject_booking_service_id').val(item.booking_service_id);
                    $('#modal_reject_booking_remarks').val('');
                    $('#status-modal-reject').modal('show');
                },

                cancel_btn: function(item) {
                    $('#modal_cancel_booking_id').val(item.booking_id);
                    $('#modal_cancel_booking_service_id').val(item.booking_service_id);
                    $('#modal_cancel_booking_remarks').val('');
                    $('#status-modal-cancel').modal('show');
                },
                getTimeSlots: function(selectedDate) {

                    $.ajax({
                        method: "POST",
                        url: "{{ route('booking.change') }}",
                        data: {
                            service: vue_list_tab_completed.this_booking_service_id,
                            is_pos: 1,
                            service_type: 3,
                            shift_id: vue_list_tab_completed.this_shift_id,
                            duration: vue_list_tab_completed.duration_minutes,
                            price: vue_list_tab_completed.price,
                            date: selectedDate,
                            beautician: vue_list_tab_completed.this_beautician_id,
                        },

                    }).done(function(data) {
                        console.log('=============time slot===========');
                        console.log(data);
                        vue_list_tab_completed.time_slot = data.time_slot;

                    });
                },
                updateTimeSlot: function(time, btId) {


                    var selected_date_ajax = $('#timeSlote-1').find("input[name=selected_date]")
                        .val();


                    if (selected_date_ajax == undefined || selected_date_ajax === "") {
                        var d = new Date();
                        selected_date_ajax = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d
                            .getDate();
                    }

                    // salon.booking.item.update
                    //=============================================
                    $.ajax({
                        method: "POST",
                        url: "{{ route('salon.booking.item.update') }}",
                        data: {
                            service_id: vue_list_tab_completed.update_service_id,
                            beautician_id: vue_list_tab_completed.this_beautician_id,
                            booking_id: vue_list_tab_completed.update_booking_id,
                            booking_service_id: vue_list_tab_completed
                                .this_booking_service_id,
                            appointment_date: selected_date_ajax,
                            slot_start: time
                        }
                    }).done(function(data) {
                        //location.reload();
                    }).fail(function(data) {
                        alert('time slot failed..!!');
                    });
                    //=====================================================
                },
                selectBeautician: function(bt) {
                    vue_list_tab_completed.selectedBt = bt;
                    vue_list_tab_completed.this_beautician_id = bt.beautician_id;
                    vue_list_tab_completed.this_shift_id = bt.shift_id;
                    console.log(vue_list_tab_completed.this_shift_id);
                    console.log('===============shift_id=================');
                    console.log(bt);
                    // console.log(vue_list_tab_completed.this_shift_id);
                    var selected_date_ajax = $('#timeSlote-1').find("input[name=selected_date]")
                        .val();
                    if (selected_date_ajax == undefined || selected_date_ajax == "") {
                        var d = new Date();
                        selected_date_ajax = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d
                            .getDate();
                    }
                    vue_list_tab_completed.getTimeSlots(selected_date_ajax);
                    console.log(vue_list_tab_completed.selectedBt.name_locale);
                },
                toggleDataBtn(type) {

                    if (type == 'old') {
                        this.isOldData = true;
                        this.isNewData = false;
                        $('#old-btn').addClass("old-new-d-active")
                        $('#new-btn').removeClass("old-new-d-active")
                    } else {
                        this.isOldData = false;
                        this.isNewData = true;
                        $('#old-btn').removeClass("old-new-d-active")
                        $('#new-btn').addClass("old-new-d-active")
                    }

                }
            },
            watch: {
                selectedRows() {
                    // Update the selectAll checkbox state dynamically
                    this.selectAll = this.selectedRows.length === this.paginator.db_rows.length;
                }
            },
            filters: {
                time_hm: function(value) {
                    return value ? value.substring(0, 5) : '';
                }
            },
        });




        $("body").on("change", "#booking_date_completed", function() {
            var $this = $(this);
            ajaxSubmitForm($($this.closest("form")[0]))
        });




        $("body").on("submit", ".view-slot-form-completed", function(e) {

            e.preventDefault();
            var $this = $(this);

            vue_list_tab_completed.selected_beautician_id = $this.find(
                'input[name=selected_beautician_id]').val();
            vue_list_tab_completed.this_beautician_id = vue_list_tab_completed.selected_beautician_id;
            var this_booking_service_id = $this.find('input[name=booking_service_id]').val();
            vue_list_tab_completed.this_booking_service_id = $this.find(
                'input[name=booking_service_id]').val();
            vue_list_tab_completed.beauticians = [];
            vue_list_tab_completed.selectedBt = null;
            $('#beautician-slot-modal-completed').modal('show');

            $.ajax({
                    method: "POST",
                    url: $(this).attr('action'),
                    data: $(this).serialize()
                })
                .done(function(data) {
                    vue_list_tab_completed.sel_beautician_id = vue_list_tab_completed
                        .this_beautician_id;

                    vue_list_tab_completed.update_service_id = data.service_id;
                    vue_list_tab_completed.update_booking_id = data.booking_info.booking_id;


                    // console.log(data.service_id);
                    vue_list_tab_completed.booking_info = data.booking_info;
                    vue_list_tab_completed.focused_active_service = data.service_id;
                    vue_list_tab_completed.focused_active_booking_date = data.booking_date;
                    vue_list_tab_completed.beauticians = [];

                    for (var i in data.booking_info.booking_only_services) {
                        var bkservice = data.booking_info.booking_only_services[i];
                        if (bkservice.service_id == data.service_id) {
                            // return bkservice.service.beauticians;
                            vue_list_tab_completed.beauticians = bkservice.service.beauticians;
                            vue_list_tab_completed.selectedBt = vue_list_tab_completed.beauticians
                                .find(item => item.beautician_id == vue_list_tab_completed
                                    .this_beautician_id);
                        }

                        vue_list_tab_completed.$forceUpdate()
                        // vue_list_tab_completed.modal_load = false;

                    }
                    var d = new Date();
                    selected_date_ajax = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d
                        .getDate();
                    vue_list_tab_completed.getTimeSlots(selected_date_ajax);
                }).fail(function(data) {});
        });
        $("#beautician-slot-modal-completed").on("hidden.bs.modal", function(e) {
            $.ajax({
                    method: "GET",
                    url: "{{ route('report.appointment.list', 'completed') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "pgajax": 'y'
                    }
                })
                .done(function(data) {
                    vue_list_tab_completed.paginator = data.vue_data.vue_list_tab_completed
                        .paginator;
                    vue_list_tab_completed.$forceUpdate();

                }).fail(function(data) {

                });

        })

    });

    // var hide_split_modal = function($form, data) {
    //     $('#status-modal-split').modal('hide');
    // };
    // var hide_open_split_modal = function($form, data) {
    //     $('#open-split-modal').modal('hide');
    // };
    var hide_approve_modal = function($form, data) {
        $('#status-modal-approve').modal('hide');
    };
    var hide_reject_modal = function($form, data) {
        $('#status-modal-reject').modal('hide');
    };
    var hide_cancel_modal = function($form, data) {
        $('#status-modal-cancel').modal('hide');
    };




    var popupSlotUpdated = function($form, data) {
        if (data.booking_info) {
            vue_list_tab_completed.booking_info = data.booking_info;

            for (var i in data.booking_info.booking_only_services) {
                var bkservice = data.booking_info.booking_only_services[i];
                if (bkservice.service_id == vue_list_tab_completed.focused_active_service) {
                    // return bkservice.service.beauticians;
                    vue_list_tab_completed.beauticians = bkservice.service.beauticians;

                }
            }
            vue_list_tab_completed.$forceUpdate();

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
$('#calendar-wrapper-3').updateCalendarOptions({
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
vue_list_tab_completed.getTimeSlots(date);
}
var defaultConfig = {
weekDayLength: 1,
date:   new Date(),
onClickDate: selectDate,
showYearDropdown: true,
startOnMonday: true,
};

var calendar = $('#calendar-wrapper-3').calendar(defaultConfig);
console.log(calendar.getSelectedDate());
`;

        eval(config);

    });
</script>


{{-- <script>
    $(document).ready(function() {
        $(document).on('shown.bs.modal', '#status-modal-split', function() {
            var selectedBeauticianId = $('#modal_split_beautician_id').val();
            var commissionStartDate = $('#modal_commission_start_date').val();
            var commissionEndDate = $('#modal_commission_end_date').val();
            var commissionPercentage = $('#modal_commission_percentage').val();
            var commissionStatus = $('#modal_commission_status').val();
            console.log('-------comission-------');
            console.log(commissionStartDate + " " + commissionEndDate + " " + commissionPercentage +
                " " + commissionStatus);


            var selectedAmount = parseFloat($('#modal_service_amount').text()); // Ensure it's a number
            console.log(selectedAmount + "beut_id" + selectedBeauticianId);
            var numberOfBeauticians = 1;
            var max_fields = 6;
            var wrapper = $(".input_fields_wrap");
            var add_button = $(".add_field_button");

            // HTML template for fields
            var html = '<div class="input-group mb-2" id="fieldGroup">' +
                '<select class="form-control voco-select2" name="beautician[]" data-placeholder="Beautician">' +
                '<option value="">Select Beautician</option>' +
                '{{ $beauticians_dropdown }}' +
                '</select>' +
                '<input type="number" class="form-control price-field" name="price[]" placeholder="Price" />' +
                '<div class="remove_field"><i class="fa fa-trash fa-2x"></i></div>' +
                '</div>';

            // Append only if the wrapper is empty
            if ($(wrapper).find(".input-group").length === 0) {
                $(wrapper).append(html);
                $(wrapper).find("select[name='beautician[]']").val(selectedBeauticianId);
                $(wrapper).find("input[name='price[]']").val(selectedAmount.toFixed(2));
                if (numberOfBeauticians === 1) {
                    $(wrapper).find(".remove_field").hide();
                }
            }

            // Update prices function
            function updatePrices() {
                var splitAmount = (selectedAmount / numberOfBeauticians).toFixed(
                    2); // Recalculate the price
                $(wrapper).find("input[name='price[]']").each(function() {
                    $(this).val(splitAmount); // Update all price fields
                });
            }

            $(add_button).click(function(e) {
                var selectedBeauticianId = $('#modal_split_beautician_id').val();
                console.log(selectedBeauticianId);

                e.preventDefault();
                var current_fields = $(wrapper).find(".input-group").length;
                // if (current_fields < max_fields) {
                $(wrapper).append(html);
                numberOfBeauticians++;
                updatePrices();
                // }
            });

            $(wrapper).on("click", ".remove_field", function() {
                $(this).closest(".input-group").remove();
                numberOfBeauticians--;
                updatePrices();
            });

            $('#submitSplitForm').click(function(e) {
                e.preventDefault();

                var beauticians = [];
                var prices = [];
                var hasDuplicates = false;

                // Collect beautician and price values
                $("select[name='beautician[]']").each(function(
                    index) {
                    var value = $(this).val();
                    console.log("Beautician Value:", value);

                    if (index === 0) {

                        return;
                    }

                    if (value && !beauticians.includes(value)) {
                        beauticians.push(value); // Add to array only if unique
                    } else if (value) {
                        hasDuplicates = true; // Flag duplicate entry
                    }
                });


                $("input[name='price[]']").each(function() {
                    var priceValue = parseFloat($(this).val());
                    if (!isNaN(priceValue)) {
                        prices.push(priceValue);
                    }
                });

                // Check for duplicate beauticians
                if (hasDuplicates) {
                    alert('Duplicate beautician entries are not allowed.');
                    return; // Prevent submission
                }

                // Validate prices if needed
                if (prices.length === 0 || prices.some(price => price <= 0 || isNaN(price))) {
                    alert('All prices must be valid numbers greater than 0.');
                    return; // Prevent submission
                }

                var data = {
                    beauticians: beauticians,
                    prices: prices,
                    booking_id: $('#modal_split_booking_id').val(),
                    beautician_id: $('#modal_split_beautician_id').val(),
                    service_name: $('#modal_service_name').text(),
                    service_started_at: $('#modal_service_started_at').val(),
                    line_total: parseFloat($('#modal_service_amount').text()),
                    start_date: commissionStartDate,
                    end_date: commissionEndDate,
                    commission_percentage: commissionPercentage,
                    commission_status: commissionStatus,
                    service_id: $('#modal_service_id').val(),
                };

                // AJAX submission
                $.ajax({
                    url: '/api/store-service-split',
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        console.log(response);

                        if (response.errorcode == 0) {
                            $('#status-modal-split').modal('hide');
                            $('.modal-backdrop').remove();
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    // error: function(xhr, status, error) {
                    //     alert('An error occurred: ' + error);
                    // }
                });
            });


        });
    });


    function clearModalFields() {
        // Clear hidden input fields
        $('#modal_split_booking_id').val('');
        $('#modal_split_beautician_id').val('');
        $('#modal_split_booking_service_id').val('');

        // Clear service name and amount text
        $('#modal_service_name1').text('');
        $('#modal_service_amount1').text('');
        $('.input_fields_wrap').empty();
        $('.modal-backdrop').remove();
        location.reload();
    }
</script> --}}
<script>
    (function() {
        var splitFieldTemplate = '<div class="input-group mb-2">' +
            '<select class="form-control voco-select2" name="beautician[]" data-placeholder="{{ __('Beautician') }}">' +
            '<option value="">{{ __('Select Beautician') }}</option>' +
            '{{ $beauticians_dropdown }}' +
            '</select>' +
            '<input type="number" step="0.01" min="0" class="form-control price-field" name="price[]" placeholder="{{ __('Amount') }}" />' +
            '<div class="remove_field"><i class="fa fa-trash fa-2x"></i></div>' +
            '</div>';

        function getSplitServiceAmount() {
            return parseFloat($('#modal_service_amount').text()) || 0;
        }

        function refreshCompletedTab() {
            $.ajax({
                method: 'GET',
                url: "{{ route('report.appointment.list', 'completed') }}",
                data: {
                    pgajax: 'y',
                    start_date: $('#start_date').val() || '',
                    end_date: $('#end_date').val() || '',
                    search: $('input[name="search"]').val() || ''
                }
            }).done(function(data) {
                if (data.vue_data && data.vue_data.vue_list_tab_completed) {
                    vue_list_tab_completed.paginator = data.vue_data.vue_list_tab_completed.paginator;
                    vue_list_tab_completed.$forceUpdate();
                }
            });
        }

        function clearSplitFieldRows() {
            $('#status-modal-split .input_fields_wrap .input-group').remove();
            $('#split-edit-remaining').text('').removeClass('is-valid is-invalid');
        }

        function appendSplitFieldRow(wrapper, beauticianId, price, hideRemove) {
            var $row = $(splitFieldTemplate).appendTo(wrapper);
            if (beauticianId) {
                $row.find("select[name='beautician[]']").val(beauticianId);
            }
            if (typeof price !== 'undefined' && price !== null && price !== '') {
                $row.find("input[name='price[]']").val(parseFloat(price).toFixed(2));
            }
            if (hideRemove) {
                $row.find('.remove_field').hide();
            }
            return $row;
        }

        function updateSplitRemainingHint() {
            var serviceAmount = getSplitServiceAmount();
            var total = 0;

            $('#status-modal-split input[name="price[]"]').each(function() {
                var value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    total += value;
                }
            });

            var remaining = serviceAmount - total;
            var $hint = $('#split-edit-remaining');

            if (!serviceAmount) {
                $hint.text('').removeClass('is-valid is-invalid');
                return remaining;
            }

            if (Math.abs(remaining) <= 0.01) {
                $hint
                    .text('{{ __('Split total matches service amount.') }}')
                    .removeClass('is-invalid')
                    .addClass('is-valid');
            } else {
                $hint
                    .text('{{ __('Remaining:') }} ' + remaining.toFixed(2))
                    .removeClass('is-valid')
                    .addClass('is-invalid');
            }

            return remaining;
        }

        function redistributeSplitPrices() {
            var serviceAmount = getSplitServiceAmount();
            var $priceFields = $('#status-modal-split input[name="price[]"]');
            var count = $priceFields.length;

            if (!count || !serviceAmount) {
                updateSplitRemainingHint();
                return;
            }

            var splitAmount = (serviceAmount / count).toFixed(2);
            $priceFields.each(function() {
                $(this).val(splitAmount);
            });
            updateSplitRemainingHint();
        }

        function loadSplitEditRows() {
            var wrapper = $('#status-modal-split .input_fields_wrap');
            var selectedBeauticianId = $('#modal_split_beautician_id').val();
            var selectedAmount = getSplitServiceAmount();
            var bookingId = $('#modal_split_booking_id').val();
            var serviceId = $('#modal_service_id').val();
            var bookingServiceId = $('#modal_split_booking_service_id').val();

            clearSplitFieldRows();

            $.ajax({
                url: '{{ route('get.split.details') }}',
                type: 'POST',
                data: {
                    booking_id: bookingId,
                    service_id: serviceId,
                    booking_service_id: bookingServiceId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    var rows = Array.isArray(response) ? response : [];

                    if (rows.length) {
                        rows.forEach(function(item, index) {
                            appendSplitFieldRow(wrapper, item.beautician_id, item.price, index === 0);
                        });
                    } else {
                        appendSplitFieldRow(wrapper, selectedBeauticianId, selectedAmount, true);
                    }

                    updateSplitRemainingHint();
                },
                error: function() {
                    appendSplitFieldRow(wrapper, selectedBeauticianId, selectedAmount, true);
                    alert('{{ __('Error loading split data.') }}');
                }
            });
        }

        $(document).ready(function() {
            $('#status-modal-split').on('shown.bs.modal', loadSplitEditRows);

            $('#status-modal-split').on('hidden.bs.modal', function() {
                clearSplitFieldRows();
            });

            $(document).on('click', '#status-modal-split .add_field_button', function(e) {
                e.preventDefault();
                appendSplitFieldRow($('#status-modal-split .input_fields_wrap'));
                redistributeSplitPrices();
            });

            $(document).on('click', '#status-modal-split .remove_field', function() {
                var $rows = $('#status-modal-split .input_fields_wrap .input-group');
                if ($rows.length <= 1) {
                    return;
                }

                $(this).closest('.input-group').remove();
                redistributeSplitPrices();
            });

            $(document).on('input', '#status-modal-split input[name="price[]"]', updateSplitRemainingHint);

            $(document).on('click', '#submitSplitForm', function(e) {
                e.preventDefault();

                var beauticians = [];
                var prices = [];
                var hasDuplicates = false;
                var hasMissingBeautician = false;

                $('#status-modal-split select[name="beautician[]"]').each(function() {
                    var value = $(this).val();
                    if (!value) {
                        hasMissingBeautician = true;
                        return;
                    }
                    if (beauticians.includes(value)) {
                        hasDuplicates = true;
                    } else {
                        beauticians.push(value);
                    }
                });

                if (hasMissingBeautician) {
                    alert('{{ __('Please select a beautician for each split row.') }}');
                    return;
                }

                if (hasDuplicates) {
                    alert('{{ __('Duplicate beautician entries are not allowed.') }}');
                    return;
                }

                $('#status-modal-split input[name="price[]"]').each(function() {
                    var priceValue = parseFloat($(this).val());
                    if (!isNaN(priceValue)) {
                        prices.push(priceValue);
                    }
                });

                if (prices.length === 0 || prices.some(function(price) {
                        return price <= 0 || isNaN(price);
                    })) {
                    alert('{{ __('All prices must be valid numbers greater than 0.') }}');
                    return;
                }

                var serviceAmount = getSplitServiceAmount();
                var splitTotal = prices.reduce(function(sum, price) {
                    return sum + price;
                }, 0);

                if (Math.abs(splitTotal - serviceAmount) > 0.01) {
                    alert('{{ __('Split total must equal the service amount.') }}');
                    return;
                }

                var data = {
                    beauticians: beauticians,
                    prices: prices,
                    booking_id: $('#modal_split_booking_id').val(),
                    beautician_id: $('#modal_split_beautician_id').val(),
                    service_name: $('#modal_service_name').text(),
                    service_started_at: $('#modal_service_started_at').val(),
                    line_total: serviceAmount,
                    start_date: $('#modal_commission_start_date').val(),
                    end_date: $('#modal_commission_end_date').val(),
                    commission_percentage: $('#modal_commission_percentage').val(),
                    commission_status: $('#modal_commission_status').val(),
                    service_id: $('#modal_service_id').val(),
                    booking_service_id: $('#modal_split_booking_service_id').val(),
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: '{{ route('api.service.split') }}',
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.errorcode == 0) {
                            $('#status-modal-split').modal('hide');
                            $('.modal-backdrop').remove();
                            refreshCompletedTab();
                        } else {
                            alert('{{ __('Error:') }} ' + (response.message || '{{ __('Unable to save split.') }}'));
                        }
                    },
                    error: function() {
                        alert('{{ __('Unable to save split. Please try again.') }}');
                    }
                });
            });
        });

        window.resetSplitEditModal = function() {
            clearSplitFieldRows();
            $('#modal_service_name').text('—');
            $('#modal_service_amount').text('0.00');
            $('#modal_split_assigned_beautician').text('—');
        };

        window.closeSplitViewModal = function() {
            $('#open-split-modal').modal('hide');
            $('#open-split-modal-rows').html('');
            $('#open-split-modal-total').text('0.00');
            $('#open-split-modal-commission-total').text('0.00');
            $('#open-split-modal-mismatch').hide().text('');
            $('#modal_split_booking_code').text('—');
            $('#modal_service_name1').text('—');
            $('#modal_service_amount1').text('0.00');
            $('#modal_assigned_beautician').text('—');
            $('#modal_split_commission_rate').text('—');
        };
    })();
</script>
