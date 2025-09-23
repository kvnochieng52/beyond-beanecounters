@extends('adminlte::page')

@section('title', 'Leads')

@section('content_header')
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Leads</h3>
        @if(auth()->user()->hasRole('Admin'))
        <a href="{{ route('lead.export-by-status', $status) }}" class="btn btn-success" style="float: right">
            <i class="fas fa-file-excel"></i> Export to Excel
        </a>
        @endif
    </div>

    <div class="card-body">

        <div class="table-responsive">

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
                        <th>PTP</th>
                        <th>PTP Retire Date</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Stage</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>

        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="/css/validator/bootstrapValidator.min.css" />
@stop

@section('js')
<script src="/js/validator/bootstrapValidator.min.js"></script>
<script>
    $(document).ready(function() {
    let status = "{{ $status }}"; // Pass status from Blade to JavaScript

   // console.log("STATUS IS: "+status);

    $('#leadsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('lead.leadByStatusData') }}",
            data: function(d) {
                d.status = status; // Append status to the request
            },
            error: function(xhr, error, thrown) {
                console.log("DataTables Error:", error);
                console.log("XHR Response:", xhr.responseText);
            }
        },
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
            { data: 'defaulter_type_name', name: 'defaulter_type_name' , searchable: false, sortable: false},
            { data: 'id_passport_number', name: 'id_passport_number' },
            { data: 'telephone', name: 'telephone' },
             { 
                data: 'amount', 
                name: 'amount',
                render: function(data, type, row) {
                    return `${row.currency_name} ${Number(data).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
                }
            },
            { 
                data: 'balance', 
                name: 'balance',
                render: function(data, type, row) {
                    return `${row.currency_name} ${Number(data).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
                    //return `${row.currency_name} ${Math.floor(data)}`;
                }
            },

            { 
                data: 'ptp_amount', 
                name: 'ptp_amount',
                searchable: false,
            
                render: function(data, type, row) {
                    return `${row.currency_name} ${Number(data).toLocaleString()}`;
                }
            },

            { 
                data: 'ptp_expiry_date', 
                name: 'ptp_expiry_date',
                searchable: false,
                render: function(data) {
                    return formatDate(data, 'date');
                }
         },
            { 
                data: 'priority_id', 
                name: 'priority_id',
                render: function(data, type, row) {
                    return `<a href="/lead/${row.id}">
                                <span class="badge text-white bg-${row.lead_priority_color_code}">
                                    ${row.lead_priority_name}
                                </span>
                            </a>`;
                }
            },
            { 
                data: 'status_id', 
                name: 'status_id',
                render: function(data, type, row) {
                    return `<a href="/lead/${row.id}">
                                <span class="badge text-white bg-${row.lead_status_color_code}">
                                    ${row.lead_status_name}
                                </span>
                            </a>`;
                }
            },
            { data: 'lead_stage_name', name: 'lead_stage_name', searchable: false, orderable: false },
            { 
                data: 'actions', 
                name: 'actions', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    return `<a href="/lead/${row.id}/edit" class="btn btn-warning btn-xs">
                                <i class="fa fa-edit"></i>
                            </a>
                            <a href="#" class="btn btn-danger btn-xs" onclick="confirmDelete(${row.id})">
                                <i class="fa fa-trash"></i>
                            </a>`;
                }
            }
        ]
    });


    function formatDate(dateString, type = 'date') {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        if (isNaN(date)) return 'Invalid Date';
        
        // Always get 2-digit day and month
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        
        if (type === 'date') {
            return `${day}-${month}-${year}`;  // DD-MM-YYYY format
        }
        else if (type === 'datetime') {
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${day}-${month}-${year} ${hours}:${minutes}`;  // DD-MM-YYYY HH:MM format
        }
        
        return '';
}
});




</script>
@stop