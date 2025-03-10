<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    public function getTransactions(Request $request)
    {
        if ($request->ajax()) {
            $transactions = Transaction::leftJoin('transaction_types', 'transactions.transaction_type', '=', 'transaction_types.id')
                ->leftJoin('users', 'transactions.created_by', '=', 'users.id') // Join users table
                ->select(
                    'transactions.id',
                    'transactions.lead_id',
                    'transaction_types.transaction_type_title',
                    'transactions.amount',
                    'transactions.description',
                    'users.name as created_by_name', // Get created by user name
                    'transactions.created_at'
                );

            return DataTables::of($transactions)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s'); // Format date
                })
                ->make(true);
        }
    }
}
