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
        // Check due profit distributions continuously; each package pays every 15 minutes based on next_profit_time.
        $schedule->command('profits:distribute')
            ->everyMinute()
            ->timezone('UTC')
            ->withoutOverlapping()
            ->onFailure(function () {
                // Log failure
                Log::error('Profit distribution command failed');
            })
            ->onSuccess(function () {
                // Log success
                Log::info('Profit distribution cycle completed successfully');
            });

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

        require base_path('routes/console.php');
    }
}
