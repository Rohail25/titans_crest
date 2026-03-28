<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyPerformanceExcellenceReward;
use App\Services\MonthlyPerformanceExcellenceService;
use Illuminate\Http\Request;

class MonthlyPerformanceController extends Controller
{
    public function __construct(
        private MonthlyPerformanceExcellenceService $monthlyPerformanceExcellenceService
    ) {}

    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:paid,not_qualified,qualified_skipped,pending_payout,rejected',
            'month' => 'nullable|date_format:Y-m',
        ]);

        $query = MonthlyPerformanceExcellenceReward::query()
            ->with('sponsor:id,name,email');

        if (!empty($validated['search'])) {
            $search = trim((string) $validated['search']);
            $query->whereHas('sponsor', function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['month'])) {
            $monthStart = now()->createFromFormat('Y-m', (string) $validated['month'])->startOfMonth()->toDateString();
            $monthEnd = now()->createFromFormat('Y-m', (string) $validated['month'])->endOfMonth()->toDateString();
            $query->whereDate('period_start', $monthStart)
                ->whereDate('period_end', $monthEnd);
        }

        $rewards = $query
            ->orderByDesc('period_start')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $statsQuery = MonthlyPerformanceExcellenceReward::query();
        $stats = [
            'total_records' => (int) $statsQuery->count(),
            'total_paid' => (int) MonthlyPerformanceExcellenceReward::query()->where('status', 'paid')->count(),
            'total_not_qualified' => (int) MonthlyPerformanceExcellenceReward::query()->where('status', 'not_qualified')->count(),
            'total_pending' => (int) MonthlyPerformanceExcellenceReward::query()->where('status', 'pending_payout')->count(),
            'total_rejected' => (int) MonthlyPerformanceExcellenceReward::query()->where('status', 'rejected')->count(),
            'total_paid_amount' => (float) MonthlyPerformanceExcellenceReward::query()->where('status', 'paid')->sum('qualifying_tier_reward'),
        ];

        return view('admin.reports.monthly-performance', [
            'rewards' => $rewards,
            'stats' => $stats,
            'filters' => [
                'search' => $validated['search'] ?? '',
                'status' => $validated['status'] ?? '',
                'month' => $validated['month'] ?? '',
            ],
        ]);
    }

    public function confirm(Request $request, MonthlyPerformanceExcellenceReward $reward)
    {
        try {
            $this->monthlyPerformanceExcellenceService->approveReward($reward, (int) $request->user()->id);
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Monthly performance payout confirmed successfully.');
    }

    public function reject(Request $request, MonthlyPerformanceExcellenceReward $reward)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $this->monthlyPerformanceExcellenceService->rejectReward(
                $reward,
                (int) $request->user()->id,
                $validated['reason'] ?? null
            );
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Monthly performance payout rejected successfully.');
    }
}
