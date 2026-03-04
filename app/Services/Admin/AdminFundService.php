<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;

class AdminFundService
{
    public static function addFundsToWallet(User $user, User $admin, float $amount, string $reason): void
    {
        DB::transaction(function () use ($user, $admin, $amount, $reason) {
            WalletService::addBalance($user, $amount, 'admin_fund_add', null, ['reason' => $reason]);
            
            AuditLogService::log(
                $admin,
                'add_funds',
                'Wallet',
                $user->id,
                null,
                ['amount' => $amount, 'reason' => $reason],
                $reason
            );
        });
    }

    public static function deductFundsFromWallet(User $user, User $admin, float $amount, string $reason): void
    {
        DB::transaction(function () use ($user, $admin, $amount, $reason) {
            $wallet = $user->wallet;
            if ($wallet->balance < $amount) {
                throw new \Exception('Insufficient balance');
            }

            WalletService::deductBalance($user, $amount, 'admin_fund_deduct', null, $reason);
            
            AuditLogService::log(
                $admin,
                'deduct_funds',
                'Wallet',
                $user->id,
                ['balance' => $wallet->balance],
                ['balance' => $wallet->balance - $amount],
                $reason
            );
        });
    }

    public static function convertSuspiciousFunds(User $user, User $admin, float $amount, string $reason): void
    {
        DB::transaction(function () use ($user, $admin, $amount, $reason) {
            $wallet = $user->wallet;
            if ($wallet->suspicious_balance < $amount) {
                throw new \Exception('Insufficient suspicious balance');
            }

            $converted = WalletService::convertSuspiciousBalance($user, $amount);
            
            if ($converted) {
                AuditLogService::log(
                    $admin,
                    'convert_suspicious_funds',
                    'Wallet',
                    $user->id,
                    ['suspicious_balance' => $wallet->suspicious_balance],
                    ['suspicious_balance' => $wallet->suspicious_balance - $amount],
                    $reason
                );
            }
        });
    }

    public static function getLedgerForUser(User $user, int $limit = 100)
    {
        return $user->earnings()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getFundStats()
    {
        return [
            'total_balance' => DB::table('wallets')->sum('balance'),
            'total_pending' => DB::table('wallets')->sum('pending_balance'),
            'total_suspicious' => DB::table('wallets')->sum('suspicious_balance'),
            'total_earnings' => DB::table('wallets')->sum('total_earned'),
            'total_deposits' => DB::table('wallets')->sum('total_deposit'),
        ];
    }
}
