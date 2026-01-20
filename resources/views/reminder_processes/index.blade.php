@extends('adminlte::page')

@section('title', 'PTP Reminder Processes')

@section('content_header')
{{-- <h1>PTP Reminder Processes</h1> --}}
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">PTP Reminder Processes</h3>
                    <div class="card-tools">
                        <a href="{{ route('reminder-processes.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Schedule Reminder
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Today's Reminders Section -->
                    <div class="card card-outline card-info mb-3">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-calendar-day"></i> Today's PTP Reminders 
                                <small class="text-muted">({{ \Carbon\Carbon::today()->toDateString() }})</small>
                            </h5>
                            <div class="card-tools">
                                @if($todayProcess)
                                    @if($todayProcess->status == 'completed')
                                        <span class="badge badge-success">Completed</span>
                                    @elseif($todayProcess->status == 'running')
                                        <span class="badge badge-warning">Running</span>
                                    @elseif($todayProcess->status == 'failed')
                                        <span class="badge badge-danger">Failed</span>
                                    @else
                                        <span class="badge badge-secondary">Pending</span>
                                    @endif
                                @else
                                    <form action="{{ route('reminder-processes.run-today') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Run today\'s PTP reminders now?')">
                                            <i class="fas fa-play"></i> Run Now
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if($todayProcess)
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Customers</span>
                                                <span class="info-box-number">{{ $todayProcess->total_customers }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Successful</span>
                                                <span class="info-box-number">{{ $todayProcess->successful_reminders }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Failed</span>
                                                <span class="info-box-number">{{ $todayProcess->failed_reminders }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-light"><i class="fas fa-percentage"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Success Rate</span>
                                                <span class="info-box-number">
                                                    @if($todayProcess->total_customers > 0)
                                                        {{ round(($todayProcess->successful_reminders / $todayProcess->total_customers) * 100, 1) }}%
                                                    @else
                                                        0%
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('reminder-processes.show', $todayProcess->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    @if($todayProcess->status == 'failed')
                                        <form action="{{ route('reminder-processes.run-today') }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Retry today\'s PTP reminders?')">
                                                <i class="fas fa-redo"></i> Retry
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    No PTP reminders have been run for today. Click "Run Now" to send reminders for today's due dates.
                                    <br><small>
                                        <strong>Note:</strong> Daily reminders are automatically scheduled to run at 8:00 AM every day.
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- All Processes Table -->
                    <h5>All Reminder Processes</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Total Customers</th>
                                <th>Successful</th>
                                <th>Failed</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($processes as $process)
                                <tr>
                                    <td>{{ $process->process_date }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $process->process_type)) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $process->status == 'completed' ? 'success' : ($process->status == 'failed' ? 'danger' : ($process->status == 'running' ? 'warning' : 'secondary')) }}">
                                            {{ ucfirst($process->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $process->total_customers }}</td>
                                    <td>{{ $process->successful_reminders }}</td>
                                    <td>{{ $process->failed_reminders }}</td>
                                    <td>{{ $process->start_time ? $process->start_time->format('H:i:s') : '-' }}</td>
                                    <td>{{ $process->end_time ? $process->end_time->format('H:i:s') : '-' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('reminder-processes.show', $process->id) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($process->status == 'failed')
                                                <form action="{{ route('reminder-processes.run-now') }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="process_date" value="{{ $process->process_date }}">
                                                    <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Run this reminder job now?')">
                                                        <i class="fas fa-redo"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No reminder processes found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $processes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
