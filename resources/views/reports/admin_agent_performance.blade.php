@extends('adminlte::page')

@section('title', 'Admin Agent Performance Report')

@section('content_header')
    <h1>Admin Agent Performance Report</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Filter Options</h3>
        </div>
        <form action="{{ route('reports.admin-agent-performance.generate') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_from">From Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                value="{{ date('Y-m-01') }}" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_to">To Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date_to" name="date_to"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="institution_id">Institution</label>
                            <select class="form-control" id="institution_id" name="institution_id" style="width: 100%">
                                <option value="">-- All Institutions --</option>
                                @foreach ($institutions as $institution)
                                    <option value="{{ $institution->id }}">{{ $institution->institution_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="agent_id">Agent</label>
                            <select class="form-control" id="agent_id" name="agent_id" style="width: 100%">
                                <option value="">-- All Agents --</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="created_by_agent">Created By Agent</label>
                            <select class="form-control" id="created_by_agent" name="created_by_agent" style="width: 100%">
                                <option value="">-- All --</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Generate Report
                </button>
            </div>
        </form>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#institution_id, #agent_id, #created_by_agent').select2({
                placeholder: "Select an option"
            });
        });
    </script>
@stop
