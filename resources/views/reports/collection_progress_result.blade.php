@extends('adminlte::page')

@section('title', 'Collection Progress Report Results')

@section('content_header')
    <h1>Collection Progress Report Results</h1>
    <div class="float-right">
        <a href="{{ route('reports.collection-progress') }}" class="btn btn-default">
            <i class="fas fa-arrow-left"></i> Back to Report Form
        </a>
        <a href="{{ route('reports.collection-progress.generate', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success">
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
                            <th>Total Leads</th>
                            <td>{{ $data['total_leads'] }}</td>
                        </tr>
                        <tr>
                            <th>Total Debt Amount</th>
                            <td>{{ number_format($data['total_debt'], 2) }}</td>
                        </tr>
                        <tr>
                            <th>Total Collected Amount</th>
                            <td>{{ number_format($data['total_collected'], 2) }}</td>
                        </tr>
                        <tr>
                            <th>Overall Collection Rate</th>
                            <td>
                                <span class="badge bg-{{ $data['collection_rate'] >= 70 ? 'success' : ($data['collection_rate'] >= 40 ? 'warning' : 'danger') }}">
                                    {{ number_format($data['collection_rate'], 2) }}%
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
                    <h3 class="card-title">Monthly Collection Progress</h3>
                </div>
                <div class="card-body">
                    <canvas id="monthlyProgressChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Monthly Collection Progress</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Target Amount</th>
                            <th>Collected Amount</th>
                            <th>Collection Rate</th>
                            <th>Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['monthly_data'] as $month)
                        <tr>
                            <td>{{ $month['month'] }}</td>
                            <td>{{ number_format($month['target'], 2) }}</td>
                            <td>{{ number_format($month['collected'], 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $month['percentage'] >= 70 ? 'success' : ($month['percentage'] >= 40 ? 'warning' : 'danger') }}">
                                    {{ number_format($month['percentage'], 2) }}%
                                </span>
                            </td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar bg-{{ $month['percentage'] >= 70 ? 'success' : ($month['percentage'] >= 40 ? 'warning' : 'danger') }}" 
                                         role="progressbar" 
                                         style="width: {{ min($month['percentage'], 100) }}%" 
                                         aria-valuenow="{{ $month['percentage'] }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ number_format($month['percentage'], 2) }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Weekly Collection Progress</h3>
        </div>
        <div class="card-body">
            <canvas id="weeklyProgressChart" height="250"></canvas>
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped" id="weekly-table">
                    <thead>
                        <tr>
                            <th>Week</th>
                            <th>Target Amount</th>
                            <th>Collected Amount</th>
                            <th>Collection Rate</th>
                            <th>Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['weekly_data'] as $week)
                        <tr>
                            <td>{{ $week['week'] }}</td>
                            <td>{{ number_format($week['target'], 2) }}</td>
                            <td>{{ number_format($week['collected'], 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $week['percentage'] >= 70 ? 'success' : ($week['percentage'] >= 40 ? 'warning' : 'danger') }}">
                                    {{ number_format($week['percentage'], 2) }}%
                                </span>
                            </td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar bg-{{ $week['percentage'] >= 70 ? 'success' : ($week['percentage'] >= 40 ? 'warning' : 'danger') }}" 
                                         role="progressbar" 
                                         style="width: {{ min($week['percentage'], 100) }}%" 
                                         aria-valuenow="{{ $week['percentage'] }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ number_format($week['percentage'], 2) }}%
                                    </div>
                                </div>
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
            // Initialize DataTable for weekly data
            $('#weekly-table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
            
            // Create the monthly progress chart
            const monthlyCtx = document.getElementById('monthlyProgressChart').getContext('2d');
            const monthlyData = {!! json_encode($data['monthly_data']) !!};
            
            const monthlyLabels = monthlyData.map(item => item.month);
            const monthlyTargets = monthlyData.map(item => item.target);
            const monthlyCollected = monthlyData.map(item => item.collected);
            
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: monthlyLabels,
                    datasets: [
                        {
                            label: 'Target Amount',
                            data: monthlyTargets,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Collected Amount',
                            data: monthlyCollected,
                            backgroundColor: 'rgba(75, 192, 192, 0.5)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Amount'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Monthly Collection Progress'
                        }
                    }
                }
            });
            
            // Create the weekly progress chart
            const weeklyCtx = document.getElementById('weeklyProgressChart').getContext('2d');
            const weeklyData = {!! json_encode($data['weekly_data']) !!};
            
            const weeklyLabels = weeklyData.map(item => item.week);
            const weeklyPercentages = weeklyData.map(item => item.percentage);
            
            new Chart(weeklyCtx, {
                type: 'line',
                data: {
                    labels: weeklyLabels,
                    datasets: [{
                        label: 'Collection Rate (%)',
                        data: weeklyPercentages,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: false
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
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Weekly Collection Progress'
                        }
                    }
                }
            });
        });
    </script>
@stop
