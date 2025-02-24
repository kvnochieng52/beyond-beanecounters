<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center pb-3">
            <h2 class="card-title pb-2">Basic Details</h2>
            <a href="/lead/{{$leadDetails->id}}/edit" class="btn btn-info btn-sm">
                <i class="fa fa-fw fa-edit"></i> Edit
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <table class="table table-bordered table-striped">
            <tr>
                <th style="width: 40%">Entity Name</th>
                <td>{{$leadDetails->title}}</td>
            </tr>

            <tr>
                <th>Ticket Number</th>
                <td>#{{$leadDetails->id}}</td>
            </tr>

            <tr>
                <th>Telephone</th>
                <td>{{$leadDetails->telephone}}</td>
            </tr>


            <tr>
                <th>Alternate Telephone</th>
                <td>{{$leadDetails->alternate_telephone}}</td>
            </tr>

            <tr>
                <th>Email</th>
                <td>{{$leadDetails->email}}</td>
            </tr>

            <tr>
                <th>Address</th>
                <td>{{$leadDetails->address}}</td>
            </tr>

            <tr>
                <th>Town</th>
                <td>{{$leadDetails->town}}</td>
            </tr>
            <tr>
                <th>Country</th>
                <td>{{$leadDetails->country_name}}</td>
            </tr>

            <tr>
                <th>Industry</th>
                <td>{{$leadDetails->lead_industry_name}}</td>
            </tr>

        </table>
    </div>
    <div class="col-md-6">
        <table class="table table-bordered table-striped">


            <tr>
                <th style="width: 40%">Status</th>
                <td>
                    <span class="badge bg-{{ $leadDetails->lead_status_color_code }}">
                        {{ strtoupper($leadDetails->lead_status_name) }}
                    </span>
                </td>
            </tr>


            <tr>
                <th>Stage</th>
                <td>
                    <span class="badge bg-secondary">
                        {{$leadDetails->lead_stage_name}}
                    </span>
                </td>
            </tr>


            <tr>
                <th>Amount</th>
                <td>{{ $leadDetails->currency_name }} {{ number_format($leadDetails->amount, 0) }}</td>
            </tr>

            <tr>
                <th>Balance</th>
                <td>{{ $leadDetails->currency_name }} {{ number_format($leadDetails->balance, 0) }}</td>
            </tr>

            <tr>
                <th>Due Date</th>
                <td>{{ \Carbon\Carbon::parse($leadDetails->due_date)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <th>Institution Name</th>
                <td>{{$leadDetails->institution_name}}</td>
            </tr>


            <tr>
                <th>Refrence/Ac Number</th>
                <td>{{$leadDetails->account_number}}</td>
            </tr>

            <tr>
                <th>Priority</th>
                <td>
                    <span class="badge bg-{{ $leadDetails->lead_priority_color_code }}">
                        {{strtoupper($leadDetails->lead_priority_name)}}
                    </span> /

                    {{$leadDetails->lead_priority_description}}

                </td>
            </tr>
            <tr>
                <th>Category</th>
                <td>{{$leadDetails->lead_category_name}}</td>
            </tr>

            <tr>
                <th>Lead Stage</th>
                <td>{{strtoupper($leadDetails->lead_stage_name)}}</td>
            </tr>

            <tr>
                <th>Lead Conversion Level</th>
                <td>{{strtoupper($leadDetails->lead_conversion_name)}}</td>
            </tr>

            <tr>
                <th>Lead Conversion Level</th>
                <td>{{strtoupper($leadDetails->lead_engagement_level_name)}}</td>
            </tr>

            <tr>
                <th>Assigned Agent</th>
                <td>{{$leadDetails->assigned_agent_name}}</td>
            </tr>

            <tr>
                <th>Assigned Department</th>
                <td>{{$leadDetails->department_name}}</td>
            </tr>
        </table>
    </div>
</div>