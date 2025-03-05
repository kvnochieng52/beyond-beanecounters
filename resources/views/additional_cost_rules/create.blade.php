@extends('adminlte::page')

@section('title', 'Add Additional Cost Rule')

@section('content_header')
{{-- <h1>Add Additional Cost Rule</h1> --}}
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <h5>Create New Rule</h5>
    </div>

    <div class="card-body">

        @include('notices')
        <form action="{{ route('additional-cost-rules.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" name="title" required>
            </div>

            <div class="mb-3">
                <label for="cost_type" class="form-label">Cost Type</label>
                <select class="form-control" name="cost_type" required>
                    @foreach($costTypes as $costType)
                    <option value="{{ $costType->id }}">{{ $costType->rule_type_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <select class="form-control" name="type" required>
                    <option value="Fixed amount">Fixed amount</option>
                    <option value="Percentage">Percentage</option>
                </select>
            </div>



            <div class="mb-3">
                <label for="value" class="form-label">Value</label>
                <input type="number" class="form-control" name="value" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="apply_due_date" name="apply_due_date">
                <label class="form-check-label" for="apply_due_date">Set Due Date</label>
            </div>

            <div class="mb-3" id="days_input" style="display: none;">
                <label for="days" class="form-label">Days</label>
                <input type="number" class="form-control" name="days">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description"></textarea>
            </div>

            <div class="mb-3">
                <label for="is_active" class="form-label">Is Active</label>
                <select class="form-control" name="is_active" required>
                    <option value="">--select--</option>
                    <option value="1">Active</option>
                    <option value="0">Not Active</option>

                </select>
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>

@stop

@section('js')
<script>
    document.getElementById('apply_due_date').addEventListener('change', function() {
        document.getElementById('days_input').style.display = this.checked ? 'block' : 'none';
    });
</script>
@stop