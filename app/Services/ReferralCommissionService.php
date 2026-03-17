<?php

namespace App\Services;

use App\Models\User;
use App\Models\ReferralCommission;
use App\Models\ReferralTree;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralCommissionService
{
    public function __construct(
        private WalletService $walletService
    ) {}

    /**
     * Distribute referral commissions when a user subscribes to a package.
     * 
     * @param User $user The user who subscribed
     * @param float $packagePrice The price of the package subscribed to
     * @return void
     */
    public function distributeCommissions(User $user, float $packagePrice): void
    {
        try {
            // Get referral commissions configuration
            $commissions = ReferralCommission::where('is_active', true)
                ->orderBy('level')
                ->get();

            if ($commissions->isEmpty()) {
                return;
            }

            // Get the user's referral tree (uplines)
            $uplines = $this->getUplines($user, $commissions->max('level'));

            foreach ($commissions as $commission) {
                if (!isset($uplines[$commission->level - 1])) {
                    continue;
                }

                $upline = $uplines[$commission->level - 1];
                $commissionAmount = ($packagePrice * $commission->percentage) / 100;

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
                            'percentage' => (float) $commission->percentage,
                            'from_user_id' => $user->id,
                            'from_user_name' => $user->name,
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
