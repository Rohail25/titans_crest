<?php

namespace App\Services;

use App\Models\User;
use App\Models\Deposit;
use Illuminate\Support\Facades\DB;

class DepositService
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Create pending deposit record
     */
    public function createDeposit(User $user, float $amount, string $txHash = null): Deposit
    {
        return Deposit::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'tx_hash' => $txHash,
            'status' => 'pending',
            'network' => 'BNB',
        ]);
    }

    /**
     * Confirm deposit and credit wallet
     */
    public function confirmDeposit(Deposit $deposit): void
    {
        DB::transaction(function () use ($deposit) {
            if ($deposit->status !== 'pending') {
                throw new \Exception('Deposit is not in pending status');
            }

            // Credit wallet
            $this->walletService->addBalance(
                $deposit->user,
                $deposit->amount,
                'deposit',
                $deposit->id,
                ['tx_hash' => $deposit->tx_hash]
            );

            // Update deposit status
            $deposit->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            // Dispatch email notification
            // event(new DepositConfirmed($deposit));
        });
    }

    /**
     * Reject deposit
     */
    public function rejectDeposit(Deposit $deposit, string $reason = null): void
    {
        if ($deposit->status !== 'pending') {
            throw new \Exception('Only pending deposits can be rejected');
        }

        $deposit->update([
            'status' => 'rejected',
            'metadata' => array_merge($deposit->metadata ?? [], ['rejection_reason' => $reason]),
        ]);
    }

    /**
     * Get user deposit history
     */
    public function getUserDepositHistory(User $user, int $limit = 10): array
    {
        return $user->deposits()
            ->latest()
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get total deposits by status
     */
    public function getDepositStats(User $user): array
    {
        return [
            'total_confirmed' => $user->deposits()->where('status', 'confirmed')->sum('amount'),
            'pending_count' => $user->deposits()->where('status', 'pending')->count(),
            'pending_amount' => $user->deposits()->where('status', 'pending')->sum('amount'),
            'total_deposits' => $user->deposits()->where('status', 'confirmed')->count(),
        ];
    }
}
