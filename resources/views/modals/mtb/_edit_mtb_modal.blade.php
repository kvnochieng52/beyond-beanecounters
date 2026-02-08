<div class="modal fade" id="edit_mtb_modal" tabindex="-1" role="dialog" aria-labelledby="editMtbModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMtbModalLabel">Edit MTD Record</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('mtb.update') }}" method="POST" class="edit_mtb_form">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="mtb_id" id="edit_mtb_id">
                    <input type="hidden" name="lead_id" value="{{ $leadDetails->id }}">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_amount_paid">Amount Paid <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="amount_paid" id="edit_amount_paid"
                                    step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_date_paid">Date Paid <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="date_paid" id="edit_date_paid"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_payment_channel">Payment Channel <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" name="payment_channel" id="edit_payment_channel" required>
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
                                <label for="edit_description">Description</label>
                                <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="errorDisp alert alert-danger" style="display: none;"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update MTD Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
