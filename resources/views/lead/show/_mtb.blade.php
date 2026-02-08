<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between pb-3">
            <div>
                <h2 class="card-title pb-2">MTD Records</h2>
            </div>
            <div>
                <a href="#" class="btn btn-info btn-sm mr-2" data-bs-toggle="modal" data-bs-target="#new_mtb_modal">
                    <i class="fa fa-fw fa-plus"></i> New MTD
                </a>
            </div>
        </div>
    </div>
</div>

<table id="mtbTable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Amount Paid</th>
            <th>Date Paid</th>
            <th>Payment Channel</th>
            <th>Description</th>
            <th>Created By</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
</table>

@include('modals.mtb._new_mtb_modal')
@include('modals.mtb._edit_mtb_modal')
