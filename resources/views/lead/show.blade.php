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
                                <a class="nav-link {{ empty(request()->get('page')) ? 'active' : '' }}"
                                    id="custom-tabs-four-basic-tab" data-toggle="pill" href="#custom-tabs-four-basic"
                                    role="tab" aria-controls="custom-tabs-four-basic" aria-selected="true">BASIC
                                    DETAILS</a>
                            </li>


                            <li class="nav-item">
                                <a class="nav-link {{ request()->get('page') == 'activities' ? 'active' : '' }}"
                                    id="custom-tabs-four-activities-tab" data-toggle="pill"
                                    href="#custom-tabs-four-activities" role="tab"
                                    aria-controls="custom-tabs-four-activities" aria-selected="false">
                                    ACTIVITIES</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-four-payment-history-tab" data-toggle="pill"
                                    href="#custom-tabs-four-payment-history" role="tab"
                                    aria-controls="custom-tabs-four-payment-history" aria-selected="false">
                                    PAYMENT HISTORY</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-four-status-tab" data-toggle="pill"
                                    href="#custom-tabs-four-status" role="tab" aria-controls="custom-tabs-four-status"
                                    aria-selected="false">
                                    STATUS</a>
                            </li>


                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-four-status-tab" data-toggle="pill"
                                    href="#custom-tabs-four-status" role="tab" aria-controls="custom-tabs-four-status"
                                    aria-selected="false">
                                    DOSUMENTS</a>
                            </li>


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
                            <div class="tab-pane fade {{ empty(request()->get('page')) ? 'show active' : '' }}"
                                id="custom-tabs-four-basic" role="tabpanel"
                                aria-labelledby="custom-tabs-four-basic-tab">


                                @if($leadDetails->defaulter_type_id==$INDIVIDUAL_DEFAULTER_TYPE_CODE)
                                @include('lead.show._basic_details_individual')
                                @else
                                @include('lead.show._basic_details_entity')

                                @endif
                            </div>
                            <div class="tab-pane fade {{ request()->get('page') == 'activities' ? 'show active' : '' }}"
                                id="custom-tabs-four-activities" role="tabpanel"
                                aria-labelledby="custom-tabs-four-activities-tab">
                                @include('lead.show._activities')
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-four-payment-history" role="tabpanel"
                                aria-labelledby="custom-tabs-four-payment-history-tab">
                                @include('lead.show._payment_history')
                            </div>

                            <div class="tab-pane fade" id="custom-tabs-four-status" role="tabpanel"
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
</style>
@stop

@section('js')

<script src="/js/validator/bootstrapValidator.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/datepicker/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>

<script>
    $(document).ready(function() {

    // Initialize Select2
    $('.select2').select2();

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
        const showFields = [1, 2, 6].includes(parseInt(activityType));

        $('.priority-group, .due-date-group, .department-group, .agent-group, .status-group').toggle(showFields);
        $('#setStartDateLabel').text(showFields ? 'Set Start Date' : 'Schedule');
    }

    // On activityType change
    $(document).on('change', 'input[name="activityType"]', function() {
        toggleFields($(this).val());
    });

    // Toggle Start Date Inputs
    $('#setStartDate').on('change', function() {
        $('#startDateInputs').toggleClass('d-none', !this.checked);
    });

    // Toggle Due Date Inputs
    $('#setDueDate').on('change', function() {
        $('#dueDateInputs').toggleClass('d-none', !this.checked);
    });

    // Form validation
    $('.activity_form').on('submit', function(e) {
    e.preventDefault();

    // Disable the submit button and show progress
    let submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

    let errorHtml = "";
    let activityType = $('input[name="activityType"]:checked').val();
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

    $('.errorDisp').hide().html('');

    if (activityTitle === '') {
        errorHtml += "<p>Please Enter Activity Title</p>";
    }

    if (description === '') {
        errorHtml += "<p>Please Enter Activity Text or Description</p>";
    }

    if ([1, 2, 6].includes(parseInt(activityType))) {
        if (priority === '') {
            errorHtml += "<p>Please Select Activity Priority</p>";
        }
        if (status === '') {
            errorHtml += "<p>Please Select Activity Status</p>";
        }
    }

    if (errorHtml) {
        $('.errorDisp').html(errorHtml).show(); // Display errors
        submitBtn.prop('disabled', false).html('Submit'); // Re-enable button on error
    } else {
        // Simulate an AJAX call or form processing (replace with actual submission logic)
        setTimeout(() => {
            this.submit(); // Submit form after processing
        }, 1000); // Simulated delay
    }
});



 
});

</script>


@stop