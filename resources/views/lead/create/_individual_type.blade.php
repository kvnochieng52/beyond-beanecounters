<form action="{{ route('lead.store') }}" method="POST" class="user_form">
    @csrf {{-- Add CSRF protection --}}

    <div class="row">
        <div class="col-md-12">
            <h6><strong><i class="fas fa-fw fa-user "></i> BASIC DETAILS</strong></h6>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Full Names*', 'full_names') !!}
                {!! Html::text('full_names')->class('form-control')
                ->placeholder('Enter Full Names')
                ->required()
                !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('ID Number*', 'id_number') !!}
                {!! Html::text('id_number')->class('form-control')
                ->placeholder('Enter ID Number')
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
        {{-- <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Ref/Account No*', 'ref_account_no') !!}
                {!! Html::text('ref_account_no')->class('form-control')
                ->placeholder('Enter Ref/Account No')
                ->required()
                !!}
            </div>
        </div> --}}


    </div>


    <div class="row">




        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Alternate Telephone', 'alternate_telephone') !!}
                {!! Html::text('alternate_telephone')->class('form-control')
                ->placeholder('Enter Alternate Telephone')

                !!}
            </div>
        </div>

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
                {!! Html::label('Gender *', 'Gender') !!}
                {!! Html::select('gender',
                $genders)->class('form-control')->placeholder('--Specify--')->required() !!}
            </div>
        </div>
    </div>





    <div class="row">
        <div class="col-md-12">
            <h6><strong><i class="fas fa-fw fa-map"></i> ADDRESS</strong></h6>
        </div>
    </div>


    <div class="row">

        <div class="col-md-12">
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
                {!! Html::label('Country', 'country') !!}
                {!!
                Html::select('country',
                $countries)->class('form-control select2')->placeholder('--Specify--')
                ->style("width:100%")
                !!}
            </div>

        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Town', 'town') !!}
                {!! Html::text('town')->class('form-control')
                ->placeholder('Enter town')
                !!}
            </div>

        </div>
    </div>

    <div class="row">

        <div class="col-md-12">
            <hr />
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h6><strong><i class="fas fa-fw fa-briefcase"></i> WORK</strong></h6>
        </div>
    </div>


    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Occupation', 'occupation') !!}
                {!! Html::text('occupation')->class('form-control')
                ->placeholder('Enter Occupation')
                !!}
            </div>

        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Company', 'company') !!}
                {!! Html::text('company')->class('form-control')
                ->placeholder('Enter Company')
                !!}
            </div>

        </div>
    </div>


    <div class="row">

        <div class="col-md-12">
            <hr />
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <h6><strong><i class="fas fa-fw fa-users"></i> NEXT OF KIN</strong></h6>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Next of Kin Name', 'kin_name') !!}
                {!! Html::text('kin_name')->class('form-control')
                ->placeholder('Enter Next Of Kin Name')
                !!}
            </div>

        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Telephone', 'kin_telephone') !!}
                {!! Html::text('kin_telephone')->class('form-control')
                ->placeholder('Enter Next of Kin Telephone')
                !!}
            </div>

        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Email', 'kin_email') !!}
                {!! Html::text('kin_email')->class('form-control')
                ->placeholder('Enter Next of Kin Email')
                !!}
            </div>

        </div>


        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Relation', 'kin_relation') !!}
                {!! Html::text('kin_relation')->class('form-control')
                ->placeholder('Enter Next of Kin Relation')
                !!}
            </div>

        </div>
    </div>





    <div class="entity_type defaulter_type" style="display: none">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Html::label('Business Name*', 'business_name') !!}
                    {!! Html::text('business_name')->class('form-control')
                    ->placeholder('Enter Business Name')
                    ->required()
                    !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <input type="hidden" class="entity_type_store" name="entity_type_store" value="">
            <button type="submit" class="btn btn-primary"><strong>CONTINUE</strong></button>
        </div>
    </div>

</form>