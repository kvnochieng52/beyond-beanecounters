<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\Payment;
use App\Models\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function storePayment(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required',
            'payment_status' => 'required'
        ]);


        $leadDetails = Lead::where('id', $request['leadID'])->first();


        $payment = new Payment();

        $payment->lead_id = $request['leadID'];
        $payment->amount = $request['amount'];
        $payment->balance_before = $leadDetails->balance;
        $payment->balance_after = $leadDetails->balance - $request['amount'];
        $payment->transaction_id = $request['transID'];
        $payment->description = $request['description'];
        $payment->status_id = $request['payment_status'];
        $payment->created_by = Auth::user()->id;
        $payment->updated_by = Auth::user()->id;
        $payment->save();

        $balance = $leadDetails->balance - $request['amount'];
        if ($request['payment_status'] == PaymentStatus::PAID) {
            $statusID = $balance >= $leadDetails->amount ? LeadStatus::PAID : LeadStatus::PARTIALLY_PAID;
        } else {
            $statusID = LeadStatus::PENDING;
        }

        Lead::where('id', $request['leadID'])->update([
            'balance' => $balance,
            'status_id' => $statusID,
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);


        return redirect('/lead/' . $request['leadID'] . '?section=payments')->with('success', 'Payment Saved Successfully');
    }
}
