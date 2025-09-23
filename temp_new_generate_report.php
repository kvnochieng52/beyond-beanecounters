    public function generateReport(Request $request)
    {
        // Validate required fields
        $request->validate([
            'from_date' => 'required|date_format:d-m-Y',
            'to_date' => 'required|date_format:d-m-Y|after_or_equal:from_date',
        ]);

        // Prepare filters array for the job
        $filters = [
            'from_date' => Carbon::createFromFormat('d-m-Y', $request->from_date)->format('Y-m-d'),
            'to_date' => Carbon::createFromFormat('d-m-Y', $request->to_date)->format('Y-m-d'),
        ];

        // Add optional filters
        if ($request->filled('ticket_no')) {
            $filters['ticket_numbers'] = array_map('trim', explode(',', $request->ticket_no));
        }

        if ($request->filled('activity_type') && is_array($request->activity_type)) {
            $filters['activity_type_ids'] = array_filter($request->activity_type);
        }

        if ($request->filled('agent') && is_array($request->agent)) {
            $filters['agent_ids'] = array_filter($request->agent);
        }

        if ($request->filled('institution') && is_array($request->institution)) {
            $filters['institution_ids'] = array_filter($request->institution);
        }

        if ($request->filled('disposition') && is_array($request->disposition)) {
            $filters['disposition_ids'] = array_filter($request->disposition);
        }

        if ($request->filled('ptp_due_from_date')) {
            $filters['ptp_due_from_date'] = Carbon::createFromFormat('d-m-Y', $request->ptp_due_from_date)->format('Y-m-d');
        }

        if ($request->filled('ptp_due_to_date')) {
            $filters['ptp_due_to_date'] = Carbon::createFromFormat('d-m-Y', $request->ptp_due_to_date)->format('Y-m-d');
        }

        // Generate report name
        $reportName = 'Activity Report - ' . $request->from_date . ' to ' . $request->to_date;

        // Create background report record
        $backgroundReport = BackgroundReport::create([
            'report_type' => 'activity_report',
            'report_name' => $reportName,
            'filters' => $filters,
            'status' => 'pending',
            'requested_by' => Auth::id(),
        ]);

        // Dispatch the job
        ProcessActivityReport::dispatch($backgroundReport);

        return redirect()->route('background-reports.index')
            ->with('success', 'Activity report has been queued for processing. You can check the progress in Background Reports.');
    }