<div class="modal fade" id="new_ptp_modal" tabindex="-1" aria-labelledby="newPTPModalLabel" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">

        <form action="{{ route('leads-store-ptp') }}" method="POST" class="user_form">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="newPTPModalLabel" class="card-title">New PTP</h5>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Html::label('Enter Amount('.$leadDetails->currency_name.')*', 'ptp_amount') !!}
                                {!! Html::text('ptp_amount')
                                ->type('number') // Ensures only numbers are allowed
                                ->class('form-control')
                                ->placeholder('Enter The Debt Amount')
                                ->attribute('step', '0.01') // Allows decimals (e.g., 10.50)
                                ->attribute('min', '0') // Prevents negative values
                                ->required()

                                !!}
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                {!! Html::label('Payment Date', 'date') !!}
                                <input type="text" class="form-control date" placeholder="Payment Date" name="date"
                                    id="date" autocomplete="off">

                            </div>
                        </div>
                    </div>






                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Html::label('Description(optional)', 'ptp_description') !!}
                                {!! Html::textarea('ptp_description')
                                ->class('form-control')
                                ->placeholder('Enter description')
                                ->rows(3) !!}
                            </div>
                        </div>
                    </div>



                    {{-- <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Html::label('Status*', 'payment_status') !!}
                                {!!
                                Html::select('payment_status', $paymentStatuses)
                                ->class('form-control')
                                ->placeholder('--Specify--')
                                ->style("width:100%")
                                ->required()
                                !!}
                            </div>
                        </div>

                    </div> --}}



                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    --}}


                    <button type="submit" class="btn btn-info">Submit Details</button>
                </div>
            </div>

            <input type="hidden" name="leadID" value="{{$leadDetails->id}}">
        </form>
    </div>
</div>