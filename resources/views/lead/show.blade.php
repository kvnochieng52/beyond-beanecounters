@extends('adminlte::page')

@section('title', 'Lead Details')

@section('content_header')
    {{-- <h1>Create Defaulter</h1> --}}
@stop

@section('content')
    <div class="card">




        {{-- <div class="card-header">
        <h3 class="card-title">Defaulter Details</h3>
        <p style="float: right; margin-bottom:0px">Fields marked * are mandatory </p>
    </div> --}}


        <div class="card-body">

            @include('notices')
            <div class="row">


                <div class="col-md-12">

                    <div class="card card-primary card-outline card-outline-tabs">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                <li class="nav-item">
                                    {{-- <a class="nav-link {{ empty(request()->get('page')) ? 'active' : '' }}"
                                    id="custom-tabs-four-basic-tab" data-toggle="pill" href="#custom-tabs-four-basic"
                                    role="tab" aria-controls="custom-tabs-four-basic" aria-selected="true">BASIC
                                    DETAILS</a> --}}



                                    <a class="nav-link {{ empty(request()->get('section')) ? 'active' : '' }}"
                                        id="custom-tabs-four-basic-tab" href="/lead/{{ $leadDetails->id }}" role="tab"
                                        aria-controls="custom-tabs-four-basic" aria-selected="true">BASIC
                                        DETAILS</a>
                                </li>


                                <li class="nav-item">
                                    <a class="nav-link {{ request()->get('section') == 'activities' ? 'active' : '' }}"
                                        id="custom-tabs-four-activities-tab"
                                        href="/lead/{{ $leadDetails->id }}?section=activities"
                                        aria-controls="custom-tabs-four-activities" aria-selected="false">
                                        ACTIVITIES</a>
                                </li>
                                {{-- <li class="nav-item">
                                <a class="nav-link {{ request()->get('section') == 'payments' ? 'active' : '' }}"
                                    id="custom-tabs-four-payment-history-tab"
                                    href="/lead/{{$leadDetails->id}}?section=payments" role="tab"
                                    aria-controls="custom-tabs-four-payment-history" aria-selected="false">
                                    PAYMENTS</a>
                            </li> --}}



                                <li class="nav-item">
                                    <a class="nav-link {{ request()->get('section') == 'promised-to-pay' ? 'active' : '' }}"
                                        id="custom-tabs-four-transaction-promised-to-pay-tab"
                                        href="/lead/{{ $leadDetails->id }}?section=promised-to-pay" role="tab"
                                        aria-controls="custom-tabs-four-transaction-history" aria-selected="false">
                                        PROMISED TO PAY</a>
                                </li>


                                {{-- <li class="nav-item">
                                    <a class="nav-link {{ request()->get('section') == 'call-disposition' ? 'active' : '' }}"
                                        id="custom-tabs-four-transaction-call-disposition-tab"
                                        href="/lead/{{ $leadDetails->id }}?section=call-disposition" role="tab"
                                        aria-controls="custom-tabs-four-transaction-history" aria-selected="false">
                                        CALL DISPOSITION</a>
                                </li> --}}

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->get('section') == 'transactions' ? 'active' : '' }}"
                                        id="custom-tabs-four-transaction-transactions-tab"
                                        href="/lead/{{ $leadDetails->id }}?section=transactions" role="tab"
                                        aria-controls="custom-tabs-four-transaction-history" aria-selected="false">
                                        PAYMENTS</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->get('section') == 'mtb' ? 'active' : '' }}"
                                        id="custom-tabs-four-mtb-tab"
                                        href="/lead/{{ $leadDetails->id }}?section=mtb" role="tab"
                                        aria-controls="custom-tabs-four-mtb" aria-selected="false">
                                        MTB</a>
                                </li>

                                {{-- <li class="nav-item">
                                    <a class="nav-link {{ request()->get('section') == 'status' ? 'active' : '' }}"
                                        id="custom-tabs-four-status-tab" role="tab"
                                        aria-controls="custom-tabs-four-status"
                                        href="/lead/{{ $leadDetails->id }}?section=status" aria-selected="false">
                                        STATUS</a>
                                </li> --}}






                                {{-- <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-four-status-tab" data-toggle="pill"
                                    href="#custom-tabs-four-status" role="tab" aria-controls="custom-tabs-four-status"
                                    aria-selected="false">
                                    DOCUMENTS</a>
                            </li> --}}


                                {{-- <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-four-customer-tab" data-toggle="pill"
                                    href="#custom-tabs-four-customer" role="tab"
                                    aria-controls="custom-tabs-four-customer" aria-selected="false">
                                    PENALTIES</a>
                            </li> --}}


                            </ul>
                        </div>
                        <div class="card-body">

                            <div class="tab-content" id="custom-tabs-four-tabContent">
                                <div class="tab-pane fade {{ empty(request()->get('section')) ? 'show active' : '' }}"
                                    id="custom-tabs-four-basic" role="tabpanel"
                                    aria-labelledby="custom-tabs-four-basic-tab">


                                    @if ($leadDetails->defaulter_type_id == $INDIVIDUAL_DEFAULTER_TYPE_CODE)
                                        @include('lead.show._basic_details_individual')
                                    @else
                                        @include('lead.show._basic_details_entity')
                                    @endif
                                </div>
                                <div class="tab-pane fade {{ request()->get('section') == 'activities' ? 'show active' : '' }}"
                                    id="custom-tabs-four-activities" role="tabpanel"
                                    aria-labelledby="custom-tabs-four-activities-tab">
                                    @include('lead.show._activities')
                                </div>
                                {{-- <div
                                class="tab-pane fade {{ request()->get('section') == 'payments' ? 'show active' : '' }}"
                                id="custom-tabs-four-payment-history" role="tabpanel"
                                aria-labelledby="custom-tabs-four-payment-history-tab">
                                @include('lead.show._payment_history')
                            </div> --}}


                                <div class="tab-pane fade {{ request()->get('section') == 'promised-to-pay' ? 'show active' : '' }}"
                                    id="custom-tabs-four-transaction-history" role="tabpanel"
                                    aria-labelledby="custom-tabs-four-transaction-history-tab">
                                    @include('lead.show._promised_to_pay')
                                </div>


                                <div class="tab-pane fade {{ request()->get('section') == 'call-disposition' ? 'show active' : '' }}"
                                    id="custom-tabs-four-transaction-history" role="tabpanel"
                                    aria-labelledby="custom-tabs-four-transaction-history-tab">
                                    @include('lead.show._call_disposition')
                                </div>




                                <div class="tab-pane fade {{ request()->get('section') == 'transactions' ? 'show active' : '' }}"
                                    id="custom-tabs-four-transaction-history" role="tabpanel"
                                    aria-labelledby="custom-tabs-four-transaction-history-tab">
                                    @include('lead.show._transaction_history')
                                </div>

                                <div class="tab-pane fade {{ request()->get('section') == 'mtb' ? 'show active' : '' }}"
                                    id="custom-tabs-four-mtb" role="tabpanel"
                                    aria-labelledby="custom-tabs-four-mtb-tab">
                                    @include('lead.show._mtb')
                                </div>

                                <div class="tab-pane fade {{ request()->get('section') == 'status' ? 'show active' : '' }}"
                                    id="custom-tabs-four-status" role="tabpanel"
                                    aria-labelledby="custom-tabs-four-status-tab">
                                    @include('lead.show._status')
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{--
<link rel="stylesheet" href="/css/admin_custom.css"> --}}

    <link rel="stylesheet" href="/css/validator/bootstrapValidator.min.css" />
    <link rel="stylesheet" href="/css/datepicker/datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">


    <style>
        .table td,
        .table th {
            padding: 4px;
        }

        .form-check-inline {
            /* margin-right: 20px; */
            /* Adjust the value as needed */
        }


        .ui-timepicker-container {
            z-index: 1056 !important;
            /* Higher than Bootstrap modal's default (1055) */
        }

        .errorDisp p {
            margin-bottom: 0px !important
        }

        .timeline {
            position: relative;
            margin: 20px 0;
            padding-left: 50px;
            /* Space for icons and line */
        }

        .timeline::before {
            content: "";
            position: absolute;
            top: 0;
            left: 30px;
            /* Position the line closer to the icon */
            width: 4px;
            height: 100%;
            /* background-color: #7f5af0; */
            border-radius: 2px;
        }

        .timeline-entry {
            position: relative;
            margin-bottom: 40px;
        }

        .timeline-date {
            position: absolute;
            top: 0;
            left: -8px;
            font-size: 13px
        }

        .timeline-icon {
            position: absolute;
            top: 0;
            left: -20px;
            /* Same as the line’s left value */
            transform: translate(-50%, 0);
            /* Center icon over the line */
            width: 30px;
            height: 30px;
            background-color: #c2c2c2;
            color: #fff;
            border-radius: 50%;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 2;
            /* Ensure icon is above the line */
        }

        .timeline-time {
            font-size: 14px;
            font-weight: bold;
            color: #5c5c5c;
            margin-bottom: 8px;
            margin-left: 15px;
            /* Space from the line */
        }

        .timeline-content {
            background-color: #fff;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-left: 20px;
            /* Align content to the right of the icon and line */
            position: relative;
        }

        .timeline-content::before {
            content: "";
            position: absolute;
            top: 18px;
            left: -12px;
            /* Pointer towards the line */
            border-width: 8px;
            border-style: solid;
            border-color: transparent #fff transparent transparent;
        }

        .timeline-content h2 {
            font-size: 18px;
            color: #333;
            margin: 0 0 10px;
        }

        .timeline-content p {
            font-size: 14px;
            color: #555;
            margin: 0;
        }

        @media (max-width: 600px) {
            .timeline {
                padding-left: 40px;
            }

            .timeline-time,
            .timeline-content {
                margin-left: 70px;
            }
        }

        .trans_options {
            display: none
        }
    </style>
@stop

@section('js')

    <script src="/js/validator/bootstrapValidator.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/datepicker/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 elements
            $('#new_debt_modal').on('shown.bs.modal', function() {

                $('.select2, .call-disposition-select2, .assigned-agent-select2, .assigned-department-select2')
                    .select2({
                        dropdownParent: $('#new_debt_modal .modal-body'), // Target modal body instead
                        width: '100%'
                    });
            });



            function confirmDelete(event) {
                if (!confirm('Are you sure you want to delete?')) {
                    event.preventDefault();
                    return false;
                }
                return true;
            }

            $('#ptpsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('ptps.data') }}',
                    data: function(d) {
                        d.lead_id = '{{ $leadDetails->id }}'; // Pass lead_id
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },

                    {
                        data: 'ptp_date',
                        name: 'ptp_date',
                        render: function(data) {
                            return formatDate(data, 'date');
                        }
                    },


                    {
                        data: 'ptp_amount',
                        name: 'ptp_amount'
                    },

                    {
                        data: 'ptp_expiry_date',
                        name: 'ptp_expiry_date',
                        render: function(data) {
                            return formatDate(data, 'date');
                        }
                    },

                    {
                        data: 'created_by_name',
                        name: 'users.name'
                    },
                    // { data: 'updated_by_name', name: 'updated_users.name' },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return formatDate(data, 'datetime');
                        }
                    },

                    {
                        data: null,
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const canDelete = @json(auth()->user()->hasAnyRole(['Admin', 'Supervisor', 'Manager']));

                            let buttons = '';

                            if (canDelete) {
                                buttons += `
                    <form action="/leads/delete-ptp/${row.id}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-xs delete-ptp-btn" onclick="return confirm('Are you sure you want to delete?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>`;
                            }

                            return buttons;
                        }
                    }

                ]
            });




            $('#callDispositionTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('call-dispositions.data') }}',
                    data: function(d) {
                        d.lead_id = '{{ $leadDetails->id }}'; // Pass lead_id
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'call_disposition_name',
                        name: 'call_dispositions.call_disposition_name'
                    },
                    {
                        data: 'created_at',
                        name: 'call_disposition_histories.created_at',
                        render: function(data) {
                            return formatDate(data, 'date');
                        }
                    },
                    {
                        data: 'created_by_name',
                        name: 'users.name'
                    },
                    {
                        data: 'created_at',
                        name: 'call_disposition_histories.created_at',
                        render: function(data) {
                            return formatDate(data, 'datetime');
                        }
                    }
                ]
            });





            function formatDate(dateString, type = 'date') {
                if (!dateString) return 'N/A';

                const date = new Date(dateString);
                if (isNaN(date)) return 'Invalid Date';

                const options = {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                };

                if (type === 'datetime') {
                    options.hour = '2-digit';
                    options.minute = '2-digit';
                }

                return date.toLocaleDateString('en-US', options);
            }




            $('#mtbTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('mtb.data') }}',
                    data: function(d) {
                        d.lead_id = '{{ $leadDetails->id }}'; // Pass lead_id
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'amount_paid',
                        name: 'amount_paid',
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: 'date_paid',
                        name: 'date_paid',
                        render: function(data) {
                            return formatDate(data, 'date');
                        }
                    },
                    {
                        data: 'payment_channel',
                        name: 'payment_channel'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'created_by_name',
                        name: 'created_by.name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return formatDate(data, 'datetime');
                        }
                    },
                    {
                        data: null,
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const canEdit = @json(auth()->user()->hasAnyRole(['Admin', 'Supervisor', 'Manager']));

                            let buttons = '';

                            if (canEdit) {
                                buttons += `
                    <button class="btn btn-warning btn-xs edit-mtb-btn" data-id="${row.id}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-xs delete-mtb-btn" data-id="${row.id}">
                        <i class="fas fa-trash"></i> Delete
                    </button>`;
                            }

                            return buttons;
                        }
                    }
                ]
            });

            $('#transactionsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('transactions.data') }}',
                    data: function(d) {
                        d.lead_id = '{{ $leadDetails->id }}'; // Pass lead_id
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'transaction_type_title',
                        name: 'transaction_types.transaction_type_title'
                    },
                    {
                        data: 'created_by_name',
                        name: 'users.name'
                    },
                    {
                        data: 'status_label',
                        name: 'transaction_statuses.status_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: null,
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const canEdit = @json(auth()->user()->hasRole('Admin'));

                            let buttons = '';

                            if (canEdit) {
                                buttons += `
                    <button class="btn btn-warning btn-xs edit-transaction-btn" data-id="${row.id}" data-transaction="${row.transaction_type_title}" data-penaltyTypeId="${row.penalty_type_id}">
                        <i class="fas fa-edit"></i> Edit
                    </button>`;
                            }

                            return buttons;
                        }
                    }

                ]
            });

            // Handle Edit MTB Button Click
            $(document).on('click', '.edit-mtb-btn', function() {
                var mtbId = $(this).data('id');

                // Open modal
                $('#edit_mtb_modal').modal('show');

                // Load MTB details
                $.ajax({
                    url: '/mtb/' + mtbId + '/edit',
                    method: 'GET',
                    success: function(response) {
                        $('#edit_mtb_id').val(response.id);
                        $('#edit_amount_paid').val(response.amount_paid);
                        $('#edit_date_paid').val(response.date_paid);
                        $('#edit_payment_channel').val(response.payment_channel);
                        $('#edit_description').val(response.description);
                    }
                });
            });

            // Handle Delete MTB Button Click
            $(document).on('click', '.delete-mtb-btn', function() {
                var mtbId = $(this).data('id');

                if (confirm('Are you sure you want to delete this MTB record?')) {
                    $.ajax({
                        url: '/mtb/' + mtbId,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#mtbTable').DataTable().ajax.reload();
                            alert('MTB record deleted successfully!');
                        },
                        error: function() {
                            alert('Error deleting MTB record!');
                        }
                    });
                }
            });

            // Handle Edit Button Click
            $(document).on('click', '.edit-transaction-btn', function() {
                var transactionId = $(this).data('id');
                var transactionTypeTitle = $(this).data('transaction');
                var penaltyType = $(this).data('penaltyTypeId');



                // Optionally set display in read-only input
                $('#edit_transaction_modal input[name="transactionTypesEdit"]').val(transactionTypeTitle);

                // Open modal
                $('#edit_transaction_modal').modal('show');

                // Load transaction details
                $.ajax({
                    url: '/transactions/' + transactionId + '/edit',
                    method: 'GET',
                    success: function(response) {
                        console.log(response)
                        // === PAYMENT FIELDS ===
                        $('#edit_transaction_modal input[name="amount"]').val(response.amount);
                        $('#edit_transaction_modal input[name="transID"]').val(response
                            .transaction_id);
                        $('#edit_transaction_modal input[name="transRecordId"]').val(response
                            .id);
                        $('#edit_transaction_modal select[name="payment_method"]').val(response
                            .payment_method).trigger('change');
                        $('#edit_transaction_modal select[name="payment_status"]').val(response
                            .status_id).trigger('change');
                        $('#edit_transaction_modal textarea[name="description"]').val(response
                            .description);

                        // === PENALTY FIELDS ===
                        $('#edit_transaction_modal select[name="penalty_type"]').val(response
                            .penalty_type_id).trigger('change');

                        $('#edit_transaction_modal input[name="trans_type_select"]').val(
                            response.transaction_type);

                        $('#edit_transaction_modal select[name="charge_type"]').val(response
                            .charge_type).trigger('change');

                        $('#edit_transaction_modal input[name="value"]').val(response.amount);

                        // === Show alert if penalty_type === 1
                        if (response.penalty_type_id == 1) {
                            $('.penalty-note').removeClass('d-none');
                        } else {
                            $('.penalty-note').addClass('d-none');
                        }
                        // Map text title to numeric ID
                        let transactionTypeMap = {
                            'Penalty': 1,
                            'Payment': 2,
                            'Discount': 3
                        };

                        let transactionTypeId = transactionTypeMap[transactionTypeTitle
                            .trim()] || 0;

                        // Show correct section
                        handleTransactionTypeDisplay(transactionTypeId);

                        $('#edit_transaction_modal input[name="transactionTypesEdit"]').val(
                            transactionTypeText);

                    }
                });
            });

            // Helper Function to toggle form sections
            function toggleTransactionSections(typeId) {
                $('.trans_options').hide();

                if (typeId == 1) {
                    $('._payment').show();
                } else if (typeId == 2) {
                    $('._penalty').show();
                } else if (typeId == 3) {
                    $('._discount').show();
                }
            }



            $('.user_form')
                .bootstrapValidator({
                    excluded: [':disabled'],
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                });

            // Initialize Select2


            // Initialize Datepicker
            $('.date').datepicker({
                autoclose: true,
                format: 'dd-mm-yyyy',
                todayHighlight: true,
                orientation: "top auto"
            });

            // Initialize Timepicker
            $('.timepicker').timepicker({
                timeFormat: 'hh:mm p',
                interval: 30,
                dropdown: true,
                scrollbar: true,
                orientation: 'top'
            });

            // Function to toggle fields based on selected activity type
            function toggleFields(activityType) {


                $('.ptp_row').hide();
                $('.sms_template_row').hide();
                const showFields = [1, 2, 6].includes(parseInt(activityType));

                $('.priority-group, .due-date-group, .department-group, .agent-group, .status-group').toggle(
                    showFields);


                $('#setStartDateLabel').text(showFields ? 'Set Start Date' : 'Schedule');

                if (activityType == 8) {
                    $('.sms_template_row').show();

                }

                if (activityType == 4) {
                    $('.ptp_row').show();

                    // Auto-check the PTP checkbox and disable it
                    $('#addPTP').prop('checked', true);
                    $('#addPTP').prop('disabled', true);

                    // Create/update hidden field to ensure value is submitted
                    if ($('#addPTP_hidden').length === 0) {
                        $('#addPTP').after('<input type="hidden" id="addPTP_hidden" name="addPTP" value="1">');
                    } else {
                        $('#addPTP_hidden').val('1');
                    }

                    // Optional: Add visual indication that it's disabled
                    $('label[for="addPTP"]').css('opacity', '0.7');

                } else {
                    // For other activity types, uncheck and enable
                    $('#addPTP').prop('checked', false);
                    $('#addPTP').prop('disabled', false);

                    // Remove or update hidden field
                    $('#addPTP_hidden').remove();

                    // Remove visual indication
                    $('label[for="addPTP"]').css('opacity', '1');
                }

                if (activityType == 19) {
                    $('.payment_row').show();

                    // Auto-check the Payment checkbox and make it readonly with click prevention
                    $('#addPayment').prop('checked', true);
                    $('#addPayment').prop('readonly', true);
                    $('#addPayment').css('pointer-events', 'none'); // Prevent clicking

                    // Prevent any attempts to uncheck via JavaScript or user interaction
                    $('#addPayment').on('click.paymentLock', function(e) {
                        e.preventDefault();
                        return false;
                    });

                    // Optional: Add visual indication that it's locked
                    $('label[for="addPayment"]').css('opacity', '0.7');

                } else {
                    $('.payment_row').hide();

                    // For other activity types, uncheck and make editable
                    $('#addPayment').prop('checked', false);
                    $('#addPayment').prop('readonly', false);
                    $('#addPayment').css('pointer-events', 'auto'); // Allow clicking

                    // Remove the click prevention event
                    $('#addPayment').off('click.paymentLock');

                    // Remove visual indication
                    $('label[for="addPayment"]').css('opacity', '1');
                }


                if (activityType == 4 || activityType == 19 || activityType == 23 ||
                    activityType == 28 || activityType == 29) {
                    $('.add_ptp_checkbox').show();
                    $('.add_payment_checkbox').show();

                } else {
                    $('.add_ptp_checkbox').hide();
                    $('.add_payment_checkbox').hide();
                }

            }

            // On activityType change
            $(document).on('change', 'select[name="activityType"]', function() {
                //alert('me');
                toggleFields($(this).val());
            });




            $(document).on('change', 'select[name="sms_template"]', function() {
                var selectedValue = $(this).val();
                var selectedText = $(this).find('option:selected').text();

                $('#activity_title').val(selectedText);

                var sms_message = '';

                if (selectedValue == 'introduction') {
                    sms_message =
                        "Dear {{ $leadDetails->title }}, Your debt for {{ $leadDetails->institution_name }}, of {{ $leadDetails->currency_name }} {{ $leadDetails->balance }} has been forwarded to Beyond BeanCounters for recovery. Urgently pay via {{ $leadDetails->how_to_pay_instructions }}, account: {{ $leadDetails->account_number }}, or reach out to us to discuss a repayment plan, 0701967176.";
                }


                if (selectedValue == 'no_anwser') {
                    sms_message =
                        "{{ $leadDetails->title }}, we have tried calling you without success. Kindly but urgently get in touch with us to discuss your debt with {{ $leadDetails->institution_name }} of {{ $leadDetails->currency_name }} {{ $leadDetails->balance }}. The debt ought to be settled to avoid additional penalties and other charges. Pay through {{ $leadDetails->how_to_pay_instructions }}, account number {{ $leadDetails->account_number }}. Notify us on 0701967176.";
                }

                if (selectedValue == 'ptp_reminder') {
                    sms_message =
                        "Dear {{ $leadDetails->title }}, remember to make payment for Your debt for {{ $leadDetails->institution_name }}, of {{ $leadDetails->currency_name }} {{ $leadDetails->balance }} today. {{ $leadDetails->how_to_pay_instructions }}, account: {{ $leadDetails->account_number }}. Notify us on 0701967176";
                }


                if (selectedValue == 'refusal_to_pay') {
                    sms_message =
                        "{{ $leadDetails->title }}, Despite previous reminders, your {{ $leadDetails->institution_name }} debt for {{ $leadDetails->currency_name }} {{ $leadDetails->balance }}, remains uncleared. Be strongly advised that failure to do so will force us to recover the debt at your cost, using our Field Collectors. Pay through {{ $leadDetails->how_to_pay_instructions }}, account {{ $leadDetails->account_number }}. Notify us on 0701967176.";
                }


                if (selectedValue == 'broken_ptp_follow_up') {
                    sms_message =
                        "Greetings, we have not yet received your  {{ $leadDetails->institution_name }} payment. Urgently pay. {{ $leadDetails->how_to_pay_instructions }}, Acc: {{ $leadDetails->account_number }}. Notify us on 0701967176";
                }


                $('#description').val(sms_message);


                if (selectedValue != 'other') {
                    $('#description').prop('readonly', true).css('background-color', '#f5f5f5');
                    $('#activity_title').prop('readonly', true).css('background-color', '#f5f5f5');
                } else {
                    $('#activity_title').val('');
                    $('#description').prop('readonly', false).css('background-color', '#ffffff');
                    $('#activity_title').prop('readonly', false).css('background-color', '#ffffff');
                }


            });

            // Toggle Start Date Inputs
            $('#setStartDate').on('change', function() {
                $('#startDateInputs').toggleClass('d-none', !this.checked);
            });

            // Toggle Due Date Inputs
            $('#setDueDate').on('change', function() {
                $('#dueDateInputs').toggleClass('d-none', !this.checked);
            });

            // Toggle Start Date Inputs Edit
            $('#setStartDateEdit').on('change', function() {
                $('#startDateInputsEdit').toggleClass('d-none', !this.checked);
            });

            // Toggle Due Date Inputs Edit
            $('#setDueDateEdit').on('change', function() {
                $('#dueDateInputsEdit').toggleClass('d-none', !this.checked);
            });

            // Initialize Bootstrap 5 modals
            var newActivityModal = document.getElementById('new_debt_modal');
            if (newActivityModal) {
                var bsNewActivityModal = new bootstrap.Modal(newActivityModal);
            }





            $('#addPTP').on('change', function() {

                if (this.checked) {
                    $('.ptp_row').show();
                } else {
                    $('.ptp_row').hide();
                }


            });


            $('#addPayment').on('change', function() {

                if (this.checked) {
                    $('.payment_row').show();
                } else {
                    $('.payment_row').hide();
                }


            });


            // Form validation
            $('.activity_form').on('submit', function(e) {
                e.preventDefault();



                // Disable the submit button and show progress
                let submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin"></i> Submitting...');

                let errorHtml = "";
                //  let activityType = $('input[name="activityType"]:checked').val();
                let activityTitle = $('#activity_title').val();
                let description = $('#description').val();
                let priority = $('#priority').val();
                let startDate = $('#start_date').val();
                let startTime = $('#start_time').val();
                let endDate = $('#end_date').val();
                let endTime = $('#end_time').val();
                let department = $('#department').val();
                let agent = $('#agent').val();
                let status = $('#status').val();
                let addToCalendar = $('#addToCalendar').prop('checked');

                let activityType = $('#activityType').val();



                let addPTP = $('#addPTP').prop('checked');
                let ptp_amount = $('#ptp_amount').val();
                let ptp_payment_date = $('#ptp_payment_date').val();




                let addPayment = $('#addPayment').prop('checked');
                let payment_amount = $('#payment_amount').val();
                let payment_transID = $('#payment_transID').val();
                let payment_method = $('#payment_method').val();



                let call_disposition = $('#call_disposition').val();




                $('.errorDisp').hide().html('');




                if (activityType === '') {
                    errorHtml += "<p>Please Select the Activity Type/Category</p>";
                }

                if (description === '') {
                    errorHtml += "<p>Please Enter Activity Text or Description</p>";
                }


                if (addPTP) {
                    if (ptp_amount === '') {
                        errorHtml += "<p>Please Enter PTP Amount</p>";
                    }

                    if (ptp_payment_date === '') {
                        errorHtml += "<p>Please Enter PTP Payment Date</p>";
                    }
                }


                if (addPayment) {
                    if (payment_amount === '') {
                        errorHtml += "<p>Please Enter Payment Amount</p>";
                    }

                    if (payment_method === '') {
                        errorHtml += "<p>Please Select Payment Method</p>";
                    }
                }



                if (call_disposition === '') {
                    errorHtml += "<p>Please Select the Call Disposition</p>";
                }


                if ([1, 2, 6].includes(parseInt(activityType))) {
                    if (priority === null || priority === '') {
                        errorHtml += "<p>Please Select Activity Priority</p>";
                    }
                    if (status === null || status === '') {
                        errorHtml += "<p>Please Select Activity Status</p>";
                    }
                }

                if (errorHtml) {
                    $('.errorDisp').html(errorHtml).show(); // Display errors
                    submitBtn.prop('disabled', false).html('Submit Details'); // Re-enable button on error
                } else {
                    // Submit the form
                    this.submit();
                }
            });


            $('#transactionTypes').on('change', function() {
                var selectedValue = $(this).val();


                $('.trans_type_select').val(selectedValue)
                $('.trans_options').hide();
                if (selectedValue == 1) {
                    $('._penalty').show()
                }

                if (selectedValue == 2) {
                    $('._payment').show()
                }

                if (selectedValue == 3) {
                    $('._discount').show()
                }

            });

            function handleTransactionTypeDisplay(value) {
                $('.trans_options').hide(); // hide all by default

                if (value == 1) {
                    $('._penalty').show();
                } else if (value == 2) {
                    $('._payment').show();
                } else if (value == 3) {
                    $('._discount').show();
                }
            }






        });
    </script>




@stop
