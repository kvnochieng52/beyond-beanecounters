@extends('adminlte::page')

@section('title', 'Calendar')

@section('content_header')
<h1>Calendar</h1>
@stop

@section('content')

<div class="row">
    <div class="col-md-12">

        <h1>Calendar Events</h1>
        <a href="{{ route('calendars.create') }}">Add Event</a>
        <div id='calendar'></div>
    </div>
</div>






@stop

@section('css')
{{--
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css"> --}}
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: @json($calendars->map(fn($calendar) => [
                    'title' => $calendar->calendar_title,
                    'start' => $calendar->start_date_time,
                    'end' => $calendar->due_date_time
                ])),
            });
            calendar.render();
        });
</script>
@stop