<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminEmailLogService;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'type' => 'nullable|in:otp,withdrawal,deposit,notification,other',
            'status' => 'nullable|in:pending,sent,failed',
            'sort' => 'nullable|in:id,recipient,type,status,created_at,sent_at',
            'direction' => 'nullable|in:asc,desc',
        ]);

        $logs = AdminEmailLogService::getFilteredLogs($validated);

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
