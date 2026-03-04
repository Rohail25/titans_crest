<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AuditLogService;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'action' => 'nullable|string',
            'target_type' => 'nullable|string',
        ]);

        if ($request->action) {
            $logs = AuditLogService::getActionLogs_ByAction($request->action);
        } elseif ($request->target_type) {
            $logs = AuditLogService::getActionLogs_ByType($request->target_type);
        } else {
            $logs = AuditLogService::getActionLogs();
        }

        return view('admin.logs.audit', compact('logs'));
    }

    public function show($id)
    {
        $log = \App\Models\AdminLog::findOrFail($id);

        return view('admin.logs.audit-detail', compact('log'));
    }

    public function byAdmin($adminId)
    {
        $admin = \App\Models\User::findOrFail($adminId);
        $logs = AuditLogService::getAdminLogs($admin);

        return view('admin.logs.audit-admin', compact('admin', 'logs'));
    }
}
