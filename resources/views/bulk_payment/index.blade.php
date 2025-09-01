@extends('adminlte::page')

@section('title', 'Bulk Upload')

@section('content_header')
    {{-- <h1>Bulk Upload Leads</h1> --}}
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('errors'))
        <div class="alert alert-danger">
            <ul>
                @foreach (session('errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- @if ($errors->any())
<div class="alert alert-danger">
    {{ implode('', $errors->all(':message')) }}
</div>
@endif --}}

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Bulk Payment Update Tool</h3>
        </div>
        <div class="card-body">
            <p>Upload the CSV file (<b>CSV File must have Ticket No, Amount, and Description columns. Description values can
                    be empty</b>)</p>

            <div class="alert alert-info">
                <h5><i class="icon fas fa-info"></i> CSV Format Requirements:</h5>
                <ul>
                    <li><strong>Ticket No:</strong> The lead ID number (required)</li>
                    <li><strong>Amount:</strong> Payment amount - must be numeric and greater than 0 (required)</li>
                    <li><strong>Description:</strong> Payment description/notes - column required but values can be empty
                        (optional values)</li>
                </ul>
                <p><strong>Example CSV format:</strong></p>
                <code>
                    Ticket No,Amount,Description<br>
                    1,500.00,Mpesa Payment-MGTSSXBTEY<br>
                    2,1000.50, Cash Payment<br>
                    3,250.75, Cash Payment<br>
                    4,100.00, Mpesa Payment-DBMSSXBTYRF
                </code>
            </div>

            <form action="{{ route('bulk-payment-process') }}" method="POST" enctype="multipart/form-data" class="user_form">
                @csrf

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="csv_file">Select CSV File*</label>
                            <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required>
                            <small class="form-text text-muted">Only CSV file formats are allowed.</small>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">
                    <i class="fas fa-upload"></i> Upload & Process Payments
                </button>
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

        .alert-info code {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            display: block;
            margin-top: 10px;
            color: #333;
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

                if (selectedValue == 1) {
                    $('.individual_type').show();
                } else {
                    $('.entity_type').show();
                }
            });
        })
    </script>
@stop
