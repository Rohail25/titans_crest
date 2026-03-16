<?php

namespace App\Services\Admin;

use App\Models\EmailLog;

class AdminEmailLogService
{
    public static function getFilteredLogs(array $filters = [], int $limit = 50)
    {
        $allowedSorts = ['id', 'recipient', 'type', 'status', 'created_at', 'sent_at'];
        $sort = in_array($filters['sort'] ?? 'created_at', $allowedSorts, true)
            ? ($filters['sort'] ?? 'created_at')
            : 'created_at';
        $direction = strtolower($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $query = EmailLog::with('user');

        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('recipient', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy($sort, $direction)
            ->paginate($limit)
            ->withQueryString();
    }

    public static function getAllLogs($limit = 50)
    {
        return self::getFilteredLogs([], $limit);
    }

    public static function getLogsByType(string $type, $limit = 50)
    {
        return self::getFilteredLogs(['type' => $type], $limit);
    }

    public static function getLogsByStatus(string $status, $limit = 50)
    {
        return self::getFilteredLogs(['status' => $status], $limit);
    }

    public static function getLogsByUser(int $userId, $limit = 50)
    {
        return EmailLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public static function searchLogs(string $query, $limit = 50)
    {
        return self::getFilteredLogs(['search' => $query], $limit);
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
