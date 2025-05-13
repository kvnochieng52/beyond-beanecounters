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



        return view('home')->with([
            'totalLeads' => Lead::count(),
            'totalAgents' => DB::table('model_has_roles')->where('role_id', 1)->count(),
            'institutions' => Institution::count(),
            'leadStats' => [
                'pending' => Lead::where('status_id', 1)->count(),
                'paid' => Lead::where('status_id', 2)->count(),
                'partially_paid' => Lead::where('status_id', 3)->count(),
                'overdue' => Lead::where('status_id', 4)->count(),
                'legal_escalation' => Lead::where('status_id', 5)->count(),
                'disputed' => Lead::where('status_id', 6)->count(),
            ],
            'recentLeads' => Lead::query()->orderBy('id', 'DESC')->take(5)->get(),
            'smsStats' => [
                'pending' => Text::where('status', 1)->count(),
                'delivered' => Text::where('status', 3)->count(),
                'undelivered' => Text::where('status', 4)->count(),
                'inQueue' => Text::where('status', 2)->count(),
            ]

        ]);
    }
}
