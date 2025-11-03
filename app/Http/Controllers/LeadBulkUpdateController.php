<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\DefaulterType;
use App\Models\Gender;
use App\Models\Country;
use App\Models\Institution;
use App\Models\Currency;
use App\Models\LeadStatus;
use App\Models\LeadStage;
use App\Models\LeadCategory;
use App\Models\LeadPriority;
use App\Models\LeadIndustry;
use App\Models\LeadConversionStatus;
use App\Models\LeadEngagementLevel;
use App\Models\Department;
use App\Models\User;
use App\Models\CallDisposition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LeadBulkUpdateController extends Controller
{
    public function index()
    {
        return view('lead.bulk_update');
    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));

        if (empty($data)) {
            return back()->withErrors(['csv_file' => 'The CSV file is empty.']);
        }

        $header = array_shift($data);

        // Validate CSV header
        $expectedColumns = $this->getExpectedColumns();
        $missingColumns = array_diff($expectedColumns, $header);

        if (!empty($missingColumns)) {
            return back()->withErrors(['csv_file' => 'Missing required columns: ' . implode(', ', $missingColumns)]);
        }

        $results = $this->processCSVData($data, $header);

        return view('lead.bulk_update_results', compact('results'));
    }

    public function downloadTemplate()
    {
        $expectedColumns = $this->getExpectedColumns();

        $filename = 'leads_bulk_update_template.csv';
        $handle = fopen('php://temp', 'w+');

        // Write header
        fputcsv($handle, $expectedColumns);

        // Write sample data
        $sampleData = $this->getSampleData();
        fputcsv($handle, $sampleData);

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function getExpectedColumns()
    {
        return [
            'ticket_no',
            'defaulter_type_name',
            'title',
            'id_passport_number',
            'account_number',
            'gender_name',
            'telephone',
            'alternate_telephone',
            'email',
            'alternate_email',
            'country_name',
            'town',
            'address',
            'occupation',
            'company_name',
            'description',
            'kin_full_names',
            'kin_telephone',
            'kin_email',
            'kin_relationship',
            'assigned_agent_name',
            'assigned_department_name',
            'institution_name',
            'amount',
            'additional_charges',
            'balance',
            'waiver_discount',
            'currency_name',
            'status_name',
            'stage_name',
            'category_name',
            'priority_name',
            'industry_name',
            'conversion_status_name',
            'engagement_level_name',
            'due_date',
            'call_disposition_name',
            'last_ptp_amount',
            'last_ptp_date',
            'last_retire_date'
        ];
    }

    private function getSampleData()
    {
        return [
            '277', // ticket_no
            'Corporate', // defaulter_type_name
            'Sample Company Ltd', // title
            'A123456789', // id_passport_number
            'ACC001', // account_number
            'Male', // gender_name
            '0712345678', // telephone
            '0723456789', // alternate_telephone
            'sample@company.com', // email
            'alt@company.com', // alternate_email
            'Kenya', // country_name
            'Nairobi', // town
            'Sample Address', // address
            'Manager', // occupation
            'Sample Company Ltd', // company_name
            'Sample description', // description
            'John Doe', // kin_full_names
            '0734567890', // kin_telephone
            'kin@email.com', // kin_email
            'Brother', // kin_relationship
            'Agent Name', // assigned_agent_name
            'Collections', // assigned_department_name
            'Sample Institution', // institution_name
            '100000.00', // amount
            '0.00', // additional_charges
            '100000.00', // balance
            '0.00', // waiver_discount
            'KES', // currency_name
            'Active', // status_name
            'New Lead', // stage_name
            'High Value', // category_name
            'High', // priority_name
            'Technology', // industry_name
            'Qualified', // conversion_status_name
            'High', // engagement_level_name
            '2025-12-31', // due_date
            'Contact Made', // call_disposition_name
            '50000.00', // last_ptp_amount
            '2025-11-30', // last_ptp_date
            '2025-12-15' // last_retire_date
        ];
    }

    private function processCSVData($data, $header)
    {
        $results = [
            'success' => 0,
            'errors' => 0,
            'details' => []
        ];

        foreach ($data as $rowIndex => $row) {
            $rowNumber = $rowIndex + 2; // +2 because we removed header and rows start from 1

            if (empty($row) || count($row) < count($header)) {
                $results['errors']++;
                $results['details'][] = [
                    'row' => $rowNumber,
                    'status' => 'error',
                    'message' => 'Row is empty or has missing columns'
                ];
                continue;
            }

            $rowData = array_combine($header, $row);

            // Skip if ticket_no is empty
            if (empty($rowData['ticket_no'])) {
                $results['errors']++;
                $results['details'][] = [
                    'row' => $rowNumber,
                    'status' => 'error',
                    'message' => 'Ticket number is required'
                ];
                continue;
            }

            try {
                $updateResult = $this->updateLeadRecord($rowData);

                if ($updateResult['success']) {
                    $results['success']++;
                    $results['details'][] = [
                        'row' => $rowNumber,
                        'ticket_no' => $rowData['ticket_no'],
                        'status' => 'success',
                        'message' => $updateResult['message']
                    ];
                } else {
                    $results['errors']++;
                    $results['details'][] = [
                        'row' => $rowNumber,
                        'ticket_no' => $rowData['ticket_no'],
                        'status' => 'error',
                        'message' => $updateResult['message']
                    ];
                }
            } catch (\Exception $e) {
                $results['errors']++;
                $results['details'][] = [
                    'row' => $rowNumber,
                    'ticket_no' => $rowData['ticket_no'] ?? 'N/A',
                    'status' => 'error',
                    'message' => 'Exception: ' . $e->getMessage()
                ];
            }
        }

        return $results;
    }

    private function updateLeadRecord($data)
    {
        $ticketNo = $data['ticket_no'];

        // Find the lead by ID (ticket_no)
        $lead = Lead::find($ticketNo);

        if (!$lead) {
            return [
                'success' => false,
                'message' => "Lead with ticket number {$ticketNo} not found"
            ];
        }

        $updateData = [];
        $lookupErrors = [];

        // Process each field and only update if it has a value
        foreach ($data as $column => $value) {
            if (empty($value) || $column === 'ticket_no') {
                continue; // Skip empty values and ticket_no
            }

            switch ($column) {
                case 'defaulter_type_name':
                    $defaulterType = $this->findByName(DefaulterType::class, 'defaulter_type_name', $value);
                    if ($defaulterType) {
                        $updateData['defaulter_type_id'] = $defaulterType->id;
                    } else {
                        $lookupErrors[] = "Defaulter type '{$value}' not found";
                    }
                    break;

                case 'gender_name':
                    $gender = $this->findByName(Gender::class, 'gender_name', $value);
                    if ($gender) {
                        $updateData['gender_id'] = $gender->id;
                    } else {
                        $lookupErrors[] = "Gender '{$value}' not found";
                    }
                    break;

                case 'country_name':
                    $country = $this->findByName(Country::class, 'country_name', $value);
                    if ($country) {
                        $updateData['country_id'] = $country->id;
                    } else {
                        $lookupErrors[] = "Country '{$value}' not found";
                    }
                    break;

                case 'institution_name':
                    $institution = $this->findByName(Institution::class, 'institution_name', $value);
                    if ($institution) {
                        $updateData['institution_id'] = $institution->id;
                    } else {
                        $lookupErrors[] = "Institution '{$value}' not found";
                    }
                    break;

                case 'currency_name':
                    $currency = $this->findByName(Currency::class, 'currency_name', $value);
                    if ($currency) {
                        $updateData['currency_id'] = $currency->id;
                    } else {
                        $lookupErrors[] = "Currency '{$value}' not found";
                    }
                    break;

                case 'status_name':
                    $status = $this->findByName(LeadStatus::class, 'lead_status_name', $value);
                    if ($status) {
                        $updateData['status_id'] = $status->id;
                    } else {
                        $lookupErrors[] = "Status '{$value}' not found";
                    }
                    break;

                case 'stage_name':
                    $stage = $this->findByName(LeadStage::class, 'lead_stage_name', $value);
                    if ($stage) {
                        $updateData['stage_id'] = $stage->id;
                    } else {
                        $lookupErrors[] = "Stage '{$value}' not found";
                    }
                    break;

                case 'category_name':
                    $category = $this->findByName(LeadCategory::class, 'lead_category_name', $value);
                    if ($category) {
                        $updateData['category_id'] = $category->id;
                    } else {
                        $lookupErrors[] = "Category '{$value}' not found";
                    }
                    break;

                case 'priority_name':
                    $priority = $this->findByName(LeadPriority::class, 'lead_priority_name', $value);
                    if ($priority) {
                        $updateData['priority_id'] = $priority->id;
                    } else {
                        $lookupErrors[] = "Priority '{$value}' not found";
                    }
                    break;

                case 'industry_name':
                    $industry = $this->findByName(LeadIndustry::class, 'lead_industry_name', $value);
                    if ($industry) {
                        $updateData['industry_id'] = $industry->id;
                    } else {
                        $lookupErrors[] = "Industry '{$value}' not found";
                    }
                    break;

                case 'conversion_status_name':
                    $conversionStatus = $this->findByName(LeadConversionStatus::class, 'lead_conversion_name', $value);
                    if ($conversionStatus) {
                        $updateData['conversion_status_id'] = $conversionStatus->id;
                    } else {
                        $lookupErrors[] = "Conversion status '{$value}' not found";
                    }
                    break;

                case 'engagement_level_name':
                    $engagementLevel = $this->findByName(LeadEngagementLevel::class, 'lead_engagement_level_name', $value);
                    if ($engagementLevel) {
                        $updateData['engagement_level_id'] = $engagementLevel->id;
                    } else {
                        $lookupErrors[] = "Engagement level '{$value}' not found";
                    }
                    break;

                case 'assigned_agent_name':
                    $agent = User::where('name', 'like', '%' . $value . '%')
                                ->orWhere('agent_code', $value)
                                ->first();
                    if ($agent) {
                        $updateData['assigned_agent'] = $agent->id;
                    } else {
                        $lookupErrors[] = "Agent '{$value}' not found";
                    }
                    break;

                case 'assigned_department_name':
                    $department = $this->findByName(Department::class, 'department_name', $value);
                    if ($department) {
                        $updateData['assigned_department'] = $department->id;
                    } else {
                        $lookupErrors[] = "Department '{$value}' not found";
                    }
                    break;

                case 'call_disposition_name':
                    $callDisposition = $this->findByName(CallDisposition::class, 'call_disposition_name', $value);
                    if ($callDisposition) {
                        $updateData['call_disposition_id'] = $callDisposition->id;
                    } else {
                        $lookupErrors[] = "Call disposition '{$value}' not found";
                    }
                    break;

                case 'due_date':
                    try {
                        $updateData['due_date'] = Carbon::parse($value)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $lookupErrors[] = "Invalid date format for due_date: '{$value}'";
                    }
                    break;

                // Direct field mappings (no lookup required)
                case 'title':
                case 'id_passport_number':
                case 'account_number':
                case 'telephone':
                case 'alternate_telephone':
                case 'email':
                case 'alternate_email':
                case 'town':
                case 'address':
                case 'occupation':
                case 'company_name':
                case 'description':
                case 'kin_full_names':
                case 'kin_telephone':
                case 'kin_email':
                case 'kin_relationship':
                    $updateData[$column] = $value;
                    break;

                case 'amount':
                case 'additional_charges':
                case 'balance':
                case 'waiver_discount':
                case 'last_ptp_amount':
                case 'last_ptp_date':
                case 'last_retire_date':
                    $updateData[$column] = is_numeric($value) ? $value : null;
                    break;
            }
        }

        // If there are lookup errors, return them
        if (!empty($lookupErrors)) {
            return [
                'success' => false,
                'message' => implode(', ', $lookupErrors)
            ];
        }

        // If no data to update, skip
        if (empty($updateData)) {
            return [
                'success' => true,
                'message' => 'No data to update (all fields were empty)'
            ];
        }

        // Add updated_by and updated_at
        $updateData['updated_by'] = Auth::user()->id;
        $updateData['updated_at'] = now();

        // Update the lead
        $lead->update($updateData);

        $updatedFields = array_keys($updateData);
        return [
            'success' => true,
            'message' => 'Updated fields: ' . implode(', ', $updatedFields)
        ];
    }

    private function findByName($model, $column, $value)
    {
        $query = $model::where($column, $value);

        // Only add is_active check if the model has that column
        if (in_array($model, [
            DefaulterType::class,
            Gender::class,
            Country::class,
            Institution::class,
            Currency::class,
            LeadStatus::class,
            LeadStage::class,
            LeadCategory::class,
            LeadPriority::class,
            LeadIndustry::class,
            LeadConversionStatus::class,
            LeadEngagementLevel::class,
            Department::class,
            CallDisposition::class
        ])) {
            $query->where('is_active', 1);
        }

        return $query->first();
    }
}