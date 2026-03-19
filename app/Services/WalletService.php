<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Earning;
use App\Models\Package;
use App\Models\ReferralTree;
use App\Models\UserPackage;
use Illuminate\Support\Facades\DB;

class WalletService
{
    private const NON_CAPPED_EARNING_TYPES = [
        'deposit',
        'refund',
        'withdrawal_refund',
        'admin_fund_add',
    ];

    private const TRACKED_EARNING_TYPES = [
        'profit_share',
        'referral',
        'bonus',
    ];

    /**
     * Get or create user wallet
     */
    public function getOrCreateWallet(User $user): Wallet
    {
        return $user->wallet()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 0,
                'pending_balance' => 0,
                'suspicious_balance' => 0,
                'total_deposit' => 0,
                'total_earned' => 0,
            ]
        );
    }

    /**
     * Get all active earning packages for a user (refreshing cap data).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserPackage>
     */
    public function getActiveEarningPackages(User $user)
    {
        $packages = $user->userPackages()
            ->where('is_active', true)
            ->where('package_status', 'active')
            ->with('package')
            ->orderBy('id')
            ->get();

        return $packages->map(fn (UserPackage $userPackage) => $this->refreshUserPackageCap($user, $userPackage));
    }

    /**
     * Get the active earning package that still has remaining cap.
     * If multiple packages are active, the first package with remaining cap is returned.
     */
    public function getActiveEarningPackage(User $user): ?UserPackage
    {
        return $this->getActiveEarningPackages($user)
            ->filter(fn (UserPackage $up) => (float) $up->total_earned < (float) $up->earning_cap)
            ->sortBy('id')
            ->first();
    }

    /**
     * Check if user has reached earning cap on active packages
     */
    public function has3xCapReached(User $user): bool
    {
        $activePackages = $this->getActiveEarningPackages($user);

        if ($activePackages->isEmpty()) {
            return true;
        }

        $remaining = $activePackages->reduce(function ($carry, UserPackage $up) {
            return $carry + max(0, (float) $up->earning_cap - (float) $up->total_earned);
        }, 0);

        return $remaining <= 0;
    }

    /**
     * Get remaining earnings possible before package caps are reached
     */
    public function get3xCapRemaining(User $user): float|int
    {
        $activePackages = $this->getActiveEarningPackages($user);

        if ($activePackages->isEmpty()) {
            return 0;
        }

        $remaining = $activePackages->reduce(function ($carry, UserPackage $up) {
            return $carry + max(0, (float) $up->earning_cap - (float) $up->total_earned);
        }, 0);

        return max(0, $remaining);
    }

    /**
     * Add funds to wallet (with transaction)
     */
    public function addBalance(User $user, float $amount, string $earningType, ?string $referenceId = null, ?array $metadata = null): Earning
    {
        return DB::transaction(function () use ($user, $amount, $earningType, $referenceId, $metadata) {
            $wallet = $this->getOrCreateWallet($user);
            $normalizedType = $this->normalizeEarningType($earningType);
            $ledgerMetadata = $metadata ?? [];

            if ($normalizedType !== $earningType) {
                $ledgerMetadata['original_earning_type'] = $earningType;
            }

            if ($this->isCappedEarningType($normalizedType, $amount)) {
                $activePackage = null;

                // If an active package is explicitly provided, use that one.
                $userPackageId = $ledgerMetadata['user_package_id'] ?? null;
                if ($userPackageId) {
                    $activePackage = UserPackage::query()
                        ->where('id', $userPackageId)
                        ->where('user_id', $user->id)
                        ->where('is_active', true)
                        ->where('package_status', 'active')
                        ->with('package')
                        ->first();
                }

                // Fall back to any active package with remaining cap
                if (!$activePackage) {
                    $activePackage = $this->getActiveEarningPackage($user);
                }

                if (!$activePackage) {
                    throw new \Exception('Your package has reached earning limits. Please subscribe to a new package.');
                }

                $activePackage = $this->refreshUserPackageCap($user, $activePackage);
                $remaining = max(0, (float) $activePackage->earning_cap - (float) $activePackage->total_earned);

                if ($remaining <= 0) {
                    $this->completeUserPackage($activePackage);
                    throw new \Exception('Your package has reached the maximum earning limit.');
                }

                $creditAmount = min($amount, $remaining);

                $wallet->increment('balance', $creditAmount);
                $wallet->increment('total_earned', $creditAmount);

                $earning = Earning::create([
                    'user_id' => $user->id,
                    'type' => $normalizedType,
                    'reference_id' => $referenceId,
                    'amount' => $creditAmount,
                    'metadata' => array_merge($ledgerMetadata, [
                        'requested_amount' => $amount,
                        'cap_applied' => $creditAmount < $amount,
                        'user_package_id' => $activePackage->id,
                    ]),
                ]);

                $activePackage->increment('total_earned', $creditAmount);
                $activePackage->refresh();

                if ((float) $activePackage->total_earned >= (float) $activePackage->earning_cap) {
                    $this->completeUserPackage($activePackage);
                }

                return $earning;
            }

            $wallet->increment('balance', $amount);

            // Track only actual earnings in total_earned (exclude deposits/funding/refunds).
            if ($normalizedType === 'deposit') {
                $wallet->increment('total_deposit', $amount);
            } elseif ($amount > 0 && in_array($normalizedType, self::TRACKED_EARNING_TYPES, true)) {
                $wallet->increment('total_earned', $amount);
            }

            // Create ledger entry (immutable)
            return Earning::create([
                'user_id' => $user->id,
                'type' => $normalizedType,
                'reference_id' => $referenceId,
                'amount' => $amount,
                'metadata' => $ledgerMetadata,
            ]);
        });
    }

    /**
     * Deduct from balance (with transaction)
     */
    public function deductBalance(User $user, float $amount, string $reason, ?string $referenceId = null): void
    {
        DB::transaction(function () use ($user, $amount, $reason, $referenceId) {
            $wallet = $this->getOrCreateWallet($user);

            if ($wallet->balance < $amount) {
                throw new \Exception('Insufficient balance');
            }

            $wallet->decrement('balance', $amount);

            Earning::create([
                'user_id' => $user->id,
                'type' => 'withdrawal',
                'reference_id' => $referenceId,
                'amount' => -$amount,
                'metadata' => ['reason' => $reason],
            ]);
        });
    }

    /**
     * Add to suspicious balance
     */
    public function addSuspiciousBalance(User $user, float $amount, string $reason): void
    {
        DB::transaction(function () use ($user, $amount, $reason) {
            $wallet = $this->getOrCreateWallet($user);
            $wallet->increment('suspicious_balance', $amount);

            Earning::create([
                'user_id' => $user->id,
                'type' => 'suspicious',
                'amount' => $amount,
                'metadata' => ['reason' => $reason],
            ]);
        });
    }

    /**
     * Convert suspicious balance to regular balance if conditions are met
     */
    public function convertSuspiciousBalance(User $user, float $amount): bool
    {
        $wallet = $user->wallet;
        
        if ($wallet->suspicious_balance < $amount) {
            return false;
        }

        // Check if user has generated 3x earnings from suspicious funds
        $suspiciousEarnings = $user->earnings()
            ->where('type', 'suspicious')
            ->sum('amount');

        if ($suspiciousEarnings >= $amount * 3) {
            DB::transaction(function () use ($user, $amount, $wallet) {
                $wallet->decrement('suspicious_balance', $amount);
                $wallet->increment('balance', $amount);
            });

            return true;
        }

        return false;
    }

    /**
     * Get wallet summary for dashboard
     */
    public function getWalletSummary(User $user): array
    {
        $wallet = $this->getOrCreateWallet($user);
        $activePackages = $this->getActiveEarningPackages($user);

        $cap = $activePackages->sum(fn (UserPackage $up) => (float) $up->earning_cap);
        $earnedAgainstCap = $activePackages->sum(fn (UserPackage $up) => (float) $up->total_earned);
        $remaining = max(0, $cap - $earnedAgainstCap);
        $capPercentage = $cap > 0 ? ($earnedAgainstCap / $cap * 100) : 0;
        $capReached = $activePackages->isEmpty() || $remaining <= 0;

        $nextProfitTime = $activePackages
            ->pluck('next_profit_time')
            ->filter()
            ->sort()
            ->first();

        $lastProfitTime = $activePackages
            ->pluck('last_profit_time')
            ->filter()
            ->sort()
            ->last();

        $actualTotalEarned = (float) $user->earnings()
            ->whereIn('type', self::TRACKED_EARNING_TYPES)
            ->sum('amount');

        if (abs((float) $wallet->total_earned - $actualTotalEarned) > 0.0001) {
            $wallet->update(['total_earned' => $actualTotalEarned]);
            $wallet->refresh();
        }

        return [
            'balance' => $wallet->balance,
            'pending_balance' => $wallet->pending_balance,
            'suspicious_balance' => $wallet->suspicious_balance,
            'total_deposit' => $wallet->total_deposit,
            'total_earned' => $actualTotalEarned,
            'earned_against_cap' => $earnedAgainstCap,
            'cap_3x' => $cap,
            'remaining_3x' => $remaining,
            'cap_percentage' => min(100, $capPercentage),
            'cap_reached' => $capReached,
            'package_status' => $activePackages->isEmpty() ? 'completed' : 'active',
            'next_profit_time' => $nextProfitTime,
            'last_profit_time' => $lastProfitTime,
        ];
    }

    public function determineCapMultiplier(User $user, float $depositAmount): int
    {
        // Packages under $500: 3x cap
        // Packages $500 or more: 4x cap
        if ($depositAmount >= 500) {
            return 4;
        }

        return 3;
    }

    public function calculateCapForUser(User $user, float $depositAmount): float
    {
        return $depositAmount * $this->determineCapMultiplier($user, $depositAmount);
    }

    public function refreshUserPackageCap(User $user, UserPackage $userPackage): UserPackage
    {
        $packagePrice = (float) ($userPackage->package?->price ?? 0);

        if ($packagePrice <= 0 && $userPackage->package_id) {
            $packagePrice = (float) (Package::query()->whereKey($userPackage->package_id)->value('price') ?? 0);
        }

        $depositAmount = (float) ($userPackage->total_deposit ?: $packagePrice ?: 0);

        if ($depositAmount <= 0) {
            $depositAmount = (float) ($this->getOrCreateWallet($user)->total_deposit ?: 0);
        }

        $calculatedCap = $this->calculateCapForUser($user, $depositAmount);

        if ((float) $userPackage->total_deposit !== $depositAmount || (float) $userPackage->earning_cap !== $calculatedCap) {
            $userPackage->update([
                'total_deposit' => $depositAmount,
                'earning_cap' => $calculatedCap,
            ]);
        }

        return $userPackage->fresh();
    }

    public function completeUserPackage(UserPackage $userPackage): void
    {
        $userPackage->update([
            'is_active' => false,
            'package_status' => 'completed',
            'expires_at' => now(),
            'next_profit_time' => null,
        ]);
    }

    private function isCappedEarningType(string $earningType, float $amount): bool
    {
        return $amount > 0 && !in_array($earningType, self::NON_CAPPED_EARNING_TYPES, true);
    }

    private function normalizeEarningType(string $earningType): string
    {
        return match ($earningType) {
            'referral_commission' => 'referral',
            'roi_profit' => 'profit_share',
            'withdrawal_refund' => 'refund',
            default => $earningType,
        };
    }
}
