<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\Text;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $leadsQuery = $isAdmin ? Lead::query() : Lead::where('assigned_agent', $userId);
        $smsQuery = $isAdmin ? Text::query() : Text::where('created_by', $userId);

        // Get lead stats in one efficient query using conditional aggregation
        $leadStats = $leadsQuery->selectRaw('
        COUNT(CASE WHEN status_id = 1 THEN 1 END) as pending,
        COUNT(CASE WHEN status_id = 2 THEN 1 END) as paid,
        COUNT(CASE WHEN status_id = 3 THEN 1 END) as partially_paid,
        COUNT(CASE WHEN status_id = 4 THEN 1 END) as overdue,
        COUNT(CASE WHEN status_id = 5 THEN 1 END) as legal_escalation,
        COUNT(CASE WHEN status_id = 6 THEN 1 END) as disputed,
        COUNT(*) as total
    ')->first();

        // Get SMS stats in one efficient query using conditional aggregation
        $smsStats = $smsQuery->selectRaw('
        COUNT(CASE WHEN status = 1 THEN 1 END) as pending,
        COUNT(CASE WHEN status = 2 THEN 1 END) as inQueue,
        COUNT(CASE WHEN status = 3 THEN 1 END) as delivered,
        COUNT(CASE WHEN status = 4 THEN 1 END) as undelivered
    ')->first();

        return view('home')->with([
            'totalLeads' => $leadStats->total,
            'totalAgents' => $isAdmin ? DB::table('model_has_roles')->where('role_id', 1)->count() : 0,
            'institutions' => $isAdmin ? Institution::count() : 0,
            'leadStats' => [
                'pending' => $leadStats->pending,
                'paid' => $leadStats->paid,
                'partially_paid' => $leadStats->partially_paid,
                'overdue' => $leadStats->overdue,
                'legal_escalation' => $leadStats->legal_escalation,
                'disputed' => $leadStats->disputed,
            ],
            'recentLeads' => $isAdmin
                ? Lead::orderBy('id', 'DESC')->take(5)->get()
                : Lead::where('assigned_agent', $userId)->orderBy('id', 'DESC')->take(5)->get(),
            'smsStats' => [
                'pending' => $smsStats->pending,
                'delivered' => $smsStats->delivered,
                'undelivered' => $smsStats->undelivered,
                'inQueue' => $smsStats->inQueue,
            ],
            'isAdmin' => $isAdmin
        ]);
    }
}
