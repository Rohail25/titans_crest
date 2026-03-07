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
        protected ReferralCommissionService $referralCommissionService,
    ) {}

    public function subscribe(User $user, Package $package): UserPackage
    {
        return DB::transaction(function () use ($user, $package) {
            if (!$package->is_active) {
                throw new \Exception('Selected package is not active.');
            }

            $hasActivePackage = $user->userPackages()->where('is_active', true)->exists();
            if ($hasActivePackage) {
                throw new \Exception('You already have an active package.');
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

            $userPackage = UserPackage::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'activated_at' => now(),
                'expires_at' => $expiresAt,
                'is_active' => true,
            ]);

            $this->referralCommissionService->distributeCommissions($user, (float) $package->price);

            return $userPackage;
        });
    }
}
