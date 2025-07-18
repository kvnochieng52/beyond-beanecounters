<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class BulkAssignController extends Controller
{
    public function index()
    {
        return view('bulk-assign.index');
    }


    public function upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
            'action' => 'required|in:assign,re-assign',
        ]);

        $action = $request['action'];
        $file = $request->file('csv_file');
        $handle = fopen($file, 'r');
        $header = fgetcsv($handle); // Get header row

        $successCount = 0;
        $failCount = 0;
        $errors = [];

        // Check if required columns exist
        $ticketNoIndex = array_search('Ticket No', $header);
        $assignAgentIndex = array_search('Assign Agent', $header);

        if ($ticketNoIndex === false || $assignAgentIndex === false) {
            fclose($handle);
            return redirect()->back()->with([
                'error' => 'CSV must contain "Ticket No" and "Assign Agent" columns.',
                'errors' => ['CSV must contain "Ticket No" and "Assign Agent" columns.']
            ]);
        }

        while (($row = fgetcsv($handle)) !== false) {
            try {
                $data = array_combine($header, $row);

                $ticketNo = $data['Ticket No'];
                $assignAgentId = $data['Assign Agent'];

                // Validate required fields
                if (empty($ticketNo) || empty($assignAgentId)) {
                    throw new \Exception("Ticket No and Assign Agent are required");
                }

                // Find the lead by ticket number
                $lead = Lead::where('id', $ticketNo)->first();

                if (!$lead) {
                    throw new \Exception("Lead with Ticket No '$ticketNo' not found");
                }

                // Process based on action
                if ($action == 'assign') {
                    // Check if lead is already assigned
                    if (!empty($lead->assigned_agent)) {
                        throw new \Exception("Lead with Ticket No '$ticketNo' is already assigned to agent ID: " . $lead->assigned_agent);
                    }

                    // Assign the agent
                    $lead->assigned_agent = $assignAgentId;
                    $lead->save();
                } elseif ($action == 're-assign') {
                    // Re-assign without checking current assignment
                    $lead->assigned_agent = $assignAgentId;
                    $lead->save();
                }

                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
                $errors[] = "Row " . ($successCount + $failCount + 1) . ": " . $e->getMessage();
            }
        }

        fclose($handle);

        return redirect()->back()->with([
            'success' => "$successCount leads processed successfully. $failCount failed.",
            'errors' => $errors
        ]);
    }
}
