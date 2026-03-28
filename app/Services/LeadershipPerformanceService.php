<?php

namespace App\Services;

use App\Models\LeadershipPerformanceReward;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeadershipPerformanceService
{
    private const INSTANT_COMMISSION_PERCENT = 7.0;
    private const DAILY_BONUS_PERCENT_OF_COMMISSION = 1.0;
    private const DAILY_BONUS_DAYS = 100;

    public function __construct(
        private WalletService $walletService
    ) {}

    public function createRecurringBonusForDirectReferral(
        User $sponsor,
        User $referredUser,
        float $instantCommissionAmount,
        ?string $triggerReferenceId = null,
        ?\Carbon\Carbon $subscriptionTime = null
    ): ?LeadershipPerformanceReward {
        if ($instantCommissionAmount <= 0) {
            return null;
        }

        $dailyBonusAmount = round(
            ($instantCommissionAmount * self::DAILY_BONUS_PERCENT_OF_COMMISSION) / 100,
            2
        );

        if ($dailyBonusAmount <= 0) {
            return null;
        }


        // Use provided subscription time for exact 24-hour scheduling, or default to now
        $subscriptionTime = $subscriptionTime ?: now();
        
        // Set first payout to automatically 24 hours from actual subscription time
        $firstPayoutAt = $subscriptionTime->copy()->addDay();
        return LeadershipPerformanceReward::create([
            'sponsor_user_id' => $sponsor->id,
            'referred_user_id' => $referredUser->id,
            'trigger_reference_id' => $triggerReferenceId,
            'instant_commission_amount' => $instantCommissionAmount,
            'daily_bonus_amount' => $dailyBonusAmount,
            'total_days' => self::DAILY_BONUS_DAYS,
            'payouts_remaining' => self::DAILY_BONUS_DAYS,
            'next_payout_date' => $firstPayoutAt->toDateString(),
            'next_payout_at' => $firstPayoutAt,
            'is_active' => true,
            'metadata' => [
                'reward_category' => 'leadership_performance',
                'leadership_phase' => 'recurring_daily_bonus',
                'instant_commission_percent' => self::INSTANT_COMMISSION_PERCENT,
                'daily_bonus_percent_of_commission' => self::DAILY_BONUS_PERCENT_OF_COMMISSION,
            ],
        ]);
    }

    public function distributeDailyBonuses(): array
    {
        $stats = [
            'processed' => 0,
            'paid' => 0,
            'skipped' => 0,
            'completed' => 0,
            'errors' => 0,
        ];

        $rewardIds = LeadershipPerformanceReward::query()
            ->where('is_active', true)
            ->where('payouts_remaining', '>', 0)
            ->where(function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereNotNull('next_payout_at')
                        ->where('next_payout_at', '<=', now());
                })->orWhere(function ($subQuery) {
                    $subQuery->whereNull('next_payout_at')
                        ->whereDate('next_payout_date', '<=', now()->toDateString());
                });
            })
            ->orderBy('id')
            ->pluck('id');

        foreach ($rewardIds as $rewardId) {
            $stats['processed']++;

            try {
                $result = DB::transaction(function () use ($rewardId) {
                    $reward = LeadershipPerformanceReward::query()
                        ->whereKey($rewardId)
                        ->lockForUpdate()
                        ->first();

                    if (!$reward || !$reward->is_active || $reward->payouts_remaining <= 0) {
                        return 'skip';
                    }

                    $nextPayoutAt = $reward->next_payout_at
                        ? Carbon::parse($reward->next_payout_at)
                        : Carbon::parse($reward->next_payout_date);  // Fallback for old records: use date as-is

                    if ($nextPayoutAt->isFuture()) {
                        return 'skip';
                    }

                    $sponsor = $reward->sponsor;
                    if (!$sponsor) {
                        $reward->update([
                            'is_active' => false,
                            'completed_at' => now(),
                        ]);

                        return 'skip';
                    }

                    $credited = false;

                    try {
                        $earning = $this->walletService->addBalance(
                            $sponsor,
                            (float) $reward->daily_bonus_amount,
                            'referral_commission',
                            (string) $reward->referred_user_id,
                            [
                                'source' => 'leadership_performance_daily_bonus',
                                'reward_category' => 'leadership_performance',
                                'leadership_phase' => 'daily',
                                'leadership_reward_id' => $reward->id,
                                'from_user_id' => $reward->referred_user_id,
                                'trigger_reference_id' => $reward->trigger_reference_id,
                                'daily_bonus_percent_of_commission' => self::DAILY_BONUS_PERCENT_OF_COMMISSION,
                            ]
                        );

                        if ($sponsor->referralTree) {
                            $sponsor->referralTree->increment('commission_earned', (float) $earning->amount);
                        }

                        $credited = true;
                    } catch (\Throwable $exception) {
                        Log::info('Leadership performance daily bonus skipped due to earning cap', [
                            'reward_id' => $reward->id,
                            'sponsor_user_id' => $sponsor->id,
                            'referred_user_id' => $reward->referred_user_id,
                            'reason' => $exception->getMessage(),
                        ]);
                    }

                    $remaining = max(0, ((int) $reward->payouts_remaining) - 1);
                    $upcomingPayoutAt = $nextPayoutAt->copy()->addDay();
                    $reward->update([
                        'payouts_remaining' => $remaining,
                        'next_payout_date' => $upcomingPayoutAt->toDateString(),
                        'next_payout_at' => $upcomingPayoutAt,
                        'last_paid_at' => now(),
                        'is_active' => $remaining > 0,
                        'completed_at' => $remaining > 0 ? null : now(),
                    ]);

                    if (!$credited) {
                        return $remaining > 0 ? 'skipped' : 'completed';
                    }

                    return $remaining > 0 ? 'paid' : 'completed_paid';
                });

                if ($result === 'paid') {
                    $stats['paid']++;
                } elseif ($result === 'completed_paid') {
                    $stats['paid']++;
                    $stats['completed']++;
                } elseif ($result === 'completed') {
                    $stats['completed']++;
                    $stats['skipped']++;
                } else {
                    $stats['skipped']++;
                }
            } catch (\Throwable $exception) {
                $stats['errors']++;

                Log::error('Leadership performance daily distribution failed', [
                    'reward_id' => $rewardId,
                    'reason' => $exception->getMessage(),
                ]);
            }
        }

        return $stats;
    }

    public function getUserLeadershipPerformanceSummary(User $user): array
    {
        $totalDailyBonusEarned = (float) $user->earnings()
            ->where('type', 'referral')
            ->where('metadata->source', 'leadership_performance_daily_bonus')
            ->sum('amount');

        $activeRewards = LeadershipPerformanceReward::query()
            ->where('sponsor_user_id', $user->id)
            ->where('is_active', true)
            ->count();

        $dailyBonusRunningAmount = (float) LeadershipPerformanceReward::query()
            ->where('sponsor_user_id', $user->id)
            ->where('is_active', true)
            ->sum('daily_bonus_amount');

        $dailyBonusReceivedToday = (float) $user->earnings()
            ->where('type', 'referral')
            ->where('metadata->source', 'leadership_performance_daily_bonus')
            ->whereDate('created_at', now()->toDateString())
            ->sum('amount');

        $lastDailyBonusPaidAt = LeadershipPerformanceReward::query()
            ->where('sponsor_user_id', $user->id)
            ->whereNotNull('last_paid_at')
            ->max('last_paid_at');

        $remainingBonusProjection = (float) LeadershipPerformanceReward::query()
            ->where('sponsor_user_id', $user->id)
            ->where('is_active', true)
            ->selectRaw('COALESCE(SUM(daily_bonus_amount * payouts_remaining), 0) as remaining_total')
            ->value('remaining_total');

        return [
            'total_earned' => $totalDailyBonusEarned,
            'active_rewards' => $activeRewards,
            'daily_running_amount' => $dailyBonusRunningAmount,
            'daily_bonus_received_today' => $dailyBonusReceivedToday,
            'last_daily_bonus_paid_at' => $lastDailyBonusPaidAt,
            'remaining_projection' => $remainingBonusProjection,
        ];
    }
}
