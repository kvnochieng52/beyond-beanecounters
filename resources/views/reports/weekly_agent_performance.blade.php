@extends('adminlte::page')

@section('title', 'Weekly Agent Performance Report')

@section('content_header')
    <h1>Weekly Agent Performance Report</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Generate Weekly Agent Performance Report</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.weekly-agent-performance.generate') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Html::label('From Date*', 'start_date') !!}
                            <input type="text" class="form-control date" placeholder="From Date" name="start_date"
                                id="start_date" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Html::label('To Date*', 'end_date') !!}
                            <input type="text" class="form-control date" placeholder="To Date" name="end_date"
                                id="end_date" autocomplete="off" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Html::label('Agent (Optional)', 'agent_id') !!}
                            <select class="form-control select2" name="agent_id" id="agent_id">
                                <option value="">All Agents</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}
                                        ({{ $agent->agent_code ?? ($agent->code ?? 'N/A') }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" name="action" value="generate" class="btn btn-primary">
                        <i class="fas fa-chart-line"></i> Generate Report
                    </button>
                    <button type="submit" name="export" value="excel" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@stop

@section('js')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(function() {
            $('.date').datepicker({
                dateFormat: 'mm/dd/yy'
            });

            // Set default dates - current week Monday to today
            var today = new Date();
            var currentDay = today.getDay();
            var daysFromMonday = currentDay === 0 ? 6 : currentDay - 1; // Sunday = 0, Monday = 1

            var monday = new Date(today);
            monday.setDate(today.getDate() - daysFromMonday);

            $('#start_date').datepicker('setDate', monday);
            $('#end_date').datepicker('setDate', today);

            $('.select2').select2();
        });
    </script>
@stop
