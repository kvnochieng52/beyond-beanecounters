<div class="modal fade" id="update_status_modal" tabindex="-1" aria-labelledby="updateStatusModalLabel"
    aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">

        <form action="{{ route('leads-update-status') }}" method="POST" class="user_form">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="updateStatusModalLabel" class="card-title">Update Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Html::label('Name', 'name') !!}
                                {!! Html::text('name')->class('form-control')
                                ->placeholder('Enter First Name')
                                ->value($leadDetails->title)
                                ->attribute('readonly', 'readonly')
                                !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Html::label('telephone', 'telephone') !!}
                                {!! Html::text('telephone')->class('form-control')
                                ->placeholder('Enter First telephone')
                                ->value($leadDetails->telephone)
                                ->attribute('readonly', 'readonly')
                                !!}
                            </div>
                        </div>


                    </div>




                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Html::label('What is the lead Stage?*', 'lead_stage') !!}
                                {!!
                                Html::select('lead_stage',
                                $leadStages)->class('form-control select2')->placeholder('--Specify--')
                                ->style("width:100%")
                                ->value($leadDetails->stage_id)
                                ->required()
                                !!}
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Html::label('What is the lead Status?*', 'lead_status') !!}
                                {!!
                                Html::select('lead_status',
                                $leadStatuses)->class('form-control select2')->placeholder('--Specify--')
                                ->style("width:100%")
                                ->value($leadDetails->status_id)
                                ->required()
                                !!}
                            </div>
                        </div>

                    </div>


                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Html::label('How Likely is the lead Conversion?*', 'lead_conversion') !!}
                                {!!
                                Html::select('lead_conversion',
                                $leadConversionLevels)->class('form-control select2')->placeholder('--Specify--')
                                ->style("width:100%")
                                ->value($leadDetails->conversion_status_id)
                                ->required()
                                !!}
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Html::label('What is the lead Status?*', 'lead_engagement') !!}
                                {!!
                                Html::select('lead_engagement',
                                $leadEngagementLevels)->class('form-control select2')->placeholder('--Specify--')
                                ->style("width:100%")
                                ->value($leadDetails->engagement_level_id)
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


                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    --}}


                    <button type="submit" class="btn btn-info">Update</button>
                </div>
            </div>

            <input type="hidden" name="leadID" value="{{$leadDetails->id}}">
        </form>
    </div>
</div>