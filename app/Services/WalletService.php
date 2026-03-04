<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Earning;
use Illuminate\Support\Facades\DB;

class WalletService
{
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
     * Check if user has reached 3x cap
     */
    public function has3xCapReached(User $user): bool
    {
        $wallet = $user->wallet;
        $maxEarnings = $wallet->total_deposit * 3;
        return $wallet->total_earned >= $maxEarnings;
    }

    /**
     * Get remaining earnings possible before 3x cap
     */
    public function get3xCapRemaining(User $user): float|int
    {
        $wallet = $user->wallet;
        $maxEarnings = $wallet->total_deposit * 3;
        $remaining = $maxEarnings - $wallet->total_earned;
        return max(0, $remaining);
    }

    /**
     * Add funds to wallet (with transaction)
     */
    public function addBalance(User $user, float $amount, string $earningType, ?string $referenceId = null, ?array $metadata = null): Earning
    {
        return DB::transaction(function () use ($user, $amount, $earningType, $referenceId, $metadata) {
            $wallet = $this->getOrCreateWallet($user);
            
            // Check 3x cap before adding earnings (excluding deposits)
            if ($earningType !== 'deposit' && $this->has3xCapReached($user)) {
                throw new \Exception('User has reached 3x cap limit');
            }

            $wallet->increment('balance', $amount);
            
            // Update total_earned for non-deposit entries
            if ($earningType !== 'deposit') {
                $wallet->increment('total_earned', $amount);
            } else {
                $wallet->increment('total_deposit', $amount);
            }

            // Create ledger entry (immutable)
            return Earning::create([
                'user_id' => $user->id,
                'type' => $earningType,
                'reference_id' => $referenceId,
                'amount' => $amount,
                'metadata' => $metadata,
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
        $wallet = $user->wallet;
        $cap3x = $wallet->total_deposit * 3;
        $remaining = max(0, $cap3x - $wallet->total_earned);
        $capPercentage = $cap3x > 0 ? ($wallet->total_earned / $cap3x * 100) : 0;

        return [
            'balance' => $wallet->balance,
            'pending_balance' => $wallet->pending_balance,
            'suspicious_balance' => $wallet->suspicious_balance,
            'total_deposit' => $wallet->total_deposit,
            'total_earned' => $wallet->total_earned,
            'cap_3x' => $cap3x,
            'remaining_3x' => $remaining,
            'cap_percentage' => min(100, $capPercentage),
            'cap_reached' => $this->has3xCapReached($user),
        ];
    }
}
