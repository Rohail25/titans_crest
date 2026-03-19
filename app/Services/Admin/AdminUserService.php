<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Services\WalletService;
use App\Models\EmailLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminUserService
{
    public static function getFilteredUsers(array $filters = [], int $perPage = 15)
    {
        $allowedSorts = ['id', 'name', 'email', 'status', 'created_at'];
        $sort = in_array($filters['sort'] ?? 'created_at', $allowedSorts, true)
            ? ($filters['sort'] ?? 'created_at')
            : 'created_at';
        $direction = strtolower($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $query = User::with('wallet')->where('role', 'user');

        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('referral_code', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();
    }

    public static function getAllUsers(int $perPage = 15)
    {
        return self::getFilteredUsers([], $perPage);
    }

    public static function searchUsers(string $query, int $perPage = 15)
    {
        return self::getFilteredUsers(['search' => $query], $perPage);
    }

    public static function getUserById(int $id): ?User
    {
        return User::with([
            'wallet',
            'deposits',
            'withdrawals',
            'earnings.userPackage.package',
        ])->findOrFail($id);
    }

    public static function getReferralNetwork(User $user): array
    {
        $levels = [];
        $visitedUserIds = collect([$user->id]);
        $referrerIds = collect([$user->id]);
        $level = 1;

        while ($referrerIds->isNotEmpty()) {
            $levelUsers = self::getReferralLevelUsers($referrerIds, $visitedUserIds);

            if ($levelUsers->isEmpty()) {
                break;
            }

            $levels[$level] = $levelUsers;
            $visitedUserIds = $visitedUserIds->merge($levelUsers->pluck('id'))->unique()->values();
            $referrerIds = $levelUsers->pluck('id');
            $level++;
        }

        $allUsers = collect($levels)->flatten(1);

        return [
            'levels' => $levels,
            'summary' => [
                'total_levels' => count($levels),
                'total_referrals' => $allUsers->count(),
                'total_network_deposit' => (float) $allUsers->sum('total_deposit'),
                'total_network_earned' => (float) $allUsers->sum('total_earned'),
            ],
        ];
    }

    public static function getUserDetailMetrics(User $user): array
    {
        $recentDeposits = $user->deposits
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        $recentWithdrawals = $user->withdrawals
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        $recentEarnings = $user->earnings
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        return [
            'recentDeposits' => $recentDeposits,
            'recentWithdrawals' => $recentWithdrawals,
            'recentEarnings' => $recentEarnings,
            'earningsSummary' => [
                'total_earnings' => (float) $user->earnings->sum('amount'),
                'profit_earnings' => (float) $user->earnings
                    ->whereIn('type', ['profit_share', 'daily_profit', 'roi_profit'])
                    ->sum('amount'),
                'referral_earnings' => (float) $user->earnings
                    ->whereIn('type', ['referral', 'referral_commission'])
                    ->sum('amount'),
                'total_entries' => $user->earnings->count(),
            ],
        ];
    }

    private static function getReferralLevelUsers(Collection $referrerIds, Collection $excludedUserIds): Collection
    {
        return User::query()
            ->select(['id', 'referred_by', 'status', 'created_at'])
            ->whereIn('referred_by', $referrerIds->all())
            ->whereNotIn('id', $excludedUserIds->all())
            ->with(['wallet:id,user_id,total_earned'])
            ->withSum([
                'deposits as total_deposit' => static function ($query) {
                    $query->where('status', 'confirmed');
                },
            ], 'amount')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(static function (User $referralUser) {
                $referralUser->total_deposit = (float) ($referralUser->total_deposit ?? 0);
                $referralUser->total_earned = (float) ($referralUser->wallet?->total_earned ?? 0);

                return $referralUser;
            });
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
            // Instantiate WalletService to call instance method
            $walletService = new WalletService();
            $walletService->addBalance($user, $amount, 'admin_fund_add', null, ['reason' => $reason]);
            
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
            'total_wallet_balance' => User::where('role', 'user')->sum(DB::raw('(SELECT balance FROM wallets WHERE wallets.user_id = users.id)')),
        ];
    }
}
