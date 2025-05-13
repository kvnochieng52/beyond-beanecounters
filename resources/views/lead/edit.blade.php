@extends('adminlte::page')

@section('title', 'Edit Lead')

@section('content_header')
@stop

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Edit Lead</h3>
        <p class="mb-0">Fields marked * are mandatory</p>
    </div>

    <div class="card-body">
        @include('notices')

        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ empty(request()->get('step')) ? 'active' : '' }}"
                            id="custom-tabs-four-basic-tab" data-toggle="pill" href="#custom-tabs-four-basic" role="tab"
                            aria-controls="custom-tabs-four-basic" aria-selected="true">
                            <strong>BASIC DETAILS</strong>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->get('step') == '2' ? 'active' : '' }}"
                            id="custom-tabs-four-debt-tab" data-toggle="pill" href="#custom-tabs-four-debt" role="tab"
                            aria-controls="custom-tabs-four-debt" aria-selected="false">
                            <strong>DEBT DETAILS</strong>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade {{ empty(request()->get('step')) ? 'show active' : '' }}"
                        id="custom-tabs-four-basic" role="tabpanel" aria-labelledby="custom-tabs-four-basic-tab">


                        @if($lead->defaulter_type_id==$individualDefaulterType)
                        @include('lead.edit._individual_basic_details')
                        @else
                        @include('lead.edit._entity_basic_details')

                        @endif
                    </div>

                    <div class="tab-pane fade {{ request()->get('step') == '2' ? 'show active' : '' }}"
                        id="custom-tabs-four-debt" role="tabpanel" aria-labelledby="custom-tabs-four-debt-tab">

                        @if($lead->defaulter_type_id==$individualDefaulterType)
                        @include('lead.edit._individual_debt_details')
                        @else
                        @include('lead.edit._entity_debt_details')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="/css/validator/bootstrapValidator.min.css">
<link rel="stylesheet" href="/css/datepicker/datepicker.min.css">

<style>
    .select2-container {
        width: 100% !important;
    }
</style>
@stop

@section('js')
<script src="/js/validator/bootstrapValidator.min.js"></script>
<script src="/js/datepicker/bootstrap-datepicker.min.js"></script>

<script>
    $(document).ready(function() {
        $('.user_form').bootstrapValidator({
            excluded: [':disabled'],
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
        });

        $('.select2').select2();

        $('#defaulter_type').on('change', function() {
            var selectedValue = $(this).val();
            $(".defaulter_type_store").val(selectedValue);
            $('.defaulter_type').hide();

            if (selectedValue == 1) {
                $('.individual_type').show();
            } else {
                $('.entity_type').show();
            }
        });

        $('.date').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
            todayHighlight: true,
            orientation: "top auto"
        }).on('changeDate show', function(e) {
            $('.user_form').bootstrapValidator('revalidateField', 'date');
        });
    });
</script>
@stop