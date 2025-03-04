@extends('adminlte::page')

@section('title', 'Edit User')

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
Form::open(['action'=>['Admin\\UserController@update',$user->id],'method'=>'POST','class'=>'form
user_form','enctype'=>'multipart/form-data'])
!!}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit User</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">

        <div class="col-md-12">

            <div class="row">
                <div class="col-md-12">
                    {{Form::label('full_names', 'Full Names* ')}}
                    <div class="form-group">
                        {{Form::text('full_names', $user->user_full_names,['class'=>'form-control', 'placeholder'=>'User
                        Full Names',
                        'required'=>'required'])}}
                    </div>
                </div>


            </div>

            <div class="row">

                <div class="col-md-4">
                    {{Form::label('email', 'Email')}}
                    <div class="form-group">
                        {{Form::email('email', $user->email,['class'=>'form-control', 'placeholder'=>'Enter The user
                        Email', 'required'=>'required'])}}
                    </div>
                </div>

                <div class="col-md-4">
                    {{Form::label('password', 'Password')}}
                    <div class="form-group">
                        {{Form::password('password',['class'=>'form-control', 'placeholder'=>'Enter a strong
                        Password'])}}
                    </div>
                </div>

                <div class="col-md-4">
                    {{Form::label('password_confirmation', 'Confirm Password* ')}}
                    <div class="form-group">
                        {{Form::password('password_confirmation',['class'=>'form-control', 'placeholder'=>'Enter a
                        strong Password'])}}
                    </div>
                </div>

                <div class="col-md-4">
                    {{Form::label('telephone', 'Telephone* ')}}
                    <div class="form-group">
                        {{Form::text('telephone', $user->telephone,['class'=>'form-control', 'placeholder'=>'User
                        Telephpone', 'required'=>'required'])}}
                    </div>
                </div>

                <div class="col-md-4">

                    {{Form::label('role', 'Role')}}

                    <div class="form-group">
                        {{ Form::select('role[]', $roles,$selected_user_roles, ['class' =>
                        'form-control select2','required'=>'required','multiple'=>true]) }}
                    </div>

                </div>



                <div class="col-md-4">

                    {{Form::label('role', 'Active')}}

                    <div class="form-group">
                        {{ Form::select('is_active', ['in Active', 'Active'],$user->is_active, ['class' =>
                        'form-control','placeholder'=>'--None--','required'=>'required']) }}
                    </div>

                </div>


                <div class="col-md-4">

                    {{Form::label('region', 'Region')}}
                    <div class="form-group">
                        {{ Form::select('region[]', $regions,$userRegions, ['class' =>
                        'form-control select2', "multiple"=>true]) }}
                    </div>

                </div>

            </div>




            <hr />

            <h5>Permissions</h5>

            @foreach($perm_groups as $group)
            <p style="text-transform: uppercase; margin-bottom:5px"><strong>{{$group->group_name}}</strong></p>

            <div class="row" style="padding-bottom:15px">
                @foreach ($group->permissions as $permission)

                <div class="col-md-2">

                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" name="permissions[]"
                            id="{{$permission->id}}" value="{{$permission->id}}"
                            @if(in_array($permission->id,$user_permissions)) checked @endif
                        @if($user_role!=0) disabled @endif
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
{{Form::hidden('_method','PUT')}} {!! Form::close() !!}
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
        $('.select2').select2({});
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