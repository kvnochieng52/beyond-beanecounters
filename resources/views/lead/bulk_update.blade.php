@extends('adminlte::page')

@section('title', 'Bulk Update Leads')

@section('content_header')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Bulk Update Leads</h3>
                <div class="card-tools">
                    <a href="{{ route('lead.bulk-update.template') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-download"></i> Download Template
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Instructions:</h5>
                    <ul>
                        <li>Download the CSV template using the button above</li>
                        <li>The first column must be <strong>ticket_no</strong> (Lead ID)</li>
                        <li>For lookup fields (like gender, country, etc.), enter the name exactly as it appears in the system</li>
                        <li>Leave fields empty if you don't want to update them</li>
                        <li>Only rows with valid ticket numbers will be processed</li>
                        <li>Maximum file size: 10MB</li>
                    </ul>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h4>Validation Errors:</h4>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('lead.bulk-update.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="csv_file">Select CSV File:</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                                <label class="custom-file-label" for="csv_file">Choose file</label>
                            </div>
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-upload"></i> Upload & Process
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="mt-4">
                    <h5>Supported Fields for Update:</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Direct Fields:</h6>
                            <ul class="small">
                                <li>title</li>
                                <li>id_passport_number</li>
                                <li>account_number</li>
                                <li>telephone</li>
                                <li>alternate_telephone</li>
                                <li>email</li>
                                <li>alternate_email</li>
                                <li>town</li>
                                <li>address</li>
                                <li>occupation</li>
                                <li>company_name</li>
                                <li>description</li>
                                <li>kin_full_names</li>
                                <li>kin_telephone</li>
                                <li>kin_email</li>
                                <li>kin_relationship</li>
                                <li>amount</li>
                                <li>additional_charges</li>
                                <li>balance</li>
                                <li>waiver_discount</li>
                                <li>due_date</li>
                                <li>last_ptp_amount</li>
                                <li>last_ptp_date</li>
                                <li>last_retire_date</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Lookup Fields (Enter Name):</h6>
                            <ul class="small">
                                <li>defaulter_type_name</li>
                                <li>gender_name</li>
                                <li>country_name</li>
                                <li>institution_name</li>
                                <li>currency_name</li>
                                <li>status_name</li>
                                <li>stage_name</li>
                                <li>category_name</li>
                                <li>priority_name</li>
                                <li>industry_name</li>
                                <li>conversion_status_name</li>
                                <li>engagement_level_name</li>
                                <li>assigned_agent_name</li>
                                <li>assigned_department_name</li>
                                <li>call_disposition_name</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
@stop

@section('js')
<script>
$(document).ready(function() {
    // Update file input label with selected file name
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
    });
});
</script>
@stop