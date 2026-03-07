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
        $this->ensureDefaultLevels();

        $commissions = ReferralCommission::orderBy('level')->get();
        
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
