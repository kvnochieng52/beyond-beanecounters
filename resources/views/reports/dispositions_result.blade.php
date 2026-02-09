@extends('adminlte::page')

@section('title', 'Dispositions Report Results')

@section('content_header')
    <h1>Dispositions Report Results</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Report for {{ \Carbon\Carbon::parse($data['start_date'])->format('M d, Y') }} to {{ \Carbon\Carbon::parse($data['end_date'])->format('M d, Y') }}</h3>
            <div class="card-tools">
                <a href="{{ route('reports.dispositions') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Filter
                </a>
                <form action="{{ route('reports.dispositions.generate') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="start_date" value="{{ $data['start_date'] }}">
                    <input type="hidden" name="end_date" value="{{ $data['end_date'] }}">
                    @if($data['filters']['agent_id'])
                        <input type="hidden" name="agent_id" value="{{ $data['filters']['agent_id'] }}">
                    @endif
                    @if($data['filters']['institution_id'])
                        <input type="hidden" name="institution_id" value="{{ $data['filters']['institution_id'] }}">
                    @endif
                    <input type="hidden" name="export" value="excel">
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            @if($data['filters']['agent_id'])
                <div class="alert alert-info">
                    <strong>Filter Applied:</strong> Agent: 
                    @php
                        $agent = \App\Models\User::find($data['filters']['agent_id']);
                        echo $agent ? $agent->name . ' (' . $agent->agent_code . ')' : 'Unknown';
                    @endphp
                </div>
            @endif
            
            @if($data['filters']['institution_id'])
                <div class="alert alert-info">
                    <strong>Filter Applied:</strong> Institution: 
                    @php
                        $institution = \App\Models\Institution::find($data['filters']['institution_id']);
                        echo $institution ? $institution->institution_name : 'Unknown';
                    @endphp
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 250px;">
                                <strong>Disposition</strong>
                            </th>
                            @foreach($data['institutions'] as $institution)
                                <th class="text-center">
                                    <small>{{ $institution->institution_name }}</small>
                                </th>
                            @endforeach
                            <th class="text-center bg-secondary text-white">
                                <strong>Total</strong>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['report_data'] as $row)
                            <tr>
                                <td>
                                    <strong>{{ $row['disposition_name'] }}</strong>
                                </td>
                                @foreach($data['institutions'] as $institution)
                                    <td class="text-center">
                                        @php
                                            $count = $row[$institution->id] ?? 0;
                                            $badgeClass = $count > 0 ? 'badge-primary' : 'badge-light';
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $count }}</span>
                                    </td>
                                @endforeach
                                <td class="text-center bg-light">
                                    <strong>{{ $row['total'] }}</strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($data['institutions']) + 2 }}" class="text-center text-muted">
                                    No data available for the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th class="text-center">
                                <strong>Total Leads</strong>
                            </th>
                            @foreach($data['institutions'] as $institution)
                                <th class="text-center">
                                    @php
                                        $institutionTotal = collect($data['report_data'])->sum(function($row) use ($institution) {
                                            return $row[$institution->id] ?? 0;
                                        });
                                    @endphp
                                    <strong>{{ $institutionTotal }}</strong>
                                </th>
                            @endforeach
                            <th class="text-center bg-secondary text-white">
                                <strong>
                                    @php
                                        $grandTotal = collect($data['report_data'])->sum('total');
                                        echo $grandTotal;
                                    @endphp
                                </strong>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add some basic styling for better visualization
            const table = document.querySelector('table');
            if (table) {
                // Make table scrollable on mobile
                table.classList.add('table-sm');
            }
        });
    </script>
@stop
