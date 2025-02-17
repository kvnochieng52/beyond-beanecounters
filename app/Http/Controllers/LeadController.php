<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Currency;
use App\Models\DefaulterType;
use App\Models\Gender;
use App\Models\Institution;
use App\Models\Lead;
use App\Models\LeadCategory;
use App\Models\LeadIndustry;
use App\Models\LeadPriority;
use App\Models\LeadStage;
use App\Models\LeadStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('lead.index')->with([
            'leads' => Lead::getLeads()
        ]);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lead $lead, Request $request)
    {
        return view('lead.edit')->with([
            'step' => $request['step'],
            'lead' => $lead,
            'genders' => Gender::where('is_active', 1)->pluck('gender_name', 'id'),
            'countries' => Country::where('is_active', 1)->pluck('country_name', 'id'),
            'defaulterTypes' => DefaulterType::where('is_active', 1)->pluck('defaulter_type_name', 'id'),
            'individualDefaulterType' => DefaulterType::INDIVIDUAL,


            //here

            'leadCategories' => LeadCategory::Where('is_active', 1)->pluck('lead_category_name', 'id'),
            'institutions' => Institution::Where('is_active', 1)->pluck('institution_name', 'id'),
            'currencies' => Currency::Where('is_active', 1)->pluck('currency_name', 'id'),
            'priorities' => LeadPriority::where('is_active', 1)
                ->select(DB::raw("CONCAT(lead_priority_name, ' - ', description) as name"), 'id')
                ->pluck('name', 'id'),

            //for company
            'industries' => LeadIndustry::where('is_active', 1)->pluck('lead_industry_name', 'id'),
            // 'leadStatus' => LeadStatus::where('is_active', 1)->pluck('id', 'lead_status_name')

            'agentsList' => User::where('is_active', 1)
                ->select(DB::raw("CONCAT(name, ' - ', agent_id) as name"), 'id')
                ->pluck('name', 'id'),

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
                $lead->assigned_agent = $request['agent'];
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lead $lead)
    {
        //
    }
}
