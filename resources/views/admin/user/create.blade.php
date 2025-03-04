@extends('adminlte::page')

@section('title', 'New User')

@section('content')

@include('notices')

<form action="{{ route('admin.users.store') }}" method="POST" class="form user_form" enctype="multipart/form-data">
    @csrf

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">New User</h3>
        </div>

        <div class="card-body">

            <div class="row">
                <div class="col-md-12">
                    <label for="full_names">Full Names*</label>
                    <div class="form-group">
                        <input type="text" name="full_names" class="form-control" placeholder="User Full Names"
                            required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="email">Email</label>
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Enter The User Email"
                            required>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="password">Password*</label>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control"
                            placeholder="Enter a strong password" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="password_confirmation">Confirm Password*</label>
                    <div class="form-group">
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="Confirm Password" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="telephone">Telephone*</label>
                    <div class="form-group">
                        <input type="text" name="telephone" class="form-control" placeholder="User Telephone" required>
                    </div>
                </div>


                <div class="col-md-4">
                    <label for="telephone">ID Number*</label>
                    <div class="form-group">
                        <input type="text" name="id_number" class="form-control" placeholder="User ID Number" required>
                    </div>
                </div>


                {{-- <div class="col-md-4">
                    <label for="telephone">Agent Code</label>
                    <div class="form-group">
                        <input type="text" name="id_number" class="form-control" placeholder="User ID Number" required>
                    </div>
                </div> --}}

                <div class="col-md-4">
                    <label for="role">Role</label>
                    <div class="form-group">
                        <select name="role[]" class="form-control select2" multiple required>
                            @foreach($roles as $key=>$role)
                            <option value="{{ $key }}">{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="is_active">Active*</label>
                    <div class="form-group">
                        <select name="is_active" class="form-control" required>
                            <option value="">--Select--</option>
                            <option value="0">Inactive</option>
                            <option value="1">Active</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="region">Region</label>
                    <div class="form-group">
                        <select name="region[]" class="form-control select2" multiple>
                            @foreach($regions as $key=>$region)
                            <option value="{{ $key }}">{{ $region }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">SUBMIT DETAILS</button>

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
        $('.select2').select2();
        
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