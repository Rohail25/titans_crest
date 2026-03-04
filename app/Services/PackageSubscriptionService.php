<?php

namespace App\Services;

use App\Models\Earning;
use App\Models\Package;
use App\Models\Setting;
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

            $this->creditReferralCommission($user, $package);

            return $userPackage;
        });
    }

    protected function creditReferralCommission(User $user, Package $package): void
    {
        $referrer = $user->referrer;
        if (!$referrer) {
            return;
        }

        $commissionPercent = (float) Setting::get('referral_commission_percent', 10);
        $commissionAmount = (float) $package->price * ($commissionPercent / 100);

        if ($commissionAmount <= 0) {
            return;
        }

        $this->walletService->addBalance(
            $referrer,
            $commissionAmount,
            'referral',
            (string) $user->id,
            [
                'referred_user_id' => $user->id,
                'referred_user_name' => $user->name,
                'package_id' => $package->id,
                'package_name' => $package->name,
                'percent' => $commissionPercent,
            ]
        );

        if ($referrer->referralTree) {
            $referrer->referralTree->increment('commission_earned', $commissionAmount);
        }
    }
}
