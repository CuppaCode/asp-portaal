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
        // Check for expiring certificates daily at 8:00 AM
        $schedule->command('certificates:check-expiring')->dailyAt('08:00');
        
        // Cleanup expired certificates weekly on Sundays at 2:00 AM
        $schedule->command('certificates:cleanup-expired')->weekly()->sundays()->at('02:00');
      
        // Expire draft claims daily at 00:00
        $schedule->command('claims:expire-drafts')->daily();
        
        // Send draft claim reminders daily at 09:00
        $schedule->command('claims:remind-drafts')->dailyAt('09:00');
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
