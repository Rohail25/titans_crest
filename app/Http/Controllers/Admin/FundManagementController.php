<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminFundService;
use Illuminate\Http\Request;

class FundManagementController extends Controller
{
    public function index()
    {
        $stats = AdminFundService::getFundStats();

        return view('admin.fund-management.index', compact('stats'));
    }

    public function addFunds(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|min:10',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);

        AdminFundService::addFundsToWallet($user, auth()->user(), $request->amount, $request->reason);

        return back()->with('success', 'Funds added successfully.');
    }

    public function deductFunds(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|min:10',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);

        AdminFundService::deductFundsFromWallet($user, auth()->user(), $request->amount, $request->reason);

        return back()->with('success', 'Funds deducted successfully.');
    }

    public function convertSuspicious(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|min:10',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);

        AdminFundService::convertSuspiciousFunds($user, auth()->user(), $request->amount, $request->reason);

        return back()->with('success', 'Suspicious funds converted successfully.');
    }

    public function showLedger($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        $ledger = AdminFundService::getLedgerForUser($user);

        return view('admin.fund-management.ledger', compact('user', 'ledger'));
    }
}
