<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Services\WalletService;
use App\Models\EmailLog;
use Illuminate\Support\Facades\DB;

class AdminUserService
{
    public static function getAllUsers($limit = 50)
    {
        return User::with('wallet')
            ->paginate($limit);
    }

    public static function searchUsers(string $query, $limit = 50)
    {
        return User::with('wallet')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('referral_code', 'like', "%{$query}%")
            ->paginate($limit);
    }

    public static function getUserById(int $id): ?User
    {
        return User::with('wallet', 'deposits', 'withdrawals', 'earnings')->findOrFail($id);
    }

    public static function banUser(User $user, User $admin, string $reason): void
    {
        DB::transaction(function () use ($user, $admin, $reason) {
            $oldValues = $user->only('status', 'ban_reason');
            
            $user->update([
                'status' => 'banned',
                'ban_reason' => $reason,
                'banned_at' => now(),
            ]);

            AuditLogService::log(
                $admin,
                'ban_user',
                'User',
                $user->id,
                $oldValues,
                ['status' => 'banned', 'ban_reason' => $reason],
                $reason
            );

            // Log email notification
            EmailLog::create([
                'user_id' => $user->id,
                'recipient' => $user->email,
                'subject' => 'Account Banned',
                'body' => "Your account has been banned. Reason: {$reason}",
                'type' => 'notification',
                'status' => 'sent',
            ]);
        });
    }

    public static function activateUser(User $user, User $admin): void
    {
        DB::transaction(function () use ($user, $admin) {
            $oldValues = $user->only('status', 'ban_reason', 'banned_at');
            
            $user->update([
                'status' => 'active',
                'ban_reason' => null,
                'banned_at' => null,
            ]);

            AuditLogService::log(
                $admin,
                'activate_user',
                'User',
                $user->id,
                $oldValues,
                ['status' => 'active', 'ban_reason' => null, 'banned_at' => null]
            );

            // Log email notification
            EmailLog::create([
                'user_id' => $user->id,
                'recipient' => $user->email,
                'subject' => 'Account Reactivated',
                'body' => 'Your account has been reactivated. You can now access all features.',
                'type' => 'notification',
                'status' => 'sent',
            ]);
        });
    }

    public static function addManualCredit(User $user, User $admin, float $amount, string $reason): void
    {
        DB::transaction(function () use ($user, $admin, $amount, $reason) {
            WalletService::addBalance($user, $amount, 'manual_credit', null, ['reason' => $reason]);
            
            AuditLogService::log(
                $admin,
                'manual_credit',
                'User',
                $user->id,
                null,
                ['amount' => $amount, 'reason' => $reason],
                $reason
            );

            // Log email notification
            EmailLog::create([
                'user_id' => $user->id,
                'recipient' => $user->email,
                'subject' => 'Manual Credit Applied',
                'body' => "A manual credit of {$amount} has been applied to your account. Reason: {$reason}",
                'type' => 'notification',
                'status' => 'sent',
            ]);
        });
    }

    public static function getUserStats()
    {
        return [
            'total_users' => User::where('role', 'user')->count(),
            'active_users' => User::where('role', 'user')->where('status', 'active')->count(),
            'banned_users' => User::where('role', 'user')->where('status', 'banned')->count(),
            'suspended_users' => User::where('role', 'user')->where('status', 'suspended')->count(),
            'total_wallet_balance' => User::where('role', 'user')->sum(\DB::raw('(SELECT balance FROM wallets WHERE wallets.user_id = users.id)')),
        ];
    }
}
