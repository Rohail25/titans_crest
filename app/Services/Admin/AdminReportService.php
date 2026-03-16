<?php

namespace App\Services\Admin;

use App\Models\EmailLog;
use Illuminate\Support\Facades\DB;

class AdminReportService
{
    private static function normalizeSort(string $sort, array $allowed, string $default = 'created_at'): string
    {
        return in_array($sort, $allowed, true) ? $sort : $default;
    }

    private static function normalizeDirection(string $direction): string
    {
        return strtolower($direction) === 'asc' ? 'asc' : 'desc';
    }

    public static function getUserReportPaginated(array $filters = [], int $perPage = 25)
    {
        $sort = self::normalizeSort($filters['sort'] ?? 'created_at', ['id', 'name', 'email', 'status', 'created_at']);
        $direction = self::normalizeDirection($filters['direction'] ?? 'desc');

        $query = \App\Models\User::where('role', 'user')->with(['wallet', 'userPackages.package', 'earnings']);

        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();
    }

    public static function getDepositReportPaginated(array $filters = [], int $perPage = 25)
    {
        $sort = self::normalizeSort($filters['sort'] ?? 'created_at', ['id', 'amount', 'status', 'created_at', 'confirmed_at']);
        $direction = self::normalizeDirection($filters['direction'] ?? 'desc');

        $query = \App\Models\Deposit::with('user');

        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();
    }

    public static function getWithdrawalReportPaginated(array $filters = [], int $perPage = 25)
    {
        $sort = self::normalizeSort($filters['sort'] ?? 'created_at', ['id', 'net_amount', 'requested_amount', 'status', 'created_at', 'approved_at']);
        $direction = self::normalizeDirection($filters['direction'] ?? 'desc');

        $query = \App\Models\Withdrawal::with('user');

        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();
    }

    public static function getEarningsReportPaginated(array $filters = [], int $perPage = 25)
    {
        $sort = self::normalizeSort($filters['sort'] ?? 'created_at', ['id', 'amount', 'type', 'created_at']);
        $direction = self::normalizeDirection($filters['direction'] ?? 'desc');

        $query = \App\Models\Earning::with('user');

        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('type', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();
    }

    public static function getUserReport(?string $status = null, ?string $dateFrom = null, ?string $dateTo = null)
    {
        return self::getUserReportPaginated([
            'status' => $status,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ], 10000)->getCollection();
    }

    public static function getDepositReport(?string $status = null, ?string $dateFrom = null, ?string $dateTo = null)
    {
        return self::getDepositReportPaginated([
            'status' => $status,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ], 10000)->getCollection();
    }

    public static function getWithdrawalReport(?string $status = null, ?string $dateFrom = null, ?string $dateTo = null)
    {
        return self::getWithdrawalReportPaginated([
            'status' => $status,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ], 10000)->getCollection();
    }

    public static function getEarningsReport(?string $dateFrom = null, ?string $dateTo = null)
    {
        return self::getEarningsReportPaginated([
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ], 10000)->getCollection();
    }

    public static function getSystemStats()
    {
        return [
            'total_users' => \App\Models\User::where('role', 'user')->count(),
            'total_deposits' => \App\Models\Deposit::where('status', 'confirmed')->sum('amount'),
            'total_withdrawals' => \App\Models\Withdrawal::where('status', 'approved')->sum('net_amount'),
            'total_earnings' => \App\Models\Earning::sum('amount'),
            'pending_withdrawals' => \App\Models\Withdrawal::where('status', 'pending_approval')->count(),
            'pending_deposits' => \App\Models\Deposit::where('status', 'pending')->count(),
        ];
    }

    public static function getDailyStats(?string $date = null)
    {
        $date = $date ? \Carbon\Carbon::parse($date) : now();

        return [
            'new_users' => \App\Models\User::where('role', 'user')
                ->whereDate('created_at', $date)
                ->count(),
            'deposits_confirmed' => \App\Models\Deposit::where('status', 'confirmed')
                ->whereDate('confirmed_at', $date)
                ->count(),
            'deposits_amount' => \App\Models\Deposit::where('status', 'confirmed')
                ->whereDate('confirmed_at', $date)
                ->sum('amount'),
            'withdrawals_approved' => \App\Models\Withdrawal::where('status', 'approved')
                ->whereDate('approved_at', $date)
                ->count(),
            'withdrawals_amount' => \App\Models\Withdrawal::where('status', 'approved')
                ->whereDate('approved_at', $date)
                ->sum('net_amount'),
            'earnings_distributed' => \App\Models\Earning::whereDate('created_at', $date)
                ->sum('amount'),
        ];
    }

    public static function exportToCSV(string $type, array $data): string
    {
        $csv = fopen('php://memory', 'r+');
        
        if ($type === 'users') {
            fputcsv($csv, ['ID', 'Name', 'Email', 'Status', 'Balance', 'Total Earned', 'Created At']);
            foreach ($data as $user) {
                fputcsv($csv, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->status,
                    $user->wallet->balance ?? 0,
                    $user->wallet->total_earned ?? 0,
                    $user->created_at,
                ]);
            }
        } elseif ($type === 'deposits') {
            fputcsv($csv, ['ID', 'User', 'Amount', 'Status', 'TX Hash', 'Created At']);
            foreach ($data as $deposit) {
                fputcsv($csv, [
                    $deposit->id,
                    $deposit->user->name,
                    $deposit->amount,
                    $deposit->status,
                    $deposit->tx_hash,
                    $deposit->created_at,
                ]);
            }
        } elseif ($type === 'withdrawals') {
            fputcsv($csv, ['ID', 'User', 'Amount', 'Status', 'Created At']);
            foreach ($data as $withdrawal) {
                fputcsv($csv, [
                    $withdrawal->id,
                    $withdrawal->user->name,
                    $withdrawal->net_amount,
                    $withdrawal->status,
                    $withdrawal->created_at,
                ]);
            }
        }

        rewind($csv);
        return stream_get_contents($csv);
    }
}
