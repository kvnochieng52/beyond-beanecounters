@extends('adminlte::page')

@section('title', 'Leads')

@section('content_header')
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Leads</h3>

        <a href="{{ route('my-leads.export') }}" class="btn btn-success" style="float: right">
            <i class="fas fa-file-excel"></i> Export My Leads
        </a>
    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table id="leadsTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>T/No.</th>
                        <th>Names/Title</th>
                        <th>Institution
                            {{--
                        <th>Defaulter Type</th> --}}
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
    $('#leadsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('my-leads') }}",
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

            // {
            // data: 'defaulter_type_name',
            // name: 'defaulter_types.defaulter_type_name' // Use the table.column format
            // },
            { 
                data: 'institution_name', 
                name: 'institution_name' // Use the table.column format
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
                data: 'ptp_amount', 
                name: 'ptp_amount',
                render: function(data, type, row) {
                    return `${row.currency_name} ${Number(data).toLocaleString()}`;
                }
            },

            { 
                data: 'ptp_expiry_date', 
                name: 'ptp_expiry_date',
                render: function(data) {
                    return formatDate(data, 'date');
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