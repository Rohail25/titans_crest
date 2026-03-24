<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use App\Models\UserPackage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfitService
{
    protected WalletService $walletService;
    protected ProfitSharingService $profitSharingService;

    public function __construct(WalletService $walletService, ProfitSharingService $profitSharingService)
    {
        $this->walletService = $walletService;
        $this->profitSharingService = $profitSharingService;
    }

    /**
     * Calculate daily profit for user
     */
    public function calculateDailyProfit(User $user): float
    {
        $activePackages = $user->userPackages()
            ->where('is_active', true)
            ->where('package_status', 'active')
            ->with('package')
            ->get();

        $totalProfit = 0;

        foreach ($activePackages as $userPackage) {
            $totalProfit += $this->calculateDailyProfitForPackage($userPackage);
        }

        return $totalProfit;
    }

    /**
     * Distribute cycle profit to user (called by scheduler every 15 minutes)
     */
    public function distributeDailyProfit(User $user): array
    {
        $stats = [
            'checked' => 0,
            'distributed' => 0,
            'completed' => 0,
        ];

        /** @var \Illuminate\Database\Eloquent\Collection<int, UserPackage> $activePackages */
        $activePackages = $user->userPackages()
            ->where('is_active', true)
            ->where('package_status', 'active')
            ->with('package')
            ->get();

        foreach ($activePackages as $userPackage) {
            $stats['checked']++;

            try {
                $result = $this->distributePackageCycleProfit($user, $userPackage);

                if ($result['distributed']) {
                    $stats['distributed']++;
                }

                if ($result['completed']) {
                    $stats['completed']++;
                }
            } catch (\Throwable $exception) {
                Log::warning('Package cycle profit skipped', [
                    'user_id' => $user->id,
                    'user_package_id' => $userPackage->id,
                    'reason' => $exception->getMessage(),
                ]);
            }
        }

        return $stats;
    }

    /**
     * Distribute profit to multiple users (batch for scheduler)
     */
    public function distributeProfitBatch(array $userIds = []): array
    {
        $stats = [
            'users' => 0,
            'checked_packages' => 0,
            'distributed_packages' => 0,
            'completed_packages' => 0,
            'errors' => 0,
        ];

        $query = User::query();

        if (!empty($userIds)) {
            $query->whereIn('id', $userIds);
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, User> $users */
        $users = $query->with('userPackages.package')
            ->whereHas('userPackages', function ($q) {
                $q->where('is_active', true)
                    ->where('package_status', 'active');
            })
            ->get();

        foreach ($users as $user) {
            $stats['users']++;

            try {
                $userStats = $this->distributeDailyProfit($user);
                $stats['checked_packages'] += $userStats['checked'];
                $stats['distributed_packages'] += $userStats['distributed'];
                $stats['completed_packages'] += $userStats['completed'];
            } catch (\Exception $e) {
                $stats['errors']++;
                Log::error("Profit distribution failed for user {$user->id}: " . $e->getMessage());
            }
        }

        return $stats;
    }

    /**
     * ROI rate based on deposit tier
     */
    public function getRoiRateForAmount(float $amount): float
    {
        $percent = $amount < 500
            ? (float) Setting::get('roi_below_500_percent', 0.65)
            : (float) Setting::get('roi_500_plus_percent', 0.75);

        return $percent / 100;
    }

    /**
     * Get profit summary for user
     */
    public function getProfitSummary(User $user): array
    {
        $dailyProfit = $this->calculateDailyProfit($user);
        
        $profitsThisMonth = $user->earnings()
            ->where('type', 'profit_share')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('amount');

        $profitsThisYear = $user->earnings()
            ->where('type', 'profit_share')
            ->where('created_at', '>=', now()->startOfYear())
            ->sum('amount');

        return [
            'daily_profit' => $dailyProfit,
            'monthly_profit' => $profitsThisMonth,
            'yearly_profit' => $profitsThisYear,
        ];
    }

    /**
     * Get active packages for user
     */
    public function getActivePackages(User $user): array
    {
        return $user->userPackages()
            ->where('is_active', true)
            ->where('package_status', 'active')
            ->with('package')
            ->get()
            ->map(function ($up) {
                // Ensure next_profit_time is set
                if (!$up->next_profit_time) {
                    $baseTime = $up->last_profit_time ?: $up->activated_at ?: $up->created_at ?: now();
                    $up->next_profit_time = $this->getNextCycleTime($baseTime);
                    $up->save();
                }

                // Keep overdue next_profit_time as-is; do not auto-update on dashboard load.
                // This ensures scheduler/ROI dispatcher handles exactly one cycle per period.

                return [
                    'id' => $up->id,
                    'user_package_id' => $up->id,
                    'name' => $up->package->name,
                    'price' => (float) ($up->total_deposit ?: $up->package->price),
                    'daily_profit_rate' => $this->getRoiRateForAmount((float) ($up->total_deposit ?: $up->package->price)),
                    'daily_profit' => $this->calculateDailyProfitForPackage($up),
                    'cycle_profit' => $this->calculateCycleProfitForPackage($up),
                    'activated_at' => $up->activated_at ? $up->activated_at->toIso8601String() : null,
                    'expires_at' => $up->expires_at ? $up->expires_at->toIso8601String() : null,
                    'earning_cap' => (float) $up->earning_cap,
                    'package_status' => $up->package_status,
                    'next_profit_time' => $up->next_profit_time ? $up->next_profit_time->toIso8601String() : null,
                ];
            })
            ->toArray();
    }

    public function getNextCycleTime(?\Carbon\Carbon $from = null): \Carbon\Carbon
    {
        $cycleMinutes = (int) \App\Models\Setting::get('profit_distribution_cycle_minutes', 15);
        return ($from?->copy() ?? now())->addMinutes($cycleMinutes);
    }

    private function calculateDailyProfitForPackage(UserPackage $userPackage): float
    {
        // Daily profit based on ROI rate:
        // 0.65% for packages < $500
        // 0.75% for packages >= $500
        $depositAmount = (float) ($userPackage->total_deposit ?: $userPackage->package?->price ?: 0);

        if ($depositAmount <= 0) {
            return 0;
        }

        return $depositAmount * $this->getRoiRateForAmount($depositAmount);
    }

    private function calculateCycleProfitForPackage(UserPackage $userPackage): float
    {
        // Daily profit is divided into 3 cycles per day
        return $this->calculateDailyProfitForPackage($userPackage) / 3;
    }

    private function distributePackageCycleProfit(User $user, UserPackage $userPackage): array
    {
        $result = [
            'distributed' => false,
            'completed' => false,
        ];

        // Only active, non-completed packages can generate profit
        if (!$userPackage->is_active || $userPackage->package_status !== 'active') {
            return $result;
        }

        if ($userPackage->next_profit_time && now()->lt($userPackage->next_profit_time)) {
            return $result;
        }

        $this->walletService->refreshUserPackageCap($user, $userPackage);
        $userPackage->refresh();

        if ((float) $userPackage->total_earned >= (float) $userPackage->earning_cap) {
            $this->walletService->completeUserPackage($userPackage);
            $result['completed'] = true;
            return $result;
        }

        $cycleProfit = $this->calculateCycleProfitForPackage($userPackage);
        if ($cycleProfit <= 0) {
            return $result;
        }

        DB::transaction(function () use ($user, $userPackage, $cycleProfit) {
            $credited = $this->walletService->addBalance(
                $user,
                $cycleProfit,
                'roi_profit',
                (string) $userPackage->id,
                [
                    'user_package_id' => $userPackage->id,
                    'daily_profit' => $this->calculateDailyProfitForPackage($userPackage),
                    'cycle_profit' => $cycleProfit,
                    'roi_rate' => $this->getRoiRateForAmount((float) ($userPackage->total_deposit ?: $userPackage->package?->price ?: 0)),
                ]
            );

            $userPackage->update([
                'last_profit_time' => now(),
                'next_profit_time' => $this->getNextCycleTime(now()),
            ]);

            $this->profitSharingService->shareProfitWithUplines($user, (float) $credited->amount);
        });

        $result['distributed'] = true;

        return $result;
    }
}
