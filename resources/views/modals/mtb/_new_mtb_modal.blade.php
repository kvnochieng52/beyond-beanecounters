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
            <form action="{{ route('mtb.store') }}" method="POST" class="mtb_form" enctype="multipart/form-data">
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

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="agent_id">Agent (Optional - Post on behalf of another)</label>
                                <select class="form-control select2-agent" name="agent_id" id="agent_id"
                                    style="width: 100%;">
                                    <option value="">Select Agent (or leave blank for current user)</option>
                                </select>
                                <small class="text-muted d-block mt-1">Can post on behalf of another agent</small>
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

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="attachments">Attachments (Optional)</label>
                                <input type="file" class="form-control" name="attachments[]" id="attachments"
                                    multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xlsx,.xls">
                                <small class="text-muted d-block mt-2">Allowed: PDF, DOC, DOCX, JPG, PNG, XLSX, XLS (Max
                                    5MB each)</small>
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

<script>
    function initializeAgentSelect() {
        if (typeof $ === 'undefined') {
            setTimeout(initializeAgentSelect, 100);
            return;
        }
        
        $(document).ready(function() {
            // Load agents when page loads
            loadAgents();
            
            function loadAgents() {
                $.ajax({
                    url: '{{ route('mtb.get-agents') }}',
                    dataType: 'json',
                    data: { q: '' },
                    success: function(data) {
                        var $select = $('.select2-agent');
                        $select.empty();
                        $select.append($('<option></option>').val('').text('Select Agent (or leave blank for current user)'));
                        $.each(data.results, function(i, item) {
                            $select.append($('<option></option>').val(item.id).text(item.text));
                        });
                        
                        // Initialize Select2 after populating options
                        $select.select2({
                            placeholder: 'Select Agent (or leave blank for current user)',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#new_mtb_modal')
                        });
                    }
                });
            }
        });
    }
    
    // Initialize when jQuery is available
    if (typeof jQuery !== 'undefined') {
        initializeAgentSelect();
    } else {
        document.addEventListener('DOMContentLoaded', initializeAgentSelect);
    }
</script>
