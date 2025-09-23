<?php

namespace App\Http\Controllers;

use App\Exports\ActivityReportExport;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\CallDisposition;
use App\Models\Institution;
use App\Models\User;
use App\Models\BackgroundReport;
use App\Jobs\ProcessActivityReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ActivityReportController extends Controller
{
    public function index()
    {


        return view('activity_report.index')->with([
            'agentsList' => User::where('is_active', 1)
                ->select(DB::raw("CONCAT(name, ' - ', agent_code) as name"), 'id')
                ->pluck('name', 'id'),
            'institutions' => Institution::Where('is_active', 1)->pluck('institution_name', 'id'),
            'call_dispositions' => CallDisposition::where('is_active', 1)->pluck('call_disposition_name', 'id'),
            'actvityTypes' => ActivityType::where('is_active', 1)->pluck('activity_type_title', 'id'),
        ]);
    }

    // public function generateReport(Request $request)
    // {
    //     $query = Activity::select([
    //         'activities.*',
    //         'lead_priorities.lead_priority_name',
    //         'lead_priorities.color_code as lead_priority_color_code',
    //         'activity_types.activity_type_title',
    //         'activity_types.icon as activity_type_icon',
    //         'activity_statuses.activity_status_name',
    //         'activity_statuses.color_code as activity_status_color_code',
    //         'departments.department_name',
    //         'AGENT_JOIN.name AS assigned_agent_name',
    //         'AGENT_JOIN.id_number AS assigned_agent_id_number',
    //         'AGENT_JOIN.agent_code AS assigned_agent_code',

    //         'CREATED_BY_JOIN.name AS created_by_name',
    //         'CREATED_BY_JOIN.id_number AS created_by_id_number',
    //         'CREATED_BY_JOIN.agent_code AS created_by_code',
    //         'CREATED_BY_JOIN.telephone AS created_by_telephone',
    //         'CREATED_BY_JOIN.email AS created_by_email',
    //         DB::raw('DATE(activities.created_at) as created_date'),
    //         DB::raw('DATE_FORMAT(activities.created_at, "%l:%i %p") as created_time'),

    //         'leads.title as lead_title',
    //         'institutions.institution_name',
    //         'leads.amount as lead_amount',
    //         'leads.balance as lead_balance',
    //         'ptps.ptp_date',
    //         'ptps.ptp_amount',
    //         'ptps.ptp_expiry_date',
    //         'ptps.created_at as ptp_created_at',
    //         'call_dispositions.call_disposition_name',
    //         'text_statuses.text_status_name',
    //         'text_statuses.color_code as text_status_color_code',
    //     ])
    //         ->leftJoin('lead_priorities', 'activities.priority_id', 'lead_priorities.id')
    //         ->leftJoin('activity_statuses', 'activities.status_id', 'activity_statuses.id')
    //         ->leftJoin('activity_types', 'activities.activity_type_id', 'activity_types.id')
    //         ->leftJoin('departments', 'activities.assigned_department_id', 'departments.id')
    //         ->leftJoin('users AS AGENT_JOIN', 'activities.assigned_user_id', '=', 'AGENT_JOIN.id')
    //         ->leftJoin('users AS CREATED_BY_JOIN', 'activities.created_by', '=', 'CREATED_BY_JOIN.id')
    //         ->leftJoin('leads', 'activities.lead_id', 'leads.id')
    //         ->leftJoin('institutions', 'leads.institution_id', 'institutions.id')
    //         ->leftJoin('call_dispositions', 'activities.act_call_disposition_id', 'call_dispositions.id')
    //         ->leftJoin('texts', 'activities.ref_text_id', 'texts.id')
    //         ->leftJoin('text_statuses', 'texts.status', '=', 'text_statuses.id');



    // }


    public function generateReport(Request $request)
    {
        // Validate required fields
        $request->validate([
            'from_date' => 'required|date_format:d-m-Y',
            'to_date' => 'required|date_format:d-m-Y|after_or_equal:from_date',
        ]);

        $query = Activity::select([
            'activities.*',
            'lead_priorities.lead_priority_name',
            'lead_priorities.color_code as lead_priority_color_code',
            'activity_types.activity_type_title',
            'activity_types.icon as activity_type_icon',
            'activity_statuses.activity_status_name',
            'activity_statuses.color_code as activity_status_color_code',
            'departments.department_name',
            'AGENT_JOIN.name AS assigned_agent_name',
            'AGENT_JOIN.id_number AS assigned_agent_id_number',
            'AGENT_JOIN.agent_code AS assigned_agent_code',
            'CREATED_BY_JOIN.name AS created_by_name',
            'CREATED_BY_JOIN.id_number AS created_by_id_number',
            'CREATED_BY_JOIN.agent_code AS created_by_code',
            'CREATED_BY_JOIN.telephone AS created_by_telephone',
            'CREATED_BY_JOIN.email AS created_by_email',
            DB::raw('DATE(activities.created_at) as created_date'),
            DB::raw('DATE_FORMAT(activities.created_at, "%l:%i %p") as created_time'),
            'leads.title as lead_title',
            'leads.id as ticket_number',
            'institutions.institution_name',
            'leads.amount as lead_amount',
            'leads.balance as lead_balance',
            'leads.waiver_discount as lead_waiver_discount',
            'call_dispositions.call_disposition_name',
            'text_statuses.text_status_name',
            'text_statuses.color_code as text_status_color_code',
            'payment_methods.method_name',
        ])
            ->leftJoin('lead_priorities', 'activities.priority_id', 'lead_priorities.id')
            ->leftJoin('activity_statuses', 'activities.status_id', 'activity_statuses.id')
            ->leftJoin('activity_types', 'activities.activity_type_id', 'activity_types.id')
            ->leftJoin('departments', 'activities.assigned_department_id', 'departments.id')
            ->leftJoin('users AS AGENT_JOIN', 'activities.assigned_user_id', '=', 'AGENT_JOIN.id')
            ->leftJoin('users AS CREATED_BY_JOIN', 'activities.created_by', '=', 'CREATED_BY_JOIN.id')
            ->leftJoin('leads', 'activities.lead_id', 'leads.id')
            ->leftJoin('institutions', 'leads.institution_id', 'institutions.id')
            ->leftJoin('call_dispositions', 'activities.act_call_disposition_id', 'call_dispositions.id')
            ->leftJoin('texts', 'activities.ref_text_id', 'texts.id')
            ->leftJoin('text_statuses', 'texts.status', '=', 'text_statuses.id')
            ->leftJoin('payment_methods', 'activities.act_payment_method', '=', 'payment_methods.id');


        // Apply Date Range Filter (Activity Creation Date)
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
            $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();

            $query->whereBetween('activities.created_at', [$fromDate, $toDate]);
        }

        // Apply Ticket Number Filter
        if ($request->filled('ticket_no')) {
            $ticketNumbers = array_map('trim', explode(',', $request->ticket_no));
            $query->whereIn('activities.lead_id', $ticketNumbers);
        }

        // Apply Activity Type Filter
        if ($request->filled('activity_type') && is_array($request->activity_type)) {
            $activityTypes = array_filter($request->activity_type);

            if (!empty($activityTypes)) {
                $query->whereIn('activities.activity_type_id', $activityTypes);
            }
        }

        // Apply Agent Filter
        if ($request->filled('agent') && is_array($request->agent)) {


            $agents = array_filter($request->agent);


            if (!empty($agents)) {
                $query->whereIn('activities.assigned_user_id', $agents);
            }
        }

        // Apply Institution Filter
        if ($request->filled('institution') && is_array($request->institution)) {
            $institutions = array_filter($request->institution);


            if (!empty($institutions)) {
                $query->whereIn('leads.institution_id', $institutions);
            }
        }

        // Apply Call Disposition Filter
        if ($request->filled('disposition') && is_array($request->disposition)) {
            $dispositions = array_filter($request->disposition);
            if (!empty($dispositions)) {
                $query->whereIn('activities.act_call_disposition_id', $dispositions);
            }
        }

        // Apply PTP Created Date Filter
        // if ($request->filled('ptp_created_from_date') && $request->filled('ptp_created_to_date')) {
        //     $ptpFromDate = Carbon::createFromFormat('d-m-Y', $request->ptp_created_from_date)->startOfDay();
        //     $ptpToDate = Carbon::createFromFormat('d-m-Y', $request->ptp_created_to_date)->endOfDay();

        //     $query->whereBetween('ptps.created_at', [$ptpFromDate, $ptpToDate]);
        // } elseif ($request->filled('ptp_created_from_date')) {
        //     $ptpFromDate = Carbon::createFromFormat('d-m-Y', $request->ptp_created_from_date)->startOfDay();
        //     $query->where('ptps.created_at', '>=', $ptpFromDate);
        // } elseif ($request->filled('ptp_created_to_date')) {
        //     $ptpToDate = Carbon::createFromFormat('d-m-Y', $request->ptp_created_to_date)->endOfDay();
        //     $query->where('ptps.created_at', '<=', $ptpToDate);
        // }

        // Apply PTP Due Date Filter
        if ($request->filled('ptp_due_from_date') && $request->filled('ptp_due_to_date')) {
            $ptpDueFromDate = Carbon::createFromFormat('d-m-Y', $request->ptp_due_from_date)->format('Y-m-d');
            $ptpDueToDate = Carbon::createFromFormat('d-m-Y', $request->ptp_due_to_date)->format('Y-m-d');

            $query->whereBetween('act_ptp_date', [$ptpDueFromDate, $ptpDueToDate]);
        } elseif ($request->filled('ptp_due_from_date')) {
            $ptpDueFromDate = Carbon::createFromFormat('d-m-Y', $request->ptp_due_from_date)->format('Y-m-d');
            $query->where('act_ptp_date', '>=', $ptpDueFromDate);
        } elseif ($request->filled('ptp_due_to_date')) {
            $ptpDueToDate = Carbon::createFromFormat('d-m-Y', $request->ptp_due_to_date)->format('Y-m-d');
            $query->where('act_ptp_date', '<=', $ptpDueToDate);
        }

        // Order by created date
        $query->orderBy('activities.created_at', 'desc');

        // Get the filtered data
        $activities = $query->get();

        // Generate filename with date range
        $filename = 'activity_report_' .
            str_replace('-', '', $request->from_date) . '_to_' .
            str_replace('-', '', $request->to_date) . '_' .
            date('YmdHis') . '.xlsx';

        // Check if any data found
        if ($activities->isEmpty()) {
            return back()->with('error', 'No activities found matching the specified criteria.');
        }

        // Queue the report for background processing instead of direct download
        $reportName = 'Activity Report - ' . $request->from_date . ' to ' . $request->to_date;

        // Prepare filters for the job
        $filters = [
            'from_date' => Carbon::createFromFormat('d-m-Y', $request->from_date)->format('Y-m-d'),
            'to_date' => Carbon::createFromFormat('d-m-Y', $request->to_date)->format('Y-m-d'),
        ];

        if ($request->filled('ticket_no')) {
            $filters['ticket_numbers'] = array_map('trim', explode(',', $request->ticket_no));
        }
        if ($request->filled('activity_type') && is_array($request->activity_type)) {
            $filters['activity_type_ids'] = array_filter($request->activity_type);
        }
        if ($request->filled('agent') && is_array($request->agent)) {
            $filters['agent_ids'] = array_filter($request->agent);
        }
        if ($request->filled('institution') && is_array($request->institution)) {
            $filters['institution_ids'] = array_filter($request->institution);
        }
        if ($request->filled('disposition') && is_array($request->disposition)) {
            $filters['disposition_ids'] = array_filter($request->disposition);
        }
        if ($request->filled('ptp_due_from_date')) {
            $filters['ptp_due_from_date'] = Carbon::createFromFormat('d-m-Y', $request->ptp_due_from_date)->format('Y-m-d');
        }
        if ($request->filled('ptp_due_to_date')) {
            $filters['ptp_due_to_date'] = Carbon::createFromFormat('d-m-Y', $request->ptp_due_to_date)->format('Y-m-d');
        }

        // Create background report record
        $backgroundReport = BackgroundReport::create([
            'report_type' => 'activity_report',
            'report_name' => $reportName,
            'filters' => $filters,
            'status' => 'pending',
            'requested_by' => Auth::id(),
        ]);

        // Dispatch the job
        ProcessActivityReport::dispatch($backgroundReport);

        return redirect()->route('background-reports.index')
            ->with('success', 'Activity report has been queued for processing. You can check the progress in Background Reports.');
    }
}
