<?php

namespace App\Services;

use App\Models\User;
use App\Models\ReferralTree;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralService
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Generate unique referral code
     */
    public function generateReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (ReferralTree::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Initialize referral tree for user
     */
    public function initializeReferralTree(User $user, ?string $referrerCode = null): ReferralTree
    {
        return DB::transaction(function () use ($user, $referrerCode) {
            $referrerId = null;

            if ($referrerCode) {
                $referrer = ReferralTree::where('referral_code', $referrerCode)->first();
                if ($referrer) {
                    $referrerId = $referrer->user_id;
                    $user->update(['referred_by' => $referrerId]);
                }
            }

            $referralCode = $this->generateReferralCode();

            return ReferralTree::create([
                'user_id' => $user->id,
                'referrer_id' => $referrerId,
                'referral_code' => $referralCode,
                'commission_earned' => 0,
                'level' => $referrerId ? 1 : 0,
            ]);
        });
    }

    /**
     * Add referral and credit commission
     */
    public function addReferral(User $user): void
    {
        $referrer = $user->referrer;

        if (!$referrer) {
            return;
        }

        DB::transaction(function () use ($user, $referrer) {
            $commissionPercent = (float) Setting::get('referral_commission_percent', 10);
            
            // Get user's active package
            $activePackage = $user->userPackages()
                ->where('is_active', true)
                ->first();

            if ($activePackage) {
                $commission = $activePackage->package->price * ($commissionPercent / 100);

                // Credit referrer
                $this->walletService->addBalance(
                    $referrer,
                    $commission,
                    'referral',
                    $user->id,
                    ['referred_user' => $user->name, 'percent' => $commissionPercent]
                );

                // Update referral tree
                $referrer->referralTree->increment('commission_earned', $commission);
            }
        });
    }

    /**
     * Get referral tree
     */
    public function getReferralTree(User $user, int $depth = 5): array
    {
        $tree = ['user' => $user, 'referrals' => []];

        if ($depth > 0) {
            $referrals = ReferralTree::where('referrer_id', $user->id)->get();

            foreach ($referrals as $referral) {
                $tree['referrals'][] = $this->getReferralTree($referral->user, $depth - 1);
            }
        }

        return $tree;
    }

    /**
     * Get referral statistics
     */
    public function getReferralStats(User $user): array
    {
        $referralTree = $user->referralTree;
        $directReferrals = ReferralTree::where('referrer_id', $user->id)->count();
        $totalReferrals = $this->countTotalReferrals($user->id);

        return [
            'referral_code' => $referralTree?->referral_code,
            'direct_referrals' => $directReferrals,
            'total_referrals' => $totalReferrals,
            'commission_earned' => $referralTree?->commission_earned ?? 0,
        ];
    }

    /**
     * Count all referrals recursively
     */
    protected function countTotalReferrals(int $userId): int
    {
        $direct = ReferralTree::where('referrer_id', $userId)->count();
        $total = $direct;

        $directIds = ReferralTree::where('referrer_id', $userId)->pluck('user_id');
        foreach ($directIds as $id) {
            $total += $this->countTotalReferrals($id);
        }

        return $total;
    }

    /**
     * Get downline for user
     */
    public function getDownline(User $user, int $level = 1): array
    {
        $downline = [];
        $referrals = ReferralTree::where('referrer_id', $user->id)->get();

        foreach ($referrals as $referral) {
            $refUser = $referral->user;
            $downline[] = [
                'user' => $refUser,
                'level' => $level,
                'commission' => $referral->commission_earned,
            ];

            if ($level < 5) {
                $downline = array_merge($downline, $this->getDownline($refUser, $level + 1));
            }
        }

        return $downline;
    }
}
