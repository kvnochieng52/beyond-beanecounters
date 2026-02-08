@extends('adminlte::page')

@section('title', 'Admin Agent Performance Report Results')

@section('content_header')
    <div class="row mb-3">
        <div class="col-md-8">
            <h1>Admin Agent Performance Report Results</h1>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('reports.admin-agent-performance') }}" class="btn btn-info">
                <i class="fas fa-filter"></i> Modify Filters
            </a>
            <form action="{{ route('reports.admin-agent-performance.generate') }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="date_from" value="{{ $data['filters']['date_from'] }}">
                <input type="hidden" name="date_to" value="{{ $data['filters']['date_to'] }}">
                @if($data['filters']['institution_id'])
                    <input type="hidden" name="institution_id" value="{{ $data['filters']['institution_id'] }}">
                @endif
                @if($data['filters']['agent_id'])
                    <input type="hidden" name="agent_id" value="{{ $data['filters']['agent_id'] }}">
                @endif
                @if($data['filters']['created_by_agent'])
                    <input type="hidden" name="created_by_agent" value="{{ $data['filters']['created_by_agent'] }}">
                @endif
                <button type="submit" name="export" value="excel" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </button>
            </form>
        </div>
    </div>
@stop

@section('content')
    <!-- Filter Summary -->
    <div class="card card-info">
        <div class="card-body">
            <h5 class="card-title">Report Period: <strong>{{ $data['filters']['date_from'] }} to
                    {{ $data['filters']['date_to'] }}</strong></h5>
        </div>
    </div>

    <!-- Report Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Agent Performance Metrics</h3>
        </div>
        <div class="card-body" style="overflow-x: auto;">
            <table class="table table-bordered table-striped table-sm">
                <thead>
                    <tr style="background-color: #1f4e78; color: white;">
                        <th rowspan="2" style="vertical-align: middle; padding: 10px;">Agent Name</th>
                        <th colspan="2" style="text-align: center; padding: 10px;">Lead Metrics</th>
                        <th colspan="2" style="text-align: center; padding: 10px;">Today's Activity</th>
                        <th colspan="2" style="text-align: center; padding: 10px;">Right Party PTP</th>
                        <th colspan="2" style="text-align: center; padding: 10px;">Monthly PTP</th>
                        <th colspan="3" style="text-align: center; padding: 10px;">MTD (Money Transfer Data)</th>
                        <th rowspan="2" style="vertical-align: middle; padding: 10px;">Payments (Month)</th>
                    </tr>
                    <tr style="background-color: #1f4e78; color: white;">
                        <th style="padding: 8px; font-size: 11px;">Leads Worked</th>
                        <th style="padding: 8px; font-size: 11px;">Good Leads</th>
                        <th style="padding: 8px; font-size: 11px;">PTP Created</th>
                        <th style="padding: 8px; font-size: 11px;">Negotiation</th>
                        <th style="padding: 8px; font-size: 11px;">Count</th>
                        <th style="padding: 8px; font-size: 11px;">Value (KSH)</th>
                        <th style="padding: 8px; font-size: 11px;">Count</th>
                        <th style="padding: 8px; font-size: 11px;">Value (KSH)</th>
                        <th style="padding: 8px; font-size: 11px;">Today Count</th>
                        <th style="padding: 8px; font-size: 11px;">Today Value</th>
                        <th style="padding: 8px; font-size: 11px;">Monthly Value</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalLeadsWorked = 0;
                        $totalGoodLeads = 0;
                        $totalPTPCreatedToday = 0;
                        $totalNegotiation = 0;
                        $totalRightPartyCount = 0;
                        $totalRightPartyValue = 0;
                        $totalPTPMonthCount = 0;
                        $totalPTPMonthValue = 0;
                        $totalMTDTodayCount = 0;
                        $totalMTDTodayValue = 0;
                        $totalMTDMonthValue = 0;
                        $totalPaymentsValue = 0;
                    @endphp

                    @foreach ($data['report_data'] as $agent)
                        @php
                            $totalLeadsWorked += $agent['leads_worked'];
                            $totalGoodLeads += $agent['good_leads'];
                            $totalPTPCreatedToday += $agent['ptp_created_today'];
                            $totalNegotiation += $agent['negotiation_in_progress'];
                            $totalRightPartyCount += $agent['right_party_ptp_count'];
                            $totalRightPartyValue += $agent['right_party_ptp_value'];
                            $totalPTPMonthCount += $agent['ptp_month_count'];
                            $totalPTPMonthValue += $agent['ptp_month_value'];
                            $totalMTDTodayCount += $agent['mtd_today_count'];
                            $totalMTDTodayValue += $agent['mtd_today_value'];
                            $totalMTDMonthValue += $agent['mtd_month_value'];
                            $totalPaymentsValue += $agent['payments_posted_value'];
                        @endphp
                        <tr>
                            <td style="font-weight: bold;">{{ $agent['agent_name'] }}</td>
                            <td style="text-align: center;">{{ $agent['leads_worked'] }}</td>
                            <td style="text-align: center; background-color: #e8f4f8;">{{ $agent['good_leads'] }}</td>
                            <td style="text-align: center;">{{ $agent['ptp_created_today'] }}</td>
                            <td style="text-align: center;">{{ $agent['negotiation_in_progress'] }}</td>
                            <td style="text-align: center; background-color: #fff3cd;">{{ $agent['right_party_ptp_count'] }}
                            </td>
                            <td style="text-align: right; background-color: #fff3cd;">
                                {{ number_format($agent['right_party_ptp_value'], 2) }}</td>
                            <td style="text-align: center;">{{ $agent['ptp_month_count'] }}</td>
                            <td style="text-align: right;">{{ number_format($agent['ptp_month_value'], 2) }}</td>
                            <td style="text-align: center; background-color: #f0f0f0;">{{ $agent['mtd_today_count'] }}</td>
                            <td style="text-align: right; background-color: #f0f0f0;">
                                {{ number_format($agent['mtd_today_value'], 2) }}</td>
                            <td style="text-align: right; background-color: #f0f0f0;">
                                {{ number_format($agent['mtd_month_value'], 2) }}</td>
                            <td style="text-align: right; color: #28a745; font-weight: bold;">
                                {{ number_format($agent['payments_posted_value'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #366092; color: white; font-weight: bold;">
                        <td style="padding: 10px;">TOTALS</td>
                        <td style="text-align: center; padding: 10px;">{{ $totalLeadsWorked }}</td>
                        <td style="text-align: center; padding: 10px;">{{ $totalGoodLeads }}</td>
                        <td style="text-align: center; padding: 10px;">{{ $totalPTPCreatedToday }}</td>
                        <td style="text-align: center; padding: 10px;">{{ $totalNegotiation }}</td>
                        <td style="text-align: center; padding: 10px;">{{ $totalRightPartyCount }}</td>
                        <td style="text-align: right; padding: 10px;">{{ number_format($totalRightPartyValue, 2) }}</td>
                        <td style="text-align: center; padding: 10px;">{{ $totalPTPMonthCount }}</td>
                        <td style="text-align: right; padding: 10px;">{{ number_format($totalPTPMonthValue, 2) }}</td>
                        <td style="text-align: center; padding: 10px;">{{ $totalMTDTodayCount }}</td>
                        <td style="text-align: right; padding: 10px;">{{ number_format($totalMTDTodayValue, 2) }}</td>
                        <td style="text-align: right; padding: 10px;">{{ number_format($totalMTDMonthValue, 2) }}</td>
                        <td style="text-align: right; padding: 10px;">{{ number_format($totalPaymentsValue, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Legend -->
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Column Definitions</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <ul style="list-style-type: none; padding-left: 0;">
                        <li><strong>Leads Worked:</strong> Total leads assigned to or created by the agent</li>
                        <li><strong>Good Leads:</strong> Sum of Negotiation + PTP Created</li>
                        <li><strong>PTP Created (Today):</strong> Promise to Pay entries created today by agent</li>
                        <li><strong>Negotiation (Today):</strong> Leads in negotiation status</li>
                        <li><strong>Right Party PTP:</strong> Activities with disposition=4 and PTP due today</li>
                        <li><strong>Monthly PTP:</strong> Promise to Pay entries for the month</li>
                        <li><strong>MTD Today:</strong> Money Transfer Data recorded today</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul style="list-style-type: none; padding-left: 0;">
                        <li><strong>MTD Monthly:</strong> Total Money Transfer Data for the month</li>
                        <li><strong>Payments (Month):</strong> Transaction payments posted for the month</li>
                        <li><strong>Created By:</strong> Records created by the agent (not just assigned)</li>
                        <li><strong>Values:</strong> All currency values in KSH</li>
                        <li><strong>Period:</strong> Report generated for the selected date range</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop
