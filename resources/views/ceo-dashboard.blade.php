@extends('adminlte::page')

@section('title', 'CEO Dashboard')

@section('content_header')
    <h1>CEO Dashboard</h1>
@stop

@section('content')
    <!-- Main Metrics Cards -->
    <div class="row">
        <!-- Active Clients -->
        <div class="col-lg-3 col-6">
            <div class="small-box" style="background-color: #5bc0de; color: white;">
                <div class="inner">
                    <h3>{{ number_format($activeClients) }}</h3>
                    <p>ACTIVE CLIENTS (NO)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <!-- Total Leads -->
        <div class="col-lg-3 col-6">
            <div class="small-box" style="background-color: #f0ad4e; color: white;">
                <div class="inner">
                    <h3>{{ number_format($totalLeads) }}</h3>
                    <p>TOTAL NO OF LEADS</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
        </div>

        <!-- Total Value -->
        <div class="col-lg-3 col-6">
            <div class="small-box" style="background-color: #5cb85c; color: white;">
                <div class="inner">
                    <h3>{{ number_format($totalValue / 1000000, 1) }}M</h3>
                    <p>TOTAL VALUE (KSHS)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="col-lg-3 col-6">
            <div class="small-box" style="background-color: #d9534f; color: white;">
                <div class="inner">
                    <h3>{{ number_format($activeUsers) }}</h3>
                    <p>TOTAL NO OF ACTIVE USERS</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row of Metrics -->
    <div class="row">
        <!-- PTPs Today (Number) -->
        <div class="col-lg-3 col-6">
            <div class="small-box" style="background-color: #d9534f; color: white;">
                <div class="inner">
                    <h3>{{ number_format($ptpsToday) }}</h3>
                    <p>PTPS TODAY (NO)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>

        <!-- PTPs Today (Value) -->
        <div class="col-lg-3 col-6">
            <div class="small-box" style="background-color: #5cb85c; color: white;">
                <div class="inner">
                    <h3>{{ number_format($ptpsTodayValue / 1000, 0) }}K</h3>
                    <p>PTPS TODAY (VALUE)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-check"></i>
                </div>
            </div>
        </div>

        <!-- PTPs This Month (Value) -->
        <div class="col-lg-3 col-6">
            <div class="small-box" style="background-color: #5bc0de; color: white;">
                <div class="inner">
                    <h3>{{ number_format($ptpsThisMonthValue / 1000, 0) }}K</h3>
                    <p>PTPS THIS MONTH (VALUE)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>

        <!-- Locked Payments -->
        <div class="col-lg-3 col-6">
            <div class="small-box" style="background-color: #f0ad4e; color: white;">
                <div class="inner">
                    <h3>{{ number_format($lockedPayments / 1000, 0) }}K</h3>
                    <p>LOCKED PAYMENTS(MTD)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-lock"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <!-- Monthly Leads Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Monthly Leads Trend</h3>
                </div>
                <div class="card-body">
                    <canvas id="monthlyLeadsChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Lead Status Distribution -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lead Status Distribution</h3>
                </div>
                <div class="card-body">
                    <canvas id="leadStatusChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- PTP Completion Rate Chart -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">PTP Completion Rate (Last 6 Months)</h3>
                </div>
                <div class="card-body">
                    <canvas id="ptpCompletionChart" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional KPI Tables -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Key Performance Indicators</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Average PTP Completion Rate</span>
                                    <span class="info-box-number">
                                        {{ number_format(collect($ptpCompletionRate)->avg('rate'), 1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-money-bill"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Average Lead Value</span>
                                    <span class="info-box-number">
                                        KSH {{ number_format($totalLeads > 0 ? $totalValue / $totalLeads : 0, 0) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-user-friends"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Leads per Active User</span>
                                    <span class="info-box-number">
                                        {{ number_format($activeUsers > 0 ? $totalLeads / $activeUsers : 0, 1) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box .inner h3 {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0 0 10px 0;
            white-space: nowrap;
            padding: 0;
        }

        .small-box .inner p {
            font-size: 1rem;
            margin: 0;
            font-weight: 600;
        }

        .small-box .icon {
            position: absolute;
            top: auto;
            bottom: 15px;
            right: 15px;
            z-index: 0;
            font-size: 3rem;
            color: rgba(255, 255, 255, 0.3);
        }

        .small-box {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .small-box .inner {
            padding: 20px;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Monthly Leads Chart
            const monthlyCtx = document.getElementById('monthlyLeadsChart').getContext('2d');
            const monthlyLeadsData = @json($monthlyLeads);

            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: monthlyLeadsData.map(item => item.month),
                    datasets: [{
                        label: 'Number of Leads',
                        data: monthlyLeadsData.map(item => item.count),
                        borderColor: '#36A2EB',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Lead Status Distribution Chart
            const statusCtx = document.getElementById('leadStatusChart').getContext('2d');
            const statusData = @json($leadStatusData);

            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusData.map(item => item.status),
                    datasets: [{
                        data: statusData.map(item => item.count),
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF',
                            '#FF9F40'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // PTP Completion Rate Chart
            const ptpCtx = document.getElementById('ptpCompletionChart').getContext('2d');
            const ptpData = @json($ptpCompletionRate);

            new Chart(ptpCtx, {
                type: 'bar',
                data: {
                    labels: ptpData.map(item => item.month),
                    datasets: [{
                        label: 'Completion Rate (%)',
                        data: ptpData.map(item => item.rate),
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@stop