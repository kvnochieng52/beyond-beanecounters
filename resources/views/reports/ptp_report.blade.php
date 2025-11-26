@extends('adminlte::page')

@section('title', 'PTP Report')

@section('content_header')
    <h1>PTP (Promise to Pay) Report</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Generate PTP Report</h3>
        </div>

        <form method="POST" action="{{ route('reports.ptp-report.generate') }}">
            @csrf
            <div class="card-body">
                <!-- Primary Date Range -->
                <div class="row">
                    <div class="col-md-12">
                        <h5><i class="fas fa-calendar"></i> Date Range Filter</h5>
                        <hr>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="from_date">From Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="from_date" id="from_date"
                                   placeholder="DD-MM-YYYY" value="{{ old('from_date') }}" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="to_date">To Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="to_date" id="to_date"
                                   placeholder="DD-MM-YYYY" value="{{ old('to_date') }}" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="date_filter_type">Date Filter Type</label>
                            <select class="form-control" name="date_filter_type" id="date_filter_type">
                                <option value="created" {{ old('date_filter_type') == 'created' ? 'selected' : '' }}>
                                    Filter by PTP Created Date
                                </option>
                                <option value="due" {{ old('date_filter_type') == 'due' ? 'selected' : '' }}>
                                    Filter by PTP Due Date
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Additional Filters -->
                <div class="row">
                    <div class="col-md-12">
                        <h5><i class="fas fa-filter"></i> Additional Filters</h5>
                        <hr>
                    </div>
                </div>

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

                    <!-- Assigned Agent Filter -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="agent_id">Filter by Assigned Agent</label>
                            <select class="form-control" name="agent_id" id="agent_id">
                                <option value="">All Assigned Agents</option>
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

                <div class="row">
                    <!-- PTP Created By Agent Filter -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="created_by_agent">Filter by PTP Created By</label>
                            <select class="form-control" name="created_by_agent" id="created_by_agent">
                                <option value="">All Agents</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}"
                                            {{ old('created_by_agent') == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }} @if($agent->agent_code) - {{ $agent->agent_code }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- PTP Due Date Range Filter -->
                <div class="row">
                    <div class="col-md-12">
                        <h5><i class="fas fa-clock"></i> PTP Due Date Range (Optional)</h5>
                        <hr>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ptp_due_from">PTP Due From</label>
                            <input type="text" class="form-control" name="ptp_due_from" id="ptp_due_from"
                                   placeholder="DD-MM-YYYY" value="{{ old('ptp_due_from') }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ptp_due_to">PTP Due To</label>
                            <input type="text" class="form-control" name="ptp_due_to" id="ptp_due_to"
                                   placeholder="DD-MM-YYYY" value="{{ old('ptp_due_to') }}">
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
                            * Required fields. Use date filters to narrow down results.
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
                    <h5>Available Filters:</h5>
                    <ul>
                        <li><strong>Institution:</strong> Filter PTPs by institution</li>
                        <li><strong>PTP Due Date:</strong> Filter PTPs by their due dates</li>
                        <li><strong>PTP Date Created:</strong> Filter PTPs by creation date</li>
                        <li><strong>Assigned Agent:</strong> Filter PTPs by lead's assigned agent</li>
                        <li><strong>PTP Created By:</strong> Filter PTPs by who created the PTP</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Report Includes:</h5>
                    <ul>
                        <li>Institution name</li>
                        <li>Ticket number and lead title</li>
                        <li>PTP creation details and dates</li>
                        <li>PTP amounts and due dates</li>
                        <li>Summary statistics and overdue analysis</li>
                        <li>Excel export functionality</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .card-header h3 {
            margin: 0;
        }
        .form-group label {
            font-weight: 600;
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

            flatpickr("#ptp_due_from", {
                dateFormat: "d-m-Y"
            });

            flatpickr("#ptp_due_to", {
                dateFormat: "d-m-Y"
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

                // Validate PTP due date range if provided
                var ptpDueFrom = $('#ptp_due_from').val();
                var ptpDueTo = $('#ptp_due_to').val();

                if (ptpDueFrom && ptpDueTo) {
                    var ptpFrom = new Date(ptpDueFrom.split('-').reverse().join('-'));
                    var ptpTo = new Date(ptpDueTo.split('-').reverse().join('-'));

                    if (ptpFrom > ptpTo) {
                        e.preventDefault();
                        alert('PTP Due From date cannot be later than PTP Due To date.');
                        return false;
                    }
                }
            });
        });
    </script>
@stop