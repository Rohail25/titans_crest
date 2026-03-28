<?php

namespace App\Services;

use App\Models\User;
use App\Models\ReferralCommission;
use App\Models\ReferralTree;
use Illuminate\Support\Facades\Log;

class ReferralCommissionService
{
    private const LEADERSHIP_INSTANT_PERCENT = 7.0;

    public function __construct(
        private WalletService $walletService,
        private LeadershipPerformanceService $leadershipPerformanceService
    ) {}

    /**
     * Distribute referral commissions when a user subscribes to a package.
     * 
     * @param User $user The user who subscribed
     * @param float $packagePrice The price of the package subscribed to
     * @return void
     */
    public function distributeCommissions(User $user, float $packagePrice, ?string $triggerReferenceId = null, ?\Carbon\Carbon $subscriptionTime = null): void
    {
        // Use provided subscription time, or default to now if not provided (for backwards compatibility)
        $subscriptionTime = $subscriptionTime ?: now();
        try {
            // Always resolve at least direct upline so Leadership (Level 1) reward is guaranteed.
            $commissions = ReferralCommission::where('is_active', true)
                ->where('level', '>', 1)
                ->orderBy('level')
                ->get();

            $maxConfiguredLevel = (int) ($commissions->max('level') ?? 1);
            $uplines = $this->getUplines($user, max(1, $maxConfiguredLevel));

            if (isset($uplines[0])) {
                $directUpline = $uplines[0];
                $instantCommissionAmount = round(($packagePrice * self::LEADERSHIP_INSTANT_PERCENT) / 100, 2);

                if ($instantCommissionAmount > 0) {
                    try {
                        $this->walletService->addBalance(
                            $directUpline,
                            $instantCommissionAmount,
                            'referral_commission',
                            (string) $user->id,
                            [
                                'source' => 'leadership_performance_instant_commission',
                                'level' => 1,
                                'percentage' => self::LEADERSHIP_INSTANT_PERCENT,
                                'reward_category' => 'leadership_performance',
                                'leadership_phase' => 'instant',
                                'from_user_id' => $user->id,
                                'from_user_name' => $user->name,
                                'trigger_reference_id' => $triggerReferenceId,
                            ]
                        );

                        if ($directUpline->referralTree) {
                            $directUpline->referralTree->increment('commission_earned', $instantCommissionAmount);
                        }

                        // Daily bonus is always 1% of original Level 1 commission amount.
                        $this->leadershipPerformanceService->createRecurringBonusForDirectReferral(
                            $directUpline,
                            $user,
                            $instantCommissionAmount,
                            $triggerReferenceId,
                            $subscriptionTime  // Pass actual subscription time for exact 24-hour scheduling
                        );
                    } catch (\Throwable $exception) {
                        Log::info('Level 1 leadership instant commission skipped', [
                            'upline_id' => $directUpline->id,
                            'from_user_id' => $user->id,
                            'reason' => $exception->getMessage(),
                        ]);
                    }
                }
            }

            if ($commissions->isEmpty()) {
                return;
            }

            foreach ($commissions as $commission) {
                if (!isset($uplines[$commission->level - 1])) {
                    continue;
                }

                $upline = $uplines[$commission->level - 1];
                $effectivePercentage = (float) $commission->percentage;
                $commissionAmount = ($packagePrice * $effectivePercentage) / 100;

                if ($commissionAmount <= 0) {
                    continue;
                }

                try {
                    $earning = $this->walletService->addBalance(
                        $upline,
                        $commissionAmount,
                        'referral_commission',
                        (string) $user->id,
                        [
                            'source' => 'package_subscription',
                            'level' => $commission->level,
                            'percentage' => $effectivePercentage,
                            'reward_category' => 'referral_commission',
                            'from_user_id' => $user->id,
                            'from_user_name' => $user->name,
                            'trigger_reference_id' => $triggerReferenceId,
                        ]
                    );

                    if ($upline->referralTree) {
                        $upline->referralTree->increment('commission_earned', (float) $earning->amount);
                    }
                } catch (\Throwable $exception) {
                    Log::info('Referral commission skipped for upline due to earning cap', [
                        'upline_id' => $upline->id,
                        'from_user_id' => $user->id,
                        'level' => $commission->level,
                        'reason' => $exception->getMessage(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error distributing referral commissions: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'package_price' => $packagePrice,
            ]);
        }
    }

    /**
     * Get uplines for a user up to a specified level.
     * 
     * @param User $user The user
     * @param int $maxLevels Maximum levels to retrieve
     * @return array Array of upline users indexed by level (0-indexed)
     */
    private function getUplines(User $user, int $maxLevels): array
    {
        $uplines = [];
        $currentUser = $user;

        for ($level = 0; $level < $maxLevels; $level++) {
            $referrerTree = ReferralTree::where('user_id', $currentUser->id)->first();

            if (!$referrerTree || !$referrerTree->referrer_id) {
                break;
            }

            $upline = User::find($referrerTree->referrer_id);
            if (!$upline) {
                break;
            }

            $uplines[$level] = $upline;
            $currentUser = $upline;
        }

        return $uplines;
    }
}
