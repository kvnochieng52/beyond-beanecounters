<div class="modal fade" id="new_transaction_modal" tabindex="-1" aria-labelledby="newtransactionModalLabel"
    aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">


        <div class="modal-content">
            <div class="modal-header">
                <h5 id="newtransactionModalLabel" class="card-title">New transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">



                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Html::label('Name', 'name') !!}
                            {!! Html::text('name')->class('form-control')
                            ->placeholder('Enter First Name')
                            ->value($leadDetails->title)
                            ->attribute('readonly', 'readonly')
                            !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Html::label('telephone', 'telephone') !!}
                            {!! Html::text('telephone')->class('form-control')
                            ->placeholder('Enter First telephone')
                            ->value($leadDetails->telephone)
                            ->attribute('readonly', 'readonly')
                            !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Html::label('email', 'email') !!}
                            {!! Html::text('email')->class('form-control')
                            ->placeholder('Enter First email')
                            ->value($leadDetails->email)
                            ->attribute('readonly', 'readonly')
                            !!}
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Html::label('Amount', 'leadAmount') !!}
                            {!! Html::text('leadAmount')->class('form-control')
                            ->placeholder('Lead Amount')
                            ->value($leadDetails->currency_name." ". number_format($leadDetails->amount,0))
                            ->attribute('readonly', 'readonly')
                            !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Html::label('Additional Charges', 'additionalCharges') !!}
                            {!! Html::text('additionalCharges')->class('form-control')
                            ->placeholder('Additional Charges')
                            ->value($leadDetails->currency_name.
                            " ".number_format($leadDetails->additional_charges,0))
                            ->attribute('readonly', 'readonly')
                            !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Html::label('Balance', 'leadBalance') !!}
                            {!! Html::text('leadBalance')->class('form-control')
                            ->placeholder('Lead Balance')
                            ->value($leadDetails->currency_name." ".number_format($leadDetails->balance,0))
                            ->attribute('readonly', 'readonly')
                            !!}
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Html::label('Transaction Types*', 'transactionTypes') !!}
                            {!!
                            Html::select('transactionTypes', $transactionTypes)
                            ->class('form-control custom-select')
                            ->placeholder('--Specify--')
                            ->style("width:100%")
                            ->required()
                            !!}
                        </div>
                    </div>

                </div>

                <div class="_payment trans_options">
                    @include('transactions._payment')
                </div>

                <div class="_penalty trans_options">
                    @include('transactions._penalty')
                </div>

                <div class="_discount trans_options">
                    @include('transactions._discount')
                </div>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                --}}



            </div>
        </div>


    </div>
</div>