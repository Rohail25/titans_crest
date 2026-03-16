<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AuditLogService;
use App\Models\AdminLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'action' => 'nullable|string',
            'target_type' => 'nullable|string',
            'sort' => 'nullable|in:id,action,target_type,created_at',
            'direction' => 'nullable|in:asc,desc',
        ]);

        $allowedSorts = ['id', 'action', 'target_type', 'created_at'];
        $sort = in_array($validated['sort'] ?? 'created_at', $allowedSorts, true)
            ? ($validated['sort'] ?? 'created_at')
            : 'created_at';
        $direction = ($validated['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $logs = AdminLog::with('admin')
            ->when(!empty($validated['search']), function ($q) use ($validated) {
                $search = trim((string) $validated['search']);
                $q->where(function ($sub) use ($search) {
                    $sub->where('action', 'like', "%{$search}%")
                        ->orWhere('target_type', 'like', "%{$search}%")
                        ->orWhereHas('admin', function ($adminQ) use ($search) {
                            $adminQ->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when(!empty($validated['action']), fn ($q) => $q->where('action', $validated['action']))
            ->when(!empty($validated['target_type']), fn ($q) => $q->where('target_type', $validated['target_type']))
            ->orderBy($sort, $direction)
            ->paginate(50)
            ->withQueryString();

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
