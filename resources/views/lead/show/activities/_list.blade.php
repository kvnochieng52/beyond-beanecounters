<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th></th>
            <th>Title</th>
            <th>Description</th>
            <th>Date</th>
            <th>Agent</th>
            <th>Due</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>


    <tbody>

        @foreach($leadListActivities as $key => $activity)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td><i class="fas fa-circle text-{{ $activity->lead_priority_color_code }}"></i></td>
            <td>
                <i class="fas fa-{{ $activity->activity_type_icon }}" style="color: #aaa9a9"></i>
                &nbsp; {{ $activity->activity_title }}
            </td>
            <td>{{ $activity->description }}</td>
            <td>{{ \Carbon\Carbon::parse($activity->created_at)->format('d-m-Y') }}</td>
            <td>{{ $activity->assigned_agent_name }}</td>
            <td>
                @if(!empty($activity->due_date_time))
                @php
                $dueDate = \Carbon\Carbon::parse($activity->due_date_time);
                $dueTimestamp = $dueDate->timestamp; // Convert to Unix timestamp
                @endphp

                <span id="countdown-{{ $activity->id }}" data-duetime="{{ $dueTimestamp }}" class="badge">
                    Calculating...
                </span>
                @endif
            </td>



            <td>
                <span class="badge bg-{{ $activity->activity_status_color_code }}">
                    {{ $activity->activity_status_name }}
                </span>
            </td>

            <!-- Edit & Delete Buttons -->
            <td>
                {{-- Edit Button --}}
                <a href="#" class="btn btn-warning btn-xs" data-bs-toggle="modal" data-bs-target="#edit_debt_modal"
                    data-id="{{ $activity->id }}" data-title="{{ $activity->activity_title }}"
                    data-description="{{ $activity->description }}" data-priority="{{ $activity->priority_id }}"
                    data-activityType="{{ $activity->activity_type_id }}"
                    data-department="{{ $activity->assigned_department_id}}"
                    data-agent="{{ $activity->assigned_user_id }}" data-status="{{ $activity->status_id }}"
                    data-startDate="{{\Carbon\Carbon::parse($activity->start_date_time)->format('d-m-Y') }}"
                    data-startTime="{{\Carbon\Carbon::parse($activity->start_date_time)->format('h:i A') }}"
                    data-endDate="{{\Carbon\Carbon::parse($activity->due_date_time)->format('d-m-Y') }}"
                    data-endTime="{{\Carbon\Carbon::parse($activity->due_date_time)->format('h:i A') }}"
                    data-calendarAdd="{{ $activity->calendar_add }}">
                    Edit
                </a>

                {{-- Delete Form --}}
                <form action="{{ route('activity.destroy', $activity->id) }}" method="POST"
                    style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs"
                        onclick="return confirm('Are you sure you want to delete this activity?')">
                        Delete
                    </button>
                </form>
            </td>
        </tr>
        @endforeach

    </tbody>


</table>


<div class="mt-3">
    {{ $leadListActivities->withQueryString()->links() }}
</div>

{{-- @include('modals.activities._edit_activity_modal') --}}


<script>
    function updateCountdown() {
        let now = Math.floor(Date.now() / 1000); // Current time in seconds

        document.querySelectorAll("[id^='countdown-']").forEach(span => {
            let dueTime = span.getAttribute("data-duetime");

            // Skip if dueTime is null, empty, or not a number
            if (!dueTime || isNaN(parseInt(dueTime))) {
                span.textContent = ''; // Clear content if you want
                span.className = '';   // Clear badge class (optional)
                return;
            }

            dueTime = parseInt(dueTime);
            let diff = dueTime - now;

            if (diff > 0) {
                // Future due date → Show time remaining
                let days = Math.floor(diff / 86400);
                let hours = Math.floor((diff % 86400) / 3600);
                let minutes = Math.floor((diff % 3600) / 60);
                span.textContent = `${days}d ${hours}h ${minutes}m left`;
                span.className = "badge bg-success";
            } else {
                // Overdue → Show time since due date
                diff = Math.abs(diff);
                let days = Math.floor(diff / 86400);
                let hours = Math.floor((diff % 86400) / 3600);
                let minutes = Math.floor((diff % 3600) / 60);
                span.textContent = `${days}d ${hours}h ${minutes}m ago`;
                span.className = "badge bg-danger";
            }
        });
    }

    // Update countdown every second
    setInterval(updateCountdown, 1000);
    updateCountdown(); // Run immediately on page load
</script>