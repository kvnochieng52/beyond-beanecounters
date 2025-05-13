<?php

namespace App\Http\Controllers;

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
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        if ($request->ajax()) {
            $leads = Lead::query()->orderBy('id', 'DESC');

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
        $agentsList = [$loggedInUserId =>"Myself" ] + $agentsList;
        
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
}
