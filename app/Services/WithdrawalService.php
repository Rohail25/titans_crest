<?php

namespace App\Services;

use App\Models\User;
use App\Models\Withdrawal;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class WithdrawalService
{
    protected WalletService $walletService;
    protected OTPService $otpService;

    const MINIMUM_WITHDRAWAL = 10;
    const WITHDRAWAL_DEDUCTION_PERCENT = 5;

    public function __construct(WalletService $walletService, OTPService $otpService)
    {
        $this->walletService = $walletService;
        $this->otpService = $otpService;
    }

    /**
     * Validate withdrawal request
     */
    public function validateWithdrawal(User $user, float $amount): array
    {
        $errors = [];
        $wallet = $user->wallet;

        if ($amount < self::MINIMUM_WITHDRAWAL) {
            $errors['amount'] = "Minimum withdrawal is $" . self::MINIMUM_WITHDRAWAL;
        }

        if ($amount > $wallet->balance) {
            $errors['balance'] = "Insufficient balance. Available: $" . $wallet->balance;
        }

        // Check if suspicious funds
        if ($this->containsSuspiciousFunds($user, $amount)) {
            $errors['suspicious'] = "Cannot withdraw from suspicious balance";
        }

        return $errors;
    }

    /**
     * Check if withdrawal amount contains suspicious funds
     */
    protected function containsSuspiciousFunds(User $user, float $amount): bool
    {
        $wallet = $user->wallet;
        
        // If total balance contains suspicious funds and requested amount might include them
        if ($wallet->suspicious_balance > 0) {
            // Simple check: if withdrawal amount is more than (balance - suspicious)
            $cleanBalance = $wallet->balance - $wallet->suspicious_balance;
            return $amount > $cleanBalance;
        }

        return false;
    }

    /**
     * Calculate withdrawal deduction
     */
    public function calculateDeduction(float $amount): float
    {
        return $amount * (self::WITHDRAWAL_DEDUCTION_PERCENT / 100);
    }

    /**
     * Start withdrawal process (step 1: initiate)
     */
    public function initiateWithdrawal(User $user, float $amount, string $walletAddress): Withdrawal
    {
        // Validate
        $errors = $this->validateWithdrawal($user, $amount);
        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }

        // Validate wallet address format
        if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $walletAddress)) {
            throw new \Exception('Invalid BNB wallet address format');
        }

        $deduction = $this->calculateDeduction($amount);
        $netAmount = $amount - $deduction;

        return DB::transaction(function () use ($user, $amount, $deduction, $netAmount, $walletAddress) {
            // Create withdrawal record with pending_otp status
            return Withdrawal::create([
                'user_id' => $user->id,
                'requested_amount' => $amount,
                'deduction_amount' => $deduction,
                'net_amount' => $netAmount,
                'wallet_address' => $walletAddress,
                'status' => 'pending_otp',
            ]);
        });
    }

    /**
     * Verify OTP and move to pending_approval
     */
    public function verifyOTPAndLockFunds(Withdrawal $withdrawal, string $otp): bool
    {
        if ($withdrawal->status !== 'pending_otp') {
            throw new \Exception('Withdrawal is not awaiting OTP verification');
        }

        // Verify OTP
        if (!$this->otpService->verifyOTP($withdrawal->user_id, $otp, 'withdrawal')) {
            return false;
        }

        DB::transaction(function () use ($withdrawal) {
            // Deduct from wallet immediately
            $this->walletService->deductBalance(
                $withdrawal->user,
                $withdrawal->requested_amount,
                'withdrawal_initiated',
                $withdrawal->id
            );

            // Update withdrawal status
            $withdrawal->update(['status' => 'pending_approval']);
        });

        return true;
    }

    /**
     * Approve withdrawal (admin action, not implemented here)
     */
    public function approveWithdrawal(Withdrawal $withdrawal, string $walletAddress): void
    {
        if ($withdrawal->status !== 'pending_approval') {
            throw new \Exception('Withdrawal is not in pending approval status');
        }

        DB::transaction(function () use ($withdrawal, $walletAddress) {
            $withdrawal->update([
                'status' => 'approved',
                'wallet_address' => $walletAddress,
                'approved_at' => now(),
            ]);

            // Dispatch payment event
            // event(new WithdrawalApproved($withdrawal));
        });
    }

    /**
     * Reject withdrawal and restore balance
     */
    public function rejectWithdrawal(Withdrawal $withdrawal, string $reason): void
    {
        if ($withdrawal->status === 'approved') {
            throw new \Exception('Cannot reject approved withdrawal');
        }

        DB::transaction(function () use ($withdrawal, $reason) {
            // If funds were already deducted, restore them
            if ($withdrawal->status === 'pending_approval') {
                $withdrawalService = app(WithdrawalService::class);
                $withdrawalService->walletService->addBalance(
                    $withdrawal->user,
                    $withdrawal->requested_amount,
                    'refund',
                    $withdrawal->id,
                    ['original_withdrawal_id' => $withdrawal->id]
                );
            }

            $withdrawal->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
            ]);
        });
    }

    /**
     * Get withdrawal history
     */
    public function getWithdrawalHistory(User $user, int $limit = 10): array
    {
        return $user->withdrawals()
            ->latest()
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get withdrawal statistics
     */
    public function getWithdrawalStats(User $user): array
    {
        return [
            'total_withdrawn' => $user->withdrawals()->where('status', 'approved')->sum('net_amount'),
            'pending_count' => $user->withdrawals()->whereIn('status', ['pending_otp', 'pending_approval'])->count(),
            'pending_amount' => $user->withdrawals()->whereIn('status', ['pending_otp', 'pending_approval'])->sum('requested_amount'),
            'total_withdrawals' => $user->withdrawals()->where('status', 'approved')->count(),
        ];
    }
}
