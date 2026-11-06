@extends('layouts.vcapp')
<style>
    .best-performer {
        text-align: center;
    }

    .best-performer .ds-performer-content>p {
        font-size: 18px;
        font-weight: 500;
        margin-bottom: 1px;
        margin-top: 14px;
    }

    .best-performer .performer-profile-containr {
        position: relative;
    }

    .best-performer .performer-profile-containr .profile-perf {
        width: 100%;
        margin-top: 40px;
        object-fit: cover;
        aspect-ratio: 1;
        border-radius: 50%;
        border: 5px solid #e6cd7f;
    }

    .best-performer .performer-profile-containr {
        position: relative;
        max-width: 180px;
        width: 100%;
        margin: 0 auto;
    }

    .best-performer .performer-profile-containr .king-top {
        width: 60px;
        object-fit: contain;
        position: absolute;
        left: 50%;
        top: 11px;
        transform: translateX(-50%);
    }

    .dashboard-calendar-card {
        margin-bottom: 1.25rem;
        overflow: visible;
    }

    .dashboard-calendar-card .calendar-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px 0;
        flex-wrap: wrap;
    }

    .dashboard-calendar-card .calendar-card-header h6 {
        margin: 0;
        font-weight: 600;
    }

    .dashboard-calendar-legend {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        font-size: 12px;
        color: #6c757d;
    }

    .dashboard-calendar-legend span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .dashboard-calendar-legend i {
        width: 10px;
        height: 10px;
        border-radius: 2px;
        display: inline-block;
    }

    .dashboard-charts-row .fd-card {
        height: 100%;
    }

    .dashboard-charts-row .chart {
        position: relative;
        width: 100%;
        height: 220px;
        min-height: 220px;
    }

    .dashboard-layout-row {
        align-items: flex-start;
    }

    .dashboard-calendar-main {
        min-width: 0;
    }

    .dashboard-calendar-main .dashbord-main-calendar {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
        margin: 8px 12px 16px !important;
    }

    .dashboard-calendar-main .dashbord-main-calendar .fc {
        max-width: 100%;
    }

    .dashboard-calendar-main .dashbord-main-calendar .fc-view-container,
    .dashboard-calendar-main .dashbord-main-calendar .fc-view,
    .dashboard-calendar-main .dashbord-main-calendar .fc-head,
    .dashboard-calendar-main .dashbord-main-calendar table {
        width: 100% !important;
    }

    /* Compact booking chips — one-line name · time */
    #salon-booking-calendar.dashbord-main-calendar .fc-day-grid-event,
    #full-calendar.dashbord-main-calendar .fc-day-grid-event,
    #salon-booking-calendar.dashbord-main-calendar .fc-event,
    #full-calendar.dashbord-main-calendar .fc-event,
    .dashbord-main-calendar .fc-day-grid-event,
    .dashbord-main-calendar .fc-event-container .fc-event {
        border: none !important;
        border-radius: 4px !important;
        padding: 2px 5px !important;
        margin: 1px 2px 0 !important;
        min-height: 18px !important;
        height: auto !important;
        line-height: 1.25 !important;
        box-sizing: border-box !important;
    }

    .dashbord-main-calendar .fc-day-grid-event .fc-content {
        padding: 0 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    .dashbord-main-calendar .fc-title,
    .dashbord-main-calendar .fc-event-container .fc-content .fc-title {
        font-size: 11px !important;
        font-weight: 600 !important;
        line-height: 1.25 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        display: block !important;
    }

    .dashbord-main-calendar .fc-booking-line {
        display: block;
        font-size: 11px !important;
        font-weight: 600 !important;
        line-height: 1.25 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        color: #fff !important;
    }

    .dashbord-main-calendar .fc-booking-line .fc-booking-time {
        display: inline;
        font-size: 10px !important;
        font-weight: 500 !important;
        opacity: 0.95;
    }

    .dashbord-main-calendar .fc-event-container .fc-content .fc-time {
        display: none !important;
    }

    .dashbord-main-calendar .fc-more {
        font-size: 11px !important;
        font-weight: 600 !important;
        color: #1d1d1d !important;
        margin-top: 2px !important;
        padding: 1px 4px !important;
        display: inline-block;
    }

    .dashbord-main-calendar .fc-day-number {
        font-size: 12px !important;
        padding: 4px 6px !important;
    }

    .dashbord-main-calendar .fc-row .fc-content-skeleton td {
        vertical-align: top;
    }

    .fc-more-popover {
        max-width: 280px;
        width: 260px;
        z-index: 1050 !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
        border-radius: 8px;
        overflow: hidden;
    }

    .fc-more-popover .fc-header {
        padding: 8px 10px !important;
        background: #E6CD7F !important;
        font-size: 12px !important;
        font-weight: 600 !important;
    }

    .fc-more-popover .fc-event {
        margin: 3px 4px !important;
        padding: 3px 6px !important;
    }

    .fc-more-popover .fc-event .fc-booking-line {
        white-space: normal !important;
    }

    .dashboard-side-performer {
        min-width: 0;
    }

    .dashboard-side-performer .fd-card {
        height: 100%;
    }

    .dashboard-side-performer .best-performer .performer-profile-containr {
        max-width: 140px;
    }

    .dashboard-secondary-calendar {
        margin-top: 0.25rem;
    }

    @media (max-width: 991.98px) {
        .dashboard-side-performer {
            margin-top: 1rem;
        }
    }

    .dashbord-main-calendar .fc-resetView-button,
    .dashbord-main-calendar .fc-toolbar .fc-resetView-button {
        background: #E6CD7F !important;
        background-image: none !important;
        color: #1d1d1d !important;
        border: none !important;
        border-radius: 40px !important;
        padding: 10px 16px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        height: auto !important;
        box-shadow: none !important;
        margin-left: 8px !important;
    }

    .dashbord-main-calendar .fc-resetView-button:hover {
        background: #d4bb6a !important;
    }

    /* Clean day/week list-style view (basicDay / basicWeek) */
    .dashbord-main-calendar .fc-basicDay-view .fc-day-grid-event,
    .dashbord-main-calendar .fc-basicWeek-view .fc-day-grid-event {
        padding: 8px 10px !important;
        margin: 4px 6px !important;
        border-radius: 8px !important;
        min-height: 36px !important;
    }

    .dashbord-main-calendar .fc-basicDay-view .fc-booking-line,
    .dashbord-main-calendar .fc-basicWeek-view .fc-booking-line {
        white-space: normal !important;
        font-size: 13px !important;
        line-height: 1.35 !important;
    }

    .dashbord-main-calendar .fc-basicDay-view .fc-booking-line .fc-booking-time,
    .dashbord-main-calendar .fc-basicWeek-view .fc-booking-line .fc-booking-time {
        font-size: 12px !important;
    }

    .dashbord-main-calendar .fc-basicDay-view .fc-day-number,
    .dashbord-main-calendar .fc-basicWeek-view .fc-day-number {
        font-size: 14px !important;
        padding: 8px !important;
    }

    .dashbord-main-calendar .fc-basicDay-view .fc-day-grid .fc-row {
        min-height: 320px !important;
    }


    /* Compact booking popup (salon + makeup) */
    #customPopup.calender_event-popup-d,
    #salonCustomPopup.calender_event-popup-d {
        max-width: 420px;
        width: calc(100% - 32px);
        padding: 16px 16px 14px !important;
        border-radius: 16px !important;
        border: 1px solid #e8e8ec !important;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.14);
    }
    .cal-popup-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding-right: 28px;
        margin-bottom: 12px;
    }
    .cal-popup-header .popup-username {
        font-size: 17px;
        font-weight: 600;
        line-height: 1.25;
        margin: 0;
        word-break: break-word;
    }
    .cal-popup-header .popup-phone {
        font-size: 13px;
        color: #666;
        margin: 4px 0 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .cal-popup-header .popup-phone i {
        font-size: 12px;
        color: #999;
    }
    .cal-popup-code {
        text-align: right;
        flex-shrink: 0;
    }
    .cal-popup-code-label {
        font-size: 11px;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin: 0 0 2px;
    }
    .cal-popup-code-value {
        font-size: 13px;
        font-weight: 600;
        color: #1d1d1d;
        margin: 0;
        background: #f4f4f6;
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-block;
    }
    .cal-popup-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        padding-top: 12px;
        border-top: 1px solid #f0f0f3;
        margin-bottom: 12px;
    }
    .cal-popup-chip {
        margin: 0;
        font-size: 12px;
        font-weight: 500;
        color: #3a7aa8;
        background: rgba(82, 168, 217, 0.14);
        padding: 8px 10px;
        border-radius: 8px;
        line-height: 1.35;
        min-width: 0;
        overflow-wrap: anywhere;
    }
    .cal-popup-chip strong {
        display: block;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #6a8fa8;
        margin-bottom: 2px;
    }
    .cal-popup-chip.full {
        grid-column: 1 / -1;
    }
    #customPopup .popup-beautytion-container,
    #salonCustomPopup .popup-beautytion-container {
        padding: 8px 10px;
        border-radius: 10px;
        margin-bottom: 12px;
        gap: 8px;
    }
    #customPopup .beauty-profile,
    #salonCustomPopup .beauty-profile {
        width: 28px;
        height: 28px;
    }
    #customPopup .popup-beautytion-name,
    #salonCustomPopup .popup-beautytion-name {
        font-size: 13px;
    }
    .cal-popup-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
    }
    #customPopup .popup-time-container,
    #salonCustomPopup .popup-time-container {
        margin-top: 0;
        justify-content: flex-start;
    }
    #customPopup .popup-datetime-slot,
    #salonCustomPopup .popup-datetime-slot {
        font-size: 12px;
        padding: 6px 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    #customPopup .popup-datetime-slot span,
    #salonCustomPopup .popup-datetime-slot span {
        font-size: 12px;
    }
    #customPopup .close-btn,
    #salonCustomPopup .close-btn {
        right: 10px;
        top: 10px;
        width: 22px;
        height: 22px;
        font-size: 13px;
    }
    .btn-open-booking {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 12px;
        border-radius: 8px;
        background: #E6CD7F;
        color: #23282c;
        font-weight: 600;
        font-size: 12px;
        text-decoration: none;
        border: none;
        white-space: nowrap;
    }
    .btn-open-booking:hover {
        background: #d4bb6a;
        color: #23282c;
        text-decoration: none;
    }
</style>
@section('content')
    @if (Session::has('message'))
        <div class="alert alert-primary" role="alert">
            {{ Session::get('message') }}
        </div>
    @endif

    @if (Session::has('error'))
        <div class="alert alert-danger" role="alert">
            {{ Session::get('error') }}
        </div>
    @endif

    {{-- 1. Charts on top --}}
    <div class="row dashboard-charts-row">
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="fbox-shadow fd-card">
                <div class="card-body">
                    <h6 class="mb-0">Daily Sale</h6>
                    <div class="chart">
                        <canvas id="chart-bars" class="chart-canvas" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="fbox-shadow fd-card">
                <div class="card-body">
                    <h6 class="mb-0">Monthly Sales</h6>
                    <div class="chart">
                        <canvas id="chart-line" class="chart-canvas" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Advance & Upcoming Bookings (full wide) --}}
    <div class="row mt-2">
        <div class="col-12 dashboard-calendar-main">
            <div class="fbox-shadow fd-card dashboard-calendar-card">
                <div class="calendar-card-header">
                    <h6>Advance & Upcoming Bookings</h6>
                    <div class="dashboard-calendar-legend">
                        <span><i style="background:#4CAF50"></i> Advance</span>
                        <span><i style="background:#2196F3"></i> Upcoming</span>
                        <span><i style="background:#F44336"></i> Expired</span>
                        <span><i style="background:#9E9E9E"></i> Draft</span>
                    </div>
                </div>
                <div id="salon-booking-calendar" class="dashbord-main-calendar"></div>
            </div>
        </div>
    </div>

    {{-- 3. Makeup Bookings (wide) + Best Performer (side) --}}
    <div class="row dashboard-layout-row mt-2 dashboard-secondary-calendar">
        <div class="col-lg-9 col-xl-9 dashboard-calendar-main">
            <div class="fbox-shadow fd-card dashboard-calendar-card">
                <div class="calendar-card-header">
                    <h6>Makeup Bookings</h6>
                </div>
                <div id="full-calendar" class="m-2 dashbord-main-calendar"></div>
            </div>
        </div>

        <div class="col-lg-3 col-xl-3 dashboard-side-performer">
            <div class="fbox-shadow fd-card">
                <div class="card-body">
                    <div class="best-performer">
                        <h6 class="mb-0">Best Performer of the Month</h6>

                        @php
                            $validPerformers = $bestPerformers->filter(function ($performer) {
                                return $performer->total_sales > 0;
                            });
                        @endphp

                        @if ($validPerformers->isNotEmpty())
                            @foreach ($validPerformers as $performer)
                                <div class="performer-profile-containr mb-3">
                                    <img class="profile-perf" width="100%"
                                        src="{{ $performer->profile_photo_path
                                            ? asset('storage/' . $performer->profile_photo_path)
                                            : asset('admin/images/profile/no-profile-image.jpg') }}">
                                    <img class="king-top" src="{{ asset('admin/images/profile/king.png') }}">
                                </div>
                                <div class="ds-performer-content mb-4">
                                    <p>{{ $performer->beautician_name }}</p>
                                    <h6>Total Sale: ₹{{ number_format((float) $performer->total_sales, 2) }}</h6>
                                </div>
                            @endforeach
                        @else
                            <div class="ds-performer-content">
                                <p>No performer this month</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="{{ asset('assets/dashboard/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/plugins/chartjs.min.js') }}"></script>
    <script>
        const salesData = <?php echo json_encode(array_values($salesByDay)); ?>;

        const daysOfWeek = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
        // JS getDay(): Sun=0..Sat=6 â†’ map to Mon-first chart index (Sun = 6)
        const jsDay = new Date().getDay();
        const currentDayIndex = jsDay === 0 ? 6 : jsDay - 1;

        // Colors array: Default is light yellow, current day is green
        const backgroundColors = daysOfWeek.map((_, index) => (index === currentDayIndex ? "rgba(25, 153, 0, 1)" :
            "#E6CD7F"));

        var dailyChartCanvas = document.getElementById("chart-bars");
        if (!dailyChartCanvas || typeof Chart === "undefined") {
            console.error("Daily sales chart: Chart.js or canvas element is missing.");
        } else {
        var ctx = dailyChartCanvas.getContext("2d");
        var dailyMax = Math.max.apply(null, salesData.concat([0]));

        new Chart(ctx, {
            type: "bar",
            data: {
                labels: ["M", "T", "W", "T", "F", "S", "S"],
                datasets: [{
                    label: "Sales",
                    tension: 0.4,
                    borderWidth: 0,
                    borderRadius: 4,
                    borderSkipped: false,
                    backgroundColor: backgroundColors,
                    data: salesData,
                    barThickness: 'flex'
                }, ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5],
                            color: '#e5e5e5'
                        },
                        ticks: {
                            suggestedMin: 0,
                            suggestedMax: dailyMax > 0 ? Math.ceil(dailyMax * 1.15) : 500,
                            beginAtZero: true,
                            padding: 10,
                            font: {
                                size: 14,
                                lineHeight: 2
                            },
                            color: "#23282c",
                            callback: function(value) {
                                return '\u20B9 ' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            color: '#23282c',
                            padding: 10,
                            font: {
                                size: 14,
                                lineHeight: 2
                            }
                        }
                    },
                },
            },
        });
        }


        const monthLabels = @json(array_values($months));

        var ctx2 = document.getElementById("chart-line").getContext("2d");

        new Chart(ctx2, {
            type: "line",
            // data: {
            //     // labels: ["J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D"],
            //     datasets: [{
            //       /*  label: "Sales",
            //         tension: 0,
            //         borderWidth: 2,
            //         pointRadius: 3,
            //         pointBackgroundColor: "#E6CD7F",
            //         pointBorderColor: "transparent",
            //         borderColor: "#E6CD7F",
            //         backgroundColor: "transparent",
            //         fill: true,
            //         data: monthlySalesData,
            //         maxBarThickness: 6*/
            //         label: "{{ $currentYear }}",
            //         data: @json($currentYearData)

            //     },
            //     {
            //     label: "{{ $previousYear }}",
            //     data: @json($previousYearData)
            // }
            // ]

            // },
            data: {
                labels: monthLabels,
                datasets: [{
                        label: "{{ $currentYear }}",
                        data: @json($currentYearData),
                        borderColor: "#4CAF50",
                        backgroundColor: "transparent",
                        tension: 0.4,
                        pointRadius: 3
                    },
                    {
                        label: "{{ $previousYear }}",
                        data: @json($previousYearData),
                        borderColor: "#2196F3",
                        backgroundColor: "transparent",
                        tension: 0.4,
                        pointRadius: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                // const fullMonths = ["January", "February", "March", "April", "May", "June",
                                //     "July", "August", "September", "October", "November", "December"
                                // ];
                                // return fullMonths[context[0].dataIndex];
                                return context[0].label;
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5],
                            color: '#e5e5e5'
                        },
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {
                                return '\u20B9 ' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            color: "#23282c",
                            padding: 10,
                            font: {
                                size: 14,
                                lineHeight: 2
                            }
                        }
                    }
                }
            },
        });
    </script>

    <script>
        var booking = @json($events);
        var salonBooking = @json($salonEvents);
        var activeSalonBookingUrl = null;

        var jq = jQuery.noConflict();

        function escapeHtml(value) {
            return String(value == null ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function formatDisplayName(name) {
            name = String(name || '').trim();
            if (!name) {
                return 'Booking';
            }
            // Title-case only when the whole name is ALL CAPS
            if (name === name.toUpperCase() && /[A-Z]/.test(name)) {
                return name.toLowerCase().replace(/\b\w/g, function(ch) {
                    return ch.toUpperCase();
                });
            }
            return name;
        }

        function renderCompactBookingEvent(event, element) {
            var name = formatDisplayName(
                event.client_name || (event.title || '').split(' - ')[0] || 'Booking'
            );
            var time = event.time_slot || '';
            if ((!time || time === '—') && (event.title || '').indexOf(' - ') !== -1) {
                time = (event.title || '').split(' - ').slice(1).join(' - ');
            }
            if (time === '—') {
                time = '';
            }

            var label = time
                ? (escapeHtml(name) + ' <span class="fc-booking-time">· ' + escapeHtml(time) + '</span>')
                : escapeHtml(name);

            element.find('.fc-title').html(
                '<span class="fc-booking-line" title="' + escapeHtml(time ? (name + ' · ' + time) : name) + '">' +
                    label +
                '</span>'
            );
            element.find('.fc-time').hide();
            element.attr('title', time ? (name + ' · ' + time) : name);
        }

        function buildDashboardCalendarOptions(calendarSelector, events, onEventClick) {
            return {
                header: {
                    left: 'prev,next today resetView',
                    center: 'title',
                    right: 'month,basicWeek,basicDay',
                },
                customButtons: {
                    resetView: {
                        text: 'Reset',
                        click: function() {
                            var cal = jq(calendarSelector);
                            cal.fullCalendar('changeView', 'month');
                            cal.fullCalendar('today');
                        }
                    }
                },
                defaultView: 'month',
                height: 'auto',
                contentHeight: 'auto',
                aspectRatio: calendarSelector === '#full-calendar' ? 1.7 : 1.55,
                handleWindowResize: true,
                eventLimit: 4,
                eventLimitText: 'more',
                eventLimitClick: function(cellInfo) {
                    jq(calendarSelector).fullCalendar('changeView', 'basicDay', cellInfo.date);
                },
                views: {
                    month: {
                        eventLimit: 4,
                        buttonText: 'Month'
                    },
                    basicWeek: {
                        eventLimit: false,
                        buttonText: 'Week'
                    },
                    basicDay: {
                        eventLimit: false,
                        buttonText: 'Day'
                    }
                },
                buttonText: {
                    today: 'Today',
                    month: 'Month',
                    week: 'Week',
                    day: 'Day'
                },
                eventTextColor: '#fff',
                events: events,
                selectable: false,
                selectHelper: false,
                editable: false,
                eventClick: onEventClick,
                eventRender: renderCompactBookingEvent,
            };
        }

        jq(document).ready(function() {
            jq('#salon-booking-calendar').fullCalendar(buildDashboardCalendarOptions(
                '#salon-booking-calendar',
                salonBooking,
                function(event, jsEvent) {
                    jsEvent.preventDefault();
                    activeSalonBookingUrl = event.url || null;

                    document.getElementById('salonPopupTitle').innerText = event.client_name || event.title || '';
                    setPopupText('salonPopupMobile', event.mobile);
                    setPopupText('salonPopupCode', event.booking_code);
                    setPopupText('salonPopupDate', event.booking_date);
                    setPopupText('salonPopupStatus', event.status);
                    setPopupText('salonPopupSource', event.booking_source);
                    setPopupText('salonPopupServices', event.services);
                    document.getElementById('salonPopupBeautician').innerText = event.beautician_name || '—';
                    updateSalonTimeSlot(event.time_slot || '');

                    var photoEl = document.getElementById('salonPopupBeauticianPhoto');
                    if (photoEl) {
                        photoEl.src = event.beautician_photo
                            ? event.beautician_photo
                            : "{{ asset('admin/images/fav_icon.jpg') }}";
                    }

                    var openBtn = document.getElementById('salonOpenBookingBtn');
                    if (openBtn) {
                        if (activeSalonBookingUrl) {
                            openBtn.href = activeSalonBookingUrl;
                            openBtn.style.display = 'inline-flex';
                        } else {
                            openBtn.removeAttribute('href');
                            openBtn.style.display = 'none';
                        }
                    }

                    document.getElementById('salonCustomPopup').style.display = 'block';
                    document.getElementById('salonOverlay').style.display = 'block';
                    jq('#salonCustomPopup').fadeIn(0);
                }
            ));

            jq('#full-calendar').fullCalendar(buildDashboardCalendarOptions(
                '#full-calendar',
                booking,
                function(event) {
                    document.getElementById('popupTitle').innerText = event.client_name || event.title || '';
                    setPopupText('popupMobile', event.mobile);
                    setPopupText('popupPlace', event.place);
                    setPopupText('popupDate', event.booking_date);
                    setPopupText('popupImportantDetails', event.important_details);
                    setPopupText('popupSerices', event.services);
                    document.getElementById('popupBeautician').innerText = event.beautician_name || '—';
                    setPopupText('popupBookingType', event.booking_type);
                    updateTimeSlot(event.time_slot || '');

                    var photoEl = document.getElementById('popupBeauticianPhoto');
                    if (photoEl) {
                        photoEl.src = event.beautician_photo
                            ? event.beautician_photo
                            : "{{ asset('admin/images/fav_icon.jpg') }}";
                    }

                    document.getElementById('customPopup').style.display = 'block';
                    document.getElementById('overlay').style.display = 'block';
                    jq('#customPopup').fadeIn(0);
                }
            ));

            // Recalculate calendar width after sidebar layout settles
            setTimeout(function() {
                jq('#salon-booking-calendar').fullCalendar('render');
                jq('#full-calendar').fullCalendar('render');
            }, 150);
            jq(document).click(function(e) {
                if (!jq(e.target).closest('#customPopup').length && !jq(e.target).closest('#full-calendar .fc-event').length) {
                    closePopup();
                }
                if (!jq(e.target).closest('#salonCustomPopup').length && !jq(e.target).closest('#salon-booking-calendar .fc-event').length) {
                    closeSalonPopup();
                }
            });
        });

        function closePopup() {
            jq('#customPopup').fadeOut(100);
            document.getElementById('overlay').style.display = 'none';
        }

        function closeSalonPopup() {
            jq('#salonCustomPopup').fadeOut(100);
            document.getElementById('salonOverlay').style.display = 'none';
            activeSalonBookingUrl = null;
        }

        function setPopupText(id, value) {
            var el = document.getElementById(id);
            if (!el) {
                return;
            }
            var text = (value || '').toString().trim();
            el.innerText = text || '—';
        }

        function updateTimeSlot(time) {
            document.getElementById('popupTimeSlot').innerHTML =
                `Time Slot <i class="fa fa-clock-o" aria-hidden="true"></i><span>${time || '—'}</span>`;
        }

        function updateSalonTimeSlot(time) {
            document.getElementById('salonPopupTimeSlot').innerHTML =
                `Time Slot <i class="fa fa-clock-o" aria-hidden="true"></i><span>${time || '—'}</span>`;
        }
    </script>

    <div id="overlay"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.25); z-index: 999; opacity: 1; transition: opacity 0.3s;">
    </div>

    <div id="customPopup" class="calender_event-popup-d zoom_in-anim"
        style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
background: white; z-index: 1000;">
        <button type="button" class="close-btn" onclick="closePopup()" aria-label="Close">&times;</button>
        <div class="cal-popup-header">
            <div>
                <p class="popup-username" id="popupTitle"></p>
                <p class="popup-phone">
                    <i class="fa fa-phone" aria-hidden="true"></i>
                    <span id="popupMobile"></span>
                </p>
            </div>
            <div class="cal-popup-code">
                <p class="cal-popup-code-label">Type</p>
                <p class="cal-popup-code-value" id="popupBookingType"></p>
            </div>
        </div>
        <div class="cal-popup-grid">
            <p class="cal-popup-chip">
                <strong>Place</strong>
                <span id="popupPlace"></span>
            </p>
            <p class="cal-popup-chip">
                <strong>Date</strong>
                <span id="popupDate"></span>
            </p>
            <p class="cal-popup-chip full">
                <strong>Service</strong>
                <span id="popupSerices"></span>
            </p>
            <p class="cal-popup-chip full">
                <strong>Address</strong>
                <span id="popupImportantDetails"></span>
            </p>
        </div>
        <div class="popup-beautytion-container">
            <img class="beauty-profile" id="popupBeauticianPhoto"
                src="{{ asset('admin/images/fav_icon.jpg') }}" alt="beautician-profile">
            <p class="popup-beautytion-name" id="popupBeautician"></p>
        </div>
        <div class="cal-popup-footer">
            <div class="popup-time-container">
                <p class="popup-datetime-slot" id="popupTimeSlot"></p>
            </div>
        </div>
    </div>

    <div id="salonOverlay"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.25); z-index: 999; opacity: 1; transition: opacity 0.3s;">
    </div>

    <div id="salonCustomPopup" class="calender_event-popup-d zoom_in-anim"
        style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
background: white; z-index: 1000;">
        <button type="button" class="close-btn" onclick="closeSalonPopup()" aria-label="Close">&times;</button>
        <div class="cal-popup-header">
            <div>
                <p class="popup-username" id="salonPopupTitle"></p>
                <p class="popup-phone">
                    <i class="fa fa-phone" aria-hidden="true"></i>
                    <span id="salonPopupMobile"></span>
                </p>
            </div>
            <div class="cal-popup-code">
                <p class="cal-popup-code-label">Booking Code</p>
                <p class="cal-popup-code-value" id="salonPopupCode"></p>
            </div>
        </div>
        <div class="cal-popup-grid">
            <p class="cal-popup-chip">
                <strong>Date</strong>
                <span id="salonPopupDate"></span>
            </p>
            <p class="cal-popup-chip">
                <strong>Status</strong>
                <span id="salonPopupStatus"></span>
            </p>
            <p class="cal-popup-chip">
                <strong>Source</strong>
                <span id="salonPopupSource"></span>
            </p>
            <p class="cal-popup-chip">
                <strong>Service</strong>
                <span id="salonPopupServices"></span>
            </p>
        </div>
        <div class="popup-beautytion-container">
            <img class="beauty-profile" id="salonPopupBeauticianPhoto"
                src="{{ asset('admin/images/fav_icon.jpg') }}" alt="beautician-profile">
            <p class="popup-beautytion-name" id="salonPopupBeautician"></p>
        </div>
        <div class="cal-popup-footer">
            <a id="salonOpenBookingBtn" class="btn-open-booking" href="#">Open Booking</a>
            <div class="popup-time-container">
                <p class="popup-datetime-slot" id="salonPopupTimeSlot"></p>
            </div>
        </div>
    </div>
@endsection
