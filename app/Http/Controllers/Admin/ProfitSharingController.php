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
    public function index()
    {
        $this->ensureDefaultLevels();

        $levels = ProfitSharingLevel::orderBy('level')->get();
        
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
