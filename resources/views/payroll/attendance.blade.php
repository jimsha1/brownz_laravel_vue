@extends('payroll.layouts.base')
<style>
    .attendance-page .list-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
    }

    .attendance-page .list-title {
        color: #1D1D1D;
        font-size: 1.53125rem;
        margin-bottom: 0;
        font-weight: 500;
    }

    .attendance-page .list-meta {
        font-size: 12px;
        color: #6c757d;
    }

    .brownz-table-list-v1.table th,
    .brownz-table-list-v.table td {
        border: 0 !important;
    }

    .check-in-out-capture {
        height: 40px;
        width: 40px;
        object-fit: cover;
        border-radius: 50%;
    }

    .full-screen-image {
        width: 100vw;
        height: 86vh;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .full-screen-image .full-screen-img-viwew {
        transform: rotate(90deg);
        transform-origin: center;
        display: block;
        width: auto;
        height: 100vw;
        max-height: 86vw;
        max-width: 100vh;
        object-fit: contain;
    }

    .attendance-hint {
        background: #f8f5ef;
        border: 1px solid #eadfce;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 0.875rem;
        color: #5c5346;
        margin-bottom: 16px;
    }

    .attendance-time-row {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .attendance-time-row .form-control {
        flex: 1;
    }

    .attendance-now-btn {
        white-space: nowrap;
        border: 1px solid #1d1d1d;
        background: #fff;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.8rem;
        cursor: pointer;
    }

    .attendance-now-btn:hover {
        background: #1d1d1d;
        color: #fff;
    }

    .attendance-now-btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

    .attendance-status-chip {
        display: inline-block;
        border-radius: 999px;
        padding: 3px 10px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.02em;
    }

    .attendance-status-chip.present {
        background: #e8f7ee;
        color: #1b7a3d;
    }

    .attendance-status-chip.absent {
        background: #fdecec;
        color: #b42318;
    }

    .attendance-status-chip.pending {
        background: #fff4e5;
        color: #b54708;
    }

    .attendance-status-chip.checked-in {
        background: #e8f0fe;
        color: #1a56db;
    }

    .attendance-status-chip.complete {
        background: #e8f7ee;
        color: #1b7a3d;
    }

    .attendance-emp-meta {
        font-size: 0.9rem;
        color: #6b7280;
        margin-bottom: 4px;
    }
</style>
@section('content')
    <div id="app" class="attendance-page">

        <div class="fbox-shadow mt-2 fd-card respo-margin-top">
            <div class="card-body">
                <h5 style="color: #1D1D1D; font-size: 1.53125rem;" class="mb-3">
                    {{ __('Attendance') }}
                </h5>

                <div class="row align-items-center br-row-gap-2">
                    <div class="col-lg-3 col-md-6">
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <input type="date" class="form-control brawnz-search-bar-in"
                                v-model="filter_date" id="filter_date"
                                title="{{ __('Date') }}"
                                @change="getattendanceList(1)">
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <select v-model="filter_employee_id" class="form-control brawnz-search-bar-in">
                                <option value="">{{ __('All Employees') }}</option>
                                <option v-for="emp in empList" :key="emp.emp_id" :value="String(emp.emp_id)">
                                    @{{ emp.name }}@{{ emp.emp_code ? ' · ' + emp.emp_code : '' }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <div class="search-box-bar-container jus-heigh-search-b">
                            <select v-model="filter_attendance_status" class="form-control brawnz-search-bar-in">
                                <option value="">{{ __('All Status') }}</option>
                                <option value="present">{{ __('Present') }}</option>
                                <option value="absent">{{ __('Absent') }}</option>
                                <option value="not_added">{{ __('Not Added') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-primary btn_dark-x mr-2"
                            v-on:click="getattendanceList(1)">
                            <i class="fa fa-search"></i> {{ __('Search') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary brownz-btn-yellow-d"
                            v-on:click="resetFilter()">
                            <i class="fa fa-refresh"></i> {{ __('Reset') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="fbox-shadow mt-3 fd-card respo-margin-top">

            <div class="m-3" style="overflow: auto">
                <div class="list-toolbar">
                    <div>
                        <p class="list-title">
                            {{ __('Attendance On') }} @{{ getDateFromCompleteFormat(filter_date) }}
                        </p>
                        <p class="list-meta mb-0" v-if="filteredAttendanceList.length">
                            @{{ filteredAttendanceList.length }} {{ __('employee(s)') }}
                        </p>
                    </div>
                </div>

                    <div width="850" height="auto" :scrollable="true" :adaptive="true"
                        class="modal fade brownz-custom-popup" id="attendanceModel" tabindex="-1"
                        aria-labelledby="incentiveModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content" style="border: none;border-radius: 24px;">
                                <div class="modal-header" style="padding: 10px">
                                    <h4 class="modal-title" id="modelHeading">@{{ modelHeading }}</h4>
                                    <button type="button" class="bootbox-close-button close" v-on:click="hide"
                                        data-dismiss="modal" aria-label="Close">
                                        ×
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form @submit.prevent="validateBeforeSubmit" v-if="!formSubmitted" method="post">

                                        <div class="attendance-emp-meta">
                                            @{{ employee_name }} [@{{ employee_code }}] · @{{ getDateFromCompleteFormat(filter_date) }}
                                        </div>

                                        <div class="attendance-hint">
                                            Save <strong>Check In</strong> first, then come back later and add
                                            <strong>Check Out</strong>. Both times are optional when marking Absent.
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 col-sm-12 form-group">
                                                <label for="check_in" class="form-label">
                                                    <strong>Check In</strong>
                                                    <span style="color:red" v-if="attendance_status === 'present'">*</span>
                                                </label>
                                                <div class="attendance-time-row">
                                                    <input type="time" class="form-control" v-model="check_in"
                                                        id="check_in" placeholder="Check In"
                                                        :disabled="attendance_status === 'absent'">
                                                    <button type="button" class="attendance-now-btn"
                                                        v-on:click="setCurrentTime('check_in')"
                                                        :disabled="attendance_status === 'absent'">
                                                        Now
                                                    </button>
                                                </div>
                                                <span v-if="formErrors['check_in']"
                                                    class="error text-danger">@{{ formErrors['check_in'][0] || formErrors['check_in'] }}</span>
                                                <p class="text-danger" v-if="clientErrors.check_in">
                                                    @{{ clientErrors.check_in }}</p>
                                            </div>

                                            <div class="col-md-6 col-sm-12 form-group">
                                                <label for="check_out" class="form-label">
                                                    <strong>Check Out</strong>
                                                    <small class="text-muted">(optional)</small>
                                                </label>
                                                <div class="attendance-time-row">
                                                    <input type="time" class="form-control" v-model="check_out"
                                                        id="check_out" placeholder="Check Out"
                                                        :disabled="attendance_status === 'absent' || !check_in">
                                                    <button type="button" class="attendance-now-btn"
                                                        v-on:click="setCurrentTime('check_out')"
                                                        :disabled="attendance_status === 'absent' || !check_in">
                                                        Now
                                                    </button>
                                                </div>
                                                <span v-if="formErrors['check_out']"
                                                    class="error text-danger">@{{ formErrors['check_out'][0] || formErrors['check_out'] }}</span>
                                                <p class="text-danger" v-if="clientErrors.check_out">
                                                    @{{ clientErrors.check_out }}</p>
                                                <small class="text-muted" v-if="!check_in && attendance_status === 'present'">
                                                    Add Check In before Check Out.
                                                </small>
                                            </div>

                                            <div class="col-md-6 col-sm-12 form-group mt-2">
                                                <label for="status" class="form-label"><strong>Status </strong><span
                                                        style="color:red">*</span></label>
                                                <select v-model="attendance_status" class="form-control"
                                                    @change="onStatusChange">
                                                    <option value="present">PRESENT</option>
                                                    <option value="absent">ABSENT</option>
                                                </select>
                                                <span v-if="formErrors['attendance_status']"
                                                    class="error text-danger">@{{ formErrors['attendance_status'][0] || formErrors['attendance_status'] }}</span>
                                                <p class="text-danger" v-if="clientErrors.attendance_status">
                                                    @{{ clientErrors.attendance_status }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="modal-footer" style="gap: 5px;">
                                            <button class="brownz-btn-yellow-d" type="button" v-on:click="hide">✖
                                                Cancel</button>
                                            <button type="submit" v-bind:disabled="submit_button_flag"
                                                class="btn_dark-x">
                                                <i class="fa fa-check"></i> @{{ submitButtonLabel }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <table class="table brownz-table-list-v1" style="over">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Emp Name</th>
                            <th scope="col">Leave Status</th>
                            <th scope="col">Check In</th>
                            <th scope="col">Check Out</th>
                            <th scope="col">Over Time</th>
                            <th scope="col">Reporting</th>
                            <th scope="col">Check In Image</th>
                            <th scope="col">Check Out Image</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-if="!filteredAttendanceList || !filteredAttendanceList.length">
                            <tr>
                                <td colspan="10">{{ __('No data found') }}</td>
                            </tr>
                        </template>
                        <template v-else v-for="(data,index) in filteredAttendanceList">

                            <tr>
                                <th scope="row">@{{ (pagination.per_page * (pagination.current_page - 1)) + index + 1 }}</th>

                                <td>@{{ data.employee_name }}[@{{ data.employee_code }}]</td>

                                <td v-if="data.leave_data && data.leave_data.length > 0">
                                    <span style="color: red;">
                                        @{{ data.leave_data[0].leave_mode === 'week_off' ? 'Week Off' :
    (data.leave_data[0].leave_type === 'full' && data.leave_data[0].leave_mode !== 'week_off') ? 'Leave' :
    (data.leave_data[0].leave_type === 'first_half') ? 'First Half' :
    (data.leave_data[0].leave_type === 'second_half') ? 'Second Half' : '' }}
                                    </span>
                                </td>
                                <td v-else>----</td>

                                <td>
                                    <span v-if="hasValidTime(data.check_in)">@{{ convertTo12Hour(data.check_in) }}</span>
                                    <span v-else style="color:#9ca3af;">--:--</span>
                                </td>

                                <td>
                                    <span v-if="hasValidTime(data.check_out)">@{{ convertTo12Hour(data.check_out) }}</span>
                                    <span v-else style="color:#9ca3af;">--:--</span>
                                </td>

                                <td>
                                    <span v-if="data.overtime != null">@{{ data.overtime }}</span>
                                    <span v-else style="color:#9ca3af;">--:--</span>
                                </td>

                                <td>
                                    <template v-if="data.attendance_status == 'present' || data.attendance_status == 'absent'">
                                        <span class="attendance-status-chip"
                                            v-bind:class="getPunchStateClass(data)">
                                            @{{ getPunchStateLabel(data) }}
                                        </span>
                                        <div style="font-size: 0.75rem; color: #6b7280; margin-top: 4px;">
                                            @{{ data.attendance_status.toUpperCase() }}
                                            <span v-if="data.attendance_updated">
                                                · @{{ getDateTimeFromCompleteFormat(data.attendance_updated) }}
                                            </span>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <span class="attendance-status-chip pending">Not Added</span>
                                    </template>
                                </td>
                                <td v-if="data.check_in_image">
                                    <a href="javascript:void(0)" v-on:click="showImageFull(data.check_in_image)"><img
                                            class="check-in-out-capture" :src="getImageUrl(data.check_in_image)"
                                            alt=""></a>
                                </td>
                                <td v-else>
                                    <span style="color: #9ca3af;">—</span>
                                </td>
                                <td v-if="data.check_out_image">
                                    <a href="javascript:void(0)" v-on:click="showImageFull(data.check_out_image)">
                                        <img class="check-in-out-capture" :src="getImageUrl(data.check_out_image)"
                                            alt=""></a>
                                </td>
                                <td v-else>
                                    <span style="color: #9ca3af;">—</span>
                                </td>
                                <td>
                                    <a class="brownz-btn-yellow-d" href="javascript:void(0)"
                                        v-on:click="popupModal(data)">
                                        <i class="fas fa-pencil-alt"></i>
                                        @{{ getActionLabel(data) }}
                                    </a>
                                </td>
                            </tr>

                        </template>
                    </tbody>
                </table>

            </div>

            <nav aria-label="pagination">
                <ul class="pagination justify-content-end">
                    <li class="page-item" v-if="pagination.current_page > 1">
                        <a class="page-link" href="#" aria-label="Previous"
                            @click.prevent="changePage(pagination.current_page - 1)">
                            <span aria-hidden="true">«</span>
                        </a>
                    </li>
                    <li class="page-item" v-for="page in pagesNumber"
                        v-bind:class="[ page == isActived ? 'active' : '']">
                        <a class="page-link" href="#" @click.prevent="changePage(page)">@{{ page }}</a>
                    </li>
                    <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                        <a class="page-link" href="#" aria-label="Next"
                            @click.prevent="changePage(pagination.current_page + 1)">
                            <span aria-hidden="true">»</span>
                        </a>
                    </li>
                </ul>
            </nav>

        </div>
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="max-width: 100%;">
                <div class="modal-content listing-popup">
                    <div class="modal-body">
                        <div class="full-screen-image">
                            <img class="full-screen-img-viwew" :src="imageToShow" alt="imageToShow">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="primary-btn" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        Vue.use(window["vue-js-modal"].default);
        var app = new Vue({
            el: '#app',

            data: {
                loading: false,
                base_url: "{{ url('/') }}",
                attendanceList: [],
                empList: [],
                modelHeading: '',
                attendence_id: '',
                emp_id: '',
                employee_id: '',
                employee_code: '',
                employee_name: '',
                date: '',
                check_in: '',
                check_out: '',
                attendance_status: 'present',
                filter_employee_id: '',
                filter_date: '',
                filter_attendance_status: '',
                formSubmitted: false,
                submit_button_flag: false,
                page_length: '100',
                pagination: {
                    total: 0,
                    per_page: 2,
                    from: 1,
                    to: 0,
                    current_page: 1
                },
                offset: 4,
                formErrors: {},
                clientErrors: {},
                imageToShow: '',
            },

            computed: {
                isActived: function() {
                    return app.pagination.current_page;
                },
                pagesNumber: function() {
                    if (!this.pagination.to) {
                        return [];
                    }
                    var from = this.pagination.current_page - this.offset;
                    if (from < 1) {
                        from = 1;
                    }
                    var to = from + (this.offset * 2);
                    if (to >= this.pagination.last_page) {
                        to = this.pagination.last_page;
                    }
                    var pagesArray = [];
                    while (from <= to) {
                        pagesArray.push(from);
                        from++;
                    }
                    return pagesArray;
                },
                submitButtonLabel: function() {
                    if (this.attendance_status === 'absent') {
                        return 'Mark Absent';
                    }
                    if (this.check_in && this.check_out) {
                        return 'Save Check In & Out';
                    }
                    if (this.check_in && !this.check_out) {
                        return 'Save Check In';
                    }
                    return 'Save Attendance';
                },
                filteredAttendanceList: function() {
                    var list = Array.isArray(this.attendanceList) ? this.attendanceList : [];
                    var empId = this.filter_employee_id ? String(this.filter_employee_id) : '';
                    var status = this.filter_attendance_status || '';

                    return list.filter(function(row) {
                        if (empId && String(row.employee_id) !== empId) {
                            return false;
                        }

                        if (status === 'not_added') {
                            return !row.attendance_status;
                        }

                        if (status && row.attendance_status !== status) {
                            return false;
                        }

                        return true;
                    });
                },
            },

            methods: {
                validateBeforeSubmit(e) {
                    this.clientErrors = {};
                    let hasError = false;

                    if (!this.attendance_status) {
                        this.$set(this.clientErrors, 'attendance_status', 'Status is required.');
                        hasError = true;
                    }

                    if (this.attendance_status === 'present') {
                        if (!this.check_in) {
                            this.$set(this.clientErrors, 'check_in', 'Check In is required for Present status.');
                            hasError = true;
                        }

                        if (this.check_out && !this.check_in) {
                            this.$set(this.clientErrors, 'check_out', 'Enter Check In before Check Out.');
                            hasError = true;
                        }

                        if (this.check_in && this.check_out && this.check_out <= this.check_in) {
                            this.$set(this.clientErrors, 'check_out', 'Check Out must be after Check In.');
                            hasError = true;
                        }
                    }

                    if (!hasError) {
                        this.submitForm();
                    }
                },

                submitForm() {
                    this.addEditSubmitForm();
                },

                changePage: function(page) {
                    this.pagination.current_page = page;
                    app.getattendanceList(page);
                },

                handleChangePage(e) {
                    app.getattendanceList();
                },

                show: function() {
                    $('#attendanceModel').modal('show');
                },

                hide: function() {
                    $('#attendanceModel').modal('hide');
                },

                onStatusChange: function() {
                    if (this.attendance_status === 'absent') {
                        this.check_in = '';
                        this.check_out = '';
                    }
                    this.clientErrors = {};
                },

                setCurrentTime: function(field) {
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    this[field] = hours + ':' + minutes;
                    this.clientErrors = {};
                },

                hasValidTime: function(time) {
                    return time && time !== '--:--' && time !== 'null';
                },

                getPunchStateLabel: function(data) {
                    if (data.attendance_status === 'absent') {
                        return 'Absent';
                    }
                    if (this.hasValidTime(data.check_in) && this.hasValidTime(data.check_out)) {
                        return 'Checked Out';
                    }
                    if (this.hasValidTime(data.check_in)) {
                        return 'Checked In';
                    }
                    return 'Pending';
                },

                getPunchStateClass: function(data) {
                    if (data.attendance_status === 'absent') {
                        return 'absent';
                    }
                    if (this.hasValidTime(data.check_in) && this.hasValidTime(data.check_out)) {
                        return 'complete';
                    }
                    if (this.hasValidTime(data.check_in)) {
                        return 'checked-in';
                    }
                    return 'pending';
                },

                getActionLabel: function(data) {
                    if (!data.attendence_id) {
                        return 'Mark Attendance';
                    }
                    if (data.attendance_status === 'absent') {
                        return 'Edit Attendance';
                    }
                    if (this.hasValidTime(data.check_in) && !this.hasValidTime(data.check_out)) {
                        return 'Add Check Out';
                    }
                    if (this.hasValidTime(data.check_in) && this.hasValidTime(data.check_out)) {
                        return 'Edit Attendance';
                    }
                    return 'Add Check In';
                },

                addEditSubmitForm: function() {
                    app.submit_button_flag = true;
                    url = "api/attendance/store";

                    let formData = new FormData();
                    formData.append('attendence_id', this.attendence_id || '');
                    formData.append('employee_id', this.employee_id);
                    formData.append('employee_code', this.employee_code);
                    formData.append('employee_name', this.employee_name);
                    formData.append('date', this.filter_date);
                    formData.append('check_in', this.attendance_status === 'absent' ? '' : (this.check_in || ''));
                    formData.append('check_out', this.attendance_status === 'absent' ? '' : (this.check_out || ''));
                    formData.append('attendance_status', this.attendance_status);

                    axios.post(url, formData).then(function(response) {
                        if (response.data.errorcode == 0) {
                            app.loading = false;
                            Vue.$toast.success(response.data.errormessage, 'Success Alert');
                            app.submit_button_flag = false;
                            app.formSubmitted = true;
                            app.formErrors = {};
                            app.clientErrors = {};
                            $('#attendanceModel').modal('hide');
                            app.getattendanceList(app.pagination.current_page);
                        } else {
                            app.loading = false;
                            Vue.$toast.error(response.data.errormessage || 'Something went wrong.', 'Danger Alert');
                            app.submit_button_flag = false;
                        }
                    }).catch(function(err) {
                        app.submit_button_flag = false;
                        if (err.response && err.response.data && err.response.data.errors) {
                            app.formErrors = err.response.data.errors;
                        } else {
                            Vue.$toast.error('Something went wrong.', 'Danger Alert');
                        }
                    });
                },

                getCurrentDate() {
                    return new Date().toISOString().slice(0, 10);
                },

                convertTo12Hour: function(time24h) {
                    if (!this.hasValidTime(time24h)) {
                        return '--:--';
                    }

                    let parts = String(time24h).split(':');
                    let hours = parseInt(parts[0], 10);
                    let minutes = parts[1] || '00';
                    let modifier = 'AM';

                    if (hours >= 12) {
                        modifier = 'PM';
                        hours = hours % 12 || 12;
                    } else if (hours === 0) {
                        hours = 12;
                    }

                    hours = hours < 10 ? '0' + hours : String(hours);
                    return hours + ':' + minutes + ' ' + modifier;
                },

                getDateTimeFromCompleteFormat: function(dateTimeString) {
                    const dateTime = new Date(dateTimeString);
                    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July',
                        'August', 'September', 'October', 'November', 'December'
                    ];
                    const year = dateTime.getFullYear();
                    const month = monthNames[dateTime.getMonth()];
                    const day = String(dateTime.getDate()).padStart(2, '0');

                    let hours = dateTime.getHours();
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12;
                    const minutes = String(dateTime.getMinutes()).padStart(2, '0');
                    const seconds = String(dateTime.getSeconds()).padStart(2, '0');

                    return `${month}-${day}/${year} : ${hours}:${minutes}:${seconds} ${ampm}`;
                },

                getDateFromCompleteFormat: function(dateTimeString) {
                    if (!dateTimeString) {
                        return '';
                    }
                    const dateTime = new Date(dateTimeString);
                    const monthNames = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JULY',
                        'AUG', 'SEP', 'OCT', 'NOV', 'DEC'
                    ];
                    const year = dateTime.getFullYear();
                    const month = monthNames[dateTime.getMonth()];
                    const day = String(dateTime.getDate()).padStart(2, '0');
                    return `${month}-${day}-${year}`;
                },

                popupModal: function(data) {
                    app.formSubmitted = false;
                    app.formErrors = {};
                    app.clientErrors = {};

                    app.employee_id = data.employee_id;
                    app.employee_code = data.employee_code;
                    app.employee_name = data.employee_name;

                    if (data.attendence_id > 0) {
                        app.attendence_id = data.attendence_id;
                        app.check_in = this.hasValidTime(data.check_in) ? String(data.check_in).substring(0, 5) : '';
                        app.check_out = this.hasValidTime(data.check_out) ? String(data.check_out).substring(0, 5) : '';
                        app.attendance_status = data.attendance_status || 'present';
                        app.modelHeading = this.getActionLabel(data) + ' — ' + data.employee_name;
                    } else {
                        app.attendence_id = '';
                        app.check_in = '';
                        app.check_out = '';
                        app.attendance_status = 'present';
                        app.modelHeading = 'Mark Attendance — ' + data.employee_name;
                    }

                    $('#attendanceModel').modal('show');
                },

                closeAddEditForm: function() {
                    app.attendence_id = '';
                    app.employee_id = '';
                    app.employee_name = '';
                    app.employee_code = '';
                    app.check_in = '';
                    app.check_out = '';
                    app.attendance_status = 'present';
                    app.formSubmitted = false;
                    app.formErrors = {};
                    app.clientErrors = {};
                    $('#attendanceModel').modal('hide');
                    app.getattendanceList();
                },

                getattendanceList: function(page = 1) {
                    url = "api/attendance/list";

                    axios.post(url, {
                        "page": page,
                        "page_length": this.page_length,
                        "employee_id": this.filter_employee_id,
                        "date": this.filter_date,
                        "attendance_status": this.filter_attendance_status
                    }).then(function(response) {
                        app.attendanceList = Object.values(response.data.data.data || {});
                        app.pagination = response.data.data.pagination;
                        app.syncEmpListFromAttendance();
                    });
                },

                syncEmpListFromAttendance: function() {
                    if (this.empList && this.empList.length) {
                        return;
                    }
                    var seen = {};
                    var employees = [];
                    (this.attendanceList || []).forEach(function(row) {
                        var id = row.employee_id;
                        if (!id || seen[id]) {
                            return;
                        }
                        seen[id] = true;
                        employees.push({
                            emp_id: id,
                            name: row.employee_name,
                            emp_code: row.employee_code,
                        });
                    });
                    employees.sort(function(a, b) {
                        return String(a.name || '').localeCompare(String(b.name || ''));
                    });
                    this.empList = employees;
                },

                getEmployeeList: function() {
                    axios.post("api/employee/list", {
                        status: 'active',
                        page_length: 500,
                    }).then(function(response) {
                        app.empList = response.data.data.data.data || [];
                    }).catch(function() {
                        // Fallback to attendance-derived list after first load.
                    });
                },

                resetFilter: function() {
                    app.filter_employee_id = '';
                    app.filter_date = app.getCurrentDate();
                    app.filter_attendance_status = '';
                    app.getattendanceList(1);
                },

                getImageUrl(imagePath) {
                    const imageUrl = encodeURI(this.base_url + '/storage/' + imagePath);
                    return imagePath ? imageUrl : '';
                },

                showImageFull(image) {
                    this.imageToShow = this.getImageUrl(image);
                    $('#imageModal').modal('show');
                }
            },

            mounted() {
                this.filter_date = this.getCurrentDate();
                this.getEmployeeList();
                this.getattendanceList();
            }
        });
    </script>
@endsection
