<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProfitService;
use App\Models\User;

class DistributeProfits extends Command
{
    protected $signature = 'profits:distribute {--users=* : Specific user IDs to distribute to}';
    protected $description = 'Distribute ROI profits based on configured cycle time to users with active packages';

    protected ProfitService $profitService;

    public function __construct(ProfitService $profitService)
    {
        parent::__construct();
        $this->profitService = $profitService;
    }

    public function handle(): int
    {
        $cycleMinutes = (int) \App\Models\Setting::get('profit_distribution_cycle_minutes', 15);
        $this->info("Starting {$cycleMinutes}-minute profit distribution cycle...");

        $startTime = now();

        try {
            $userIds = $this->option('users');
            
            if (!empty($userIds)) {
                $this->line("Distributing profits to specific users: " . implode(', ', $userIds));
            }

            // Distribute profits
            $stats = $this->profitService->distributeProfitBatch($userIds);

            $duration = now()->diffInSeconds($startTime);
            $this->info("✓ Profit distribution cycle completed successfully in {$duration} seconds");
            $this->info("Users checked: {$stats['users']} | Packages checked: {$stats['checked_packages']} | " .
                "Distributed: {$stats['distributed_packages']} | Completed: {$stats['completed_packages']} | Errors: {$stats['errors']}");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("✗ Error distributing profits: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
