<?php

namespace App\Services;

use App\Models\User;
use App\Models\ProfitSharingLevel;
use App\Models\ReferralTree;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfitSharingService
{
    public function __construct(
        private WalletService $walletService
    ) {}

    /**
     * Distribute daily profit to uplines based on profit sharing configuration.
     * 
     * @param User $user The user who generated the profit
     * @param float $dailyProfit The daily profit amount to distribute
     * @return void
     */
    public function shareProfitWithUplines(User $user, float $dailyProfit): void
    {
        try {
            // Get profit sharing levels configuration
            $levels = ProfitSharingLevel::orderBy('level')->get();

            if ($levels->isEmpty() || $dailyProfit <= 0) {
                return;
            }

            // Get the user's uplines
            $uplines = $this->getUplines($user, $levels->max('level'));

            foreach ($levels as $level) {
                if (!isset($uplines[$level->level - 1])) {
                    continue;
                }

                $upline = $uplines[$level->level - 1];
                $shareAmount = ($dailyProfit * $level->percentage) / 100;

                if ($shareAmount <= 0) {
                    continue;
                }

                try {
                    $this->walletService->addBalance(
                        $upline,
                        $shareAmount,
                        'profit_share',
                        (string) $user->id,
                        [
                            'source' => 'upline_daily_profit_share',
                            'level' => $level->level,
                            'percentage' => (float) $level->percentage,
                            'from_user_id' => $user->id,
                            'from_user_name' => $user->name,
                        ]
                    );
                } catch (\Throwable $exception) {
                    Log::info('Profit share skipped for upline due to earning cap', [
                        'upline_id' => $upline->id,
                        'from_user_id' => $user->id,
                        'level' => $level->level,
                        'reason' => $exception->getMessage(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error distributing profit shares: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'daily_profit' => $dailyProfit,
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
