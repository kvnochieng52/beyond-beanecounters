@extends('adminlte::page')

@section('title', 'Defaulter:: Create')

@section('content_header')
{{-- <h1>Create Defaulter</h1> --}}
@stop

@section('content')
<div class="card">

    <div class="card-header">
        <h3 class="card-title">New Defaulter</h3>
        <p style="float: right; margin-bottom:0px">Fields marked * are mandatory </p>
    </div>


    <div class="card-body">
        <form action="{{ route('lead.store') }}" method="POST" class="user_form">
            @csrf {{-- Add CSRF protection --}}

            <div class="row">
                <div class="col-md-12">
                    <h6><strong><i class="fas fa-fw fa-user "></i> BASIC DETAILS</strong></h6>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('First Name*', 'first_name') !!}
                        {!! Html::text('first_name')->class('form-control')
                        ->placeholder('Enter First Name')
                        ->required()
                        !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('Middle Name*', 'middle_name') !!}
                        {!! Html::text('middle_name')->class('form-control')
                        ->placeholder('Enter Middle Name')
                        ->required() !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('Last Name*', 'last_name') !!}
                        {!! Html::text('last_name')->class('form-control')
                        ->placeholder('Enter Last Name')
                        ->required()
                        !!}
                    </div>
                </div>
            </div>


            <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('ID Number*', 'id_number') !!}
                        {!! Html::text('id_number')->class('form-control')
                        ->placeholder('Enter ID Number')
                        ->required()
                        !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('Telephone*', 'telephone') !!}
                        {!! Html::text('telephone')->class('form-control')
                        ->placeholder('Enter Telephone')
                        ->required()
                        !!}
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('Email*', 'email') !!}
                        {!! Html::email('email')->class('form-control')
                        ->placeholder('Enter Email')
                        ->required()
                        !!}
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('Gender *', 'Gender') !!}
                        {!! Html::select('gender',
                        $genders)->class('form-control')->placeholder('--Specify--')->required() !!}
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-12">
                    <hr />
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <h6><strong><i class="fas fa-fw fa-map"></i> ADDRESS</strong></h6>
                </div>
            </div>


            <div class="row">

                <div class="col-md-12">
                    <div class="form-group">
                        {!! Html::label('Address', 'address') !!}
                        {!! Html::textarea('address')->class('form-control')
                        ->placeholder('Enter Address')
                        !!}
                    </div>

                </div>
            </div>


            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('Country', 'country') !!}
                        {!! Html::select('country',
                        $countries)->class('form-control select2')->placeholder('--Specify--') !!}
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('Town', 'town') !!}
                        {!! Html::text('town')->class('form-control')
                        ->placeholder('Enter town')
                        !!}
                    </div>

                </div>
            </div>

            <div class="row">

                <div class="col-md-12">
                    <hr />
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <h6><strong><i class="fas fa-fw fa-briefcase"></i> WORK</strong></h6>
                </div>
            </div>


            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('Occupation', 'occupation') !!}
                        {!! Html::text('occupation')->class('form-control')
                        ->placeholder('Enter Occupation')
                        !!}
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('Company', 'company') !!}
                        {!! Html::text('company')->class('form-control')
                        ->placeholder('Enter Company')
                        !!}
                    </div>

                </div>
            </div>


            <div class="row">

                <div class="col-md-12">
                    <hr />
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <h6><strong><i class="fas fa-fw fa-users"></i> NEXT OF KIN</strong></h6>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('Next of Kin Name', 'kin_name') !!}
                        {!! Html::text('kin_name')->class('form-control')
                        ->placeholder('Enter Next Of Kin Name')
                        !!}
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('Telephone', 'kin_telephone') !!}
                        {!! Html::text('kin_telephone')->class('form-control')
                        ->placeholder('Enter Next of Kin Telephone')
                        !!}
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('Email', 'kin_email') !!}
                        {!! Html::text('kin_email')->class('form-control')
                        ->placeholder('Enter Next of Kin Email')
                        !!}
                    </div>

                </div>


                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('Relation', 'kin_relation') !!}
                        {!! Html::text('kin_relation')->class('form-control')
                        ->placeholder('Enter Next of Kin Relation')
                        !!}
                    </div>

                </div>
            </div>
            <button type="submit" class="btn btn-primary"><strong>SUBMIT DETAILS</strong></button>
    </div>

    </form>
</div>
</div>
@stop

@section('css')
{{-- Add here extra stylesheets --}}
{{--
<link rel="stylesheet" href="/css/admin_custom.css"> --}}

<link rel="stylesheet" href="/css/validator/bootstrapValidator.min.css" />

@stop

@section('js')
<script src="/js/validator/bootstrapValidator.min.js"></script>

<script>
    $(document).ready(function() {
    
        $('.user_form')
            .bootstrapValidator({
            excluded: [':disabled'],
            feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
            },
        });

        $('.select2').select2();
    })
</script>
@stop