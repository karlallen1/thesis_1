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
        // ✅ Archive stats daily at 8:05 PM
        $schedule->command('stats:archive')->dailyAt('20:05');

        // ✅ Cancel expired applications daily at midnight
        $schedule->command('app:cancel-expired-applications')->daily();

        // Optional: Run every hour (comment out daily if using this)
        // $schedule->command('app:cancel-expired-applications')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}