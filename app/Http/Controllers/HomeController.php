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

        return view('home')->with([
            'totalLeads' => $totalLeads,
            'totalAgents' => $totalAgents,
            'institutions' => $institutions,
            'leadStats' => $leadStats,
            'recentLeads' => $recentLeads,
            'smsStats' => $smsStats,
            'isAdmin' => $isAdmin
        ]);
    }
}
