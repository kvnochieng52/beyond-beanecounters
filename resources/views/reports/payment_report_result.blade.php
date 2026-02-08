@extends('adminlte::page')

@section('title', 'Payment Report Results')

@section('content_header')
    <h1>Payment Report Results</h1>
@stop

@section('content')
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($data['summary']['total_payments']) }}</h3>
                    <p>Total Payments</p>
                </div>
                <div class="icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>KSH {{ number_format($data['summary']['total_amount'], 2) }}</h3>
                    <p>Total Amount</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>KSH {{ number_format($data['summary']['avg_payment_amount'], 2) }}</h3>
                    <p>Average Payment</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calculator"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $data['filters']['from_date'] }} - {{ $data['filters']['to_date'] }}</h3>
                    <p>Date Range</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box" style="background-color: #17A2B8;">
                <div class="inner">
                    <h3 style="color: white;">{{ number_format($data['summary']['total_mtd_records']) }}</h3>
                    <p style="color: white;">Total MTD Records</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exchange-alt" style="color: white;"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box" style="background-color: #6F42C1;">
                <div class="inner">
                    <h3 style="color: white;">KSH {{ number_format($data['summary']['total_mtd_amount'], 2) }}</h3>
                    <p style="color: white;">Total MTD Amount</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill" style="color: white;"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box" style="background-color: #FFC107;">
                <div class="inner">
                    <h3 style="color: #333;">KSH {{ number_format($data['summary']['avg_mtd_amount'], 2) }}</h3>
                    <p style="color: #333;">Average MTD</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line" style="color: #333;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Information -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Applied Filters</h3>
            <div class="card-tools">
                <a href="{{ route('reports.payment-report') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Form
                </a>
                <form method="POST" action="{{ route('reports.payment-report.generate') }}" style="display: inline;">
                    @csrf
                    @foreach($data['filters'] as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="hidden" name="report_type" value="{{ request('report_type', 'detailed') }}">
                    <button type="submit" name="export" value="excel" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Date Range:</strong><br>
                    {{ $data['filters']['from_date'] }} to {{ $data['filters']['to_date'] }}
                </div>
                <div class="col-md-3">
                    <strong>Institution:</strong><br>
                    {{ $data['filters']['institution_id'] ? 'Filtered' : 'All Institutions' }}
                </div>
                <div class="col-md-3">
                    <strong>Agent:</strong><br>
                    {{ $data['filters']['agent_id'] ? 'Filtered' : 'All Agents' }}
                </div>
                <div class="col-md-3">
                    <strong>Report Type:</strong><br>
                    {{ ucwords(str_replace('_', ' ', request('report_type', 'detailed'))) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Report Type Specific Content -->
    @if(request('report_type') == 'by_agent')
        <!-- Report by Agent -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Payment Report by Agent</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Agent Name</th>
                                <th>Agent Code</th>
                                <th>Total Payments</th>
                                <th>Total Amount (KSH)</th>
                                <th>Average Amount (KSH)</th>
                                <th>Percentage of Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['summary']['agent_summary'] as $agentName => $summary)
                                @if($agentName)
                                    <tr>
                                        <td>{{ $agentName ?? 'Unassigned' }}</td>
                                        <td>
                                            @php
                                                $agent = $data['payments']->where('agent_name', $agentName)->first();
                                            @endphp
                                            {{ $agent->agent_code ?? '-' }}
                                        </td>
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

    @elseif(request('report_type') == 'by_institution')
        <!-- Report by Institution -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Payment Report by Institution</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Institution Name</th>
                                <th>Total Payments</th>
                                <th>Total Amount (KSH)</th>
                                <th>Average Amount (KSH)</th>
                                <th>Percentage of Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['summary']['institution_summary'] as $institutionName => $summary)
                                @if($institutionName)
                                    <tr>
                                        <td>{{ $institutionName ?? 'Unknown Institution' }}</td>
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

    @elseif(request('report_type') == 'by_date')
        <!-- Report by Date -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Payment Report by Date</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Total Payments</th>
                                <th>Total Amount (KSH)</th>
                                <th>Percentage of Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['summary']['daily_summary'] as $date => $summary)
                                <tr>
                                    <td>{{ Carbon\Carbon::parse($date)->format('d M Y') }}</td>
                                    <td>{{ number_format($summary['count']) }}</td>
                                    <td>{{ number_format($summary['total_amount'], 2) }}</td>
                                    <td>{{ number_format(($summary['total_amount'] / $data['summary']['total_amount']) * 100, 2) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @else
        <!-- Detailed Report -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detailed Payment Report</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm" id="paymentsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Ticket No</th>
                                <th>Lead Name</th>
                                <th>Institution</th>
                                <th>Agent</th>
                                <th>Payment Amount (KSH)</th>
                                <th>Transaction ID</th>
                                <th>Status</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['payments'] as $index => $payment)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ Carbon\Carbon::parse($payment->created_at)->format('d M Y') }}</td>
                                    <td><strong>#{{ $payment->ticket_number }}</strong></td>
                                    <td>{{ $payment->lead_name ?? 'N/A' }}</td>
                                    <td>{{ $payment->institution_name ?? 'N/A' }}</td>
                                    <td>
                                        {{ $payment->agent_name ?? 'Unassigned' }}
                                        @if($payment->agent_code)
                                            <br><small class="text-muted">{{ $payment->agent_code }}</small>
                                        @endif
                                    </td>
                                    <td><strong>{{ number_format($payment->amount, 2) }}</strong></td>
                                    <td>{{ $payment->transaction_id ?? '-' }}</td>
                                    <td>
                                        @if($payment->payment_status_name)
                                            <span class="badge badge-info">{{ $payment->payment_status_name }}</span>
                                        @else
                                            <span class="badge badge-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->description ? Str::limit($payment->description, 30) : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- MTD (Money Transfer Data) Records Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">MTD Records</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm" id="mtdTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date Paid</th>
                                <th>Ticket No</th>
                                <th>Lead Name</th>
                                <th>Institution</th>
                                <th>Agent</th>
                                <th>Amount Paid (KSH)</th>
                                <th>Payment Channel</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($data['mtd_records']->count() > 0)
                                @foreach($data['mtd_records'] as $index => $mtd)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ Carbon\Carbon::parse($mtd->date_paid)->format('d M Y') }}</td>
                                        <td><strong>#{{ $mtd->ticket_number }}</strong></td>
                                        <td>{{ $mtd->lead_name ?? 'N/A' }}</td>
                                        <td>{{ $mtd->institution_name ?? 'N/A' }}</td>
                                        <td>
                                            {{ $mtd->agent_name ?? 'Unassigned' }}
                                            @if($mtd->agent_code)
                                                <br><small class="text-muted">{{ $mtd->agent_code }}</small>
                                            @endif
                                        </td>
                                        <td><strong>{{ number_format($mtd->amount_paid, 2) }}</strong></td>
                                        <td>{{ $mtd->payment_channel ?? '-' }}</td>
                                        <td>{{ $mtd->description ? Str::limit($mtd->description, 30) : '-' }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9" class="text-center text-muted">No MTD records found for the selected criteria</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <!-- Detailed Report (by Agent/Institution) -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detailed Payment Report</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm" id="paymentsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Ticket No</th>
                                <th>Lead Name</th>
                                <th>Institution</th>
                                <th>Agent</th>
                                <th>Payment Amount (KSH)</th>
                                <th>Transaction ID</th>
                                <th>Status</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['payments'] as $index => $payment)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ Carbon\Carbon::parse($payment->created_at)->format('d M Y') }}</td>
                                    <td><strong>#{{ $payment->ticket_number }}</strong></td>
                                    <td>{{ $payment->lead_name ?? 'N/A' }}</td>
                                    <td>{{ $payment->institution_name ?? 'N/A' }}</td>
                                    <td>
                                        {{ $payment->agent_name ?? 'Unassigned' }}
                                        @if($payment->agent_code)
                                            <br><small class="text-muted">{{ $payment->agent_code }}</small>
                                        @endif
                                    </td>
                                    <td><strong>{{ number_format($payment->amount, 2) }}</strong></td>
                                    <td>{{ $payment->transaction_id ?? '-' }}</td>
                                    <td>
                                        @if($payment->payment_status_name)
                                            <span class="badge badge-info">{{ $payment->payment_status_name }}</span>
                                        @else
                                            <span class="badge badge-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->description ? Str::limit($payment->description, 30) : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- MTD (Money Transfer Data) Records Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">MTD Records</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm" id="mtdTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date Paid</th>
                                <th>Ticket No</th>
                                <th>Lead Name</th>
                                <th>Institution</th>
                                <th>Agent</th>
                                <th>Amount Paid (KSH)</th>
                                <th>Payment Channel</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($data['mtd_records']->count() > 0)
                                @foreach($data['mtd_records'] as $index => $mtd)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ Carbon\Carbon::parse($mtd->date_paid)->format('d M Y') }}</td>
                                        <td><strong>#{{ $mtd->ticket_number }}</strong></td>
                                        <td>{{ $mtd->lead_name ?? 'N/A' }}</td>
                                        <td>{{ $mtd->institution_name ?? 'N/A' }}</td>
                                        <td>
                                            {{ $mtd->agent_name ?? 'Unassigned' }}
                                            @if($mtd->agent_code)
                                                <br><small class="text-muted">{{ $mtd->agent_code }}</small>
                                            @endif
                                        </td>
                                        <td><strong>{{ number_format($mtd->amount_paid, 2) }}</strong></td>
                                        <td>{{ $mtd->payment_channel ?? '-' }}</td>
                                        <td>{{ $mtd->description ? Str::limit($mtd->description, 30) : '-' }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9" class="text-center text-muted">No MTD records found for the selected criteria</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <!-- Charts Section -->
    @if($data['payments']->count() > 0)
        <div class="row">
            <!-- Payment Trend Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Payment Trend</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="paymentTrendChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Institutions Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top Institutions by Payment Volume</h3>
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
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable for detailed view
            @if(request('report_type') == 'detailed' || !request('report_type'))
                $('#paymentsTable').DataTable({
                    responsive: true,
                    pageLength: 25,
                    order: [[1, 'desc']], // Sort by date
                    columnDefs: [
                        { orderable: false, targets: [0] }
                    ]
                });
            @endif

            // Charts
            @if($data['payments']->count() > 0)
                // Payment Trend Chart
                const trendCtx = document.getElementById('paymentTrendChart').getContext('2d');
                const dailyData = @json($data['summary']['daily_summary']);

                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: Object.keys(dailyData).map(date => {
                            const d = new Date(date);
                            return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
                        }),
                        datasets: [{
                            label: 'Payment Amount (KSH)',
                            data: Object.values(dailyData).map(item => item.total_amount),
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
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'KSH ' + value.toLocaleString();
                                    }
                                }
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