<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\MonthlyPerformanceExcellenceReward;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonthlyPerformanceExcellenceService
{
    /**
     * Tier matrix (target volume and leg requirements).
     */
    private const TIER_MATRIX = [
        ['target_volume' => 4000.00, 'reward_amount' => 80.00, 'min_legs' => 2],
        ['target_volume' => 10000.00, 'reward_amount' => 250.00, 'min_legs' => 3],
        ['target_volume' => 20000.00, 'reward_amount' => 500.00, 'min_legs' => 3],
        ['target_volume' => 40000.00, 'reward_amount' => 1200.00, 'min_legs' => 4],
        ['target_volume' => 80000.00, 'reward_amount' => 2400.00, 'min_legs' => 4],
        ['target_volume' => 150000.00, 'reward_amount' => 5000.00, 'min_legs' => 5],
        ['target_volume' => 300000.00, 'reward_amount' => 10000.00, 'min_legs' => 5],
    ];

    public function __construct(
        private WalletService $walletService
    ) {}

    /**
     * Process the latest fully closed month.
     */
    public function distributeForClosedMonth(): array
    {
        $closedMonthStart = now()->startOfMonth()->subMonthNoOverflow()->startOfMonth();

        return $this->distributeForMonth($closedMonthStart);
    }

    /**
     * Process a specific month by month start date.
     */
    public function distributeForMonth(Carbon $monthStart): array
    {
        $periodStart = $monthStart->copy()->startOfMonth();
        $periodEnd = $monthStart->copy()->endOfMonth();

        $stats = [
            'leaders_scanned' => 0,
            'processed' => 0,
            'pending_payout' => 0,
            'paid' => 0,
            'not_qualified' => 0,
            'skipped' => 0,
            'already_processed' => 0,
            'errors' => 0,
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
        ];

        $minimumRegistrationDays = max(0, (int) Setting::get('monthly_performance_min_registration_days', 30));
        $eligibilityCutoff = $periodEnd->copy()->subDays($minimumRegistrationDays)->endOfDay();

        $leaderIds = User::query()
            ->whereNotNull('referred_by')
            ->distinct()
            ->pluck('referred_by')
            ->filter()
            ->values();

        if ($leaderIds->isEmpty()) {
            return $stats;
        }

        $eligibleLeadersQuery = User::query()
            ->whereIn('id', $leaderIds->all());

        if ($minimumRegistrationDays > 0) {
            $eligibleLeadersQuery->where('created_at', '<=', $eligibilityCutoff);
        }

        $stats['leaders_scanned'] = (clone $eligibleLeadersQuery)->count();

        if ($stats['leaders_scanned'] === 0) {
            return $stats;
        }

        $eligibleLeadersQuery
            ->orderBy('id')
            ->chunkById(200, function ($leaders) use (&$stats, $periodStart, $periodEnd) {
                foreach ($leaders as $leader) {
                    if (!$leader instanceof User) {
                        continue;
                    }

                    try {
                        $result = $this->processLeaderForPeriod($leader, $periodStart, $periodEnd);
                        if (array_key_exists($result, $stats)) {
                            $stats[$result]++;
                        }
                    } catch (\Throwable $exception) {
                        $stats['errors']++;

                        Log::error('Monthly performance excellence processing failed', [
                            'leader_user_id' => $leader->id,
                            'period_start' => $periodStart->toDateString(),
                            'period_end' => $periodEnd->toDateString(),
                            'reason' => $exception->getMessage(),
                        ]);
                    }
                }
            });

        return $stats;
    }

    /**
     * Public resolver for deterministic tier matching and test scenarios.
     */
    public static function resolveQualifyingTier(float $totalVolume, int $qualifiedLegs): ?array
    {
        $tiers = collect(self::TIER_MATRIX)->sortBy('target_volume')->values();

        if ($tiers->isEmpty()) {
            return null;
        }

        $firstTier = $tiers->first();
        if (!is_array($firstTier) || $qualifiedLegs < (int) ($firstTier['min_legs'] ?? 0)) {
            return null;
        }

        $highestCrossedIndex = null;
        foreach ($tiers as $index => $tier) {
            if ($totalVolume >= (float) $tier['target_volume']) {
                $highestCrossedIndex = $index;
            }
        }

        if ($highestCrossedIndex === null) {
            return null;
        }

        $highestCrossedTier = $tiers->get($highestCrossedIndex);
        if (!is_array($highestCrossedTier)) {
            return null;
        }

        if ($qualifiedLegs >= (int) $highestCrossedTier['min_legs']) {
            return [
                'target_volume' => (float) $highestCrossedTier['target_volume'],
                'reward_amount' => (float) $highestCrossedTier['reward_amount'],
                'min_legs' => (int) $highestCrossedTier['min_legs'],
            ];
        }

        // If user crossed a higher target but missed that tier's leg condition,
        // fallback to the previous crossed target instead of dropping to smallest tier.
        if ($highestCrossedIndex > 0) {
            $fallbackTier = $tiers->get($highestCrossedIndex - 1);

            if (is_array($fallbackTier)) {
                return [
                    'target_volume' => (float) $fallbackTier['target_volume'],
                    'reward_amount' => (float) $fallbackTier['reward_amount'],
                    'min_legs' => (int) $fallbackTier['min_legs'],
                ];
            }
        }

        return [
            'target_volume' => (float) $firstTier['target_volume'],
            'reward_amount' => (float) $firstTier['reward_amount'],
            'min_legs' => (int) $firstTier['min_legs'],
        ];
    }

    public function getUserMonthlyPerformanceSummary(User $user): array
    {
        $records = MonthlyPerformanceExcellenceReward::query()
            ->where('sponsor_user_id', $user->id)
            ->orderByDesc('period_start')
            ->get();

        $latest = $records->first();
        $paidRecords = $records->where('status', 'paid');

        return [
            'total_earned' => (float) $paidRecords->sum('qualifying_tier_reward'),
            'total_months_processed' => $records->count(),
            'qualified_months' => $records->whereIn('status', ['paid', 'qualified_skipped', 'pending_payout'])->count(),
            'last_reward_amount' => (float) ($latest?->qualifying_tier_reward ?? 0),
            'last_total_volume' => (float) ($latest?->total_volume ?? 0),
            'last_period' => $latest
                ? [
                    'period_start' => optional($latest->period_start)->toDateString(),
                    'period_end' => optional($latest->period_end)->toDateString(),
                    'status' => $latest->status,
                    'qualified_legs' => (int) $latest->qualified_legs,
                    'tier_volume' => $latest->qualifying_tier_volume !== null ? (float) $latest->qualifying_tier_volume : null,
                ]
                : null,
        ];
    }

    public function getUserMonthlyPerformanceRecords(User $user)
    {
        return MonthlyPerformanceExcellenceReward::query()
            ->where('sponsor_user_id', $user->id)
            ->orderByDesc('period_start')
            ->orderByDesc('id')
            ->get();
    }

    private function processLeaderForPeriod(User $leader, Carbon $periodStart, Carbon $periodEnd): string
    {
        return DB::transaction(function () use ($leader, $periodStart, $periodEnd) {
            $isOpenMonth = $periodEnd->isSameMonth(now()) && $periodEnd->isSameYear(now());

            $existing = MonthlyPerformanceExcellenceReward::query()
                ->where('sponsor_user_id', $leader->id)
                ->whereDate('period_start', $periodStart->toDateString())
                ->whereDate('period_end', $periodEnd->toDateString())
                ->lockForUpdate()
                ->first();

            $legVolumes = $this->calculateLegVolumes($leader->id, $periodStart, $periodEnd);
            $qualifiedLegs = collect($legVolumes)->filter(static fn (float $volume) => $volume > 0)->count();
            $totalVolume = round((float) collect($legVolumes)->sum(), 2);
            $tier = self::resolveQualifyingTier($totalVolume, $qualifiedLegs);
            $targetRewardAmount = round((float) ($tier['reward_amount'] ?? 0), 2);

            if ($existing && $existing->status === 'rejected') {
                return 'already_processed';
            }

            $priorPaidAmount = (float) ($existing?->metadata['total_paid_amount'] ?? 0);

            if ($existing && $existing->status === 'paid' && !$isOpenMonth) {
                return 'already_processed';
            }

            if ($existing && $existing->status === 'paid' && $priorPaidAmount <= 0) {
                $priorPaidAmount = (float) $existing->qualifying_tier_reward;
            }

            $pendingAmount = max(0.0, round($targetRewardAmount - $priorPaidAmount, 2));

            $baseMetadata = array_merge($existing?->metadata ?? [], [
                'calculation_depth' => 4,
                'reward_matrix' => self::TIER_MATRIX,
                'recalculated_at' => now()->toDateTimeString(),
            ]);

            if ($existing) {
                $nextStatus = $tier
                    ? ($pendingAmount > 0 ? 'pending_payout' : ($priorPaidAmount > 0 ? 'paid' : 'pending_payout'))
                    : ($priorPaidAmount > 0 ? 'paid' : 'not_qualified');

                $existing->update([
                    'total_volume' => $totalVolume,
                    'qualified_legs' => $qualifiedLegs,
                    'qualifying_tier_volume' => $tier['target_volume'] ?? null,
                    'qualifying_tier_reward' => $targetRewardAmount,
                    'qualifying_tier_min_legs' => $tier['min_legs'] ?? null,
                    'status' => $nextStatus,
                    'paid_at' => $nextStatus === 'paid' ? $existing->paid_at : null,
                    'leg_volumes' => $legVolumes,
                    'metadata' => array_merge($baseMetadata, [
                        'total_paid_amount' => $priorPaidAmount,
                        'pending_payout_amount' => $pendingAmount,
                    ]),
                ]);
                $reward = $existing->fresh();
            } else {
                $reward = MonthlyPerformanceExcellenceReward::create([
                    'sponsor_user_id' => $leader->id,
                    'period_start' => $periodStart->toDateString(),
                    'period_end' => $periodEnd->toDateString(),
                    'total_volume' => $totalVolume,
                    'qualified_legs' => $qualifiedLegs,
                    'qualifying_tier_volume' => $tier['target_volume'] ?? null,
                    'qualifying_tier_reward' => $targetRewardAmount,
                    'qualifying_tier_min_legs' => $tier['min_legs'] ?? null,
                    'status' => $tier ? 'pending_payout' : 'not_qualified',
                    'leg_volumes' => $legVolumes,
                    'metadata' => array_merge($baseMetadata, [
                        'total_paid_amount' => 0,
                        'pending_payout_amount' => $targetRewardAmount,
                    ]),
                ]);
            }

            if (!$tier) {
                return 'not_qualified';
            }

            return $reward->status === 'pending_payout' ? 'pending_payout' : 'already_processed';
        });
    }

    public function approveReward(MonthlyPerformanceExcellenceReward $reward, int $adminId): void
    {
        DB::transaction(function () use ($reward, $adminId) {
            $lockedReward = MonthlyPerformanceExcellenceReward::query()
                ->whereKey($reward->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedReward->status !== 'pending_payout') {
                throw new \RuntimeException('Only pending payouts can be confirmed.');
            }

            $alreadyPaidAmount = (float) ($lockedReward->metadata['total_paid_amount'] ?? 0);
            $targetRewardAmount = round((float) $lockedReward->qualifying_tier_reward, 2);
            $rewardAmount = max(0.0, round($targetRewardAmount - $alreadyPaidAmount, 2));

            if ($rewardAmount <= 0) {
                throw new \RuntimeException('Reward amount must be greater than zero for confirmation.');
            }

            $leader = User::query()->findOrFail($lockedReward->sponsor_user_id);
            $periodStart = optional($lockedReward->period_start)->toDateString();
            $periodEnd = optional($lockedReward->period_end)->toDateString();

            $earning = $this->walletService->addBalance(
                $leader,
                $rewardAmount,
                'bonus',
                (string) $lockedReward->id,
                [
                    'source' => 'monthly_performance_excellence_tiered_bonus',
                    'reward_category' => 'monthly_performance_excellence',
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'target_volume' => (float) $lockedReward->qualifying_tier_volume,
                    'required_legs' => (int) $lockedReward->qualifying_tier_min_legs,
                    'qualified_legs' => (int) $lockedReward->qualified_legs,
                    'calculation_depth' => 4,
                    'approved_by_admin_id' => $adminId,
                ]
            );

            $lockedReward->update([
                'status' => 'paid',
                'paid_at' => now(),
                'metadata' => array_merge($lockedReward->metadata ?? [], [
                    'approved_by_admin_id' => $adminId,
                    'earning_id' => $earning->id,
                    'earning_amount' => (float) $earning->amount,
                    'total_paid_amount' => round($alreadyPaidAmount + (float) $earning->amount, 2),
                    'pending_payout_amount' => 0,
                ]),
            ]);
        });
    }

    public function rejectReward(MonthlyPerformanceExcellenceReward $reward, int $adminId, ?string $reason = null): void
    {
        DB::transaction(function () use ($reward, $adminId, $reason) {
            $lockedReward = MonthlyPerformanceExcellenceReward::query()
                ->whereKey($reward->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedReward->status !== 'pending_payout') {
                throw new \RuntimeException('Only pending payouts can be rejected.');
            }

            $lockedReward->update([
                'status' => 'rejected',
                'paid_at' => null,
                'metadata' => array_merge($lockedReward->metadata ?? [], [
                    'rejected_by_admin_id' => $adminId,
                    'rejection_reason' => $reason,
                    'rejected_at' => now()->toDateTimeString(),
                ]),
            ]);
        });
    }

    /**
     * Build per-leg business volume from level 1 to level 4 only.
     */
    private function calculateLegVolumes(int $leaderUserId, Carbon $periodStart, Carbon $periodEnd): array
    {
        $legVolumes = [];

        $directLegIds = User::query()
            ->where('referred_by', $leaderUserId)
            ->pluck('id');

        foreach ($directLegIds as $directLegId) {
            $legVolumes[(string) $directLegId] = $this->calculateLegBranchVolume((int) $directLegId, $periodStart, $periodEnd);
        }

        return $legVolumes;
    }

    private function calculateLegBranchVolume(int $rootLegUserId, Carbon $periodStart, Carbon $periodEnd): float
    {
        // A leg is the direct referral plus its full downline tree up to Level 4.
        $treeUserIds = $this->getLegTreeUserIds($rootLegUserId, 4);

        if (empty($treeUserIds)) {
            return 0.0;
        }

        $volume = (float) Deposit::query()
            ->where('status', 'confirmed')
            ->whereIn('user_id', $treeUserIds)
            ->where(function ($query) use ($periodStart, $periodEnd) {
                $query->whereBetween('confirmed_at', [
                    $periodStart->copy()->startOfDay(),
                    $periodEnd->copy()->endOfDay(),
                ])->orWhereBetween('created_at', [
                    // Include deposits by creation month as well to avoid missing leg/team volume
                    // when confirmation happened later or confirmed_at is not reliably populated.
                    $periodStart->copy()->startOfDay(),
                    $periodEnd->copy()->endOfDay(),
                ]);
            })
            ->sum('amount');

        return round($volume, 2);
    }

    /**
     * Build a leg subtree starting from the direct referral and traversing down to max depth.
     * Depth is leader-relative where direct referral is level 1.
     */
    private function getLegTreeUserIds(int $rootLegUserId, int $maxDepth = 4): array
    {
        $allUserIds = collect([$rootLegUserId]);
        $currentLevelUserIds = collect([$rootLegUserId]);

        for ($depth = 1; $depth < $maxDepth; $depth++) {
            if ($currentLevelUserIds->isEmpty()) {
                break;
            }

            $nextLevelUserIds = User::query()
                ->whereIn('referred_by', $currentLevelUserIds->all())
                ->pluck('id');

            if ($nextLevelUserIds->isEmpty()) {
                break;
            }

            $allUserIds = $allUserIds->merge($nextLevelUserIds)->unique()->values();
            $currentLevelUserIds = $nextLevelUserIds;
        }

        return $allUserIds->all();
    }
}
