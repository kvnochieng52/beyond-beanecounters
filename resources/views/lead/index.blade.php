@extends('adminlte::page')

@section('title', 'Leads')

@section('content_header')
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Leads</h3>
        <p style="float: right; margin-bottom:0px">Fields marked * are mandatory </p>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>T/No.</th>
                    <th>Names/Title</th>
                    <th>Defaulter Type</th>
                    <th>ID Number</th>
                    <th>Telephone</th>
                    <th>Amount</th>
                    <th>Balance</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Stage</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($leads as $key => $lead)
                <tr>
                    <td>{{ $leads->firstItem() + $key }}</td>
                    <td>
                        <a href="/lead/{{$lead->id}}">

                            <strong>#{{ $lead->id }}</strong>

                        </a>
                    </td>
                    <td>
                        <a href="/lead/{{$lead->id}}"><strong>{{ $lead->title }}</strong></a><br />
                        <small>Agent: {{ $lead->assigned_agent_name }}</small>
                    </td>
                    <td>{{ $lead->defaulter_type_name }}</td>
                    <td>{{ $lead->id_passport_number }}</td>
                    <td>{{ $lead->telephone }}</td>
                    <td>{{ $lead->currency_name }} {{ number_format($lead->amount, 0) }}</td>
                    <td>{{ $lead->currency_name }} {{ number_format($lead->balance, 0) }}</td>
                    <td>
                        <a href="/lead/{{$lead->id}}">
                            <span class="badge bg-{{ $lead->lead_priority_color_code }}">
                                {{$lead->lead_priority_name}}
                            </span>
                        </a>
                    </td>
                    <td>
                        <a href="/lead/{{$lead->id}}">
                            <span class="badge bg-{{ $lead->lead_status_color_code }}">
                                {{ $lead->lead_status_name}}
                            </span>
                        </a>
                    </td>
                    <td>{{ $lead->lead_stage_name }}</td>
                    <td>
                        <a href="/lead/{{$lead->id}}/edit" class="btn btn-warning btn-xs">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="#" class="btn btn-danger btn-xs" onclick="confirmDelete({{ $lead->id }})">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $leads->links('') }}
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="/css/validator/bootstrapValidator.min.css" />
@stop

@section('js')
<script src="/js/validator/bootstrapValidator.min.js"></script>
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
    });
</script>
@stop