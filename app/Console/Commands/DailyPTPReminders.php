<?php

namespace App\Console\Commands;

use App\Jobs\PTPReminderJob;
use Illuminate\Console\Command;
use Carbon\Carbon;

class DailyPTPReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ptp:daily-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily PTP reminders automatically at 8:00 AM for today\'s due dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();
        
        $this->info("Starting daily PTP reminders for: {$today}");
        
        // Dispatch the job for today
        PTPReminderJob::dispatch($today);
        
        $this->info("Daily PTP reminders have been queued for {$today}");
        $this->info("Process will run automatically and can be monitored at: /reminder-processes");
        
        return 0;
    }
}
