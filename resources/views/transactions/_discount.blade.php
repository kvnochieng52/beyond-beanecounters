<form action="{{ route('transactions.store') }}" method="POST" class="user_form">
    @csrf



    <div class="row">


        <div class="col-md-4">
            <label for="charge_type" class="form-label">Charge Type</label>
            <select class="form-control custom-select" name="charge_type" required>
                <option value="">--Specify--</option>
                <option value="Fixed amount">Fixed amount</option>
                <option value="Percentage">Percentage</option>
            </select>
        </div>





        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Enter Value*', 'value') !!}
                {!! Html::text('value')
                ->type('number') // Ensures only numbers are allowed
                ->class('form-control')
                ->placeholder('Enter The value')
                // ->attribute('step', '0.01') // Allows decimals (e.g., 10.50)
                // ->attribute('min', '0') // Prevents negative values
                ->required()

                !!}
            </div>
        </div>




    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {!! Html::label('Description(optional)', 'description') !!}
                {!! Html::textarea('description')
                ->class('form-control')
                ->placeholder('Enter description')
                ->rows(3) !!}
            </div>
        </div>
    </div>

    <input type="hidden" name="trans_type_select" class="trans_type_select">
    <input type="hidden" name="leadID" value="{{$leadDetails->id}}">

    <button type="submit" class="btn btn-info">Submit Details</button>
</form>