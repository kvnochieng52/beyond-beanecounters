@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    {{-- <h1>Dashboard</h1> --}}
@stop

@section('content')


    <div class="card">
        <div class="card-header">
            <h4>Activity Report</h4>
        </div>


        <div class="card-body">
            <form action="{{ route('activity.reports.generate') }}" method="POST">
                @csrf


                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Html::label('From Date (Activity Creation From Date)*', 'from_date') !!}
                            <input type="text" class="form-control date" placeholder="Payment Date" name="from_date"
                                id="from_date" autocomplete="off" required>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Html::label('To Date (Activity Creation To Date)*', 'to_date') !!}
                            <input type="text" class="form-control date" placeholder="Payment Date" name="to_date"
                                id="to_date" autocomplete="off" required>

                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-12">


                        <div class="form-group">
                            {!! Html::label('Ticket No(Comma Separated)', 'ticket_no') !!}
                            <input type="text" class="form-control" placeholder="ticket_no" name="ticket_no"
                                id="ticket_no" autocomplete="off">

                        </div>
                    </div>
                </div>


                <div class="row">

                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-group">
                                <label for="activity_type">Select Activity Type (Leave Blank for All)</label>
                                <select multiple class="form-control select2 @error('activity_type') is-invalid @enderror"
                                    id="activity_type" name="activity_type">
                                    <option value="">-- Select Agent (Leave Blank for All) --</option>
                                    @foreach ($actvityTypes as $key => $activity_type)
                                        <option value="{{ $key }}">
                                            {{ $activity_type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('activity_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-group">
                                <label for="agent">Select Agent(Leave Blank for All)</label>
                                <select multiple class="form-control select2 @error('agent') is-invalid @enderror"
                                    id="agent" name="agent">
                                    <option value="">-- Select Agent (Leave Blank for All) --</option>
                                    @foreach ($agentsList as $key => $agent)
                                        <option value="{{ $key }}">
                                            {{ $agent }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('agent')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-12">

                        <div class="form-group">
                            <div class="form-group">
                                <label for="institution">Select Institution(Leave Blank for All)</label>
                                <select multiple class="form-control select2 @error('institution') is-invalid @enderror"
                                    id="institution" name="institution">
                                    <option value="">-- Select Agent (Leave Blank for All) --</option>
                                    @foreach ($institutions as $key => $institution)
                                        <option value="{{ $key }}">
                                            {{ $institution }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('institution')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">

                    <div class="col-md-12">

                        <div class="form-group">
                            <div class="form-group">
                                <label for="institution">Select Call Dispositions(leave blank for all)</label>
                                <select multiple class="form-control select2 @error('disposition') is-invalid @enderror"
                                    id="disposition" name="disposition">
                                    <option value="">-- Select Agent (Leave Blank for All) --</option>
                                    @foreach ($call_dispositions as $key => $disposition)
                                        <option value="{{ $key }}">
                                            {{ $disposition }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('disposition')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                {{-- 
                <h5>PTP Created Date</h5>
                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">
                            {!! Html::label('PTP from Date', 'ptp_created_from_date') !!}
                            <input type="text" class="form-control date" placeholder="PTP Due Date"
                                name="ptp_created_from_date" id="ptp_created_from_date" autocomplete="off">

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Html::label('PTP to Date', 'ptp_created_to_date') !!}
                            <input type="text" class="form-control date" placeholder="PTP Due Date"
                                name="ptp_created_to_date" id="ptp_created_to_date" autocomplete="off">

                        </div>
                    </div>
                </div> --}}


                <h5>PTP Due Date</h5>
                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">
                            {!! Html::label('PTP Due From Date', 'ptp_due_from_date') !!}
                            <input type="text" class="form-control date" placeholder="PTP From Due Date"
                                name="ptp_due_from_date" id="ptp_due_from_date" autocomplete="off">

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Html::label('PTP Due To  Date', 'ptp_due_to_date') !!}
                            <input type="text" class="form-control date" placeholder="PTP Due Date To Date"
                                name="ptp_due_to_date" id="ptp_due_to_date" autocomplete="off">

                        </div>
                    </div>
                </div>


                <div class="row">

                    <div class="col-md-12">

                        <input type="submit" class="btn btn-info" value="Submit">
                    </div>
                </div>

            </form>
        </div>

    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/datepicker/datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/datepicker/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.date').datepicker({
                autoclose: true,
                format: 'dd-mm-yyyy',
                todayHighlight: true,
                orientation: "bottom auto"
            });


            $('.select2').select2({
                width: '100%'
            });
        });
    </script>
@stop
