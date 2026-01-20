@extends('adminlte::page')

@section('title', 'Schedule PTP Reminder')

@section('content_header')
{{-- <h1>Schedule PTP Reminder</h1> --}}
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Schedule PTP Reminder</h3>
                    <div class="card-tools">
                        <a href="{{ route('reminder-processes.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('reminder-processes.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="process_date">Process Date</label>
                            <input type="date" id="process_date" name="process_date" class="form-control" 
                                   value="{{ old('process_date', \Carbon\Carbon::today()->toDateString()) }}" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-clock"></i> Schedule Reminder Job
                            </button>
                            
                            <button type="submit" formaction="{{ route('reminder-processes.run-now') }}" 
                                    class="btn btn-warning ml-2" onclick="return confirm('Run the reminder job immediately?')">
                                <i class="fas fa-play"></i> Run Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
