<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center pb-3">
            <h2 class="card-title pb-2">Promised to Pay</h2>

            <a href="#" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#new_ptp_modal">
                <i class="fa fa-fw fa-plus"></i> Add New PTP
            </a>
        </div>
    </div>
</div>



<div class="table-responsive">
    <table id="ptpsTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>PTP Date</th>
                <th>PTP Amount</th>
                <th>Expiry Date</th>
                <th>Created By</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- DataTable will populate this -->
        </tbody>
    </table>
</div>

@include('modals.ptp._new_ptp_modal')