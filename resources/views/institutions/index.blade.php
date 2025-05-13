@extends('adminlte::page')
@section('title', 'Institutions')
{{-- @section('content_header')
<h1>Institutions</h1>
@stop --}}
@section('content')

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0">Manage Institutions</h3>
        <a href="{{ route('institutions.create') }}" class="btn btn-info float-right">New Institution</a>
    </div>
    <div class="card-body">
        <table id="institutions-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Website</th>
                    <th>Telephone</th>
                    <th>Contact Person</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@stop
@section('js')
<script>
    $(document).ready(function() {
    $('#institutions-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('institutions.getInstitutions') }}',
        columns: [
            { data: 'institution_name', name: 'institution_name' },
            { data: 'address', name: 'address' },
            { data: 'email', name: 'email' },
            { data: 'website', name: 'website' },
            { data: 'telephone', name: 'telephone' },
            { data: 'contact_person', name: 'contact_person' },
            { data: 'is_active', name: 'is_active', render: function(data) {
                return data ? 'Active' : 'Inactive';
            }},
            { data: 'id', name: 'id', orderable: false, searchable: false,
                render: function(data, type, row) {
                    return `<a href='/institutions/${data}/edit' class='btn btn-warning'>Edit</a>
                            <form action='/institutions/${data}' method='POST' style='display:inline;'>
                                @csrf @method('DELETE')
                                <button type='submit' class='btn btn-danger' onclick='return confirm("Are you sure?");'>Delete</button>
                            </form>`;
                }
            }
        ]
    });
});
</script>
@stop