<?php

namespace App\Http\Controllers;

use App\Models\AdditionalCostRule;
use App\Models\Lead;
use App\Models\ScheduledRule;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\TransBulk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TransBulkController extends Controller
{


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $transBulks = TransBulk::with('user')->orderBy('id', 'DESC')->get();

            return DataTables::of($transBulks)
                ->addIndexColumn()
                ->addColumn('user_name', function ($row) {
                    return $row->user ? $row->user->name : 'N/A';
                })
                ->addColumn('csv_file', function ($row) {
                    if ($row->csv_file) {
                        $filePath = asset('uploads/bulk_transaction/' . basename($row->csv_file));
                        return '<a href="' . $filePath . '" download class="btn btn-sm btn-primary">Download</a>';
                    }
                    return 'N/A';
                })
                ->addColumn('rules', function ($row) {
                    $ruleIds = explode(',', $row->rules);
                    $ruleTitles = AdditionalCostRule::whereIn('id', $ruleIds)->pluck('title')->toArray();
                    return implode(', ', $ruleTitles);
                })
                ->addColumn('uploaded_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : 'N/A';
                })
                ->addColumn('status', function () {
                    return '<span class="badge badge-success">PROCESSED</span>';
                })
                ->rawColumns(['csv_file', 'status'])
                ->make(true);
        }

        return view('trans_bulk.index');
    }




    public function upload(Request $request)
    {

        return view('trans_bulk.upload')->with([
            'rules' => AdditionalCostRule::where('is_active', 1)
                ->selectRaw("CONCAT(rule_code, ' : ', title) as rule_title, id")
                ->orderBy('id', 'DESC')
                ->pluck('rule_title', 'id')
        ]);
    }


    public function process(Request $request)
    {


        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        // Store file in the 'public/uploads/bulk_transaction/' directory
        $file = $request->file('csv_file');
        $fileName = time() . '_' . $file->getClientOriginalName(); // Unique file name
        $filePath = 'uploads/bulk_transaction/' . $fileName; // Relative path
        $file->move(public_path('uploads/bulk_transaction'), $fileName); // Move file to public folder

        $handle = fopen(public_path($filePath), 'r');
        $header = fgetcsv($handle); // Get header row

        $successCount = 0;
        $failCount = 0;
        $errors = [];



        while (($row = fgetcsv($handle)) !== false) {
            try {
                $data = array_combine($header, $row);
                $ticketNo = $data['Ticket No'];

                if (!empty($request['rules'])) {
                    foreach ($request['rules'] as $rule) {
                        $leadDetails = Lead::getLeadByID($ticketNo);

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
                                $calculationValue = ($ruleDetails->value / 100) * $leadDetails->amount;
                            } else {
                                $calculationValue = $ruleDetails->value;
                            }

                            $transaction = new Transaction();
                            $transaction->lead_id =  $ticketNo;
                            $transaction->transaction_type = $costType == 'Discount' ? TransactionType::DISCOUNT : TransactionType::PENALTY;
                            $transaction->penalty_type_id = $ruleDetails->cost_type;
                            $transaction->amount =  $costType == 'Discount' ? $calculationValue * -1 : $calculationValue;
                            $transaction->description = $costType . "-Bulk-" . $ruleDetails->title;
                            $transaction->rule_id =  $ruleDetails->id;
                            $transaction->status_id = TransactionStatus::POSTED;
                            $transaction->created_by = Auth::user()->id;
                            $transaction->updated_by = Auth::user()->id;
                            $transaction->save();

                            if ($costType == 'Discount') {
                                $leadDetails->balance -= $calculationValue;
                            } else {
                                $leadDetails->balance += $calculationValue;
                                $leadDetails->additional_charges += $calculationValue;
                            }
                            $leadDetails->save();
                        } else {
                            $scheduleRule = new ScheduledRule();
                            $scheduleRule->lead_id = $ticketNo;
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

        // Save file path in TransBulk model
        $transBulk = new TransBulk();
        $transBulk->csv_file = $filePath; // Store relative file path
        $transBulk->rules = implode(',', $request['rules']);
        $transBulk->created_by = Auth::user()->id;
        $transBulk->updated_by = Auth::user()->id;
        $transBulk->save();


        return redirect('/trans-bulk/')->with('success', 'CSV Proccessed Successfully');
    }
}
