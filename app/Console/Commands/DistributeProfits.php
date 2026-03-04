<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProfitService;
use App\Models\User;

class DistributeProfits extends Command
{
    protected $signature = 'profits:distribute {--users=* : Specific user IDs to distribute to}';
    protected $description = 'Distribute daily profits to users based on their active packages';

    protected ProfitService $profitService;

    public function __construct(ProfitService $profitService)
    {
        parent::__construct();
        $this->profitService = $profitService;
    }

    public function handle(): int
    {
        $this->info('Starting daily profit distribution...');

        $startTime = now();

        try {
            $userIds = $this->option('users');
            
            if (!empty($userIds)) {
                $this->line("Distributing profits to specific users: " . implode(', ', $userIds));
            }

            // Distribute profits
            $this->profitService->distributeProfitBatch($userIds);

            $duration = now()->diffInSeconds($startTime);
            $this->info("✓ Daily profit distribution completed successfully in {$duration} seconds");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("✗ Error distributing profits: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
