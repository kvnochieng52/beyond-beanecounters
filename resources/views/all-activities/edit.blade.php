@extends('adminlte::page')

@section('title', 'Activity')

@section('content_header')
@stop

@section('content')

<form action="{{ route('update-activity',$editActivity->id) }}" method="POST" class="activity_form" id="editActivityForm">
    @csrf
    <input type="hidden" name="activity_id" id="activity_id">
    <input type="hidden" name="_method" value="POST">
    <div class="card">
        <div class="card-header">
            <h5 id="editDebtModalLabel" class="card-title">Edit Activity</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('Name', 'name') !!}
                        {!! Html::text('name')
                            ->class('form-control')
                            ->placeholder('Enter First Name')
                            ->attribute('readonly', 'readonly')
                            ->value($editActivity->assigned_agent_name)
                        !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('Telephone', 'telephone') !!}
                        {!! Html::text('telephone')
                            ->class('form-control')
                            ->placeholder('Enter Telephone')
                            ->attribute('readonly', 'readonly')
                            ->value($editActivity->assigned_agent_telephone)
                        !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Html::label('Email', 'email') !!}
                        {!! Html::text('email')
                            ->class('form-control')
                            ->placeholder('Enter Email')
                            ->attribute('readonly', 'readonly')
                            ->value( $editActivity->assigned_agent_email)
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
                              id="activityType{{ $activityType->id }}" value="{{ $activityType->id }}"
                             {{ $activityType->id == 1 ? 'checked' : '' }}>
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
                        {!! Html::text('activity_title')
                            ->class('form-control')
                            ->placeholder('Enter the Title/Subject')
                            ->value( $editActivity->activity_title)
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
                            ->rows(4)
                            ->value( $editActivity->description)
                        !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 priority-group">
                    <label for="priority">Priority*</label>
                    <select name="priority" class="form-control">
                        @foreach ($priorities as $id => $name)
                            <option value="{{ $id }}" {{ $id == $editActivity->priority_id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <div id="startDateInputsEdit" class="d-flex gap-2 ">
                            <input type="date" id="start_date" name="start_date" class="form-control datepicker"
       value="{{ $editActivity->start_date_time ? date('Y-m-d', strtotime($editActivity->start_date_time)) : '' }}"
       data-original="{{ $editActivity->start_date_time ? date('d-m-Y', strtotime($editActivity->start_date_time)) : '' }}">





                            <select name="start_time" class="form-control">
                                @foreach(range(0, 23) as $hour)
                                    @foreach(['00', '30'] as $minute)
                                        @php
                                            $time = sprintf("%02d:%s %s", ($hour % 12 == 0 ? 12 : $hour % 12), $minute, $hour < 12 ? 'AM' : 'PM');

                                            $selected = $editActivity->start_date_time && date('h:i A', strtotime($editActivity->start_date_time)) == $time;
                                        @endphp
                                        <option value="{{ $time }}" {{ $selected ? 'selected' : '' }}>{{ $time }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="end_date">Due/End Date</label>
                        <div id="dueDateInputsEdit" class="d-flex gap-2">
                            <input type="date" id="end_date" name="end_date" class="form-control datepicker"
       value="{{ $editActivity->due_date_time ? date('Y-m-d', strtotime($editActivity->due_date_time)) : '' }}"
       data-original="{{ $editActivity->due_date_time ? date('d-m-Y', strtotime($editActivity->due_date_time)) : '' }}">

                            <select name="end_time" class="form-control">
                                @foreach(range(0, 23) as $hour)
                                    @foreach(['00', '30'] as $minute)
                                        @php
                                            $time = sprintf("%02d:%s %s", ($hour % 12 == 0 ? 12 : $hour % 12), $minute, $hour < 12 ? 'AM' : 'PM');

                                            $selected = $editActivity->due_date_time && date('h:i A', strtotime($editActivity->due_date_time)) == $time;
                                        @endphp
                                        <option value="{{ $time }}" {{ $selected ? 'selected' : '' }}>{{ $time }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

        </div>


            <div class="row">
                <div class="col-md-12 pt-3">
                    <div class="check-primary">
                        <input type="checkbox" name="addToCalendar" id="addToCalendar" {{ $editActivity->calendar_add ? 'checked' : '' }}>
                        <label for="addToCalendar"
                            title="Activity will be added to calendar with the set start and end date above">
                            Add this activity to my calendar
                        </label>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible errorDisp" style="display: none;">
                        <!-- Error messages will be displayed here -->
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-info">Submit Details</button>
        </div>
    </div>

    {{-- <input type="hidden" name="leadID" value="{{$leadDetails->id}}"> --}}
    <input type="hidden" name="activity_id" id="activity_id">

</form>
@stop

@section('css')
@stop

@section('js')
<script>
      // Prevent change on all readonly-radio class inputs
      document.querySelectorAll('.readonly-radio').forEach(function(radio) {
          radio.addEventListener('click', function(e) {
              e.preventDefault();
          });
      });

</script>
@stop
