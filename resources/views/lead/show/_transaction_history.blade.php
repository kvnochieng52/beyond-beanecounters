<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center pb-3">
            <h2 class="card-title pb-2">Transactions</h2>

            <a href="#" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#new_transaction_modal">
                <i class="fa fa-fw fa-plus"></i> New Transaction
            </a>


        </div>
    </div>
</div>

<table id="transactionsTable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Transaction Type</th>
            <th>By</th> <!-- New Column -->
            <th>Date</th>
        </tr>
    </thead>
</table>


@include('modals.transactions._new_transaction_modal')