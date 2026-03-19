<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    public static function getKPIs()
    {
        return [
            'total_users' => \App\Models\User::where('role', 'user')->count(),
            'active_users' => \App\Models\User::where('role', 'user')->where('status', 'active')->count(),
            'total_balance' => DB::table('wallets')->sum('balance'),
            'total_earnings' => DB::table('wallets')->sum('total_earned'),
            'total_withdrawals' => DB::table('withdrawals')->sum('net_amount'),
            'pending_withdrawals' => \App\Models\Withdrawal::where('status', 'pending_approval')->count(),
            'pending_withdrawals_amount' => \App\Models\Withdrawal::where('status', 'pending_approval')->sum('net_amount'),
            'approved_withdrawals' => \App\Models\Withdrawal::where('status', 'approved')->count(),
            'approved_withdrawals_amount' => \App\Models\Withdrawal::where('status', 'approved')->sum('net_amount'),
            'pending_deposits' => \App\Models\Deposit::where('status', 'pending')->count(),
            'pending_deposits_amount' => \App\Models\Deposit::where('status', 'pending')->sum('amount'),
        ];
    }

    public static function getUserGrowthChart($days = 30)
    {
        $data = \App\Models\User::where('role', 'user')
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->toArray(),
            'data' => $data->pluck('count')->toArray(),
        ];
    }

    public static function getDepositTrendChart($days = 30)
    {
        $data = \App\Models\Deposit::where('status', 'confirmed')
            ->where('confirmed_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(confirmed_at) as date, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->toArray(),
            'counts' => $data->pluck('count')->toArray(),
            'totals' => $data->pluck('total')->toArray(),
        ];
    }

    public static function getWithdrawalTrendChart($days = 30)
    {
        $data = \App\Models\Withdrawal::where('status', 'approved')
            ->where('approved_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(approved_at) as date, COUNT(*) as count, SUM(net_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->toArray(),
            'counts' => $data->pluck('count')->toArray(),
            'totals' => $data->pluck('total')->toArray(),
        ];
    }

    public static function getRecentActivities($limit = 10)
    {
        return \App\Models\AdminLog::with('admin')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getSystemHealth()
    {
        return [
            'database_status' => 'healthy',
            'last_profit_distribution' => \App\Models\Earning::where('type', 'profit_share')->latest('created_at')->first()?->created_at,
            'failed_emails' => \App\Models\EmailLog::where('status', 'failed')->count(),
            'users_needing_attention' => \App\Models\User::where('status', '!=', 'active')->count(),
        ];
    }
}
