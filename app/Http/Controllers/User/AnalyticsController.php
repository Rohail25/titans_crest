<?php

namespace App\Http\Controllers\User;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Services\ProfitService;
use App\Services\WalletService;

class AnalyticsController extends Controller
{
    public function __construct(
        protected ProfitService $profitService,
        protected WalletService $walletService,
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $walletSummary = $this->walletService->getWalletSummary($user);
        $profitSummary = $this->profitService->getProfitSummary($user);
        $activePackages = $this->profitService->getActivePackages($user);

        // Get earnings data for charts
        $earningsData = $user->earnings()
            ->where('created_at', '>=', now()->subMonths(6))
            ->get()
            ->groupBy(function ($earning) {
                return $earning->created_at->format('Y-m-d');
            })
            ->map(fn($group) => $group->sum('amount'));

        // Get monthly breakdown
        $monthlyEarnings = $user->earnings()
            ->where('created_at', '>=', now()->subMonths(12))
            ->get()
            ->groupBy(function ($earning) {
                return $earning->created_at->format('Y-m');
            })
            ->map(fn($group) => $group->sum('amount'));

        return view('user.analytics.index', [
            'wallet' => $walletSummary,
            'profit' => $profitSummary,
            'packages' => $activePackages,
            'earningsData' => $earningsData,
            'monthlyEarnings' => $monthlyEarnings,
        ]);
    }
}
