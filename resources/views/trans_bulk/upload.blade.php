@extends('adminlte::page')

@section('title', 'Bulk Upload')

@section('content_header')
{{-- <h1>Bulk Upload Leads</h1> --}}
@stop

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('errors'))
<div class="alert alert-danger">
    <ul>
        @foreach(session('errors') as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- @if($errors->any())
<div class="alert alert-danger">
    {{ implode('', $errors->all(':message')) }}
</div>
@endif --}}

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Upload Bulk Transactions</h3>
    </div>
    <div class="card-body">
        <p>Download the sample CSV file for <a href="/sample_csvs/trans_import_samplec.csv" target="_blank"><strong><i
                        class="fas fa-fw fa-file"></i> Download</strong></a>
        </p>


        <form action="{{ route('trans.bulk.process') }}" method="POST" enctype="multipart/form-data" class="user_form">


            @csrf

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="csv_file">Select CSV File*</label>
                        <input type="file" name="csv_file" class="form-control" required>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Html::label('Rules', 'rules[]') !!}
                        <div style="width: 100%">
                            {!! Html::select('rules[]', $rules)
                            ->class('form-control select2')
                            // ->placeholder('--Specify--')
                            ->attribute('style', 'width: 100%; padding: 10px;')
                            ->attribute('multiple','multiple')
                            // ->value($lead->rules_id)

                            !!}
                        </div>
                    </div>
                </div>
            </div>


            <button type="submit" class="btn btn-primary mt-3">Upload</button>
        </form>
    </div>
</div>
@stop

@section('css')

<link rel="stylesheet" href="/css/validator/bootstrapValidator.min.css" />
<style>
    .alert {
        margin-top: 20px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff !important;
        border: 1px solid #32a2ed !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #ffffff;
    }
</style>
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