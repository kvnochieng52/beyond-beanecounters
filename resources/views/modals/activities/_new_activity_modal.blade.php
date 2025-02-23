<div class="modal fade" id="new_debt_modal" tabindex="-1" aria-labelledby="newDebtModalLabel" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">

        <form action="{{ route('store-activity') }}" method="POST" class="activity_form">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="newDebtModalLabel" class="card-title">New Activity</h5>
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
                        <div class="col-md-12">
                            @foreach ($activityTypes as $activityType)
                            <div class="form-check form-check-inline">
                                <span class="btn btn-default btn-sm">
                                    <input class="form-check-input" type="radio" name="activityType"
                                        id="activityType{{ $activityType->id }}" value="{{ $activityType->id }}" {{
                                        $activityType->id == 1 ?
                                    'checked' : '' }}>
                                    <label class="form-check-label" for="activityType{{ $activityType->id }}">
                                        <i class="fa fa-fw fa-{{ $activityType->icon }}"></i>
                                        {{ $activityType->activity_type_title }}
                                    </label>
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12 pt-3">
                            <div class="form-group">
                                {!! Html::label('Title/Subject*', 'activity_title') !!}
                                {!! Html::text('activity_title')->class('form-control')
                                ->placeholder('Enter the Title/Subject')
                                // ->value($leadDetails->title)
                                // ->attribute('readonly', 'readonly')
                                !!}
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Html::label('Text/Description*', 'description') !!}
                                {!! Html::textarea('description')
                                ->class('form-control')
                                ->placeholder('Enter description')
                                ->rows(4) !!}
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-4 priority-group">
                            {!! Html::label('Priority*', 'priority') !!}
                            {!! Html::select('priority',
                            $priorities)->class('form-control')->placeholder('--Specify--')->style("width:100%") !!}
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="setStartDate">
                                    <label class="form-check-label" id="setStartDateLabel"
                                        for="setStartDate"><strong>Set Start
                                            Date</strong></label>
                                </div>
                                <div id="startDateInputs" class="d-none mt-2">
                                    <div class="d-flex gap-2">
                                        <input type="text" class="form-control date" placeholder="Start Date"
                                            name="start_date" id="start_date" autocomplete="off">
                                        <input type="text" class="form-control timepicker" placeholder="Start Time"
                                            name="start_time" id="start_time" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 due-date-group">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="setDueDate">
                                    <label class="form-check-label" for="setDueDate"><strong>Due/End
                                            Date</strong></label>
                                </div>
                                <div id="dueDateInputs" class="d-none mt-2">
                                    <div class="d-flex gap-2">
                                        <input type="text" class="form-control date" placeholder="Due/End Date"
                                            name="end_date" id="end_date" autocomplete="off">
                                        <input type="text" class="form-control timepicker" placeholder="Due/End Time"
                                            name="end_time" id="end_time" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>


                    <div class="row">

                        <div class="col-md-4 department-group pt-3">
                            {!! Html::label('Assign Department', 'department') !!}
                            {!! Html::select('department', $departments)->class('form-control
                            select2')->placeholder('--Specify--')->style("width:100%") !!}
                        </div>

                        <div class="col-md-4 agent-group pt-3">
                            {!! Html::label('Assign Agent', 'agent') !!}
                            {!! Html::select('agent', $agentsList)->class('form-control
                            select2')->placeholder('--Specify--')->style("width:100%") !!}
                        </div>


                        <div class="col-md-4 status-group pt-3">
                            {!! Html::label('Status*', 'status') !!}
                            {!! Html::select('status',
                            $activityStatuses)->class('form-control')->placeholder('--Specify--')->style("width:100%")
                            !!}
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-12 pt-3">
                            <div class="icheck-primary">
                                <input type="checkbox" id="addToCalendar" name="addToCalendar" value="1">
                                <label for="addToCalendar"
                                    title="Activity will be added to calendar with the set start and end date above">
                                    Add this activity to my calendar
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger alert-dismissible errorDisp" style="display: none ">

                                {{-- <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true">×</button> --}}
                                sdsd
                            </div>
                        </div>
                    </div>

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