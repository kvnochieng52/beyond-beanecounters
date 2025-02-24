<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th></th>
            <th>Title</th>
            <th>Description</th>
            <th>Date</th>
            <th>Agent</th>
            <th>Status</th>
        </tr>
    </thead>


    <tbody>
        @foreach($leadListActivities as $key=>$activity)
        <tr>
            <td>{{$key+1}}</td>
            <td><i class="fas fa-circle text-{{$activity->lead_priority_color_code}}"></i></td>
            <td><i class="fas fa-{{$activity->activity_type_icon}}" style="color: #aaa9a9"></i>
                &nbsp; {{$activity->activity_title}}</td>
            <td>{{$activity->description}}</td>
            <td>{{ \Carbon\Carbon::parse($activity->created_at)->format('d-m-Y') }}</td>
            <td>{{$activity->assigned_agent_name}}</td>
            <td><span class="badge bg-{{$activity->activity_status_color_code}}">
                    {{$activity->activity_status_name}}
                </span></td>


        </tr>


        @endforeach

    </tbody>


</table>


<div class="mt-3">
    {{ $leadListActivities->withQueryString()->links() }}
</div>