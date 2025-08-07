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
        // $schedule->command('inspire')->hourly();
        
        // Clean up old activity logs (keep last 90 days)
        $schedule->command('activitylog:clean')->daily();
        
        // Update currency exchange rates daily
        $schedule->command('currencies:update-rates')->dailyAt('02:00');
        
        // Clean up expired banners
        $schedule->command('banners:cleanup')->daily();
        
        // Send daily vendor reports
        $schedule->command('vendors:send-daily-report')->dailyAt('09:00');
        
        // Clear expired sessions
        $schedule->command('session:gc')->daily();
        
        // Backup database
        // $schedule->command('backup:run')->daily()->at('03:00');
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
