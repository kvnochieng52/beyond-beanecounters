@extends('adminlte::page')

@section('title', 'Background Reports')

@section('content_header')
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Background Reports</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" onclick="refreshTable()">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>
    </div>

    <div class="card-body">
        @include('notices')

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>About Background Reports:</strong> Large reports are processed in the background to prevent timeouts.
            Once processing is complete, you can download the reports from this page.
        </div>

        <div class="table-responsive">
            <table id="backgroundReportsTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Report Name</th>
                        <th>Requested By</th>
                        <th>Status</th>
                        <th>Requested Date</th>
                        <th>Duration</th>
                        <th>File Size</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this background report?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .badge {
        font-size: 0.875em;
    }

    .table td {
        vertical-align: middle;
    }

    .btn-group .btn {
        margin-right: 5px;
    }
</style>
@stop

@section('js')
<script>
    let table;

    $(document).ready(function() {
        table = $('#backgroundReportsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('background-reports.data') }}",
            order: [[3, 'desc']], // Order by requested date descending
            columns: [
                { data: 'report_name', name: 'report_name' },
                { data: 'user_name', name: 'user.name' },
                { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                { data: 'created_at_formatted', name: 'created_at' },
                { data: 'duration', name: 'duration', orderable: false, searchable: false },
                { data: 'file_size', name: 'file_size', orderable: false, searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            pageLength: 25,
            responsive: true,
        });

        // Auto-refresh every 30 seconds
        setInterval(function() {
            table.ajax.reload(null, false);
        }, 30000);
    });

    function refreshTable() {
        table.ajax.reload();
    }

    function deleteReport(id) {
        $('#deleteForm').attr('action', `/background-reports/${id}`);
        $('#deleteModal').modal('show');
    }

    // Show success/error messages
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif

    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif
</script>
@stop