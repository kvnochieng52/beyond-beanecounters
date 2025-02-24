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
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="mb-0" style="font-size:14px; color:#888;">
                            By: {{$activity->assigned_agent_name}}
                        </p>

                        <div>
                            <a href="">Edit</a> | <a href="">Delete</a>
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