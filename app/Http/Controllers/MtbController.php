<?php

namespace App\Http\Controllers;

use App\Models\Mtb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class MtbController extends Controller
{
    public function getMtbs(Request $request)
    {
        if ($request->ajax()) {
            $mtbs = Mtb::leftJoin('users', 'mtbs.created_by', '=', 'users.id')
                ->select(
                    'mtbs.id',
                    'mtbs.lead_id',
                    'mtbs.amount_paid',
                    'mtbs.date_paid',
                    'mtbs.payment_channel',
                    'mtbs.description',
                    'users.name as created_by_name',
                    'mtbs.created_at'
                )
                ->where('mtbs.lead_id', $request['lead_id'])
                ->orderBy('id', 'DESC');

            return DataTables::of($mtbs)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'amount_paid' => 'required|numeric|min:0',
            'date_paid' => 'required|date',
            'payment_channel' => 'required|in:Mpesa,CASH,CHEQUE',
            'description' => 'nullable|string'
        ]);

        $mtb = new Mtb();
        $mtb->lead_id = $request->lead_id;
        $mtb->amount_paid = $request->amount_paid;
        $mtb->date_paid = $request->date_paid;
        $mtb->payment_channel = $request->payment_channel;
        $mtb->description = $request->description;
        $mtb->created_by = Auth::user()->id;
        $mtb->updated_by = Auth::user()->id;
        $mtb->save();

        return redirect()->back()->with('success', 'MTB record created successfully!');
    }

    public function edit($id)
    {
        $mtb = Mtb::findOrFail($id);
        return response()->json([
            'id' => $mtb->id,
            'lead_id' => $mtb->lead_id,
            'amount_paid' => $mtb->amount_paid,
            'date_paid' => $mtb->date_paid,
            'payment_channel' => $mtb->payment_channel,
            'description' => $mtb->description
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'mtb_id' => 'required|exists:mtbs,id',
            'lead_id' => 'required|exists:leads,id',
            'amount_paid' => 'required|numeric|min:0',
            'date_paid' => 'required|date',
            'payment_channel' => 'required|in:Mpesa,CASH,CHEQUE',
            'description' => 'nullable|string'
        ]);

        $mtb = Mtb::findOrFail($request->mtb_id);
        $mtb->amount_paid = $request->amount_paid;
        $mtb->date_paid = $request->date_paid;
        $mtb->payment_channel = $request->payment_channel;
        $mtb->description = $request->description;
        $mtb->updated_by = Auth::user()->id;
        $mtb->save();

        return redirect()->back()->with('success', 'MTB record updated successfully!');
    }

    public function destroy($id)
    {
        $mtb = Mtb::findOrFail($id);
        $mtb->delete();

        return response()->json(['success' => 'MTB record deleted successfully!']);
    }
}
