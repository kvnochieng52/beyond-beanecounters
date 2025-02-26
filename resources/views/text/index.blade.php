@extends('adminlte::page')

@section('title', 'SMS Details')

@section('content_header')
<h1>SMS Details</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-striped" id="smsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Contact Type</th>
                    <th>Recipient Contacts</th>
                    <th>Message</th>
                    <th>Scheduled</th>
                    <th>Schedule Date</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@stop

@section('css')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(function() {
        $('#smsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('text.index') }}',
            columns: [
                { data: 'id' },
                { data: 'text_title' },
                { data: 'contact_type' },
                { data: 'recepient_contacts' },
                { data: 'message' },
                { data: 'scheduled', render: data => data ? 'Yes' : 'No' },
                { data: 'schedule_date' },
                { data: 'status', orderable: false, searchable: false },
                { data: 'created_at' },
            ],
        });
    });
</script>
@stop