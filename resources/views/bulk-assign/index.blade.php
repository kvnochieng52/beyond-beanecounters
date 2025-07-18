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
        <h3 class="card-title">Bulk Assign/Re-Assign Leads</h3>
    </div>
    <div class="card-body">
        <p> <a href="/sample_csvs/sample_bulk_assign.csv" target="_blank"><strong><i class="fas fa-fw fa-user"></i>
                    Download</strong></a> Sample CSV file

        </p>


        <form action="{{ route('bulk-assign-upload') }}" method="POST" enctype="multipart/form-data" class="user_form">


            @csrf


            <div class="form-group mt-3">
                <label>What do you what to do?*</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="action" id="assign" value="assign">
                    <label class="form-check-label" for="assign">Assign Leads</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="action" id="re-assign" value="re-assign">
                    <label class="form-check-label" for="re-assign">Re-Assign Leads</label>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="csv_file">Select CSV File*</label>
                        <input type="file" name="csv_file" class="form-control" required>
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