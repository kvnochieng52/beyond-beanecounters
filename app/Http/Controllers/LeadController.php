<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\DefaulterType;
use App\Models\Gender;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $entityType = $request['entity_type_store'];

        if ($entityType == 1) {
            $this->validate($request, [
                'full_names' => 'required',
                'id_number' => 'required',
                'telephone' => 'required',
                'email' => 'required',
                'gender' => 'required',
            ]);

            $lead = new Lead();

            $lead->defaulter_type_id = $entityType;
            $lead->full_names = $request['full_names'];
            $lead->id_passport_number = $request['id_number'];
            $lead->gender_id = $request['gender'];
            $lead->telephone = $request['telephone'];
            $lead->alternate_telephone = $request['alternate_telephone'];
            $lead->email = $request['email'];

            $lead->address = $request['address'];
            $lead->country_id = $request['country'];
            $lead->town = $request['town'];
            $lead->occupation = $request['occupation'];
            $lead->company = $request['company'];
            $lead->kin_full_names = $request['kin_name'];
            $lead->kin_telephone = $request['kin_telephone'];
            $lead->kin_email = $request['kin_email'];
            $lead->kin_relationship = $request['kin_relation'];
            $lead->created_by = Auth::user()->id;
            $lead->updated_by = Auth::user()->id;
        } else {
        }
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
    public function edit(Lead $lead)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lead $lead)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lead $lead)
    {
        //
    }
}
