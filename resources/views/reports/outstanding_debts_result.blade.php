@extends('adminlte::page')

@section('title', 'Outstanding Debts Report Results')

@section('content_header')
    <h1>Outstanding Debts Report Results</h1>
    <div class="float-right">
        <a href="{{ route('reports.outstanding-debts') }}" class="btn btn-default">
            <i class="fas fa-arrow-left"></i> Back to Report Form
        </a>
        <a href="{{ route('reports.outstanding-debts.generate', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success">
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
                            <th>As of Date</th>
                            <td>{{ $data['as_of_date'] }}</td>
                        </tr>
                        <tr>
                            <th>Total Leads with Outstanding Balances</th>
                            <td>{{ $data['total_leads'] }}</td>
                        </tr>
                        <tr>
                            <th>Total Outstanding Amount</th>
                            <td>{{ number_format($data['total_outstanding'], 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Aging Analysis Chart</h3>
                </div>
                <div class="card-body">
                    <canvas id="agingChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Aging Analysis</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Days Overdue</th>
                            <th>Number of Leads</th>
                            <th>Outstanding Amount</th>
                            <th>Percentage of Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['overdue_groups'] as $range => $group)
                        @php
                            $percentage = $data['total_outstanding'] > 0 
                                ? ($group['amount'] / $data['total_outstanding']) * 100 
                                : 0;
                        @endphp
                        <tr>
                            <td>{{ $range }} days</td>
                            <td>{{ $group['count'] }}</td>
                            <td>{{ number_format($group['amount'], 2) }}</td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar bg-{{ $range == '91+' ? 'danger' : ($range == '61-90' ? 'warning' : ($range == '31-60' ? 'info' : 'success')) }}" 
                                         role="progressbar" 
                                         style="width: {{ $percentage }}%" 
                                         aria-valuenow="{{ $percentage }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ number_format($percentage, 2) }}%
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
            <h3 class="card-title">Detailed Lead Information</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="leads-table">
                    <thead>
                        <tr>
                            <th>Lead ID</th>
                            <th>Lead Title</th>
                            <th>Institution</th>
                            <th>Original Amount</th>
                            <th>Outstanding Balance</th>
                            <th>Due Date</th>
                            <th>Days Overdue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['leads'] as $lead)
                        <tr class="{{ $lead['days_overdue'] > 90 ? 'table-danger' : ($lead['days_overdue'] > 60 ? 'table-warning' : ($lead['days_overdue'] > 30 ? 'table-info' : '')) }}">
                            <td>{{ $lead['id'] }}</td>
                            <td>{{ $lead['title'] }}</td>
                            <td>{{ $lead['institution'] }}</td>
                            <td>{{ number_format($lead['amount'], 2) }}</td>
                            <td>{{ number_format($lead['balance'], 2) }}</td>
                            <td>{{ $lead['due_date'] }}</td>
                            <td>
                                <span class="badge bg-{{ $lead['days_overdue'] > 90 ? 'danger' : ($lead['days_overdue'] > 60 ? 'warning' : ($lead['days_overdue'] > 30 ? 'info' : 'success')) }}">
                                    {{ $lead['days_overdue'] }}
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
            $('#leads-table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
            
            // Create the aging analysis chart
            const ctx = document.getElementById('agingChart').getContext('2d');
            
            const labels = [];
            const data = [];
            const backgroundColors = [
                'rgba(40, 167, 69, 0.8)',  // 0-30 days (success)
                'rgba(23, 162, 184, 0.8)', // 31-60 days (info)
                'rgba(255, 193, 7, 0.8)',  // 61-90 days (warning)
                'rgba(220, 53, 69, 0.8)'   // 91+ days (danger)
            ];
            
            @foreach($data['overdue_groups'] as $range => $group)
                labels.push('{{ $range }} days');
                data.push({{ $group['amount'] }});
            @endforeach
            
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: backgroundColors,
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(23, 162, 184, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(220, 53, 69, 1)'
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
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(2);
                                    return context.label + ': ' + new Intl.NumberFormat().format(value) + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@stop
