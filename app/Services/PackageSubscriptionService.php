<?php

namespace App\Services;

use App\Models\Earning;
use App\Models\Package;
use App\Models\User;
use App\Models\UserPackage;
use Illuminate\Support\Facades\DB;

class PackageSubscriptionService
{
    public function __construct(
        protected WalletService $walletService,
    ) {}

    public function subscribe(User $user, Package $package): UserPackage
    {
        return DB::transaction(function () use ($user, $package) {
            if (!$package->is_active) {
                throw new \Exception('Selected package is not active.');
            }

            $wallet = $this->walletService->getOrCreateWallet($user);
            if ((float) $wallet->balance < (float) $package->price) {
                throw new \Exception('Insufficient balance to subscribe this package.');
            }

            $wallet->decrement('balance', (float) $package->price);

            Earning::create([
                'user_id' => $user->id,
                'type' => 'package_subscription',
                'reference_id' => $package->id,
                'amount' => -((float) $package->price),
                'metadata' => [
                    'package_id' => $package->id,
                    'package_name' => $package->name,
                ],
            ]);

            $expiresAt = $package->duration_days
                ? now()->addDays((int) $package->duration_days)
                : null;

            $depositAmount = (float) $package->price;
            $earningCap = $this->walletService->calculateCapForUser($user, $depositAmount);

            $userPackage = UserPackage::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'activated_at' => now(),
                'expires_at' => $expiresAt,
                'is_active' => true,
                'total_deposit' => $depositAmount,
                'total_earned' => 0,
                'earning_cap' => $earningCap,
                'package_status' => 'active',
                'last_profit_time' => null,
                'next_profit_time' => $this->getNextProfitTime(),
            ]);

            // IMPORTANT: Commission is NOT distributed here anymore.
            // It will be distributed when the user's DEPOSIT is confirmed (see DepositService::confirmDeposit)
            // This ensures upline only gets commission after downline has actually invested (deposited).

            return $userPackage;
        });
    }

    private function getNextProfitTime(): \Carbon\Carbon
    {
        return now()->addHours(8);
    }
}
