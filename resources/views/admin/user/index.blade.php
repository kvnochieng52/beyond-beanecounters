@extends('adminlte::page')

@section('title', 'All Users')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Users</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Names</th>
                        <th>Email</th>
                        <th>Telephone</th>
                        <th>Role</th>
                        <th>Active</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $key=>$user)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td><a href="{{ url('admin/users/'.$user->id.'/edit') }}"><b>{{$user->user_full_names}}</b></a>
                        </td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->telephone}}</td>
                        <td>{{$user->role}}</td>
                        <td>
                            <a href="{{ url('admin/users/'.$user->id.'/edit') }}">
                                @if($user->is_active == 1)
                                <span class="badge badge-success">Active</span>
                                @else
                                <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </a>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="d-inline"
                                onsubmit="return confirm('Are you sure you want to delete this User?');">
                                @csrf
                                @method('DELETE')
                                <a href="{{ url('admin/users/'.$user->id.'/edit') }}" title="Edit Details"
                                    class="btn btn-xs btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="submit" class="btn btn-xs btn-secondary btn-flat">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
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