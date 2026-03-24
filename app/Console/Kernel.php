<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Schedule is now handled in bootstrap/app.php
    }

        // Optional: Run OTP cleanup daily (expire old OTPs)
        $schedule->command('tinker')
            ->eval("
                \App\Models\OtpRequest::where('status', 'pending')
                    ->where('expires_at', '<', now())
                    ->update(['status' => 'expired']);
            ")
            ->dailyAt('02:00')
            ->timezone('UTC');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        // Command alias to support old name from user request
        \Artisan::command('profit:distribute', function () {
            $this->call('profits:distribute');
        });

        require base_path('routes/console.php');
    }
}
