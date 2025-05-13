@extends('adminlte::page')

@section('title', 'Collection Rates Report Results')

@section('content_header')
    <h1>Collection Rates Report Results</h1>
    <div class="float-right">
        <a href="{{ route('reports.collection-rates') }}" class="btn btn-default">
            <i class="fas fa-arrow-left"></i> Back to Report Form
        </a>
        <a href="{{ route('reports.collection-rates.generate', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success">
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
                    <h3 class="card-title">Collection Rate Chart</h3>
                </div>
                <div class="card-body">
                    <canvas id="collectionRateChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    @if(count($data['institutions']) > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Collection Rates by Institution</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Institution</th>
                            <th>Total Debt</th>
                            <th>Total Collected</th>
                            <th>Collection Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['institutions'] as $institution)
                        <tr>
                            <td>{{ $institution['name'] }}</td>
                            <td>{{ number_format($institution['total_debt'], 2) }}</td>
                            <td>{{ number_format($institution['total_collected'], 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $institution['collection_rate'] >= 70 ? 'success' : ($institution['collection_rate'] >= 40 ? 'warning' : 'danger') }}">
                                    {{ number_format($institution['collection_rate'], 2) }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Create the collection rate chart
            const ctx = document.getElementById('collectionRateChart').getContext('2d');
            
            const collectionRate = {{ $data['collection_rate'] }};
            const uncollectedRate = 100 - collectionRate;
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Collected', 'Uncollected'],
                    datasets: [{
                        data: [collectionRate, uncollectedRate],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(220, 53, 69, 0.8)'
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
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
                                    return context.label + ': ' + context.raw.toFixed(2) + '%';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@stop
