@extends('adminlte::page')

@section('title', 'PTP Report Results')

@section('content_header')
    <h1>PTP Report Results</h1>
@stop

@section('content')
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($data['summary']['total_ptps']) }}</h3>
                    <p>Total PTPs</p>
                </div>
                <div class="icon">
                    <i class="fas fa-handshake"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>KSH {{ number_format($data['summary']['total_amount'], 2) }}</h3>
                    <p>Total PTP Amount</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($data['summary']['overdue_ptps']) }}</h3>
                    <p>Overdue PTPs</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>KSH {{ number_format($data['summary']['avg_ptp_amount'], 2) }}</h3>
                    <p>Average PTP Amount</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calculator"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Information -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Applied Filters</h3>
            <div class="card-tools">
                <a href="{{ route('reports.ptp-report') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Form
                </a>
                <form method="POST" action="{{ route('reports.ptp-report.generate') }}" style="display: inline;">
                    @csrf
                    @foreach($data['filters'] as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <button type="submit" name="export" value="excel" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    <strong>Date Range:</strong><br>
                    {{ $data['filters']['from_date'] }} to {{ $data['filters']['to_date'] }}
                </div>
                <div class="col-md-2">
                    <strong>Filter Type:</strong><br>
                    {{ ucwords(str_replace('_', ' ', $data['filters']['date_filter_type'])) }} Date
                </div>
                <div class="col-md-2">
                    <strong>Institution:</strong><br>
                    {{ $data['filters']['institution_id'] ? 'Filtered' : 'All' }}
                </div>
                <div class="col-md-2">
                    <strong>Assigned Agent:</strong><br>
                    {{ $data['filters']['agent_id'] ? 'Filtered' : 'All' }}
                </div>
                <div class="col-md-2">
                    <strong>Created By:</strong><br>
                    {{ $data['filters']['created_by_agent'] ? 'Filtered' : 'All' }}
                </div>
                <div class="col-md-2">
                    <strong>PTP Due Range:</strong><br>
                    @if($data['filters']['ptp_due_from'] && $data['filters']['ptp_due_to'])
                        {{ $data['filters']['ptp_due_from'] }} - {{ $data['filters']['ptp_due_to'] }}
                    @else
                        All Dates
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed PTP Report -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detailed PTP Report</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm" id="ptpTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Institution</th>
                            <th>Ticket No.</th>
                            <th>Lead Title</th>
                            <th>PTP Created By</th>
                            <th>PTP Created Date</th>
                            <th>PTP Due Date</th>
                            <th>PTP Amount (KSH)</th>
                            <th>Assigned Agent</th>
                            <th>Lead Amount (KSH)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['ptps'] as $index => $ptp)
                            @php
                                $isOverdue = \Carbon\Carbon::parse($ptp->act_ptp_date)->lt(\Carbon\Carbon::today());
                                $isDueToday = \Carbon\Carbon::parse($ptp->act_ptp_date)->isToday();
                                $isDueTomorrow = \Carbon\Carbon::parse($ptp->act_ptp_date)->isTomorrow();
                            @endphp
                            <tr class="{{ $isOverdue ? 'table-danger' : ($isDueToday ? 'table-warning' : ($isDueTomorrow ? 'table-info' : '')) }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $ptp->institution_name ?? 'N/A' }}</td>
                                <td><strong>#{{ $ptp->ticket_number }}</strong></td>
                                <td>{{ $ptp->lead_title ?? 'N/A' }}</td>
                                <td>
                                    {{ $ptp->created_by_name ?? 'Unknown' }}
                                    @if($ptp->created_by_code)
                                        <br><small class="text-muted">{{ $ptp->created_by_code }}</small>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($ptp->ptp_created_date)->format('d M Y H:i') }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($ptp->act_ptp_date)->format('d M Y') }}
                                    @if($isOverdue)
                                        <br><small class="badge badge-danger">Overdue</small>
                                    @elseif($isDueToday)
                                        <br><small class="badge badge-warning">Due Today</small>
                                    @elseif($isDueTomorrow)
                                        <br><small class="badge badge-info">Due Tomorrow</small>
                                    @endif
                                </td>
                                <td><strong>{{ number_format($ptp->act_ptp_amount, 2) }}</strong></td>
                                <td>
                                    {{ $ptp->assigned_agent_name ?? 'Unassigned' }}
                                    @if($ptp->assigned_agent_code)
                                        <br><small class="text-muted">{{ $ptp->assigned_agent_code }}</small>
                                    @endif
                                </td>
                                <td>{{ number_format($ptp->lead_amount ?? 0, 2) }}</td>
                                <td>
                                    @if($isOverdue)
                                        <span class="badge badge-danger">Overdue</span>
                                    @elseif($isDueToday)
                                        <span class="badge badge-warning">Due Today</span>
                                    @elseif($isDueTomorrow)
                                        <span class="badge badge-info">Due Tomorrow</span>
                                    @else
                                        <span class="badge badge-success">Active</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Analytics -->
    <div class="row">
        <!-- Institution Summary -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">PTPs by Institution</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Institution</th>
                                    <th>Count</th>
                                    <th>Total Amount</th>
                                    <th>Avg Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['summary']['institution_summary'] as $institutionName => $summary)
                                    @if($institutionName)
                                        <tr>
                                            <td>{{ $institutionName }}</td>
                                            <td>{{ number_format($summary['count']) }}</td>
                                            <td>KSH {{ number_format($summary['total_amount'], 2) }}</td>
                                            <td>KSH {{ number_format($summary['avg_amount'], 2) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agent Summary -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">PTPs by Assigned Agent</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Agent</th>
                                    <th>Count</th>
                                    <th>Total Amount</th>
                                    <th>Avg Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['summary']['agent_summary'] as $agentName => $summary)
                                    @if($agentName)
                                        <tr>
                                            <td>{{ $agentName }}</td>
                                            <td>{{ number_format($summary['count']) }}</td>
                                            <td>KSH {{ number_format($summary['total_amount'], 2) }}</td>
                                            <td>KSH {{ number_format($summary['avg_amount'], 2) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Created By Summary -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">PTPs by Creator</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Created By</th>
                                    <th>Total PTPs</th>
                                    <th>Total Amount (KSH)</th>
                                    <th>Average Amount (KSH)</th>
                                    <th>Percentage of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['summary']['created_by_summary'] as $creatorName => $summary)
                                    @if($creatorName)
                                        <tr>
                                            <td>{{ $creatorName }}</td>
                                            <td>{{ number_format($summary['count']) }}</td>
                                            <td>{{ number_format($summary['total_amount'], 2) }}</td>
                                            <td>{{ number_format($summary['avg_amount'], 2) }}</td>
                                            <td>{{ number_format(($summary['total_amount'] / $data['summary']['total_amount']) * 100, 2) }}%</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    @if($data['ptps']->count() > 0)
        <div class="row">
            <!-- PTP Due Date Trend -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">PTP Due Date Distribution</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="dueDateChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Institutions by PTP Volume -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top Institutions by PTP Volume</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="institutionsChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <style>
        .table-danger {
            background-color: rgba(220, 53, 69, 0.1);
        }
        .table-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }
        .table-info {
            background-color: rgba(23, 162, 184, 0.1);
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#ptpTable').DataTable({
                responsive: true,
                pageLength: 25,
                order: [[6, 'asc']], // Sort by PTP Due Date
                columnDefs: [
                    { orderable: false, targets: [0] }
                ]
            });

            // Charts
            @if($data['ptps']->count() > 0)
                // Due Date Distribution Chart
                const dueDateCtx = document.getElementById('dueDateChart').getContext('2d');
                const dueDateData = @json($data['summary']['due_date_summary']);

                new Chart(dueDateCtx, {
                    type: 'line',
                    data: {
                        labels: Object.keys(dueDateData).map(date => {
                            const d = new Date(date);
                            return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
                        }),
                        datasets: [{
                            label: 'PTP Count',
                            data: Object.values(dueDateData).map(item => item.count),
                            borderColor: '#36A2EB',
                            backgroundColor: 'rgba(54, 162, 235, 0.1)',
                            borderWidth: 2,
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

                // Top Institutions Chart
                const institutionsCtx = document.getElementById('institutionsChart').getContext('2d');
                const institutionData = @json($data['summary']['institution_summary']);
                const topInstitutions = Object.entries(institutionData)
                    .filter(([name]) => name && name !== '')
                    .sort(([,a], [,b]) => b.total_amount - a.total_amount)
                    .slice(0, 5);

                new Chart(institutionsCtx, {
                    type: 'doughnut',
                    data: {
                        labels: topInstitutions.map(([name]) => name),
                        datasets: [{
                            data: topInstitutions.map(([,data]) => data.total_amount),
                            backgroundColor: [
                                '#FF6384',
                                '#36A2EB',
                                '#FFCE56',
                                '#4BC0C0',
                                '#9966FF'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': KSH ' + context.parsed.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            @endif
        });
    </script>
@stop