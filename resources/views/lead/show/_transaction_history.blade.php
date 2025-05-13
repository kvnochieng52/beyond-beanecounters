<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between pb-3">

            <div>
                <h2 class="card-title pb-2">Transactions</h2>
            </div>

            <div>
                <a href="#" class="btn btn-info btn-sm mr-2" data-bs-toggle="modal"
                    data-bs-target="#new_transaction_modal">
                    <i class="fa fa-fw fa-plus"></i> New Transaction
                </a>

                <a href="/transactions/invoice/{{$leadDetails->id}}" class="btn btn-default btn-sm" target="_blank">
                    <i class="fa fa-fw fa-file"></i> Generate Invoice
                </a>
            </div>



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
            <th>By</th>
            <th>Status</th> <!-- New Column -->
            <th>Date</th>
        </tr>
    </thead>
</table>


@include('modals.transactions._new_transaction_modal')