@extends('layouts.vcapp')
@section('content')
    <h5 style="text-align: center"><b>{{ $user_name ?? 'Beautician' }}</b></h5>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/moment@2.27.0/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.js"></script>

    <style>
        {{ $style ?? '' }}
    </style>

    <div class="fbox-shadow respo-margin-top mt-5 fd-card">
        <div class="response"></div>
        <div id="calendar" class="m-2 dashbord-main-calendar"></div>
    </div>

    <script>
        jQuery(function ($) {
            var events = {!! $user_calender !!};
            if (!Array.isArray(events)) {
                events = [];
            }

            $("#calendar").fullCalendar({
                events: events
            });
        });
    </script>
@endsection
