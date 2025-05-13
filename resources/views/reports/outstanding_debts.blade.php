@extends('adminlte::page')

@section('title', 'Outstanding Debts Report')

@section('content_header')
    <h1>Outstanding Debts Report</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Generate Outstanding Debts Report</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.outstanding-debts.generate') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="as_of_date">As of Date</label>
                            <input type="date" class="form-control @error('as_of_date') is-invalid @enderror" id="as_of_date" name="as_of_date" value="{{ old('as_of_date', date('Y-m-d')) }}" required>
                            @error('as_of_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="institution_id">Institution (Optional)</label>
                            <select class="form-control @error('institution_id') is-invalid @enderror" id="institution_id" name="institution_id">
                                <option value="">All Institutions</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}" {{ old('institution_id') == $institution->id ? 'selected' : '' }}>
                                        {{ $institution->institution_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('institution_id')
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
                            <label for="min_days_overdue">Minimum Days Overdue (Optional)</label>
                            <input type="number" class="form-control @error('min_days_overdue') is-invalid @enderror" id="min_days_overdue" name="min_days_overdue" value="{{ old('min_days_overdue') }}" min="0">
                            @error('min_days_overdue')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="max_days_overdue">Maximum Days Overdue (Optional)</label>
                            <input type="number" class="form-control @error('max_days_overdue') is-invalid @enderror" id="max_days_overdue" name="max_days_overdue" value="{{ old('max_days_overdue') }}" min="0">
                            @error('max_days_overdue')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                    <button type="submit" name="export" value="excel" class="btn btn-success">Export to Excel</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Set default date if not already set
            if (!$('#as_of_date').val()) {
                // Today
                $('#as_of_date').val(new Date().toISOString().slice(0, 10));
            }
        });
    </script>
@stop
