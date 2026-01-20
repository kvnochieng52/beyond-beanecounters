<?php

namespace App\Http\Controllers;

use App\Models\ReminderProcess;
use App\Jobs\PTPReminderJob;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReminderProcessController extends Controller
{
    public function index()
    {
        $processes = ReminderProcess::orderBy('process_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get today's process if it exists
        $todayProcess = ReminderProcess::where('process_date', Carbon::today()->toDateString())
            ->first();

        return view('reminder_processes.index', compact('processes', 'todayProcess'));
    }

    public function show($id)
    {
        $process = ReminderProcess::findOrFail($id);
        return view('reminder_processes.show', compact('process'));
    }

    public function create()
    {
        return view('reminder_processes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'process_date' => 'required|date',
        ]);

        $processDate = $request->input('process_date');
        
        // Dispatch the job
        PTPReminderJob::dispatch($processDate);

        return redirect()->route('reminder-processes.index')
            ->with('success', "PTP Reminder job has been queued for {$processDate}");
    }

    public function runNow(Request $request)
    {
        $request->validate([
            'process_date' => 'required|date',
        ]);

        $processDate = $request->input('process_date');
        
        // Run the job synchronously
        $job = new PTPReminderJob($processDate);
        $job->handle();

        return redirect()->route('reminder-processes.index')
            ->with('success', "PTP Reminder job has been executed for {$processDate}");
    }

    public function runTodayReminders()
    {
        $today = Carbon::today()->toDateString();
        
        // Run the job synchronously for today
        $job = new PTPReminderJob($today);
        $job->handle();

        return redirect()->route('reminder-processes.index')
            ->with('success', "Today's PTP reminders have been executed for {$today}");
    }

    public function destroy($id)
    {
        $process = ReminderProcess::findOrFail($id);
        $process->delete();

        return redirect()->route('reminder-processes.index')
            ->with('success', 'Reminder process deleted successfully');
    }
}
