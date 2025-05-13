@extends('adminlte::page')

@section('title', 'Agent Performance Report Results')

@section('content_header')
    <h1>Agent Performance Report Results</h1>
    <div class="float-right">
        <a href="{{ route('reports.agent-performance') }}" class="btn btn-default">
            <i class="fas fa-arrow-left"></i> Back to Report Form
        </a>
        <a href="{{ route('reports.agent-performance.generate', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Export to Excel
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Report Summary</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Date Range</th>
                            <td>{{ $data['start_date'] }} to {{ $data['end_date'] }}</td>
                        </tr>
                        <tr>
                            <th>Total Assigned Amount</th>
                            <td>{{ number_format($data['total_assigned'], 2) }}</td>
                        </tr>
                        <tr>
                            <th>Total Collected Amount</th>
                            <td>{{ number_format($data['total_collected'], 2) }}</td>
                        </tr>
                        <tr>
                            <th>Overall Collection Rate</th>
                            <td>
                                <span class="badge bg-{{ $data['overall_collection_rate'] >= 70 ? 'success' : ($data['overall_collection_rate'] >= 40 ? 'warning' : 'danger') }}">
                                    {{ number_format($data['overall_collection_rate'], 2) }}%
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top Performers</h3>
                </div>
                <div class="card-body">
                    <canvas id="topPerformersChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Agent Performance Details</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="agents-table">
                    <thead>
                        <tr>
                            <th>Agent Name</th>
                            <th>Total Leads</th>
                            <th>Assigned Amount</th>
                            <th>Collected Amount</th>
                            <th>Collection Rate</th>
                            <th>Closed Leads</th>
                            <th>Overdue Cases</th>
                            <th>Avg Days to Close</th>
                            <th>Performance Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['agents'] as $agent)
                        @php
                            // Calculate performance rating
                            $rate = $agent['collection_rate'];
                            if ($rate >= 90) {
                                $rating = 'Excellent';
                                $ratingClass = 'success';
                            } elseif ($rate >= 75) {
                                $rating = 'Very Good';
                                $ratingClass = 'success';
                            } elseif ($rate >= 60) {
                                $rating = 'Good';
                                $ratingClass = 'info';
                            } elseif ($rate >= 40) {
                                $rating = 'Average';
                                $ratingClass = 'warning';
                            } elseif ($rate >= 20) {
                                $rating = 'Below Average';
                                $ratingClass = 'warning';
                            } else {
                                $rating = 'Poor';
                                $ratingClass = 'danger';
                            }
                        @endphp
                        <tr>
                            <td>{{ $agent['name'] }}</td>
                            <td>{{ $agent['total_leads'] }}</td>
                            <td>{{ number_format($agent['assigned_amount'], 2) }}</td>
                            <td>{{ number_format($agent['collected_amount'], 2) }}</td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar bg-{{ $agent['collection_rate'] >= 70 ? 'success' : ($agent['collection_rate'] >= 40 ? 'warning' : 'danger') }}" 
                                         role="progressbar" 
                                         style="width: {{ min($agent['collection_rate'], 100) }}%" 
                                         aria-valuenow="{{ $agent['collection_rate'] }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ number_format($agent['collection_rate'], 2) }}%
                                    </div>
                                </div>
                            </td>
                            <td>{{ $agent['closed_leads'] }}</td>
                            <td>
                                <span class="badge bg-{{ $agent['overdue_cases'] > 0 ? 'danger' : 'success' }}">
                                    {{ $agent['overdue_cases'] }}
                                </span>
                            </td>
                            <td>{{ number_format($agent['avg_days_to_close'], 1) }}</td>
                            <td>
                                <span class="badge bg-{{ $ratingClass }}">
                                    {{ $rating }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#agents-table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
            
            // Create the top performers chart
            const ctx = document.getElementById('topPerformersChart').getContext('2d');
            
            // Get top 5 performers
            const agentData = {!! json_encode($data['agents']) !!};
            const topAgents = agentData.slice(0, 5);
            
            const labels = [];
            const collectionRates = [];
            const backgroundColors = [
                'rgba(40, 167, 69, 0.8)',
                'rgba(23, 162, 184, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(220, 53, 69, 0.8)',
                'rgba(108, 117, 125, 0.8)'
            ];
            
            topAgents.forEach((agent, index) => {
                labels.push(agent.name);
                collectionRates.push(agent.collection_rate);
            });
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Collection Rate (%)',
                        data: collectionRates,
                        backgroundColor: backgroundColors,
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(23, 162, 184, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(220, 53, 69, 1)',
                            'rgba(108, 117, 125, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Collection Rate (%)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Top 5 Performers by Collection Rate'
                        }
                    }
                }
            });
        });
    </script>
@stop
