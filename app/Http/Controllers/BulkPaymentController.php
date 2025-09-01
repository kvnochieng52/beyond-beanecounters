<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BulkPaymentController extends Controller
{
    public function index(Request $request)
    {
        return view('bulk_payment.index')->with([]);
    }

    public function bulkPaymentProcess(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file, 'r');
        $header = fgetcsv($handle); // Get header row

        $successCount = 0;
        $failCount = 0;
        $errors = [];

        // Check if required columns exist
        $ticketNoIndex = array_search('Ticket No', $header);
        $amountIndex = array_search('Amount', $header);
        $descriptionIndex = array_search('Description', $header);

        if ($ticketNoIndex === false) {
            fclose($handle);
            return redirect()->back()->with([
                'error' => 'CSV must contain "Ticket No" column.',
                'errors' => ['CSV must contain "Ticket No" column.']
            ]);
        }

        if ($amountIndex === false) {
            fclose($handle);
            return redirect()->back()->with([
                'error' => 'CSV must contain "Amount" column.',
                'errors' => ['CSV must contain "Amount" column.']
            ]);
        }

        if ($descriptionIndex === false) {
            fclose($handle);
            return redirect()->back()->with([
                'error' => 'CSV must contain "Description" column.',
                'errors' => ['CSV must contain "Description" column.']
            ]);
        }

        $rowNumber = 1; // Start from 1 (header is row 0)

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            try {
                $data = array_combine($header, $row);

                $ticketNo = $data['Ticket No'];
                $amount = $data['Amount'];
                $description = $data['Description'] ?? ''; // Allow empty description values

                // Validate required fields
                if (empty($ticketNo)) {
                    throw new \Exception("Ticket No is required");
                }

                if (empty($amount) || !is_numeric($amount)) {
                    throw new \Exception("Amount is required and must be numeric");
                }

                // Description can be empty - no validation needed

                // Convert amount to decimal
                $amount = floatval($amount);

                if ($amount <= 0) {
                    throw new \Exception("Amount must be greater than zero");
                }

                // Find the lead by ticket number (ID)
                $lead = Lead::where('id', $ticketNo)->first();

                if (!$lead) {
                    throw new \Exception("Lead with Ticket No '$ticketNo' not found");
                }

                // Check if lead has a current balance
                if (is_null($lead->balance)) {
                    throw new \Exception("Lead with Ticket No '$ticketNo' has no balance set");
                }

                // Calculate new balance: current balance - payment amount
                $oldBalance = $lead->balance;
                $newBalance = $oldBalance - $amount;

                // Update the lead balance and description
                $lead->balance = $newBalance;
                //  $lead->description = $description; // Update description (can be empty)
                $lead->updated_by = auth()->id(); // Set who updated the record

                // Create transaction record
                $transaction = new Transaction();
                $transaction->lead_id = $lead->id;
                $transaction->transaction_type = TransactionType::PAYMENT;
                $transaction->amount = $amount * -1; // Negative for payment
                $transaction->description = "Bulk Payment Update - " . $description;
                $transaction->transaction_id = null;
                $transaction->balance_before = $oldBalance;
                $transaction->balance_after = $newBalance;
                $transaction->status_id = TransactionStatus::PAID;
                $transaction->payment_method = null;
                $transaction->created_by = auth()->id();
                $transaction->updated_by = auth()->id();
                $transaction->save();



                $activity = new Activity();
                if ($newBalance <= 0) {
                    $activity->activity_type_id = 19;
                    $activity->activity_title = "Payment Received";
                } else {
                    $activity->activity_type_id = 28;
                    $activity->activity_title =  " Partial Payment Made";
                }

                $activity->description = "Bulk Payment Update - " . $description;

                $activity->lead_id = $lead->id;
                $activity->assigned_user_id =  Auth::user()->id;
                $activity->status_id = 2; // Default to first status if not provided

                $activity->act_payment_amount  = $amount;

                $activity->created_by = Auth::user()->id;
                $activity->updated_by = Auth::user()->id;
                $activity->save();

                // Update lead status based on balance
                if ($newBalance <= 0) {
                    $lead->status_id = LeadStatus::PAID;
                } else {
                    $lead->status_id = LeadStatus::PARTIALLY_PAID;
                }

                $lead->save();

                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
                $errors[] = "Row $rowNumber: " . $e->getMessage();
            }
        }

        fclose($handle);

        $message = "$successCount payments processed successfully.";
        if ($failCount > 0) {
            $message .= " $failCount failed.";
        }

        return redirect()->back()->with([
            'success' => $message,
            'errors' => $errors
        ]);
    }
}
