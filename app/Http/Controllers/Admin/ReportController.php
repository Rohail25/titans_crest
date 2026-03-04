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
        $request->validate([
            'status' => 'nullable|in:active,inactive,banned,suspended',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $users = AdminReportService::getUserReport(
            $request->status,
            $request->date_from,
            $request->date_to
        );

        if ($request->has('export')) {
            $csv = AdminReportService::exportToCSV('users', $users);
            return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="users-report.csv"');
        }

        return view('admin.reports.users', compact('users'));
    }

    public function deposits(Request $request)
    {
        $request->validate([
            'status' => 'nullable|in:pending,confirmed,rejected',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $deposits = AdminReportService::getDepositReport(
            $request->status,
            $request->date_from,
            $request->date_to
        );

        if ($request->has('export')) {
            $csv = AdminReportService::exportToCSV('deposits', $deposits);
            return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="deposits-report.csv"');
        }

        return view('admin.reports.deposits', compact('deposits'));
    }

    public function withdrawals(Request $request)
    {
        $request->validate([
            'status' => 'nullable|in:pending_otp,pending_approval,approved,rejected',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $withdrawals = AdminReportService::getWithdrawalReport(
            $request->status,
            $request->date_from,
            $request->date_to
        );

        if ($request->has('export')) {
            $csv = AdminReportService::exportToCSV('withdrawals', $withdrawals);
            return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="withdrawals-report.csv"');
        }

        return view('admin.reports.withdrawals', compact('withdrawals'));
    }

    public function earnings(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $earnings = AdminReportService::getEarningsReport(
            $request->date_from,
            $request->date_to
        );

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
