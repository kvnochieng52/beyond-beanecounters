@extends('adminlte::page')

@section('title', 'Weekly Agent Performance Report Results')

@section('content_header')
    <h1>Weekly Agent Performance Report Results</h1>
    <div class="row">
        <a href="{{ route('reports.weekly-agent-performance') }}" class="btn btn-default">
            <i class="fas fa-arrow-left"></i> Back to Report Form
        </a>
        <a href="{{ route('reports.weekly-agent-performance.generate', array_merge($data['filters'], ['export' => 'excel'])) }}"
            class="btn btn-success">
            <i class="fas fa-file-excel"></i> Export to Excel
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Report Summary</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-calendar-week"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Report Period</span>
                                    <span class="info-box-number">{{ $data['period'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-users"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active Agents</span>
                                    <span class="info-box-number">{{ $data['agents']->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-money-check-alt"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Weekly Total</span>
                                    <span
                                        class="info-box-number">{{ number_format($data['agents']->sum('total_collected'), 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">MTD Period</span>
                                    <span class="info-box-number">{{ $data['mtd_start'] }} - Today</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-handshake"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total PTPs</span>
                                    <span class="info-box-number">{{ $data['agents']->sum('ptp_count') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-money-bill-wave"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total PTP Value</span>
                                    <span
                                        class="info-box-number">{{ number_format($data['agents']->sum('ptp_value'), 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($data['agents']->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Weekly Agent Performance Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="reports-table">
                                <thead>
                                    <tr>
                                        <th>Agent Name</th>
                                        <th>Agent Code</th>
                                        <th>Avg. Dispositions/Day</th>
                                        <th>PTP Count</th>
                                        <th>PTP Value</th>
                                        <th>Total Collected (Week)</th>
                                        <th>MTD Collected</th>
                                        @foreach ($data['institutions'] as $instId => $instName)
                                            <th>{{ $instName }} (Week)</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['agents'] as $agent)
                                        <tr>
                                            <td>{{ $agent['agent_name'] }}</td>
                                            <td>{{ $agent['agent_code'] }}</td>
                                            <td>{{ $agent['average_dispositions'] }}</td>
                                            <td>{{ $agent['ptp_count'] }}</td>
                                            <td>{{ number_format($agent['ptp_value'], 2) }}</td>
                                            <td>{{ number_format($agent['total_collected'], 2) }}</td>
                                            <td>{{ number_format($agent['mtd_collected'], 2) }}</td>
                                            @foreach ($data['institutions'] as $instId => $instName)
                                                <td>{{ number_format($agent['inst_' . $instId] ?? 0, 2) }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">TOTALS</th>
                                        <th>{{ number_format($data['agents']->avg('average_dispositions'), 0) }}</th>
                                        <th>{{ $data['agents']->sum('ptp_count') }}</th>
                                        <th>{{ number_format($data['agents']->sum('ptp_value'), 2) }}</th>
                                        <th>{{ number_format($data['agents']->sum('total_collected'), 2) }}</th>
                                        <th>{{ number_format($data['agents']->sum('mtd_collected'), 2) }}</th>
                                        @foreach ($data['institutions'] as $instId => $instName)
                                            <th>{{ number_format($data['agents']->sum('inst_' . $instId), 2) }}</th>
                                        @endforeach
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h4>No Data Found</h4>
                        <p>No agent activity was found for the selected period.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(function() {
            $('#reports-table').DataTable({
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "ordering": true,
                "info": true,
                "paging": true,
                "searching": true,
                "scrollX": true,
                "pageLength": 25,
                "order": [
                    [5, "desc"]
                ], // Order by Total Collected (Week) descending
                "columnDefs": [{
                        "targets": [2, 3, 4, 5,
                        6], // Average dispositions, PTP count, PTP value, total collected, MTD collected
                        "className": "text-right"
                    },
                    @if (count($data['institutions']) > 0)
                        {
                            "targets": [
                                {{ implode(',', range(7, 7 + count($data['institutions']) - 1)) }}
                            ], // Institution columns
                            "className": "text-right"
                        }
                    @endif
                ]
            });
        });
    </script>
@stop
