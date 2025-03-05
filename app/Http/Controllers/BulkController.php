<?php

namespace App\Http\Controllers;

use App\Models\AdditionalCostRule;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Gender;
use App\Models\Country;
use App\Models\User;
use App\Models\Institution;
use App\Models\Currency;
use App\Models\LeadPriority;
use App\Models\ScheduledRule;
use App\Models\Transaction;
use App\Models\TransactionType;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BulkController extends Controller
{
    public function showUploadForm()
    {
        return view('bulk.upload')->with([
            'institutions' => Institution::Where('is_active', 1)->pluck('institution_name', 'id'),
            'rules' => AdditionalCostRule::where('is_active', 1)
                ->selectRaw("CONCAT(rule_code, ' : ', title) as rule_title, id")
                ->orderBy('id', 'DESC')
                ->pluck('rule_title', 'id')
        ]);
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
                        ->orWhere('agent_code', $data['assigned_agent_id']);
                })->value('id');

                $currencyId = Currency::whereRaw('LOWER(currency_name) = ?', strtolower($data['currency']))->value('id');
                $priorityId = LeadPriority::whereRaw('LOWER(lead_priority_name) = ?', strtolower($data['priority']))->value('id');

                if ($leadType == 1) {

                    $genderId = Gender::whereRaw('LOWER(gender_name) = ?', strtolower($data['gender']))->value('id');
                    // $institutionId = Institution::whereRaw('LOWER(institution_name) = ?', strtolower($data['institution']))->value('id');

                    $institutionId = $request['institution'];

                    $leadID = Lead::create([
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
                    $leadID = Lead::create([
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




                //apply rules

                if (!empty($request['rules'])) {
                    foreach ($request['rules'] as $rule) {
                        $ruleDetails = AdditionalCostRule::select([
                            'additional_cost_rules.*',
                            'additional_cost_rule_types.rule_type_name',
                        ])
                            ->where('additional_cost_rules.id', $rule)
                            ->leftJoin('additional_cost_rule_types', 'additional_cost_rules.cost_type', 'additional_cost_rule_types.id')
                            ->first();
                        $type = $ruleDetails->type;
                        $costType = $ruleDetails->rule_type_name;


                        if ($ruleDetails->apply_due_date == '0') {

                            if ($type == 'Percentage') {
                                $calculationValue = ($ruleDetails->value / 100) * $data['amount'];
                            } else {
                                $calculationValue = $ruleDetails->value;
                            }

                            if ($costType == 'Discount') {
                                $finalAmount = $data['amount'] - $calculationValue;
                                $txType = TransactionType::DISCOUNT;
                            } else {
                                $finalAmount = $calculationValue + $data['amount'];
                                $txType = TransactionType::PENALTY;
                            }

                            Lead::where('id', $leadID->id)->update([
                                'balance' => $finalAmount,
                            ]);

                            Transaction::create([
                                'lead_id' => $leadID->id,
                                'transaction_type' => $txType,
                                'amount' => $costType == 'Discount' ? $calculationValue * -1 : $calculationValue,
                                'description' => $costType . "-" . $ruleDetails->title,
                                'rule_id' => $ruleDetails->id,
                                'created_by' => Auth::id(),
                                'updated_by' => Auth::id(),
                            ]);
                        } else {
                            $scheduleRule = new ScheduledRule();
                            $scheduleRule->lead_id = $leadID->id;
                            $scheduleRule->rule_id = $ruleDetails->id;
                            $scheduleRule->title = $ruleDetails->title;
                            $scheduleRule->type = $ruleDetails->type;
                            $scheduleRule->rule_code = $ruleDetails->rule_code;
                            $scheduleRule->cost_type = $ruleDetails->cost_type;
                            $scheduleRule->value = $ruleDetails->value;
                            $scheduleRule->days = $ruleDetails->days;
                            $scheduleRule->is_active = 1;
                            $scheduleRule->created_by = Auth::id();
                            $scheduleRule->updated_by = Auth::id();
                            $scheduleRule->save();
                        }
                    }
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
