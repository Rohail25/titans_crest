<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminWithdrawalService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'status' => 'nullable|in:pending_otp,pending_approval,approved,rejected,cancelled',
        ]);

        $withdrawals = AdminWithdrawalService::getWithdrawals($request->status);
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
            'tx_hash' => 'required|string',
        ]);

        $withdrawal = \App\Models\Withdrawal::findOrFail($id);

        AdminWithdrawalService::approveWithdrawal(
            $withdrawal,
            auth()->user(),
            $request->tx_hash
        );

        return redirect()->route('admin.withdrawals.index')->with('success', 'Withdrawal approved successfully. Transaction hash recorded.');
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
