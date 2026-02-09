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

        // Send Agent Performance Report every 2 hours from 8:20 AM to 8:20 PM (weekdays only)
        // Cron: minute 20, hours 8,10,12,14,16,18,20 (8AM to 8PM), every day of month, every month, weekdays only (1-5 = Mon-Fri)
        $schedule->command('report:agent-performance')
            ->cron('20 8,10,12,14,16,18,20 * * 1-5')
            ->description('Generate and send Agent Performance Report every 2 hours (Mon-Fri, 8:20 AM to 8:20 PM)')
            ->withoutOverlapping();

        // Send Agent Weekly Report every day at 8:20 AM
        $schedule->command('report:agent-weekly')
            ->dailyAt('08:20')
            ->description('Generate and send Agent Weekly Report daily at 8:20 AM')
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
