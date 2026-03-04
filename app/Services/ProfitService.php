<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPackage;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class ProfitService
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Calculate daily profit for user
     */
    public function calculateDailyProfit(User $user): float
    {
        $activePackages = $user->userPackages()
            ->where('is_active', true)
            ->with('package')
            ->get();

        $totalProfit = 0;

        foreach ($activePackages as $userPackage) {
            $package = $userPackage->package;
            $profit = $package->price * $package->daily_profit_rate;
            $totalProfit += $profit;
        }

        return $totalProfit;
    }

    /**
     * Distribute daily profit to user (called by scheduler)
     */
    public function distributeDailyProfit(User $user): void
    {
        // Skip if 3x cap reached
        if ($this->walletService->has3xCapReached($user)) {
            return;
        }

        $profit = $this->calculateDailyProfit($user);

        if ($profit <= 0) {
            return;
        }

        // Check remaining capacity before 3x cap
        $remaining = $this->walletService->get3xCapRemaining($user);
        if ($remaining <= 0) {
            return;
        }

        // Don't exceed 3x cap
        $creditAmount = min($profit, $remaining);

        DB::transaction(function () use ($user, $creditAmount) {
            $this->walletService->addBalance(
                $user,
                $creditAmount,
                'profit_share',
                null,
                ['calculated_profit' => $this->calculateDailyProfit($user)]
            );
        });
    }

    /**
     * Distribute profit to multiple users (batch for scheduler)
     */
    public function distributeProfitBatch(array $userIds = []): void
    {
        $query = User::query();

        if (!empty($userIds)) {
            $query->whereIn('id', $userIds);
        }

        $users = $query->with('userPackages.package')
            ->whereHas('userPackages', function ($q) {
                $q->where('is_active', true);
            })
            ->get();

        foreach ($users as $user) {
            try {
                $this->distributeDailyProfit($user);
            } catch (\Exception $e) {
                // Log error but continue with next user
                \Log::error("Profit distribution failed for user {$user->id}: " . $e->getMessage());
            }
        }
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
            ->with('package')
            ->get()
            ->map(fn($up) => [
                'id' => $up->id,
                'name' => $up->package->name,
                'price' => $up->package->price,
                'daily_profit_rate' => $up->package->daily_profit_rate,
                'daily_profit' => $up->package->price * $up->package->daily_profit_rate,
                'activated_at' => $up->activated_at,
                'expires_at' => $up->expires_at,
            ])
            ->toArray();
    }
}
