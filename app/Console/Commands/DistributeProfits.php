<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProfitService;
use App\Models\User;

class DistributeProfits extends Command
{
    protected $signature = 'profits:distribute {--users=* : Specific user IDs to distribute to}';
    protected $description = 'Distribute ROI profits every 8 hours to users with active packages';

    protected ProfitService $profitService;

    public function __construct(ProfitService $profitService)
    {
        parent::__construct();
        $this->profitService = $profitService;
    }

    public function handle(): int
    {
        $this->info('Starting 8-hour profit distribution cycle...');

        $startTime = now();

        try {
            $userIds = $this->option('users');
            
            if (!empty($userIds)) {
                $this->line("Distributing profits to specific users: " . implode(', ', $userIds));
            }

            // Distribute profits
            $this->profitService->distributeProfitBatch($userIds);

            $duration = now()->diffInSeconds($startTime);
            $this->info("✓ Profit distribution cycle completed successfully in {$duration} seconds");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("✗ Error distributing profits: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
