<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminEmailLogService;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:otp,withdrawal,deposit,notification,other',
            'status' => 'nullable|in:pending,sent,failed',
        ]);

        if ($request->has('q')) {
            $logs = AdminEmailLogService::searchLogs($request->q);
        } elseif ($request->type) {
            $logs = AdminEmailLogService::getLogsByType($request->type);
        } elseif ($request->status) {
            $logs = AdminEmailLogService::getLogsByStatus($request->status);
        } else {
            $logs = AdminEmailLogService::getAllLogs();
        }

        $stats = AdminEmailLogService::getEmailStats();

        return view('admin.logs.email', compact('logs', 'stats'));
    }

    public function show($id)
    {
        $log = \App\Models\EmailLog::findOrFail($id);

        return view('admin.logs.email-detail', compact('log'));
    }

    public function failed()
    {
        $logs = AdminEmailLogService::getFailedEmails();
        $stats = AdminEmailLogService::getEmailStats();

        return view('admin.logs.email-failed', compact('logs', 'stats'));
    }
}
