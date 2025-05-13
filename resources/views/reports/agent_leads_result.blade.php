@extends('adminlte::page')

@section('title', 'Agent Leads Report Results')

@section('content_header')
    <h1>Agent Leads Report Results</h1>
    <div class="float-right">
        <a href="{{ route('reports.agent-leads') }}" class="btn btn-default">
            <i class="fas fa-arrow-left"></i> Back to Report Form
        </a>
        <a href="{{ route('reports.agent-leads.generate', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Export to Excel
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Agent Information</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Agent Name</th>
                            <td>{{ $data['agent']->name }}</td>
                        </tr>
                        <tr>
                            <th>Date Range</th>
                            <td>{{ $data['start_date'] }} to {{ $data['end_date'] }}</td>
                        </tr>
                        <tr>
                            <th>Total Leads</th>
                            <td>{{ $data['total_leads'] }}</td>
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
                            <th>Collection Rate</th>
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
                    <h3 class="card-title">Lead Status Summary</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Closed Leads</span>
                                    <span class="info-box-number">{{ $data['closed_leads'] }}</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: {{ $data['total_leads'] > 0 ? ($data['closed_leads'] / $data['total_leads']) * 100 : 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        {{ $data['total_leads'] > 0 ? number_format(($data['closed_leads'] / $data['total_leads']) * 100, 1) : 0 }}% of total leads
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Overdue Leads</span>
                                    <span class="info-box-number">{{ $data['overdue_leads'] }}</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" style="width: {{ $data['total_leads'] > 0 ? ($data['overdue_leads'] / $data['total_leads']) * 100 : 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        {{ $data['total_leads'] > 0 ? number_format(($data['overdue_leads'] / $data['total_leads']) * 100, 1) : 0 }}% of total leads
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <canvas id="leadStatusChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detailed Lead Information</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="leads-table">
                    <thead>
                        <tr>
                            <th>Lead ID</th>
                            <th>Title</th>
                            <th>Institution</th>
                            <th>Amount</th>
                            <th>Balance</th>
                            <th>Collected</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Created Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['leads'] as $lead)
                        <tr class="{{ $lead['is_overdue'] ? 'table-danger' : ($lead['is_closed'] ? 'table-success' : '') }}">
                            <td>{{ $lead['id'] }}</td>
                            <td>{{ $lead['title'] }}</td>
                            <td>{{ $lead['institution'] }}</td>
                            <td>{{ number_format($lead['amount'], 2) }}</td>
                            <td>{{ number_format($lead['balance'], 2) }}</td>
                            <td>{{ number_format($lead['collected'], 2) }}</td>
                            <td>{{ $lead['due_date'] }}</td>
                            <td>
                                <span class="badge bg-{{ $lead['is_closed'] ? 'success' : ($lead['is_overdue'] ? 'danger' : 'info') }}">
                                    {{ $lead['status'] }}
                                </span>
                            </td>
                            <td>{{ $lead['created_at'] }}</td>
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
            $('#leads-table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
            
            // Create the lead status chart
            const ctx = document.getElementById('leadStatusChart').getContext('2d');
            
            const closedLeads = {{ $data['closed_leads'] }};
            const overdueLeads = {{ $data['overdue_leads'] }};
            const activeLeads = {{ $data['total_leads'] - $data['closed_leads'] - $data['overdue_leads'] }};
            
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Closed', 'Overdue', 'Active'],
                    datasets: [{
                        data: [closedLeads, overdueLeads, activeLeads],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(220, 53, 69, 0.8)',
                            'rgba(23, 162, 184, 0.8)'
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(220, 53, 69, 1)',
                            'rgba(23, 162, 184, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                                    return label + ': ' + value + ' (' + percentage + ')';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@stop
