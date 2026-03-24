<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserPackage;
use App\Models\Setting;

class VerifyProfitTimer extends Command
{
    protected $signature = 'profit:verify-timer';
    protected $description = 'Verify profit distribution timer status for all active packages';

    public function handle(): int
    {
        $cycleMinutes = (int) Setting::get('profit_distribution_cycle_minutes', 15);
        $currentTime = now();

        $this->info("==== PROFIT TIMER VERIFICATION ====");
        $this->info("Current Time: {$currentTime}");
        $this->info("Configured Cycle: {$cycleMinutes} minutes");
        $this->line('');

        $packages = UserPackage::where('is_active', true)
            ->where('package_status', 'active')
            ->with('user', 'package')
            ->get();

        if ($packages->isEmpty()) {
            $this->warn('No active packages found!');
            return self::SUCCESS;
        }

        $dueSoon = [];
        $notDueYet = [];
        $overdue = [];

        foreach ($packages as $package) {
            $nextProfit = $package->next_profit_time;
            $status = '❌ OVERDUE';
            $category = 'overdue';

            if (!$nextProfit) {
                $status = '⚠️  NOT SET';
                $category = 'notDueYet';
            } elseif ($currentTime->lt($nextProfit)) {
                $secondsRemaining = $currentTime->diffInSeconds($nextProfit);
                $status = "⏳ IN {$secondsRemaining}s";
                $category = 'dueSoon';
            } else {
                $status = '✅ DUE NOW';
                $category = 'overdue';
            }

            $display = [
                'id' => $package->id,
                'user_id' => $package->user_id,
                'user' => $package->user->email ?? 'Unknown',
                'package' => $package->package->name ?? 'Unknown',
                'next_profit_time' => $nextProfit?->format('Y-m-d H:i:s') ?? 'NOT SET',
                'status' => $status,
            ];

            if ($category === 'overdue') {
                $overdue[] = $display;
            } elseif ($category === 'dueSoon') {
                $dueSoon[] = $display;
            } else {
                $notDueYet[] = $display;
            }
        }

        $this->line("\n========== OVERDUE (Will distribute now) ==========");
        if (empty($overdue)) {
            $this->info("No overdue packages");
        } else {
            $this->table(
                array_keys($overdue[0]),
                $overdue
            );
        }

        $this->line("\n========== DUE SOON (Will distribute shortly) ==========");
        if (empty($dueSoon)) {
            $this->info("No packages due soon");
        } else {
            $this->table(
                array_keys($dueSoon[0]),
                $dueSoon
            );
        }

        $this->line("\n========== NOT SET (Needs initialization) ==========");
        if (empty($notDueYet)) {
            $this->info("All packages have timer set");
        } else {
            $this->table(
                array_keys($notDueYet[0]),
                $notDueYet
            );
        }

        $this->line("\n========== SUMMARY ==========");
        $this->info("Total Active Packages: " . count($packages));
        $this->info("Ready to Distribute: " . count($overdue));
        $this->info("Due Soon: " . count($dueSoon));
        $this->info("Not Set: " . count($notDueYet));

        return self::SUCCESS;
    }
}
