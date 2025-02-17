<form action="{{ route('lead.store') }}" method="POST" class="user_form">
    @csrf {{-- Add CSRF protection --}}
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Entity Name*', 'entity_name') !!}
                {!! Html::text('entity_name')->class('form-control')
                ->placeholder('Enter Entity Name')
                ->required()
                !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <input type="hidden" class="entity_type_store" name="entity_type_store" value="">
            <button type="submit" class="btn btn-primary"><strong>SUBMIT DETAILS</strong></button>
        </div>
    </div>
</form>