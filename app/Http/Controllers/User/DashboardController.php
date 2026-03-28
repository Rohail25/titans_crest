<?php

namespace App\Http\Controllers\User;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Setting;
use App\Services\WalletService;
use App\Services\ProfitService;
use App\Services\DepositService;
use App\Services\WithdrawalService;
use App\Services\ReferralService;
use App\Services\LeadershipPerformanceService;
use App\Services\MonthlyPerformanceExcellenceService;

class DashboardController extends Controller
{
    public function __construct(
        protected WalletService $walletService,
        protected ProfitService $profitService,
        protected DepositService $depositService,
        protected WithdrawalService $withdrawalService,
        protected ReferralService $referralService,
        protected LeadershipPerformanceService $leadershipPerformanceService,
        protected MonthlyPerformanceExcellenceService $monthlyPerformanceExcellenceService,
    ) {}

    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $walletSummary = $this->walletService->getWalletSummary($user);
        $profitSummary = $this->profitService->getProfitSummary($user);
        $activePackages = $this->profitService->getActivePackages($user);
        $depositStats = $this->depositService->getDepositStats($user);
        $withdrawalStats = $this->withdrawalService->getWithdrawalStats($user);
        $referralStats = $this->referralService->getReferralStats($user);
        $teamPerformance = $this->referralService->getDashboardTeamPerformance($user);
        $leadershipPerformance = $this->leadershipPerformanceService->getUserLeadershipPerformanceSummary($user);
        $monthlyPerformance = $this->monthlyPerformanceExcellenceService->getUserMonthlyPerformanceSummary($user);
        $monthlyPerformanceRecords = $this->monthlyPerformanceExcellenceService->getUserMonthlyPerformanceRecords($user);
        $availablePackages = Package::where('is_active', true)
            ->orderBy('price')
            ->get(['id', 'name', 'price', 'daily_profit_rate', 'duration_days']);

        $latestCompletedPackage = $user->userPackages()
            ->where('package_status', 'completed')
            ->latest('updated_at')
            ->first();

        $nextProfitTime = collect($activePackages)
            ->map(function ($package) {
                // If next_profit_time is missing or in the past, calculate it from last_profit_time
                if (empty($package['next_profit_time'])) {
                    return null; // Skip packages without profit time
                }
                
                // Parse the ISO string to check if it's in the past
                $nextTime = \Carbon\Carbon::parse($package['next_profit_time']);
                if ($nextTime->lt(now())) {
                    // If overdue, calculate next cycle from current time + configured cycle minutes
                    // This prevents timer from showing 00:00:00
                    $cycleMinutes = (int) \App\Models\Setting::get('profit_distribution_cycle_minutes', 15);
                    $calculatedTime = now()->addMinutes($cycleMinutes);
                    return $calculatedTime->toIso8601String();
                }
                
                return $package['next_profit_time'];
            })
            ->filter()
            ->sort()
            ->first();

        // Ensure nextProfitTime is a string (it should be from getActivePackages)
        if ($nextProfitTime && is_object($nextProfitTime)) {
            $nextProfitTime = $nextProfitTime->toIso8601String();
        }

        // Get recent earnings
        $recentEarnings = $user->earnings()
            ->latest()
            ->limit(5)
            ->get();

        $cycleMinutes = (int) \App\Models\Setting::get('profit_distribution_cycle_minutes', 15);

        return view('user.dashboard', [
            'wallet' => $walletSummary,
            'profit' => $profitSummary,
            'packages' => $activePackages,
            'deposits' => $depositStats,
            'withdrawals' => $withdrawalStats,
            'referrals' => $referralStats,
            'leadershipPerformance' => $leadershipPerformance,
            'monthlyPerformance' => $monthlyPerformance,
            'monthlyPerformanceRecords' => $monthlyPerformanceRecords,
            'teamPerformance' => $teamPerformance,
            'availablePackages' => $availablePackages,
            'recentEarnings' => $recentEarnings,
            'latestCompletedPackage' => $latestCompletedPackage,
            'nextProfitTime' => $nextProfitTime,
            'profitCycleMinutes' => $cycleMinutes,
        ]);
    }

    public function triggerProfitDistribution()
    {
        try {
            $user = Auth::user();

            $stats = $this->profitService->distributeDailyProfit($user);
            $cycleMinutes = (int) Setting::get('profit_distribution_cycle_minutes', 15);

            return response()->json([
                'status' => 'success',
                'message' => 'Profit distribution executed.',
                'stats' => $stats,
                'cycle_minutes' => $cycleMinutes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Profit distribution failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
