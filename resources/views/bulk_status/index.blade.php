@extends('adminlte::page')

@section('title', 'Bulk Status Update')

@section('content_header')
    <h1>Bulk Status Update</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('errors'))
        <div class="alert alert-danger">
            <ul>
                @foreach (session('errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Bulk Status Update Tool</h3>
        </div>
        <div class="card-body">
            <p>Upload the CSV file (<b>CSV File must have Ticket No and Status columns</b>)</p>

            <div class="alert" style="background-color:#f8f8f8 !important">
                <h5><i class="icon fas fa-info"></i> CSV Format Requirements:</h5>
                <ul>
                    <li><strong>Ticket No:</strong> The lead ID number (required)</li>
                    <li><strong>Status:</strong> Lead status name (required)</li>
                </ul>

                <h6>Valid Status Values:</h6>
                <div class="row">
                    @foreach ($leadStatuses as $status)
                        <div class="col-md-4 mb-2">
                            <span class="badge badge-{{ $status->color_code }} badge-lg">
                                {{ $status->lead_status_name }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <p class="mt-3"><strong>Example CSV format:</strong></p>
                <code>
                    Ticket No,Status<br>
                    1,Pending<br>
                    2,Paid<br>
                    3,Partially Paid<br>
                    4,Overdue<br>
                    5,Legal Escalation<br>
                    6,Disputed
                </code>
            </div>

            <form action="{{ route('bulk-status-process') }}" method="POST" enctype="multipart/form-data"
                class="status_form">
                @csrf

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="csv_file">Select CSV File*</label>
                            <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required>
                            <small class="form-text text-muted">Only CSV and TXT files are allowed.</small>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">
                    <i class="fas fa-upload"></i> Upload & Update Statuses
                </button>
            </form>
        </div>
    </div>

    {{-- <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Available Lead Statuses</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Status Name</th>
                            <th>Color</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leadStatuses as $status)
                            <tr>
                                <td>{{ $status->id }}</td>
                                <td>
                                    <span class="badge badge-{{ $status->color_code }}">
                                        {{ $status->lead_status_name }}
                                    </span>
                                </td>
                                <td>{{ $status->color_code }}</td>
                                <td>{{ $status->description ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div> --}}
@stop

@section('css')
    <link rel="stylesheet" href="/css/validator/bootstrapValidator.min.css" />
    <style>
        .alert {
            margin-top: 20px;
        }

        .alert-info code {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            display: block;
            margin-top: 10px;
            color: #333;
        }

        .badge-lg {
            font-size: 0.9em;
            padding: 0.5em 0.8em;
        }
    </style>
@stop

@section('js')
    <script src="/js/validator/bootstrapValidator.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.status_form')
                .bootstrapValidator({
                    excluded: [':disabled'],
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                });
        });
    </script>
@stop
