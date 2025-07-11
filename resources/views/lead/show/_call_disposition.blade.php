<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center pb-3">
            <h2 class="card-title pb-2">Call Disposition</h2>

            <a href="#" class="btn btn-info btn-sm" data-bs-toggle="modal"
                data-bs-target="#update_call_disposition_modal">
                <i class="fa fa-fw fa-mobile"></i> Update Disposition
            </a>
        </div>
    </div>
</div>


<div class="table-responsive">
    <table id="callDispositionTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Call Disposition</th>
                <th>Date</th>
                <th>Created By</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>


            <!-- DataTable will populate this -->
        </tbody>
    </table>
</div>

@include('modals.call_disposition._update_call_disposition_modal')