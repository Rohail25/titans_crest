<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $stats = AdminReportService::getSystemStats();
        
        return view('admin.reports.index', compact('stats'));
    }

    public function users(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,banned,suspended',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'sort' => 'nullable|in:id,name,email,status,created_at',
            'direction' => 'nullable|in:asc,desc',
        ]);

        $users = AdminReportService::getUserReportPaginated($validated);

        if ($request->has('export')) {
            $csv = AdminReportService::exportToCSV('users', $users->getCollection()->all());
            return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="users-report.csv"');
        }

        return view('admin.reports.users', compact('users'));
    }

    public function deposits(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:pending,confirmed,rejected',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'sort' => 'nullable|in:id,amount,status,created_at,confirmed_at',
            'direction' => 'nullable|in:asc,desc',
        ]);

        $deposits = AdminReportService::getDepositReportPaginated($validated);

        if ($request->has('export')) {
            $csv = AdminReportService::exportToCSV('deposits', $deposits->getCollection()->all());
            return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="deposits-report.csv"');
        }

        return view('admin.reports.deposits', compact('deposits'));
    }

    public function withdrawals(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:pending_otp,pending_approval,approved,rejected',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'sort' => 'nullable|in:id,net_amount,requested_amount,status,created_at,approved_at',
            'direction' => 'nullable|in:asc,desc',
        ]);

        $withdrawals = AdminReportService::getWithdrawalReportPaginated($validated);

        if ($request->has('export')) {
            $csv = AdminReportService::exportToCSV('withdrawals', $withdrawals->getCollection()->all());
            return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="withdrawals-report.csv"');
        }

        return view('admin.reports.withdrawals', compact('withdrawals'));
    }

    public function earnings(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:deposit,referral,profit_share,bonus,suspicious,withdrawal,admin_fund_add,admin_fund_deduct,package_subscription,refund',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'sort' => 'nullable|in:id,amount,type,created_at',
            'direction' => 'nullable|in:asc,desc',
        ]);

        $earnings = AdminReportService::getEarningsReportPaginated($validated);

        return view('admin.reports.earnings', compact('earnings'));
    }

    public function daily(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date',
        ]);

        $date = $request->date ?? now()->toDateString();
        $stats = AdminReportService::getDailyStats($date);

        return view('admin.reports.daily', compact('stats', 'date'));
    }
}
