<?php

namespace App\Console\Commands;

use App\Services\MonthlyPerformanceExcellenceService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DistributeMonthlyPerformanceExcellence extends Command
{
    protected $signature = 'monthly-performance:distribute {--month= : Month in YYYY-MM format. Defaults to latest closed month}';

    protected $description = 'Distribute Monthly Performance Excellence tiered bonuses for a closed month';

    public function __construct(
        private MonthlyPerformanceExcellenceService $monthlyPerformanceExcellenceService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            $monthOption = $this->option('month');

            if ($monthOption) {
                $monthStart = Carbon::createFromFormat('Y-m', (string) $monthOption)->startOfMonth();
                $stats = $this->monthlyPerformanceExcellenceService->distributeForMonth($monthStart);
            } else {
                $stats = $this->monthlyPerformanceExcellenceService->distributeForClosedMonth();
            }

            $this->info(
                'Monthly Performance Excellence completed. '
                . "Period: {$stats['period_start']} to {$stats['period_end']} | "
                . "Leaders: {$stats['leaders_scanned']} | "
                . "Pending Payout: {$stats['pending_payout']} | "
                . "Paid: {$stats['paid']} | "
                . "Not Qualified: {$stats['not_qualified']} | "
                . "Skipped: {$stats['skipped']} | "
                . "Already Processed: {$stats['already_processed']} | "
                . "Errors: {$stats['errors']}"
            );

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error('Monthly Performance Excellence failed: ' . $exception->getMessage());

            return self::FAILURE;
        }
    }
}
