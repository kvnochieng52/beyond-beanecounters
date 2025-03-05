@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
{{-- <h1>Dashboard</h1> --}}
@stop

@section('content')

<div class="card">

    <div class="card-header">
        <h3>Notifications</h3>
        <a href="{{ route('due-notifications.create') }}" class="btn btn-success">Create Notification</a>
    </div>

    <div class="card-body">

        @include('notices')
        <table id="notifications-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Moment</th>
                    <th>Days</th>
                    <th>Active</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@stop

@section('css')
{{-- Add here extra stylesheets --}}
{{--
<link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
<script>
    $(function() {
    $('#notifications-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('due-notifications.index') }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'title', name: 'title' },
            { data: 'message', name: 'message' },
            { data: 'moment', name: 'moment' },
            { data: 'days', name: 'days' },
            { data: 'is_active', name: 'is_active' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
</script>
@stop