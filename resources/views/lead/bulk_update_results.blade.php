@extends('adminlte::page')

@section('title', 'Bulk Update Results')

@section('content_header')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Bulk Update Results</h3>
                <div class="card-tools">
                    <a href="{{ route('lead.bulk-update.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Upload
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Successfully Updated</span>
                                <span class="info-box-number">{{ $results['success'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-danger">
                            <span class="info-box-icon"><i class="fas fa-times"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Errors</span>
                                <span class="info-box-number">{{ $results['errors'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-list"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Processed</span>
                                <span class="info-box-number">{{ $results['success'] + $results['errors'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-striped" id="resultsTable">
                        <thead>
                            <tr>
                                <th>Row #</th>
                                <th>Ticket No</th>
                                <th>Status</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results['details'] as $detail)
                            <tr class="{{ $detail['status'] == 'success' ? 'table-success' : 'table-danger' }}">
                                <td>{{ $detail['row'] }}</td>
                                <td>{{ $detail['ticket_no'] ?? 'N/A' }}</td>
                                <td>
                                    @if($detail['status'] == 'success')
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Success
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times"></i> Error
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $detail['message'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($results['success'] > 0)
                    <div class="alert alert-success mt-3">
                        <h5><i class="icon fas fa-check"></i> Success!</h5>
                        {{ $results['success'] }} record(s) were successfully updated.
                    </div>
                @endif

                @if($results['errors'] > 0)
                    <div class="alert alert-warning mt-3">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Notice!</h5>
                        {{ $results['errors'] }} record(s) had errors and were not updated. Please review the error messages above and correct your CSV file.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.table-success {
    background-color: rgba(40, 167, 69, 0.1) !important;
}
.table-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    $('#resultsTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "pageLength": 25,
        "order": [[ 0, "asc" ]],
        "columnDefs": [
            { "width": "10%", "targets": 0 },
            { "width": "15%", "targets": 1 },
            { "width": "15%", "targets": 2 },
            { "width": "60%", "targets": 3 }
        ]
    });
});
</script>
@stop