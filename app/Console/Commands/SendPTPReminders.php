<?php

namespace App\Console\Commands;

use App\Jobs\PTPReminderJob;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendPTPReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ptp:send-reminders {--date= : The date to send reminders for (YYYY-MM-DD format). Defaults to today.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send PTP (Promise to Pay) reminder SMS to customers with due dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ?? Carbon::today()->toDateString();
        
        $this->info("Queueing PTP reminders for date: {$date}");
        
        // Dispatch the job
        PTPReminderJob::dispatch($date);
        
        $this->info("PTP reminders have been queued for {$date}");
        $this->info("You can monitor the process at: /reminder-processes");
        
        return 0;
    }
}
