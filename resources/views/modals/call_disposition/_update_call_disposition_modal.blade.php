<div class="modal fade" id="update_call_disposition_modal" tabindex="-1"
    aria-labelledby="updateCallDispositionModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">

        <form action="{{ route('leads-store-call-disposition') }}" method="POST" class="user_form">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="updateCallDispositionModalLabel" class="card-title">New PTP</h5>
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
                        <div class="col-md-12 call-disposition-group pt-3 pb-3">
                            {!! Html::label('Call Disposition', 'call_disposition') !!}
                            {!! Html::select('call_disposition', $callDispositions)->class('form-control
                            select2')->id('call_disposition')->placeholder('--Select Call
                            Disposition--')->style("width:100%") !!}
                        </div>

                    </div>






                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Html::label('Description(optional)', 'call_deposition_description') !!}
                                {!! Html::textarea('call_deposition_description')
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