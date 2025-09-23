<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BulkWaiverDiscountController extends Controller
{
    public function showUploadForm()
    {
        return view('bulk_waiver_discount.upload');
    }

    public function upload(Request $request)
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

        while (($row = fgetcsv($handle)) !== false) {
            try {
                $data = array_combine($header, $row);

                // Find lead by ticket ID or account number
                $lead = null;
                if (!empty($data['ticket_id'])) {
                    $lead = Lead::find($data['ticket_id']);
                } elseif (!empty($data['account_number'])) {
                    $lead = Lead::where('account_number', $data['account_number'])->first();
                }

                if (!$lead) {
                    $failCount++;
                    $errors[] = "Row " . ($successCount + $failCount + 1) . ": Lead not found for ticket ID: " . ($data['ticket_id'] ?? $data['account_number']);
                    continue;
                }

                // Update waiver_discount field
                $waiverAmount = isset($data['waiver_discount']) ? floatval($data['waiver_discount']) : 0;

                $lead->waiver_discount = $waiverAmount;
                $lead->updated_by = Auth::id();
                $lead->updated_at = Carbon::now();
                $lead->save();

                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
                $errors[] = "Row " . ($successCount + $failCount + 1) . ": " . $e->getMessage();
            }
        }

        fclose($handle);

        return redirect()->back()->with([
            'success' => "$successCount waiver/discounts updated successfully. $failCount failed.",
            'errors' => $errors
        ]);
    }
}
