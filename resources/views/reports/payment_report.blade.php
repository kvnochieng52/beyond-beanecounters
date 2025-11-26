@extends('adminlte::page')

@section('title', 'Payment Report')

@section('content_header')
    <h1>Payment Report</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Generate Payment Report</h3>
        </div>

        <form method="POST" action="{{ route('reports.payment-report.generate') }}">
            @csrf
            <div class="card-body">
                <div class="row">
                    <!-- Date Range -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="from_date">From Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="from_date" id="from_date"
                                   placeholder="DD-MM-YYYY" value="{{ old('from_date') }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="to_date">To Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="to_date" id="to_date"
                                   placeholder="DD-MM-YYYY" value="{{ old('to_date') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Filter Options Row -->
                <div class="row">
                    <!-- Institution Filter -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="institution_id">Filter by Institution</label>
                            <select class="form-control" name="institution_id" id="institution_id">
                                <option value="">All Institutions</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}"
                                            {{ old('institution_id') == $institution->id ? 'selected' : '' }}>
                                        {{ $institution->institution_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Agent Filter -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="agent_id">Filter by Collection Agent</label>
                            <select class="form-control" name="agent_id" id="agent_id">
                                <option value="">All Agents</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}"
                                            {{ old('agent_id') == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }} @if($agent->agent_code) - {{ $agent->agent_code }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Report Type Selection -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Report Type</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="report_type"
                                               id="by_agent" value="by_agent" checked>
                                        <label class="form-check-label" for="by_agent">
                                            Report by Agent Name
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="report_type"
                                               id="by_date" value="by_date">
                                        <label class="form-check-label" for="by_date">
                                            Report by Date/Range
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="report_type"
                                               id="by_institution" value="by_institution">
                                        <label class="form-check-label" for="by_institution">
                                            Report by Institution
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="report_type"
                                               id="detailed" value="detailed">
                                        <label class="form-check-label" for="detailed">
                                            Detailed Report
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-chart-bar"></i> Generate Report
                        </button>
                        <button type="submit" name="export" value="excel" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </button>
                    </div>
                    <div class="col-md-6 text-right">
                        <small class="text-muted">
                            * Required fields
                        </small>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Information Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Report Information</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Report Types Available:</h5>
                    <ul>
                        <li><strong>Report by Agent Name:</strong> Groups payments by collection agent</li>
                        <li><strong>Report by Date/Range:</strong> Groups payments by date</li>
                        <li><strong>Report by Institution:</strong> Groups payments by institution</li>
                        <li><strong>Detailed Report:</strong> Shows all individual payment records</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Available Filters:</h5>
                    <ul>
                        <li>Date range (required)</li>
                        <li>Institution (optional)</li>
                        <li>Collection Agent (optional)</li>
                        <li>Payment Status (optional)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .form-check-input {
            margin-top: 0.3rem;
        }
        .form-check-label {
            font-weight: 500;
        }
        .card-header h3 {
            margin: 0;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function() {
            // Initialize date pickers
            flatpickr("#from_date", {
                dateFormat: "d-m-Y",
                maxDate: "today"
            });

            flatpickr("#to_date", {
                dateFormat: "d-m-Y",
                maxDate: "today"
            });

            // Form validation
            $('form').on('submit', function(e) {
                var fromDate = $('#from_date').val();
                var toDate = $('#to_date').val();

                if (!fromDate || !toDate) {
                    e.preventDefault();
                    alert('Please select both from and to dates.');
                    return false;
                }

                // Convert dates for comparison
                var from = new Date(fromDate.split('-').reverse().join('-'));
                var to = new Date(toDate.split('-').reverse().join('-'));

                if (from > to) {
                    e.preventDefault();
                    alert('From date cannot be later than to date.');
                    return false;
                }
            });
        });
    </script>
@stop