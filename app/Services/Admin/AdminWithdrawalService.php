<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\Withdrawal;
use App\Services\WalletService;
use App\Models\EmailLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminWithdrawalService
{
    public static function getWithdrawals(?string $status = null, int $perPage = 10)
    {
        $query = Withdrawal::with('user')->orderBy('created_at', 'desc');

        if (!empty($status)) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public static function getPendingWithdrawals($limit = 50)
    {
        return Withdrawal::with('user')
            ->where('status', 'pending_approval')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public static function getWithdrawalById(int $id): ?Withdrawal
    {
        return Withdrawal::with('user')->findOrFail($id);
    }

    public static function approveWithdrawal(Withdrawal $withdrawal, User $admin, string $txHash): void
    {
        DB::transaction(function () use ($withdrawal, $admin, $txHash) {
            if ($withdrawal->status !== 'pending_approval') {
                throw new \Exception('Withdrawal cannot be approved. Status: ' . $withdrawal->status);
            }

            $walletService = new WalletService();
            $walletService->releasePendingBalance($withdrawal->user, (float) $withdrawal->requested_amount);

            $withdrawal->update([
                'status' => 'approved',
                'tx_hash' => $txHash,
                'approved_at' => now(),
            ]);

            AuditLogService::log(
                $admin,
                'approve_withdrawal',
                'Withdrawal',
                $withdrawal->id,
                ['status' => 'pending_approval'],
                ['status' => 'approved', 'tx_hash' => $txHash]
            );

            // Log email notification
            EmailLog::create([
                'user_id' => $withdrawal->user_id,
                'recipient' => $withdrawal->user->email,
                'subject' => 'Withdrawal Approved',
                'body' => "Your withdrawal of BNB {$withdrawal->net_amount} has been approved and sent to {$withdrawal->wallet_address}. Transaction hash: {$txHash}",
                'type' => 'withdrawal',
                'status' => 'sent',
            ]);
        });
    }

    public static function rejectWithdrawal(Withdrawal $withdrawal, User $admin, string $reason): void
    {
        DB::transaction(function () use ($withdrawal, $admin, $reason) {
            if ($withdrawal->status !== 'pending_approval') {
                throw new \Exception('Withdrawal cannot be rejected. Status: ' . $withdrawal->status);
            }

            // Refund balance if funds were locked
            if ($withdrawal->status === 'pending_approval') {
                $walletService = new WalletService();
                $walletService->releasePendingBalance($withdrawal->user, (float) $withdrawal->requested_amount);
                $walletService->addBalance(
                    $withdrawal->user,
                    (float) $withdrawal->requested_amount,
                    'withdrawal_refund',
                    (string) $withdrawal->id,
                    ['reason' => $reason]
                );
            }

            $withdrawal->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'rejected_at' => now(),
            ]);

            AuditLogService::log(
                $admin,
                'reject_withdrawal',
                'Withdrawal',
                $withdrawal->id,
                ['status' => 'pending_approval'],
                ['status' => 'rejected', 'reason' => $reason],
                $reason
            );

            // Log email notification
            EmailLog::create([
                'user_id' => $withdrawal->user_id,
                'recipient' => $withdrawal->user->email,
                'subject' => 'Withdrawal Rejected',
                'body' => "Your withdrawal request has been rejected. Reason: {$reason}. Your funds have been refunded.",
                'type' => 'withdrawal',
                'status' => 'sent',
            ]);
        });
    }

    public static function getWithdrawalStats()
    {
        return [
            'pending_count' => Withdrawal::where('status', 'pending_approval')->count(),
            'pending_total' => Withdrawal::where('status', 'pending_approval')->sum('net_amount'),
            'approved_today' => Withdrawal::where('status', 'approved')
                ->where('approved_at', '>=', now()->startOfDay())
                ->count(),
            'approved_today_total' => Withdrawal::where('status', 'approved')
                ->where('approved_at', '>=', now()->startOfDay())
                ->sum('net_amount'),
            'rejected_today' => Withdrawal::where('status', 'rejected')
                ->where('rejected_at', '>=', now()->startOfDay())
                ->count(),
        ];
    }
}
