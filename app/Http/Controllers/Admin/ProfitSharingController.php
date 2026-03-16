<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfitSharingLevel;
use Illuminate\Http\Request;

class ProfitSharingController extends Controller
{
    /**
     * Display all profit sharing levels.
     */
    public function index(Request $request)
    {
        $this->ensureDefaultLevels();

        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'sort' => 'nullable|in:id,level,percentage,created_at',
            'direction' => 'nullable|in:asc,desc',
        ]);

        $allowedSorts = ['id', 'level', 'percentage', 'created_at'];
        $sort = in_array($validated['sort'] ?? 'created_at', $allowedSorts, true) ? ($validated['sort'] ?? 'created_at') : 'created_at';
        $direction = ($validated['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $levels = ProfitSharingLevel::query()
            ->when(!empty($validated['search']), function ($q) use ($validated) {
                $search = trim((string) $validated['search']);
                $q->where('level', 'like', "%{$search}%");
            })
            ->orderBy($sort, $direction)
            ->paginate(25)
            ->withQueryString();
        
        return view('admin.commissions.profit-sharing-index', [
            'levels' => $levels,
        ]);
    }

    /**
     * Update profit sharing levels.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'levels' => 'required|array',
            'levels.*.id' => 'required|integer|exists:profit_sharing_levels,id',
            'levels.*.percentage' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($validated['levels'] as $levelData) {
            ProfitSharingLevel::where('id', $levelData['id'])
                ->update([
                    'percentage' => $levelData['percentage'],
                ]);
        }

        return back()->with('success', 'Profit sharing levels updated successfully');
    }

    private function ensureDefaultLevels(): void
    {
        $defaults = [
            1 => 20.00,
            2 => 10.00,
            3 => 5.00,
            4 => 5.00,
            5 => 3.00,
            6 => 3.00,
            7 => 2.00,
            8 => 2.00,
            9 => 2.00,
            10 => 2.00,
        ];

        foreach ($defaults as $level => $percentage) {
            ProfitSharingLevel::firstOrCreate(
                ['level' => $level],
                ['percentage' => $percentage]
            );
        }
    }
}
