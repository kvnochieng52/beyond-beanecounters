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
            <th>Attachments</th>
            <th>Created By</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
</table>

<!-- Attachments Modal -->
<div class="modal fade" id="mtb_attachments_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">MTD Attachments</h5>
                <button type="button" class="close" data-bs-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="mtbAttachmentsTable" class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>File Name</th>
                            <th>File Type</th>
                            <th>File Size</th>
                            <th>Uploaded By</th>
                            <th>Uploaded At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@include('modals.mtb._new_mtb_modal')
@include('modals.mtb._edit_mtb_modal')
