<div class="row">
    <div class="col-md-12">
        <div class="timeline">




            @foreach ($leadTimeLineActivities as $date => $activityList)

            <div class="timeline-date">
                {{-- <h6 style="color:#7f5af0;">05-September-2024</h6> --}}

                <h6 style="color:#7f5af0;">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</h6>
            </div>

            @foreach ($activityList as $activity)

            <div class="timeline-entry">
                <div class="timeline-icon"><i class="fas fa-{{$activity->activity_type_icon}}"
                        style="font-size: 14px"></i></div>
                <div class="timeline-time">{{$activity->created_time}}</div>
                <div class="timeline-content">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">
                            {{-- <a href="#"> --}}
                                <i class="fas fa-circle text-{{$activity->lead_priority_color_code}}"
                                    style="font-size: 12px"></i>
                                {{ $activity->activity_title }}
                                {{-- </a> --}}
                        </h2>

                        <span class="badge bg-{{$activity->activity_status_color_code}}">
                            {{$activity->activity_status_name}}
                        </span>
                    </div>
                    <p>{{$activity->description}}</p>
                    <p>Due:
                        @php
                        $dueDate = \Carbon\Carbon::parse($activity->due_date_time);
                        $dueTimestamp = $dueDate->timestamp; // Convert to Unix timestamp
                        @endphp

                        <span id="countdown-{{ $activity->id }}" data-duetime="{{ $dueTimestamp }}" class="badge">
                            Calculating...
                        </span>
                    </p>
                    <div class="d-flex justify-content-between align-items-center">

                        <p class="mb-0" style="font-size:14px; color:#888;">
                            By: {{$activity->assigned_agent_name}}
                        </p>

                        <div>
                            {{-- Edit Button --}}
                            <a href="#" class="btn btn-warning btn-xs" data-bs-toggle="modal"
                                data-bs-target="#edit_debt_modal" data-id="{{ $activity->id }}"
                                data-title="{{ $activity->activity_title }}"
                                data-description="{{ $activity->description }}"
                                data-priority="{{ $activity->priority_id }}"
                                data-activityType="{{ $activity->activity_type_id }}"
                                data-department="{{ $activity->assigned_department_id }}"
                                data-agent="{{ $activity->assigned_user_id }}" data-status="{{ $activity->status_id }}"
                                data-startDate="{{\Carbon\Carbon::parse($activity->start_date_time)->format('d-m-Y') }}"
                                data-startTime="{{\Carbon\Carbon::parse($activity->start_date_time)->format('h:i A') }}"
                                data-endDate="{{\Carbon\Carbon::parse($activity->due_date_time)->format('d-m-Y') }}"
                                data-endTime="{{\Carbon\Carbon::parse($activity->due_date_time)->format('h:i A') }}"
                                data-calendarAdd="{{ $activity->calendar_add }}">
                                Edit
                            </a> | <form action="{{ route('activity.destroy', $activity->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-xs"
                                    onclick="return confirm('Are you sure you want to delete this activity?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @endforeach


            @endforeach

        </div>

        {{-- <div class="mt-3">
            {{ $leadTimeLineActivities->links() }}
        </div> --}}
    </div>


</div>
{{-- @include('modals.activities._edit_activity_modal') --}}


<script>
    function updateCountdown() {
        let now = Math.floor(Date.now() / 1000); // Current time in seconds

        document.querySelectorAll("[id^='countdown-']").forEach(span => {
            let dueTime = parseInt(span.getAttribute("data-duetime"));
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