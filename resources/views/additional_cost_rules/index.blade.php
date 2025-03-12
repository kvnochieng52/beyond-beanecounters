@extends('adminlte::page')

@section('title', 'Additional Cost Rules')

@section('content_header')
{{-- <h1>Additional Cost Rules</h1> --}}
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Rules/Discounts</h3>
        <a href="{{ route('additional-cost-rules.create') }}" class="btn btn-info float-right">Add Rule</a>
    </div>

    <div class="card-body">
        <table id="costRulesTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Code</th>
                    <th>Cost Type</th>
                    <th>Value</th>
                    <th>Due Date Applied</th>
                    <th>Days</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@stop

@section('css')
{{-- Add extra styles here --}}
@stop

@section('js')
<script>
    $(document).ready(function () {
        $('#costRulesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('additional-cost-rules.index') }}",
            columns: [
                { data: 'title', name: 'title' },
                { data: 'rule_code', name: 'rule_code' },
                { data: 'rule_type_name', name: 'additional_cost_rule_types.rule_type_name' },
                { data: 'value', name: 'value' },
                { data: 'apply_due_date', name: 'apply_due_date' },
                { data: 'days', name: 'days' },
                { data: 'active', name: 'is_active', orderable: false, searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ]
        });
    });
</script>
@stop