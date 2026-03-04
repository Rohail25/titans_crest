<?php

namespace App\Services\Admin;

use App\Models\EmailLog;

class AdminEmailLogService
{
    public static function getAllLogs($limit = 50)
    {
        return EmailLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public static function getLogsByType(string $type, $limit = 50)
    {
        return EmailLog::with('user')
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public static function getLogsByStatus(string $status, $limit = 50)
    {
        return EmailLog::with('user')
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public static function getLogsByUser(int $userId, $limit = 50)
    {
        return EmailLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public static function searchLogs(string $query, $limit = 50)
    {
        return EmailLog::with('user')
            ->where('recipient', 'like', "%{$query}%")
            ->orWhere('subject', 'like', "%{$query}%")
            ->orWhere('body', 'like', "%{$query}%")
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public static function getEmailStats()
    {
        return [
            'total_emails' => EmailLog::count(),
            'sent_emails' => EmailLog::where('status', 'sent')->count(),
            'failed_emails' => EmailLog::where('status', 'failed')->count(),
            'pending_emails' => EmailLog::where('status', 'pending')->count(),
            'today_sent' => EmailLog::where('status', 'sent')
                ->whereDate('sent_at', now()->toDateString())
                ->count(),
        ];
    }

    public static function getFailedEmails($limit = 50)
    {
        return EmailLog::where('status', 'failed')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }
}
