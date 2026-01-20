@extends('adminlte::page')

@section('title', 'PTP Reminder Process Details')

@section('content_header')
{{-- <h1>PTP Reminder Process Details</h1> --}}
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">PTP Reminder Process Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('reminder-processes.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th><strong>Process Date:</strong></th>
                                    <td>{{ $process->process_date }}</td>
                                </tr>
                                <tr>
                                    <th><strong>Type:</strong></th>
                                    <td>{{ ucfirst(str_replace('_', ' ', $process->process_type)) }}</td>
                                </tr>
                                <tr>
                                    <th><strong>Status:</strong></th>
                                    <td>
                                        <span class="badge badge-{{ $process->status == 'completed' ? 'success' : ($process->status == 'failed' ? 'danger' : ($process->status == 'running' ? 'warning' : 'secondary')) }}">
                                            {{ ucfirst($process->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th><strong>Start Time:</strong></th>
                                    <td>{{ $process->start_time ? $process->start_time->format('Y-m-d H:i:s') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th><strong>End Time:</strong></th>
                                    <td>{{ $process->end_time ? $process->end_time->format('Y-m-d H:i:s') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th><strong>Total Customers:</strong></th>
                                    <td>{{ $process->total_customers }}</td>
                                </tr>
                                <tr>
                                    <th><strong>Successful Reminders:</strong></th>
                                    <td class="text-success">{{ $process->successful_reminders }}</td>
                                </tr>
                                <tr>
                                    <th><strong>Failed Reminders:</strong></th>
                                    <td class="text-danger">{{ $process->failed_reminders }}</td>
                                </tr>
                                <tr>
                                    <th><strong>Success Rate:</strong></th>
                                    <td>
                                        @if($process->total_customers > 0)
                                            {{ round(($process->successful_reminders / $process->total_customers) * 100, 2) }}%
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($process->error_message)
                        <div class="alert alert-danger">
                            <strong>Error Message:</strong> {{ $process->error_message }}
                        </div>
                    @endif

                    @if($process->processed_customers && count($process->processed_customers) > 0)
                        <h5>Processed Customers</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Lead ID</th>
                                        <th>Activity ID</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Message</th>
                                        <th>Error</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($process->processed_customers as $customer)
                                        <tr>
                                            <td>{{ $customer['lead_id'] ?? '-' }}</td>
                                            <td>{{ $customer['activity_id'] ?? '-' }}</td>
                                            <td>{{ $customer['phone'] ?? '-' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $customer['status'] == 'success' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($customer['status']) }}
                                                </span>
                                            </td>
                                            <td class="text-truncate" style="max-width: 200px;" title="{{ $customer['message'] ?? '' }}">
                                                {{ $customer['message'] ?? '-' }}
                                            </td>
                                            <td class="text-danger">{{ $customer['error'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
