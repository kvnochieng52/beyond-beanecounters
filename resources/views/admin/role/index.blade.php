@extends('adminlte::page')

@section('title', 'Manage Roles')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Roles</h3>
        <a href="{{ url('/admin/roles/create') }}" class="btn btn-info btn-sm pull-right" style="float: right">
            <strong><i class="fas fa-plus"></i> NEW ROLE</strong>
        </a>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Role Name</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($roles as $key => $role)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $role->name }}</td>
                        <td>
                            {{-- Edit Button --}}
                            <a href="{{ url('/admin/roles/' . $role->id . '/edit') }}" title="Edit Details"
                                class="btn btn-xs btn-info">
                                <strong><i class="fas fa-edit"></i></strong>
                            </a>

                            @php $roles_not_to_delete = [1, 2, 3,5]; @endphp

                            @if (!in_array($role->id, $roles_not_to_delete))
                            {{-- Delete Form --}}
                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                @csrf
                                {{-- @method('DELETE') --}}
                                <input type="hidden" name="role" value="{{ $role->id }}">
                                <button type="submit" class="btn btn-danger btn-xs btn-flat"
                                    onclick="return confirm('Are you sure you want to delete this role?');">
                                    <strong><i class="fas fa-trash"></i></strong>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
    $(function () {
        $("#example1").DataTable({
            "responsive": false,
            "autoWidth": false,
            "ordering": false,
        });
    });
</script>
@stop