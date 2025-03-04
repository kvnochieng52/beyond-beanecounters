@extends('adminlte::page')

@section('title', 'Roles:: Edit')

@section('content')

@if($errors->any())
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    @foreach($errors->all() as $error)
    {{ $error }}<br>
    @endforeach
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{ session('error') }}
</div>
@endif

<form action="{{ route('admin.roles.update', $role_details->id) }}" method="POST" class="form user_form"
    enctype="multipart/form-data">
    @csrf


    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Role</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="col-md-12">
                <input type="hidden" name="role_id" value="{{ $role_details->id }}">

                <div class="row">
                    <div class="col-md-6">
                        <label for="role">Enter the Role Name*</label>
                        <div class="form-group">
                            <input type="text" name="role" id="role" class="form-control"
                                placeholder="Enter the role name" value="{{ $role_details->name }}" required>
                        </div>
                    </div>
                </div>

                <hr />
                <h5>Role Permissions</h5>
                <p>Select the permissions for this role</p>

                @foreach($perm_groups as $group)
                <p style="text-transform: uppercase; margin-bottom:5px"><strong>{{ $group->group_name }}</strong></p>
                <div class="row" style="padding-bottom:15px">
                    @foreach ($group->permissions as $permission)
                    <div class="col-md-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                id="permission_{{ $permission->id }}" class="custom-control-input"
                                @if(in_array($permission->id, $role_permissions)) checked @endif>
                            <label for="permission_{{ $permission->id }}" class="custom-control-label"
                                style="font-weight: normal; font-size:13px">
                                {{ $permission->name }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach

                <hr />

                <button type="submit" class="btn btn-primary btn-flat">UPDATE DETAILS</button>
            </div>
        </div>
    </div>
</form>
@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
<link rel="stylesheet" href="/css/validator/bootstrapValidator.min.css" />
@stop

@section('js')
<script src="/js/validator/bootstrapValidator.min.js"></script>
<script>
    $(function () {
        $('.user_form').bootstrapValidator({
            excluded: [':disabled'],
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            }
        });
    });
</script>
@stop