<?php

namespace App\Console\Commands;

use App\Services\LeadershipPerformanceService;
use Illuminate\Console\Command;

class DistributeLeadershipPerformanceBonuses extends Command
{
    protected $signature = 'leadership-performance:distribute';

    protected $description = 'Distribute daily leadership performance recurring bonuses to eligible sponsors';

    public function __construct(
        private LeadershipPerformanceService $leadershipPerformanceService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting leadership performance daily bonus distribution...');

        try {
            $stats = $this->leadershipPerformanceService->distributeDailyBonuses();

            $this->info(
                'Completed leadership performance distribution. '
                . "Processed: {$stats['processed']} | "
                . "Paid: {$stats['paid']} | "
                . "Skipped: {$stats['skipped']} | "
                . "Completed: {$stats['completed']} | "
                . "Errors: {$stats['errors']}"
            );

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error('Leadership performance distribution failed: ' . $exception->getMessage());

            return self::FAILURE;
        }
    }
}
