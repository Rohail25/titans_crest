<?php

namespace App\Services\Admin;

use App\Models\Deposit;
use App\Models\User;
use App\Models\Wallet;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;

class AdminFundService
{
    public static function addFundsToWallet(User $user, User $admin, float $amount, string $reason): void
    {
        DB::transaction(function () use ($user, $admin, $amount, $reason) {
            $deposit = Deposit::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'status' => 'confirmed',
                'network' => 'ADMIN',
                'metadata' => [
                    'source' => 'admin_fund_add',
                    'admin_id' => $admin->id,
                    'reason' => $reason,
                ],
            ]);
            $walletDeposit = Wallet::where('user_id', $user->id)->first();
            $walletDeposit->total_deposit += $amount;
            $walletDeposit->save();
            AuditLogService::log(
                $admin,
                'add_funds',
                'Deposit',
                $deposit->id,
                null,
                ['user_id' => $user->id, 'amount' => $amount, 'reason' => $reason],
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

            $walletService = new WalletService();
            $walletService->deductBalance($user, $amount, 'admin_fund_deduct: ' . $reason, null);
            
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

            $walletService = new WalletService();
            $converted = $walletService->convertSuspiciousBalance($user, $amount);
            
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
