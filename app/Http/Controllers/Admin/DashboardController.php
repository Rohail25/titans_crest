<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminDashboardService;

class DashboardController extends Controller
{
    public function index()
    {
        $kpis = AdminDashboardService::getKPIs();
        $userGrowth = AdminDashboardService::getUserGrowthChart();
        $depositTrend = AdminDashboardService::getDepositTrendChart();
        $withdrawalTrend = AdminDashboardService::getWithdrawalTrendChart();
        $recentActivities = AdminDashboardService::getRecentActivities();
        $systemHealth = AdminDashboardService::getSystemHealth();

        return view('admin.dashboard', compact(
            'kpis',
            'userGrowth',
            'depositTrend',
            'withdrawalTrend',
            'recentActivities',
            'systemHealth'
        ));
    }
}
