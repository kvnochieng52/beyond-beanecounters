<?php

namespace App\Http\Controllers;

use App\Exports\AgentLeadsExport;
use App\Exports\AgentPerformanceExport;
use App\Exports\CollectionProgressExport;
use App\Exports\CollectionRateExport;
use App\Exports\OutstandingDebtExport;
use App\Exports\LeadsReportExport;
use App\Jobs\ProcessLeadsReport;
use App\Models\Institution;
use App\Models\Lead;
use App\Models\Transaction;
use App\Models\BackgroundReport;
use App\Models\LeadStatus;
use App\Models\LeadPriority;
use App\Models\LeadCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        // $this->middleware('permission:Lead Reports');
    }

    public function collectionRates()
    {
        $institutions = Institution::where('is_active', 1)->get();
        return view('reports.collection_rates', compact('institutions'));
    }

    public function collectionProgress()
    {
        $institutions = Institution::where('is_active', 1)->get();
        return view('reports.collection_progress', compact('institutions'));
    }

    public function generateCollectionProgress(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'institution_id' => 'nullable|exists:institutions,id',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $institutionId = $request->institution_id;

        // Get all leads within the date range
        $leadsQuery = Lead::whereBetween('created_at', [$startDate, $endDate]);

        if ($institutionId) {
            $leadsQuery->where('institution_id', $institutionId);
        }

        $leads = $leadsQuery->get();

        // Calculate collection progress metrics
        $totalLeads = $leads->count();
        $totalDebt = $leads->sum('amount');
        $totalCollected = 0;
        $collectionByMonth = [];
        $collectionByWeek = [];

        // Initialize collection by month data
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $yearMonth = $currentDate->format('Y-m');
            $monthName = $currentDate->format('M Y');
            $collectionByMonth[$yearMonth] = [
                'month' => $monthName,
                'target' => 0,
                'collected' => 0,
                'percentage' => 0
            ];
            $currentDate->addMonth();
        }

        // Initialize collection by week data
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $yearWeek = $currentDate->format('Y-W');
            $weekName = 'Week ' . $currentDate->format('W') . ' ' . $currentDate->format('Y');
            $collectionByWeek[$yearWeek] = [
                'week' => $weekName,
                'target' => 0,
                'collected' => 0,
                'percentage' => 0
            ];
            $currentDate->addWeek();
        }

        foreach ($leads as $lead) {
            // Calculate target amounts by month and week
            $leadMonth = Carbon::parse($lead->created_at)->format('Y-m');
            $leadWeek = Carbon::parse($lead->created_at)->format('Y-W');

            if (isset($collectionByMonth[$leadMonth])) {
                $collectionByMonth[$leadMonth]['target'] += $lead->amount;
            }

            if (isset($collectionByWeek[$leadWeek])) {
                $collectionByWeek[$leadWeek]['target'] += $lead->amount;
            }

            // Get payments for this lead
            $payments = Transaction::where('lead_id', $lead->id)
                ->where('transaction_type', 1) // Assuming 1 is for payments
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            foreach ($payments as $payment) {
                $totalCollected += $payment->amount;

                // Add to monthly collection
                $paymentMonth = Carbon::parse($payment->created_at)->format('Y-m');
                if (isset($collectionByMonth[$paymentMonth])) {
                    $collectionByMonth[$paymentMonth]['collected'] += $payment->amount;
                }

                // Add to weekly collection
                $paymentWeek = Carbon::parse($payment->created_at)->format('Y-W');
                if (isset($collectionByWeek[$paymentWeek])) {
                    $collectionByWeek[$paymentWeek]['collected'] += $payment->amount;
                }
            }
        }

        // Calculate percentages
        foreach ($collectionByMonth as $month => $data) {
            if ($data['target'] > 0) {
                $collectionByMonth[$month]['percentage'] = ($data['collected'] / $data['target']) * 100;
            }
        }

        foreach ($collectionByWeek as $week => $data) {
            if ($data['target'] > 0) {
                $collectionByWeek[$week]['percentage'] = ($data['collected'] / $data['target']) * 100;
            }
        }

        // Convert to arrays for the view
        $monthlyData = array_values($collectionByMonth);
        $weeklyData = array_values($collectionByWeek);

        $data = [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_leads' => $totalLeads,
            'total_debt' => $totalDebt,
            'total_collected' => $totalCollected,
            'collection_rate' => $totalDebt > 0 ? ($totalCollected / $totalDebt) * 100 : 0,
            'monthly_data' => $monthlyData,
            'weekly_data' => $weeklyData
        ];

        if ($request->has('export') && $request->export == 'excel') {
            return Excel::download(new CollectionProgressExport($data), 'collection_progress_report.xlsx');
        }

        return view('reports.collection_progress_result', compact('data'));
    }

    public function agentLeads()
    {
        $agents = \App\Models\User::all();
        $institutions = Institution::where('is_active', 1)->get();
        return view('reports.agent_leads', compact('agents', 'institutions'));
    }

    public function generateAgentLeads(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'institution_id' => 'nullable|exists:institutions,id',
        ]);

        $agentId = $request->agent_id;
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $institutionId = $request->institution_id;

        // Get agent details
        $agent = \App\Models\User::findOrFail($agentId);

        // Get leads assigned to this agent
        $leadsQuery = Lead::where('assigned_agent', $agentId)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($institutionId) {
            $leadsQuery->where('institution_id', $institutionId);
        }

        $leads = $leadsQuery->get();

        // Calculate summary metrics
        $totalLeads = $leads->count();
        $totalAssigned = $leads->sum('amount');
        $totalCollected = 0;
        $closedLeads = 0;
        $overdueLeads = 0;

        // Prepare leads data for display
        $leadsData = [];

        foreach ($leads as $lead) {
            // Get payments for this lead
            $payments = Transaction::where('lead_id', $lead->id)
                ->where('transaction_type', 1) // Assuming 1 is for payments
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');

            $totalCollected += $payments;

            // Check if lead is closed (fully paid)
            $isClosed = $lead->balance <= 0;
            if ($isClosed) {
                $closedLeads++;
            }

            // Check if lead is overdue
            $isOverdue = $lead->due_date && Carbon::parse($lead->due_date)->lt(Carbon::now()) && $lead->balance > 0;
            if ($isOverdue) {
                $overdueLeads++;
            }

            // Get institution name
            $institutionName = $lead->institution ? $lead->institution->institution_name : 'N/A';

            // Get lead status
            $statusName = $lead->status ? $lead->status->status_title : 'N/A';

            $leadsData[] = [
                'id' => $lead->id,
                'title' => $lead->title,
                'institution' => $institutionName,
                'amount' => $lead->amount,
                'balance' => $lead->balance,
                'waiver_discount' => $lead->waiver_discount,
                'collected' => $payments,
                'due_date' => $lead->due_date ? Carbon::parse($lead->due_date)->format('Y-m-d') : 'N/A',
                'status' => $statusName,
                'is_closed' => $isClosed,
                'is_overdue' => $isOverdue,
                'created_at' => Carbon::parse($lead->created_at)->format('Y-m-d')
            ];
        }

        $data = [
            'agent' => $agent,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_leads' => $totalLeads,
            'total_assigned' => $totalAssigned,
            'total_collected' => $totalCollected,
            'collection_rate' => $totalAssigned > 0 ? ($totalCollected / $totalAssigned) * 100 : 0,
            'closed_leads' => $closedLeads,
            'overdue_leads' => $overdueLeads,
            'leads' => $leadsData
        ];

        if ($request->has('export') && $request->export == 'excel') {
            return Excel::download(new AgentLeadsExport($data), 'agent_leads_report.xlsx');
        }

        return view('reports.agent_leads_result', compact('data'));
    }

    public function agentPerformance()
    {
        $institutions = Institution::where('is_active', 1)->get();
        return view('reports.agent_performance', compact('institutions'));
    }

    public function generateAgentPerformance(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'institution_id' => 'nullable|exists:institutions,id',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $institutionId = $request->institution_id;




        // Get all users (agents)
        $agents = \App\Models\User::all();

        // For debugging
        // dd($agents);

        $agentData = [];
        $totalCollected = 0;
        $totalAssigned = 0;

        foreach ($agents as $agent) {
            // Get leads assigned to this agent
            $leadsQuery = Lead::where('assigned_agent', $agent->id)
                ->whereBetween('created_at', [$startDate, $endDate]);

            if ($institutionId) {
                $leadsQuery->where('institution_id', $institutionId);
            }

            $leads = $leadsQuery->get();

            // Calculate metrics
            $assignedAmount = $leads->sum('amount');
            $totalAssigned += $assignedAmount;

            $collectedAmount = 0;
            $closedLeads = 0;
            $overdueCases = 0;

            foreach ($leads as $lead) {
                // Get payments for this lead
                $payments = Transaction::where('lead_id', $lead->id)
                    ->where('transaction_type', 1) // Assuming 1 is for payments
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('amount');

                $collectedAmount += $payments;

                // Check if lead is closed (fully paid)
                if ($lead->balance <= 0) {
                    $closedLeads++;
                }

                // Check if lead is overdue
                if ($lead->due_date && Carbon::parse($lead->due_date)->lt(Carbon::now()) && $lead->balance > 0) {
                    $overdueCases++;
                }
            }

            $totalCollected += $collectedAmount;

            // Calculate collection rate
            $collectionRate = $assignedAmount > 0 ? ($collectedAmount / $assignedAmount) * 100 : 0;

            // Calculate average days to close
            $avgDaysToClose = 0;
            $closedLeadsCount = 0;

            foreach ($leads as $lead) {
                if ($lead->balance <= 0) {
                    $lastPayment = Transaction::where('lead_id', $lead->id)
                        ->where('transaction_type', 1)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($lastPayment) {
                        $daysToClose = Carbon::parse($lead->created_at)->diffInDays(Carbon::parse($lastPayment->created_at));
                        $avgDaysToClose += $daysToClose;
                        $closedLeadsCount++;
                    }
                }
            }

            if ($closedLeadsCount > 0) {
                $avgDaysToClose = $avgDaysToClose / $closedLeadsCount;
            }

            $agentData[] = [
                'id' => $agent->id,
                'name' => $agent->name,
                'total_leads' => $leads->count(),
                'assigned_amount' => $assignedAmount,
                'collected_amount' => $collectedAmount,
                'collection_rate' => $collectionRate,
                'closed_leads' => $closedLeads,
                'overdue_cases' => $overdueCases,
                'avg_days_to_close' => $avgDaysToClose
            ];
        }

        // Sort agents by collection rate (descending)
        usort($agentData, function ($a, $b) {
            return $b['collection_rate'] <=> $a['collection_rate'];
        });

        $data = [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_assigned' => $totalAssigned,
            'total_collected' => $totalCollected,
            'overall_collection_rate' => $totalAssigned > 0 ? ($totalCollected / $totalAssigned) * 100 : 0,
            'agents' => $agentData
        ];

        if ($request->has('export') && $request->export == 'excel') {
            return Excel::download(new AgentPerformanceExport($data), 'agent_performance_report.xlsx');
        }

        return view('reports.agent_performance_result', compact('data'));
    }

    public function generateCollectionRates(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'institution_id' => 'nullable|exists:institutions,id',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $institutionId = $request->institution_id;

        // Get all leads within the date range
        $leadsQuery = Lead::whereBetween('created_at', [$startDate, $endDate]);

        if ($institutionId) {
            $leadsQuery->where('institution_id', $institutionId);
        }

        $leads = $leadsQuery->get();

        // Calculate collection rates
        $totalLeads = $leads->count();
        $totalDebt = $leads->sum('amount');
        $totalCollected = 0;

        foreach ($leads as $lead) {
            // Get payments for this lead
            $payments = Transaction::where('lead_id', $lead->id)
                ->where('transaction_type', 1) // Assuming 1 is for payments
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');

            $totalCollected += $payments;
        }

        $collectionRate = $totalDebt > 0 ? ($totalCollected / $totalDebt) * 100 : 0;

        // Get collection rate by institution
        $institutionData = [];
        if (!$institutionId) {
            $institutions = Institution::where('is_active', 1)->get();
            foreach ($institutions as $institution) {
                $institutionLeads = $leads->where('institution_id', $institution->id);
                $institutionDebt = $institutionLeads->sum('amount');
                $institutionCollected = 0;

                foreach ($institutionLeads as $lead) {
                    $payments = Transaction::where('lead_id', $lead->id)
                        ->where('transaction_type', 1)
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->sum('amount');

                    $institutionCollected += $payments;
                }

                $institutionRate = $institutionDebt > 0 ? ($institutionCollected / $institutionDebt) * 100 : 0;

                $institutionData[] = [
                    'name' => $institution->institution_name,
                    'total_debt' => $institutionDebt,
                    'total_collected' => $institutionCollected,
                    'collection_rate' => $institutionRate
                ];
            }
        }

        $data = [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_leads' => $totalLeads,
            'total_debt' => $totalDebt,
            'total_collected' => $totalCollected,
            'collection_rate' => $collectionRate,
            'institutions' => $institutionData
        ];

        if ($request->has('export') && $request->export == 'excel') {
            return Excel::download(new CollectionRateExport($data), 'collection_rates_report.xlsx');
        }

        return view('reports.collection_rates_result', compact('data'));
    }

    public function outstandingDebts()
    {
        $institutions = Institution::where('is_active', 1)->get();
        return view('reports.outstanding_debts', compact('institutions'));
    }

    public function generateOutstandingDebts(Request $request)
    {
        $request->validate([
            'as_of_date' => 'required|date',
            'institution_id' => 'nullable|exists:institutions,id',
            'min_days_overdue' => 'nullable|integer|min:0',
            'max_days_overdue' => 'nullable|integer|min:0',
        ]);

        $asOfDate = Carbon::parse($request->as_of_date)->endOfDay();
        $institutionId = $request->institution_id;
        $minDaysOverdue = $request->min_days_overdue;
        $maxDaysOverdue = $request->max_days_overdue;

        // Get all leads with outstanding balances
        $leadsQuery = Lead::query()->where('balance', '>', 0)
            ->where('leads.created_at', '<=', $asOfDate);

        if ($institutionId) {
            $leadsQuery->where('institution_id', $institutionId);
        }

        // Filter by days overdue if specified
        if ($minDaysOverdue) {
            $leadsQuery->whereRaw('DATEDIFF(?, due_date) >= ?', [$asOfDate, $minDaysOverdue]);
        }

        if ($maxDaysOverdue) {
            $leadsQuery->whereRaw('DATEDIFF(?, due_date) <= ?', [$asOfDate, $maxDaysOverdue]);
        }

        $leads = $leadsQuery->get();

        // Calculate outstanding debt statistics
        $totalOutstanding = $leads->sum('balance');
        $totalLeads = $leads->count();

        // Group by days overdue
        $overdueGroups = [
            '0-30' => ['count' => 0, 'amount' => 0],
            '31-60' => ['count' => 0, 'amount' => 0],
            '61-90' => ['count' => 0, 'amount' => 0],
            '91+' => ['count' => 0, 'amount' => 0],
        ];

        foreach ($leads as $lead) {
            if (!$lead->due_date) continue;

            $daysOverdue = $asOfDate->diffInDays(Carbon::parse($lead->due_date), false);

            if ($daysOverdue <= 30) {
                $overdueGroups['0-30']['count']++;
                $overdueGroups['0-30']['amount'] += $lead->balance;
            } elseif ($daysOverdue <= 60) {
                $overdueGroups['31-60']['count']++;
                $overdueGroups['31-60']['amount'] += $lead->balance;
            } elseif ($daysOverdue <= 90) {
                $overdueGroups['61-90']['count']++;
                $overdueGroups['61-90']['amount'] += $lead->balance;
            } else {
                $overdueGroups['91+']['count']++;
                $overdueGroups['91+']['amount'] += $lead->balance;
            }
        }

        // Get detailed lead data
        $leadData = $leads->map(function ($lead) use ($asOfDate) {
            $daysOverdue = $lead->due_date ? $asOfDate->diffInDays(Carbon::parse($lead->due_date), false) : 0;

            return [
                'id' => $lead->id,
                'title' => $lead->title,
                'institution' => $lead->institution_name ?? 'N/A',
                'amount' => $lead->amount,
                'balance' => $lead->balance,
                'waiver_discount' => $lead->waiver_discount,
                'due_date' => $lead->due_date ? Carbon::parse($lead->due_date)->format('Y-m-d') : 'N/A',
                'days_overdue' => $daysOverdue > 0 ? $daysOverdue : 0,
                'assigned_agent' => $lead->assigned_agent_name ?? 'N/A',
            ];
        });

        $data = [
            'as_of_date' => $asOfDate->format('Y-m-d'),
            'total_outstanding' => $totalOutstanding,
            'total_leads' => $totalLeads,
            'overdue_groups' => $overdueGroups,
            'leads' => $leadData
        ];

        if ($request->has('export') && $request->export == 'excel') {
            return Excel::download(new OutstandingDebtExport($data), 'outstanding_debts_report.xlsx');
        }

        return view('reports.outstanding_debts_result', compact('data'));
    }

    public function leadsReport()
    {
        $institutions = Institution::where('is_active', 1)->get();
        $statuses = LeadStatus::where('is_active', 1)->get();
        $priorities = LeadPriority::where('is_active', 1)->get();
        $categories = LeadCategory::where('is_active', 1)->get();

        return view('reports.leads', compact('institutions', 'statuses', 'priorities', 'categories'));
    }

    public function generateLeadsReport(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
        ]);

        // Prepare filters
        $filters = [
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'institution_ids' => $request->institution_ids ? array_filter($request->institution_ids) : [],
            'status_ids' => $request->status_ids ? array_filter($request->status_ids) : [],
            'priority_ids' => $request->priority_ids ? array_filter($request->priority_ids) : [],
            'category_ids' => $request->category_ids ? array_filter($request->category_ids) : [],
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount,
        ];

        // Generate report name
        $reportName = 'Leads Report (' .
                     Carbon::parse($request->from_date)->format('d/m/Y') . ' - ' .
                     Carbon::parse($request->to_date)->format('d/m/Y') . ')';

        // Create background report entry
        $backgroundReport = BackgroundReport::create([
            'report_type' => 'leads_report',
            'report_name' => $reportName,
            'filters' => $filters,
            'status' => 'pending',
            'requested_by' => Auth::id(),
        ]);

        // Dispatch the job
        ProcessLeadsReport::dispatch($backgroundReport);

        return redirect()->route('background-reports.index')
            ->with('success', 'Leads report has been queued for background processing. You will be able to download it once completed.');
    }
}
