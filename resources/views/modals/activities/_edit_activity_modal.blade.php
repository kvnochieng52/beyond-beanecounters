{{-- <div class="modal fade" id="edit_debt_modal" tabindex="-1" aria-labelledby="editDebtModalLabel" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">

        <form action="{{ route('edit-activity',$leadDetails->id) }}" method="POST" class="activity_form"
            id="editActivityForm">
            @csrf
            <input type="hidden" name="activity_id_edit" id="activity_id_edit">

            <input type="hidden" name="_method" value="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="editDebtModalLabel" class="card-title">Edit Activity</h5>
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
                                    <input class="form-check-input readonly-radio" type="radio" name="activityType"
                                        id="activityType{{ $activityType->id }}" value="{{ $activityType->id }}" {{
                                        $activityType->id == 1 ? 'checked' : '' }}>
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
                                    <input class="form-check-input" type="checkbox" id="setStartDateEdit">
                                    <label class="form-check-label" id="setStartDateLabel"
                                        for="setStartDateEdit"><strong>Set Start
                                            Date</strong></label>
                                </div>
                                <div id="startDateInputsEdit" class="d-none mt-2">
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
                                    <input class="form-check-input" type="checkbox" id="setDueDateEdit">
                                    <label class="form-check-label" for="setDueDateEdit"><strong>Due/End
                                            Date</strong></label>
                                </div>
                                <div id="dueDateInputsEdit" class="d-none mt-2">
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
                            ')->placeholder('--Specify--')->style("width:100%") !!}
                        </div>

                        <div class="col-md-4 agent-group pt-3">
                            {!! Html::label('Assign Agent', 'agent') !!}
                            {!! Html::select('agent', $agentsList)->class('form-control
                            ')->placeholder('--Specify--')->style("width:100%") !!}
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
                                <input type="checkbox" id="addToCalendar" name="addToCalendar">
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



                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">


                    <button type="submit" class="btn btn-info">Submit Details</button>
                </div>
            </div>

            <input type="hidden" name="leadID" value="{{$leadDetails->id}}">
        </form>
    </div>
</div> --}}

<script>
    //     document.addEventListener('DOMContentLoaded', function () {
//       const editButtons = document.querySelectorAll('[data-bs-target="#edit_debt_modal"]');
    

//       editButtons.forEach(button => {
//           button.addEventListener('click', function () {
//               // Extract data attributes from the clicked button
//               const activityId = this.getAttribute('data-id');
//               const activityTitle = this.getAttribute('data-title');
//               const description = this.getAttribute('data-description');
//               const priority = this.getAttribute('data-priority');
//               const activityType = this.getAttribute('data-activityType');
//               const department = this.getAttribute('data-department');
//               const agent = this.getAttribute('data-agent');
//               const status = this.getAttribute('data-status');
//               const startDate = this.getAttribute('data-startDate');
//               const startTime = this.getAttribute('data-startTime');
//               const endDate = this.getAttribute('data-endDate');
//               const endTime = this.getAttribute('data-endTime');
//               const calendarAdd = this.getAttribute('data-calendarAdd') ===1;

//               // Populate the modal fields
//               document.getElementById('editActivityForm').action = `{{ url('/activity/edit-activity/') }}/${activityId}`;
//               document.getElementById('activity_id').value = activityId;
//               document.querySelector('[name="activity_title"]').value = activityTitle;
//               document.querySelector('[name="description"]').value = description;
//               document.querySelector('[name="priority"]').value = priority;
//               document.querySelector('[name="department"]').value = department;
//               document.querySelector('[name="agent"]').value = agent;
//               document.querySelector('[name="status"]').value = status;

//               // Set the radio button for activity type
//               const activityTypeInput = document.getElementById(`activityType${activityType}`);
//               if (activityTypeInput) {
//                   activityTypeInput.checked = true;
//               }

//               // Handle Start Date & Time
//               if (startDate && startDate.trim() !== '') {
//                   document.getElementById('setStartDateEdit').checked = true;
//                   document.getElementById('startDateInputsEdit').classList.remove('d-none');
//                   document.getElementById('start_date').value = startDate;
//                   document.getElementById('start_time').value = startTime;
//               } else {
//                   document.getElementById('setStartDateEdit').checked = false;
//                   document.getElementById('startDateInputsEdit').classList.add('d-none');
//               }

//               // Handle End Date & Time
//               if (endDate) {
//                   document.getElementById('setDueDateEdit').checked = true;
//                   document.getElementById('dueDateInputsEdit').classList.remove('d-none');
//                   document.getElementById('end_date').value = endDate;
//                   document.getElementById('end_time').value = endTime;
//               } else {
//                   document.getElementById('setDueDateEdit').checked = false;
//                   document.getElementById('dueDateInputsEdit').classList.add('d-none');
//               }

//               // Handle Calendar Add Checkbox
//               document.getElementById('addToCalendar').checked = calendarAdd;
//           });
//       });
//   });

//       // Prevent change on all readonly-radio class inputs
//       document.querySelectorAll('.readonly-radio').forEach(function(radio) {
//           radio.addEventListener('click', function(e) {
//               e.preventDefault();
//           });
//       });

</script>