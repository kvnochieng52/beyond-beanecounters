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
                                <h5 class="info-box-number text-danger"><a href="subscribers/npd_subs"
                                        class="text-danger">0</a>
                                </h5>


                            </div>
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="info-box mb-3 bg-default">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Agents</span>
                                <h5 class="info-box-number text-danger"><a href="subscribers/npd_subs"
                                        class="text-danger">0</a>
                                </h5>


                            </div>
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="info-box mb-3 bg-default">
                            <span class="info-box-icon"><i class="fas fa-building"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text"> Institutions</span>
                                <h5 class="info-box-number text-danger"><a href="subscribers/npd_subs"
                                        class="text-danger">0</a>
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

                            <h5 class="text-info"><a href="subscribers/status/0">18491</a></h5>
                            <span class="description-text">Pending </span>
                        </div>
                    </div> <!-- /.col -->
                    <div class="col-sm-2 col-6">
                        <div class="description-block border-right">
                            <h5 class="text-info"><a href="subscribers/status/1">0</a>
                            </h5>
                            <span class="description-text">PAID </span>
                        </div> <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-2 col-6">
                        <div class="description-block border-right">
                            <h5 class="text-info"><a href="subscribers/status/2">1</a>
                            </h5>
                            <span class="description-text">Partially Paid </span>
                        </div> <!-- /.description-block -->
                    </div>
                    <div class="col-sm-2 col-6">
                        <div class="description-block border-right">
                            <h5 class="text-info"><a href="subscribers/status/2">1</a>
                            </h5>
                            <span class="description-text">Overdue</span>
                        </div> <!-- /.description-block -->
                    </div>
                    <div class="col-sm-2 col-6">
                        <div class="description-block border-right">
                            <h5 class="text-info"><a href="subscribers/status/2">1</a>
                            </h5>
                            <span class="description-text">Legal Escalation </span>
                        </div> <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-2 col-6">
                        <div class="description-block">
                            <h5 class="text-info"><a href="subscribers/status/4">37</a>
                            </h5>
                            <span class="description-text">Disputed</span>
                        </div> <!-- /.description-block -->
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
                            Pending SMS <span class="float-right badge bg-primary">31</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-dark">
                            Delivered SMS <span class="float-right badge bg-info">5</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-dark text-dark">
                            Undelivered SMS <span class="float-right badge bg-success">12</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-dark">
                            SMS IN Queue <span class="float-right badge bg-danger">842</span>
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
                            <th>Product</th>
                            <th>Price</th>
                            <th>Sales</th>
                            <th>More</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <img src="dist/img/default-150x150.png" alt="Product 1"
                                    class="img-circle img-size-32 mr-2">
                                Some Product
                            </td>
                            <td>$13 USD</td>
                            <td>
                                <small class="text-success mr-1">
                                    <i class="fas fa-arrow-up"></i>
                                    12%
                                </small>
                                12,000 Sold
                            </td>
                            <td>
                                <a href="#" class="text-muted">
                                    <i class="fas fa-search"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="dist/img/default-150x150.png" alt="Product 1"
                                    class="img-circle img-size-32 mr-2">
                                Another Product
                            </td>
                            <td>$29 USD</td>
                            <td>
                                <small class="text-warning mr-1">
                                    <i class="fas fa-arrow-down"></i>
                                    0.5%
                                </small>
                                123,234 Sold
                            </td>
                            <td>
                                <a href="#" class="text-muted">
                                    <i class="fas fa-search"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="dist/img/default-150x150.png" alt="Product 1"
                                    class="img-circle img-size-32 mr-2">
                                Amazing Product
                            </td>
                            <td>$1,230 USD</td>
                            <td>
                                <small class="text-danger mr-1">
                                    <i class="fas fa-arrow-down"></i>
                                    3%
                                </small>
                                198 Sold
                            </td>
                            <td>
                                <a href="#" class="text-muted">
                                    <i class="fas fa-search"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="dist/img/default-150x150.png" alt="Product 1"
                                    class="img-circle img-size-32 mr-2">
                                Perfect Item
                                <span class="badge bg-danger">NEW</span>
                            </td>
                            <td>$199 USD</td>
                            <td>
                                <small class="text-success mr-1">
                                    <i class="fas fa-arrow-up"></i>
                                    63%
                                </small>
                                87 Sold
                            </td>
                            <td>
                                <a href="#" class="text-muted">
                                    <i class="fas fa-search"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="dist/img/default-150x150.png" alt="Product 1"
                                    class="img-circle img-size-32 mr-2">
                                Perfect Item
                                <span class="badge bg-danger">NEW</span>
                            </td>
                            <td>$199 USD</td>
                            <td>
                                <small class="text-success mr-1">
                                    <i class="fas fa-arrow-up"></i>
                                    63%
                                </small>
                                87 Sold
                            </td>
                            <td>
                                <a href="#" class="text-muted">
                                    <i class="fas fa-search"></i>
                                </a>
                            </td>
                        </tr>

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
                            <th>Product</th>
                            <th>Price</th>
                            <th>Sales</th>
                            <th>More</th>
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

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Paid', 'Partially Paid', 'Overdue', 'Legal Escalation', 'Disputed'],
                datasets: [{
                    data: [18491, 0, 1, 1, 1, 37], 
                    backgroundColor: ['#17a2b8', '#28a745', '#ffc107', '#dc3545', '#6610f2', '#17a2b8'],
                    hoverOffset: 2 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom', // Moves legend below the chart
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@stop