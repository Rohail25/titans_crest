<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        // Check due profit distributions continuously; each package pays every 15 minutes based on next_profit_time.
        $schedule->command('profits:distribute')
            ->everyMinute()
            ->onFailure(function () {
                // Log failure
                Log::error('Profit distribution command failed');
            })
            ->onSuccess(function () {
                // Log success
                Log::info('Profit distribution cycle completed successfully');
            });
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\AdminRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
