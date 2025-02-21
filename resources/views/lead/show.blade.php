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
                            <div class="tab-pane fade {{ request()->get('page') == 'debts' ? 'show active' : '' }}"
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
    $('.user_form').on('submit', function(e) {

       // e.preventDefault();
        // let isValid = true;

        // $(this).find('[required]').each(function() {
        //     if (!$(this).val().trim()) {
        //         $(this).addClass('is-invalid');
        //         isValid = false;
        //     } else {
        //         $(this).removeClass('is-invalid');
        //     }
        // });

        // if (!isValid) {
          
        //     alert('Please fill in all required fields.');
        // }
    });



 
});

</script>


@stop