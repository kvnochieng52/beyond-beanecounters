@extends('adminlte::page')

@section('title', 'Edit Additional Cost Rule')

@section('content_header')
{{-- <h1>Edit Additional Cost Rule</h1> --}}
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <h3>Edit Rule</h3>
    </div>

    <div class="card-body">
        @include('notices')
        <form action="{{ route('additional-cost-rules.update', $additionalCostRule->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" name="title" value="{{ $additionalCostRule->title }}" required>
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <select class="form-control" name="type">
                    <option value="Fixed amount" {{ $additionalCostRule->type == 'Fixed amount' ? 'selected' : ''
                        }}>Fixed amount</option>
                    <option value="Percentage" {{ $additionalCostRule->type == 'Percentage' ? 'selected' : ''
                        }}>Percentage</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="cost_type" class="form-label">Cost Type</label>
                <select class="form-control" name="cost_type">
                    @foreach($costTypes as $costType)
                    <option value="{{ $costType->id }}" {{ $costType->id == $additionalCostRule->cost_type ? 'selected'
                        : '' }}>{{ $costType->rule_type_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="value" class="form-label">Value</label>
                <input type="number" class="form-control" name="value" value="{{ $additionalCostRule->value }}"
                    required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="apply_due_date" name="apply_due_date" {{
                    $additionalCostRule->apply_due_date ? 'checked' : '' }}>
                <label class="form-check-label" for="apply_due_date">Set Due Date</label>
            </div>

            <div class="mb-3" id="days_input"
                style="{{ $additionalCostRule->apply_due_date ? 'display: block;' : 'display: none;' }}">
                <label for="days" class="form-label">Days</label>
                <input type="number" class="form-control" name="days" value="{{ $additionalCostRule->days }}">
            </div>


            <div class="mb-3">
                <label for="is_active" class="form-label">Is Active</label>
                <select class="form-control" name="is_active" required>
                    <option value="">--select--</option>
                    <option value="1" {{$additionalCostRule->cost_type ==1 ? 'selected': '' }}>Active</option>
                    <option value="0" {{$additionalCostRule->cost_type ==0 ? 'selected': '' }}>Not Active</option>

                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
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