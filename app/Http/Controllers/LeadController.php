<?php

namespace App\Http\Controllers;

use App\Exports\LeadsByStatusExport;
use App\Models\Activity;
use App\Models\ActivityStatus;
use App\Models\ActivityType;
use App\Models\AdditionalCostRuleType;
use App\Models\Country;
use App\Models\Currency;
use App\Models\DefaulterType;
use App\Models\Department;
use App\Models\Gender;
use App\Models\Institution;
use App\Models\Lead;
use App\Models\LeadCategory;
use App\Models\LeadConversionStatus;
use App\Models\LeadEngagementLevel;
use App\Models\LeadIndustry;
use App\Models\LeadPriority;
use App\Models\LeadStage;
use App\Models\LeadStatus;
use App\Models\LeadStatusHistory;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use App\Models\Ptp;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\LeadsExport;
use App\Models\CallDisposition;
use App\Models\CallDispositionHistory;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {




    //     if ($request->ajax()) {
    //         $leads = Lead::query()->orderBy('id', 'DESC');

    //         // echo "here";
    //         // exit;

    //         return DataTables::of($leads)
    //             ->addIndexColumn()
    //             ->addColumn('actions', function ($lead) {
    //                 return '
    //                 <a href="/lead/' . $lead->id . '/edit" class="btn btn-warning btn-xs">
    //                     <i class="fa fa-edit"></i>
    //                 </a>
    //                 <a href="#" class="btn btn-danger btn-xs" onclick="confirmDelete(' . $lead->id . ')">
    //                     <i class="fa fa-trash"></i>
    //                 </a>
    //             ';
    //             })
    //             ->rawColumns(['actions'])
    //             ->make(true);
    //     }



    //     return view('lead.index');
    // }


    public function index(Request $request)
    {
        if ($request->ajax()) {


            $user = User::find(Auth::user()->id);
            $leads = Lead::query()->orderBy('id', 'DESC');

            if ($user->hasRole('Agent')) {
                $leads->where(function ($q) use ($user) {
                    $q->where('leads.created_by', $user->id)
                        ->orWhere('leads.assigned_agent', $user->id)
                        ->orWhereNull('leads.assigned_agent');
                });
            }

            return DataTables::of($leads)
                ->addIndexColumn()
                ->addColumn('actions', function ($lead) {
                    return '
                <a href="/lead/' . $lead->id . '/edit" class="btn btn-warning btn-xs">
                    <i class="fa fa-edit"></i>
                </a>
                <a href="#" class="btn btn-danger btn-xs" onclick="confirmDelete(' . $lead->id . ')">
                    <i class="fa fa-trash"></i>
                </a>
            ';
                })
                ->filterColumn('defaulter_types.defaulter_type_name', function ($query, $keyword) {
                    $query->where('defaulter_types.defaulter_type_name', 'like', "%{$keyword}%");
                })
                ->filterColumn('lead_priorities.lead_priority_name', function ($query, $keyword) {
                    $query->where('lead_priorities.lead_priority_name', 'like', "%{$keyword}%");
                })
                ->filterColumn('lead_statuses.lead_status_name', function ($query, $keyword) {
                    $query->where('lead_statuses.lead_status_name', 'like', "%{$keyword}%");
                })
                ->filterColumn('lead_stages.lead_stage_name', function ($query, $keyword) {
                    $query->where('lead_stages.lead_stage_name', 'like', "%{$keyword}%");
                })

                ->filterColumn('institution_name', function ($query, $keyword) {
                    $query->where('institution_name', 'like', "%{$keyword}%");
                })

                ->filterColumn('ptp_amount', function ($query, $keyword) {
                    $query->where('ptp_amount', 'like', "%{$keyword}%");
                })

                ->filterColumn('ptp_expiry_date', function ($query, $keyword) {
                    $query->where('ptp_expiry_date', 'like', "%{$keyword}%");
                })

                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('lead.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        //  dd(DefaulterType::pluck('defaulter_type_name', 'id'));
        return view('lead.create')->with([
            'genders' => Gender::where('is_active', 1)->pluck('gender_name', 'id'),
            'countries' => Country::where('is_active', 1)->pluck('country_name', 'id'),
            'defaulterTypes' => DefaulterType::where('is_active', 1)->pluck('defaulter_type_name', 'id'),
            'individualDefaulterType' => DefaulterType::INDIVIDUAL,
            //for company
            'industries' => LeadIndustry::where('is_active', 1)->pluck('lead_industry_name', 'id'),

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {



        $defaulterType = $request['defaulter_type_store'];

        if ($defaulterType == 1) {
            $this->validate($request, [
                'full_names' => 'required',
                'id_number' => 'required',
                'telephone' => 'required',
                'email' => 'required',
                'gender' => 'required',
            ]);

            $lead = new Lead();

            $lead->defaulter_type_id = $defaulterType;
            $lead->title = $request['full_names'];
            $lead->id_passport_number = $request['id_number'];
            $lead->gender_id = $request['gender'];
            $lead->telephone = $request['telephone'];
            $lead->alternate_telephone = $request['alternate_telephone'];
            $lead->email = $request['email'];
            $lead->address = $request['address'];
            $lead->country_id = $request['country'];
            $lead->town = $request['town'];
            $lead->occupation = $request['occupation'];
            $lead->company_name = $request['company'];
            $lead->kin_full_names = $request['kin_name'];
            $lead->kin_telephone = $request['kin_telephone'];
            $lead->kin_email = $request['kin_email'];
            $lead->kin_relationship = $request['kin_relation'];
            $lead->status_id = LeadStatus::PENDING;
            $lead->stage_id = LeadStage::NEW_LEAD;
            $lead->created_by = Auth::user()->id;
            $lead->updated_by = Auth::user()->id;
            $lead->save();

            $leadID = $lead->id;
        } else {
            $this->validate($request, [
                'entity_name' => 'required',
                'telephone' => 'required',
                'email' => 'required',
                'country' => 'required',
                'town' => 'required',
                'industry' => 'required',
            ]);

            $lead = new Lead();

            $lead->defaulter_type_id = $defaulterType;
            $lead->title = $request['entity_name'];
            $lead->telephone = $request['telephone'];
            $lead->alternate_telephone = $request['alternate_telephone'];
            $lead->email = $request['email'];
            $lead->address = $request['address'];
            $lead->country_id = $request['country'];
            $lead->town = $request['town'];
            $lead->industry_id = $request['industry'];
            $lead->status_id = LeadStatus::PENDING;
            $lead->stage_id = LeadStage::NEW_LEAD;
            $lead->created_by = Auth::user()->id;
            $lead->updated_by = Auth::user()->id;
            $lead->save();
            $leadID = $lead->id;
        }

        return  redirect('/lead/' . $leadID . '/edit?step=2')->with('success', 'Details Saved Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lead $lead)
    {

        return view('lead.show')->with([
            'leadDetails' => Lead::getLeadByID($lead->id),
            'INDIVIDUAL_DEFAULTER_TYPE_CODE' => DefaulterType::INDIVIDUAL,
            'activityTypes' => ActivityType::where('is_active', 1)->orderBy('order', 'ASC')->get(),
            'priorities' => LeadPriority::where('is_active', 1)->pluck('lead_priority_name', 'id'),
            'departments' => Department::where('is_active', 1)->pluck('department_name', 'id'),
            'agentsList' => User::where('is_active', 1)
                ->select(DB::raw("CONCAT(name, ' - ', agent_code) as name"), 'id')
                ->pluck('name', 'id'),
            'activityStatuses' => ActivityStatus::where('is_active', 1)->pluck('activity_status_name', 'id'),
            'leadListActivities' => Activity::getLeadActivities($lead->id),
            'leadTimeLineActivities' => Activity::getLeadInTimeLine($lead->id),

            'payments' => Payment::getLeadPayments($lead->id),
            'leadStages' => LeadStage::where('is_active', 1)->pluck('lead_stage_name', 'id'),
            'leadStatuses' => LeadStatus::where('is_active', 1)->pluck('lead_status_name', 'id'),
            'leadConversionLevels' => LeadConversionStatus::where('is_active', 1)->pluck('lead_conversion_name', 'id'),
            'leadEngagementLevels' => LeadEngagementLevel::where('is_active', 1)->pluck('lead_engagement_level_name', 'id'),
            'leadsStatusHistory' => LeadStatusHistory::getLeadHistory($lead->id),
            'transactionTypes' => TransactionType::where('is_active', 1)->pluck('transaction_type_title', 'id'),
            'paymentStatuses' => TransactionStatus::where('is_active', 1)->whereIn('id', [TransactionStatus::PENDING, TransactionStatus::PAID, TransactionStatus::FAILED, TransactionStatus::CANCELLED])->pluck('status_name', 'id'),
            'paymentMethods' => PaymentMethod::where('is_active', 1)->pluck('method_name', 'id'),
            'costTypes' => AdditionalCostRuleType::where('is_active', 1)->where('id', '!=', 5)->pluck('rule_type_name', 'id'),
            'callDispositions' => CallDisposition::where('is_active', 1)->pluck('call_disposition_name', 'id'),

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lead $lead, Request $request)
    {

        $loggedInUserId = auth()->id();
        $agentsList = User::where('is_active', 1)
            ->select(DB::raw("CONCAT(name, ' - ', agent_code) as name"), 'id')
            ->pluck('name', 'id')
            ->toArray();

        // Add "Me" item
        $agentsList = [$loggedInUserId => "Myself"] + $agentsList;

        // dd($agentsList);
        return view('lead.edit')->with([
            'step' => $request['step'],
            'lead' => $lead,
            'genders' => Gender::where('is_active', 1)->pluck('gender_name', 'id'),
            'countries' => Country::where('is_active', 1)->pluck('country_name', 'id'),
            'defaulterTypes' => DefaulterType::where('is_active', 1)->pluck('defaulter_type_name', 'id'),
            'individualDefaulterType' => DefaulterType::INDIVIDUAL,
            'leadCategories' => LeadCategory::Where('is_active', 1)->pluck('lead_category_name', 'id'),
            'institutions' => Institution::Where('is_active', 1)->pluck('institution_name', 'id'),
            'currencies' => Currency::Where('is_active', 1)->pluck('currency_name', 'id'),
            'priorities' => LeadPriority::where('is_active', 1)
                ->select(DB::raw("CONCAT(lead_priority_name, ' - ', description) as name"), 'id')
                ->pluck('name', 'id'),
            'industries' => LeadIndustry::where('is_active', 1)->pluck('lead_industry_name', 'id'),
            'agentsList' => $agentsList,
            'departments' => Department::where('is_active', 1)->pluck('department_name', 'id')

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lead $lead)
    {
        $step = $request->input('step');
        $defaulterType = $lead->defaulter_type_id;



        switch ($step) {
            case 2:

                $validateItems = [
                    'amount' => 'required',
                    'balance' => 'required',
                    'currency' => 'required',
                    'due_date' => 'required',
                    'category' => 'required',
                    'priority' => 'required',
                    'agent' => 'required',
                ];
                if ($defaulterType == DefaulterType::INDIVIDUAL) {
                    $validateItems['institution'] = 'required';
                }
                $this->validate($request, $validateItems);

                $lead->amount = $request['amount'];
                $lead->balance = $request['balance'];
                $lead->account_number = $request['account_number'];
                $lead->currency_id = $request['currency'];
                if ($defaulterType == DefaulterType::INDIVIDUAL) {
                    $lead->institution_id = $request['institution'];
                }

                $lead->due_date = Carbon::parse($request['due_date'])->format("Y-m-d");
                $lead->category_id = $request['category'];
                $lead->priority_id = $request['priority'];
                $lead->assigned_agent = !empty($request['agent']) ? $request['agent'] : Auth::user()->id;
                $lead->assigned_department = $request['department'];
                $lead->updated_by = Auth::user()->id;
                $lead->save();

                return  redirect('/lead/' . $lead->id . '/edit?step=2')->with('success', 'Details Saved Successfully');

                break;

            default:

                if ($defaulterType == DefaulterType::INDIVIDUAL) {
                    $this->validate($request, [
                        'full_names' => 'required',
                        'id_number' => 'required',
                        'telephone' => 'required',
                        'email' => 'required',
                        'gender' => 'required',
                    ]);

                    $lead->defaulter_type_id = $defaulterType;
                    $lead->title = $request['full_names'];
                    $lead->id_passport_number = $request['id_number'];
                    $lead->gender_id = $request['gender'];
                    $lead->telephone = $request['telephone'];
                    $lead->alternate_telephone = $request['alternate_telephone'];
                    $lead->email = $request['email'];
                    $lead->address = $request['address'];
                    $lead->country_id = $request['country'];
                    $lead->town = $request['town'];
                    $lead->occupation = $request['occupation'];
                    $lead->company_name = $request['company'];
                    $lead->kin_full_names = $request['kin_name'];
                    $lead->kin_telephone = $request['kin_telephone'];
                    $lead->kin_email = $request['kin_email'];
                    $lead->kin_relationship = $request['kin_relation'];
                    $lead->updated_by = Auth::user()->id;
                    $lead->save();
                } else {


                    $this->validate($request, [
                        'entity_name' => 'required',
                        'telephone' => 'required',
                        'email' => 'required',
                        'country' => 'required',
                        'town' => 'required',
                        'industry' => 'required',
                    ]);


                    $lead->defaulter_type_id = $defaulterType;
                    $lead->title = $request['entity_name'];
                    $lead->telephone = $request['telephone'];
                    $lead->alternate_telephone = $request['alternate_telephone'];
                    $lead->email = $request['email'];
                    $lead->address = $request['address'];
                    $lead->country_id = $request['country'];
                    $lead->town = $request['town'];
                    $lead->industry_id = $request['industry'];
                    $lead->updated_by = Auth::user()->id;
                    $lead->save();
                }


                return  redirect('/lead/' . $lead->id . '/edit')->with('success', 'Details Saved Successfully');

                break;
        }
    }


    public function updateStatus(Request $request)
    {
        $this->validate($request, [
            'lead_stage' => 'required',
            'lead_status' => 'required',
            'lead_conversion' => 'required',
            'lead_engagement' => 'required',
        ]);


        Lead::where('id', $request['leadID'])->update([
            'stage_id' => $request['lead_stage'],
            'status_id' => $request['lead_status'],
            'conversion_status_id' => $request['lead_conversion'],
            'engagement_level_id' => $request['lead_engagement'],
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);


        $leadHistory = new LeadStatusHistory();
        $leadHistory->lead_id = $request['leadID'];
        $leadHistory->lead_status_id = $request['lead_status'];
        $leadHistory->lead_stage_id = $request['lead_stage'];
        $leadHistory->lead_conversion_id = $request['lead_conversion'];
        $leadHistory->lead_engagement_level = $request['lead_engagement'];
        $leadHistory->description = $request['description'];
        $leadHistory->created_by = Auth::user()->id;
        $leadHistory->updated_by = Auth::user()->id;
        $leadHistory->save();

        return redirect('/lead/' . $request['leadID'] . '?section=status')->with('success', 'Status Saved Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lead $lead)
    {
        //
    }


    public function leadByStatus($status)
    {

        return view('lead.by_status')->with([
            'status' => $status // Pass status to the view
        ]);
    }



    public function leadByStatusData(Request $request)
    {
        $status = $request->input('status'); // Retrieve status from request

        $leads = Lead::query()
            ->where('status_id', $status) // Apply filter
            ->orderBy('id', 'DESC');


        $user = User::find(Auth::user()->id);

        if ($user->hasRole('Agent')) {
            $leads->where(function ($q) use ($user) {
                $q->where('leads.created_by', $user->id)
                    ->orWhere('leads.assigned_agent', $user->id)
                    ->orWhereNull('leads.assigned_agent');
            });
        }

        return DataTables::of($leads)
            ->addIndexColumn()
            ->addColumn('actions', function ($lead) {
                return '
                <a href="/lead/' . $lead->id . '/edit" class="btn btn-warning btn-xs">
                    <i class="fa fa-edit"></i>
                </a>
                <a href="#" class="btn btn-danger btn-xs" onclick="confirmDelete(' . $lead->id . ')">
                    <i class="fa fa-trash"></i>
                </a>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }




    public function storePtp(Request $request)
    {
        $this->validate($request, [
            'leadID' => 'required',
            'date' => 'required',
            'ptp_amount' => 'required',
        ]);



        $ptp = new Ptp();
        $ptp->lead_id = $request['leadID'];
        $ptp->ptp_date = Carbon::parse($request['date'])->format('Y-m-d');
        $ptp->ptp_amount = $request['ptp_amount'];
        $ptp->ptp_expiry_date = Carbon::parse($request['date'])->addDay()->format('Y-m-d');
        $ptp->description = $request['ptp_description'];
        $ptp->created_by = Auth::user()->id;
        $ptp->updated_by = Auth::user()->id;
        $ptp->save();


        $lead = Lead::find($request['leadID']);
        $lead->last_ptp_id = $ptp->id;
        $lead->updated_by = Auth::user()->id;
        $lead->save();

        return  back()->with('success', 'Details Saved Successfully');

        // return redirect('/lead/' . $lead->id . '/edit')->with('success', 'PTP Saved Successfully');
    }


    public function getPtps(Request $request)
    {
        if ($request->ajax()) {
            $ptps = Ptp::leftJoin('users', 'ptps.created_by', '=', 'users.id')
                ->leftJoin('users as updated_users', 'ptps.updated_by', '=', 'updated_users.id')
                ->select(
                    'ptps.id',
                    'ptps.lead_id',
                    'ptps.ptp_date',
                    'ptps.ptp_amount',
                    'ptps.ptp_expiry_date',
                    'ptps.created_by',
                    'ptps.updated_by',
                    'users.name as created_by_name',
                    'updated_users.name as updated_by_name',
                    'ptps.created_at',
                    'ptps.updated_at'
                )
                ->where('ptps.lead_id', $request['lead_id'])
                ->orderBy('ptps.id', 'DESC');

            return DataTables::of($ptps)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s'); // Format date
                })
                ->editColumn('ptp_date', function ($row) {
                    return $row->ptp_date ? date('Y-m-d', strtotime($row->ptp_date)) : '';
                })
                ->editColumn('ptp_expiry_date', function ($row) {
                    return $row->ptp_expiry_date ? date('Y-m-d', strtotime($row->ptp_expiry_date)) : '';
                })
                ->editColumn('ptp_amount', function ($row) {
                    return number_format($row->ptp_amount, 2);
                })
                ->make(true);
        }
    }


    public function deletePtp($id)
    {

        Ptp::find($id)->delete();
        return redirect()->back()->with('success', 'PTP deleted successfully');
    }


    public function export()
    {
        return Excel::download(new LeadsExport, 'leads-' . date('Y-m-d') . '.xlsx');
    }


    public function exportByStatus($status)
    {
        return Excel::download(new LeadsByStatusExport($status), 'leads-status-' . $status . '-' . date('Y-m-d') . '.xlsx');
    }



    public function storeCallDisposition(Request $request)
    {
        $this->validate($request, [
            'leadID' => 'required',
            'call_disposition' => 'required',
        ]);

        $lead = Lead::find($request['leadID']);
        $lead->call_disposition_id = $request['call_disposition'];
        $lead->updated_by = Auth::user()->id;
        $lead->save();


        $callDispositionHistory = new CallDispositionHistory();
        $callDispositionHistory->lead_id = $request['leadID'];
        $callDispositionHistory->call_disposition_id = $request['call_disposition'];
        $callDispositionHistory->description = $request['call_deposition_description'];
        $callDispositionHistory->created_by = Auth::user()->id;
        $callDispositionHistory->updated_by = Auth::user()->id;
        $callDispositionHistory->save();


        return redirect()->back()->with('success', 'Call Disposition updated successfully');
    }


    public function getCallDispositionsData(Request $request)
    {
        $leadId = $request->input('lead_id');

        $query = DB::table('call_disposition_histories')
            ->join('call_dispositions', 'call_disposition_histories.call_disposition_id', '=', 'call_dispositions.id')
            ->join('users', 'call_disposition_histories.created_by', '=', 'users.id')
            ->select([
                'call_disposition_histories.id',
                'call_dispositions.call_disposition_name',
                'call_disposition_histories.description',
                'call_disposition_histories.created_at',
                'users.name as created_by_name',
                'call_disposition_histories.lead_id'
            ])
            ->where('call_disposition_histories.lead_id', $leadId)
            ->orderBy('call_disposition_histories.created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return $row->created_at;
            })
            ->editColumn('call_disposition_name', function ($row) {
                return $row->call_disposition_name ?? 'N/A';
            })
            ->editColumn('created_by_name', function ($row) {
                return $row->created_by_name ?? 'N/A';
            })
            ->filterColumn('call_disposition_name', function ($query, $keyword) {
                $query->where('call_dispositions.call_disposition_name', 'like', "%{$keyword}%");
            })
            ->filterColumn('created_by_name', function ($query, $keyword) {
                $query->where('users.name', 'like', "%{$keyword}%");
            })
            ->rawColumns([])
            ->make(true);
    }



    public function myLeads(Request $request)

    {



        $user = User::find(Auth::user()->id);

        if ($request->ajax()) {
            $leads = Lead::query()->orderBy('id', 'DESC');

            if ($user->hasRole('Agent')) {
                $leads->where('leads.assigned_agent', $user->id);
            }

            return DataTables::of($leads)
                ->addIndexColumn()
                ->addColumn('actions', function ($lead) {
                    return '
                <a href="/lead/' . $lead->id . '/edit" class="btn btn-warning btn-xs">
                    <i class="fa fa-edit"></i>
                </a>
                <a href="#" class="btn btn-danger btn-xs" onclick="confirmDelete(' . $lead->id . ')">
                    <i class="fa fa-trash"></i>
                </a>
            ';
                })
                ->filterColumn('defaulter_types.defaulter_type_name', function ($query, $keyword) {
                    $query->where('defaulter_types.defaulter_type_name', 'like', "%{$keyword}%");
                })
                ->filterColumn('lead_priorities.lead_priority_name', function ($query, $keyword) {
                    $query->where('lead_priorities.lead_priority_name', 'like', "%{$keyword}%");
                })
                ->filterColumn('lead_statuses.lead_status_name', function ($query, $keyword) {
                    $query->where('lead_statuses.lead_status_name', 'like', "%{$keyword}%");
                })
                ->filterColumn('lead_stages.lead_stage_name', function ($query, $keyword) {
                    $query->where('lead_stages.lead_stage_name', 'like', "%{$keyword}%");
                })

                ->filterColumn('institution_name', function ($query, $keyword) {
                    $query->where('institution_name', 'like', "%{$keyword}%");
                })

                ->filterColumn('ptp_amount', function ($query, $keyword) {
                    $query->where('ptp_amount', 'like', "%{$keyword}%");
                })

                ->filterColumn('ptp_expiry_date', function ($query, $keyword) {
                    $query->where('ptp_expiry_date', 'like', "%{$keyword}%");
                })

                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('lead.my_leads');
    }
}
