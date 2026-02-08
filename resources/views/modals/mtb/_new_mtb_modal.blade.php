<div class="modal fade" id="new_mtb_modal" tabindex="-1" role="dialog" aria-labelledby="newMtbModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newMtbModalLabel">New MTD Record</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('mtb.store') }}" method="POST" class="mtb_form">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="lead_id" value="{{ $leadDetails->id }}">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount_paid">Amount Paid <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="amount_paid" id="amount_paid"
                                    step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_paid">Date Paid <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="date_paid" id="date_paid" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_channel">Payment Channel <span class="text-danger">*</span></label>
                                <select class="form-control" name="payment_channel" id="payment_channel" required>
                                    <option value="">Select Payment Channel</option>
                                    <option value="Mpesa">Mpesa</option>
                                    <option value="CASH">CASH</option>
                                    <option value="CHEQUE">CHEQUE</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="errorDisp alert alert-danger" style="display: none;"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save MTD Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
