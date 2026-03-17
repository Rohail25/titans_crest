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
     * Check if user has reached earning cap on active package
     */
    public function has3xCapReached(User $user): bool
    {
        $activePackage = $this->getActiveEarningPackage($user);

        if (!$activePackage) {
            return true;
        }

        $activePackage = $this->refreshUserPackageCap($user, $activePackage);

        return (float) $activePackage->total_earned >= (float) $activePackage->earning_cap;
    }

    /**
     * Get remaining earnings possible before package cap
     */
    public function get3xCapRemaining(User $user): float|int
    {
        $activePackage = $this->getActiveEarningPackage($user);

        if (!$activePackage) {
            return 0;
        }

        $activePackage = $this->refreshUserPackageCap($user, $activePackage);
        $remaining = (float) $activePackage->earning_cap - (float) $activePackage->total_earned;

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
                $activePackage = $this->getActiveEarningPackage($user);

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

            // Update total_earned for non-deposit entries
            if ($normalizedType !== 'deposit') {
                $wallet->increment('total_earned', $amount);
            } else {
                $wallet->increment('total_deposit', $amount);
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
        $activePackage = $this->getActiveEarningPackage($user);

        if ($activePackage) {
            $activePackage = $this->refreshUserPackageCap($user, $activePackage);
        }

        $cap = $activePackage ? (float) $activePackage->earning_cap : 0;
        $earnedAgainstCap = $activePackage ? (float) $activePackage->total_earned : 0;
        $remaining = max(0, $cap - $earnedAgainstCap);
        $capPercentage = $cap > 0 ? ($earnedAgainstCap / $cap * 100) : 0;
        $capReached = !$activePackage || $remaining <= 0;

        return [
            'balance' => $wallet->balance,
            'pending_balance' => $wallet->pending_balance,
            'suspicious_balance' => $wallet->suspicious_balance,
            'total_deposit' => $wallet->total_deposit,
            'total_earned' => $wallet->total_earned,
            'cap_3x' => $cap,
            'remaining_3x' => $remaining,
            'cap_percentage' => min(100, $capPercentage),
            'cap_reached' => $capReached,
            'package_status' => $activePackage?->package_status ?? 'completed',
            'next_profit_time' => $activePackage?->next_profit_time,
            'last_profit_time' => $activePackage?->last_profit_time,
        ];
    }

    public function getActiveEarningPackage(User $user): ?UserPackage
    {
        return $user->userPackages()
            ->where('is_active', true)
            ->where('package_status', 'active')
            ->with('package:id,price')
            ->latest('id')
            ->first();
    }

    public function determineCapMultiplier(User $user, float $depositAmount): int
    {
        if ($depositAmount >= 500) {
            return 4;
        }

        $hasDirectReferral = User::where('referred_by', $user->id)->exists()
            || ReferralTree::where('referrer_id', $user->id)->exists();

        return $hasDirectReferral ? 3 : 2;
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
