@extends('adminlte::page')

@section('title', 'Calendar')

@section('content_header')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Calendar</h3>
                <p style="margin-bottom:0px">Fields marked * are mandatory</p>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-8">
                        <div id='calendar'></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Modal -->
<!-- Event Modal -->
<!-- Event Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="eventForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="eventId">
                    <div class="mb-3">
                        <label for="eventTitle" class="form-label">Title *</label>
                        <input type="text" class="form-control" id="eventTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="eventDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="eventStart" class="form-label">Start Date & Time *</label>
                        <input type="datetime-local" class="form-control" id="eventStart" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventEnd" class="form-label">End Date & Time *</label>
                        <input type="datetime-local" class="form-control" id="eventEnd" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger d-none" id="deleteEventBtn">Delete</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@stop

@section('css')
<link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/fullcalendar/main.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
    .fc .fc-col-header-cell-cushion {
        color: black !important;
        font-weight: bold !important;
    }

    .fc .fc-daygrid-day-number {
        color: #4a4a4a !important;
        font-size: 16px !important;
        font-weight: bold !important;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
    const eventForm = document.getElementById('eventForm');
    const deleteEventBtn = document.getElementById('deleteEventBtn');
    const titleInput = document.getElementById('eventTitle');
    const descriptionInput = document.getElementById('eventDescription');
    const startInput = document.getElementById('eventStart');
    const endInput = document.getElementById('eventEnd');
    const eventIdInput = document.getElementById('eventId');
    let currentEvent = null;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        selectable: true,
        editable: true,
        events: {!! json_encode($calendars->map(fn($calendar) => [
            'id' => $calendar->id,
            'title' => $calendar->calendar_title,
            'start' => $calendar->start_date_time,
            'end' => $calendar->due_date_time,
            'description' => $calendar->description
        ])) !!},

        select(info) {
            currentEvent = null;
            eventForm.reset();
            deleteEventBtn.classList.add('d-none'); // Hide delete button on new event
            eventIdInput.value = '';
            startInput.value = info.startStr;
            endInput.value = info.endStr;
            eventModal.show();
        },

        eventClick(info) {
            currentEvent = info.event;
            deleteEventBtn.classList.remove('d-none'); // Show delete button
            eventIdInput.value = info.event.id;
            titleInput.value = info.event.title;
            descriptionInput.value = info.event.extendedProps.description ?? '';
            startInput.value = info.event.start?.toISOString().slice(0, 16) ?? '';
            endInput.value = info.event.end?.toISOString().slice(0, 16) ?? '';
            eventModal.show();
        }
    });

    eventForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const eventData = {
            id: eventIdInput.value,
            title: titleInput.value,
            description: descriptionInput.value,
            start: startInput.value,
            end: endInput.value,
            _token: '{{ csrf_token() }}'
        };

        fetch('/store-calendar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(eventData)
        })
        .then(response => response.json())
        .then(data => {
            if (currentEvent) {
                currentEvent.setProp('title', data.title);
                currentEvent.setExtendedProp('description', data.description);
                currentEvent.setStart(data.start);
                currentEvent.setEnd(data.end);
            } else {
                calendar.addEvent({
                    id: data.id,
                    title: data.title,
                    start: data.start,
                    end: data.end,
                    description: data.description
                });
            }
            eventModal.hide();
            eventForm.reset();
        })
        .catch(error => console.error('Error:', error));
    });

   deleteEventBtn.addEventListener('click', function() {
    if (!currentEvent) return;

    if (confirm('Are you sure you want to delete this event?')) {
        fetch('/delete-calendar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Ensure meta tag exists
            },
            body: JSON.stringify({ id: currentEvent.id })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                currentEvent.remove();  // Remove event from calendar
                eventModal.hide();      // Hide modal after deletion
            } else {
                alert(data.message || 'Failed to delete the event.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred.');
        });
    }
});


    calendar.render();
});

</script>
@stop