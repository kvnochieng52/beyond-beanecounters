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
            'action' => 'required|in:assign,re-assign,un-assign',
        ]);

        $action = $request['action'];
        $file = $request->file('csv_file');
        $handle = fopen($file, 'r');
        $header = fgetcsv($handle); // Get header row

        $successCount = 0;
        $failCount = 0;
        $errors = [];

        // Check if required columns exist based on action
        $ticketNoIndex = array_search('Ticket No', $header);

        if ($ticketNoIndex === false) {
            fclose($handle);
            return redirect()->back()->with([
                'error' => 'CSV must contain "Ticket No" column.',
                'errors' => ['CSV must contain "Ticket No" column.']
            ]);
        }

        // Only check for Assign Agent column if not un-assign action
        if ($action !== 'un-assign') {
            $assignAgentIndex = array_search('Assign Agent', $header);
            if ($assignAgentIndex === false) {
                fclose($handle);
                return redirect()->back()->with([
                    'error' => 'CSV must contain "Assign Agent" column for assign/re-assign actions.',
                    'errors' => ['CSV must contain "Assign Agent" column for assign/re-assign actions.']
                ]);
            }
        }

        while (($row = fgetcsv($handle)) !== false) {
            try {
                $data = array_combine($header, $row);

                $ticketNo = $data['Ticket No'];

                // Validate required fields
                if (empty($ticketNo)) {
                    throw new \Exception("Ticket No is required");
                }

                // Find the lead by ticket number
                $lead = Lead::where('id', $ticketNo)->first();

                if (!$lead) {
                    throw new \Exception("Lead with Ticket No '$ticketNo' not found");
                }

                // Process based on action
                switch ($action) {
                    case 'assign':
                        // Check if lead is already assigned
                        if (!empty($lead->assigned_agent)) {
                            throw new \Exception("Lead with Ticket No '$ticketNo' is already assigned to agent ID: " . $lead->assigned_agent);
                        }

                        $assignAgentId = $data['Assign Agent'];
                        if (empty($assignAgentId)) {
                            throw new \Exception("Assign Agent is required for assign action");
                        }

                        $lead->assigned_agent = $assignAgentId;
                        $lead->save();
                        break;

                    case 're-assign':
                        $assignAgentId = $data['Assign Agent'];
                        if (empty($assignAgentId)) {
                            throw new \Exception("Assign Agent is required for re-assign action");
                        }

                        $lead->assigned_agent = $assignAgentId;
                        $lead->save();
                        break;

                    case 'un-assign':
                        $lead->assigned_agent = null;
                        $lead->save();
                        break;
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
