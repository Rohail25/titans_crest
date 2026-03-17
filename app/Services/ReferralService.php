<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\User;
use App\Models\ReferralTree;
use App\Models\Setting;
use Illuminate\Support\Collection;
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

                try {
                    // Credit referrer
                    $this->walletService->addBalance(
                        $referrer,
                        $commission,
                        'referral_commission',
                        $user->id,
                        ['referred_user' => $user->name, 'percent' => $commissionPercent]
                    );

                    // Update referral tree
                    $referrer->referralTree->increment('commission_earned', $commission);
                } catch (\Throwable $exception) {
                    // Skip commission if upline has no active package or reached earning cap.
                }
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

    public function getDashboardTeamPerformance(User $user): array
    {
        $levels = $this->getTeamLevels($user, 5);
        $members = collect($levels)
            ->flatMap(static fn (array $levelMembers) => $levelMembers)
            ->values();

        $memberIds = $members->pluck('id')->unique()->values();
        $levelLookup = $members
            ->mapWithKeys(static fn (array $member) => [$member['id'] => $member['level']]);

        $recentTeamDeposits = collect();
        $monthlyTeamDeposit = 0;

        if ($memberIds->isNotEmpty()) {
            $recentTeamDeposits = Deposit::query()
                ->whereIn('user_id', $memberIds->all())
                ->where('status', 'confirmed')
                ->with('user:id,name')
                ->orderByDesc('confirmed_at')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->map(static function (Deposit $deposit) use ($levelLookup) {
                    $deposit->team_level = $levelLookup->get($deposit->user_id);

                    return $deposit;
                });

            $monthlyTeamDeposit = (float) Deposit::query()
                ->whereIn('user_id', $memberIds->all())
                ->where('status', 'confirmed')
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('amount');
        }

        return [
            'summary' => [
                'team_members' => $members->count(),
                'team_with_deposit' => $members->where('total_deposit', '>', 0)->count(),
                'direct_team_deposit' => (float) collect($levels['level1'] ?? [])->sum('total_deposit'),
                'monthly_team_deposit' => $monthlyTeamDeposit,
                'total_team_deposit' => (float) $members->sum('total_deposit'),
            ],
            'levels' => $levels,
            'recent_deposits' => $recentTeamDeposits,
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

    private function getTeamLevels(User $user, int $maxLevels = 5): array
    {
        $levels = [];
        $currentReferrerIds = collect([$user->id]);

        for ($level = 1; $level <= $maxLevels; $level++) {
            $members = $this->getLevelUsers($currentReferrerIds, $level);

            if ($members->isEmpty()) {
                break;
            }

            $levels['level' . $level] = $members->all();
            $currentReferrerIds = $members->pluck('id');
        }

        return $levels;
    }

    private function getLevelUsers(Collection $referrerIds, int $level): Collection
    {
        if ($referrerIds->isEmpty()) {
            return collect();
        }

        return User::query()
            ->select(['id', 'name', 'referred_by', 'status', 'created_at'])
            ->whereIn('referred_by', $referrerIds->all())
            ->withSum([
                'deposits as total_deposit' => static function ($query) {
                    $query->where('status', 'confirmed');
                },
            ], 'amount')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(static function (User $referralUser) use ($level) {
                return [
                    'id' => $referralUser->id,
                    'name' => $referralUser->name,
                    'level' => $level,
                    'status' => $referralUser->status,
                    'joined_at' => $referralUser->created_at,
                    'total_deposit' => (float) ($referralUser->total_deposit ?? 0),
                ];
            });
    }
}
