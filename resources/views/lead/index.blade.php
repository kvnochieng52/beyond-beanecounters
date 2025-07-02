@extends('adminlte::page')

@section('title', 'Leads')

@section('content_header')
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Leads</h3>
        <p style="float: right; margin-bottom:0px">Fields marked * are mandatory </p>
    </div>

    <div class="card-body">

        <table id="leadsTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>T/No.</th>
                    <th>Names/Title</th>
                    <th>Defaulter Type</th>
                    <th>ID Number</th>
                    <th>Telephone</th>
                    <th>Amount</th>
                    <th>Balance</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Stage</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
        {{-- <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>T/No.</th>
                    <th>Names/Title</th>
                    <th>Defaulter Type</th>
                    <th>ID Number</th>
                    <th>Telephone</th>
                    <th>Amount</th>
                    <th>Balance</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Stage</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($leads as $key => $lead)
                <tr>
                    <td>{{ $leads->firstItem() + $key }}</td>
                    <td>
                        <a href="/lead/{{$lead->id}}">

                            <strong>#{{ $lead->id }}</strong>

                        </a>
                    </td>
                    <td>
                        <a href="/lead/{{$lead->id}}"><strong>{{ $lead->title }}</strong></a><br />
                        <small>Agent: {{ $lead->assigned_agent_name }}</small>
                    </td>
                    <td>{{ $lead->defaulter_type_name }}</td>
                    <td>{{ $lead->id_passport_number }}</td>
                    <td>{{ $lead->telephone }}</td>
                    <td>{{ $lead->currency_name }} {{ number_format($lead->amount, 0) }}</td>
                    <td>{{ $lead->currency_name }} {{ number_format($lead->balance, 0) }}</td>
                    <td>
                        <a href="/lead/{{$lead->id}}">
                            <span class="badge bg-{{ $lead->lead_priority_color_code }}">
                                {{$lead->lead_priority_name}}
                            </span>
                        </a>
                    </td>
                    <td>
                        <a href="/lead/{{$lead->id}}">
                            <span class="badge bg-{{ $lead->lead_status_color_code }}">
                                {{ $lead->lead_status_name}}
                            </span>
                        </a>
                    </td>
                    <td>{{ $lead->lead_stage_name }}</td>
                    <td>
                        <a href="/lead/{{$lead->id}}/edit" class="btn btn-warning btn-xs">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="#" class="btn btn-danger btn-xs" onclick="confirmDelete({{ $lead->id }})">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table> --}}

        <!-- Pagination -->

    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="/css/validator/bootstrapValidator.min.css" />
@stop

@section('js')
<script src="/js/validator/bootstrapValidator.min.js"></script>
<script>
    //     $(document).ready(function() {
//     $('#leadsTable').DataTable({
//         processing: true,
//         serverSide: true,
//         ajax: "{{ route('lead.index') }}",
//         columns: [
//             { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
//             { 
//                 data: 'id', 
//                 name: 'id',
//                 render: function(data, type, row) {
//                     return `<a href="/lead/${row.id}"><strong>#${data}</strong></a>`;
//                 }
//             },
//             { 
//                 data: 'title', 
//                 name: 'title',
//                 render: function(data, type, row) {
//                     return `<a href="/lead/${row.id}"><strong>${data}</strong></a><br />
//                             <small>Agent: ${row.assigned_agent_name}</small>`;
//                 }
//             },
//             { data: 'defaulter_type_name', name: 'defaulter_type_name' },
//             { data: 'id_passport_number', name: 'id_passport_number' },
//             { data: 'telephone', name: 'telephone' },
//             { 
//                 data: 'amount', 
//                 name: 'amount',
//                 render: function(data, type, row) {
//                     return `${row.currency_name} ${Number(data).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
//                 }
//             },
//             { 
//                 data: 'balance', 
//                 name: 'balance',
//                 render: function(data, type, row) {
//                     return `${row.currency_name} ${Number(data).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
//                     //return `${row.currency_name} ${Math.floor(data)}`;
//                 }
//             },
//             { 
//                 data: 'priority_id', 
//                 name: 'priority_id',
//                 render: function(data, type, row) {
//                     return `<a href="/lead/${row.id}">
//                                 <span class="badge text-white bg-${row.lead_priority_color_code}">
//                                     ${row.lead_priority_name}
//                                 </span>
//                             </a>`;
//                 }
//             },
//             { 
//                 data: 'status_id', 
//                 name: 'status_id',
//                 render: function(data, type, row) {
//                     return `<a href="/lead/${row.id}">
//                                 <span class="badge text-white bg-${row.lead_status_color_code}">
//                                     ${row.lead_status_name}
//                                 </span>
//                             </a>`;
//                 }
//             },
//             { data: 'lead_stage_name', name: 'lead_stage_name' },
//             { 
//                 data: 'actions', 
//                 name: 'actions', 
//                 orderable: false, 
//                 searchable: false,
//                 render: function(data, type, row) {
//                     return `<a href="/lead/${row.id}/edit" class="btn btn-warning btn-xs">
//                                 <i class="fa fa-edit"></i>
//                             </a>
//                             <a href="#" class="btn btn-danger btn-xs" onclick="confirmDelete(${row.id})">
//                                 <i class="fa fa-trash"></i>
//                             </a>`;
//                 }
//             }
//         ]
//     });
// });


$(document).ready(function() {
    $('#leadsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('lead.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { 
                data: 'id', 
                name: 'id',
                render: function(data, type, row) {
                    return `<a href="/lead/${row.id}"><strong>#${data}</strong></a>`;
                }
            },
            { 
                data: 'title', 
                name: 'title',
                render: function(data, type, row) {
                    return `<a href="/lead/${row.id}"><strong>${data}</strong></a><br />
                            <small>Agent: ${row.assigned_agent_name}</small>`;
                }
            },
            { 
                data: 'defaulter_type_name', 
                name: 'defaulter_types.defaulter_type_name' // Use the table.column format
            },
            { data: 'id_passport_number', name: 'id_passport_number' },
            { data: 'telephone', name: 'telephone' },
            { 
                data: 'amount', 
                name: 'amount',
                render: function(data, type, row) {
                    return `${row.currency_name} ${Number(data).toLocaleString()}`;
                }
            },
            { 
                data: 'balance', 
                name: 'balance',
                render: function(data, type, row) {
                    return `${row.currency_name} ${Number(data).toLocaleString()}`;
                }
            },
            { 
                data: 'priority_id', 
                name: 'lead_priorities.lead_priority_name', // Use the joined table column
                render: function(data, type, row) {
                    return `<a href="/lead/${row.id}">
                                <span class="badge bg-${row.lead_priority_color_code}">
                                    ${row.lead_priority_name}
                                </span>
                            </a>`;
                }
            },
            { 
                data: 'status_id', 
                name: 'lead_statuses.lead_status_name', // Use the joined table column
                render: function(data, type, row) {
                    return `<a href="/lead/${row.id}">
                                <span class="badge bg-${row.lead_status_color_code}">
                                    ${row.lead_status_name}
                                </span>
                            </a>`;
                }
            },
            { 
                data: 'lead_stage_name', 
                name: 'lead_stages.lead_stage_name' // Use the joined table column
            },
            { 
                data: 'actions', 
                name: 'actions', 
                orderable: false, 
                searchable: false
            }
        ]
    });
});

</script>
@stop