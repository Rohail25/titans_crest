<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigurationController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive',
            'sort' => 'nullable|in:id,name,price,daily_profit_rate,duration_days,created_at',
            'direction' => 'nullable|in:asc,desc',
        ]);

        $settings = AdminConfigService::getAll();
        $packages = AdminConfigService::getFilteredPackageSettings($validated);

        return view('admin.settings.index', compact('settings', 'packages'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'bnb_wallet_address' => 'required|string',
            'referral_commission_percent' => 'required|numeric|min:0|max:100',
            'withdrawal_fee_percent' => 'required|numeric|min:0|max:100',
            'otp_expiry_minutes' => 'required|integer|min:1',
            'min_withdrawal_amount' => 'required|numeric|min:0',
            'max_daily_profit_multiplier' => 'required|numeric|min:0.1|max:10',
        ]);

        $settings = [
            'bnb_wallet_address' => $request->bnb_wallet_address,
            'referral_commission_percent' => $request->referral_commission_percent,
            'withdrawal_fee_percent' => $request->withdrawal_fee_percent,
            'otp_expiry_minutes' => $request->otp_expiry_minutes,
            'min_withdrawal_amount' => $request->min_withdrawal_amount,
            'max_daily_profit_multiplier' => $request->max_daily_profit_multiplier,
        ];

        AdminConfigService::updateSettings($settings, Auth::user());

        return back()->with('success', 'Settings updated successfully.');
    }

    public function updatePackage(Request $request, $packageId)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'daily_profit_rate' => 'required|numeric|min:0|max:100',
            'duration_days' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ]);

        AdminConfigService::updatePackage(
            $packageId,
            $request->only(['name', 'price', 'daily_profit_rate', 'duration_days', 'is_active']),
            Auth::user()
        );

        return back()->with('success', 'Package updated successfully.');
    }
}
