<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Send daily PTP reminders at 8:00 AM every day
        $schedule->command('ptp:daily-reminders')
            ->dailyAt('08:00')
            ->description('Send daily PTP reminders for today\'s due dates')
            ->withoutOverlapping();

        // Send Agent Performance Report every 2 hours from 8:20 AM to 7:00 PM (6 times a day)
        // Using cron: minute 20 of hours 8, 10, 12, 14, 16, 18
        $schedule->command('report:agent-performance')
            ->cron('20 8,10,12,14,16,18 * * *')
            ->description('Generate and send Agent Performance Report every 2 hours')
            ->withoutOverlapping();

        // Send Agent Weekly Report daily at 8:30 AM
        // Shows last 7 days of activity
        $schedule->command('report:agent-weekly')
            ->dailyAt('08:30')
            ->description('Generate and send Agent Weekly Report daily')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
