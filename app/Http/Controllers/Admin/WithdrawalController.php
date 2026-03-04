<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminWithdrawalService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = AdminWithdrawalService::getPendingWithdrawals();
        $stats = AdminWithdrawalService::getWithdrawalStats();

        return view('admin.withdrawals.index', compact('withdrawals', 'stats'));
    }

    public function show($id)
    {
        $withdrawal = AdminWithdrawalService::getWithdrawalById($id);
        $recentWithdrawals = $withdrawal->user->withdrawals()
            ->latest()
            ->take(5)
            ->get();

        return view('admin.withdrawals.show', compact('withdrawal', 'recentWithdrawals'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'wallet_address' => 'required|string',
            'tx_hash' => 'nullable|string',
        ]);

        $withdrawal = \App\Models\Withdrawal::findOrFail($id);

        AdminWithdrawalService::approveWithdrawal(
            $withdrawal,
            auth()->user(),
            $request->wallet_address,
            $request->tx_hash
        );

        return redirect()->route('admin.withdrawals.index')->with('success', 'Withdrawal approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $withdrawal = \App\Models\Withdrawal::findOrFail($id);

        AdminWithdrawalService::rejectWithdrawal(
            $withdrawal,
            auth()->user(),
            $request->reason
        );

        return redirect()->route('admin.withdrawals.index')->with('success', 'Withdrawal rejected successfully.');
    }
}
