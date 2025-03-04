@extends('adminlte::page')

@section('title', 'Roles:: Edit')

{{-- @section('content_header')
<h1>Candidates</h1>
@stop --}}

@section('content')

@if(count($errors)>0)
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    @foreach($errors->all() as $error)
    {{$error}}<br>
    @endforeach
</div>
@endif


@if(session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    {{session('success')}}
</div>
@endif


@if(session('error'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    {{session('error')}}
</div>
@endif

{!!
Form::open(['action'=>['Admin\\UserController@update_role'],'method'=>'POST','class'=>'form
user_form','enctype'=>'multipart/form-data'])
!!}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Role</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">

        <div class="col-md-12">

            <input type="hidden" name="role_id" value="{{$role_details->id}}">

            <div class="row">
                <div class="col-md-6">
                    {{Form::label('role', 'Enter the Role Name* ')}}
                    <div class="form-group">
                        {{Form::text('role',$role_details->name,['class'=>'form-control',
                        'placeholder'=>'Enter the role name',
                        'required'=>'required'])}}
                    </div>
                </div>

            </div>




            <hr />

            <h5>Role Permissions</h5>
            <p>Select the permission for this roles</p>

            @foreach($perm_groups as $group)
            <p style="text-transform: uppercase; margin-bottom:5px"><strong>{{$group->group_name}}</strong></p>

            <div class="row" style="padding-bottom:15px">
                @foreach ($group->permissions as $permission)

                <div class="col-md-2">

                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" name="permissions[]"
                            id="{{$permission->id}}" value="{{$permission->id}}"
                            @if(in_array($permission->id,$role_permissions)) checked @endif
                        >
                        <label for="{{$permission->id}}" class="custom-control-label"
                            style="font-weight: normal; font-size:13px">{{$permission->name}}
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
{!! Form::close() !!}
@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
<link rel="stylesheet"
    href="//cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.2/css/bootstrapValidator.min.css" />
@stop

@section('js')
<script src="https://oss.maxcdn.com/jquery.bootstrapvalidator/0.5.2/js/bootstrapValidator.min.js"></script>
<script>
    $(function () {
      $('.user_form')
            .bootstrapValidator({
                // Only disabled elements are excluded
                // The invisible elements belonging to inactive tabs must be validated
                excluded: [':disabled'],
                feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
                },
            })
   
    });
</script>
@stop