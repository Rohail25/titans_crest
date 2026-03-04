<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\AdminLog;

class AuditLogService
{
    public static function log(User $admin, string $action, string $targetType, $targetId = null, ?array $oldValues = null, ?array $newValues = null, ?string $reason = null): void
    {
        AdminLog::logAction($admin, $action, $targetType, $targetId, $oldValues, $newValues, $reason);
    }

    public static function getActionLogs($limit = 100)
    {
        return AdminLog::with('admin')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getAdminLogs(User $admin, $limit = 100)
    {
        return AdminLog::where('admin_id', $admin->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getActionLogs_ByType(string $targetType, $limit = 100)
    {
        return AdminLog::where('target_type', $targetType)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getActionLogs_ByAction(string $action, $limit = 100)
    {
        return AdminLog::where('action', $action)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
