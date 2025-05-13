<?php

namespace App\Http\Controllers;

use App\Models\AdditionalCostRule;
use App\Models\AdditionalCostRuleType;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
<<<<<<< HEAD
=======
use Barryvdh\DomPDF\Facade\Pdf;
>>>>>>> 25aba04858ba4dafe48e1bc78d0efc8c5ecab38b

class TransactionController extends Controller
{

    public function storeTransaction(Request $request)
    {
        $transactionType = $request['trans_type_select'];

        $transTypeDetails = TransactionType::where('id', $transactionType)->first();

        if ($transactionType == TransactionType::PAYMENT) {


            $paymentMethodDetails = PaymentMethod::where('id', $request['payment_status'])->first();

            $desc = $transTypeDetails->transaction_type_title . "/"  . $paymentMethodDetails->method_name . " -Manual- " . $request['description'];
            $transaction = new Transaction();
            $transaction->lead_id = $request['leadID'];
            $transaction->transaction_type = $transactionType;
            $transaction->amount = $request['amount'] * -1;
            $transaction->description = $desc;
            $transaction->transaction_id = $request['transID'];
            $transaction->status_id = $request['payment_status'];
            $transaction->transaction_id = $request['transID'];
            $transaction->payment_method = $request['payment_method'];
            $transaction->created_by = Auth::user()->id;
            $transaction->updated_by = Auth::user()->id;
            $transaction->save();


            $leadDetails = Lead::where('id', $request['leadID'])->first();
            $leadDetails->balance = $leadDetails->balance - $request['amount'];
            $leadDetails->save();


            if ($request['payment_status'] = TransactionStatus::PAID) {
                if ($leadDetails->balance <= 0) {
                    $leadDetails->status_id = LeadStatus::PAID;
                    $leadDetails->save();
                } else {
                    $leadDetails->status_id = LeadStatus::PARTIALLY_PAID;
                    $leadDetails->save();
                }
            }
        }

        if ($transactionType == TransactionType::PENALTY) {


            $leadDetails = Lead::getLeadByID($request['leadID']);


            $chargeType = $request['charge_type'];


            $penaltyTypeDetails = AdditionalCostRuleType::where('id', $request['penalty_type'])->first();

            if ($chargeType == "Percentage") {

                $amount = ($request['value'] / 100) * $leadDetails->amount;
                $desc = $transTypeDetails->transaction_type_title . "/" . $penaltyTypeDetails->rule_type_name . " of " . $request['value'] . "% -Manual- " .  $request['description'];
            } else {
                $amount = $request['value'];
                $desc = $transTypeDetails->transaction_type_title . "/" . $penaltyTypeDetails->rule_type_name .  " of " . $leadDetails->currency_name . "  " . $request['value'] . " -Manual- " .  $request['description'];
            }

            $transaction = new Transaction();

            $transaction->lead_id = $request['leadID'];
            $transaction->transaction_type = $transactionType;
            $transaction->penalty_type_id = $request['penalty_type'];
            $transaction->amount =  $amount;
            $transaction->description =  $desc;
            $transaction->charge_type = $request['charge_type'];
            $transaction->status_id = TransactionStatus::POSTED;
            $transaction->created_by = Auth::user()->id;
            $transaction->updated_by = Auth::user()->id;
            $transaction->save();

            $leadDetails->balance = $leadDetails->balance + $amount;
            $leadDetails->additional_charges = $leadDetails->additional_charges + $amount;

            $leadDetails->save();
        }



        if ($transactionType == TransactionType::DISCOUNT) {



            $leadDetails = Lead::getLeadByID($request['leadID']);
            $chargeType = $request['charge_type'];

            if ($chargeType == "Percentage") {

                $amount = ($request['value'] / 100) * $leadDetails->amount;
                $desc = $transTypeDetails->transaction_type_title . " of " . $request['value'] . "% -Manual- " .  $request['description'];
            } else {
                $amount = $request['value'];
                $desc = $transTypeDetails->transaction_type_title . " of " . $leadDetails->currency_name . "  " . $request['value'] . " -Manual- " .  $request['description'];
            }


            $transaction = new Transaction();

            $transaction->lead_id = $request['leadID'];
            $transaction->transaction_type = $transactionType;
            $transaction->amount =  ($amount) * -1;
            $transaction->description =  $desc;
            $transaction->charge_type = $request['charge_type'];
            $transaction->status_id = TransactionStatus::POSTED;
            $transaction->created_by = Auth::user()->id;
            $transaction->updated_by = Auth::user()->id;
            $transaction->save();

            $leadDetails->balance = $leadDetails->balance - $amount;
            $leadDetails->save();
        }

        return  back()->with('success', 'Details Saved Successfully');
    }



    public function getTransactions(Request $request)
    {
        if ($request->ajax()) {
            $transactions = Transaction::leftJoin('transaction_types', 'transactions.transaction_type', '=', 'transaction_types.id')
                ->leftJoin('users', 'transactions.created_by', '=', 'users.id')
                ->leftJoin('transaction_statuses', 'transactions.status_id', '=', 'transaction_statuses.id') // Join transaction statuses
                ->select(
                    'transactions.id',
                    'transactions.lead_id',
                    'transaction_types.transaction_type_title',
                    'transactions.amount',
                    'transactions.penalty_type_id',
                    'transactions.description',
                    'users.name as created_by_name',
                    'transactions.created_at',
                    'transaction_statuses.status_name as status',
                    'transaction_statuses.color_code' // Get color code for label styling
                )
                ->where('transactions.lead_id', $request['lead_id'])
                ->orderBy('id', 'DESC');


            return DataTables::of($transactions)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s'); // Format date
                })
                ->addColumn('status_label', function ($row) {
                    return '<span class="badge bg-' . $row->color_code . '">' . strtoupper($row->status) . '</span>';
                })
                ->rawColumns(['status_label']) // Allow HTML rendering
                ->make(true);
        }
    }

    public function editTransaction($id){

        $transaction = Transaction::findOrFail($id);



       return response()->json([
        'id' => $transaction->id,
        'lead_id' => $transaction->lead_id,
        'transaction_type' => $transaction->transaction_type,
        'penalty_type_id' => $transaction->penalty_type_id,
        'charge_type' => $transaction->charge_type ?? null, // assuming you store this
        'value' => abs($transaction->amount), // in case it's stored as negative
        'amount' => abs($transaction->amount),
        'description' => $transaction->description,
        'transaction_id' => $transaction->transaction_id,
        'payment_method' => $transaction->payment_method,
        'status_id' => $transaction->status_id,
        'rule_id'=>$transaction->rule_id,
        'transaction_method'=>$transaction->transaction_method,
        'balance_before'=>$transaction->balance_before,
        'balance_after'=>$transaction->balance_after,
        'charge_type'=>$transaction->charge_type,
    ]);

    }

    public function updateTransaction(Request $request)
{

    $transactionType = $request['trans_type_select'];
    $transTypeDetails = TransactionType::where('id', $transactionType)->first();
    $transaction = Transaction::where('id', $request['transRecordId'])->first(); // Get existing transaction
   // dd($transaction);

   // $originalTransAmount = $transaction->amount;
//    dd([$originalTransAmount,$transaction] );

    if ($transactionType == TransactionType::PAYMENT) {

        $originalTransAmount = $transaction->amount;

        $paymentMethodDetails = PaymentMethod::where('id', $request['payment_status'])->first();
        $desc = $transTypeDetails->transaction_type_title . "/" . $paymentMethodDetails->method_name . " -Manual- " . $request['description'];

        //$transaction->lead_id = $request['leadID'];
        $transaction->transaction_type = $transactionType;
        $transaction->amount = $request['amount'] * -1;
        $transaction->description = $desc;
        $transaction->transaction_id = $request['transID'];
        $transaction->status_id = $request['payment_status'];
        $transaction->payment_method = $request['payment_method'];
        $transaction->updated_by = Auth::user()->id;
        $transaction->save();

        $leadDetails = Lead::where('id', $request['leadID'])->first();

       //dd([$leadDetails->balance,$originalTransAmount ,$request['amount']]);


       $transBalance = $leadDetails->balance;
       //$originalTransAmount = -5;
       $amount = $request['amount'];

       $balance = $transBalance + ($originalTransAmount *-1) - $amount;

       $leadDetails->balance = $balance;

        $leadDetails->save();

        if ($request['payment_status'] == TransactionStatus::PAID) {
            if ($leadDetails->balance <= 0) {
                $leadDetails->status_id = LeadStatus::PAID;
            } else {
                $leadDetails->status_id = LeadStatus::PARTIALLY_PAID;
            }
            $leadDetails->save();
        }
    }

    if ($transactionType == TransactionType::PENALTY) {
        $leadDetails = Lead::getLeadByID($request['leadID']);
        $chargeType = $request['charge_type'];
        $penaltyTypeDetails = AdditionalCostRuleType::where('id', $request['penalty_type'])->first();

        if ($chargeType == "Percentage") {
            $amount = ($request['value'] / 100) * $leadDetails->amount;
            $desc = $transTypeDetails->transaction_type_title . "/" . $penaltyTypeDetails->rule_type_name . " of " . $request['value'] . "% -Manual- " . $request['description'];
        } else {
            $amount = $request['value'];
            $desc = $transTypeDetails->transaction_type_title . "/" . $penaltyTypeDetails->rule_type_name . " of " . $leadDetails->currency_name . " " . $request['value'] . " -Manual- " . $request['description'];
        }


        $transaction = Transaction::findOrFail($request['transRecordId']);

        $transaction->lead_id = $request['leadID'];
        $transaction->transaction_type = $transactionType;
        $transaction->penalty_type_id = $request['penalty_type'];
        $transaction->amount = $amount;
        $transaction->description = $desc;
        $transaction->charge_type = $chargeType;
        $transaction->status_id = TransactionStatus::POSTED;
        $transaction->updated_by = Auth::user()->id;

       // dd($transaction);
        $transaction->save();

        $leadDetails->balance = $leadDetails->balance + $amount;
        $leadDetails->additional_charges = $leadDetails->additional_charges + $amount;
        $leadDetails->save();
    }

    if ($transactionType == TransactionType::DISCOUNT) {
        $leadDetails = Lead::getLeadByID($request['leadID']);
        $chargeType = $request['charge_type'];

        if ($chargeType == "Percentage") {
            $amount = ($request['value'] / 100) * $leadDetails->amount;
            $desc = $transTypeDetails->transaction_type_title . " of " . $request['value'] . "% -Manual- " . $request['description'];
        } else {
            $amount = $request['value'];
            $desc = $transTypeDetails->transaction_type_title . " of " . $leadDetails->currency_name . " " . $request['value'] . " -Manual- " . $request['description'];
        }

        $transaction = Transaction::findOrFail($request['transRecordId']);

        $transaction->lead_id = $request['leadID'];
        $transaction->transaction_type = $transactionType;
        $transaction->amount = ($amount) * -1;
        $transaction->description = $desc;
        $transaction->charge_type = $chargeType;
        $transaction->status_id = TransactionStatus::POSTED;
        $transaction->updated_by = Auth::user()->id;
        $transaction->save();

        $leadDetails->balance = $leadDetails->balance - $amount;
        $leadDetails->save();
    }

    return back()->with('success', 'Transaction Updated Successfully');
}

}
