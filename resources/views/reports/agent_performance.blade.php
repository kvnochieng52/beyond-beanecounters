@extends('adminlte::page')

@section('title', 'Agent Performance Report')

@section('content_header')
    <h1>Agent Performance Report</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Generate Agent Performance Report</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.agent-performance.generate') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-01')) }}" required>
                            @error('start_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', date('Y-m-d')) }}" required>
                            @error('end_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
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
            // Set default dates if not already set
            if (!$('#start_date').val()) {
                // First day of current month
                $('#start_date').val(new Date().toISOString().slice(0, 8) + '01');
            }
            if (!$('#end_date').val()) {
                // Today
                $('#end_date').val(new Date().toISOString().slice(0, 10));
            }
        });
    </script>
@stop
