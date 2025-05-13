@extends('adminlte::page')

@section('title', 'Dashboard')

{{-- @section('content_header')
<h1>Dashboard</h1>
@stop --}}

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box mb-3 bg-default">
                            <span class="info-box-icon"><i class="fas fa-user-plus"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text"> Total Leads</span>
                                <h5 class="info-box-number text-success"><a href="/lead"
                                        class="text-success">{{$totalLeads}}</a>
                                </h5>


                            </div>
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="info-box mb-3 bg-default">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Agents</span>
                                <h5 class="info-box-number text-success"><a href="#"
                                        class="text-success">{{$totalAgents}}</a>
                                </h5>


                            </div>
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="info-box mb-3 bg-default">
                            <span class="info-box-icon"><i class="fas fa-building"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text"> Institutions</span>
                                <h5 class="info-box-number text-success"><a href="/institutions"
                                        class="text-success">{{$institutions}}</a>
                                </h5>


                            </div>
                        </div>
                    </div>



                </div>
            </div>

            <div class="card-footer">


                <div class="row">
                    <div class="col-sm-2 col-6">
                        <div class="description-block border-right">
                            <h5 class="text-info"><a href="{{ url('leads/status/1') }}">{{ $leadStats['pending']
                                    }}</a></h5>
                            <span class="description-text">Pending</span>
                        </div>
                    </div>

                    <div class="col-sm-2 col-6">
                        <div class="description-block border-right">
                            <h5 class="text-info"><a href="{{ url('leads/status/2') }}">{{ $leadStats['paid']
                                    }}</a></h5>
                            <span class="description-text">PAID</span>
                        </div>
                    </div>

                    <div class="col-sm-2 col-6">
                        <div class="description-block border-right">
                            <h5 class="text-info"><a href="{{ url('leads/status/3') }}">{{
                                    $leadStats['partially_paid'] }}</a>
                            </h5>
                            <span class="description-text">Partially Paid</span>
                        </div>
                    </div>

                    <div class="col-sm-2 col-6">
                        <div class="description-block border-right">
                            <h5 class="text-info"><a href="{{ url('leads/status/4') }}">{{ $leadStats['overdue']
                                    }}</a></h5>
                            <span class="description-text">Overdue</span>
                        </div>
                    </div>

                    <div class="col-sm-2 col-6">
                        <div class="description-block border-right">
                            <h5 class="text-info"><a href="{{ url('leads/status/5') }}">{{
                                    $leadStats['legal_escalation'] }}</a>
                            </h5>
                            <span class="description-text">Legal Escalation</span>
                        </div>
                    </div>

                    <div class="col-sm-2 col-6">
                        <div class="description-block">
                            <h5 class="text-info"><a href="{{ url('leads/status/6') }}">{{ $leadStats['disputed']
                                    }}</a></h5>
                            <span class="description-text">Disputed</span>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lead Stats</h3>
            </div>
            <div class="card-body">
                <canvas id="leadsStatusDonutChart" style="max-width: 500px; max-height: 400px;"></canvas>
            </div>
        </div>

        <div class="card card-widget widget-user-2 shadow-sm">
            <!-- Add the bg color to the header using any of the bg-* classes -->


            <div class="card-header">
                <h3 class="card-title">SMS Stats</h3>
            </div>
            <div class="card-body p-0">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link text-dark">
                            Pending SMS <span class="float-right badge bg-info">{{$smsStats['pending']}}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-dark">
                            Delivered SMS <span class="float-right badge bg-success">{{$smsStats['delivered']}}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-dark text-dark">
                            Undelivered SMS <span
                                class="float-right badge bg-danger">{{$smsStats['undelivered']}}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-dark">
                            SMS IN Queue <span class="float-right badge bg-warning">{{$smsStats['inQueue']}}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Leads</h3>
            </div>
            <div class="card-body table-responsive p-0">





                <table class="table table-striped table-valign-middle">
                    <thead>
                        <tr>
                            <th>T/No</th>
                            <th>Names</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Stage</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($recentLeads as $lead)


                        <tr>
                            <td>
                                <a href="/lead/{{$lead->id}}"><strong>#{{$lead->id}}</strong></a>
                            </td>
                            <td>{{$lead->title}}</td>
                            <td>{{$lead->defaulter_type_name}}</td>
                            <td><strong>{{ $lead->currency_name }} {{ number_format($lead->amount, 0, '.', ',')
                                    }}</strong></td>
                            <td>
                                <a href="/lead/{{$lead->id}}">
                                    <span class="badge text-white bg-{{$lead->lead_status_color_code}}">
                                        {{$lead->lead_status_name}}
                                    </span>
                                </a>
                            </td>

                            <td>{{$lead->lead_stage_name}}</td>
                            <td>
                                <a href="/lead/{{$lead->id}}" class="text-muted">
                                    <i class="fas fa-search"></i>
                                </a>
                            </td>
                        </tr>

                        @endforeach
                    </tbody>
                </table>






            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Overdue Activities</h3>
            </div>
            <div class="card-body table-responsive p-0">

                <table class="table table-striped table-valign-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Defaulter Name</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>A. Status</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>


    </div>

</div>

@stop

@section('css')
{{-- Add here extra stylesheets --}}
{{--
<link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var ctx = document.getElementById('leadsStatusDonutChart').getContext('2d');

        var data = {
            labels: ["Pending", "Paid", "Partially Paid", "Overdue", "Legal Escalation", "Disputed"],
            datasets: [{
                data: [
                    {{ $leadStats['pending'] }},
                    {{ $leadStats['paid'] }},
                    {{ $leadStats['partially_paid'] }},
                    {{ $leadStats['overdue'] }},
                    {{ $leadStats['legal_escalation'] }},
                    {{ $leadStats['disputed'] }}
                ],
                backgroundColor: ["#36A2EB", "#4BC0C0", "#FF6384", "#FF9F40", "#FFCD56", "#9966FF"],
            }]
        };

        new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                plugins: {
                    legend: {
                        position: 'bottom' // Moves the legend below the chart
                    }
                }
            }
        });
    });
</script>
@stop