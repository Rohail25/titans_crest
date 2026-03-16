<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralCommission;
use Illuminate\Http\Request;

class ReferralCommissionController extends Controller
{
    /**
     * Display all referral commission levels.
     */
    public function index()
    {
        $request = request();
        $this->ensureDefaultLevels();

        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive',
            'sort' => 'nullable|in:id,level,percentage,is_active,created_at',
            'direction' => 'nullable|in:asc,desc',
        ]);

        $allowedSorts = ['id', 'level', 'percentage', 'is_active', 'created_at'];
        $sort = in_array($validated['sort'] ?? 'created_at', $allowedSorts, true) ? ($validated['sort'] ?? 'created_at') : 'created_at';
        $direction = ($validated['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $commissions = ReferralCommission::query()
            ->when(!empty($validated['search']), function ($q) use ($validated) {
                $search = trim((string) $validated['search']);
                $q->where('level', 'like', "%{$search}%");
            })
            ->when(!empty($validated['status']), function ($q) use ($validated) {
                $q->where('is_active', $validated['status'] === 'active');
            })
            ->orderBy($sort, $direction)
            ->paginate(25)
            ->withQueryString();
        
        return view('admin.commissions.referral-index', [
            'commissions' => $commissions,
        ]);
    }

    /**
     * Update referral commission levels.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'commissions' => 'required|array',
            'commissions.*.id' => 'required|integer|exists:referral_commissions,id',
            'commissions.*.percentage' => 'required|numeric|min:0|max:100',
            'commissions.*.is_active' => 'nullable|boolean',
        ]);

        foreach ($validated['commissions'] as $commissionData) {
            ReferralCommission::where('id', $commissionData['id'])
                ->update([
                    'percentage' => $commissionData['percentage'],
                    'is_active' => $commissionData['is_active'] ?? false,
                ]);
        }

        return back()->with('success', 'Referral commissions updated successfully');
    }

    private function ensureDefaultLevels(): void
    {
        $defaults = [
            1 => 7.00,
            2 => 4.00,
            3 => 2.00,
            4 => 1.00,
            5 => 1.00,
        ];

        foreach ($defaults as $level => $percentage) {
            ReferralCommission::firstOrCreate(
                ['level' => $level],
                ['percentage' => $percentage, 'is_active' => true]
            );
        }
    }
}
