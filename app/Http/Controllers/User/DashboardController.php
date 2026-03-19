<?php

namespace App\Http\Controllers\User;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Services\WalletService;
use App\Services\ProfitService;
use App\Services\DepositService;
use App\Services\WithdrawalService;
use App\Services\ReferralService;

class DashboardController extends Controller
{
    public function __construct(
        protected WalletService $walletService,
        protected ProfitService $profitService,
        protected DepositService $depositService,
        protected WithdrawalService $withdrawalService,
        protected ReferralService $referralService,
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
        $availablePackages = Package::where('is_active', true)
            ->orderBy('price')
            ->get(['id', 'name', 'price', 'daily_profit_rate', 'duration_days']);

        $latestCompletedPackage = $user->userPackages()
            ->where('package_status', 'completed')
            ->latest('updated_at')
            ->first();

        $nextProfitTime = collect($activePackages)
            ->pluck('next_profit_time')
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

        return view('user.dashboard', [
            'wallet' => $walletSummary,
            'profit' => $profitSummary,
            'packages' => $activePackages,
            'deposits' => $depositStats,
            'withdrawals' => $withdrawalStats,
            'referrals' => $referralStats,
            'teamPerformance' => $teamPerformance,
            'availablePackages' => $availablePackages,
            'recentEarnings' => $recentEarnings,
            'latestCompletedPackage' => $latestCompletedPackage,
            'nextProfitTime' => $nextProfitTime,
        ]);
    }
}
