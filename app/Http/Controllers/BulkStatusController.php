<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadStatus;
use Illuminate\Http\Request;

class BulkStatusController extends Controller
{
    public function index(Request $request)
    {
        // Get all active lead statuses for reference
        $leadStatuses = LeadStatus::where('is_active', 1)->orderBy('order')->get();

        return view('bulk_status.index')->with([
            'leadStatuses' => $leadStatuses
        ]);
    }

    public function bulkStatusProcess(Request $request)
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
        $statusIndex = array_search('Status', $header);

        if ($ticketNoIndex === false) {
            fclose($handle);
            return redirect()->back()->with([
                'error' => 'CSV must contain "Ticket No" column.',
                'errors' => ['CSV must contain "Ticket No" column.']
            ]);
        }

        if ($statusIndex === false) {
            fclose($handle);
            return redirect()->back()->with([
                'error' => 'CSV must contain "Status" column.',
                'errors' => ['CSV must contain "Status" column.']
            ]);
        }

        // Get all lead statuses for validation
        $leadStatuses = LeadStatus::where('is_active', 1)->pluck('id', 'lead_status_name')->toArray();
        $validStatuses = array_keys($leadStatuses);

        $rowNumber = 1; // Start from 1 (header is row 0)

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            try {
                $data = array_combine($header, $row);

                $ticketNo = $data['Ticket No'];
                $status = $data['Status'];

                // Validate required fields
                if (empty($ticketNo)) {
                    throw new \Exception("Ticket No is required");
                }

                if (empty($status)) {
                    throw new \Exception("Status is required");
                }

                // Validate status exists in our system
                if (!in_array($status, $validStatuses)) {
                    throw new \Exception("Invalid status '$status'. Valid statuses are: " . implode(', ', $validStatuses));
                }

                // Find the lead by ticket number (ID)
                $lead = Lead::where('id', $ticketNo)->first();

                if (!$lead) {
                    throw new \Exception("Lead with Ticket No '$ticketNo' not found");
                }

                // Get the status ID from the lead_statuses table
                $statusId = $leadStatuses[$status];

                // Check if status is actually changing
                if ($lead->status_id == $statusId) {
                    throw new \Exception("Lead with Ticket No '$ticketNo' already has status '$status'");
                }

                // Update the lead status
                $oldStatusId = $lead->status_id;
                $lead->status_id = $statusId;
                $lead->updated_by = auth()->id();
                $lead->save();

                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
                $errors[] = "Row $rowNumber: " . $e->getMessage();
            }
        }

        fclose($handle);

        $message = "$successCount lead statuses updated successfully.";
        if ($failCount > 0) {
            $message .= " $failCount failed.";
        }

        return redirect()->back()->with([
            'success' => $message,
            'errors' => $errors
        ]);
    }
}
