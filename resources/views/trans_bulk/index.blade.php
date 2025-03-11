@extends('adminlte::page')

@section('title', 'Trans Bulk')

{{-- @section('content_header')
<h1>Transaction Bulk Uploads</h1>
@stop --}}

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Uploaded Transactions</h3>
    </div>
    <div class="card-body">
        @include('notices')
        <table class="table table-bordered table-striped" id="transBulkTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>CSV File</th>
                    <th>Rules</th>
                    <th>Uploaded At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@stop

@section('css')
{{-- Add custom styles if needed --}}
@stop

@section('js')
<script>
    $(document).ready(function() {
    $('#transBulkTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('trans_bulk.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'user_name', name: 'user_name' },
            { data: 'csv_file', name: 'csv_file', orderable: false, searchable: false },
            { data: 'rules', name: 'rules' },
            { data: 'uploaded_at', name: 'uploaded_at' },
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });
});
</script>
@stop