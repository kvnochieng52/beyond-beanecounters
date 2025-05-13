<h6><strong><i class="fas fa-fw fa-building "></i> BASIC DETAILS</strong></h6>

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

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Telephone*', 'telephone') !!}
                {!! Html::text('telephone')->class('form-control')
                ->placeholder('Enter Telephone')
                ->required()
                !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Alternate Telephone', 'alternate_telephone') !!}
                {!! Html::text('alternate_telephone')->class('form-control')
                ->placeholder('Enter Alternate Telephone')

                !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Email*', 'email') !!}
                {!! Html::email('email')->class('form-control')
                ->placeholder('Enter Email')
                ->required()
                !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Country*', 'country') !!}
                {!!
                Html::select('country',
                $countries)->class('form-control select2')->placeholder('--Specify--')
                ->style("width:100%")
                ->required()
                !!}
            </div>

        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Town*', 'town') !!}
                {!! Html::text('town')->class('form-control')
                ->placeholder('Enter town')
                ->required()
                !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Address', 'address') !!}
                {!! Html::textarea('address')->class('form-control')
                ->placeholder('Enter Address')
                !!}
            </div>

        </div>
    </div>


    <div class="row">

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Industry*', 'industry') !!}
                {!!
                Html::select('industry',
                $industries)->class('form-control select2')->placeholder('--Specify--')
                ->style("width:100%")
                ->required()
                !!}
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <input type="hidden" class="defaulter_type_store" name="defaulter_type_store" value="">
            <button type="submit" class="btn btn-primary"><strong>SUBMIT DETAILS</strong></button>
        </div>
    </div>
</form>