<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Institution;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\Text;
use App\Exports\PTPTodayExport;
use App\Exports\PTPThisWeekExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin'); // Adjust role name as needed
        $userId = $user->id;

        // Build base queries with conditions
        if ($isAdmin) {
            // Admin sees all data
            $totalLeads = Lead::count();
            $leadStats = [
                'pending' => Lead::where('status_id', 1)->count(),
                'paid' => Lead::where('status_id', 2)->count(),
                'partially_paid' => Lead::where('status_id', 3)->count(),
                'overdue' => Lead::where('status_id', 4)->count(),
                'legal_escalation' => Lead::where('status_id', 5)->count(),
                'disputed' => Lead::where('status_id', 6)->count(),
            ];
            $recentLeads = Lead::orderBy('id', 'DESC')->take(5)->get();
            $smsStats = [
                'pending' => Text::where('status', 1)->count(),
                'delivered' => Text::where('status', 3)->count(),
                'undelivered' => Text::where('status', 4)->count(),
                'inQueue' => Text::where('status', 2)->count(),
            ];
            $totalAgents = DB::table('model_has_roles')->where('role_id', 1)->count();
            $institutions = Institution::count();
        } else {
            // Non-admin sees filtered data
            $totalLeads = Lead::where('assigned_agent', $userId)->count();
            $leadStats = [
                'pending' => Lead::where('assigned_agent', $userId)->where('status_id', 1)->count(),
                'paid' => Lead::where('assigned_agent', $userId)->where('status_id', 2)->count(),
                'partially_paid' => Lead::where('assigned_agent', $userId)->where('status_id', 3)->count(),
                'overdue' => Lead::where('assigned_agent', $userId)->where('status_id', 4)->count(),
                'legal_escalation' => Lead::where('assigned_agent', $userId)->where('status_id', 5)->count(),
                'disputed' => Lead::where('assigned_agent', $userId)->where('status_id', 6)->count(),
            ];
            $recentLeads = Lead::where('assigned_agent', $userId)->orderBy('id', 'DESC')->take(5)->get();
            $smsStats = [
                'pending' => Text::where('created_by', $userId)->where('status', 1)->count(),
                'delivered' => Text::where('created_by', $userId)->where('status', 3)->count(),
                'undelivered' => Text::where('created_by', $userId)->where('status', 4)->count(),
                'inQueue' => Text::where('created_by', $userId)->where('status', 2)->count(),
            ];
            $totalAgents = 0; // Hide for non-admin
            $institutions = 0; // Hide for non-admin
        }

        // Get PTPs for today and this week
        $today = Carbon::today();
        $weekEnd = Carbon::today()->endOfWeek();

        // Base query for PTPs with lead details
        $ptpBaseQuery = Activity::select([
                'activities.id as activity_id',
                'activities.act_ptp_date',
                'activities.act_ptp_amount',
                'leads.id as lead_id',
                'leads.title as lead_name',
                'leads.email as lead_email',
                'leads.telephone as lead_telephone',
                'institutions.institution_name',
                'assigned_agents.name as assigned_agent_name',
                'assigned_agents.agent_code as assigned_agent_code'
            ])
            ->leftJoin('leads', 'activities.lead_id', '=', 'leads.id')
            ->leftJoin('institutions', 'leads.institution_id', '=', 'institutions.id')
            ->leftJoin('users as assigned_agents', 'leads.assigned_agent', '=', 'assigned_agents.id')
            ->whereNotNull('activities.act_ptp_date')
            ->whereNotNull('activities.act_ptp_amount');

        if ($isAdmin) {
            // Admin sees all PTPs
            $ptpsToday = (clone $ptpBaseQuery)->whereDate('activities.act_ptp_date', $today)->paginate(8, ['*'], 'ptp_today_page');
            $ptpsThisWeek = (clone $ptpBaseQuery)->whereBetween('activities.act_ptp_date', [$today, $weekEnd])->paginate(8, ['*'], 'ptp_week_page');
        } else {
            // User sees only PTPs they created
            $ptpsToday = (clone $ptpBaseQuery)
                ->where('activities.created_by', $userId)
                ->whereDate('activities.act_ptp_date', $today)
                ->paginate(8, ['*'], 'ptp_today_page');
            $ptpsThisWeek = (clone $ptpBaseQuery)
                ->where('activities.created_by', $userId)
                ->whereBetween('activities.act_ptp_date', [$today, $weekEnd])
                ->paginate(8, ['*'], 'ptp_week_page');
        }

        // Get scheduled activities for today
        $activitiesToday = Activity::select([
                'activities.id as activity_id',
                'activities.activity_title',
                'activities.description',
                'activities.start_date_time',
                'activities.due_date_time',
                'leads.id as lead_id',
                'leads.title as lead_name',
                'activity_types.activity_type_title',
                'activity_types.icon as activity_type_icon',
                'lead_priorities.lead_priority_name',
                'lead_priorities.color_code as priority_color'
            ])
            ->leftJoin('leads', 'activities.lead_id', '=', 'leads.id')
            ->leftJoin('activity_types', 'activities.activity_type_id', '=', 'activity_types.id')
            ->leftJoin('lead_priorities', 'activities.priority_id', '=', 'lead_priorities.id')
            ->whereDate('activities.start_date_time', $today)
            ->whereNotNull('activities.start_date_time');

        if (!$isAdmin) {
            // Non-admin users see only activities they created
            $activitiesToday->where('activities.created_by', $userId);
        }

        $activitiesToday = $activitiesToday->orderBy('activities.start_date_time')->get();

        return view('home')->with([
            'totalLeads' => $totalLeads,
            'totalAgents' => $totalAgents,
            'institutions' => $institutions,
            'leadStats' => $leadStats,
            'recentLeads' => $recentLeads,
            'smsStats' => $smsStats,
            'isAdmin' => $isAdmin,
            'ptpsToday' => $ptpsToday,
            'ptpsThisWeek' => $ptpsThisWeek,
            'activitiesToday' => $activitiesToday
        ]);
    }

    public function exportPTPToday()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');
        $userId = $user->id;
        $today = Carbon::today();

        // Build the same query as in index method
        $ptpQuery = Activity::select([
                'activities.id as activity_id',
                'activities.act_ptp_date',
                'activities.act_ptp_amount',
                'leads.id as lead_id',
                'leads.title as lead_name',
                'leads.email as lead_email',
                'leads.telephone as lead_telephone',
                'institutions.institution_name',
                'assigned_agents.name as assigned_agent_name',
                'assigned_agents.agent_code as assigned_agent_code'
            ])
            ->leftJoin('leads', 'activities.lead_id', '=', 'leads.id')
            ->leftJoin('institutions', 'leads.institution_id', '=', 'institutions.id')
            ->leftJoin('users as assigned_agents', 'leads.assigned_agent', '=', 'assigned_agents.id')
            ->whereNotNull('activities.act_ptp_date')
            ->whereNotNull('activities.act_ptp_amount')
            ->whereDate('activities.act_ptp_date', $today);

        if (!$isAdmin) {
            $ptpQuery->where('activities.created_by', $userId);
        }

        $ptps = $ptpQuery->get();

        return Excel::download(new PTPTodayExport($ptps), 'ptp-today-' . date('Y-m-d') . '.xlsx');
    }

    public function exportPTPThisWeek()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');
        $userId = $user->id;
        $today = Carbon::today();
        $weekEnd = Carbon::today()->endOfWeek();

        // Build the same query as in index method
        $ptpQuery = Activity::select([
                'activities.id as activity_id',
                'activities.act_ptp_date',
                'activities.act_ptp_amount',
                'leads.id as lead_id',
                'leads.title as lead_name',
                'leads.email as lead_email',
                'leads.telephone as lead_telephone',
                'institutions.institution_name',
                'assigned_agents.name as assigned_agent_name',
                'assigned_agents.agent_code as assigned_agent_code'
            ])
            ->leftJoin('leads', 'activities.lead_id', '=', 'leads.id')
            ->leftJoin('institutions', 'leads.institution_id', '=', 'institutions.id')
            ->leftJoin('users as assigned_agents', 'leads.assigned_agent', '=', 'assigned_agents.id')
            ->whereNotNull('activities.act_ptp_date')
            ->whereNotNull('activities.act_ptp_amount')
            ->whereBetween('activities.act_ptp_date', [$today, $weekEnd]);

        if (!$isAdmin) {
            $ptpQuery->where('activities.created_by', $userId);
        }

        $ptps = $ptpQuery->get();

        return Excel::download(new PTPThisWeekExport($ptps), 'ptp-this-week-' . date('Y-m-d') . '.xlsx');
    }
}
