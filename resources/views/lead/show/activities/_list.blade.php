<style>
    .compact-table {
        font-size: 0.875rem;
        /* 14px */
    }

    .compact-table td,
    .compact-table th {
        padding: 0.375rem 0.5rem;
        /* Reduced padding */
        vertical-align: middle;
    }

    .description-cell {
        max-width: 200px;
        /* Limit width */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        position: relative;
    }

    .description-cell:hover {
        white-space: normal;
        overflow: visible;
        background-color: #f8f9fa;
        z-index: 10;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border-radius: 3px;
        padding: 0.5rem;
    }

    .title-cell {
        /* max-width: 150px; */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .btn-xs {
        padding: 0.125rem 0.375rem;
        font-size: 0.75rem;
        margin: 0 1px;
    }

    .badge {
        font-size: 0.75rem;
    }

    .agent-cell {
        max-width: 100px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<table class="table table-bordered table-striped compact-table">
    <thead>
        <tr>
            <th style="width: 40px;">#</th>
            <th style="width: 30px;"></th>
            <th style="width: 150px;">Title</th>
            <th style="width: 200px;">Description</th>
            <th style="width: 80px;">Date</th>
            <th style="width: 100px;">Agent</th>
            <th style="width: 120px;">Due</th>
            <th style="width: 80px;">Status</th>
            <th style="width: 120px;">Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($leadListActivities as $key => $activity)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td><i class="fas fa-circle text-{{ $activity->lead_priority_color_code }}"></i></td>
            <td class="title-cell" title="{{ $activity->activity_title }}">
                <i class="fas fa-{{ $activity->activity_type_icon }}" style="color: #aaa9a9"></i>
                &nbsp; {{ $activity->activity_title }}


                @if($activity->activity_type_id == 3)
                <span class="badge bg-{{ $activity->text_status_color_code }}">
                    {{ $activity->text_status_name }}
                </span>
                @endif
            </td>
            <td class="description-cell" title="{{ $activity->description }}">
                {{ $activity->description }}
            </td>
            <td style="font-size: 0.8rem;">{{ \Carbon\Carbon::parse($activity->created_at)->format('d/m/Y') }}</td>
            <td class="agent-cell" title="{{ $activity->assigned_agent_name }}">
                {{ $activity->assigned_agent_name }}
            </td>
            <td>
                @if(!empty($activity->due_date_time))
                @php
                $dueDate = \Carbon\Carbon::parse($activity->due_date_time);
                $dueTimestamp = $dueDate->timestamp;
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

            <td>
                {{-- Edit Button --}}
                {{-- <a href="#" class="btn btn-warning btn-xs" data-bs-toggle="modal" data-bs-target="#edit_debt_modal"
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
                </a> --}}

                {{-- Delete Form --}}
                {{-- <form action="{{ route('activity.destroy', $activity->id) }}" method="POST"
                    style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs"
                        onclick="return confirm('Are you sure you want to delete this activity?')">
                        Delete
                    </button>
                </form> --}}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-3">
    {{ $leadListActivities->withQueryString()->links() }}
</div>

@include('modals.activities._edit_activity_modal')

<script>
    function updateCountdown() {
        let now = Math.floor(Date.now() / 1000);

        document.querySelectorAll("[id^='countdown-']").forEach(span => {
            let dueTime = span.getAttribute("data-duetime");

            if (!dueTime || isNaN(parseInt(dueTime))) {
                span.textContent = '';
                span.className = '';
                return;
            }

            dueTime = parseInt(dueTime);
            let diff = dueTime - now;

            if (diff > 0) {
                let days = Math.floor(diff / 86400);
                let hours = Math.floor((diff % 86400) / 3600);
                let minutes = Math.floor((diff % 3600) / 60);
                span.textContent = `${days}d ${hours}h ${minutes}m left`;
                span.className = "badge bg-success";
            } else {
                diff = Math.abs(diff);
                let days = Math.floor(diff / 86400);
                let hours = Math.floor((diff % 86400) / 3600);
                let minutes = Math.floor((diff % 3600) / 60);
                span.textContent = `${days}d ${hours}h ${minutes}m ago`;
                span.className = "badge bg-danger";
            }
        });
    }

    setInterval(updateCountdown, 1000);
    updateCountdown();
</script>