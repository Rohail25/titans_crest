<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminWithdrawalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:pending_otp,pending_approval,approved,rejected,cancelled',
            'per_page' => 'nullable|integer|min:5|max:200',
        ]);

        $perPage = $validated['per_page'] ?? 15;

        $withdrawals = AdminWithdrawalService::getWithdrawals($request->status, $perPage);
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
            Auth::user(),
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
            Auth::user(),
            $request->reason
        );

        return redirect()->route('admin.withdrawals.index')->with('success', 'Withdrawal rejected successfully.');
    }
}
