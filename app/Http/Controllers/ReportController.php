<?php

namespace App\Http\Controllers;

use App\Exports\AdminAgentPerformanceExport;
use App\Exports\AgentLeadsExport;
use App\Exports\AgentPerformanceExport;
use App\Exports\CollectionProgressExport;
use App\Exports\CollectionRateExport;
use App\Exports\DispositionsReportExport;
use App\Exports\OutstandingDebtExport;
use App\Exports\LeadsReportExport;
use App\Jobs\ProcessLeadsReport;
use App\Models\Activity;
use App\Models\Institution;
use App\Models\Lead;
use App\Models\LeadCategory;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\TransactionStatus;
use App\Models\BackgroundReport;
use App\Models\LeadStatus;
use App\Models\LeadPriority;
use App\Models\User;
use App\Models\Ptp;
use App\Models\Mtb;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public function paymentReport()
    {
        // Only allow Admin users to access payment reports
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access. Only administrators can access payment reports.');
        }

        $institutions = Institution::where('is_active', 1)->get();
        $agents = \App\Models\User::where('is_active', 1)->get();

        return view('reports.payment_report', compact('institutions', 'agents'));
    }

    public function generatePaymentReport(Request $request)
    {
        // Only allow Admin users to generate payment reports
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access. Only administrators can generate payment reports.');
        }

        $request->validate([
            'from_date' => 'required|date_format:d-m-Y',
            'to_date' => 'required|date_format:d-m-Y|after_or_equal:from_date',
        ]);

        $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
        $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();

        // Base query for transactions (payments) with related data
        $paymentsQuery = Transaction::select([
            'transactions.*',
            'leads.title as lead_name',
            'leads.id as ticket_number',
            'leads.amount as lead_amount',
            'leads.balance as lead_balance',
            'institutions.institution_name',
            'users.name as agent_name',
            'users.agent_code',
            'transaction_types.transaction_type_title',
            'transaction_statuses.status_name as payment_status_name'
        ])
            ->leftJoin('leads', 'transactions.lead_id', '=', 'leads.id')
            ->leftJoin('institutions', 'leads.institution_id', '=', 'institutions.id')
            ->leftJoin('users', 'leads.assigned_agent', '=', 'users.id')
            ->leftJoin('transaction_types', 'transactions.transaction_type', '=', 'transaction_types.id')
            ->leftJoin('transaction_statuses', 'transactions.status_id', '=', 'transaction_statuses.id')
            ->where('transactions.transaction_type', TransactionType::PAYMENT)
            ->whereBetween('transactions.created_at', [$fromDate, $toDate]);

        // Apply filters
        if ($request->filled('institution_id') && $request->institution_id != '') {
            $paymentsQuery->where('leads.institution_id', $request->institution_id);
        }

        if ($request->filled('agent_id') && $request->agent_id != '') {
            $paymentsQuery->where('leads.assigned_agent', $request->agent_id);
        }

        if ($request->filled('payment_status') && $request->payment_status != '') {
            $paymentsQuery->where('transactions.status_id', $request->payment_status);
        }

        $payments = $paymentsQuery->orderBy('transactions.created_at', 'desc')->get();

        // Get MTD (Money Transfer Data) records from the mtbs table
        $mtdQuery = DB::table('mtbs')
            ->select([
                'mtbs.*',
                'leads.title as lead_name',
                'leads.id as ticket_number',
                'leads.amount as lead_amount',
                'leads.balance as lead_balance',
                'institutions.institution_name',
                'users.name as agent_name',
                'users.agent_code',
                'created_user.name as created_by_name'
            ])
            ->leftJoin('leads', 'mtbs.lead_id', '=', 'leads.id')
            ->leftJoin('institutions', 'leads.institution_id', '=', 'institutions.id')
            ->leftJoin('users', 'leads.assigned_agent', '=', 'users.id')
            ->leftJoin('users as created_user', 'mtbs.created_by', '=', 'created_user.id')
            ->whereBetween('mtbs.date_paid', [$fromDate->toDateString(), $toDate->toDateString()]);

        // Apply same filters to MTD
        if ($request->filled('institution_id') && $request->institution_id != '') {
            $mtdQuery->where('leads.institution_id', $request->institution_id);
        }

        if ($request->filled('agent_id') && $request->agent_id != '') {
            $mtdQuery->where('leads.assigned_agent', $request->agent_id);
        }

        $mtdRecords = $mtdQuery->orderBy('mtbs.date_paid', 'desc')->get();

        // Calculate summary data
        $totalPayments = $payments->count();
        $totalAmount = $payments->sum('amount');
        $avgPaymentAmount = $totalPayments > 0 ? $totalAmount / $totalPayments : 0;

        // Calculate MTD summary
        $totalMtdRecords = $mtdRecords->count();
        $totalMtdAmount = $mtdRecords->sum('amount_paid');
        $avgMtdAmount = $totalMtdRecords > 0 ? $totalMtdAmount / $totalMtdRecords : 0;

        // Group by institution
        $institutionSummary = $payments->groupBy('institution_name')->map(function ($institutionPayments) {
            return [
                'count' => $institutionPayments->count(),
                'total_amount' => $institutionPayments->sum('amount'),
                'avg_amount' => $institutionPayments->avg('amount')
            ];
        });

        // Group by agent
        $agentSummary = $payments->groupBy('agent_name')->map(function ($agentPayments) {
            return [
                'count' => $agentPayments->count(),
                'total_amount' => $agentPayments->sum('amount'),
                'avg_amount' => $agentPayments->avg('amount')
            ];
        });

        // Group by agent for MTD
        $mtdAgentSummary = $mtdRecords->groupBy('agent_name')->map(function ($mtdAgentRecords) {
            return [
                'count' => $mtdAgentRecords->count(),
                'total_amount' => $mtdAgentRecords->sum('amount_paid'),
                'avg_amount' => $mtdAgentRecords->avg('amount_paid')
            ];
        });

        // Group by date
        $dailySummary = $payments->groupBy(function ($payment) {
            return Carbon::parse($payment->created_at)->format('Y-m-d');
        })->map(function ($dayPayments) {
            return [
                'count' => $dayPayments->count(),
                'total_amount' => $dayPayments->sum('amount')
            ];
        });

        $data = [
            'payments' => $payments,
            'mtd_records' => $mtdRecords,
            'filters' => [
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'institution_id' => $request->institution_id ?? '',
                'agent_id' => $request->agent_id ?? '',
                'payment_status' => $request->payment_status ?? ''
            ],
            'summary' => [
                'total_payments' => $totalPayments,
                'total_amount' => $totalAmount,
                'avg_payment_amount' => $avgPaymentAmount,
                'total_mtd_records' => $totalMtdRecords,
                'total_mtd_amount' => $totalMtdAmount,
                'avg_mtd_amount' => $avgMtdAmount,
                'institution_summary' => $institutionSummary,
                'agent_summary' => $agentSummary,
                'mtd_agent_summary' => $mtdAgentSummary,
                'daily_summary' => $dailySummary
            ]
        ];

        if ($request->has('export') && $request->export == 'excel') {
            return Excel::download(new \App\Exports\PaymentReportExport($data), 'payment_report_' . date('Y-m-d') . '.xlsx');
        }

        return view('reports.payment_report_result', compact('data'));
    }

    public function ptpReport()
    {
        // Only allow Admin users to access PTP reports
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access. Only administrators can access PTP reports.');
        }

        $institutions = Institution::where('is_active', 1)->get();
        $agents = \App\Models\User::where('is_active', 1)->get();

        return view('reports.ptp_report', compact('institutions', 'agents'));
    }

    public function generatePTPReport(Request $request)
    {
        // Only allow Admin users to generate PTP reports
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access. Only administrators can generate PTP reports.');
        }

        $request->validate([
            'from_date' => 'required|date_format:d-m-Y',
            'to_date' => 'required|date_format:d-m-Y|after_or_equal:from_date',
        ]);

        $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
        $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();

        // Base query for PTPs with related data
        $ptpQuery = Activity::select([
            'activities.id as activity_id',
            'activities.act_ptp_date',
            'activities.act_ptp_amount',
            'activities.created_at as ptp_created_date',
            'leads.id as ticket_number',
            'leads.title as lead_title',
            'leads.amount as lead_amount',
            'leads.balance as lead_balance',
            'institutions.institution_name',
            'assigned_agents.name as assigned_agent_name',
            'assigned_agents.agent_code as assigned_agent_code',
            'created_by_users.name as created_by_name',
            'created_by_users.agent_code as created_by_code'
        ])
            ->leftJoin('leads', 'activities.lead_id', '=', 'leads.id')
            ->leftJoin('institutions', 'leads.institution_id', '=', 'institutions.id')
            ->leftJoin('users as assigned_agents', 'leads.assigned_agent', '=', 'assigned_agents.id')
            ->leftJoin('users as created_by_users', 'activities.created_by', '=', 'created_by_users.id')
            ->whereNotNull('activities.act_ptp_date')
            ->whereNotNull('activities.act_ptp_amount');

        // Apply date range filter based on filter type
        $filterType = $request->input('date_filter_type', 'created'); // 'created' or 'due'

        if ($filterType === 'due') {
            // Filter by PTP due date
            $ptpQuery->whereBetween('activities.act_ptp_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')]);
        } else {
            // Filter by PTP created date (default)
            $ptpQuery->whereBetween('activities.created_at', [$fromDate, $toDate]);
        }

        // Apply other filters
        if ($request->filled('institution_id') && $request->institution_id != '') {
            $ptpQuery->where('leads.institution_id', $request->institution_id);
        }

        if ($request->filled('agent_id') && $request->agent_id != '') {
            $ptpQuery->where('leads.assigned_agent', $request->agent_id);
        }

        if ($request->filled('created_by_agent') && $request->created_by_agent != '') {
            $ptpQuery->where('activities.created_by', $request->created_by_agent);
        }

        // Filter by PTP due date range if specified
        if ($request->filled('ptp_due_from') && $request->filled('ptp_due_to')) {
            $ptpDueFrom = Carbon::createFromFormat('d-m-Y', $request->ptp_due_from)->format('Y-m-d');
            $ptpDueTo = Carbon::createFromFormat('d-m-Y', $request->ptp_due_to)->format('Y-m-d');
            $ptpQuery->whereBetween('activities.act_ptp_date', [$ptpDueFrom, $ptpDueTo]);
        }

        $ptps = $ptpQuery->orderBy('activities.created_at', 'desc')->get();

        // Calculate summary data
        $totalPTPs = $ptps->count();
        $totalAmount = $ptps->sum('act_ptp_amount');
        $avgPTPAmount = $totalPTPs > 0 ? $totalAmount / $totalPTPs : 0;

        // Group by institution
        $institutionSummary = $ptps->groupBy('institution_name')->map(function ($institutionPTPs) {
            return [
                'count' => $institutionPTPs->count(),
                'total_amount' => $institutionPTPs->sum('act_ptp_amount'),
                'avg_amount' => $institutionPTPs->avg('act_ptp_amount')
            ];
        });

        // Group by agent
        $agentSummary = $ptps->groupBy('assigned_agent_name')->map(function ($agentPTPs) {
            return [
                'count' => $agentPTPs->count(),
                'total_amount' => $agentPTPs->sum('act_ptp_amount'),
                'avg_amount' => $agentPTPs->avg('act_ptp_amount')
            ];
        });

        // Group by created by
        $createdBySummary = $ptps->groupBy('created_by_name')->map(function ($createdByPTPs) {
            return [
                'count' => $createdByPTPs->count(),
                'total_amount' => $createdByPTPs->sum('act_ptp_amount'),
                'avg_amount' => $createdByPTPs->avg('act_ptp_amount')
            ];
        });

        // Group by due date
        $dueDateSummary = $ptps->groupBy(function ($ptp) {
            return Carbon::parse($ptp->act_ptp_date)->format('Y-m-d');
        })->map(function ($datePTPs) {
            return [
                'count' => $datePTPs->count(),
                'total_amount' => $datePTPs->sum('act_ptp_amount')
            ];
        });

        // Calculate overdue PTPs
        $today = Carbon::today();
        $overduePTPs = $ptps->filter(function ($ptp) use ($today) {
            return Carbon::parse($ptp->act_ptp_date)->lt($today);
        });

        $data = [
            'ptps' => $ptps,
            'filters' => [
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'institution_id' => $request->institution_id ?? '',
                'agent_id' => $request->agent_id ?? '',
                'created_by_agent' => $request->created_by_agent ?? '',
                'date_filter_type' => $filterType,
                'ptp_due_from' => $request->ptp_due_from ?? '',
                'ptp_due_to' => $request->ptp_due_to ?? ''
            ],
            'summary' => [
                'total_ptps' => $totalPTPs,
                'total_amount' => $totalAmount,
                'avg_ptp_amount' => $avgPTPAmount,
                'overdue_ptps' => $overduePTPs->count(),
                'overdue_amount' => $overduePTPs->sum('act_ptp_amount'),
                'institution_summary' => $institutionSummary,
                'agent_summary' => $agentSummary,
                'created_by_summary' => $createdBySummary,
                'due_date_summary' => $dueDateSummary
            ]
        ];

        if ($request->has('export') && $request->export == 'excel') {
            return Excel::download(new \App\Exports\PTPReportExport($data), 'ptp_report_' . date('Y-m-d') . '.xlsx');
        }

        return view('reports.ptp_report_result', compact('data'));
    }

    public function adminAgentPerformance()
    {
        $institutions = Institution::where('is_active', 1)->get();
        $agents = User::where('is_active', 1)->orderBy('name')->get();
        return view('reports.admin_agent_performance', compact('institutions', 'agents'));
    }

    public function generateAdminAgentPerformance(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);
        $dateFrom = Carbon::parse($request->date_from)->startOfDay();
        $dateTo = Carbon::parse($request->date_to)->endOfDay();
        $monthStart = Carbon::parse($request->date_from)->startOfMonth()->startOfDay();
        $monthEnd = Carbon::parse($request->date_from)->endOfMonth()->endOfDay();

        $institutionId = $request->institution_id ?? null;
        $agentId = $request->agent_id ?? null;
        $createdByAgent = $request->created_by_agent ?? null;
        // Get agents (either all, or filtered by created_by, or specific agent)
        if ($createdByAgent) {
            // Get agents who created records by this supervisor
            $agentIds = Activity::where('created_by', $createdByAgent)
                ->distinct()
                ->pluck('created_by');
            $agentsQuery = User::whereIn('id', $agentIds)->where('is_active', 1);
        } elseif ($agentId) {
            $agentsQuery = User::where('id', $agentId);
        } else {
            // Get all users who have created activities (not just those in users table)
            $agentIds = Activity::distinct()->pluck('created_by');
            $agentsQuery = User::whereIn('id', $agentIds)->where('is_active', 1);
        }

        $agents = $agentsQuery->get();
        $reportData = [];

        foreach ($agents as $agent) {
            // 1. Agent name
            $agentName = $agent->name;

            // 2. Leads worked - count distinct leads from activities created by this agent
            $leadsWorkedQuery = Activity::where('created_by', $agent->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->distinct();
            if ($institutionId) {
                $leadsWorkedQuery->whereHas('lead', function ($q) use ($institutionId) {
                    $q->where('institution_id', $institutionId);
                });
            }
            $leadsWorkedCount = $leadsWorkedQuery->distinct('lead_id')->count('lead_id');

            // 3. Negotiation in progress - leads with activities having disposition=4
            $negotiationQuery = Activity::where('created_by', $agent->id)
                ->where('act_call_disposition_id', 4)
                ->distinct();
            if ($institutionId) {
                $negotiationQuery->whereHas('lead', function ($q) use ($institutionId) {
                    $q->where('institution_id', $institutionId);
                });
            }
            $negotiationCount = $negotiationQuery->distinct('lead_id')->count('lead_id');

            // 4. Promised to pay - PTPs created TODAY by this agent
            $ptpsCreatedTodayQuery = Ptp::where('created_by', $agent->id)
                ->whereDate('created_at', Carbon::today());
            if ($institutionId) {
                $ptpsCreatedTodayQuery->whereHas('lead', function ($q) use ($institutionId) {
                    $q->where('institution_id', $institutionId);
                });
            }
            $ptpsCreatedTodayCount = $ptpsCreatedTodayQuery->count();

            // 5. Good Leads = Negotiation + PTP
            $goodLeads = $negotiationCount + $ptpsCreatedTodayCount;

            // 6. Right Party PTP Today - activities with disposition=4 and PTP due today
            // 6. Right Party PTP Today - activities with disposition=3 due today
            $rightPartyPtpCountQuery = Activity::where('created_by', $agent->id)
                ->where('act_call_disposition_id', 3)
                ->whereDate('act_ptp_date', Carbon::today());
            $rightPartyPtpValueQuery = Activity::where('created_by', $agent->id)
                ->where('act_call_disposition_id', 3)
                ->whereDate('act_ptp_date', Carbon::today());

            if ($institutionId) {
                $rightPartyPtpCountQuery->whereHas('lead', function ($q) use ($institutionId) {
                    $q->where('institution_id', $institutionId);
                });
                $rightPartyPtpValueQuery->whereHas('lead', function ($q) use ($institutionId) {
                    $q->where('institution_id', $institutionId);
                });
            }

            $rightPartyPtpCount = $rightPartyPtpCountQuery->count();
            $rightPartyPtpValue = $rightPartyPtpValueQuery->sum('act_ptp_amount') ?? 0;

            // 8. PTP for the month - count and value (activities with disposition=3)
            $ptpMonthCountQuery = Activity::where('created_by', $agent->id)
                ->where('act_call_disposition_id', 3)
                ->whereBetween('created_at', [$monthStart, $monthEnd]);
            $ptpMonthValueQuery = Activity::where('created_by', $agent->id)
                ->where('act_call_disposition_id', 3)
                ->whereBetween('created_at', [$monthStart, $monthEnd]);

            if ($institutionId) {
                $ptpMonthCountQuery->whereHas('lead', function ($q) use ($institutionId) {
                    $q->where('institution_id', $institutionId);
                });
                $ptpMonthValueQuery->whereHas('lead', function ($q) use ($institutionId) {
                    $q->where('institution_id', $institutionId);
                });
            }

            $ptpMonthCount = $ptpMonthCountQuery->count();
            $ptpMonthValue = $ptpMonthValueQuery->sum('act_ptp_amount') ?? 0;

            // 10. MTD Today count and value
            $mtdTodayCountQuery = Mtb::where('created_by', $agent->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo]);
            $mtdTodayValueQuery = Mtb::where('created_by', $agent->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo]);
            if ($institutionId) {
                $mtdTodayCountQuery->whereHas('lead', function ($q) use ($institutionId) {
                    $q->where('institution_id', $institutionId);
                });
                $mtdTodayValueQuery->whereHas('lead', function ($q) use ($institutionId) {
                    $q->where('institution_id', $institutionId);
                });
            }
            $mtdTodayCount = $mtdTodayCountQuery->count();
            $mtdTodayValue = $mtdTodayValueQuery->sum('amount_paid') ?? 0;

            // 12. MTD Monthly
            $mtdMonthQuery = Mtb::where('created_by', $agent->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd]);
            if ($institutionId) {
                $mtdMonthQuery->whereHas('lead', function ($q) use ($institutionId) {
                    $q->where('institution_id', $institutionId);
                });
            }
            $mtdMonthValue = $mtdMonthQuery->sum('amount_paid') ?? 0;

            // 13. Payments Posted So far (for the month)
            $paymentsQuery = Transaction::where('created_by', $agent->id)
                ->where('transaction_type', TransactionType::PAYMENT)
                ->whereBetween('created_at', [$monthStart, $monthEnd]);
            if ($institutionId) {
                $paymentsQuery->whereHas('lead', function ($q) use ($institutionId) {
                    $q->where('institution_id', $institutionId);
                });
            }
            $paymentsPostedValue = $paymentsQuery->sum('amount') ?? 0;

            $reportData[] = [
                'agent_name' => $agentName,
                'agent_id' => $agent->id,
                'leads_worked' => $leadsWorkedCount,
                'negotiation_in_progress' => $negotiationCount,
                'ptp_created_today' => $ptpsCreatedTodayCount,
                'good_leads' => $goodLeads,
                'right_party_ptp_count' => $rightPartyPtpCount,
                'right_party_ptp_value' => $rightPartyPtpValue,
                'ptp_month_count' => $ptpMonthCount,
                'ptp_month_value' => $ptpMonthValue,
                'mtd_today_count' => $mtdTodayCount,
                'mtd_today_value' => $mtdTodayValue,
                'mtd_month_value' => $mtdMonthValue,
                'payments_posted_value' => $paymentsPostedValue
            ];
        }

        $data = [
            'report_data' => $reportData,
            'filters' => [
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'institution_id' => $institutionId,
                'agent_id' => $agentId,
                'created_by_agent' => $createdByAgent,
            ]
        ];

        if ($request->has('export') && $request->export == 'excel') {
            return Excel::download(
                new AdminAgentPerformanceExport($reportData),
                'admin_agent_performance_' . date('Y-m-d') . '.xlsx'
            );
        }

        return view('reports.admin_agent_performance_result', compact('data'));
    }

    public function dispositionsReport()
    {
        $institutions = Institution::where('is_active', 1)->get();
        $users = User::where('is_active', 1)->get();
        return view('reports.dispositions', compact('institutions', 'users'));
    }

    public function generateDispositionsReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'agent_id' => 'nullable|exists:users,id',
            'institution_id' => 'nullable|exists:institutions,id',
        ]);

        $dateStart = Carbon::parse($request->start_date)->startOfDay();
        $dateEnd = Carbon::parse($request->end_date)->endOfDay();

        // Get all dispositions
        $dispositions = DB::table('call_dispositions')
            ->where('is_active', 1)
            ->orderBy('id')
            ->get();

        // Get all institutions
        $institutions = Institution::where('is_active', 1)
            ->orderBy('institution_name')
            ->get();

        // Build the report data as a pivot table
        $reportData = [];

        foreach ($dispositions as $disposition) {
            $row = [
                'disposition_id' => $disposition->id,
                'disposition_name' => $disposition->call_disposition_name,
                'total' => 0
            ];

            foreach ($institutions as $institution) {
                $query = Lead::where('call_disposition_id', $disposition->id)
                    ->where('institution_id', $institution->id)
                    ->whereBetween('updated_at', [$dateStart, $dateEnd]);

                // Apply optional filters
                if ($request->filled('agent_id')) {
                    // Filter by assigned agent or agent who created the lead
                    $query->where(function ($q) use ($request) {
                        $q->where('assigned_agent', $request->agent_id)
                            ->orWhere('created_by', $request->agent_id);
                    });
                }

                if ($request->filled('institution_id')) {
                    $query->where('institution_id', $request->institution_id);
                }

                $count = $query->count();
                $row[$institution->id] = $count;
                $row['total'] += $count;
            }

            $reportData[] = $row;
        }

        $data = [
            'start_date' => $dateStart->format('Y-m-d'),
            'end_date' => $dateEnd->format('Y-m-d'),
            'dispositions' => $dispositions,
            'institutions' => $institutions,
            'report_data' => $reportData,
            'filters' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'agent_id' => $request->agent_id,
                'institution_id' => $request->institution_id,
            ]
        ];

        if ($request->has('export') && $request->export == 'excel') {
            return Excel::download(
                new DispositionsReportExport($data),
                'dispositions_report_' . $dateStart->format('Y-m-d') . '_to_' . $dateEnd->format('Y-m-d') . '.xlsx'
            );
        }

        return view('reports.dispositions_result', compact('data'));
    }
}
