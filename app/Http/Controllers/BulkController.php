<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Gender;
use App\Models\Country;
use App\Models\User;
use App\Models\Institution;
use App\Models\Currency;
use App\Models\LeadPriority;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BulkController extends Controller
{
    public function showUploadForm()
    {
        return view('bulk.upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
            'lead_type' => 'required|in:1,2', // Validate lead_type
        ]);

        $leadType = $request->input('lead_type');
        $file = $request->file('csv_file');
        $handle = fopen($file, 'r');
        $header = fgetcsv($handle); // Get header row

        $successCount = 0;
        $failCount = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            try {
                $data = array_combine($header, $row);

                // Common fields
                $defaulterTypeId = $leadType; // Use the selected lead_type
                $countryId = Country::whereRaw('LOWER(country_name) = ?', strtolower($data['country']))->value('id');
                $assignedAgentId = User::where(function ($query) use ($data) {
                    $query->where('id_number', $data['assigned_agent_id'])
                        ->orWhere('id', $data['assigned_agent_id']);
                })->value('id');

                $currencyId = Currency::whereRaw('LOWER(currency_name) = ?', strtolower($data['currency']))->value('id');
                $priorityId = LeadPriority::whereRaw('LOWER(lead_priority_name) = ?', strtolower($data['priority']))->value('id');

                if ($leadType == 1) {
                    // 🔹 Process Individual Leads
                    $genderId = Gender::whereRaw('LOWER(gender_name) = ?', strtolower($data['gender']))->value('id');
                    $institutionId = Institution::whereRaw('LOWER(institution_name) = ?', strtolower($data['institution']))->value('id');

                    Lead::create([
                        'title' => $data['title'] ?? null,
                        'id_passport_number' => $data['id_passport_number'] ?? null,
                        'account_number' => $data['account_number'] ?? null,
                        'defaulter_type_id' => $defaulterTypeId,
                        'gender_id' => $genderId,
                        'telephone' => $data['telephone'] ?? null,
                        'alternate_telephone' => $data['alternate_telephone'] ?? null,
                        'email' => $data['email'] ?? null,
                        'alternate_email' => $data['alternate_email'] ?? null,
                        'country_id' => $countryId,
                        'town' => $data['town'] ?? null,
                        'address' => $data['address'] ?? null,
                        'occupation' => $data['occupation'] ?? null,
                        'company_name' => $data['company_name'] ?? null,
                        'description' => $data['description'] ?? null,
                        'kin_full_names' => $data['kin_full_names'] ?? null,
                        'kin_telephone' => $data['kin_telephone'] ?? null,
                        'kin_email' => $data['kin_email'] ?? null,
                        'kin_relationship' => $data['kin_relationship'] ?? null,
                        'assigned_agent' => $assignedAgentId,
                        'institution_id' => $institutionId,
                        'amount' => $data['amount'] ?? null,
                        'additional_charges' => $data['additional_charges'] ?? 0,
                        'balance' => $data['balance'] ?? null,
                        'currency_id' => $currencyId,
                        'status_id' => 1,
                        'stage_id' => 1,
                        'priority_id' => $priorityId,
                        'due_date' => !empty($data['due_date']) ? Carbon::parse($data['due_date'])->format('Y-m-d') : null,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                } else {
                    // 🔹 Process Entity Leads (based on sample CSV provided)
                    Lead::create([
                        'title' => $data['title'] ?? null,
                        'account_number' => $data['account_number'] ?? null,
                        'defaulter_type_id' => $defaulterTypeId,
                        'telephone' => $data['telephone'] ?? null,
                        'alternate_telephone' => $data['alternate_telephone'] ?? null,
                        'email' => $data['email'] ?? null,
                        'alternate_email' => $data['alternate_email'] ?? null,
                        'country_id' => $countryId,
                        'town' => $data['town'] ?? null,
                        'address' => $data['address'] ?? null,
                        'company_name' => $data['company_name'] ?? null,
                        'description' => $data['description'] ?? null,
                        'assigned_agent' => $assignedAgentId,
                        'amount' => $data['amount'] ?? null,
                        'additional_charges' => $data['additional_charges'] ?? 0,
                        'balance' => $data['balance'] ?? null,
                        'currency_id' => $currencyId,
                        'status_id' => 1,
                        'stage_id' => 1,
                        'priority_id' => $priorityId,
                        'due_date' => !empty($data['due_date']) ? Carbon::parse($data['due_date'])->format('Y-m-d') : null,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }

                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
                $errors[] = "Row " . ($successCount + $failCount + 1) . ": " . $e->getMessage();
            }
        }

        fclose($handle);

        return redirect()->back()->with([
            'success' => "$successCount leads imported successfully. $failCount failed.",
            'errors' => $errors
        ]);
    }
}
