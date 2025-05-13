@extends('adminlte::page')

@section('title', 'New Lead')

@section('content_header')
{{-- <h1>Create Defaulter</h1> --}}
@stop

@section('content')
<div class="card">

    <div class="card-header">
        <h3 class="card-title">New Lead</h3>
        <p style="float: right; margin-bottom:0px">Fields marked * are mandatory </p>
    </div>


    <div class="card-body">

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Html::label('Select Defaulter Type *', 'Defaulter Type') !!}
                    {!!
                    Html::select('defaulter_type',$defaulterTypes)
                    ->id('defaulter_type')
                    ->class('form-control select2')
                    ->placeholder('--Specify--')
                    // ->required()

                    !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <hr />
            </div>
        </div>

        <div class="individual_type defaulter_type" style="display: none">
            @include('lead.create._individual_type')
        </div>

        <div class="entity_type defaulter_type" style="display: none">
            @include('lead.create._entity_type')
        </div>

    </div>
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


        $('#defaulter_type').on('change', function() {
            var selectedValue = $(this).val();


            $(".defaulter_type_store").val(selectedValue);
    
            $('.defaulter_type').hide();

            if(selectedValue==1){
                $('.individual_type').show();
            }else{
               $('.entity_type').show(); 
            }
           
        });
    })
</script>
@stop