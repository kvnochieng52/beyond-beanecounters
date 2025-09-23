@extends('adminlte::page')

@section('title', 'Leads Report')

@section('content_header')
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Leads Report</h4>
        <p class="text-muted">Generate comprehensive leads report based on selected filters. Reports are processed in the background.</p>
    </div>

    <div class="card-body">
        @include('notices')

        <form action="{{ route('reports.leads.generate') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Html::label('From Date (Lead Creation From Date)*', 'from_date') !!}
                        <input type="date" class="form-control" name="from_date" id="from_date" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Html::label('To Date (Lead Creation To Date)*', 'to_date') !!}
                        <input type="date" class="form-control" name="to_date" id="to_date" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="institution_ids[]">Select Institution(s) (Leave Blank for All)</label>
                        <select multiple class="form-control select2 @error('institution_ids[]') is-invalid @enderror"
                            id="institution_ids[]" name="institution_ids[]">
                            @foreach ($institutions as $institution)
                                <option value="{{ $institution->id }}">
                                    {{ $institution->institution_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('institution_ids[]')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status_ids[]">Select Status(es) (Leave Blank for All)</label>
                        <select multiple class="form-control select2 @error('status_ids[]') is-invalid @enderror"
                            id="status_ids[]" name="status_ids[]">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}">
                                    {{ $status->lead_status_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('status_ids[]')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="priority_ids[]">Select Priority(ies) (Leave Blank for All)</label>
                        <select multiple class="form-control select2 @error('priority_ids[]') is-invalid @enderror"
                            id="priority_ids[]" name="priority_ids[]">
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority->id }}">
                                    {{ $priority->lead_priority_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority_ids[]')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category_ids[]">Select Category(ies) (Leave Blank for All)</label>
                        <select multiple class="form-control select2 @error('category_ids[]') is-invalid @enderror"
                            id="category_ids[]" name="category_ids[]">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->lead_category_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_ids[]')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Html::label('Minimum Amount (Leave blank for no minimum)', 'min_amount') !!}
                        <input type="number" class="form-control @error('min_amount') is-invalid @enderror"
                               name="min_amount" id="min_amount" step="0.01" min="0" placeholder="0.00">
                        @error('min_amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Html::label('Maximum Amount (Leave blank for no maximum)', 'max_amount') !!}
                        <input type="number" class="form-control @error('max_amount') is-invalid @enderror"
                               name="max_amount" id="max_amount" step="0.01" min="0" placeholder="0.00">
                        @error('max_amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> This report will be processed in the background. Once completed, you can download it from the
                        <a href="{{ route('background-reports.index') }}" target="_blank">Background Reports</a> page.
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-cog fa-spin" style="display: none;" id="loading-icon"></i>
                    <i class="fas fa-file-export" id="export-icon"></i>
                    Generate Leads Report
                </button>
                <a href="{{ route('background-reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-clock"></i>
                    View Background Reports
                </a>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css">
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap',
        placeholder: 'Select options...',
        allowClear: true
    });

    // Handle form submission
    $('form').on('submit', function() {
        $('#loading-icon').show();
        $('#export-icon').hide();
        $('button[type="submit"]').prop('disabled', true).text('Processing...');
    });

    // Set default dates to current month
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    $('#from_date').val(firstDay.toISOString().substr(0, 10));
    $('#to_date').val(lastDay.toISOString().substr(0, 10));

    // Amount range validation
    $('#min_amount, #max_amount').on('input', function() {
        const minAmount = parseFloat($('#min_amount').val()) || 0;
        const maxAmount = parseFloat($('#max_amount').val()) || 0;

        if (minAmount > 0 && maxAmount > 0 && maxAmount < minAmount) {
            $('#max_amount')[0].setCustomValidity('Maximum amount must be greater than or equal to minimum amount');
        } else {
            $('#max_amount')[0].setCustomValidity('');
        }
    });
});

// Show success/error messages
@if(session('success'))
    toastr.success('{{ session('success') }}');
@endif

@if(session('error'))
    toastr.error('{{ session('error') }}');
@endif
</script>
@stop