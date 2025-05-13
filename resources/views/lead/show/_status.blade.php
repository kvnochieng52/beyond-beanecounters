<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center pb-3">
            <h2 class="card-title pb-2">Status</h2>

            <a href="#" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#update_status_modal">
                <i class="fa fa-fw fa-edit"></i>Update Status
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">

        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <th style="width: 50%">Lead Stage</th>
                    <th>Lead Status</th>
                </tr>

                <tr>
                    <td>{{ strtoupper($leadDetails->lead_stage_name) }}</td>
                    <td>
                        <span class="badge bg-{{ $leadDetails->lead_status_color_code }}">
                            {{ strtoupper($leadDetails->lead_status_name) }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <th>Lead Conversion Level</th>
                    <th>Lead Engagement Level</th>
                </tr>

                <tr>
                    <td>{{ strtoupper($leadDetails->lead_conversion_name) }}</td>
                    <td>{{ strtoupper($leadDetails->lead_engagement_level_name) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <hr />
        <h2 class="card-title pb-2">Status History</h2>


        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Lead Stage</th>
                    <th>Conversion Level</th>
                    <th>Engagement Level</th>
                    <th>Description</th>
                    <th>Agent</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>


            <tbody>
                @foreach ($leadsStatusHistory as $key=>$history)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$history->lead_stage_name}}</td>
                    <td>{{$history->lead_conversion_name}}</td>
                    <td>{{$history->lead_engagement_level_name}}</td>
                    <td>{{$history->description}}</td>
                    <td>{{$history->agent_name}}</td>
                    <td>{{ \Carbon\Carbon::parse($history->created_at)->format('d-m-Y') }}</td>
                    <td><span class="badge bg-{{ $history->lead_status_color_code }}">
                            {{ strtoupper($history->lead_status_name) }}
                        </span></td>
                </tr>

                @endforeach
            </tbody>
        </table>


        <div class="mt-3">
            {{ $leadsStatusHistory->withQueryString()->links() }}
        </div>
    </div>
</div>




@include('modals.status._update_status_modal')