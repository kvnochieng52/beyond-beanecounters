<form action="{{ route('lead.update', $lead->id) }}" method="POST" class="user_form">
    @csrf
    @method('PUT')

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
                ->value($lead->title)
                ->required()
                !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('ID Number*', 'id_number') !!}
                {!! Html::text('id_number')->class('form-control')
                ->placeholder('Enter ID Number')
                ->value($lead->id_passport_number)
                ->required()
                !!}
            </div>
        </div>


        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Telephone*', 'telephone') !!}
                {!! Html::text('telephone')->class('form-control')
                ->placeholder('Enter Telephone')
                ->value($lead->telephone)
                ->required()
                !!}
            </div>
        </div>


    </div>


    <div class="row">




        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Alternate Telephone', 'alternate_telephone') !!}
                {!!
                Html::text('alternate_telephone')
                ->class('form-control')
                ->placeholder('Enter Alternate Telephone')
                ->value($lead->alternate_telephone)

                !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Email*', 'email') !!}
                {!! Html::email('email')->class('form-control')
                ->placeholder('Enter Email')
                ->value($lead->email)
                ->required()
                !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Gender *', 'Gender') !!}
                {!! Html::select('gender',$genders)
                ->class('form-control')
                ->placeholder('--Specify--')
                ->value($lead->gender_id)
                ->required()

                !!}
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
                {!!
                Html::textarea('address')
                ->class('form-control')
                ->placeholder('Enter Address')
                ->value($lead->address)
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
                $countries)
                ->class('form-control select2')
                ->placeholder('--Specify--')
                ->style("width:100%")
                ->value($lead->country_id)
                !!}
            </div>

        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Town', 'town') !!}
                {!!
                Html::text('town')
                ->class('form-control')
                ->placeholder('Enter town')
                ->value($lead->town)
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
                {!!
                Html::text('occupation')
                ->class('form-control')
                ->placeholder('Enter Occupation')
                ->value($lead->occupation)
                !!}
            </div>

        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Company', 'company') !!}
                {!! Html::text('company')->class('form-control')
                ->placeholder('Enter Company')
                ->value($lead->company_name)
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
                ->value($lead->kin_full_names)
                !!}
            </div>

        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Telephone', 'kin_telephone') !!}
                {!! Html::text('kin_telephone')->class('form-control')
                ->placeholder('Enter Next of Kin Telephone')
                ->value($lead->kin_telephone)
                !!}
            </div>

        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Email', 'kin_email') !!}
                {!! Html::text('kin_email')->class('form-control')
                ->placeholder('Enter Next of Kin Email')
                ->value($lead->kin_email)
                !!}
            </div>

        </div>


        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Relation', 'kin_relation') !!}
                {!! Html::text('kin_relation')->class('form-control')
                ->placeholder('Enter Next of Kin Relation')
                ->value($lead->kin_relationship)
                !!}
            </div>

        </div>
    </div>





    <div class="row">
        <div class="col-md-12">
            <input type="hidden" name="step" value="1">
            <button type="submit" class="btn btn-primary"><strong>SUBMIT</strong></button>
        </div>



    </div>

</form>