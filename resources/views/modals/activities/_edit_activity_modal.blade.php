<div class="modal fade" id="edit_debt_modal" tabindex="-1" aria-labelledby="editDebtModalLabel" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">

        <form action="{{ route('edit-activity',$leadDetails->id) }}" method="POST" class="activity_form" id="editActivityForm">
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
                                     <input class="form-check-input readonly-radio" type="radio" name="activityType_edit"
                                      id="activityType_edit{{ $activityType->id }}" value="{{ $activityType->id }}"
                                     {{ $activityType->id == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activityType_edit{{ $activityType->id }}">
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
                                {!! Html::label('Title/Subject*', 'activity_title_edit') !!}
                                {!! Html::text('activity_title_edit')->class('form-control')
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
                                {!! Html::label('Text/Description*', 'description_edit') !!}
                                {!! Html::textarea('description_edit')
                                ->class('form-control')
                                ->placeholder('Enter description')
                                ->rows(4) !!}
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-4 priority-group">
                            {!! Html::label('Priority*', 'priority_edit') !!}
                            {!! Html::select('priority_edit',
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
                                            name="start_date_edit" id="start_date" autocomplete="off">
                                        <input type="text" class="form-control timepicker" placeholder="Start Time"
                                            name="start_time_edit" id="start_time" autocomplete="off">
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
                                            name="end_date_edit" id="end_date" autocomplete="off">
                                        <input type="text" class="form-control timepicker" placeholder="Due/End Time"
                                            name="end_time_edit" id="end_time" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>


                    <div class="row">

                        <div class="col-md-4 department-group pt-3">
                            {!! Html::label('Assign Department', 'department_edit') !!}
                            {!! Html::select('department_edit', $departments)->class('form-control
                            ')->placeholder('--Specify--')->style("width:100%") !!}
                        </div>

                        <div class="col-md-4 agent-group pt-3">
                            {!! Html::label('Assign Agent', 'agent_edit') !!}
                            {!! Html::select('agent_edit', $agentsList)->class('form-control
                            ')->placeholder('--Specify--')->style("width:100%") !!}
                        </div>


                        <div class="col-md-4 status-group pt-3">
                            {!! Html::label('Status*', 'status_edit') !!}
                            {!! Html::select('status_edit',
                            $activityStatuses)->class('form-control')->placeholder('--Specify--')->style("width:100%")
                            !!}
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-12 pt-3">
                            <div class="icheck-primary">
                                <input type="checkbox" id="addToCalendar_edit" name="addToCalendar_edit">
                                <label for="addToCalendar_edit"
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

{{--
<script>
    document.addEventListener('DOMContentLoaded', function () {
      const editButtons = document.querySelectorAll('[data-bs-target="#edit_debt_modal"]');
    //   console.log('Edit Buttons Found:', editButtons);

      editButtons.forEach(button => {
          button.addEventListener('click', function () {
              // Extract data attributes from the clicked button
              const activityId = this.getAttribute('data-id');
              const activityTitleEdit = this.getAttribute('data-title');
              const descriptionEdit = this.getAttribute('data-description');
              const priorityEdit = this.getAttribute('data-priority');
              const activityType = this.getAttribute('data-activityType');
              const departmentEdit = this.getAttribute('data-department');
              const agentEdit = this.getAttribute('data-agent');
              const statusEdit = this.getAttribute('data-status');
              const startDate = this.getAttribute('data-startDate');
              const startTime = this.getAttribute('data-startTime');
              const endDate = this.getAttribute('data-endDate');
              const endTime = this.getAttribute('data-endTime');
              const calendarAdd = this.getAttribute('data-calendarAdd') ===1;

              // Populate the modal fields
              document.getElementById('editActivityForm').action = `{{ url('/activity/edit-activity/') }}/${activityId}`;
              document.getElementById('activity_id_edit').value = activityId;
              document.querySelector('[name="activity_title_edit"]').value = activityTitleEdit;
              document.querySelector('[name="description_edit"]').value = descriptionEdit;
              document.querySelector('[name="priority_edit"]').value = priorityEdit;
              document.querySelector('[name="department_edit"]').value = departmentEdit;
              document.querySelector('[name="agent_edit"]').value = agentEdit;
              document.querySelector('[name="status_edit"]').value = statusEdit;


              // Set the radio button for activity type
              const activityTypeInput = document.getElementById(`activityType_edit${activityType}`);
              if (activityTypeInput) {
                  activityTypeInput.checked = true;
              }

              // Handle Start Date & Time
              if (startDate && startDate.trim() !== '') {
                  document.getElementById('setStartDateEdit').checked = true;
                  document.getElementById('startDateInputsEdit').classList.remove('d-none');
                  document.getElementById('start_date').value = startDate;
                  document.getElementById('start_time').value = startTime;
              } else {
                  document.getElementById('setStartDateEdit').checked = false;
                  document.getElementById('startDateInputsEdit').classList.add('d-none');
              }

              // Handle End Date & Time
              if (endDate) {
                  document.getElementById('setDueDateEdit').checked = true;
                  document.getElementById('dueDateInputsEdit').classList.remove('d-none');
                  document.getElementById('end_date').value = endDate;
                  document.getElementById('end_time').value = endTime;
              } else {
                  document.getElementById('setDueDateEdit').checked = false;
                  document.getElementById('dueDateInputsEdit').classList.add('d-none');
              }

              // Handle Calendar Add Checkbox
              document.getElementById('addToCalendar_edit').checked = calendarAdd;
          });
      });
  });

      // Prevent change on all readonly-radio class inputs
      document.querySelectorAll('.readonly-radio').forEach(function(radio) {
          radio.addEventListener('click', function(e) {
              e.preventDefault();
          });
      });

</script> --}}

<script>
    // Add this script to your page to help diagnose and fix form validation issues
document.addEventListener('DOMContentLoaded', function() {
  // Debug form submission
  const editActivityForm = document.getElementById('editActivityForm');

  if (editActivityForm) {
    // Log all form values before submission
    editActivityForm.addEventListener('submit', function(e) {
      e.preventDefault(); // Prevent normal submission for debugging


      // Collect all form data
      const formData = new FormData(this);
      console.log('Form data being submitted:');

      // Check specifically for the required fields
      const requiredFields = {
        'activity_title_edit': 'Activity Title',
        'description_edit': 'Activity Text/Description',
        'priority_edit': 'Activity Priority',
        'status_edit': 'Activity Status'
      };

      let hasErrors = false;
      const errorMessages = [];

      // Check each required field
      for (const [fieldName, fieldLabel] of Object.entries(requiredFields)) {
        const value = formData.get(fieldName);
        console.log(`${fieldLabel} (${fieldName}): "${value}"`);

        if (!value || value.trim() === '') {
          hasErrors = true;
          errorMessages.push(`Please Enter ${fieldLabel}`);
        }
      }

      // Display all form data for debugging
      for (const [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
      }

      // Show error messages if any
      const errorDisp = document.querySelector('.errorDisp');
      if (hasErrors) {
        errorDisp.innerHTML = errorMessages.map(msg => `<p>${msg}</p>`).join('');
        errorDisp.style.display = 'block';
      } else {
        // If no errors, actually submit the form
        errorDisp.style.display = 'none';
        console.log('Form validation passed, submitting...');
        this.removeEventListener('submit', arguments.callee);
        this.submit();
      }
    });

    editButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Extract activity ID and set it in the form
      const activityId = this.getAttribute('data-id');
      console.log('Setting activity ID:', activityId);

      // Set the activity ID in the hidden field
      const idField = document.getElementById('activity_id_edit');
      if (idField) {
        idField.value = activityId;
      }

      // Update the form action URL
      const form = document.getElementById('editActivityForm');
      if (form) {
        form.action = `/activity/edit-activity/${activityId}`;
        console.log('Updated form action to:', form.action);
      }
    });
  });

    // Fix for select elements that might not be properly initialized
    document.querySelectorAll('select').forEach(select => {
      // Ensure the select elements have proper values
      if (select.value === '' && select.options.length > 1) {
        // Try to select the first non-placeholder option
        for (let i = 0; i < select.options.length; i++) {
          if (select.options[i].value !== '') {
            select.selectedIndex = i;
            break;
          }
        }
      }

      // Trigger change event to ensure any attached handlers run
      const event = new Event('change');
      select.dispatchEvent(event);
    });
  }
});
</script>

