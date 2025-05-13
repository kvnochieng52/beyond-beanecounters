@extends('adminlte::page')

@section('title', 'Edit User')

@section('content')

@if(count($errors) > 0)
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

<form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="form user_form"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit User</h3>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <label for="full_names">Full Names*</label>
                        <input type="text" name="full_names" value="{{ $user->user_full_names }}" class="form-control"
                            placeholder="User Full Names" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label for="email">Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" class="form-control"
                            placeholder="Enter The User Email" required>
                    </div>

                    <div class="col-md-4">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control"
                            placeholder="Enter a Strong Password">
                    </div>

                    <div class="col-md-4">
                        <label for="password_confirmation">Confirm Password*</label>
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="Confirm Password">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label for="telephone">Telephone*</label>
                        <input type="text" name="telephone" value="{{ $user->telephone }}" class="form-control"
                            placeholder="User Telephone" required>
                    </div>

                    <div class="col-md-4">
                        <label for="telephone">ID Number*</label>
                        <div class="form-group">
                            <input type="text" name="id_number" class="form-control" placeholder="User ID Number"
                                value="{{ $user->id_number }}" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="role">Role</label>
                        <select name="role[]" class="form-control select2" multiple required>
                            @foreach($roles as $key=>$role)
                            <option value="{{ $key }}" {{ in_array($key, $selected_user_roles) ? 'selected' : '' }}>{{
                                $role }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-4">
                        <label for="region">Region</label>
                        <div class="form-group">
                            <select name="region[]" class="form-control select2" multiple>
                                @foreach($regions as $key=>$region)
                                <option value="{{ $key }}" {{ in_array($key, $selected_user_regions) ? 'selected' : ''
                                    }}>{{ $region }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="is_active">Active</label>
                        <select name="is_active" class="form-control" required>
                            <option value="0" {{ $user->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                            <option value="1" {{ $user->is_active == 1 ? 'selected' : '' }}>Active</option>
                        </select>
                    </div>


                </div>

                <hr />

                <h5>Permissions</h5>
                @foreach($perm_groups as $group)
                <p style="text-transform: uppercase; margin-bottom:5px"><strong>{{ $group->group_name }}</strong></p>
                <div class="row" style="padding-bottom:15px">
                    @foreach ($group->permissions as $permission)
                    <div class="col-md-2">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" name="permissions[]"
                                id="permission_{{ $permission->id }}" value="{{ $permission->id }}"
                                @if(in_array($permission->id, $user_permissions)) checked @endif>
                            <label for="permission_{{ $permission->id }}" class="custom-control-label"
                                style="font-weight: normal; font-size:13px">{{ $permission->name }}</label>
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
        $('.select2').select2({});
        $('.user_form').bootstrapValidator({
            excluded: [':disabled'],
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
        });
    });
</script>
@stop