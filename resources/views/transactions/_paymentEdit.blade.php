<form action="{{ route('transactions.update') }}" method="POST" class="user_form">
    @csrf



    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Enter Amount('.$leadDetails->currency_name.')*', 'amount') !!}
                {!! Html::text('amount')
                ->type('number') // Ensures only numbers are allowed
                ->class('form-control')
                ->placeholder('Enter The Debt Amount')
                ->attribute('step', '0.01') // Allows decimals (e.g., 10.50)
                ->attribute('min', '0') // Prevents negative values
                ->required()

                !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Transaction ID', 'transID') !!}
                {!! Html::text('transID')->class('form-control')
                ->placeholder('Enter Transaction ID')

                !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Payment Method*', 'payment_method') !!}
                {!!
                Html::select('payment_method', $paymentMethods)
                ->class('form-control custom-select')
                ->placeholder('--Specify--')
                ->style("width:100%")
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




    <div class="row">

        <div class="col-md-4">
            <div class="form-group">
                {!! Html::label('Status*', 'payment_status') !!}
                {!!
                Html::select('payment_status', $paymentStatuses)
                ->class('form-control custom-select')
                ->placeholder('--Specify--')
                ->style("width:100%")
                ->required()
                !!}
            </div>
        </div>

    </div>
    <input type="hidden" name="trans_type_select" class="trans_type_select">
    <input type="hidden" name="leadID" value="{{$leadDetails->id}}">
    <input type="hidden" name="transRecordId" class="transRecordId" >



    <button type="submit" class="btn btn-info">Submit Details</button>
</form>

